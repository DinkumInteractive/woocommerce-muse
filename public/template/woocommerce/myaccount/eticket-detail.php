<?php

$eticket_details = Wc_Muse_Account_Eticket_Detail::get_instance();

// Debug.
do_action(
	'browser_debug',
	'etickets-detail.php',
	array(
		'$eticket_details' => $eticket_details,
	)
);

$venue_name                 = $eticket_details->get_data( 'print_venue_name' );
$venue_address              = $eticket_details->get_data( 'venue_address' );
$venue_phone_number         = $eticket_details->get_data( 'venue_phone' );
$venue_parking_informations = $eticket_details->get_data( 'parking_options' );
$venue_extra_information    = $eticket_details->get_data( 'venue_extra_information' );
$venue_location_image_url   = $eticket_details->get_data( 'venue_map_embed' );
$venue_upcoming_events      = $eticket_details->get_data( 'venue_upcoming_events' );

?>
<div class="ticPrint-loc row">
	<div class="col-md-6">

		<h2 class="ticPrint-title">
			Location
		</h2>
		<p><?php echo $venue_name; ?></p>
		<p><?php echo $venue_address; ?></p>
		<p><?php echo $venue_phone_number; ?></p>
		<div class="space15"></div>
		<h2 class="ticPrint-title">
			Parking Information
		</h2>
		<?php
		if ( $venue_parking_informations ) {
			foreach ( $venue_parking_informations as $venue_parking_information ) :
				?>
			<h4><?php echo $venue_parking_information['parking_opt_name']; ?></h4>
							<?php echo $venue_parking_information['parking_opt_address']; ?>
					<?php
		endforeach;
		};
		?>

		<div class="ticPrint-info">
			<h2 class="ticPrint-title">
				More Information:
			</h2>
			<?php echo $venue_extra_information; ?>
		</div>
		<div class="ticPrint-share">
			<span class="ticPrint-share__text">SHARE & CONNECT:</span> <a class="fab fa-facebook-square" href="#"></a> <a class="fab fa-twitter-square" href="#"></a> <a class="fab fa-instagram" href="#"></a>
		</div>
	</div>
	<div class="col-md-6">
		<div class="ticPrint-map">
			<?php if ( $venue_location_image_url ) : ?>
			<!-- <iframe allowfullscreen frameborder="0" height="300" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3058.831580062877!2d-75.1812971848377!3d39.945156779422824!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c6e85830ff771b%3A0x1c2d6e6dda3bd3ce!2sDinkum%20Interactive!5e0!3m2!1sen!2sid!4v1570766299164!5m2!1sen!2sid" style="border:0;" width="100%"></iframe> -->
				<img height="300" src="<?php echo $venue_location_image_url; ?>" alt="venue-location" style="border:0;" width="100%">
			<?php endif; ?>
		</div>

		<?php if ( $venue_upcoming_events ) : ?>
			<h2 class="ticPrint-title">
				UPCOMING CONCERTS
			</h2>

			<div class="ticPrint-sidevents">

				<?php foreach ( $venue_upcoming_events as $venue_upcoming_event ) : ?>
					<div class="ticPrint-sidevent">
						<a href="<?php echo $venue_upcoming_event['permalink']; ?>">
							<h3 class="ticPrint-sidevent__title"><?php echo $venue_upcoming_event['name']; ?></h3>
							<span><?php echo $venue_upcoming_event['date']; ?></span>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif ?>
	</div>
</div>
