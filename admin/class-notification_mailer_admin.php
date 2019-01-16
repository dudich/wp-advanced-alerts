<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://dev.loc
 * @since      1.0.0
 *
 * @package    Notification-mailer
 * @subpackage Notification-mailer/admin
 */

if ( ! class_exists( 'NotificationModel' ) ) {
	require_once NOTIFICATION_MAILER_DIR_PATH . '/includes/NotificationModel.php';
}

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Notification-mailer
 * @subpackage Notification-mailer/admin
 * @author     Developer <dev@dev.loc>
 */
class Notification_mailer_Admin {

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
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/notification-mailer-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/notification-mailer-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function templates_set_screen_options( $status, $option, $value ) {
		return ( $option == 'templates_per_page' ) ? (int) $value : $status;
	}

	public function templates_settings() {
		register_setting( 'templates-options', 'templates-general', array( $this, 'sanitize_callback' ) );

		add_settings_section( 'templates-options', __( 'General Settings', NM_DOMAIN ), '', 'templates-options' );

		add_settings_field( 'redirect_url', __( 'URL for redirect', NM_DOMAIN ), array(
			$this,
			'redirect_url'
		), 'templates-options', 'templates-options' );
	}

	public function redirect_url() {
		$val = get_option( 'templates-general' );
		$val = $val ? $val['redirect_url'] : null;
		?>
		<input type="url" name="templates-general[redirect_url]" id="redirect_url" value="<?php echo esc_attr( $val ); ?>"
		       placeholder="https://example.com/example-page/"/>
		<br/>
		<span class="description"><?php _e( 'Enter a full page URL', NM_DOMAIN ); ?></span>
		<?php
	}

	public function sanitize_callback( $options ) {

		foreach ( $options as $name => & $val ) {

			if ( $name == 'checkbox' ) {
				$val = intval( $val );
			}

			if ( $name == 'templates_statuses' ) {
				$val = sanitize_textarea_field( $val );
			}
		}

		return $options;
	}

	/**
	 *  admin_menu
	 */
	public function templates_admin_menu() {
		add_menu_page( __( 'Templates', NM_DOMAIN ), __( 'Templates', NM_DOMAIN ), 'manage_options', 'templates', array(
			$this,
			'admin_menu_templates_page'
		), 'dashicons-email', '88.025' );

//		add_submenu_page( 'templates-options', __( 'Templates Settings', NM_DOMAIN ), __( 'Settings', NM_DOMAIN ), 'manage_options', 'templates-options' );
		$hook_templates = add_submenu_page( 'templates-options', __( 'Templates', NM_DOMAIN ), __( 'Templates', NM_DOMAIN ), 'manage_options', 'templates', array(
			$this,
			'admin_menu_templates_page'
		) );

		add_action( "load-$hook_templates", array( $this, 'templates_list_table_load' ) );
	}

