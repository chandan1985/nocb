<?php

namespace wpsolr\pro\extensions\wp_rocket;

use wpsolr\core\classes\extensions\WPSOLR_Extension;
use wpsolr\core\classes\services\WPSOLR_Service_Container;


/**
 * Class WPSOLR_Plugin_WP_Rocket
 * @package wpsolr\pro\extensions\wp_rocket
 *
 * Manage WP_Rocket
 * @link https://wordpress.org/plugins/wp_rocket/
 */
class WPSOLR_Plugin_WP_Rocket extends WPSOLR_Extension {


	/**
	 * Constructor
	 * Subscribe to actions
	 **/
	function __construct() {

		if ( WPSOLR_Service_Container::getOption()->get_wp_rocket_is_defer_js() ) {
			// This extension must only execute in admin, and only when authorized.

			add_filter( 'rocket_defer_inline_exclusions', [
				$this,
				'rocket_defer_inline_exclusions',
			], 10, 1 );
		}

	}


	/**
	 * Prevent undefined variable JS error on wp_localize_script_autocomplete when WP Rocket defers JS
	 *
	 * @param string[] $exclusions
	 *
	 * @return mixed|string
	 */
	public function rocket_defer_inline_exclusions( $exclusions ) {

		return [ 'wp_localize_script_autocomplete' ];
	}

}