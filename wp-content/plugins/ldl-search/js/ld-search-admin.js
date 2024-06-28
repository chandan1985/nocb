/*
 * ld-search-admin.js
 */


var LD_s = {};
LD_s.r_d = [];
LD_s.max_fields = 5;

LD_s.getFieldPairs = function(selectNode, valHandle, eKey, aKey, host) {
	jQuery.ajax({
                method:"POST",
                url:"/wp-admin/admin-ajax.php?action=ldl_relay",
                data:'admin_host='+host+'&admin_ekey='+eKey+'&admin_akey='+aKey+'&MYTHYR={"MODE":"INVERSEFIELDSEARCH"}',
                dataType:"json",

                success:function(data) {
                        try {
                                if ( typeof data.ERROR == "string" )  throw data.ERROR;
                                if ( data.length == 0 ) throw "Zero field pairs found. Notify your database administrator!";

                                selectNode.empty();
				for ( var d=0; d<data.length; d++ ) {
					var opt = data[d];
					var key = opt.BUSINESS_FIELD.KEY + "|" + opt.PERSON_FIELD.KEY;
					var nam = opt.BUSINESS_FIELD.NAME + " / " + opt.PERSON_FIELD.NAME;
					var sel = jQuery("#s_"+valHandle).val() == key ? ' selected' : "";
					selectNode.append( '<option value="'+key+'"'+sel+'>'+nam+'</option>' );
				}
                		jQuery("#s_"+valHandle).val( selectNode.val() );

                                jQuery('[ldl-lookup="'+valHandle+'"]').attr( "ldl-status", "success" );

                        } catch ( e ) {
				console.log(e);
                                LD_s.errorOut( e, valHandle );
                        }
                }

        });
}

LD_s.getLists = function(recType, selectNode, eKey, aKey, host) {
	var listValue = jQuery("#s_" + recType + "_listid_value");

	jQuery.ajax({
		method:"POST",
		url:"/wp-admin/admin-ajax.php?action=ldl_relay",
		data:'admin_host='+host+'&admin_ekey='+eKey+'&admin_akey='+aKey+'&MCORE={"MODE":"LISTSEARCH", "META":{"RECTYP":"' + recType + '"}}',
		dataType:"json",

		success:function(data) {
			try {
				if ( typeof data.ERROR == "string" )  throw data.ERROR;
				if ( data.LISTCOUNT == 0 ) throw "Zero" + recType + "Lists found. Create a suitable list in your Legendary Database and try again";

				selectNode.empty();
				var selVal = listValue.val();
				selectNode.append(`
					<option value="0" disabled` + (selVal == 0 ? ` selected` : ``) + `>
						Select a ` + (recType == "business" ? "Business" : "Executive") + ` List
					</option>
					`);
				
				for ( var i=0; i< data.LISTS.length; i++ ) {
                	var list = data.LISTS[i];
					var sel = selVal*1 == list.LISTID*1 ? ' selected' : "";
                    selectNode.append( '<option value="'+list.LISTID+'"'+sel+'>'+list.NAME+'</option>' );
                }
                listValue.val( selectNode.val() );
				
				jQuery('[ldl-lookup="'+recType+'-list"]').attr( "ldl-status", "success" );

			} catch ( e ) {
				LD_s.errorOut( e, recType+'-list' );
			}
		}

	});	
}

LD_s.errorOut = function(msg, context) {
	context = typeof context == "string" && context != null ? context : "list";
	jQuery('[ldl-lookup="'+context+'"] .ldl-error').html( msg );
	jQuery('[ldl-lookup="'+context+'"]').attr( "ldl-status", "error" );
	
}


async function getFields(aKey, eKey, host) {

	var r_d = [];

	await jQuery.ajax({
		method:"POST",
		url:"/wp-admin/admin-ajax.php?action=ldl_relay",
		data:'admin_host='+host+'&admin_ekey='+eKey+'&admin_akey='+aKey+'&MCORE={"MODE":"FIELDSEARCH", "FIELD":{"DATACLASS":{"TYP":"Text Label Numeric"}, "FIELDROOT":{"RECTYPS":["Business","Person"], "FLUIDITY":"static"}}}',
		dataType:"json",
		
		success: function(data) {
			try {
				console.log( data );
				/*data.forEach(e => {
					r_d.push({'id': e.KEY, 'text': e.NAME, 'data': e});
				});*/
				for ( var d=0; d<data.length; d++ ) {
					var fld = data[d];
					var recTypsArr = typeof fld.FIELDROOT.RECTYPS == "object" ? fld.FIELDROOT.RECTYPS : [];
					var recTyps = "";
					if ( recTypsArr.indexOf( "Business" ) != -1 ) recTyps = "Business";
					if ( recTypsArr.indexOf( "Person" ) != -1 ) recTyps += (recTyps.length==0?"":",") + "Person";
					r_d.push({'id':fld.KEY, 'text':fld.NAME, 'data':{TYP:fld.TYP,RECTYPS:recTyps}});
				}

			} catch (e) {
				console.log(e, 'fields');
			}
		}
	});

	return r_d;

}

function s2Init(ph) {

	jQuery('.ld-search-fields').select2({
		placeholder: ph,
		width: '400px',
		data: LD_s.r_d
	});

}


