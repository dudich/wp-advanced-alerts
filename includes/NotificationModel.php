<?php
require_once 'InitialFillNotificationTemplateTable.php';

class NotificationModel {
	protected $db;
	protected $notification_log_table;
	protected $notification_types_table;
	protected $notification_templates;
	protected $notification_destination_types_table;

	public function __construct() {
		global $wpdb;
		$this->db                             = $wpdb;
		$wpdb->notification_templates         = $wpdb->prefix . 'notification_templates';
		$wpdb->notification_log               = $wpdb->prefix . 'notification_log';
		$wpdb->notification_types             = $wpdb->prefix . 'notification_types';
		$wpdb->notification_destination_types = $wpdb->prefix . 'notification_destination_types';

		$this->notification_log_table               = $this->db->prefix . 'notification_log';
		$this->notification_types_table             = $this->db->prefix . 'notification_types';
		$this->notification_templates               = $this->db->prefix . 'notification_templates';
		$this->notification_destination_types_table = $this->db->prefix . 'notification_destination_types';
	}

	/**
	 * Create tables for  mailing system
	 * @return array|void
	 */
	public function createTables() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$charset_collate = "DEFAULT CHARACTER SET {$this->db->charset} COLLATE {$this->db->collate}";
		$sql             = "CREATE TABLE IF NOT EXISTS {$this->notification_log_table} (
			id int unsigned NOT NULL auto_increment,
			template_id int unsigned NOT NULL,
			recipient_id int unsigned NOT NULL,
			post_type varchar(64)  DEFAULT NULL,
			post_id int unsigned DEFAULT NULL,
			created_at timestamp default current_timestamp,
			PRIMARY KEY (id)
			)
			{$charset_collate};";

		$result = dbDelta( $sql );

		$sql2 = "CREATE TABLE IF NOT EXISTS {$this->notification_types_table} (
			id int unsigned NOT NULL auto_increment,
			name varchar(255) NOT NULL,
			description varchar(255) DEFAULT NULL,
			PRIMARY KEY (id)
			)
			{$charset_collate};";

		$result2 = dbDelta( $sql2 );

		$sql3 = "CREATE TABLE IF NOT EXISTS {$this->notification_templates} (
			id int unsigned NOT NULL auto_increment,
		    template_name VARCHAR(128) NOT NULL,
			notification_type_id INT NOT NULL,
			destination_type_id INT NOT NULL,
			subject varchar(255)  DEFAULT NULL,
			template_body text DEFAULT NULL,
			description varchar(255) DEFAULT NULL,
			PRIMARY KEY (id)
			)
			{$charset_collate};";

		$result3 = dbDelta( $sql3 );

		$sql4 = "CREATE TABLE IF NOT EXISTS {$this->notification_destination_types_table} (
			id int unsigned NOT NULL auto_increment,
			name varchar(255) NOT NULL,
			description varchar(255) DEFAULT NULL,
			PRIMARY KEY (id)
			)
			{$charset_collate};";

		$result4 = dbDelta( $sql4 );

		// fill destination_types table if it's empty

		$count = $this->db->get_var( "SELECT COUNT('id') FROM {$this->notification_types_table};" );

		if ( $count == 0 ) {
			$types = [
				[
					'name'        => 'email',
					'description' => 'Email Notification',
				],

			];
			foreach ( $types as $type ) {
				$res = $this->db->insert( $this->notification_types_table, $type, [ '%s', '%s' ] );
			}

		}

		// fill notification table if it's empty

		$count = $this->db->get_var( "SELECT COUNT('id') FROM {$this->notification_destination_types_table};" );

		if ( $count == 0 ) {
			$types = [
				[
					'name'        => 'Building Emails',
					'description' => 'Buiding Notification',
				],
				[
					'name'        => 'Contractor Emails',
					'description' => 'Contractor Notification',
				],
				[
					'name'        => 'Emails sent from functions.php',
					'description' => 'Emails sent from functions.php',
				],
				[
					'name'        => 'Old core Emails',
					'description' => 'Old core Emails sent from Fre_Mailing and AE_Mailing classes',
				],
			];
			foreach ( $types as $type ) {
				$res = $this->db->insert( $this->notification_destination_types_table, $type, [ '%s', '%s' ] );
			}

		}

		$countNotificationTemplates = $this->db->get_var( "SELECT COUNT('id') FROM {$this->notification_templates};" );

		if ( $countNotificationTemplates == 0 ) {
			$fillTable = new InitialFillNotificationTemplateTable();
			$result    = $fillTable->initialFillNotificationTemplatesTable();
		}

