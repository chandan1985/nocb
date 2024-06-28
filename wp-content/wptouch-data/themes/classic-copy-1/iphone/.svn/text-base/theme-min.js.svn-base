var touchJS = jQuery.noConflict();
function doClassicReady() {	touchJS.timerId = setInterval( function() {
var timers = touchJS.timers;
for ( var i = 0; i < timers.length; i++ ) {
if ( !timers[i]() ) {
timers.splice( i--, 1 );
}
}
if ( !timers.length ) {
clearInterval( touchJS.timerId );
touchJS.timerId = null;
}
}, 83);
touchJS( function () {
var tabContainers = touchJS( '#menu-container > div' );
var loginTab = touchJS( '#menu-tab5' );
touchJS( '#tab-inner-wrap-left a' ).click( function () {
tabContainers.hide().filter( this.hash ).opacityToggle( 450 );
touchJS( '#tab-inner-wrap-left a' ).removeClass( 'selected' );
touchJS( this ).addClass( 'selected' );
if ( loginTab ) {
touchJS( 'input#log' ).focus();
} else {
touchJS( 'input#log' ).blur();	}
return false;
}).filter( ':first' ).click();
});
touchJS( 'a#header-menu-toggle' ).unbind( 'click' ).click( function() {
touchJS( '#main-menu' ).opacityToggle( 450 );
touchJS( '#main-menu' ).toggleClass( 'show-menu' );
touchJS( this ).toggleClass( 'menu-toggle-open' );
return false;
});	touchJS( 'a#tab-search' ).unbind( 'click' ).click( function() {
touchJS( '#search-bar' ).toggleClass( 'show-search' );
touchJS( this ).toggleClass( 'search-toggle-open' );
if ( touchJS( '#search-bar' ).hasClass( 'show-search' ) ) {
touchJS( 'input#search-input' ).focus();
} else{
touchJS( 'input#search-input' ).blur();	}
return false;
});	touchJS( '#main-menu' ).find( 'li.has_children ul' ).hide();
touchJS( '#main-menu ul li.has_children > a' ).unbind( 'click' ).click( function() {
touchJS( this ).parent().children( 'ul' ).opacityToggle( 350 );
touchJS( this ).toggleClass( 'arrow-toggle' );
touchJS( this ).parent().toggleClass( 'open-tree' );
return false;
});
if ( touchJS( '#prowl-message' ).length ) {
setTimeout( function() { touchJS( '#prowl-message' ).fadeOut( 350 ); }, 2500 );
}
touchJS( 'a#share-post' ).unbind( 'click' ).click( function() {
touchJS( '#inner-ajax #share-absolute' ).opacityToggle( 450 ).viewportCenter();
});	touchJS( 'a#share-close' ).unbind( 'click' ).click( function() {
touchJS( '#inner-ajax #share-absolute' ).opacityToggle( 450 );
return false;
});	touchJS('li#instapaper a').unbind( 'click' ).click( function() {
var userName = prompt( WPtouch.instapaper_username, '' );
if ( userName ) {
var somePassword = prompt( WPtouch.instapaper_password, '' );
if ( !somePassword ) {
somePassword = 'default';	}
var ajaxParams = {
url: document.location.href,
username: userName,
password: somePassword,
title: document.title
};
WPtouchAjax( 'instapaper', ajaxParams, function( result ) {
if ( result == '1' ) {
alert( WPtouch.instapaper_saved );
} else {
alert( WPtouch.instapaper_try_again );
}
});
}
return false;
});
var shareOverlay = touchJS( '#share-absolute' ).get(0);
if ( shareOverlay ) {
shareOverlay.addEventListener( 'touchmove', classicTouchMove, false );	touchJS( '#email a' ).click( function() {
touchJS( 'a#share-close' ).click();
return true;
});
}
if ( touchJS( '.commentlist' ).length ) {
touchJS( '.commentlist li :first' ).addClass( 'first' );
touchJS( '.commentlist img.avatar:first' ).addClass( 'first' );
}
if ( touchJS( window ).width() <= 320 ) {
touchJS( 'body' ).addClass( 'portrait' );
} else {
touchJS( 'body' ).addClass( 'landscape' );
}
window.onorientationchange = function() {
var orientation = window.orientation;
switch( orientation ) {
case 0:
touchJS( 'body' ).toggleClass( 'portrait' ).removeClass( 'landscape' );
break;
case 180:
touchJS( 'body' ).toggleClass( 'portrait' ).removeClass( 'landscape' );
break;
case 90:
touchJS( 'body' ).toggleClass( 'landscape' ).removeClass( 'portrait' );
break;
case -90:
touchJS( 'body' ).toggleClass( 'landscape' ).removeClass( 'portrait' );
break;
}
}
if ( touchJS( 'a.com-toggle').length ) {
touchJS( 'a.com-toggle' ).click( function() {
classic_showhide_response();
return false;
});
}
hideAddressBar();
webAppOnly();
hijackPostLinks();
loadMoreEntries();
loadMoreComments();
classicExcerptToggle();
webAppOverlay();
var webApp = window.navigator.standalone;
if ( webApp ) {
touchJS( 'div.wptouch-shortcode-webapp-only' ).show();	} else {
touchJS( 'div.wptouch-shortcode-mobile-only' ).show();
}
}
function hideAddressBar() {
var webApp = window.navigator.standalone;
if ( !webApp ) {
if ( top.location!= self.location ) { top.location = self.location.href }
window.addEventListener( 'load', function() {
setTimeout( scrollTo, 0, 0, 1 );
}, false );
}
}
function classicTouchMove( event ) {
event.preventDefault();	}
touchJS.fn.fadeToggle = function( speed, easing, callback ) {
return this.animate( { opacity: 'toggle', height: 'toggle' }, speed, easing, callback );
}
touchJS.fn.opacityToggle = function( speed, easing, callback ) {
return this.animate( { opacity: 'toggle' }, speed, easing, callback );
}
touchJS.fn.showToggle = function( speed, easing, callback ) {
return this.animate( { display: 'toggle' }, speed, easing, callback );
}
touchJS.fn.viewportCenter = function() {
this.css( 'position','absolute');
this.css( 'top', ( touchJS( window ).height() - this.height() ) / 3 + touchJS( window ).scrollTop() + 'px' );
this.css( 'left', ( touchJS( window ).width() - this.width() ) / 2 + touchJS( window ).scrollLeft() + 'px' );
return this;
}
touchJS.fn.viewportBottom = function() {
this.css( 'position','absolute');
this.css( 'top', ( touchJS( document ).height() - this.height() ) / 6 + touchJS( window ).scrollTop() + 'px' );
this.css( 'left', ( touchJS( window ).width() - this.width() ) / 2 + touchJS( window ).scrollLeft() + 'px' );
return this;
}
function webAppOverlay() {
touchJS( '#web-app-overlay' ).viewportBottom();
touchJS( '#web-app-overlay a' ).unbind( 'click' ).click( function() {
touchJS( '#web-app-overlay' ).fadeOut();
});
}
function webAppOnly() {
var webApp = window.navigator.standalone;
if ( webApp ) {
touchJS( '#welcome-message' ).hide();
touchJS( '#switch' ).remove();
touchJS( '#switch' ).remove();
touchJS( 'a.comment-reply-link' ).remove();
touchJS( 'a.comment-edit-link' ).remove();
touchJS( 'body' ).addClass( 'web-app' );
if ( touchJS( 'body.black-translucent' ).length ) {
touchJS( 'body.black-translucent' ).css('margin-top', '20px');
}
}
}
function wptouchGetDocumentUrl() {
if ( window.navigator.standalone && wptouchAjaxUrl ) {
return wptouchAjaxUrl;	}
return document.location.href;	}
function wptouchGetDocumentTitle() {
if ( window.navigator.standalone ) {
return prompt( WPtouch.classic_post_desc, '' );
} else {
return document.title;
}
}
function hijackPostLinks() {
touchJS( '#main-menu ul li.has_children > a, a.load-more-link' ).addClass( 'no-ajax' );
touchJS( 'a.translate_translate' ).addClass( 'no-ajax' );
if ( window.navigator.standalone ) {
touchJS( 'li.force-external a' ).addClass( 'no-ajax' );
touchJS( '#main-menu ul li a img' ).click( function() {
touchJS( this ).parent().click();	return false;
});
}
var allExternalLinks = touchJS( 'a:not(.no-ajax)' );
if ( allExternalLinks.length ) {
allExternalLinks.unbind( 'click' ).click( function( e ) {
var url = e.target.href;
var isPhoneNumber = ( url.indexOf( 'tel:' ) >= 0 );
var isUnsupportedFile = ( url.lastIndexOf( '.pdf' || '.mp4' || '.mov' || '.doc' || '.xls' || '.pages' || '.numbers' || '.txt' || '.zip' || '.wav' ) >= 0  );
var localDomain = document.domain;
var webApp = window.navigator.standalone;
if ( isPhoneNumber || isUnsupportedFile ) {
return true;	}
if ( webApp ) {
if ( touchJS( this ).hasClass( 'comment-reply-link' ) || touchJS( this ).hasClass( 'thdrpy' ) || touchJS( this ).hasClass( 'thdmang' ) ) {
return true;	}
var actualLink = touchJS( this ).attr( 'href' );
if ( actualLink[0] == '#' ) {
return true;
}	if ( url.match( localDomain ) && !touchJS( this ).parent().hasClass( 'email' ) ) {
if ( touchJS( '#main-menu' ).hasClass( 'show-menu' ) ) {
touchJS( 'a#header-menu-toggle' ).click();
}	loadPage( url );	return false;
} else {
if ( touchJS( this ).parent().hasClass( 'email' ) ) {
return true;	}
var answer = confirm( WPtouch.external_link_text + ' \n' + WPtouch.open_browser_text );
if ( answer ) {
return true;
} else {
return false;
}
}
} else {
if ( touchJS( this ).parent().hasClass( 'email' ) ) {
touchJS( '#main-menu' ).opacityToggle( 0 );
touchJS( '#main-menu' ).toggleClass( 'show-menu' );
touchJS( 'a#header-menu-toggle' ).toggleClass( 'menu-toggle-open' );
}
}
});
}
}
function classicExcerptToggle() {
touchJS( 'a.excerpt-button' ).unbind( 'click' ).click( function() {
touchJS( this ).toggleClass( 'open' );
var postID = touchJS( this ).attr( "rel" );
var parentPost = touchJS( this ).parents( "div.post" );
if ( parentPost.length ) {
var firstParent = touchJS( parentPost.get(0) );
firstParent.find( 'div.content' ).opacityToggle( 350 );	}	return false;	});
}
function loadMoreEntries() {
var loadMoreLink = touchJS( 'a.load-more-link' );
if ( loadMoreLink.length ) {
loadMoreLink.unbind( 'click' ).click( function() {
touchJS( this ).addClass( 'ajax-spinner' );
var loadMoreURL = touchJS( this ).attr( 'rel' );
touchJS( '#content' ).append( "<div class='ajax-page-target'></div>" );
touchJS( 'div.ajax-page-target' ).hide().load( loadMoreURL + ' #content .post,#content .load-more-link', function() {
touchJS( 'div.ajax-page-target' ).replaceWith( touchJS( 'div.ajax-page-target' ).html() );	setTimeout( function() { loadMoreLink.fadeOut( 500 ); }, 500 );
loadMoreEntries();
hijackPostLinks();	classicExcerptToggle();
});
return false;
});	}	}
function loadMoreComments() {
var loadMoreLink = touchJS( 'ol.commentlist li.load-more-comments-link a' );
if ( loadMoreLink.length ) {
loadMoreLink.unbind( 'click' ).click( function() {
touchJS( this ).addClass( 'ajax-spinner' );
var loadMoreURL = touchJS( this ).attr( 'href' );
touchJS( 'ol.commentlist' ).append( "<div class='ajax-page-target'></div>" );
touchJS( 'div.ajax-page-target' ).hide().load( loadMoreURL + ' ol.commentlist > li', function() {
touchJS( 'div.ajax-page-target' ).replaceWith( touchJS( 'div.ajax-page-target' ).html() );	setTimeout( function() { loadMoreLink.fadeOut( 350 ); }, 500 );
webAppOnly();
loadMoreComments();
});
return false;
});	}	}
function classic_showhide_response() {
touchJS( 'ol.commentlist' ).toggleClass( 'shown' );
touchJS( 'ol.commentlist' ).toggleClass( 'hidden' );
touchJS( 'img#com-arrow' ).toggleClass( 'com-arrow-down' );
}
function saveURL( type ) {
var storage = window [ type + 'Storage' ];
if ( !window [ type + 'Storage' ] ) return;
localStorage.setItem( 'value', wptouchAjaxUrl );
}
function goToPersistent() {
var webApp = window.navigator.standalone;
if ( webApp ) {	var lastUrl = localStorage.getItem( 'value' );
var storage = window [ 'localStorage' ];
if ( !window [ 'localStorage' ] ) return;
if ( window.location != localStorage.getItem( 'value' ) ) {
loadSavedPage( lastUrl );
} else {
localStorage.setItem( 'value', window.location.href );
}
}
}
var wptouchAjaxUrl = '';
function loadPage( url ) {
touchJS( 'body' ).append( '<div id="progress"></div>' )
touchJS( '#progress' ).viewportCenter();
touchJS( '#outer-ajax' ).load( url + ' #inner-ajax', function( allDone ) {
wptouchAjaxUrl = url;
touchJS('#progress').fadeOut(350);
setTimeout( function() {
touchJS('#progress').remove();
}, 1500 );	saveURL( 'local' );
doClassicReady();
scrollTo( 0, 0 );
} );
}
var wptouchAjaxUrl = '';
function loadSavedPage( url ) {
touchJS( '#content' ).load( url + ' #content', function( allDone ) {
wptouchAjaxUrl = url;
doClassicReady();
} );
}
touchJS( document ).ready( function() { goToPersistent(); doClassicReady(); } );