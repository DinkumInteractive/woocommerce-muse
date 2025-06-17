<?php

/**
 * WC Muse Admin User Hooks
 *
 * @package    Wc_Muse
 * @subpackage Wc_Muse/admin
 * @author     Dinkum Interactive <support@dinkuminteractive.com>
 */
class Wc_Muse_Admin_User_Hooks {

	/*
	 * Add a dropdown in users listing screen to filter out the verified and unverified users
	 */
	public function add_muse_id_section_filter() {

		$verification_values = array(
			'0' => __( 'Muse ID not exists', 'wc-muse' ),
			'1' => __( 'Muse ID exists', 'wc-muse' ),
		);
		if ( isset( $_GET['user_muse_id_status'] ) ) {
			$status = $_GET['user_muse_id_status'];
			$status = $status[0];
			if ( '' === $status ) {
				$status = - 1;
			}
		} else {
			$status = - 1;
		}
		echo ' <select name="user_muse_id_status[]" style="float:none;"><option value="">Muse ID Status</option>';
		foreach ( $verification_values as $key1 => $value1 ) {
			$selected = '';
			if ( isset( $verification_values[ $status ] ) ) {
				$selected = ( $status == $key1 ) ? 'selected' : '';
			}
			echo '<option value="' . $key1 . '"' . $selected . '>' . $value1 . '</option>';
		}
		echo '</select>';
		echo '<input type="submit" class="button" value="Filter">';
	}

	/*
	 * Modify user query based on email verification status of the user
	 */
	public function filter_users_by_muse_id_section( $query ) {
		global $pagenow;

		if ( is_admin() && 'users.php' === $pagenow && isset( $_GET['user_muse_id_status'] ) && is_array( $_GET['user_muse_id_status'] ) ) { 
			$status = $_GET['user_muse_id_status'];
			$status = $status[0];

			if ( '' !== $status ) {
				$meta_query = array(
					array(
						'key'     => '_wc_muse_customer_id',
						'compare' => 'NOT EXISTS',
					),
				);
				if ( '1' == $status ) {
					$meta_query = array(
						array(
							'key'   => '_wc_muse_customer_id',
							'compare' => 'EXISTS',
						),
					);
					$query->set( 'meta_key', '_wc_muse_customer_id' );
				}
				$query->set( 'meta_query', $meta_query );
			}
		}
	}

}