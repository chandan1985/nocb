<?php
/*
 * Admin Class for BC-Paywall PSA
 * Author: Dave Long
 */

// Disallow direct access
if(!defined( 'ABSPATH' )) {
    die( 'Direct access not allowed.' );
}

class bc_paywall_admin extends dmcss_wp {

    /*
     * Class constructor
     * Set up actions & filters, & initialize non-constant class vars
     *
     * @return void
     */
    public $dmcss_data = array();

    public function __construct() {
        parent::__construct();
        $dmcss_data['pages'] = array();
		//Handling log data 
        if(isset($_POST['paywall_log_export'])) {
            $columnHeader ='';
            $columnHeader = "First Name"."\t"."Last Name"."\t"."Email"."\t"."Username"."\t"."Time"."\t"."IP Address"."\t"."Blog ID"."\t"."User Type"."\t";
            $setData='';
            $rowData = '';
            global $wpdb;
            $table_name = $wpdb->prefix . 'paywall_users_logs';
            $users = 'select * from '.$table_name.'';
            $results1 = $wpdb->get_results($users);
            foreach($results1 as $value) {
                if($value->first_name == '') { $value->first_name = 'NULL'; }
                if($value->last_name  == '') { $value->last_name  = 'NULL'; }
                $value = $value->first_name ."\t". $value->last_name ."\t". $value->email ."\t". $value->username ."\t". $value->time ."\t". $value->ip_address ."\t". $value->blog_id ."\t". $value->user_role ."\t";
                $rowData .= $value."\n";
            }
            $setData .= trim($rowData)."\n";
            echo ucwords($columnHeader)."\n".$setData."\n";  
            $timevar = date('d-m-Y-h-i-sa');
            $filename = 'Paywall Log-'  .'-'.$timevar.'.xls';
            header("Content-type: application/vnd.ms-excel; name='excel'");
            header("Content-Disposition: attachment; filename=".$filename);
            header("Pragma: no-cache");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            exit;
        }

        if(isset($_POST['clear_paywall_log'])) {
            global $wpdb;
            $table_name = $wpdb->prefix . "paywall_users_logs";
            $sql = "TRUNCATE " . $table_name;
            $result = $wpdb->query($sql);
            echo "<script type='text/javascript'>window.location=document.location.href;</script>";
            exit;
        }

        // Prevent admin access by Subscribers & Registered Users
        add_action('admin_init', array(&$this, 'admin_redirect'));

        // Setup option data
        add_action( 'admin_init', array( &$this,'register_settings' ) );

        // Import CSS
        add_action('admin_print_styles', array(&$this, 'enqueueStylesheet'));
        // Enqueue needed JS / jQuery only on our admin page
        add_action( 'admin_enqueue_scripts', array( &$this, 'importJS' ) );

        // Add admin menus
        add_filter('manage_users_columns', array(&$this,'manage_users_columns'));
        add_filter('manage_users_custom_column', array(&$this, 'manage_users_custom_column'), 10, 3);
        add_action('admin_menu', array(&$this, 'admin_menu'));

        // add paywall columns to manage posts (and cpts) pages
        // loop through cpts that have paywall
        add_filter('manage_post_posts_columns' , array(&$this, 'tdc_paywall_add_columns' ));
        add_action('manage_post_posts_custom_column' , array(&$this, 'tdc_paywall_display_posts_columns'), 9, 2 );
        add_filter('manage_page_posts_columns' , array(&$this, 'tdc_paywall_add_columns' ));
        add_action('manage_page_posts_custom_column' , array(&$this, 'tdc_paywall_display_posts_columns'), 9, 2 );

        // also setup each CPT that needs the paywall
        if (isset($this->options['cpt_lockdown']) && is_array($this->options['cpt_lockdown']) ) {
        	foreach ( $this->options['cpt_lockdown']  as $post_type ) {
               add_filter('manage_'.$post_type.'_posts_columns' , array(&$this, 'tdc_paywall_add_columns' ));
               add_action('manage_'.$post_type.'_posts_custom_column' , array(&$this, 'tdc_paywall_display_posts_columns'), 9, 2 );
           }
       }

        // handle submissions
       add_action('save_post', array(&$this, 'publication_save'), 11, 2);
       add_action('publish_post', array(&$this, 'publication_save'), 11, 2);
   }

   /******* WP FITLERS ************/

    /**
     * Add 'BC Paywall PSA' menu & 'Publication' / 'Security' meta boxes
     *
     * @return void
     */
    public function admin_menu(){
        global $blog_id, $current_user;
        if( function_exists( 'add_meta_box' ) ){
            // hardcode posts and pages
            add_meta_box('dmcss_pub_code','Publication', array(&$this, 'publication_options'), 'post', 'side');
            add_meta_box('dmcss_security_policy','Security', array(&$this, 'security_options'), 'post', 'side');
            add_meta_box('dmcss_pub_code','Publication', array(&$this, 'publication_options'),'page','side');
            add_meta_box('dmcss_security_policy','Security', array(&$this, 'security_options'),'page','side');

            	// also setup each CPT that needs the paywall
            if (isset($this->options['cpt_lockdown']) && is_array($this->options['cpt_lockdown']) ) {
            	foreach ( $this->options['cpt_lockdown']  as $post_type ) {
                   add_meta_box('dmcss_pub_code','Publication', array(&$this, 'publication_options'),$post_type,'side');
                   add_meta_box('dmcss_security_policy','Security', array(&$this, 'security_options'), $post_type, 'side');
               }
           }
       }
       if( function_exists( 'add_menu_page' ) && function_exists( 'add_submenu_page' ) ){
        add_menu_page( 'Paywall Settings', 'BC Paywall PSA', 'Administrator', 'tdc-paywall-general',  array( $this, 'general_settings_page' ) );
        $this->dmcss_data['pages'][0] = add_submenu_page( 'tdc-paywall-general', 'BC Paywall PSA- General Options', 'General Options', 'Administrator', 'tdc-paywall-general',  array( $this, 'general_settings_page' ) );
        $this->dmcss_data['pages'][1] = add_submenu_page( 'tdc-paywall-general', 'BC Paywall PSA- Publications', 'Publications', 'Administrator', 'tdc-paywall-pubs',  array( $this, 'publication_settings_page' ) );
        $this->dmcss_data['pages'][2] = add_submenu_page( 'tdc-paywall-general', 'BC Paywall PSA- Paywall Layout', 'Paywall Layout', 'Administrator', 'tdc-paywall-layout',  array( $this, 'layout_settings_page' ) );

            // Only show Administration & Import menus to IT users
        if( preg_match( '/thedolancompany\.com|dolanmedia\.com/', $current_user->user_email ) ) {
            $this->dmcss_data['pages'][3] = add_submenu_page( 'tdc-paywall-general', 'BC Paywall PSA Administration', 'Administration', 'Administrator', 'tdc-paywall-admin',  array( $this, 'admin_settings_page' ) );
            if( 1 != $blog_id ) {
                $this->dmcss_data['pages'][4] = add_submenu_page( 'tdc-paywall-general', 'BC Paywall Settings Import', ' Settings Import', 'Administrator', 'tdc-paywall-import',  array( $this, 'import_settings_page' ) );
            }
				// $this->dmcss_data['pages'][6] = add_submenu_page( 'tdc-paywall-general', 'BC Paywall PSA Log', 'Paywall User Logs', 'Paywall User Logs', 'tdc-paywall-user-logs',  array( $this, 'paywall_user_log' ) );
        }
            # show registered users report
        $this->dmcss_data['pages'][2] = add_submenu_page( 'tdc-paywall-general', 'BC Paywall PSA- Registration Report', 'Registered Users', 'Administrator', 'tdc-paywall-ru-report',  array( $this, 'registration_report_page' ) );

            // Only show category subrates if we're using them
        if( isset($this->options['use_category_subrates']) ) {
            $this->dmcss_data['pages'][5] = add_submenu_page( 'tdc-paywall-general', 'BC Paywall PSA Category Subrates', 'Category Subrates', 'Administrator', 'tdc-paywall-category',  array( $this, 'category_subrate_settings_page' ) );
        }

        $this->dmcss_data['pages'][6] = add_submenu_page( 'tdc-paywall-general', 'Blueconic Configurations', 'Blueconic Configurations', 'Blueconic Configurations', 'tdc-paywall-bc-config',  array( $this, 'bc_config_settings_page' ) );
    }
}

    /**
     * Add custom columns to Users page in admin
     *
     * @param array $columns
     * @return column list
     */
    public function manage_users_columns( $columns ){
        $columns['dmcss_publications'] = 'PSA Publication';
        $columns['dmcss_subrates'] = 'PSA Subrate';
        $columns['dmcss_accounts'] = 'PSA Account';
        $columns['dmcss_timestamp'] = 'PSA Logged in';
        $columns['dmcss_lastValidated'] = 'PSA Last Validated';

        return $columns;
    }

