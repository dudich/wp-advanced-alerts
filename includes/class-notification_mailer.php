<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://dev.loc
 * @since      1.0.0
 *
 * @package    Notification-mailer
 * @subpackage Notification-mailer/includes
 */
require_once 'NotificationModel.php';
require_once 'ProcessNotification.php';
require_once 'class-classnotificationbase.php';
require_once 'class-am_cron.php';

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Notification-mailer
 * @subpackage Notification-mailer/includes
 * @author     Developer <dev@dev.loc>
 */
class Notification_mailer {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Notification_mailer_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	protected $processNotification;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'notification-mailer';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_common_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_wp_cron_hooks();

		if ( ! class_exists( 'ProcessNotification' ) ) {
			require_once 'ProcessNotification.php';
		}
		$this->processNotification = new ProcessNotification();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Notification_mailer_Loader. Orchestrates the hooks of the plugin.
	 * - Notification_mailer_i18n. Defines internationalization functionality.
	 * - Notification_mailer_Admin. Defines all hooks for the admin area.
	 * - Notification_mailer_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once NOTIFICATION_MAILER_DIR_PATH . 'includes/class-notification_mailer_loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once NOTIFICATION_MAILER_DIR_PATH . 'includes/class-notification_mailer_i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once NOTIFICATION_MAILER_DIR_PATH . 'admin/class-notification_mailer_admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once NOTIFICATION_MAILER_DIR_PATH . 'public/class-notification_mailer_public.php';

