/*
 * legendary-admin.js
 */

var LD = {};
LD.errorOut = function(msg, context) {
	context = typeof context == "string" && context != null ? context : "list";
	jQuery('[ldl-lookup="'+context+'"] .ldl-error').html( msg );
	jQuery('[ldl-lookup="'+context+'"]').attr( "ldl-status", "error" );
	
}
LD.getProjectLists = function() {
	var eKey = jQuery("#ldl_ekey").val();
	var aKey = jQuery("#ldl_akey").val();
	var host = jQuery("#ldl_host").val();
	if ( eKey == "" ) return LD.errorOut( "Enter a valid EKEY!" );
	if ( aKey == "" ) return LD.errorOut( "Enter a valid AKEY!" );
	if ( host == "" ) return LD.errorOut( "Enter a valud Host URL!" );
	var listSelector = jQuery("select#ldl_projectcenter_listid");
	var listValue = jQuery("#ldl_projectcenter_listid_value");
	LD.errorOut( "Loading ..." );
	jQuery.ajax({
		method:"POST",
		url:"/wp-admin/admin-ajax.php?action=ldl_relay",
		data:'admin_host='+host+'&admin_ekey='+eKey+'&admin_akey='+aKey+'&MCORE={"MODE":"LISTSEARCH", "META":{"RECTYP":"Project"}}',
		dataType:"json",
		success:function(data) {
			try {
				if ( typeof data.ERROR == "string" )  throw data.ERROR;
				if ( data.LISTCOUNT == 0 ) throw "Zero Project Lists found. Create a suitable list in your Legendary Database and try again";
				listSelector.empty();
				var selVal = listValue.val();
				for ( var i=0; i< data.LISTS.length; i++ ) {
					var list = data.LISTS[i];
					var sel = selVal*1 == list.LISTID*1 ? ' selected' : "";
					listSelector.append( '<option value="'+list.LISTID+'"'+sel+'>'+list.NAME+'</option>' );
				}
				listValue.val( listSelector.val() );
				jQuery('[ldl-lookup="list"]').attr( "ldl-status", "success" );
			} catch ( e ) {
				LD.errorOut( e );
			}
		}

	});
}

LD.getTasks = function() {
	var eKey = jQuery("#ldl_ekey").val();
        var aKey = jQuery("#ldl_akey").val();
        var host = jQuery("#ldl_host").val();
        if ( eKey == "" ) return LD.errorOut( "Enter a valid EKEY!" );
        if ( aKey == "" ) return LD.errorOut( "Enter a valid AKEY!" );
        if ( host == "" ) return LD.errorOut( "Enter a valud Host URL!" );
        var qkeySelector = jQuery("select#ldl_qkey");
	var qkeyValue = jQuery("#ldl_qkey_value");
	LD.errorOut( "Loading ...", "qkey" );
	jQuery.ajax({
                method:"POST",
                url:"/wp-admin/admin-ajax.php?action=ldl_relay",
                data:'admin_host='+host+'&admin_ekey='+eKey+'&admin_akey='+aKey+'&MCORE={"MODE":"TASKSEARCH"}',
                dataType:"json",
                success:function(data) {
                        try {
                                if ( typeof data.ERROR == "string" )  throw data.ERROR;
                                if ( data.length == 0 ) throw "Zero Tasks found. Create a suitable Task in your Legendary Database and try again";
                                qkeySelector.empty();
				qkeySelector.append( '<option value=""></option>' );
                                var selVal = qkeyValue.val();
                                for ( var i=0; i< data.length; i++ ) {
                                        var task = data[i];
                                        var sel = selVal == task.QKEY ? ' selected' : "";
                                        qkeySelector.append( '<option value="'+task.QKEY+'"'+sel+'>'+task.NAME+'</option>' );
                                }
                                qkeyValue.val( qkeySelector.val() );
				jQuery('[ldl-lookup="qkey"]').attr( "ldl-status", "success" );
                        } catch ( e ) {
                                LD.errorOut( e, "qkey" );
                        }
                }

        });
}

LD.init = function() {
	jQuery("#ldl_ekey, #ldl_akey, #ldl_host").on( "change", function() {
	//	LD.getProjectLists();
		LD.getTasks();
	} );
	jQuery("select#ldl_projectcenter_listid").on( "change", function(e) {
		jQuery("#ldl_projectcenter_listid_value").val( jQuery(e.currentTarget).val() );
	});
	jQuery("select#ldl_qkey").on( "change", function(e) {
                jQuery("#ldl_qkey_value").val( jQuery(e.currentTarget).val() );
        });
	jQuery("#ldl_options_master").on( "click", 'input[type="radio"]', function(e) {
		var tgt = jQuery(e.currentTarget);
		var v = tgt.val();
		var lp = jQuery("#ldl_landing_pg");
		var opts = jQuery("#ldl_options" );
		if ( v == 2 ) {
			lp.hide();
			opts.hide();
		} else if ( v == 1 ) {
			opts.hide();
			lp.show();
		} else {
			opts.show();
			lp.show();
		}
	});
	jQuery('#ldl_options_master input[type="radio"]:checked').click();
	//LD.getProjectLists();
	LD.getTasks();
}
