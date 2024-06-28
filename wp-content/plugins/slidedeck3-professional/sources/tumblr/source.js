(function($){ 
    // Get ready to trigger the ajax update.
 
window.TumblrSource = {
        elems: {},
        
        
        initialize: function(){
            var self = this;
            
            this.elems.form = $('#slidedeck-update-form');
            this.slidedeck_id = $('#slidedeck_id').val(); 
            
     }
    };
    
    $(document).ready(function(){
        TumblrSource.initialize();
    });
        
    var ajaxOptions = [
        "options[tumblr_post_type]",
        
    ];
    for(var o in ajaxOptions){
        SlideDeckPreview.ajaxOptions.push(ajaxOptions[o]);
    }

})(jQuery);
