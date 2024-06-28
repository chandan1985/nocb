<?php
/*
Plugin Name: DMC Dynamic Redirect
Version: 1.0
Plugin URI: http://www.dolanmedia.com/
Author: Dave Long
Author URI: http://www.dolanmedia.com
Description: Redirect pages based on source URL and DMCSS cookie status;
replaces DJC / FNC custom page template.

Modifications: 
11/19/2010 Dave Long - Fixed error message when no redirect configured in the admin
02/21/2012 Dave Long - Added IT-only lockout to admin panel
05/23/2012 Dave Long - Modified to use dmcss['current_user'] cookie
*/

// Business rule: Access to admin panel limited to dolanmedia & thedolancompany email domains

// Required for wp_redirect function
require_once(ABSPATH . WPINC.'/pluggable.php');

if (!defined( 'ABSPATH' ))
    die( 'Direct access not allowed.' );

$dmc_dynamic_redirect = new dmc_dynamic_redirect();

/** Begin dmc_dynamic_redirect
 *  class definition.
 */
class dmc_dynamic_redirect {

    function __construct()
    {
        if(is_admin()){
            add_action('admin_menu', array($this, 'dmc_dynamic_redirect_admin_menuitem'));
        }else{
            add_action('widgets_init', array($this, 'check_for_redirect' ), 3);
        }
    }

    // Add option page to WP admin panel if current user has a dolanmedia or thedolancompany email
    function dmc_dynamic_redirect_admin_menuitem()
    {
        global $current_user;
        preg_match("/.*\@(.*)\.com/", $current_user->user_email, $matches);
        if(isset($matches[1]) && ($matches[1] == 'thedolancompany' || $matches[1] == 'dolanmedia')) {
            if (function_exists('add_options_page')) {
                add_options_page('Dynamic Redirect', 'Dynamic Redirect', 'manage_options', basename(__FILE__), array(&$this, 'dmc_dynamic_redirect_adminsection'));
            }
        }
    }

    /** this function represents the admin setup page for this
     *  plugin.
     */
    function dmc_dynamic_redirect_adminsection()
    {
        if(isset($_POST['dmc_dynamic_redirect'])){
            $settings = array();
            if(is_array($_POST['dmc_dynamic_redirect'])){

                // Check for values to save / delete
                foreach($_POST['dmc_dynamic_redirect'] as $post){
                    if(!$post['delete'] && strlen($post['page'])){

                        $temp = array_keys($post);
                        array_shift($temp);

                        // Check local redirect URLs for missing forward slash and correct
                        foreach($temp as $item){
                            if(stripos($post[$item], '/') === false && $item != 'incoming'){
                                $post[$item] = "/".$post[$item];
                            }
                        }
                        $settings[] = $post;
                    }
                }

                if(get_option('dmc_dynamic_redirect') !== false){
                    update_option('dmc_dynamic_redirect', $settings);
                }
                else{
                    add_option('dmc_dynamic_redirect', $settings);
                }
                echo('<div style="margin-left:10px; padding-left: 10px; border:solid; border-width:1px; background-color:#E5E5E5; max-width:650px">
				<p style="font-weight:bold">Options saved.</p></div>');
            }
        }

        include dirname( __FILE__ ) . '/admin-view.php';
    }

    /*	This function redirects to a provided URL
    *	If the URL is not set, no redirect occurs
    */
    function redirect_page($destination)
    {
        // If destination not set, abort the redirect
        if(!$destination){
            return;
        }
        else{
            //ob_start();
            wp_redirect($destination);
            //ob_end_flush();
            exit;
        }
    }

    /** this function checks the current url, HTTP referer,
     * 	and DMCSS cookie then decides whether / where to redirect.
     */
    function check_for_redirect()
    {
        $options = get_option('dmc_dynamic_redirect');
        //Strip URL vars to ensure proper matching
        $pattern = '/(\/\?)(.)+/';
        $current = preg_replace($pattern, '', trim($_SERVER["REQUEST_URI"],'/'));

        // Check saved options & redirect to first matching page
        if($options !== false){
            foreach($options as $option){

                // if it is a regex with |, treat it differently.
                if (strpos($option['page'], '|') !== FALSE) {
                    // eventually need to handle the logged in and not logged in fields too, but for now, just default is enough
                    $default = preg_replace($option['page'], $option['default_url'], $current);
                    // if the regex changed the value, the pattern matched, so redirect now
                    if ($default !== $current) {
                        $this->redirect_page($default);
                    }
                    // regular page redirect
                } else {

                    // Validate default and reset to home if needed
                    if(strlen($option['default_url']) == 0)
                        $default = get_bloginfo(url);
                    else
                        $default = $option['default_url'];
                    $page = trim($option['page'],'/');
                    $loggedin = trim($option['loggedin'],'/');
                    $loggedout = trim($option['loggedout'],'/');

                    // Check REQUEST_URI for a redirected page
                    if(strlen($option['page']) > 0 && $current == $page){
                        // We are on a redirected page; check login status for next step
                        if(!isset($_COOKIE['dmcss']['current_user'])){
                            // We are not logged in; check HTTP referer and proceed
                            $source = $_SERVER['HTTP_REFERER'];
                            if($source && strlen($option['incoming']) > 0){
                                // Able to determine HTTP Referer && incoming is valid
                                if(stripos($source,$option['incoming']) > 0){
                                    // HTTP Referer matches incoming; redirect to logged out or fall through if logged out matches current page
                                    if($loggedout == $page){
                                        return;
                                    }
                                    $this->redirect_page($option['loggedout']);
                                }
                                // Coming in from page other than HTTP Referer; redirect to default or fall through
                                else{
                                    // Fall through if default is the page itself
                                    if(trim($default,'/') == $page)
                                        return;
                                    // Otherwise redirect to default
                                    $this->redirect_page($default);
                                }
                            }
                            // HTTP Referer invalid or incoming not set, redirect to default or fall through
                            else{
                                // Fall through if default is the page itself
                                if(trim($default,'/') == $page)
                                    return;
                                // Otherwise redirect to default
                                $this->redirect_page($default);
                            }
                        }else{
                            // Redirect to logged in page if set; otherwise do nothing
                            if(strlen($option['loggedin']) > 0)
                                // Fall through to page if circular redirect
                                if($loggedin == $page){
                                    return;
                                }
                            $this->redirect_page($option['loggedin']);
                        }
                    }
                } // regex vs normal
            } // foreach
        }
        // If no redirect found, do nothing
        return;
    }
}
?>