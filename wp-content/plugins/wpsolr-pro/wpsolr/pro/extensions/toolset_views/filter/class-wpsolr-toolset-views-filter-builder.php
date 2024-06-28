<?php

namespace wpsolr\pro\extensions\toolset_views\filter;

use wpsolr\core\classes\engines\WPSOLR_AbstractSearchClient;


class WPSOLR_Toolset_Views_Filter_Builder {

	/** @var string */
	static protected $view_id;

	/** @var WPSOLR_AbstractSearchClient */
	static protected $search_engine_client;

	/** @var array $view_settings archive or view settings */
	protected static $view_settings;

	/** @var \WP_Query $wp_query */
	protected static $wp_query;

	/**
	 * @param string $view_id
	 * @param WPSOLR_AbstractSearchClient $search_engine_client
	 * @param array $view_settings archive or view settings
	 * @param \WP_Query $wp_query
	 */
	static public function build_filters( $view_id, $search_engine_client, $view_settings, $wp_query ) {

		self::$view_id              = $view_id;
		self::$search_engine_client = $search_engine_client;
		self::$view_settings        = $view_settings;
		self::$wp_query             = $wp_query;

		WPSOLR_Toolset_Views_Filter_Post_Current::_add_filters();
		WPSOLR_Toolset_Views_Filter_Post_Author::_add_filters();
		WPSOLR_Toolset_Views_Filter_Post_Date::_add_filters();
		WPSOLR_Toolset_Views_Filter_Post_id::_add_filters();
		WPSOLR_Toolset_Views_Filter_Post_Parent::_add_filters();
		WPSOLR_Toolset_Views_Filter_Post_Status::_add_filters();
		WPSOLR_Toolset_Views_Filter_Post_Sticky::_add_filters();
		WPSOLR_Toolset_Views_Filter_Post_Category::_add_filters();
		WPSOLR_Toolset_Views_Filter_Post_Tag::_add_filters();
		WPSOLR_Toolset_Views_Filter_Post_Custom_Field::_add_filters();
	}

	/**
	 * Defined in children
	 */
	static protected function _add_filters() {
		// Override
	}

	/**
	 * @param string $parameter_name
	 *
	 * @return mixed
	 */
	static protected function get_wpa_setting( $parameter_name ) {
		return isset( self::$view_settings[ $parameter_name ] ) ? self::$view_settings[ $parameter_name ] : [];
	}

	/**
	 * @param $inner_filters
	 * @param $compare
	 * @param $field_name
	 * @param $field_value
	 */
	protected static function create_compare_filter( &$inner_filters, $compare, $field_name, $field_value ) {
		switch ( $compare ) {
			case 'LIKE':
				$inner_filters[] = self::$search_engine_client->search_engine_client_create_filter_wildcard(
					$field_name,
					sprintf( '*%s*', $field_value )
				);
				break;

			case 'NOT LIKE':
				$inner_filters[] =
					self::$search_engine_client->search_engine_client_create_filter_wildcard_not(
						$field_name,
						sprintf( '*%s*', $field_value )
					);
				break;

			case '=':
				$inner_filters[] = self::$search_engine_client->search_engine_client_create_filter_in_terms(
					$field_name,
					[ $field_value ]
				);
				break;

			case 'IN':
				$inner_filters[] = self::$search_engine_client->search_engine_client_create_filter_in_terms(
					$field_name,
					self::to_array( $field_value, true )
				);
				break;

			case 'NOT IN':
				$inner_filters[] = self::$search_engine_client->search_engine_client_create_filter_not_in_terms(
					$field_name,
					self::to_array( $field_value, true )
				);
				break;

			case '!=':
				$inner_filters[] = self::$search_engine_client->search_engine_client_create_filter_not_in_terms(
					$field_name,
					[ $field_value ]
				);
				break;


			case '<':
				$inner_filters[] = self::$search_engine_client->search_engine_client_create_filter_lt(
					$field_name,
					[ $field_value ]
				);
				break;

			case '<=':
				$inner_filters[] = self::$search_engine_client->search_engine_client_create_filter_lte(
					$field_name,
					[ $field_value ]
				);
				break;

			case '>':
				$inner_filters[] = self::$search_engine_client->search_engine_client_create_filter_gt(
					$field_name,
					[ $field_value ]
				);
				break;

			case '>=':
				$inner_filters[] = self::$search_engine_client->search_engine_client_create_filter_gte(
					$field_name,
					[ $field_value ]
				);
				break;

			case 'BETWEEN':
				$inner_filters[] = self::$search_engine_client->search_engine_client_create_filter_between(
					$field_name,
					self::to_array( $field_value, false )
				);
				break;

			case 'NOT BETWEEN':
				$inner_filters[] = self::$search_engine_client->search_engine_client_create_filter_not_between(
					$field_name,
					self::to_array( $field_value, false )
				);
				break;
		}
	}

	/**
	 * @param $field_values
	 *
	 * [['1'], ['2'], ['1, 2'], ['2,1]] => ['1', '2']
	 *
	 * @param bool $is_deduplicate
	 *
	 * @return array
	 */
	protected static function to_array( $field_values, $is_deduplicate = true ) {

		$results = [];
		foreach ( is_array( $field_values ) ? $field_values : [ $field_values ] as $field_value ) {

			$results = array_merge( $results, explode( ',', $field_value ) );
		}

		if ( $is_deduplicate ) {

			$deduplicated = [];
			foreach ( $results as $key => $val ) {
				$deduplicated[ $val ] = true;
			}

			$results = array_keys( $deduplicated );
		}

		return $results;
	}
}
