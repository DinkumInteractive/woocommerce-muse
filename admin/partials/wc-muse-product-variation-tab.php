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
			<label for="<?php echo $unique_variable_name; ?>[seat_slug]"><?php _e( 'Seat Slug', 'wc-muse' ) ?></label>
			<input type="text" name="<?php echo $unique_variable_name; ?>[seat_slug]" value="<?php echo $seat_slug; ?>">
		</p>
	</div>

</div>
