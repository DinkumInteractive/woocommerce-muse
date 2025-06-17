<?php 
$post_id = $_GET['post']; 
$item_slug = get_post_meta( $post_id, 'item_slug', true );
$seat_slug = get_post_meta( $post_id, 'seat_slug', true );
$sub_item_slug = get_post_meta( $post_id, 'sub_item_slug', true );
$ticket_type = get_post_meta( $post_id, 'ticket_type', true );
$eticket_visibility_date = get_post_meta( $post_id, 'eticket_visibility_date', true );
?>
<div id="general_product_muse" class="panel woocommerce_options_panel" style="display:block">
	
	<div class="options-group">
		<p class="form-field item_slug_field ">
			<label for="item_slug"><?php _e( 'Event Slug (separated by comma on series)', 'wc-muse' ) ?></label>
			<input type="text" name="item_slug" id="item_slug" value="<?php echo $item_slug; ?>">
		</p>
	</div>

	<div class="options-group">
		<p class="form-field sub_item_slug_field ">
			<label for="sub_item_slug"><?php _e( 'Series Slug', 'wc-muse' ) ?></label>
			<input type="text" name="sub_item_slug" id="sub_item_slug" value="<?php echo $sub_item_slug; ?>">
		</p>
	</div>

	<div class="options-group">
		<p class="form-field seat_slug_field ">
			<label for="seat_slug"><?php _e( 'Seat Slug', 'wc-muse' ) ?></label>
			<input type="text" name="seat_slug" id="seat_slug" value="<?php echo $seat_slug; ?>">
		</p>
	</div>

	<div class="options-group">
		<p class="form-field ticket_type_field ">
			<label for="ticket_type"><?php _e( 'Item Type', 'wc-muse' ) ?></label>
			<select name="ticket_type" id="ticket_type">
				<option value="event" <?php selected( 'event', $ticket_type, true ); ?>><?php _e( 'Event', 'wc-muse' ) ?></option>
				<option value="season-pass" <?php selected( 'season-pass', $ticket_type, true ); ?>><?php _e( 'Season Pass', 'wc-muse' ) ?></option>
				<option value="serie" <?php selected( 'serie', $ticket_type, true ); ?>><?php _e( 'Serie', 'wc-muse' ) ?></option>
				<option value="serie-single-event" <?php selected( 'serie-single-event', $ticket_type, true ); ?>><?php _e( 'Serie Single Event', 'wc-muse' ) ?></option>
			</select>
		</p>
	</div>

	<div class="options-group">
		<p class="form-field eticket_visibility_date_field ">
			<label for="eticket_visibility_date"><?php _e( 'Eticket Visibility Date', 'wc-muse' ) ?></label>
			<input type="date" name="eticket_visibility_date" id="eticket_visibility_date" value="<?php echo $eticket_visibility_date; ?>">
		</p>
	</div>

</div>

