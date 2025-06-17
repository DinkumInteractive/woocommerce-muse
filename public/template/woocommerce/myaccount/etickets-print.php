<?php
defined( 'ABSPATH' ) || exit;

if ( ! isset( $_GET['id'] ) ) {
	return false;
}

$wc_muse_customer_manager = Wc_Muse_Customers::get_instance();

$ticket_id      = sanitize_text_field( $_GET['id'] );
$ticket_details = $wc_muse_customer_manager->get_ticket_details( get_current_user_id(), $ticket_id );
$order          = wc_get_order( $ticket_details->order_number );
$timestamp      = strtotime( $ticket_details->start_date );
$event          = get_post( $ticket_details->web_id );
// $event = get_post( 68558 );
$venue_id = get_post_meta( $event->ID, 'event_venues', true );
$venue    = get_post( $venue_id );

$event_name   = $ticket_details->print_event_name;
$order_number = $ticket_details->order_number;
$event_date   = Wc_Muse_Account_Function::get_formatted_date( $ticket_details->start_date );
$venue_name   = ( $venue->post_title ? $ticket_details->print_venue_name : '-' );
$section_name = ( $ticket_details->section_name ? $ticket_details->section_name : '-' );
$section_row  = ( $ticket_details->row ? $ticket_details->row : '-' );
$section_seat = ( $ticket_details->seat ? $ticket_details->seat : '-' );
$qr_code      = $ticket_details->qr_image_url;

$eticket_details = Wc_Muse_Account_Eticket_Detail::get_instance();
$eticket_details->set_data( $ticket_details->get_data() );
?>
<div class="container">
	<div class="ticketPrint row align-items-center">
		<div class="vticlogo col-md">
			<img alt="" src="<?php echo get_template_directory_uri(); ?>/assets/img/logo-alt.jpg">
		</div>
		<div class="ticPrint-headblock col-md-8">
			<span>Admit One <br>Print and bring this ticket with you to the event</span>
		</div>
	</div>

	<div class="ticScreen ticScreen-print d-print-block">
		<div class="row">
			<div class="col-md-2 black-eticket">
				<p>e-ticket</p>
			</div>
			<div class="col-md ticScreen-desc d-print-md">
				<div class="row">
					<div class="col-md-8">
						<h3 class="ticScreen__name"><?php echo $event_name; ?></h3>
						<span class="ticScreen__orderNumb">Order #<?php echo $order_number; ?></span>
						<div class="space10"></div>
						<ul class="ticScreen__schedules">
							<li class="ticScreen__schedule">
								<span class="ticScreen__sduleIcon fas fa-calendar-alt"></span>
								<p><?php echo $event_date; ?></p>
							</li>
							<li class="ticScreen__schedule">
								<span class="ticScreen__sduleIcon fas fa-map-marker-alt"></span>
								<p><?php echo $venue_name; ?></p>
							</li>
							<li class="ticScreen__schedule">
								<span class="ticScreen__sduleIcon fas fa-couch"></span>
								<div class="row">
									<div class="col-lg-6">
										<p>
											Section<span class="ticScreen__textblue"><?php echo $section_name; ?></span>
										</p>
									</div>
									<div class="col-lg-3">
										<p>
											Row <span class="ticScreen__textblue"><?php echo $section_row; ?></span>
										</p>
									</div>
									<div class="col-lg-3">
										<p>
											Seat <span class="ticScreen__textblue"><?php echo $section_seat; ?></span>
										</p>
									</div>
								</div>
							</li>
						</ul>
					</div>
					<div class="col-md-4">
						<div class="ticScreen__qr">
							<img alt="qrcode" src="<?php echo $qr_code; ?>">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php $eticket_details->get_template(); ?>
</div>
<div class="space60"></div>

