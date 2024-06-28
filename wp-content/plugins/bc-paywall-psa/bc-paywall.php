<?php
/*
 * User-facing Class for BC-Paywall PSA
 * Author: Asentech 
 */

// Disallow direct access
if( !defined( 'ABSPATH' ) ) {
    die( 'Direct access not allowed.' );
}

//Added recaptcha code
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// check for plugin using plugin name
if ( is_plugin_active( 'advanced-nocaptcha-recaptcha/advanced-nocaptcha-recaptcha.php' ) || is_plugin_active( 'wp-recaptcha/wp-recaptcha.php' )) { 
function my_added_login_field(){
    $old_options = get_option("recaptcha_options");
    $pub= $old_options['site_key'];
    $private= $old_options['secret'];
    ?>
    <script src="https://www.google.com/recaptcha/api.js?onload=recaptchaCallback&render=explicit" async defer></script>
    <script>
    function recaptchaCallback (e) {
    var container = document.getElementById('g-recaptcha')
    container.innerHTML = ''
    var recaptcha = document.createElement('div')
    grecaptcha.render(recaptcha, {
    'sitekey': '<?php echo $pub;?>'
    })
    container.appendChild(recaptcha)
    }
    </script>
    <p>
    <div class="g-recaptcha" id="g-recaptcha" data-sitekey="<?php echo $pub;?>" render="explicit"></div>
    </p>
    <?php   } 
    function my_login_form_update() { ?>
    <style type="text/css">
    /* #g-recaptcha{margin-bottom:20px;}
    #loginform{display:inline-block;} */
    </style>
    <?php }

    //add_action('login_form','my_added_login_field');
    //add_action( 'login_enqueue_scripts', 'my_login_form_update' );
} 

// Update the active plugins & refresh if trying to load legacy Paywall
if( !class_exists( 'dmcss_wp' ) ) {
    $old = 'tdc-paywall/tdc-paywall.php';
    $new = 'tdc-paywall/dmcss-wp.php';
    $updated = false;

    // Check network plugins
    $network_plugs = get_site_option( 'active_sitewide_plugins' );
    if( array_key_exists( $old, $network_plugs ) ) {
        $timestamp = $network_plugs[$old];
        unset( $network_plugs[$old] );
        $network_plugs[$new] = $timestamp;
        update_site_option( 'active_sitewide_plugins', $network_plugs );
        $updated = true;
    }

    // Check blog plugins
    $plugs = get_option( 'active_plugins' );
    $index = array_search( $old, $plugs );
    if( false !== $index ) {
        $plugs[$index] = $new;
        update_option( 'active_plugins', $plugs );
        $updated = true;
    }

    // Refresh if we were able to update; otherwise throw error
    if( $updated ) {
        header( 'Location: ' . $_SERVER['REQUEST_URI'] );
    }
    else {
        wp_die( 'Paywall load failure.' );
    }
}

function free_from_birth(){

    //Check if post is free by Allow Free for Hours
    wp_reset_postdata();
    $post_id = get_the_ID();
    $post = get_post($post_id);
    $faux_paywall = bc_paywall::get_instance();
    $allow_free_from_birth = $faux_paywall->get_allow_free_from_birth();
    if($allow_free_from_birth == true){
        if($faux_paywall->valid_by_birth_time($post_id, $post->post_date)){     
            return true;
        }
    }
}

/* Blueconic Script */
add_action('wp_head', 'add_bc_script_in_head', 1);
function add_bc_script_in_head(){ 

  global $current_user;
  
  $current_user = wp_get_current_user();
  $userData = get_userdata($current_user->ID);
  $userRoles = $userData->roles; 
  if (!is_array($userRoles)) {
     $userRoles = array(); // Convert to an array if it's not already
  }

  if(is_user_logged_in() && in_array("TDC_Subscriber", $userRoles) || in_array("editor", $userRoles) || in_array("administrator", $userRoles)){
        $token = base64_encode( $current_user->user_email );
    } else {
        $token = '';
    }
          
  $bc_config = get_option('tdc_paywall_data');
  if (!is_array($bc_config)) {
    $bc_config = array(); // Convert to an array if it's not already
}
  
  /* Setting Blueconic script in <head> tag */
  if(isset($bc_config['bc_head_script'])){
    echo $bc_config['bc_head_script'];
  }

  /* Replacing Technavia link with addition of token if user logged in */
  if(isset($bc_config['technavia_link']) && $bc_config['technavia_link'] != ''){
?>
    <script type="text/javascript">
        var Tecnnavia_URL = "<?php echo $bc_config['technavia_link'];?>";
        var Tecnnavia_token = "<?php echo $token;?>";
    </script>
<?php
  }

}

add_action('wp_head', 'replace_technavia_url_with_token');
function replace_technavia_url_with_token(){
    $bc_config = get_option('tdc_paywall_data');

    if(isset($bc_config['technavia_link']) && $bc_config['technavia_link'] != ''){
?>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery('body a').each(function(){

                var ancher_url = jQuery(this).attr('href');
                
                if ((typeof ancher_url !== 'undefined' && ancher_url !== false) && (ancher_url.indexOf('://') > -1)) {
                   
                    var tecnnavia_domain = Tecnnavia_URL.split('://');
                    var ancher_domain = ancher_url.split('://');
                
                    if(ancher_url && Tecnnavia_token != ''){              
                         
                        if(ancher_domain[1] == tecnnavia_domain[1]){
                            jQuery(this).attr('href', Tecnnavia_URL+'?token='+Tecnnavia_token);
                        } else {
                            if(ancher_domain[1].indexOf('?') > -1 && ancher_domain[1].indexOf('?token') < 0){
                                var ancher_link_domain = ancher_domain[1].split('?');

                                if(ancher_link_domain[0] == tecnnavia_domain[1]){
                                    jQuery(this).attr('href', ancher_domain[0]+'://'+ancher_domain[1]+'&token='+Tecnnavia_token);
                                }
                            }
                        }
                    } 
                } 
            });
        });
    </script>
<?php
    }
}


/* After login redirect to home page */
add_filter( 'login_redirect', 'login_redirect', 10, 3 );
function login_redirect( $redirect_to, $request, $user ){
    $role = $user->roles[0];
    $dashboard = admin_url();
    if(get_current_blog_id()==1){
         if( $role == 'administrator'  ) {
            $redirect_to = $dashboard;
         
        } else {
             $redirect_to = home_url();
         }  
    } else {
        $redirect_to = $dashboard;
    }
    
    return $redirect_to;
}

class bc_paywall extends dmcss_wp {
    // Enumerated constants for keeping track of access level / type
    const NO_ACCESS = 0;
    const SUBSCRIBER_ACCESS = 1;
    const REGISTERED_ACCESS = 2;
    const REGISTERED_NO_ACCESS = 3;
    const BYPASS = -1;
    const CONFIRM_REGISTER = -3;
    const ACTIVATE_USER = -7;
    const ADMIN_BYPASS = -5;
    const NO_PUB_ACCESS = 5;
    const LOGIN_FORM = -9;
    const CATEGORY_SUBRATES = 9;

    // Variable for Memcached group
    const MEMCACHED_GROUP = 'paywall-users';

    // Array for data used during a page load; allow no access by default
    private $dmcss_data = array( 'access' => self::NO_ACCESS );

    /*
     * Class constructor
     * Set up actions & filters, & initialize non-constant class vars
     *
     * @return void
     */
    public function __construct( $empty = 0) {
        parent::__construct();

        if($empty == 1){
            
            add_action( 'init', array( &$this, 'init' ) );
        } else {
            
            // Initialize error holder
            $this->dmcss_data['error'] = new WP_Error();

            // Validate token & subscription, initialize dmcss_data array
            add_action( 'init', array( &$this, 'init' ) );
            // Copy any cookie messages to $_REQUEST and erase cookie
            add_action( 'plugins_loaded', array( &$this, 'clear_message_cookies' ) );

            // Import css & js, add Admin link, & set up redirect if needed
            add_action( 'wp', array( &$this, 'wp' ) );
            add_action( 'wp_print_styles', array( &$this, 'enqueueStylesheet' ) );
            add_action( 'wp_enqueue_scripts', array( &$this, 'importJS' ) );

            // Add filters for login, set to high priority
            add_filter( 'authenticate', array( &$this, 'authenticate' ), 1, 3 );
            
            //Catch all wordpress users not accounted for via other login functions and check them against circ
            add_action( 'wp_login', array( &$this, 'validate_wpuser'), 1, 2 );
           // add_action( 'wp_login', array( &$this, 'validate_wpuser_tech'), 1, 2 );

            // Add filter to set login duration of WP Users
            add_filter( 'auth_cookie_expiration', array( &$this, 'updateCookieDuration' ), 1, 3 );

            // Add filters for the_title & the_content
            if( !empty($this->options[ 'show_key_icon']) ) {
                add_filter( 'the_title', array( &$this, 'the_title' ), 11, 2 );
                add_filter( 'the_title', array( &$this, 'add_lock_on_title' ), 11, 2 );
            }
            add_filter( 'the_content', array( &$this, 'the_content' ), 11, 2 );

            // Add comment filters
            add_filter( 'comments_array', array( &$this, 'comments_array_filter' ), 10, 2 );
            add_filter( 'comment_post_redirect', array( &$this, 'comment_post_redirect' ), 0, 2 );

            //If a user is registering as a a Registered User, change the login link in the welcome user email.
            
            add_filter('update_welcome_user_email', array( &$this, 'change_welcome_mail_loginlink' ), 10, 4 );

            // Redirect Circ Users on logout
            add_action( 'wp_logout', array( &$this, 'logout_redirect' ) );

            // Custom wp_die handler; prevent wordpress failure notice on logout
            add_filter( 'wp_die_handler', array( &$this, 'get_custom_wp_die_handler' ) );

            add_filter ( 'login_footer', array(&$this, 'alter_password_reset_login_link'), 10, 0 );
            // Actions & Filters for Registered User functionality
            if( isset($this->options['allow_registered_users']) ) {
                // Add filter to intercept Subscriber password reset
                add_filter( 'allow_password_reset', array( &$this, 'password_reset_filter' ), 10, 2 );
            }

            // Configure Memcached if available
            if ( function_exists( 'wp_cache_add_global_groups' ) ) {
                wp_cache_add_global_groups( array( self::MEMCACHED_GROUP ) );
            }
            
            
            // If the master bypass switch is on
            /*if(isset($this->options['master_bypass']) && !$this->options['master_bypass'] ) {
                // Alter the Loginout links
                add_filter( 'wp_nav_menu_items', array( &$this, 'loginout_link' ), 10, 2 );
                if( isset($this->options['popup_login']) ) {
                    add_filter( 'wp_nav_menu_args', array( &$this, 'display_popup_login' ) );
                }
            }*/

            // If display login form switch is on
            if(isset($this->options['display_login_form']) && $this->options['display_login_form'] ) {
                // Alter the Loginout links
                add_filter( 'wp_nav_menu_items', array( &$this, 'loginout_link' ), 10, 2 );
                if( isset($this->options['popup_login']) ) {
                    add_filter( 'wp_nav_menu_args', array( &$this, 'display_popup_login' ) );
                }
            }
            
            //Filters adding subscriber policy classes to post and body classes
            
            add_filter( 'post_class', array( &$this, 'subscriber_policy_class'), 10, 1 );
            add_filter( 'body_class', array( &$this, 'subscriber_policy_class'), 10, 1 );
            add_action( 'wp_head', array( &$this, 'subscriber_policy_meta'), PHP_INT_MAX);

            add_action( 'rest_api_init', array( &$this,'technavia_authentication_route' ));
            // add_filter( 'wp_nav_menu_items', array( &$this, 'loginout_links' ), 10, 2 );

        
        }
        
    }

