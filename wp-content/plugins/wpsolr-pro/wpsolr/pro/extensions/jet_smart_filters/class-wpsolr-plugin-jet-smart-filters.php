<?php

namespace wpsolr\pro\extensions\jet_smart_filters;

use wpsolr\core\classes\engines\WPSOLR_AbstractSearchClient;
use wpsolr\core\classes\ui\WPSOLR_Query;
use wpsolr\core\classes\WPSOLR_Events;
use wpsolr\core\classes\WpSolrSchema;
use wpsolr\pro\extensions\jet_engine\WPSOLR_Plugin_Jet_Engine;

class WPSOLR_Plugin_Jet_Smart_Filters extends WPSOLR_Plugin_Jet_Engine {

	/**
	 * Constructor
	 * Subscribe to actions
	 */

	function __construct() {


		add_action( 'jet-smart-filters/init', function ( $jetsmart_filters ) {
			/**
			 * Remove current Jet filters indexer instance filters
			 */
			global $wp_filter;
			unset( $wp_filter['jet-smart-filters/filters/localized-data'] );

			/**
			 * Replace Jet filters indexer instance with ours
			 */
			$jetsmart_filters->indexer->data = new WPSOLR_Jet_Smart_Filters_Indexer_Data();
		} );


		// Store Jet Engine query arguments
		add_filter( 'jet-engine/listing/grid/posts-query-args', [ $this, 'add_query_args' ], 10, 2 );

		// Add custom filters
		add_action( WPSOLR_Events::WPSOLR_ACTION_SOLARIUM_QUERY, [
			$this,
			'wpsolr_action_query',
		], 10, 1 );

	}

	public function wpsolr_action_query( $parameters ) {

		if ( empty( $_REQUEST['action'] ) ||
		     empty( $_REQUEST['query'] ) ||
		     ( 'jet_smart_filters' !== $_REQUEST['action'] )
		) {
			// Not a SmartFilter query
			return;
		}

		/* @var WPSOLR_Query $wpsolr_query */
		$wpsolr_query = $parameters[ WPSOLR_Events::WPSOLR_ACTION_SOLARIUM_QUERY__PARAM_WPSOLR_QUERY ];
		/* @var WPSOLR_AbstractSearchClient $search_engine_client */
		$search_engine_client = $parameters[ WPSOLR_Events::WPSOLR_ACTION_SOLARIUM_QUERY__PARAM_SOLARIUM_CLIENT ];

		/**
		 ** Filters
		 * $_REQUEST['query'] => {"_meta_query_end-date|date-range":"10\/31\/2020:10\/31\/2020","_meta_query_color|is_custom_checkbox":["red","green"]}
		 */
		$main_keywords         = '';
		$custom_field_keywords = [];
		foreach ( $_REQUEST['query'] as $filter_name_encoded => $filter_value ) {

			/**
			 * Security: escape request values
			 */
			$filter_name_encoded = esc_attr( $filter_name_encoded );
			$filter_value        = esc_attr( $filter_value );

			$filter_no_prefix = explode( '|', preg_replace( '/_meta_query_(.*)/', '$1', $filter_name_encoded ) );
			$filter_name      = $filter_no_prefix[0];
			$filter_type      = $filter_no_prefix[1];

			$field_name_with_extension = WpSolrSchema::replace_field_name_extension( $filter_name . WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING );
			$filter_explanation        = sprintf( 'JetSmartFilters %s %s', $filter_type, $filter_name );
			switch ( $filter_type ) {
				case 'search':
					if ( '__s_query' === $filter_name ) {
						// Filter default search on all fields

						$main_keywords = $filter_value;

					} else {
						// Filter search on one field

						$custom_field_keywords[ $filter_name ] = $filter_value;
					}
					break;

				case 'range':
				case 'date-range':
				case 'check-range':

					/**
					 * Check range can $filter_value can be an array of range values.
					 * Let's make a 'OR' on all the ranges conditions
					 */
					$new_filters = [];
					foreach ( ( is_array( $filter_value ) ? $filter_value : [ $filter_value ] ) as $filter_value_string ) {
						$range = explode( ':', $filter_value_string );

						if ( 'date-range' === $filter_type ) {
							// Convert from '10/01/2020' to timestamp
							foreach ( $range as &$date ) {
								if ( ! empty( $date ) ) {
									$date = $search_engine_client->search_engine_client_format_date( $date );
								}
							}
						}

						if ( ! empty( $range[0] ) && ! empty( $range[1] ) ) {
							$new_filters[] = $search_engine_client->search_engine_client_create_filter_between(
								$field_name_with_extension,
								$range
							);

						} elseif ( empty( $range[0] ) ) {

							$new_filters[] = $search_engine_client->search_engine_client_create_filter_lte(
								$field_name_with_extension,
								[ $range[1] ]
							);

						} elseif ( empty( $range[1] ) ) {

							$new_filters[] = $search_engine_client->search_engine_client_create_filter_gte(
								$field_name_with_extension,
								[ $range[0] ]
							);
						}
					}

					if ( ! empty( $new_filters ) ) {
						$search_engine_client->search_engine_client_add_filter( $filter_explanation,
							$search_engine_client->search_engine_client_create_or( $new_filters )
						);
					}
					break;

				default:
					$search_engine_client->search_engine_client_add_filter_in_terms( $filter_explanation,
						$field_name_with_extension,
						is_array( $filter_value ) ? $filter_value : [ $filter_value ]
					);
					break;
			}

		}

		/**
		 * Add main and custom field keywords
		 */
		if ( ! empty( $main_keywords ) || ! empty( $custom_field_keywords ) ) {
			$search_engine_client->set_keywords( $main_keywords, $custom_field_keywords );
		}

	}

}
