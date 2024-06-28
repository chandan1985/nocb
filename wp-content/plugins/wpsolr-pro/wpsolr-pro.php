<?php
/**
 * Plugin Name: WPSOLR PRO
 * Description: Full integration with Weaviate AI search, Elasticsearch search, OpenSearch search, Algolia search, Google Retail search , Solr search.
 * Version: 23.2
 * Author: wpsolr
 * Plugin URI: https://www.wpsolr.com
 * License: GPL2
 */

use wpsolr\core\classes\extensions\indexes\WPSOLR_Option_Indexes;
use wpsolr\core\classes\extensions\suggestions\WPSOLR_Option_Suggestions;
use wpsolr\core\classes\extensions\WPSOLR_Extension;
use wpsolr\pro\WPSOLR_Pro_Updates;

define( 'WPSOLR_PLUGIN_VERSION', '23.2' );
define( 'WPSOLR_PLUGIN_SHORT_NAME', 'WPSOLR PRO' );
define( 'WPSOLR_SLUG', 'wpsolr-pro/wpsolr-pro.php' );

include_once dirname( __FILE__ ) . '/wpsolr.inc.php';

add_action( 'after_setup_theme', function () {
	new WPSOLR_Pro_Updates( WPSOLR_SLUG, __FILE__ );
} );

/**
 * Activation actions
 */
function wpsolr_register_activation_hook() {

	/*
	 * Migrate old data on plugin update
	 */
	WPSOLR_Extension::require_once_wpsolr_extension( WPSOLR_Extension::OPTION_INDEXES, true );
	$option_object = new WPSOLR_Option_Indexes();
	$option_object->migrate_data_from_v4_9();
	WPSOLR_Option_Suggestions::migrate_data_from_v21_4();
}

register_activation_hook( __FILE__, 'wpsolr_register_activation_hook' );
