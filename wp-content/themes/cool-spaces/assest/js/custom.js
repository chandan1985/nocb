$=jQuery

jQuery(document).ready(function () {

  $('.breaking-news-ticker').newsTicker({
    speed: 800,
    max_rows: 1,
    row_height:30,
    
  });

  jQuery('#primary, #secondary').theiaStickySidebar({
        // Settings
        additionalMarginTop: 30
      });  


  /* search toggle */
  $('body').click(function(evt){
    if(!( $(evt.target).closest('.search-section').length || $(evt.target).hasClass('search-toggle') ) ){
     if ($(".search-toggle").hasClass("search-active")){
      $(".search-toggle").removeClass("search-active");
      $(".search-box").slideUp("slow");
    }
  }
});
  $(".search-toggle").click(function(){
    $(".search-box").toggle("slow");
    if ( !$(".search-toggle").hasClass("search-active")){
     $(".search-toggle").addClass("search-active");

   }
   else{
    $(".search-toggle").removeClass("search-active");
  }
  
});

  jQuery('.menu-top-menu-container').meanmenu({
    meanMenuContainer: '.main-navigation',
    meanScreenWidth:"767",
    meanRevealPosition: "left",
  });


  /* back-to-top button*/

  $('.back-to-top').hide();
  $('.back-to-top').on("click",function(e) {
   e.preventDefault();
   $('html, body').animate({ scrollTop: 0 }, 'slow');
 });

  
  $(window).scroll(function(){
    var scrollheight =400;
    if( $(window).scrollTop() > scrollheight ) {
     $('.back-to-top').fadeIn();

   }
   else {
    $('.back-to-top').fadeOut();
  }
});


  if($(".latest-post-wrapper").niceScroll!=undefined){

    $(".latest-post-wrapper").niceScroll({
      cursorcolor:"#55bdbf",
      cursorwidth: "5px",
      cursorborder: "1px solid #55bdbf",
      cursorminheight: 20,
    });
    
  }
  


           // slider

           var owllogo = $("#owl-sidebar-slider");

           owllogo.owlCarousel({
            items:1,
            loop:true,
            nav:true,
            dots:false,
            smartSpeed:900,
            autoplay:true,
            autoplayTimeout:5000,
            fallbackEasing: 'easing',
            transitionStyle : "fade",
            autoplayHoverPause:true,
            animateOut: 'fadeOut'
          });

           var owllogo = $("#partner-slider");

           owllogo.owlCarousel({
            items:4,
            loop:true,
            nav:true,
            dots:false,
            smartSpeed:900,
            autoplay:true,
            autoplayTimeout:5000,
            fallbackEasing: 'easing',
            transitionStyle : "fade",
            autoplayHoverPause:true,
            animateOut: 'fadeOut'
          });


           var owl = $(".news-slider");
           owl.owlCarousel({
            items:1,
            loop:true,
            nav:false,
            dots:true,
            smartSpeed:900,
            autoplay:true,
            autoplayTimeout:4500,
            fallbackEasing: 'easing',
            transitionStyle : "fade",
            autoplayHoverPause:true
            
          });

           var owl = $(".news-slider-tab");
           owl.owlCarousel({
            items:1,
            loop:true,
            nav:false,
            dots:true,
            smartSpeed:900,
            autoplay:true,
            autoplayTimeout:4500,
            fallbackEasing: 'easing',
            transitionStyle : "fade",
            autoplayHoverPause:true
            
          });

           var property_slide_show = $('#news-slide-show').owlCarousel({
            items: 1,
            loop: false,
            center: true,
            margin: 10,
            callbacks: true,
            dots: false,
            URLhashListener: true,
            autoplayHoverPause: true,
            startPosition: 'URLHash'

          });

           property_slide_show.on('translated.owl.carousel', function() {
            var activeIndex = $('#news-slide-show').find('.owl-item.center').index();
            $('.video-post-section .post').removeClass('active');
            $('.video-post-section .post').eq(activeIndex).addClass('active');
            
          });


           $('.play').on('click', function() {
            owl.trigger('play.owl.autoplay', [1000])
          })
           $('.stop').on('click', function() {
            owl.trigger('stop.owl.autoplay')
          })

              // main slider

              $(document).ready(function() {

                var sync1 = $("#main-slider");
                var sync2 = $("#main-slider-option");
  var slidesPerPage = 4; //globaly define number of elements per page
  var syncedSecondary = true;

  sync1.owlCarousel({
    items : 1,
    slideSpeed : 2000,
    nav: false,
    // autoplay: true,
    dots: true,
    loop: true,
    fallbackEasing: 'easing',
    transitionStyle : "fade",
    animateOut: 'fadeOut',
    responsiveRefreshRate : 200,
    navText: ['<svg width="100%" height="100%" viewBox="0 0 11 20"><path style="fill:none;stroke-width: 1px;stroke: #000;" d="M9.554,1.001l-8.607,8.607l8.607,8.606"/></svg>','<svg width="100%" height="100%" viewBox="0 0 11 20" version="1.1"><path style="fill:none;stroke-width: 1px;stroke: #000;" d="M1.054,18.214l8.606,-8.606l-8.606,-8.607"/></svg>'],
  }).on('changed.owl.carousel', syncPosition);

  sync2
  .on('initialized.owl.carousel', function () {
    sync2.find(".owl-item").eq(0).addClass("current");
  })
  .owlCarousel({
    items : slidesPerPage,
    dots: false,
    nav: false,
    smartSpeed: 200,
    slideSpeed : 500,
    slideBy: slidesPerPage, //alternatively you can slide by 1, this way the active slide will stick to the first item in the second carousel
    responsiveRefreshRate : 100
  }).on('changed.owl.carousel', syncPosition2);

  function syncPosition(el) {
    //if you set loop to false, you have to restore this next line
    //var current = el.item.index;
    
    //if you disable loop you have to comment this block
    var count = el.item.count-1;
    var current = Math.round(el.item.index - (el.item.count/2) - .5);
    
    if(current < 0) {
      current = count;
    }
    if(current > count) {
      current = 0;
    }
    
    //end block

    sync2
    .find(".owl-item")
    .removeClass("current")
    .eq(current)
    .addClass("current");
    var onscreen = sync2.find('.owl-item.active').length - 1;
    var start = sync2.find('.owl-item.active').first().index();
    var end = sync2.find('.owl-item.active').last().index();
    
    if (current > end) {
      sync2.data('owl.carousel').to(current, 100, true);
    }
    if (current < start) {
      sync2.data('owl.carousel').to(current - onscreen, 100, true);
    }
  }
  
  function syncPosition2(el) {
    if(syncedSecondary) {
      var number = el.item.index;
      sync1.data('owl.carousel').to(number, 100, true);
    }
  }
  
  sync2.on("click", ".owl-item", function(e){
    e.preventDefault();
    var number = $(this).index();
    sync1.data('owl.carousel').to(number, 300, true);
  });
});


        // custom tab
        jQuery('.tabs .tab-links a').on('click', function(e)  {
          var currentAttrValue = jQuery(this).attr('href');
          
              // Show/Hide Tabs
              jQuery('.tabs ' + currentAttrValue).fadeIn(400).siblings().hide();
              
              // Change/remove current tab to active
              jQuery(this).parent('li').addClass('active').siblings().removeClass('active');
              
              e.preventDefault();
            });


        
      });
