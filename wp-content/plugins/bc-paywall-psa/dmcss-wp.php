<?php
/*
Plugin Name: BC Paywall PSA
Plugin URI: http://www.thedolancompany.com
Description: Integrates PSA with Wordpress users
Version: 3.0.1
Author: Asentech

Modifications:
Sept 2014: Updated class structure; avoids pulling in non-class PHP files
*/

 // Disallow direct access
if( !defined( 'ABSPATH' ) ) {
	die( 'Direct access not allowed.' );
}

// Initialize bypass constant if not defined
if( !defined( 'PAYWALL_BYPASS_FILE' ) ) {
	define( 'PAYWALL_BYPASS_FILE', dirname(__FILE__) . '/paywall-bypass.txt' );
}

// Disable user logging by default
if( !defined( 'PAYWALL_LOG_USERS' ) ) {
	define( 'PAYWALL_LOG_USERS', false );
}

// Start plugin
global $dmcss_wp; 
if( is_admin() ) {
	require_once( dirname( __FILE__ ) . '/bc-paywall-admin.php' );
	$dmcss_wp = new bc_paywall_admin();
}
else {
	require_once( dirname( __FILE__ ) . '/bc-paywall.php' );
	$dmcss_wp = new bc_paywall();
}

/* call custom templates from plugin */
add_filter( 'theme_page_templates', 'add_page_template_to_plugin' );
function add_page_template_to_plugin($templates){
    
   $templates[plugin_dir_path( __FILE__ ) . 'templates/template-subscr-psa.php'] = __( 'Acton Subscribe Page');
   $templates[plugin_dir_path( __FILE__ ) . 'templates/template-subscribeDetails.php'] = __( 'Details Subscribe Page');
   $templates[plugin_dir_path( __FILE__ ) . 'templates/template-manage-subscription.php'] = __( 'Manage Subscription Page');
   $templates[plugin_dir_path( __FILE__ ) . 'templates/template-subscribe.php'] = __( 'Subscribe Page');
   $templates[plugin_dir_path( __FILE__ ) . 'templates/template-paywall-thank-you.php'] = __( 'Thank You Page Paywall');
   $templates[plugin_dir_path( __FILE__ ) . 'templates/user-login.php'] = __( 'User login');
   return $templates;
}

/* Attach templates to pages */
add_filter( 'template_include', 'pt_change_page_template', 99 );
function pt_change_page_template($template){
	
	$page_array = array('subscribe', 'subscribe-2', 'print-digital', 'print-digital-poc', 'thank-you', 'thank-you-subscribe', 'manage-account-fc', 'manage-account-em', 'user-login');

    if (is_page($page_array)) {
        $meta = get_post_meta(get_the_ID());
        
        if (!empty($meta['_wp_page_template'][0]) && $meta['_wp_page_template'][0] != $template) {
            $template = $meta['_wp_page_template'][0];
        }
    } 

    return $template;
}

/* BC Acton Dialogue form submission via ajax */
add_action('wp_ajax_nopriv_bc_acton_submission', 'process_acton_submission');
add_action('wp_ajax_bc_acton_submission', 'process_acton_submission');

