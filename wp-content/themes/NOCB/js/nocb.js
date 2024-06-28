jQuery(document).ready(function () {
    var divCount = jQuery(".sponsored-related-content .content-box").length;
    if (divCount === 0) {
        jQuery(".sponsored-section").addClass("hide");
    }
    var firstimg = jQuery(".pg-control-image").first().attr("src");
    var firstcap = jQuery(".pg-control-image").first().attr("data-cap");
    jQuery(".pg-control-image").first().addClass("active-image");
    jQuery(".pg-view-image").attr("src", firstimg);
    if (firstcap == "") {
        jQuery(".pg-view-caption").addClass("hidden")
        jQuery(".pg-caption").addClass("hidden")
    }
    else {
        jQuery(".pg-view-caption").removeClass("hidden")
        jQuery(".pg-caption").removeClass("hidden")
    }
    jQuery(".pg-view-caption").html(firstcap);
    jQuery(".pg-caption").html(firstcap);

    // jQuery('.newsdetail-wrapper .news-detail').contents().filter(function () {
    //     return this.nodeType != 1;
    // }).remove();
    // jQuery('.newsdetail-wrapper .news-detail .art-content').contents().filter(function () {
    //     return this.nodeType != 1;
    // }).remove();
    // jQuery('.newsdetail-wrapper .news-detail #bsf_rt_marker').contents().filter(function () {
    //     return this.nodeType != 1;
    // }).remove();

    jQuery(".pg-view-left").click(function (e) {
        if (jQuery(".active-image").parent(".pg-control-imglink").prev().size() > 0) {
            jQuery(".active-image").removeClass("active-image").parent(".pg-control-imglink").prev().children().addClass("active-image");
            var newactiveimage = jQuery(".active-image").attr("src");
            var newactivecap = jQuery(".active-image").attr("data-cap");
            if (newactivecap == "") {
                jQuery(".pg-view-caption").addClass("hidden")
                jQuery(".pg-caption").addClass("hidden")
            }
            else {
                jQuery(".pg-view-caption").removeClass("hidden")
                jQuery(".pg-caption").removeClass("hidden")
            }
            jQuery(".pg-view-image").attr("src", newactiveimage);
            jQuery(".pg-view-caption").html(newactivecap);
            jQuery(".pg-caption").html(newactivecap);
        }
        else {
            jQuery(".active-image").removeClass("active-image");
            jQuery(".pg-control-imglink").last().children().addClass("active-image");
            var newactiveimage = jQuery(".active-image").attr("src");
            var newactivecap = jQuery(".active-image").attr("data-cap");
            if (newactivecap == "") {
                jQuery(".pg-view-caption").addClass("hidden")
                jQuery(".pg-caption").addClass("hidden")
            }
            else {
                jQuery(".pg-view-caption").removeClass("hidden")
                jQuery(".pg-caption").removeClass("hidden")
            }
            jQuery(".pg-view-image").attr("src", newactiveimage);
            jQuery(".pg-view-caption").html(newactivecap);
            jQuery(".pg-caption").html(newactivecap);
        };
        return false;
    });

    jQuery(".pg-view-right").click(function (e) {
        if (jQuery(".active-image").parent(".pg-control-imglink").next().size() > 0) {
            jQuery(".active-image").removeClass("active-image").parent(".pg-control-imglink").next().children().addClass("active-image");
            var newactiveimage = jQuery(".active-image").attr("src");
            var newactivecap = jQuery(".active-image").attr("data-cap");
            if (newactivecap == "") {
                jQuery(".pg-view-caption").addClass("hidden")
                jQuery(".pg-caption").addClass("hidden")
            }
            else {
                jQuery(".pg-view-caption").removeClass("hidden")
                jQuery(".pg-caption").removeClass("hidden")
            }
            jQuery(".pg-view-image").attr("src", newactiveimage);
            jQuery(".pg-view-caption").html(newactivecap);
            jQuery(".pg-caption").html(newactivecap);
        }
        else {
            jQuery(".active-image").removeClass("active-image");
            jQuery(".pg-control-imglink").first().children().addClass("active-image");
            var newactiveimage = jQuery(".active-image").attr("src");
            var newactivecap = jQuery(".active-image").attr("data-cap");
            if (newactivecap == "") {
                jQuery(".pg-view-caption").addClass("hidden")
                jQuery(".pg-caption").addClass("hidden")
            }
            else {
                jQuery(".pg-view-caption").removeClass("hidden")
                jQuery(".pg-caption").removeClass("hidden")
            }
            jQuery(".pg-view-image").attr("src", newactiveimage);
            jQuery(".pg-view-caption").html(newactivecap);
            jQuery(".pg-caption").html(newactivecap);
        };
        return false;
    });

    jQuery('.digital-ediion-section .featuredContent_slideshow').show();
    jQuery('.digital-ediion-section .featuredContent_slideshow').slick({
        infinite: true,
        speed: 300,
        slidesToShow: 1,
        autoplay: true,
        autoplaySpeed: 3000,
    });

    // Alphabetical filter on powerlist page
    const contentBox = jQuery(".powerlist-wrapper .powerlist-box");
    contentBox.map((index, item) => {
        if (!(jQuery(item).hasClass("top-ten"))) {
            jQuery(item).hide()
        }
    });
    jQuery(".alpha-pagination li.top-10").click(function () {
        const className = jQuery(this)[0].className;
        if (className.includes("top-10")) {
            contentBox.map((index, item) => {
                jQuery(item).show();
            });
            contentBox.map((index, item) => {
                if (!jQuery(item).hasClass("top-ten")) {
                    jQuery(item).hide();
                }
            });
        } else if (className.includes("all")) {
            contentBox.map((index, item) => {
                jQuery(item).show();
            });
        } else {
            contentBox.map((index, item) => {
                jQuery(item).show();
            });
            contentBox.map((index, item) => {
                const titleText = jQuery(item)
                    .children(".powerlist-content-wrap")
                    .children(".title")
                    .children()[0].outerText;
                if (titleText.charAt(0) != jQuery(this)[0].outerText) {
                    jQuery(item).hide();
                }
            });
        }
    });

    // Clear selection from URL on page load
    var uri = window.location.toString();
    if (uri.indexOf("#") > 0) {
        var clean_uri = uri.substring(0, uri.indexOf("#"));
        window.history.replaceState({},
            document.title, clean_uri);
    }


    jQuery('.alpha-pagination .page-link').click(function () {
        jQuery('.alpha-pagination .page-link').removeClass("active");
        jQuery(this).addClass("active");
    });

    // jQuery('.newsdetail-wrapper .news-detail').contents().filter(function () {
    //     return this.nodeType != 1;
    // }).remove();
    jQuery("p:contains('gallery')").text('');
    jQuery("p:contains('caption id')").text('');
    jQuery("p:contains('poll id')").text('');
    jQuery("p:contains('Login Login User ID')").hide();
    jQuery("p a:contains('Share this event')").text('');
    jQuery("p:contains('Share this event')").text('');

    function windowSize() {
        var width = jQuery(window).width()
        if (width <= 767) {
            if (jQuery(".events_class.mobile-view .event-item").length < 4) {
                jQuery(".events_class.mobile-view .loadmore-btn").remove();
            }
            jQuery(".events_class.mobile-view .event-item").hide();
            jQuery(".events_class.mobile-view .event-item").slice(0, 4).show();
            jQuery(".events_class.mobile-view .loadmore-btn").on('click', function (e) {
                e.preventDefault();
                jQuery(".events_class.mobile-view .event-item:hidden").slice(0, 4).show();
                if (jQuery(".events_class.mobile-view .event-item:hidden").length == 0) {
                    jQuery(".events_class.mobile-view .loadmore-btn").remove();
                }
            });

        }
    }
    windowSize();
    jQuery(window).resize(function () {
        windowSize();
    })

    jQuery(".toggle-head-open").click(function () {
        jQuery(this).parent().find(".toggle-content").slideToggle("slow");
        jQuery(this).hide();
        jQuery(this).parent().find(".toggle-head-close").show();
    });
    jQuery(".toggle-head-close").click(function () {
        jQuery(this).parent().find(".toggle-content").slideToggle("slow");
        jQuery(this).hide();
        jQuery(this).parent().find(".toggle-head-open").show();
    });
    jQuery('.toggle-content p:empty').remove();

    jQuery(".legal-category-with-two-column ul").each(function () {
        var elem = jQuery(this);
        if (elem.children("li").length % 2 == 0) {
            jQuery('.legal-category-with-two-column .legal-read-more').css('left', '52%')
        }
    });

});

