<?php
require_once 'NotificationModel.php';

class ClassNotificationBase extends NotificationModel {
	protected $notificationModel;
	protected $processedMails;

	public function __construct() {
		parent::__construct();
		$this->notificationModel = new NotificationModel();
		$this->processedMails  = [
			// Builders
			[
				'interval_name' => 'hourly',
				'suffix'        => 'Email2',
				'destination'   => 1,
				'interval_sec'  => 60 * 60,
				'interval_desc' => 'hourly',
			],
			[
				'interval_name' => 'every_6_hours',
				'suffix'        => 'Email3',
				'destination'   => 1,
				'interval_sec'  => 6 * 60 * 60,
				'interval_desc' => 'Every 6 hours',
			],
			[
				'suffix'        => 'Email4',
				'destination'   => 1,
				'interval_name' => 'daily',
				'interval_sec'  => 24 * 60 * 60,
				'interval_desc' => 'Every day',
			],
			[
				'suffix'        => 'Email5',
				'destination'   => 1,
				'interval_name' => 'daily',
				'interval_sec'  => 24 * 60 * 60,
				'interval_desc' => 'Every day',
			],
			[
				'suffix'        => 'Email9',
				'destination'   => 1,
				'interval_name' => 'every_6_hours',
				'interval_sec'  => 6 * 60 * 60,
				'interval_desc' => 'Every 6 hours',
			],
			[
				'suffix'        => 'Email10',
				'destination'   => 1,
				'interval_name' => 'every_6_hours',
				'interval_sec'  => 6 * 60 * 60,
				'interval_desc' => 'Every 6 hours',
			],
			[
				'suffix'        => 'Email11',
				'destination'   => 1,
				'interval_name' => 'every_6_hours',
				'interval_sec'  => 6 * 60 * 60,
				'interval_desc' => 'Every 6 hours',
			],
			[
				'suffix'        => 'Email12',
				'destination'   => 1,
				'interval_name' => 'daily',
				'interval_sec'  => 24 * 60 * 60,
				'interval_desc' => 'Every day',
			],
			[
				'suffix'        => 'Email13',
				'destination'   => 1,
				'interval_name' => 'daily',
				'interval_sec'  => 24 * 60 * 60,
				'interval_desc' => 'Every day',

			],
			[
				'suffix'        => 'Email15',
				'destination'   => 1,
				'interval_name' => 'daily',
				'interval_sec'  => 24 * 60 * 60,
				'interval_desc' => 'Every day',

			],

			// Contractors

			[
				'suffix'        => 'Email2',
				'destination'   => 2,
				'interval_name' => 'every_6_hours',
				'interval_sec'  => 6 * 60 * 60,
				'interval_desc' => 'Every 6 hours',
			],
			[
				'suffix'        => 'Email4',
				'destination'   => 2,
				'interval_name' => 'every_6_hours',
				'interval_sec'  => 6 * 60 * 60,
				'interval_desc' => 'Every 6 hours',
			],
			[
				'suffix'        => 'Email5',
				'destination'   => 2,
				'interval_name' => 'daily',
				'interval_sec'  => 24 * 60 * 60,
				'interval_desc' => 'Every day',
			],
			[
				'suffix'        => 'Email6',
				'destination'   => 2,
				'interval_name' => 'every_6_hours',
				'interval_sec'  => 6 * 60 * 60,
				'interval_desc' => 'Every 6 hours',
			],
			[
				'suffix'        => 'Email7',
				'destination'   => 2,
				'interval_name' => 'every_6_hours',
				'interval_sec'  => 6 * 60 * 60,
				'interval_desc' => 'Every 6 hours',
			],
			[
				'suffix'        => 'Email8',
				'destination'   => 2,
				'interval_name' => 'every_6_hours',
				'interval_sec'  => 6 * 60 * 60,
				'interval_desc' => 'Every 6 hours',
			],
			[
				'suffix'        => 'Email9',
				'destination'   => 2,
				'interval_name' => 'daily',
				'interval_sec'  => 24 * 60 * 60,
				'interval_desc' => 'Every day',

			],
			[
				'suffix'        => 'Email13',
				'destination'   => 2,
				'interval_name' => 'daily',
				'interval_sec'  => 24 * 60 * 60,
				'interval_desc' => 'Every day',

			],
			[
				'suffix'        => 'Email14',
				'destination'   => 2,
				'interval_name' => 'daily',
				'interval_sec'  => 24 * 60 * 60,
				'interval_desc' => 'Every day',

			],

		];
	}

	public function getProcessedEmails() {
		return $this->processedMails;
	}

	public function getFillTemplate( $template_source, $dataSubject = null, $dataBody = null ) {
		return $this->fillTemplate( $template_source, $dataSubject, $dataBody );
	}

	protected function fillTemplate( $template_source, $dataSubject = null, $dataBody = null ) {
		$template                = clone $template_source;
		$template->template_body = apply_filters( 'the_content', $template->template_body );

		if ( ! empty( $template ) ) {

			if ( ! empty( $dataBody ) ) {
				if ( $this->isValidParameters( $template->template_body, $dataBody ) ) {
					$template->template_body = $this->replaceVars( $template->template_body, $dataBody );
				} else {
					return false;
				}
			}
			if ( ! empty( $dataSubject ) ) {
				if ( $this->isValidParameters( $template->subject, $dataSubject ) ) {
					$template->subject = $this->replaceVars( $template->subject, $dataSubject );
				} else {
					return false;
				}
			}
		}

		return $template;
	}

	/*
	 *  $vars should be in the following format:
	 *  $vars = array('%%NAME%%'=>'Bob', '%%USERNAME%%'=>'user_name');
	 */

	private function replaceVars( $template, $vars ) {
		return str_replace( array_keys( $vars ), $vars, $template );
	}

