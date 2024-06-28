<?php
/*
Plugin Name: DMC Comment Moderator
Plugin URI: 
Description: Adds an option to the WP options table called commentmod_email which holds an email address. When you activation the second plugin 'DMC Comment Notification' all the moderation comment notifications are sent to this email address instead of the admin email address.
Author: Pallavi Unkule
Version: 1.0
Author URI: http://www.thedolancompany.com
*/

function cmod_menu()
{
    include 'dmc-comment-moderator-action.php';
    include 'overridenotifyfunction.php';
}

function cmod_action()
{
    add_submenu_page("options-general.php","Change Comment Moderator Email Address", "DMC Moderator Email", 'administrator',"dmc-comment-moderator-admin.php", "cmod_menu");
}

add_action('admin_menu', 'cmod_action');

?>