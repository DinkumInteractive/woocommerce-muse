<?php

/**
 * Customer Manager.
 *
 * @since      1.0.0
 * @package    Wc_Muse
 * @subpackage Wc_Muse/includes
 * @author     Dinkum Interactive <support@dinkuminteractive.com>
 */
class Wc_Muse_Customers {

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
	 * Initialize the plugin.
	 *
	 * @since     1.0.0
	 */
	function __construct() {

		$this->connector = new Wc_Muse_Connector();

		$this->connector->set_debug( 'yes' === get_option( 'options_debug_muse_customer_handler' ) );

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

	/**
	 * Return the customer data.
	 *
	 * @since     1.0.0
	 *
	 * @return    array    Customer data.
	 */
	public static function get_woo_customer_data( $user, $load_address = false ) {

		$updating = false;

		if ( is_integer( $user ) ) {

			$customer = new WC_Customer( $user );

		} elseif ( is_object( $user ) && isset( $user->ID ) ) {

			$customer = new WC_Customer( $user->ID );

			$updating = true;

		} elseif ( is_object( $user ) && method_exists( $user, 'get_id' ) ) {

			$customer = $user;
		}

		if ( $customer->get_id() === 0 ) {
			return false;
		}

		$first_name = (
			$updating && isset( $user->first_name ) ?
				$user->first_name
					: get_user_meta( $customer->get_id(), 'first_name', true )
		);

		$last_name = (
			$updating && isset( $user->last_name ) ?
				$user->last_name
					: get_user_meta( $customer->get_id(), 'last_name', true )
		);

		$content = apply_filters(
			'wc_muse_customer',
			array(
				'wp_id'           => $customer->get_id(),
				'email'           => $customer->get_email(),
				'phone_number'    => get_user_meta( $customer->get_id(), '_account_phone_number', true ),
				'muse_profile_id' => get_user_meta( $customer->get_id(), '_wc_muse_customer_id', true ),
				'first_name'      => ( $first_name ? $first_name : self::get_name_from_email( 'first', $customer->get_email() ) ),
				'last_name'       => ( $last_name ? $last_name : self::get_name_from_email( 'last', $customer->get_email() ) ),
			)
		);

		if (
			$load_address
			&&
			in_array( $load_address, array( 'billing', 'shipping' ) )
		) {

			switch ( $load_address ) {

				case 'billing':
					$content['addresses']['billing'] = $customer->get_billing();
					break;

				case 'shipping':
					$content['addresses']['shipping'] = $customer->get_shipping();
					break;
			}
		} else {

			// $content['addresses'] = array(
			// 'billing' => $customer->get_billing(),
			// 'shipping' => $customer->get_shipping(),
			// );
		}

		return $content;

	}

	/**
	 * Return muse customer data.
	 *
	 * @since     1.0.0
	 *
	 * @return    array    Customer data.
	 */
	public function get_muse_customer_data( $user_id ) {

		$user_data = get_userdata( $user_id );

		try {

			$organization_id = $this->connector->organization_id;
			$admin_email     = $this->connector->admin_email;

			$response = $this->connector->get( "integrations/{$organization_id}/profiles/by-email?email={$user_data->user_email}&admin_email={$admin_email}" );

			return $response;

		} catch ( Exception $e ) {

			do_action(
				'wc_muse_get_muse_customer_data_by_email_failed',
				$id,
				array(
					'code'    => $e->getCode(),
					'message' => $e->getMessage(),
				)
			);

			return false;

		}
	}

	/**
	 * Export customer data to muse.
	 *
	 * @since     1.0.0
	 *
	 * @return    array    Customer data.
	 */
	public function export_customer( $user, $load_address = false ) {

		try {

			$organization_id = $this->connector->organization_id;

			$content = $this->get_woo_customer_data( $user, $load_address );

			/*
			  @DEBUG: customer export.
			bd_var_dump($content); exit;
			 */

			if ( $load_address ) {

				$content = $this->format_address( $content, $user );

				$content = $this->set_default_address( $content, $user );
			}

			/*
			  @DEBUG: customer export.
			bd_var_dump($content); exit;
			 */

			if ( isset( $content['muse_profile_id'] ) && $content['muse_profile_id'] ) {

				$response = $this->connector->patch( "integrations/{$organization_id}/profiles/{$content['muse_profile_id']}", $content );

			} else {

				$content['admin_email'] = $this->connector->admin_email;

				/*
				  @DEBUG: customer export.
				bd_var_dump($content); exit;
				 */

				$response = $this->connector->post( "integrations/{$organization_id}/profiles", $content );
			}

			/*
			  @DEBUG: customer export.
			bd_var_dump($response); exit;
			 */

			do_action( 'wc_muse_customer_export_success', $content, $response );

			if ( isset( $response->id ) ) {

				/*
				  @DEBUG: customer export.
				bd_var_dump($content['wp_id']);
				bd_var_dump($response->id);
				exit;
				 */

				/*	Saving muse customer ID */
				update_user_meta( $content['wp_id'], '_wc_muse_customer_id', $response->id );
				update_user_meta( $content['wp_id'], 'muse_exported', true );

				/*	Saving muse braintree ID */
				$this->maybe_save_braintree_id( $content['wp_id'], $response );

				/*
				  @DEBUG: customer export.
				bd_var_dump($response);
				exit;
				 */

				return $response;
			}

			return false;

		} catch ( Exception $e ) {

			/*
			  @DEBUG: customer export.
			bd_var_dump($content['wp_id']);
			bd_var_dump($response->id);
			exit;
			 */

			$to        = get_option( 'wc-muse-report_email' );
			$subject   = sprintf( 'Export customer failed - Customer #%s', $content['wp_id'] );
			$message   = sprintf( 'Unexpected response when exporting customer <a href="%s">#%s</a>', get_edit_user_link( $content['wp_id'] ), $content['wp_id'] );
			$muse_mail = new Wc_Muse_Mail( $to, $subject, $message );
			$muse_mail->send();

			do_action(
				'wc_muse_customer_export_failed',
				$user,
				array(
					'code'    => $e->getCode(),
					'message' => $e->getMessage(),
				)
			);

			/*
			  @TODO: Improve error notice.
			bd_var_dump($e); exit;
			 */
			throw new Exception( 'Failed when updating customer information. Please try again or contact support if the problem persist.' );
		}
	}

	public function format_address( $content, $customer ) {
		// Muse fields
		// [:first_name, :last_name, :first_line, :second_line, :city, :state, :state_code, :country_code, :zip_code]
		$_content = array();

		if ( isset( $content['addresses']['shipping'] ) ) {

			$_content['addresses']['shipping'] = $content['addresses']['shipping'];

			$content['addresses']['shipping'] = array();

			$content['addresses']['shipping']['first_name'] =
				isset( $_content['addresses']['shipping']['first_name'] ) ?
					$_content['addresses']['shipping']['first_name'] :
						$customer->get_shipping_first_name();

			$content['addresses']['shipping']['last_name'] =
				isset( $_content['addresses']['shipping']['last_name'] ) ?
					$_content['addresses']['shipping']['last_name'] :
						$customer->get_shipping_last_name();

			$content['addresses']['shipping']['company'] =
				isset( $_content['addresses']['shipping']['company'] ) ?
					$_content['addresses']['shipping']['company'] :
						$customer->get_shipping_company();

			$content['addresses']['shipping']['first_line'] =
				isset( $_content['addresses']['shipping']['address_1'] ) ?
					$_content['addresses']['shipping']['address_1'] :
						$customer->get_shipping_address_1();

			$content['addresses']['shipping']['second_line'] =
				isset( $_content['addresses']['shipping']['address_2'] ) ?
					$_content['addresses']['shipping']['address_2'] :
						$customer->get_shipping_address_2();

			$content['addresses']['shipping']['city'] =
				isset( $_content['addresses']['shipping']['city'] ) ?
					$_content['addresses']['shipping']['city'] :
						$customer->get_shipping_city();

			$content['addresses']['shipping']['state_code'] =
				isset( $_content['addresses']['shipping']['state'] ) ?
					$_content['addresses']['shipping']['state'] :
						$customer->get_shipping_state();

			$content['addresses']['shipping']['country_code'] =
				isset( $_content['addresses']['shipping']['country'] ) ?
					$_content['addresses']['shipping']['country'] :
						$customer->get_shipping_country();

			$content['addresses']['shipping']['zip_code'] =
				isset( $_content['addresses']['shipping']['postcode'] ) ?
					$_content['addresses']['shipping']['postcode'] :
						$customer->get_shipping_postcode();

			$content['addresses']['shipping']['state'] =
				isset( $_content['addresses']['shipping']['country'] ) && isset( $_content['addresses']['shipping']['state'] ) ?
					Wc_Muse_Helper::get_state_name( $_content['addresses']['shipping']['country'], $_content['addresses']['shipping']['state'] ) :
						Wc_Muse_Helper::get_state_name( $customer->get_shipping_country(), $customer->get_shipping_state() );

			$content['addresses']['shipping']['country'] =
				isset( $_content['addresses']['shipping']['country'] ) ?
					Wc_Muse_Helper::get_country_name( $_content['addresses']['shipping']['country'] ) :
						Wc_Muse_Helper::get_country_name( $customer->get_shipping_country() );

			$content['addresses']['shipping']['default'] = true;
		}

		if ( isset( $content['addresses']['billing'] ) ) {

			$_content['addresses']['billing'] = $content['addresses']['billing'];

			$content['addresses']['billing'] = array();

			$content['addresses']['billing']['first_name'] =
				isset( $_content['addresses']['billing']['first_name'] ) ?
					$_content['addresses']['billing']['first_name'] :
						$customer->get_billing_first_name();

			$content['addresses']['billing']['last_name'] =
				isset( $_content['addresses']['billing']['last_name'] ) ?
					$_content['addresses']['billing']['last_name'] :
						$customer->get_billing_last_name();

			$content['addresses']['billing']['company'] =
				isset( $_content['addresses']['billing']['company'] ) ?
					$_content['addresses']['billing']['company'] :
						$customer->get_billing_company();

			$content['addresses']['billing']['first_line'] =
				isset( $_content['addresses']['billing']['address_1'] ) ?
					$_content['addresses']['billing']['address_1'] :
						$customer->get_billing_address_1();

			$content['addresses']['billing']['second_line'] =
				isset( $_content['addresses']['billing']['address_2'] ) ?
					$_content['addresses']['billing']['address_2'] :
						$customer->get_billing_address_2();

			$content['addresses']['billing']['city'] =
				isset( $_content['addresses']['billing']['city'] ) ?
					$_content['addresses']['billing']['city'] :
						$customer->get_billing_city();

			$content['addresses']['billing']['state_code'] =
				isset( $_content['addresses']['billing']['state'] ) ?
					$_content['addresses']['billing']['state'] :
						$customer->get_billing_state();

			$content['addresses']['billing']['country_code'] =
				isset( $_content['addresses']['billing']['country'] ) ?
					$_content['addresses']['billing']['country'] :
						$customer->get_billing_country();

			$content['addresses']['billing']['zip_code'] =
				isset( $_content['addresses']['billing']['postcode'] ) ?
					$_content['addresses']['billing']['postcode'] :
						$customer->get_billing_postcode();

			$content['addresses']['billing']['state'] =
				isset( $_content['addresses']['billing']['country'] ) && isset( $_content['addresses']['billing']['state'] ) ?
					Wc_Muse_Helper::get_state_name( $_content['addresses']['billing']['country'], $_content['addresses']['billing']['state'] ) :
						Wc_Muse_Helper::get_state_name( $customer->get_billing_country(), $customer->get_billing_state() );

			$content['addresses']['billing']['country'] =
				isset( $_content['addresses']['billing']['country'] ) ?
					Wc_Muse_Helper::get_country_name( $_content['addresses']['billing']['country'] ) :
						Wc_Muse_Helper::get_country_name( $customer->get_billing_country() );

			$content['addresses']['billing']['default'] = false;
		}

		/*
		  @DEBUG: customer export formatted content.
		bd_var_dump($content); exit;
		 */

		return $content;
	}

	public function set_default_address( $content, $customer ) {

		if ( $all_meta_data = $customer->get_meta_data() ) {
			foreach ( $all_meta_data as $key => $meta_data ) {

				if ( $meta_data->key == '_account_billing_default' ) {
					if ( isset( $content['addresses']['billing'] ) ) {
						$content['addresses']['billing']['default'] = boolval( $meta_data->value );
					}
				} elseif ( $meta_data->key == '_account_shipping_default' ) {
					if ( isset( $content['addresses']['shipping'] ) ) {
						$content['addresses']['shipping']['default'] = boolval( $meta_data->value );
					}
				}
			}
		}

		return $content;
	}


	public function maybe_save_braintree_id( $woo_customer_id, $response ) {

		$braintree_id = false;

		if ( isset( $response->gateway_accounts_sluggized ) && $response->gateway_accounts_sluggized ) {

			foreach ( $response->gateway_accounts_sluggized as $gateway_account ) {

				if (
				isset( $gateway_account->slug )
				&&
				isset( $gateway_account->gateway_account_id )
				&&
				'braintree' === $gateway_account->slug
				) {
					$braintree_id = $gateway_account->gateway_account_id;
				}
			}
		}

		if ( $braintree_id ) {

			$cc_handler = Wc_Muse_Handler_Credit_Card::get_instance();

			$cc_handler->update_customer_braintree_id( $woo_customer_id, $braintree_id );
		}
	}

	/**
	 * Get customer upcoming ticket.
	 *
	 * @since     1.0.0
	 *
	 * @return    array    Upcoming Tickets.
	 */
	public function get_upcoming_tickets( $woo_user_id ) {

		$muse_user_id = get_user_meta( $woo_user_id, '_wc_muse_customer_id', true );
		// $muse_user_id = '807c294d-fe36-4e1c-b436-dd4341dcc2f3';

		try {

			$organization_id = $this->connector->organization_id;

			// $content = array( 'profile_id' => $muse_user_id );
			$content = array();

			$response = $this->connector->get( "integrations/{$organization_id}/upcoming-tickets?profile_id={$muse_user_id}", $content );

			do_action( 'wc_muse_customer_get_upcoming_tickets_success', $muse_user_id, $response );

			if ( isset( $response->errors ) && $response->errors ) {
				return false;
			}

			$etickets = array();

			if ( $response ) {
				foreach ( $response as $eticket_data ) {
					$etickets[] = new Wc_Muse_Eticket( $eticket_data );
				}
			}

			return $etickets;

		} catch ( Exception $e ) {

			do_action(
				'wc_muse_customer_get_upcoming_tickets_failed',
				$muse_user_id,
				array(
					'code'    => $e->getCode(),
					'message' => $e->getMessage(),
				)
			);

			return false;

		}

	}

	/**
	 * Get upcoming ticket.
	 *
	 * @since     1.0.0
	 *
	 * @return    array    Ticket Details.
	 */
	public function get_ticket_details( $woo_user_id, $ticket_id ) {

		$muse_user_id = get_user_meta( $woo_user_id, '_wc_muse_customer_id', true );
		// $muse_user_id = '807c294d-fe36-4e1c-b436-dd4341dcc2f3';
		// $muse_user_id = 'cbb83707-0ff7-432c-9ec1-af85359dd15c';

		try {

			$organization_id = $this->connector->organization_id;

			$response = $this->connector->get( "integrations/{$organization_id}/upcoming-tickets/{$ticket_id}?profile_id={$muse_user_id}" );

			do_action( 'wc_muse_customer_get_ticket_details_success', $muse_user_id, $response );

			return ( isset( $response[0] ) ? new Wc_Muse_Eticket( $response[0] ) : false );

		} catch ( Exception $e ) {

			do_action(
				'wc_muse_customer_get_ticket_details_failed',
				$id,
				array(
					'code'    => $e->getCode(),
					'message' => $e->getMessage(),
				)
			);

			return false;

		}

	}

	/**
	 * Verify new customer.
	 *
	 * @since     1.0.0
	 *
	 * @return    array    Ticket Details.
	 */
	public function verify_customer( $woo_user_id ) {

		// $muse_customer = $this->export_customer( $woo_user_id );

		// var_dump( $muse_customer );

		// exit;

		// $muse_user_id = get_user_meta( $woo_user_id, '_wc_muse_customer_id', true );

		// $woo_customer_data = $this->get_woo_customer_data( $woo_user_id );

		/*	Check if customer exist in Muse?  */

	}

	/**
	 * Get firstname / lastname by email.
	 *
	 * @since     1.0.0
	 *
	 * @param     $field    string     first || last.
	 * @param     $email    string     email format string.
	 *
	 * @return    string    First / last name.
	 */
	public static function get_name_from_email( $field, $email ) {

		if ( ! in_array( $field, array( 'first', 'last' ) ) ) {
			return false;
		}

		if ( $parts = explode( '@', $email ) ) {
			return ( 'first' === $field ? $parts[0] : $parts[1] );
		}
	}

}
