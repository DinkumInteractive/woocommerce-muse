<?php

/**
 * Payment manager.
 *
 * @since      1.0.0
 */
class Wc_Muse_Payment {

	/* Order Object */
	private $order;

	/* Allowed payment method */
	private $allowed_method = array(
		'stripe',
		'braintree_credit_card',
		'bacs',
	);

	/**
	 * Initialize payment class.
	 *
	 * @since    1.0.0
	 * @param    obj        $wc_order        The name of the WordPress action that is being registered.
	 */
	public function __construct( $wc_order ) {

		$this->validate_order( $wc_order );

		$this->set_order( $wc_order );

	}

	/**
	 * Validate order instance.
	 */
	public function validate_order( $wc_order ) {

		if ( ! is_a($wc_order, 'WC_Order' ) ) {
			throw new WP_Error( 'invalid_order_class', __( 'Order class is not recognized.', 'wc-muse' ), $wc_order );
		}

		return true;

	}

	/**
	 * Set class to said order.
	 */
	public function set_order( $wc_order ) {
		$this->order = $wc_order;
	}

	/**
	 * Check if order has a valid payment method.
	 */
	public function is_payment_method_allowed() {

		$payment_method = $this->order->get_payment_method();

		return ( in_array( $payment_method, $this->allowed_method ) ? true : false );
		
	}

	/**
	 * Get payment data.
	 */
	public function get_data() {

		if ( ! $this->is_payment_method_allowed() ) return false;

		$payment_gateway = wc_get_payment_gateway_by_order( $this->order );

		$data = array(
			'method' => $this->order->get_payment_method(),
		);

		$order_id = $this->order->get_id();

		switch ( $this->order->get_payment_method() ) {

			case 'stripe':
				$stripe_metas = get_post_meta( $order_id, '_stripe_metas_for_cartspan', true );
				if ( $stripe_metas ) {
					$data['card_type'] = $stripe_metas['cc_type'];
					$data['last_4'] = $stripe_metas['cc_last4'];
					$data['transaction_id'] = $stripe_metas['cc_trans_id'];
				}
				break;

			case 'braintree_credit_card':
				$data['transaction_id'] = get_post_meta( $order_id, '_wc_braintree_credit_card_trans_id', true );
				$data['card_type'] = get_post_meta( $order_id, '_wc_braintree_credit_card_card_type', true );
				$data['last_4'] = get_post_meta( $order_id, '_wc_braintree_credit_card_account_four', true );
				$data['expiration_date'] = get_post_meta( $order_id, '_wc_braintree_credit_card_card_expiry_date', true );
				$data['auth_code'] = get_post_meta( $order_id, '_wc_braintree_credit_card_authorization_code', true );
				$data['customer_id'] = get_post_meta( $order_id, '_wc_braintree_credit_card_customer_id', true );
				$data['action'] = ( get_post_meta( $order_id, '_wc_braintree_credit_card_charge_captured', true ) === 'no' ) ? 'authorize' : 'charge';
				$data['method'] = 'braintree';
				$data['type'] = 'credit_card';
				break;

		}

		return $data;
		
	}

}
