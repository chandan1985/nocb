<?php

namespace wpsolr\pro\extensions\toolset_views\filter;

use wpsolr\core\classes\WpSolrSchema;

class WPSOLR_Toolset_Views_Filter_Post_Current extends WPSOLR_Toolset_Views_Filter_Builder {


	/**
	 * @inheritdoc
	 */
	static protected function _add_filters() {

		if ( ! is_archive() && ! empty( self::get_wpa_setting( 'post_type_dont_include_current_page' ) ) ) {

			// Exclude current page is not an archive
			$current_page_id = get_queried_object_id();
			self::$search_engine_client->search_engine_client_add_filter_not_in_terms( 'toolset views filter current page', WpSolrSchema::_FIELD_NAME_PID_I, [ $current_page_id ] );
		}

	}

}