    /**
     * Populate custom Users columns
     *
     * @param string $empty
     * @param string $column_name
     * @param int $id
     * @return column data
     */
    public function manage_users_custom_column( $empty = '', $column_name='', $id='' ){
        // Get user meta for DMCSS User
        $meta = get_user_meta( $id, 'dmcss_current_user', true );
        $column = '';

        // Format column data if populated
        if( is_array( $meta ) ) {
            switch( $column_name ) {
                case 'dmcss_publications':
                    // Return pub from $meta
                foreach( $meta as $pub => $data ) {
                    $column .= $pub . '<br>';
                }
                break;
                case 'dmcss_subrates':
                    // Return subrate from $meta
                foreach( $meta as $pub => $data ) {
                    if ($data['subrate']) {
                        $column .= $data['subrate'].'<br>';
                    }
                    else {
                       $column .= '<br>';
                   }
               }
               break;
               case 'dmcss_accounts':
                    // Return account Number from $meta
               foreach( $meta as $pub => $data ) {
                $column .= $data['accountNumber'].'<br>';
            }
            break;
            case 'dmcss_timestamp':
            case 'dmcss_lastValidated':
                    // Look up and return last validated
            $meta = get_user_meta( $id, $column_name, true);
            if( !is_numeric( $meta ) ){
                $column .= 'Session expired';
            }
            else {
                $timezone = get_option('timezone_string');
                if( !$timezone ) {
                    $offset = get_option('gmt_offset');
                    $meta = $meta + ( $offset * 3600 );
                    $timezone = 'UTC';
                }
                $now = 'now';
                $time = new \DateTime( $now, new DateTimeZone( $timezone ) );
                $time->setTimestamp ( $meta );
                $column .= $time->format( 'm-d-Y H:i:s' );
            }
            break;
        }
    }
    return $column;
}

/* Display custom column */
function tdc_paywall_display_posts_columns( $column, $post_id ) {
    $key = $column;
    switch ( $column ) {
        case 'dmcss_pub_code':
        echo get_post_meta( $post_id, $key, true );
        break;

            // case 'dmcss_security_policy':
            //     echo get_post_meta( $post_id, $key, true );
            //     break;
        case 'dmcss_security_policy':
                //echo get_post_meta( $post_id, $key, true );
        echo '<div id="dmcss_security_policy_' . $post_id . '">' . get_post_meta( $post_id, $key, true ). '</div>';
        break;


        case 'dmcss_free_from_birth':
        echo get_post_meta( $post_id, $key, true );
        break;
    }
}

/* Add custom column to post list */
function tdc_paywall_add_columns( $columns ) {
    return array_merge( $columns,
        array( 'dmcss_pub_code' => __( 'Pub Code', 'your_text_domain' ),
            'dmcss_security_policy' => __( 'Paywall', 'your_text_domain' ),
            'dmcss_free_from_birth' => __( 'Free from Birth', 'your_text_domain' ) ) );
}
/******** END WP FILTERS **********/

/******** WP ACTIONS **************/

    /**
     * Prevent DMCSS users from accessing the admin
     *
     * @return void
     */
    public function admin_redirect() {
        global $current_user;

        // Don't trap the call with paywall if its an ajaxy call
        if( !preg_match( '/admin-ajax\.php/i', $_SERVER['REQUEST_URI'] ) ) {
            $role = array_shift( $current_user->roles );
            if( preg_match( '/' . dmcss_wp::SUBSCRIBER_ROLE . '|' . dmcss_wp::REGISTERED_USER_ROLE . '/i', $role ) ){
                // Subscriber / Registered User; decide where to redirect
                if( !isset( $_SERVER['HTTP_REFERER'] ) || preg_match( '/wp-login/i', $_SERVER['HTTP_REFERER'] ) || !preg_match( '/' . get_bloginfo('url') . '/i', $_SERVER['HTTP_REFERER'] ) ) {
                    $redirect = get_bloginfo( 'url' );
                }
                else {
                    $redirect = $this->remove_get_var( $_SERVER['HTTP_REFERER'], 'dmcss' );
                }

                // Preserve any messages set during login
                if( isset( $_REQUEST['dmcss[messages]'] ) ) {
                    @setcookie('dmcss[messages]', serialize($_REQUEST['dmcss[messages]']), time() + 2592000, '/', $_SERVER['HTTP_HOST']);
                }
                //ob_start();
                wp_redirect( $redirect );
                //ob_end_flush();
                exit();
            }
        }
    }

    /**
     * Import JS & jQuery for admin UI
     *
     * @return void
     */
    public function importJS( $hook ){
        if(isset($this->dmcss_data['pages']) && is_array( $this->dmcss_data['pages'] ) && in_array( $hook, $this->dmcss_data['pages'] ) ) {
            wp_register_script( 'dmcsswpAdmin', plugins_url( '/admin.js', __FILE__ ), array( 'jquery' ) );
            wp_enqueue_script( 'dmcsswpAdmin' );
        }
    }

    /******** END WP ACTIONS **********/

    /******** PUBLIC FUNCTIONS ********/

    /**
     * Prints out the publication select box when editing pages/posts
     *
     * @param object $post
     * @return void
     */
    public function publication_options( $post ){
        $output = '';

        /*
         * is_array() added because publication value is initialized by plugin as an
         * empty string. Changed to show publications when one or more are present
         * to fix dependencies with other plugins like the public notice import.
         */
        if( is_array( $this->options['publications'] ) && 1 <= count( $this->options['publications'] ) ){
            $key = 'dmcss_pub_code';

            $firstPub=true;
            foreach( $this->options['publications'] as $pub ){
                $checked = '';
                $oo = get_post_meta( $post->ID, $key, true );
                if( substr_count( $oo, $pub['code']) > 0 ){
                    $checked = 'checked="checked"';
                }
                elseif ( $post->post_status=='auto-draft' && $firstPub ){
                    $checked = 'checked="checked"';
                    $firstPub = false;
                }
                $output .= '<input type="checkbox" name="'.$key.'[]" value="'.$pub['code'].'"'.$checked.'> '.$pub['title']. '<BR>';
            }
        }
        $output .= '<p style="color:#666;font-style:italic;">This determines which publication subscribers can see past the paywall on the post.</p>';
        echo $output;
    }

/**
     * Saves the custom fields added to the page/post edit page.
     *
     * @param int $post_id
     * @param object $post
     * @return $post
     */
public function publication_save( $post_id, $post ){
    global $wpdb, $post;

    if ( !$post_id ) $post_id = $_POST['post_ID'];
    if ( !$post_id ) return $post;

    if (isset($_POST['originalaction']) && $_POST['originalaction'] != 'post' && isset($_POST['post_ID']) && $_POST['post_ID'] != $post_id ) return;


    $key = 'dmcss_pub_code';
			// Check the pubcode
    if(isset($_POST[$key]) && is_array( $_POST[$key] ) ) {
        $value= @stripslashes( implode( ',', $_POST[$key] ) );
    }else {
        $value = isset($this->options['publications'][0]['code']) ? $this->options['publications'][0]['code'] : '';
    }

        // If the value changed...
    if( $value != get_post_meta( $post_id, $key, true ) ) {
        if( !add_post_meta( $post_id, $key, $value, true ) ) {
            update_post_meta( $post_id, $key, $value );
        }
    }

        // Check the security policy
    $key = 'dmcss_security_policy';
    if( !empty( $_POST[$key] ) ) {
     $value= @stripslashes( $_POST[$key] );
		}//Check to see if this is the quick edit button, or the post-edit form
		elseif(isset($_POST['action']) && $_POST['action'] == 'inline-save' ){
			$value = get_post_meta( $post_id, $key, true );
		}else {
			$value = isset($this->options['security_default']) ? $this->options['security_default'] : '';
		}
		
		// If the value changed...
		if( $value != get_post_meta( $post_id, $key, true ) ) {
			if( !add_post_meta( $post_id, $key, $value, true ) ) {
				update_post_meta( $post_id, $key, $value );
			}
		}
		
		  // Check the free until date
        $key = 'dmcss_free_from_birth';
        if( !empty( $_POST[$key] ) && is_numeric($_POST[$key]) ) {
         $value= @stripslashes( $_POST[$key] );
		}//Check to see if this is the quick edit button, or the post-edit form
		elseif(isset($_POST['action']) && $_POST['action'] == 'inline-save' ){
			$value = get_post_meta( $post_id, $key, true );
		}else {
			$value = '';
		}
		
		// If the value changed...
		if( $value != get_post_meta( $post_id, $key, true ) ) {
			if( !add_post_meta( $post_id, $key, $value, true ) ) {
				update_post_meta( $post_id, $key, $value );
			}
		}
		

        return $post;
    }

