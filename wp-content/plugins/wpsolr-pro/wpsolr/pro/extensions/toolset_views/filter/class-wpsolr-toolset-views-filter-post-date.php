<?php

namespace wpsolr\pro\extensions\toolset_views\filter;

use wpsolr\core\classes\WpSolrSchema;

class WPSOLR_Toolset_Views_Filter_Post_Date extends WPSOLR_Toolset_Views_Filter_Builder {


	/**
	 * Date formats
	 */
	const YEAR = 'year';

	const MONTH = 'month';

	const WEEK = 'week';

	const DAY = 'day';

	const DAYOFYEAR = 'dayofyear';

	const DAYOFWEEK = 'dayofweek';

	const HOUR = 'hour';

	const MINUTE = 'minute';

	const SECOND = 'second';

	/**
	 * Fields
	 */
	const POST_DATE = 'post_date';

	const POST_DATE_GMT = 'post_date_gmt';

	const POST_MODIFIED = 'post_modified';

	const POST_MODIFIED_GMT = 'post_modified_gmt';


	static $map_date_types = [
		self::YEAR      => WpSolrSchema::_FIELD_NAME_YEAR_I,
		self::MONTH     => WpSolrSchema::_FIELD_NAME_YEAR_MONTH_I,
		self::WEEK      => WpSolrSchema::_FIELD_NAME_YEAR_WEEK_I,
		self::DAY       => WpSolrSchema::_FIELD_NAME_MONTH_DAY_I,
		self::DAYOFYEAR => WpSolrSchema::_FIELD_NAME_YEAR_DAY_I,
		self::DAYOFWEEK => WpSolrSchema::_FIELD_NAME_WEEK_DAY_I,
		self::HOUR      => WpSolrSchema::_FIELD_NAME_DAY_HOUR_I,
		self::MINUTE    => WpSolrSchema::_FIELD_NAME_DAY_MINUTE_I,
		self::SECOND    => WpSolrSchema::_FIELD_NAME_DAY_SECOND_I,
	];

	static $map_date_fields = [
		self::POST_DATE         => WpSolrSchema::_FIELD_NAME_POST_DATE,
		self::POST_DATE_GMT     => WpSolrSchema::_FIELD_NAME_POST_DATE_GMT,
		self::POST_MODIFIED     => WpSolrSchema::_FIELD_NAME_POST_MODIFIED,
		self::POST_MODIFIED_GMT => WpSolrSchema::_FIELD_NAME_POST_MODIFIED_GMT,
	];

	/**
	 *        $date_operator = [ "!=", "<", "<=", ">", ">=", "IN", "NOT IN", "BETWEEN", "NOT BETWEEN" ];
	 *        $date_column   = [ 'post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt' ];
	 *        $date_query    = [
	 *            [
	 *                [
	 *                'year'      => '2018',
	 *                'month'     => '10',
	 *                'week'      => '1',
	 *                'day'       => '1',
	 *                'dayofyear' => '1',
	 *                'dayofweek' => 2,
	 *                'hour'      => '1',
	 *                'minute'    => '1',
	 *                'second'    => '1',
	 *                'compare'   => '=',
	 *                'column'    => 'post_date',
	 *                ]
	 *            ],
	 *        'relation' => 'AND',
	 *        ];
	 *
	 * @inheritdoc
	 */
	static protected function _add_filters() {

		if ( ! empty( $date_filter = self::get_wpa_setting( 'date_filter' ) ) && ! is_year() && ! is_month() && ! is_day() ) {

			// Set in archive_apply_settings(), then archive_filter_post_date()
			$date_query = self::$wp_query->get( 'date_query' );

			$outer_filters = [];
			$is_filter_and = ( 'AND' === $date_query['relation'] );
			foreach ( $date_query as $position => $date_query_values ) {

				if ( is_array( $date_query_values ) ) {

					$column  = $date_query_values['column'];
					$compare = $date_query_values['compare'];

					$inner_filters = [];
					foreach ( $date_query_values as $date_query_name => $date_query_value ) {

						if ( isset( self::$map_date_types[ $date_query_name ] ) ) {

							$field_name = self::$map_date_fields[ $column ] . self::$map_date_types[ $date_query_name ];

							self::create_compare_filter( $inner_filters, $compare, $field_name, $date_query_value );
						}

					}

					// Filters inside a form are implicitly 'AND'
					if ( ! empty( $inner_filters ) ) {
						$outer_filters[] = ( count( $inner_filters ) === 1 ) ? $inner_filters[0] :
							self::$search_engine_client->search_engine_client_create_and( $inner_filters );
					}
				}

			}

			// And or 'OR' between the outer filters
			if ( ! empty( $outer_filters ) ) {

				$outer_filters =
					( count( $outer_filters ) === 1 ) ? $outer_filters[0] :
						( $is_filter_and ? self::$search_engine_client->search_engine_client_create_and( $outer_filters )
							: self::$search_engine_client->search_engine_client_create_or( $outer_filters ) );

				self::$search_engine_client->search_engine_client_add_filter( 'Toolset Views date filters', $outer_filters );
			}

		}
	}

}
