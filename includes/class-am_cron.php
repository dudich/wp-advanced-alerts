<?php

/**
 */
class Am_Cron {

	static $DEBUG = 0;

	static $opts;

	protected $id;

	function __construct( $args ) {

		if ( empty( $args['events'] ) ) {
			wp_die( 'ERROR: Am_Cron events parameter is not set. ' . print_r( debug_backtrace(), 1 ) );
		}

		$args_def = [
			'id'            => implode( '--', array_keys( $args['events'] ) ),
			'auto_activate' => true,
			'events'        => [
				'hook_name' => [
					'start_time'    => 0,
					'args'          => array(), // args for callback function
					'callback'      => [ __CLASS__, 'default_callback' ],
					'interval_name' => '',
					'interval_sec'  => 0,
					'interval_desc' => '',
				],
			],
		];

		$event_def = $args_def['events']['hook_name'];
		unset( $args_def['events'] );

		$args = array_merge( $args_def, $args );
		foreach ( $args['events'] as & $events ) {
			$events = array_merge( $event_def, $events );
		}
		unset( $events );

		$args = (object) $args;
//        error_log(print_r($args->events, true), 0);
		if ( ! $this->id = $args->id ) {
			wp_die( 'ERROR: Am_Cron wrong init: id not set. ' . print_r( $args, 1 ) );
		}

		self::$opts[ $this->id ] = $args;

		// after 'self::$opts' set
		add_filter( 'cron_schedules', [ $this, 'add_intervals' ] );

		// after 'cron_schedules'
		if ( ! empty( $args->auto_activate ) && is_admin() ) {
			self::activate( $this->id );
		}

		foreach ( $args->events as $hook => $data ) {
			add_action( $hook, $data['callback'] );
		}

		if ( self::$DEBUG && defined( 'DOING_CRON' ) && DOING_CRON ) {
			add_action( 'wp_loaded', function () {
				echo 'Current time: ' . time() . "\n\n\n" . 'Existing Intervals:' . "\n" . print_r( wp_get_schedules(), 1 ) . "\n\n\n" . print_r( _get_cron_array(), 1 );
			} );
		}

	}

	function add_intervals( $schedules ) {
		foreach ( self::$opts[ $this->id ]->events as $hook => $data ) {
			if ( ! $data['interval_sec'] || isset( $schedules[ $data['interval_name'] ] ) ) {
				continue;
			}

			$schedules[ $data['interval_name'] ] = array(
				'interval' => $data['interval_sec'],
				'display'  => $data['interval_desc'],
			);
		}

		return $schedules;
	}

	// Add cron task
	static function activate( $id = '' ) {
		$opts = $id ? array( $id => self::$opts[ $id ] ) : self::$opts;

		foreach ( $opts as $opt ) {
			foreach ( $opt->events as $hook => $data ) {
				if ( ! wp_next_scheduled( $hook, $data['args'] ) ) {
					wp_schedule_event( ( $data['start_time'] ?: time() ), $data['interval_name'], $hook, $data['args'] );
				}
			}
		}
	}

	// Delete cron task
	static function deactivate( $id = '' ) {
		$opts = $id ? array( $id => self::$opts[ $id ] ) : self::$opts;

		foreach ( $opts as $opt ) {
			foreach ( $opt->events as $hook => $data ) {
				wp_clear_scheduled_hook( $hook, $data['args'] );
			}
		}
	}

	static function default_callback() {
		echo "ERROR: One of Am_Cron callback function is not set.\n\nAm_Cron::\$opts - " . print_r( self::$opts, 1 ) . "\n\n\n\n" . print_r( _get_cron_array(), 1 );
	}

}