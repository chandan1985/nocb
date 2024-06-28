function toggleMobileMenu(){
	if(jQuery('#mobilemenu').css('display') == 'none'){
		jQuery( '#mobilemenu' ).fadeIn();
		jQuery( '.e3lan-top' ).fadeOut();
		jQuery( '.mobile-menu-background-cover' ).fadeIn();
		jQuery('.top-menu-liney a').css('background-position-x', '-34px');
	}else{
		jQuery( '#mobilemenu' ).fadeOut();
		jQuery( '.e3lan-top' ).fadeIn();
		jQuery( '.mobile-menu-background-cover' ).fadeOut();
		jQuery('.top-menu-liney a').css('background-position-x', '0px');
	}
}