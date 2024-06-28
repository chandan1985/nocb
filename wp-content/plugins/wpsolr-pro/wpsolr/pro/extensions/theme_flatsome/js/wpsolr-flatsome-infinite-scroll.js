jQuery(document).ready(function ($) {

    $(document).on('wpsolr_on_ajax_success', function (event, param) {

        const viewMoreButton = jQuery('button.view-more-button.products-archive');
        const byButton = flatsome_infinite_scroll.type === 'button';
        if (byButton) {
            viewMoreButton.css('display', '');
        }

        const container = jQuery('.shop-container .products');
        const paginationNext = '.woocommerce-pagination li a.next';
        if (container.length === 0 || jQuery(paginationNext).length === 0 || !param.has_pagination) {
            if (byButton) {
                viewMoreButton.css('display', 'none');
            }

            //console.log('no pagination or no results');
            return
        }

        /**
         * On WPSOLR ajax facets click, refresh the flatsome infinite scroll object
         */
        let infScroll = $('.shop-container .products').data('infiniteScroll');
        if (infScroll) {
            infScroll.create();

            //console.log(infScroll.getAbsolutePath());
        } else {
            console.error("WPSOLR: could not find infinite scroll Flatsome object $('.shop-container .products').data('infiniteScroll') .");
        }

    });

});
