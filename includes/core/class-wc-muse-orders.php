<?php

/**
 * Order Manager.
 *
 * @since      1.0.0
 * @package    Wc_Muse
 * @subpackage Wc_Muse/includes
 * @author     Dinkum Interactive <support@dinkuminteractive.com>
 */
class Wc_Muse_Orders {

	/**
	 * Instance
	 * @var obj
	 */
	protected static $instance = null;

	/**
	 * Connector class
	 * @var obj
	 */
	public $connector = false;

	/**
	 * Initialize the plugin.
	 *
	 * @since     1.0.0
	 */
	function __construct() {

		$this->connector = new Wc_Muse_Connector();

		$this->connector->set_debug( true );
	
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
		    self::$instance = new self;
		}

		return self::$instance;

	}

	/**
     * Schedule the event to send orders every X time
     * @return void
     */
	function export_orders_schedule(){

		/*	@TODO: add cron?
		 */

	}

	/**
     * Get orders from WooCommerce
     * @return array of WC_Order
     */
	function get_wc_orders( $limit = -1, $page = 1 ){

		$query = new WC_Order_Query( array(
			'limit' => $limit,
			'page' => $page,
			'status' => $this->get_order_status(),
			'orderby' => 'ID',
			'order' => 'ASC',
			'meta_key' => '_wc_muse_order_export_success',
			'meta_compare' => 'NOT EXISTS'
		) );

		$orders = $query->get_orders();

		return ( $orders ? $orders : [] );

	}

	/**
     * Get allowed orders status to retrieve
     * @return array of order status
     */
	function get_order_status(){

		// $order_status = wc_get_order_statuses();
		// TODO: we need an input setting to define this in the admin
		$order_status = get_option( 'wc-muse-order_status_included' );

		return $order_status;

	}

	/**
     * Convert WC order into json
     * @return void
     */
	function convert_wc_order( $wc_order ){

		$customer_note = apply_filters( 'wc_muse_order_customer_note', $wc_order->get_customer_note(), $wc_order );

		$order = array(

			'notes' => !empty( $customer_note ) ? sprintf( 'Customer note in the website: %s', $customer_note ) : 'Empty customer note in the website ',

			'admin_email' => get_option( 'wc-muse-admin_email' ),

			'tags' => get_option( 'wc-muse-order_tags' ),

			'legacy_order_id' => $wc_order->get_order_number(),

			'wp_order_id' => $wc_order->get_id(),

			'profile' => $this->get_customer_profile( $wc_order ),
			/*	Fields:
				- first_name
				- last_name
				- email
				- legacy_id
				- phone_number
			*/

			'shipping_address' => array(
				'first_name' => $wc_order->get_shipping_first_name(),
				'last_name' => $wc_order->get_shipping_last_name(),
				'first_line' => $wc_order->get_shipping_address_1(),
				'second_line' => $wc_order->get_shipping_address_2(),
				'city' => $wc_order->get_shipping_city(),
				'state' => $this->get_state_name($wc_order->get_shipping_country(), $wc_order->get_shipping_state()),
				'state_code' => $wc_order->get_shipping_state(),
				'country_name' => $this->get_country_name($wc_order->get_shipping_country()),
				'country_code' => $wc_order->get_shipping_country(),
				'zip_code' => $wc_order->get_shipping_postcode(),
			),

			'billing_address' => array(
				'first_name' => $wc_order->get_billing_first_name(),
				'last_name' => $wc_order->get_billing_last_name(),
				'first_line' => $wc_order->get_billing_address_1(),
				'second_line' => $wc_order->get_billing_address_2(),
				'city' => $wc_order->get_billing_city(),
				'state' => $this->get_state_name($wc_order->get_billing_country(), $wc_order->get_billing_state()),
				'state_code' => $wc_order->get_billing_state(),
				'country_name' => $this->get_country_name($wc_order->get_billing_country()),
				'country_code' => $wc_order->get_billing_country(),
				'zip_code' => $wc_order->get_billing_postcode(),
			),

			'payment' => $this->get_order_payment( $wc_order ),

			'fees' => $this->get_order_fees( $wc_order ),

			'totals' => array(
				'subtotal' => $wc_order->get_subtotal(), 
				'total' => $wc_order->get_total(),
				'discount' => $wc_order->get_total_discount(),
				'shipping' => $wc_order->get_shipping_total(),
				'taxes' =>$wc_order->get_total_tax(),
				'total_fees' => $this->get_order_total_fees( $wc_order ),
			),

			'coupons' => $this->get_order_coupons( $wc_order ),
			/*	Fields in array:
				- name
				- discount_amount
			*/

			'order_items' => $this->get_order_items( $wc_order ),
			/*	Fields in array:
				- series_slug
				- event_slug
				- seat_preference_slug
				- ticket_quantity
				- ticket_price
			*/

		);

		return apply_filters( 'wc_muse_order', $order );

	}

	function get_country_name($country_code) {
		// Get all countries key/names in an array:
		$countries = WC()->countries->get_countries();

		return isset($countries[$country_code]) ? $countries[$country_code] : $country_code;
	}

	function get_state_name($country_code, $state_code) {
		// Get all country states key/names in a multilevel array:
		$country_states = WC()->countries->get_states();

		return (isset($country_states[$country_code]) && isset($country_states[$country_code][$state_code])) ? $country_states[$country_code][$state_code] : $state_code;
	}

	function get_customer_profile( $wc_order ) {

		if ( $wc_order->get_customer_id() ) {
			$customer = new WC_Customer($wc_order->get_customer_id());

			$profile = array(
				'first_name' => $customer->get_first_name(),
				'last_name' => $customer->get_last_name(),
				'email' => $customer->get_email(),
				'legacy_id' => $customer->get_ID(),
				'phone_number' => $customer->get_billing_phone(),
			);
		} else {
			$profile = array(
				'first_name' => $wc_order->get_billing_first_name(),
				'last_name' => $wc_order->get_billing_last_name(),
				'email' => $wc_order->get_billing_email(),
				'phone_number' => $wc_order->get_billing_phone(),
			);
		}

		return $profile;

	}

	function get_order_coupons( $wc_order ) {

		$coupons = $wc_order->get_used_coupons();

		$order_coupons = array();

		foreach ($coupons as $i => $code) {

			$coupon = new WC_Coupon($code);
			
			$order_coupons[] = array(
				'name' => $code, 
				'discount_amount' => $coupon->get_amount(),
			);

		}

		return $order_coupons;

	}

	function get_order_items( $wc_order ) {

		$items = $wc_order->get_items();

		$order_items = array();

		foreach ($items as $i => $order_item) {

			$product = $order_item->get_product();

			$product_id = $product->get_id();

			switch ( $product->get_type() ) {

				case 'simple':
					$product_parent_id = $product->get_id();
					break;

				case 'variation':
					$product_parent_id = $product->get_parent_id();
					break;

			}

			$events = apply_filters( 'wc_muse_order_item_event_slugs', $this->get_event_slugs($product_parent_id), $product_parent_id, $order_item );

			if ( is_array($events) ) {
				foreach ($events as $event_slug) {
					$data = array(
						'qty' => $order_item->get_quantity(),
						'price' => $wc_order->get_item_subtotal( $order_item, false, false ),
						'slug' => $event_slug,
						'series' => get_post_meta( $product_parent_id, 'sub_item_slug', true ),
						'type' => get_post_meta( $product_parent_id, 'ticket_type', true ),
						'seat_slug' => get_post_meta( $product_id, 'seat_slug', true )
					);

					$order_items[] = $data;
				}
			}

		}

		return $order_items;

	}

	function get_event_slugs( $product_parent_id ) {
		$event_slugs = trim( get_post_meta( $product_parent_id, 'item_slug', true ) );
		if ( $event_slugs ){
			$event_slugs = array_map('trim', explode( ',', $event_slugs ));
		}

		return apply_filters( 'wc_muse_order_event_slugs', $event_slugs, $product_parent_id, $wc_order );
	}

	function get_order_payment( $wc_order ) {

		$payment_manager = new Wc_Muse_Payment( $wc_order );

		$payment_data = $payment_manager->get_data();

		/*$payment_data = array(
			'last_4' => '', 
			'card_type' => '',
			'cardholder_name' => '',
			'expiration_month' => '',
			'expiration_year' => '',
			'transaction_id' => '',
			'action' => '',
			'method' => '',
			'type' => '',
			'amount' => '',
		);*/

		return $payment_data;

	}

	function get_order_fees( $wc_order ) {

		$order_fees = array();

		if ( $fees = $wc_order->get_fees() ) {

			foreach ( $fees as $fee ) {
				
				$order_fees[] = array( 'name' => $fee->get_name(), 'amount' => $fee->get_total() );

			}

		}

		return apply_filters( 'wc_muse_order_fees', $order_fees, $wc_order );

	}

	function get_order_total_fees( $wc_order ) {

		$order_fees = 0;

		if ( $fees = $wc_order->get_fees() ) {

			foreach ( $fees as $fee ) {

				$order_fees += $fee->get_total();

			}

		}

		return apply_filters( 'wc_muse_order_total_fees', $order_fees, $wc_order );

	}

	public static function complete_order( $order ) {

		return $this->update_status( $order, 'completed' );

	}

	public static function update_status( $order, $status ) {

		if ( !$order || !( $order instanceof WC_Order ) ) return false;

		return $order->update_status( $status );

	}

	public function export_order( $wc_order ) {

		try {

			$organization_id = $this->connector->organization_id;

			$content = array( 'order_data' => $this->convert_wc_order( $wc_order ) );

			$response = $this->connector->post( "integrations/{$organization_id}/orders", $content );

			do_action( 'wc_muse_order_export_success', $wc_order, $response );

			return $response;
			
		} catch ( Exception $e ) {
			
			do_action( 'wc_muse_order_export_failed', $wc_order, array( 'code' => $e->getCode(), 'message' => $e->getMessage() ) );

			return false;

		}

	}

	public function get_muse_order( $muse_order_id ) {

		try {

			$response = $this->connector->get( "orders/{$muse_order_id}/overview" );

			return $response;
			
		} catch ( Exception $e ) {
			
			do_action( 'wc_muse_order_read_failed', $wc_order, array( 'code' => $e->getCode(), 'message' => $e->getMessage() ) );

			return false;

		}

	}

}