		$this->loader = new Notification_mailer_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Notification_mailer_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Notification_mailer_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}


	/**
	 * Register all of the hooks related to the both admin and public area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_common_hooks() {
		$this->loader->add_action( 'ae_insert_user', $this, 'user_register_send_email' );
//		$this->loader->add_action( 'transition_post_status', $this, 'project_posted_send_email_old', 10, 3 );
//		$this->loader->add_action( 'ae_publish_post', $this, 'project_posted_send_email' );
		$this->loader->add_action( 'ae_after_change_status_publish', $this, 'project_posted_send_email_new' );
//	    $this->loader->add_action( 'transition_post_status', $this, 'project_bid_received_send_email', 10, 3 );
		$this->loader->add_action( 'ae_insert_bid', $this, 'project_bid_received_send_email', 10, 2 );
		$this->loader->add_action( 'transition_post_status', $this, 'project_archived_send_email', 10, 3 );
		$this->loader->add_action( 'delete_post', $this, 'project_deleted_send_email', 10, 1 );
		$this->loader->add_action( 'fre_accept_bid', $this, 'bid_accept_send_email', 10, 1 );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Notification_mailer_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

//		$this->loader->add_action( 'admin_init', $plugin_admin, 'templates_settings' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'templates_admin_menu' );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'templates_admin_head' );
		$this->loader->add_filter( 'set-screen-option', $plugin_admin, 'templates_set_screen_options', 10, 3 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Notification_mailer_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_ajax_notify_bidders_selection_date', $plugin_public, 'notify_bidders_selection_date' );
		$this->loader->add_action( 'wp_ajax_nopriv_notify_bidders_selection_date', $plugin_public, 'notify_bidders_selection_date' );


	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Notification_mailer_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/* Building Email 1 (Welcome)
	 * Contractor Email 1: signs up
	 */
	public function user_register_send_email( $user_id ) {
		$args = array(
			'user' => get_userdata( $user_id ),
		);
		/* Building Email 1 (Welcome) */
		if ( in_array( EMPLOYER, $args['user']->roles ) ) {
			$this->processNotification->runNotification( 'Email1', 1, $args );
		} /* Contractor Email 1: signs up */
		elseif ( in_array( FREELANCER, $args['user']->roles ) ) {
			$this->processNotification->runNotification( 'Email1', 2, $args );
		}
	}

	/* Building Email 6 (project posted) */
	public function project_posted_send_email_old( $new_status, $old_status, $project ) {
		if ( 'project' === $project->post_type && 'publish' == $new_status && $new_status !== $old_status && ! wp_is_post_revision( $project->ID ) ) {
			$args = array(
				'user'      => get_userdata( $project->post_author ),
				'post'      => $project,
				'post_link' => get_permalink( $project->ID ),
			);
			$this->processNotification->runNotification( 'Email6', 1, $args );
		}
	}

	public function project_posted_send_email( $project_id ) {
		$project = get_post( $project_id );
		$args    = array(
			'user'      => get_userdata( $project->post_author ),
			'post'      => $project,
			'post_link' => get_permalink( $project->ID ),
		);
		$this->processNotification->runNotification( 'Email6', 1, $args );
	}

	public function project_posted_send_email_new( $project ) {
//		$project = get_post( $project_id );
		$args = array(
			'user'      => get_userdata( $project->post_author ),
			'post'      => $project,
			'post_link' => get_permalink( $project->ID ),
		);
		$this->processNotification->runNotification( 'Email6', 1, $args );
	}

	/* Building Email 7, 8, 17 (bid received)
	 * Contractor Email 12 (after a bid is submitted)
	 */
	public function project_bid_received_send_email( $bid_id ) {
		$bid     = get_post( $bid_id );
		$project = get_post( $bid->post_parent );

		/* Contractor Email 12 (after a bid is submitted) */
		$args = array(
			'user'      => get_userdata( $bid->post_author ),
			'post'      => $project,
			'post_link' => get_permalink( $project->ID ),
		);
		$this->processNotification->runNotification( 'Email12', 2, $args );

		if ( ! empty( $bid->post_parent ) ) {
			$bids_number = NotificationModel::getBidsNumberForProject( $bid->post_parent );

			/* Building Email 7 (Building) (after project is posted and first bid is received) */
			if ( empty( $bids_number ) || $bids_number <= 1 ) {
				$args = array(
					'post'      => $project,
					'post_link' => get_permalink( $project->ID ),
					'user'      => get_userdata( $project->post_author ),
				);
				$this->processNotification->runNotification( 'Email7', 1, $args );
			}

			/* Building Email 8 (Building) (after project is posted and 5 bids are received) */
			if ( ! empty( $bids_number ) && $bids_number == 5 ) {
				$args = array(
					'post'      => $project,
					'post_link' => get_permalink( $project->ID ),
					'user'      => get_userdata( $project->post_author ),
				);
				$this->processNotification->runNotification( 'Email8', 1, $args );
			}

			/* Building Email 17 (Building) (bid received, all bids after first bid) */
			if ( ! empty( $bids_number ) && $bids_number > 1 ) {
				$args = array(
					'post'      => $project,
					'post_link' => get_permalink( $project->ID ),
					'user'      => get_userdata( $project->post_author ),
				);
				$this->processNotification->runNotification( 'Email17', 1, $args );
			}
		}
	}

	/* Building Email 16 (project archived/deleted) */
	public function project_archived_send_email( $new_status, $old_status, $project ) {
		if ( 'project' === $project->post_type && ( 'archive' == $new_status || 'trash' == $new_status ) && $new_status !== $old_status && ! wp_is_post_revision( $project->ID ) ) {
			$args = array(
				'user'      => get_userdata( $project->post_author ),
				'post'      => $project,
				'post_link' => get_permalink( $project->ID ),
			);
			$this->processNotification->runNotification( 'Email16', 1, $args );
		}
	}

	public function project_deleted_send_email( $project_id ) {
		$project = get_post( $project_id );
		$args    = array(
			'user'      => get_userdata( $project->post_author ),
			'post'      => $project,
			'post_link' => get_permalink( $project->ID ),
		);
		$this->processNotification->runNotification( 'Email16', 1, $args );
	}

	/* Building Email 14 (after a winning bid is chosen)
	 * Contractor Email 10: bid is selected
	 * Contractor Email 11: bid is not selected
	 */
	public function bid_accept_send_email( $bid_id ) {
		$bid_author = get_userdata( get_post_field( 'post_author', $bid_id ) );
		$project    = get_post( get_post_field( 'post_parent', $bid_id ) );

		/* Contractor Email 10: bid is selected */
		$args = array(
			'user'      => $bid_author,
			'post'      => $project,
			'post_link' => get_permalink( $project->ID ),
		);
		$this->processNotification->runNotification( 'Email10', 2, $args );

		/* Building Email 14 (after a winning bid is chosen) */
		$project_author = get_userdata( $project->post_author );
		$args           = array(
			'user_project' => $project_author,
			'user_bid'     => $bid_author,
			'post'         => $project,
			'post_link'    => get_permalink( $project->ID ),
		);
		$this->processNotification->runNotification( 'Email14', 1, $args );

		/* Contractor Email 11: bid is not selected */
		$unaccepted_bids_authors = NotificationModel::getUnacceptedBidsAuthorsForProject( $project->ID, $bid_id );
		if ( ! empty( $unaccepted_bids_authors ) && is_array( $unaccepted_bids_authors ) ) {
			$args = array(
				'user_project' => $project_author,
				'users_bids'   => [],
				'post'         => $project,
				'post_link'    => get_permalink( $project->ID ),
			);
			foreach ( $unaccepted_bids_authors as $bids_author ) {
				$args['users_bids'][] = get_userdata( $bids_author->post_author );
			}
			if ( ! empty( $args['users_bids'] ) ) {
				$this->processNotification->runNotification( 'Email11', 2, $args );
			}
		}
	}

	/**
	 * Define wp_cron hooks and callbacks
	 * Processed email notification events are defined in the ClassNotificationBase class
	 */

	public function define_wp_cron_hooks() {
		$mailer          = new ProcessNotification();
		$processedEmails = $mailer->getProcessedEmails();
		$events          = [];
		foreach ( $processedEmails as $processData ) {
			$processMethod            = 'process' . $processData['suffix'] . '_' . $processData['destination'];
			$events[ $processMethod ] = [
				'callback'      => [ $mailer, $processMethod ],
				'args'          => array( [ $processData['suffix'], $processData['destination'] ] ),
				'interval_name' => $processData['interval_name'],
				'interval_sec'  => $processData['interval_sec'],
				'interval_desc' => $processData['interval_desc'],
			];
		}
		$args = [
			'id'     => 'cron_job_1',
			'events' => $events
		];
		$cron = new Am_Cron( $args );

		return true;
	}

}
