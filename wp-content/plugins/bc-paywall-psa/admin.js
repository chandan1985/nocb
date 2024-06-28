	/**
	 * dmcss-wp admin (javascript/jQuery)
	 *
	 * Javascript & jQuery to add pubs and toggle visibility of UI elements
	 *
	 * @author Asentech
	 * @uses jQuery
	 */

	// Initialize UI & setup click handlers
	(function($) {
		$(document).ready(function() {
			//Enable add row when required field is populated
			$( '.widefat' ).on( 'change', '.tdc_required', function() {
				if( '' == $(this).val() ) {
					$( '.widefat .dmcss_row_add_button' ).attr( 'disabled', 'disabled' );
				}
				else {
					$( '.widefat .dmcss_row_add_button' ).removeAttr( 'disabled' );
				}
			});

			// Click hanlder to add additional table rows ( publication / category subrate )
			$( '.widefat' ).on( 'click', '.dmcss_row_add_button', function() {
				dmcss_new_row_add();
			});

			// Enable import when confirmed
			$( '#paywall_confirm_import' ).click( function() {
				if( $(this).is( ':checked' ) ) {
					$( '#paywall_import_button' ).removeAttr( 'disabled' );
				}
				else {
					$( '#paywall_import_button' ).attr( 'disabled', 'disabled' );
				}
			});

			$( '#data_fade' ).click( function() {
				$( '#data_contents' ).toggle();
			});
		})

	// Function to Add additional publications
	function dmcss_new_row_add(){
		// Create our new html to insert
		var position = $( '.widefat tbody tr' ).length;
		var alt = $( '.widefat .dmcss_pub' ).length + position;
		var html = $( '.dmcss_templates .dmcss_new_row' ).parent().html().replace( /%i%/g, position );

		// Remove old Add buttons
		$( '.widefat .dmcss_row_add_button' ).remove();
		$( '.widefat input' ).removeClass( 'tdc_required' );
		$( '.widefat input' ).unbind( 'change' );

		// Insert new html
		$( '.widefat .dmcss_new_row:last' ).after(html);

		if( alt % 2 === 0 ) {
			$( '.widefat .dmcss_new_row:last' ).addClass( 'alternate' );
		}

		// Put our cursor in a convenient place
		$( '.widefat .dmcss_new_row:last .dmcss_row_focus' )[0].focus();
	}
})(jQuery);