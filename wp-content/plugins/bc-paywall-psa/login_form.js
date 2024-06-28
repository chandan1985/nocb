	/**
	 * dmcss-wp login form (javascript/jQuery)
	 *
	 * Javascript & jQuery to implement tabbed login form and toggle visibility of UI elements
	 *
	 * @author Asentech
	 * @uses jQuery
	 */

	(function($) {
		$(document).ready(function() {
			// Toggle between active tabs
			$( '#dmcss_tabs li a' ).click( function ( $event ) {
				$event.preventDefault();
				$( 'div.active_tab' ).hide();
				$( '.active_tab' ).removeClass( 'active_tab' );
				$parent = $event.target.id.replace( 'link', 'tab' );
				$tab =  $event.target.id.replace( 'link', 'div' );
				$( '#' + $parent ).addClass( 'active_tab' );
				$( '#' + $tab ).addClass( 'active_tab' );
				$( 'div.active_tab' ).show();
			});

			// Un-hide default tab
			$( '.dmcss_tab_div.active_tab' ).show();

			// Disable inputs if we're previewing
			$( '#form_tabs input.disabled' ).attr( 'disabled', 'disabled' );
		});
	})(jQuery);