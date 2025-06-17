<?php if ( is_user_logged_in() ): ?>

	<?php $profile_info = wp_get_current_user(); ?>

	<div class="mb-4 woo-account-header">

		<div class="row">
			<div class="col-lg-7 acc-holderwrap">
				<img class="account-image" src="<?php echo get_avatar_url(get_current_user_id()) ?>" alt="<?php echo $profile_info->display_name ?>">
				<div class="account-info-holder">
					<p class="wooac-user">
						<?php $first_name = get_user_meta( $profile_info->ID, 'first_name', true ); ?>
						<?php $display_name = $first_name ? $first_name : $profile_info->user_email; ?>
						<?php printf( __( 'Welcome back,  %1$s !', 'woocommerce' ), '<span>' . esc_html( $display_name ) . '</span>' ); ?>
					</p>
					<p class="wooac-user--act">
						<a href="<?php echo wc_customer_edit_account_url(); ?>"><?php _e( 'Edit Profile', 'pcms-concerts' ); ?></a>
					</p>
				</div>
			</div>
			<div class="col-lg-5">
				<div class="account-action-holder float-right">
					<a class="btn-calendar btn-calendar--black btn--draw-borde" href="#" data-toggle="modal" data-target="#exampleModal">
						<span class="fas fa-calendar-alt"></span><span><?php _e( 'Calendar', 'pcms-concerts' ) ?></span>
					</a>
					<a href="<?php echo esc_url( wc_logout_url( wc_get_page_permalink( 'myaccount' ) ) ); ?>" class="btn-calendar btn-logoutz">
					<span class="fas fa-sign-out-alt"></span> <span><?php _e( 'Log out', 'woocommerce' ); ?></span>
					</a>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="space10"></div>
			<div class="col-md-12">
				<?php do_action( 'woo_account_menu' ); ?>
			</div>
		</div>

	</div>

<?php else: ?>

  	<div class="space45"></div>

<?php endif; ?>