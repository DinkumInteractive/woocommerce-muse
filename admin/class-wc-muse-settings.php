<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Wc_Muse
 * @subpackage Wc_Muse/admin
 * @author     Dinkum Interactive <support@dinkuminteractive.com>
 */
class Wc_Muse_Settings {

	/**
	 * WC settings configurations.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $settings    WC Settings configurations.
	 */
	private $settings;

	public function __construct() {

		$this->settings = array(

			'section_general' => array(
				'name'     => __( 'General Settings', 'wc-muse' ),
				'type'     => 'title',
				'id'       => 'wc-muse-section-general',
			),

			'enable_sync' => array(
				'name'     => __( 'Enable WC Muse', 'wc-muse' ),
				'type'     => 'checkbox',
				'default'  => 'no',
				'id'       => 'wc-muse-enable_sync',
			),

			'connection_status' => array(
				'name'     => __( 'Connection Status', 'wc-muse' ),
				'type'     => 'text',
				'default'  => __( 'Not Connected', 'wc-muse' ),
				'custom_attributes' => array( 'disabled' => 'true' ),
				'id'       => 'wc-muse-connected',
			),

			'api_token' => array(
				'name'     => __( 'API Token', 'wc-muse' ),
				'type'     => 'text',
				'id'       => 'wc-muse-api_token',
			),

			'section_general_end' => array(
				'type'     => 'sectionend',
				'id'       => 'wc-muse-section-general-end',
			),

		);

	}

	public function get_settings() {
		return apply_filters( 'wc_settings_wc_muse_settings', $this->settings );
	}

	/**
	 * Validate input.
	 *
	 * @since    1.0.0
	 */
	public function validate_input() {

		// 	Validate 
		if ( isset( $_POST['wc-muse-api_token'] ) ) {

			$valid_token = apply_filters( 'wc_muse_validate_token', $_POST['wc-muse-api_token'] );

			if ( $valid_token ) {
				WC_Admin_Settings::add_message( __( 'Connected to the API.', 'wc-muse' ) );
			} else {
				WC_Admin_Settings::add_error( __( 'Invalid token specified.', 'wc-muse' ) );
			}

		}

	}

}

