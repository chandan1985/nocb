		jQuery( document ).ready(function() {
            jQuery("#div1").css('display','none');              
			///console.log(adajax_object.ajax_url);
        });
        
        // jQuery(window).load(function() {
                        // var checkExist = setInterval(function() {
                        // var optinvar = (typeof(template_optin) !== 'undefined' && template_optin != null && template_optin.template) ? template_optin.template : '';
                        // if(jQuery("#"+optinvar+"-form").length){
                              // console.log("Exists!");
                              // optin_handle();
                              // clearInterval(checkExist);
                           // }
                        // }, 500); // check every 500ms
                         // setTimeout(function(){ 
                            // clearInterval(checkExist);
                         // }, 8000); // check for 8 sec
        // });

        function optin_handle() {
                       // var optinvar = (typeof(template_optin) !== 'undefined' && template_optin != null && template_optin.template) ? template_optin.template : '';
                        //var selector = "."+optinvar+"-form-wrap > form";
                        //var close_selector = "."+optinvar+"-close";
                        //jQuery(selector).addClass('optinForm');
                       // jQuery(selector).submit(function() {
                        var user_email = jQuery(".optinForm input[name=email]").val();
                        if(user_email == '') {
                         jQuery("#response_act").html('<p style="font-size: 16px;"><label>This field cannot be empty</label></p>');
                         setTimeout(function() {
                                jQuery("#response_act").css('display','none');
                            }, 2000);
                        }    
                        else {
                        grep(user_email);  
                        }
                        return false;
                      //});
        }

        function get_acton_details(){
            
            var user_email = jQuery('#user_email_address').val();
            var access_token = jQuery('#access_token').val();
            var base_url = jQuery('#base_url').val();
            var acton_listid = jQuery('#acton_listid').val();
            
            var uncheked_arr = [];
            i = p = 0;
            var arr = [];
            jQuery('.optionsCheckbox:checked').each(function () {
                arr[i++] = jQuery(this).val();
            });
            
            jQuery('.optionsCheckbox:not(:checked)').each(function () {
                uncheked_arr[p++] = jQuery(this).val();
            });
            
			
            jQuery('#spinner').show();
			jQuery("#response").hide();
            jQuery.ajax({
                method:'POST',
                data : {'action':'update_acton','data':arr, 'user_email':user_email, 'uncheked':uncheked_arr,'acton_listid':acton_listid,},
                url: adajax_object.ajax_url+'?action=update_acton',
                success: function(result){
                    jQuery('#spinner').hide();
                    jQuery("#response").css('display','block');
					var result = removeNL(result);
                    if(result == 'Record inserted') {
                     jQuery("#response").html('<p style="font-size: 16px;"><label>Thank you for subscribing </label></p>');  
                    }
                    if(result == 'Record updated') {
                      jQuery("#response").html('<p style="font-size: 16px;"><label>Thank you for updating your account</label></p>');
                    }
                }
            });
        }
		
		 function removeNL(str) {
            var strClean = "";
            for (i=0; i < str.length; i++) {
                if (str.charAt(i) != '\n' &&
                    str.charAt(i) != '\r' &&
                    str.charAt(i) != '\t') {
                    strClean = strClean + str.charAt(i);
                }
            }
            return strClean;
        }
		
		function grep(user_email) { 
            jQuery('#spinner1').show();
            jQuery.ajax({
                method:'POST',
                data : {'action':'create_acton','user_email':user_email },
                url: adajax_object.ajax_url+'?action=create_acton',
                success: function(result){
                    jQuery('#spinner1').hide();
                    jQuery("#response_act").css('display','block');
                    jQuery("#response_act").html('<p style="font-size: 16px;"><label>'+result+'</label></p>');
                    setTimeout(function(){ 
                      // jQuery(close_selector).trigger('click');
					  jQuery("[title=Close]").trigger('click');
                     }, 2000);
                }
            });
        }