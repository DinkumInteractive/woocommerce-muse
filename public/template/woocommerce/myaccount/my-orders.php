<?php
/**
 * My Orders - Deprecated
 *
 * @deprecated 2.6.0 this template file is no longer used. My Account shortcode uses orders.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$my_orders_columns = apply_filters(
	'woocommerce_my_account_my_orders_columns',
	array(
		// 'order-number'  => __( 'Order', 'woocommerce' ),
		'order-date'   => __( 'Buy Date', 'woocommerce' ),
		'concert-name' => __( 'Concert Name', 'woocommerce' ),
		'concert-date' => __( 'Concert Date', 'woocommerce' ),
		'order-status' => __( 'Status', 'woocommerce' ),
		'order-total'  => __( 'Total', 'woocommerce' ),
	// 'order-actions' => '&nbsp;',
	)
);

$customer_orders = get_posts(
	apply_filters(
		'woocommerce_my_account_my_orders_query',
		array(
			'numberposts' => $order_count,
			'meta_key'    => '_customer_user',
			'meta_value'  => get_current_user_id(),
			'post_type'   => wc_get_order_types( 'view-orders' ),
			'post_status' => array_keys( wc_get_order_statuses() ),
		)
	)
);

if ( $customer_orders ) : ?>
<div class="wcBox-white">
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
			foreach ( $customer_orders as $customer_order ) :
				$order      = wc_get_order( $customer_order );
				$item_count = $order->get_item_count();
				?>
				<tr class="order">
					<?php foreach ( $my_orders_columns as $column_id => $column_name ) : ?>
						<td class="<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">

							<a class="order-detail-link" href="<?php echo esc_url( $order->get_view_order_url() ); ?>"></a>

							<?php if ( has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) : ?>
								<?php do_action( 'woocommerce_my_account_my_orders_column_' . $column_id, $order ); ?>
							<?php else : ?>
								<?php
								switch ( $column_id ) :
									?>
									<?php
									default:
												  echo 'N/A';
										break;
									?>

									<?php
									case 'order-number':
										?>
										<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
											<?php echo _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number(); ?>
										</a>
									<?php break; ?>

									<?php
									case 'order-date':
										?>
										<time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time>
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
										<?php echo $order->get_formatted_order_total(); ?>
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
</div>
<?php endif; ?>
