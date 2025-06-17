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
	public $debug_mode  = false;
	public $admin_email = '';
	private $log_handler;
	private $context;

	public function __construct() {

		if ( 'sandbox' === get_option( 'wc-muse-environment' ) ) {
			$this->base_url        = get_option( 'wc-muse-sandbox_api_url' );
			$this->auth_token      = get_option( 'wc-muse-sandbox_auth_token' );
			$this->organization_id = get_option( 'wc-muse-sandbox_org_id' );
		} else {
			$this->base_url        = get_option( 'wc-muse-api_url' );
			$this->auth_token      = get_option( 'wc-muse-auth_token' );
			$this->organization_id = get_option( 'wc-muse-org_id' );
		}

		// Use logger if WC >= version 3
		if ( Wc_Muse::woocommerce_version_compare( '3.0.0' ) ) {

			$this->log_handler = wc_get_logger();

			$this->context = array( 'source' => 'woocommerce-muse' );

		}

		if ( empty( $this->admin_email ) ) {
			$this->admin_email = get_option( 'wc-muse-admin_email' );
		}
	}

	public function get( $query = '', $package = false ) {

		$url = $this->get_url( $query );

		// var_dump($url);// exit;

		$content = $this->transform_package( $package );

		// var_dump($content);// exit;

		$args = array(
			'headers' => array(
				'Content-type: application/json; charset=UTF-8',
				'Content-Length: ' . strlen( $content ),
				'authorization:' . $this->auth_token,
			),
			'content' => $content,
		);

		try {
			$response = $this->action( 'get', $url, $args );

			// var_dump($response); exit;

			return $this->transform_response( $response );

		} catch ( Exception $e ) {
			throw $e;
		}

	}

	public function post( $query = '', $package = false, $custom_request = false ) {

		$url = $this->get_url( $query );

		// var_dump($url);// exit;

		$content = $this->transform_package( $package );

		// var_dump($content);// exit;

		$args = array(
			'headers' => array(
				'Content-type: application/json; charset=UTF-8',
				'Content-Length: ' . strlen( $content ),
				'authorization:' . $this->auth_token,
			),
			'content' => $content,
		);

		try {

			$response = $this->action( 'post', $url, $args, $custom_request );

			// var_dump($response); exit;

			return $this->transform_response( $response );

		} catch ( Exception $e ) {

			throw $e;

		}

	}

	public function put( $query = '', $package = false, $custom_request = false ) {

		$url = $this->get_url( $query );

		// var_dump($url);// exit;

		$content = $this->transform_package( $package );

		// var_dump($content);// exit;

		$args = array(
			'headers' => array(
				'Content-type: application/json; charset=UTF-8',
				'Content-Length: ' . strlen( $content ),
				'authorization:' . $this->auth_token,
			),
			'content' => $content,
		);

		try {

			$response = $this->action( 'put', $url, $args, $custom_request );

			// var_dump($response); exit;

			return $this->transform_response( $response );

		} catch ( Exception $e ) {

			throw $e;

		}

	}

	public function patch( $query = '', $package = false, $custom_request = false ) {

		$url = $this->get_url( $query );

		// var_dump($url);// exit;

		$content = $this->transform_package( $package );

		// var_dump($content);// exit;

		$args = array(
			'headers' => array(
				'Content-type: application/json; charset=UTF-8',
				'Content-Length: ' . strlen( $content ),
				'authorization:' . $this->auth_token,
			),
			'content' => $content,
		);

		try {

			$response = $this->action( 'patch', $url, $args, $custom_request );

			// var_dump($response); exit;

			return $this->transform_response( $response );

		} catch ( Exception $e ) {

			throw $e;

		}

	}

	private function action( $type, $url, $args = false, $custom_request = false ) {

		$curl = curl_init();

		curl_setopt( $curl, CURLOPT_URL, $url );

		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );

		switch ( $type ) {

			case 'post':
			case 'put':
			case 'patch':
				if ( ! $args ) {
					return false;
				}
				curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 10 );
				curl_setopt( $curl, CURLOPT_TIMEOUT, 10 );
				curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
				curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, false );
				curl_setopt( $curl, CURLOPT_HTTPHEADER, $args['headers'] );
				curl_setopt( $curl, CURLOPT_POSTFIELDS, $args['content'] );
				$request = 'POST';
				if ( $type == 'put' ) {
					$request = 'PUT';
				} elseif ( $type == 'patch' ) {
					$request = 'PATCH';
				} else {
					curl_setopt( $curl, CURLOPT_POST, 1 );
				}
				curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $request );
				break;

			case 'get':
				curl_setopt( $curl, CURLOPT_HEADER, 0 );
				curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'GET' );
				curl_setopt( $curl, CURLOPT_HTTPHEADER, $args['headers'] );
				if ( isset( $args['content'] ) ) {
					curl_setopt( $curl, CURLOPT_POSTFIELDS, $args['content'] );
				}
				break;

		}

		if ( $custom_request ) {
			curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $custom_request );
		}

		/*
		  @TODO: testing header out
		curl_setopt( $curl, CURLINFO_HEADER_OUT, true );
		 */

		$response    = curl_exec( $curl );
		$http_status = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
		curl_close( $curl );

		/*
		  @DEBUG: Checking response
		 *
		 */
		if ( 'yes' === get_option( 'options_debug_muse_global_request' ) ) {

			echo '<h1>URL</h1>';
			echo '<pre>';
			var_dump( $url );
			echo '</pre>';
			echo '<h1>TYPE</h1>';
			echo '<pre>';
			var_dump( $type );
			echo '</pre>';
			echo '<h1>HTTP STATUS </h1>';
			echo '<pre>';
			var_dump( $http_status );
			echo '</pre>';
			echo '<h1>ARGS</h1>';
			echo '<pre>';
			var_dump( $args );
			echo '</pre>';
			echo '<h1>RESPONSE</h1>';
			echo '<pre>';
			var_dump( $response );
			echo '</pre>';
			exit;
		}

		// Update log with errors
		if ( $this->log_handler && $this->debug_mode ) {

			ob_start();
			echo '# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #';
			echo "\n\n";
			echo 'URL';
			echo "\n";
			var_dump( $url );
			echo "\n";
			echo 'TYPE';
			echo "\n";
			var_dump( $type );
			echo "\n";
			echo 'HTTP STATUS';
			echo "\n";
			var_dump( $http_status );
			echo "\n";
			echo 'ARGS';
			echo "\n";
			var_dump( $args );
			echo "\n";
			echo 'RESPONSE';
			echo "\n";
			var_dump( $response );
			echo "\n\n\n\n";
			$message = ob_get_clean();

			$this->log_handler->notice( $message, $this->context );
		}

		if ( $http_status === 200 || $http_status === 201 ) {

			return $response;

		} else {

			// Something goes wrong
			throw new Exception( json_encode( $response ), $http_status );
		}

	}

	public function transform_package( $package ) {

		return json_encode( $package );

	}

	private function transform_response( $response ) {

		return json_decode( $response );

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
