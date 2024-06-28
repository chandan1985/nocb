
if ( typeof $ == "undefined" || $ == null ) {
	var $ = jQuery;
}

function ldl_dynamic_search( doDownload, premiumAccess ) {
        doDownload = typeof doDownload == "boolean" ? doDownload : false;
	premiumAccess = typeof premiumAccess == "boolean" ? premiumAccess : false;
	var searchParms = [];
	var aFieldPair = jQuery('form[ldl-keypair]').attr( "ldl-keypair" ).split("|");
        var guidNode = jQuery("#ldl-custom-search-toggle");
	var recTyp = guidNode.val()=="company"?"Business":"Person";
	var containerFld = recTyp == "Business" ? aFieldPair[0] : aFieldPair[1];

	jQuery('.ld-inp-select').each(function() {
            let key = jQuery(this).attr('name').replace("[]","");
            let labels = jQuery(this).select2('data');
		let rectyps = jQuery(this).attr("ldl-rectyps");
            let lbls = ld_sp_lbls(labels);
		//filter.ROWS.push({MOD:"REQ", VAL:'firm__global__hq__yesno_static:y', KEY:"employeeat-leadership-set_static"});	
            if (lbls.length !== 0) {
                if ( rectyps.indexOf( recTyp ) == -1 ) {
			searchParms.push({MOD:"REQ", VAL:key.replace(/-/g, '__')+"_static:("+lbls+")", KEY:containerFld+"_static"});
		} else {
			searchParms.push({MOD: "REQ", VAL: lbls, KEY: key + "_static"});
		}
            }
        });

        jQuery('.ld-inp-text').each(function() {
            	let key = jQuery(this).attr('name');
            	let val = jQuery(this).val().trim();
		let rectyps = jQuery(this).attr("ldl-rectyps");
		if (val) {
			if ( rectyps.indexOf( recTyp ) == -1 ) {
				searchParms.push({MOD:"REQ", VAL:key.replace(/-/g, '__')+"_static:"+val, KEY:containerFld+"_static"});
			} else {
                		searchParms.push({MOD: "REQ", VAL: val, KEY: key +"_static"});
			}
            	}

        });

        jQuery('.ld-numeric-range').each(function() {
		let key = jQuery(this).data('fldkey');
		let low = jQuery(this).find('.ld-numeric-low').val();
		let high = jQuery(this).find('.ld-numeric-high').val();
		let rectyps = jQuery(this).attr("ldl-rectyps");

		if (low || high) {
			if ( rectyps.indexOf( recTyp ) == -1 ) {
				if ( !low ) low = -99999999999999999;
				if ( !high ) high = 99999999999999999;
                                searchParms.push({MOD:"REQ", VAL:key.replace(/-/g, '__')+"_static:["+low+" TO "+high+"]", KEY:containerFld+"_static"});
			} else {
				searchParms.push({MOD: "REQ", VAL: "range", FROM: low, TO: high, KEY: key +"_static"});
			}
		}
        });

	var filter = {ROWS:searchParms};
	jQuery("#ld-download-btn, #ld-search-btn").text( "Working ..." );
        jQuery("#ld-download-btn, #ld-search-btn").prop( "disabled", true );
        if ( jQuery("#ldatacustomcompanysearch-main-content").length == 0 ) {
		if ( doDownload ) {
			var listId = jQuery("#ldl-custom-search-toggle option:selected").attr("ld-list-id");
			var activeListData = {listId:listId, filter:(filter.ROWS.length == 0 ? null : filter)};
			var activeList = {listId: listId, postData:function() { return activeListData; } };
			ld_search_download( (premiumAccess  ? "DOWNLOADLIST" : "CHECKOUT"), activeList );
		} else {
                	jQuery("#ldl-posted-search-filter").val( JSON.stringify(searchParms) );
			jQuery("#ldl_custom_search_form").submit();
		}
        } else {
		var guidId = "ldatacustom"+guidNode.val()+"search";
                var activeList = $MYTHYR.local.activeLists[ guidId ];
                activeList.filter = (filter.ROWS.length == 0 ? null : {toData:function() { return filter; },toJson:function() {return JSON.stringify(filter);}});
                if ( doDownload ) {
			ld_search_download( (premiumAccess ? "DOWNLOADLIST" : "CHECKOUT"), activeList );
		} else {
			activeList.build();
		}
        }
        console.log(searchParms);
}