	/**
	 *  admin_menu_page callback function for render plugin Settings page
	 */
	public function admin_menu_settings_page() {
		?>
		<div class="wrap">
			<h1><?php echo get_admin_page_title(); ?></h1>

			<form action="options.php" method="POST">
				<?php
				settings_fields( 'templates-options' );
				do_settings_sections( 'templates-options' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 *  admin_menu_page callback function for render plugin Promotions page
	 */
	public function admin_menu_templates_page() {
		if ( isset( $_GET['action'] ) && 'add_new' == $_GET['action'] ) {
			$this->add_template();
		} elseif ( isset( $_GET['action'] ) && 'edit' == $_GET['action'] && isset( $_GET['template_id'] ) && (int) $_GET['template_id'] > 0 ) {
			$this->edit_template( (int) $_GET['template_id'] );
		} else {
			$this->templates_list();
		}
	}

	public function templates_list_table_load() {
		require_once NOTIFICATION_MAILER_DIR_PATH . '/admin/class-templates_list_table.php';
		$GLOBALS['Templates_List_Table'] = new Templates_List_Table();
	}

	public function templates_list() {
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php echo get_admin_page_title(); ?></h1>
			<a href="<?php echo admin_url( 'admin.php?page=templates&action=add_new' ); ?>"
			   class="page-title-action"><?php _e( 'Add New', NM_DOMAIN ); ?></a>
			<hr class="wp-header-end">
			<div id="ajax-response"></div>

			<?php
			echo '<form class="search-form wp-clearfix" action="" method="GET">';
			echo '<input type="hidden" name="page" value="templates"/>';
			$GLOBALS['Templates_List_Table']->search_box( __( 'search by name', NM_DOMAIN ), 'template' );
			echo '</form>';

			echo '<form action="" method="POST">';
			$GLOBALS['Templates_List_Table']->display();
			echo '</form>';
			?>
		</div>
		<?php
	}

	public function create_template() {
		if ( isset( $_POST['action'] ) && 'createtemplate' == $_POST['action'] ) {
			check_admin_referer( 'create-template', '_wpnonce_create-template' );

			$creating = isset( $_POST['createtemplate'] );

			$new_template = array(
				'template_name'        => $creating && isset( $_POST['template_name'] ) ? wp_unslash( $_POST['template_name'] ) : '',
				'subject'              => $creating && isset( $_POST['subject'] ) ? ( $_POST['subject'] ) : '',
				'template_body'        => $creating && isset( $_POST['template_body'] ) ? wp_unslash( $_POST['template_body'] ) : '',
				'description'          => $creating && isset( $_POST['description'] ) ? wp_unslash( $_POST['description'] ) : '',
				'destination_type_id'  => $creating && isset( $_POST['destination_type_id'] ) ? (int) $_POST['destination_type_id'] : 1,
				'notification_type_id' => $creating && isset( $_POST['notification_type_id'] ) ? (int) $_POST['notification_type_id'] : 1,
			);

			$result = NotificationModel::addNewTemplate( $new_template );

			if ( ! empty( $result['template_id'] ) ) {

				$redirect = admin_url( 'admin.php?page=templates&action=edit&template_id=' . $result['template_id'] );
				echo ' <script>document.location.href = "' . $redirect . '";</script> ';
			}

			if ( ! empty( $result['error'] ) || ! empty( $result['warning'] ) ) {
				$this->show_notices( $result );
			}
		}
	}

	public function add_template() {
		wp_enqueue_script( 'wp-ajax-response' );
		?>
		<div class="wrap">
			<h1><?php _e( 'Add New Template', NM_DOMAIN ); ?></h1>
			<?php $this->create_template(); ?>
			<div id="ajax-response"></div>

			<form method="post" name="createtemplate" id="createtemplate" class="validate" novalidate="novalidate">
				<input name="action" type="hidden" value="createtemplate"/>
				<?php wp_nonce_field( 'create-template', '_wpnonce_create-template' );

				$this->render_template_form_table();

				submit_button( __( 'Add New Template', NM_DOMAIN ), 'primary', 'createtemplate', true, array( 'id' => 'createtemplatesub' ) ); ?>

			</form>

		</div>
		<?php
	}

	public function edit_template( $template_id = 0 ) {
		if ( isset( $_POST['action'] ) && 'updatetemplate' == $_POST['action'] ) {
			check_admin_referer( 'update-template', '_wpnonce_update-template' );

			$new_template = array(
				'template_id'          => isset( $template_id ) ? wp_unslash( $template_id ) : 0,
				'template_name'        => isset( $_POST['template_name'] ) ? wp_unslash( $_POST['template_name'] ) : '',
				'subject'              => isset( $_POST['subject'] ) ? ( $_POST['subject'] ) : '',
				'template_body'        => isset( $_POST['template_body'] ) ? wp_unslash( $_POST['template_body'] ) : '',
				'description'          => isset( $_POST['description'] ) ? wp_unslash( $_POST['description'] ) : '',
				'destination_type_id'  => isset( $_POST['destination_type_id'] ) ? (int) $_POST['destination_type_id'] : 1,
				'notification_type_id' => isset( $_POST['notification_type_id'] ) ? (int) $_POST['notification_type_id'] : 1,
			);

			$result = NotificationModel::updateTemplate( $new_template );

			$this->show_notices( $result );
		}

		if ( isset( $_GET['action'] ) && 'edit' == $_GET['action'] ) {
			wp_enqueue_script( 'wp-ajax-response' );

			$template_data = NotificationModel::getTemplateById( $template_id, ARRAY_A ); ?>
			<div class="wrap">
				<h1><?php _e( 'Edit Template', NM_DOMAIN ); ?></h1>
				<div id="ajax-response"></div>

				<form method="post" name="updatetemplate" id="updatetemplate" class="validate" novalidate="novalidate">
					<input name="action" type="hidden" value="updatetemplate"/>
					<?php wp_nonce_field( 'update-template', '_wpnonce_update-template' );

					$this->render_template_form_table( $template_data );

					submit_button( __( 'Update Template', NM_DOMAIN ), 'primary', 'updatetemplate', false, array( 'id' => 'updatetemplatesub' ) ); ?>
					<a href="<?php echo admin_url( 'admin.php?page=templates&action=add_new' ); ?>"
					   class="button button-primary"><?php _e( 'Add New', NM_DOMAIN ); ?></a>

				</form>
			</div>
			<?php
		}
	}

	/**
	 * Render form fields for add/edit Promotion
	 *
	 * @param array $template_data
	 */
	public function render_template_form_table( $template_data = array() ) {

		$template = array(
			'template_id'          => isset( $template_data['id'] ) ? wp_unslash( $template_data['id'] ) : 0,
			'template_name'        => isset( $template_data['template_name'] ) ? wp_unslash( $template_data['template_name'] ) : '',
			'subject'              => isset( $template_data['subject'] ) ? ( $template_data['subject'] ) : '',
			'template_body'        => isset( $template_data['template_body'] ) ? wp_unslash( $template_data['template_body'] ) : '',
			'description'          => isset( $template_data['description'] ) ? wp_unslash( $template_data['description'] ) : '',
			'destination_type_id'  => isset( $template_data['destination_type_id'] ) ? (int) $template_data['destination_type_id'] : '',
			'notification_type_id' => isset( $template_data['notification_type_id'] ) ? (int) $template_data['notification_type_id'] : 1,
		);

		$destination_types       = NotificationModel::getDestinationTypes();
		$destination_type_select = '<select name="destination_type_id" id="destination_type_id" required="required">';
		if ( ! empty( $destination_types ) ) {
			foreach ( $destination_types as $destination_type ) {
				$destination_type_select .= '<option value="' . $destination_type->id . '" ' . selected( $template['destination_type_id'], $destination_type->id, false ) . '>' . $destination_type->name . '</option>';
			}
		}
		$destination_type_select .= '</select>';

		$notification_types       = NotificationModel::getNotificationTypes();
		$notification_type_select = '<select name="notification_type_id" id="notification_type_id" required="required">';
		if ( ! empty( $notification_types ) ) {
			foreach ( $notification_types as $notification_type ) {
				$notification_type_select .= '<option value="' . $notification_type->id . '" ' . selected( $template['notification_type_id'], $notification_type->id, false ) . '>' . $notification_type->name . '</option>';
			}
		}
		$notification_type_select .= '</select>';
		?>
		<table class="form-table">
			<tr class="form-field form-required">
				<th scope="row">
					<label for="template_name"><?php _e( 'Template Name', NM_DOMAIN ); ?>
						<span class="description"><?php _e( '(required)', NM_DOMAIN ); ?></span>
					</label>
				</th>
				<td>
					<input name="template_name" type="text" id="template_name" value="<?php echo $template['template_name']; ?>"
					       aria-required="true"
					       autocapitalize="none"
					       maxlength="60"/>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row">
					<label for="subject"><?php _e( 'Subject', NM_DOMAIN ); ?> </label>
				</th>
				<td>
					<input name="subject" type="text" id="subject" value="<?php echo $template['subject']; ?>"
					       autocomplete="off"/>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row">
					<label for="template_body"><?php _e( 'Template Body', NM_DOMAIN ); ?> </label>
				</th>
				<td>
					<?php wp_editor( $template['template_body'], 'template_body', array(
						'wpautop'          => 1,
						'media_buttons'    => 1,
						'textarea_name'    => 'template_body',
						'textarea_rows'    => 10,
						'tabindex'         => null,
						'editor_css'       => '',
						'editor_class'     => '',
						'teeny'            => 0,
						'dfw'              => 0,
						'tinymce'          => 1,
						'quicktags'        => 1,
						'drag_drop_upload' => false
					) ); ?>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row">
					<label for="description"><?php _e( 'Description', NM_DOMAIN ); ?></label>
				</th>
				<td>
					<input name="description" type="text" id="description" value="<?php echo $template['description']; ?>"/><br/>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row">
					<label for="destination_type_id"><?php _e( 'Destination Type', NM_DOMAIN ); ?>
						<span class="description"><?php _e( '(required)', NM_DOMAIN ); ?></span>
					</label>
				</th>
				<td class="destination-type-select">
					<?php echo $destination_type_select; ?>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row">
					<label for="notification_type_id"><?php _e( 'Notification Type', NM_DOMAIN ); ?>
						<span class="description"><?php _e( '(required)', NM_DOMAIN ); ?></span>
					</label>
				</th>
				<td class="notification-type-select">
					<?php echo $notification_type_select; ?>
				</td>
			</tr>
		</table>
		<?php
	}

	public function show_notices( $notice = array() ) {

		$notices = array(
			'success' => isset( $notice['success'] ) ? $notice['success'] : '',
			'error'   => isset( $notice['error'] ) ? $notice['error'] : '',
			'warning' => isset( $notice['warning'] ) ? $notice['warning'] : '',
			'info'    => isset( $notice['info'] ) ? $notice['info'] : '',
		);

		if ( ! empty( $notices ) ) {

			if ( is_array( $notices ) ) {
				foreach ( $notices as $key => $val ) {
					if ( ! empty( $val ) ) {
						echo '<div class="notice notice-' . $key . ' is-dismissible">';
						if ( is_array( $val ) ) {
							echo '<ul>';
							foreach ( $val as $value ) {
								echo "<li>$value</li>";
							}
							echo '</ul>';
						} else {
							echo "<p>$val</p>";
						}
						echo '</div>';
					}
				}
			} else {
				echo '
						<div class="notice notice-info is-dismissible">
							<p>' . $notices . '</p>
						</div>
					';
			}

		}
	}

	/**--------------------------------------------------------------------------------------
	 *  admin_head
	 *-------------------------------------------------------------------------------------*/
	public function templates_admin_head() {
		?>
		<style type="text/css">
		</style>
		<?php
	}

}
