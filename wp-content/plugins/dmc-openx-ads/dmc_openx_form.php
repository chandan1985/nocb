<label for="<?php echo $this->get_field_id('title'); ?>">Title (for identification purposes only):</label>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_html($title); ?>" />
<br />
<label for="<?php echo $this->get_field_id('zoneID'); ?>">Zone ID:</label>
<input class="widefat" id="<?php echo $this->get_field_id('zoneID'); ?>" name="<?php echo $this->get_field_name('zoneID'); ?>" type="text" value="<?php echo esc_html($zoneID); ?>" />
<br />
<?php 
	$old_delivery = get_option( "deprecated_openxwpwidget_url2openx" );
	$new_delivery = get_option( "new_openxwpwidget_url2openx" );
?>
<label for="<?php echo $this->get_field_id('deliveryserver'); ?>">Delivery Server:</label>
<select class="widefat" name="<?php echo $this->get_field_name('deliveryserver'); ?>" id="<?php echo $this->get_field_id('deliveryserver'); ?>">
	<option value="<?php echo esc_html($old_delivery); ?>" 
		<?php 		
			if( $deliveryserver == $old_delivery ) {
				echo 'selected';
			} 
		?>
		>
		<?php
			echo $old_delivery;
		?>
	</option>
	<option value="<?php echo esc_html($new_delivery); ?>" 
		<?php 		
			if( $deliveryserver == $new_delivery ) {
				echo 'selected';
			}
		?>
		>
		<?php
			echo $new_delivery;
		?>
	</option>
</select>
<br />
<label for="<?php echo $this->get_field_id('alignment'); ?>">Alignment:</label>
<select class="widefat" name="<?php echo $this->get_field_name('alignment'); ?>" id="<?php echo $this->get_field_id('alignment'); ?>">
	<option value="left" <?php if($alignment == 'left') {echo 'selected';} ?>>LEFT</option>
	<option value="center" <?php if($alignment == 'center') {echo 'selected';} ?>>CENTER</option>
	<option value="right" <?php if($alignment == 'right') {echo 'selected';} ?>>RIGHT</option>
</select>
<label for="<?php echo $this->get_field_id('paddingtop'); ?>">Padding-Top:</label>
<input class="widefat" id="<?php echo $this->get_field_id('paddingtop'); ?>" name="<?php echo $this->get_field_name('paddingtop'); ?>" type="text" value="<?php echo esc_html($paddingtop); ?>" />
<br />
<label for="<?php echo $this->get_field_id('paddingright'); ?>">Padding-Right:</label>
<input class="widefat" id="<?php echo $this->get_field_id('paddingright'); ?>" name="<?php echo $this->get_field_name('paddingright'); ?>" type="text" value="<?php echo esc_html($paddingright); ?>" />
<br />
<label for="<?php echo $this->get_field_id('paddingbottom'); ?>">Padding-Bottom:</label>
<input class="widefat" id="<?php echo $this->get_field_id('paddingbottom'); ?>" name="<?php echo $this->get_field_name('paddingbottom'); ?>" type="text" value="<?php echo esc_html($paddingbottom); ?>" />
<br />
<label for="<?php echo $this->get_field_id('paddingleft'); ?>">Padding-Left:</label>
<input class="widefat" id="<?php echo $this->get_field_id('paddingleft'); ?>" name="<?php echo $this->get_field_name('paddingleft'); ?>" type="text" value="<?php echo esc_html($paddingleft); ?>" />
<label>Don't show banner again on same page?</label>
<br />
<input id="<?php echo $this->get_field_id('block') . '-no'; ?>" name="<?php echo $this->get_field_name('block'); ?>" type="radio" value="0" "CHECKED" />
<label for="<?php echo $this->get_field_id('block') . '-no'; ?>"><strong>No</strong></label>
<br />
<input id="<?php echo $this->get_field_id('block') . '-yes'; ?>" name="<?php echo $this->get_field_name('block'); ?>" type="radio" value="1" <?php if(isset($block) && $block == '1') {echo "CHECKED";} ?>>
<label for="<?php echo $this->get_field_id('block') . '-yes'; ?>"><strong>Yes</strong></label>
<br />

<br /><br />

