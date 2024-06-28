<?php
/*
  Plugin Name: BTM Acton Wp Plugin
  Description: Using this plugin we can store ActOn Credentails
  Version: 1.1
  Author: Asentech
  Author URI: http://asentechllc.com/
  License: GPL v2 or higher
 */

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class btmActon {

    public function __construct($file) {
        $this->token = 'hemant'; 

        $this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
        
        require_once( 'classes/acton_class.php');
        $this->btmActonapi = new actonClass($file);
		
		add_filter('theme_page_templates', array(&$this, 'optin_webhook_select'));
		add_filter('page_template', array(&$this, 'optin_webhook_template' ));
		
        add_action('wp_ajax_update_acton', array(&$this, 'update_acton'));
        add_action('wp_ajax_nopriv_update_acton', array(&$this, 'update_acton'));
		
		add_action('wp_ajax_create_acton', array(&$this, 'create_acton'));
        add_action('wp_ajax_nopriv_create_acton', array(&$this, 'create_acton'));
		
        if (is_admin()) {
            require_once( 'classes/btmActon-admin.php');
            new btmActonAdmin($file);
        } else {
            add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
            add_shortcode('display_newsletter_list', array(&$this, 'display_e_newsletters'));
        }
    }

    function enqueue_scripts() {
        wp_deregister_script('loadadjs');
        $plugin_url = trailingslashit(plugins_url('', $plugin = __FILE__));
        wp_register_script('loadadjs', esc_url($plugin_url . 'js/interads.js'), array('jquery'), true, true);
        wp_enqueue_script('loadadjs');
        wp_localize_script('loadadjs', 'adajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
		
	    // $results = get_option("btm_acton_details");
        // $optin_template = $results['optin_template'];
        // $template_optin = array("template" => $optin_template);
        // wp_localize_script('loadadjs', 'template_optin', $template_optin);
        // wp_enqueue_script('loadadjs');
    } 

    function display_e_newsletters() { 
		ob_start();
        $user_info = wp_get_current_user();

        if (is_user_logged_in() && !empty($user_info)) {
            //Add style
            
            $user_email = $user_info->user_email;
            $user_fname = $user_info->user_firstname;
            $user_lname = $user_info->user_lastname;

            $results = get_option("btm_acton_details");
            $base_url = $results['btm_acton_end_point'];
            $client_id = $results['btm_acton_client_id'];
            $secret_key = $results['acton_secret_key'];
            $acton_user_name = $results['acton_user_name'];
            $acton_password = $results['acton_password'];
            $btm_siteid = $results['btm_siteid'];
            $acton_listid = $results['acton_listid'];
            $acton_sub_listid = $results['acton_sub_listid'];
            $_auth = array(
                'client_id' => "$client_id",
                'client_secret' => "$secret_key",
            );
            $_auth['username'] = "$acton_user_name";
            $_auth['password'] = "$acton_password";

            $get_acton_details = $this->btmActonapi->check_token();

            $access_token = $get_acton_details;
            $fields_info = $this->btmActonapi->get_fields_info($base_url, $access_token, $acton_listid, $user_email);
             

            if (empty($fields_info) || isset($fields_info['errorCode'])) {
              // $new_contact = $this->btmActonapi->create_new_contact($access_token,$base_url,$acton_listid,$user_fname,$user_lname,$user_email,$results); //For auto account creation
                 $fields_info = $this->btmActonapi->get_fields_info($base_url, $access_token, $acton_listid,$user_email);
            }
               
              
            ?>

            <form action="#" name="display_newsletters_list" method="POST">
            <?php
            foreach ($results['display_name'] AS $key => $value) { 
			
                if (!empty($value) && isset($fields_info[$results['custom_field_name'][$key]])) {
                    ?>
                        <p style="font-size: 16px;"><input class="optionsCheckbox" type="checkbox" <?php if ($fields_info[$results['custom_field_name'][$key]] == 'true') {
                        echo 'checked=checked';
                    } ?> name="all_acton_newsletters[]" value="<?php echo $results['custom_field_name'][$key]; ?>"> <label><?php echo $value; ?></label></p>
                <?php }
				elseif (!empty($value)) {  ?>
                  <p style="font-size: 16px;"><input class="optionsCheckbox" type="checkbox" <?php if ($fields_info[$results['custom_field_name'][$key]] == 'true') {
                        echo 'checked=checked';
                    } ?> name="all_acton_newsletters[]" value="<?php echo $results['custom_field_name'][$key]; ?>"> <label><?php echo $value; ?></label></p>
					
              <?php }  } ?>
			  
                <input type="hidden" id="user_email_address" name="user_email_address" value="<?php if (isset($user_email)) {
                echo $user_email;
            } ?>" >
                <input type="hidden" id="access_token" name="access_token" value="<?php if (isset($access_token)) {
                echo $access_token;
            } ?>" >
                <input type="hidden" id="base_url" name="base_url" value="<?php if (isset($base_url)) {
                echo $base_url;
            } ?>" >
                <input type="hidden" id="acton_listid" name="acton_listid" value="<?php if (isset($acton_listid)) {
                echo $acton_listid;
            } ?>" >
                <input type="button" id="save_value" class="button button-primary"  name="submit_list" value="Submit" onclick="get_acton_details();">
                <div id="spinner" style="background: url('/wp-admin/images/wpspin_light-2x.gif') no-repeat; width: 35px; height: 35px;display:none;left; margin:5px 10px;"></div>
            </form>
            
            <div id="response"></div>
            
            <?php
        } else {
            echo '<p style=" margin: 1.1rem 0;"><span id="email-notes">Please login to view your subscriptions.<span></p>';
        }
	  return ob_get_clean();
    }


    function update_acton() {
        //Getting Posted Values from Form

        $user_info = wp_get_current_user();

        if (is_user_logged_in() && !empty($user_info)){
            $user_email = $user_info->user_email;
        }
        else{
            echo "Error ocurred";
        }

        $checked_val = $_POST['data'];
        $user_email = $user_email; 
        $uncheked_opt = $_POST['uncheked'];
        $list_id = $_POST['acton_listid'];

        $_data = array();
        foreach ($checked_val AS $key => $option) {
            $_data["$option"] = 'true';
        }

        foreach ($uncheked_opt AS $key => $unchecked_option) {
            $_data["$unchecked_option"] = 'false';
        }

        $data_string = json_encode($_data);


        $update_contact_info = $this->btmActonapi->update_contact_info($list_id, $user_email, $data_string);

        if ($update_contact_info->status) {
            echo $update_contact_info->message;
        } else {
            echo "Sorry we couldn't complete your request please try again in a moment";
			echo "<!--".$update_contact_info->message."-->";
        }
        exit;
    }
	
		function create_acton() { 
			$user_email = $_REQUEST['user_email'];
			$is_inline  = $_REQUEST['inline'];
			$exit_intent = $_REQUEST['exit_intent'];

			$user_fname = '';
			$user_lname = '';
			$results = get_option("btm_acton_details");
			$base_url = $results['btm_acton_end_point'];
			$acton_listid = $results['acton_listid'];
			
			$SourceCodeField = $results['SourceCodeField'];
			if($exit_intent == true){
				$SourceCode = $exit_intent; 
			}else{    
				$SourceCode = $results['SourceCode'];
			}
			$SourceCodeInline = $results['SourceCodeInline'];
			$sourceTypeField = $results['sourceTypeField'];
			$sourceType = $results['sourceType'];
			
			
			$get_acton_details = $this->btmActonapi->check_token();
			$access_token = $get_acton_details;

			if($is_inline == 'yes') { 
			   $new_contact = $this->btmActonapi->create_new_contact($access_token,$base_url,$acton_listid,$user_fname,$user_lname,$user_email,$SourceCodeField,$SourceCode,$SourceCodeInline,$sourceTypeField,$sourceType,$results); 
			}
			else {
				$new_contact = $this->btmActonapi->create_new_contact($access_token,$base_url,$acton_listid,$user_fname,$user_lname,$user_email,$SourceCodeField,$SourceCode,$sourceTypeField,$sourceType,$results);
			}
			
			if( !empty($new_contact) ) {
			  $new_contact_array = json_decode($new_contact);
			  if( $new_contact_array->status  == 'success' &&  $new_contact_array->message  == 'Record updated' ) {
				echo "Thank you for updating your account";
			  }
			  else if ($new_contact_array->status  == 'success' &&  $new_contact_array->message  == 'Record inserted' ) {
				echo "Thank You For Subscribing";
			  }
			  else {
				exit;
			  }
		   } 
        exit;  
    }
	
	/*template call for plugin folder*/
	function optin_webhook_template( $page_template ){
		if ( get_page_template_slug() == '/template-optin-webhook.php' ) {
			$page_template = dirname( __FILE__ ) . '/template-optin-webhook.php';
		} 
		return $page_template;
	}
	
	function optin_webhook_select( $post_templates ) { 
		$post_templates['/template-optin-webhook.php'] = "Optin Webhook";
		return $post_templates;
	}

}

$start = new btmActon(__FILE__);
?>
