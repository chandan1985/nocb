/* WPtouch Pro Classic JS */
/* This file holds all the default jQuery & Ajax functions for the classic theme */
/* Description: JavaScript for the Classic theme */
/* Expected jQuery version: 1.3.2 */

var touchJS = jQuery.noConflict();
var WPtouchWebApp = window.navigator.standalone;

/* Get out of frames! */
if ( !WPtouchWebApp ) {
	if ( top.location!= self.location ) { 
		top.location = self.location.href
	}
}

function doClassicReady() {

	/*  Header #tab-bar tabs */
	touchJS( function() {
	    var tabContainers = touchJS( '#menu-container > div' );
	     var loginTab = touchJS( '#menu-tab5' );
	
	    touchJS( '#tab-inner-wrap-left a' ).click( function () {
	        tabContainers.hide().filter( this.hash ).opacityToggle( 350 );
	    	touchJS( '#tab-inner-wrap-left a' ).removeClass( 'selected' );
	   		touchJS( this ).addClass( 'selected' );
	        return false;
	    }).filter( ':first' ).click();
	});

	/* Header Menu Toggle (toggle button animation and menu showing) */
	touchJS( 'a#header-menu-toggle' ).unbind( 'click' ).click( function() {
		touchJS( '#main-menu' ).opacityToggle( 350 );
		touchJS( this ).toggleClass( 'menu-toggle-open' );
		return false;
	});	

	/* Toggling the search bar from within the menu */
	touchJS( 'a#tab-search' ).unbind( 'click' ).click( function() {
		
		touchJS( '#search-bar' ).toggleClass( 'show-search' );
		touchJS( this ).toggleClass( 'search-toggle-open' );

		return false;
	});	

	/* Filter parent link href's and make them toggles for thier children */
	touchJS( '#main-menu' ).find( 'li.has_children ul' ).hide();
	
	touchJS( '#main-menu ul li.has_children > a' ).unbind( 'click' ).click( function() {
		touchJS( this ).parent().children( 'ul' ).opacityToggle( 350 );
		touchJS( this ).toggleClass( 'arrow-toggle' );
		touchJS( this ).parent().toggleClass( 'open-tree' );
		return false;
	});

	/* If Prowl Message Sent */
	if ( touchJS( '#prowl-message' ).length ) {
		setTimeout( function() { touchJS( '#prowl-message' ).fadeOut( 350 ); }, 2500 );
	}

	/* 2.0.8.1 try to make imgs and captions nicer in posts */	
	if ( touchJS( '.single' ).length ) {
		touchJS( '.content img, .content .wp-caption' ).each( function() {
			if ( touchJS( this ).width() <= 250 && touchJS( this ).width() >= 100 ) {
				touchJS( this ).addClass( 'aligncenter' );
			}
		});
	}

	/* 2.0.8.2 take care of pesky plugin image protect stuff */	
	if ( touchJS( '.single .p3-img-protect' ).length ) {
		touchJS( '.single .p3-img-protect' ).each( function() {
			touchJS( '.p3-overlay' ).remove();
			var insideContent = touchJS( this ).html();
			touchJS( this ).replaceWith( insideContent );
		});
	}

	/* Single post page share menu */
	touchJS( 'a#share-post' ).unbind( 'click' ).click( function() {
		touchJS( '#inner-ajax #share-absolute' ).opacityToggle( 350 ).viewportCenter();
	});	

	touchJS( 'a#share-close' ).unbind( 'click' ).click( function() {
		touchJS( '#inner-ajax #share-absolute' ).opacityToggle( 350 );
		return false;
	});	
	
	touchJS('li#instapaper a').unbind( 'click' ).click( function() {
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
		return false;
	});

	var shareOverlay = touchJS( '#share-absolute' ).get(0);
	if ( shareOverlay ) {
		shareOverlay.addEventListener( 'touchmove', classicTouchMove, false );	
		touchJS( '#email a' ).click( function() {
			touchJS( 'a#share-close' ).click();
			return true;
		});
		var windowHeight = touchJS( window ).height() + 100 + 'px';
		touchJS( '#share-absolute' ).css( 'height', windowHeight );
	}
	
	/* Comments */
	/* Add a rounded top left corner to the first gravatar in comments, remove double border */
	touchJS( '.commentlist li :first, .commentlist img.avatar:first' ).addClass( 'first' );

	touchJS( 'a.com-toggle' ).unbind( 'click' ).bind( 'click', function() {
		classic_showhide_response();
		return false;
	});
		
	/* Detect window width and add corresponding 'portrait' or 'landscape' classes onload */
	if ( touchJS( window ).width() <= 320 ) { 
		touchJS( 'body' ).addClass( 'portrait' );
	} else {
		touchJS( 'body' ).addClass( 'landscape' );
	}

	/* Detect orientation change and add or remove corresponding 'portrait' or 'landscape' classes */
	window.onorientationchange = function() {
		var orientation = window.orientation;
			switch( orientation ) {
				//Portrait
				case 0:
				case 180:
				touchJS( 'body' ).addClass( 'portrait' ).removeClass( 'landscape' );
				break;
				//Landscape
				case 90:
				case -90:
				touchJS( 'body' ).addClass( 'landscape' ).removeClass( 'portrait' );
				break;
				default:
				touchJS( 'body' ).addClass( 'portrait' ).removeClass( 'landscape' );				
			}
	}
	
/* Ajaxify commentform */
	var postURL = document.location;
	var CommentFormOptions = {
		beforeSubmit: function( e ) {
			touchJS( '#commentform textarea' ).addClass( 'loading' );			
		},
		success: function( e ) {
			touchJS( '#commentform textarea' ).removeClass( 'loading' ).addClass( 'success' );			
			alert( WPtouch.comment_success );
			setTimeout( function () { 
				touchJS( '#commentform textarea' ).removeClass( 'success' );
			}, 1500 );
		},
		error: function( e ) {
			touchJS( '#commentform textarea' ).removeClass( 'loading' ).addClass( 'error' );
			alert( WPtouch.comment_failure );
			setTimeout( function () { 
				touchJS( '#commentform textarea' ).removeClass( 'error' );
			}, 3000 );
		},
		resetForm: true,
		timeout:   10000
	} 	//end options
	
	touchJS( '#commentform' ).ajaxForm( CommentFormOptions );
	
	webAppOnly();
	hijackPostLinks();
	loadMoreEntries();
	loadMoreComments();
	comReplyArrows();
	classicExcerptToggle();
	webAppBubble_welcomeMessage();
	
	touchJS( 'a.login-req, a.comment-reply-login' ).unbind( 'click' ).bind( 'click', function( e ) {
		touchJS( 'a#header-menu-toggle, a#tab-login' ).click();
		scrollTo(0,0,1);
		e.preventDefault();
		e.stopPropagation();
	});
			
	if ( WPtouchWebApp ) {
		touchJS( 'div.wptouch-shortcode-webapp-only' ).show();	
	} else {
	/* Hide addressBar */
		if ( !touchJS( '.single' ).length ) {
			touchJS( window ).load( function() {
			    setTimeout( scrollTo, 0, 0, 1 );
			} );
		}	
		touchJS( 'div.wptouch-shortcode-mobile-only' ).show();
		touchJS( '#web-app-overlay a' ).unbind( 'click' ).click( function() {
			dismissWebAppNotice();
			touchJS( '#web-app-overlay' ).fadeOut();
		});
	}

}
/* End Document Ready Functions */

