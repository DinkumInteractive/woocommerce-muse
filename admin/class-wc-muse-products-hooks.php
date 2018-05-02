<?php

/**
 * WC Muse products hook
 *
 * @package    Wc_Muse
 * @subpackage Wc_Muse/admin
 * @author     Dinkum Interactive <support@dinkuminteractive.com>
 */
class Wc_Muse_Products_Hook {

	/**
	 * 	Add metabox tab in WooCommerce.
	 *
	 * 	@since    0.1.0
	 */
	public function add_tab( $tabs ) {

		// Adds the new tab
		$tabs[ 'wc_muse' ] = array(
			'label'		=> __( 'Muse', 'wc-muse' ),
			'target' 	=> 'general_product_muse',
			'callback' 	=> array( $this, 'add_tab_content' ),
			'class' 	=> apply_filters( 'wc_muse_show_tab', array( 'show_if_simple', 'show_if_variable', 'show_if_grouped' ) ),
		);

		return $tabs;

	}

	public function add_tab_content() {

		include WC_MUSE_ADMIN_TEMPLATE_DIR . 'wc-muse-product-tab.php';

	}

	/**
	 * Display extra info for each variation.
	 *
	 * @since 0.5.0
	 *
	 * @param int     $loop
	 * @param array   $variation_data
	 * @param WP_Post $variation
	 */
	public function add_variation_meta_info( $loop, $variation_data, $variation ) {

		require WC_MUSE_ADMIN_TEMPLATE_DIR . 'wc-muse-product-variation-tab.php';

	}

	/**
	 * 	Save product variations metas.
	 */
	public function save_product_variations_meta( $product_id ) {

		if ( ! isset( $_POST['wc_muse_variation'] ) ) return false;

		$variation_data = $_POST['wc_muse_variation'];

		foreach ( $variation_data as $variation ) {

			$variant_id = $variation['id'];

			update_post_meta( $variant_id, 'seat_slug', $variation['seat_slug'] );

		}

	}

	/**
	 * 	Save product metas.
	 *
	 * 	@since    1.0.0		Save product meta settings from edit-product.
	 */
	public function save_post_meta( $post_id, $post, $update ) {

		$post_type = get_post_type( $post_id );

		$product_type = sanitize_text_field( $_POST['product-type'] );

		// 	Validate post type
		if ( defined('DOING_AJAX') && DOING_AJAX ) return;

		if ( 'product' != $post_type ) return;

		if ( 'auto-draft' === $post->post_status ) return;

		if ( ! isset( $_POST['item_slug'] ) ) return;

		//	Momentary remove to avoid endless loop	
		remove_action( 'save_post_product', array( $this, 'save_post_meta' ), 0 );

		// 	Update Post Meta
		update_post_meta( $post_id, 'item_slug', sanitize_text_field( $_POST['item_slug'] ) );
		update_post_meta( $post_id, 'ticket_type', sanitize_text_field( $_POST['ticket_type'] ) );
		update_post_meta( $post_id, 'sub_item_slug', sanitize_text_field( $_POST['sub_item_slug'] ) );

		//	Reattach hook
		add_action( 'save_post_product', array( $this, 'save_post_meta' ), 0, 3 );

	}

}
