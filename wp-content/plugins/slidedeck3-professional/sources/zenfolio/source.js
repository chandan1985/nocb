(function($){
    window.zenfolioSource = {
        elems: {},
        
	updatezenfolioGalleries : function(){
		var self = this;
		var username = $('#options-zenfolio_username').val();
		var password = $('#options-zenfolio_password').val(); 

            $.ajax({
                url: ajaxurl,
                data: "action=update_zenfolio_activation_info&zenfolio_username="+username+"&zenfolio_password="+password,  
		type: 'POST',
                success: function(data){
                    $('#zenfolio-user-galleries').html( data ).find('.fancy').fancy();
                    SlideDeckPreview.ajaxUpdate();
                }
            });	

	},
        initialize: function(){
            var self = this;
            
            this.elems.form = $('#slidedeck-update-form');
            
            this.slidedeck_id = $('#slidedeck_id').val();
            
             // Zenfolio Username 
            this.elems.form.delegate('.zenfolio-authenticate-ajax-token', 'click', function(event){
                event.preventDefault();
                self.updatezenfolioGalleries();
            });

            // Prevent enter key from submitting text fields
            this.elems.form.delegate('#options-zenfolio_username', 'keydown', function(event){
                if( 13 == event.keyCode){
                    event.preventDefault();
                    $('.zenfolio-authenticate-ajax-token').click();
                    return false;
                }
                return true;
            });
        }
    };
    
    var ajaxOptions = [
        "options[zenfolio_username]",
        "options[zenfolio_password]",
	"options[zenfolio_galleries]",
    ];
    for(var o in ajaxOptions){
        SlideDeckPreview.ajaxOptions.push(ajaxOptions[o]);
    }
    
    $(document).ready(function(){
        zenfolioSource.initialize();
    });
})(jQuery);
