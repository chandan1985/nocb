(function ($) {
 	$(function () {
 	// Create a clone of the menu, right next to original.
		
		//clone the menu
		$('#tdc_az_menu').addClass('original').clone().insertAfter('#tdc_az_menu').addClass('cloned').css('position','fixed').removeClass('original');
		$('#tdc_az_menu.cloned').css('display','none');
		
		//add the clickable scrolling elements
		var navScroll = document.createElement('li');
		$(navScroll).addClass('navScroll').addClass('original').css('z-index','500').appendTo('div#tdc_az_menu.original ul').html('<a>>></a>');
		$(navScroll).clone().addClass('cloned').removeClass('original').appendTo('div#tdc_az_menu.cloned ul').css('display','none');
		$('.cloned.navScroll').html('<a><<</a>').css('display','none');
	
	
		var clicked = 0;
		$('.navScroll').click(function() {
			
			if (clicked ==0 ) {
				//show the second menu
				$('.original').css('display','none');
				$('.cloned').css('display','block');
				clicked = 1;
			} else {
				//only show the original menu.
				$('.cloned').css('display','none');
				$('.original').css('display','block');
				clicked = 0;
			}
		});
		
	/*
		$( window ).resize(function() {
			if ($(window).width() > 478){
				$('#tdc_az_menu.original ul').css('display',block');
				$('#tdc_az_menu.cloned ul').css('display',none');
				$('.original.navScroll').css('display',none');
				$('.cloned.navScroll').css('display',none');
			}
		});
	*/	
	
	});
})(jQuery);