    /*
     * Register & set up plugin options page via settings API
     */
    public function register_settings() {
        register_setting( 'tdc_paywall_data', 'tdc_paywall_data', array( &$this, 'validate_options' ) );
    }

    /**
     * Prints out the security select box when editing pages/posts
     *
     * @param object $post
     * @return void
     */
    public function security_options( $post ){
        $key = 'dmcss_security_policy';

        $policy = get_post_meta( $post->ID, $key, true );
        if( empty( $policy ) ) {
            $policy = $this->options['security_default'];
        }

        $output = '<div class="misc-pub-section">';
        $output .= '<label class="dmcss_select_label" for="dmcss_security_policy">Security Policy:</label><select class="dmcss_select" name="' . $key . '">';
        foreach( $this->security_options as $option ) {
            if( preg_match( "/$option/i", $policy ) ) {
                $output .= '<option value="' . $option . '" selected="selected">' . $option . '</option>';
            }
            else {
                $output .= '<option value="' . $option . '">' . $option . '</option>';
            }
        }
        $output .= '</select>';

        if( $this->options['allow_free_from_birth'] == true){

         $key = 'dmcss_free_from_birth';
         $site_code = $this->options['publications'][0]['code'];
         $free_hrs = '0';
         $free_for = get_post_meta( $post->ID, $key, true );
         if( empty( $free_for ) ) {
            $free_for = '';
        }
        $output .= '</div>';
        $output .= '<div class="misc-pub-section">';
        $output .= '<label>Hours Free:</label>';
        if ($site_code == 'njbiz'){
         $output .= '<input maxlength="3" style="width:40px;" type="text" class="widefat" value="'.$free_hrs.'" name="' . $key . '">';
     }else{
         $output .= '<input maxlength="3" style="width:40px;" type="text" class="widefat" value="'.$free_for.'" name="' . $key . '">';
     }
     $output .= '</input>';

			/////

     $free_until_date = intval(strtotime( $post->post_date )) + (intval($free_for)*3600);
	// translators: Publish box date format, see http://php.net/date
     if ( isset($free_for) && $free_for != '' && is_numeric($free_for) ) {
        $datef = 'M j, Y @ G:i' ;
        $published_date = intval(strtotime( $post->post_date ));
        $pubdate = date_i18n( $datef, $published_date );
        $pub_date = 'Published on: <b>'.$pubdate.'</b><br/>';
        $stamp = 'Scheduled for Lock: <b>%s</b>';
				//$date = date_i18n( $datef, strtotime( $free_until_date ) );
        $date = date_i18n( $datef, $free_until_date );
        printf($pub_date);
        printf($stamp, $date);
    }
			 else { // draft (no saves, and thus no date specified)
                $stamp = 'Lock <b>immediately</b>';
                print($stamp);
            }


        }

        $output .= '</div>';

        echo $output;
    }

    /*
     * Sanitize and validate input. Accepts an array, return a sanitized array.
     * @param array $input
     * @return array $input
     */
    public function validate_options( $input ) {
        global $blog_id;
        $error = false;

        // Special validation by referer
        $referer = wp_get_referer();
      //  $log_str = time() . ' called validate ' . $referer . ' data: ' . var_export( $input, true );
      //  error_log( $log_str . PHP_EOL, 3, '/srv/files/validate.log' );

        if( preg_match( '/tdc-paywall-general/', $referer ) ) {
            // Validate General Options
            $keys = array( 'master_bypass', 'validate_tokens', 'allow_registered_users', 'popup_login', 'show_key_icon', 'show_excerpt', 'key_justification', 'home_lock', 'category_lock', 'allow_free_from_birth' );
            foreach( $keys as $key ) {
                if( !isset( $input[$key] ) ) {
                    $input[$key] = false;
                }
            }
        }
        elseif( preg_match( '/tdc-paywall-pubs/', $referer ) ) {
            // Validate Publications
            $length = count( $input['publications'] );
            for( $i = 0; $i < $length; $i++ ) {
                if( empty( $input['publications'][$i]['code'] ) || isset( $input['publications'][$i]['delete'] ) ) {
                    unset( $input['publications'][$i] );
                }
            }
            if( empty( $input['publications'] ) ) {
                add_settings_error( 'tdc_paywall_data', 'paywall_settings_error', __( 'No valid publications found.' ), 'error' );
                $error = true;
            }
        }
        elseif( preg_match( '/tdc-paywall-layout/', $referer ) ) {
            // Validate Paywall Layout Options
            $keys = array( 'custom_register', 'custom_login', 'show_subscribe', 'show_forgot_password' );
            foreach( $keys as $key ) {
                if( !isset( $input[$key] ) ) {
                    $input[$key] = false;
                }
            }

            // Compare Page requires the path to be populated & the page to exist
            if( 'Compare Page' == $input['subscribe_display'] && ( empty( $input['compare_path'] ) || !get_page_by_path( $input['compare_path'] ) ) ) {
                if( empty( $input['compare_path'] ) ) {
                    add_settings_error( 'tdc_paywall_data', 'paywall_settings_error', __( 'Invalid compare page path' ), 'error' );
                }
                else {
                    add_settings_error( 'tdc_paywall_data', 'paywall_settings_error', __( 'Invalid compare page path; ' . get_site_url( $blog_id, $input['compare_path'] ) . ' does not exist.' ), 'error' );
                }
                $error = true;
            }
            // Custom subscribe requires $subscribe_link to be included
            if( 'Custom Display' == $input['subscribe_display'] && empty( $input['custom_subscribe_text'] ) ) {
                add_settings_error( 'tdc_paywall_data', 'paywall_settings_error', __( 'Invalid custom formatting for subscribe tab; $subscribe_link is required' ), 'error' );
                $error = true;
            }
            // Custom register requires $register_form to be included
            if( $input['custom_register'] && !preg_match( '/\$register_form/', $input['custom_register_text'] ) ) {
                add_settings_error( 'tdc_paywall_data', 'paywall_settings_error', __( 'Invalid custom formatting for register tab; $register_form is required' ), 'error' );
                $error = true;
            }
            // Custom login requires $login_form to be included
            if( $input['custom_login'] && !preg_match( '/\$login_form/', $input['custom_login_text'] ) ) {
                add_settings_error( 'tdc_paywall_data', 'paywall_settings_error', __( 'Invalid custom formatting for login tab; $login_form is required' ), 'error' );
                $error = true;
            }
        }
        elseif( preg_match( '/tdc-paywall-admin/', $referer ) ) {
            // Validate Admin Options
            $keys = array( 'show_debug', 'allow_circ_admin', 'use_category_subrates' );
            foreach( $keys as $key ) {
                if( !isset( $input[$key] ) ) {
                    $input[$key] = false;
                }
            }
            if( empty( $input['ws_url'] ) ) {
                add_settings_error( 'tdc_paywall_data', 'paywall_settings_error', __( 'Web Service URL is required.' ), 'error' );
                $error = true;
            }
        }
        elseif( preg_match( '/tdc-paywall-import/', $referer ) ) {
            // Validate Data Import
            if( $input['import_souce_blog'] && !$input['confirm_import'] ) {
                add_settings_error( 'tdc_paywall_data', 'paywall_settings_error', __( 'Import not confirmed; no changes saved. ' ), 'error' );
                $error = true;
            }
            else {
                switch_to_blog( $input['import_souce_blog'] );
                $ops = get_option( 'tdc_paywall_data' );
                if( !is_array( $ops ) ) {
                    add_settings_error( 'tdc_paywall_data', 'paywall_settings_error', __( 'Invalid source settings; no changes saved.' ), 'error' );
                    $error = true;
                    return $this->options;
                }
                restore_current_blog();
                if( !$error ) {
                    add_settings_error( 'tdc_paywall_data', 'paywall_settings_updated', __( 'Paywall Settings successfully imported. ' ), 'updated' );
                    return $ops;
                }
            }
        }
        elseif( preg_match( '/tdc-paywall-category/', $referer ) ) {
            // Validate Category Subrates
            if( empty( $input['category_subrates'] ) ) {
                add_settings_error( 'tdc_paywall_data', 'paywall_settings_error', __( 'No valid subrate(s) found.' ), 'error' );
                $error = true;
            }
            $length = count( $input['category_subrates'] );
            for( $i = 0; $i < $length; $i++ ) {
                if( empty( $input['category_subrates'][$i]['subrate'] ) || isset( $input['category_subrates'][$i]['delete'] ) ) {
                    unset( $input['category_subrates'][$i] );
                }
            }
        }

        // If no errors, merge other options & save
        if( !$error ) {
            $ops = get_option( 'tdc_paywall_data', array() );
            $input = array_merge( $ops, $input );
            ksort( $input, SORT_STRING );

            add_settings_error( 'tdc_paywall_data', 'paywall_settings_updated', __( 'Settings saved.' ), 'updated');
            return $input;
        }
        else {
            return $this->options;
        }
    }

