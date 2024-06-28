<?php
/*
Plugin Name: TDC WordPress
Version: 0.1
Plugin URI: http://www.thedolancompany.com/
Author: Dave Long, Jerry Johnson
Author URI: http://www.thedolancompany.com
Description: Enable TDC WordPress customizations
*/


/*
 * TDC WordPress include file
 *
 * Place this file in the root of the mu-plugins directory to enable this
 * plugin.
 */
if(!defined('TDC_WORDPRESS_ENVIRONMENT')) {
	define('TDC_WORDPRESS_ENVIRONMENT', '');
}
@include_once( 'tdc-wordpress/tdc-color-admin-bar.php' );
@include_once( 'tdc-wordpress/tdc-display-hostname.php' );
@include_once( 'tdc-wordpress/tdc-user-management.php' );
@include_once( 'tdc-wordpress/tdc-wordpress-customization.php' );
@include_once( 'tdc-wordpress/tdc-dashboard-widgets.php' );
@include_once( 'tdc-wordpress/tdc-scb-path.php' );

// Initialize classes
global $tdc_color_admin_bar;
$tdc_color_admin_bar = new tdc_color_admin_bar();
global $tdc_display_hostname;
$tdc_display_hostname = new tdc_display_hostname();
global $tdc_user_management;
$tdc_user_management = new tdc_user_management();
global $tdc_wordpress_customization;
$tdc_wordpress_customization = new tdc_wordpress_customization();
global $tdc_dashboard_widgets;
$tdc_dashboard_widgets = new tdc_dashboard_widgets();

// Environment specific functionality
if( 'lab' == TDC_WORDPRESS_ENVIRONMENT ) {
	@include_once( 'tdc-wordpress/tdc-lab-warning.php' );
	global $tdc_lab_warning;
	$tdc_lab_warning = new tdc_lab_warning();
}