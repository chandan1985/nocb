<?php

namespace wpsolr\pro\extensions\theme_mylisting;

use wpsolr\core\classes\engines\solarium\WPSOLR_SearchSolariumClient;
use wpsolr\core\classes\engines\WPSOLR_AbstractIndexClient;
use wpsolr\core\classes\engines\WPSOLR_AbstractSearchClient;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\ui\WPSOLR_Query;
use wpsolr\core\classes\utilities\WPSOLR_Option;
use wpsolr\core\classes\utilities\WPSOLR_Regexp;
use wpsolr\core\classes\utilities\WPSOLR_Sanitize;
use wpsolr\core\classes\WpSolrSchema;
use wpsolr\pro\extensions\geolocation\WPSOLR_Option_GeoLocation;
use wpsolr\pro\extensions\theme_listify\WPSOLR_Theme_Listify;

class WPSOLR_Theme_Mylisting extends WPSOLR_Theme_Listify {

	const WPSOLR_GEOLOCATION = 'wpsolr_mylisting_geolocation';
	const RATING = '_case27_average_rating';
	const CUTOM_FIELD_LISTING_TYPE = '_case27_listing_type';
	const PRIORITY = '_featured';

	public function __construct() {

		// Parent
		parent::__construct();

		if ( is_admin() ||
		     ( ! empty( $_REQUEST ) &&
		       ( ! empty( $_REQUEST["_wpnonce"] ) || ! empty( $_REQUEST['doing_wp_cron'] ) ) )
		) // Undefined property: wpsolr\pro\extensions\theme_mylisting\WPSOLR_Theme_Mylisting_Replace_Query::$wc_tax_rate_classes in /srv/www/wordpress-develop/src/wp-includes/wp-db.php on line 647
		{

			add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ], 100 );

		} else {

			$this->mylisting_feature_listings_in_location_search();
		}

	}

	/**
	 * @inheritdoc
	 */

	function init_search_events() {

		// Search parameters
		add_filter( 'mylisting/explore/args', [ $this, 'job_manager_get_listings_args' ], 9, 1 );

		if ( WPSOLR_Service_Container::getOption()->get_theme_mylisting_is_replace_similar_listing() ) {
			// Similar listing parameters
			add_filter( 'mylisting/similar-listings/args', [ $this, 'job_manager_get_listings_args' ], 9, 1 );
		}
	}

	public function admin_enqueue_scripts() {

		// Load scripts and css only in WPSOLR admin pages
		if ( false !== strpos( $_SERVER['REQUEST_URI'], 'solr_settings' ) ) {

			/**
			 * select2 dropdown list
			 *
			 * Prevent js console error from Mylisting theme: "admin.js Uncaught TypeError: Cannot read property 'require' of undefined"
			 *
			 */
			wp_dequeue_script( 'theme-script-main' ); // do not load admin.js
			wp_deregister_script( 'theme-script-main' ); // do not load admin.js

		}

	}

	/**
	 * @inheritdoc
	 */
	protected function get_default_custom_fields() {

		$results = parent::get_default_custom_fields();

		// Add priority
		$results[ static::PRIORITY ] = [
			self::_FIELD_POST_TYPES                                                   => [ self::POST_TYPE_JOB_LISTING ],
			WPSOLR_Option::OPTION_INDEX_CUSTOM_FIELD_PROPERTY_SOLR_TYPE               => WpSolrSchema::_SOLR_DYNAMIC_TYPE_INTEGER,
			WPSOLR_Option::OPTION_INDEX_CUSTOM_FIELD_PROPERTY_CONVERSION_ERROR_ACTION => WPSOLR_Option::OPTION_INDEX_CUSTOM_FIELD_PROPERTY_CONVERSION_ERROR_ACTION_IGNORE_FIELD,
		];

		// Add listing type
		$results[ static::CUTOM_FIELD_LISTING_TYPE ] = [
			self::_FIELD_POST_TYPES                                                   => [ self::POST_TYPE_JOB_LISTING ],
			WPSOLR_Option::OPTION_INDEX_CUSTOM_FIELD_PROPERTY_SOLR_TYPE               => WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING,
			WPSOLR_Option::OPTION_INDEX_CUSTOM_FIELD_PROPERTY_CONVERSION_ERROR_ACTION => WPSOLR_Option::OPTION_INDEX_CUSTOM_FIELD_PROPERTY_CONVERSION_ERROR_ACTION_IGNORE_FIELD,
		];

		/**
		 * Add MyListing fields which are not stored as custom fields
		 */
		$mylisting_types = get_posts( [
			'post_type'      => 'case27_listing_type',
			'fields'         => 'ids',
			'posts_per_page' => - 1,
		] );

		foreach ( $mylisting_types as $type_id ) {
			$type = \MyListing\Src\Listing_Type::get( $type_id );
			if ( ! $type ) {
				continue;
			}

			// loop through each listing type fields
			foreach ( $type->get_fields() as $field ) {
				if ( ! $field ) {
					continue;
				}

				$field_extension = '';
				switch ( $field->get_type() ) {
					case 'recurring-date':
						/*
						 * Recurring dates are stored in a custom table
						 * /wp-content/themes/my-listing/includes/src/recurring-dates/recurring-dates.php
						 */
						$field_extension = WpSolrSchema::_SOLR_DYNAMIC_TYPE_EMBEDDED_OBJECT;
						break;
				}

				// Add the field
				if ( ! empty( $field_extension ) &&
				     ! isset( $results[ $field->get_key() ] )
				) {
					$results[ $field->get_key() ] = [
						self::_FIELD_POST_TYPES                                                   => [ self::POST_TYPE_JOB_LISTING ],
						WPSOLR_Option::OPTION_INDEX_CUSTOM_FIELD_PROPERTY_SOLR_TYPE               => $field_extension,
						WPSOLR_Option::OPTION_INDEX_CUSTOM_FIELD_PROPERTY_CONVERSION_ERROR_ACTION => WPSOLR_Option::OPTION_INDEX_CUSTOM_FIELD_PROPERTY_CONVERSION_ERROR_ACTION_IGNORE_FIELD,
					];
				}

			}
		}

		return $results;
	}

	protected function get_default_taxonomies() {
		return [ 'job_listing_category', 'region', 'case27_job_listing_tags' ];
	}

	/**
	 *
	 */
	public function mylisting_feature_listings_in_location_search() {

		$this->listify_feature_listings_in_location_search = true;

		$this->filter_by_distance = WPSOLR_Theme_Mylisting_Replace_Query::wpsolr_replace_wpdb( $this );
	}

	/**
	 * @inheritDoc
	 */
	protected function get_query( $args ) {
		return ! isset( $args['s'] ) ? '' : $args['s'];
	}

	/**
	 * @inherit
	 */
	function add_custom_filters( WPSOLR_AbstractSearchClient $search_engine_client ) {

		if ( empty( $this->search_listings_args ) ) {
			return;
		}

		/**
		 * Recurring dates (nested object requiring nested query with a path)
		 */
		if ( ! empty( $this->search_listings_args['recurring_dates'] ) ) {
			$filter_recurring_dates = [];
			$sort_recurring_dates   = [];
			foreach ( $this->search_listings_args['recurring_dates'] as $field_key => $date ) {

				// Create a nested range filter on current recurring date
				$field_name = $field_key . WpSolrSchema::_SOLR_DYNAMIC_TYPE_EMBEDDED_OBJECT;

				/**
				 * Nested sort without filter
				 */
				$sort_def = [];
				if ( isset( $date['orderby'] ) && ( true === $date['orderby'] ) ) {
					$sort_def = [ 'field_name' => $field_name, 'order' => $date['order'], 'mode' => 'min', ];
				}

				$range = [];
				if ( ! empty( $date['start'] ) ) {
					$range[] = $search_engine_client->search_engine_client_create_filter_gte( sprintf( '%s.%s', $field_name, 'end' ),
						[ $search_engine_client->search_engine_client_format_date( $date['start'] ) ]
					);
				}
				if ( ! empty( $date['end'] ) ) {
					$range[] = $search_engine_client->search_engine_client_create_filter_lte( sprintf( '%s.%s', $field_name, 'start' ),
						[ $search_engine_client->search_engine_client_format_date( $date['end'] ) ]
					);
				}

				if ( ! empty( $range ) ) {
					/**
					 * Nested filter
					 */
					if ( ! empty( $date['where_clause'] ) && $date['where_clause'] === true ) {
						$filter_recurring_dates[] = $search_engine_client->search_engine_client_create_nested_query(
							$field_name,
							$search_engine_client->search_engine_client_create_and( $range )
						);
					}

					/**
					 * Nested sort with filter
					 */
					if ( isset( $date['orderby'] ) && ( true === $date['orderby'] ) ) {
						$sort_def['nested'] = $search_engine_client->search_engine_client_create_nested_filter(
							$field_name,
							$search_engine_client->search_engine_client_create_and( $range )
						)['nested'];
					}
				}

				if ( ! empty( $sort_def ) ) {
					$sort_recurring_dates[] = $sort_def;
				}
			}

			if ( ! empty( $sort_recurring_dates ) ) {
				foreach ( $sort_recurring_dates as $sort_recurring_date ) {
					$search_engine_client->search_engine_client_add_sort(
						sprintf( '%s.%s', $sort_recurring_date['field_name'], 'start' ),
						$sort_recurring_date['order'],
						[
							'mode'   => $sort_recurring_date['mode'],
							'nested' => $sort_recurring_date['nested'],
						]
					);
				}
			}

			if ( ! empty( $filter_recurring_dates ) ) {
				// Finally, use a 'OR' on all recurring dates to find at least one even date matching the conditions
				$search_engine_client->search_engine_client_add_filter( 'MyListing recurring dates',
					$search_engine_client->search_engine_client_create_or( $filter_recurring_dates )
				);
			}

		}

		/**
		 * post_type url parameter
		 */
		if ( ! empty( $this->search_listings_args['meta_query'] ) ) {

			//error_log( print_r( $this->search_listings_args['meta_query'], true ) );

			foreach ( $this->search_listings_args['meta_query'] as $meta_query_name => $meta_query ) {

				if ( isset( $meta_query['relation'] ) ) {
					/*
					 [
						'relation' => 'OR',
						[
							'key'     => '_wpsolr-multi-select',
							'value'   => '"2"',
							'compare' => 'LIKE',
						],
						[
							'key'     => '_wpsolr-multi-select',
							'value'   => '"1"',
							'compare' => 'LIKE',
						],
					]
					*/
					$relation_meta_query_values     = [];
					$relation_meta_query_field_name = '';
					foreach ( $meta_query as $position => $relation_meta_query ) {
						if ( 'relation' !== $position ) {
							$relation_meta_query_field_name = $relation_meta_query['key'];
							$relation_meta_query_values[]   = trim( $relation_meta_query['value'], '"' ); // trim select ids from extra quotes added by MyListing: ""1"" => "1"
						}
					}

					if ( 'OR' === $meta_query['relation'] ) {

						$search_engine_client->search_engine_client_add_filter_in_terms( sprintf( 'WPSOLR_MyListing %s', $relation_meta_query_field_name ),
							WpSolrSchema::replace_field_name_extension( $relation_meta_query_field_name . WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING ), $relation_meta_query_values );

					} else {

						$search_engine_client->search_engine_client_add_filter_in_all_terms( sprintf( 'WPSOLR_MyListing %s', $relation_meta_query_field_name ),
							WpSolrSchema::replace_field_name_extension( $relation_meta_query_field_name . WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING ), $relation_meta_query_values );
					}


				} else {

					$meta_query_compare = ! empty( $meta_query['compare'] ) ? $meta_query['compare'] : ( is_array( $meta_query['value'] ) ? 'IN' : '=' );

					switch ( $meta_query_compare ) {
						case '=':
							$search_engine_client->search_engine_client_add_filter_term( sprintf( 'WPSOLR_MyListing %s', $meta_query_name ),
								WpSolrSchema::replace_field_name_extension( $meta_query['key'] . WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING ), false, $meta_query['value'] );
							break;

						case 'IN':
							$search_engine_client->search_engine_client_add_filter_in_terms( sprintf( 'WPSOLR_MyListing %s', $meta_query_name ),
								WpSolrSchema::replace_field_name_extension( $meta_query['key'] . WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING ), $meta_query['value'] );
							break;

						default:
							$meta_type            = isset( $meta_query['type'] ) ? $meta_query['type'] : '';
							$meta_type_is_date    = ( $meta_type == 'DATE' );
							$meta_type_is_numeric = ( $meta_type == 'NUMERIC' );
							$from                 = '*';
							$to                   = '*';
							$is_filter            = true;
							switch ( $meta_query_compare ) {
								case 'BETWEEN':
									$from = $meta_type_is_date
										? $search_engine_client->search_engine_client_format_date( $meta_query['value'][0] . ' 00:00:00' )
										: $meta_query['value'][0];
									$to   = $meta_type_is_date
										? $search_engine_client->search_engine_client_format_date( $meta_query['value'][1] . ' 23:59:59' )
										: $meta_query['value'][1];
									break;

								case '>=':
									$from = $meta_type_is_date
										? $search_engine_client->search_engine_client_format_date( $meta_query['value'] . ' 00:00:00' )
										: $from = $meta_query['value'];
									break;

								case '<=':
									$to = $meta_type_is_date
										? $search_engine_client->search_engine_client_format_date( $meta_query['value'] . ' 23:59:59' )
										: $from = $meta_query['value'];
									break;

								default:
									// Not a filter
									$is_filter = false;
									break;
							}

							if ( $is_filter ) {

								$search_engine_client->search_engine_client_add_filter_range_upper_included(
									sprintf( 'WPSOLR_MyListing %s', 'between' ),
									WpSolrSchema::replace_field_name_extension( $meta_query['key'] . WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING ),
									false,
									$from,
									$to,
									$meta_type_is_date
								);
							}

							break;
					}
				}
			}
		}

		/**
		 * Exclude listings
		 */
		if ( ! empty( $this->search_listings_args['post__not_in'] ) ) {
			$search_engine_client->search_engine_client_add_filter_not_in_terms( 'WPSOLR_MyListing not in PIDs',
				WpSolrSchema::_FIELD_NAME_PID_I, $this->search_listings_args['post__not_in'] );
		}

		/**
		 * Add geo distance filter
		 */
		if ( is_object( $this->filter_by_distance ) && $this->filter_by_distance->wpsolr_get_latitude() ) {
			$search_engine_client->search_engine_client_add_filter_geolocation_distance(
				static::WPSOLR_GEOLOCATION . WPSOLR_Option_GeoLocation::_SOLR_DYNAMIC_TYPE_LATITUDE_LONGITUDE,
				$this->filter_by_distance->wpsolr_get_latitude(),
				$this->filter_by_distance->wpsolr_get_longitude(),
				$this->filter_by_distance->wpsolr_get_radius()
			);
		}

	}

	/**
	 * @inheritdoc
	 */
	function get_option_is_replace_search() {
		return WPSOLR_Service_Container::getOption()->get_theme_mylisting_is_replace_search();
	}

	/**
	 * @inheritdoc
	 */
	function get_option_is_caching() {
		return WPSOLR_Service_Container::getOption()->get_theme_mylisting_is_caching();
	}

	/**
	 * @inheritdoc
	 */
	function get_option_is_replace_sort_options() {
		true;
	}

	/**
	 * @inherit
	 */
	protected function add_sort( WPSOLR_Query $wpsolr_query ) {

		/**
		 * Recurring dates nested sort
		 */
		if ( ! empty( $this->search_listings_args['recurring_dates'] ) ) {
			foreach ( $this->search_listings_args['recurring_dates'] as $field_key => $date ) {
				/**
				 * Nested sort
				 */
				if ( isset( $date['orderby'] ) && ( true === $date['orderby'] ) ) {
					// Stop here. Sort already done before with filters.
					return;
				}
			}
		}

		/**
		 * Add sort
		 *
		 * $this->search_listings_args['orderby'] : ['clause-wpsolr-tag-line-desc-89d6594a559fbe21' => 'DESC', 'clause-wpsolr-tag-line-desc-39e27273e6ea5735' => 'DESC']
		 * $this->search_listings_args['meta_query'] = [
		 *    'clause-wpsolr-tag-line-desc-89d6594a559fbe21'  => ['key'     => '_job_tagline', 'compare' => 'EXISTS', 'type'    => 'CHAR'],
		 *    'clause-wpsolr-tag-line-desc-39e27273e6ea5735' => ['key'     => '_job_phone', 'compare' => 'EXISTS', 'type'    => 'CHAR'],
		 *    'listing_type_query'                            => [...],
		 *    ];
		 *
		 */
		$wpsolr_sort = [];

		$orderby = $this->search_listings_args['orderby'];
		$orderby = empty( $orderby ) ? [] : ( ! is_array( $orderby ) ? [ $orderby ] : $orderby );

		/**
		 * Standard sort
		 */
		foreach ( $orderby as $clause_id => $order ) {

			if ( $this->search_listings_args['mylisting_orderby_rating'] ) {

				$orderby_field_name = '_case27_average_rating';
				$order              = $this->search_listings_args['order'];

			} elseif ( ! empty( $this->search_listings_args['meta_query'][ $clause_id ] ) ) {

				$orderby_data       = $this->search_listings_args['meta_query'][ $clause_id ];
				$orderby_field_name = $orderby_data['key'];

			} elseif ( ! empty( $_POST['form_data']['sort'] ) &&
			           ( 'nearby' === $_POST['form_data']['sort'] ) &&
			           ! empty( $_POST['form_data']['search_location_lat'] ) && is_numeric( $_POST['form_data']['search_location_lat'] ) &&
			           ! empty( $_POST['form_data']['search_location_lng'] ) && is_numeric( $_POST['form_data']['search_location_lng'] )
			) {

				// Sort by distance
				$orderby_field_name = static::WPSOLR_GEOLOCATION;
				$order              = WPSOLR_SearchSolariumClient::SORT_ASC;
				$wpsolr_query->set_wpsolr_latitude( WPSOLR_Sanitize::sanitize_text_field( $_POST['form_data']['search_location_lat'] ) );
				$wpsolr_query->set_wpsolr_longitude( WPSOLR_Sanitize::sanitize_text_field( $_POST['form_data']['search_location_lng'] ) );

			} elseif ( ! empty( $_GET['form_data']['sort'] ) &&
			           ( 'nearby' === $_GET['form_data']['sort'] ) &&
			           ! empty( $_GET['form_data']['search_location_lat'] ) && is_numeric( $_GET['form_data']['search_location_lat'] ) &&
			           ! empty( $_GET['form_data']['search_location_lng'] ) && is_numeric( $_GET['form_data']['search_location_lng'] )
			) {

				// Sort by distance
				$orderby_field_name = static::WPSOLR_GEOLOCATION;
				$order              = WPSOLR_SearchSolariumClient::SORT_ASC;
				$wpsolr_query->set_wpsolr_latitude( WPSOLR_Sanitize::sanitize_text_field( $_GET['form_data']['search_location_lat'] ) );
				$wpsolr_query->set_wpsolr_longitude( WPSOLR_Sanitize::sanitize_text_field( $_GET['form_data']['search_location_lng'] ) );

			} elseif ( ! empty( $_POST['form_data']['sort'] ) &&
			           ( 'nearby' === $_POST['form_data']['sort'] ) &&
			           ! empty( $_POST['form_data']['lat'] ) && is_numeric( $_POST['form_data']['lat'] ) &&
			           ! empty( $_POST['form_data']['lng'] ) && is_numeric( $_POST['form_data']['lng'] )
			) {

				// Sort by distance
				$orderby_field_name = static::WPSOLR_GEOLOCATION;
				$order              = WPSOLR_SearchSolariumClient::SORT_ASC;
				$wpsolr_query->set_wpsolr_latitude( WPSOLR_Sanitize::sanitize_text_field( $_POST['form_data']['lat'] ) );
				$wpsolr_query->set_wpsolr_longitude( WPSOLR_Sanitize::sanitize_text_field( $_POST['form_data']['lng'] ) );

			} elseif ( ! empty( $_GET['form_data']['sort'] ) &&
			           ( 'nearby' === $_GET['form_data']['sort'] ) &&
			           ! empty( $_GET['form_data']['lat'] ) && is_numeric( $_GET['form_data']['lat'] ) &&
			           ! empty( $_GET['form_data']['lng'] ) && is_numeric( $_GET['form_data']['lng'] )
			) {

				// Sort by distance
				$orderby_field_name = static::WPSOLR_GEOLOCATION;
				$order              = WPSOLR_SearchSolariumClient::SORT_ASC;
				$wpsolr_query->set_wpsolr_latitude( WPSOLR_Sanitize::sanitize_text_field( $_GET['form_data']['lat'] ) );
				$wpsolr_query->set_wpsolr_longitude( WPSOLR_Sanitize::sanitize_text_field( $_GET['form_data']['lng'] ) );

			} else {

				$orderby_field_name = $clause_id;
			}

			if ( false !== strpos( $orderby_field_name, 'RAND(' ) ) {
				// random field is like 'RAND(145678)'
				$orderby_field_name = 'random';
			}

			$order        = strtolower( $order );
			$order_is_asc = ( WPSOLR_SearchSolariumClient::SORT_ASC === $order );

			switch ( $orderby_field_name ) {
				case 'date':
					$wpsolr_sort[] = $order_is_asc ? WPSOLR_SearchSolariumClient::SORT_CODE_BY_DATE_ASC : WPSOLR_SearchSolariumClient::SORT_CODE_BY_DATE_DESC;
					break;

				case 'title':
					$wpsolr_sort[] = $order_is_asc ? WPSOLR_SearchSolariumClient::SORT_CODE_BY_TITLE_S_ASC : WPSOLR_SearchSolariumClient::SORT_CODE_BY_TITLE_S_DESC;
					break;

				case 'author':
					$wpsolr_sort[] = $order_is_asc ? WPSOLR_SearchSolariumClient::SORT_CODE_BY_AUTHOR_ASC : WPSOLR_SearchSolariumClient::SORT_CODE_BY_AUTHOR_DESC;
					break;

				case 'comment_count':
					$wpsolr_sort[] = $order_is_asc ? WPSOLR_SearchSolariumClient::SORT_CODE_BY_NUMBER_COMMENTS_ASC : WPSOLR_SearchSolariumClient::SORT_CODE_BY_NUMBER_COMMENTS_DESC;
					break;

				case 'menu_order':
					$wpsolr_sort[] = $order_is_asc ? WPSOLR_SearchSolariumClient::SORT_CODE_BY_MENU_ORDER_ASC : WPSOLR_SearchSolariumClient::SORT_CODE_BY_MENU_ORDER_DESC;
					break;

				case 'ID':
					$wpsolr_sort[] = $order_is_asc ? WPSOLR_SearchSolariumClient::SORT_CODE_BY_PID_ASC : WPSOLR_SearchSolariumClient::SORT_CODE_BY_PID_DESC;
					break;

				case 'random':
					$wpsolr_sort[] = WPSOLR_SearchSolariumClient::SORT_CODE_BY_RANDOM;
					break;

				case 'relevance':
					$wpsolr_sort[] = WPSOLR_SearchSolariumClient::SORT_CODE_BY_RELEVANCY_DESC;
					break;

				case 'none':
					// No sort
					break;

				default:
					// Dynamic fields
					$wpsolr_sort[] = sprintf( '%s%s_%s', $orderby_field_name, WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING, $order );
					break;

			}

		}

		// Add priority sort if required
		if ( ! $this->search_listings_args['mylisting_ignore_priority'] ) {
			array_unshift( $wpsolr_sort, sprintf( '%s%s_%s', static::PRIORITY, WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING, WPSOLR_SearchSolariumClient::SORT_DESC ) );
		}

		if ( ! empty( $wpsolr_sort ) ) {
			$wpsolr_query->set_wpsolr_sort( $wpsolr_sort[0] );

			if ( count( $wpsolr_sort ) > 1 ) {
				$wpsolr_query->set_wpsolr_sort_secondary( $wpsolr_sort[1] );
			}
		}

	}

	/**
	 * Unserialize some fields (for instance, multi select fields)
	 *
	 * @param array $document_for_update
	 * @param $solr_indexing_options
	 * @param $post
	 * @param $attachment_body
	 * @param WPSOLR_AbstractIndexClient $search_engine_client
	 *
	 * @return array Document updated with fields
	 */
	function wpsolr_filter_solarium_document_for_update( array $document_for_update, $solr_indexing_options, $post, $attachment_body, WPSOLR_AbstractIndexClient $search_engine_client ) {

		if ( self::POST_TYPE_JOB_LISTING === $post->post_type ) {

			foreach ( $document_for_update as $field_name => &$field_values ) {
				foreach ( is_array( $field_values ) ? $field_values : [ $field_values ] as $pos => $field_value ) {
					if ( is_serialized( $field_value ) ) {
						$field_values[ $pos ] = @unserialize( $field_value );
						if ( is_array( $field_values[ $pos ] ) && ! empty( $field_values[ $pos ] ) ) {
							foreach ( $field_values[ $pos ] as $pos_value => $value ) {
								if ( $pos_value > 0 ) {
									$field_values[] = $value;
								}
							}
							$field_values[ $pos ] = $field_values[ $pos ][0];
						}
					}
				}
			}

			// Add year field for dates by year filters
			$this->_wpsolr_add_calculated_fields_to_index( $document_for_update, $solr_indexing_options, $post, $attachment_body, $search_engine_client );
		}

		return parent::wpsolr_filter_solarium_document_for_update( $document_for_update, $solr_indexing_options, $post, $attachment_body, $search_engine_client );
	}

	/**
	 * Add calculated fields
	 *
	 * @param array $document_for_update
	 * @param $solr_indexing_options
	 * @param $post
	 * @param $attachment_body
	 * @param WPSOLR_AbstractIndexClient $search_engine_client
	 *
	 * @return void
	 */
	protected function _wpsolr_add_calculated_fields_to_index( &$document_for_update, $solr_indexing_options, $post, $attachment_body, $search_engine_client ) {

		// For MyListing date fields, add the corresponding year for filters "by year only".
		foreach ( $document_for_update as $field_name => &$field_values ) {

			// Fields like '_myfield_dt'
			if ( ( 0 === strpos( $field_name, '_', 0 ) ) &&
			     ( false !== strpos( $field_name, WpSolrSchema::_SOLR_DYNAMIC_TYPE_DATE, strlen( $field_name ) - 3 ) )
			) {
				$field_name_without_ending = WPSOLR_Regexp::remove_string_at_the_end( $field_name, WpSolrSchema::_SOLR_DYNAMIC_TYPE_DATE );
				$field_name_year           = sprintf( '%s%s', $field_name_without_ending, WpSolrSchema::_FIELD_NAME_YEAR_I );

				// Add field to facets
				if ( ! empty( $field_name_year ) &&
				     empty( $document_for_update[ $field_name_year ] ) && // not already there
				     ! empty( $document_for_update[ $field_name ] ) && // date not empty
				     ! empty( $document_for_update[ $field_name ][0]
				     )
				) {
					// Field like '_myfield_y_i'

					$document_for_update[ $field_name_year ] = (int) date_i18n( 'Y', strtotime( $document_for_update[ $field_name ][0] ) );
				}
			}

		}

		// Add the recurring date object
		if ( $listing = \MyListing\Src\Listing::get( $post->ID ) ) {

			// loop through each listing type fields
			foreach ( $listing->get_fields() as $field ) {
				if ( ! $field ) {
					continue;
				}

				$field_extension = '';
				switch ( $field->get_type() ) {
					case 'recurring-date':
						/*
						 * Recurring dates are stored in a custom table
						 * /wp-content/themes/my-listing/includes/src/recurring-dates/recurring-dates.php
						 */
						$field_name = sprintf( '%s%s', $field->get_key(), WpSolrSchema::_SOLR_DYNAMIC_TYPE_EMBEDDED_OBJECT );

						$results = [];
						if ( $recurring_dates_def = $field->get_value() ) {
							/**
							 * $recurring_dates_def: [{"start":"2020-04-05 00:00:00","end":"2020-04-07 00:00:00","repeat":true,"frequency":1,"unit":"weeks","until":"2022-04-05"}]
							 */

							// Store all recurring dates in the future
							$upcoming_recurring_dates = \MyListing\Src\Recurring_Dates\get_upcoming_instances( $field->get_value(), 5000, 'now' );
							foreach ( $upcoming_recurring_dates as $upcoming_recurring_date ) {
								// Also, convert string to search engine date format
								$results[] = [
									'start' => $search_engine_client->search_engine_client_format_date( $upcoming_recurring_date['start'] ),
									'end'   => $search_engine_client->search_engine_client_format_date( $upcoming_recurring_date['end'] ),
								];
							}

							if ( ! empty( $results ) ) {
								$document_for_update[ $field_name ] = $results;
							}
						}
						break;
				}


			}
		}


	}

}
