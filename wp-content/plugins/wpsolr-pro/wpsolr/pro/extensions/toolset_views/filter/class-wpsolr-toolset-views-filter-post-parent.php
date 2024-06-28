<?php

namespace wpsolr\pro\extensions\toolset_views\filter;

use wpsolr\core\classes\WpSolrSchema;

class WPSOLR_Toolset_Views_Filter_Post_Parent extends WPSOLR_Toolset_Views_Filter_Builder {


	/**
	 * @inheritdoc
	 */
	static protected function _add_filters() {

		if ( ! empty( self::get_wpa_setting( 'parent_mode' ) ) ) {

			if ( false !== array_search( 'no_parent', self::get_wpa_setting( 'parent_mode' ) ) ) {
				// Posts with no parent

				self::$search_engine_client->search_engine_client_add_filter( 'toolset views no parent',
					self::$search_engine_client->search_engine_client_create_filter_no_values( WpSolrSchema::_FIELD_NAME_POST_PARENT_I )
				);

			} else if ( false !== array_search( 'this_page', self::get_wpa_setting( 'parent_mode' ) ) ) {
				// Posts with parent post id

				self::$search_engine_client->search_engine_client_add_filter_term( 'Toolset views parent id',
					WpSolrSchema::_FIELD_NAME_POST_PARENT_I,
					false,
					self::get_wpa_setting( 'parent_id' ) );

			} else if ( false !== array_search( 'url_parameter', self::get_wpa_setting( 'parent_mode' ) ) ) {
				// Posts with parent post id

				parse_str( $_SERVER['QUERY_STRING'], $url_parameters );

				if ( ! empty( $url_parameters[ self::get_wpa_setting( 'parent_url_parameter' ) ] ) ) {

					self::$search_engine_client->search_engine_client_add_filter_term( 'Toolset views parent id',
						WpSolrSchema::_FIELD_NAME_POST_PARENT_I,
						false,
						$url_parameters[ self::get_wpa_setting( 'parent_url_parameter' ) ] );
				}

			}

		}

	}

}
