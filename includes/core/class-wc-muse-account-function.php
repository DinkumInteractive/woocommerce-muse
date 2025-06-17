<?php
defined( 'ABSPATH' ) || exit;

class Wc_Muse_Account_Function {

	public $theme_slug;

	protected static $instance = null;

	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	public function __construct() {

		// Settings
		$this->theme_slug = 'pcms-concerts';

		// Use WooCommerce lost password url.
		add_filter( 'lostpassword_url', array( $this, 'get_account_lost_password_url' ) );

		// Add custom endpoint
		add_action( 'init', array( $this, 'add_custom_endpoints' ) );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'account_menu_items' ), 10, 1 );
		add_action( 'woocommerce_get_query_vars', array( $this, 'woocommerce_add_query_vars' ) );

		// Add account page header action
		add_action( 'pcms_account_header', array( $this, 'get_template_account_header' ) );

		// Add menu
		add_action( 'woo_account_menu', array( $this, 'woo_account_menu' ) );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'woocommerce_account_menu_items' ), 10, 2 );

		// Dashboard eticket
		add_action( 'woocommerce_account_dashboard', array( $this, 'woocommerce_account_dashboard_template' ) );

		// Eticket page content
		add_filter( 'body_class', array( $this, 'account_etickets_view_page_body_class' ) );
		add_action( 'woocommerce_account_etickets_endpoint', array( $this, 'account_etickets_endpoint_content' ) );

		// Add custom field save in Account Details
		// add_filter( 'woocommerce_save_account_details_errors', array( $this, 'woocommerce_save_account_details_errors' ) );

		// Send data to muse on addresses update.
		// add_action( 'woocommerce_customer_save_address', array( $this, 'woocommerce_customer_save_address' ), 10, 2 );

		// Account address
		add_filter( 'woocommerce_billing_fields', array( $this, 'add_woocommerce_address_field_default_billing' ), 10, 2 );
		add_filter( 'woocommerce_shipping_fields', array( $this, 'add_woocommerce_address_field_default_shipping' ), 10, 2 );
		add_action( 'woocommerce_after_save_address_validation', array( $this, 'woocommerce_after_save_address_validation' ), 90, 4 );
		add_action( 'woocommerce_after_save_address_validation', array( $this, 'address_export_user_info' ), 99, 4 );

		// Account details
		add_filter( 'woocommerce_save_account_details_required_fields', array( $this, 'woocommerce_save_account_details_required_fields' ) );
		add_action( 'woocommerce_save_account_details_errors', array( $this, 'account_details_export_user_info' ), 10, 2 );

		// Login with only email
		add_filter( 'woocommerce_process_login_errors', array( $this, 'woocommerce_process_login_errors' ), 10, 3 );

		// Localize script for eticket page
		add_filter( 'woo-account-localize-script', array( $this, 'eticket_add_localize_script' ), 10 );

		// Eticket ajax cancel ticket.
		add_action( 'wp_ajax_muse_cancel_ticket', array( $this, 'muse_cancel_ticket' ) );
		add_action( 'wp_ajax_nopriv_muse_cancel_ticket', array( $this, 'muse_cancel_ticket' ) );

		// On user activated
		// add_action( 'xlwuev_on_email_verification', array( $this, 'xlwuev_on_email_verification' ), 10, 1 );
		add_action( 'update_user_metadata', array( $this, 'update_user_metadata' ), 15, 4 );

		add_action( 'template_redirect', array( $this, 'maybe_repair_customer_meta' ), 10 );
	}

	public function get_account_lost_password_url() {

		return wc_lostpassword_url();

	}

	public function add_custom_endpoints() {

		add_rewrite_endpoint( 'etickets', EP_PAGES );

	}

	public function account_menu_items( $items ) {

		// Add etickets page after orders
		$offset = array_search( 'orders', array_keys( $items ) ) + 1;
		$items  = array_merge(
			array_slice( $items, 0, $offset ),
			array( 'etickets' => __( 'E-Tickets', 'pcms-concerts' ) ),
			array_slice( $items, $offset, null )
		);

		return $items;

	}

	public function woocommerce_add_query_vars( $query_vars ) {

		$query_vars['etickets'] = 'etickets';

		return $query_vars;

	}

	public function get_template_account_header() {

		wc_get_template( 'myaccount/account-header.php' );

	}

	public function woo_account_menu() {

		do_action( 'woocommerce_account_navigation' );

	}

	public function woocommerce_account_menu_items( $items, $endpoints ) {

		unset( $items['customer-logout'] );

		return $items;

	}

	public static function get_woo_account_pages_title() {

		$endpoint = WC()->query->get_current_endpoint();

		/*
		  @DEBUG: Check endpoint name.
		var_dump($endpoint);
		 */

		switch ( $endpoint ) {

			default:
				$title = ( is_user_logged_in() ? __( 'Dashboard', 'pcms-concerts' ) : __( 'Account', 'pcms-concerts' ) );
				break;

			case 'orders':
				$title = __( 'Orders', 'pcms-concerts' );
				break;

			case 'etickets':
				$title = __( 'E-Tickets', 'pcms-concerts' );
				break;

			case 'downloads':
				$title = __( 'Downloads', 'pcms-concerts' );
				break;

			case 'edit-address':
				$title = __( 'Addresses', 'pcms-concerts' );
				break;

			case 'payment-methods':
				$title = __( 'Payment Methods', 'pcms-concerts' );
				break;

			case 'add-payment-method':
				$title = __( 'Add Payment Method', 'pcms-concerts' );
				break;

			case 'edit-account':
				$title = __( 'Account Details', 'pcms-concerts' );
				break;

			case 'lost-password':
				$title = __( 'Lost Password', 'pcms-concerts' );
				break;
		}

		return $title;

	}

	public function woocommerce_account_dashboard_template() {
		wc_get_template( 'myaccount/dashboard-etickets.php' );
	}

	public static function get_order_concert_names( $order ) {

		$names = array();

		if ( $order->get_items() ) {
			foreach ( $order->get_items() as $item ) {
				$names[] = $item->get_name();
			}
		}

		return $names;

	}

	public static function get_order_concert_dates( $order ) {

		$dates = array();

		if ( $order->get_items() ) {
			foreach ( $order->get_items() as $item ) {
				$date = '';
				$day  = get_post_meta( $item->get_product_id(), 'event_date', true );
				$time = get_post_meta( $item->get_product_id(), 'event_time', true );
				if ( $day && $time ) {
					$day     = strtotime( $day );
					$dates[] = date( 'M j, Y', $day ) . ' - ' . $time;
				} else {
					$dates[] = 'NA / TBA';
				}
			}
		}

		return $dates;

	}

	public function account_etickets_endpoint_content() {

		wc_get_template( 'myaccount/etickets.php' );

	}

	public function account_etickets_view_page_body_class( $classes ) {

		if ( is_wc_endpoint_url( 'etickets' ) && isset( $_GET['action'] ) ) {

			if ( $_GET['action'] === 'view' ) {
				$classes[] = 'woocommerce-etickets-view';
			}

			if ( $_GET['action'] === 'print' ) {
				$classes[] = 'woocommerce-etickets-print';
			}
		}

		return $classes;

	}

	public function get_woocommerce_address_field_default_args() {

		$args = array(
			'type'    => 'checkbox',
			'label'   => __( 'Primary address', 'pcms-concerts' ),
			'default' => false,
		);

		return $args;
	}

	public function add_woocommerce_address_field_default_billing( $address_fields, $country ) {

		$address_fields['_account_billing_default'] = $this->get_woocommerce_address_field_default_args();

		return $address_fields;
	}

	public function add_woocommerce_address_field_default_shipping( $address_fields, $country ) {

		$address_fields['_account_shipping_default'] = $this->get_woocommerce_address_field_default_args();

		return $address_fields;
	}

	public function woocommerce_after_save_address_validation( $user_id, $load_address, $address, $customer ) {

		$this->validate_default_address_field( $load_address, $customer );
	}

	public function validate_default_address_field( $load_address, $customer ) {

		$unload_address = ( 'billing' === $load_address ? 'shipping' : 'billing' );

		$current_load_key = "_account_{$load_address}_default";

		$current_unload_key = "_account_{$unload_address}_default";

		$unload = false;

		if ( $all_meta_data = $customer->get_meta_data() ) {

			foreach ( $all_meta_data as $key => $meta_data ) {

				if ( $current_load_key === $meta_data->key && 1 === $meta_data->value ) {

					$unload = true;
				}
			}
		}

		if ( $unload && isset( $all_meta_data ) && $all_meta_data ) {

			foreach ( $all_meta_data as $key => $meta_data ) {

				if ( $current_unload_key === $meta_data->key ) {

					$meta_data->value = 0;
				}
			}
		}
	}

	public function address_export_user_info( $user_id, $load_address, $address, $customer ) {

		// bd_var_dump($_POST); exit;

		$user = $customer;

		$this->export_user_to_muse( $user, $load_address );
	}

	public function woocommerce_save_account_details_required_fields( $required_fields ) {

		$required_fields['account_phone_number'] = __( 'Phone number', 'pcms-concerts' );

		return $required_fields;
	}

	public function account_details_export_user_info( $wp_error, $user ) {

		// Not exporting to muse if there's any error.
		if ( $wp_error->get_error_messages() ) {
			return false;
		}

		// Update phone number.
		if ( isset( $_POST['account_phone_number'] ) && $_POST['account_phone_number'] ) {
			update_user_meta( $user->ID, '_account_phone_number', sanitize_text_field( $_POST['account_phone_number'] ) );
		}

		$this->export_user_to_muse( $user );
	}

	public function export_user_to_muse( $user, $load_address = false ) {

		/* Export user information to muse.*/
		$muse_customer_manager = Wc_Muse_Customers::get_instance();

		try {

			$muse_customer_manager->export_customer( $user, $load_address );

		} catch ( Exception $e ) {

			wc_add_notice( $e->getMessage(), 'error' );
		}
	}

	// public function woocommerce_save_account_details_errors( $errors ) {

	// if ( isset( $_POST['account_phone_number'] ) && empty( $_POST['account_phone_number'] ) )
	// $errors->add( 'account_phone_number', __( 'Phone number is a required field.', 'pcms-concerts' ) );

	// return $errors;

	// }

	// public function woocommerce_customer_save_address( $user_id, $load_address ) {

	// * Export user information to muse.*/
	// $muse_customer_manager = Wc_Muse_Customers::get_instance();

	// $response = $muse_customer_manager->export_customer( $user_id, $load_address );

	// }

	public function woocommerce_process_login_errors( $errors, $post_username, $post_password ) {

		if ( ! filter_var( $post_username, FILTER_VALIDATE_EMAIL ) ) {
			throw new Exception( '<strong>' . __( 'Error', 'pcms-concerts' ) . ':</strong> ' . __( 'Please enter a valid email ID.', 'pcms-concerts' ) );
		}

		return $errors;

	}

	public function get_account_nonce( $case ) {
		switch ( $case ) {

			case 'eticket':
				$nonce = 'pcms_muse_cancel_ticket';
				break;

		}
		return $nonce;
	}

	public function eticket_add_localize_script( $scripts ) {

		if ( ! is_account_page() ) {
			return $scripts;
		}
		if ( ! is_wc_endpoint_url( 'etickets' ) ) {
			return $scripts;
		}

		$scripts['ajax_nonce'] = wp_create_nonce( $this->get_account_nonce( 'eticket' ) );

		return $scripts;

	}

	public function muse_cancel_ticket() {

		$data = $_POST['data'];

		$nonce = sanitize_text_field( $data['ajax_nonce'] );

		if ( ! wp_verify_nonce( $nonce, $this->get_account_nonce( 'eticket' ) ) ) {
			exit;
		}

		$muse_ticket_manager = Wc_Muse_Tickets::get_instance();

		$cancel_ticket = $muse_ticket_manager->cancel_ticket( $data['selected_id'], $data['order_item_id'], $data['order_id'], $data['cancel_type'] );

		if ( $cancel_ticket ) {

			$response = array(
				'success'      => true,
				'ticket_id'    => $data['selected_id'],
				'notification' => array(
					'title'   => __( 'Ticket Canceled', 'pcms2018' ),
					'content' => __( 'Your ticket has been canceled.', 'pcms2018' ),
				),
			);

		} else {

			$response = array(
				'success'      => false,
				'notification' => array(
					'title'   => __( 'Cancel Ticket Failed', 'pcms2018' ),
					'content' => __( 'Failed to cancel your ticket. Please try again.', 'pcms2018' ),
				),
			);

		}

		wp_send_json( $response );

		wp_die();

	}

	public static function get_formatted_date( $muse_date ) {
		return get_date_from_gmt( $muse_date, 'D, M j, Y  g:i a' );
		// $timestamp = strtotime( $muse_date );

		// return sprintf( "%s \n %s", date( 'D, M j, Y ', $timestamp ), date( ' g:i a', $timestamp ));
	}

	public function xlwuev_on_email_verification( $user_id ) {

		/* Handle newly verified woo customer.*/
		$muse_customer_manager = Wc_Muse_Customers::get_instance();

		$response = $muse_customer_manager->verify_customer( $user_id );

	}

	public function update_user_metadata( $meta_id, $object_id, $meta_key, $_meta_value ) {

		if ( 'wcemailverified' === $meta_key ) {

			/* Change to 'customer' role if not already. */
			$user = new WP_User( $object_id );

			if ( ! in_array( 'customer', $user->roles ) ) {
				$user->set_role( 'customer' );
			}

			/* Handle newly verified woo customer.*/
			$muse_customer_manager = Wc_Muse_Customers::get_instance();

			if ( $object_id && $_meta_value ) {
				$muse_customer_manager->export_customer( $object_id );
			}
		}

		if ( '_wc_braintree_credit_card_payment_tokens_sandbox' === $meta_key || '_wc_braintree_credit_card_payment_tokens' === $meta_key ) {

			$Handler_Credit_Card = new Wc_Muse_Handler_Credit_Card();

			$Handler_Credit_Card->maybe_refresh_ccs( $object_id );
		}

		return $meta_id;
	}

	public function maybe_repair_customer_meta() {

		if ( is_account_page()
			&&
			isset( $_GET['wc_muse'] )
			&&
			$_GET['wc_muse'] === 'account_repair'
		) {

			$muse_id = get_user_meta( get_current_user_id(), '_wc_muse_customer_id', true );

			if ( ! $muse_id ) {

				$muse_customer_manager = Wc_Muse_Customers::get_instance();

				try {

					$export_response = $muse_customer_manager->export_customer( (int) get_current_user_id() );

				} catch ( Exception $e ) {

				}

				if ( get_user_meta( get_current_user_id(), '_wc_muse_customer_id', true ) ) {

					wp_redirect( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) );

					exit;
				}
			}
		}

		if ( is_account_page()
			&&
			'' != WC()->query->get_current_endpoint()
			&&
			'lost-password' != WC()->query->get_current_endpoint()
		) {

			if ( ! $muse_id = get_user_meta( get_current_user_id(), '_wc_muse_customer_id', true ) ) {

				wp_redirect( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) );

				exit;
			}
		}
	}

	public static function get_venue_upcoming_events( $venue_id, $limit = false ) {

		$today = date( 'Ymd' );

		$args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => 20,
			'meta_query'     => array(
				array(
					'key'     => 'event_venues',
					'value'   => $venue_id,
					'compare' => 'LIKE',
				),
				array(
					'key'     => 'event_date',
					'value'   => $today,
					'type'    => 'numeric',
					'compare' => '>=',
				),
			),
			'orderby'        => 'meta_value_num',
			'meta_key'       => 'event_date',
			'order'          => 'ASC',
		);

		if ( $limit ) {
			$args['posts_per_page'] = $limit;
		}

		$wp_query = new WP_Query();

		$wp_query->query( $args );

		$upcoming_events = array();

		if ( $wp_query->have_posts() ) {
			while ( $wp_query->have_posts() ) {

				$wp_query->the_post();

				$upcoming_event_date = strtotime( get_field( 'event_date' ) );
				$upcoming_event_time = get_field( 'event_time' );

				ob_start();
				the_content();
				$content = ob_get_clean();

				$upcoming_event = array(
					'name'      => get_the_title(),
					'date'      => sprintf( '%s - %s', date( 'D, M j, Y ', $upcoming_event_date ), $upcoming_event_time ),
					'info'      => $content,
					'permalink' => get_the_permalink(),
				);

				$upcoming_events[] = $upcoming_event;
			}
		}

		wp_reset_query();

		return $upcoming_events;
	}

	public static function get_woo_order_status_icon( $order ) {

		if ( ! function_exists( 'wc_order_status_manager_get_order_status_posts' ) ) {
			return false;
		}

		$order_status_posts = wc_order_status_manager_get_order_status_posts();
		$post_id            = '';

		foreach ( $order_status_posts as $order_status_post ) {
			if ( $order->get_status() === $order_status_post->post_name ) {
				$post_id = $order_status_post->ID;
				break;
			}
		}

		if ( ! $post_id ) {
			return false;
		}

		$status = new WC_Order_Status_Manager_Order_Status( $post_id );
		$color  = $status->get_color();
		$icon   = $status->get_icon();
		$style  = '';

		if ( $color ) {

			if ( $icon ) {
				$style = 'color: ' . $color . ';';
			} else {
				$style = 'background-color: ' . $color . '; color: ' . wc_order_status_manager()->get_icons_instance()->get_contrast_text_color( $color ) . ';';
			}
		}

		if ( is_numeric( $icon ) ) {

			$icon_src = wp_get_attachment_image_src( $icon, 'wc_order_status_icon' );

			if ( $icon_src ) {
				$style .= 'background-image: url( ' . $icon_src[0] . ');';
			}
		}

		return sprintf( '<span class="%1$s %2$s tips" style="%3$s" data-tip="%4$s">%5$s</span>', sanitize_title( $status->get_slug() ), ( $icon ? 'has-icon ' . $icon : '' ), $style, esc_attr( $status->get_name() ), esc_html( $status->get_name() ) );

	}
}

