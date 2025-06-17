<?php 
$unique_variable_name ="wc_muse_variation[]";
$item_slug 			= get_post_meta( $variation->ID, 'item_slug', true ); // Concert slug
$sub_item_slug 	= get_post_meta( $variation->ID, 'sub_item_slug', true ); // Series slug
$seat_slug 			= get_post_meta( $variation->ID, 'seat_slug', true );
?>

<div class="form-meta">

	<p class="muse-variation-title"><strong><?php _e( 'Muse', 'wc-muse' ); ?></strong></p>

	<input type="hidden" name="<?php echo $unique_variable_name; ?>[id]" value="<?php echo $variation->ID; ?>">
	
	<div class="options-group">
		<p class="form-field form-row-full">
			<label for="<?php echo $unique_variable_name; ?>[seat_slug]"><?php _e( 'Seat Slug', 'wc-muse' ) ?></label>
			<input type="text" name="<?php echo $unique_variable_name; ?>[seat_slug]" value="<?php echo $seat_slug; ?>">
		</p>
	</div>
	
	<div class="options-group">
		<p class="form-field form-row-full">
			<label for="<?php echo $unique_variable_name; ?>[item_slug]"><?php _e( 'Concert Slug', 'wc-muse' ) ?></label>
			<input type="text" name="<?php echo $unique_variable_name; ?>[item_slug]" value="<?php echo $item_slug; ?>">
		</p>
	</div>
	
	<div class="options-group">
		<p class="form-field form-row-full">
			<label for="<?php echo $unique_variable_name; ?>[sub_item_slug]"><?php _e( 'Muse Series Slug', 'wc-muse' ) ?></label>
			<input type="text" name="<?php echo $unique_variable_name; ?>[sub_item_slug]" value="<?php echo $sub_item_slug; ?>">
		</p>
	</div>

</div>
