jQuery( function($){
	if(typeof(interAds) !== 'undefined' && interAds != null){
		//WAIT TIME
		var is_wait = interAds.is_count;
		var is_cached = interAds.is_cached;
		if(is_wait){
			setTimeout(
			  function() 
			  {
				$('#interads').fadeIn('fast');
				interads_close();
			  }, (is_wait*60000));
		}
		if(!is_wait && !is_cached){
					interads_count();
		}
		
		
		
	}
});

function interads_count(){
	var min_txt = (typeof(interAds) !== 'undefined' && interAds != null && interAds.minutes) ? interAds.minutes : '';
	var sec_txt = (typeof(interAds) !== 'undefined' && interAds != null && interAds.seconds) ? interAds.seconds : '';
    
    if(typeof(interAds.is_count) !== 'undefined' && interAds.is_count != null && interAds.is_count > 0){

    count = jQuery('.interads-kkcount-down').data('seconds');

    jQuery('.interads-kkcount-down').countdown({
          date: +(new Date) + (30000 * count),
          render: function(data) {
            if( data.min > 0)
               jQuery(this.el).html("<div>" + this.leadingZeros(data.min, 2) + " <span> "+ min_txt +" : </span></div><div>" + this.leadingZeros(data.sec, 2) + " <span>"+ sec_txt +"</span></div>");
            else
               jQuery(this.el).html("<div>" + this.leadingZeros(data.sec, 2) + " <span>"+ sec_txt +"</span></div>");
               
          },
          onEnd: function() {
            interads_close();
          }
        });
    }
}

//Close Ad
function interads_close(){
    jQuery('#interadsmain').fadeOut('fast', function() { 
        jQuery('#interadsmain').remove();
        if(typeof(interAds.returl) !== 'undefined' && interAds.returl != null){ 
        	window.location.href = interAds.returl;
        }
        return false;
    });
    
    
}

// Countdown timer section

	/*var timeleft = 20;
	var downloadTimer = setInterval(function(){
	if(timeleft <= 0){
	clearInterval(downloadTimer);
	document.getElementById("countdown").innerHTML = "0 Seconds.";
	} else {
	document.getElementById("countdown").innerHTML = timeleft + " seconds.";
	}
	timeleft -= 1;
	}, 1000);*/

