<?php
/*
Plugin Name:  Legendary Core for Wordpress
Plugin URI:   https://www.legendarydata.com/docs
Description:  Legendary Core plugin for Wordpress
Version:      1.00.009
Author:       support@legendarydata.com
Author URI:   https://www.legendarydata.com
License:      LDL
License URI:  https://www.legendarydata.com/#/lisc
*/

defined('ABSPATH') or die('Approved use only!');

define( "LDL_CORE_VERSION", "1.00.009" );

/* 20210204 tdf add abstraction functions to allow customization of user model via secondary plugin */

function ldl_get_current_user() {
	if ( function_exists( 'ldl_custom_get_current_user' ) ) {
		return ldl_custom_get_current_user();
	}
	return wp_get_current_user();
}

function ldl_get_user_meta( $userId, $metaKey, $single ) {
	if ( function_exists( 'ldl_custom_get_user_meta' ) ) {
		return ldl_custom_get_user_meta( $userId, $metaKey, $single );
	}
	return get_user_meta( $userId, $metaKey, $single );
}

/* Set Version Number here following a Major.Minor.Patch 'pattern' */
/** DEH ; more general to PLUGIN_VERSION and use .NNN for Patch to allow for many rolls */

function activate_legendary_core() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-legendary-core-activator.php';
	Legendary_Core_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-legendary-core-deactivator.php
 */
function deactivate_legendary_core() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-legendary-core-deactivator.php';
	Legendary_Core_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_legendary_core' );
//register_deactivation_hook( __FILE__, 'deactivate_legendary_core' );

/**
 * The core plugin class that is used to define ~?(internationalization),
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-legendary-core.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 */
function run_legendary_core() {
	$plugin = new Legendary_Core();
	$plugin->run();

}
run_legendary_core();