function process_acton_submission(){
	
	$body = array();
	$bc_config = get_option('tdc_paywall_data');
	$bcvariantID = $bc_config['bc_variant_id'];  
    
    $acton_details = get_option("btm_acton_details");
    $base_url = $acton_details['btm_acton_end_point'];
	$acton_listid = $acton_details['acton_listid'];
	$site_id = $acton_details['btm_siteid'];

	$_auth = array(
		'client_id' => $acton_details['btm_acton_client_id'],
		'client_secret' => $acton_details['acton_secret_key'],
	);
	$_auth['grant_type'] = 'password';
	$_auth['username'] = $site_id.$acton_details['acton_user_name'];
	$_auth['password'] = $acton_details['acton_password'];

	$cht = curl_init();
    $header = array(
        'POST',
        'HTTP/1.1',
        'Host: restapi.actonsoftware.com',
        'Accept: application/json',
        'Content-Type: application/x-www-form-urlencoded',
    );
    curl_setopt($cht, CURLOPT_HTTPHEADER, $header);
    curl_setopt($cht, CURLOPT_URL, $base_url . '/token');
    curl_setopt($cht, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cht, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($cht, CURLOPT_TIMEOUT, 30);
    curl_setopt($cht, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($cht, CURLOPT_POST, 1);
    curl_setopt($cht, CURLOPT_POSTFIELDS, http_build_query($_auth));
	$token_result =  json_decode(curl_exec($cht));

	$refresh_token = $token_result->refresh_token;
    $expires_at = $token_result->expires_in;
    $access_token = $token_result->access_token;
    
    if (isset($refresh_token) && isset($access_token)){
        if (isset($expires_at) && $expires_at){
            $expires_at = mktime(date("H"), date("i"), date("s")+$expires_at, date("m"), date("d"), date("Y"));
        } else {
			$expires_at = mktime(date("H"), date("i"), date("s")+3600, date("m"), date("d"), date("Y"));
       	}
    }

    $access_token_data = array('refresh_token' => "$refresh_token", 'access_token' => "$access_token", 'expires_at' => $expires_at);

    update_option('acton_auth_response', json_encode($access_token_data)); 
   
   	if(!empty($acton_details['display_name'])){
   		$fields = array_filter($acton_details['display_name']);
   	}

   	if(!empty($fields)){
   		foreach($fields as $field){
   			$body["$field"] = 'true';
   		}
   	}

    if(isset($_POST['email']) && $_POST['email'] != ''){
    	$email = $_POST['email'];

    	$body['SourceType'] = 'Blueconic';
    	$body['SourceCode'] = 'Free_Article_View';
    	$body['Email'] = $_POST['email'];

    	$ch = curl_init();
		$headers = array(
			"PUT /api/1/list/$acton_listid/record?email=$email",
			'HTTP/1.1',
			'Host: restapi.actonsoftware.com',
			"Authorization: Bearer $access_token",
			'Cache-Control: no-cache',
			'Content-Type: application/json',
		);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $base_url . "/api/1/list/" . $acton_listid . "/record?email=".$email);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
		curl_setopt($ch, CURLOPT_TIMEOUT, 300);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
		$result = curl_exec($ch);
		$errors = curl_error($ch);
		curl_close($ch);

		$response = json_decode($result);
		if($response->status == 'success'){
			$response->bcvariantID = $bcvariantID;
		}

    	echo json_encode($response);
    }

    wp_die();
}

/* Hide page attributes meta box for selected page */
add_action( 'admin_menu', 'remove_page_attribute_meta_boxes');
function remove_page_attribute_meta_boxes() {
	if(is_admin()){

		$current_user_id = get_current_user_id();
		$user = get_userdata($current_user_id );
		
		if(isset($_GET['post'])){
			$post_id = $_GET['post'];
		} else {
			$post_id = 0;
		}	

		$post = get_post($post_id);

		$bc_config = get_option('tdc_paywall_data');
		$pages_array = explode(',', $bc_config['meta_box_hide']);
		$pages_slug = array_map('trim', $pages_array);

		if($user->data->user_login == 'asentechssg' && in_array($post->post_name, $pages_slug)){
			remove_meta_box('pageparentdiv', 'page', 'side');
		}
		
	}
    
}

abstract class dmcss_wp {
	// Role and capability constants
	const DMCSS_LEGACY_ROLE = 'DMC_CircPro_User';
	const DMCSS_LEGACY_CAPABILITY = 'DMC_Subscriber_Only';
	const SUBSCRIBER_ROLE = 'TDC_Subscriber';
	const SUBSCRIBER_ROLE_LABEL = 'TDC Subscriber';
	const SUBSCRIBER_CAPABILITY = 'TDC_Subscriber_Only';
	const REGISTERED_USER_ROLE = 'TDC_Registered_User';
	const REGISTERED_USER_LABEL = 'TDC Registered User';
	const REGISTERED_USER_CAPABILITY = 'TDC_Registered_Only';

	// Message and file pointer constants
	const DEFAULT_FIELD_LABEL = 'User Name/E-mail:';
	const DEFAULT_INVALID_EMAIL = '<strong>ERROR:</strong> User name and Password are required to log in.';
	const DEFAULT_SESSION_EXPIRED = 'Your logged in session has expired.  Please log in again.';
	const DEFAULT_LOGGEDOUT_MSG = 'You are now logged out.';
	const DEFAULT_DUPLICATE_SESSION_MESSAGE = '<strong>ERROR</strong>: Your session was discontinued because another user
	has logged in with the same username and password.  Please call customer service at 1-800-451-9998 if you think your
	login information has been compromised.';
	const DEFAULT_RESET_FAILURE_MSG = 'Automated password reset is currently unavailable.  Please call customer service
	at 1-800-451-9998 for assistance.';
	const DEFAULT_BYPASSED_MSG = 'We have temporarily suspended access control on our stories.';
	const DEFAULT_KEY_LOCATION = '/tdc-paywall/images/key.gif';
	const DEFAULT_CAPTCHA_ERROR = 'Invalid CAPTCHA response; please try again.';

	// Array to hold paywall settings
	protected $options;
	// Array of security policy options
	protected $security_options = array( 'Always Free', 'Subscriber Only', 'Never Free' );
	// Variable for no access message
	protected $DEFAULT_NO_ACCESS = 'You have not subscribed to this publication.';
	// Variables for default key locations
	protected $default_key_location = '';
	//Set Pawayll Roles array
	protected $paywall_roles = array ( 'super admin', 'administrator', 'editor' , 'author', 'contributor', 'subscriber', 'TDC_Subscriber', 'TDC_Registered_User' );

	/*
	 * Class constructor
	 * Set up actions & filters, & initialize non-constant class vars
	 *
	 * @return void
	 */
	public function __construct(){
		// Check for Legacy upgrade & Initialize WordPress Options
		$this->options = get_option( 'tdc_paywall_data', array() );
		//Add Version 
		$version = get_option('tdc_paywall_version');
		if( get_role( dmcss_wp::DMCSS_LEGACY_ROLE ) ) {
			$this->legacy_cleanup();
		}
		if( empty( $this->options ) ) {
			add_option( 'tdc_paywall_data', array() );
		}
		if( empty( $version ) ) {
			add_option( 'tdc_paywall_version','3.0.0' );
		}
			
		if( version_compare($version, '3.0.1', '<') ) {
			update_option( 'tdc_paywall_version','3.0.1' );
		    global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();
			$table_name = $wpdb->prefix . 'paywall_users_logs';
			if($wpdb->get_var( "show tables like '$table_name'" ) != $table_name) 
			    {
			        $sql = "CREATE TABLE $table_name (
			          id mediumint(9) NOT NULL AUTO_INCREMENT,
			          first_name varchar(55) DEFAULT '' NOT NULL,
			          last_name varchar(55) DEFAULT '' NOT NULL,
			          email varchar(100) DEFAULT '' NOT NULL,
			          username varchar(100) DEFAULT '' NOT NULL,
			          time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			          ip_address varchar(50) DEFAULT '' NOT NULL,
			          blog_id varchar(50) DEFAULT '' NOT NULL,
			          user_role varchar(50) DEFAULT '' NOT NULL,
			          PRIMARY KEY  (id)
			        ) $charset_collate;";

			        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			        dbDelta( $sql );
			    }
		}	
		
		// Configure security options
		if( !empty($this->options['allow_registered_users']) ) {
			array_push( $this->security_options, 'Registered User' );
		}
			
		// Append subscribe link to no access message
		if(isset($this->options['subscribe_display']) && preg_match( '/deactivate/i', $this->options['subscribe_display'] ) ) {
			if( !empty( $this->options['subscribe_url'] ) ) {
				$this->DEFAULT_NO_ACCESS .= '  Please <a href="' . $this->options['subscribe_url'] . '">click here</a> to subscribe to this publication, or call customer service at 1-800-451-9998.';
			}
			else {
				$this->DEFAULT_NO_ACCESS .= '  To subscribe to this publication, please call customer service at 1-800-451-9998.';
			}
		}

		// Initialize default key location variables
		$this->default_key_location = WP_PLUGIN_URL . dmcss_wp::DEFAULT_KEY_LOCATION;

		// Show debug info if enabled
		if( isset($this->options['show_debug']) ) {
			add_action( 'wp_footer', array( &$this, 'output_debug' ) );
		}

		// Add shortcode for loginout link
		add_shortcode( 'login', array( 'dmcss_wp', 'loginout_shortcode' ) );
	}

	/******** PUBLIC FUNCTIONS ********/

	/**
	 * Add custom role & capabilities
	 *
	 * @return void
	 */
	public static function activate(){
		global $wp_roles;
		// Create new roles for Subscribers & Registered Users
		add_role( dmcss_wp::SUBSCRIBER_ROLE, dmcss_wp::SUBSCRIBER_ROLE_LABEL, array( 'read'=> true, 'level_0'=> true, dmcss_wp::SUBSCRIBER_CAPABILITY => true, dmcss_wp::REGISTERED_USER_CAPABILITY => true ) );
		add_role( dmcss_wp::REGISTERED_USER_ROLE, dmcss_wp::REGISTERED_USER_LABEL, array( 'read'=> true, 'level_0'=> true, dmcss_wp::REGISTERED_USER_CAPABILITY => true ) );
		
		// Add ability to read locked-down content to existing roles
		$role = $wp_roles->get_role( 'administrator' );
		$role->add_cap( dmcss_wp::SUBSCRIBER_CAPABILITY );
		$role->add_cap( dmcss_wp::REGISTERED_USER_CAPABILITY );
		$role = $wp_roles->get_role( 'editor' );
		$role->add_cap( dmcss_wp::SUBSCRIBER_CAPABILITY );
		$role->add_cap( dmcss_wp::REGISTERED_USER_CAPABILITY );
		$role = $wp_roles->get_role( 'author' );
		$role->add_cap( dmcss_wp::SUBSCRIBER_CAPABILITY );
		$role->add_cap( dmcss_wp::REGISTERED_USER_CAPABILITY );
					
	}

	/**
	 * Remove custom role & capabilities
	 *
	 * @return void
	 */
	public static function deactivate(){
		global $wp_roles;
		// Remove roles for DMCSS Users
		remove_role( dmcss_wp::DMCSS_LEGACY_ROLE );
		remove_role( dmcss_wp::SUBSCRIBER_ROLE );
		remove_role( dmcss_wp::REGISTERED_USER_ROLE );
		
		// Remove ability to read locked-down content from existing roles
		$role = $wp_roles->get_role( 'administrator' );
		$role->remove_cap( dmcss_wp::SUBSCRIBER_CAPABILITY );
		$role->remove_cap( dmcss_wp::REGISTERED_USER_CAPABILITY );
		$role->remove_cap( dmcss_wp::DMCSS_LEGACY_CAPABILITY );
		$role = $wp_roles->get_role( 'editor' );
		$role->remove_cap( dmcss_wp::SUBSCRIBER_CAPABILITY );
		$role->remove_cap( dmcss_wp::REGISTERED_USER_CAPABILITY );
		$role->remove_cap( dmcss_wp::DMCSS_LEGACY_CAPABILITY );
		$role = $wp_roles->get_role( 'author' );
		$role->remove_cap( dmcss_wp::SUBSCRIBER_CAPABILITY );
		$role->remove_cap( dmcss_wp::REGISTERED_USER_CAPABILITY );
		$role->remove_cap( dmcss_wp::DMCSS_LEGACY_CAPABILITY );
	}

	/**
	 * Import plugin CSS
	 *
	 * @return void
	 */		
	 public function enqueueStylesheet(){
		wp_register_style('dmcssStylesheet', plugins_url('/style.css', __FILE__));
		wp_enqueue_style('dmcssStylesheet');
	}

	/**
	 * Output debug info in page footer
	 *
	 * @return void
	 */	
 	public function output_debug(){
		$debug = explode( ',', $this->options['debug_ips'] );
		$ip_regex = '/^10|192\.*/';
		$view_ip = $_SERVER['REMOTE_ADDR'];
		//Only show debugging if user's ip is valid debug ip
		if( $view_ip == '127.0.0.1' || preg_match( $ip_regex, $view_ip ) || array_search( $view_ip,$debug ) !== false ){
			// Check $_REQUEST & $_COOKIE for any messages
			if( isset( $_REQUEST['dmcss[messages]'] ) ) {
				$messages = $_REQUEST['dmcss[messages]'];
				//$output = 'request scope';
			}
			else{
				$messages = isset($_COOKIE['dmcss[messages]']) ? unserialize( stripslashes( $_COOKIE['dmcss[messages]'] ) ) : '';
				//$output = 'cookie scope';
			}

			// Walk through array and output messages arranged by type
			if( is_array( $messages ) && !empty( $messages ) ) {
				$output .= '<div class = "dmcss_messages"><h2>DMCSS Debugging</h2>' . '<p>Remote Address:' . $_SERVER['REMOTE_ADDR'] . '</p>';
				foreach( $messages as $type => $messages ){
					$cat = $type;
					if( is_array( $messages ) ) {
						foreach( $messages as $message ) {
							$output .= '
								<div class="dmcss_message dmcss_type_' . $cat . '">' .
									$message . '
								</div>';
						}
					}
				}
				echo( $output . '</div>' );				
			}
		}
	}

	/******** END PUBLIC FUNCTIONS ****/

	/******** STATIC FUNCTIONS ********/

	/**
	 * Add get variable specified by $key
	 *
	 * @param string $url 
	 * @param string $key 
	 * @return URL
	 */
	public static function add_get_var($url, $key, $val){
		// make sure it doesn't already exist
		$url = self::remove_get_var($url, $key);
		
		// if we already have GET variables...
		if(preg_match('/\?.+/', $url))		$result = $url."&$key=$val";
		// if we are ending with a '?'...
		elseif(preg_match('/\?$/', $url))	$result = $url."$key=$val";
		// if we have no GET variables...
		else								$result = $url."?$key=$val";
		
		return $result;
	}

	/**
	 * Return current user's acct number for primary pub or false if no valid user
	 * @param string $pubcode - site's primary code will be used if empty
	 * @return acctNumber or false
	 */
	public static function get_dmcss_acct( $pubcode = false ){
		// If no logged in user, return false
		if( is_user_logged_in() ){
			if( !$pubcode ) {
				// Try to look up pubcode; return false if we can't find it
				$options = get_option( 'tdc_paywall_data' );
				if( $options && isset( $options['publications'] ) ) {
					$pubcode = $options['publications'][0]['code'];
				}
			}

			// Get current logged in user if exists
			$current_user = wp_get_current_user();
			$dmcss_user = get_user_meta( $current_user->ID, 'dmcss_current_user', true );

			// Return valid acct number if available
			if( $pubcode && !empty( $dmcss_user ) && isset( $dmcss_user[$pubcode]['accountNumber'] ) ) {
				return $dmcss_user[$pubcode]['accountNumber'];
			}
			elseif( !empty( $dmcss_user ) ) {
				// If no valid acct for $pubcode; return first valid acct# found
				foreach( $dmcss_user as $pub ) {
					if( isset( $pub['accountNumber'] ) ) {
						return $pub['accountNumber'];
					}
				}
			}
		}
		return false;
	}

	/**
	 * Return password reset URL; either subscribe site or WP depending on whether registered users are allowed
	 * WP URL if registration allowed, otherwise subscribe
	 *
	 * @return string password reset URL or boolean false if unavailable
	 */
	public static function get_password_url() {
		$options = get_option( 'tdc_paywall_data' );
		if( $options['allow_registered_users'] ) {
			return wp_lostpassword_url();
		}
		elseif( !empty( $options['forgot_pwd_url'] ) ) {
			return $options['forgot_pwd_url'];
		}
		else {
			return false;
		}
	}

	/**
	 * Return array containing primary pubcode or all pubcodes for site
	 *
	 * @param boolean $all_codes - whether to return all codes or primary if false
	 * @return array $codes or false if not set
	 */
	public static function get_publication_code( $all_codes = false ) {
		$options = get_option( 'tdc_paywall_data' );

		if( !isset( $options['publications'] ) || !is_array( $options['publications'] ) ) {
			// Return false if no pubs available
			return false;
		}
		elseif( !$all_codes ) {
			// Return primary if we don't want all pubs
			return array( $options['publications'][0]['code'] );
		}
		else {
			// Return array of all pubcodes
			$codes = array();
			foreach( $options['publications'] as $pub ) {
				array_push( $codes, $pub['code'] );
			}
			return $codes;
		}
	}

	/**
	 * Returns publication name from option data.
	 *  
	 * @param string $code The publication code, example: molw, libn, djc
	 * @return string The publication name; false on failure
	 */
	public static function get_publication_name( $code ){
		// Retrieve paywall data
		$options = get_option( 'tdc_paywall_data' );

		// Return publication name if available
		if( $options && isset( $options['publications'] ) ) {
			foreach( $options['publications'] as $pub ) {
				if( preg_match( "/$code/i", $pub['code'] ) ) {
					return $pub['name'];
				}
			}
		}
		return false;
	}

	/*
	 * Shortcode to generate loginout link
	 *
	 * @param array $atts
	 * @return void
	 */
	public static function loginout_shortcode( $atts ) {
		// If user signed in, show Wordpress logout link
		if( is_user_logged_in() ) {
			$link = '<a href="' . wp_logout_url() . '">LOG OUT</a>';
		}
		// For signed in circ user / WP wrapper; show logout link and redirect to blog home
		elseif( isset( $_COOKIE['dmcss']['current_user'] ) && preg_match( '/accountNumber/', $_COOKIE['dmcss']['current_user'] ) && preg_match( '/coldfusion/i', $_SERVER['HTTP_USER_AGENT'] ) ) {
			$link = '<a href="' . self::add_get_var( site_url(), 'dmcss', 'logout' ) . '">' . __('LOG OUT') . '</a>';
		}
		// Otherwise show link to custom dmcss login screen
		else {
			$link = '<a href="' . self::add_get_var( $_SERVER['REQUEST_URI'], 'dmcss', 'login' ) . '">' . __( 'LOG IN' ) . '</a>';
			if( isset($_GET['loggedout']) && $_GET['loggedout'] == 'true' ) {
				$link = self::remove_get_var( $link, 'loggedout' );
			}
		}
		return apply_filters( 'loginout', $link );
	}	

	/**
	 * Return true / false if post is currently free to view
	 *
	 * @param object $post
	 * @return true / false
	 */
	public static function post_is_free( $post ){
		$policy = get_post_meta( $post->ID, 'dmcss_security_policy', true );
		if( $policy == 'Subscriber Only' ) {
			// Subscriber only - return false
			return false;
		}
		else {
			// Registered user or Always free, return true
			return true;
		}
	}


	/**
	 * Removed get variable specified by $key
	 *
	 * @param string $url 
	 * @param string $key 
	 * @return URL
	 */
	public static function remove_get_var( $url, $key ){
		$search = array('/\?'.$key.'=[^&]*$/', // it's the only GET variable
			'/&'.$key.'=[^&]*$/', // something else before it
			'/&'.$key.'=[^&]*&/', // something else before and after it
			'/\?'.$key.'=[^&]*&/'); // something else after it
		$replace = array('',
			'',
			'&',
			'?');
		
		return preg_replace($search, $replace, $url);
	}

	/******** END STATIC FUNCTIONS ****/	

	/******** PRIVATE FUNCTIONS *******/

	/**
	 * Copy legacy options to new setting
	 *
	 * @return void
	 */
	private function import_legacy_options() {
		// Old options to copy
		$legacy_map = array(
			'dmcss_allow_circ_admin' => 'allow_circ_admin',
			'dmcss_category_lock' => 'category_lock',
			'dmcss_category_subrates' => 'use_category_subrates',
			'dmcss_debug_ips' => 'debug_ips',
			'dmcss_duplicate_session' => 'duplicate_session',
			'dmcss_field_label' => 'field_label',
			'dmcss_forgot_pwd_url' => 'forgot_pwd_url',
			'dmcss_home_lock' => 'home_lock',
			'dmcss_invalid_email' => 'invalid_email',
			'dmcss_key_icon_url' => 'key_icon_url',
			'dmcss_key_justification' => 'key_justification',
			'dmcss_logged_out' => 'logged_out',
			'dmcss_login_duration' => 'login_duration',
			'dmcss_login_text' => 'custom_subscribe_text',
			'dmcss_master_bypass' => 'master_bypass',
			'dmcss_display_login_form' => 'display_login_form',
			'dmcss_no_access' => 'no_access',
			'dmcss_publication' => 'publications',
			'dmcss_security_default' => 'security_default',
			'dmcss_session_expired' => 'session_expired',
			'dmcss_show_debug' => 'show_debug',
			'dmcss_show_excerpt' => 'show_excerpt',
			'dmcss_show_key_icon' => 'show_key_icon',
			'dmcss_subrates' => 'category_subrates',
			'dmcss_subsadmin_url' => 'subsadmin_url',
			'dmcss_subscribe_url' => 'subscribe_url',
			'dmcss_ws_url' => 'ws_url'
		);

		foreach( $legacy_map as $key => $value ) {
			$op = get_option( $key, NULL );
			if( NULL !== $op ) {
				if( '0' == $op ) {
					$this->options[$value] = false;
				}
				else {
					$this->options[$value] = $op;
				}
				delete_option( $key );
			}
		}
		ksort( $this->options );
		update_option( 'tdc_paywall_data', $this->options );
	}

	/**
	 * Remove old role/cap, purge unused options, & add new roles & caps
	 *
	 * @return void
	 */
	private function legacy_cleanup() {
		global $wpdb, $wp_roles;

		// Move all subscribers to new subscriber role
		$qry = "UPDATE wp_usermeta
				SET meta_value = 'a:1:{s:14:\"TDC_Subscriber\";s:1:\"1\";}'
				WHERE meta_value = 'a:1:{s:16:\"DMC_CircPro_User\";s:1:\"1\";}'";
		$wpdb->query( $qry );

		// Move all legacy caps to new subscriber cap
		$qry = "UPDATE wp_usermeta
				SET meta_value = REPLACE( meta_value, 'DMC_Subscriber_Only', 'TDC_Subscriber_Only' )";
		$wpdb->query( $qry );

		// Cleanup any leftover users
		$qry = "UPDATE wp_usermeta
				SET meta_value = 'a:1:{s:14:\"TDC_Subscriber\";b:1;}'
				WHERE meta_value = 'a:1:{s:16:\"DMC_CircPro_User\";b:1;}'";
		$wpdb->query( $qry );

		// Remove legacy role & add new ones
		remove_role( dmcss_wp::DMCSS_LEGACY_ROLE );
		add_role( dmcss_wp::SUBSCRIBER_ROLE, dmcss_wp::SUBSCRIBER_ROLE_LABEL, array( 'read'=> true, 'level_0'=> true, dmcss_wp::SUBSCRIBER_CAPABILITY => true, dmcss_wp::REGISTERED_USER_CAPABILITY => true ) );
		add_role( dmcss_wp::REGISTERED_USER_ROLE, dmcss_wp::REGISTERED_USER_LABEL, array( 'read'=> true, 'level_0'=> true, dmcss_wp::REGISTERED_USER_CAPABILITY => true ) );

		// Remove legacy cap and add new ones
		$role = $wp_roles->get_role( 'administrator' );
		$role->add_cap( dmcss_wp::SUBSCRIBER_CAPABILITY );
		$role->add_cap( dmcss_wp::REGISTERED_USER_CAPABILITY );
		$role->remove_cap( dmcss_wp::DMCSS_LEGACY_CAPABILITY );
		$role = $wp_roles->get_role( 'editor' );
		$role->add_cap( dmcss_wp::SUBSCRIBER_CAPABILITY );
		$role->add_cap( dmcss_wp::REGISTERED_USER_CAPABILITY );
		$role->remove_cap( dmcss_wp::DMCSS_LEGACY_CAPABILITY );
		$role = $wp_roles->get_role( 'author' );
		$role->add_cap( dmcss_wp::SUBSCRIBER_CAPABILITY );
		$role->add_cap( dmcss_wp::REGISTERED_USER_CAPABILITY );
		$role->remove_cap( dmcss_wp::DMCSS_LEGACY_CAPABILITY );

		// Old options to clean up
		$purge_options = array( 'dmcss_after_login', 'dmcss_allow_cap_login', 'dmcss_before_login', 'dmcss_button_css','dmcss_crawler_agent',
		'dmcss_crawler_ip', 'dmcss_custom_login', 'dmcss_custom_mobile_login', 'dmcss_mobile_active', 'dmcss_mobile_after_login', 'dmcss_mobile_before_login',
		'dmcss_mobile_button_css', 'dmcss_mobile_login_text', 'dmcss_remove_toolbar', 'dmcss_show_cap_header', 'dmcss_show_title' );
		foreach( $purge_options as $option ) {
			delete_option( $option );
		}

		// Import any options that are still used
		$this->import_legacy_options();
	}

	/******** END PRIVATE FUNCTIONS ***/
}

//called when plugin is activated, used to add custom role & capabilities
register_activation_hook( __FILE__, array('dmcss_wp', 'activate'));
//called when plugin is deactivated, used to remove custom role & capabilities
register_deactivation_hook( __FILE__, array('dmcss_wp','deactivate'));