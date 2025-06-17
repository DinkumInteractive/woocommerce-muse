<?php
/**
 * My Account page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * My Account navigation.
 *
 * @since 2.6.0
 */
///do_action( 'woocommerce_account_navigation' ); ?>

<!-- 
<div class="wc-noticez">
	<p class="wooac-user">
		<?php printf( __( 'Welcome back,  %1$s !', 'woocommerce' ), '<strong>' . esc_html( $current_user->display_name ) . '</strong>' ); ?>
	</p>
	<a href="<?php echo esc_url( wc_logout_url( wc_get_page_permalink( 'myaccount' ) ) ); ?>" class="wooac-user__logout">
		<?php _e( 'Log out', 'woocommerce' ); ?>
	</a>
</div>
 -->

<?php if ( $muse_id = get_user_meta( get_current_user_id(), '_wc_muse_customer_id', true ) ): ?>

	<div class="row">
		<div class="col-md-7 order-2 order-md-1">
			<h1 class="title-heading"><?php echo Wc_Muse_Account_Function::get_woo_account_pages_title(); ?></h1>
		</div>
		<div class="col-md-5 order-1 order-md-2">
			<div class="text-right">
				<?php woocommerce_breadcrumb( array( 'delimiter' => ' &raquo; ' ) ) ?>
			</div>
		</div>
	</div>
	<div class="space15"></div>
	<div class="woocommerce-MyAccount-content">
		<?php
			/**
			 * My Account content.
			 *
			 * @since 2.6.0
			 */
			do_action( 'woocommerce_account_content' );
		?>
	</div>
<?php else: ?>

	<?php the_field( 'wc_muse_content_unverified_customer', 'options' ) ?>
<?php endif; ?>
