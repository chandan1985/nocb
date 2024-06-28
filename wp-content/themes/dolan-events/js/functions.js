$(document).ready(function() {
$('#tdr-nav > li').hover(function() {
  	$(this).addClass('hover');
  	},function(){
  	$(this).removeClass('hover');
  });

$('div#photos').photostack({
	    speed : 600,
        easeIn : 'easeOutExpo',
        easeOut : 'easeInExpo',
        shadow : true,
        speed : 'fast'
	});
});