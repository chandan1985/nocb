<script>
    jQuery(document).ready(function(jQuery) {
        var ajaxurll = "<?php echo admin_url('admin-ajax.php'); ?>";
        function loadmore_author(offset){
            // jQuery('.ajaxLoader').show();
            var data = {
                'action': 'ajax_authorMobile',
                'offset': offset,
            };
            //separately from ajaxurl for front end AJAX implementations
            jQuery.post(ajaxurll, data, function(response) {
                if(response){
                    jQuery('.author-mobile .loadmore-btn').remove();
                    jQuery(".author-mobile").append(response);
                }
            });
        }
        loadmore_author();
        jQuery(document).on("click", '.author-mobile .loadmore-btn', function(event) { 
			var offset = jQuery(this).attr( "data-id" );
			loadmore_author(offset);
            // jQuery('.ajaxLoader').show();
		});
    });
</script>
<div class="author-mobile" style="min-height: 400px; position: relative;">
    <div id="ajaxLoader" class="ajaxLoader" style="display: none;"></div>
</div>