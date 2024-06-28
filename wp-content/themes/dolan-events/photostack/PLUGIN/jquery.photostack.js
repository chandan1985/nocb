(function($){
  $.fn.photostack = function(options) {
    var defaults = {
      prev : '#prev', next : '#next', speed : 'slow', direction : 'vertical',
      easeIn : null, easeOut : null,
      randomTop : 10, randomLeft : 10,
      autoplay : false, autoplayDirection : 'next', autoplayTimeout : 5000, autoplayStart : '#start', autoplayStop : '#stop',
      rotate : true, rotateDeg : 5,
      shadow : false, shadowOffsetX : 0, shadowOffsetY : 0, shadowBlur : 5, shadowColor : 'black'
    };
    var options = $.extend(defaults, options);
    var maxWidth, maxHeight, maxzIndex;
    var inAnimation = false;
    
    return this.each(function() {
      obj = $(this);
      maxWidth = 0, maxHeight = 0, maxzIndex = 0;
      obj.children('img').each(function() {
        var img = $(this);
        
        maxWidth = img.width() > maxWidth ? img.width() : maxWidth;
        maxHeight = img.height() > maxHeight ? img.height() : maxHeight;
        
        img.css({
         'z-index' : ++maxzIndex,
         'top' : randomPixels(options.randomTop)+'px',
         'left' : randomPixels(options.randomLeft)+'px'//,
        });
        
        if(navigator.appName != 'Microsoft Internet Explorer') { //hate!
          img.css('display', 'none');
          
          $(new Image()).attr('src', img.attr('src')).load(function() {
            img.fadeIn('slow');
          });
        }
        
        if(options.rotate) {
          var property = 'rotate('+randomPixels(options.rotateDeg)+'deg)';
          img.css({
            '-moz-transform' : property,
            '-webkit-transform' : property
          });
        }
        
        if(options.shadow) {
          var property = options.shadowOffsetX+'px '+options.shadowOffsetY+'px '+options.shadowBlur+'px '+options.shadowColor;
          img.css({
            '-moz-box-shadow' : property,
            '-webkit-box-shadow' : property
          });
        }        
      });
      
      obj.children('img').css({
        'position' : 'absolute'
      }).wrapAll('<div class="photostack-wrapper"></div>').parent().css({
        'position' : 'relative',
        'width' : maxWidth,
        'height' : maxHeight
      });
      
      maxWidth += options.randomLeft;
      maxHeight += options.randomTop;
      
      if(options.rotate) {
        maxWidth += options.rotateDeg * 5;
        maxHeight += options.rotateDeg * 5;
      }

      $(options.prev).click(function() {
        swapPhotos(obj.children('.photostack-wrapper').children('img'), '1', maxzIndex, '', -1);
        return false;
      });
      
      $(options.next).click(function() {
        swapPhotos(obj.children('.photostack-wrapper').children('img'), maxzIndex, '1', '-', 1);
        return false;
      });
      
      var interval;
      if(options.autoplay) startAutoplay();
      $(options.autoplayStart).click(function() {
        interval = startAutoplay();
        return false;
      });
      $(options.autoplayStop).click(function() {
        clearInterval(interval);
        return false;
      });
    });
    
    function swapPhotos(collection, target, newzIndex, direction, inDecrease) {
      if(inAnimation) return false;
      else inAnimation = true;
      
      collection.each(function() {
        if($(this).css('z-index') == target) {
          var animationIn, animationOut;
          if(options.direction == 'vertical') {
            animationIn = { 'top' : direction+maxHeight+'px', 'left' : randomPixels(options.randomLeft)+'px' };
            animationOut = { 'top' : randomPixels(options.randomTop)+'px' };
          } else {
            animationIn = { 'left' : direction+maxWidth+'px', 'top' : randomPixels(options.randomTop)+'px' };
            animationOut = { 'left' : randomPixels(options.randomLeft)+'px' };
          }
          
          $(this).animate(animationIn, options.speed, options.easeIn, function() {
            $(this).css('z-index', newzIndex).animate(animationOut, options.speed, options.easeOut, function() {
              inAnimation = false;
            });
          });
        } else {
          $(this).animate({ 'text-decoration' : 'none' }, options.speed, function() {
            $(this).css('z-index', parseInt($(this).css('z-index')) + inDecrease);
          });
        }
      });
    }
    
    function randomPixels(maxNum) {
      var ranNum = Math.floor(Math.random() * maxNum);
      return Math.floor(Math.random()*2) == 0 ? "-"+ranNum : ranNum;
    }
    
    function startAutoplay() {
      return setInterval(function() {
        if(options.autoplayDirection == 'next') swapPhotos(obj.children('.photostack-wrapper').children('img'), maxzIndex, '1', '-', 1);
        else { swapPhotos(obj.children('.photostack-wrapper').children('img'), '1', maxzIndex, '', -1); }
      }, options.autoplayTimeout);
    }
  };
})(jQuery);