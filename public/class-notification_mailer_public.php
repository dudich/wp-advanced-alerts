<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://dev.loc
 * @since      1.0.0
 *
 * @package    Notification-mailer
 * @subpackage Notification-mailer/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Notification-mailer
 * @subpackage Notification-mailer/public
 * @author     Developer <dev@dev.loc>
 */
require_once NOTIFICATION_MAILER_DIR_PATH . '/includes/ProcessNotification.php';

class Notification_mailer_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Notification_mailer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Notification_mailer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/notification-mailer-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Notification_mailer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Notification_mailer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/notification-mailer-public.js', array( 'jquery' ), $this->version, false );

	}

	public function complete_account() {
		$pass                      = $_POST['p'];
		$email                     = $_POST['email'];
		$contractorInvitationModel = new ContractorInvitationModel();
		$res                       = $contractorInvitationModel->updatePassword( $email, $pass );
		if ( $res > 0 ) {
			$response = [
				'status'  => 'success',
				'message' => 'Password updated successfully.'
			];

		} else if ( $res === false ) {
			$response = [
				'status'  => 'error',
				'message' => 'Problem on updating password. Please try again.'
			];
		} else if ( $res === 0 ) {
			$response = [
				'status'  => 'error',
				'message' => 'This password is currently used. Please try again.'
			];
		}

		if ( ! empty( $response ) ) {
			wp_send_json( $response );
		}
	}

	public function notify_bidders_selection_date() {
		$selectionDate = $_POST['date'];
		$message       = $_POST['message'];
		$projectId     = $_POST['project_id'];
		$processClass  = new ProcessNotification();
		$data          = [
			'selection_date' => $selectionDate,
			'message'        => $message,
			'project_id'     => $projectId
		];

		// Save deadline
		$save_meta = update_post_meta( $projectId, 'deadline', $selectionDate );

		if ( $save_meta === false ) {
			$response = [
				'status'  => 'error',
				'message' => 'Can\'t update deadline.'
			];
			wp_send_json( $response );
		}

		// Clear notification logs (notification_log table) for email 11, 12, 13

		$clearResult = $processClass->resetDeadlineLog( $projectId );

		$result = $processClass->processEmail13_2( [ 'Email13', 2, $data ] );

		if ( $result > 0 ) {
			$response = [
				'status'  => 'success',
				'message' => 'Bidders notified .'
			];
		} else if ( $result === false ) {
			$response = [
				'status'  => 'error',
				'message' => 'Problem on updating selection date. Please try again.'
			];
		} else if ( $result === 0 ) {
			$response = [
				'status'  => 'error',
				'message' => 'This value is already set as selection date.'
			];
		}

		if ( ! empty( $response ) ) {
			wp_send_json( $response );
		}
	}

}
