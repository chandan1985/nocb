<ul class="slide-content-fields">
    <li><label><?php _e( "Title", $namespace ); ?><br />
        <input type="text" name="post_title" value="<?php echo get_the_title( $slide->ID ); ?>" />
    </label></li>
</ul>

<div class="p"><label><?php _e( "Insert HTML", $namespace ); ?></label><br />
<textarea name="post_excerpt" cols="40" rows="20"><?php echo esc_textarea( $slide->post_content ); ?></textarea></div>

<?php
		// check if developer or scheduler addon is installed
		if( in_array( 'scheduler', SlideDeckPlugin::$addons_installed ) && get_option( "slidedeck_addon_activate", false ) ) { 
			?>
<ul class="slide-content-fields">
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
</ul>
	<?php } ?>

<script type="text/javascript">
    (function($, window, undefined){
        window.htmlSlideEditor = CodeMirror.fromTextArea($('#slidedeck-custom-slide-editor textarea[name="post_excerpt"]')[0], {
            lineNumbers: true,
            theme: "slidedeck",
            readOnly: false,
            indentUnit: 4,
            tabSize: 4,
            lineWrapping: true,
            onCursorActivity: function() {
                htmlSlideEditor.setLineClass(htmlSlideEditorLine, null);
                htmlSlideEditorLine = htmlSlideEditor.setLineClass(htmlSlideEditor.getCursor().line, "activeline");
            }
        });
        window.htmlSlideEditorLine = htmlSlideEditor.setLineClass(0, "activeline");
        setTimeout(function(){
            window.htmlSlideEditor.refresh();
        }, 250);
    })(jQuery, window, null);
</script>