    /*

     * Filter to add loginout link to WP Nav Menu 'top-menu'
     *
     * @param object $items
     * @param array $args
     * @return object nav menu items w/ added loginout link
     */
    public function loginout_links( $items, $args ) {
            
        $slugs = wp_list_pluck( $args, 'slug' );
            
        if( $slugs['menu'] == 'main-navigationnew-navigation') {
       
            $current_user = wp_get_current_user();
            $token = base64_encode( $current_user->user_email );
            $bc_config = get_option('tdc_paywall_data');
            if(isset($bc_config['technavia_link'])){
                $tech_link = $bc_config['technavia_link'];
            }

            $tecnavia_url  = $tech_link;
            $tecnavia_url_text  = "Digital Edition";
            
            // If user signed in, show Wordpress logout link
            global $current_user; //get the current user
            $current_user = wp_get_current_user();               
            $roles = ( array ) $current_user->roles;
           if(is_user_logged_in() && $roles[0]=='TDC_Subscriber' || $roles[0]=="administrator"){             

            $items .= '<li class="menu-item  menu-item-type-custom  menu-item-object-custom"><a href="' . $tecnavia_url . '?token=' .$token. '">' . $tecnavia_url_text . '</a></li>';
           }else{
            $items .= '<li class="menu-item  menu-item-type-custom  menu-item-object-custom"><a href="' . $tecnavia_url . '">' . $tecnavia_url_text . '</a></li>';
           }
        }
        
        return $items;
    }

    /*
    * Tecnavia create authentication route
    */
   public function technavia_authentication_route() {
        $namespace = 'checkLogin';
        $base = 'someScript';
        register_rest_route( $namespace, '/' . $base, array(
          'methods' => 'POST',
          'callback' => array($this, 'validate_wpuser_tech'),
          'permission_callback' => '__return_true',
        ) );
    }
  

  public  function validate_wpuser_tech( $data ) {
       global $wpdb;
       $token = $data->get_param( 'token' );
       
       if(!empty($token)) {
        
           $current_user = wp_get_current_user();
           $userEmail =   base64_decode( $token );
           $base_prefix = $wpdb->base_prefix.'users';
       
           $userRow = $wpdb->get_row( "SELECT * FROM $base_prefix WHERE user_email="."'".$userEmail."'" );
         
            if(!empty($userRow->user_email)) {
                $user_email =  $userRow->user_email;
                $user_login = $userRow->user_login;
                $premium_access = 'yes';
                $project_access = 'yes';
                $tracking_id = $userRow->ID  ? : null;
                $logged_in = 'yes';

                //echo 'token yes';
            } else {
                $premium_access = 'no';
                $project_access = 'no';
                $tracking_id = null;
                $logged_in = 'no';
                $user_email =  null;
                $user_login = null;
            }

            $xml = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><LOGIN></LOGIN>");

            $token = $xml->addChild('TOKEN', $token);
            $tracking_id = $xml->addChild('UNIQUE_USER_ID',$tracking_id);
            $email_address = $xml->addChild('EMAIL', $user_email);
            $username = $xml->addChild('USERNAME', $user_login);
            $logged_in = $xml->addChild('IS_LOGGED', $logged_in);
            $text = $xml->asXML();
            $text = preg_replace('/^(\'(.*)\'|"(.*)")$/', '$2$3', $text);
            $text = preg_replace("/\r|\n/", "", $text);   
            
            return $text;

        } else {
            return;
        }

    }


    // add subscriber policy in body and post class
    public function subscriber_policy_class( $classes ) {
        
        global $post;
        
        if(!is_object($post))
            return;
        
        $meta = get_post_meta( $post->ID, 'dmcss_security_policy', true );  
        $meta = str_replace(' ', '_', $meta);
        $meta = strtolower($meta);
        $classes[] = $meta;
        return $classes;
        
    }

    // Apply subscriber policy in meta tag for Blueconic */  
     public function subscriber_policy_meta() {
        
        global $post;
        
        if(!is_object($post))
            return;
        

        if(is_single() && 'post' == get_post_type()){
            $meta = get_post_meta( $post->ID, 'dmcss_security_policy', true );  
            $meta = str_replace(' ', '_', $meta);
            $meta = strtolower($meta);
            echo "<meta property='og:policy' content='".$meta."' />";

        } else {
            $meta = get_post_meta( $post->ID, 'dmcss_security_policy', true );  
            $meta = str_replace(' ', '_', $meta);
            $meta = strtolower($meta);

            if($meta == 'never_free'){
                echo "<meta property='og:policy' content='".$meta."' />";
            }
        }
        
    }
    
    //alter the login and out links
    public function alter_password_reset_login_link () {
        if(isset($_GET['action']) && $_GET['action'] == 'lostpassword'){
            echo('<script>  var element = document.getElementById("nav"); element.childNodes[1].setAttribute("href", "'.get_site_url().'?dmcss=login"); </script>');
        }
    }


    /******* WP FITLERS ************/

    /*
     * Filter to authenticate Circ Users against the Web Service
     *
     * @param object $user WP User object
     * @param string $username
     * @param string $password
     * @return WP User
     */
     
    public function authenticate( $user, $username, $password ) {
        global $wp_object_cache;

        // If we're not logging in, don't do anything.
        if( isset( $_POST['wp-submit'] ) && !$user ) {
            // Check if username or password empty before proceeding
            if( 1 > strlen( $username ) || 1 > strlen( $password ) ) {
                $em_msg = dmcss_wp::DEFAULT_INVALID_EMAIL;
            }
            else{
                // Inputs valid, proceed with login
                // Setup defaults
                $check_against_circ = false;
                $user_id = '';
                // Check if $username is a valid WP login
                if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
                            $user_lookup = get_user_by( 'email', $username );
                        }
                        else {
                            $user_lookup = get_user_by( 'login', $username );
                        }
                
                if( false !== $user_lookup ) {
                    // Valid login; check role & set user value or otherwise fall through to WP login
                    $standard_roles = array_intersect($user_lookup->roles, $this->paywall_roles);
                    $role = array_shift($standard_roles);
                    
                    if( preg_match( '/' . dmcss_wp::SUBSCRIBER_ROLE . '/i', $role ) ) {
                            // call the web service
                            $check_against_circ = true;
                 
                    }
                    elseif(preg_match( '/' . dmcss_wp::REGISTERED_USER_ROLE . '/i', $role )){
                            // call the web service
                            $check_against_circ = true;
                    }
                }
                else {
                    //Check If user email exist but not user login
                      if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
                           $user_id = email_exists( $username );
                        }
                        else {
                        $pubcode_check = '';
                        foreach( $this->options['publications'] as $pub1_check ){
                            if( 0 < strlen( $pubcode_check ) ) {
                                $pubcode_check .= ',';
                            }
                            $pubcode_check .= $pub1_check['code'];
                         }

                            if( 1 < strlen( $pubcode_check ) ) 
                            {
                            $result_check = $this->login_via_wsdl( $username, $password, $pubcode_check );

                            if (trim($result_check) != '0') 
                              { 
                                     $xmlObject_check = new SimpleXMLElement($result_check);
                                     $node_check = $xmlObject_check->children();

                                  if(!isset($node_check[0]->TOKEN) && !isset($node_check[0]->API)) 
                                      {
                                        $node_cnt_check = count($node_check);
                                        $sub_rec_check = $node_check->sub;      
                                        $email_id_PSA_check1 = $sub_rec_check->CUS_EMAIL;
                                        $email_id_PSA_check = urldecode($email_id_PSA_check1);
                                      }       
                              }
                            }

                            $user_id = email_exists( $email_id_PSA_check );
                          }

                        if( !$user_id ) {
                            // Email not known, call Circ
                            $check_against_circ = true;
                        }
                        elseif( $user_id && $this->options['allow_circ_admin'] ) {
                            // WP User & circ login allowed
                            $user_lookup = new WP_User( $user_id );
                            $standard_roles = array_intersect($user_lookup->roles, $this->paywall_roles);
                            $role = array_shift($standard_roles);
                            $check_against_circ = true;
                        }
                        else {
                            // WP User & circ login not allowed
                            $em_msg = '<strong>ERROR</strong>: Invalid username or password.';
                        }
                //    }
                }

                // If we're calling circ, check memcached for login attempts; prevent brute-force flood attacks
                if( $wp_object_cache->memcache_active ) {
                    // Memcached is active, check it.
                    $attempts = (int)wp_cache_get( $username, self::MEMCACHED_GROUP );
                    if( 5 <= $attempts ) {
                        // Block login if threshold exceeded
                        $check_against_circ = false;
                        $em_msg = '<strong>ERROR</strong>: The maximum number of login attempts has been exceeded.  Please wait 5 minutes before trying again.';
                    }
                    else {
                        // Otherwise count the login attempt
                        $attempts++;
                        wp_cache_set( $username, $attempts, self::MEMCACHED_GROUP, 300 );
                    }
                }
                else {
                    // Memcached not active
                    if( !preg_match( '/' . dmcss_wp::SUBSCRIBER_ROLE . '/i', $role ) && $user_id ) {
                        // WP User using PSA credentials; show error
                        $check_against_circ = false;
                        $em_msg = '<strong>ERROR</strong>: Invalid username or password.';
                    }
                }

                // User doesn't exist or is a PSA user; setup web service call
                if ( $check_against_circ ){
                    // Check Web Service & bypass before trying to call
                    if( empty( $this->options['ws_url'] ) ) {
                        // Web Service is undefined; show pre-defined error message
                        $em_msg = '<strong>ERROR</strong>: Web service is undefined.  If this issue persists, please notify customer service at 1-800-451-9998.';
                    }
                    elseif( $this->dmcss_data['access'] === self::BYPASS ) {
                        // Paywall is bypassed; show message
                        $em_msg = dmcss_wp::DEFAULT_BYPASSED_MSG;
                    }
                    else {
                        // Build publication array for web service call
                        $pubcode = '';
                        foreach( $this->options['publications'] as $pub ){
                            if( 0 < strlen( $pubcode ) ) {
                                $pubcode .= ',';
                            }
                            $pubcode .= $pub['code'];
                        }

                        // Confirm we have at least 1 publication configured before calling web service
                        if( 1 < strlen( $pubcode ) ) {
                            $result = $this->login_via_wsdl( $username, $password, $pubcode );

                            // Check that result is non-false
                            if(trim($result) != ''){
                                if( trim($result) == '0' ) {
                                  // The login was unsuccessful; we'll want to display this error message to the user.
                                    $em_msg = '<strong>ERROR</strong>: Wrong Credentials';                          
                                }
                                
                                elseif (trim($result) != '0') { 
                                     $xmlObject = new SimpleXMLElement($result);
                                     $node = $xmlObject->children();
                                   if(isset($node[0]->TOKEN)) {
                                         $em_msg = '<strong>ERROR</strong>:Wrong TOKEN';
                                    }
                                    else if(isset($node[0]->API)) {
                                         $em_msg = '<strong>ERROR</strong>:Wrong API';  
                                    }

                                    else {
                                         if(!isset($node[0]->TOKEN) && !isset($node[0]->API)) {
                                        $node_cnt = count($node);
                                        $sub_rec = $node->sub;      
                                        $sub_status = $sub_rec->SUB_STATUS;
                                        $user_id_PSA1 = $sub_rec->CUS_USERID;
                                        $user_id_PSA = urldecode($user_id_PSA1);
                                        $email_id_PSA1 = $sub_rec->CUS_EMAIL;
                                        $email_id_PSA = urldecode($email_id_PSA1);
                                        $sub_last_order = $sub_rec->SUB_LAST_ORDER;

                                        if($email_id_PSA == "") {
                                          $email_id_PSA = $username."asen".time()."@psa.com";
                                         }
                                         
                                        if ($sub_status != 'A'){
                                             $renewal_url =  $this->options['PSA_url'];
                                             $em_msg = '<strong>Your Subscription has expired</strong><br><a style="font-size: 17px;font-style: oblique;font-weight: bold;text-decoration: underline;" href="'.$renewal_url.'?orddba='.$sub_last_order.'">Click here to renew</a>';
                                            // $em_msg = '<strong>ERROR</strong>:Not An Active Subscriber'; 
                                        }
                                        
                                       else {
                                      // Login success! now update user so we have 1-1 match between WP and PSA users
                                          //Update Jobtrac CF Database
                                            $jobtrac_enable = $this->options['jobtrac_enable'];
                                            if($jobtrac_enable == 'yes'){
                                                $sub_service = $sub_rec->SUB_SERVICE;
                                                if($sub_service == 'JP' || $sub_service == 'JD' || $sub_service == 'JS')
                                                 {
                                                  //getting params from API Response
                                                    $external_password = $sub_rec->CUS_PASSWORD;
                                                    $transaction_first_name = $sub_rec->CUS_FNAME;
                                                    $transaction_last_name = $sub_rec->CUS_LNAME;
                                                    $rowguid = '';
                                                    $isdolan = 0;
                                                    $transaction_companyname = $sub_rec->CUS_COMPANY;
                                                   //calling API for CF updation  
                                                  $this->jobtrac_user_registration($user_id_PSA,$external_password,$email_id_PSA,$transaction_first_name,$transaction_last_name,$transaction_companyname);
                                                 }
                                            }
                                        //Update Jobtrac CF Database
                                        
                                        //Update ProjectProjectCenter CF Database
                                            $project_center = $this->options['project_center_enable'];
                                            if($project_center == 'yes'){
                                                $sub_service = $sub_rec->SUB_SERVICE;
                                                if($sub_service == 'PC' || $sub_service == 'PD')
                                                 {
                                                  //getting params from API Response
                                                    $external_password = $sub_rec->CUS_PASSWORD;
                                                    $transaction_first_name = $sub_rec->CUS_FNAME;
                                                    $transaction_last_name = $sub_rec->CUS_LNAME;
                                                    $rowguid = '';
                                                    $isdolan = 0;
                                                    $transaction_companyname = $sub_rec->CUS_COMPANY;
                                                   //calling API for CF updation  
                                                  $this->projectcenter_user_registration($user_id_PSA,$external_password,$email_id_PSA,$transaction_first_name,$transaction_last_name,$transaction_companyname);
                                                 }
                                            }
                                        //Update ProjectCenter CF Database  
                                            
                                          
                                        $this->dmcss_data['circ'] = 1;
                                        $timestamp = date( 'H:i:s' );
                                        $this->add_message( 'notice', '<p>' . $timestamp . ' ' . $email_id_PSA . ' successfully logged in.</p>' );
                                       $user = $this->sync_wp_user( $user_id_PSA, $email_id_PSA, $user_lookup, $password, $result );
                                      }
                                   } 
                                
                                
                                    }
                                }
                                else {
                                    // The web service returned an invalid result; display an error message to the user.
                                    require_once( 'bc-paywall-templates.php' );
                                    bc_paywall_templates::ws_custom_error( $user_id_PSA, $pubcode, $result );
                                }
                               
                            }
                            // Log to user log if activated
                            if( PAYWALL_LOG_USERS && defined( 'PAYWALL_USER_LOG' ) ) {
                                $log_str = '\'' . time() . '\',\'' .
                                    gethostbyaddr('127.0.0.1') . '\',\'' .
                                    $_SERVER[ 'SERVER_NAME' ] . '\',\'' .
                                    $email_id_PSA . '\',\'' .
                                    $pubcode . '\',\'' .
                                    $_SERVER['REMOTE_ADDR'] . '\',\'' .
                                    $_SERVER['HTTP_REFERER'] . '\',\'' .
                                    $_SERVER['HTTP_USER_AGENT'] . '\'';
                                if( !empty( $em_msg ) ) {
                                    $log_str .=  ',\'' . $em_msg . '\'';
                                }
                                else {
                                  $xmlObject = new SimpleXMLElement($result);
                                   $array2 = json_decode(json_encode((array)$xmlObject), TRUE);
                                    $token1 = $this->options['ws_url_token'];
                                    $log_str .=  ',\'' . $array2['sub']['SUB_PUB'] . '\',\'' . $array2['sub']['CUS_CUSTNUM'] . '\',\'' . $array2['sub']['SUB_SERVICE'] . '\',\'' . $token1 . '\'';
                                }
                                error_log( $log_str . PHP_EOL, 3, PAYWALL_USER_LOG );
                            }
                        }
                        else {
                            // No publications; show error
                            $em_msg = '<strong>ERROR</strong>: Publication is undefined.  If this issue persists, please notify customer service at 1-800-451-9998.';
                        }
                    }
                }
            }

