<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'woocommerce_before_customer_login_form' ); ?>

<h1 class="title-heading"><?php the_title(); ?></h1>
<div class="space30"></div>
<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>

<div class="u-columns col2-set wcBox-white" id="customer_login">

	<div class="tab-buttonwrap fullrow row">
		<a class="col-md-6 text-center wc-tab-button tab-button tab-button-login" href="#login"><?php _e( 'Login', 'pcms-concerts' ); ?></a>
		<a class="col-md-6 text-center wc-tab-button tab-button tab-button-register" href="#register"><?php _e( 'Register', 'pcms-concerts' ); ?></a>
	</div>

	<div class="fullrow row row-tab-content row-login">

		<div class="col-md-12">
			<form class="woocommerce-form woocommerce-form-login login woocommerce-form--custom" method="post">

				<?php do_action( 'woocommerce_login_form_start' ); ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="username"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text wooform-control" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
				</p>
				<div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input class="woocommerce-Input woocommerce-Input--text input-text wooform-control" type="password" name="password" id="password" autocomplete="current-password" />
					<p class="woocommerce-LostPassword lost_password">
						<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
					</p>
				</div>
				<?php do_action( 'woocommerce_login_form' ); ?>

				<p>
					<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
						<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
					</label>
				</p>
			
				<div class="form-row__btn">
					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
					<button type="submit" class="woocommerce-button button btnwoo-blue btnwoo-blue--full  woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
				</div>
			
				<div class="login-line">
					<span><?php _e( 'OR LOGIN WITH', 'pcms-concerts' ); ?></span>
				</div>
				<?php do_action( 'woocommerce_login_form_end' ); ?>
				<div class="login-noaccount">
					<p><?php echo sprintf( "Don’t have an account yet? <a href='%s'> Create account now!</a>", '#register' ); ?></p>
				</div>
			</form>
		</div>

	</div>

	<div class="fullrow row row-tab-content row-register">

		<!-- <h2><?php //esc_html_e( 'Register', 'woocommerce' ); ?></h2> -->

		<div class="col-md-12">
			<form method="post" class="woocommerce-form woocommerce-form-register register woocommerce-form--custom" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

				<?php do_action( 'woocommerce_register_form_start' ); ?>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
						<input type="text" class="woocommerce-Input woocommerce-Input--text input-text form-control" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
					</p>

				<?php endif; ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="email" class="woocommerce-Input woocommerce-Input--text input-text form-control" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
				</p>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
						<input type="password" class="woocommerce-Input woocommerce-Input--text input-text form-control" name="password" id="reg_password" autocomplete="new-password" />
					</p>

				<?php else : ?>

					<p><?php esc_html_e( 'A password will be sent to your email address.', 'woocommerce' ); ?></p>

				<?php endif; ?>

				<?php do_action( 'woocommerce_register_form' ); ?>

				<p class="woocommerce-FormRow form-row">
					<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
					<button type="submit" class="woocommerce-Button button btnwoo-blue btnwoo-blue--full" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
				</p>
				<?php do_action( 'woocommerce_register_form_end' ); ?>

				<div class="login-line">
					<span>OR REGISTER WITH</span>
				</div>
				<?php do_action( 'woocommerce_login_form_end' ); ?>

			</form>
		</div>

	</div>

</div>
<?php else: ?>
	<p><?php esc_html_e( 'Feature under construction. Check back soon!', 'wc-muse' ); ?></p>
<?php endif; ?>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
