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
	public $api_token;

	public function __construct() {

		$this->base_url = 'https://muse/api/';
		$this->api_token = get_option( 'wc-muse-api_token' );

	}

	protected function get( $query = '' ) {

		$url = $this->get_url( $query );

		$response = $this->action( 'get', $url );

		$this->validate_response( $response );

		return $this->transform_response( $response );

	}

	protected function post( $query = '', $package = false, $extra = false ) {
		
		$url 		= $this->get_url( $query, $extra );

		$content 	= $this->transform_package( $package );

		$args 		= array(
			'headers'	=> array(
				'Content-type: application/json; charset=UTF-8',
				'Content-Length: ' . strlen( $content ),
			),
			'content'	=> $content,
		);

		$response = $this->action( 'post', $url, $args );

		$this->validate_response( $response );

		if ( $conversor ) {

			return $this->transform( $response );

		}

		return $response;

	}

	private function action( $type, $url, $args = false ) {

		$curl = curl_init();

		curl_setopt( $curl, CURLOPT_URL, $url );

		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );

		switch ( $type ) {

			case 'post':
				if ( ! $args ) return false;
				curl_setopt( $curl, CURLOPT_HTTPHEADER, $args['headers'] );
				curl_setopt( $curl, CURLOPT_POSTFIELDS, $args['content'] );
				curl_setopt( $curl, CURLOPT_POST, 1 );
				break;

			case 'get':
				curl_setopt( $curl, CURLOPT_HEADER, 0 );
				break;
			
		}

		$response = curl_exec( $curl );

		curl_close( $curl );

		return $response;

	}

	private function transform_package( $package ) {

		$content = json_encode( $package );

		return $content;

	}

	private function transform_response( $response ) {

		$response = json_decode( $package );

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

		$query = $query ? "/$query" : $query;

		$url = "{$this->base_url}/{$query}?access_token={$this->api_token}{$extra_param}";

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
