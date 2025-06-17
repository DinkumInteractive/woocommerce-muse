<?php
/**
 * My Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-address.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
	$get_addresses = apply_filters( 'woocommerce_my_account_get_addresses', array(
		'billing' => __( 'Billing address', 'woocommerce' ),
		'shipping' => __( 'Shipping address', 'woocommerce' ),
	), $customer_id );
} else {
	$get_addresses = apply_filters( 'woocommerce_my_account_get_addresses', array(
		'billing' => __( 'Billing address', 'woocommerce' ),
	), $customer_id );
}

$oldcol = 1;
$col    = 1;
?>
<div class="edit-address-wrapper">
	<?php if ( ! get_user_meta( get_current_user_id(), 'wcemailverified', true ) && !current_user_can( 'manage_options' ) ): ?>
		<div class="edit-address-curtain">
			<div class="edit-address-curtain-note">
				<?php _e( 'To access this feature, please confirm your email address first.', 'pcms-concerts' ) ?>
			</div>
		</div>
	<?php endif; ?>

	<p>
		<?php echo apply_filters( 'woocommerce_my_account_my_address_description', __( 'The following addresses will be used on the checkout page by default.', 'woocommerce' ) ); ?>
	</p>
	<div class="space15"></div>

	<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) : ?>
		<div class="row woocommerce-Addresses addresses">
	<?php endif; ?>

	<?php foreach ( $get_addresses as $name => $title ) : ?>

		<div class="col-md-6 woocommerce-Address">
			<div class="wcBox-white position-relative">

				<header class="woocommerce-Address-title title wcTitle-line-wrap">

					<h3 class="wc-title">

						<?php echo $title; ?>

						<?php if ( get_user_meta( get_current_user_id(), "_account_{$name}_default", true ) ): ?>

							<span class="badge badge-primary float-right"><?php _e( 'Primary', 'pcms-concerts' ) ?></span>
							
						<?php endif ?>
					</h3>
				</header>


				<address class="addressess">
						<?php
						$address = wc_get_account_formatted_address( $name );
						echo $address ? wp_kses_post( $address ) : esc_html_e( 'You have not set up this type of address yet.', 'woocommerce' );
						?>
						<div class="space15"></div>
						<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="edit btn--blueborder btn--floatingaddress btn"><?php _e( 'Edit', 'woocommerce' ); ?></a>
				</address>
			</div>
		</div>

	<?php endforeach; ?>

	<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) : ?>
		</div>
	<?php endif; ?>
</div>
