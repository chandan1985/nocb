<?php

namespace wpsolr\pro\extensions\toolset_views\filter;

use wpsolr\core\classes\WpSolrSchema;

class WPSOLR_Toolset_Views_Filter_Post_Status extends WPSOLR_Toolset_Views_Filter_Builder {


	/**
	 * @inheritdoc
	 */
	static protected function _add_filters() {

		if ( ! empty( $statuses = self::get_wpa_setting( 'post_status' ) ) ) {

			if ( ! empty( $statuses ) && ( false === array_search( 'any', $statuses ) ) ) {

				self::$search_engine_client->search_engine_client_add_filter_in_terms( 'Toolset views post status',
					WpSolrSchema::_FIELD_NAME_STATUS_S,
					$statuses,
					'WPSOLR_Toolset_Views_Filter_Post_Status'
				);
			}

		}

	}

}
