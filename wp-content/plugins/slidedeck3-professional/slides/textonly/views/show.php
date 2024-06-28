<?php
$default_layout = $slide->meta['_layout'];
?>

<ul class="slide-content-fields">
    <li>
        <strong><?php _e( "Choose Layout", $namespace ); ?></strong>
        <ul>
        	<?php foreach( $layouts as $layout => $label ): ?>
        		<li class="layout">
        			<label<?php if( $default_layout == $layout ) echo ' class="active-layout"'; ?>>
        				<img src="<?php echo $url; ?>/images/layout-thumbnail-<?php echo $layout; ?>.png" alt="<?php echo $label; ?>" />
        				<span class="label"><?php echo $label; ?></span>
        				<input type="radio" name="_layout" value="<?php echo $layout; ?>"<?php if( $slide->meta['_layout'] == $layout ) echo ' checked="checked"'; ?> />
        			</label>
        		</li>
        	<?php endforeach; ?>
        </ul>
    </li>

    <li>
        <label><?php _e( "Slide Link", $namespace ); ?><br />
            <input type="text" name="_permalink" value="<?php echo $slide->meta['_permalink']; ?>" />
        </label>
    </li>
    
    <li class="slide-title no-border option">
        <label><?php _e( "Title", $namespace ); ?><br />
            <input type="text" name="post_title" value="<?php echo get_the_title( $slide->ID ); ?>" />
        </label>
    </li>
    
    <li class="last slide-copy option">
        <label><?php _e( "Copy", $namespace ); ?></label>
        <textarea class="slidedeck_mceEditor" name="post_excerpt" cols="40" rows="5" id="slidedeck-slide-caption-description-<?php echo $slide->ID; ?>"><?php echo esc_textarea( wpautop( $slide->post_excerpt ) ); ?></textarea>
    </li>
	<?php
		// check if developer or scheduler addon is installed
		if( in_array( 'scheduler', SlideDeckPlugin::$addons_installed ) && get_option( "slidedeck_addon_activate", false ) ) { 
			?>
		<li>
		<?php 
		$checked = ( isset($slide->meta['_slide_scheduled']) && $slide->meta['_slide_scheduled'] === "schedule" ) ? 'checked' : '';
		?>
        <input type="checkbox" name="_slide_scheduled" id="_slide_scheduled" value="schedule" <?php echo $checked; ?>/>
		<label style="display: inline-block;" for="_slide_scheduled"><?php _e( "Schedule this slide ?", $namespace ); ?></label>
    </li>
	<?php 
	$is_scheduled = ( isset($slide->meta['_slide_scheduled']) && $slide->meta['_slide_scheduled'] === "schedule" ) ? '' : 'style="display:none;"';
	?>
	<li class="slidedeck-show-schedule" <?php echo $is_scheduled; ?> >
        <label><?php _e( "Start Date", $namespace ); ?></label>
        <input type="text" style="width: 270px;" class="slidedeck-date-picker" name="_slide_start_date" value="<?php echo $slide->meta['_slide_start_date']; ?>" />
    </li>
	<li class="slidedeck-show-schedule" <?php echo $is_scheduled; ?> >
        <label><?php _e( "End Date", $namespace ); ?></label>
        <input type="text" style="width: 270px;" class="slidedeck-date-picker" name="_slide_end_date" value="<?php echo $slide->meta['_slide_end_date']; ?>" />
    </li>
	<script type="text/javascript">
	jQuery(function() {
		jQuery( ".slidedeck-date-picker" ).datepicker();
		jQuery('#_slide_scheduled').change(function() {
			if ( jQuery('#_slide_scheduled').is(':checked') ) {
				jQuery('.slidedeck-show-schedule').show();
			} else {
				jQuery('.slidedeck-show-schedule').hide();
			}
		});
	});	
	</script>
	<?php } ?>
    
</ul>

<script type="text/javascript">
    sd_layoutoptions = {
        "basic" : {
            "fields" : ".slide-title, .slide-copy, .image-scaling"
        },
        "multi-column" : {
            "fields" : ".slide-title, .slide-copy, .image-scaling"
        },
        "block-quote" : {
            "fields" : ".slide-title, .slide-copy, .image-scaling"
        }
    };

    (function($, window, undefined){
        $(function(){
            // Show correct fields for layout when opening flyout
            var layoutoption = sd_layoutoptions['<?php echo $default_layout; ?>'];
            $('.slide-content-fields').find('li.option').not(layoutoption.fields).hide();
            $('.slide-content-fields').find(layoutoption.fields).show();
        });   
    })(jQuery, window, null);
</script>