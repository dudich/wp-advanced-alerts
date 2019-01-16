<?php
require_once 'class-classnotificationbase.php';

class ProcessNotification extends ClassNotificationBase {
	public function __construct() {
		parent::__construct();
	}

	public function runNotification( $notificationSuffix, $destinationType, $args = array() ) {
		$method = 'process' . $notificationSuffix . '_' . $destinationType;
		$result = null;
		if ( method_exists( $this, $method ) ) {
			$result = $this->$method( $notificationSuffix, $destinationType, $args );
		}

		return $result;
	}

	/***
	 * Building Emails
	 */

	/***
	 * Building Email 1 (Welcome)
	 *
	 * @param string $notificationSuffix
	 * @param int $destinationType
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail1_1( $notificationSuffix, $destinationType, $args = array() ) {
		if ( empty( $args['user']->user_email ) ) {
			return false;
		}

		$template = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );
		if ( empty( $template ) ) {
			return false;
		}

		$res = $this->send_mail( $args['user']->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '' );

		return $res;
	}

	/***
	 * Building Email 2 (6 hours after sign-up)
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail2_1( $args = array() ) {
		$notificationSuffix = isset( $args[0] ) ? $args[0] : false;
		$destinationType    = isset( $args[1] ) ? $args[1] : false;
		if ( empty( $notificationSuffix ) || empty( $destinationType ) ) {
			error_log( print_r( 'processEmail2_1: Not valid parameters. Time: ' . date( 'Y-m-d H:i:s', time() ), true ), 0 );

			return false;
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );
		if ( empty( $template_source ) ) {
			return false;
		}
		$users  = $this->notificationModel::getUsersAfter6HourAfterSignUp( $template_source->id );
		$result = true;

		if ( ! empty( $users ) ) {
			if ( is_array( $users ) ) {
				foreach ( $users as $user ) {
					$args        = [
						'%%personal_name%%' => ! empty( $user->first_name . ' ' . $user->last_name ) ? $user->first_name . ' ' . $user->last_name : $user->display_name,
					];
					$dataBody    = $args;
					$dataSubject = [];

					$template = $this->fillTemplate(
						$template_source,
						$dataSubject,
						$dataBody
					);
					$log_data = [
						'template_id'  => $template->id,
						'recipient_id' => $user->ID,
						'post_type'    => '',
						'post_id'      => 0,
					];

					$res = $this->send_mail( $user->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '', $log_data );

					if ( isset( $res ) && false == $res ) {
						$result = $res;
					}
				}
			}
		}

		return $result;
	}

	/***
	 * Building Email 3 (no project posted 1 day after sign up)
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail3_1( $args = array() ) {
		$notificationSuffix = isset( $args[0] ) ? $args[0] : false;
		$destinationType    = isset( $args[1] ) ? $args[1] : false;
		if ( empty( $notificationSuffix ) || empty( $destinationType ) ) {
			error_log( print_r( 'processEmail3_1: Not valid parameters. Time: ' . date( 'Y-m-d H:i:s', time() ), true ), 0 );

			return false;
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );
		if ( empty( $template_source ) ) {
			return false;
		}
		$users = $this->notificationModel::getUsersAfter1DayAfterSignUp( $template_source->id );

		$result = true;
		if ( ! empty( $users ) ) {
			if ( is_array( $users ) ) {
				foreach ( $users as $user ) {
					$args        = [
						'%%personal_name%%' => ! empty( $user->first_name . ' ' . $user->last_name ) ? $user->first_name . ' ' . $user->last_name : $user->display_name,
					];
					$dataBody    = $args;
					$dataSubject = [];

					$template = $this->fillTemplate(
						$template_source,
						$dataSubject,
						$dataBody
					);
					$log_data = [
						'template_id'  => $template->id,
						'recipient_id' => $user->ID,
						'post_type'    => '',
						'post_id'      => 0,
					];

					$res = $this->send_mail( $user->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '', $log_data );

					if ( isset( $res ) && false == $res ) {
						$result = $res;
					}
				}
			}
		}

		return $result;
	}

	/***
	 * Building Email 4 (no project posted 3 days after sign up)
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail4_1( $args = array() ) {
		$notificationSuffix = isset( $args[0] ) ? $args[0] : false;
		$destinationType    = isset( $args[1] ) ? $args[1] : false;
		if ( empty( $notificationSuffix ) || empty( $destinationType ) ) {
			error_log( print_r( 'processEmail4_1: Not valid parameters. Time: ' . date( 'Y-m-d H:i:s', time() ), true ), 0 );

			return false;
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );
		if ( empty( $template_source ) ) {
			return false;
		}
		$users = $this->notificationModel::getUsersAfter3DayAfterSignUp( $template_source->id );

		$result = true;
		if ( ! empty( $users ) ) {
			if ( is_array( $users ) ) {
				foreach ( $users as $user ) {
					$args        = [
						'%%personal_name%%' => ! empty( $user->first_name . ' ' . $user->last_name ) ? $user->first_name . ' ' . $user->last_name : $user->display_name,
					];
					$dataBody    = $args;
					$dataSubject = [];

					$template = $this->fillTemplate(
						$template_source,
						$dataSubject,
						$dataBody
					);
					$log_data = [
						'template_id'  => $template->id,
						'recipient_id' => $user->ID,
						'post_type'    => '',
						'post_id'      => 0,
					];

					$res = $this->send_mail( $user->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '', $log_data );

					if ( isset( $res ) && false == $res ) {
						$result = $res;
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Email 5 (send every 10 days after a user’s last project was posted – also send to users who have not posted any projects at all, every 10 days)
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail5_1( $args = array() ) {
		$notificationSuffix = isset( $args[0] ) ? $args[0] : false;
		$destinationType    = isset( $args[1] ) ? $args[1] : false;
		if ( empty( $notificationSuffix ) || empty( $destinationType ) ) {
			error_log( print_r( 'processEmail5_1: Not valid parameters. Time: ' . date( 'Y-m-d H:i:s', time() ), true ), 0 );

			return false;
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );
		if ( empty( $template_source ) ) {
			return false;
		}
		$users  = $this->notificationModel::getUsersAfter10DayAfterLastProjectPosted( $template_source->id );
		$result = true;

		if ( ! empty( $users ) ) {
			if ( is_array( $users ) ) {
				foreach ( $users as $user ) {

					$args        = [
						'%%personal_name%%' => ! empty( $user->first_name . ' ' . $user->last_name ) ? $user->first_name . ' ' . $user->last_name : $user->display_name,
					];
					$dataBody    = $args;
					$dataSubject = [];

					$template = $this->fillTemplate(
						$template_source,
						$dataSubject,
						$dataBody
					);
					if ( $template->template_body === false ) {
						error_log( print_r( 'Number of inserted data doesn\'t match number of placeholders in the template', true ), 0 );

						return false;
					}
					$log_data = [
						'template_id'  => $template->id,
						'recipient_id' => $user->ID,
						'post_type'    => '',
						'post_id'      => 0,
					];

					$res = $this->send_mail( $user->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '', $log_data );

					if ( isset( $res ) && false == $res ) {
						$result = $res;
					}
				}
			}
		}

		return $result;
	}

	/***
	 * Building Email 6 (project posted)
	 *
	 * @param string $notificationSuffix
	 * @param int $destinationType
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail6_1( $notificationSuffix, $destinationType, $args = array() ) {
		$dataSubject = $dataBody = [];
		if ( ! empty( $args ) ) {
			if ( ! empty( $args['user'] ) ) {
				$user_name = trim( $args['user']->first_name . ' ' . $args['user']->last_name );
				if ( empty( $user_name ) ) {
					$user_name = $args['user']->display_name;
				}
				$dataBody['%%personal_name%%'] = $user_name;
			}

			if ( ! empty( $args['post'] ) ) {
				$project_name                    = get_project_title_for( $args['post']->ID, 'builder' );
				$dataSubject['%%project_name%%'] = $dataBody['%%project_name%%'] = $project_name;
			}

			if ( ! empty( $args['post_link'] ) ) {
				if ( empty( $project_name ) ) {
					$project_name = $args['post_link'];
				}
				$dataBody['%%project_link%%'] = $this->createProjectLink( $args['post_link'], $project_name );
			}
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );

		if ( empty( $template_source ) || empty( $dataSubject ) || empty( $dataBody ) ) {
			return false;
		}
		$template = $this->fillTemplate(
			$template_source,
			$dataSubject,
			$dataBody
		);
		$res      = $this->send_mail( $args['user']->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '' );

		return $res;
	}

	/***
	 * Building Email 7 (after project is posted and first bid is received)
	 *
	 * @param string $notificationSuffix
	 * @param int $destinationType
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail7_1( $notificationSuffix, $destinationType, $args = array() ) {
		$dataSubject = $dataBody = [];
		if ( ! empty( $args ) ) {
			if ( ! empty( $args['user'] ) ) {
				$user_name = trim( $args['user']->first_name . ' ' . $args['user']->last_name );
				if ( empty( $user_name ) ) {
					$user_name = $args['user']->display_name;
				}
				$dataBody['%%personal_name%%'] = $user_name;
			}

			if ( ! empty( $args['post'] ) ) {
				$project_name                    = get_project_title_for( $args['post']->ID, 'builder' );
				$dataSubject['%%project_name%%'] = $dataBody['%%project_name%%'] = $project_name;
			}

			if ( ! empty( $args['post_link'] ) ) {
				if ( empty( $project_name ) ) {
					$project_name = $args['post_link'];
				}
				$dataBody['%%project_link%%'] = $this->createProjectLink( $args['post_link'], $project_name );
			}
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );

		if ( empty( $template_source ) || empty( $dataSubject ) || empty( $dataBody ) ) {
			return false;
		}
		$template = $this->fillTemplate(
			$template_source,
			$dataSubject,
			$dataBody
		);
		$res      = $this->send_mail( $args['user']->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '' );

		return $res;
	}

	/***
	 * Building Email 8 (after project is posted and 5 bids are received)
	 *
	 * @param string $notificationSuffix
	 * @param int $destinationType
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail8_1( $notificationSuffix, $destinationType, $args = array() ) {
		$dataSubject = $dataBody = [];
		if ( ! empty( $args ) ) {
			if ( ! empty( $args['user'] ) ) {
				$user_name = trim( $args['user']->first_name . ' ' . $args['user']->last_name );
				if ( empty( $user_name ) ) {
					$user_name = $args['user']->display_name;
				}
				$dataBody['%%personal_name%%'] = $user_name;
			}

			if ( ! empty( $args['post'] ) ) {
				$project_name                    = get_project_title_for( $args['post']->ID, 'builder' );
				$dataSubject['%%project_name%%'] = $dataBody['%%project_name%%'] = $project_name;
			}

			if ( ! empty( $args['post_link'] ) ) {
				if ( empty( $project_name ) ) {
					$project_name = $args['post_link'];
				}
				$dataBody['%%project_link%%'] = $this->createProjectLink( $args['post_link'], $project_name );
			}
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );

		if ( empty( $template_source ) || empty( $dataSubject ) || empty( $dataBody ) ) {
			return false;
		}
		$template = $this->fillTemplate(
			$template_source,
			$dataSubject,
			$dataBody
		);
		$res      = $this->send_mail( $args['user']->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '' );

		return $res;
	}

	/**
	 * Email 9 (1 day before bid deadline is reached IF USER RECEIVED 3 BIDS OR LESS)
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail9_1( $args = array() ) {
		$notificationSuffix = isset( $args[0] ) ? $args[0] : false;
		$destinationType    = isset( $args[1] ) ? $args[1] : false;
		if ( empty( $notificationSuffix ) || empty( $destinationType ) ) {
			error_log( print_r( 'processEmail9_1: Not valid parameters. Time: ' . date( 'Y-m-d H:i:s', time() ), true ), 0 );

			return false;
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );
		if ( empty( $template_source ) ) {
			return false;
		}
		$users  = $this->notificationModel::getUsers1DayBeforeBidDeadlineAndLessThen4bids( $template_source->id );
		$result = true;

		if ( ! empty( $users ) ) {
			if ( is_array( $users ) ) {
				foreach ( $users as $user ) {
					$project_name  = get_project_title_for( $user->project_id, 'builder' );
					$linkToProject = $this->createProjectLink( get_permalink( $user->project_id ), $project_name );
					$args          = [
						'%%personal_name%%'  => ! empty( $user->first_name . ' ' . $user->last_name ) ? $user->first_name . ' ' . $user->last_name : $user->display_name,
						'%%project_name%%'   => $project_name,
						'%%number_of_bids%%' => sprintf( _n( '%s bid', '%s bids', $user->n_bids, 'am_domain' ), $user->n_bids ),
						'%%project_link%%'   => $linkToProject,
					];
					$dataBody      = $args;
					$dataSubject   = [];

					$template = $this->fillTemplate(
						$template_source,
						$dataSubject,
						$dataBody
					);
					if ( $template->template_body === false ) {
						error_log( print_r( 'Number of inserted data doesn\'t match number of placeholders in the template', true ), 0 );

						return false;
					}
					$log_data = [
						'template_id'  => $template->id,
						'recipient_id' => $user->ID,
						'post_type'    => 'project',
						'post_id'      => $user->project_id,
					];

					$res = $this->send_mail( $user->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '', $log_data );

					if ( isset( $res ) && false == $res ) {
						$result = $res;
					}
				}
			}
		}

		return $result;
	}

	/**
	 *
	 * Email 10 (1 day before bid deadline is reached IF USER RECEIVED MORE THAN 3 BIDS)
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail10_1( $args = array() ) {
		$notificationSuffix = isset( $args[0] ) ? $args[0] : false;
		$destinationType    = isset( $args[1] ) ? $args[1] : false;
		if ( empty( $notificationSuffix ) || empty( $destinationType ) ) {
			error_log( print_r( 'processEmail10_1: Not valid parameters. Time: ' . date( 'Y-m-d H:i:s', time() ), true ), 0 );

			return false;
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );
		if ( empty( $template_source ) ) {
			return false;
		}
		$users  = $this->notificationModel::getUsers1DayBeforeBidDeadlineAndMoreThen3bids( $template_source->id );
		$result = true;

		if ( ! empty( $users ) ) {
			if ( is_array( $users ) ) {
				foreach ( $users as $user ) {

					$args        = [
						'%%personal_name%%'          => ! empty( $user->first_name . ' ' . $user->last_name ) ? $user->first_name . ' ' . $user->last_name : $user->display_name,
						'%%project_name%%'           => get_project_title_for( $user->project_id, 'builder' ),
						'%%number_of_bids%%'         => sprintf( _n( '%s bid', '%s bids', $user->n_bids, 'am_domain' ), $user->n_bids ),
						'%%percent_number_of_bids%%' => number_format( $user->n_bids > 3 ? ( $user->n_bids / 3 ) * 100 - 100 : 0, 2 ),

					];
					$dataBody    = $args;
					$dataSubject = [];

					$template = $this->fillTemplate(
						$template_source,
						$dataSubject,
						$dataBody
					);
					if ( $template->template_body === false ) {
						error_log( print_r( 'Number of inserted data doesn\'t match number of placeholders in the template', true ), 0 );

						return false;
					}
					$log_data = [
						'template_id'  => $template->id,
						'recipient_id' => $user->ID,
						'post_type'    => 'project',
						'post_id'      => $user->project_id,
					];

					$res = $this->send_mail( $user->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '', $log_data );

					if ( isset( $res ) && false == $res ) {
						$result = $res;
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Email 11 (1 day after project deadline is reached, no bid chosen)
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail11_1( $args = array() ) {
		$notificationSuffix = isset( $args[0] ) ? $args[0] : false;
		$destinationType    = isset( $args[1] ) ? $args[1] : false;
		if ( empty( $notificationSuffix ) || empty( $destinationType ) ) {
			error_log( print_r( 'processEmail11_1: Not valid parameters. Time: ' . date( 'Y-m-d H:i:s', time() ), true ), 0 );

			return false;
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );
		if ( empty( $template_source ) ) {
			return false;
		}
		$users = $this->notificationModel::getUsersWithProjectsWithoutAcceptedBids1DayAfterDeadline( $template_source->id );

		$result = true;

		if ( ! empty( $users ) ) {
			if ( is_array( $users ) ) {
				foreach ( $users as $user ) {
					$linkToProject = '<a href="' . get_permalink( $user->project_id ) . '">' . get_permalink( $user->project_id ) . '</a>';
					$args          = [
						'%%personal_name%%'   => ! empty( $user->first_name . ' ' . $user->last_name ) ? $user->first_name . ' ' . $user->last_name : $user->display_name,
						'%%project_name%%'    => get_project_title_for( $user->project_id, 'builder' ),
						'%%number_of_bids%%'  => sprintf( _n( '%s bid', '%s bids', $user->n_bids, 'am_domain' ), $user->n_bids ),
						'%%link_to_project%%' => $linkToProject,

					];
					$dataBody      = $args;
					$dataSubject   = [];

					$template = $this->fillTemplate(
						$template_source,
						$dataSubject,
						$dataBody
					);
					if ( $template->template_body === false ) {
						error_log( print_r( 'Number of inserted data doesn\'t match number of placeholders in the template', true ), 0 );

						return false;
					}
					$log_data = [
						'template_id'  => $template->id,
						'recipient_id' => $user->ID,
						'post_type'    => 'project',
						'post_id'      => $user->project_id,
					];

					$res = $this->send_mail( $user->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '', $log_data );

					if ( isset( $res ) && false == $res ) {
						$result = $res;
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Email 12 (3 days after project deadline is reached, no bid chosen)
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail12_1( $args = array() ) {
		$notificationSuffix = isset( $args[0] ) ? $args[0] : false;
		$destinationType    = isset( $args[1] ) ? $args[1] : false;
		if ( empty( $notificationSuffix ) || empty( $destinationType ) ) {
			error_log( print_r( 'processEmail12_1: Not valid parameters. Time: ' . date( 'Y-m-d H:i:s', time() ), true ), 0 );

			return false;
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );
		if ( empty( $template_source ) ) {
			return false;
		}
		$users  = $this->notificationModel::getUsersWithProjectsWithNoBids3DayAfterDeadline( $template_source->id );
		$result = true;

		if ( ! empty( $users ) ) {
			if ( is_array( $users ) ) {
				foreach ( $users as $user ) {
					$linkToProject = '<a href="' . get_permalink( $user->project_id ) . '">' . get_permalink( $user->project_id ) . '</a>';
					$args          = [
						'%%personal_name%%'   => ! empty( $user->first_name . ' ' . $user->last_name ) ? $user->first_name . ' ' . $user->last_name : $user->display_name,
						'%%project_name%%'    => get_project_title_for( $user->project_id, 'builder' ),
						'%%number_of_bids%%'  => sprintf( _n( '%s bid', '%s bids', $user->n_bids, 'am_domain' ), $user->n_bids ),
						'%%link_to_project%%' => $linkToProject,

					];
					$dataBody      = $args;
					$dataSubject   = [];

					$template = $this->fillTemplate(
						$template_source,
						$dataSubject,
						$dataBody
					);
					if ( $template->template_body === false ) {
						error_log( print_r( 'Number of inserted data doesn\'t match number of placeholders in the template', true ), 0 );

						return false;
					}
					$log_data = [
						'template_id'  => $template->id,
						'recipient_id' => $user->ID,
						'post_type'    => 'project',
						'post_id'      => $user->project_id,
					];

					$res = $this->send_mail( $user->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '', $log_data );

					if ( isset( $res ) && false == $res ) {
						$result = $res;
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Email 13 (each week after project deadline is reached, no bid chosen)
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail13_1( $args = array() ) {
		$notificationSuffix = isset( $args[0] ) ? $args[0] : false;
		$destinationType    = isset( $args[1] ) ? $args[1] : false;
		if ( empty( $notificationSuffix ) || empty( $destinationType ) ) {
			error_log( print_r( 'processEmail13_1: Not valid parameters. Time: ' . date( 'Y-m-d H:i:s', time() ), true ), 0 );

			return false;
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );
		if ( empty( $template_source ) ) {
			return false;
		}
		$users  = $this->notificationModel::getUsersWithProjectsWithNoBidsEveryWeekAfterDeadline( $template_source->id );
		$result = true;

		if ( ! empty( $users ) ) {
			if ( is_array( $users ) ) {
				foreach ( $users as $user ) {
					$linkToProject = '<a href="' . get_permalink( $user->project_id ) . '">' . get_permalink( $user->project_id ) . '</a>';
					$args          = [
						'%%personal_name%%'   => ! empty( $user->first_name . ' ' . $user->last_name ) ? $user->first_name . ' ' . $user->last_name : $user->display_name,
						'%%project_name%%'    => get_project_title_for( $user->project_id, 'builder' ),
						'%%number_of_bids%%'  => sprintf( _n( '%s bid', '%s bids', $user->n_bids, 'am_domain' ), $user->n_bids ),
						'%%link_to_project%%' => $linkToProject,

					];
					$dataBody      = $args;
					$dataSubject   = [];

					$template = $this->fillTemplate(
						$template_source,
						$dataSubject,
						$dataBody
					);
					if ( $template->template_body === false ) {
						error_log( print_r( 'Number of inserted data doesn\'t match number of placeholders in the template', true ), 0 );

						return false;
					}
					$log_data = [
						'template_id'  => $template->id,
						'recipient_id' => $user->ID,
						'post_type'    => 'project',
						'post_id'      => $user->project_id,
					];

					$res = $this->send_mail( $user->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '', $log_data );

					if ( isset( $res ) && false == $res ) {
						$result = $res;
					}
				}
			}
		}

		return $result;
	}


	/***
	 * Building Email 14 (after a winning bid is chosen)
	 *
	 * @param string $notificationSuffix
	 * @param int $destinationType
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail14_1( $notificationSuffix, $destinationType, $args = array() ) {
		$dataSubject = $dataBody = [];
		if ( ! empty( $args ) ) {
			if ( ! empty( $args['user_project'] ) ) {
				$user_name_project = trim( $args['user_project']->first_name . ' ' . $args['user_project']->last_name );
				if ( empty( $user_name_project ) ) {
					$user_name_project = $args['user_project']->display_name;
				}
				$dataBody['%%personal_name%%'] = $user_name_project;
			}

			if ( ! empty( $args['user_bid'] ) ) {
				$user_name_bid = trim( $args['user_bid']->first_name . ' ' . $args['user_bid']->last_name );
				if ( empty( $user_name_bid ) ) {
					$user_name_bid = $args['user_bid']->display_name;
				}
				$dataBody['%%contractor_name%%'] = $user_name_bid;
			}

			if ( ! empty( $args['post'] ) ) {
				$dataSubject['%%project_name%%'] = $dataBody['%%project_name%%'] = get_project_title_for( $args['post']->ID, 'builder' );
			}
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );

		if ( empty( $template_source ) || empty( $dataSubject ) || empty( $dataBody ) ) {
			return false;
		}
		$template = $this->fillTemplate(
			$template_source,
			$dataSubject,
			$dataBody
		);
		$res      = $this->send_mail( $args['user_project']->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '' );

		return $res;
	}

	public function processEmail15_1( $args = array() ) {
		$notificationSuffix = isset( $args[0] ) ? $args[0] : false;
		$destinationType    = isset( $args[1] ) ? $args[1] : false;
		if ( empty( $notificationSuffix ) || empty( $destinationType ) ) {
			error_log( print_r( 'processEmail15_1: Not valid parameters. Time: ' . date( 'Y-m-d H:i:s', time() ), true ), 0 );

			return false;
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );
		if ( empty( $template_source ) ) {
			return false;
		}
		$users = $this->notificationModel::getUsers1WeekAfterWiningBidChosen( $template_source->id );

		$result = true;

		if ( ! empty( $users ) ) {
			if ( is_array( $users ) ) {
				foreach ( $users as $user ) {

					$args        = [
						'%%personal_name%%' => ! empty( $user->first_name . ' ' . $user->last_name ) ? $user->first_name . ' ' . $user->last_name : $user->display_name,
						'%%project_name%%'  => get_project_title_for( $user->project_id, 'builder' ),
					];
					$dataBody    = $args;
					$dataSubject = [];

					$template = $this->fillTemplate(
						$template_source,
						$dataSubject,
						$dataBody
					);
					if ( $template->template_body === false ) {
						error_log( print_r( 'Number of inserted data doesn\'t match number of placeholders in the template', true ), 0 );

						return false;
					}
					$log_data = [
						'template_id'  => $template->id,
						'recipient_id' => $user->ID,
						'post_type'    => 'project',
						'post_id'      => $user->project_id,
					];
					$res      = $this->send_mail( $user->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '', $log_data );

					if ( isset( $res ) && false == $res ) {
						$result = $res;
					}
				}
			}
		}

		return $result;
	}


	/***
	 * Building Email 16 (project archived/deleted)
	 *
	 * @param string $notificationSuffix
	 * @param int $destinationType
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail16_1( $notificationSuffix, $destinationType, $args = array() ) {
		$dataSubject = $dataBody = [];
		if ( ! empty( $args ) ) {
			if ( ! empty( $args['user'] ) ) {
				$user_name = trim( $args['user']->first_name . ' ' . $args['user']->last_name );
				if ( empty( $user_name ) ) {
					$user_name = $args['user']->display_name;
				}
				$dataBody['%%personal_name%%'] = $user_name;
			}

			if ( ! empty( $args['post'] ) ) {
				$project_name                    = get_project_title_for( $args['post']->ID, 'builder' );
				$dataSubject['%%project_name%%'] = $dataBody['%%project_name%%'] = $project_name;
			}

			if ( ! empty( $args['post_link'] ) ) {
				if ( empty( $project_name ) ) {
					$project_name = $args['post_link'];
				}
				$dataBody['%%project_link%%'] = $this->createProjectLink( $args['post_link'], $project_name );
			}
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );

		if ( empty( $template_source ) || empty( $dataSubject ) || empty( $dataBody ) ) {
			return false;
		}
		$template = $this->fillTemplate(
			$template_source,
			$dataSubject,
			$dataBody
		);
		$res      = $this->send_mail( $args['user']->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '' );

		return $res;
	}

	/***
	 * Building Email 17 (bid received, all bids after first bid)
	 *
	 * @param string $notificationSuffix
	 * @param int $destinationType
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail17_1( $notificationSuffix, $destinationType, $args = array() ) {
		$dataSubject = $dataBody = [];
		if ( ! empty( $args ) ) {
			if ( ! empty( $args['user'] ) ) {
				$user_name = trim( $args['user']->first_name . ' ' . $args['user']->last_name );
				if ( empty( $user_name ) ) {
					$user_name = $args['user']->display_name;
				}
				$dataBody['%%personal_name%%'] = $user_name;
			}

			if ( ! empty( $args['post'] ) ) {
				$project_name                    = get_project_title_for( $args['post']->ID, 'builder' );
				$dataSubject['%%project_name%%'] = $dataBody['%%project_name%%'] = $project_name;
			}

			if ( ! empty( $args['post_link'] ) ) {
				if ( empty( $project_name ) ) {
					$project_name = $args['post_link'];
				}
				$dataBody['%%project_link%%'] = $this->createProjectLink( $args['post_link'], $project_name );
			}
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );

		if ( empty( $template_source ) || empty( $dataSubject ) || empty( $dataBody ) ) {
			return false;
		}
		$template = $this->fillTemplate(
			$template_source,
			$dataSubject,
			$dataBody
		);
		$res      = $this->send_mail( $args['user']->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '' );

		return $res;
	}

	/***
	 * Contractor Emails
	 */