	private function isValidParameters( $template, $data ) {
		return true;
		if ( substr_count( $template, '%s' ) != count( $data ) ) {
			return false; // ERROR
		}

		return true;
	}

	public function prepareTemplate( $template ) {
		$template->subject       = preg_replace( "/%%[^%{2}]*%%/", "%s", $template->subject );
		$template->template_body = preg_replace( "/%%[^%{2}]*%%/", "%s", $template->template_body );

		return $template;
	}

	public function send_mail( $to, $subject, $content, $filter = array(), $headers = '', $log_data = [] ) {

		if ( $headers == '' ) {
			$headers .= "From: " . get_option( 'blogname' ) . " < " . get_option( 'admin_email' ) . "> \r\n";
		}

		$content = html_entity_decode( (string) $content, ENT_QUOTES, 'UTF-8' );
		$subject = html_entity_decode( (string) $subject, ENT_QUOTES, 'UTF-8' );

		add_filter( 'wp_mail_content_type', function ( $content_type ) {
			return 'text/html';
		} );

		$result = wp_mail( $to, $subject, $this->get_mail_header() . $content . $this->get_mail_footer(), $headers );
		if ( $result && ! empty( $log_data ) ) { // log success notification if everything is ok
			$result = $this->addNotificationLogRecord( $log_data );
		}
		remove_filter( 'wp_mail_content_type', function ( $content_type ) {
			return 'text/html';
		} );

		return $result;
	}

	function get_mail_header() {

		$mail_header = apply_filters( 'am_get_mail_header', '' );
		if ( $mail_header != '' ) {
			return $mail_header;
		}

		$logo_url = get_stylesheet_directory_uri() . "/images/blue_logo.svg";

		$logo_url = apply_filters( 'am_mail_logo_url', $logo_url );

		$customize = et_get_customization();
//        $customize = '';

		$mail_header = '<html>
                        <head>
                        </head>
                        <body style="font-family: Arial, sans-serif;font-size: 0.9em;margin: 0; padding: 0; color: #222222;">
                        <div style="margin: 0 auto; width:600px; border: 1px solid ' . $customize['background'] . '">
                            <table width="100%" cellspacing="0" cellpadding="0">
                            <tr style="background: ' . $customize['header'] . '; height: 63px; vertical-align: middle;">
                                <td style="padding: 10px 5px 10px 20px; width: 20%;">
                                    <img style="max-height: 100px" src="' . $logo_url . '" alt="' . get_option( 'blogname' ) . '">
                                </td>
                                <td style="padding: 10px 20px 10px 5px">
                                    <span style="text-shadow: 0 0 1px #151515; color: #b0b0b0;">' . get_option( 'blogdescription' ) . '</span>
                                </td>
                            </tr>
                            <tr><td colspan="2" style="height: 5px; background-color: ' . $customize['background'] . ';"></td></tr>
                            <tr>
                                <td colspan="2" style="background: #ffffff; color: #222222; line-height: 18px; padding: 10px 20px;">';


		return $mail_header;
	}

	/**
	 * return mail footer html template
	 */
	function get_mail_footer() {

		$mail_footer = apply_filters( 'am_get_mail_footer', '' );
		if ( $mail_footer != '' ) {
			return $mail_footer;
		}

		$mail_footer = '</td>
                        </tr>
                        </table>
                    </div>
                    </body>
                    </html>';

		return $mail_footer;
	}

	protected function checkArgs( $args, $template ) {
		return count( $args ) && ( count( $args ) == substr_count( $template, '%s' ) );
	}

	protected function addNotificationLogRecord( $log_data ) {
		return $this->notificationModel::logNotification( $log_data );
	}

	protected function getContractorRelatedCategories( $user_id ) {
		$user_id = (int) $user_id;
		if ( empty( $user_id ) ) {
			return false;
		}

		$profile_args       = array(
			'author'         => $user_id,
			'post_type'      => PROFILE,
			'posts_per_page' => 1
		);
		$profiles           = get_posts( $profile_args );
		$profile_id         = $profiles[0]->ID;
		$profile_categories = wp_get_object_terms( $profile_id, 'project_category', array( 'fields' => 'ids' ) );
		foreach ( $profile_categories as $key => $value ) {
			$term = get_term( $value, 'project_category' );
			if ( $term->parent && ! in_array( $term->parent, $profile_categories ) ) {
				array_push( $profile_categories, $term->parent );
			}
		}

		return $profile_categories;
	}

	protected function getContractorRelatedProjects( $user_id ) {
		$user_id = (int) $user_id;
		if ( empty( $user_id ) ) {
			return false;
		}

		$profile_categories = $this->getContractorRelatedCategories( $user_id );

		if ( empty( $profile_categories ) ) {
			return false;
		}

		$args = array(
			'post_type'   => PROJECT,
			'post_status' => 'publish',
			'tax_query'   => array(
				array(
					'taxonomy' => 'project_category',
					'field'    => 'term_id',
					'terms'    => $profile_categories,
					'operator' => 'IN'
				)
			),
		);

		$projects_list = $projects = '';

		$query = new WP_Query( $args );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$projects .= '<li><a href="' . get_permalink() . '" target="_blank">' . get_the_title() . '</a></li>';
			}
		}

		if ( ! empty( $projects ) ) {
			$projects_list .= '<ul>' . $projects . '</ul>';
		}

		return $projects_list;
	}

	public function resetDeadlineLog( $projectId ) {
		$templateIds    = $this->notificationModel::templateIdsByTemplateNames( [ 'Email11', 'Email12', 'Email13' ], 1 );
		$clearLogResult = $this->notificationModel::resetLogOnChangeDeadline( $projectId, $templateIds );

		return $clearLogResult;

	}

}