<?php

namespace wpsolr\pro\extensions\toolset_views\filter;

use wpsolr\core\classes\WpSolrSchema;

class WPSOLR_Toolset_Views_Filter_Post_Author extends WPSOLR_Toolset_Views_Filter_Builder {


	/**
	 * @inheritdoc
	 */
	static protected function _add_filters() {

		if ( ! empty( self::get_wpa_setting( 'author_mode' ) ) && ! is_author() ) {

			// Current user
			if ( false !== array_search( 'current_user', self::get_wpa_setting( 'author_mode' ) ) ) {

				self::$search_engine_client->search_engine_client_add_filter_term( 'toolset views filter curent user', WpSolrSchema::_FIELD_NAME_AUTHOR_ID_S, false, get_current_user_id() );
			}

			// Specific user
			if ( false !== array_search( 'this_user', self::get_wpa_setting( 'author_mode' ) ) ) {

				self::$search_engine_client->search_engine_client_add_filter_term( 'toolset views filter curent user', WpSolrSchema::_FIELD_NAME_AUTHOR_ID_S, false, self::get_wpa_setting( 'author_id' ) );
			}

			// Specific user parameters
			if ( false !== array_search( 'by_url', self::get_wpa_setting( 'author_mode' ) ) ) {

				$author_url_parameter = self::get_wpa_setting( 'author_url' );
				$author_url_type      = self::get_wpa_setting( 'author_url_type' );

				parse_str( $_SERVER['QUERY_STRING'], $url_parameters );

				if ( ! empty( $url_parameters[ $author_url_parameter ] ) ) {

					$field_name = ( "id" === $author_url_type ) ? WpSolrSchema::_FIELD_NAME_AUTHOR_ID_S : WpSolrSchema::_FIELD_NAME_AUTHOR;

					self::$search_engine_client->search_engine_client_add_filter_term( 'toolset views filter user in parameter', $field_name, false, $url_parameters[ $author_url_parameter ] );
				}
			}

		}

	}

}
