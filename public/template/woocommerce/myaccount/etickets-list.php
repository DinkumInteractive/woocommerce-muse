<?php
defined( 'ABSPATH' ) || exit;

$page_url = wc_get_account_endpoint_url( 'etickets' );

$wc_muse_customer_manager = Wc_Muse_Customers::get_instance();
$wc_muse_order_manager    = Wc_Muse_Orders::get_instance();

$upcoming_tickets = $wc_muse_customer_manager->get_upcoming_tickets( get_current_user_id() );

// Sorting tickets.
// $sorted_tickets = array();

// if ( $upcoming_tickets ) foreach ( $upcoming_tickets as $upcoming_ticket ) {

// if ( ! $upcoming_ticket->visible ) continue;

// $sorted_tickets[$upcoming_ticket->order_number][] = $upcoming_ticket;

// krsort($sorted_tickets);
// }

// Debug.
do_action( 'browser_debug', 'Etickets List', $upcoming_tickets );
?>

<div class="wcBox-white">

	<header class="woocommerce-Address-title title wcTitle-line-wrap">
		<h3 class="wc-title"><?php _e( 'Ticket Details', 'pcms-concerts' ); ?></h3>
	</header>

	<?php if ( $upcoming_tickets ) : ?>

		<table class="shop_table shop_table_responsive my_account_orders">

			<thead>
				<tr>
					<th class="order-id-number"><span class="nobr"><?php _e( 'Order', 'pcms-concerts' ); ?></span></th>
					<th class="order-event-name"><span class="nobr"><?php _e( 'Event Name', 'pcms-concerts' ); ?></span></th>
					<th class="order-event-date"><span class="nobr"><?php _e( 'Date and time', 'pcms-concerts' ); ?></span></th>
					<th class="order-event-place"><span class="nobr"><?php _e( 'Place details', 'pcms-concerts' ); ?></span></th>
					<th class="order-event-actions"><span class="nobr"><?php _e( 'Actions', 'pcms-concerts' ); ?></span></th>
				</tr>
			</thead>

			<tbody>

				<?php foreach ( $upcoming_tickets as $x => $upcoming_ticket ) : ?>
			
					<?php
					if ( ! $upcoming_ticket->visible ) {
						continue;}
					?>

					<tr class="ticket-row" data-ticket_id="<?php echo $upcoming_ticket->id; ?>" data-order_id="<?php echo $upcoming_ticket->order_id; ?>" data-order_item_id="<?php echo $upcoming_ticket->order_item_id; ?>">
						<td>
							<?php $order = $wc_muse_order_manager->get_order_by_muse_order_id( $upcoming_ticket->order_id ); ?>
							<p><a href="<?php echo ( $order ? $order->get_view_order_url() : '#' ); ?>" class="order-detail-link"><?php echo ( $upcoming_ticket->order_number ? $upcoming_ticket->order_number : 'N/A' ); ?></a></p>
						</td>
						<td>
							<p><?php echo $upcoming_ticket->print_event_name; ?></p>
						</td>
						<td>
							<p>
								<?php echo Wc_Muse_Account_Function::get_formatted_date( $upcoming_ticket->start_date ); ?>
							</p>
						</td>
						<td>
							<p><?php echo sprintf( '%s, %s %s', $upcoming_ticket->section_name, $upcoming_ticket->row, $upcoming_ticket->seat ); ?></p>
						</td>
						<td>
							<?php foreach ( $upcoming_ticket as $field => $value ) : ?>
								<input type="hidden" class="ticket-<?php echo $field; ?>" value="<?php echo $value; ?>">
							<?php endforeach; ?>
							<p>
								<a href="<?php echo sprintf( '%s?action=%s&id=%s', $page_url, 'view', $upcoming_ticket->id ); ?>" class="ticket-row-action-view" style="position: relative;"><?php _e( 'View', 'pcms-concerts' ); ?></a>
								&nbsp;
								<a target="_BLANK" href="<?php echo sprintf( '%s?action=%s&id=%s', $page_url, 'print', $upcoming_ticket->id ); ?>" class="ticket-row-action-print" style="position: relative;"><?php _e( 'Print', 'pcms-concerts' ); ?></a>
								&nbsp;
								<a href="<?php echo sprintf( '%s?action=%s&id=%s', $page_url, 'cancel', $upcoming_ticket->id ); ?>" class="ticket-row-action-cancel" style="position: relative;"><?php _e( 'Cancel', 'pcms-concerts' ); ?></a>
							</p>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

	<?php else : ?>

		<tr>
			<td colspan="5"><p><?php _e( 'No tickets to show', 'pcms2018' ); ?></p></td>
		</tr>
	<?php endif; ?>
</div>


