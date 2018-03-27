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
	public $debug_mode;

	public function __construct() {

		$this->base_url = get_option( 'wc-muse-api_url' );
		$this->auth_token = get_option( 'wc-muse-auth_token' );
		$this->organization_id = get_option( 'wc-muse-org_id' );

	}

	public function get( $query = '' ) {

		$url = $this->get_url( $query );

		$response = $this->action( 'get', $url );

		if ( $this->debug_mode ) {

			$this->validate_response( $response );

		}

		return $this->transform_response( $response );

	}

	public function post( $query = '', $package = false, $extra = false ) {
		
		$url 		= $this->get_url( $query, $extra );

		var_dump($url);

		$content 	= $this->transform_package( $package );

		$args 		= array(
			'headers'	=> array(
				'Content-type: application/json; charset=UTF-8',
				'Content-Length: ' . strlen( $content ),
				'authorization:' . $this->auth_token,
			),
			'content'	=> $content,
		);

		$response = $this->action( 'post', $url, $args );

		if ( $this->debug_mode ) {

			$this->validate_response( $response );

		}

		return $this->transform_response( $response );

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
				break;
			
		}

		/*	@TODO: testing header out
		curl_setopt( $curl, CURLINFO_HEADER_OUT, true );
		 */

		$response = curl_exec( $curl );

		/*	@TODO: header
		$header_sent = curl_getinfo( $curl, CURLINFO_HEADER_OUT );
		echo "<pre>"; var_dump($header_sent); echo "</pre>"; exit;
		 */

		curl_close( $curl );

		return $response;

	}

	private function transform_package( $package ) {

		$content = json_encode( $package );

		return $content;

	}

	private function transform_response( $response ) {

		$response = ( $this->debug_mode ? $response : json_decode( $response ) );

		return $response;

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

		$url = "{$this->base_url}/{$query}?{$extra_param}";

		return $url;

	}

	private function parse_extra( $args ) {

		$extra_param = '';

		foreach ( $args as $key => $value ) {

			$extra_param .= '&' . $key . '=' . $value;

		}

		return $extra_param;

	}

	public function validate_response( $response ) {

		if ( $response === FALSE ) {

			//Something goes wrong
			throw new Exception( __( 'Can\'t connect to API.', 'wc-muse' ) );

		}

	}

}