function ld_search_download(mod, activeList) {
	/*CHECKOUT or DOWNLOADLIST*/
	var req = 'MYTHYR={"EKEY":"@@EKEY@@", "MODE":"'+mod+'"';
	req += ', "ORDER":{"ORIGINURL":"'+top.window.location.href+'"';
	if ( $MYTHYR.activeOrderKey != null ) {
		req += ',"ORDERKEY":"'+$MYTHYR.activeOrderKey+'"';
	}
	req += '}';
	/*if ( typeof $MYTHYR.host == "undefined" ) {
		$MYTHYR.host = "/wp-admin/admin-ajax.php?action=ldl_relay&mode=estore&url=/";
		$MYTHYR.libHost = jQuery("#mythyr-host-container").html()+"/";
	}*/
	var getHost = typeof $MYTHYR.libHost == "string" ? $MYTHYR.libHost : $MYTHYR.host;
	if ( mod == "CHECKOUT" ) {
		
		req += ', "LIST":{"LISTID":'+activeList.listId+'}';
		
	} else if ( mod == "DOWNLOADLIST" ) {
		/* force the local host since it will need to be enhanced before it gets to the server */
		getHost = $MYTHYR.host;
	}
	req += ', "DASHLET":'+JSON.stringify(activeList.postData());
	req += '}';
	
	jQuery.ajax({
		success:jQuery.proxy(function(dat) {
			if ( typeof dat.JOB == "object" ) {
				this.jobWatcher = new $MYTHYR.jobWatcher(dat.JOB.JOBID, activeList);
				this.jobWatcher.complete = jQuery.proxy(function(data) {
					$MYTHYR.miniConsole.progress( "Complete", 1, this.activeList );
					$MYTHYR.miniConsole.progress.reset(this.activeList);
					ld_reset_buttons();
					if ( typeof data.CART == "object" ) {
						$MYTHYR.local.miniCart.build( data.CART, this.activeList );
					} else if ( typeof data.FILENAME == "string" ) {
						$MYTHYR.jobWatcher.retrieveFile( data.FILENAME );
					}
				}, this.jobWatcher );
				this.jobWatcher.status();
				
			}
			if ( typeof dat.ORDERKEY == "string" ) {
				$MYTHYR.activeOrderKey = dat.ORDERKEY;
				$MYTHYR.setCookie( "mythyr-order-key", dat.ORDERKEY, 2 );
			}
			if ( typeof dat.ERROR == "string" ) {
				ld_reset_buttons();
				$MYTHYR.miniConsole.errorOut( dat.ERROR, activeList );
				return;
			}
			if ( typeof dat.CART == "object" ) $MYTHYR.local.miniCart.build( dat.CART, activeList );
			if ( typeof dat.FILENAME == "string" ) {
				ld_reset_buttons();	
				$MYTHYR.jobWatcher.retrieveFile( dat.FILENAME );
			}
		}, this ),
		failure:function(dat) {console.log("error!");console.log(dat);},
		method:"POST",
		data:req,
		url:getHost+"ajax",
		datatype:"json"
	});
}

function ld_reset_buttons() {
	jQuery("#ld-search-btn").text( "Search" );
	jQuery("#ld-download-btn").text( "Download" );
	jQuery("#ld-download-btn, #ld-search-btn").prop( "disabled", false );
}


jQuery(document).ready(function() {
    var adapter = jQuery.fn.select2.amd.require("select2/selection/customSelectionAdapter");
    jQuery('.ld-inp-select').each(async function(i) {
        var n = jQuery(this).attr('name').replace("[]","");
	var e = '.ld-lbl-selections_' + n;
        try {
            var label_data = await getLabelData(n);
            
            if ( label_data.length == 0 ) throw "Error - No labels found";
            jQuery(this).select2({
                selectionAdapter: adapter,
                selectionContainer: jQuery(e),
                multiple: true,
                placeholder: 'Select a value',
                data: label_data,
            });
   		var postData = jQuery(this).attr("ld-posted-val");
		if ( postData != null ) {
			console.log( "post data: "+postData );
			var ar = postData.split("`");
			jQuery(this).val(ar).trigger('change');
		} 
        } catch(e) {
console.log(e);
            var msg = typeof e === 'object' ? e.statusText : e;
            jQuery(".ld-lbl-err[data-fldname='" + jQuery(this).attr('name') + "']").html(msg);
        }

    });

    jQuery('#ld-search-btn').click(ldl_dynamic_search);
    	var downloadBtn = jQuery("#ld-download-btn");
	var premAccess = downloadBtn.attr( "ld-premium-access" ) == "true";
	downloadBtn.on( "click", function() {
		ldl_dynamic_search( true, premAccess );
	});
	// init the search results javascript  ~~ should I only call this if the shortcode is activated?  Maybe could
    // call it from the shortcode html 
    ld_init_search_results();
	jQuery(".ld-search-form").on( "keypress", ".ld-inp-numeric", function(e) {
		if ( ld_numeric_keycode(e) ) return;	
		e.preventDefault();
		e.stopPropagation();
	});
});

