/**
* Custom Js for backend 
*
* @package Mag_Lite
*/
jQuery(document).ready(function($) {
    $('#mag-lite-img-container li label img').click(function(){    	
        $('#mag-lite-img-container li').each(function(){
            $(this).find('img').removeClass ('mag-lite-radio-img-selected') ;
        });
        $(this).addClass ('mag-lite-radio-img-selected') ;
    });                    
});
( function( api ) {

	api.sectionConstructor['upsell'] = api.Section.extend( {

		// No events for this type of section.
		attachEvents: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	} );

} )( wp.customize );