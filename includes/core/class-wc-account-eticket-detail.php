<?php

class Wc_Muse_Account_Eticket_Detail {

	/**
	 * Instance
	 *
	 * @var obj
	 */
	protected static $instance = null;

	/**
	 * Instance data
	 *
	 * @var array
	 */
	public $data;

	/**
	 * Initiate detail object.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public function set_data( $data ) {

		$this->data = $data;

		$this->data->venue_id = get_post_meta( $this->data->web_id, 'event_venues', true );
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
	 * Print eticket detail template.
	 *
	 * @since     1.0.0
	 *
	 * @return    html    Eticket detail template.
	 */
	public function get_template() {

		if ( $this->data ) {

			wc_get_template( 'myaccount/eticket-detail.php' );
		}
	}

	/**
	 * Print eticket detail template.
	 *
	 * @since     1.0.0
	 *
	 * @return    html    Eticket detail template.
	 */
	public function get_data( $key ) {

		$value = ( isset( $this->data->$key ) ? $this->data->$key : false );

		switch ( $key ) {

			case 'venue_address':
			case 'venue_phone':
				$value = get_post_meta( $this->data->venue_id, $key, true );
				break;

			case 'parking_options':
			case 'venue_map_embed':
				$value = get_field( $key, $this->data->venue_id );
				break;

			case 'venue_extra_information':
				$venue = get_post( $this->data->venue_id );
				$value = $venue->post_content;
				break;

			case 'venue_upcoming_events':
				$value = Wc_Muse_Account_Function::get_venue_upcoming_events( $this->data->venue_id, 5 );
				break;
		}

		return $value;
	}
}
