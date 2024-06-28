<?php

namespace wpsolr\pro\extensions\toolset_views\filter;

use wpsolr\core\classes\WpSolrSchema;

class WPSOLR_Toolset_Views_Filter_Post_Sticky extends WPSOLR_Toolset_Views_Filter_Builder {


	/**
	 * @inheritdoc
	 */
	static protected function _add_filters() {

		if ( ! empty( self::get_wpa_setting( 'post_sticky' ) ) && get_option( 'sticky_posts' ) ) {

			if ( 'include' === self::get_wpa_setting( 'post_sticky' ) ) {

				self::$search_engine_client->search_engine_client_add_filter_in_terms( 'Toolset views post sticky',
					WpSolrSchema::_FIELD_NAME_PID,
					get_option( 'sticky_posts' )
				);

			} else {

				self::$search_engine_client->search_engine_client_add_filter_not_in_terms( 'Toolset views post not sticky',
					WpSolrSchema::_FIELD_NAME_PID,
					get_option( 'sticky_posts' )
				);
			}


		}

	}

}
