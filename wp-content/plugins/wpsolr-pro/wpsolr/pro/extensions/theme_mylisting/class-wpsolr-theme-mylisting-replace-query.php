<?php

namespace wpsolr\pro\extensions\theme_mylisting;

use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\WpSolrSchema;

class WPSOLR_Theme_Mylisting_Replace_Query extends WPSOLR_Theme_Mylisting_Replace_Query_Abstract {

	const WPSOLR_QUERY_PROXIMITY = 'proximity';
	const WPSOLR_QUERY_DROPDOWN = 'dropdown';
	const WPSOLR_QUERY_YEARS_ONLY = 'years_only';
	const WPSOLR_QUERY_MIN_MAX = 'min_max';

	/** @var string Which query has been recognized? */
	protected $wpsolr_query_type = '';

	/**
	 * @inheritDoc
	 */
	protected function _wpsolr_get_sql_fragments_per_query_type() {
		$results = [];

		$results[ self::WPSOLR_QUERY_PROXIMITY ] = [
			'radians(',
		];

		if ( WPSOLR_Service_Container::getOption()->get_theme_mylisting_is_replace_filters_side_bar_queries() ) {

			/**
			 * line 86 wp-content/themes/my-listing/includes/src/listing-types/filters/range.php
			 *
			 * SELECT wp_postmeta.meta_value
			 * FROM wp_posts
			 * INNER JOIN wp_postmeta
			 * ON ( wp_posts.ID = wp_postmeta.post_id )
			 * INNER JOIN wp_postmeta AS mt1
			 * ON ( wp_posts.ID = mt1.post_id )
			 * WHERE wp_postmeta.meta_key = '_gender'
			 * AND wp_postmeta.meta_value != ''
			 * AND mt1.meta_key = '_case27_listing_type'
			 * AND mt1.meta_value = 'physicians'
			 * AND wp_posts.post_type = 'job_listing'
			 * AND wp_posts.post_status = 'publish'
			 * GROUP BY wp_postmeta.meta_value
			 * ORDER BY wp_posts.post_name DESC
			 */
			$results[ self::WPSOLR_QUERY_MIN_MAX ] = [
				".post_type = 'job_listing'",
				'GROUP BY',
				'ORDER BY',
				'.meta_value +0',
			];

			/**
			 * line 291 wp-content/themes/my-listing/includes/src/listing-types/filters/dropdown.php
			 *
			 * SELECT wp_postmeta.meta_value
			 * FROM wp_posts
			 * INNER JOIN wp_postmeta
			 * ON ( wp_posts.ID = wp_postmeta.post_id )
			 * INNER JOIN wp_postmeta AS mt1
			 * ON ( wp_posts.ID = mt1.post_id )
			 * WHERE wp_postmeta.meta_key = '_gender'
			 * AND wp_postmeta.meta_value != ''
			 * AND mt1.meta_key = '_case27_listing_type'
			 * AND mt1.meta_value = 'physicians'
			 * AND wp_posts.post_type = 'job_listing'
			 * AND wp_posts.post_status = 'publish'
			 * GROUP BY wp_postmeta.meta_value
			 * ORDER BY wp_posts.post_name DESC
			 */
			$results[ self::WPSOLR_QUERY_DROPDOWN ] = [
				"meta_key = '_case27_listing_type'",
				".post_type = 'job_listing'"
			];

			/**
			 * Years grouped by date
			 * line 185 wp-content/themes/my-listing/includes/src/listing-types/filters/date.php
			 *
			 * SELECT YEAR(wp_8_postmeta.meta_value) as item_year
			 * FROM wp_8_posts
			 * INNER JOIN wp_8_postmeta ON ( wp_8_posts.ID = wp_8_postmeta.post_id )
			 * WHERE wp_8_postmeta.meta_key = %s
			 * AND wp_8_postmeta.meta_value != ''
			 * AND wp_8_posts.post_type = 'job_listing'
			 * AND wp_8_posts.post_status = 'publish'
			 * GROUP BY item_year
			 * ORDER BY item_year DESC
			 */
			$results[ self::WPSOLR_QUERY_YEARS_ONLY ] = [
				'SELECT YEAR',
				'item_year',
				".post_type = 'job_listing'"
			];
		}

		return $results;
	}

	/**
	 * @inheritDoc
	 */
	public function wpsolr_get_is_custom_query( $query ) {
		return $this->wpsolr_get_is_custom_query_from_sql_fragments( $query );
	}

	/**
	 * @inheritdoc
	 */
	protected function wpsolr_custom_prepare( $query, $args ) {

		switch ( $this->wpsolr_query_type ) {
			case static::WPSOLR_QUERY_PROXIMITY:
				// [$earth_radius, $lat, $lng, $lat, $proximity]
				$this->earth_radius     = $args[0];
				$this->wpsolr_latitude  = $args[1];
				$this->wpsolr_longitude = $args[2];
				$this->wpsolr_radius    = $args[4];

				$query = '';
				break;

			case static::WPSOLR_QUERY_DROPDOWN:
				$this->field_name   = $args[0];
				$this->listing_type = $args[1];
				break;

			case static::WPSOLR_QUERY_YEARS_ONLY:
				$this->field_name = sprintf( '%s%s', $args[0], WpSolrSchema::_FIELD_NAME_YEAR_I );
				break;

			case static::WPSOLR_QUERY_MIN_MAX:
				$this->field_name = $args[0];
				break;
		}

		return $query;
	}


}