jQuery(document).ajaxStop(function () {
    jQuery("p:contains('gallery')").text('');
    jQuery("p:contains('caption id')").text('');
})

setTimeout(() => {
    //Section scroll
    if (jQuery(".section.latest-news .latest-news-header .nav-tabs").prop('scrollWidth') > (jQuery(".latest-news .latest-news-header").width() - 100)) {
        jQuery(".section.latest-news .latest-news-header").append('<div class="nav-scroll-controls"><span class="slide-left"></span><span class="slide-right"></span></div>');
    }
    else {
        jQuery(".section.latest-news .latest-news-header .nav-scroll-controls").remove();
    };

    jQuery('.section.latest-news .nav-scroll-controls .slide-right').click(function (event) {
        event.preventDefault();
        jQuery('.section.latest-news .latest-news-header nav').animate({
            scrollLeft: "+=100px"
        });
    });

    jQuery('.section.latest-news .nav-scroll-controls .slide-left').click(function (event) {
        event.preventDefault();
        jQuery('.section.latest-news .latest-news-header nav').animate({
            scrollLeft: "-=100px"
        });
    });

    //Products scroll
    if (jQuery(".products.latest-news .latest-news-header .nav-tabs").prop('scrollWidth') > (jQuery(".latest-news .latest-news-header").width() - 100)) {
        jQuery(".products.latest-news .latest-news-header").append('<div class="nav-scroll-controls"><span class="slide-left"></span><span class="slide-right"></span></div>');
    }
    else {
        jQuery(".products.latest-news .latest-news-header .nav-scroll-controls").remove();
    };

    jQuery('.products.latest-news .nav-scroll-controls .slide-right').click(function (event) {
        event.preventDefault();
        jQuery('.products.latest-news .latest-news-header nav').animate({
            scrollLeft: "+=100px"
        });
    });

    jQuery('.products.latest-news .nav-scroll-controls .slide-left').click(function (event) {
        event.preventDefault();
        jQuery('.products.latest-news .latest-news-header nav').animate({
            scrollLeft: "-=100px"
        });
    });
})
jQuery('.latest-news .latest-news-header button.nav-link:first-child',).addClass('active');
jQuery(document).ajaxStop(function () {
    jQuery('.ajaxLoader').hide();
    jQuery('.latest.latest-news').removeClass('for-ajax');
})