<div id="eticket-modal" class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?php _e( 'Cancel Ticket', 'pcms-concerts' ); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<span class="warning"><?php _e( 'This action CANNOT be undone. This will automatically cancel your order.' ); ?></span>
			<div class="modal-body">
				<div class="eticket-modal-ajax-response"></div>
				<table>
					<thead>
						<tr>
							<th class="order-id-number"><span class="nobr"><?php esc_html_e( 'Order', 'pcms-concerts' ); ?></span></th>
							<th class="order-event-name"><span class="nobr"><?php esc_html_e( 'Event Name', 'pcms-concerts' ); ?></span></th>
							<th class="order-event-date"><span class="nobr"><?php esc_html_e( 'Date and time', 'pcms-concerts' ); ?></span></th>
							<th class="order-event-place"><span class="nobr"><?php esc_html_e( 'Place details', 'pcms-concerts' ); ?></span></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
				<p class="text-cancel-info text-cancel-info-donate">
					<b><?php esc_html_e( 'Donate', 'pcms-concerts' ); ?></b>
					<br>
					<?php esc_html_e( 'This action will convert the value of your tickets into a tax-deductible donation to PCMS. Your tickets will no longer be valid, and we will send you a receipt for tax purposes.', 'pcms-concerts' ); ?>
				</p>
				<p class="text-cancel-info text-cancel-info-account_credit">
					<b><?php esc_html_e( 'Account Credit', 'pcms-concerts' ); ?></b>
					<br>
					<?php esc_html_e( 'This action will convert the value of your tickets into credit, which can be applied to a future concert. Please be aware this credit will expire at the end of the season, as we cannot carry credit across seasons.', 'pcms-concerts' ); ?>
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn--blueborder btn eticket-modal-actions" data-action="donate"><?php _e( 'Donate', 'pcms-concerts' ); ?></button>
				<button type="button" class="btn--blueborder btn eticket-modal-actions" data-action="account_credit"><?php _e( 'Account Credit', 'pcms-concerts' ); ?></button>
				<button type="button" class="btn--redborder btn" data-dismiss="modal"><?php _e( 'Close', 'pcms-concerts' ); ?></button>
			</div>
		</div>
	</div>
</div>


<div id="eticket-modal-confirmation" class="modal" tabindex="-2" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?php _e( 'Cancel Confirmation', 'pcms-concerts' ); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<span class="warning"><?php _e( 'Are you sure you want to continue?' ); ?></span>
			<div class="modal-body">
				<p>
					<?php
					echo sprintf(
						'%s <b>%s</b> %s',
						__( 'You are about to', 'pcms-concerts' ),
						__( 'cancel', 'pcms-concerts' ),
						__( 'the ticket. This action cannot be undone!', 'pcms-concerts' )
					)
					?>
				</p>
				<table>
					<thead>
						<tr>
							<th class="order-id-number"><span class="nobr"><?php esc_html_e( 'Order', 'pcms-concerts' ); ?></span></th>
							<th class="order-event-name"><span class="nobr"><?php esc_html_e( 'Event Name', 'pcms-concerts' ); ?></span></th>
							<th class="order-event-date"><span class="nobr"><?php esc_html_e( 'Date and time', 'pcms-concerts' ); ?></span></th>
							<th class="order-event-place"><span class="nobr"><?php esc_html_e( 'Place details', 'pcms-concerts' ); ?></span></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
				<p class="text-cancel-info text-cancel-info-donate">
					<b><?php esc_html_e( 'Donate', 'pcms-concerts' ); ?></b>
					<br>
					<?php esc_html_e( 'This action will convert the value of your tickets into a tax-deductible donation to PCMS. Your tickets will no longer be valid, and we will send you a receipt for tax purposes.', 'pcms-concerts' ); ?>
				</p>
				<p class="text-cancel-info text-cancel-info-account_credit">
					<b><?php esc_html_e( 'Account Credit', 'pcms-concerts' ); ?></b>
					<br>
					<?php esc_html_e( 'This action will convert the value of your tickets into credit, which can be applied to a future concert. Please be aware this credit will expire at the end of the season, as we cannot carry credit across seasons.', 'pcms-concerts' ); ?>
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn--blueborder btn eticket-modal-confirmation-continue">
					<span class="text-cancel-action text-cancel-action-donate"><?php esc_html_e( 'Donate', 'pcms-concerts' ); ?></span>
					<span class="text-cancel-action text-cancel-action-account_credit"><?php esc_html_e( 'Account Credit', 'pcms-concerts' ); ?></span>
				</button>
				<button type="button" class="btn--redborder btn" data-dismiss="modal"><?php _e( 'Close', 'pcms-concerts' ); ?></button>
			</div>
		</div>
	</div>
</div>


<div id="eticket-modal-notification" class="modal" tabindex="-3" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn--redborder btn" data-dismiss="modal"><?php _e( 'Close', 'pcms-concerts' ); ?></button>
			</div>
		</div>
		</div>
	</div>
</div>

