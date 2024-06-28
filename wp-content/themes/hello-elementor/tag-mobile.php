<script>
    jQuery(document).ready(function($) {
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
        function loadmore_tag(offset){
            $('.ajaxLoader').show();
            var data = {
                'action': 'ajax_tagMobile',
                'offset': offset,
            };
            //separately from ajaxurl for front end AJAX implementations
            jQuery.post(ajaxurl, data, function(response) {
                if(response){
                    $('.category-mobile .loadmore-btn').remove();
                    $('.category-mobile .category-footer-mobile').remove();
                    $(".category-mobile").append(response);
                }
            });
        }
        loadmore_tag();
        $(document).on("click", '.category-mobile .loadmore-btn', function(event) { 
			var offset = $(this).attr( "data-id" );
            // alert(offset);
			loadmore_tag(offset);
            $('.ajaxLoader').show();
		});
    });
</script>
<div class="category-mobile" style="min-height: 400px; position: relative;">
<div id="ajaxLoader" class="ajaxLoader" style="display: none;"></div>
</div>