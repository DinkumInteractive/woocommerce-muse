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
	protected static $connector = false;

	/**
	 * Array of order status to retrieve from woo orders
	 * @var array
	 */
	public $order_status_to_retrieve = array('wc-shipping');
	public $order_status_to_export = array('wc-processing');
	public $order_status_to_process = array('wc-processing');

	/**
	 * Time in seconds to clear log transients
	 * @var integer
	 */
	public $clear_transient_after = 43200; // 12 hours

	/**
	 * Initialize the plugin.
	 *
	 * @since     1.0.0
	 */
	function __construct() {

		$this->connector = new Wc_Muse_Connector();
	
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
	function get_wc_orders( $limit, $page ){

		$query = new WC_Order_Query( array(
			'limit' => $limit,
			'page' => $page,
            'post_status' => $this->get_order_status(),
			'orderby' => 'ID',
			'order'   => 'ASC',
		) );

		$orders = $query->get_orders();

		return ( $orders ? $orders : false );

	}

	/**
     * Get allowed orders status to retrieve
     * @return array of order status
     */
	function get_order_status(){

		return array_intersect(array_keys( wc_get_order_statuses() ), $this->order_status_to_retrieve);

	}

	/**
     * Get allowed orders status to retrieve
     * @return string
     */
	function get_orders_to_export( $limit, $page ){

		$wc_orders = $this->get_wc_orders( $limit, $page );

		if ( ! $wc_orders ) return false;

		$order = array();

		foreach ( $wc_orders as $wc_order ) {

			$order[] = $this->convert_wc_order( $wc_order );

		}

		// return json_encode( $order );
		return $order;

	}

	/**
     * Convert WC order into json
     * @return void
     */
	function convert_wc_order( $wc_order ){

		// var_dump($wc_order->get_subtotal());

		$order = array(

			'notes' => '',

			'legacy_order_id' => $wc_order->get_ID(),

			'profile' => $this->get_customer_profile( $wc_order->get_customer_id() ),
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
				'first_line' => '', // not available
				'second_line' => '', // not available
				'city' => $wc_order->get_shipping_city(),
				'state' => $wc_order->get_shipping_state(),
				'state_code' => $wc_order->get_shipping_state(),
				'country_code' => $wc_order->get_shipping_country(),
				'zip_code' => $wc_order->get_shipping_postcode(),
			),

			'billing_address' => array(
				'first_name' => $wc_order->get_billing_first_name(),
				'last_name' => $wc_order->get_billing_last_name(),
				'first_line' => '',
				'second_line' => '',
				'city' => $wc_order->get_billing_city(),
				'state' => $wc_order->get_billing_state(),
				'state_code' => $wc_order->get_billing_state(),
				'country_code' => $wc_order->get_billing_country(),
				'zip_code' => $wc_order->get_billing_postcode(),
			),

			'payment' => array(
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
			),

			'totals' => array(
				'subtotal' => $wc_order->get_subtotal(), 
				'total' => $wc_order->get_total(),
				'discount' => $wc_order->get_total_discount(),
				'shipping' => $wc_order->get_shipping_total(),
				'taxes' =>$wc_order->get_total_tax(),
				'fees' => '',
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

		return $order;

	}

	function get_customer_profile( $customer_id ) {

		if ( ! $customer_id ) return false;

		$customer = new WC_Customer($customer_id);

		$profile = array(
			'first_name' => $customer->get_first_name(),
			'last_name' => $customer->get_last_name(),
			'email' => $customer->get_email(),
			'legacy_id' => $customer->get_ID(),
			'phone_number' => $customer->get_billing_phone(),
		);

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

			$product_id = $order_item->get_product_id();

			$order_items[] = array(
				'slug' => get_post_meta( $product_id, 'item_slug', true ),
				'type' => get_post_meta( $product_id, 'ticket_type', true ),
				'seat_slug' => get_post_meta( $product_id, 'seat_slug', true ),
				'qty' => $order_item->get_quantity(),
				'price' => $order_item->get_total(),
			);

		}

		return $order_items;

	}

}
