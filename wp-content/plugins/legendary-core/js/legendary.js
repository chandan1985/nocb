/*
 * legendary.js
 */
var LD = {};
if ( typeof $ == "undefined" || $ == null ) {
	var $ = jQuery;
}
/* Helper method for slugifying strings */
LD.slugify= function(string) {
  const a = 'àáâäæãåāăąçćčđďèéêëēėęěğǵḧîïíīįìłḿñńǹňôöòóœøōõőṕŕřßśšşșťțûüùúūǘůűųẃẍÿýžźż·/_,:;'
  const b = 'aaaaaaaaaacccddeeeeeeeegghiiiiiilmnnnnoooooooooprrsssssttuuuuuuuuuwxyyzzz------'
  const p = new RegExp(a.split('').join('|'), 'g')

  return string.toString().toLowerCase()
    .replace(/\s+/g, '-') // Replace spaces with -
    .replace(p, c => b.charAt(a.indexOf(c))) // Replace special characters
    .replace(/&/g, '-and-') // Replace & with 'and'
    .replace(/[^\w\-]+/g, '') // Remove all non-word characters
    .replace(/\-\-+/g, '-') // Replace multiple - with single -
    .replace(/^-+/, '') // Trim - from start of text
    .replace(/-+$/, '') // Trim - from end of text
}

/* Currently unused method for opening details from inside a standard listw widget */
LD.openDetails = function(node, basepg) {
	var recId = $(node).attr("rec-id");
	var rNam = $(node).attr("rec-name");
	window.location.href='/'+basepg+'/?/view/'+recId+'/'+LD.slugify(rNam);
}

/* init page method called inline from plugins to instantiate the page */
LD.initPage = function() {
	var basepg = $('[ldl-basepg]').attr("ldl-basepg");
	/* overrides the native MYTHYR method for refactoring internal links based on the list object -- this makes the links
 	 * internally consistent within the wordpress plugin */
	$MYTHYR.buildListLink = function(list) {
                return '/'+basepg+'/?/d/'+list.LISTID+'/'+LD.slugify( list.NAME );
        }
	/* find all instances of attribute [ldl-list-id] and use as the basis for creating an interactive list on the page, using the native MYTHYR list
 	 * widget -- see constructor $MYTHYR.widget */
	$( '[ldl-list-id]' ).each( function() {
		var node = $(this);
		var listId = node.attr( "ldl-list-id" );
		var host = node.attr( "ldl-host" );
		var views = node.attr("ldl-views");
		var dsearch = node.attr( "ldl-dsearch" ) == "true";
		var aViews = [];
		var allowedOpts = ["list","pie","series","map"];
		if ( views != null && views.length > 0 ) {
			var ar = views.split( "," );
			for ( var i=0; i<ar.length; i++ ) {
				var opt = ar[i];
				if ( allowedOpts.indexOf( opt ) == -1 ) continue;
				if ( opt == "map" && typeof google == "undefined" ) continue;
				aViews.push( opt );
			}
		}
		if ( aViews.length == 0 ) {
			aViews = ["list"];
		}
		var cfg = {id:listId, prefix:"ldldata", directory:false, dsearch:dsearch, sorter:false, hideTitle:false, key:"", libhost:host,  host:"/wp-admin/admin-ajax.php?action=ldl_relay&mode=estore&url=", views:aViews, ecomAjaxMethod:"POST"};
		var qKey = node.attr( "ldl-qkey");
		if ( typeof qKey == "string" && qKey != null && qKey.length > 0 ) {
			cfg.q={key:qKey};
		}
		$MYTHYR.widget("list",cfg);
	});
	/* find all instances of attribute [ldl-estore] and use as the basis for creating an interactive e-store  on the page, using the nativ
 	 * MYTHYR e-store widget -- see constructor $MYTHYR.estore */
	$( '[ldl-estore]').each( function() {
		var node = $(this);
		var host = node.attr("ldl-host");
		var cfg = {prefix:"ldldata", libHost:host};
		var qKey = node.attr( "ldl-qkey");
		cfg.esearch = true;
                if ( typeof qKey == "string" && qKey != null && qKey.length > 0 ) {
                        cfg.q={key:qKey};
                }
		cfg.ajaxMethod="POST";
		cfg.host="/wp-admin/admin-ajax.php?action=ldl_relay&mode=estore&url=";
		$MYTHYR.estore(cfg);	
	});
	/* find all instances of attribute ldl-resolve and use as the basis for hyperlinking names of published businesses and executives found
 	 * within the body of a post on the page -- this is controlled in the admin tool under Enhanced ... */
	$( '[ldl-resolve]').each( function() {
		var node = $(this);
		var host = node.attr("ldl-host");
		var postId = node.attr( "ldl-postid" );
		var ttl = node.attr("ldl-title");
		var dt = node.attr( "ldl-date" );
                var cfg = {prefix:"ldlresolve", host:host, pageId:postId, pageTitle:ttl, pageDt:dt, firstReferenceOnly:true };
		$MYTHYR.resolve(cfg);
	});
}