//		return $result;
	}

	/**
	 * @param $user
	 */
	private static function addUserMetaData( $user ) {
		$user_info = get_userdata( $user->ID );
		if ( $user_info ) {
			$roles            = $user_info->roles;
			$user->role       = array_pop( $roles );
			$user->first_name = $user_info->first_name;
			$user->last_name  = $user_info->last_name;
			$user->email      = $user_info->user_email;
		}
	}

	/**
	 * Email 2 (6 hours after sign-up):
	 *
	 * @param integer|string $templateId
	 *
	 * @return array|object|null
	 */
	public static function getUsersAfter6HourAfterSignUp( $templateId ) {

		global $wpdb;

		$wpdb->notification_log = $wpdb->prefix . 'notification_log';
		$query                  = $wpdb->prepare( "
        SELECT $wpdb->users.*
        FROM $wpdb->users 
        LEFT JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id
        LEFT JOIN $wpdb->notification_log ON $wpdb->notification_log.recipient_id = $wpdb->users.ID 
          AND $wpdb->notification_log.template_id = %d
        WHERE $wpdb->usermeta.meta_key = 'wpzu_capabilities'
        AND $wpdb->usermeta.meta_value like %s
        AND $wpdb->users.user_registered BETWEEN DATE_ADD(UTC_TIMESTAMP(), INTERVAL -7 HOUR) AND DATE_ADD(UTC_TIMESTAMP(), INTERVAL -6 HOUR)
        AND $wpdb->notification_log.id is null  
        order by $wpdb->users.ID desc
    ", [ $templateId, '%' . $wpdb->esc_like( 'employer' ) . '%' ] );

		$res = $wpdb->get_results( $query );
		if ( ! empty( $res ) ) {
			foreach ( $res as $user ) {
				self::addUserMetaData( $user );
			}
		}


		return $res;
	}

	/**
	 * Email 3 (no project posted 1 day after sign up)
	 *
	 * @param integer|string $templateId
	 *
	 * @return array|object|null
	 */
	public static function getUsersAfter1DayAfterSignUp( $templateId ) {
		global $wpdb;
		$wpdb->notification_log = $wpdb->prefix . 'notification_log';
		$query                  = $wpdb->prepare( "
        SELECT $wpdb->users.*
        FROM $wpdb->users 
        LEFT JOIN $wpdb->posts ON $wpdb->users.id =  $wpdb->posts.post_author
          AND $wpdb->posts.post_status = 'publish'
          AND $wpdb->posts.post_type = 'project'
        LEFT JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id
        LEFT JOIN $wpdb->notification_log ON $wpdb->notification_log.recipient_id = $wpdb->users.ID 
          AND $wpdb->notification_log.template_id = %d
        WHERE $wpdb->usermeta.meta_key = 'wpzu_capabilities'
        AND $wpdb->usermeta.meta_value like %s
        AND $wpdb->users.user_registered BETWEEN DATE_ADD(UTC_TIMESTAMP(), INTERVAL -2 DAY) AND DATE_ADD(UTC_TIMESTAMP(), INTERVAL -1 DAY)
        AND $wpdb->posts.id is null
        AND $wpdb->notification_log.id is null  
        order by $wpdb->users.ID desc
    ", [ $templateId, '%' . $wpdb->esc_like( 'employer' ) . '%' ] );

		$res = $wpdb->get_results( $query );
		if ( ! empty( $res ) ) {
			foreach ( $res as $user ) {
				self::addUserMetaData( $user );
			}
		}

		return $res;
	}

	/**
	 * Email 4 (no project posted 3 days after sign up):
	 *
	 * @param integer|string $templateId
	 *
	 * @return array|object|null
	 */
	public static function getUsersAfter3DayAfterSignUp( $templateId ) {

		global $wpdb;

		$wpdb->notification_log = $wpdb->prefix . 'notification_log';
		$query                  = $wpdb->prepare( "
        SELECT $wpdb->users.*
        FROM $wpdb->users 
        LEFT JOIN $wpdb->posts ON $wpdb->users.id =  $wpdb->posts.post_author 
        AND $wpdb->posts.post_status = 'publish'
          AND $wpdb->posts.post_type = 'project'
        LEFT JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id
        LEFT JOIN $wpdb->notification_log ON $wpdb->notification_log.recipient_id = $wpdb->users.ID 
          AND $wpdb->notification_log.template_id = %d
        WHERE $wpdb->usermeta.meta_key = 'wpzu_capabilities'
        AND $wpdb->usermeta.meta_value like %s
        AND $wpdb->users.user_registered BETWEEN DATE_ADD(UTC_TIMESTAMP(), INTERVAL -4 DAY) AND DATE_ADD(UTC_TIMESTAMP(), INTERVAL -3 DAY)
        AND $wpdb->posts.id is null
        AND $wpdb->notification_log.id is null  
        order by $wpdb->users.ID desc
    ", [ $templateId, '%' . $wpdb->esc_like( 'employer' ) . '%' ] );

		$res = $wpdb->get_results( $query );

		if ( ! empty( $res ) ) {
			foreach ( $res as $user ) {
				self::addUserMetaData( $user );
			}
		}

		return $res;
	}

	/**
	 * Email 5 (send every 10 days after a user’s last project was posted – also send to users who have not posted any projects at all, every 10 days)
	 *
	 * @param integer|string $templateId
	 *
	 * @return array|object|null
	 */
	public static function getUsersAfter10DayAfterLastProjectPosted( $templateId ) {

		global $wpdb;

		$wpdb->notification_log = $wpdb->prefix . 'notification_log';
		$query                  = $wpdb->prepare( "
        SELECT $wpdb->users.*
        FROM $wpdb->users 
        LEFT JOIN $wpdb->posts ON $wpdb->users.id =  $wpdb->posts.post_author
          AND $wpdb->posts.post_modified > DATE_ADD(UTC_TIMESTAMP(), INTERVAL -10 DAY)
          AND $wpdb->posts.post_status = 'publish'
          AND $wpdb->posts.post_type = 'project'
        LEFT JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id
        LEFT JOIN $wpdb->notification_log ON $wpdb->notification_log.recipient_id = $wpdb->users.ID 
          AND $wpdb->notification_log.template_id = %d
          AND $wpdb->notification_log.created_at > DATE_ADD(UTC_TIMESTAMP(), INTERVAL -10 DAY)
        WHERE $wpdb->usermeta.meta_key = 'wpzu_capabilities'
        AND $wpdb->usermeta.meta_value like %s
        AND $wpdb->posts.id is null
        AND $wpdb->notification_log.id is null
        AND $wpdb->posts.post_date > STR_TO_DATE('12/20/2018', '%s')    
        order by $wpdb->users.ID desc
    ", [ $templateId, '%' . $wpdb->esc_like( 'employer' ) . '%', '%m/%d/%Y' ] );

		$res = $wpdb->get_results( $query );

		if ( ! empty( $res ) ) {
			foreach ( $res as $user ) {
				self::addUserMetaData( $user );
			}
		}

		return $res;
	}

	/**
	 * Email 9 (1 day before bid deadline is reached IF USER RECEIVED 3 BIDS OR LESS):
	 *
	 * @param integer|string $templateId
	 *
	 * @return array|object|null
	 */
	public static function getUsers1DayBeforeBidDeadlineAndLessThen4bids( $templateId ) {
		global $wpdb;

		$wpdb->notification_log = $wpdb->prefix . 'notification_log';

		$query = $wpdb->prepare( "
        SELECT $wpdb->users.*, projects.post_name as project_name, projects.ID as project_id, count(bids.id) as n_bids
        FROM $wpdb->users 
        LEFT JOIN $wpdb->posts as projects ON $wpdb->users.id =  projects.post_author
          AND projects.post_status = 'publish'
          AND projects.post_type = 'project'
        LEFT JOIN $wpdb->posts as bids on bids.post_type ='bid' 
          AND bids.post_parent = projects.ID
          INNER JOIN $wpdb->postmeta as projects_meta
	               on projects.ID = projects_meta.post_id
		               and projects_meta.meta_key = 'bidding_end_date'
        LEFT JOIN $wpdb->notification_log ON $wpdb->notification_log.recipient_id = $wpdb->users.ID 
          AND $wpdb->notification_log.template_id = %d
          AND $wpdb->notification_log.post_id = projects.ID 
        WHERE STR_TO_DATE(projects_meta.meta_value, '%s') BETWEEN UTC_TIMESTAMP() and DATE_ADD(UTC_TIMESTAMP(), INTERVAL +1 DAY)
        AND $wpdb->notification_log.id is null
        group by $wpdb->users.ID, project_id 
        having n_bids < 4
        order by $wpdb->users.ID desc
    ", [ $templateId, '%m/%d/%Y' ] );

		$res = $wpdb->get_results( $query );

		if ( ! empty( $res ) ) {
			foreach ( $res as $user ) {
				self::addUserMetaData( $user );
			}
		}

		return $res;
	}

	/**
	 * Email 10 (1 day before bid deadline is reached IF USER RECEIVED MORE THAN 3 BIDS):
	 *
	 * @param integer|string $templateId
	 *
	 * @return array|object|null
	 */
	public static function getUsers1DayBeforeBidDeadlineAndMoreThen3bids( $templateId ) {
		global $wpdb;

		$wpdb->notification_log = $wpdb->prefix . 'notification_log';

		$query = $wpdb->prepare( "
        SELECT $wpdb->users.*, projects.post_name as project_name, projects.ID as project_id, count(bids.id) as n_bids
        FROM $wpdb->users 
        LEFT JOIN $wpdb->posts as projects ON $wpdb->users.id =  projects.post_author
          AND projects.post_status = 'publish'
          AND projects.post_type = 'project'
        LEFT JOIN $wpdb->posts as bids on bids.post_type ='bid' 
          AND bids.post_parent = projects.ID
          INNER JOIN $wpdb->postmeta as projects_meta
	               on projects.ID = projects_meta.post_id
		               and projects_meta.meta_key = 'bidding_end_date'
        LEFT JOIN $wpdb->notification_log ON $wpdb->notification_log.recipient_id = $wpdb->users.ID 
          AND $wpdb->notification_log.template_id = %d
          AND $wpdb->notification_log.post_id = projects.ID 
        WHERE STR_TO_DATE(projects_meta.meta_value, '%s') BETWEEN UTC_TIMESTAMP() and DATE_ADD(UTC_TIMESTAMP(), INTERVAL +1 DAY)
        AND $wpdb->notification_log.id is null
        group by $wpdb->users.ID, project_id 
        having n_bids > 3
        order by $wpdb->users.ID desc
    ", [ $templateId, '%m/%d/%Y' ] );

		$res = $wpdb->get_results( $query );

		if ( ! empty( $res ) ) {
			foreach ( $res as $user ) {
				self::addUserMetaData( $user );
			}
		}

		return $res;
	}

	/**
	 * Email 11 (1 day after project deadline is reached, no bid chosen):
	 *
	 * @param integer|string $templateId
	 *
	 * @return array|object|null
	 */
	public static function getUsersWithProjectsWithoutAcceptedBids1DayAfterDeadline( $templateId ) {
		global $wpdb;

		$wpdb->notification_log = $wpdb->prefix . 'notification_log';
//      bids.post_status = 'publish' - no accepted bids
		$query = $wpdb->prepare( "
        SELECT $wpdb->users.*, projects.post_name as project_name, projects.ID as project_id, count(bids.id) as n_bids
        FROM $wpdb->users 
        LEFT JOIN $wpdb->posts as projects ON $wpdb->users.id =  projects.post_author
          AND projects.post_status = 'publish'
          AND projects.post_type = 'project'
        LEFT JOIN $wpdb->posts as bids on bids.post_type ='bid' 
          AND bids.post_parent = projects.ID
          AND bids.post_status = 'publish'
          INNER JOIN $wpdb->postmeta as bidding_end_date
	               on projects.ID = bidding_end_date.post_id
		               and bidding_end_date.meta_key = 'bidding_end_date'
          LEFT JOIN $wpdb->postmeta as deadline
	               on projects.ID = deadline.post_id
		               and deadline.meta_key = 'deadline'
        LEFT JOIN $wpdb->notification_log ON $wpdb->notification_log.recipient_id = $wpdb->users.ID 
          AND $wpdb->notification_log.template_id = %d
          AND $wpdb->notification_log.post_id = projects.ID
          AND $wpdb->notification_log.created_at > STR_TO_DATE(IF(deadline.meta_value, deadline.meta_value, bidding_end_date.meta_value), '%s')
        WHERE STR_TO_DATE(IF(deadline.meta_value, deadline.meta_value, bidding_end_date.meta_value), '%s') BETWEEN DATE(DATE_ADD(UTC_TIMESTAMP(), INTERVAL -2 DAY)) and DATE(DATE_ADD(UTC_TIMESTAMP(), INTERVAL -1 DAY))
         AND $wpdb->notification_log.id is null
        group by $wpdb->users.ID, project_id
        HAVING n_bids > 0
        order by $wpdb->users.ID desc
    ", [ $templateId, '%m/%d/%Y', '%m/%d/%Y' ] );

		$res = $wpdb->get_results( $query );

		if ( ! empty( $res ) ) {
			foreach ( $res as $user ) {
				self::addUserMetaData( $user );
			}
		}

		return $res;
	}

	/**
	 * Email 12 (3 days after project deadline is reached, no bid chosen):
	 *
	 * @param integer|string $templateId
	 *
	 * @return array|object|null
	 */
	public static function getUsersWithProjectsWithNoBids3DayAfterDeadline( $templateId ) {
		global $wpdb;

		$wpdb->notification_log = $wpdb->prefix . 'notification_log';
//      bids.post_status = 'publish' - no accepted bids
		$query = $wpdb->prepare( "
        SELECT $wpdb->users.*, projects.post_name as project_name, projects.ID as project_id, count(bids.id) as n_bids
        FROM $wpdb->users 
        LEFT JOIN $wpdb->posts as projects ON $wpdb->users.id =  projects.post_author
          AND projects.post_status = 'publish'
          AND projects.post_type = 'project'
        LEFT JOIN $wpdb->posts as bids on bids.post_type ='bid' 
          AND bids.post_parent = projects.ID
          AND bids.post_status = 'publish'
        INNER JOIN $wpdb->postmeta as bidding_end_date
 	               on projects.ID = bidding_end_date.post_id
		               and bidding_end_date.meta_key = 'bidding_end_date'
        LEFT JOIN $wpdb->postmeta as deadline
	               on projects.ID = deadline.post_id
		               and deadline.meta_key = 'deadline'
        LEFT JOIN $wpdb->notification_log ON $wpdb->notification_log.recipient_id = $wpdb->users.ID 
          AND $wpdb->notification_log.template_id = %d
          AND $wpdb->notification_log.post_id = projects.ID
          AND $wpdb->notification_log.created_at > STR_TO_DATE(IF(deadline.meta_value, deadline.meta_value, bidding_end_date.meta_value), '%s')
        WHERE STR_TO_DATE(IF(deadline.meta_value, deadline.meta_value, bidding_end_date.meta_value), '%s') BETWEEN DATE(DATE_ADD(UTC_TIMESTAMP(), INTERVAL -4 DAY)) and DATE(DATE_ADD(UTC_TIMESTAMP(), INTERVAL -3 DAY))
        AND $wpdb->notification_log.id is null
        group by $wpdb->users.ID, projects.ID
        HAVING n_bids > 0
        order by $wpdb->users.ID desc
    ", [ $templateId, '%m/%d/%Y', '%m/%d/%Y' ] );

		$res = $wpdb->get_results( $query );

		if ( ! empty( $res ) ) {
			foreach ( $res as $user ) {
				self::addUserMetaData( $user );
			}
		}

		return $res;
	}

	/**
	 * get users for Email13
	 * Email 13 (each week after project deadline is reached, no bid chosen)
	 *
	 * @param integer|string $templateId
	 *
	 * @return array|object|null
	 */
	public static function getUsersWithProjectsWithNoBidsEveryWeekAfterDeadline( $templateId ) {
		global $wpdb;

		$query = $wpdb->prepare( "
            SELECT $wpdb->users.*,  projects.post_name as project_name, projects.ID as project_id, count(bids.id) as n_bids
            FROM $wpdb->users 
            LEFT JOIN $wpdb->posts as projects ON $wpdb->users.id =  projects.post_author
              AND projects.post_status = 'publish'
              AND projects.post_type = 'project'
            LEFT JOIN $wpdb->posts as bids on bids.post_type ='bid' 
              AND bids.post_parent = projects.ID
              AND bids.post_status = 'publish'
            INNER JOIN $wpdb->postmeta as bidding_end_date
                 on projects.ID = bidding_end_date.post_id
                   and bidding_end_date.meta_key = 'bidding_end_date'
            LEFT JOIN $wpdb->postmeta as deadline
                 on projects.ID = deadline.post_id
                   and deadline.meta_key = 'deadline'
            LEFT JOIN $wpdb->notification_log as log ON log.recipient_id = $wpdb->users.ID 
              AND log.template_id = %d
              AND log.post_id = projects.ID
              AND log.created_at >= DATE_ADD(UTC_TIMESTAMP(), INTERVAL -1 WEEK ) 
            WHERE STR_TO_DATE(IF(deadline.meta_value, deadline.meta_value, bidding_end_date.meta_value), '%s') < DATE(DATE_ADD(UTC_TIMESTAMP(), INTERVAL -1 WEEK ) )
              AND log.id is null
            AND projects.post_date > STR_TO_DATE('12/20/2018', '%s')  
            GROUP BY projects.ID
            HAVING n_bids > 0
            ORDER BY  $wpdb->users.ID ASC
        ", [ $templateId, '%m/%d/%Y', '%m/%d/%Y' ] );

		$res = $wpdb->get_results( $query );

		if ( ! empty( $res ) ) {
			foreach ( $res as $user ) {
				self::addUserMetaData( $user );
			}
		}

		return $res;
	}


	/**
	 * Email 15 (1 week after winning bid is chosen):
	 *
	 * @param integer|string $templateId
	 *
	 * @return array|object|null
	 */
	public function getUsers1WeekAfterWiningBidChosen( $templateId ) {
		global $wpdb;
		$wpdb->notification_log = $wpdb->prefix . 'notification_log';
		$query                  = $wpdb->prepare( "
            SELECT $wpdb->users.*,  projects.post_name as project_name,  projects.ID as project_id 
            FROM $wpdb->users 
            INNER JOIN $wpdb->posts as projects ON $wpdb->users.id =  projects.post_author
              AND projects.post_status = 'close'
              AND projects.post_type = 'project'
              AND projects.post_modified BETWEEN DATE_ADD(UTC_TIMESTAMP(), INTERVAL -2 WEEK ) and DATE_ADD(UTC_TIMESTAMP(), INTERVAL -1 WEEK) 
            LEFT JOIN $wpdb->notification_log ON $wpdb->notification_log.recipient_id = $wpdb->users.ID 
              AND $wpdb->notification_log.template_id = %d
              AND $wpdb->notification_log.post_id = projects.ID
            WHERE $wpdb->notification_log.id is null 
            AND projects.post_date > STR_TO_DATE('12/20/2018', '%s')  
        ", [ $templateId, '%m/%d/%Y' ] );

		$res = $wpdb->get_results( $query );
		if ( ! empty( $res ) ) {
			foreach ( $res as $user ) {
				self::addUserMetaData( $user );
			}
		}

		return $res;
	}

	//======================================================== Contractors ==========================================================

	/**
	 * Email 2: 1 day after sign up
	 *
	 * @param integer|string $templateId
	 *
	 * @return array|object|null
	 */
	public function getContractors1DayAfterSignUp( $templateId ) {
		global $wpdb;

		$wpdb->notification_log = $wpdb->prefix . 'notification_log';
		$query                  = $wpdb->prepare( "
        SELECT $wpdb->users.*
        FROM $wpdb->users 
        LEFT JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id
        LEFT JOIN $wpdb->notification_log ON $wpdb->notification_log.recipient_id = $wpdb->users.ID 
          AND $wpdb->notification_log.template_id = %d
        WHERE $wpdb->usermeta.meta_key = 'wpzu_capabilities'
        AND $wpdb->usermeta.meta_value like %s
        AND $wpdb->users.user_registered BETWEEN DATE_ADD(UTC_TIMESTAMP(), INTERVAL -2 DAY) AND DATE_ADD(UTC_TIMESTAMP(), INTERVAL -1 DAY)
        AND $wpdb->notification_log.id is null  
        order by $wpdb->users.ID desc
    ", [ $templateId, '%' . $wpdb->esc_like( 'freelancer' ) . '%' ] );

		$res = $wpdb->get_results( $query );
		if ( ! empty( $res ) ) {
			foreach ( $res as $user ) {
				self::addUserMetaData( $user );
			}
		}

		return $res;
	}

	/**
	 * Email 4: 2 days after bid invitation and no bid from vendor
	 *
	 * @param integer|string $templateId
	 *
	 * @return array|object|null
	 */
	public static function getContractorsWithNoBid2dayAfterInvitation( $templateId ) {
		global $wpdb;

		$wpdb->notification_log = $wpdb->prefix . 'notification_log';
		$query                  = $wpdb->prepare( "
        SELECT projects.* , invited_list.meta_value as contractor_list
        FROM $wpdb->posts as projects 
        LEFT JOIN $wpdb->notification_log 
            ON ($wpdb->notification_log.post_type = 'project'
            AND $wpdb->notification_log.post_id = projects.ID               
            AND $wpdb->notification_log.template_id = %d )
        LEFT JOIN $wpdb->postmeta as invited_list ON invited_list.post_id = projects.ID
        WHERE projects.post_status = 'publish'
            AND projects.post_type = 'project'
            AND projects.post_modified BETWEEN DATE_ADD(UTC_TIMESTAMP(), INTERVAL -3 day) and DATE_ADD(UTC_TIMESTAMP(), INTERVAL -2 day)
            AND invited_list.meta_key = 'invited_users_list'
            AND $wpdb->notification_log.id is null 
        order by projects.ID desc
    ", [ $templateId ] );

		$projects = $wpdb->get_results( $query );
		$users    = [];
		if ( ! empty( $projects ) ) {
			foreach ( $projects as $project ) {
				$list    = maybe_unserialize( $project->contractor_list );
				$listStr = '(' . implode( ',', $list ) . ')';
				$query1  = $wpdb->prepare( "
                SELECT users.* FROM $wpdb->users as users
                LEFT JOIN $wpdb->posts as bids ON bids.post_author = users.ID
                  AND bids.post_type = 'bid'
                  AND bids.post_status = 'publish'
                  AND bids.post_parent = {$project->ID}
                WHERE users.ID IN {$listStr}
                  AND bids.ID is null
            ", [] );

				$users[ $project->ID ] = [
					'users'        => [],
					'project_name' => get_project_title_for( $project->ID, 'freelancer' ),
					'project_id'   => $project->ID,
				];

				$invitedArr = $wpdb->get_results( $query1 );

				foreach ( $invitedArr as $invited ) {
					self::addUserMetaData( $invited );
				}
				$users[ $project->ID ]['users'] = $invitedArr;

			}
		}

		return $users;
	}

	/**
	 * Email 5: 1 week after bid invitation and no bid
	 *
	 * @param integer|string $templateId
	 *
	 * @return array|object|null
	 */

	public static function getContractorsWithNoBid1WeekAfterInvitation( $templateId ) {
		global $wpdb;

		$wpdb->notification_log = $wpdb->prefix . 'notification_log';
		$query                  = $wpdb->prepare( "
        SELECT projects.* , invited_list.meta_value as contractor_list, bidding_end_date_meta.meta_value as bidding_end_date
        FROM $wpdb->posts as projects 
        LEFT JOIN $wpdb->notification_log 
            ON ($wpdb->notification_log.post_type = 'project'
            AND $wpdb->notification_log.post_id = projects.ID               
            AND $wpdb->notification_log.template_id = %d )
        LEFT JOIN $wpdb->postmeta as bidding_end_date_meta ON bidding_end_date_meta.post_id = projects.ID
        LEFT JOIN $wpdb->postmeta as invited_list ON invited_list.post_id = projects.ID
        WHERE projects.post_status = 'publish'
            AND projects.post_type = 'project'
            AND projects.post_modified BETWEEN DATE_ADD(UTC_TIMESTAMP(), INTERVAL -8 day) and DATE_ADD(UTC_TIMESTAMP(), INTERVAL -7 day)
            AND invited_list.meta_key = 'invited_users_list'
            AND bidding_end_date_meta.meta_key = 'bidding_end_date'
            AND $wpdb->notification_log.id is null 
            AND projects.post_date > STR_TO_DATE('12/20/2018', '%s')  
        order by projects.ID desc
    ", [ $templateId, '%m/%d/%Y' ] );

		$projects = $wpdb->get_results( $query );
		$users    = [];
		if ( ! empty( $projects ) ) {
			foreach ( $projects as $project ) {
				$list    = maybe_unserialize( $project->contractor_list );
				$listStr = '(' . implode( ',', $list ) . ')';
				$query1  = $wpdb->prepare( "
                SELECT users.* FROM $wpdb->users as users
                LEFT JOIN $wpdb->posts as bids ON bids.post_author = users.ID
                  AND bids.post_type = 'bid'
                  AND bids.post_status = 'publish'
                  AND bids.post_parent = {$project->ID}
                WHERE users.ID IN {$listStr}
                  AND bids.ID is null
            ", [] );

				$users[ $project->ID ] = [
					'users'            => [],
					'project_name'     => get_project_title_for( $project->ID, 'freelancer' ),
					'project_id'       => $project->ID,
					'bidding_end_date' => $project->bidding_end_date
				];

				$invitedArr = $wpdb->get_results( $query1 );

				foreach ( $invitedArr as $invited ) {
					self::addUserMetaData( $invited );
				}
				$users[ $project->ID ]['users'] = $invitedArr;

			}
		}

		return $users;
	}

	/**
	 * Email 6: 1 day before bid deadline reached
	 *
	 * @param integer|string $templateId
	 *
	 * @return array|object|null
	 */

	public static function getContractorsWithNoBid1DayBeforeBidDeadline( $templateId ) {
		global $wpdb;

		$wpdb->notification_log = $wpdb->prefix . 'notification_log';
		$query                  = $wpdb->prepare( "
        SELECT projects.* , invited_list.meta_value as contractor_list, bidding_end_date_meta.meta_value as bidding_end_date
        FROM $wpdb->posts as projects 
        LEFT JOIN $wpdb->notification_log 
            ON ($wpdb->notification_log.post_type = 'project'
            AND $wpdb->notification_log.post_id = projects.ID               
            AND $wpdb->notification_log.template_id = %d )
        LEFT JOIN $wpdb->postmeta as bidding_end_date_meta ON bidding_end_date_meta.post_id = projects.ID
        LEFT JOIN $wpdb->postmeta as invited_list ON invited_list.post_id = projects.ID
        WHERE projects.post_status = 'publish'
            AND projects.post_type = 'project'
            AND invited_list.meta_key = 'invited_users_list'
            AND bidding_end_date_meta.meta_key = 'bidding_end_date'
            AND STR_TO_DATE(bidding_end_date_meta.meta_value, '%s') BETWEEN UTC_TIMESTAMP() and DATE_ADD(UTC_TIMESTAMP(), INTERVAL +1 day)
            AND $wpdb->notification_log.id is null 
        order by projects.ID desc
    ", [ $templateId, '%m/%d/%Y' ] );

		$projects = $wpdb->get_results( $query );
		$users    = [];
		if ( ! empty( $projects ) ) {
			foreach ( $projects as $project ) {
				$list    = maybe_unserialize( $project->contractor_list );
				$listStr = '(' . implode( ',', $list ) . ')';
				$query1  = $wpdb->prepare( "
                SELECT users.* FROM $wpdb->users as users
                LEFT JOIN $wpdb->posts as bids ON bids.post_author = users.ID
                  AND bids.post_type = 'bid'
                  AND bids.post_status = 'publish'
                  AND bids.post_parent = {$project->ID}
                WHERE users.ID IN {$listStr}
                  AND bids.ID is null
            ", [] );

				$users[ $project->ID ] = [
					'users'            => [],
					'project_name'     => get_project_title_for( $project->ID, 'freelancer' ),
					'project_id'       => $project->ID,
					'bidding_end_date' => $project->bidding_end_date
				];

				$invitedArr = $wpdb->get_results( $query1 );

				foreach ( $invitedArr as $invited ) {
					self::addUserMetaData( $invited );
				}
				$users[ $project->ID ]['users'] = $invitedArr;

			}
		}

		return $users;
	}

	/**
	 * Email 7: 3 days after bid deadline reached and no bid is selected (sent to bidders only)
	 *
	 * @param integer|string $templateId
	 *
	 * @return array|object|null
	 */
	public static function getContractorsWithProjectWithNoBids3DayAfterDeadline( $templateId ) {
		global $wpdb;

		$query = $wpdb->prepare( "
					SELECT bids.post_author AS ID,
			       bids.post_parent AS project_id,
			       projects.post_title AS project_name
					FROM $wpdb->posts AS bids
					  INNER JOIN $wpdb->posts AS projects
					    ON projects.ID = bids.post_parent
					    AND projects.post_type = 'project'
					    AND projects.post_status = 'publish'
					  INNER JOIN $wpdb->users ON $wpdb->users.ID = bids.post_author  
					  INNER JOIN $wpdb->postmeta as projects_meta
					    ON projects_meta.post_id = projects.ID
					    AND projects_meta.meta_key = 'bidding_end_date'
					    AND STR_TO_DATE(projects_meta.meta_value, %s) BETWEEN DATE_ADD(UTC_TIMESTAMP(), INTERVAL -4 DAY ) AND DATE_ADD(UTC_TIMESTAMP(), INTERVAL -3 DAY)
					  LEFT JOIN $wpdb->notification_log AS log
					    ON log.recipient_id = bids.post_author
					    AND log.template_id = %d
					    AND log.post_id = projects.ID
					WHERE bids.post_type = 'bid'
					  AND bids.post_status = 'publish'
					  AND log.id IS NULL
					ORDER BY ID ASC;
        ", [ '%m/%d/%Y', $templateId ] );

//        /* Structure of the result array element */
//        $res_element = {
//          'ID'           => (integer) bidder_id,
//          'project_id'   => (integer) project_id,
//          'project_name' => (string) project_title
//        }

		$res = $wpdb->get_results( $query );

		if ( ! empty( $res ) ) {
			foreach ( $res as $user ) {
				self::addUserMetaData( $user );
			}
		}

		return $res;
	}

	/**
	 * Email 8: 1 week after bid deadline reached and no bid is selected (sent to bidders only)
	 *
	 * @param integer|string $templateId
	 *
	 * @return array|object|null
	 */
	public static function getContractorsWithProjectWithNoBids1WeekAfterDeadline( $templateId ) {
		global $wpdb;

		$query = $wpdb->prepare( "
					SELECT bids.post_author AS ID,
			       bids.post_parent AS project_id,
			       projects.post_title AS project_name
					FROM $wpdb->posts AS bids
					  INNER JOIN $wpdb->posts AS projects
					    ON projects.ID = bids.post_parent
					    AND projects.post_type = 'project'
					    AND projects.post_status = 'publish'
					  INNER JOIN $wpdb->users ON $wpdb->users.ID = bids.post_author  
					  INNER JOIN $wpdb->postmeta as projects_meta
					    ON projects_meta.post_id = projects.ID
					    AND projects_meta.meta_key = 'bidding_end_date'
					    AND STR_TO_DATE(projects_meta.meta_value, %s) BETWEEN DATE_ADD(UTC_TIMESTAMP(), INTERVAL -8 DAY ) AND DATE_ADD(UTC_TIMESTAMP(), INTERVAL -7 DAY)
					  LEFT JOIN $wpdb->notification_log AS log
					    ON log.recipient_id = bids.post_author
					    AND log.template_id = %d
					    AND log.post_id = projects.ID
					WHERE bids.post_type = 'bid'
					  AND bids.post_status = 'publish'
					  AND log.id IS NULL
					  AND projects.post_date > STR_TO_DATE('12/20/2018', '%s')  
					ORDER BY ID ASC;
        ", [ '%m/%d/%Y', $templateId, '%m/%d/%Y' ] );

//        /* Structure of the result array element */
//        $res_element = {
//          'ID'           => (integer) bidder_id,
//          'project_id'   => (integer) project_id,
//          'project_name' => (string) project_title
//        }

		$res = $wpdb->get_results( $query );
		if ( ! empty( $res ) ) {
			foreach ( $res as $user ) {
				self::addUserMetaData( $user );
			}
		}

		return $res;
	}

	/**
	 * Email 9: 2 weeks (and every week thereafter) after bid deadline reached and no bid is selected (sent to bidders only)
	 *
	 * @param integer|string $templateId
	 *
	 * @return array|object|null
	 */
	public static function getContractorsWithProjectWithNoBids2WeekAfterDeadline( $templateId ) {
		global $wpdb;

		$query = $wpdb->prepare( "
					SELECT bids.post_author AS ID,
			       bids.post_parent AS project_id,
			       projects.post_title AS project_name
					FROM $wpdb->posts AS bids
					  INNER JOIN $wpdb->posts AS projects
					    ON projects.ID = bids.post_parent
					    AND projects.post_type = 'project'
					    AND projects.post_status = 'publish'
					  INNER JOIN $wpdb->users ON $wpdb->users.ID = bids.post_author  
					  INNER JOIN $wpdb->postmeta as projects_meta
					    ON projects_meta.post_id = projects.ID
					    AND projects_meta.meta_key = 'bidding_end_date'
					    AND STR_TO_DATE(projects_meta.meta_value, %s) < DATE_ADD(UTC_TIMESTAMP(), INTERVAL -14 DAY)
					  LEFT JOIN $wpdb->notification_log AS log
					    ON log.recipient_id = bids.post_author
					    AND log.template_id = %d
					    AND log.post_id = projects.ID
					    AND log.created_at > DATE_ADD(UTC_TIMESTAMP(), INTERVAL -7 DAY)
					WHERE bids.post_type = 'bid'
					  AND bids.post_status = 'publish'
					  AND log.id IS NULL
					  AND projects.post_date > STR_TO_DATE('12/20/2018', '%s')  
					ORDER BY ID ASC;
        ", [ '%m/%d/%Y', $templateId, '%m/%d/%Y' ] );

//        /* Structure of the result array element */
//        $res_element = {
//          'ID'           => (integer) bidder_id,
//          'project_id'   => (integer) project_id,
//          'project_name' => (string) project_title
//        }

		$res = $wpdb->get_results( $query );
		if ( ! empty( $res ) ) {
			foreach ( $res as $user ) {
				self::addUserMetaData( $user );
			}
		}

		return $res;
	}

	/**
	 * Get bidders for project
	 *
	 * @param $project_id
	 * @param string $output_type
	 *
	 * @return array|null|object
	 */
	public static function getBidersForProject( $project_id, $output_type = OBJECT ) {
		global $wpdb;

		$query  = $wpdb->prepare( "
        SELECT bidders.*, projects.post_title as project_name, projects.ID as project_id
        FROM $wpdb->posts as bids
        INNER JOIN $wpdb->posts as projects ON projects.ID = bids.post_parent AND projects.post_type='project'
        INNER JOIN $wpdb->users as bidders ON bids.post_author = bidders.ID
        WHERE bids.post_type = 'bid'
        AND bids.post_status = 'publish'
        AND bids.post_parent = %d
    ", $project_id );
		$query1 = $wpdb->prepare( "
            SELECT owner.* FROM $wpdb->posts as projects
            LEFT JOIN $wpdb->users as owner ON projects.post_author =  owner.ID
            WHERE projects.ID = %s
        ", $project_id );

		$owner = $wpdb->get_row( $query1, $output_type );
		self::addUserMetaData( $owner );
		$res          = new stdClass();
		$res->owner   = $owner;
		$res->bidders = $wpdb->get_results( $query, $output_type );

		if ( ! empty( $res->bidders ) ) {
			foreach ( $res->bidders as $bidder ) {
				self::addUserMetaData( $bidder );
			}
		}

		return $res;

	}

	/**
	 * Get Contractors who have commented previously on the project
	 *
	 * @param $project_id
	 * @param string $output_type
	 *
	 * @return array|null|object
	 */
	public static function getContractorsCommentedProject( $project_id, $output_type = OBJECT ) {
		global $wpdb;

		$project_owner_id = get_post_field( 'post_author', $project_id );

		$query = $wpdb->prepare( "
        SELECT DISTINCT user_id as ID, comment_author_email as user_email
        FROM $wpdb->comments
        WHERE comment_post_ID = %d AND user_id != %d
    ", $project_id, $project_owner_id );

		$res = $wpdb->get_results( $query, $output_type );

		return $res;
	}

	public static function getContractors1DayBeforeSiteVisit( $templateId ) {
		global $wpdb;

		$todayWP  = current_time( 'Y-m-d H:i:s', false );
		$tomorrow = new DateTime( $todayWP );
		$tomorrow->modify( '+1 day' );
		$reminder_visit_date = $tomorrow->format( 'm/d/Y' );
		$query               = $wpdb->prepare( "
            SELECT DISTINCT pm.post_id, projects.post_title as project_name, projects.ID as project_id
            FROM $wpdb->postmeta as pm
            LEFT JOIN $wpdb->posts as projects ON projects.ID = pm.post_id 
            LEFT JOIN $wpdb->notification_log AS log
					    ON log.post_id = projects.ID
					    AND log.template_id = %d
            WHERE pm.meta_key = 'site_visit_date' AND pm.meta_value = %s  
            AND log.id IS NULL
            ORDER BY pm.post_id 
	    ", [ $templateId, $reminder_visit_date ] );

		$projects = $wpdb->get_results( $query );

		foreach ( $projects as $project ) {
			$project->site_visit_date  = get_post_meta( $project->post_id, 'site_visit_date', true ) . " " . get_post_meta( $project->post_id, 'site_visit_time', true );
			$project->link_project     = '<a rel="nofollow" href="' . get_permalink( $project->post_id ) . '">' . get_the_title( $project->post_id ) . '</a>';
			$project->address          = get_post_meta( $project->post_id, 'address', true );
			$project->site_visit_phone = get_post_meta( $project->post_id, 'site_visit_phone', true );
			$project->address          = get_post_meta( $project->post_id, 'address', true );
			$visitors                  = maybe_unserialize( get_post_meta( $project->post_id, 'attending_visitors', true ) );
			$visitor_list              = array();
			foreach ( $visitors as $id => $is_attending ) {
				if ( $is_attending == 'yes' ) {
					$visitor_list[ $id ] = get_userdata( $id );
				}
			}
			$project->visitors = $visitor_list;
		}

		return $projects;
	}


	//======================================================== Contractors ==========================================================

	/**
	 * Get number of bids for project
	 *
	 * @param $project_id
	 *
	 * @return int
	 */
	public static function getBidsNumberForProject( $project_id ) {
		global $wpdb;

		$query = $wpdb->prepare( "
        SELECT COUNT(*)
        FROM $wpdb->posts
        WHERE $wpdb->posts.post_type = 'bid'
        and ($wpdb->posts.post_status = 'publish'
            or $wpdb->posts.post_status = 'complete'
            or $wpdb->posts.post_status = 'accept' 
            or $wpdb->posts.post_status = 'unaccept')
        and $wpdb->posts.post_parent = %d
    ", $project_id );

		$res = $wpdb->get_var( $query );

		return (int) $res;

	}

	/**
	 * Get authors IDs of unaccepted bids for project
	 *
	 * @param $project_id
	 * @param $accepted_bid
	 * @param string $output_type
	 *
	 * @return array|object|null
	 */
	public static function getUnacceptedBidsAuthorsForProject( $project_id, $accepted_bid, $output_type = OBJECT ) {
		global $wpdb;

		$query = $wpdb->prepare( "
        SELECT $wpdb->posts.post_author
        FROM $wpdb->posts
        WHERE $wpdb->posts.post_type = 'bid'
        AND $wpdb->posts.post_status = 'unaccept'
        AND $wpdb->posts.post_status != 'complete'
        AND $wpdb->posts.post_parent = %d
        AND $wpdb->posts.ID != %d
    ", $project_id, $accepted_bid );

		$res = $wpdb->get_results( $query, $output_type );

		return $res;

	}

	/**
	 * Get list off all templates
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public static function getTemplatesList( $args = array() ) {
		global $wpdb;

		$arg = array(
			'orderby'     => ! empty( $args['orderby'] ) ? $args['orderby'] : '',
			'order'       => ! empty( $args['order'] ) ? $args['order'] : 'ASC',
			'per_page'    => ! empty( $args['per_page'] ) ? $args['per_page'] : 0,
			'page_number' => ! empty( $args['page_number'] ) ? $args['page_number'] : 1,
			's'           => ! empty( $args['s'] ) ? $args['s'] : '',
		);

		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM " . $wpdb->prefix . "notification_templates";

		if ( ! empty( $arg['s'] ) ) {
			$sql .= ' WHERE template_name LIKE "%' . $arg['s'] . '%"';
		}
		if ( ! empty( $arg['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $arg['orderby'] );
			$sql .= ' ' . esc_sql( $arg['order'] );
		}
		if ( $arg['per_page'] > 0 ) {
			$sql .= " LIMIT " . $arg['per_page'];
			$sql .= ' OFFSET ' . ( $arg['page_number'] - 1 ) * $arg['per_page'];
		}

		$templates       = array();
		$templates_data  = $wpdb->get_results( $sql );
		$templates_found = $wpdb->get_var( 'SELECT FOUND_ROWS();' );

		$templates['data']  = $templates_data;
		$templates['found'] = $templates_found;

		return $templates;
	}

	/**
	 * Get template by template ID
	 *
	 * @param $template_id
	 * @param string $output_type
	 *
	 * @return array|bool|object|null
	 */
	public static function getTemplateById( $template_id, $output_type = OBJECT ) {
		global $wpdb;

		if ( empty( $template_id ) ) {
			return false;
		}

		$template_data = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}notification_templates WHERE id = {$template_id}", $output_type );

		return $template_data;
	}

	/**
	 * Get template by template name and destination type
	 *
	 * @param $template_name
	 * @param $destination_type
	 * @param string $output_type
	 *
	 * @return array|bool|object|null
	 */
	public static function getTemplate( $template_name, $destination_type, $output_type = OBJECT ) {
		global $wpdb;

		if ( empty( $template_name ) || empty( $destination_type ) ) {
			return false;
		}

		$query         = $wpdb->prepare(
			"SELECT * 
					FROM {$wpdb->prefix}notification_templates 
					WHERE template_name = %s 
					AND destination_type_id = %d",
			$template_name,
			$destination_type
		);
		$template_data = $wpdb->get_row( $query, $output_type );

		return $template_data;
	}

	/**
	 * Get types of destinations
	 *
	 * @param string $output
	 *
	 * @return array|object|null
	 */
	public static function getDestinationTypes( $output = OBJECT ) {
		global $wpdb;

		$all_destination_types = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}notification_destination_types", $output );

		return $all_destination_types;
	}

	/**
	 * Get types of notifications
	 *
	 * @param string $output
	 *
	 * @return array|object|null
	 */
	public static function getNotificationTypes( $output = OBJECT ) {
		global $wpdb;

		$all_notification_types = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}notification_types", $output );

		return $all_notification_types;
	}

	/**
	 * Get destination type by ID
	 *
	 * @param $type_id
	 *
	 * @return array|bool|object|null
	 */
	public static function getDestinationTypeById( $type_id ) {
		global $wpdb;

		if ( empty( $type_id ) ) {
			return false;
		}

		$destination_type_data = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}notification_destination_types WHERE id = {$type_id}", ARRAY_A );

		return $destination_type_data;
	}

	/**
	 * Get notification type by ID
	 *
	 * @param $type_id
	 *
	 * @return array|bool|object|null
	 */
	public static function getNotificationTypeById( $type_id ) {
		global $wpdb;

		if ( empty( $type_id ) ) {
			return false;
		}

		$notification_type_data = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}notification_types WHERE id = {$type_id}", ARRAY_A );

		return $notification_type_data;
	}

	/**
	 * Delete templates by array of IDs
	 *
	 * @param $template_ids
	 *
	 * @return false|int
	 */
	public static function deleteTemplatesByIds( $template_ids ) {
		global $wpdb;

		$ids    = implode( ',', array_map( 'absint', $template_ids ) );
		$result = $wpdb->query( "DELETE 
					FROM {$wpdb->prefix}notification_templates 
					WHERE id IN ({$ids})" );

		return $result;
	}

	/**
	 * Delete template by ID
	 *
	 * @param $template_id
	 *
	 * @return false|int
	 */
	public static function deleteTemplateById( $template_id ) {
		global $wpdb;

		$result = $wpdb->delete( $wpdb->prefix . 'notification_templates', array( 'id' => $template_id ), array( '%d' ) );

		return $result;
	}

	/**
	 * Add new template
	 *
	 * @param array $new_template_data
	 *
	 * @return array
	 */
	public static function addNewTemplate( $new_template_data = array() ) {
		global $wpdb;

		$new_template = array(
			'template_name'        => '',
			'subject'              => '',
			'template_body'        => '',
			'description'          => '',
			'destination_type_id'  => 1,
			'notification_type_id' => 1,
		);

		if ( ! empty( $new_template_data ) && is_array( $new_template_data ) ) {
			foreach ( $new_template as $key => $val ) {
				if ( ! empty( $new_template_data[ $key ] ) ) {
					switch ( $key ) {
						case 'template_id':
						case 'destination_type_id':
						case 'notification_type_id':
							$new_template[ $key ] = (int) $new_template_data[ $key ];
							break;
						case 'template_body':
							$new_template[ $key ] = wp_kses_post( $new_template_data[ $key ] );
							break;
						default:
							$new_template[ $key ] = sanitize_text_field( $new_template_data[ $key ] );
					}
				}
			}
		}

		$results = array();

		/* Check Template for exist */
		$template = self::getTemplate( $new_template['template_name'], $new_template['destination_type_id'], ARRAY_A );
		if ( ! empty( $template ) ) {
			$results['warning'] = sprintf( __( 'Template "%s" is already exist. You can edit this Template data %shere%s', NM_DOMAIN ), $new_template['template_name'], '<a href="admin.php?page=templates&action=edit&template_id=' . $template['id'] . '">', '</a>' );
		}

		if ( empty( $new_template['template_name'] ) ) {
			$results['error'] = __( 'Template data is empty or incorrect.', NM_DOMAIN );
		}

		if ( empty( $template ) && ! empty( $new_template['template_name'] ) && empty( $results['error'] ) ) {

			$result = $wpdb->insert(
				$wpdb->prefix . 'notification_templates',
				array(
					'template_name'        => $new_template['template_name'],
					'subject'              => $new_template['subject'],
					'template_body'        => $new_template['template_body'],
					'description'          => $new_template['description'],
					'destination_type_id'  => $new_template['destination_type_id'],
					'notification_type_id' => $new_template['notification_type_id'],
				),
				array(
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
					'%d',
				)
			);

			if ( ! $result ) {
				$results['error'] = $wpdb->last_error;
			} else {
				$results['success']     = __( 'Template was created.', NM_DOMAIN );
				$results['template_id'] = $wpdb->insert_id;
			}
		}

		return $results;
	}

	/**
	 * Update exists template
	 *
	 * @param array $template_data
	 *
	 * @return array|bool
	 */
	public static function updateTemplate( $template_data = array() ) {
		global $wpdb;

		if ( empty( $template_data ) ) {
			return false;
		}

		$template = array(
			'template_id'          => 0,
			'template_name'        => '',
			'subject'              => '',
			'template_body'        => '',
			'description'          => '',
			'destination_type_id'  => 1,
			'notification_type_id' => 1,
		);

		if ( ! empty( $template_data ) && is_array( $template_data ) ) {
			foreach ( $template as $key => $val ) {
				if ( ! empty( $template_data[ $key ] ) ) {
					switch ( $key ) {
						case 'template_id':
						case 'destination_type_id':
						case 'notification_type_id':
							$template[ $key ] = (int) $template_data[ $key ];
							break;
						case 'template_body':
							$template[ $key ] = wp_kses_post( $template_data[ $key ] );
							break;
						default:
							$template[ $key ] = sanitize_text_field( $template_data[ $key ] );
					}
				}
			}
		}

		if ( empty( $template['template_id'] ) ) {
			return false;
		}

		$results = array();

		if ( empty( $template['template_name'] ) ) {
			$results['error'] = __( 'Template Name is empty or incorrect.', NM_DOMAIN );
		}

		if ( ! empty( $template['template_id'] ) && ! empty( $template['template_name'] ) && empty( $results['error'] ) ) {
			$result = $wpdb->update(
				$wpdb->prefix . 'notification_templates',
				array(
					'template_name'        => $template['template_name'],
					'subject'              => $template['subject'],
					'template_body'        => $template['template_body'],
					'description'          => $template['description'],
					'destination_type_id'  => $template['destination_type_id'],
					'notification_type_id' => $template['notification_type_id'],
				),
				array(
					'id' => $template['template_id'],
				),
				array(
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
					'%d',
				),
				array(
					'%d',
				)
			);

			if ( false === $result ) {
				$results['error'] = $wpdb->last_error;
			} elseif ( empty( $result ) ) {
				$results['warning'] = $wpdb->last_result;
			} else {
				$results['success']     = __( 'Template was updated.', NM_DOMAIN );
				$results['template_id'] = $wpdb->insert_id;
			}
		}

		return $results;
	}

	/**
	 * Get a template by name to check for template is exists
	 *
	 * @param $template_name
	 *
	 * @return array|bool|object|null
	 */
	public static function getTemplateByName( $template_name ) {
		global $wpdb;

		if ( empty( $template_name ) ) {
			return false;
		}

		$template_data = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}notification_templates WHERE template_name = '{$template_name}'", ARRAY_A );

		return $template_data;
	}

	public static function logNotification( $log_data ) {
		global $wpdb;

		$wpdb->notification_log = $wpdb->prefix . 'notification_log';
		$res                    = $wpdb->insert( $wpdb->notification_log, $log_data, [ '%d', '%d', '%s', '%d' ] );

		return $res;
	}

	static function templateIdsByTemplateNames( $templateNamesArr, $destinationType ) {
		global $wpdb;
		$placeholders = implode( ', ', array_fill( 0, count( $templateNamesArr ), '%s' ) );
		$result = [];

		$query = $wpdb->prepare( "
            SELECT id FROM {$wpdb->notification_templates} WHERE  $wpdb->notification_templates.template_name in ( $placeholders )
            AND destination_type_id={$destinationType}
", $templateNamesArr );
		$res   = $wpdb->get_results( $query );
		foreach ( $res as $resId ) {
			$result[] = $resId->id;
		}

		return $result;
	}

	static public function resetLogOnChangeDeadline( $projectId, $templateIds ) {
		global $wpdb;
		$placeholders = implode( ', ', array_fill( 0, count( $templateIds ), '%d' ) );
		$result       = $wpdb->query( $wpdb->prepare( "DELETE 
					FROM {$wpdb->prefix}notification_log
                    WHERE template_id IN ({$placeholders})
                    AND post_id={$projectId}", $templateIds ) );

		return $result;
	}
}

