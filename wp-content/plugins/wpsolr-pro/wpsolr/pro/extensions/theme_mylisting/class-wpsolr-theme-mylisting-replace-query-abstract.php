<?php

namespace wpsolr\pro\extensions\theme_mylisting;

use wpsolr\core\classes\engines\WPSOLR_AbstractSearchClient;
use wpsolr\core\classes\extensions\localization\OptionLocalization;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\ui\WPSOLR_Query_Parameters;
use wpsolr\core\classes\utilities\WPSOLR_Option;
use wpsolr\core\classes\WPSOLR_Events;
use wpsolr\core\classes\WpSolrSchema;
use wpsolr\pro\extensions\theme_listify\WPSOLR_Job_Manager_Abstract;
use wpsolr\pro\extensions\theme_listify\WPSOLR_Theme_Listify_Filter_By_Distance;

/**
 * Replace sidebar UI filter SQL query
 */
abstract class WPSOLR_Theme_Mylisting_Replace_Query_Abstract extends WPSOLR_Theme_Listify_Filter_By_Distance {

	/** @var string[] sql texts to match a sql query */
	const _sql_fragments = [];

	/** @var string */
	protected $field_name = '';

	/** @var string */
	protected $listing_type = '';

	/** @var array */
	protected $wpsolr_facets_types = [];

	/** @var array */
	protected $wpsolr_range_field_get_var = [];

	/**
	 * @inheritDoc
	 */
	protected function _wpsolr_init() {

		add_action( WPSOLR_Events::WPSOLR_ACTION_SOLARIUM_QUERY, [
			$this,
			'wpsolr_action_query',
		], 10, 1 );

		add_action( WPSOLR_Events::WPSOLR_FILTER_FACETS_TO_DISPLAY, [
			$this,
			'wpsolr_filter_facets_to_display',
		], 10, 1 );

		add_filter( WPSOLR_Events::WPSOLR_FILTER_FACET_TYPE, [ $this, 'wpsolr_filter_facet_type' ], 10, 2 );
	}

	/**
	 * Get the facet type from a facet layout
	 *
	 * @param string $default_value
	 * @param string $facet_name
	 *
	 * @return string
	 */
	public function wpsolr_filter_facet_type( $default_value, $facet_name ) {

		return $this->wpsolr_facets_types[ $facet_name ] ?? $default_value;
	}

	protected function _get_ml_filters() {

		$explore = $GLOBALS['c27-explore'] ?? null;

		$results = [];
		if ( ! empty( $explore ) ) {

			foreach ( $explore->types as $type ) {
				$results = array_merge( $results, $type->get_advanced_filters() );
			}

		} else {

			$listing_types_array = \MyListing\get_posts_dropdown( 'case27_listing_type', 'post_name' );

			$listing_types = [];
			foreach ( $listing_types_array as $listing_type => $listing_type_label ) {
				$listing_type = trim( $listing_type );

				$listing_type_post = get_posts( [
					'name'        => $listing_type,
					'post_type'   => 'case27_listing_type',
					'post_status' => 'publish',
					'numberposts' => 1
				] );

				if ( $listing_type_post ) {
					$listing_types[] = \MyListing\Src\Listing_Type::get( $listing_type_post[0] );
				}
			}

			foreach ( $listing_types as $type ) {
				$results = array_merge( $results, $type->get_basic_filters( 'basic' ) );
			}

		}

		return $results;
	}

	/**
	 * Replace facets in WPSOLR settings with facets in MyListing settings
	 *
	 * @param string[] $facets_to_display ['type', 'categories', 'product_cat_str']
	 *
	 * @return array
	 */
	public function wpsolr_filter_facets_to_display( $facets_to_display ) {


		if ( ! empty( $this->field_name ) ) {

			$indexed_custom_fields = WPSOLR_Service_Container::getOption()->get_option_index_custom_fields( true );
			foreach ( $this->_get_ml_filters() as $filter ) {

				$field_name_with_str = [];
				$field_name          = sprintf( '_%s_str', $filter->get_prop( 'show_field' ) );
				switch ( $filter->get_type() ) {
					case 'dropdown':
					case 'checkboxes':
						$field_name_with_str = [
							'field_name'       => $field_name,
							'facet_field_name' => $field_name,
							'facet_type'       => WPSOLR_Option::OPTION_FACET_FACETS_TYPE_FIELD,
						];
						break;

					case 'range':
						$field_name_with_str = [
							'field_name'       => $field_name,
							'facet_field_name' => $field_name,
							'facet_type'       => WPSOLR_Option::OPTION_FACET_FACETS_TYPE_MIN_MAX,
						];

						break;

					case 'date':
						if ( $filter->get_prop( 'format' ) === 'year' ) {
							$field_name_with_str = [
								'field_name'       => $field_name,
								'facet_field_name' => sprintf( '_%s%s', $filter->get_prop( 'show_field' ), WpSolrSchema::_FIELD_NAME_YEAR_I ),
								'facet_type'       => WPSOLR_Option::OPTION_FACET_FACETS_TYPE_FIELD,
							];
						}
						break;
				}

				// Add field to facets
				if ( ! empty( $field_name_with_str ) &&
				     ! in_array( $field_name_with_str['facet_field_name'], $facets_to_display ) && // not already there
				     ( in_array( $field_name_with_str['field_name'], $indexed_custom_fields ) )  // in indexed custom fields
				) {
					$facets_to_display[]                                                   = $field_name_with_str['facet_field_name'];
					$this->wpsolr_facets_types[ $field_name_with_str['facet_field_name'] ] = $field_name_with_str['facet_type'];
				}

			}
		}

		return $facets_to_display;
	}

