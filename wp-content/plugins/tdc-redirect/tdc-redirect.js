jQuery.noConflict();



jQuery(document).ready( function($) {
	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	}

	$('.redirect-list').sortable({
		items: '.redirect_rule',
		helper : fixHelper,
		opacity: 0.6,
		cursor: 'move',
		axis: 'y',
		update: function() {
			var order = $(this).sortable('serialize') + '&action=update_redirect_order';
			$.post(ajaxurl, order, function(response) {
			});			
		}
	});
});