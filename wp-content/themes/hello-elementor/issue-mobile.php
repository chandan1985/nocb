<script>
    jQuery(document).ready(function() {
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
        function loadmore_issue(offset){
            jQuery('.ajaxLoader').show();
            var data = {
                'action': 'ajax_issue_Mobile',
                'offset': offset,
            };
            //separately from ajaxurl for front end AJAX implementations
            jQuery.post(ajaxurl, data, function(response) {
                if(response){
                    jQuery('.category-mobile .loadmore-btn').remove();
                    jQuery('.category-mobile .category-footer-mobile').remove();
                    jQuery(".category-mobile").append(response);
                }
            });
        }
        loadmore_issues();
        jQuery(document).on("click", '.category-mobile .loadmore-btn', function(event) { 
			var offset = jQuery(this).attr( "data-id" );
            // alert(offset);
			loadmore_issues(offset);
            jQuery('.ajaxLoader').show();
		});
    });
</script>
<div class="category-mobile" style="min-height: 400px; position: relative;">
<div id="ajaxLoader" class="ajaxLoader" style="display: none;"></div>
</div>