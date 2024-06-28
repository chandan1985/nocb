<?php
/*
 * Display Hostname module for TDC WordPress
 *
 * Include file must be placed in the root of the mu-plugins directory to enable this
 * plugin.
 */

if ( !defined( 'ABSPATH' ) )
	die( 'Direct access not allowed.' );

class tdc_display_hostname {

	/*
	 * Class constructor
	 * Build class & add action for server/template info.
	 */
	function __construct(){
		add_action( 'admin_bar_menu', array( &$this, 'addServerInfo' ), 1000 );
	}

	/*
	 * Add server & template info to the admin bar
	 */
	function addServerInfo() {
		global $wp_admin_bar, $current_user, $template;

		// Only show to super admins if admin bar is active
		if ( isset( $current_user->ID ) && function_exists( 'is_admin_bar_showing' ) && is_admin_bar_showing() ) {
			$myuser = get_userdata( $current_user->ID );
			if( in_array( 'administrator', $myuser->roles ) || in_array( 'TDC_Support', $myuser->roles ) || is_super_admin() ){
				if( is_admin() ){
					$page_temp = $_SERVER['SCRIPT_NAME'];
					if(! empty( $_SERVER['QUERY_STRING'] ) )
						$page_temp .= '?'.$_SERVER['QUERY_STRING'];
				}
				else
					$page_temp = $template;

				$server_info = 'Current server: '.gethostbyaddr( '127.0.0.1' );
				$template_info = 'Current template: '.$page_temp;

				/* Add the main siteadmin menu item */
				$wp_admin_bar->add_menu( array( 'id' => 'nocb_templ_server_info', 'title' => __( 'Template & Server Info', 'textdomain' ), 'href' => FALSE ) );
				$wp_admin_bar->add_menu( array( 'id' => 'nocb_curr_server', 'parent' => 'template_info', 'title' => $server_info, 'href' => FALSE ) );
				$wp_admin_bar->add_menu( array( 'id' => 'nocb_curr_tpl', 'parent' => 'template_info', 'title' => $template_info, 'href' => FALSE ) );
			}
		}
	}
}
?>