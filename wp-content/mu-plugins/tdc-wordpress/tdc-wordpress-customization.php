<?php
/*
 * TDC WordPress main plugin file
 *
 * Include file must be placed in the root of the mu-plugins directory to enable this
 * plugin.
 */

if ( !defined( 'ABSPATH' ) )
	die( 'Direct access not allowed.' );

class tdc_wordpress_customization {

	/*
	 * Class constructor
	 * Build class & set up actions & filters based on page type
	 */
	function __construct(){
		// Setup WordPress Actions for all pages
		// Setup WordPress Actions for admin pages
		if( is_admin() ){
			// Remove Jetpack menu for non-admins
			add_action( 'admin_init', array( &$this, 'remove_jetpack_menu' ), 100 );
			// Remove the updates menu item
			add_action( 'network_admin_menu', array( &$this, 'remove_update_menu' ) );
			// Remove stock WP dashboard widgets
			add_action( 'wp_dashboard_setup', array( &$this, 'remove_dashboard_widgets' ) );
			add_action( 'wp_network_dashboard_setup', array( &$this, 'remove_network_dashboard_widgets' ) );

			// Disable update checks
			/* add_filter( 'pre_site_transient_update_core', function(){
				return null;
			} );
			add_filter( 'pre_site_transient_update_plugins', function(){
				return null;
			} );
			add_filter( 'pre_site_transient_update_themes', function(){
				return null;
			} ); */

			// Remove update prompts
			remove_action( 'admin_init', '_maybe_update_core' );
			remove_action( 'wp_version_check', 'wp_version_check' );
			add_action( 'admin_bar_menu', array( &$this, 'remove_jarida_update_notification' ), 1500 );
			add_action( 'admin_menu', array( &$this, 'remove_jarida_update_menu' ), 20 );
		}
	}

	/******* WP ACTIONS ************/

	/*
	* Remove WordPress Development Blog & Other WordPress News widgets from blog-level dashboard
	*/
	function remove_dashboard_widgets() {
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
	}

	// Remove Jarida updates from Jarida menu
	function remove_jarida_update_menu() {
		remove_submenu_page( 'panel', 'theme-update-notifier' );
	}

	// Remove Jarida updates from admin bar
	function remove_jarida_update_notification() {
		global $wp_admin_bar;

		$wp_admin_bar->remove_node( 'update_notifier' );
	}

	/*
	* Remove WordPress Development Blog & Other WordPress News widgets from network dashboard
	*/
	function remove_network_dashboard_widgets() {
		remove_meta_box( 'dashboard_primary', 'dashboard-network', 'side' );
		remove_meta_box( 'dashboard_secondary', 'dashboard-network', 'side' );
	}

	/*
	 * Remove the Update menu from the network
	 */
	function remove_update_menu() {
		remove_menu_page( 'upgrade.php' );
		remove_menu_page( 'update-core.php' );
	}

	/*
	 * Hide Jetpack menu from non-admin users
	 */
	function remove_jetpack_menu() {
		global $current_user;

		if( isset( $current_user->ID ) ){
			$myuser = get_userdata( $current_user->ID );
            if (isset($myuser->roles) && is_array( $myuser->roles ) ) {
			    if( !( in_array( 'administrator', $myuser->roles ) || is_super_admin( $current_user->user_login ) ) )
				    remove_menu_page( 'jetpack' );
            }
		}
	}

	/******* END WP ACTIONS ********/
}
?>