<?php

/**
 * Email helper.
 *
 * @since      1.0.0
 * @package    Wc_Muse
 * @subpackage Wc_Muse/includes
 * @author     Dinkum Interactive <support@dinkuminteractive.com>
 */
class Wc_Muse_Mail {

	/**
	 * Target email to send.
	 * 
	 * @var string
	 */
	public $email;

	/**
	 * Email subject.
	 * 
	 * @var string
	 */
	public $subject;

	/**
	 * Email message.
	 * 
	 * @var string
	 */
	public $message;

	/**
	 * Mail content type.
	 * 
	 * @var string
	 */
	private $wp_mail_content_type = 'text/html';

	/**
	 * Whether the email contents were sent successfully.
	 * 
	 * @var bool
	 */
	private $wp_mail;

	/**
	 * Create mail instance.
	 * 
	 * @param mixed   $to        An array or comma-separated list of email addresses to send message.
	 * @param String  $subject   Email subject.
	 * @param String  $message   Message contents.
	 *
	 * @return [obj]  Class instance.
	 */
	public function __construct( $to, String $subject, String $message ) {

		$this->email = $to;
		$this->subject = $subject;
		$this->message = $message;

		return $this;
	}

	/**
	 * Set mail content type.
	 * 
	 * @param String   $type    WP Mail content type.
	 *
	 * @return [obj]  Class instance.
	 */
	public function set_wp_mail_content_type( String $type ) {

		$this->wp_mail_content_type = $type;

		return $this;
	}

	/**
	 * Send email based on instance parameters.
	 * 
	 * @param  String   $to      String of predefined email name.
	 * @return boolean
	 */
	public function send() {

		add_filter( 'wp_mail_content_type', array( $this, 'get_wp_mail_content_type' ), 10, 1 );

		$this->wp_mail = wp_mail( $this->email, $this->subject, $this->message );

		remove_filter( 'wp_mail_content_type', array( $this, 'get_wp_mail_content_type' ), 10, 1 );

		if ( ! $this->wp_mail )
			$this->log_mail_error();

		return ( bool ) $this->wp_mail;
	}

	/**
	 * Return mail content type.
	 * 
	 * @return string;
	 */
	public function get_wp_mail_content_type() {

		return ( string ) $this->wp_mail_content_type;
	}

	/**
	 * True if mail succeeded. False if not.
	 * 
	 * @return boolean
	 */
	public function is_success() {

		return ( bool ) $this->wp_mail;
	}

	/**
	 * Log mail error using WC Logger
	 * 
	 */
	private function log_mail_error() {

		//	Use logger if WC >= version 3
		if ( ! Wc_Muse::woocommerce_version_compare( '3.0.0' ) )
			return false;

		$log_handler = wc_get_logger();

		$message = $this->get_log_mail_error_message();

		$context = array( 'source' => 'woocommerce-muse' );

		$log_handler->warning( $message, $context );
	}

	/**
	 * Get customized log message.
	 * 
	 * @return string
	 */
	private function get_log_mail_error_message() {

		ob_start();

		_e( 'Failed when sending wc-muse mail', 'wc-muse' );

		echo "\n# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #\n\n";

		var_dump( $this );
		
		echo "\n\n\n\n";

		return ( string ) ob_get_clean();
	}
}