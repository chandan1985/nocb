<?php

/*
  Plugin Name: Asentech - Interstitial Welcome Page Promotion
  Plugin URI: http://asentechllc.com/
  Description: Allows you to show Interstitial Ads on any WordPress site as per new google standards
  Version: 1.2.1
  Author: asentech
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
if(!class_exists('InterstitialPromotion')) {
    
    class InterstitialPromotion {

        public function __construct($file) {
            $this->token = 'hemant';
            $this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
            require_once( 'classes/interstitial-screen-admin.php' );
            //Add style
            add_action('template_redirect', array(&$this, 'cookie_redirect'));
        }

        /**
         * Register frontend CSS and JS files.
         * @since  1.0
         * @return void
         */
        public function enqueue_styles() {
            wp_register_script('some_handle', esc_url($this->plugin_url . 'js/interpromotion.js'));
            wp_enqueue_style('style', esc_url($this->plugin_url . 'css/interpromotion.css'));
            wp_enqueue_script('scripts1', esc_url($this->plugin_url . 'js/jquery.countdown.js'), array('jquery'), '1.4.0', false);
            $retUrl = (isset($_GET['retUrl']) && $_GET['retUrl'] != "/welcome-ad/") ? $_GET['retUrl'] : "/";
            $JSinterAds = array("is_wait" => "5", "is_count" => "15", "seconds" => " sec", "id_post" => "2", "returl" => $retUrl, "is_cached" => "0");
            wp_localize_script('some_handle', 'interAds', $JSinterAds);
            wp_enqueue_script('some_handle');
        }

    // End enqueue_styles()
        function cookie_redirect() {
            global $wp_query,$post;
            // THE PAGE SLUG, YOU WANT TO BE REDIRECTED TO, WHEN THERE IS NO COOKIE
            $pageslug = "welcome-ad";
            // THE COOKIE NAME
            $cookie_name = "theInterstitial";
            $pagename = $this->wpse8170_loop();
            $options = get_option('interstitial-ad');
            $options_exclusions = get_option('interstitial-ad-exclusion');
        /*if (empty($options) || wp_is_mobile()) {
                    return;
            }*/
            if (empty($options)) {
                    return;
            }
            
            /* saquib comment
            $post = get_queried_object();
            $postType = get_post_type_object(get_post_type($post));


            if ($postType) {
                $current_post_type =  strtolower(esc_html($postType->labels->singular_name));
            }

            if($current_post_type == 'page') {
                $arr = $options_exclusions['post_type'];
            
                if ( in_array($current_post_type, $arr) && $post->post_name == 'welcome-ad' ) {
                    return;
                }  
            }
            */

            if(is_object($post) && $post->ID)
            {
                        
                //if(is_single())
                //{ 
                /*saquib comment
                $arr = $options_exclusions['post_type'];
                $slug = $postType->name;

                if ( in_array($slug, $arr) ) {
                return;
                } 
                */
                
                    
                    global $wp;
                    $current_url = home_url(add_query_arg(array(), $wp->request));
                    // $url = get_permalink( $current_url );
                    $url = trailingslashit( home_url( $wp->request ) );
                    $wp_rejected_uri = $options_exclusions['wp_rejected_uri'];
                    
            
                    if(!empty($wp_rejected_uri))
                    $wp_rejected_uriArr =	preg_split('/\r\n|[\r\n]/', $wp_rejected_uri);
                    
                    if (is_array($wp_rejected_uriArr) || is_object($wp_rejected_uriArr)) {
                    foreach ( $wp_rejected_uriArr as $expr ) {
                        if( $expr != '' && @preg_match( "~$expr~", $url ) )
                            return ;
                    }
                    }
                    
                //}
            }

            
            
            
            $option_arr = isset($options_exclusions['page_type']) ? $options_exclusions['page_type'] : '';
            if(!empty($option_arr)) {
                if ( ( is_search() && in_array('search', $option_arr) ) || (is_page() && in_array('pages', $option_arr) && ($post->post_name != 'welcome-ad') ) || (is_front_page() && in_array('home', $option_arr)  )  || (is_home() && in_array('home', $option_arr) ) || (is_author() && in_array('author', $option_arr) )  || ( is_feed()) && in_array('feed', $option_arr) || ( is_archive()) && in_array('archives', $option_arr)  )
                {
                    return;
                } 
            }  
            

            // if ( ( is_search() && $options_exclusions['search']) || (is_page() && $options_exclusions['pages'] && ($pageslug != $pagename) )  || (is_tag() && $options_exclusions['tag'] )  || (is_single() && $options_exclusions['single'] )  || (is_category() && $options_exclusions['category'] )  || (is_front_page() && $options_exclusions['frontpage'] && ($pageslug != $pagename)  )  || (is_home() && $options_exclusions['home'] && ($pageslug != $pagename)  ) || (is_author() && $options_exclusions['author'] )  || ( is_feed()) && $options_exclusions['feed'] || ( is_archive()) && $options_exclusions['archives']  )
            // {
            //     return;
            // } 
                
            
            if (
                    (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'ColdFusion') !== false) ||
                    (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Twitterbot') !== false) ||
                    (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'facebook') !== false) ||
                    (isset($_SERVER['HTTP_ACCEPT']) && $_SERVER['HTTP_ACCEPT'] === '*/*') ||
                    (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'projectcenter.djcoregon.com') !== false) ||
                    (strpos($_SERVER['HTTP_USER_AGENT'], 'developers.google.com') !== false) ||
                    (strpos($_SERVER['HTTP_USER_AGENT'], 'Googlebot') !== false) ||
                    (isset($_REQUEST['dmcss']) && $_REQUEST['dmcss'] == 'login')
            ) {
                
                return;
            }
            if (is_admin()) {
                return;
            }
            if (!$options['enabled']) {
                return;
            }
            if (!empty($options['active_date']) && !empty($options['active_time'])) {
                $activate = strtotime(str_replace('-', '/', $options['active_date']));
                $activate = strtotime('00:00', $activate);
                ;
                $enddate = strtotime(str_replace('-', '/', $options['active_time']));
                $enddate = strtotime('23:59', $enddate);
                ;
                $current = current_time('timestamp');
                if ($current < $activate || $current > $enddate)
                    return;
            }
            if ($pagename == 'home') {
                $pagename = 'home';
            } elseif ($pagename == 'page') {
                $pagename = get_post_field('post_name');
            } elseif ($pagename == 'single') {
                $pagename = get_post_field('post_name');
            } else {
                $pagename = '';
            }
            
            if($wp_query->query['post_type']=='tribe_events' || $pagename=="post-event")
            {
                return;
            }
            
            $num_of_times = (isset($options['num_of_times']) && is_numeric($options['num_of_times']) && $options['num_of_times'] > 0) ? $options['num_of_times'] : 1;
            $display_count = (24 / $num_of_times);
            $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $this->my_header_scripts($cookie_name, $display_count, $actual_link);
            
            


            if ($pagename == $pageslug) {
                if ($pagename === 'welcome-ad') {
                    $this->enqueue_styles();
                    //$this->enqueue_scripts();
                    remove_filter('the_content', 'sharing_display', 19);
                    $load = dirname(__FILE__) . "/interstitial-promotion-template.php";
                    if ($load) {
                        load_template($load);
                        exit();
                    }
                }
                exit;
            }
            
        }

        function wpse8170_loop() {
            global $wp_query;
            $loop = 'notfound';
            if ($wp_query->is_page) {
                $loop = is_front_page() ? 'front' : 'page';
            } elseif ($wp_query->is_home) {
                $loop = 'home';
            } elseif ($wp_query->is_single) {
                $loop = ( $wp_query->is_attachment ) ? 'attachment' : 'single';
            } elseif ($wp_query->is_category) {
                $loop = 'category';
            } elseif ($wp_query->is_tag) {
                $loop = 'tag';
            } elseif ($wp_query->is_tax) {
                $loop = 'tax';
            } elseif ($wp_query->is_archive) {
                if ($wp_query->is_day) {
                    $loop = 'day';
                } elseif ($wp_query->is_month) {
                    $loop = 'month';
                } elseif ($wp_query->is_year) {
                    $loop = 'year';
                } elseif ($wp_query->is_author) {
                    $loop = 'author';
                } else {
                    $loop = 'archive';
                }
            } elseif ($wp_query->is_search) {
                $loop = 'search';
            } elseif ($wp_query->is_404) {
                $loop = 'notfound';
            } else {
                $loop = 'notfound';
            }
            return $loop;
        }

        function my_header_scripts($cookie_name, $displayTime, $actual_link) {
            $args = array(
                'cookie_name' => $cookie_name,
                'display_count' => $displayTime,
                'actual_link' => $actual_link,
            );
            add_action('wp_head', function() use ( $args ) {
                $cookie_name = $args['cookie_name'];
                $displayTime = $args['display_count']; // max posts
                $actual_link = $args['actual_link'];
                echo '<script type="text/javascript">
                //Next code is used for checking cookie and doing redirect if cookie not exist
    (function () {
        function getCookie(cname) {
            var name = cname + "=";
            var ca = document.cookie.split(";");
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == " ")
                    c = c.substring(1);
                if (c.indexOf(name) == 0)
                    return c.substring(name.length, c.length);
            }
            return "";
        }
        function setCookie(cname, cvalue, exdays) {
            var d = new Date();
            d.setTime(d.getTime()); // set cookie for half hour
            d.setTime(d.getTime() + (exdays * 1800 * 1000)); // set cookie for half hour
            var expires = "expires=" + d.toUTCString();
            document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/";
        }
        function urlParam(name){
            var results = new RegExp(\'[\?&]\' + name + \'=([^&#]*)\').exec(window.location.href);
            if (results==null){
            return null;
            }
            else{
            return decodeURI(results[1]) || 0;
            }
        }
        
        
        var popUp = getCookie("' . $cookie_name . '");
        var isMobile = navigator.userAgent.match(/(iPad)|(iPhone)|(iPod)|(android)|(iemobile)|(blackberry)|(webOS)/i);
        
            if (popUp == "") {
                popUp = popUp + 1;
                setCookie("' . $cookie_name . '" , 1, ' . $displayTime . ');
                //setcookie($cookie_name, 2, time() + $display_count, SITECOOKIEPATH, COOKIE_DOMAIN, false, true);
                
                
                

                //var urlParams = new URLSearchParams(window.location.search);
                var promocode = urlParam(\'promocode\');
                if(promocode)
                window.location.href = "/welcome-ad/?retUrl=" + location.pathname+"?promocode="+promocode;
                else
                window.location.href = "/welcome-ad/?retUrl=" + location.pathname;
            }
        
    }());
            </script>';
            }, 1);
        }

    }
}

$start = new InterstitialPromotion(__FILE__);
?>