<?php
if ( isset( $_POST ) ) {

	// Test outbound order
	if ( isset( $_POST['test_outbound_order'] ) ) {

		$order_id = sanitize_text_field( $_POST['order_id'] );

		$wc_order = new WC_Order( $order_id );

		$wc_muse_orders = new Wc_Muse_Orders();

		$content_order = $wc_muse_orders->convert_wc_order( $wc_order );

		$debug['content'][] = $content_order;

		$debug['content'][] = '####################  Order sent to muse #############';
		$debug['content'][] = json_encode( array( 'order_data' => $content_order ) );

	}

	// Test outbound request
	if ( isset( $_POST['test_outbound_request'] ) ) {

		$order_id = sanitize_text_field( $_POST['order_id'] );

		$wc_muse_orders = new Wc_Muse_Orders();

		$wc_order = new WC_Order( $order_id );

		try {

			$debug['response'] = $wc_muse_orders->export_order( $wc_order );

		} catch ( Exception $e ) {

			$debug['response'] = 'Cannot connect';

		}
	}

	// Test Changing order status
	if ( isset( $_POST['test_change_order_status'] ) ) {

		$order_id = sanitize_text_field( $_POST['order_id'] );

		$wc_order = new WC_Order( $order_id );

		$new_status = get_option( 'wc-muse-order_status_processed' );

		$order = wc_muse_orders::update_status( $wc_order, $new_status );

		$debug['response'] = $order;

	}

	// Test Changing order status
	if ( isset( $_POST['test_get_orders'] ) ) {

		$wc_muse_orders = Wc_Muse_Orders::get_instance();

		$wc_muse_core = new Wc_Muse_Core();

		$debug['response'] = $wc_muse_core->export_orders();

	}

	// Test Changing order status
	if ( isset( $_POST['test_get_orders_with_status'] ) ) {

		$wc_muse_orders = Wc_Muse_Orders::get_instance();

		$debug['response'] = $wc_muse_orders->get_wc_orders();

	}

	// Test Changing order status
	if ( isset( $_POST['test_get_order_from_muse'] ) ) {

		$muse_order_id = sanitize_text_field( $_POST['order_id'] );

		$wc_muse_orders = Wc_Muse_Orders::get_instance();

		$debug['response'] = $wc_muse_orders->get_muse_order( $muse_order_id );

	}

	// Test Export order
	if ( isset( $_POST['test_export_order_to_muse'] ) ) {

		$muse_order_id = sanitize_text_field( $_POST['order_id'] );

		$wc_muse_orders = Wc_Muse_Orders::get_instance();

		$wc_order = wc_get_order( $muse_order_id );

		$debug['response'] = $wc_muse_orders->export_order( $wc_order );

	}

	// Test Export customer
	if ( isset( $_POST['test_export_customer_to_muse'] ) ) {

		$customer_id = sanitize_text_field( $_POST['customer_id'] );

		// $customer_manager = new Wc_Muse_Customers();

		// $response = $customer_manager->export_customer( (int) $customer_id );

		$connector = new Wc_Muse_Connector();

		$organization_id = $connector->organization_id;

		$content = array(
			'email' => 'sean.michael.piettojo@gmail.com',
		);

		$response = $connector->post( "integrations/{$organization_id}/profiles", $content );

		$debug['response'] = $response;
	}

	// Test Export customer
	if ( isset( $_POST['sync_muse_profiles'] ) ) {

		$wp_profiles = new Wc_Muse_Wp_Profile();

		$debug['response'] = $wp_profiles->process_muse_profiles();
	}
}

/*
  @NOTE: Use $debug['content'], $debug['response'] to debug.
 */
?>

