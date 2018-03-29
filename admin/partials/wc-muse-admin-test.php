<?php 
if ( isset( $_POST ) ){

	// 	Test outbound order
	if ( isset( $_POST['test_outbound_order'] ) ) {

		$order_id = sanitize_text_field( $_POST['order_id'] );

		$wc_order = new WC_Order( $order_id );

		$wc_muse_orders = new Wc_Muse_Orders();

		$content_order = $wc_muse_orders->convert_wc_order( $wc_order );

		$debug['content'] = $content_order;

	}

	// 	Test outbound request
	if ( isset( $_POST['test_outbound_request'] ) ) {

		$order_id = sanitize_text_field( $_POST['order_id'] );

		$wc_muse_orders = new Wc_Muse_Orders();

		$wc_order = new WC_Order( $order_id );

		try {

			$debug['response'] = $wc_muse_orders->export_order( $wc_order );
			
		} catch (Exception $e) {
			
			$debug['response'] = 'Cannot connect';

		}

	}

	// 	Test Changing order status
	if ( isset( $_POST['test_change_order_status'] ) ) {

		$order_id = sanitize_text_field( $_POST['order_id'] );

		$order = wc_muse_orders::complete_order( $order_id );

		update_post_meta( $order_id, '_muse_response', '1' );

		$debug['response'] = $order;

	}

	// 	Test Changing order status
	if ( isset( $_POST['test_get_orders'] ) ) {

		$wc_muse_orders = Wc_Muse_Orders::get_instance();

		$wc_muse_core = new Wc_Muse_Core();

		$debug['response'] = $wc_muse_core->export_orders();

	}

}

/*	@NOTE: Use $debug['content'], $debug['response'] to debug.
 */
 ?>

<div class="wrap">
	
	<h1><?php _e( 'WooCommerce Muse Testing', 'wc-muse' ) ?></h1>
	
	<!-- Testing order -->
	<h2><?php _e( 'Outbound Order', 'wc-muse' ) ?></h2>
	<form method="post">
		<input type="hidden" name="test_outbound_order" value="1">
		<p><input type="text" name="order_id"></p>
		<button type="submit" class="button button-primary"><?php _e( 'Test', 'wc-muse' ) ?></button>
	</form>
	
	<!-- Testing request -->
	<h2><?php _e( 'Outbound Request', 'wc-muse' ) ?></h2>
	<form method="post">
		<input type="hidden" name="test_outbound_request" value="1">
		<p><input type="text" name="order_id"></p>
		<button type="submit" class="button button-primary"><?php _e( 'Test', 'wc-muse' ) ?></button>
	</form>
	
	<!-- Change order status -->
	<h2><?php _e( 'Change order status', 'wc-muse' ) ?></h2>
	<form method="post">
		<input type="hidden" name="test_change_order_status" value="1">
		<p><input type="text" name="order_id"></p>
		<button type="submit" class="button button-primary"><?php _e( 'Test', 'wc-muse' ) ?></button>
	</form>
	
	<!-- Change order status -->
	<h2><?php _e( 'Get orders to export', 'wc-muse' ) ?></h2>
	<form method="post">
		<input type="hidden" name="test_get_orders" value="1">
		<button type="submit" class="button button-primary"><?php _e( 'Test', 'wc-muse' ) ?></button>
	</form>

	<!-- Results -->
	<?php if ( isset( $debug ) ): ?>

		<h2>Value - $_POST</h2>
		<pre style="background-color: #fff; padding: 20px; width: 50%; overflow: scroll;"><?php var_dump($_POST); ?></pre>

		<?php if ( isset( $debug['content'] ) ): ?>
			<h2>Content</h2>
			<pre style="background-color: #fff; padding: 20px; width: 50%;"><?php var_dump($debug['content']); ?></pre>
		<?php endif; ?>

		<?php if ( isset( $debug['response'] ) ): ?>
			<h2>Response</h2>
			<pre style="background-color: #fff; padding: 20px; width: 50%;"><?php var_dump($debug['response']); ?></pre>
		<?php endif; ?>

	<?php endif; ?>

</div>
