<?php

namespace wpsolr\pro\extensions\toolset_views\filter;

use wpsolr\core\classes\WpSolrSchema;

class WPSOLR_Toolset_Views_Filter_Post_id extends WPSOLR_Toolset_Views_Filter_Builder {


	/**
	 * @inheritdoc
	 */
	static protected function _add_filters() {

		if ( ! empty( self::get_wpa_setting( 'id_mode' ) ) ) {

			// ids
			if ( false !== array_search( 'by_ids', self::get_wpa_setting( 'id_mode' ) )
			     && ! empty( $post_id_ids_list = self::get_wpa_setting( 'post_id_ids_list' ) )
			     && is_string( $post_id_ids_list )
			) {

				if ( 'in' === self::get_wpa_setting( 'id_in_or_out' ) ) {

					self::$search_engine_client->search_engine_client_add_filter_in_terms( 'toolset views filter post ids',
						WpSolrSchema::_FIELD_NAME_PID,
						explode( ',', $post_id_ids_list )
					);
				} else {

					self::$search_engine_client->search_engine_client_add_filter_not_in_terms( 'toolset views filter post ids',
						WpSolrSchema::_FIELD_NAME_PID,
						explode( ',', $post_id_ids_list )
					);
				}

			}

			// ids in url parameter
			if ( false !== array_search( 'by_url', self::get_wpa_setting( 'id_mode' ) ) ) {

				parse_str( $_SERVER['QUERY_STRING'], $url_parameters );

				if ( ! empty( $post_ids_url_values = $url_parameters[ self::get_wpa_setting( 'post_ids_url' ) ] )
				     && is_array( $post_ids_url_values )
				) {

					if ( 'in' === self::get_wpa_setting( 'id_in_or_out' ) ) {

						self::$search_engine_client->search_engine_client_add_filter_in_terms( 'toolset views filter url post ids',
							WpSolrSchema::_FIELD_NAME_PID,
							$post_ids_url_values
						);

					} else {

						self::$search_engine_client->search_engine_client_add_filter_not_in_terms( 'toolset views filter url post ids',
							WpSolrSchema::_FIELD_NAME_PID,
							$post_ids_url_values
						);
					}

				}


			}

		}

	}

}
