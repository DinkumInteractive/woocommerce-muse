<?php

/**
 * Customer Manager.
 *
 * @since      1.0.0
 * @package    Wc_Muse
 * @subpackage Wc_Muse/includes
 * @author     Dinkum Interactive <support@dinkuminteractive.com>
 */
class Wc_Muse_Wp_Profile {

	/**
	 * Instance
	 *
	 * @var obj
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin.
	 *
	 * @since     1.0.0
	 */
	function __construct() {

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
	 * Process muse profiles to update them
	 *
	 * @since     1.0.0
	 */
	public function process_muse_profiles() {
		global $wpdb;
		$message = '';

		// Added just for security reasons, remove this line when you want to run this process again.
		return false;

		// Update Muse IDs.
		$results = $wpdb->get_results( "SELECT me.* FROM `muse_emails` me INNER JOIN $wpdb->users wpu ON me.default_email = wpu.user_email" );

		if ( $results ) {
			foreach ( $results as $muse_email ) {
				$user = get_user_by( 'email', $muse_email->default_email );
				if ( $user ) {
					$message .= $this->update_user_muse_id( $user, $muse_email->muse_id );
					// Update Braintree IDs.
					$results_bt = $wpdb->get_results( "SELECT mb.* FROM `muse_braintree_ids` mb INNER JOIN $wpdb->users wpu ON mb.default_email = wpu.user_email AND wpu.user_email = '{$muse_email->default_email}'" );
					if ( $results_bt ) {
						foreach ( $results_bt as $muse_bt_id ) {
							$message .= $this->update_user_muse_braintree_id( $user, $muse_bt_id->muse_id, $muse_bt_id->gateway_account_id );
						}
					}
					$message .= "<br>----------------------- \n";
				} else {
					$message .= sprintf( "<br> User with email '%s' was not found. \n", $muse_email->default_email );
				}
			}
		}
		return $message;
	}

	/**
	 * Update Muse ID in the usermeta table
	 *
	 * @since     1.0.0
	 *
	 * @return    string    message.
	 */
	public function update_user_muse_id( $user, $muse_id ) {

		// check for matching user.
		if ( $user ) {
			if ( update_user_meta( $user->ID, '_wc_muse_customer_id', $muse_id ) ) {
				$content = sprintf( "<br> User with email '%s (%s)' was updated with Muse ID '%s'. \n", $user->user_email, $user->ID, $muse_id );
			} else {
				$content = sprintf( "<br> User with email '%s (%s)' was NOT UPDATE or maybe already contains Muse ID '%s'. \n", $user->user_email, $user->ID, $muse_id );
			}
		} else {
			$content = sprintf( "<br> User with email '%s' was not found. \n", $user->user_email );
		}

		return $content;

	}

	/**
	 * Update Muse ID in the usermeta table
	 *
	 * @since     1.0.0
	 *
	 * @return    string    message.
	 */
	public function update_user_muse_braintree_id( $user, $muse_id, $braintree_user_id ) {

		// check for matching user.
		if ( $user ) {
			$stored_muse_id = get_user_meta( $user->ID, '_wc_muse_customer_id', true );
			if ( $stored_muse_id === $muse_id ) {

				if ( $braintree_user_id && $this->save_braintree_id( $user->ID, $braintree_user_id ) ) {
					$content = sprintf( "<br> User with email '%s (%s)' was updated with Braintree ID '%s'. \n", $user->user_email, $user->ID, $braintree_user_id );
				} elseif ( ! $braintree_user_id ) {
					$content = sprintf( "<br> User with email '%s (%s)' was NOT UPDATE because Braintree ID is EMPTY", $user->user_email, $user->ID );
				} else {
					$content = sprintf( "<br> User with email '%s (%s)' was NOT UPDATE or maybe already contains Braintree ID '%s'. \n", $user->user_email, $user->ID, $braintree_user_id );
				}
			} else {
				$content = sprintf( "<br> User with email '%s (%s)' doesn't match Muse ID '%s'. \n", $user->user_email, $user->ID, $muse_id );
			}
		} else {
			$content = sprintf( "<br> User with email '%s' was not found. \n", $user->user_email );
		}

		return $content;

	}

	public function save_braintree_id( $woo_customer_id, $braintree_id ) {

		if ( $braintree_id && class_exists( 'Wc_Muse_Handler_Credit_Card' ) ) {

			$cc_handler = Wc_Muse_Handler_Credit_Card::get_instance();

			$cc_handler->update_customer_braintree_id( $woo_customer_id, $braintree_id );

			return true;
		} else {
			return false;
		}
	}

}
