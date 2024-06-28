<script>
    jQuery(document).ready(function($) {
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
        function loadmore_sponsored_content(offset){
            $('.ajaxLoader').show();
            var data = {
                'action': 'ajax_sponsored_contentMobile',
                'offset': offset,
            };
            //separately from ajaxurl for front end AJAX implementations
            jQuery.post(ajaxurl, data, function(response) {
                if(response){
                    $('.sponsored-content-mobile .loadmore-btn').remove();
                    $('.sponsored-content-mobile .category-footer-mobile').remove();
                    $(".sponsored-content-mobile").append(response);
                }
            });
        }
        loadmore_sponsored_content();
        $(document).on("click", '.sponsored-content-mobile .loadmore-btn', function(event) { 
            var offset = $(this).attr( "data-id" );
            loadmore_sponsored_content(offset);
            $('.ajaxLoader').show();
        });
    });
</script>
<div class="sponsored-content-mobile" style="min-height: 400px; position: relative;">
    <div id="ajaxLoader" class="ajaxLoader" style="display: none;"></div>
</div>