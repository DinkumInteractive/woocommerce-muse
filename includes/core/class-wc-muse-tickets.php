<?php

/**
 * Ticket Manager.
 *
 * @since      1.0.0
 * @package    Wc_Muse
 * @subpackage Wc_Muse/includes
 * @author     Dinkum Interactive <support@dinkuminteractive.com>
 */
class Wc_Muse_Tickets {

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

		$this->connector->set_debug( 'yes' === get_option( 'options_debug_muse_ticket_handler' ) );
	
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
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public function cancel_ticket( $ticket_id, $order_item_id, $order_id, $cancel_type ) {

		/* Request ticket cancel to muse. */
		try {

			$organization_id = $this->connector->organization_id;

			$args = array( 'order_item_places_ids' => array( $ticket_id ), 'admin_email' => $this->connector->admin_email );

			switch ( $cancel_type ) {

				case 'donate':
					$args['create_donation'] = true;
					break;
				
				case 'account_credit':
					$args['create_credit'] = true;
					break;
			}
			$response = $this->connector->put( "integrations/{$organization_id}/order-items/{$order_item_id}/cancel", $args );
			
		} catch ( Exception $e ) {
			
			$response = false;
		}

		$this->admin_email_notice( $response, $order_id, $ticket_id );

		return $response;
	}

	/**
	 * Notice admin by email.
	 * 
	 * @param   obj      $response    Curl response.
	 * @param   string   $order_id    Order ID.
	 * @param   string   $ticket_id   Muse Ticket ID.
	 * @return  bool              WP Mail response.
	 */
	public function admin_email_notice( $response, $order_id, $ticket_id ){

		$user_id = get_current_user_id();

		$to = get_option( 'wc-muse-report_email' );
		
		$subject = sprintf( 'Ticket cancelled in website - Customer #%s', $user_id );
		
		if ( $response ) {

			ob_start();
			echo sprintf( '<p><b>Customer <a href="%s">#%s</a></b></p>', get_edit_user_link( $user_id ), $user_id );
			echo sprintf( '<p>%s : %s</p>', __( 'Order ID', 'wc-muse' ), $order_id );
			echo sprintf( '<p>%s : %s</p>', __( 'Ticket ID', 'wc-muse' ), $ticket_id );
			echo sprintf( '<p><b>%s</b></p>', __( 'Server response', 'wc-muse' ) );
			echo '<pre>';
			var_dump( $response );
			echo '</pre>';
			$message = ob_get_clean();

		} else {

			ob_start();
			echo sprintf( '<p><b>Customer <a href="%s">#%s</a></b></p>', get_edit_user_link( $user_id ), $user_id );
			echo sprintf( '<p>%s : %s</p>', __( 'Order ID', 'wc-muse' ), $order_id );
			echo sprintf( '<p>%s : %s</p>', __( 'Ticket ID', 'wc-muse' ), $ticket_id );
			echo sprintf( '<p><b>%s</b></p>', __( 'Response unavailable', 'wc-muse' ) );
			echo sprintf( '<p>%s</p>', __( 'Unable to get response from the server. Please check.', 'wc-muse' ) );
			$message = ob_get_clean();
		}
		
		$muse_mail = new Wc_Muse_Mail( $to, $subject, $message );

		return (bool) $muse_mail->send();
	}
}