    /******** END PUBLIC FUNCTIONS ****/
    /******** PRIVATE FUNCTIONS *******/

    /**
     * Build html for subrate table
     * @param array $cat - category array
     * @param array $categories - full category list
     * @param string $name_prefix - name for HTML elements
     * @param boolean $required - required field?
     * @return table cell html
     */
    private function category_input_td( $cat, $categories, $name_prefix, $required = false ){
        $count = '';
        $result = '<td><select name="'.$name_prefix.'[ID]" class="dmcss_row_focus">';

        while( $category = current( $categories ) ) {
            $found = false;
            $key = key( $categories );

            // Remove selected categories from the list; can only be used once
            foreach( $this->options['category_subrates'] as $saved ) {
                $test = array_search( $category->cat_ID, $saved );
                if( $test ) {
                    unset( $categories[$key] );
                    $found = true;
                    break;
                }
            }

            // Output option & post count for selected categories
            if(isset($cat['ID']) && $cat['ID'] == $category->cat_ID ){
                $count = $category->count;
                $result .= '<option value = "' . $category->cat_ID . '" selected="selected">' . $category->name . '</option>';
            }
            elseif( !$found ) {
                $result .= '<option value = "' . $category->cat_ID . '">' . $category->name . '</option>';
                $category = next( $categories );
            }
        }
        $result .= '</select></td><td><input size="60" type="text" ';
        if( $required ) {
            $result .= 'class="tdc_required" ';
        }
        $result .= 'name="' . $name_prefix.'[subrate]" value="' . (isset($cat['subrate']) ? $cat['subrate'] : '') . '" /></td><td>' . $count . '</td>';

        return $result;
    }

    /**
     * Build html for publications table
     * @param array $pub - publication array
     * @param string $name_prefix - name for HTML elements
     * @param boolean $required - required field?
     * @return table cell html
     */
    private function pub_input_td( $pub, $name_prefix, $required = false ){
        $result = '
        <td><input size="30" type="text" class="dmcss_row_focus" name="' . $name_prefix . '[title]" value="' . (isset($pub['title']) ? $pub['title'] : '') . '" /></td>
        <td><input size="10" ';
        if( $required ) {
            $result .= 'class="tdc_required" ';
        }
        $result .= 'type="text" name="' . $name_prefix . '[code]" value="' . (isset($pub['code']) ? $pub['code'] : '') . '" /></td>
        <td><input size="60" type="text" name="' . $name_prefix . '[subrate]" value="' . (isset($pub['subrate']) ? $pub['subrate'] : '') . '" /></td>
        ';
        return $result;
    }

    /******** END PRIVATE FUNCTIONS ***/

    /******** DISPLAY FUNCTIONS *******/

    /*
     * Build plugin options page via settings API
     *
     * @param array $options - options data
     * @return Paywall Data div html
     */
    private function show_paywall_data( $options ) {
        if( !$this->options['show_debug'] || empty( $options ) ) {
            return;
        }
        ?>
        <h4 id="data_fade">Paywall Data</h4>
        <div id="data_contents"><?php
        foreach( $options as $key => $value ) {
            if( !is_array( $value ) && preg_match( '/\<\w+\>/', $value ) ) {
                $value = '<textarea disabled="disabled">' . $value . '</textarea>';
            }
            echo( "<p><strong>$key:</strong>" . var_export( $value, true ) . '<p>' );
        } ?>
        </div><?php
    }

    public function admin_settings_page() {
        settings_errors( 'tdc_paywall_data' ); ?>
        <div class="wrap paywall_admin">
            <h2><?php _e( 'BC Paywall PSA' ); ?></h2>
            <h3><?php _e( 'Administration' ); ?></h3>
            <form method="post" action="options.php" enctype="multipart/form-data">
                <?php settings_fields( 'tdc_paywall_data'); ?>
                <?php $this->show_paywall_data( $this->options ); ?>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row"><label for="tdc_paywall_data[ws_url]"><?php _e('Web Service url') ?></label></th>
                            <td>
                                <input size="80" type="text" name="tdc_paywall_data[ws_url]" value="<?php echo $this->options['ws_url']; ?>" />
                                <p class="dmcss_description">Valid Web Service example: https://portal.publishersserviceassociates.com/api_auth/index.php</p>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row"><label for="tdc_paywall_data[ws_url_token]"><?php _e('Web Service Token') ?></label></th>
                            <td>
                                <input size="80" type="text" name="tdc_paywall_data[ws_url_token]" value="<?php echo $this->options['ws_url_token']; ?>" />
                                <p class="dmcss_description">Valid Web Service token : 4aa7b57fd811fb7aaf080778c0335592 <b>Please avoid Space</b></p>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row"><label for="tdc_paywall_data[PSA_url]"><?php _e('PSA Manage URL') ?></label></th>
                            <td>
                                <input size="80" type="text" name="tdc_paywall_data[PSA_url]" value="<?php echo $this->options['PSA_url']; ?>" />
                                <p class="dmcss_description">Valid PSA URL : https://journalmultimediaservice.com/<b>PUB_CODE</b>/manage.php <b>Please avoid Space</b></p>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row"><label for="tdc_paywall_data[public_notice_URL]"><?php _e('Public Notice URL') ?></label></th>
                            <td>
                                <input size="80" type="text" name="tdc_paywall_data[public_notice_URL]" value="<?php echo $this->options['public_notice_URL']; ?>" />
                                <p class="dmcss_description">Valid URL : http://publicnotices.domain.com <b>Please avoid Space</b></p>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row"><label for="tdc_paywall_data[jobtrac_enable]"><?php _e('Enable Jobtrac') ?></label></th>
                            <td>
                                <input size="80" type="text" name="tdc_paywall_data[jobtrac_enable]" value="<?php echo $this->options['jobtrac_enable']; ?>" />
                                <p class="dmcss_description">Please insert "yes" avoiding space, else leave blank.</b></p>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row"><label for="tdc_paywall_data[project_center_enable]"><?php _e('Enable Project Center') ?></label></th>
                            <td>
                                <input size="80" type="text" name="tdc_paywall_data[project_center_enable]" value="<?php echo $this->options['project_center_enable']; ?>" />
                                <p class="dmcss_description">Please insert "yes" avoiding space, else leave blank.</b></p>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row"><label for="tdc_paywall_data[thankyou_msg]"><?php _e('Thank You Email Message') ?></label></th>
                            <td>
                                <textarea rows="10" cols="100" name="tdc_paywall_data[thankyou_msg]"><?php echo stripslashes( $this->options['thankyou_msg'] ); ?></textarea>
                                <p class="dmcss_description">Thank You Email Message </p>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row"><label for="tdc_paywall_data[email_from]"><?php _e('Email From') ?></label></th>
                            <td>
                                <input size="80" type="text" name="tdc_paywall_data[email_from]" value="<?php echo $this->options['email_from']; ?>" />
                                <p class="dmcss_description">Sender Email Id : <b>support@domain.com</b> </p>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row"><label for="tdc_paywall_data[email_from_name]"><?php _e('Email From Name') ?></label></th>
                            <td>
                                <input size="80" type="text" name="tdc_paywall_data[email_from_name]" value="<?php echo $this->options['email_from_name']; ?>" />
                                <p class="dmcss_description">Email From Name : <b>Publication Admin</b></p>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row"><label for="tdc_paywall_data[email_sub]"><?php _e('Email Subject') ?></label></th>
                            <td>
                                <input size="80" type="text" name="tdc_paywall_data[email_sub]" value="<?php echo $this->options['email_sub']; ?>" />
                                <p class="dmcss_description">Email Subject : <b>Subscription Successful</b></p>
                            </td>
                        </tr>


                        <tr valign="top" style="display: none;">
                            <th scope="row"><label for="tdc_paywall_data[subsadmin_url]"><?php _e('Subsadmin url') ?></label></th>
                            <td>
                                <input size="80" type="text" name="tdc_paywall_data[subsadmin_url]" value="<?php echo $this->options['subsadmin_url']; ?>" />
                                <p class="dmcss_description">Valid Subsadmin example: http://circpro2.subsadmin.dolanmedia.com/</p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Debugging Options') ?></th>
                            <td>
                                <div class="option">
                                    <input type="checkbox" name="tdc_paywall_data[show_debug]" value="1" <?php if( $this->options['show_debug'] ): ?> checked="yes" <?php endif; ?> />
                                    <label for="tdc_paywall_data[show_debug]"><?php _e('Enable debug output') ?></label>
                                </div>
                                <div class="option">
                                    <p style="font-weight:bold; font-size:11px; font-style:normal;">Debugging IPs (separate by commas)</p>
                                    <input type="text" size="80" name="tdc_paywall_data[debug_ips]" value="<?php echo $this->options['debug_ips']; ?>"/>
                                    <p class="dmcss_description">127.0.0.1, 192.168.X.X, and 10.X.X.X are enabled by default.</p>
                                </div>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Login Options') ?></th>
                            <td>
                                <div class="option">
                                    <input type="checkbox" name="tdc_paywall_data[allow_circ_admin]" value="1" <?php if( $this->options['allow_circ_admin'] ): ?> checked="yes" <?php endif; ?> />
                                    <label for="tdc_paywall_data[allow_circ_admin]"><?php _e('Enable admin login via circ') ?></label>
                                    <p class="dmcss_description">Selecting this option will allow WordPress admin users to log in with circ credentials.</p>
                                </div>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Category Subrates') ?></th>
                            <td>
                                <div class="option">
                                    <input type="checkbox" name="tdc_paywall_data[use_category_subrates]" value="1" <?php if( $this->options['use_category_subrates'] ): ?> checked="yes" <?php endif; ?> />
                                    <label for="tdc_paywall_data[use_category_subrates]"><?php _e('Lock categories down by subrate') ?></label>
                                    <p class="dmcss_description">Selecting this option will allow category access to be restricted by subrate.</p>
                                </div>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Free From Birth') ?></th>
                            <td>
                              <div class="option">
                                <input type="checkbox" name="tdc_paywall_data[allow_free_from_birth]" value="1" <?php if( $this->options['allow_free_from_birth'] ) echo 'checked="checked"'; ?> />
                                <label for="tdc_paywall_data[allow_registered_users]"><?php _e( 'Allow free from birth' ); ?></label>
                                <p class="dmcss_description">Allows a Subscriber Only or Registered User post to be free for a number of hours.</p>
                            </div>
                        </td>
                    </tr>

                </tbody>
            </table>
            <p class="submit"><input class="button-primary" type="submit" value="Save Changes" /></p>
        </form>
        </div><?php
    }

