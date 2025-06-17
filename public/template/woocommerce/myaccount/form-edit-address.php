<?php
/**
 * Edit address form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-address.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

$page_title = ( 'billing' === $load_address ) ? __( 'Billing address', 'woocommerce' ) : __( 'Shipping address', 'woocommerce' );

do_action( 'woocommerce_before_edit_account_address_form' ); ?>


<?php if ( ! $load_address ) : ?>
	<?php wc_get_template( 'myaccount/my-address.php' ); ?>
<?php else : ?>

<div class="space15"></div>
<div class="wcBox-white">
	<?php $current_user = wp_get_current_user(); ?>
	
	<?php if ( current_user_can( 'manage_options' ) || get_user_meta( $current_user->ID, 'wcemailverified', true ) ): ?>

		<form method="post">
			<header class="wcTitle-line-wrap">
				<div class="row">
					<div class="col-md-6">
						<h3 class="wc-title"><?php echo apply_filters( 'woocommerce_my_account_edit_address_title', $page_title, $load_address ); ?></h3><?php // @codingStandardsIgnoreLine ?>
					</div>
					<div class="col-md-6">
            <input type="hidden" name="muse-info-ajax_nonce" id="muse-info-ajax_nonce" value="<?php echo wp_create_nonce( AJAX_NONCE_MUSE_CUSTOMER_INFO ); ?>">
						<input type="hidden" name="muse-info-user_id" id="muse-info-user_id" value="<?php echo $current_user->ID ?>">
						<input type="hidden" name="muse-info-user_email" id="muse-info-user_email" value="<?php echo $current_user->user_email ?>">
					</div>
				</div>
			</header>

			<div class="woocommerce-address-fields">

				<div class="address-fields-notice"></div>

				<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>

				<div class="woocommerce-address-fields__field-wrapper wc-formwrap">
					<?php
					foreach ( $address as $key => $field ) {
						woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
					}
					?>
				</div>

				<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>
				<div class="space15"></div>	
				<p>
					<button type="submit" class="btn--blueborder btn" name="save_address" value="<?php esc_attr_e( 'Save address', 'woocommerce' ); ?>"><?php esc_html_e( 'Save address', 'woocommerce' ); ?></button>
					<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>" class="btn--greyborder btn" ><?php esc_html_e( 'Cancel', 'woocommerce' ); ?></a>
					<?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>	
					<input type="hidden" name="action" value="edit_address" />
				</p>
			</div>

		</form>

	<?php else: ?>
		<?php _e( 'To access this feature, please confirm your email address first.', 'pcms-concerts' ) ?>
	<?php endif; ?>

<?php endif; ?>

<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>
</div>