jQuery(document).ajaxStop(function () {
    jQuery(".smallDevice .section .content-box").slice(0, 2).addClass('display');
    jQuery(".section .latestNews-mobile.loadmore-btn").on('click', function (e) {
        e.preventDefault();
        jQuery(".smallDevice .section .content-box:hidden").slice(0, 2).addClass('display');
        if (jQuery(".smallDevice .section .content-box:hidden").length == 0) {
            jQuery(".section .latestNews-mobile.loadmore-btn").remove();
        }
    });

    jQuery(".smallDevice .products .content-box").slice(0, 2).addClass('display');
    jQuery(".products .latestNews-mobile.loadmore-btn").on('click', function (e) {
        e.preventDefault();
        jQuery(".smallDevice .products .content-box:hidden").slice(0, 2).addClass('display');
        if (jQuery(".smallDevice .products .content-box:hidden").length == 0) {
            jQuery(".products .latestNews-mobile.loadmore-btn").remove();
        }
    });
})

function urlChange() {
    const tab = document.querySelector('.tribe-events-c-view-selector--tabs');
    if (tab != null) {
        tab.onclick = function () {
            document.querySelector('.tribe-events-c-view-selector__list-item--month a').addEventListener('click', function () {
                // history.pushState({}, '', '/events');
                window.location.href = '/events';
            })
        }
    }
}
jQuery(document).ajaxStop(function () {
    urlChange();
})
urlChange();