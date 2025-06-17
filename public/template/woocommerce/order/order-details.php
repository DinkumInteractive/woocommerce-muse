<?php
/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! $order = wc_get_order( $order_id ) ) {
	return;
}

$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads             = $order->get_downloadable_items();
$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();

if ( $show_downloads ) {
	wc_get_template(
		'order/order-downloads.php',
		array(
			'downloads'  => $downloads,
			'show_title' => true,
		)
	);
}
?>
<section class="woocommerce-order-details">
	<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

	

	<div class="wcBox-white">
		<header class="woocommerce-Address-title title wcTitle-line-wrap">
			<h2 class="woocommerce-order-details__title wc-title"><?php _e( 'Order details', 'woocommerce' ); ?></h2>
		</header>
		<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

			<thead>
				<tr>
					<th class="woocommerce-table__product-name product-name"><?php _e( 'Product', 'woocommerce' ); ?></th>
					<th class="woocommerce-table__product-table product-total"><?php _e( 'Total', 'woocommerce' ); ?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				do_action( 'woocommerce_order_details_before_order_table_items', $order );

				foreach ( $order_items as $item_id => $item ) {
					$product = $item->get_product();

					wc_get_template(
						'order/order-details-item.php',
						array(
							'order'              => $order,
							'item_id'            => $item_id,
							'item'               => $item,
							'show_purchase_note' => $show_purchase_note,
							'purchase_note'      => $product ? $product->get_purchase_note() : '',
							'product'            => $product,
						)
					);
				}

				do_action( 'woocommerce_order_details_after_order_table_items', $order );
				?>
			</tbody>

			<tfoot>
				<?php
				foreach ( $order->get_order_item_totals() as $key => $total ) {
					?>
						<tr>
							<th scope="row"><?php echo $total['label']; ?></th>
							<td><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : $total['value']; ?></td>
						</tr>
						<?php
				}
				?>
				<?php if ( $order->get_customer_note() ) : ?>
					<tr>
						<th><?php _e( 'Note:', 'woocommerce' ); ?></th>
						<td><?php echo wptexturize( $order->get_customer_note() ); ?></td>
					</tr>
				<?php endif; ?>
			</tfoot>
		</table>
	</div>

	<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
</section>


<?php if ( class_exists( 'Wc_Muse_Orders' ) ) : ?>
	<?php $muse_order_id = get_post_meta( $order->get_id(), '_wc_muse_order_id', true ); ?>
	<?php // https://www.pcmsconcerts.org/wp-admin/post.php?post=68929&action=edit ?>
	<?php // $muse_order_id = '665b5d88-2533-4545-868c-1a63eef5cbb6'; ?>
	<?php $wc_muse_order = Wc_Muse_Orders::get_instance(); ?>

	<print_style id="print_style">
		h1 {
			font-size: 80px;
		}
	</print_style>

	<div class="wcBox-white">
		<header class="woocommerce-Address-title title wcTitle-line-wrap">
			<h3 class="wc-title"><?php _e( 'Ticket Details', 'pcms-concerts' ); ?></h3>
		</header>
		<table class="shop_table shop_table_responsive my_account_orders">

			<?php if ( $wc_muse_order->is_valid_id( $muse_order_id ) ) : ?>

				<?php $muse_order = $wc_muse_order->get_muse_order( $muse_order_id ); ?>

				<?php do_action( 'browser_debug', 'order-details.php', array( '$muse_order' => $muse_order ) ); ?>

				<?php if ( $muse_order && isset( $muse_order->order_items ) && 'completed' === $muse_order->state ) : ?>

					<thead>
						<tr>
							<th class="order-event-name"><span class="nobr"><?php _e( 'Event Name', 'pcms-concerts' ); ?></span></th>
							<th class="order-event-date"><span class="nobr"><?php _e( 'Date and time', 'pcms-concerts' ); ?></span></th>
							<th class="order-event-place"><span class="nobr"><?php _e( 'Place details', 'pcms-concerts' ); ?></span></th>
							<!-- <th class="order-event-actions"><span class="nobr"><?php _e( 'Actions', 'pcms-concerts' ); ?></span></th> -->
						</tr>
					</thead>

					<tbody>
						<?php $order_items = $muse_order->order_items; ?>
						<?php foreach ( $order_items as $order_item ) : ?>
							<tr class="order ticket-row">
								<td>
									<p><?php echo $order_item->event->name; ?></p>
								</td>
								<td>
									<?php $timestamp = strtotime( $order_item->event->start_datetime ); ?>
									<p><?php echo date( 'l jS \o\f F Y h:i A', $timestamp ); ?></p>
								</td>
								<td>
									<?php if ( $order_item->order_item_places ) : ?>
										<ul>
											<?php foreach ( $order_item->order_item_places as $order_item_place ) : ?>
												<?php $url_eticket_detail = sprintf( '%s?action=%s&id=%s', wc_get_account_endpoint_url( 'etickets' ), 'view', $order_item_place->id ); ?>
												<li><a href="<?php echo $url_eticket_detail; ?>" target="_blank"><?php echo $order_item_place->place_details_label; ?></a></li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				<?php elseif ( $muse_order && isset( $muse_order->order_items ) && 'cancelled' === $muse_order->state ) : ?>

					<tbody>
						<tr>
							<td>
								<p class="text-center"><?php _e( 'Order cancelled.', 'pcms2018' ); ?></p>
							</td>
						</tr>
					</tbody>
				<?php else : ?>

					<tbody>
						<tr>
							<td>
								<p class="text-center"><?php _e( 'Tickets will be displayed when they are ready.', 'pcms2018' ); ?></p>
							</td>
						</tr>
					</tbody>
				<?php endif; ?>
			<?php else : ?>
			<tbody>
				<tr>
					<td>
						<p class="text-center"><?php _e( 'No tickets to display for this order.', 'pcms2018' ); ?></p>
					</td>
				</tr>
			</tbody>
			<?php endif; ?>
		</table>
	</div>
<?php endif; ?>


<?php
if ( $show_customer_details ) {
	wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
}?>