/* New jQuery function opacityToggle() */
touchJS.fn.opacityToggle = function( speed, easing, callback ) { 
	return this.animate( { opacity: 'toggle' }, speed, easing, callback ); 
}

/* New jQuery function viewportCenter() */
touchJS.fn.viewportCenter = function() {
    this.css( 'position','absolute');
    this.css( 'top', ( touchJS( window ).height() - this.height() ) / 3 + touchJS( window ).scrollTop() + 'px' );
    this.css( 'left', ( touchJS( window ).width() - this.width() ) / 2 + touchJS( window ).scrollLeft() + 'px' );
    return this;
}

/* New jQuery function viewportBottom() */
	touchJS.fn.viewportBottom = function() {
      this.css( 'position','absolute');
      this.css( 'top', ( touchJS( window ).height() - this.height() ) + 8 + 'px' );
      this.css( 'left', ( touchJS( window ).width() - this.width() ) / 2 + touchJS( window ).scrollLeft() + 'px' );
      return this;
	}
	
function classicTouchMove( event ) {
	event.preventDefault();	
}

function webAppBubble_welcomeMessage() {
	if ( !WPtouchWebApp ) {	
		if ( touchJS( '.idevice' ).length ) {
			touchJS( '#web-app-overlay' ).viewportBottom().show();
		}

		touchJS( '#welcome-message' ).show();

		touchJS( '#close-wa-overlay' ).bind( 'click', function( e ) {
			dismissWebAppNotice();
			e.preventDefault();
			e.stopPropagation();
		});
		touchJS( 'a#close-msg' ).unbind('click').bind( 'click', function( e ) {
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

function webAppOnly() {
	if ( WPtouchWebApp ) {
		touchJS( '#switch' ).remove();
		touchJS( 'a.comment-reply-link' ).remove();
		touchJS( 'a.comment-edit-link' ).remove();
		touchJS( 'body' ).addClass( 'web-app' );
		if ( touchJS( 'body.black-translucent' ).length ) {
			touchJS( 'body.black-translucent' ).css('margin-top', '20px');
		}
	}
}

/* Get the document URL, taking into account previous AJAX requests */
function wptouchGetDocumentUrl() {
	if ( WPtouchWebApp && wptouchAjaxUrl ) {
		return wptouchAjaxUrl;	
	} 
	
	return document.location.href;	
}

function wptouchGetDocumentTitle() {
	if ( WPtouchWebApp ) {
		return prompt( WPtouch.classic_post_desc, '' );
	} else {
		return document.title;
	}
}

/* Hijack links inside .content (posts, pages) in web app mode, ask users if they want to open a browser to view */
function hijackPostLinks() {
	/* Make the menu toggles not do AJAX in webapp mode */
	touchJS( '#main-menu ul li.has_children > a, a.load-more-link' ).addClass( 'no-ajax' );
	
	/* Make Google AJAX translator links non-ajax */
	touchJS( 'a.translate_translate' ).addClass( 'no-ajax' );
	
	if ( WPtouchWebApp ) {
		/* For all external links in the menu to not have ajax */
		touchJS( 'li.force-external a' ).addClass( 'no-ajax' );

		/* Menu workaround */
		touchJS( '#main-menu ul li a img' ).click( function() {
			touchJS( this ).parent().click();		
			return false;
		});
	}
	
	var allExternalLinks = touchJS( 'a:not(.no-ajax)' );
	if ( allExternalLinks.length ) {    
	    allExternalLinks.unbind( 'click' ).click( function( e ) {
			var url = touchJS( this ).attr( 'href' );
			var isUnsupportedLink = ( 
				url.lastIndexOf( '.pdf' ) >= 0 || url.lastIndexOf( '.xls' ) >= 0 || url.lastIndexOf( '.numbers' ) >= 0 || url.lastIndexOf( '.pages' ) >= 0 || 
				url.lastIndexOf( '.mp3' ) >= 0 || url.lastIndexOf( '.mp4' ) >= 0 || url.lastIndexOf( '.m4v' ) >= 0 || url.lastIndexOf( '.mov' ) >= 0 ||
				url.indexOf( 'mailto:' ) >= 0 || url.indexOf( 'tel:' ) >= 0
			);
	      
	      	/* Check for phone numbers, email addresses, unsupported file types */
	      	if ( isUnsupportedLink ) {
	      		return true;	
	      	}
			
			if ( WPtouchWebApp ) {
				if ( touchJS( this ).hasClass( 'comment-reply-link, thdrpy, thdmang' ) ) {
					return true;	
				}

				var actualLink = touchJS( this ).attr( 'href' );
				if ( actualLink[0] == '#' ) {
					return false;
				}
			
		        var localDomain = document.domain;
				if ( ( url.match( localDomain ) && !touchJS( this ).parent().hasClass( 'email' ) ) || !url.match( 'http' ) ) {
					/* Check to see if menu is showing */
					if ( touchJS( '#main-menu' ).hasClass( 'show-menu' ) ) {
						/* Menu is showing, so lets collapse it */
						touchJS( 'a#header-menu-toggle' ).click();
					}				
					
					var hasMatch = 0;
				/* If we have pages to boot out of Web-App Mode */
					if ( typeof wptouch_ignored_urls != 'undefined' ) {
						jQuery.each( wptouch_ignored_urls, function( i, value ) {
							if ( url.match( value ) ) {
								hasMatch = 1;
							}
						});
					}						
					
					if ( hasMatch ) {
			       	var answer = confirm( WPtouch.external_link_text + ' \n' + WPtouch.open_browser_text );
						if ( answer ) {
							return true;	
						}
					} else {
						loadPage( url );
					}
						
					return false;
				} else {
					if ( touchJS( this ).parent().hasClass( 'email' ) ) {
						return true;	
					}
										
			       	var answer = confirm( WPtouch.external_link_text + ' \n' + WPtouch.open_browser_text );
					if ( answer ) {
						return true;
					} else {
						return false;
					}
				}
			} else { /* If not web app */
				
				if ( touchJS( this ).parent().hasClass( 'email' ) ) {
					touchJS( '#main-menu' ).opacityToggle( 0 );
					touchJS( 'a#header-menu-toggle' ).toggleClass( 'menu-toggle-open' );
				}
			}
	    });
	}
}

function classicExcerptToggle() {
	touchJS( 'a.excerpt-button' ).live( 'click', function() {
		touchJS( this ).toggleClass( 'open' );
		var postID = touchJS( this ).attr( "rel" );
		var parentPost = touchJS( this ).parents( "div.post" );
		if ( parentPost.length ) {
			var firstParent = touchJS( parentPost.get(0) );
			firstParent.find( 'div.content' ).opacityToggle( 350 );	
		}	
		return false;	
	});
}

function loadMoreEntries() {
var loadMoreLink = touchJS( 'a.load-more-link' );
	if ( loadMoreLink.length ) {
		loadMoreLink.unbind( 'click' ).live( 'click', function() {
			touchJS( this ).addClass( 'ajax-spinner' ).fadeOut( 2200 );
			var loadMoreURL = touchJS( this ).attr( 'rel' );
			touchJS( '#content' ).append( "<div class='ajax-page-target'></div>" );
			touchJS( 'div.ajax-page-target' ).hide().load( loadMoreURL + ' #content .post, #content .load-more-link', function() {
				touchJS( 'div.ajax-page-target' ).replaceWith( touchJS( 'div.ajax-page-target' ).html() );				
				if ( WPtouchWebApp ) { hijackPostLinks(); }
			});
			return false;
		});	
	}	
}

function loadMoreComments() {
	var loadMoreLink = touchJS( 'ol.commentlist li.load-more-comments-link a' );
	if ( loadMoreLink.length ) {
		loadMoreLink.unbind( 'click' ).click( function() {
			touchJS( this ).addClass( 'ajax-spinner' );
			var loadMoreURL = touchJS( this ).attr( 'href' );
			touchJS( 'ol.commentlist' ).append( "<div class='ajax-page-target'></div>" );
			touchJS( 'div.ajax-page-target' ).hide().load( loadMoreURL + ' ol.commentlist > li', function() {
				touchJS( 'div.ajax-page-target' ).replaceWith( touchJS( 'div.ajax-page-target' ).html() );				
				setTimeout( function() { loadMoreLink.fadeOut( 350 ); }, 500 );
 				if ( WPtouchWebApp ) { hijackPostLinks(); webAppOnly(); }
				loadMoreComments();
			});
			return false;
		});	
	}	
}

function comReplyArrows() {
	var comReply = touchJS( 'ol.commentlist li li .comment-top' );
	touchJS.each( comReply, function() {
		touchJS( comReply ).prepend( '<div class="com-down-arrow"></div>' );
	});
}

function classic_showhide_response() {
	touchJS( 'ol.commentlist' ).toggleClass( 'shown' );
	touchJS( 'ol.commentlist' ).toggleClass( 'hidden' );
	touchJS( 'img#com-arrow' ).toggleClass( 'com-arrow-down' );
}

/* Load domain urls with Ajax (works with hijackPostLinks(); ) */
var wptouchAjaxUrl = '';
function loadPage( url ) {
	touchJS( 'body' ).append( '<div id="progress"></div>' );
	touchJS( '#progress' ).viewportCenter();
	touchJS( '#outer-ajax' ).load( url + ' #inner-ajax', function( allDone ) {
		wptouchAjaxUrl = url;
		touchJS('#progress').remove();
	  	createCookie( 'wptouch-load-last-url', url, 365 );
		scrollTo( 0, 0 );
		doClassicReady();
	} );
}

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
	var ca = document.cookie.split(';');
	for( var i=0;i < ca.length;i++ ) {
		var c = ca[i];
		while ( c.charAt( 0 )==' ' ) c = c.substring( 1,c.length );
		if ( c.indexOf( nameEQ ) == 0 ) return c.substring( nameEQ.length,c.length );
	}
	return null;
}

touchJS( document ).ready( function() { doClassicReady(); } );