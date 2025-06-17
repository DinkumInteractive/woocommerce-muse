<?php

/**
 * Customer's credit card helper.
 *
 * @since      1.0.0
 * @package    Wc_Muse
 * @subpackage Wc_Muse/includes
 * @author     Dinkum Interactive <support@dinkuminteractive.com>
 */
class Wc_Muse_Handler_Credit_Card {

	/**
	 * Instance
	 *
	 * @var obj
	 */
	protected static $instance = null;

	/**
	 * Connector class
	 *
	 * @var obj
	 */
	public $connector = false;

	/**
	 * Braintree settings
	 *
	 * @var obj
	 */
	private $settings = false;

	/**
	 * Initialize the plugin.
	 *
	 * @since     1.0.0
	 */
	function __construct() {

		$this->connector = new Wc_Muse_Connector();

		$this->connector->set_debug( boolval( get_option( 'options_debug_muse_cc_handler' ) ) );

		$this->settings = get_option( 'woocommerce_braintree_credit_card_settings' );
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
			self::$instance = new self();
		}

		return self::$instance;

	}

	public function maybe_refresh_ccs( $woo_id ) {

		$customer = new WC_Customer( $woo_id );

		if ( ! is_wp_error( $customer ) ) {
			$this->refresh_ccs( $woo_id );
		}
	}

	public function export_ccs( $woo_id, $ccs ) {

		if ( ! $muse_id = get_user_meta( $woo_id, '_wc_muse_customer_id', true ) ) {
			return false;
		}

		if ( ! $braintree_id = $this->get_customer_braintree_id( $woo_id ) ) {
			return false;
		}

		$exports = array();

		if ( $ccs ) {
			foreach ( $ccs as $token => $cc ) {

				$args = array(
					'admin_email'          => $this->connector->admin_email,
					'gateway'              => 'braintree',
					'payment_method_type'  => 'credit_card',
					'payment_method_token' => $token,
					'gateway_account_id'   => $braintree_id,
				);

				$exports[] = $this->export_cc( $muse_id, $args );
			}
		}

		// if ( $exports )
		// foreach ( $exports as $key => $export ) {

		// if ( is_a( $export, 'Exception' ) ) {

		// }
		// }
	}

	public function export_cc( $muse_id, $args ) {

		try {

			$response = $this->connector->post( "integrations/{$this->connector->organization_id}/profiles/{$muse_id}/payment-methods", $args );

		} catch ( Exception $e ) {

			$response = $e;
		}

		return $response;
	}

	public function refresh_ccs( $woo_id ) {

		if ( ! $muse_id = get_user_meta( $woo_id, '_wc_muse_customer_id', true ) ) {
			return false;
		}

		if ( ! $braintree_id = $this->get_customer_braintree_id( $woo_id ) ) {
			return false;
		}

			$args = array(
				'admin_email' => $this->connector->admin_email,
				'gateway'     => get_option( 'wc-muse-payment_gateway_id' ),
			);

			try {

				// Endpoint to use: /integrations/:organization_id/profiles/:profile_id/payment-methods-reload
				$response = $this->connector->post(
					"integrations/{$this->connector->organization_id}/profiles/{$muse_id}/payment-methods-reload",
					$args
				);

			} catch ( Exception $e ) {

				$response = $e;
			}

			return $response;
	}

	public function js_export_cc( $braintree_id, $payment_tokens, $action = 'create' ) {

		/*
		  @DEBUG: Braintree object.
		 *
			object(WC_Braintree_Payment_Method)
			["id":protected]=>
			string(7) "j3hgfsr"
			["data":protected]=>
			array(7) {
			  ["default"]=>
			  bool(false)
			  ["type"]=>
			  string(11) "credit_card"
			  ["last_four"]=>
			  string(4) "8431"
			  ["card_type"]=>
			  string(4) "amex"
			  ["exp_month"]=>
			  string(2) "02"
			  ["exp_year"]=>
			  string(4) "2022"
			  ["billing_address_id"]=>
			  string(2) "wv"
		 */

		if ( ! $customer = $this->get_customer_by_braintree_id( $braintree_id ) ) {
			return null;
		}

		$credit_cards = array();

		if ( $payment_tokens ) {
			foreach ( $payment_tokens as $payment_token ) {
				$credit_cards[] = array(
					'type'             => 'braintree',
					'method'           => 'credit_card',
					'action'           => $action,
					'default'          => $payment_token->is_default(),
					'last_4'           => $payment_token->get_last_four(),
					'card_type'        => $payment_token->get_card_type(),
					'cardholder_name'  => '',  // we don't have this.
					'expiration_month' => $payment_token->get_exp_month(),
					'expiration_year'  => $payment_token->get_exp_year(),
				);
			}
		}

		/*
		  @DEBUG: Request data to muse.
		 *
		bd_var_dump($credit_cards);
		 */

		if ( $credit_cards ) {

			// Export credit card.
			try {

				/*
				  @TODO: Export credit card.
				 */

			} catch ( Exception $e ) {

				do_action(
					'wc_muse_credit_card_export_failed',
					$braintree_id,
					$customer,
					$credit_cards,
					array(
						'code'    => $e->getCode(),
						'message' => $e->getMessage(),
					)
				);
			}
		}
	}

	public static function get_customer_by_braintree_id( $braintree_id ) {

		$args = array(
			'meta_key'   => 'wc_braintree_customer_id_sandbox',
			'meta_value' => $braintree_id,
		);

		$customers = get_users( $args );

		return ( $customers ? $customers[0] : null );
	}

	public function get_environment() {

		return ( isset( $this->settings['environment'] ) ? $this->settings['environment'] : null );
	}

	public function get_customer_braintree_id( $customer_id ) {

		$environment = $this->get_environment();

		if ( 'production' === $environment ) {
			return get_user_meta( $customer_id, 'wc_braintree_customer_id', true );
		}

		if ( 'sandbox' === $environment ) {
			return get_user_meta( $customer_id, 'wc_braintree_customer_id_sandbox', true );
		}
	}

	public function update_customer_braintree_id( $customer_id, $braintree_id ) {

		$environment = $this->get_environment();

		if ( 'production' === $environment ) {
			return update_user_meta( $customer_id, 'wc_braintree_customer_id', $braintree_id );
		}

		if ( 'sandbox' === $environment ) {
			return update_user_meta( $customer_id, 'wc_braintree_customer_id_sandbox', $braintree_id );
		}
	}
}
