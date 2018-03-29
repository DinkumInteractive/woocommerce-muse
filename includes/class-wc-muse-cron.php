<?php

// Exit if accessed directly 
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class that manages all cron activities.
 *
 * @since      0.4.1
 */
class Wc_Muse_Cron {

	private $cron_enabled;

	private $cron_interval;

	private $schedule_recurrence = 'cron_muse_interval';

	/**
	 * Initialize the plugin.
	 *
	 * @since     0.4.1
	 * @since     0.4.2 - Add wc version validation before using the logger class.
	 */
	function __construct() {

		$this->cron_enabled = ( get_option( 'wc-muse-enable_cron' ) === 'yes' );
		$this->cron_interval = get_option( 'wc-muse-cron_in_minute' );

	}

	public function add_cron_schedules( $schedules ) {

		if ( $this->cron_enabled ) {

			$interval = (int) $this->cron_interval;

			if ( $interval ) {

				$interval = $interval <= 5 ? 5 : $interval;

				$schedules[$this->schedule_recurrence] = array(
					'interval'  => $this->cron_interval * 60, 
					'display'   => __( sprintf( 'Send order every %s Minutes', $interval ), 'wc-muse' )
				);

			}

		} elseif ( ! $this->cron_enabled  && isset( $schedules[$this->schedule_recurrence] ) ) {

			unset( $schedules[$this->schedule_recurrence] );

		}

		return $schedules;

	}


	/**
	 * Schedule the event

	 * @return void
	 */
	public function send_order_schedule() {

		//	Check if event scheduled before
		if ( $this->cron_enabled && ! wp_next_scheduled( 'wc_muse_send_order' ) ) {

			$schedules = wp_get_schedules();

			if ( isset( $schedules[$this->schedule_recurrence] ) ) {
				//	Shedule event to run after the time set in settings page
				wp_schedule_event ( time(), $this->schedule_recurrence, 'wc_muse_send_order' );
			} else {
				//	Remove scheduled event if recurrense doesn't exists
				wp_clear_scheduled_hook( 'wc_muse_send_order' );
			}

		} elseif ( ! $this->cron_enabled && wp_next_scheduled( 'wc_muse_send_order' ) ) {

			//	Remove scheduled event
			$this->uneschedule_wc_muse_send_order();

		}

	}

	public function uneschedule_wc_muse_send_order() {
		wp_clear_scheduled_hook( 'wc_muse_send_order' );
	}

	public function on_settings_saved( $old_value, $new_value ) {
		if ( $old_value !== $new_value ) {
			//	If settings value was changed, we need to remove the cron
			$this->uneschedule_wc_muse_send_order();
		}
	}

	/*	@TODO: send order via cron here
	 */
	public function send_order() {

		$time = current_time( 'mysql' );

		do_action( 'wc_muse_cron_send_order', $time );
		
	}

}
