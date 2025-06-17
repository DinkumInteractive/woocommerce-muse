<?php
defined( 'ABSPATH' ) || exit;

if ( ! isset( $_GET['id'] ) ) {
	return false;
}

$wc_muse_customer_manager = Wc_Muse_Customers::get_instance();
$wc_muse_order_manager    = Wc_Muse_Orders::get_instance();

// Helper.
$ticket_id      = sanitize_text_field( $_GET['id'] );
$ticket_details = $wc_muse_customer_manager->get_ticket_details( get_current_user_id(), $ticket_id );

if ( $ticket_details ) :


	// Vars.
	$order            = $wc_muse_order_manager->get_order_by_muse_order_id( $ticket_details->order_id );
	$wc_product       = wc_get_product( $ticket_details->web_id );
	$header_logo      = WC_MUSE_PUBLIC_IMG_URI . '/logo-alt.jpg';
	$event_name       = $ticket_details->print_event_name;
	$event_thumbnails = $wc_product->get_gallery_image_ids();
	$event_thumbnail  = $event_thumbnails ? wp_get_attachment_url( $event_thumbnails[0] ) : false;
	$venue_name       = $ticket_details->print_venue_name;
	$timestamp        = strtotime( $ticket_details->start_date );
	$event_date       = Wc_Muse_Account_Function::get_formatted_date( $ticket_details->start_date );
	$section_name     = ( $ticket_details->section_name ? $ticket_details->section_name : '-' );
	$section_row      = ( $ticket_details->row ? $ticket_details->row : '-' );
	$section_seat     = ( $ticket_details->seat ? $ticket_details->seat : '-' );
	$qr_code          = $ticket_details->qr_image_url;
	$order_number     = $ticket_details->order_number;
	$order_link       = ( $order ? $order->get_view_order_url() : null );
	$close_link       = wc_get_account_endpoint_url( 'etickets' );
	$print_link       = sprintf( '%s?action=%s&id=%s', wc_get_account_endpoint_url( 'etickets' ), 'print', $ticket_details->id );

	/*
	  Debug.
	 *
	 */
	do_action(
		'browser_debug',
		'etickets-view.php',
		array(
			'$ticket_details' => $ticket_details,
		)
	);

	$eticket_details = Wc_Muse_Account_Eticket_Detail::get_instance();
	$eticket_details->set_data( $ticket_details->get_data() );
	?>
	<div class="vticket-desktop-view">
		<div class="container">
			<div class="ticketPrint row align-items-center">
				<div class="vticlogo col-md">
					<img alt="" src="<?php echo $header_logo; ?>">
				</div>
				<div class="ticPrint-headblock col-md-8">

					<?php if ( $ticket_details->visible ) : ?>

						<span>Admit One <br>Print and bring this ticket with you to the event</span>

					<?php else : ?>

						<span><?php echo sprintf( 'Ticket details will be available on %s', $ticket_details->available_date ); ?></span>
					<?php endif; ?>
				</div>
			</div>

			<?php
			if ( $ticket_details->visible ) :
					$ticket_thumbnail = empty( $ticket_details->event_featured_image_url ) ? $event_thumbnail : $ticket_details->event_featured_image_url;
				?>
				<div class="ticScreen d-print-none">
					<div class="row">
						<div class="col-lg-4 ticScreen__img" style="background-image: url('<?php echo esc_url( $ticket_thumbnail ); ?>');"></div>
						<div class="col-lg-8 ticScreen-desc">
							<div class="row">
								<div class="col-md-8">
									<h3 class="ticScreen__name">
										<?php echo $event_name; ?>
									</h3><span class="ticScreen__orderNumb">Order #<?php echo esc_html( $order_number ); ?></span>
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
											<p>
												Section<span class="ticScreen__textblue"><?php echo $section_name; ?></span>
											</p>
											<div class="row">
												<div class="col-lg-6">
													<p>
														Row <span class="ticScreen__textblue"><?php echo $section_row; ?></span>
													</p>
												</div>
												<div class="col-lg-6">
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
										<img alt="qrcode" src="<?php echo $qr_code; ?>" onerror="this.style.display='none';">
									</div>
								</div>
							</div>
							<div class="ticScreen--act">
								<?php if ( $order_link ) : ?>
									<a class="vticbtn vticbtn--blue" href="<?php echo $order_link; ?>">see receipt</a> <a class="vticbtn vticbtn--red" href="<?php echo $close_link; ?>">close</a>
								<?php endif; ?>
								<a target="_BLANK" href="<?php echo esc_url( $print_link ); ?>" class="vticbtn"><?php _e( 'Print', 'pcms-concerts' ); ?></a>
							</div>
						</div>
					</div>
				</div>

				<?php $eticket_details->get_template(); ?>
			<?php endif; ?>
		</div>
	</div>
	<div class="space60"></div>
	</div>

	<div class="vticket-mobile-view"> 
		<div class="vticket">
			<div class="vticheader">
				<a href="#">
				<div class="vticlogo"><img alt="" src="<?php echo $header_logo; ?>"></div></a>
			</div>
			<div class="vtichero">
				<figure class="vtichero__img">
					<img alt="" src="<?php echo $header_img; ?>">
				</figure>
				<p class="vtichero__name"><span><strong><?php echo $event_name; ?></strong></span></p>
			</div><!-- /vtichero -->
			<div class="container">
				<div class="row vticschedule-wrap">
					<div class="col-6 vticschedule">
						<h3 class="vticket-titlesm">VENUE</h3>
						<span><?php echo $venue_name; ?></span>
					</div><!--vticschedule-->
					<div class="col-6 vticschedule">
						<h3 class="vticket-titlesm">DATE AND TIME</h3>
						<span><?php echo $event_date; ?></span>
					</div><!--vticschedule-->
				</div>
				<div class="vticseat">
					<h3 class="vticket-titlesm">SEAT NUMBER</h3>
					<div class="space5"></div>
					<span>Section</span> 
					<span class="vticseat-emph"><?php echo $section_name; ?></span>
					<div class="container">
						<div class="row">
							<?php if ( $section_row ) : ?>
								<div class="col vticseat__numb">
									<span>Row</span> <span class="vticseat-emph"><?php echo $section_row; ?></span>
								</div>
							<?php endif; ?>
							<?php if ( $section_seat ) : ?>
								<div class="col vticseat__numb">
									<span>Seat</span> <span class="vticseat-emph"><?php echo $section_seat; ?></span>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div><!-- /vticseat -->
				<div class="vticqr">
					<figure class="vticqr__img">
						<img alt="" src="<?php echo $qr_code; ?>">
					</figure><span>Scan QR Code</span>
				</div>
				<?php if ( $order_link ) : ?>
					<a class="vticbtn vticbtn--blue" href="<?php echo $order_link; ?>">see receipt</a>
				<?php endif; ?>
				<a class="vticbtn vticbtn--red" href="<?php echo $close_link; ?>">close</a>
			</div>
		</div><!-- Vticket -->
	</div>
<?php else : ?>

	<div class="woocommerce-notices-wrapper">
		<ul class="woocommerce-error" role="alert">
			<li><?php _e( 'Ticket details is currently unavailable.', 'woocommerce-muse' ); ?></li>
		</ul>
	</div>
<?php endif; ?>
