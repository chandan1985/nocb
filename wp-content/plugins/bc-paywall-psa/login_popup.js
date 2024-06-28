	/**
	 * dmcss-wp popup login form (javascript/jQuery)
	 *
	 * Javascript & jQuery to implement popup login form & AJAX submit
	 *
	 * @author Asentech 
	 * @uses jQuery
	 */

	(function($) {
		$(document).ready(function() {
			// Show popup form when clicking 'LOG IN'
			$('#login_popup').click( function( event ) {
				// Calculate position of popup div based on WP wrapper & top-nav height
				var wrapper = $('.wrapper').offset();
				var menu_pos = $('.top-menu').offset();
				$('#popup_loginform').css({ left: ( menu_pos.left - wrapper.left ), top: ( $('.top-nav').outerHeight() ) });
				$('#popup_loginform').toggle();
				event.preventDefault();
				event.stopPropagation();
			});

			// Hide popup form if click elsewhere
			$('html').click(function() {
				$('#popup_loginform').hide();
			});

			// Prevent hide if clicking inside popup
			$('#popup_loginform').click(function(event){
				event.stopPropagation();
			});
		});
	})(jQuery);