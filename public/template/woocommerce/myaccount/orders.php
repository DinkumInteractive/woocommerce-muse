<?php
/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/orders.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_orders', $has_orders ); ?>

<?php
$my_orders_columns = apply_filters(
	'woocommerce_my_account_my_orders_columns',
	array(
		'order-number' => __( 'Order', 'woocommerce' ),
		'order-date'   => __( 'Buy Date', 'woocommerce' ),
		// 'concert-name'    => __( 'Concert Name', 'woocommerce' ),
		// 'concert-date'    => __( 'Concert Date', 'woocommerce' ),
		'order-status' => __( 'Status', 'woocommerce' ),
		'order-total'  => __( 'Total', 'woocommerce' ),
	// 'order-actions' => '&nbsp;',
	)
);

// $customer_orders = get_posts( apply_filters( 'woocommerce_my_account_my_orders_query', array(
// 'numberposts' => 20,
// 'meta_key'    => '_customer_user',
// 'meta_value'  => get_current_user_id(),
// 'post_type'   => wc_get_order_types( 'view-orders' ),
// 'post_status' => array_keys( wc_get_order_statuses() ),
// ) ) );
?>

<?php if ( $has_orders ) : ?>
<div class="wcBox-white">
		<div style="text-align: right;">
			<a class="woocommerce-Button button" style="color: #ffffff;" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
				<?php esc_html_e( 'Create Order', 'woocommerce-muse' ); ?>
			</a>
		</div>
		<header class="woocommerce-Address-title title wcTitle-line-wrap">
				<h3 class="wc-title"><?php echo apply_filters( 'woocommerce_my_account_my_orders_title', __( 'Recent orders', 'woocommerce' ) ); ?></h3>
		</header>

		<table class="shop_table shop_table_responsive my_account_orders">

			<thead>
				<tr>
					<?php foreach ( $my_orders_columns as $column_id => $column_name ) : ?>
						<th class="<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
					<?php endforeach; ?>
				</tr>
			</thead>

			<tbody>
				<?php
				foreach ( $customer_orders->orders as $customer_order ) :
					$order      = wc_get_order( $customer_order );
					$item_count = $order->get_item_count();
					?>
					<tr class="order">
						<?php foreach ( $my_orders_columns as $column_id => $column_name ) : ?>
							<td class="<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">

								<a class="order-detail-link" href="<?php echo esc_url( $order->get_view_order_url() ); ?>"></a>

								<?php if ( has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) : ?>
									<p><?php echo ( $column_id === 'order-status' ? Wc_Muse_Account_Function::get_woo_order_status_icon( $order ) : '' ); ?></p>
								<?php else : ?>
									<?php
									switch ( $column_id ) :
										default:
											echo 'N/A';
											break;
										?>

										<?php
										case 'order-number':
											?>
											<p><?php echo _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number(); ?></p>
										<?php break; ?>

										<?php
										case 'order-date':
											?>
											<p><time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time></p>
										<?php break; ?>

										<?php
										case 'concert-name':
											?>
											<?php $names = Wc_Muse_Account_Function::get_order_concert_names( $order ); ?>
											<?php
											if ( $names ) :
												foreach ( $names as $name ) :
													?>
												<p class="order-list-concert-name">- <?php echo $name; ?></p>
													<?php
											endforeach;
endif;
											?>
										<?php break; ?>

										<?php
										case 'concert-date':
											?>
											<?php $dates = Wc_Muse_Account_Function::get_order_concert_dates( $order ); ?>
											<?php
											if ( $dates ) :
												foreach ( $dates as $date ) :
													?>
												<p class="order-list-concert-date"><?php echo $date; ?></p>
													<?php
											endforeach;
endif;
											?>
										<?php break; ?>

										<?php
										case 'order-status':
											?>
											<span class="status-<?php echo $order->get_status(); ?>">
												<?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
											</span>
										<?php break; ?>

										<?php
										case 'order-total':
											?>
											<?php // printf( _n( '%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce' ), $order->get_formatted_order_total(), $item_count ); ?>
											<p><?php echo $order->get_formatted_order_total(); ?></p>
										<?php break; ?>

										<?php
										case 'order-actions':
											?>
											<?php $actions = wc_get_account_orders_actions( $order ); ?>
											<?php if ( ! empty( $actions ) ) : ?>
												<?php foreach ( $actions as $key => $action ) : ?>
													<?php echo '<a href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>'; ?>
												<?php endforeach; ?>
											<?php endif; ?>
										<?php break; ?>

									<?php endswitch; ?>
								<?php endif; ?>
							</td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

		<?php if ( isset( $customer_orders->max_num_pages ) && 1 < $customer_orders->max_num_pages ) : ?>
			<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
				<?php if ( 1 !== $current_page ) : ?>
					<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php _e( 'Previous', 'woocommerce' ); ?></a>
				<?php endif; ?>

				<?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
					<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php _e( 'Next', 'woocommerce' ); ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	<?php else : ?>
		<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
			<a class="woocommerce-Button button" style="color: #ffffff;" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
				<?php esc_html_e( 'Create Order', 'woocommerce-muse' ); ?>
			</a>
			<?php _e( 'No order has been made yet.', 'woocommerce' ); ?>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
</div>
