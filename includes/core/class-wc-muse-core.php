<?php

/**
 * Core wp connection manager to muse.
 *
 * @since      1.0.0
 * @package    Wc_Muse
 * @subpackage Wc_Muse/includes
 * @author     Dinkum Interactive <support@dinkuminteractive.com>
 */
class Wc_Muse_Core {

	public function validate_token( $token ) {

		$wc_muse_order = new Wc_Muse_Orders();

		$test = $wc_muse_order->get_orders_to_export( 10, 1 );

		echo "<pre>";
		var_dump($test);
		echo "</pre>";

		exit;
		
		/*	@TODO: create validation process.
		 */
		return false;

	}

}
