<?php

/**
 * Fired during plugin activation
 *
 * @link       http://dev.loc
 * @since      1.0.0
 *
 * @package    Notification-mailer
 * @subpackage Notification-mailer/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Notification-mailer
 * @subpackage Notification-mailer/includes
 * @author     Developer <dev@dev.loc>
 */
require_once( 'NotificationModel.php' );

class Notification_mailer_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */

	private static $db;

	public static function activate() {
		global $wpdb;
		self::$db   = $wpdb;
		$table_name = $wpdb->prefix . 'notification_log';

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			$contractorInvitationModel = new NotificationModel();
			$contractorInvitationModel->createTables();
		}
	}

}
