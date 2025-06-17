<?php 
defined( 'ABSPATH' ) || exit;

//  Helpers
$wc_muse_customer_manager = Wc_Muse_Customers::get_instance();
$upcoming_tickets = $wc_muse_customer_manager->get_upcoming_tickets( get_current_user_id() );
$page_url_eticket = wc_get_account_endpoint_url( 'etickets' );
$page_url_change_password = wc_get_account_endpoint_url( 'edit-account' ) . '#pcms-change-password';
$page_url_edit_address = wc_get_account_endpoint_url( 'edit-address' );
$page_url_payment_methods = wc_get_account_endpoint_url( 'payment-methods' );
$page_url_contact_support = get_field( 'eticket_contact_support_link', 'option' );


//	Sorting tickets.
$sorted_tickets = array();

if ( $upcoming_tickets ) foreach ( $upcoming_tickets as $upcoming_ticket ) {

	if ( ! $upcoming_ticket->visible ) continue;
	
	$sorted_tickets[$upcoming_ticket->start_date][] = $upcoming_ticket;

	ksort($sorted_tickets);

	$sorted_tickets = array_slice( $sorted_tickets, 0, 2 );
}


//	Debug
do_action( 'browser_debug', 'Upcoming Ticket', $upcoming_tickets );
do_action( 'browser_debug', 'Upcoming Sorted Ticket', $sorted_tickets );

 ?>


<div class="row">
	<div class="col-lg-6">
		<h2 class="titlez-md">Upcoming Tickets</h2>

		<?php if ( $sorted_tickets ): ?>

			<?php foreach ( $sorted_tickets as $date => $tickets ): ?>
				<?php $date = Wc_Muse_Account_Function::get_formatted_date( $date ); ?>

				<span class="ticboxz__date"><?php echo $date; ?></span>

				<?php foreach ( $tickets as $ticket ): ?>
					<?php $print_event_name = $ticket->print_event_name; ?>
					<?php $print_venue_name = $ticket->print_venue_name; ?>
					<?php $section_name = $ticket->section_name; ?>
					<?php $row = $ticket->row; ?>
					<?php $seat = $ticket->seat; ?>
					<?php $detail_link = sprintf( '%s?action=%s&id=%s', $page_url_eticket, 'view', $ticket->id ); ?>

					<div class="ticboxz">
						<div class="row">
							<div class="col-lg-8 ticboxz_content">
								<span class="ticobxz__name"><?php echo $print_event_name ?></span>
								<span><?php echo $print_venue_name ?></span>
								<span class="ticobxz__seat"><?php echo sprintf( 'Seat number : %s, Row %s, Seat %s ', $section_name, $row, $seat ) ?></span>
							</div>
							<div class="col-lg-4 ticboxz__act">
								<a href="<?php echo $detail_link ?>">Detail</a>		
							</div>
						</div>
					</div> <!-- /ticboxz -->
				<?php endforeach; ?>
				<div class="space15"></div>
			<?php endforeach; ?>

			<div class="btnwrap-center">
				<a class="vticbtn vticbtn--blue" href="<?php echo $page_url_eticket; ?>">VIEW ALL TICKETS</a>
			</div>

		<?php else: ?>

			<p><?php _e( 'No ticket to show', 'pcms2018' ) ?></p>
			
		<?php endif; ?>
	</div>

	<div class="col-lg-6 pl-lg-4">
		<h2 class="titlez-md">Quick Access</h2>

		<a href="<?php echo $page_url_change_password; ?>">
			<div class="qacesboxz">
				<div class="row">
					<div class="col-md-2">
						<span class="qacesboxz__icon">
							<img src="<?php echo WC_MUSE_PUBLIC_IMG_URI; ?>/icon-qacess.png" alt="">
						</span>
					</div>
					<div class="col-md-10">
						<span class="qacesboxz__head"><?php _e( 'Change Password', 'pcms2018' ) ?></span>
						<span class="qacesboxz__text"><?php _e( 'Click here to change your account password.', 'pcms2018' ) ?></span>
					</div>
				</div>
			</div> <!-- qacesboxz -->
		</a>

		<a href="<?php echo $page_url_edit_address; ?>">
			<div class="qacesboxz">
				<div class="row">
					<div class="col-md-2">
						<span class="qacesboxz__icon">
							<img src="<?php echo WC_MUSE_PUBLIC_IMG_URI; ?>/icon-qacess2.png" alt="">
						</span>
					</div>
					<div class="col-md-10">
						<span class="qacesboxz__head"><?php _e( 'Update Contact Information', 'pcms2018' ) ?></span>
						<span class="qacesboxz__text"><?php _e( 'View or edit your contact informations.', 'pcms2018' ) ?></span>
					</div>
				</div>
			</div> <!-- qacesboxz -->
		</a>

		<a href="<?php echo $page_url_payment_methods; ?>">
			<div class="qacesboxz">
				<div class="row">
					<div class="col-md-2">
						<span class="qacesboxz__icon">
							<img src="<?php echo WC_MUSE_PUBLIC_IMG_URI; ?>/icon-qacess3.png" alt="">
						</span>
					</div>
					<div class="col-md-10">
						<span class="qacesboxz__head"><?php _e( 'Update Payment Method', 'pcms2018' ) ?></span>
						<span class="qacesboxz__text"><?php _e( 'View and edit your payment methods.', 'pcms2018' ) ?></span>
					</div>
				</div>
			</div> <!-- qacesboxz -->
		</a>

		<a href="<?php echo $page_url_contact_support; ?>">
			<div class="qacesboxz">
				<div class="row">
					<div class="col-md-2">
						<span class="qacesboxz__icon">
							<img src="<?php echo WC_MUSE_PUBLIC_IMG_URI; ?>/icon-qacess4.png" alt="">
						</span>
					</div>
					<div class="col-md-10">
						<span class="qacesboxz__head"><?php _e( 'Contact Support', 'pcms2018' ) ?></span>
						<span class="qacesboxz__text"><?php _e( 'Need help? Get in touch with our box office.', 'pcms2018' ) ?></span>
					</div>
				</div>
			</div> <!-- qacesboxz -->
		</a>

	</div>
</div>

