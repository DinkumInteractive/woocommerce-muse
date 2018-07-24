<?php

// Exit if accessed directly 
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class that manages all cron activities.
 *
 * @since      1.0.0
 */
class Wc_Muse_Cron {

	private $cron_enabled;

	private $cron_interval;

	private $schedule_recurrence = 'cron_muse_interval';

	/**
	 * Initialize the plugin.
	 *
	 * @since      1.0.0
	 */
	function __construct() {

		$this->cron_enabled = ( get_option( 'wc-muse-enable_cron' ) === 'yes' );
		$this->cron_interval = get_option( 'wc-muse-cron_in_minute', 5 );

	}

	public function add_cron_schedules( $schedules ) {

		$interval = (int) $this->cron_interval;

		if ( $interval ) {

			$interval = $interval <= 5 ? 5 : $interval;

			$schedules[$this->schedule_recurrence] = array(
				'interval'  => ($interval * 60), 
				'display'   => __( sprintf( 'Send order every %s Minutes', $interval ), 'wc-muse' )
			);

		}

		return $schedules;

	}


	/**
	 * Schedule the event

	 * @return void
	 */
	public function send_order_schedule() {

		if ( wp_next_scheduled( 'wc_muse_send_order' ) ) return;

		if ( ! $this->cron_enabled ) return;

		$schedules = wp_get_schedules();

		if ( isset( $schedules[$this->schedule_recurrence] ) ) {
			wp_schedule_event( time(), $this->schedule_recurrence, 'wc_muse_send_order' );
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
