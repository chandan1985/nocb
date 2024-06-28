<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class btmActonAdmin {

    public function __construct($file) {
        add_action('admin_init', array(&$this, 'register_settings'));
        add_action('admin_menu', array(&$this, 'BTM_Acton_plugin_menu'));
    }

    function BTM_Acton_plugin_menu() {
        add_menu_page('BTM Acton Plugin Settings', 'BTM Acton Settings', 'manage_options', 'btm_acton_settings', array(&$this, 'btm_acton_settings_page'), 'dashicons-admin-generic');
    }

    /*
     * Register & set up plugin options page via settings API
     */

    function register_settings() {
        register_setting('btm_acton_details', 'btm_acton_details', array(&$this, 'validate_options'));
    }

    function validate_options($input) {

        return $input;
    }

    function btm_acton_settings_page() {
        global $wpdb;
        //wp_enqueue_style('thickbox');
        wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
       // wp_enqueue_media();
       // wp_enqueue_script('media-grid');
       // wp_enqueue_script('media');
        //wp_enqueue_script('thickbox');
        wp_register_script('interstitial-ad-script', plugins_url('../js/admin-script.js', __FILE__), array('jquery', 'jquery-ui-sortable'),false, false);
       
        wp_enqueue_script('interstitial-ad-script');


        $table_name = $wpdb->prefix . 'options';
        $my_fields = get_option('btm_acton_details');
        ?>
<div class="main-settings-div">  
<form action="options.php" enctype="multipart/form-data" method="post" class="detail_form">  
            <?php settings_fields('btm_acton_details'); ?>	
            <div><h1>ActOn Settings</h1></div>
            <table> 
                <tbody> 
                    <tr class="form-field">
                        <td colspan="2">
                <table cellspacing="2" cellpadding="5" style=""  class="form-table form-field_white">
                    <tbody>
                        <tr class="bordered"><th><h2 class="settings_space"> Global Settings </h2></th></tr>
                        <tr class="form-field">
                            <th valign="top" scope="row">
                                <label for="btm_acton_endpoint" class="lable_font_size"><?php _e('ActOn EndPoint', 'btmacton_details'); ?></label>
                            </th>
                            <td>
                                <input type="text" class="btm_acton_text_field" name="btm_acton_details[btm_acton_end_point]" value="<?php
                                if (!empty($my_fields['btm_acton_end_point'])) {
                                    echo $my_fields['btm_acton_end_point'];
                                }
                                ?>"/></td>
                        </tr>

                        <tr class="form-field">
                            <th valign="top" scope="row">
                                <label for="btm_acton_ClientId" class="lable_font_size"><?php _e('ActOn ClientId', 'btmacton_details'); ?></label>
                            </th>
                            <td><input type="text" class="btm_acton_text_field" name="btm_acton_details[btm_acton_client_id]"  value="<?php
                                if (!empty($my_fields['btm_acton_client_id'])) {
                                    echo $my_fields['btm_acton_client_id'];
                                }
                                ?>"/></td>
                        </tr>

                        <tr class="form-field">
                            <th valign="top" scope="row">
                                <label for="acton_secret_key" class="lable_font_size"><?php _e('ActOn SecretKey', 'btmacton_details'); ?></label>
                            </th>
                            <td><input type="text" class="btm_acton_text_field" name="btm_acton_details[acton_secret_key]"  value="<?php
                                       if (!empty($my_fields['acton_secret_key'])) {
                                           echo $my_fields['acton_secret_key'];
                                       }
                                       ?>"/></td>
                        </tr>
                    </tbody>
                </table>
                <table cellspacing="2" cellpadding="5"  class="form-table form-field_white">
                    <tbody>
                        <tr class="bordered"><th><h2 class="settings_space">Site Settings</h2></th></tr>
                        <tr class="form-field">
                            <th valign="top" scope="row">
                                <label for="btm_user_name" class="lable_font_size"><?php _e('ActOn Username', 'btmacton_details'); ?></label>
                            </th>
                            <td><input type="text" class="btm_acton_text_field" name="btm_acton_details[acton_user_name]" value="<?php
                        if (!empty($my_fields['acton_user_name'])) {
                            echo $my_fields['acton_user_name'];
                        }
                        ?>"/></td>
                        </tr>

                        <tr class="form-field">
                            <th valign="top" scope="row">
                                <label for="btm_password" class="lable_font_size"><?php _e('ActOn Password', 'btmacton_details'); ?></label>
                            </th>
                            <td><input type="password" class="btm_acton_text_field" name="btm_acton_details[acton_password]" value="<?php
                        if (!empty($my_fields['acton_password'])) {
                            echo $my_fields['acton_password'];
                        }
                        ?>"/></td>
                        </tr>
                        <tr class="form-field">
                            <th valign="top" scope="row">
                                <label for="btm_siteid" class="lable_font_size"><?php _e('BTM SiteId', 'btmacton_details'); ?></label>
                            </th>
                            <td><input type="text" class="btm_acton_text_field" name="btm_acton_details[btm_siteid]" value="<?php
                               if (!empty($my_fields['btm_siteid'])) {
                                   echo $my_fields['btm_siteid'];
                               }
                               ?>"/></td>
                        </tr>
                    </tbody>
                </table>
                <table cellspacing="2" cellpadding="5" class="form-table form-field_white">
                    <tbody>
                        <tr class="bordered"><th><h2 class="settings_space">List Settings</h2></th></tr>
                        <tr class="form-field">
                            <th valign="top" scope="row">
                                <label for="acton_listid" class="lable_font_size"><?php _e('ActOn ListId', 'btmacton_details'); ?></label>
                            </th>
                            <td><input type="text" class="btm_acton_text_field" name="btm_acton_details[acton_listid]" value="<?php
                               if (!empty($my_fields['acton_listid'])) {
                                   echo $my_fields['acton_listid'];
                               }
                                       ?>"/></td>
                        </tr>
                        <tr class="form-field">
                            <th valign="top" scope="row">
                                <label for="btm_sub_listid" class="lable_font_size"><?php _e('ActOn Special listId', 'btmacton_details'); ?></label>
                            </th>
                            <td><input type="text" class="btm_acton_text_field" name="btm_acton_details[acton_sub_listid]"  value="<?php
                        if (!empty($my_fields['acton_sub_listid'])) {
                            echo $my_fields['acton_sub_listid'];
                        }
                        ?>"/></td>
                        </tr>
                    </tbody>
                </table>
				<table cellspacing="2" cellpadding="5" class="form-table form-field_white">
                    <tbody>
                        <tr class="bordered"><th><h2 class="settings_space">optin Template Settings</h2></th></tr>
                        <tr class="form-field">
                            <th valign="top" scope="row">
                                <label for="optin_template" class="lable_font_size"><?php _e('Optin Template', 'btmacton_details'); ?></label>
                            </th>
                            <td><input type="text" class="btm_acton_text_field" name="btm_acton_details[optin_template]" value="<?php
                               if (!empty($my_fields['optin_template'])) {
                                   echo $my_fields['optin_template'];
                               }
                                       ?>"/></td>
                        </tr>
						
						<tr class="form-field">
                            <th valign="top" scope="row">
                                <label for="SourceCodeField" class="lable_font_size"><?php _e('Source Code Field ', 'btmacton_details'); ?></label>
                            </th>
                            <td><input type="text" class="btm_acton_text_field" name="btm_acton_details[SourceCodeField]" value="<?php
                               if (!empty($my_fields['SourceCodeField'])) {
                                   echo $my_fields['SourceCodeField'];
                               }
                                       ?>"/></td>
                        </tr>
                        <tr class="form-field">
                            <th valign="top" scope="row">
                                <label for="SourceCode" class="lable_font_size"><?php _e('Source Code', 'btmacton_details'); ?></label>
                            </th>
                            <td><input type="text" class="btm_acton_text_field" name="btm_acton_details[SourceCode]" value="<?php
                               if (!empty($my_fields['SourceCode'])) {
                                   echo $my_fields['SourceCode'];
                               }
                                       ?>"/></td>
                        </tr>
                        <tr class="form-field">
                            <th valign="top" scope="row">
                                <label for="SourceCodeInline" class="lable_font_size"><?php _e('Source Code Inline', 'btmacton_details'); ?></label>
                            </th>
                            <td><input type="text" class="btm_acton_text_field" name="btm_acton_details[SourceCodeInline]" value="<?php
                              if (!empty($my_fields['SourceCodeInline'])) {
                                   echo $my_fields['SourceCodeInline'];
                               }
                                       ?>"/></td>
                        </tr>

                        <tr class="form-field">
                            <th valign="top" scope="row">
                                <label for="sourceTypeField" class="lable_font_size"><?php _e('Source Type Field', 'btmacton_details'); ?></label>
                            </th>
                            <td><input type="text" class="btm_acton_text_field" name="btm_acton_details[sourceTypeField]" value="<?php
                               if (!empty($my_fields['sourceTypeField'])) {
                                     echo $my_fields['sourceTypeField'];
                               }
                               ?>"/>
                            </td>
                        </tr>
                        <tr class="form-field">
                            <th valign="top" scope="row">
                                <label for="sourceType" class="lable_font_size"><?php _e('Source Type', 'btmacton_details'); ?></label>
                            </th>
                            <td><input type="text" class="btm_acton_text_field" name="btm_acton_details[sourceType]" value="<?php
                               if (!empty($my_fields['sourceType'])) {
                                     echo $my_fields['sourceType'];
                               }
                               ?>"/>
                            </td>
                        </tr>
						
                    </tbody>
                </table>
                <table cellspacing="2" cellpadding="5"  class="form-field_white">

                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="serial_number" class="lable_font_size"><?php _e('S No', 'btmacton_details'); ?></label>
                        </th>
                        <th valign="top" scope="row">
                            <label for="display_name" class="lable_font_size"><?php _e('Display Name', 'btmacton_details'); ?></label>
                        </th>
                        <th valign="top" scope="row">
                            <label for="list_id" class="lable_font_size"><?php _e('List Id', 'btmacton_details'); ?></label>
                        </th>
                        <th valign="top" scope="row">
                            <label for="custom_field_name" class="lable_font_size"><?php _e('Custom Field Name', 'btmacton_details'); ?></label>
                        </th>
                    </tr>
                    <tbody class="display_list">
                                <?php for ($list = 0; $list < 12; $list++) { ?>
                            <tr class="display_style_list">
                                <td style="width:10%;color:#fff; font-weight: bold;font-size: 16px;text-align: center;"><?php echo $list + 1; ?></td>
                                <td><input type="text" class="btm_acton_text_field" name="btm_acton_details[display_name][]"  value="<?php
                                           if (!empty($my_fields['display_name'][$list])) {
                                               echo $my_fields['display_name'][$list];
                                           }
                                           ?>"/></td>
                                <td><input type="text" class="btm_acton_text_field" name="btm_acton_details[llist_id][]"  value="<?php
                                           if (!empty($my_fields['llist_id'][$list])) {
                                               echo $my_fields['llist_id'][$list];
                                           }
                                           ?>"/></td>
                                <td><input type="text" class="btm_acton_text_field" name="btm_acton_details[custom_field_name][]"  value="<?php
                                           if (!empty($my_fields['custom_field_name'][$list])) {
                                               echo $my_fields['custom_field_name'][$list];
                                           }
                                           ?>"/></td>                              
                            </tr>
        <?php } ?>

                    </tbody>
                </table>
				<!-- --------------------------------------------------------------------------------------------------------------- -->
				<h2>Below Fields for Exit Intent Campaign</h2> 
				<table cellspacing="2" cellpadding="5"  class="form-field_white">

                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="serial_number" class="lable_font_size"><?php _e('S No', 'btmacton_details'); ?></label>
                        </th>
                        <th valign="top" scope="row">
                            <label for="display_name" class="lable_font_size"><?php _e('Display Name', 'btmacton_details'); ?></label>
                        </th>
                        <th valign="top" scope="row">
                            <label for="list_id" class="lable_font_size"><?php _e('List Id', 'btmacton_details'); ?></label>
                        </th>
                        <th valign="top" scope="row">
                            <label for="custom_field_name" class="lable_font_size"><?php _e('Custom Field Name', 'btmacton_details'); ?></label>
                        </th>
                    </tr>
                    <tbody class="display_list">
                                <?php for ($list = 0; $list < 8; $list++) { ?>
                            <tr class="display_style_list">
                                <td style="width:10%;color:#fff; font-weight: bold;font-size: 16px;text-align: center;"><?php echo $list + 1; ?></td>
                                <td><input type="text" class="btm_acton_text_field" name="btm_acton_details[display_name_exit][]"  value="<?php
                                           if (!empty($my_fields['display_name_exit'][$list])) {
                                               echo $my_fields['display_name_exit'][$list];
                                           }
                                           ?>"/></td>
                                <td><input type="text" class="btm_acton_text_field" name="btm_acton_details[llist_id_exit][]"  value="<?php
                                           if (!empty($my_fields['llist_id_exit'][$list])) {
                                               echo $my_fields['llist_id_exit'][$list];
                                           }
                                           ?>"/></td>
                                <td><input type="text" class="btm_acton_text_field" name="btm_acton_details[custom_field_name_exit][]"  value="<?php
                                           if (!empty($my_fields['custom_field_name_exit'][$list])) {
                                               echo $my_fields['custom_field_name_exit'][$list];
                                           }
                                           ?>"/></td>                              
                            </tr>
        <?php } ?>

                    </tbody>
                </table>
				<!-- -------------------------------------------------------------------------------------------------------------- -->
                </td>
                </tr>
                <tr class="form-field">
                    <th valign="top" scope="row"></th>
                    <td><input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" /></td>
                </tr>            
                </tbody>        
            </table>    
        </form>
        
        <div class="short_code_main_div">
            <h2><?php echo 'Usage & Shortcodes'; ?></h2>
            <hr/>
            <p>Usage is simple. Copy and paste the shortcode below to any page or post on your site and the email preference center will appear.</p>
            
            <div class="shortcode_innder_div">
                <label for="display_shortcode">[display_newsletter_list]</label>
            </div>
            
        </div>
        </div>

        <style>
            .main-settings-div{width: 100%;}
            .detail_form{width: 40%; float: left;}
            .form-field_white{width: 100%;background: #ffffff;}
            .form-field_white th{text-align: left;}
            .display_style_list{background: #aaa;}
            .lable_font_size{font-size: 15px;padding: 15px;}
            .btm_acton_text_field{float: left;}
            .submit_button{width: 20%;float:left;margin-top: 15px;font-size: 26px;}
            .settings_space{padding-left: 18px; margin: auto;}
            tr.bordered { border-bottom: 1px solid #ddd;  }
            
            .short_code_main_div{background-color: #fff; width: 22%; float: left;margin-top: 3.5%;}
            .short_code_main_div h2{padding-left: 20px;}
            .short_code_main_div p{padding: 0px 20px 20px; font-size: 18px; font-weight: 400;}
            .shortcode_innder_div{margin-left: 18px;margin-bottom: 20px;border: 1px solid #ddd;padding: 8px;font-size: 17px;margin-right: 32px;}
            .shortcode_innder_div input{padding: 10px;width: 94%; font-size: 18px;}
        </style>
       
        <?php
    }

}
