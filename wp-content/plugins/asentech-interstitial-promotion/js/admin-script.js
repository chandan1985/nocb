jQuery( function($){

	//Enable Tabs
	init_settings();
	
	
	
	
//Select options:
	function init_settings(){
		var is_show = "dfp";
		
		jQuery("#adtype").change(function() {
		  is_show = $('option:selected', this).val();
			if(is_show == "dfp"){
				jQuery('#dfp').show();
				jQuery('#openx').hide();
				
			}else{
				jQuery('#dfp').hide();
				jQuery('#openx').show();
			}
		});
		
		
	}

	
	// The "Upload" button
jQuery('.upload_image_button').click(function() {
	//event.preventDefault();
		
	var send_attachment_bkp = wp.media.editor.send.attachment;
	var button = jQuery(this);
	wp.media.editor.send.attachment = function(props, attachment) {
		jQuery(button).parent().prev().attr('src', attachment.url);
		jQuery(button).prev().val(attachment.id);
		wp.media.editor.send.attachment = send_attachment_bkp;
	}
	wp.media.editor.open(button);
	return false;
});

// The "Remove" button (remove the value from input type='hidden')
jQuery('.remove_image_button').click(function(event) {
	event.preventDefault(); // Added this to prevent remove button submitting and refreshing page when clicked
	var answer = confirm('Are you sure?');
	if (answer == true) {
		var src = jQuery(this).parent().prev().attr('data-src');
		jQuery(this).parent().prev().attr('src', src);
		jQuery(this).prev().prev().val('');
	}
	return false;
});	
	
});


jQuery(document).ready(function($) {
$('.custom_date').datepicker({
dateFormat : 'mm-dd-yy',
minDate: new Date(2018, 1 - 1, 1)
});
});


	

	