    public function category_subrate_settings_page() {
        $args = array(
            'type'                     => 'post',
            'child_of'                 => 0,
            'orderby'                  => 'name',
            'order'                    => 'ASC',
            'hide_empty'               => 0,
            'hierarchical'             => 1,
            'taxonomy'                 => 'category',
            'pad_counts'               => false
        );
        $categories = get_categories( $args );
        if( empty( $this->options['category_subrates'] ) ) {
            $this->options['category_subrates'] = array();
        } ?>

        <div class="dmcss_templates">
            <table>
                <tr class="dmcss_new_row">
                    <td> </td><?php
                    echo $this->category_input_td( array(), $categories, 'tdc_paywall_data[category_subrates][%i%]', true ); ?>
                    <td class="dmcss_row_add"><button  type="button" class="dmcss_row_add_button" disabled="disabled">add</button></td>
                </tr>
            </table>
            </div><?php

            settings_errors( 'tdc_paywall_data' ); ?>
            <div id="tdc-paywall-category" class="wrap paywall_admin">
                <h2><?php _e( 'BC Paywall PSA' ); ?></h2>
                <h3><?php _e( 'Category Subrates' ); ?></h3>
                <form method="post" action="options.php" enctype="multipart/form-data">
                    <?php settings_fields( 'tdc_paywall_data'); ?>
                    <?php $this->show_paywall_data( $this->options ); ?>
                    <table class="widefat">
                        <thead>
                            <tr><th class="dmcss_category_delete">Delete</th><th>Category Name</th><th>Category SubRates (separate multiples with comma)</th><th>Posts</th><th></th></tr>
                        </thead>
                        <tbody>
                            <?php
                            for( $i = 0; $i < count( $this->options['category_subrates'] ); $i++ ): ?>
                                <tr class="dmcss_category<?php if($i % 2 === 0): ?> alternate <?php endif; ?>">
                                    <td class="dmcss_category_delete"><input type="checkbox" id="dmcss_category_<?php echo $i; ?>_delete" name="tdc_paywall_data[category_subrates][<?php echo $i; ?>][delete]" /></td>
                                    <?php echo $this->category_input_td( $this->options['category_subrates'][$i], $categories, "tdc_paywall_data[category_subrates][$i]" ); ?>
                                    <td></td>
                                </tr>
                            <?php endfor; ?>
                            <tr class="dmcss_new_row <?php if( 0 === count( $this->options['category_subrates'] ) % 2 ): ?> alternate <?php endif; ?>">
                                <td> </td><?php
                                echo $this->category_input_td( array(), $categories, "tdc_paywall_data[category_subrates][$i]", true ); ?>
                                <td class="dmcss_row_add"><button  type="button" class="dmcss_row_add_button" disabled="disabled">add</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="submit"><input class="button-primary" type="submit" value="Save Changes" /></p>
                </form>
                </div><?php
            }

