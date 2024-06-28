<?php
/*
 * Color Admin Bar module for TDC WordPress
 *
 * Include file must be placed in the root of the mu-plugins directory to enable this
 * plugin.
 */

if ( !defined( 'ABSPATH' ) )
	die( 'Direct access not allowed.' );

class tdc_color_admin_bar {

	/*
	 * Class constructor
	 * Build class & set up action to change admin bar color
	 */
	function __construct(){
		add_action( 'init', array( &$this, 'tdc_color_admin_bar_style' ) );
	}

	/*
	 * Set color of admin bar based on environment
	 */
	function tdc_color_admin_bar_style() {
		if ( function_exists( 'is_admin_bar_showing' ) && is_admin_bar_showing() ) {
			switch( TDC_WORDPRESS_ENVIRONMENT ) {
				case 'production':
					break;
				case 'staging':
					wp_enqueue_style( 'tdc-color-admin-bar', plugins_url( '/css/blue-admin-bar.css', __FILE__ ), array( 'admin-bar' ), '1.0' );
					break;
				case 'development':
					wp_enqueue_style( 'tdc-color-admin-bar', plugins_url( '/css/yellow-admin-bar.css', __FILE__ ), array( 'admin-bar' ), '1.0' );
					break;
				case 'lab':
					wp_enqueue_style( 'tdc-color-admin-bar', plugins_url( '/css/green-admin-bar.css', __FILE__ ), array( 'admin-bar' ), '1.0' );
					break;
				default:
					wp_enqueue_style( 'tdc-color-admin-bar', plugins_url( '/css/red-admin-bar.css', __FILE__ ), array( 'admin-bar' ), '1.0' );
			}
		}
	}
}
?>