<div class="wrap">

	<h1><?php _e( 'WooCommerce Muse Testing', 'wc-muse' ); ?></h1>

	<!-- Change order status -->
	<h2><?php _e( 'Export Orders to Muse', 'wc-muse' ); ?></h2>
	<form method="post">
		<input type="hidden" name="test_get_orders" value="1">
		<button type="submit" class="button button-primary"><?php _e( 'Export all Orders to Muse', 'wc-muse' ); ?></button>
	</form>


	<hr>
	<hr>

	<!-- Testing order -->
	<h2><?php _e( 'Outbound Order', 'wc-muse' ); ?></h2>
	<form method="post">
		<input type="hidden" name="test_outbound_order" value="1">
		<p><input type="text" name="order_id"></p>
		<button type="submit" class="button button-primary"><?php _e( 'Test', 'wc-muse' ); ?></button>
	</form>

	<!-- Testing request -->
	<h2><?php _e( 'Outbound Request', 'wc-muse' ); ?></h2>
	<form method="post">
		<input type="hidden" name="test_outbound_request" value="1">
		<p><input type="text" name="order_id"></p>
		<button type="submit" class="button button-primary"><?php _e( 'Test', 'wc-muse' ); ?></button>
	</form>

	<!-- Change order status -->
	<h2><?php _e( 'Change order status', 'wc-muse' ); ?></h2>
	<form method="post">
		<input type="hidden" name="test_change_order_status" value="1">
		<p><input type="text" name="order_id"></p>
		<button type="submit" class="button button-primary"><?php _e( 'Test', 'wc-muse' ); ?></button>
	</form>

	<!-- Change order status -->
	<h2><?php _e( 'Get orders (Without export)', 'wc-muse' ); ?></h2>
	<form method="post">
		<input type="hidden" name="test_get_orders_with_status" value="1">
		<button type="submit" class="button button-primary"><?php _e( 'Test', 'wc-muse' ); ?></button>
	</form>

	<!-- Read order from muse -->
	<h2><?php _e( 'Read order from Muse (Fixed order ID)', 'wc-muse' ); ?></h2>
	<form method="post">
		<input type="hidden" name="test_get_order_from_muse" value="1">
		<p><input type="text" name="order_id"></p>
		<button type="submit" class="button button-primary"><?php _e( 'Test', 'wc-muse' ); ?></button>
	</form>

	<!-- Export order to muse -->
	<h2><?php _e( 'Export order to Muse', 'wc-muse' ); ?></h2>
	<form method="post">
		<input type="hidden" name="test_export_order_to_muse" value="1">
		<p><input type="text" name="order_id"></p>
		<button type="submit" class="button button-primary"><?php _e( 'Test', 'wc-muse' ); ?></button>
	</form>

	<!-- Export customer to muse -->
	<h2><?php _e( 'Export customer to Muse', 'wc-muse' ); ?></h2>
	<form method="post">
		<input type="hidden" name="test_export_customer_to_muse" value="1">
		<p><input type="text" name="customer_id"></p>
		<button type="submit" class="button button-primary"><?php _e( 'Test', 'wc-muse' ); ?></button>
	</form>


	<!-- SYNC users -->
	<!-- <hr>
	<hr>
	<h2><?php _e( 'Sync WP users with Muse profiles', 'wc-muse' ); ?></h2>
	<form method="post">
		<input type="hidden" name="sync_muse_profiles" value="1">
		<button type="submit" class="button button-primary"><?php _e( 'Sync Muse Profiles', 'wc-muse' ); ?></button>
	</form> -->


	<!-- Results -->
	<?php if ( isset( $debug ) ) : ?>

		<h2>Value - $_POST</h2>
		<pre style="background-color: #fff; padding: 20px; width: 50%; overflow: scroll;"><?php var_dump( $_POST ); ?></pre>

		<?php if ( isset( $debug['content'] ) ) : ?>
			<h2>Content</h2>
			<pre style="background-color: #fff; padding: 20px; width: 50%;"><?php var_dump( $debug['content'] ); ?></pre>
		<?php endif; ?>

		<?php if ( isset( $debug['response'] ) ) : ?>
			<h2>Response</h2>
			<pre style="background-color: #fff; padding: 20px; width: 50%;"><?php var_dump( $debug['response'] ); ?></pre>
		<?php endif; ?>

	<?php endif; ?>

</div>