	/**
	 * Get a range value from its value, not from its post ID
	 * Because MyListing range.php gets the post ID of the max or of the min, then retrieves the value with get_post_meta
	 * but min/max retrieves values, so MyListing calls with $object_id as the value !!!
	 *
	 * @param $return ull|array|string The value to return
	 * @param $object_id int ID of the object metadata is for
	 * @param @meta_key string Metadata key
	 * @param $single bool Whether to return only the first value of the specified $meta_key
	 *
	 * @return mixed Will be an array if $single is false. Will be value of meta data field if $single is true.
	 *
	 * @since m2m
	 */
	public function wpsolr_get_post_range_value( $return, $object_id, $meta_key, $single ) {

		if ( is_null( $return ) && $single && $this->_is_field_range( $meta_key )
		) {
			// Done. No need.
			remove_filter( 'get_post_metadata', array( $this, 'wpsolr_get_post_range_value' ), 10 );

			return $object_id;
		}

		return $return;
	}


	protected function _is_field_range( $field_name ) {
		return ( WPSOLR_Option::OPTION_FACET_FACETS_TYPE_MIN_MAX ===
		         $this->wpsolr_filter_facet_type( WPSOLR_Option::OPTION_FACET_FACETS_TYPE_FIELD,
			         sprintf( '%s%s', $field_name, WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING ) ) );
	}

	/**
	 *
	 * Add a filter on product post type.
	 *
	 * @param array $parameters
	 *
	 */
	public
	function wpsolr_action_query(
		$parameters
	) {

		/* @var WPSOLR_AbstractSearchClient $search_engine_client */
		$search_engine_client = $parameters[ WPSOLR_Events::WPSOLR_ACTION_SOLARIUM_QUERY__PARAM_SOLARIUM_CLIENT ];

		if ( ! empty( $this->listing_type ) ) {
			// Force type listing. we don't want to get values from blog posts or woocommerce products
			$search_engine_client->search_engine_client_add_filter_term( sprintf( 'WPSOLR_Theme_MyListing type:%s', WPSOLR_Job_Manager_Abstract::POST_TYPE_JOB_LISTING ), WpSolrSchema::_FIELD_NAME_TYPE, false, WPSOLR_Job_Manager_Abstract::POST_TYPE_JOB_LISTING );

		}

	}

	/** @var array */
	protected
	static $cached_facets = null;

	/**
	 * @return bool
	 */
	public
	function wpsolr_get_is_custom() {
		return true;
	}

	/**
	 * @param bool $wpsolr_is_custom
	 */
	public
	function wpsolr_set_is_custom(
		$wpsolr_is_custom
	) {
		// Several successive calls can happen
		parent::wpsolr_set_is_custom( true );
	}

	/**
	 * Replace dropdown filter SQL in postmeta with Elasticsearch facets in listing type
	 * @inheritDoc
	 */
	protected
	function wpsolr_custom_get_col(
		$query = null, $x = 0
	) {
		return $this->get_search_engine_results();
	}

	protected function wpsolr_custom_get_var( $query = null, $x = 0, $y = 0 ) {
		$value = $this->get_search_engine_results();

		if ( ! empty( $value ) && $this->_is_field_range( $this->field_name ) ) {

			$min_max = explode( '-', $value[0] );

			$value = $min_max[ $this->wpsolr_range_field_get_var[ $this->field_name ] ?? 0 ];

			$this->wpsolr_range_field_get_var[ $this->field_name ] = 1;

			// Activate the filter at the last moment, just before range.php calls the range value
			add_filter( 'get_post_metadata', array( $this, 'wpsolr_get_post_range_value' ), 10, 4 );
		}

		return $value;
	}

	protected
	function get_search_engine_results() {

		if ( is_null( static::$cached_facets ) ) {

			$wpsolr_query = WPSOLR_Query_Parameters::CreateQuery();//$wpsolr_query->wpsolr_set_is_admin( $is_search_admin );
			$solr_client  = WPSOLR_Service_Container::get_solr_client( false );
			$results      = $solr_client->execute_wpsolr_query( $wpsolr_query, false );
			$facets_data  = $solr_client->get_results_facets( $wpsolr_query, $results, OptionLocalization::get_options(), [] );

			$facets_results = [];
			foreach ( $facets_data['facets'] as $facet ) {
				$field_name_with_extension = WpSolrSchema::get_field_without_str_ending( $facet['id'] );

				foreach ( $facet['items'] as $facet_item ) {
					$facets_results[ $field_name_with_extension ][] = $facet_item['value'];
				}
			}

			// Save in cache
			static::$cached_facets = $facets_results;
		}

		$field_name_with_extension = WpSolrSchema::get_field_without_str_ending( $this->field_name );

		return static::$cached_facets[ $field_name_with_extension ] ?? [];
	}
}