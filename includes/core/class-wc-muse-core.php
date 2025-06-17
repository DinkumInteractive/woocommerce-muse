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

	public function export_orders() {

		$wc_muse_orders = Wc_Muse_Orders::get_instance();

		$export = array();

		$page = 1;

		while ( $orders = $wc_muse_orders->get_wc_orders( 10, $page ) ) {

			if ( ! $orders ) break;

			foreach ( $orders as $order ) {

				$export[] = $wc_muse_orders->export_order( $order );

			}

			$page++;

		}

		return $export;

	}

	public function change_order_status_success( $wc_muse_order ) {

		$wc_muse_orders = Wc_Muse_Orders::get_instance();

		$new_status = get_option( 'wc-muse-order_status_processed' );

		$wc_muse_orders->update_status( $wc_muse_order, $new_status );
	}

	public function change_order_status_failed( $wc_muse_order ) {

		$wc_muse_orders = Wc_Muse_Orders::get_instance();

		$new_status = get_option( 'wc-muse-order_status_failed' );

		$wc_muse_orders->update_status( $wc_muse_order, $new_status );
	}

	public function update_success_meta( $wc_muse_order, $response ) {

		if ( ! method_exists( $wc_muse_order, 'get_id' ) ) {
			wc_get_logger()->error( sprintf( 'Get ID method does not exists in: %s', serialize( $wc_muse_order ) ), array( 'source' => 'woocommerce-muse' ) );
			return;
		}

		if ( ! isset( $response->id ) ) {
			wc_get_logger()->error( sprintf( 'Response object does not have attribute `id`: %s', serialize( $wc_muse_order ) ), array( 'source' => 'woocommerce-muse' ) );
		}

		if ( isset( $response->id ) ) {

			update_post_meta( $wc_muse_order->get_id(), '_wc_muse_order_export_success', true );

			update_post_meta( $wc_muse_order->get_id(), '_wc_muse_order_id', sanitize_text_field( $response->id ) );

		} else {

			$this->change_order_status_failed( $wc_muse_order );

			// update_post_meta( $wc_muse_order->get_id(), '_wc_muse_order_export_success', false );
			// update_post_meta( $wc_muse_order->get_id(), '_wc_muse_order_id', sprintf( 'Response object does not have attribute `id`: %s', serialize( $wc_muse_order ) ) );
		}
	}

	public function update_failed_meta( $wc_muse_order, $response ) {

		if ( !method_exists( $wc_muse_order, 'get_id' ) ) {
			wc_get_logger()->error( sprintf( 'Get ID method does not exists in: %s', serialize( $wc_muse_order ) ), array( 'source' => 'woocommerce-muse' ) );
			return;
		}

		update_post_meta( $wc_muse_order->get_id(), '_wc_muse_order_export_failed', $response );
	}

	public static function test_cron_run($title=false) {

		/*
		*/
		ob_start();
		$wc_muse_orders = Wc_Muse_Orders::get_instance();
		var_dump($wc_muse_orders);
		$content = ob_get_clean();

		$title = ($title?$title:'test');

		$postarr = array(
			'post_title' => $title,
			'post_content' => $content,
		);

		wp_insert_post( $postarr );

	}

}