LD_s.init = async function(aKey, eKey, host) {

	LD_s.getLists('business',jQuery('#ld_search_business_list'), aKey, eKey, host);
	LD_s.getLists('person'  ,jQuery('#ld_search_person_list'), aKey, eKey, host);
	LD_s.getFieldPairs( jQuery('#ld_search_container_field_pair'), 'container-field-pairs', aKey, eKey, host );

	LD_s.r_d = await getFields(aKey, eKey, host);

	s2Init("Select up to " + LD_s.max_fields + " fields");

	jQuery('.sortable').sortable({
		update: function(event, ui) {
			updateOptionsFlds();
			saveButton();
        },
	});

	// Build out the ordered-fields area from the value stored in the options table
	// and placed in the hidden field.
	var sof_data = jQuery('#s_search-ordered-fields').val();
	if (sof_data) {
		var sof = JSON.parse(sof_data);
		sof.forEach(i => {

			// transform into object that can be used for appendSearchFld
			var obj = {id: i.fld_id, data: {TYP: i.fld_type, RECTYPS:i.fld_rectyps}, text: i.fld_name};

			// append it to the ordered-fields area
			appendSearchFld(obj);

		
		});
	}

	jQuery("select#ld_search_business_list").on( "change", function(e) {
		jQuery("#s_business_listid_value").val( jQuery(e.currentTarget).val() );
		saveButton();
	});

	jQuery("select#ld_search_person_list").on( "change", function(e) {
		jQuery("#s_person_listid_value").val( jQuery(e.currentTarget).val() );
		saveButton();
	});

	jQuery("select#ld_search_container_field_pair").on( "change", function(e) {
                jQuery("#s_container-field-pairs").val( jQuery(e.currentTarget).val() );
                saveButton();
        });

	jQuery(".ld-search-fields").on('select2:select', function(e) { 
		appendSearchFld(e.params.data);
	});


	saveButton();

}

jQuery(document).ready(function() {
	//console.log(ld_search_options);
	var eKey = jQuery("#ldl_ekey").val();
	var aKey = jQuery("#ldl_akey").val();
	var host = jQuery("#ldl_host").val();


	LD_s.init(aKey, eKey, host);

});



function disableFldOption(id) {
	// disable the option in the dropdown (because it's already been selected)
	var e_o = jQuery("option[value='" + id +"']");
	e_o.attr('disabled', 'disabled');
}

function enableFldOption(id) {
	// enable the option in the dropdown (because it's already been selected)
	var e_o = jQuery("option[value='" + id +"']");
	e_o.removeAttr('disabled');
}

function rmvFld(id) {
	var e = jQuery('[data-fld_id=' + id + ']');
	e.remove();
	enableFldOption(id);
	enableDisableSearch();
	updateOptionsFlds();

	saveButton();
}

function renameFld(id, val) {
	var e = jQuery('[data-fld_id=' + id + ']');
	if ( val.length > 0 ) {
		e.attr( "data-fld_name", val );
		e.data( "fld_name", val );
	} else {
		e.find( "input" ).val( e.attr( "data-fld_name" ) );
	}
	updateOptionsFlds();
}


function appendSearchFld(e) {
	jQuery('.ld-search-fields-data').append(
		`<div class="sortable-field-choices search-fields" 
			data-fld_id="` + e.id + `" 
			data-fld_type="` + e.data.TYP + `"
			data-fld_rectyps="` + e.data.RECTYPS + `"
			data-fld_name="` + e.text + `">
			<span class="del-fld" onclick="rmvFld('` + e.id + `');">&#x24E7</span>` 
			+ '<input type="text" class="rnam-inp" value="' + e.text + '" onchange="renameFld(\'' + e.id+ '\', this.value);" />' + 
		`</div>`
	);

	disableFldOption(e.id);
	enableDisableSearch();
	updateOptionsFlds();

	// Clear the value in the select box
	jQuery('.ld-search-fields').val('');
	jQuery('.ld-search-fields').trigger('change.select2');
	
	// saveButton Update
	saveButton();

}

function enableDisableSearch() {
	if (jQuery('.search-fields').length >= LD_s.max_fields) {
		jQuery('.ld-search-fields').prop('disabled', true);
	} else {
		jQuery('.ld-search-fields').prop('disabled', false);
	}
}

function updateOptionsFlds() {
	// define selector
	var items = jQuery('.search-fields');
	var all_items;
	if (items.length) {
		all_items = [];
		items.each(function (i) {
			var d = jQuery(this).data();
			console.log( d );
			all_items.push({fld_id: d.fld_id, fld_type: d.fld_type, fld_name: d.fld_name, fld_rectyps: d.fld_rectyps});
		})
		jQuery('#s_search-ordered-fields').val(JSON.stringify(all_items));

	} else {
		jQuery('#s_search-ordered-fields').val('');
	}

	jQuery('#s_search-ordered-fields').val(JSON.stringify(all_items));
	
}

function saveButton() {
	var sf_l = jQuery('.search-fields').length;
	if ( sf_l >= 1 && 
		jQuery("#s_business_listid_value").val() != '' &&
		jQuery("select#ld_search_person_list").val() != ''
		) {

		jQuery('#ldl-search-save-btn.button-primary').prop('disabled', false);
	} else {
		jQuery('#ldl-search-save-btn.button-primary').prop('disabled', true);
	}

	setPlaceholder(sf_l);
}

function setPlaceholder(sf_l) {
	var ph;
	
	if (sf_l == 0) {
		ph = "Select up to " + LD_s.max_fields + " fields ";
	
	} else if (sf_l == 1) {
		ph = "1 field selected";
	
	} else if (sf_l >= 2 && sf_l <= (LD_s.max_fields -1) ) {
		ph = sf_l + " fields selected";
	
	} else if (sf_l == LD_s.max_fields ) {

		ph = "Maximum number of fields selected (" + LD_s.max_fields + ")";
	
	}

	s2Init(ph);	

}
