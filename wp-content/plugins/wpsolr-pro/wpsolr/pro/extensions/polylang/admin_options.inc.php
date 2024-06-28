<?php

use wpsolr\core\classes\extensions\licenses\OptionLicenses;
use wpsolr\core\classes\extensions\WPSOLR_Extension;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\utilities\WPSOLR_Help;
use wpsolr\core\classes\utilities\WPSOLR_Option;
use wpsolr\pro\extensions\polylang\WPSOLR_Plugin_Polylang;

/**
 * Included file to display admin options
 */

WPSOLR_Extension::require_once_wpsolr_extension( WPSOLR_Extension::EXTENSION_POLYLANG, true );
WPSOLR_Extension::require_once_wpsolr_extension( WPSOLR_Extension::OPTION_INDEXES, true );

$extension_options_name = WPSOLR_Option::OPTION_EXTENSION_POLYLANG;
$settings_fields_name   = 'solr_extension_polylang_options';

$extension_options = WPSOLR_Service_Container::getOption()->get_option_polylang();
$is_plugin_active  = WPSOLR_Extension::is_plugin_active( WPSOLR_Extension::EXTENSION_POLYLANG );

$plugin_name    = "Polylang";
$plugin_link    = "https://polylang.wordpress.com/documentation/";
$plugin_version = "(Tested with Polylang 2.2.1)";

if ( $is_plugin_active ) {
	$ml_plugin = WPSOLR_Plugin_Polylang::create();
}

$package_name = OptionLicenses::LICENSE_PACKAGE_POLYLANG;

$plugin_help_link = WPSOLR_Help::HELP_ADDON_POLYLANG
?>

<?php
include_once( WPSOLR_Extension::get_option_file( WPSOLR_Extension::EXTENSION_WPML, 'template.inc.php' ) );
