<?php

/**
 * Fired during plugin deactivation
 *
 * @link       support@dinkuminteractive.com
 * @since      1.0.0
 *
 * @package    Wc_Muse
 * @subpackage Wc_Muse/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Wc_Muse
 * @subpackage Wc_Muse/includes
 * @author     Dinkum Interactive <support@dinkuminteractive.com>
 */
class Wc_Muse_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		$timestamp = wp_next_scheduled( 'wc_muse_send_order' );
		wp_unschedule_event( $timestamp, 'wc_muse_send_order' );
	}

}