function ld_numeric_keycode(e) {
	/* take an event keystroke and determine if allowable for a numeric-only field */
	var tgt = jQuery(e.currentTarget);
	var code = e.keyCode || e.charCode;
	if ( code >=48 && code <= 57 ) { //numbers
		return true;
	} else if ( code == 44 || ( code == 46 && tgt.val().indexOf(".") == -1 ) ) {
		return true; //commas, one period
	} else if ( code == 45 && tgt.val().indexOf("-") == -1 ) {
		return true; //one dash for negs
	} else if ( code == 8 || 
		(e.ctrlKey == true && (code == 118 || code == 99) ) ) { //backspace, paste, copy
		return true;
	} else {
		return false;
	}
}

function ld_sp_lbls(l_a) {

    // l_a is an object - so use a for loop
    var rv = '';
    var cnt = 0;

    for (i in l_a) {
        if (l_a[i].text) {
            rv += (cnt == 0 ? '' : ' ') + l_a[i].id.split("|")[0];
            cnt++;
        }
    }

    return rv;
}

async function getLabelData(label) {
    var r_d = {};
    await jQuery.ajax({
        method: "POST",
        url: "/wp-admin/admin-ajax.php?action=ld_search_label_lookup",
        data: {label: label},
        dataType: "json",
        success: function(data, status, xhr) {
            r_d = data;
        },
        error: function(xhr, status, error) {
            jQuery(".ld-lbl-err[data-fldname='" + jQuery(this).attr('name') + "']").html(xhr.status);
        }
    });

    return r_d;

}

function ld_init_search_results() {
	/* if any widgets -- estore or list -- on page then show the download btn */
    	if ( jQuery('[ldl-host], [ldl-lib-host]').length > 0 ) jQuery("#ld-download-btn").show();
	var listDivs = jQuery(".ldl-custom-search[mythyr-widget-guid]");
    if ( listDivs.length > 0 ) {
        listDivs.each( function() {
            var listDiv = jQuery(this);
            var listId = listDiv.attr("ldl-list-id");
            var libhost = listDiv.attr("ldl-lib-host");
            var views = listDiv.attr("ldl-chart-options");
            if ( views == null || views == "" ) views = "list";
    
            var customSearch = true;
            var guid = listDiv.attr("mythyr-widget-guid");
            var prefix = "ldata";
    
            if ( guid != null ) prefix = guid;

            var cfg = {id:listId, prefix:prefix, directory:false, dsearch:false, sorter:false, hideTitle:true, key:"@AKEY@", libhost:libhost,  host:"/wp-admin/admin-ajax.php?action=ldl_relay&mode=estore&url=", views:views.split(",")};
            if ( guid != null ) {
                cfg.guid = guid;
            }
            if ( guid != null ) {
                cfg.customize = function(cfg, handle, console, activeList) {
		    activeList.onViewRefresh = jQuery.proxy(function() {
			jQuery( this.mainHandle+' span.ldl-reccount' ).remove();
                        if ( typeof this.reccount != "undefined" ) {
                            var recs = ( this.guid == "ldatacustomcompanysearch" ? "businesses" : "executives" );
                            jQuery( '<span class="ldl-reccount">Found: '+LDATA.utils.formatNumber(this.reccount,"#,###")+' '+recs+'</span>' ).insertAfter(this.mainHandle+' [scroller-which="next"]' );
                        }
			ld_reset_buttons();
                    }, activeList);
                    if ( activeList.filter == null ) {
			var postFilter = jQuery("#ldl-posted-search-filter").val();
			if ( postFilter.length > 0 ) {
				var filter = {ROWS:JSON.parse( postFilter.replace(/\\/g, "") )};
				activeList.filter = (filter.ROWS.length == 0 ? null : {toData:function() { return filter; },toJson:function() {return JSON.stringify(filter);}});	
			} else {
                        	throw "No filter!";
                        	return; //do NOT exec build()!
			}
                    }
                }
            }
            
            //console.log('widget: '+JSON.stringify(cfg) );

            $MYTHYR.widget("list",cfg);
            
        } );
    
        var customToggle = jQuery('#ldl-custom-search-toggle');
    
        if ( customToggle != null && customToggle.length > 0 ) {
            customToggle.on( "change", function(e) {
                var tgt = jQuery(e.currentTarget);
    
                jQuery( '[custom-search-view]' ).attr( "custom-search-view", tgt.val() );
		ldl_dynamic_search();
            });
        }
    }
    
}