            public function general_settings_page() {
                settings_errors( 'tdc_paywall_data' ); ?>
                <div id="tdc-paywall-general" class="wrap paywall_admin">
                    <h2><?php _e( 'BC Paywall PSA' ); ?></h2>
                    <h3><?php _e( 'General Options' ); ?></h3>
                    <form method="post" action="options.php" enctype="multipart/form-data">
                        <?php settings_fields( 'tdc_paywall_data'); ?>
                        <?php $this->show_paywall_data( $this->options ); ?>
                        <table class="form-table">
                            <tbody>
                                <tr valign="top">
                                    <th scope="row"><?php _e( 'Master Bypass' ); ?></th>
                                    <td>
                                        <div class="option">
                                            <input type="checkbox" name="tdc_paywall_data[master_bypass]" value="1" <?php if( $this->options['master_bypass'] ) echo 'checked="checked"'; ?> />
                                            <label for="tdc_paywall_data[master_bypass]"><?php _e( 'Override security and treat all posts as free' ); ?></label>
                                        </div>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e( 'Display Login Form' ); ?></th>
                                    <td>
                                        <div class="option">
                                            <input type="checkbox" name="tdc_paywall_data[display_login_form]" value="1" <?php if( $this->options['display_login_form'] ) echo 'checked="checked"'; ?> />
                                            <label for="tdc_paywall_data[display_login_form]"><?php _e( 'Show login form whether Paywall enabled or disabled' ); ?></label>
                                        </div>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e( 'Security Policies' ); ?></th>
                                    <td>
                                        <div class="option">
                                            <label for="tdc_paywall_data[login_duration]"><?php _e( 'User login duration (days)' ); ?></label>
                                            <input type="text" name="tdc_paywall_data[login_duration]" value="<?php echo $this->options['login_duration']; ?>" size="4"/>
                                            <p class="dmcss_description">Leave blank for default of 60 days.</p>
                                        </div>
                                        <div class="option">
                                            <label for="tdc_paywall_data[security_default]"><?php _e( 'Default security policy for all pages and posts' ); ?></label>
                                            <select name="tdc_paywall_data[security_default]">
                                                <?php
                                                $policy = $this->options['security_default'];
                                                foreach( $this->security_options as $option ) {
                                                    if( preg_match( '/' . $option . '/i', $policy ) ) {
                                                        echo( '<option value="' . $option . '" selected="selected">' . $option . '</option>' );
                                                    }
                                                    else {
                                                        echo( '<option value="' . $option . '">' . $option . '</option>' );
                                                    }
                                                }?>
                                            </select>
                                        </div>
                                        <div class="option">
                                            <input type="checkbox" name="tdc_paywall_data[validate_tokens]" value="1" <?php if( $this->options['validate_tokens'] ) echo 'checked="checked"'; ?> />
                                            <label for="tdc_paywall_data[validate_tokens]"><?php _e( 'Validate user tokens' ); ?></label>
                                            <p class="dmcss_description">If tokens are validated, only a single user/device may be logged into each acount.</p>
                                        </div>
                                        <div class="option">
                                            <input type="checkbox" name="tdc_paywall_data[allow_registered_users]" value="1" <?php if( $this->options['allow_registered_users'] ) echo 'checked="checked"'; ?> />
                                            <label for="tdc_paywall_data[allow_registered_users]"><?php _e( 'Allow registered user access' ); ?></label>
                                        </div>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e( 'CPT Paywall Lockdown' ); ?></th>
                                    <td>
                                        <p class="dmcss_description">Select which Custom Post Types can by locked behind the paywall.</p>
                                        <?php
                                        $args = array(
                                            'public'   => true,
                                            '_builtin' => false
                                        );

                        $output = 'names'; // names or objects, note names is the default
                        $operator = 'and'; // 'and' or 'or'

                        $post_types = get_post_types( $args, $output, $operator );

                        $cur_cpt=1;
                        $cpt_lockdown = isset($this->options['cpt_lockdown']) ? $this->options['cpt_lockdown'] : array();
                        foreach ( $post_types  as $post_type ) {
                            ?>
                            <div class="option">
                                <input type="checkbox" name="tdc_paywall_data[cpt_lockdown][<?php echo $cur_cpt; ?>]" value="<?php echo $post_type; ?>" <?php if(in_array($post_type, $cpt_lockdown) ) echo 'checked="checked"'; ?> />
                                <label for="tdc_paywall_data[cpt_lockdown]"><?php echo $post_type; ?></label>
                            </div>
                            <?php
                            $cur_cpt=$cur_cpt+1;
                        }
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Display options' ); ?></th>
                    <td>
                        <div class="option">
                            <input type="checkbox" name="tdc_paywall_data[popup_login]" value="1" <?php if( $this->options['popup_login'] ) echo 'checked="checked"'; ?> />
                            <label for="tdc_paywall_data[popup_login]"><?php _e( 'Enable Login Popup' ); ?></label>
                            <p class="dmcss_description">Enabling this will replace the top nav 'LOG IN' link with a popup login form.</p>
                        </div>
                        <div class="option">
                            <input type="hidden" name="tdc_paywall_data[show_key_icon]" value="0" />
                            <input type="checkbox" name="tdc_paywall_data[show_key_icon]" value="1" <?php if( $this->options['show_key_icon'] ) echo 'checked="checked"'; ?> />
                            <label for="tdc_paywall_data[show_key_icon]"><?php _e( 'Display key icon on locked posts' ); ?></label>
                        </div>
                        <div class="option">
                            <label for="tdc_paywall_data[key_icon_url]"><?php _e( 'Alternate key icon url' ); ?></label>
                            <input size="80" type="text" name="tdc_paywall_data[key_icon_url]" value="<?php echo $this->options['key_icon_url']; ?>" />
                            <p class="dmcss_description">Leave blank for default key icon:<img src="<?php echo( $this->default_key_location ); ?>"/></p>
                        </div>
                        <div class="option">
                            <input type="checkbox" name="tdc_paywall_data[show_excerpt]" value="1" <?php if( $this->options['show_excerpt'] ) echo 'checked="checked"'; ?> />
                            <label for="tdc_paywall_data[show_excerpt]"><?php _e( 'Display excerpt before login form' ); ?></label>
                        </div>
                        <div class="option">
                            <input type="checkbox" name="tdc_paywall_data[key_justification]" value="1" <?php if( $this->options['key_justification'] ) echo 'checked="checked"'; ?> />
                            <label for="tdc_paywall_data[key_justification]"><?php _e( 'Override default and display key before title' ); ?></label>
                        </div>
                        <div class="option">
                            <input type="checkbox" name="tdc_paywall_data[home_lock]" value="1" <?php if( $this->options['home_lock'] ) echo 'checked="checked"'; ?> />
                            <label for="tdc_paywall_data[home_lock]"><?php _e( 'Lock out home page' ); ?></label>
                            <p class="dmcss_description">Enabling this will put the home page behind the subscriber wall.</p>
                        </div>
                        <div class="option">
                            <input type="checkbox" name="tdc_paywall_data[category_lock]" value="1" <?php if( $this->options['category_lock'] ) echo 'checked="checked"'; ?> />
                            <label for="tdc_paywall_data[category_lock]"><?php _e( 'Lock out category pages' ); ?></label>
                            <p class="dmcss_description">Enabling this will put cateogories with locked stories behind the subscriber wall.</p>
                        </div>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'User Name field label' ); ?></th>
                    <td>
                        <input type="text" name="tdc_paywall_data[field_label]" value="<?php echo $this->options['field_label']; ?>" size="50" />
                        <p class="dmcss_description">Leave blank to display default:</p>
                        <div class="dmcss_message">
                            <?php echo( dmcss_wp::DEFAULT_FIELD_LABEL ); ?>
                        </div>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Invalid login message' ); ?></th>
                    <td>
                        <textarea name="tdc_paywall_data[invalid_email]"><?php echo stripslashes( $this->options['invalid_email'] ); ?></textarea>
                        <p class="dmcss_description">Leave blank to display default:</p>
                        <div id="dmcss_login_error" class="dmcss_message">
                            <?php echo( dmcss_wp::DEFAULT_INVALID_EMAIL ); ?>
                        </div>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Message for users without sufficient access' ); ?></th>
                    <td>
                        <textarea name="tdc_paywall_data[no_access]"><?php echo stripslashes( $this->options['no_access'] ); ?></textarea>
                        <p class="dmcss_description">Leave blank to display default:</p>
                        <div id="dmcss_login_error" class="dmcss_message">
                            <?php echo( $this->DEFAULT_NO_ACCESS ); ?>
                        </div>
                    </td>
                    </tr><?php
                // Only show the duplicate session message if we're validating tokens
                    if( $this->options['validate_tokens'] ): ?>
                        <tr valign="top">
                            <th scope="row"><?php _e( 'Duplicate session message' ); ?></th>
                            <td>
                                <textarea name="tdc_paywall_data[duplicate_session]"><?php echo stripslashes( $this->options['duplicate_session'] ); ?></textarea>
                                <p class="dmcss_description">Leave blank to display default:</p>
                                <div id="dmcss_login_error" class="dmcss_message">
                                    <?php echo( dmcss_wp::DEFAULT_DUPLICATE_SESSION_MESSAGE ); ?>
                                </div>
                            </td>
                            </tr><?php
                        endif; ?>
                        <tr valign="top">
                            <th scope="row"><?php _e( 'Session expired message' ) ?></th>
                            <td>
                                <textarea name="tdc_paywall_data[session_expired]"><?php echo stripslashes( $this->options['session_expired'] ); ?></textarea>
                                <p class="dmcss_description">Leave blank to display default:</p>
                                <div id="dmcss_login_error" class="dmcss_message">
                                    <?php echo( dmcss_wp::DEFAULT_SESSION_EXPIRED ); ?>
                                </div>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e( 'Logged out message' ); ?></th>
                            <td>
                                <textarea name="tdc_paywall_data[logged_out]"><?php echo stripslashes( $this->options['logged_out'] ); ?></textarea>
                                <p class="dmcss_description">Leave blank to display default:</p>
                                <div id="dmcss_login_message" class="dmcss_message">
                                    <?php echo( dmcss_wp::DEFAULT_LOGGEDOUT_MSG ); ?>
                                </div>
                            </td>
                            </tr><?php
                // Only show registered user marketing if we allow registration ?>
                <tr valign="top" <?php if( !$this->options['allow_registered_users'] ) { echo( 'style="display:none;"' ); } ?>>
                    <th scope="row"><?php _e( 'Registered User Marketing' ); ?></th>
                    <td>
                        <div class="option">
                            <label for="tdc_paywall_data[registered_marketing]" class="section-header"><?php _e( 'Custom Marketing Content' ); ?></label><br>
                            <textarea name="tdc_paywall_data[registered_marketing]" cols="80" rows="20"><?php echo stripslashes( $this->options['registered_marketing'] ); ?></textarea>
                            <p class="dmcss_description">Marketing content shown when registered users view content.  HTML allowed.</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit"><input class="button-primary" type="submit" value="Save Changes" /></p>
    </form>
    </div><?php
}

