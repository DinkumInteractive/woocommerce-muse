<?php

/**
 * Helper functions.
 *
 * @since      1.0.0
 * @package    Wc_Muse
 * @subpackage Wc_Muse/includes
 * @author     Dinkum Interactive <support@dinkuminteractive.com>
 */
class Wc_Muse_Helper {

	public static function get_country_name( $country_code ) {

		//	Get all countries key/names in an array:
		$countries = WC()->countries->get_countries();

		return isset( $countries[$country_code] ) ? $countries[$country_code] : $country_code;
	}

	public static function get_state_name( $country_code, $state_code ) {

		//	Get all country states key/names in a multilevel array:
		$country_states = WC()->countries->get_states();

		return (isset($country_states[$country_code]) && isset($country_states[$country_code][$state_code])) ? $country_states[$country_code][$state_code] : $state_code;
	}
}
