<?php
/*
    Plugin Name:  Legendary Data - Search Plugin
    Plugin URI:   https://www.legendarydata.com/docs
    Description:  Search your LISTS, Businesses, and Contacts
    Version:      1.00.001
    Author:       Alvin Kreitman / Legendary Data
    Author URI:   https://www.legendarydata.com
    License:      LDL
    License URI:  https://www.legendarydata.com/#/lisc
 */


defined('ABSPATH') or die('Approved use only!');
define( "LDL_SEARCH_VERSION", "1.00.001" );


function ldl_search_activate() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-ldl-search-activator.php';
    
    LDL_search_activator::activate();
    
    flush_rewrite_rules();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ldl-search-deactivator.php
 */

function ldl_search_deactivate() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-ldl-search-deactivator.php';
    LDL_search_deactivator::deactivate();
}

register_activation_hook(__FILE__, 'ldl_search_activate');
register_deactivation_hook( __FILE__, 'ldl_search_deactivate' );

/**
 * The core search plugin class 
 */
require plugin_dir_path(__FILE__) . 'includes/class-ldl-search.php';

/**
 * ldl_search_verify_required() plugin requires Legendary Core.
 * Deactivate if  core resources are not installed.
 */

function ldl_search_verify_required() {

    $toolboxf = WP_PLUGIN_DIR . '/legendary-core/includes/class-toolbox.php';
    $apif = WP_PLUGIN_DIR . '/legendary-core/includes/class-legendary-api.php';
    
    $txt = 'Sorry. It appears the required Legendary Core plugin is not installed correctly.';
    
    // whis is this being required here?
    require_once ABSPATH . 'wp-admin/includes/plugin.php';

    if (!file_exists($toolboxf)) {
     
        $plugin = plugin_basename( __FILE__ );
        
        deactivate_plugins($plugin);
        
        error_log($txt);
        flush_rewrite_rules();
        
        return false;
    }

    if (!file_exists($apif)) {
        $plugin = plugin_basename( __FILE__ );
        
        deactivate_plugins($plugin);
        
        error_log($txt);
        flush_rewrite_rules();
        
        return false;
    }

    return true;
}


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 */
function run_ldl_search()
{
    //verify required Legendary Core
    if (ldl_search_verify_required()) {
        $plugin = new LDL_search();
        $plugin->run();
    }
}

run_ldl_search();
