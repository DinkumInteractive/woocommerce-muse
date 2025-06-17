<?php
/**
 * Lost password form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-lost-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.2
 */

defined( 'ABSPATH' ) || exit;

$url_myaccount = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );

?>

<?php do_action( 'woocommerce_before_lost_password_form' ); ?>

<h1 class="title-heading"><?php the_title(); ?></h1>

<div class="space30"></div>

<div class="wcBox-white">

	<div class="tab-buttonwrap fullrow row">
		<a class="col-md-6 text-center wc-tab-button active" href="#">Lost Password</a>
		<a class="col-md-6 text-center wc-tab-button" href="<?php echo $url_myaccount; ?>">Login</a>
	</div>

	<div class="fullrow row">

		<div class="col-md-12">

			<form method="post" class="woocommerce-ResetPassword lost_reset_password">

				<p><?php echo apply_filters( 'woocommerce_lost_password_message', esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce' ) ); ?></p><?php // @codingStandardsIgnoreLine ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="user_login"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input class="woocommerce-Input woocommerce-Input--text input-text" type="text" name="user_login" id="user_login" autocomplete="username" />
				</p>

				<div class="clear"></div>

				<?php do_action( 'woocommerce_lostpassword_form' ); ?>

				<div class="space20"></div>

				<p class="woocommerce-form-row form-row">
					<input type="hidden" name="wc_reset_password" value="true" />
					<button type="submit" class="woocommerce-Button button" value="<?php esc_attr_e( 'Reset password', 'woocommerce' ); ?>"><?php esc_html_e( 'Reset password', 'woocommerce' ); ?></button>
				</p>

				<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>
			</form>
		</div>
	</div>
</div>

<?php do_action( 'woocommerce_after_lost_password_form' ); ?>
