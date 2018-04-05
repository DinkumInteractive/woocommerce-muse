<?php

/**
 * Main connector.
 *
 * @since      1.0.0
 * @package    Wc_Muse
 * @subpackage Wc_Muse/includes/core
 * @author     Dinkum Interactive <support@dinkuminteractive.com>
 */
class Wc_Muse_Connector {

	public $base_url;
	public $auth_token;
	public $organization_id;
	public $debug_mode = false;
	private $log_handler;
	private $context;

	public function __construct() {

		$this->base_url = get_option( 'wc-muse-api_url' );
		$this->auth_token = get_option( 'wc-muse-auth_token' );
		$this->organization_id = get_option( 'wc-muse-org_id' );

		//	Use logger if WC >= version 3
		if ( Wc_Muse::woocommerce_version_compare( '3.0.0' ) ) {

			$this->log_handler = wc_get_logger();

			$this->context = array( 'source' => 'woocommerce-muse' );

		}

	}

	public function get( $query = '' ) {

		$url = $this->get_url( $query );

		$args 		= array(
			'headers'	=> array(
				'Content-type: application/json; charset=UTF-8',
				'Content-Length: ' . strlen( $content ),
				'authorization:' . $this->auth_token,
			),
		);

		try {
			$response = $this->action( 'get', $url, $args );
			return $this->transform_response( $response );
		} catch (Exception $e) {
			throw $e;
		}

	}

	public function post( $query = '', $package = false, $extra = false ) {
		
		$url 		= $this->get_url( $query, $extra );

		$content 	= $this->transform_package( $package );

		$args 		= array(
			'headers'	=> array(
				'Content-type: application/json; charset=UTF-8',
				'Content-Length: ' . strlen( $content ),
				'authorization:' . $this->auth_token,
			),
			'content'	=> $content,
		);

		try {

			$response = $this->action( 'post', $url, $args );
			return $this->transform_response( $response );

		} catch (Exception $e) {

			throw $e;

		}

	}

	private function action( $type, $url, $args = false ) {

		$curl = curl_init();

		curl_setopt( $curl, CURLOPT_URL, $url );

		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );

		switch ( $type ) {

			case 'post':
				if ( ! $args ) return false;
				curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 10 );
				curl_setopt( $curl, CURLOPT_TIMEOUT, 10 );
				curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
				curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, false );
				curl_setopt( $curl, CURLOPT_HTTPHEADER, $args['headers'] );
				curl_setopt( $curl, CURLOPT_POSTFIELDS, $args['content'] );
				curl_setopt( $curl, CURLOPT_POST, 1 );
				break;

			case 'get':
				curl_setopt( $curl, CURLOPT_HEADER, 0 );
				curl_setopt( $curl, CURLOPT_HTTPHEADER, $args['headers'] );
				break;
			
		}

		/*	@TODO: testing header out
		curl_setopt( $curl, CURLINFO_HEADER_OUT, true );
		 */

		$response = curl_exec( $curl );
		$http_status = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
		curl_close( $curl );

		if ( $http_status === 200 || $http_status === 201 ) {
			return $response;
		} else {
			//	Update log with errors
			if ( $this->log_handler && $this->debug_mode ) {

				$message = $http_status . "\n";
				$message .= json_encode( $response ) . "\n\n";

				$this->log_handler->notice( $message, $this->context );

			}

			//Something goes wrong
			throw new Exception( json_encode( $response ), $http_status );
		}

	}

	private function transform_package( $package ) {

		return json_encode( $package );

	}

	private function transform_response( $response ) {

		return $this->debug_mode ? $response : json_decode( $response );

	}

	private function get_url( $query, $extra = false ) {

		$extra_param = '';

		if ( $extra ) {

			if ( is_array( $extra ) ) {

				$extra_param = $this->parse_extra( $extra );

			} elseif ( is_string( $extra ) ) {

				$extra_param = $extra;

			}

		}

		$url = "{$this->base_url}/{$query}" . ( $extra_param ? "?{$extra_param}" : '' );

		return $url;

	}

	private function parse_extra( $args ) {

		$extra_param = '';

		foreach ( $args as $key => $value ) {

			$extra_param .= '&' . $key . '=' . $value;

		}

		return $extra_param;

	}

	public function set_debug( $bool ) {

		$this->debug_mode = $bool;

	}

}
