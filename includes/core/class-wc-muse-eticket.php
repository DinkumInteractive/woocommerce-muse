<?php

/**
 * Eticket class.
 *
 * @since      1.0.0
 * @package    Wc_Muse
 * @subpackage Wc_Muse/includes
 * @author     Dinkum Interactive <support@dinkuminteractive.com>
 */
class Wc_Muse_Eticket {

	protected $data;

	public function __construct( $data ) {

		$this->data = $data;

		$this->add_available_date();

		$this->add_data_visible();
	}

	public function __get( $key ) {

		return $this->data->$key;
	}

	public function get_data() {

		return $this->data;
	}

	private function add_data_visible() {

		if ( ! $this->data->web_id )
			return false;

		$this->data->visible = ( date('Y-m-d') >= $this->data->available_date );
	}

	private function add_available_date() {

		if ( ! $this->data->web_id )
			return false;

		$this->data->available_date = get_post_meta( $this->data->web_id, 'eticket_visibility_date', true );
	}
}