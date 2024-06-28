jQuery(document).ready(function () {

    var search_text = jQuery(".search-form-container #adv-search-form input").val();
    jQuery(".search-wrapper .results-listing .body-text:contains(" + search_text + ")").each(function () {
        var newText = jQuery(this).html().replace(search_text, '<mark data-markjs="true" class="hilite">' + search_text + '</mark>');
        jQuery(this).html(newText);
    });
    // mobile specific --START
    function windowSize() {
        var width = jQuery(window).width()
        if (width <= 767) {
            jQuery('body').addClass('smallDevice');
            jQuery('body').removeClass('largeDevice');
            jQuery('.select-region-or-brand-menu .menu-item-has-children .sub-menu').removeClass('extranalclass');

            jQuery('.ns-landing-page .right-cs').show();
            jQuery('.ns-landing-page .right-cs').slick({
                autoplay: true,
                infinite: true,
                slidesToShow: 1,
                slidesToScroll: 1,
            });
            jQuery(".search-wrapper .wp-pagenavi a.nextpostslink").text('Load more');
        }
        else {
            jQuery('body').addClass('largeDevice');
            jQuery('body').removeClass('smallDevice');
        }
    }
    windowSize();
    jQuery(window).resize(function () {
        windowSize();
    });

    // mobile specific --END

    jQuery('.date-menu-section .hamburger-menu').click(function () {
        jQuery('.pop-out-menu-area').toggleClass('pop-out-menu-open')
        jQuery('.date-menu-section').toggleClass('open-close')
        jQuery('body').toggleClass('show-hide')
    });

    jQuery('.mobile-date-menu-section .hamburger-menu').click(function () {
        jQuery('body').toggleClass('scroll-hide show-hide');
        jQuery('.mobile-date-menu-section').toggleClass('open-close');
        jQuery('.mobile-pop-out-menu').toggleClass('mobile-pop-out-menu-open');
    });

    jQuery('.featuredContent_slideshow').show();
    jQuery('.featuredContent_slideshow').slick({
        infinite: true,
        speed: 300,
        slidesToShow: 1,
        autoplay: true,
        autoplaySpeed: 3000,
    });

    function outside_click_handler() {
        if (jQuery('body').find('.show-hide').prevObject.length === 1) {
            var container = jQuery('.pop-out-menu-area')
            var menuHamburger = jQuery('.date-menu-section')
            jQuery(document).click(function (e) {
                if (!container.is(e.target) &&
                    container.has(e.target).length === 0 &&
                    !menuHamburger.is(e.target) &&
                    menuHamburger.has(e.target).length === 0
                ) {
                    jQuery('.pop-out-menu-area').removeClass('pop-out-menu-open')
                    jQuery('.date-menu-section').removeClass('open-close')
                    jQuery('body').removeClass('show-hide')
                }
            })
        }
    }
    document.body.addEventListener('click', outside_click_handler, true)

    jQuery("#pop-out-menu-region").mouseleave(function () {
        jQuery('.pop-out-menu-area').removeClass('pop-out-menu-open')
        jQuery('.date-menu-section').removeClass('open-close')
        jQuery('body').removeClass('show-hide')
    });

    jQuery("#menu-main-navigation .menu-item-has-children").hover(function () {
        jQuery(this).find(".sub-menu").toggle();
    });
    jQuery(".btm-header-left .select-region-or-brand h2").click(function () {
        jQuery(".btm-header-left .select-region-or-brand .sp-tab__lay-default").toggle();
    });
    jQuery(".btm-header .manage-account .menu-item-has-children").mouseenter(function () {
        jQuery(".btm-header .manage-account  .menu-item-has-children .sub-menu").slideDown();
    });
    jQuery(".btm-header .manage-account  .menu-item-has-children").mouseleave(function () {
        jQuery(".btm-header .manage-account  .menu-item-has-children .sub-menu").hide();
    });

    jQuery('#advanced_search select#author_search').on('click', function () {
        var prnt = jQuery(this).parents('#advanced_search .author-field-wrap');
        if (!prnt.hasClass('select-opened')) {
            prnt.addClass('select-opened');
        } else {
            prnt.removeClass('select-opened');
        }
    });
    jQuery('#advanced_search select#author_search').on('blur', function () {
        var prnt = jQuery(this).parents('#advanced_search .author-field-wrap');
        if (prnt.hasClass('select-opened')) {
            prnt.removeClass('select-opened');
        }
    });

    jQuery(".select-region-or-brand-menu h2").click(function () {
        jQuery(".select-region-or-brand-menu .menu-select-region-or-brand-container").toggle();
        jQuery('.btm-header-section .select-region-or-brand-menu .menu > li:first-child > a').addClass('active');
        jQuery('.largeDevice .btm-header-section .select-region-or-brand-menu .menu > li:first-child .sub-menu').addClass('extranalclass');
        jQuery(".mobile-top-bar").toggleClass('region-extended');
    });

    jQuery(".largeDevice .select-region-or-brand-menu .menu .menu-item-has-children").click(function () {
        var id = jQuery(this).attr('id');
        jQuery('.select-region-or-brand-menu .menu-item-has-children .sub-menu').removeClass('extranalclass');
        jQuery('#' + id + ' .sub-menu').addClass('extranalclass');
        jQuery('.select-region-or-brand-menu .menu-item-has-children a').removeClass('active');
        jQuery('#' + id + ' a').addClass('active');
    });

    jQuery(".select-region-or-brand-menu .menu").mouseleave(function () {
        jQuery(".select-region-or-brand-menu .menu-select-region-or-brand-container").hide();
        jQuery('.select-region-or-brand-menu .menu-item-has-children .sub-menu').removeClass('extranalclass');
        jQuery('.select-region-or-brand-menu .menu-item-has-children a').removeClass('active');
    });
    // for mobile
    // jQuery(".mobile-top-bar .menu-select-region-or-brand-container").hide();
    jQuery('.mobile-top-bar .select-region-or-brand-menu .menu li.menu-item-has-children').click(function () {
        jQuery(this).toggleClass(' active ');
        jQuery(this).siblings().removeClass(' active ');
        jQuery('.smallDevice .select-region-or-brand-menu .menu-item-has-children .sub-menu').stop().slideUp();
        jQuery('.smallDevice .active .sub-menu').stop().slideDown();
    });

    if (jQuery(window).width() < 768) {
        jQuery(".homepage-top-stories .top-news-section.col-sm-5").insertAfter(jQuery(".homepage-featured-section"));
        // jQuery(".search-wrapper .wp-pagenavi a.nextpostslink").text('Load more');
    }

    // Hide photo gallery and video gallery id on article listing page
    jQuery("p:contains('caption id')").text('');
    jQuery("p:contains('iframe')").hide();
    jQuery("p:contains('SlideDeck2 id')").text('');
    jQuery("p:contains('gallery order')").text('');
    jQuery("p:contains('gallery ids')").text('');

    jQuery('.event-content__sidebar:eq(1)').hide();    //  hide double occuring 'event-content__sidebar' class
    // for load more content on homepage

    jQuery( ".previouspostslink" ).insertBefore( ".first" );


});

// for load more content on homepage
jQuery(document).ajaxStop(function () {
    jQuery('.ajaxLoader').hide();
    jQuery("p:contains('caption id')").hide();
    jQuery("p:contains('SlideDeck2 id')").text('');
    jQuery("p:contains('gallery order')").text('');
    jQuery("p:contains('gallery ids')").text('');
    jQuery("p:contains('iframe')").attr("style", "display: none !important");
})

setTimeout(() => {
    jQuery('.gallery .gallery-item').each(function () {
        jQuery(this).find('a').attr("data-fancybox", "gallery");
        var dataCaption = jQuery(this).find('figcaption').text();
        jQuery(this).find('a').attr("data-caption", dataCaption);
    });
});



