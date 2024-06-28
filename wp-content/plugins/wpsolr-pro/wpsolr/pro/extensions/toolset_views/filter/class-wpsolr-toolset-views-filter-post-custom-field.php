<?php

namespace wpsolr\pro\extensions\toolset_views\filter;

use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\WpSolrSchema;

class WPSOLR_Toolset_Views_Filter_Post_Custom_Field extends WPSOLR_Toolset_Views_Filter_Post_Date {


	/**
	 *
	 * @inheritdoc
	 */
	static protected function _add_filters() {
		global $wp_query;

		if ( ! empty( self::get_wpa_setting( 'custom_fields_relationship' ) )
		     && ! empty( $meta_query = self::$wp_query->get( 'meta_query' ) ) ) {

			$custom_fields = WPSOLR_Service_Container::getOption()->get_option_index_custom_fields( true );

			$inner_filters = [];
			foreach ( $meta_query as $position => $meta_query_values ) {

				if ( is_array( $meta_query_values ) ) {

					$field_name  = $meta_query_values['key'];
					$field_value = $meta_query_values['value'];
					$compare     = $meta_query_values['compare'];

					// Work only on custom fields indexed in screen 2.2
					$array_pos = array_search( $field_name . WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING, $custom_fields );
					if ( false !== $array_pos ) {

						if ( WpSolrSchema::get_custom_field_is_date_type( $custom_fields[ $array_pos ] ) ) {
							// $field_value is an epoch date (integer). Use the field_int rather than the field_dt.

							$field_name_with_extension = WpSolrSchema::replace_field_name_extension( $custom_fields[ $array_pos ] ) . WpSolrSchema::_SOLR_DYNAMIC_TYPE_INTEGER;

						} else {

							$field_name_with_extension = WpSolrSchema::replace_field_name_extension( $custom_fields[ $array_pos ] );
						}

						self::create_compare_filter( $inner_filters, $compare, $field_name_with_extension, $field_value );
					}

				}

			}

			if ( ! empty( $inner_filters ) ) {
				// And between the custom fields

				$outer_filters = self::$search_engine_client->search_engine_client_create_and( $inner_filters );

				self::$search_engine_client->search_engine_client_add_filter( 'Toolset Views custom fields filters', $outer_filters );
			}
		}

	}

}