	/***
	 * Contractor Email 1: signs up
	 *
	 * @param string $notificationSuffix
	 * @param int $destinationType
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail1_2( $notificationSuffix, $destinationType, $args = array() ) {
		if ( empty( $args['user']->user_email ) ) {
			return false;
		}

		$template = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );
		if ( empty( $template ) ) {
			return false;
		}

		$res = $this->send_mail( $args['user']->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '' );

		return $res;
	}

	/***
	 * Contractor Email 2: 1 day after sign up
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail2_2( $args = array() ) {
		$notificationSuffix = isset( $args[0] ) ? $args[0] : false;
		$destinationType    = isset( $args[1] ) ? $args[1] : false;
		if ( empty( $notificationSuffix ) || empty( $destinationType ) ) {
			error_log( print_r( 'processEmail2_2: Not valid parameters. Time: ' . date( 'Y-m-d H:i:s', time() ), true ), 0 );

			return false;
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );
		if ( empty( $template_source ) ) {
			return false;
		}
		$users  = $this->notificationModel::getContractors1DayAfterSignUp( $template_source->id );
		$result = true;

		if ( ! empty( $users ) ) {
			if ( is_array( $users ) ) {
				foreach ( $users as $user ) {

					$args        = [
						'%%personal_name%%' => ! empty( $user->first_name . ' ' . $user->last_name ) ? $user->first_name . ' ' . $user->last_name : $user->display_name,
					];
					$dataBody    = $args;
					$dataSubject = [];

					$template = $this->fillTemplate(
						$template_source,
						$dataSubject,
						$dataBody
					);
					if ( $template->template_body === false ) {
						error_log( print_r( 'Number of inserted data doesn\'t match number of placeholders in the template', true ), 0 );

						return false;
					}
					$log_data = [
						'template_id'  => $template->id,
						'recipient_id' => $user->ID,
						'post_type'    => 'project',
						'post_id'      => $user->project_id,
					];
					$res      = $this->send_mail( $user->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '', $log_data );

					if ( isset( $res ) && false == $res ) {
						$result = $res;
					}
				}
			}
		}

		return $result;
	}

	/***
	 * Contractor Email 3: invitation to bid
	 *
	 * @param string $notificationSuffix
	 * @param int $destinationType
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail3_2( $notificationSuffix, $destinationType, $args = array() ) {
		$dataSubject = $dataBody = [];
		if ( ! empty( $args ) ) {
			if ( ! empty( $args['user'] ) ) {
				$user_name = trim( $args['user']->first_name . ' ' . $args['user']->last_name );
				if ( empty( $user_name ) ) {
					$user_name = $args['user']->display_name;
				}
				$dataBody['%%personal_name%%'] = $user_name;
			}

			if ( ! empty( $args['post'] ) ) {
				$project_name = get_project_title_for( $args['post']->ID, 'freelancer' );
				$dataSubject['%%project_name%%'] = $dataBody['%%project_name%%'] = $project_name;
			}

			if ( ! empty( $args['post_link'] ) ) {
				if ( empty( $project_name ) ) {
					$project_name = $args['post_link'];
				}
				$dataBody['%%project_link%%'] = $this->createProjectLink( $args['post_link'], $project_name );
			}
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );

		if ( empty( $template_source ) || empty( $dataSubject ) || empty( $dataBody ) ) {
			return false;
		}
		$template = $this->fillTemplate(
			$template_source,
			$dataSubject,
			$dataBody
		);
		$res      = $this->send_mail( $args['user']->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '' );

		return $res;
	}

	/***
	 * Contractor Email 4: 2 days after bid invitation and no bid from vendor
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail4_2( $args = array() ) {
		$notificationSuffix = isset( $args[0] ) ? $args[0] : false;
		$destinationType    = isset( $args[1] ) ? $args[1] : false;
		if ( empty( $notificationSuffix ) || empty( $destinationType ) ) {
			error_log( print_r( 'processEmail4_2: Not valid parameters. Time: ' . date( 'Y-m-d H:i:s', time() ), true ), 0 );

			return false;
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );
		$projects        = $this->notificationModel::getContractorsWithNoBid2dayAfterInvitation( $template_source->id );
		$result          = true;

		if ( ! empty( $projects ) ) {
			if ( is_array( $projects ) ) {
				foreach ( $projects as $projectId => $projectData ) {
					$project_name = $projectData['project_name'];
					$link         = get_permalink( $projectId );
					$project_link = $this->createProjectLink( $link, $project_name );
					foreach ( $projectData['users'] as $user ) {

						$args        = [
							'%%personal_name%%' => ! empty( $user->first_name . ' ' . $user->last_name ) ? $user->first_name . ' ' . $user->last_name : $user->display_name,
							'%%project_name%%'  => $project_name,
							'%%project_link%%'  => $project_link
						];
						$dataBody    = $args;
						$dataSubject = [];

						$template = $this->fillTemplate(
							$template_source,
							$dataSubject,
							$dataBody
						);
						if ( $template->template_body === false ) {
							error_log( print_r( 'Number of inserted data doesn\'t match number of placeholders in the template', true ), 0 );

							return false;
						}
						$log_data = [
							'template_id'  => $template->id,
							'recipient_id' => $user->ID,
							'post_type'    => 'project',
							'post_id'      => $projectId,
						];

						$res = $this->send_mail( $user->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '', $log_data );

						if ( isset( $res ) && false == $res ) {
							$result = $res;
						}

					}

				}
			}
		}

		return $result;
	}

	/***
	 * Contractor Email 5: 1 week after bid invitation and no bid
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail5_2( $args = array() ) {
		$notificationSuffix = isset( $args[0] ) ? $args[0] : false;
		$destinationType    = isset( $args[1] ) ? $args[1] : false;
		if ( empty( $notificationSuffix ) || empty( $destinationType ) ) {
			error_log( print_r( 'processEmail5_2: Not valid parameters. Time: ' . date( 'Y-m-d H:i:s', time() ), true ), 0 );

			return false;
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );
		$projects        = $this->notificationModel::getContractorsWithNoBid1WeekAfterInvitation( $template_source->id );
		$result          = true;

		if ( ! empty( $projects ) ) {
			if ( is_array( $projects ) ) {
				foreach ( $projects as $projectId => $projectData ) {
					$project_name     = $projectData['project_name'];
					$link             = get_permalink( $projectId );
					$project_link     = $this->createProjectLink( $link, $project_name );
					$bidding_end_date = $projectData['bidding_end_date'];
					foreach ( $projectData['users'] as $user ) {

						$args        = [
							'%%personal_name%%'    => ! empty( $user->first_name . ' ' . $user->last_name ) ? $user->first_name . ' ' . $user->last_name : $user->display_name,
							'%%project_name%%'     => $project_name,
							'%%project_link%%'     => $project_link,
							'%%bidding_end_date%%' => $bidding_end_date
						];
						$dataBody    = $args;
						$dataSubject = [];

						$template = $this->fillTemplate(
							$template_source,
							$dataSubject,
							$dataBody
						);
						if ( $template->template_body === false ) {
							error_log( print_r( 'Number of inserted data doesn\'t match number of placeholders in the template', true ), 0 );

							return false;
						}
						$log_data = [
							'template_id'  => $template->id,
							'recipient_id' => $user->ID,
							'post_type'    => 'project',
							'post_id'      => $projectId,
						];

						$res = $this->send_mail( $user->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '', $log_data );

						if ( isset( $res ) && false == $res ) {
							$result = $res;
						}

					}

				}
			}
		}

		return $result;
	}

	/***
	 * Contractor Email 6: 1 day before bid deadline reached
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail6_2( $args = array() ) {
		$notificationSuffix = isset( $args[0] ) ? $args[0] : false;
		$destinationType    = isset( $args[1] ) ? $args[1] : false;
		if ( empty( $notificationSuffix ) || empty( $destinationType ) ) {
			error_log( print_r( 'processEmail6_2: Not valid parameters. Time: ' . date( 'Y-m-d H:i:s', time() ), true ), 0 );

			return false;
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );
		$projects        = $this->notificationModel::getContractorsWithNoBid1DayBeforeBidDeadline( $template_source->id );
		$result          = true;

		if ( ! empty( $projects ) ) {
			if ( is_array( $projects ) ) {
				foreach ( $projects as $projectId => $projectData ) {
					$project_name     = $projectData['project_name'];
					$link             = get_permalink( $projectId );
					$project_link     = $this->createProjectLink( $link, $project_name );
					$bidding_end_date = $projectData['bidding_end_date'];
					foreach ( $projectData['users'] as $user ) {

						$args        = [
							'%%personal_name%%'    => ! empty( $user->first_name . ' ' . $user->last_name ) ? $user->first_name . ' ' . $user->last_name : $user->display_name,
							'%%project_name%%'     => $project_name,
							'%%project_link%%'     => $project_link,
							'%%bidding_end_date%%' => $bidding_end_date
						];
						$dataBody    = $args;
						$dataSubject = [];

						$template = $this->fillTemplate(
							$template_source,
							$dataSubject,
							$dataBody
						);
						if ( $template->template_body === false ) {
							error_log( print_r( 'Number of inserted data doesn\'t match number of placeholders in the template', true ), 0 );

							return false;
						}
						$log_data = [
							'template_id'  => $template->id,
							'recipient_id' => $user->ID,
							'post_type'    => 'project',
							'post_id'      => $projectId,
						];

						$res = $this->send_mail( $user->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '', $log_data );

						if ( isset( $res ) && false == $res ) {
							$result = $res;
						}

					}

				}
			}
		}

		return $result;
	}

	/***
	 * Contractor Email 7: 3 days after bid deadline reached and no bid is selected (sent to bidders only)
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail7_2( $args = array() ) {
		$notificationSuffix = isset( $args[0] ) ? $args[0] : false;
		$destinationType    = isset( $args[1] ) ? $args[1] : false;
		if ( empty( $notificationSuffix ) || empty( $destinationType ) ) {
			error_log( print_r( 'processEmail7_2: Not valid parameters. Time: ' . date( 'Y-m-d H:i:s', time() ), true ), 0 );

			return false;
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );
		$users           = $this->notificationModel::getContractorsWithProjectWithNoBids3DayAfterDeadline( $template_source->id );
		$result          = true;

		if ( ! empty( $users ) ) {
			if ( is_array( $users ) ) {
				foreach ( $users as $user ) {
					$project_name = get_project_title_for( $user->project_id, 'freelancer' );
					$link         = get_permalink( $user->project_id );
					$project_link = $this->createProjectLink( $link, $project_name );

					$args        = [
						'%%personal_name%%' => ! empty( $user->first_name . ' ' . $user->last_name ) ? $user->first_name . ' ' . $user->last_name : $user->display_name,
						'%%project_name%%'  => $project_name,
						'%%project_link%%'  => $project_link,
					];
					$dataBody    = $args;
					$dataSubject = [];

					$template = $this->fillTemplate(
						$template_source,
						$dataSubject,
						$dataBody
					);
					if ( $template->template_body === false ) {
						error_log( print_r( 'Number of inserted data doesn\'t match number of placeholders in the template', true ), 0 );

						return false;
					}
					$log_data = [
						'template_id'  => $template->id,
						'recipient_id' => $user->ID,
						'post_type'    => 'project',
						'post_id'      => $user->project_id,
					];
					$res      = $this->send_mail( $user->email, $template->subject, $template->template_body, $filter = array(), $headers = '', $log_data );

					if ( isset( $res ) && false == $res ) {
						$result = $res;
					}

				}
			}
		}

		return $result;
	}

	/***
	 * Contractor Email 8: 1 week after bid deadline reached and no bid is selected (sent to bidders only)
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail8_2( $args = array() ) {
		$notificationSuffix = isset( $args[0] ) ? $args[0] : false;
		$destinationType    = isset( $args[1] ) ? $args[1] : false;
		if ( empty( $notificationSuffix ) || empty( $destinationType ) ) {
			error_log( print_r( 'processEmail8_2: Not valid parameters. Time: ' . date( 'Y-m-d H:i:s', time() ), true ), 0 );

			return false;
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );
		$users           = $this->notificationModel::getContractorsWithProjectWithNoBids1WeekAfterDeadline( $template_source->id );
		$result          = true;

		if ( ! empty( $users ) ) {
			if ( is_array( $users ) ) {
				foreach ( $users as $user ) {
					$project_name = get_project_title_for( $user->project_id, 'freelancer' );
					$link         = get_permalink( $user->project_id );
					$project_link = $this->createProjectLink( $link, $project_name );

					$args        = [
						'%%personal_name%%' => ! empty( $user->first_name . ' ' . $user->last_name ) ? $user->first_name . ' ' . $user->last_name : $user->display_name,
						'%%project_name%%'  => $project_name,
						'%%project_link%%'  => $project_link,
					];
					$dataBody    = $args;
					$dataSubject = [];

					$template = $this->fillTemplate(
						$template_source,
						$dataSubject,
						$dataBody
					);
					if ( $template->template_body === false ) {
						error_log( print_r( 'Number of inserted data doesn\'t match number of placeholders in the template', true ), 0 );

						return false;
					}
					$log_data = [
						'template_id'  => $template->id,
						'recipient_id' => $user->ID,
						'post_type'    => 'project',
						'post_id'      => $user->project_id,
					];
					$res      = $this->send_mail( $user->email, $template->subject, $template->template_body, $filter = array(), $headers = '', $log_data );

					if ( isset( $res ) && false == $res ) {
						$result = $res;
					}

				}
			}
		}

		return $result;
	}

	/***
	 * Contractor Email 9: 2 weeks (and every week thereafter) after bid deadline reached and no bid is selected (sent to bidders only)
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail9_2( $args = array() ) {
		$notificationSuffix = isset( $args[0] ) ? $args[0] : false;
		$destinationType    = isset( $args[1] ) ? $args[1] : false;
		if ( empty( $notificationSuffix ) || empty( $destinationType ) ) {
			error_log( print_r( 'processEmail9_2: Not valid parameters. Time: ' . date( 'Y-m-d H:i:s', time() ), true ), 0 );

			return false;
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );
		$users           = $this->notificationModel::getContractorsWithProjectWithNoBids2WeekAfterDeadline( $template_source->id );
		$result          = true;

		if ( ! empty( $users ) ) {
			if ( is_array( $users ) ) {
				foreach ( $users as $user ) {
					$project_name = get_project_title_for( $user->project_id, 'freelancer' );
					$link         = get_permalink( $user->project_id );
					$project_link = $this->createProjectLink( $link, $project_name );

					$args        = [
						'%%personal_name%%' => ! empty( $user->first_name . ' ' . $user->last_name ) ? $user->first_name . ' ' . $user->last_name : $user->display_name,
						'%%project_name%%'  => $project_name,
						'%%project_link%%'  => $project_link,
					];
					$dataBody    = $args;
					$dataSubject = [];

					$template = $this->fillTemplate(
						$template_source,
						$dataSubject,
						$dataBody
					);
					if ( $template->template_body === false ) {
						error_log( print_r( 'Number of inserted data doesn\'t match number of placeholders in the template', true ), 0 );

						return false;
					}
					$log_data = [
						'template_id'  => $template->id,
						'recipient_id' => $user->ID,
						'post_type'    => 'project',
						'post_id'      => $user->project_id,
					];
					$res      = $this->send_mail( $user->email, $template->subject, $template->template_body, $filter = array(), $headers = '', $log_data );

					if ( isset( $res ) && false == $res ) {
						$result = $res;
					}

				}
			}
		}

		return $result;
	}

	/***
	 * Contractor Email 10: bid is selected
	 *
	 * @param string $notificationSuffix
	 * @param int $destinationType
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail10_2( $notificationSuffix, $destinationType, $args = array() ) {
		$dataSubject = $dataBody = [];
		if ( ! empty( $args ) ) {
			if ( ! empty( $args['user'] ) ) {
				$user_name = trim( $args['user']->first_name . ' ' . $args['user']->last_name );
				if ( empty( $user_name ) ) {
					$user_name = $args['user']->display_name;
				}
				$dataBody['%%personal_name%%'] = $user_name;
			}

			if ( ! empty( $args['post'] ) ) {
				$project_name                    = get_project_title_for( $args['post']->ID, 'freelancer' );
				$dataSubject['%%project_name%%'] = $dataBody['%%project_name%%'] = $project_name;
			}

			if ( ! empty( $args['post_link'] ) ) {
				if ( empty( $project_name ) ) {
					$project_name = $args['post_link'];
				}
				$dataBody['%%project_link%%'] = $this->createProjectLink( $args['post_link'], $project_name );
			}
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );

		if ( empty( $template_source ) || empty( $dataSubject ) || empty( $dataBody ) ) {
			return false;
		}
		$template = $this->fillTemplate(
			$template_source,
			$dataSubject,
			$dataBody
		);
		$res      = $this->send_mail( $args['user']->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '' );

		return $res;
	}

	/***
	 * Contractor Email 11: bid is not selected
	 *
	 * @param string $notificationSuffix
	 * @param int $destinationType
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail11_2( $notificationSuffix, $destinationType, $args = array() ) {
		$dataSubject = $dataBody = [];
		$result      = true;

		if ( ! empty( $args ) ) {
			if ( ! empty( $args['users_bids'] ) && is_array( $args['users_bids'] ) ) {
				foreach ( $args['users_bids'] as $user_bid ) {

					if ( ! empty( $user_bid ) ) {
						$user_bid_name = trim( $user_bid->first_name . ' ' . $user_bid->last_name );
						if ( empty( $user_bid_name ) ) {
							$user_bid_name = $user_bid->display_name;
						}
						$dataBody['%%personal_name%%'] = $user_bid_name;
					}

					if ( ! empty( $args['user_project'] ) ) {
						$user_project_name = trim( $args['user_project']->first_name . ' ' . $args['user_project']->last_name );
						if ( empty( $user_project_name ) ) {
							$user_project_name = $args['user_project']->display_name;
						}
						$dataBody['%%company_name_of_project_owner%%'] = $user_project_name;
					}

					if ( ! empty( $args['post'] ) ) {
						$project_name                    = get_project_title_for( $args['post']->ID, 'freelancer' );
						$dataSubject['%%project_name%%'] = $dataBody['%%project_name%%'] = $project_name;
					}

					if ( ! empty( $args['post_link'] ) ) {
						if ( empty( $project_name ) ) {
							$project_name = $args['post_link'];
						}
						$dataBody['%%project_link%%'] = $this->createProjectLink( $args['post_link'], $project_name );
					}

					$available_projects_list = $this->getContractorRelatedProjects( $user_bid->ID );
					if ( ! empty( $available_projects_list ) ) {
						$dataBody['%%link_to_available_projects%%'] = $available_projects_list;
					}

					$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );

					if ( empty( $template_source ) || empty( $dataSubject ) || empty( $dataBody ) ) {
						return false;
					}
					$template = $this->fillTemplate(
						$template_source,
						$dataSubject,
						$dataBody
					);
					$res      = $this->send_mail( $user_bid->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '' );

					if ( isset( $res ) && false == $res ) {
						$result = $res;
					}
				}

			}
		}

		return $result;
	}

	/***
	 * Contractor Email 12 (after a bid is submitted)
	 *
	 * @param string $notificationSuffix
	 * @param int $destinationType
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail12_2( $notificationSuffix, $destinationType, $args = array() ) {
		$dataSubject = $dataBody = [];
		if ( ! empty( $args ) ) {
			if ( ! empty( $args['user'] ) ) {
				$user_name = trim( $args['user']->first_name . ' ' . $args['user']->last_name );
				if ( empty( $user_name ) ) {
					$user_name = $args['user']->display_name;
				}
				$dataSubject['%%personal_name%%'] = $dataBody['%%personal_name%%'] = $user_name;
			}

			if ( ! empty( $args['post'] ) ) {
				$project_name                    = get_project_title_for( $args['post']->ID, 'freelancer' );
				$dataSubject['%%project_name%%'] = $dataBody['%%project_name%%'] = $project_name;
			}

			if ( ! empty( $args['post_link'] ) ) {
				if ( empty( $project_name ) ) {
					$project_name = $args['post_link'];
				}
				$dataBody['%%project_link%%'] = $this->createProjectLink( $args['post_link'], $project_name );
			}
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );

		if ( empty( $template_source ) || empty( $dataBody ) ) {
			return false;
		}
		$template = $this->fillTemplate(
			$template_source,
			$dataSubject,
			$dataBody
		);
		$res      = $this->send_mail( $args['user']->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '' );

		return $res;
	}

	/***
	 * Contractor mail 13: when a bid acceptance date is set
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail13_2( $args = array() ) {
		$notificationSuffix = isset( $args[0] ) ? $args[0] : false;
		$destinationType    = isset( $args[1] ) ? $args[1] : false;
		$data               = $args[2];
		if ( empty( $notificationSuffix ) || empty( $destinationType ) ) {
			error_log( print_r( 'processEmail13_2: Not valid parameters. Time: ' . date( 'Y-m-d H:i:s', time() ), true ), 0 );

			return false;
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );
		$users           = $this->notificationModel::getBidersForProject( $data['project_id'] );
		$result          = true;

		if ( ! empty( $users->bidders ) ) {
			if ( is_array( $users->bidders ) ) {
				foreach ( $users->bidders as $user ) {
					$project_name = get_project_title_for( $user->project_id, 'freelancer' );
					$owner_name   = ! empty( $users->owner->first_name . ' ' . $users->owner->last_name ) ? $users->owner->first_name . ' ' . $users->owner->last_name : $users->owner->display_name;

					$dataBody = [
						'%%personal_name%%'  => ! empty( $user->first_name . ' ' . $user->last_name ) ? $user->first_name . ' ' . $user->last_name : $user->display_name,
						'%%project_name%%'   => $project_name,
						'%%project_owner%%'  => $owner_name,
						'%%selection_date%%' => $data['selection_date']
					];

					if ( ! empty( $data['message'] ) ) {
						$dataBody['%%owner_message%%'] = '<p>
                            Message from the project owner – ' . $owner_name . '</p><p>' . $data['message'] . '</p>';
					}
					$dataSubject = [];

					$template = $this->fillTemplate(
						$template_source,
						$dataSubject,
						$dataBody
					);
					if ( $template->template_body === false ) {
						error_log( print_r( 'Number of inserted data doesn\'t match number of placeholders in the template', true ), 0 );

						return false;
					}
					$log_data = [
						'template_id'  => $template->id,
						'recipient_id' => $user->ID,
						'post_type'    => 'project',
						'post_id'      => $user->project_id,
					];
					$res      = $this->send_mail( $user->email, $template->subject, $template->template_body, $filter = array(), $headers = '', $log_data );

					if ( isset( $res ) && false == $res ) {
						$result = $res;
					}

				}
			}
		}

		return $result;
	}

	/***
	 * Contractor Email 14: site visit reminder emails
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function processEmail14_2( $args = array() ) {
		$notificationSuffix = isset( $args[0] ) ? $args[0] : false;
		$destinationType    = isset( $args[1] ) ? $args[1] : false;
		if ( empty( $notificationSuffix ) || empty( $destinationType ) ) {
			error_log( print_r( 'processEmail14_2: Not valid parameters. Time: ' . date( 'Y-m-d H:i:s', time() ), true ), 0 );

			return false;
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );
		$projects        = $this->notificationModel::getContractors1DayBeforeSiteVisit( $template_source->id );
		$result          = true;

		if ( ! empty( $projects ) ) {
			if ( is_array( $projects ) ) {
				foreach ( $projects as $project ) {
					$project_name = get_project_title_for( $project->project_id, 'freelancer' );
					foreach ( $project->visitors as $visitor_id => $visitor ) {
						if ( $visitor == false ) {
							continue;
						}
						$dataBody = [
							'%%personal_name%%'    => ! empty( $visitor->first_name . ' ' . $visitor->last_name ) ? $visitor->first_name . ' ' . $visitor->last_name : $visitor->display_name,
							'%%project_name%%'     => $project_name,
							'%%blogname%%'         => get_option( 'blogname' ),
							'%%site_visit_date%%'  => $project->site_visit_date,
							'%%address%%'          => $project->address,
							'%%project_link%%'     => $this->createProjectLink( get_permalink( $project->post_id ), $project_name ),
							'%%site_visit_phone%%' => $project->site_visit_phone
						];

						$dataSubject = [
							'%%project_name%%' => $project_name
						];

						$template = $this->fillTemplate(
							$template_source,
							$dataSubject,
							$dataBody
						);
						if ( $template->template_body === false ) {
							return false;
						}
						$log_data = [
							'template_id'  => $template->id,
							'recipient_id' => $visitor_id,
							'post_type'    => 'project',
							'post_id'      => $project->post_id,
						];
						$res      = $this->send_mail( $visitor->user_email, $template->subject, $template->template_body, $filter = array(), $headers = '', $log_data );

						if ( isset( $res ) && false == $res ) {
							$result = $res;
						}

					}
				}
			}
		}

		return $result;
	}

	public function fillTemplateAndSendEmail( $notificationSuffix, $destinationType, $args ) {
		$dataSubject = $dataBody = [];
		if ( ! empty( $args ) ) {
			if ( ! empty( $args['data_body'] ) ) {
				$dataBody = $args['data_body'];
			}

			if ( ! empty( $args['data_subject'] ) ) {
				$dataSubject = $args['data_subject'];
			}
		}
		$template_source = $this->notificationModel::getTemplate( $notificationSuffix, $destinationType );

		$template = $this->fillTemplate(
			$template_source,
			$dataSubject,
			$dataBody
		);

		$headers = isset( $args['headers'] ) ? $args['headers'] : '';

		$res = $this->send_mail( $args['to'], $template->subject, $template->template_body, array(), $headers );

		return $res;
	}

	protected function createProjectLink ( $project_link, $project_name, $target_link = '_blank' ) {
		return '<a href="' . $project_link . '" target="' . $target_link . '">' . $project_name . '</a>';
	}

}
