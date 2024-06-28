/*
 * Post Bulk Edit Script
 * Hooks into the inline post editor functionality to extend it to our custom metadata
 */

jQuery(document).ready(function(){
setTimeout(function(){
if (jQuery('.article_dfp_ads iframe').length > 0) {
    jQuery(".article_dfp_ads").css("display", "block");
}
else
{
jQuery(".article_dfp_ads").css("display", "none");
}
},5000);

if(jQuery('.entry').parent().find('.sharedaddy').length > 0) {
	var $toolbar = jQuery('.entry > .sharedaddy');
    $toolbar.parent().after($toolbar);
}

});