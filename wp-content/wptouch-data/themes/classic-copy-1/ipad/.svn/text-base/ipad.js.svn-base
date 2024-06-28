/* WPtouch Pro Classic iPad JS */
/* This file holds all the default jQuery & Ajax functions for the classic theme on iPad */
/* Description: JavaScript for the Classic theme */
/* Required jQuery version: 1.4.x */

var touchJS = jQuery.noConflict();
var WPtouchWebApp = window.navigator.standalone;

/* If it's iPad, use touchstart, in desktop browser, use click (touchstart/end is faster on iOS) */
if ( navigator.platform == 'iPad' && typeof orientation != 'undefined' ) { 
	var touchStartOrClick = 'touchstart'; 
	var touchEndOrClick = 'touchend'; 
} else {
	var touchStartOrClick = 'click'; 
	var touchEndOrClick = 'click'; 
};

/* Try to get out of frames! */
if ( top.location!= self.location ) { 
	top.location = self.location.href
}

function doClassiciPadReady() {

/* Empty localStorage for persistence and web-app notice, and all cookies (uncomment and re-load to clear)  */
//	localStorage.clear();
// eraseCookie('wptouch-ipad-orientation');
// eraseCookie('wptouch-web-app-mode');
// document.cookie = 'wptouch_welcome=0; 0; path=/';

/* Prevent default touchmove function, ready for iScrolls */	
	document.addEventListener( 'touchmove', function( e ){ e.preventDefault(); } );
	
/* Add the orientation event listener */	
	window.addEventListener( 'orientationchange', function( e ) {
		classicUpdateOrientation();
	});
	
/* Setup iScrolls */

	classicMainScroll = new iScroll( 'iscroll-content', { desktopCompatibility: false, fadeScrollbar: false, shrinkScrollbar: true, bounce: true, checkDOMChanges:false } );

	if ( touchJS( '#pages-iscroll' ).length ) {
		classicPagesScroll = new iScroll( 'pages-iscroll', { desktopCompatibility: false, fadeScrollbar: false, checkDOMChanges:false } );
	} else { classicPagesScroll = classicMainScroll; }
	if ( touchJS( '#recent-iscroll' ).length ) {
		classicRecentScroll = new iScroll( 'recent-iscroll', { desktopCompatibility: false, checkDOMChanges:false } );
	} else { classicRecentScroll = classicMainScroll; }

	if ( touchJS( '#popular-iscroll' ).length ) {
		classicPopularScroll = new iScroll( 'popular-iscroll', { desktopCompatibility: false, checkDOMChanges:false } );
	} else { classicPopularScroll = classicMainScroll; }

	if ( touchJS( '#tags-iscroll' ).length ) {
		classicTagsScroll = new iScroll( 'tags-iscroll', { desktopCompatibility: false, checkDOMChanges:false } );
	} else { classicTagsScroll = classicMainScroll; }

	if ( touchJS( '#cats-iscroll' ).length ) {
		classicCatsScroll = new iScroll( 'cats-iscroll', { desktopCompatibility: false, checkDOMChanges:false } );
	} else { classicCatsScroll = classicMainScroll; }
	
	if ( touchJS( '#flickr-iscroll' ).length ) {
		classicFlickrScroll = new iScroll( 'flickr-iscroll', { desktopCompatibility: false, checkDOMChanges:false } );
	} else { classicFlickrScroll = classicMainScroll; }

/* Menubar Button Left Popover Triggers */	
	touchJS( '.head-left div.menubar-button' ).bind( touchEndOrClick, function( e ) {
		var popoverName = '#pop-' + touchJS( this ).attr( 'id' );
		var linkOffset = touchJS( this ).offset();
		touchJS( popoverName ).css({
			top: linkOffset.top + 32 + 'px',
			left: linkOffset.left - touchJS( popoverName ).width() / 7
		}).popOverToggle();
		touchJS( popoverName + ' .menu-pointer-arrow' ).css( 'left', '56px' );
		headerDismissSpan();
		setTimeout( function () { classicPagesScroll.refresh(); classicRecentScroll.refresh(); }, 0 );
	});
	
/* Menubar Button Right Popover Triggers */	
		touchJS( '.head-right div.menubar-button' ).bind( touchEndOrClick, function( e ) {
			var popoverName = '#pop-' + touchJS( this ).attr( 'id' );
			var linkOffset = touchJS( this ).offset();
			touchJS( popoverName ).css({
				top: linkOffset.top + 32 + 'px',
				left: linkOffset.left - touchJS( popoverName ).width() / 1.45
			} ).popOverToggle();
		touchJS( popoverName + ' .menu-pointer-arrow' ).css( 'right', '58px' );
		headerDismissSpan();
		setTimeout( function () { classicFlickrScroll.refresh(); }, 0 );
	});
	
/*  Tap the menubar to scroll the main content to top */	
	touchJS( '.head-center h1' ).bind( touchStartOrClick, function( e ) {
		classicMainScroll.scrollTo( 0, 0 );
	});

/*  Menubar Blog PopOver Inner Tabs */
	touchJS( function() {
	    var tabContainers = touchJS( '#pop-blog > div' );

	    touchJS( 'ul.menu-tabs a' ).unbind( 'click' ).bind( touchStartOrClick, function( e ) {
	        tabContainers.hide().filter( this.hash ).show();
	    	touchJS( 'ul.menu-tabs a' ).removeClass( 'selected' );
	   		touchJS( this ).addClass( 'selected' );
			e.preventDefault();
			setTimeout( function() { 
				classicRecentScroll.refresh(); 
				classicPopularScroll.refresh(); 
				classicTagsScroll.refresh(); 
				classicCatsScroll.refresh(); 
			}, 0 );
	  	  }).filter( ':first' ).trigger( touchStartOrClick );
	});

/* Bind .pressed styling to touchstart and touchend, to mimic default iOS functionality */
	touchJS( '.button' ).each( function() {
		touchJS( this ).bind( touchStartOrClick, function( e ) {
			touchJS( this ).addClass( 'pressed' );
		});
		touchJS( this ).bind( touchEndOrClick, function( e ) {
			touchJS( this ).removeClass( 'pressed' );
		});
	});

/* Add highlights to popover li's */
	touchJS( '#popovers-container .pop-inner li a' ).each( function() {
		touchJS( this ).bind( 'click', function( e ) {
			touchJS( this ).parent().addClass( 'highlight' );
		});
	});

/* Add touch feedback to .content links */
	touchJS( '.content a, .title-area a, #switch a, .footer a' ).each( function() { 
		touchJS( this ).bind( touchStartOrClick, function( e ) {
			touchJS( this ).addClass( 'active' );
		});
		touchJS( this ).bind( touchEndOrClick, function( e ) {
			touchJS( this ).removeClass( 'active' );
		});
	});
	
/*Page menu: Hide the Child ULs */
	touchJS( '#pages-wrapper' ).find( 'li.has_children ul' ).hide();

/*Page menu: Filter parent link href's and make them toggles for thier children */
	touchJS( '#pages-wrapper ul li.has_children > a' ).unbind( 'click' ).bind( 'click', function( e ) {
		touchJS( this ).next().webkitSlideToggle( 350 );
		touchJS( this ).toggleClass( 'arrow-toggle' );
		touchJS( this ).parent().toggleClass( 'open-tree' );
		setTimeout( function () { classicPagesScroll.refresh(); }, 0 );
		e.preventDefault();
	});
	
/*  Single post page share popover */	
	touchJS( 'a.share-post' ).unbind( 'click' ).bind( touchStartOrClick, function( e ) {
		var linkOffset = touchJS( this ).offset();
			touchJS( '#share-popover' ).css({
				top: linkOffset.top - 250 + 'px',
				left: linkOffset.left -	 25 + 'px',
				}).fadeIn( 350 );
		headerDismissSpan();
		e.preventDefault();
	});
	
//	touchJS( 'a#airprint' ).unbind( 'click' ).bind( touchStartOrClick, function( e ) {
//		window.print();
//		e.preventDefault();			
//	});
	
/*  On single posts and pages, move the comments to the fly-in box */
	touchJS( '#respond' ).detach().appendTo( '.comment-reply-box .pop-inner' ).css( 'display', 'block' );
	touchJS( '#share-placeholder' ).detach().appendTo( '#share-popover' ).css( 'display', 'block' );

/*  Make sure the menubar stays present when forms are out of focus */	
	touchJS( 'a.leave-a-comment, a.comments-close-button' ).unbind( 'click' ).bind( touchEndOrClick, function( e ) {
		touchJS( '.comment-reply-box' ).toggleClass( 'fly-in' ).flyInToggle();
		touchJS( 'input#comment_parent' ).val( '0' );
		touchJS( '#box-head h3' ).html( WPtouch.leave_a_comment );
		touchJS( '#peek' ).hide();
		touchJS( '#container1 textarea' ).removeClass( 'reply' );
		touchJS('#commentform input, #commentform textarea').blur();
		e.preventDefault();
	});

/* Peek in Comment Reply */
  touchJS( '#peek' ).unbind( 'click' ).bind( touchEndOrClick, function( e ) {
  	touchJS( '#container1' ).toggleClass( 'slide' );
  	touchJS( '#container2' ).toggleClass( 'slide2' );
	e.preventDefault();  	
  });
  
/* Log In To Comment Trigger */
  	touchJS( 'a.comment-reply-login, a.reply-to-comment' ).bind( touchStartOrClick, function( e ) {
		touchJS( '#account.menubar-button' ).trigger( touchEndOrClick );
		e.preventDefault();
		e.stopImmediatePropagation();
	});	

/* Try to make imgs and captions nicer in posts (images and caption larger than 350px get aligncenter)  */	
	if ( touchJS( '.single' ).length ) {
		touchJS( '.content img, .content .wp-caption' ).each( function() {
			if ( touchJS( this ).width() >= 350 ) {
				touchJS( this ).addClass( 'aligncenter' );
			}
		});
	}

/*  Make sure the menubar stays present when form textareas are out of focus */	
	touchJS( 'textarea, form#prowl-direct-message input, form#loginform input' ).bind( 'blur', function() {
		scrollTo( 0, 0, 1 );		
	});
	
/* Instapaper Share Hookup */
	touchJS('li#instapaper a').unbind( 'click' ).bind( 'click', function( e ) {
		var userName = prompt( WPtouch.instapaper_username, '' );
		if ( userName ) {
			var somePassword = prompt( WPtouch.instapaper_password, '' );
			if ( !somePassword ) {
				somePassword = 'default';	
			}			
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
		e.preventDefault();
	});

/* Ajaxify commentform */
	var postURL = document.location;
	var CommentFormOptions = {
		beforeSubmit: function( e ) {
			touchJS( '#container1' ).append( '<div id="comment-spinner"></div>' );
		},
		success: function( e ) {
			touchJS( 'ol.commentlist' ).load( postURL + ' ol.commentlist > li', function(){ 
				touchJS( '#comment-spinner' ).remove();
				touchJS( '#container1 textarea' ).addClass( 'success' );			
				commentReplyLinks();
			});
			setTimeout( function () { 
				touchJS( 'a.comments-close-button' ).trigger( touchEndOrClick );
				touchJS( '#container1 textarea' ).removeClass( 'success' );
				classicMainScroll.refresh();
			}, 750 );
		},
		error: function( e ) {
		touchJS( '#comment-spinner' ).remove();
			touchJS( '#container1 textarea' ).addClass( 'error' );
			touchJS( '#container1' ).prepend( '<div id="error-text">' + WPtouch.comment_failure +'</div>' );
			setTimeout( function () { 
				touchJS( '#container1 textarea' ).removeClass( 'error' );
				touchJS( '#error-text' ).remove();
			}, 3000 );
		},
		resetForm: true,
		timeout:   10000
	} 	//end options
	
	touchJS( '#commentform' ).ajaxForm( CommentFormOptions );

/* Ajaxify Prowl Form */
	var prowlFormOptions = {
		beforeSubmit: function formValidate( formData, jqForm, options ) { 
				for ( var i=0; i < formData.length; i++ ) { 
					if ( !formData[i].value ) { 
						alert( WPtouch.validation_message ); 
						return false; 
					}
				touchJS( '#prowl-direct-message p' ).prepend( '<div id="prowl-spinner"></div>' );
				}
		},
		success: function( e ) {
		touchJS( '#prowl-spinner' ).remove();
			touchJS( '#prowl-direct-message textarea' ).addClass( 'success' );
			setTimeout( function () { 
				touchJS( '#message' ).trigger( touchEndOrClick );
				touchJS( '#prowl-direct-message textarea' ).removeClass( 'success' );
			}, 1000 );
		},
		error: function( e ) {
		touchJS( '#prowl-spinner' ).remove();
			touchJS( '#prowl-direct-message textarea' ).addClass( 'error' );
			touchJS( '#prowl-direct-message' ).prepend( '<div id="prowl-error-text">' + WPtouch.prowl_failure +'</div>' );
			setTimeout( function () { 
				touchJS( '#prowl-direct-message textarea' ).removeClass( 'error' );
				touchJS( '#prowl-error-text' ).remove();
			}, 4000 );
		},
		resetForm: true,
		timeout:   3500
	} 	//end options
	
	touchJS( '#prowl-direct-message' ).ajaxForm( prowlFormOptions );

/* Refresh the main iScroll when all page items are loaded */	
	touchJS( window ).load( function() {  
		var welcomeMessage = touchJS( '#welcome-message' ).length;		
		var formHeight = touchJS( 'div.comment-reply-box' ).height() + 150;
		touchJS( 'div.comment-reply-box' ).css('-webkit-transform', 'translateY(-'+formHeight+'px) rotate(-10deg)' ).show();
		if ( welcomeMessage ) { 
			touchJS( '#welcome-message' ).show();
		}
		setTimeout( function () { touchJS( '#iscroll-wrapper' ).addClass('in-display'); classicMainScroll.refresh(); }, 0 );
	});

/* Functions to run onReady */
	classicUpdateOrientation();
	loadMoreEntries();
	commentReplyLinks();
	loadMoreComments();
	webAppOnly();
	webAppBubble_welcomeMessage();

/***** End Document Ready Functions *****/	
}

/* Detect orientation and do some Voodoo with the menubar's menu */
function classicUpdateOrientation() {	
	var windowHeight = touchJS( window ).height() - 44;
	var imageHeight = touchJS( '#logo-area' ).height();
	var menuHeight = windowHeight - imageHeight;
	var orientationCookie = readCookie( 'wptouch-ipad-orientation' );
	switch( window.orientation ) {
		// Portrait
		case 0:
		case 180:
			touchJS( 'body' ).removeClass( 'landscape' ).addClass( 'portrait' );
			touchJS( '#iscroll-wrapper' ).css( 'height', windowHeight );
			touchJS( '#main-menu #pages-wrapper' ).detach().appendTo( '#pop-menu .pop-inner' ).css( 'height', 'auto' ).css( 'max-height', '500px' );
			touchJS( '.popover.open' ).each( function() { touchJS( this ).removeClass( 'open' ).hide(); });
			createCookie( 'wptouch-ipad-orientation', 'portrait', 365 );
		break;
		// Landscape
		case 90:
		case -90:
			touchJS( 'body' ).removeClass( 'portrait' ).addClass( 'landscape' );
			touchJS( '#iscroll-wrapper' ).css( 'height', windowHeight );
			touchJS( '#pages-wrapper' ).detach().appendTo( '#main-menu' ).css( 'height', menuHeight ).css( 'max-height', 'none' );
			touchJS( '.popover.open' ).each( function() { touchJS( this ).removeClass( 'open' ).hide(); });
			createCookie( 'wptouch-ipad-orientation', 'landscape', 365 );
		break;
		default:
			touchJS( 'body' ).removeClass( 'portrait' ).addClass( 'landscape' );
			touchJS( '#iscroll-wrapper' ).css( 'height', windowHeight );
			touchJS( '#pages-wrapper' ).detach().appendTo( '#main-menu' ).css( 'height', menuHeight ).css( 'max-height', 'none' );
			if ( !orientationCookie ) { 
				createCookie( 'wptouch-ipad-orientation', 'landscape', 365 );
			}
	}
	setTimeout( function () { classicMainScroll.refresh(); classicPagesScroll.refresh(); }, 750 );
}

/* Create a dismiss span that will reverse open popovers when triggered */
function headerDismissSpan() {
	if ( !touchJS( '#dismiss-underlay' ).length ) {
		touchJS( 'body' ).append( '<span id="dismiss-underlay"></span>' );
		touchJS( '#dismiss-underlay' ).bind( touchStartOrClick, function( e ) {
			touchJS( '#popovers-container .popover.open' ).removeClass( 'open' ).fadeOut( 350 );
			touchJS( '#share-popover' ).fadeOut( 350 );
			touchJS( this ).remove();
			e.preventDefault();
		});
	} else {
		touchJS( '#dismiss-underlay' ).remove();	
	}
}

/* Main Load More Entries */
function loadMoreEntries() {
var loadMoreLink = touchJS( 'a.load-more-link' );
	if ( loadMoreLink.length ) {
		loadMoreLink.unbind( 'click' ).live( 'click', function( e ) {
			var loadMoreURL = touchJS( this ).attr( 'href' );
			touchJS( 'a.load-more-link span' ).addClass( 'ajax-spinner' );
			touchJS( this ).delay(2000).fadeOut();
			touchJS( '#content' ).append( "<div class='ajax-page-target'></div>" );

			touchJS( 'div.ajax-page-target' ).hide().load( loadMoreURL + ' #content .post, #content .load-more-link', function() {
				touchJS( this ).replaceWith( touchJS( this ).html() );
				webAppOnly();
				setTimeout( function() { classicMainScroll.refresh(); }, 0 );
			});
			e.preventDefault();
			e.stopPropagation();
		});	
	}	
}

/* Load More Comments */
function loadMoreComments() {
	var loadMoreLink = touchJS( 'ol.commentlist li.load-more-comments-link a' );
	if ( loadMoreLink.length ) {
		loadMoreLink.unbind( 'click' ).live( 'click', function( e ) {
			var loadMoreURL = touchJS( this ).attr( 'href' );
			touchJS( this ).addClass( 'ajax-spinner' ).delay( 1250 ).fadeOut( 350 );
			touchJS( 'ol.commentlist' ).append( "<div class='ajax-page-target'></div>" );

			touchJS( 'div.ajax-page-target' ).hide().load( loadMoreURL + ' ol.commentlist > li', function() {
				touchJS( this ).replaceWith( touchJS( this ).html() );	
				commentReplyLinks();
				webAppOnly();
		 		setTimeout( function() { classicMainScroll.refresh(); }, 0 );
			});
			e.preventDefault();
			e.stopPropagation();
		});	
	}	
}

/* Comment Reply Gravy - Not perfect yet  */	
function commentReplyLinks() {
	touchJS( '.commentlist a.comment-reply-link' ).unbind('click').bind( touchStartOrClick, function( e ) {
		var CommentID = touchJS( this ).closest( 'li.comment' ).attr( 'ID' );
		var CommenterName = touchJS( '#' + CommentID ).find( 'span.fn:first' ).text();
		var PostID = touchJS( '.commentlist ol' ).attr( 'ID' );
		var CommentContent = touchJS( 'li#' + CommentID + ' .comment-content:first > p' ).text();

		touchJS( '.comment-reply-box' ).addClass( 'fly-in' ).flyInToggle();		
		touchJS( '#box-head h3' ).html( WPtouch.leave_a_reply + ' <span>' + CommenterName + '</span>');
		touchJS( '#peek' ).show();
		touchJS( 'input#comment_parent' ).val( CommentID );
		touchJS( '#container1 textarea' ).addClass( 'reply' );
		touchJS( '#container2' ).html( CommentContent );
		e.preventDefault();
		e.stopImmediatePropagation();
	});
}

function webAppOnly() {
	if ( WPtouchWebApp ) {
		saveURL();
		touchJS( '#account-link-area, #switch' ).hide();
		// The Secret Sauce ( Nobody makes gravy like mom)
		var webAppLinks = touchJS( 'a' ).not( 'a.no-ajax, #pages-wrapper .has_children > a, ol.commentlist li.load-more-comments-link a, a.load-more-link' );
		
		webAppLinks.each( function() {
			var targetUrl = touchJS( this ).attr( 'href' );
			var localDomain = document.domain;
			var unSupportedLink = ( 
				targetUrl.lastIndexOf( '.pdf' ) >= 0 || targetUrl.lastIndexOf( '.xls' ) >= 0 || targetUrl.lastIndexOf( '.numbers' ) >= 0 || 
				targetUrl.lastIndexOf( '.pages' ) >= 0 || targetUrl.lastIndexOf( '.mp3' ) >= 0 || targetUrl.lastIndexOf( '.mp4' ) >= 0 || 
				targetUrl.lastIndexOf( '.m4v' ) >= 0 || targetUrl.lastIndexOf( '.mov' ) >= 0 || targetUrl.match( 'feed' ) || targetUrl.match( 'mailto:' )
			);			
			
			if ( ( !unSupportedLink && targetUrl.match( localDomain ) ) || !targetUrl.match( 'http:' ) ) {
				touchJS( this ).unbind( 'click' ).bind( 'click', function( e ) {
					var myTargetUrl = touchJS( this ).attr( 'href' );
					window.location = myTargetUrl;  
					e.preventDefault();	
					e.stopImmediatePropagation();
				});
			}
		}).bind( touchStartOrClick, function( e ) {
			if ( ( unSupportedLink && !targetUrl.match( localDomain ) ) || targetUrl.match( 'http:' ) ) {
	       	var confirmForExternal = confirm( WPtouch.external_link_text + ' \n' + WPtouch.open_browser_text );
				if ( confirmForExternal ) {
					return true;
				} else {			
					e.preventDefault();
					e.stopImmediatePropagation();
				}
			}
		});
	}
}

function saveURL() {
	var persistenceOn = touchJS( 'body.loadsaved' ).length;
	var savedUrl = window.location.href;
	if ( persistenceOn ) {
		createCookie( 'wptouch-load-last-url', savedUrl, 365 );
	} else {
		eraseCookie( 'wptouch-load-last-url' );
	}
}

function webAppBubble_welcomeMessage() {
	if ( !WPtouchWebApp ) {	
		touchJS( '#web-app-overlay' ).show();
		touchJS( 'a#close-wa-overlay' ).bind( touchStartOrClick, function( e ) {
			dismissWebAppNotice();
			e.preventDefault();
			e.stopPropagation();
		});
		touchJS( 'a#close-msg' ).bind( touchStartOrClick, function( e ) {
			dismissWelcomeMessage();
			e.preventDefault();
			e.stopPropagation();
		});
	}
}

function dismissWebAppNotice() {
	createCookie( 'notice-bubble', 'seen', 365 );
	touchJS( '#web-app-overlay' ).fadeOut( 350 );
}

function dismissWelcomeMessage() {
	createCookie( 'wptouch_welcome', '1', 365 );
	touchJS( '#welcome-message' ).fadeOut( 350 );
}

//	if ( window.history.length < 2 ) {
//		touchJS( '#back, #forward' ).hide();
//	} else if ( window.history.length = 2 ) {
//		touchJS( '#forward' ).hide();
//	} else {	
//		touchJS( '#back, #forward' ).show();
//	}
//	  
//	  touchJS( '#back, #forward' ).bind( touchEndOrClick, function( e ) {
//		if ( touchJS( this ).attr( 'ID' ) == 'back' ) {
//		  	history.back();
//	  	} else {
//		  	history.forward();	  	
//	  	}
//	  });


/* New jQuery function popOverToggle() for popover windows */
touchJS.fn.popOverToggle = function() { 
	if ( !this.hasClass( 'open' ) ) {
		this.show().addClass( 'open' );
	} else {
		this.removeClass( 'open' ).fadeOut( 350 );
	}
}

/* New jQuery function flyInToggle() for Message/Comment Windows */
touchJS.fn.flyInToggle = function() { 
	var boxHeight = this.height() + 150;
	if ( this.hasClass( 'fly-in' ) ) {
		this.css('-webkit-transform', 'translateY(0px) rotate(0deg)' ).css( 'opacity', '1' ).css( '-webkit-transition', '350ms' );
	} else {
		this.css('-webkit-transform', 'translateY(-'+boxHeight+'px) rotate(-10deg)' ).css( 'opacity', '.5' ).css( '-webkit-transition', '350ms' );
	}
}

/* New jQuery function webkitSlideToggle() */
touchJS.fn.webkitSlideToggle = function() { 
	if ( !this.hasClass( 'slide-in' ) ) {
		this.show().addClass( 'slide-in' );
	} else {
		this.slideUp( 350 ).removeClass( 'slide-in' );
	}
}

/* Cookie Functions */

function createCookie( name, value, days ) {
	if ( days ) {
		var date = new Date();
		date.setTime( date.getTime() + ( days*24*60*60*1000 ) );
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path="+WPtouch.siteurl;
}

function readCookie( name ) {
	var nameEQ = name + "=";
	var ca = document.cookie.split( ';' );
	for( var i=0;i < ca.length;i++ ) {
		var c = ca[i];
		while ( c.charAt(0)==' ' ) c = c.substring( 1, c.length );
		if ( c.indexOf( nameEQ ) == 0 ) return c.substring( nameEQ.length, c.length );
	}
	return null;
}

function eraseCookie( name ) {
	createCookie( name,"",-1 );
}

touchJS( document ).ready( function() { doClassiciPadReady(); } );