public function import_settings_page() {
    global $blog_id, $wpdb;

    settings_errors( 'tdc_paywall_data' ); ?>
    <div class="wrap paywall_admin">
        <h2><?php _e( 'BC Paywall PSA' ); ?></h2>
        <h3><?php _e( 'Paywall Settings Import' ); ?></h3>
        <form method="post" action="options.php" enctype="multipart/form-data">
            <?php settings_fields( 'tdc_paywall_data'); ?>
            <?php $this->show_paywall_data( $this->options ); ?>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row"><label for="tdc_paywall_data[import_souce_blog]"><?php _e('Copy Settings From') ?></label></th>
                        <td>
                            <div class="option">
                                <select name="tdc_paywall_data[import_souce_blog]">
                                    <?php
                                    $blogs = $wpdb->get_results('SELECT blog_id, path FROM wp_blogs WHERE blog_id != '.$blog_id.' AND deleted = 0 AND mature = 0 ORDER BY blog_id;');
                                    foreach( $blogs as $blog ){
                                        if( $blog->path == '/' ) {
                                            $blog->path = 'main blog';
                                        }
                                        else {
                                            $blog->path = preg_replace( '/\//', '', $blog->path );
                                        }
                                        echo('<option value="'.$blog->blog_id.'">'.$blog->path.'</option>');
                                    }
                                    ?>
                                </select>
                                <p class="dmcss_description">Importing data will REPLACE ALL BC Paywall PSA settings on this blog with ones from the chosen blog.</p>
                            </div>
                            <div class="option" style="margin-top:10px">
                                <input type="checkbox" id="paywall_confirm_import" name="tdc_paywall_data[confirm_import]" value="1"/>
                                <label for="tdc_paywall_data[confirm_import]"><?php _e('Import from Selected Blog') ?></label>
                                <p class="dmcss_description">Click above to confirm data import.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="submit"><input id="paywall_import_button" class="button-primary" type="submit" value="Save Changes" disabled="disabled"/></p>
        </form>
        </div><?php
    }

    public function layout_settings_page() {
        settings_errors( 'tdc_paywall_data' ); ?>
        <div class="wrap paywall_admin">
            <h2><?php _e( 'BC Paywall PSA' ); ?></h2>
            <h3><?php _e( 'Paywall Layout' ); ?></h3>
            <form method="post" action="options.php" enctype="multipart/form-data">
                <?php settings_fields( 'tdc_paywall_data'); ?>
                <?php $this->show_paywall_data( $this->options ); ?>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row"><?php _e( 'Paywall Excerpt Length' ); ?></th>
                            <td>
                                <div class="option">
                                 <input type="hidden" value="0" name="tdc_paywall_data[paywall_excerpt_paragraphs]" />
                                 <input name="tdc_paywall_data[paywall_excerpt_paragraphs]" type="checkbox" value="1" <?php if( $this->options['paywall_excerpt_paragraphs']){echo 'checked="checked"';} ?> />
                                 <label for="tdc_paywall_data[paywall_excerpt_paragraphs]" ><?php _e( 'Use Excerpt Paragraphs' );?></label><br/>
                                 <label for="tdc_paywall_data[paywall_excerpt_length]" class="section-header"><?php _e( 'Paywall Excerpt Length' ); ?></label>
                                 <input name="tdc_paywall_data[paywall_excerpt_length]" value = "<?php echo stripslashes( $this->options['paywall_excerpt_length'] ); ?>" />
                                 <p class="dmcss_description">The default is 55 words. Leave blank for default. Otherwise specify  number of paragraphs or words.</p>
                             </div>
                         </td>
                     </tr>
                     <tr valign="top">
                        <th scope="row"><?php _e( 'Before Login Message' ); ?></th>
                        <td>
                            <div class="option">
                                <label for="tdc_paywall_data[before_login_text]" class="section-header"><?php _e( 'Custom Before Login Formatting' ); ?></label><br>
                                <textarea name="tdc_paywall_data[before_login_text]" cols="80" rows="20"><?php echo stripslashes( $this->options['before_login_text'] ); ?></textarea>
                                <p class="dmcss_description">Available form elements: $subscribe_link, $forgot_password_link</p>
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Subscribe Tab' ); ?></th>
                        <td>
                            <div class="option">
                                <label for="tdc_paywall_data[subscribe_display]"><?php _e( 'Display options for subscribe tab' ); ?></label>
                                <select name="tdc_paywall_data[subscribe_display]">
                                    <?php
                                    $display_options = array(  'Deactivate', 'Custom Display', 'Compare Page' );
                                    $policy = $this->options['subscribe_display'];
                                    foreach( $display_options as $option ) {
                                        if( preg_match( '/' . $option . '/i', $policy ) ) {
                                            echo( '<option value="' . $option . '" selected="selected">' . $option . '</option>' );
                                        }
                                        else {
                                            echo( '<option value="' . $option . '">' . $option . '</option>' );
                                        }
                                    }?>
                                </select>
                            </div>
                            <div class="option">
                                <label for="tdc_paywall_data[compare_path]"><?php _e( 'Compare page path' ); ?></label>
                                <input size="80" type="text" name="tdc_paywall_data[compare_path]" value="<?php echo $this->options['compare_path']; ?>" />
                                <p class="dmcss_description">Example: entire 'compare' to show content of http://domainname.com/compare/</p>
                            </div>
                            <div class="option">
                                <label for="tdc_paywall_data[custom_subscribe_text]" class="section-header"><?php _e( 'Custom Output Formatting' ); ?></label><br>
                                <textarea name="tdc_paywall_data[custom_subscribe_text]" cols="80" rows="20"><?php echo stripslashes( $this->options['custom_subscribe_text'] ); ?></textarea>
                                <p class="dmcss_description">Available form elements: $subscribe_link, $forgot_password_link</p>
                            </div>
                        </td>
                        </tr><?php
                // Only show registered user configuration if we allow registration ?>
                <tr valign="top" <?php if( !$this->options['allow_registered_users'] ) { echo( 'style="display:none;"' ); } ?>>
                    <th scope="row"><?php _e( 'Register Tab' ); ?></th>
                    <td>
                        <div class="option">
                            <input type="checkbox" name="tdc_paywall_data[custom_register]" value="1" <?php if( $this->options['custom_register'] ) echo 'checked="checked"'; ?> />
                            <label for="custom_register"><?php _e('Use Custom Formatting') ?></label>
                        </div>
                        <div class="option">
                            <label for="tdc_paywall_data[custom_register_text]" class="section-header"><?php _e( 'Custom Output Formatting' ); ?></label><br>
                            <textarea name="tdc_paywall_data[custom_register_text]" cols="80" rows="20"><?php echo stripslashes( $this->options['custom_register_text'] ); ?></textarea>
                            <p class="dmcss_description">Available form elements: $register_form, $subscribe_link, $forgot_password_link</p>
                        </div>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Login Tab' ); ?></th>
                    <td>
                        <div class="option">
                            <input type="checkbox" name="tdc_paywall_data[custom_login]" value="1" <?php if( $this->options['custom_login'] ) echo 'checked="checked"'; ?> />
                            <label for="tdc_paywall_data[custom_login]"><?php _e('Use Custom Formatting') ?></label>
                            <p class="dmcss_description">Custom formatting overrides display of subscribe and forgot password links.</p>
                        </div>
                        <div class="option">
                            <input type="checkbox" name="tdc_paywall_data[show_subscribe]" value="1" <?php if( $this->options['show_subscribe'] ) echo 'checked="checked"'; ?> />
                            <label for="tdc_paywall_data[show_subscribe]"><?php _e('Show subscribe link') ?></label>
                        </div>
                        <div class="option">
                            <input type="checkbox" name="tdc_paywall_data[show_forgot_password]" value="1" <?php if( $this->options['show_forgot_password'] ) echo 'checked="checked"'; ?> />
                            <label for="tdc_paywall_data[show_forgot_password]"><?php _e('Show forgot password link') ?></label>
                        </div>
                        <div class="option">
                            <label for="tdc_paywall_data[custom_login_text]" class="section-header"><?php _e( 'Custom Output Formatting' ); ?></label><br>
                            <textarea name="tdc_paywall_data[custom_login_text]" cols="80" rows="20"><?php echo stripslashes( $this->options['custom_login_text'] ); ?></textarea>
                            <p class="dmcss_description">Available form elements: $login_form, $subscribe_link, $forgot_password_link</p>
                        </div>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Pre-' ); ?></th>
                    <td>
                        <div class="option">
                            <label for="tdc_paywall_data[pre_paywall_sidebar]"><?php _e( 'Pre-paywall sidebar' ); ?></label>
                            <select name="tdc_paywall_data[pre_paywall_sidebar]">
                                <?php
                                global $wp_registered_sidebars;
                                $pre_paywall_sidebar = $this->options['pre_paywall_sidebar'];
                                echo '	<option value="">-- None --</option>';
                                foreach ($wp_registered_sidebars as $a_sidebar)
                                {
                                    echo '<option value="'.$a_sidebar['id'].'"';
                                    if ( $pre_paywall_sidebar == $a_sidebar['id'] ) {
                                        echo " selected='selected'";
                                    }
                                    echo '>'.$a_sidebar['name'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="option">
                            <label for="tdc_paywall_data[post_paywall_sidebar]"><?php _e( 'Post-paywall sidebar' ); ?></label>
                            <select name="tdc_paywall_data[post_paywall_sidebar]">
                                <?php
                                $post_paywall_sidebar = $this->options['post_paywall_sidebar'];
                                echo '	<option value="">-- None --</option>';
                                foreach ($wp_registered_sidebars as $a_sidebar)
                                {
                                    echo '<option value="'.$a_sidebar['id'].'"';
                                    if ( $post_paywall_sidebar == $a_sidebar['id'] ) {
                                        echo " selected='selected'";
                                    }
                                    echo '>'.$a_sidebar['name'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit"><input class="button-primary" type="submit" value="Save Changes" /></p>
    </form>
    </div><?php
}

public function bc_config_settings_page(){
    settings_errors( 'tdc_paywall_data' ); ?>

    <div id="tdc-paywall-bc-config" class="wrap paywall_admin">
         <h2><?php _e( 'BC Paywall PSA' ); ?></h2>
         <h3><?php _e( 'Blueconic Configurations' ); ?></h3>

         <form method="post" action="options.php" enctype="multipart/form-data">
             <?php settings_fields( 'tdc_paywall_data'); ?>
             <?php $this->show_paywall_data( $this->options ); ?>
             <table class="form-table">
                 <tbody>
                     <tr valign="top">
                        <th scope="row"><?php _e( 'Blueconic Header Script' ); ?></th>
                        <td>
                            <div class="option">
                                <textarea name="tdc_paywall_data[bc_head_script]" cols="200" rows="200" class="bc_head_script"><?php echo $this->options['bc_head_script']; ?></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Blueconic Footer Script' ); ?></th>
                        <td>
                            <div class="option">
                                <textarea name="tdc_paywall_data[bc_footer_script]" cols="200" rows="200" class="bc_footer_script"><?php echo $this->options['bc_footer_script']; ?></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Subscribe page embedded data' ); ?></th>
                        <td>
                            <div class="option">
                                <textarea name="tdc_paywall_data[sub_embedded_data]" cols="200" rows="200" class="sub_embedded_data"><?php echo $this->options['sub_embedded_data']; ?></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Technavia URL' ); ?></th>
                        <td>
                            <div class="option">
                                <input type="text" name="tdc_paywall_data[technavia_link]" class="technavia_link" value="<?php echo $this->options['technavia_link']; ?>" style="width: 500px;">
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Blueconic Acton Dialogue Action URL' ); ?></th>
                        <td>
                            <div class="option">
                                <input type="text" name="tdc_paywall_data[acton_action_url]" class="acton_action_url" value="<?php echo $this->options['acton_action_url']; ?>" style="width: 500px;">
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Blueconic Acton Dialogue Variant ID For Conversion' ); ?></th>
                        <td>
                            <div class="option">
                                <input type="text" name="tdc_paywall_data[bc_variant_id]" class="bc_variant_id" value="<?php echo $this->options['bc_variant_id']; ?>" style="width: 500px;">
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <?php _e( 'Pages for page attribute meta box hide' ); ?>
                            <br/><em>Add multiple pages slug comma separated</em>    
                        </th>
                        <td>
                            <div class="option">
                                <textarea name="tdc_paywall_data[meta_box_hide]" cols="200" rows="200" class="meta_box_hide"><?php echo $this->options['meta_box_hide']; ?></textarea>
                            </div>
                        </td>
                    </tr>
                 </tbody>
             </table>
             <p class="submit"><input class="button-primary" type="submit" value="Save Changes" /></p>
         </form>
    </div>
<?php        
}

public function publication_settings_page() {
    settings_errors( 'tdc_paywall_data' ); ?>
    <div class="dmcss_templates">
        <table>
            <tr class="dmcss_new_row">
                <td> </td>
                <?php
                echo( $this->pub_input_td( array(), "tdc_paywall_data[publications][%i%]", true ) );
                ?>
                <td class="dmcss_row_add"><button type="button" class="dmcss_row_add_button" disabled="disabled">add</button></td>
            </tr>
        </table>
    </div>
    <div class="wrap paywall_admin">
        <h2><?php _e('BC Paywall PSA') ?></h2>
        <h3><?php _e('Publications') ?></h3>
        <form method="post" action="options.php" enctype="multipart/form-data">
            <?php settings_fields('tdc_paywall_data'); ?>
            <?php $this->show_paywall_data( $this->options ); ?>
            <table class="widefat">
                <thead>
                    <tr><th class="dmcss_pub_delete">Delete</th><th>Title</th><th>Code</th><th>Blog SubRates (separate multiples with comma)</th><th></th></tr>
                </thead>
                <tbody>
                    <?php
                    $publication = $this->options['publications'];
                    for( $i=0; $i < count($publication); $i++ ): ?>
                        <tr class="dmcss_pub<?php if( $i % 2 === 0 ): ?> alternate <?php endif; ?>">
                            <td class="dmcss_pub_delete"><input type="checkbox" name="tdc_paywall_data[publications][<?php echo( $i ); ?>][delete]" value="1"/></td>
                            <?php echo( $this->pub_input_td( $publication[$i], "tdc_paywall_data[publications][$i]" ) ); ?>
                            <td> <?php if( 0 == $i ) echo( '(primary)' ); ?></td>
                        </tr>
                    <?php endfor; ?>
                    <tr class="dmcss_new_row <?php if( 0 == count($publication) % 2 ): ?> alternate <?php endif; ?>">
                        <td> </td>
                        <?php $pub = isset($publication[$i]) ? $publication[$i] : ''; ?>
                        <?php echo( $this->pub_input_td( $pub, "tdc_paywall_data[publications][$i]", true ) ); ?>
                        <td class="dmcss_row_add"><button  type="button" class="dmcss_row_add_button" disabled="disabled">add</button></td>
                    </tr>
                </tbody>
            </table>
            <p class="dmcss_description">If multiple publications are used, the primary publication will be used for account tracking of users with multiple subscriptions.</p>
            <table class="form-table" style="margin-top:20px">
                <tbody>
                    <tr valign="top">
                        <th scope="row"><label for="tdc_paywall_data[subscribe_url]"><?php _e( 'Subscribe url' ) ?></label></th>
                        <td>
                            <input type="text" name="tdc_paywall_data[subscribe_url]"  size="80" value="<?php echo( $this->options['subscribe_url'] ); ?>" />
                            <p class="dmcss_description">Required for the $subscribe_link macro and for the show subscribe link option on login tab.</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="tdc_paywall_data[forgot_pwd_url]"><?php _e( 'Forgot password url' ); ?></label></th>
                        <td>
                            <input type="text" size="80" name="tdc_paywall_data[forgot_pwd_url]" value="<?php echo( $this->options['forgot_pwd_url'] ); ?>" />
                            <p class="dmcss_description">Required for the $forgot_password_link macro and for the show forgot password link option on login tab.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="submit"><input class="button-primary" type="submit" name="submit" value="Save Changes" /></p>
        </form>
        </div><?php
    }

    public function registration_report_page() {
        ?>
        <div class="wrap paywall_ru_users">
            <h2><?php _e('Registered User Report') ?></h2>

            <table class=widefat>
                <thead>
                    <tr><th>User Email</th><th>Registration Date</th></tr>
                </thead>
                <?php

                global $wpdb;

                $users = "select user_email, date(user_registered) as 'user_registered' from wp_users where id IN (SELECT user_id from wp_usermeta where meta_key like '%_capabilities' and meta_value like '%TDC_Registered_User%')";
                $results = $wpdb->get_results($users);
                if (count($results) > 0) {
                    foreach($results as $row) {
                        echo '<tr><td>' . $row->user_email . '</td><td>' . $row->user_registered . '</td></tr>' . "\n";
                    }
                }
                else {
                    echo '<tr><td colspan=2><i>No Registered users</i></td></tr>';
                }

                ?>

            </table>

        </div>
        <?php
    }

    public function paywall_user_log() {
        ?>
        <div class="wrap paywall_ru_users">
            <h2><?php _e('User Log Report') ?></h2>

            <?php
            global $current_user;
            if( preg_match( '/thedolancompany\.com|dolanmedia\.com/', $current_user->user_email ) ) { ?>
                <div class="alignright">
                  <form method="POST" action="<?php echo get_site_url();?>/wp-admin/admin.php?page=tdc-paywall-user-logs">
                      <input type="hidden" name="clear_paywall_log"></input>
                      <input type="submit" name="submit_paywall_log" value="Clear Logs">
                  </form>
              </div>

              <div class="alignright">
                  <form method="POST" action="<?php echo get_site_url();?>/wp-admin/admin.php?page=tdc-paywall-user-logs">
                      <input type="hidden" name="paywall_log_export"></input>
                      <input type="submit" name="submit_paywall_log" value="Export To Excel">
                  </form>
              </div>

          <?php } ?>

          <table class=widefat>
            <thead>
               <tr>
                <th>First Name</th>
                <th>Last  Name</th>
                <th>Email </th>
                <th>Username </th>
                <th>Time </th>
                <th>IP Address </th>
                <th>Blog ID </th>
                <th>User Type </th>
            </tr>
        </thead>
        <?php

        global $wpdb;
        $table_name = $wpdb->prefix . 'paywall_users_logs';
        $users = 'select * from '.$table_name.'';
        $results = $wpdb->get_results($users);
        if (count($results) > 0) {
            foreach($results as $row) {
                echo '<tr><td>' . $row->first_name . '</td><td>' . $row->last_name . '</td><td>' . $row->email . '</td><td>' . $row->username . '</td><td>' . $row->time . '</td><td>' . $row->ip_address . '</td><td>' . $row->blog_id . '</td><td>' . $row->user_role . '</td></tr>' . "\n";
            }
        }
        else {
            echo '<tr><td colspan=2><i>No Logs Yet</i></td></tr>';
        }

        ?>

    </table>

</div>
<?php
}

}
add_action('admin_head', 'zephr_admin_custom_css');
function zephr_admin_custom_css() {
  echo '<style>
  label[for=dmcss_pub_code-hide] p{display:none;}
  </style>';
}
