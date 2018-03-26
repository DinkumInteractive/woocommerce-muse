<?php 
$post_id = $variation->ID; 
$unique_variable_name ="wc_muse_variation[]";
$item_slug = get_post_meta( $variation->ID, 'item_slug', true );
$seat_slug = get_post_meta( $variation->ID, 'seat_slug', true );
$ticket_type = get_post_meta( $variation->ID, 'ticket_type', true );
?>

<div class="form-meta">

	<p class="muse-variation-title"><strong><?php _e( 'Muse', 'wc-muse' ); ?></strong></p>

	<input type="hidden" name="<?php echo $unique_variable_name; ?>[id]" value="<?php echo $post_id; ?>">

	<div class="options-group">
		<p class="form-field form-row-full">
			<label for="<?php echo $unique_variable_name; ?>[item_slug]"><?php _e( 'Item Slug', 'wc-muse' ) ?></label>
			<input type="text" name="<?php echo $unique_variable_name; ?>[item_slug]" value="<?php echo $item_slug; ?>">
		</p>
	</div>
	
	<div class="options-group">
		<p class="form-field form-row-full">
			<label for="<?php echo $unique_variable_name; ?>[seat_slug]"><?php _e( 'Seat Slug', 'wc-muse' ) ?></label>
			<input type="text" name="<?php echo $unique_variable_name; ?>[seat_slug]" value="<?php echo $seat_slug; ?>">
		</p>
	</div>

	<div class="options-group">
		<p class="form-field form-row-full">
			<label for="<?php echo $unique_variable_name; ?>[ticket_type]"><?php _e( 'Item Type', 'wc-muse' ) ?></label>
			<select name="<?php echo $unique_variable_name; ?>[ticket_type]" class="select short">
				<option value="event" <?php echo ( 'event' === $ticket_type ? 'selected' : '' ); ?>><?php _e( 'Event', 'wc-muse' ) ?></option>
				<option value="serie" <?php echo ( 'serie' === $ticket_type ? 'selected' : '' ); ?>><?php _e( 'Serie', 'wc-muse' ) ?></option>
			</select>
		</p>
	</div>

</div>