            if( !empty( $em_msg ) ) {
                remove_action( 'authenticate', 'wp_authenticate_username_password', 20 );
                $this->logout_with_message( $em_msg );
            }
        }
        return $user;
    }

    /**
     * Removes the comments if we are replacing the content with something like the login screen
     *
     * @param object $comments array of comment objects
     * @param int $post_id
     * @return $comments array or empty array if no access
     */
    public function comments_array_filter( $comments, $post_id ) {
        global $post;

        // Check security meta
        $security = get_post_meta( $post->ID, 'dmcss_security_policy', true );
        if(
            ( isset( $_GET['dmcss'] ) && 'login' == $_GET['dmcss'] && !is_user_logged_in() )
            || $security == 'Subscriber Only' && !current_user_can( dmcss_wp::SUBSCRIBER_CAPABILITY )
            || $security == 'Registered User' && !current_user_can( dmcss_wp::REGISTERED_USER_CAPABILITY )
        ){
            // the current user can't view this post, so they shouldn't see the comments either
            $comments = array();

            // Also close comments if $post global set
            if( is_object( $post ) ) {
                $post->comment_status = 'closed';
            }
        }

        return $comments;
    }
    
    /*Sets LOGINLINK link to Registered User Login Link if the user is a registered user */
    public function change_welcome_mail_loginlink( $welcome_email, $user_id, $password, $meta ) {
            $welcome_email = str_replace( 'LOGINLINK', site_url().'?dmcss=login', $welcome_email );
            return $welcome_email;
    }
    
    /**
     * Deletes comments if the current user can't view the post it is commented on.
     *
     * @param string $location
     * @param string $comment
     * @return void
     */
    public function comment_post_redirect( $location, $comment ) {
        // Check security for this post directly; data cache not built yet
        $security = get_post_meta( $comment->comment_post_ID, 'dmcss_security_policy', true );

        // Delete comments if the current user can't view this post
        if(
            ( isset( $_GET['dmcss'] ) && 'login' == $_GET['dmcss'] && !is_user_logged_in() )
            || $security == 'Subscriber Only' && !current_user_can( dmcss_wp::SUBSCRIBER_CAPABILITY )
            || $security == 'Registered User' && !current_user_can( dmcss_wp::REGISTERED_USER_CAPABILITY )
        ){
            wp_delete_comment( $comment->comment_ID );
        }

        return $location;
    }

    /*
     * Customer wp_die handler to prevent wordpress failure notice on logout
     *
     * @return void
     */
    public function custom_wp_die_handler( $message, $title = '', $args = array() ) {
        if( is_string( $message ) && preg_match( '/wp-login.php\?action=logout/', $message ) ){
            wp_logout();
        }
        else
            call_user_func( '_default_wp_die_handler', $message, $title, $args );
    }

    /**
     * Make use of wp_nav_menu call for top-menu to output prior to menu itself
     *
     * @param array $args
     * @return $args
     */
    public function display_popup_login( $args ) {
        if( 'top-menu' == $args['theme_location'] ) { ?>
            <div class="form-horizontal shadow dmcss_login_form" id="popup_loginform">
            <?php require_once( 'bc-paywall-templates.php' );
            echo( bc_paywall_templates::login_tab_content( $this->options ) ); ?>
            </div><?php
        }
        return $args;
    }

    /*
     * Filter to intercept wp_die and prevent wordpress failure notice on logout
     *
     * @return pointer to custom_wp_die_handler()
     */
    public function get_custom_wp_die_handler() {
        return array(&$this, 'custom_wp_die_handler');
    }

    /*
     * Filter to add loginout link to WP Nav Menu 'top-menu'
     *
     * @param object $items
     * @param array $args
     * @return object nav menu items w/ added loginout link
     */
    public function loginout_link( $items, $args ) {
        if( 'top-menu' == $args->theme_location ) {
            if( $this->options['popup_login'] ) {
                // If user signed in, show Wordpress logout link
                if( is_user_logged_in() ) {
                    $items .= '<li><a href="' . wp_logout_url() . '">LOG OUT</a></li>';
                }
                else {
                    $items .= '<li><a id="login_popup" href="#">LOG IN</a></li>';
                }
            } else {
                // If user signed in, show Wordpress logout link
                if( is_user_logged_in() ) {
                    $items .= '<li><a href="' . wp_logout_url() . '">LOG OUT</a></li>';
                }
                // For signed in PSA user / WP wrapper; show logout link and redirect to blog home
                elseif( isset( $_COOKIE['dmcss']['current_user'] ) && preg_match( '/accountNumber/', $_COOKIE['dmcss']['current_user'] ) && preg_match( '/coldfusion/i', $_SERVER['HTTP_USER_AGENT'] ) ) {
                    $items .= '<li><a href="' . self::add_get_var( site_url(), 'dmcss', 'logout' ) . '">' . __('LOG OUT') . '</a></li>';
                }
                else {
                    $user_link = add_query_arg("dmcss", "login", site_url('/user-login/'));
                    $link = '';
                    $link .= '<li><a href="' .$user_link. '">' . __('LOG IN') . '</a></li>';
                    if( isset($_GET['loggedout']) && 'true' == $_GET['loggedout'] ) {
                        $link = self::remove_get_var( $link, 'loggedout' );
                    }
                    $items .= $link;
                }
            }
        }

        return $items;
    }

    /*
     * Filter to send activation emails to registered users
     *
     * @param string $user - username
     * @param string $user_email
     * @param string $key - activation key
     * @param array $meta - usermeta stored in wp_signups
     * @return boolean false - prevent duplicate email sent by WordPress
     */
    public function new_user_email( $user, $user_email, $key, $meta ){
        // Send email with activation link.
        $admin_email = get_site_option( 'admin_email' );
        if ( $admin_email == '' ) {
            $admin_email = 'support@' . $_SERVER['SERVER_NAME'];
        }

        // Set up message header
        if( get_site_option( 'site_name' ) ) {
            $from_name = esc_html( get_site_option( 'site_name' ) );
        }
        elseif( isset( $this->options['publications'][0]['title'] ) ) {
            $from_name = $this->options['publications'][0]['title'];
        }
        else {
            $from_name = 'Publication Admin';
        }
        $message_headers = "From: \"{$from_name}\" <{$admin_email}>\n" . "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";

        // Build message content
        $subject = sprintf( '[%s] Activate %s', $from_name, $user );
        $message = sprintf( 'Hi,' . "\n" . 'Thank you for registering with %1$s.' .
            "\n" . 'To activate your account, please click the following link:' . "\n" . '%2$s' . "\n\n" .
            'If you do not want to join this site please ignore this email. This invitation will expire in a few days.',
            wp_specialchars_decode(get_bloginfo( 'name' )), site_url( "?dmcss=activate&key=$key" ) );

        wp_mail( $user_email, $subject, $message, $message_headers );
        return false;
    }

    /*
     * Filter to intercept password reset & redirect PSA users
     *
     * @return $allow_reset, WP_Error, or redirect
     */
    public function password_reset_filter( $allow_reset, $user_id ) {
        // Check role for $user_id
        $user_lookup = get_user_by( 'id', $user_id );
        $standard_roles = array_intersect($user_lookup->roles, $this->paywall_roles);
        $role = array_shift($standard_roles);
        //$role = $user_lookup->roles[0];

        if( !empty( $this->options['forgot_pwd_url'] ) && preg_match( '/' . dmcss_wp::SUBSCRIBER_ROLE . '/i', $role ) ) {
            // Redirect Subscribers to the subscribe site
            header( 'Location: ' . $this->options['forgot_pwd_url'] );
            exit;
        }
        elseif( empty( $this->options['forgot_pwd_url'] ) && preg_match( '/' . dmcss_wp::SUBSCRIBER_ROLE . '/i', $role ) ) {
            // Return an error if we don't have a password URL for subscribers
            $err_msg = $this->options['reset_failure'];
            if( empty( $err_msg ) ) {
                $err_msg = dmcss_wp::DEFAULT_RESET_FAILURE_MSG;
            }
            return new WP_Error( 'reset_failure', __( $err_msg ) );
        }
        return $allow_reset;
    }

    /*
     * Filter to determine if post(s) are behind paywall or not
     * -- If they are then we show lock icon
     *
     * @param $posts = WordPress loop variable
     * @return post title(s) w/ lock icon if needed
     */
    public function posts_filter( $posts ) {
        
        $keyURL = $this->options['key_icon_url'];
        if( empty( $keyURL ) ) {
            $keyURL = $this->default_key_location;
        }
        $key_html = '&nbsp;<span class="dmcss_key_icon"><img alt="(access required)" src="'.$keyURL.'" border=0/></span>';

        // Add lock icon to posts if not added directly via the_title
        foreach( $posts as $post ){
            $policy_meta = get_post_meta( $post->ID, 'dmcss_security_policy', true );  
            $policy_meta = str_replace(' ', '_', $policy_meta);
            $policy_meta = strtolower($policy_meta);
            
            if($policy_meta == 'subscriber_only' && !empty($this->options[ 'show_key_icon'])){
               if( $this->options['key_justification'] ) {
                    $new_title =  '<span class="lockleft">'.$post->post_title.'</span>';
                } else {
                    $new_title = $post->post_title.$key_html;
                }
                
                $post->post_title = apply_filters( 'the_title', $new_title, $post->ID );
            } else {
                $post->post_title = apply_filters( 'the_title', $post->post_title, $post->ID );
            }
            
        }
        return $posts;
    }
    /*
     * Filter to determine if article is behind paywall or not
     * -- If it is then we show login form
     *
     * @param $input = post's content
     * @param int $post_id
     * @return post content or login form
     */
    public function the_content( $input, $post_id = '' ) {

        //Check for security bypass and show content if found
        if( self::BYPASS !== $this->dmcss_data['access'] || 0 == $post_id ) {
            if( empty( $post_id ) ) {
                $post_id = get_the_ID();
            }

            // If post object passed in, set $post & $post_id variables
            if( is_object( $post_id ) ) {
                $post = $post_id;
                $post_id = $post->ID;
            }
            else {
                // Otherwise get the current post
                $post = get_post( $post_id );
            }

            if((isset($_GET['dmcss']) && $_GET['dmcss'] == 'login') && (isset($this->options['display_login_form']) && $this->options['display_login_form'])) {
                return $this->draw_login_page( $post, 'login' );
            }   

            // Check if we asked for a login page
            if( self::LOGIN_FORM === $this->dmcss_data['access'] || self::ACTIVATE_USER === $this->dmcss_data['access'] ) {
                return $this->draw_login_page( $post, 'login' );
            }
            elseif( self::CONFIRM_REGISTER === $this->dmcss_data['access'] ) {
                return $this->draw_login_page( $post, 'register' );
            }

            // Check user access and determine if content shown
            $user_access = $this->current_user_can_view( $post_id );
            
            if( false === $user_access && ( is_single() || is_page() ) ) {
                // User does not have access; show login form if on page / post
                $input = $this->draw_login_page( $post, 'subscribe' );
            }
            elseif( self::REGISTERED_NO_ACCESS === $user_access && ( is_single() || is_page() ) ) {
                // User does not have access; show login / registration form if on page / post
                $input = $this->draw_login_page( $post, 'register' );
            }
            elseif( null === $user_access && ( is_single() || is_page() ) ) {
                // User does not have access to subrate or pubcode; show access error if on page / post
                $input = $this->show_access_error( $post );
            }
            elseif( self::REGISTERED_ACCESS === $user_access && !empty( $this->options['registered_marketing'] ) ) {
                // Add marketing content to registered user views if available
                return $input . '<div id="dmcss_registered_content">' . $this->options['registered_marketing'] . '</div>';
            }
        }
        return $input;
    }

    /*
     * Filter to determine if article is behind paywall or not
     * -- If it is then we show lock icon
     *
     * @param $input = post's title
     * @param int $post_id
     * @return post title w/ lock icon if needed
     */
    public function the_title( $input, $post_id = '' ) {
        //die("870");
        //Check for nav_menu, security bypass or already present lock and don't show lock if found
        $my_post = get_post( $post_id );

        if( 'nav_menu_item' == $my_post->post_type || self::BYPASS === $this->dmcss_data['access'] || self::ADMIN_BYPASS === $this->dmcss_data['access'] || ( preg_match( '<span class="lockleft">', $input ) || preg_match( '/<span class="dmcss_key_icon">/', $input ) ) ) {
            return $input;
        } else {
                
            // If $post_id not set, look it up
            if( empty( $post_id ) ) {
                $post_id = get_the_ID();
            }

            // If post object passed in, grab the ID
            if( is_object( $post_id ) ) {
                $post_id = $post_id->ID;
            }

            // Don't lock down postID 0
            if( 0 == $post_id ) {
                return $input;
            }

            $user_access = $this->current_user_can_view( $post_id );
            // Display lock if user doesn't have access
            if(  $user_access == 1 || $user_access == 2  ) {
                return $input;
            }
            else {
                return $this->title_lock_icon( $input );
            }
        }
    }


     public function add_lock_on_title( $input, $post_id = '' ) {

        $my_post = get_post( $post_id );
        
        if( 'nav_menu_item' == $my_post->post_type || ( preg_match( '<span class="lockleft">', $input ) || preg_match( '/<span class="dmcss_key_icon">/', $input ) ) ) {
            return $input;
        } else {

            // If $post_id not set, look it up
            if( empty( $post_id ) ) {
                $post_id = get_the_ID();
            }

            // If post object passed in, grab the ID
            if( is_object( $post_id ) ) {
                $post_id = $post_id->ID;
            }

            // Don't lock down postID 0
            if( 0 == $post_id ) {
                return $input;
            }

            $policy = get_post_meta( $post_id, 'dmcss_security_policy', true );  
            $policy_replace = str_replace(' ', '_', $policy);
            $policy_meta = strtolower($policy_replace);
            
            if(($policy_meta == 'subscriber_only') && !empty($this->options['show_key_icon'])){
                return $this->title_lock_icon( $input );
            } else {
                return $input;
            }
        }
        
    }

    /*
     * Filter to reset login cookie duration of WP Users
     *
     * @param $duration - default cookie duration
     * @param $user_id - current user's id
     * @param $remember - status of 'remember me' box
     * @return login cookie duration
     */
    public function updateCookieDuration( $duration, $user_id, $remember ) {
        // For PSA users, set login duration according to admin setting or default of 30 days
        // Don't change duration for non-PSA users
        if( !empty( $this->dmcss_data['circ'] ) ) {
            $loginDuration = $this->options['login_duration'];
            if( !empty( $loginDuration ) ) {
                $duration = $loginDuration * 24 * 3600;
            }
            else {
                $duration = 2592000;
            }
        }
        return $duration;
    }

    /******** END WP FILTERS **********/

    /******** WP ACTIONS **************/

    /**
     * Reset message cookie and save messages in REQUEST Scope
     * Does not work reliably on multiple consecutive page loads***
     * @return void
     */
    public function clear_message_cookies() {
        if(isset($_COOKIE['dmcss']['error'])){
            $this->dmcss_data['error']->add( 'dmcss_error', $_COOKIE['dmcss']['error'] );
            @setcookie('dmcss[error]', null, time() + 2592000, '/', $_SERVER['HTTP_HOST']);
        }
        $messages = isset($_COOKIE['dmcss[messages]']) ? unserialize(stripslashes($_COOKIE['dmcss[messages]'])) : '';

        if(is_array($messages) && !empty($messages)){
            $_REQUEST['dmcss[messages]'] = $messages;
            $msg = array();
            $status = setcookie('dmcss[messages]', serialize($msg), time() + 2592000, '/', $_SERVER['HTTP_HOST']);
            /*
                        echo('messages were:');
                        print_r($messages);
                        echo('<br>');
                        echo('messages are:');
                        print_r($msg);
                        echo('<br>');
                        echo('cookie set:'.$status.'<br>');
                        echo('cookie is:');
                        print_r(unserialize(stripslashes($_COOKIE['dmcss[messages]'])));
                        echo('<br>');
                        echo('request is:');
                        print_r($_REQUEST['dmcss[messages]']);
            */
        }
    }

    /**
     * Import JS & jQuery for login form
     *
     * @return void
     */
    public function importJS(){
        if( isset($this->options['popup_login']) ) {
            wp_register_script( 'dmcss_login_popup', plugins_url( '/login_popup.js', __FILE__ ), array( 'jquery' ) );
            wp_enqueue_script( 'dmcss_login_popup' );
        }
        wp_register_script( 'dmcss_login_form', plugins_url( '/login_form.js', __FILE__ ), array( 'jquery' ) );
        wp_enqueue_script( 'dmcss_login_form' );
    }

    /**
     * Set security defaults, validate token & subcription
     *
     * @return void
     */
    public function init() {
        //Coldfusion HTTPS support call to PSA 
        $url_path = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/');
          if ( $url_path === 'coldfusion-psa-url' ) {
             $load = dirname(__FILE__) . "/coldfusion-psa-template.php";
             if ($load) {
                include( $load );
                exit(); 
             }
          }
         //Coldfusion HTTPS support call to PSA end here 
        global $current_user;
        /**
         * Set security to 'bypass' and exit if...
         * global bypass set
         * web service undefined
         */
        if( file_exists( PAYWALL_BYPASS_FILE ) || empty( $this->options['ws_url'] ) ){
            $this->dmcss_data['access'] = self::BYPASS;
            return;
        }
        
        /**      
        * External on/off switch for the paywall, allows external (non-paywall) code to remotely disable the paywall     
        * a return of boolean true will engage bypass mode and bail out      
        */   
        
        $filter_return = apply_filters('paywall-external-bypass', false);    
        if ($filter_return === TRUE) {   
            $this->dmcss_data['access'] = self::BYPASS;      
            return;      
            }
        

        if( is_user_logged_in() ) {
            // Check for requested logout
            if( isset( $_GET['dmcss'] ) && 'logout' == $_GET['dmcss'] ) {
                wp_logout();
            }

            // Get current logged in user if exists, dmcss meta, & user role
            $current_user = wp_get_current_user();
            $dmcss_user = get_user_meta( $current_user->ID,'dmcss_current_user', true );
            
            $standard_roles = array_intersect($current_user->roles, $this->paywall_roles);
            $role = array_shift($standard_roles);
            //$role = $current_user->roles[0];
            
            // Set security bypass for author / editor / admin
            if( is_super_admin() || preg_match( '/author|editor|administrator/i', $role ) ) {
                $this->dmcss_data['access'] = self::BYPASS;
                return;
            }

            // Check role for this blog; add PSA user role if role empty & PSA info set
            if( !is_super_admin() && empty( $role ) && !empty( $dmcss_user ) ) {
                $current_user->add_role( dmcss_wp::SUBSCRIBER_ROLE );
            }
            // Not author / editor / admin; check token & validation if user has PSA capability
            elseif( current_user_can( dmcss_wp::SUBSCRIBER_CAPABILITY ) ){
                // Save current_user to dmcss_data; convert case for reliable comparison
                $this->dmcss_data['user'] = array_change_key_case($dmcss_user);

                // Compare token for each pub; log out if not matching
                if( $this->options['validate_tokens'] ) {
                    $cookie = unserialize(stripslashes($_COOKIE['dmcss']['current_user']));
                    $keys = array_keys($dmcss_user);

                    foreach( $keys as $key ){
                        if($cookie[$key]['token'] != $dmcss_user[$key]['token']){
                            // Log to user log if activated
                            if( PAYWALL_LOG_USERS && defined( 'PAYWALL_USER_LOG' ) ) {
                                $log_str = '\'' . time() . '\',\'' .
                                    gethostbyaddr('127.0.0.1') . '\',\'' .
                                    $_SERVER[ 'SERVER_NAME' ] . '\',\'' .
                                    $current_user->user_login . '\',\'Log User Off\',\'' .
                                    $_SERVER['REMOTE_ADDR']   . '\',\'' .
                                    $_SERVER['HTTP_REFERER'] . '\',\'' .
                                    $_SERVER['HTTP_USER_AGENT'] . '\',\'' .
                                    'invalid token for pub: ' . $key . ' acct: ' . $dmcss_user[$key]['accountNumber'] . '\'' . PHP_EOL;
                                error_log( $log_str, 3, PAYWALL_USER_LOG );
                            }

                            update_user_meta( $current_user->ID, 'dmcss_lastValidated', 0 );
                            if( empty( $this->options['duplicate_session'] ) ) {
                                $this->logout_with_message( dmcss_wp::DEFAULT_DUPLICATE_SESSION_MESSAGE );
                            }
                            $this->logout_with_message( $this->options['duplicate_session'] );
                            return;
                        }
                    }
                }

                // Look up last validated & Determine time since last validated via WS
                $now = time();
                $validated = get_user_meta( $current_user->ID, 'dmcss_lastValidated', true );
                $since_validated = $now - $validated;

                // Call web service to validate subscription once every 24 hrs
                if( $since_validated >= 86400 ){
                    $login_time = get_user_meta( $current_user->ID, 'dmcss_timestamp', true );
                    $duration = $now - $login_time;

                    // Update last validated time
                    if($this->validate_subscription_via_wsdl( $duration ) ){
                        update_user_meta( $current_user->ID, 'dmcss_lastValidated', $now );
                    }
                    // Or log out if subscription no longer valid
                    else{
                        if( PAYWALL_LOG_USERS && defined( 'PAYWALL_USER_LOG' ) ) {
                            $log_str = '\'' . time() . '\',\'' .
                                gethostbyaddr('127.0.0.1') . '\',\'' .
                                $_SERVER[ 'SERVER_NAME' ] . '\',\'' .
                                $current_user->user_login . '\',\'Log User Off\',\'' .
                                $_SERVER['REMOTE_ADDR']   . '\',\'' .
                                $_SERVER['HTTP_REFERER'] . '\',\'' .
                                $_SERVER['HTTP_USER_AGENT'] . '\',\'' .
                                'invalid subscription / duration reached (' . $duration . ')\'' . PHP_EOL;
                            error_log( $log_str, 3, PAYWALL_USER_LOG );
                        }

                        update_user_meta( $current_user->ID, 'dmcss_lastValidated', 0 );
                        if( empty( $this->options['session_expired'] ) ) {
                            $this->logout_with_message( dmcss_wp::DEFAULT_SESSION_EXPIRED );
                        }
                        $this->logout_with_message( $this->options['session_expired'] );
                        return;
                    }
                }
            }
        }
    }

    /**
     * Redirect users on logout to previous page or home page - do not redirect when logging out from admin
     * Also unset any PSA cookies
     *
     * @return void
     */
    public function logout_redirect() {
        // Unset PSA coookies
        @setcookie('dmcss_userID', '', time() - 31536000, '/', $_SERVER['HTTP_HOST']);
        @setcookie('dmcss[current_user]', '', time() - 31536000, '/', $_SERVER['HTTP_HOST']);
        @setcookie('dmcss_current_user', '', time() - 31536000, '/', $_SERVER['HTTP_HOST']);

        // If logging out of the admin, do not redirect
        if(strstr($_SERVER['HTTP_REFERER'], '/wp-admin/') !== false){
            return;
        }
        elseif( isset( $_GET['dmcss'] ) && 'logout' == $_GET['dmcss'] ) {
            // if we're logging out via a GET variable...
            // send them back to the page without the GET variable
            $redirect = $this->remove_get_var($_SERVER['REQUEST_URI'], 'dmcss');
        }
        elseif($_SERVER['HTTP_REFERER'] && (strstr($_SERVER['HTTP_REFERER'], '/wp-admin/') === false)){
            // if we know the page that logged us out and it's not in the admin...
            // we should only hit this section if this is being called from the wp_logout hook
            // go back to the referrer
            $redirect = $this->add_get_var($_SERVER['HTTP_REFERER'], 'loggedout', 'true');
        }
        else{
            // default - go to the main page
            $redirect = get_bloginfo('url');
        }

        // Preserve any messages set during login
        if(isset($_REQUEST['dmcss[messages]']))
            @setcookie('dmcss[messages]', serialize($_REQUEST['dmcss[messages]']), time() + 2592000, '/', $_SERVER['HTTP_HOST']);
        //ob_start();
        wp_redirect($redirect);
        //ob_end_flush();
        exit();
    }

    /**
     * Load correct page template when requesting login page
     *
     * @return void
     */
    public function template_redirect() {
        ///workaround so when logging in not from a post or page that previously selected post/page doesn't display
        if(!is_single() && !is_page() ) $GLOBALS['posts'] = 0;

        // if we are replacing the content, we should use a page or post template
        if( file_exists( TEMPLATEPATH . '/custom-login.php' ) && ( is_single() || is_home() || is_front_page() || is_page() || is_category() ) ) {
            $template = TEMPLATEPATH . '/custom-login.php';
        }
        elseif( file_exists( STYLESHEETPATH . '/single.php') && ( is_single() || is_home() || is_category() ) ) {
            $template = STYLESHEETPATH . '/single.php';
        }
        elseif( file_exists( STYLESHEETPATH . '/page.php' ) && is_page() ) {
            $template = STYLESHEETPATH . '/page.php';
        }
        // Default to wp-login if no template available
        else{
            wp_redirect( get_bloginfo( 'url' ) . '/wp-login.php' );
            exit;
        }
        include( $template );
        exit;
    }

    /*
     * Filter to validate WP Users against the Web Service
     *
     * @return WP User
     */
      public function validate_wpuser( $ulogin, $user ) {
        // Check if user signed in via PSA and paywall isn't bypassed
      
        $password = $_POST['pwd'];
        if( $this->dmcss_data['access'] !== self::BYPASS && !empty( $this->options['ws_url'] ) ){
            // User did not validate against PSA - build publication array for web service call
            $pubcode = '';
            foreach( $this->options['publications'] as $pub ){
                if( 0 < strlen( $pubcode ) ) {
                    $pubcode .= ',';
                }
                $pubcode .= isset($pub['code']) ? $pub['code'] : ''; // $pub[code]
            }
            // Call web service with email
            // Confirm we have at least 1 publication configured before calling web service
            if( 1 < strlen( $pubcode ) ) {
            
                $result = $this->validate_wpuser_via_wsdl($user->user_login,$password,$pubcode);
                
                // Check that result is non-false, a properly formatted array, & index[0] (error msg) is not set


                if($result !='0' ) {
                     $xmlObject1 = new SimpleXMLElement($result);
                        $node1 = $xmlObject1->children();
                         
                      
                    
                     if(!isset($token) && !isset($node1[0]->API)) {
                        $node_cnt = count($node1);
                        $sub_rec1 = $node1->sub;  

                        //Logging User -- PSA Users
                            $urldecoded_email = urldecode($sub_rec1->CUS_EMAIL);
                            $urldecode_strtolower = strtolower($urldecoded_email);
                            $blog_id = get_current_blog_id();
                            if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
                            {
                              $ip=$_SERVER['HTTP_CLIENT_IP'];
                            }
                            elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
                            {
                              $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
                            }
                            else
                            {
                              $ip=$_SERVER['REMOTE_ADDR'];
                            }
                            

                        $sub_status1 = $sub_rec1->SUB_STATUS;
                        $standard_roles1 = array_intersect($user->roles, $this->paywall_roles);
                        $role1 = array_shift($standard_roles1);
                                               
                   if( preg_match( '/contributor|subscriber/i', $role1 ) && $sub_status1 != 'A' ) {
                        $user->remove_cap( dmcss_wp::REGISTERED_USER_CAPABILITY );
                        $user->remove_cap( dmcss_wp::SUBSCRIBER_CAPABILITY );
                        // The web service returned an invalid result or credential validation unsuccessful; remove subscriber-only & registered user capability from subscriber / contributor    

                         return $user;
                       }
                
                   }

                   $current_user = wp_get_current_user();

                         
                } else {
                    //Logging User -- Wordpress Users
                    $blog_id = get_current_blog_id();
                    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
                    {
                      $ip=$_SERVER['HTTP_CLIENT_IP'];
                    }
                    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
                    {
                      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
                    }
                    else
                    {
                      $ip=$_SERVER['REMOTE_ADDR'];
                    }
                    // global $wpdb;
                    // $table_name = $wpdb->prefix . 'paywall_users_logs';
                    // $wpdb->insert( 
                        // $table_name, 
                        // array( 
                            // 'first_name' => '', 
                            // 'last_name' =>  '', 
                            // 'email' =>      $user->user_email, 
                            // 'username' =>   $user->user_login,
                            // 'time' => current_time( 'mysql' ), 
                            // 'ip_address' => $ip, 
                            // 'blog_id' => $blog_id, 
                            // 'user_role' => 'Wordpress User', 
                        // ) 
                    // );
                  //Logging User -- Wordpress Users 
                }
                // User has been validated; add dmcss usermeta & cookies
                $this->update_wp_user_meta( $user->ID,$result );
                // Add Subscriber-Only & Registered User cap to contributor / subscriber users
                $standard_roles = array_intersect($user->roles, $this->paywall_roles);
                $role = array_shift($standard_roles);
                //$role = $user->roles[0];
                
                if( preg_match( '/contributor|subscriber/i', $role ) ) {
                    $user->add_cap( dmcss_wp::REGISTERED_USER_CAPABILITY );
                    $user->add_cap( dmcss_wp::SUBSCRIBER_CAPABILITY );
                }

            }
        }

        $bc_config = get_option('tdc_paywall_data');
        if(isset($bc_config['technavia_link'])){
            $tech_link = $bc_config['technavia_link'];
        }
                
         if($_POST['login_red']=="login"){
            $token = base64_encode( $user->user_email );
            wp_redirect($tech_link.'?token='.$token);
            exit;   
        } 

        return $user;
    }
    /**
     * Check show_title, login get var, & blog-level subrates
     *
     * @return void
     */
    public function wp() {
        //Check for URL forward & logged in user; redirect if needed
        if( is_user_logged_in() && isset( $_GET['forward'] ) ) {
           // ob_start();
            wp_redirect( $_GET['forward'] );
           // ob_end_flush();
            exit;
        }

        // Validate registration if one has been submitted
        if( isset( $_GET['dmcss'] ) && 'register' == $_GET['dmcss'] && isset( $_POST['user_email'] ) ) {
            $this->validate_user_signup( $_POST['user_email'] );
            $this->dmcss_data['access'] = self::CONFIRM_REGISTER;
            if( !is_single() || !is_page() ) {
                $GLOBALS['wp_query']->post_count = 1;
                $GLOBALS['posts'] = array_slice( $GLOBALS['posts'], 0, 1 );
                add_action('template_redirect', array( &$this, 'template_redirect') );
            }
            return;
        }
        // Check for registered user activation
        elseif( isset( $_GET['dmcss'] ) && 'activate' == $_GET['dmcss'] && isset( $_GET['key'] ) ) {
            $result = wpmu_activate_signup( $_GET['key'] );
            if ( is_wp_error($result) ) {
                $this->dmcss_data['error'] = $result;
            }
            else {
                extract( $result );
                $user = get_userdata( (int) $user_id);
                update_user_meta( $user_id, 'show_admin_bar_front', 'false' );
                $this->dmcss_data['message'] = '
                <div id="signup-welcome">
                    <p class="welcome-header">Your account is now active!</p>
                    <p><strong>Username:</strong> ' . $user->user_login . '</p>
                    <p><strong>Password:</strong> ' . $password . '</p>
                </div>';
            }
            $this->dmcss_data['access'] = self::ACTIVATE_USER;
            if( !is_single() || !is_page() ) {
                $GLOBALS['wp_query']->post_count = 1;
                $GLOBALS['posts'] = array_slice( $GLOBALS['posts'], 0, 1 );
                add_action('template_redirect', array( &$this, 'template_redirect') );
            }
            return;
        }
        // Send to login page if we asked for one or if home / category page(s) locked out and security not bypassed
        elseif( ( self::BYPASS !== $this->dmcss_data['access'] && !$this->options['master_bypass'] ) &&
            ( ( isset( $_GET['dmcss'] ) && 'login' == $_GET['dmcss'] && !is_user_logged_in() ) ||
                ( $this->options['home_lock'] && ( is_home() || is_front_page() ) && !current_user_can( dmcss_wp::SUBSCRIBER_CAPABILITY ) ) ||
                ( $this->options['category_lock'] && is_archive() && !current_user_can( dmcss_wp::SUBSCRIBER_CAPABILITY ) ) )
        ) {
            // Special access case if we asked for login page or home / category lock
            $this->dmcss_data['access'] = self::LOGIN_FORM;
            $GLOBALS['wp_query']->post_count = 1;
            $GLOBALS['posts'] = array_slice( $GLOBALS['posts'], 0, 1 );
            add_action('template_redirect', array( &$this, 'template_redirect') );
            return;
        }

        // Check for logged in DMCSS User
        if( isset($this->options['master_bypass']) && $this->options['master_bypass']) {
            $this->dmcss_data['access'] = self::ADMIN_BYPASS;
            
        }
        elseif( current_user_can( dmcss_wp::SUBSCRIBER_CAPABILITY ) && self::BYPASS !== $this->dmcss_data['access'] ) {
            // Default to signed in, but no pub access;
            $this->dmcss_data['access'] = self::NO_PUB_ACCESS;

            
            // Walk through blog pubs; check if user has access
            foreach( $this->options['publications'] as $pub ) {
                // Check if user has this pub; need to do case-insensitive search
                
                // if( array_key_exists( strtolower( $pub['code'] ), $this->dmcss_data['user'] ) ) {
                    // // Check subrates if set for this pub
                    // if( !empty( $pub['subrate'] ) ) {
                         
                     // //   if( $this->dmcss_data['user'][$pub['code']] == $pub['subrate'] ) {
                        // if( $this->dmcss_data['user'][$pub['code']]['subrate'] == $pub['subrate'] ) {
                            // $this->dmcss_data['access'] = self::SUBSCRIBER_ACCESS;
                            // break;
                        // }

                        // else {
                             // $this->dmcss_data['access'] = self::NO_PUB_ACCESS;
                        // }
                    // }
                    // // Pub subrate not set; grant access
                    // else {
                        // $this->dmcss_data['access'] = self::SUBSCRIBER_ACCESS;
                        // break;
                    // }
                // }
                
                if( array_key_exists( strtolower( $pub['code'] ) , $this->dmcss_data['user'] ) ) {
                    // Check subrates if set for this pub
                    if( !empty( $pub['subrate'] ) ) { 
                        $subrate_holder = $pub['subrate'];
                        $comma_separated = explode(",",$subrate_holder);
                        foreach ($comma_separated as $subrate_check ) {
                       if( $this->dmcss_data['user'][$pub['code']]['subrate'] == $subrate_check) {  
                            $this->dmcss_data['access'] = self::SUBSCRIBER_ACCESS;
                            break;
                           }
                           else {
                             $this->dmcss_data['access'] = self::NO_PUB_ACCESS;
                           }
                        }   
                    }
                    // Pub subrate not set; grant access
                    else {
                        $this->dmcss_data['access'] = self::SUBSCRIBER_ACCESS;
                        break;
                    }
                }
                
            }

            // User has blog access; check category subrate lockdown
            if( $this->options['use_category_subrates'] && self::SUBSCRIBER_ACCESS === $this->dmcss_data['access'] ) {
                $subs = $this->options['category_subrates'];
                if( 0 < count( $subs ) ){
                    $this->dmcss_data['access'] = self::CATEGORY_SUBRATES;
                }
            }

        }
        elseif( is_user_logged_in() && self::BYPASS !== $this->dmcss_data['access'] ) {
            // Logged in, but no subscriber access.  Set access to reflect this
            $this->dmcss_data['access'] = self::NO_PUB_ACCESS;
        }
        add_filter('the_posts', array(&$this, 'posts_filter'), 11);
    }

    /******** END WP ACTIONS **********/

    /******** PUBLIC FUNCTIONS ********/

    /*
     * Function to determine if current_user can read post with $post_id
     *
     * @param int $post_id
     * @return true, false, REGISTERED_ACCESS, REGISTERED_NO_ACCESS, or null if access error
     */
    public function current_user_can_view( $post_id ) {
        global $switched;
        // Check for security bypass and return true if found
        if( $this->dmcss_data['access'] === self::BYPASS || $this->dmcss_data['access'] === self::ADMIN_BYPASS ) {
            return true;
        }

        // Check security meta and determine if content shown
        $meta = get_post_meta( $post_id, 'dmcss_security_policy', true );
        $post = get_post( $post_id );
        

        // Force registered posts to subscriber only if registered access is not allowed
        if( array_key_exists('allow_registered_users',$this->options) && !$this->options['allow_registered_users'] && preg_match( '/registered/i', $meta ) ) {
            $meta = 'Subscriber Only';
        }


        switch( $meta ){
            case 'Always Free':
                // Content is free - allow access
                $this->add_message( 'notice', '<p>Post:' . $post_id . ' ' . $post->post_title . '</p><p> Always Free</p>' );
                return true;
                break;
            case 'Registered User':
                //Check if post is free by Allow Free From Birth
                if( array_key_exists('allow_free_from_birth',$this->options) && $this->options['allow_free_from_birth'] == true && !current_user_can( dmcss_wp::SUBSCRIBER_CAPABILITY ) ){
                    if( $this->valid_by_birth_time( $post_id, $post->post_date )){
                        if ( function_exists('tdc_gac_add_customvar') ) {
                            tdc_gac_add_customvar(1,'Paywall Access','Free From Birth');
                            }
                        $this->add_message( $message_type, $message_prefix . 'access by Allow Free for Hours</p>' );
                        return true;
                    }
                }
            
                if( current_user_can( dmcss_wp::REGISTERED_USER_CAPABILITY ) ) {
                    $this->add_message( 'notice', '<p>Post:' . $post_id . ' ' . $post->post_title . '</p><p> Registered User - logged in access</p>' );
                    return self::REGISTERED_ACCESS;
                }
                elseif( is_user_logged_in() ) {
                    $this->add_message( 'notice', '<p>Post:' . $post_id . ' ' . $post->post_title . '</p><p> Registered User - no capability</p>' );
                    return self::REGISTERED_NO_ACCESS;
                }
                else {
                    $this->add_message( 'notice', '<p>Post:' . $post_id . ' ' . $post->post_title . '</p><p> Registered User - not logged in</p>' );
                    return self::REGISTERED_NO_ACCESS;
                }
                break;
            case 'Subscriber Only':
            default:
                // Subscriber only or dmcss_security_policy on page / post not set - treat as subscriber only for any configured pub
                // Check if we're pulling content from an un-secured blog, return true if so
                
                if( $switched && !$meta ) {
                    return true;

                }

                // Configure debug messages
                $message_prefix = '<p>Post:' . $post_id . ' ' . $post->post_title . '</p><p> Subscriber Only - ';
                $message_type = 'notice';
                if( !preg_match( '/subscriber only/i', $meta ) ) {
                    $message_prefix = '<p>Post:' . $post_id . ' ' . $post->post_title . '</p><p> Security Meta not set - ';

                }
                
                //Check if post is free by Allow Free From Birth
                if( array_key_exists('allow_free_from_birth' , $this->options) && $this->options['allow_free_from_birth'] == true && !current_user_can( dmcss_wp::SUBSCRIBER_CAPABILITY ) ){
                    if( $this->valid_by_birth_time( $post_id, $post->post_date )){
                        if ( function_exists('tdc_gac_add_customvar') ) {
                            tdc_gac_add_customvar(1,'Paywall Access','Free From Birth');
                            }
                        $this->add_message( $message_type, $message_prefix . 'access by Allow Free for Hours</p>' );
                        return true;
                    }
                }
                        

               // Check if categories locked down by subrate
                if( self::CATEGORY_SUBRATES === $this->dmcss_data['access'] ) {
                    
                    // Determine if this post is in locked-down category
                    $locked_rate = $this->check_category_subrates( $post_id );
                    if( false !== $locked_rate ) {
                        // Subrate found, post is in a locked category
                        $access = $this->check_post_subrates( $locked_rate );

                        if( $access ) {
                            $this->add_message( $message_type, $message_prefix . 'logged in access</p>' );
                            return true;
                        }
                        else {
                            // Otherwise return access error
                            $this->add_message( $message_type, $message_prefix . 'locked & no subrate access</p>' );
                            return null;
                        }
                    }
                }

                // No category lockdown or no locked category; compare pubcodes
                if( self::SUBSCRIBER_ACCESS === $this->dmcss_data['access'] || ( self::CATEGORY_SUBRATES === $this->dmcss_data['access'] && !$locked_rate ) ){  
                    $access = $this->check_post_pubcodes( $post_id );
                    if( $access ){     
                        $this->add_message( $message_type, $message_prefix . 'logged in access</p>');
                        return true;
                    }
                    else {
                         // Otherwise return access error
                        $this->add_message( $message_type, $message_prefix . 'locked & no pub access</p>');
                        return null;
                    }
                }
                
                // Logged in, but no pub access; return error
                elseif( $this->dmcss_data['access'] === self::NO_PUB_ACCESS ){
                    $this->add_message( $message_type, $message_prefix . 'logged in, but no access</p>');
                    return null;
                }
                // Not logged in; return false
                else{
                    $this->add_message( $message_type, $message_prefix . 'locked & not logged in</p>');
                    return false;
                }

        }
        // Return false if security is indeterminate
        return false;
    }
    
    
    /**
     * Access plugin instance. You can create further instances by calling
     * the constructor directly.
     *
     * @wp-hook wp_loaded
     * @return  object T5_Spam_Block
     */ 
    protected static $instance = NULL;
    public static function get_instance()
    {
        if ( NULL === self::$instance )
            self::$instance = new self(1);

        return self::$instance;
    }
    
    /*
    Create static getter function for bc_paywall options - globally accessible - likely to be modified later.
    */
    
    public function get_allow_free_from_birth(){
    if(!current_user_can( dmcss_wp::SUBSCRIBER_CAPABILITY )){
        return $this->options['allow_free_from_birth'];
    }
    return false;
    }
    
    /*
    Function to see if a Free From Birth Post is valid by it's post date and hours settings.
    */
    
    function valid_by_birth_time($post_id, $post_date){
    if (!is_page($post_id)){
        $free_from_birth = get_post_meta($post_id, 'dmcss_free_from_birth', true );                 
        if(isset($free_from_birth ) && $free_from_birth != '' && is_numeric($free_from_birth)){
            if(intval(strtotime( current_time('mysql') )) < (intval(strtotime( $post_date ))+(intval($free_from_birth)*3600)) ){
                return true;
            }
        }
    }
    return false;
    }
    
    /**
     * Returns true / false if user has access to $code
     *
     * @param string $code The publication code, example: molw, libn, djc
     *
     * @return boolean true / false if user has access to $code or true if admin user
     */
    public function user_has_access( $code ) {
        // Return true if security is bypassed
        if( self::BYPASS === $this->dmcss_data['access'] ) {
            return true;
        }
        // Check for logged in user
        if( is_user_logged_in() ) {
            // Use cached copy of dmcss_current_user
            if( is_array( $this->dmcss_data['user'] ) && array_key_exists( strtolower( $code ), $this->dmcss_data['user'] ) ) {
                return true;
            }
        }
        // Return false if user not logged in or pubcode not found
        return false;
    }

    /******** END PUBLIC FUNCTIONS ****/

    /******** PRIVATE FUNCTIONS *******/

    /**
     * Add message to dmcss messages in cookie & request scope
     *
     * @param string $type
     * @param string $message
     * @return true if message set; false if not logging
     */
    private function add_message( $type='notice', $message='' ) {
        // Do nothing if we're not debugging
        if( !$this->options['show_debug'] )
            return false;
        // Check for existing messages
        if(isset($_REQUEST['dmcss[messages]']))
            $messages = $_REQUEST['dmcss[messages]'];
        else
            $messages = unserialize(stripslashes($_COOKIE['dmcss[messages]']));

        // create arrays if needed
        if(!is_array($messages))
            $messages = array();
        if(!is_array($messages[$type]))
            $messages[$type] = array();

        // Check to see if we already have this message
        foreach($messages[$type] as $existing){
            if(strcasecmp($existing, $message) == 0)
                // Duplicate found; return false without setting message again
                return false;
        }
        // append our message
        $messages[$type][] = $message;
        // store it
        @setcookie('dmcss[messages]', serialize($messages), time() + 2592000, '/', $_SERVER['HTTP_HOST']);

        ///store in request scope also
        $_REQUEST['dmcss[messages]'] = $messages;

        return true;
    }

    /**
     * Build and return excerpt for $post
     *
     * @param object $post
     * @return excerpt for $post
     */
    private function build_post_excerpt( $post ) {
        // Check for <!--more-->
        if( preg_match( '/.*!--more--\>/s', $post->post_content, $matches ) ){
            $excerpt = strip_tags( array_shift( $matches ) );
            $excerpt = stripslashes( strip_shortcodes( $excerpt ) );
        }       
        
        // Build 55 word excerpt, excerpt by paragraphs, or user specified length excerpt
        else{
            $excerpt = strip_tags( $post->post_content );
            $excerpt = stripslashes(strip_shortcodes( $excerpt ) );
            
            //If custom paywall excerpt length is specified, add a filter to apply it
            if($this->options['paywall_excerpt_paragraphs']){
            $paragraphs = explode( "\n", $excerpt);
            $paragraphs = array_slice($paragraphs,0,$this->options['paywall_excerpt_length'] );
            $excerpt ='<p>' . implode( "</p><p>", $paragraphs) . '</p>';
            
            return $excerpt;
            }
            elseif( $this->options['paywall_excerpt_length'] ) {
                $excerpt_length = $this->options['paywall_excerpt_length'];
            }else{
            $excerpt_length = 55;
            }
            $words = explode( ' ', $excerpt, $excerpt_length + 1 );
            if( count( $words ) > $excerpt_length ) {
                array_pop( $words );
                array_push( $words, '...' );
                $excerpt = implode( ' ', $words );
            }
        }
        return $excerpt;
    }

    /*
     * Function to determine if article is in locked category
     *
     * @param object $post
     * @return list of subrates or false if none found
     */
    private function check_category_subrates( $post_id ) {
        // Get all post categories
        $categories = get_the_category( $post_id );
        foreach( $this->options['category_subrates'] as $locked_category ){
            // Loop through locked categories and check each against post categories
            foreach( $categories as $cat ) {
                // If subrate found, post is in a locked category
                if( $cat->cat_ID = $locked_category['ID'] ) {
                    return $locked_category['subrate'];
                }
            }
        }

        // Post is not in a locked category
        return false;
    }

    /*
     * Function to determine if user and post pubcode(s) match
     *
     * @param object $post
     * @return boolean user has access or not
     */
    private function check_post_pubcodes( $post_id ) {
        // Check if post has a pubcode; return true if it doesn't
        $post_pub = get_post_meta( $post_id, 'dmcss_pub_code', true );
        if( empty( $post_pub ) ) {
            return true;
        }
        else {
            //for dailyreporter
            if($post_pub == "wisc,mlkss" || $post_pub == "wisc" || $post_pub == "mlkss") {
                $post_pub = "tdr-wi";
              }    
            //for dailyreporter
            
            //for DJC Oregon
              if($post_pub == "djc") {
                $post_pub = "djc-or";
              } 
            //for DJC Oregon 
            
            //for JRLR 23rdLN
              if($post_pub == "23RDLN") {
                $post_pub = "jrlr";
              } 
            //for JRLR 23rdLN
            
            //for Dailyrecord
              if($post_pub == "mddr") {
                $post_pub = "tdr-md";
              }
              if($post_pub == "mdflm") {
                $post_pub = "mflu";
              }
            //for Dailyrecord
            
            //for NYDailyrecord
              if($post_pub == "roch") {
                $post_pub = "tdr-ny";
              }
            //for NYDailyrecord
            
            
            // Check post pubcode(s) vs. user pubcode(s)
            $user_pubs = array_keys($this->dmcss_data['user']);
            foreach( $user_pubs as $user_pub ){
                // If matching pubcode found, return true
                if( preg_match( '/' . $user_pub . '/i', $post_pub ) ) {
                    return true;
                }
            }
        }

        // Return false if no matching pubcode found
        return false;
    }

    /*
     * Function to determine if user has access to locked subrate
     *
     * @param object $post
     * @param string $locked_subrate
     * @return boolean user has access or not
     */
    private function check_post_subrates( $locked_subrate ) {
        // Check post subrate(s) vs. user subrate(s)
        foreach( $this->dmcss_data['user'] as $user_pub ) {
            // If matching subrate found, return true
            if( preg_match( '/' . strval( $user_pub['subrate'] ) . '/i', $locked_subrate ) ) {
                return true;
            }
        }
        // Return false if no matching subrate found
        return false;
    }

    /**
     * Add the requested login form to the current page / post
     *
     * @param object $post
     * @param string $default = 'subscribe|register|login'
     * @return page / post with login form
     */
    private function draw_login_page( $post, $default = 'subscribe' ) {
        require_once( 'bc-paywall-templates.php' );

        // Show basic login form
        if( $this->options['show_excerpt'] ) {
            $excerpt = $post->post_excerpt;
            // If no post excerpt, make one
            if( empty( $excerpt ) ) {
                $excerpt = $this->build_post_excerpt( $post );
            }
        }

        // Generate paywall content; need to use ob_start to fire login / register hooks
        ob_start();
        bc_paywall_templates::display_paywall( $this->options, $this->dmcss_data, $default );
        $output = $excerpt . '<div class="dmcss_login_form">' . ob_get_contents() . '</div>';
        ob_end_clean();
        return $output;
    }

    /**
     * Attempt to log user into publication(s) defined in the admin
     *
     * @param string $username
     * @param string $password
     * @param string $pubcode
     * @return array - Web Service response
     */

 private function login_via_wsdl( $username,$password,$pubcode ) {
            $customer_number = "";
            $zip = "";
          $postFields = "token=".$this->options['ws_url_token']."&email=".trim($username)."&password=".trim($password)."&PUB_CODE=".trim($pubcode);      
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $url = $this->options['ws_url'];
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
            $result = curl_exec($curl);              
        return $result;
   } 

    /**
     * Log user off & redirect to login page w/ message
     *
     * @param string $msg
     * @return null
     */
    private function logout_with_message( $msg ) {
        if( isset( $msg ) ) {
            @setcookie( 'dmcss[error]', $msg, time() + 2592000, '/', $_SERVER['HTTP_HOST'] );
            wp_logout();
           // ob_start();
            $redirect = $this->add_get_var( $_SERVER['HTTP_REFERER'], 'dmcss', 'login' );
            wp_redirect( $redirect );
           // ob_end_flush();
            exit;
        }
        return null;
    }

    /**
     * Determine if user registration is valid;
     * Send confirmation email or show error
     *
     * @param string $user_name
     * @return void
     */
    private function validate_user_signup( $user_name ) {
        global $blog_id;

        $result = $this->validate_username( $user_name );
        extract( $result );

        // Add a default message in place of a blank CAPTCHA error
        foreach( $errors->get_error_codes() as $code ) {
            $err_msg = $errors->get_error_message( $code );
            if( preg_match( '/captcha/i', $code ) && empty( $err_msg ) ) {
                $errors->add( 'captcha_error', dmcss_wp:: DEFAULT_CAPTCHA_ERROR );
            }
        }

        if( 0 == count( $errors->get_error_codes() ) ) {
            add_filter( 'wpmu_signup_user_notification', array( &$this, 'new_user_email' ), 10, 4 );
            wpmu_signup_user($user_name, $user_name, array( 'add_to_blog' => $blog_id, 'new_role' => dmcss_wp::REGISTERED_USER_ROLE ) );

            // Add registration message
            if( get_site_option( 'site_name' ) ) {
                $site_name = esc_html( get_site_option( 'site_name' ) );
            }
            elseif( isset( $this->options['publications'][0]['title'] ) ) {
                $site_name = $this->options['publications'][0]['title'];
            }
            else {
                $site_name = 'Publication Admin';
            }
            $this->dmcss_data['message'] = '
            <div id="signup-welcome">
                <p class="welcome-header">Thank you for registering with ' . $site_name . '.</p>
                <p>But, before you can log in, <strong>you must activate your access</strong>.</p>
                <p>Please check your inbox at <strong>' . $user_name . '</strong> and click the link given.</p>
                <p>If you do not activate your acess within two days, you will have to sign up again.</p>
            </div>';
        }
        $this->dmcss_data['error'] = $errors;
    }

    /**
     * Show error if DMCSS User doesn't have access
     *
     * @param object $post
     * @return page / post excerpt + access error message
     */
    private function show_access_error( $post ) {
        require_once( 'bc-paywall-templates.php' );

        if( $this->options['show_excerpt'] ) {
            $excerpt = $post->post_excerpt;
            // If no post excerpt, make one
            if( empty( $excerpt ) ) {
                $excerpt = $this->build_post_excerpt( $post );
            }
        }
        $msg = $this->options['no_access'];
        if( empty( $msg ) ) {
            $msg = $this->DEFAULT_NO_ACCESS;
        }
        return $excerpt.'<div class="dmcss_login_form"><div id="login_error" class="message">'.$msg.'</div><div id="subscribe_div">' . bc_paywall_templates::build_subscribe_text( $this->options ) . '</div></div>';
    }

    /**
     * Map PSA Users to Wordpress Users
     *
     * @param string $username
     * @param object $wp_user
     * @param string $password
     * @param array $ws_result
     * @return new WordPress User
     */
    private function sync_wp_user( $user_id_PSA, $email_id_PSA1, $wp_user, $password, $ws_result ) {
        $email_id_PSA = strtolower($email_id_PSA1);
        // Check if existing or new user
        if ( !$wp_user ){
            // Insert new PSA User
            add_filter('send_password_change_email', '__return_false');
            add_filter( 'send_email_change_email', '__return_false' );
            $user_id = wp_insert_user( array(
                'user_login' => $user_id_PSA,
                'user_nicename' => $user_id_PSA,
                'user_pass' => $password,
                'user_email' => $email_id_PSA,
                'role' => dmcss_wp::SUBSCRIBER_ROLE,
                'show_admin_bar_front' => 'false'
            ) );
        }
        else {

            global $wpdb;
            $sql = "UPDATE {$wpdb->users} SET user_login = %s WHERE ID = %d";
            $sql = $wpdb->prepare($sql, $user_id_PSA , $wp_user->ID);
            $wpdb->query($sql);

            $standard_roles = array_intersect($wp_user->roles, $this->paywall_roles);
            $role = array_shift($standard_roles);
             //$role = array_shift( $wp_user->roles );
            if( preg_match( '/' . dmcss_wp::SUBSCRIBER_ROLE . '/i', $role ) ) {
                // Update existing PSA user
              if( wp_check_password( $password, $wp_user->user_pass, $wp_user->ID ) ) {
                    // User password didn't change; ignore it.
                    add_filter('send_password_change_email', '__return_false');
                    add_filter( 'send_email_change_email', '__return_false' );
                    $user_id = wp_update_user( array(
                        'ID' => $wp_user->ID,
                        'user_nicename' => $user_id_PSA,
                        'user_email' => $email_id_PSA,
                        'role' => dmcss_wp::SUBSCRIBER_ROLE,
                        'show_admin_bar_front' => 'false'
                    ));
                }
                else {
                    // User password changed; update it.
                   add_filter('send_password_change_email', '__return_false');
                   add_filter( 'send_email_change_email', '__return_false' );
                    $user_id = wp_update_user( array(
                        'ID' => $wp_user->ID,
                        'user_nicename' => $user_id_PSA,
                        'user_pass' => $password,
                        'user_email' => $email_id_PSA,
                        'role' => dmcss_wp::SUBSCRIBER_ROLE,
                        'show_admin_bar_front' => 'false'
                    ));
                }
            }
            elseif( preg_match( '/' . dmcss_wp::REGISTERED_USER_ROLE . '/i', $role ) ) {

                global $wpdb;
                $sql = "UPDATE {$wpdb->users} SET user_login = %s WHERE ID = %d";
                $sql = $wpdb->prepare($sql, $user_id_PSA , $wp_user->ID);
                $wpdb->query($sql);

                add_filter('send_password_change_email', '__return_false');
                add_filter( 'send_email_change_email', '__return_false' );
                $user_id = wp_update_user( array(
                    'ID' => $wp_user->ID,
                    'user_nicename' => $user_id_PSA,
                    'user_email' => $email_id_PSA,
                    'role' => dmcss_wp::SUBSCRIBER_ROLE,
                    'show_admin_bar_front' => 'false'
                ));
            }
            else {
                $user_id = $wp_user->ID;
            }
        }
        $this->update_wp_user_meta( $user_id, $ws_result );
        return new WP_User( $user_id );
    }
    /**
     * Add lock icon to the supplied title
     *
     * @param string $title
     * @return $title + lock icon
     */
    private function title_lock_icon( $title ) {
        //die("2099");
        // Use key configured in admin
        $keyURL = $this->options['key_icon_url'];
        if( empty( $keyURL ) ) {
            $keyURL = $this->default_key_location;
        }
        $key_html = '&nbsp;<span class="dmcss_key_icon"><img alt="(access required)" src="'.$keyURL.'" border=0/></span>';
        if( $this->options['key_justification'] ) {
            return '<span class="lockleft">'.$title.'</span>';
        }
        else {
            return $title.$key_html;
        }
    }

    /**
     * Update user meta after login
     *
     * @param int $user_id
     * @param array $result
     * @return void
     */

    private function update_wp_user_meta( $user_id, $result ) {
        $loginDuration = $this->options['login_duration'];
        if( !empty( $loginDuration ) ) {
                $cookieDuration = $loginDuration * 24 * 3600;
            }
            else {
                $cookieDuration = 5184000;
        }
        // $cookieDuration = 3600;
        $cookiePath = '/';
        $cookieDomain = $_SERVER['HTTP_HOST'];
        
        $dmcssPublications = array();

        // Update timestamps
        $login_time = get_user_meta( $user_id, 'dmcss_timestamp', true );
        do_action( 'dmcss_last_login', array( $user_id => $login_time ) );
        update_user_meta( $user_id, 'dmcss_timestamp', time() );
        update_user_meta( $user_id, 'dmcss_lastValidated', time() );

          if(!empty($result) && trim($result) !='0' ) {
                $xmlObject = new SimpleXMLElement($result,TRUE);
                $node = $xmlObject->children();
                if(!isset($node[0]->TOKEN) && !isset($node[0]->API))
                {
                    $node_cnt = count($node);
                    $sub_rec = $node->sub;   
                    $sub_status = $sub_rec->SUB_STATUS;
                    $userID = $sub_rec->SUB_DBASUB;
                  
               $array2 = json_decode(json_encode((array)$xmlObject), TRUE);
                $pub = $array2['sub']['SUB_PUB'];
 
                $dmcssPublications[ $pub ]['accountNumber'] = $array2['sub']['CUS_CUSTNUM'];
                $dmcssPublications[ $pub ]['token'] = $this->options['ws_url_token'];
                if ($array2['sub']['SUB_SERVICE'] !='' ) {
                  $dmcssPublications[ $pub ]['subrate'] = $array2['sub']['SUB_SERVICE'];
                }
                else {
                  $dmcssPublications[ $pub ]['subrate'] = '';
                }
                
                $dmcssPublications[ $pub ]['accountNumber'] = $array2['sub']['CUS_CUSTNUM'];
                $dmcssPublications[ $pub ]['token'] = $this->options['ws_url_token'];
                
                if ($array2['sub']['SUB_DBASUB'] !='' ) {
                  $dmcssPublications[ $pub ]['dmcss_userID'] = $array2['sub']['SUB_DBASUB'];
                }
                else {
                  $dmcssPublications[ $pub ]['dmcss_userID'] = '';
                }
              }
             } //Result 0 check if end here
            
             else {
                $pub = $this->options['publications'][0]['code'];
                $dmcssPublications[ $pub ]['accountNumber'] = '';
                $dmcssPublications[ $pub ]['token'] = $this->options['ws_url_token'];
                $dmcssPublications[ $pub ]['subrate'] = '';
                $dmcssPublications[ $pub ]['dmcss_userID'] = '';
             }

        update_user_meta( $user_id, 'dmcss_current_user', $dmcssPublications );
        // Set cookies for userID & user Publications
        $cookieExpires = time() + $cookieDuration;
        
        @setcookie( 'dmcss[current_user]', serialize($dmcssPublications), $cookieExpires, $cookiePath, $cookieDomain );
        $dmcssPublications[ 'application' ] = 'wordpress';
        $json = json_encode( $dmcssPublications );
        @setcookie( 'dmcss_current_user', $json, $cookieExpires, $cookiePath, $cookieDomain );
        @setcookie( 'dmcss_userID',$array2['sub']['CUS_CUSTNUM'], $cookieExpires, $cookiePath, $cookieDomain );
        @setcookie( 'dmcss_userName',$array2['sub']['CUS_USERID'], $cookieExpires, $cookiePath, $cookieDomain );
    }
    
    
    /**
     * Validate user's subscriptions and login duration < publication session limit
     *
     * @return boolean - login still valid
     */

    private function validate_subscription_via_wsdl( $duration = 0 ) {
                     global $current_user;
                     $pubcode = '';
                        foreach( $this->options['publications'] as $pub ){
                            if( 0 < strlen( $pubcode ) ) {
                                $pubcode .= ',';
                            }
                            $pubcode .= $pub['code'];
                        }
                        // Confirm we have at least 1 publication configured before calling web service
                        if( 1 < strlen( $pubcode ) ) {
                          return true;
                        }
        return false;
}

     /**
     * Determine if registered user's email is valid and not already in use
     *
     * @return array - $result formatted for WP signup
     */
    private function validate_username( $user_name ) {
        global $wpdb;
        $errors = new WP_Error();

        $orig_username = $user_name;
        $user_name = sanitize_email( $user_name );
        if ( empty( $user_name ) ) {
            $errors->add( 'user_name', __( 'Please enter a username.' ) );
        }

        if ( is_email_address_unsafe( $user_name ) ) {
            $errors->add( 'user_email',  __( 'You cannot use that email address to signup. We are having problems with them blocking some of our email. Please use another email provider.' ) );
        }

        if ( !is_email( $user_name ) ) {
            $errors->add('user_email', __( 'Please enter a valid email address.' ) );
        }

        $limited_email_domains = get_site_option( 'limited_email_domains' );
        if ( is_array( $limited_email_domains ) && false == empty( $limited_email_domains ) ) {
            $emaildomain = substr( $user_name, 1 + strpos( $user_name, '@' ) );
            if ( false == in_array( $emaildomain, $limited_email_domains ) ) {
                $errors->add( 'user_email', __( 'Sorry, that email address is not allowed!' ) );
            }
        }

        // Check if the username or email has been used already.
        if ( username_exists( $user_name || email_exists( $user_name ) ) ) {
            $errors->add( 'user_email', __( 'Sorry, that email address is already used!' ) );
        }

        // Has someone already signed up for this username?
        $signup = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->signups WHERE user_email = %s", $user_name ) );
        if ( null != $signup ) {
            $registered_at =  mysql2date( 'U', $signup->registered );
            $now = current_time( 'timestamp', true );
            $diff = $now - $registered_at;
            // If registered more than two days ago, cancel registration and let this signup go through.
            if ( $diff > 2 * DAY_IN_SECONDS ) {
                $wpdb->delete( $wpdb->signups, array( 'user_login' => $user_name ) );
            }
            else {
                $errors->add( 'user_email', __( 'That email address has already been used. Please check your inbox for an activation email. It will become available in a couple of days if you do nothing.' ) );
            }
        }
        $result = array( 'user_name' => $user_name, 'orig_username' => $orig_username, 'user_email' => $user_name, 'errors' => $errors );

        return apply_filters( 'wpmu_validate_user_signup', $result );
    }

    /**
     * Validate wordpress user against publication(s) defined in the admin
     *
     * @param string $username
     * @param string $password
     * @param string $pubcode
     * @return array - Web Service response
    **/
     private function validate_wpuser_via_wsdl( $username,$password,$pubcode) {
            $customer_number = "";
            $zip = "";  
            $postFields = "token=".$this->options['ws_url_token']."&email=".trim($username)."&password=".trim($password)."&customer_number=".trim($customer_number)."&zip=".trim($zip)."&PUB_CODE=".trim($pubcode);
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $url = $this->options['ws_url'];
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
            $result = curl_exec($curl);
        return $result;
    }
    
    
    private function jobtrac_user_registration( $external_user_ID,$external_password,$user_email,$transaction_first_name,$transaction_last_name,$companyname) {
            $rowguid = '';
            $isdolan = 0;  
            $postFields = "username=".trim($external_user_ID)."&password=".trim($external_password)."&email=".trim($user_email)."&firstname=".trim($transaction_first_name)."&lastname=".trim($transaction_last_name)."&rowguid=".trim($rowguid)."&isdolan=".trim($isdolan)."&companyname=".trim($companyname);
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $url = "http://jobtrac.dailyreporter.com/psaData.cfm";
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
            $result = curl_exec($curl);
    }
    
    
    private function projectcenter_user_registration( $external_user_ID,$external_password,$user_email,$transaction_first_name,$transaction_last_name,$companyname) {
            $rowguid = '';
            $isdolan = 1;  
            $postFields = "username=".trim($external_user_ID)."&password=".trim($external_password)."&email=".trim($user_email)."&firstname=".trim($transaction_first_name)."&lastname=".trim($transaction_last_name)."&rowguid=".trim($rowguid)."&isdolan=".trim($isdolan)."&companyname=".trim($companyname);
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $url = "http://jobtrac.dailyreporter.com/psaData.cfm";
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
            $result = curl_exec($curl);
    }

    /******** END PRIVATE FUNCTIONS ***/

}

    function iframeshortcode( $atts ) {
      $atts = shortcode_atts(
         array(
            'src' => '',
            'pub' => '',
            'token' => '',
            'channel' => '',
            'tracking_code' => '',
            'return_url' => '',
            'width' => '100%',
            'height' => '',
        ), $atts, 'iframesc' );

        if(!empty($atts['pub'])) {

            $tracking_code_url =isset($_GET['promocode']) ? trim($_GET['promocode']) : '';
            if( $tracking_code_url != '' ) {
              $iframe_html = '<iframe src="'.$atts['src'].'?PUB_CODE='.$atts['pub'].'&amp;token='.$atts['token'].'&amp;channel='.$atts['channel'].'&amp;tracking_code='.$atts['tracking_code'].'&amp;tracking_code='.$tracking_code_url.'&amp;return_url='.$atts['return_url'].'" width='.$atts['width'].' height='.$atts['height'].' frameborder="0" scrolling="no"></iframe>';
            } 
            else {
              $iframe_html = '<iframe src="'.$atts['src'].'?PUB_CODE='.$atts['pub'].'&amp;token='.$atts['token'].'&amp;channel='.$atts['channel'].'&amp;tracking_code='.$atts['tracking_code'].'&amp;return_url='.$atts['return_url'].'" width='.$atts['width'].' height='.$atts['height'].' frameborder="0" scrolling="no"></iframe>'; 
            }       
        }

        else {
          
            $iframe_html = '<iframe class="else" src="'.$atts['src'].'?return_url='.$atts['return_url'].'" width='.$atts['width'].' height='.$atts['height'].' align="middle"></iframe>';
        }

        return $iframe_html;
       // return "ssssss";
    }
    add_shortcode( 'iframesc', 'iframeshortcode' );
    
    
    function reniframeshortcode( $atts ) {
      $atts = shortcode_atts(
         array(
            'src' => '',
            'domain_name' => '',
            'width' => '100%',
            'height' => '',
        ), $atts, 'reniframe' );

            $orddba = trim($_GET['orddba']);
            if( $orddba != '' ) {
              $iframe_html = '<iframe src="'.$atts['src'].'?orddba='.$orddba.'&amp;domain_name='.$atts['domain_name'].'" width='.$atts['width'].' height='.$atts['height'].' frameborder="0" scrolling="no"></iframe>';
            } 

        return $iframe_html;
    }
    add_shortcode( 'reniframe', 'reniframeshortcode' );