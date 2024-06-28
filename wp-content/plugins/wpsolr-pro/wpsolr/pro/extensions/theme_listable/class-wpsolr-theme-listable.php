<?php

namespace wpsolr\pro\extensions\theme_listable;

use wpsolr\core\classes\engines\WPSOLR_AbstractSearchClient;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\ui\WPSOLR_Query;
use wpsolr\pro\extensions\theme_listify\WPSOLR_Job_Manager_Abstract;

/**
 * Class WPSOLR_Theme_Listable
 *
 * Manage Listable theme
 */
class WPSOLR_Theme_Listable extends WPSOLR_Job_Manager_Abstract {

	/**
	 * @inheritdoc
	 */
	function get_option_is_replace_search() {
		return WPSOLR_Service_Container::getOption()->get_theme_listable_is_replace_search();
	}

	/**
	 * @inheritdoc
	 */
	function get_option_is_caching() {
		return WPSOLR_Service_Container::getOption()->get_theme_listable_is_caching();
	}


	/**
	 * @inheritdoc
	 */
	protected function get_default_custom_fields() {

		return array_merge( $this->get_geolocation_custom_fields(), $this->get_job_custom_fields() );
	}

	/**
	 * @inheritdoc
	 */

	function init_search_events() {
		// Nothing
	}

	/**
	 * @inheritDoc
	 */
	protected function add_sort( WPSOLR_Query $wpsolr_query ) {
		// Nothing.
	}

	/**
	 * @inheritDoc
	 *
	 */
	function add_custom_filters( WPSOLR_AbstractSearchClient $search_engine_client ) {
		// Nothing.
	}
}