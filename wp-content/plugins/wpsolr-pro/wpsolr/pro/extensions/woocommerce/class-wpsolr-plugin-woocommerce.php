<?php

namespace wpsolr\pro\extensions\woocommerce;

use WC_Product;
use WC_Product_Variation;
use WP_Post;
use WP_Term;
use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\core\classes\engines\WPSOLR_AbstractIndexClient;
use wpsolr\core\classes\engines\WPSOLR_AbstractSearchClient;
use wpsolr\core\classes\extensions\localization\OptionLocalization;
use wpsolr\core\classes\extensions\premium\WPSOLR_Option_Premium;
use wpsolr\core\classes\extensions\WPSOLR_Extension;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\ui\WPSOLR_Data_Sort;
use wpsolr\core\classes\ui\WPSOLR_Query;
use wpsolr\core\classes\utilities\WPSOLR_Help;
use wpsolr\core\classes\utilities\WPSOLR_Option;
use wpsolr\core\classes\WPSOLR_Events;
use wpsolr\core\classes\WpSolrSchema;

/**
 * Class WPSOLR_Plugin_WooCommerce
 *
 * Manage WooCommerce plugin
 */
class WPSOLR_Plugin_WooCommerce extends WPSOLR_Extension {

	// Polylang options
	const _OPTIONS_NAME = WPSOLR_Option::OPTION_EXTENSION_WOOCOMMERCE;

	// Product types
	const PRODUCT_TYPE_VARIABLE = 'variable';

	// Product category field
	const FIELD_PRODUCT_CAT_STR = 'product_cat_str';

	// Post type of orders
	const POST_TYPE_SHOP_ORDER = 'shop_order';

	// Product type
	const POST_TYPE_PRODUCT = 'product';
	const POST_TYPE_VARIATION = 'product_variation';

	// Order fields
	const FIELD_POST_DATE_DT = 'post_date_dt';
	const FIELD_ORDER_TOTAL_F = '_order_total_f';
	const CUSTOM_FIELD_ORDER_LINE_PRODUCT_TITLE = 'wpsolr_order_line_product_title';
	const CUSTOM_FIELD_ORDER_LINE_PRODUCT_TITLE_STR = 'wpsolr_order_line_product_title_str';

	// WooCommerce url parameter 'orderby'
	const WOOCOMERCE_URL_PARAMETER_SORT_BY = 'orderby';

	const ORDER_STATUS_ALL = 'all';

	// Url product category pattern.
	// Ex: /anything => /anything
	// Ex: /anything/next1/ => next1
	// Ex: /anything/next1/next2 => next2
	const URL_PATTERN_PRODUCT_CATEGORY = '/.*\/([^\/]+)$/';

	// Custom field visibility used to filter catalog or search results
	const CUSTOM_FIELD_VISIBILITY_STR = 'product_visibility_str';
	const CUSTOM_FIELD_VISIBILITY = 'product_visibility'; // taxonomy
	const VISIBILITY_CATALOG_HIDDEN = [ 'exclude-from-search', 'exclude-from-catalog' ];
	const VISIBILITY_CATALOG_EXCLUDE = [ 'exclude-from-catalog' ];
	const VISIBILITY_SEARCH_EXCLUDE = [ 'exclude-from-search' ];

	// Manage stock visibility
	const CUSTOM_FIELD_STOCK_STR = 'product_stock_str';
	const STOCK_IS_OK = 'y';
	const STOCK_IS_NOT_OK = 'n';

	// Manage on sale visibility
	const CUSTOM_FIELD_ON_SALE = 'wpsolr_on_sale';
	const CUSTOM_FIELD_ON_SALE_STR = 'wpsolr_on_sale_str';
	const ON_SALE_VALUE_OK = 'y';
	const ON_SALE_VALUE_NOT_OK = 'n';
	const ON_SALE_VALUES = [ self::ON_SALE_VALUE_OK, self::ON_SALE_VALUE_NOT_OK ];
	const WPSOLR_ON_SALE_SUBSTITUTE_STR = 'wpsolr_on_sale_%s_str'; // field storing an attribute with on sale values
	const WPSOLR_NOT_ON_SALE_SUBSTITUTE_STR = 'wpsolr_not_on_sale_%s_str'; // field storing an attribute with not on sale values


	/**
	 * Remove the parameters from the url.
	 * anything?something => anything
	 */
	const URL_PATTERN_NO_PARAMETERS = '/([^?]*)?.*/';
	/**
	 * Remove the ending slash
	 */
	const URL_PATTERN_NO_ENDING_SLASH = '/(.*)\/$/';
	/**
	 * Remove the ending /page/x.
	 * anything/page/2 => anything
	 */
	const URL_PATTERN_NO_PAGES = '/(.*)\/page\/.*/';

	const URL_PARAMETER_CUSTOMER_USER = '_customer_user';

	/*
	 * @var bool $is_replace_category_search
	 */
	protected $is_replace_category_search;

	/*
	 * @var bool is_index_downloadable_files
	 */
	protected $is_index_downloadable_files;


	/*
	 * @var string $product_term_name
	 */
	protected $product_term_name;

	/*
	 * @var string $product_taxonomy_name
	 */
	protected $product_taxonomy_name;


	/*
	 * @var string $product_term_id
	 */
	protected $product_term_id;

	/** @var array */
	protected $attributes_for_variant_image;


	/**
	 * Constructor.
	 */
	function __construct() {


		$this->attributes_for_variant_image = WPSOLR_Service_Container::getOption()->get_facets_is_show_variation_image();

		$this->init_default_events();

		add_filter( WPSOLR_Events::WPSOLR_FILTER_INCLUDE_FILE, [ $this, 'wpsolr_filter_include_file' ], 10, 1 );

		add_filter( 'woocommerce_product_get_image', [
			$this,
			'replace_product_thumbnail_with_variant_image_of_the_selected_attribute',
		], 10, 5 );


		add_filter( WPSOLR_Events::WPSOLR_FILTER_EXTRA_URL_PARAMETERS, [
			$this,
			'filter_extra_url_parameters',
		], 10, 1 );

		add_filter( WPSOLR_Events::WPSOLR_FILTER_IS_PARSE_QUERY, [
			$this,
			'filter_is_parse_query',
		], 10, 1 );

		add_action( WPSOLR_Events::WPSOLR_ACTION_SOLARIUM_QUERY, [
			$this,
			'wpsolr_action_query',
		], 10, 1 );

		add_action( WPSOLR_Events::WPSOLR_FILTER_IS_REPLACE_BY_WPSOLR_QUERY, [
			$this,
			'wpsolr_filter_is_replace_by_wpsolr_query',
		], 10, 1 );

		add_filter( WPSOLR_Events::WPSOLR_FILTER_SOLARIUM_DOCUMENT_BEFORE_UPDATE, [
			$this,
			'add_fields_to_document_before_update',
		], 10, 5 );

		// Customize the WooCOmmerce sort list-box
		add_filter( 'woocommerce_default_catalog_orderby_options', [
			$this,
			'custom_woocommerce_catalog_orderby',
		], 10 );
		add_filter( 'woocommerce_catalog_orderby', [
			$this,
			'custom_woocommerce_catalog_orderby',
		], 10 );

		add_filter( 'wc_get_template', [ $this, 'wc_get_template' ], 10, 5 );

		add_action( WPSOLR_Events::WPSOLR_FILTER_FACETS_TO_DISPLAY, [
			$this,
			'wpsolr_filter_facets_to_display',
		], 10, 1 );

		add_filter( WPSOLR_Events::WPSOLR_FILTER_INDEX_CUSTOM_FIELDS, [
			$this,
			'get_index_custom_fields',
		], 10, 2 );

		add_filter( WPSOLR_Events::WPSOLR_FILTER_FACET_ITEMS, [
			$this,
			'get_facet_items',
		], 10, 3 );

		add_filter( WPSOLR_Events::WPSOLR_FILTER_FACET_CUSTOM_DESCRIPTION, [
			$this,
			'wpsolr_filter_facet_custom_description',
		], 10, 2 );


		// Add pricing to suggestions
		add_filter( WPSOLR_Events::WPSOLR_ACTION_BEFORE_RENDER_TEMPLATE, [
			$this,
			'wpsolr_action_before_render_template',
		], 10, 1 );

		// Terms to index
		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			add_filter( WPSOLR_Events::WPSOLR_FILTER_POST_TERMS_TO_INDEX, [
				$this,
				'filter_product_terms_to_index',
			], 10, 2 );
		}

		// Clean after
		add_action( WPSOLR_Events::WPSOLR_ACTION_IS_REPLACE_BY_WPSOLR_QUERY_AFTER_POST_PRE_QUERY, [
			$this,
			'wpsolr_action_is_replace_by_wpsolr_query_after_post_pre_query',
		], 10, 0 );

		// Extract Gallery images from AI APis
		add_filter( WPSOLR_Events::WPSOLR_FILTER_AI_POST_TYPE_IMAGES_URLS, [
			$this,
			'wpsolr_filter_ai_post_type_images_urls',
		], 10, 2 );

		add_filter( WPSOLR_Events::WPSOLR_FILTER_FACET_NAME_SUBSTITUTE, [
			$this,
			'wpsolr_filter_facet_name_substitute',
		], 10, 3 );

		add_filter( WPSOLR_Events::WPSOLR_FILTER_GET_POST_ATTACHMENTS, [
			$this,
			'filter_get_post_attachments',
		], 10, 2 );

		add_filter( WPSOLR_Events::WPSOLR_FILTER_JAVASCRIPT_FRONT_LOCALIZED_PARAMETERS, [
			$this,
			'wpsolr_filter_javascript_front_localized_parameters',
		], 10, 1 );

		// Highlighting fields
		add_filter( WPSOLR_Events::WPSOLR_FILTER_HIGHLIGHTING_FIELDS, [
			$this,
			'wpsolr_filter_highlighting_fields'
		], 10, 1 );

		/**
		 * WooCommerce
		 */
		// Deactivate WooCommerce single result redirect, which breaks Ajax filters with one result
		add_filter( 'woocommerce_redirect_single_search_result', '__return_false' );

		add_filter( WPSOLR_Events::WPSOLR_FILTER_POST_CUSTOM_FIELDS, [
			$this,
			'filter_custom_fields',
		], 10, 2 );
	}

	/*
	 * Constructor
	 * Subscribe to actions
	 */

	/**
	 * Factory
	 *
	 * @return WPSOLR_Plugin_WooCommerce
	 */
	static function create() {

		return new self();
	}

	/**
	 * @inheritdoc
	 */
	protected function get_default_custom_fields() {

		$results = [];

		// Add calculated on sale field
		$results[ static::CUSTOM_FIELD_ON_SALE ] = [
			self::_FIELD_POST_TYPES                                                   => [ self::POST_TYPE_PRODUCT ],
			WPSOLR_Option::OPTION_INDEX_CUSTOM_FIELD_PROPERTY_SOLR_TYPE               => WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING,
			WPSOLR_Option::OPTION_INDEX_CUSTOM_FIELD_PROPERTY_CONVERSION_ERROR_ACTION => WPSOLR_Option::OPTION_INDEX_CUSTOM_FIELD_PROPERTY_CONVERSION_ERROR_ACTION_IGNORE_FIELD,
		];

		$results[ static::CUSTOM_FIELD_ORDER_LINE_PRODUCT_TITLE ] = [
			self::_FIELD_POST_TYPES                                                   => [ self::POST_TYPE_SHOP_ORDER ],
			WPSOLR_Option::OPTION_INDEX_CUSTOM_FIELD_PROPERTY_SOLR_TYPE               => WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING,
			WPSOLR_Option::OPTION_INDEX_CUSTOM_FIELD_PROPERTY_CONVERSION_ERROR_ACTION => WPSOLR_Option::OPTION_INDEX_CUSTOM_FIELD_PROPERTY_CONVERSION_ERROR_ACTION_IGNORE_FIELD,
		];

		return $results;
	}

	/**
	 * Switch product thumnnail with its variant image with the right color.
	 * If a variable product is found for filter color='Blue', return it's first variant image with color 'Blue' instead of the default product thumnail.
	 *
	 * @param $image_url
	 * @param $product
	 * @param $size
	 * @param $attr
	 * @param $placeholder
	 *
	 * @return string
	 */
	public function replace_product_thumbnail_with_variant_image_of_the_selected_attribute( $image_url, $product, $size, $attr, $placeholder ) {
		global /** @var WPSOLR_Query $wp_query */
		$wp_query;

		if ( ( $product instanceof \WC_Product_Variable ) &&
		     ( $wp_query instanceof WPSOLR_Query ) &&
		     ! empty( $wp_query->posts )
		) {

			$wpsolr_filters = $wp_query->get_filter_query_fields_group_by_name();

			if ( ! empty( $wpsolr_filters ) && ! empty( $this->attributes_for_variant_image ) ) {

				/*
				 * Loop on attributes selected for showing variant images
				 */
				foreach ( $this->attributes_for_variant_image as $attribute_for_variant_image => $no_matter ) {

					/*
					 * Check that the attribute is a selected filter
					 */
					if ( ! empty( $wpsolr_filters[ $attribute_for_variant_image ] ) ) {

						$colors = $wpsolr_filters[ $attribute_for_variant_image ];
						foreach ( $colors as $color_selected ) {

							foreach ( $product->get_children() as $child_id ) {

								$variation = wc_get_product( $child_id );

								if ( false !== $variation ) {

									// pa_color_str => pa_color
									$color = $variation->get_attribute( WpSolrSchema::get_field_without_str_ending( $attribute_for_variant_image ) );

									/*
									 * The selected attribute value is the variant attribute value also: use the variation image
									 */
									if ( ! empty( $color ) && ( $color_selected === $color ) && ! empty( $variation->get_image_id() ) ) {

										$variation_image_url = wp_get_attachment_image( $variation->get_image_id(), $size, false, $attr );

										if ( ! empty( $variation_image_url ) ) {

											$image_url = $variation_image_url;

											// Found the variation image for the color. Stop now.
											return $image_url;
										}

									}
								}
							}
						}
					}

				}


			}

		}

		return $image_url;
	}

	/**
	 * Return all woo commerce attributes names (slugs)
	 *
	 * @param string[] $custom_fields
	 * @param string $model_type
	 *
	 * @return array
	 */
	public function get_index_custom_fields( $custom_fields, $model_type ) {

		if ( self::POST_TYPE_PRODUCT === $model_type ) {

			if ( ! isset( $custom_fields ) ) {
				$custom_fields = [];
			}

			/* Attributes are now taxonomies. No need to add them to custom fields selection. */
			foreach ( $custom_fields as $key => $custom_field_name ) {

				// Remove custom fields which are attributes.
				if ( $this->get_container()->get_service_wpsolr()->starts_with( $custom_field_name, 'attribute_pa_' ) ) {
					unset( $custom_fields[ $key ] );
				}
			}
		}

		return $custom_fields;
	}

	/**
	 * Return all woo commerce attribute values
	 * @return array
	 */
	public function get_facet_items( $attributes_values, $field_name, $facet_name ) {

		if ( ! isset( $field_name ) ) {
			return $attributes_values;
		}

		if ( in_array( sprintf( 'pa_%s', $field_name ), wc_get_attribute_taxonomy_names(), true ) ) {
			foreach ( get_terms( [ 'taxonomy' => sprintf( 'pa_%s', $field_name ), 'fields' => 'names' ] ) as $term ) {
				array_push( $attributes_values, $term );
			};
		}

		switch ( $field_name ) {
			case static::CUSTOM_FIELD_ON_SALE:
				// This field is calculated and indexed (not stored in post metas).
				$attributes_values = static::ON_SALE_VALUES;
				break;
		}

		return $attributes_values;
	}

	/**
	 * Return all woo commerce attributes
	 * @return array
	 */
	public function get_attribute_taxonomies() {

		// Standard woo function
		return wc_get_attribute_taxonomies();
	}


	/**
	 * @return bool
	 */
	public function get_is_category_search() {

		if ( isset( $this->is_replace_category_search ) ) {
			// Use cached value.
			return $this->is_replace_category_search;
		}

		$this->is_replace_category_search = ( WPSOLR_Service_Container::getOption()->get_option_plugin_woocommerce_is_replace_product_category_search() && $this->is_product_category_url() );

		return $this->is_replace_category_search;
	}

	/**
	 * @return bool
	 */
	public function get_is_index_downloadable_files() {

		if ( isset( $this->is_index_downloadable_files ) ) {
			// Use cached value.
			return $this->is_index_downloadable_files;
		}

		$this->is_index_downloadable_files = ( WPSOLR_Service_Container::getOption()->get_option_plugin_woocommerce_is_index_downloadable_files() );

		return $this->is_index_downloadable_files;
	}

	/**
	 * Extract product category from url.
	 * Must be done because is_product_category() does not work at this early stage.
	 *
	 * @return bool
	 */
	public function is_product_category_url() {

		if ( is_admin() || ! is_main_query() ) {
			return false;
		}

		$url = $_SERVER['REQUEST_URI'];

		if ( false !== strpos( $url, '.php' ) ) {
			// Ajax or cron
			return false;
		}

		if ( is_shop() ) {
			return true;
		}

		if ( is_product_taxonomy() ) {
			// Extract product get_
			$product_taxonomy = get_queried_object();
			if ( $product_taxonomy ) {
				$this->product_taxonomy_name = $product_taxonomy->taxonomy;
				$this->product_term_name     = $product_taxonomy->name;
				$this->product_term_id       = $product_taxonomy->term_id;

				return true;
			}

		}

		return false;
	}

	/**
	 *
	 * Replace WP query by a WPSOLR query when the current WP Query is an order type query.
	 *
	 * @param bool $is_replace_by_wpsolr_query
	 *
	 * @return bool
	 */
	public function wpsolr_filter_is_replace_by_wpsolr_query( $is_replace_by_wpsolr_query ) {
		global $wp_query;

		// A category page
		if ( ( $this->get_is_category_search() )
		     && WPSOLR_Service_Container::getOption()->get_search_is_replace_default_wp_search()
		     && WPSOLR_Service_Container::getOption()->get_search_is_use_current_theme_search_template()
		) {
			return true;
		}

		if ( is_admin() && WPSOLR_Service_Container::getOption()->get_option_plugin_woocommerce_is_replace_admin_orders_search() ) {

			// ) && ! empty( $_REQUEST['s']
			if ( $this->_is_admin_post_edit() && ! empty( $_REQUEST['post_type'] ) && ( self::POST_TYPE_SHOP_ORDER === $_REQUEST['post_type'] ) ) {
				// This is an order query, in the admin.
				return true;
			}
		}

		return $is_replace_by_wpsolr_query;
	}


	/**
	 * Add extra parameters to SEO redirect url.
	 *
	 * @param array $url_parameters
	 *
	 * @return array
	 */
	public function filter_extra_url_parameters( $url_parameters = [] ) {

		// Required by themes to show results as product results.
		$url_parameters['post_type'] = self::POST_TYPE_PRODUCT;

		return $url_parameters;
	}

	/**
	 * Do not execute parse_query() on wpsolr_query for orders. Too slow when a lot of orders metas are there.
	 *
	 * @param $true
	 *
	 * @return bool
	 */
	public function filter_is_parse_query( $true ) {

		if ( is_admin() && WPSOLR_Service_Container::getOption()->get_option_plugin_woocommerce_is_replace_admin_orders_search() ) {

			if ( $this->_is_admin_post_edit() && ! empty( $_REQUEST['post_type'] ) && ( self::POST_TYPE_SHOP_ORDER === $_REQUEST['post_type'] ) ) {
				// This is an order query, in the admin. Do not execute parse_query(), as it is deadly slow (heavy joins on metas in shop_order_search_custom_fields()).
				return false;
			}
		}

		return $true;
	}

	/**
	 *
	 * Add a filter on order post type.
	 *
	 * @param array $parameters
	 *
	 */
	public function wpsolr_action_query( $parameters ) {

		if ( ! $this->is_active_on_current_view() ) {
			return;
		}

		/* @var WPSOLR_Query $wpsolr_query */
		$wpsolr_query = $parameters[ WPSOLR_Events::WPSOLR_ACTION_SOLARIUM_QUERY__PARAM_WPSOLR_QUERY ];
		/* @var mixed $search_engine_query */
		$search_engine_query = $parameters[ WPSOLR_Events::WPSOLR_ACTION_SOLARIUM_QUERY__PARAM_SOLARIUM_QUERY ];
		/* @var WPSOLR_AbstractSearchClient $search_engine_client */
		$search_engine_client = $parameters[ WPSOLR_Events::WPSOLR_ACTION_SOLARIUM_QUERY__PARAM_SOLARIUM_CLIENT ];

		// WooCommerce 3.3.x fix - Else no results shown in /shop
		$wpsolr_query->set( "wc_query", true );

		$is_admin = is_admin();

		if ( $is_admin && WPSOLR_Service_Container::getOption()->get_option_plugin_woocommerce_is_replace_admin_orders_search() ) {
			if ( ! empty( $wpsolr_query->query['post_type'] ) && ( self::POST_TYPE_SHOP_ORDER === $wpsolr_query->query['post_type'] ) ) {

				// sort by
				$wpsolr_order_by_mapping_fields = [
					'ID'          => 'PID',
					'date'        => self::FIELD_POST_DATE_DT,
					'order_total' => self::FIELD_ORDER_TOTAL_F,
				];
				$original_order_by              = ! empty( $_GET['orderby'] ) ? $_GET['orderby'] : 'post_date';
				$orderby                        = ! empty( $wpsolr_order_by_mapping_fields[ $original_order_by ] ) ? $wpsolr_order_by_mapping_fields[ $original_order_by ] : self::FIELD_POST_DATE_DT;
				$order                          = ( empty( $_GET['order'] ) || ( 'desc' === $_GET['order'] ) ) ? WpSolrSchema::SORT_DESC : WpSolrSchema::SORT_ASC;
				$search_engine_client->search_engine_client_add_sort( $orderby, $order );

				// Filter by order status
				$order_status = ! empty( $_GET['post_status'] ) ? $_GET['post_status'] : '';
				if ( ! empty( $order_status ) && ( self::ORDER_STATUS_ALL !== $order_status ) ) {
					$search_engine_client->search_engine_client_add_filter_term( 'post_status', WpSolrSchema::_FIELD_NAME_STATUS_S, false, $order_status );
				}

				// Filter by customer id
				$customer_id = ! empty( $_GET[ self::URL_PARAMETER_CUSTOMER_USER ] ) ? $_GET[ self::URL_PARAMETER_CUSTOMER_USER ] : '';
				if ( ! empty( $customer_id ) ) {
					$search_engine_client->search_engine_client_add_filter_term(
						self::URL_PARAMETER_CUSTOMER_USER, self::URL_PARAMETER_CUSTOMER_USER . WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING, false, $customer_id
					);
				}
			}
		} elseif ( is_search() && WPSOLR_Service_Container::getOption()->get_option_plugin_woocommerce_is_replace_admin_orders_search() ) {
			// search page on front-end, filter out orders from results.

			$search_engine_client->search_engine_client_add_filter_not_in_terms(
				sprintf( '-type:%s', self::POST_TYPE_SHOP_ORDER ), WpSolrSchema::_FIELD_NAME_TYPE, [ self::POST_TYPE_SHOP_ORDER ]
			);
		}

		// Add category filter on category pages
		if ( $this->get_is_category_search() && ! empty( $this->product_term_name ) ) {

			$filter_query_field_name = $search_engine_client->get_facet_hierarchy_name( WpSolrSchema::_FIELD_NAME_NON_FLAT_HIERARCHY, $this->product_taxonomy_name . WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING );

			$search_engine_client->search_engine_client_add_filter_term(
				sprintf( 'woocommerce %s:"%s"', $filter_query_field_name, $this->product_term_name ),
				$filter_query_field_name,
				false,
				$search_engine_client->search_engine_client_escape_term( $this->product_term_name )
			);

			// Add filter on search visibility
			$this->_search_engine_client_add_filter_visibility( $search_engine_client, self::VISIBILITY_CATALOG_EXCLUDE );


		} else {

			// Add filter on search visibility
			$this->_search_engine_client_add_filter_visibility( $search_engine_client, self::VISIBILITY_SEARCH_EXCLUDE );

		}

		// Stock filter
		$is_search_admin_ajax = WPSOLR_Option_Premium::get_is_search_admin();
		if ( ( ! $this->_is_admin_post_edit() && ! $is_search_admin_ajax ) && ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) ) {

			// Add filter on _visibility
			$search_engine_client->search_engine_client_add_filter(
				'stock visibility',
				$search_engine_client->search_engine_client_create_or_betwwen_not_and_in_terms(
					[ WpSolrSchema::_FIELD_NAME_TYPE, self::CUSTOM_FIELD_STOCK_STR ],
					[
						[ self::POST_TYPE_PRODUCT ],
						[ self::STOCK_IS_OK ],
					]
				)
			);
		}
	}

	/**
	 * Returns a single product attribute.
	 * We use this instead of standard wc get_attribute(), because it return a string whatever the attribute type (text or select):
	 * color_array => 'red, green'
	 * brand_text => 'Texas, CO.'
	 *
	 * But we then need to create an array with explode:
	 * color_array => array('red, green') // ok
	 * brand_text => array('Texas, CO.') // wrong, brand is split in 2 by the comma
	 *
	 * @param \WC_Product $product
	 * @param array $attribute
	 *
	 * @return array
	 * @internal param array $attr
	 */
	public function get_attribute( $product, $attribute ) {

		if ( isset( $attribute['is_taxonomy'] ) && $attribute['is_taxonomy'] ) {

			return wc_get_product_terms( $product->get_id(), $attribute['name'], [ 'fields' => 'names' ] );

		} else {

			return explode( '|', $attribute['value'] );
		}

	}

	/**
	 * Add fields to a document
	 *
	 * @param array $document_for_update
	 * @param $solr_indexing_options
	 * @param $post
	 * @param $attachment_body
	 * @param WPSOLR_AbstractIndexClient $search_engine_client
	 *
	 * @return array Document updated with fields
	 */
	function add_fields_to_document_before_update( array $document_for_update, $solr_indexing_options, $post, $attachment_body, WPSOLR_AbstractIndexClient $search_engine_client ) {

		switch ( $post->post_type ) {

			case self::POST_TYPE_SHOP_ORDER:

				// add order post_date for sorting
				if ( ! empty( $post->post_date ) ) {
					$field_name                         = self::FIELD_POST_DATE_DT;
					$document_for_update[ $field_name ] = $search_engine_client->search_engine_client_format_date( $post->post_date );

				}
				break;

			case self::POST_TYPE_PRODUCT:

				// Add visibility.
				$product_visibility = get_post_custom_values( self::CUSTOM_FIELD_VISIBILITY, $post->ID );
				if ( $search_engine_client->get_has_exists_filter() ) {
					if ( ! empty( $product_visibility ) ) {
						$document_for_update[ self::CUSTOM_FIELD_VISIBILITY_STR ] = $product_visibility;
					}
				} else {
					$document_for_update[ self::CUSTOM_FIELD_VISIBILITY_STR ] = ! empty( $product_visibility ) ? $product_visibility : WPSOLR_AbstractEngineClient::FIELD_VALUE_UNDEFINED;
				}

				if ( $product = wc_get_product( $post->ID ) ) {

					// Add product stock
					$document_for_update[ self::CUSTOM_FIELD_STOCK_STR ] =
						$product->is_in_stock() ? self::STOCK_IS_OK : self::STOCK_IS_NOT_OK;

					/**
					 * On sale, only if in sale field is indexed
					 */
					if ( ! empty( $solr_indexing_options ) &&
					     ! empty( $solr_indexing_options['cust_fields'] ) &&
					     ! empty( $solr_indexing_options['cust_fields']['product'] ) &&
					     ! empty( $solr_indexing_options['cust_fields']['product'][ static::CUSTOM_FIELD_ON_SALE_STR ] )
					) {

						// "On sale" status
						$document_for_update[ self::CUSTOM_FIELD_ON_SALE_STR ] = $product->is_on_sale() ? self::ON_SALE_VALUE_OK : self::ON_SALE_VALUE_NOT_OK;

						// Update also post meta to be able to set on sale values visible/invisible on filter item settings
						update_post_meta( $post->ID, self::CUSTOM_FIELD_ON_SALE, $document_for_update[ self::CUSTOM_FIELD_ON_SALE_STR ] );

						// For each attribute, fill a new "on sale" taxonomy that will be displayed when the facet "on sale" is selected
						foreach ( $product->get_children() as $child_id ) {
							/** @var \WC_Product_Variation $variation */
							$variation = wc_get_product( $child_id );
							if ( false !== $variation ) {
								foreach ( $variation->get_attributes() as $attribute_name => $attribute_value ) {
									$on_sale_attribute_name = $this->get_on_sale_attribute_name( $search_engine_client, $attribute_name, $variation->is_on_sale() );
									$attribute_name         = get_term_by( 'name', $attribute_value, $attribute_name )->name;
									if ( ( empty( $document_for_update[ $on_sale_attribute_name ] ) || ( false === array_search( $attribute_value, $document_for_update[ $on_sale_attribute_name ], true ) ) )
									) {
										$document_for_update[ $on_sale_attribute_name ][] = $attribute_name;
									}
								}
							}
						}
					}

				}
				break;
		}

		return $document_for_update;
	}

	/**
	 * Replace WooCommerce sort list with WPSOLR sort list
	 *
	 * @param $located
	 * @param $template_name
	 * @param $args
	 * @param $template_path
	 * @param $default_path
	 *
	 * @return mixed
	 */
	function wc_get_template( $located, $template_name, $args, $template_path, $default_path ) {

		if ( WPSOLR_Service_Container::getOption()->get_option_plugin_woocommerce_is_replace_sort_items() ) {
			// Replace template to prevent insertion of default 'Relevance'
			switch ( $template_name ) {
				case 'loop/orderby.php':
					$located = sprintf( '%s%s%s', plugin_dir_path( __FILE__ ), 'templates/', $template_name );
					break;
			}
		}

		return $located;
	}


	/**
	 * Replace WooCommerce sort list with WPSOLR sort list
	 *
	 * @param array $sortby
	 *
	 * @return array
	 */
	function custom_woocommerce_catalog_orderby( $sortby ) {
		global $wp_query;

		if ( ! WPSOLR_Service_Container::getOption()->get_option_plugin_woocommerce_is_replace_sort_items() ) {
			// Use standard WooCommerce sort items.
			return $sortby;
		}

		$results = [];

		// Retrieve WPSOLR sort fields, with their translations.
		$sorts = WPSOLR_Data_Sort::get_data(
			WPSOLR_Service_Container::getOption()->get_sortby_items_as_array(),
			WPSOLR_Service_Container::getOption()->get_sortby_items_labels(),
			WPSOLR_Service_Container::get_query()->get_wpsolr_sort(),
			OptionLocalization::get_options()
		);

		if ( ! empty( $sorts ) && ! empty( $sorts['items'] ) ) {
			foreach ( $sorts['items'] as $sort_item ) {
				$results[ $sort_item['id'] ] = $sort_item['name'];
			}
		}

		return $results;
	}

	/**
	 * Remove product category of facets to display if we are on a category page.
	 *
	 * @param array $facets_to_display ['type', 'categories', 'product_cat_str']
	 *
	 * @return array
	 */
	public function wpsolr_filter_facets_to_display( array $facets_to_display ) {

		if ( $this->get_is_category_search() ) {
			$index = array_search( self::FIELD_PRODUCT_CAT_STR, $facets_to_display, true );
			if ( false !== $index ) {
				//unset( $facets_to_display[ $index ] );
			}
		}

		return $facets_to_display;
	}

	/**
	 * Include the file containing the help feature.
	 *
	 * @param int $help_id
	 *
	 * @return string File name & path
	 */
	public function wpsolr_filter_include_file( $help_id ) {

		$file_name = '';

		switch ( $help_id ) {

			case WPSOLR_Help::HELP_FACET_SHOW_WOOCOMMERCE_VARIATION_IMAGE:
				$file_name = 'facet-woocommerce-show-variation-image.inc.php';
				break;
		}

		$result = ! empty( $file_name ) ? sprintf( '%s/includes/%s', dirname( __FILE__ ), $file_name ) : $help_id;

		return $result;
	}

	/**
	 * Add pricing to content grouped suggestions
	 *
	 * @param array $template_params
	 *
	 * @return array
	 */
	public function wpsolr_action_before_render_template( $template_params ) {

		$is_suggestion     = isset( $template_params['template_data']['settings'][ WPSOLR_Option::OPTION_SUGGESTION_UUID ] );
		$is_recommendation = isset( $template_params['template_data']['settings'][ WPSOLR_Option::OPTION_RECOMMENDATION_UUID ] );

		if ( isset( $template_params['template_data']['settings'] ) &&
		     isset( $template_params['template_data']['settings']['type'] ) &&
		     ( $is_recommendation || ( $is_suggestion && in_array( $template_params['template_data']['settings']['type'], [
					     WPSOLR_Option::OPTION_SEARCH_SUGGEST_CONTENT_TYPE_CONTENT,
					     WPSOLR_Option::OPTION_SEARCH_SUGGEST_CONTENT_TYPE_CONTENT_GROUPED,
					     WPSOLR_Option::OPTION_SEARCH_SUGGEST_CONTENT_TYPE_QUESTIONS_ANSWERS,
				     ] ) ) )
		) {

			// This is a content or content grouped suggestions type
			$template_type = $template_params['template_data']['settings']['type'];

			foreach ( $template_params['template_data']['results'] as $indice => &$post_type_result ) {

				if ( $is_suggestion ) {
					$post_type = ( WPSOLR_Option::OPTION_SEARCH_SUGGEST_CONTENT_TYPE_CONTENT_GROUPED === $template_type )
						? $indice
						: $post_type_result[ wpsolrschema::_FIELD_NAME_TYPE ];
				} else {
					$post_type = $post_type_result[ wpsolrschema::_FIELD_NAME_TYPE ];
				}

				if ( isset( $template_params['template_data']['settings'][ WPSOLR_Option::OPTION_SUGGESTION_MODELS ] ) &&
				     isset( $template_params['template_data']['settings'][ WPSOLR_Option::OPTION_SUGGESTION_MODELS ][ $post_type ] )
				) {
					$is_show_rating      = isset( $template_params['template_data']['settings'][ WPSOLR_Option::OPTION_SUGGESTION_MODELS ][ $post_type ][ WPSOLR_Option::OPTION_SUGGESTION_MODEL_PRODUCT_IS_SHOW_RATING ] );
					$is_show_price       = isset( $template_params['template_data']['settings'][ WPSOLR_Option::OPTION_SUGGESTION_MODELS ][ $post_type ][ WPSOLR_Option::OPTION_SUGGESTION_MODEL_PRODUCT_IS_SHOW_PRICE ] );
					$is_show_add_to_cart = isset( $template_params['template_data']['settings'][ WPSOLR_Option::OPTION_SUGGESTION_MODELS ][ $post_type ][ WPSOLR_Option::OPTION_SUGGESTION_MODEL_PRODUCT_IS_SHOW_ADD_TO_CART ] );

					switch ( $post_type ) {
						case self::POST_TYPE_PRODUCT:
						case self::POST_TYPE_VARIATION:

							if ( $is_suggestion ) {
								switch ( $template_type ) {
									case WPSOLR_Option::OPTION_SEARCH_SUGGEST_CONTENT_TYPE_CONTENT_GROUPED:
										foreach ( $post_type_result['items'] as &$item ) {
											$this->_add_product_infos_to_suggestions( $item, $is_show_rating, $is_show_price, $is_show_add_to_cart );
										}
										break;

									case WPSOLR_Option::OPTION_SEARCH_SUGGEST_CONTENT_TYPE_CONTENT:
									case WPSOLR_Option::OPTION_SEARCH_SUGGEST_CONTENT_TYPE_QUESTIONS_ANSWERS:
										$this->_add_product_infos_to_suggestions( $post_type_result, $is_show_rating, $is_show_price, $is_show_add_to_cart );
										break;
								}

							} else {
								$this->_add_product_infos_to_suggestions( $post_type_result, $is_show_rating, $is_show_price, $is_show_add_to_cart );
							}
							break;
					}
				}
			}

		}

		return $template_params;
	}

	/**
	 * Return terms valid for indexing
	 *
	 * @param WP_Term[] $terms
	 * @param array $params
	 *
	 * @return string[]
	 */
	public function filter_product_terms_to_index( $terms, $params ) {

		$results = [];

		/** @var WP_Post $post */
		$post = $params['post'];
		/** @var string taxonomy_name */
		$taxonomy_name = $params['taxonomy_name'];

		if ( self::POST_TYPE_PRODUCT === $post->post_type ) {
			$product = wc_get_product( $post->ID );

			if ( $product->is_type( 'variable' ) ) {
				// Do not index attribute values for variable products that have no variants in stock for that attribute
				// Example: do not show facet 'red' if no variations in stock have that color attribute.

				foreach ( ( ! $terms ? [] : $terms ) as $term ) {
					// Each term which that is an attribute must be in a variation stocked

					if ( ! $product->get_attribute( $taxonomy_name ) ) {
						// Not an attribute: this term can be indexed
						$results[] = $term;

					} else {

						foreach ( $product->get_children() as $child_id ) {

							/** @var \WC_Product_Variation $variation */
							$variation = wc_get_product( $child_id );

							if ( ( false !== $variation ) && $variation->is_in_stock() ) {

								$attribute = $variation->get_attribute( $taxonomy_name );

								if ( ! empty( $attribute ) && ( $attribute === $term->name ) ) {
									$results[] = $term;
									break;
								}
							}
						}

					}
				}

			} else {
				// Not a variable: all terms can be indexed
				$results = $terms;
			}

		} else {
			// Not a product: all terms can be indexed
			$results = $terms;
		}

		// Default terms.
		return $results;
	}

	/**
	 * Clean the mess after 'posts_pre_query'
	 */
	public function wpsolr_action_is_replace_by_wpsolr_query_after_post_pre_query() {
		// Necessary from WC 4.4.1, or results are not shown at all
		// https://www.wpsolr.com/forums/topic/woocommerce-archive-pages-not-showing-products/

		if ( isset( $GLOBALS['woocommerce_loop'] ) ) {
			unset( $GLOBALS['woocommerce_loop'] );
		}
	}

	/**
	 * @param string[] $images_urls
	 * @param int $post_id
	 *
	 * @return string[]
	 */
	public function wpsolr_filter_ai_post_type_images_urls( $images_urls, $post_id ) {

		$product = wc_get_product( $post_id );

		if ( $product ) {

			$attachment_ids = $product->get_gallery_image_ids();
			foreach ( $attachment_ids as $attachment_id ) {
				// Add the image URL
				$images_urls[] = wp_get_attachment_url( $attachment_id );
			}
		}

		return $images_urls;
	}

	/**
	 * @param WC_Product_Variation|WC_Product $object
	 * @param WPSOLR_AbstractIndexClient $search_engine_client
	 * @param array $on_sale_dates
	 */
	protected function _add_sale_dates( $object, $search_engine_client, &$on_sale_dates ) {
		$date_on_sale_from = $object->get_date_on_sale_from();
		$date_on_sale_to   = $object->get_date_on_sale_to();

		$on_sale_date = [];
		if ( ! empty( $date_on_sale_from ) ) {
			$on_sale_date['from_dt'] = $search_engine_client->search_engine_client_format_date( $date_on_sale_from->getTimestamp() );
		}
		if ( ! empty( $date_on_sale_to ) ) {
			$on_sale_date['to_dt'] = $search_engine_client->search_engine_client_format_date( $date_on_sale_from->modify( '+1 day' )->getTimestamp() );
		}

		if ( ! empty( $on_sale_date ) ) {
			$on_sale_dates[] = $on_sale_date;
		}
	}


	/**
	 * Replace facet names with their on sale facet names
	 *
	 * @param string $facet_name
	 * @param WPSOLR_Query $wpsolr_query
	 * @param WPSOLR_AbstractSearchClient $search_engine_client
	 *
	 * @return string
	 */
	function wpsolr_filter_facet_name_substitute( string $facet_name, WPSOLR_Query $wpsolr_query, WPSOLR_AbstractSearchClient $search_engine_client ) {

		$filters = $wpsolr_query->get_filter_query_fields_group_by_name();
		if ( ! empty( $filters[ static::CUSTOM_FIELD_ON_SALE_STR ] ) &&
		     ( count( $filters[ static::CUSTOM_FIELD_ON_SALE_STR ] ) == 1 )
		) {
			// the on sale filter is activated (on sale or not on sale, but not both), replace the attribute results with the substitute results
			$on_sale = $filters[ static::CUSTOM_FIELD_ON_SALE_STR ][0];

			if ( in_array( $on_sale, static::ON_SALE_VALUES ) && in_array( WpSolrSchema::get_field_without_str_ending( $facet_name ), wc_get_attribute_taxonomy_names(), true ) ) {
				$facet_name = $this->get_on_sale_attribute_name( $search_engine_client, WpSolrSchema::get_field_without_str_ending( $facet_name ),
					( static::ON_SALE_VALUE_OK === $on_sale ) );
			}

		}


		return $facet_name;
	}

	/**
	 * Replace facet names with their on sale facet names
	 *
	 * @param string $description
	 * @param string $facet_name
	 *
	 * @return string
	 */
	function wpsolr_filter_facet_custom_description( string $description, string $facet_name ) {

		if ( static::CUSTOM_FIELD_ON_SALE_STR === $facet_name ) {
			$description = <<<'TAG'
<p class="wpsolr_err">To be accurate, this facet requires a full reindexing of your products every day if your sale prices use sale dates.<br>
You can use the <a href="admin.php?page=solr_settings&tab=solr_plugins&subtab=extension_cron_opt" target="_wpsolr_cron">Cron extension</a> to schedule your reindexing.</p>
TAG;

		}

		return $description;
	}


	/**
	 * @param WPSOLR_AbstractEngineClient $search_engine_client
	 * @param string $attribute_name
	 * @param bool $is_on_sale
	 *
	 * @return string
	 */
	protected function get_on_sale_attribute_name( WPSOLR_AbstractEngineClient $search_engine_client, string $attribute_name, bool $is_on_sale ) {
		return $search_engine_client->get_facet_hierarchy_name( WpSolrSchema::_FIELD_NAME_NON_FLAT_HIERARCHY,
			sprintf( $is_on_sale ? self::WPSOLR_ON_SALE_SUBSTITUTE_STR : self::WPSOLR_NOT_ON_SALE_SUBSTITUTE_STR, $attribute_name )
		);
	}

	/**
	 * Retrieve embedded urls in the post shortcodes
	 *
	 * @param array $attachments
	 * @param string $post
	 *
	 * @return array
	 */
	public function filter_get_post_attachments( $attachments, $post_id ) {

		/** @var WC_Product $product */
		if ( $this->get_is_index_downloadable_files() &&
		     ( $product = wc_get_product( $post_id ) ) &&
		     $product->is_downloadable()
		) {

			foreach ( $product->get_downloads() as $file_id => $file ) {
				array_push( $attachments, [ 'url' => $file['file'] ] );
			}

		}

		return $attachments;
	}

	/**
	 * Add suggestions options
	 *
	 * @param array $parameters
	 *
	 * @return array
	 */
	public function wpsolr_filter_javascript_front_localized_parameters( $parameters ) {

		// Prevent issue with Flatsome (and probably other theme's) mobile Hamburger menu which can not be opened after a first click on facets
		if ( empty( $parameters['data']['css_ajax_container_page_title'] ) ) {
			$parameters['data']['css_ajax_container_page_title'] = '.woocommerce-result-count';
		}

		return $parameters;
	}

	/**
	 * @param array $fields
	 *
	 * @return mixed
	 */
	public function wpsolr_filter_highlighting_fields( array $fields ) {
		// Only hightlight title for WooCommerce
		return [ WpSolrSchema::_FIELD_NAME_TITLE ];
	}

	/**
	 * @return bool
	 */
	protected function _is_admin_post_edit(): bool {
		return ( false !== strpos( $_SERVER['REQUEST_URI'], '/wp-admin/edit.php' ) );
	}

	/**
	 * @param array $suggestion_data
	 * @param bool $is_show_price
	 * @param bool $is_show_add_to_cart
	 */
	protected function _add_product_infos_to_suggestions( array &$suggestion_data, bool $is_show_rating, bool $is_show_price, bool $is_show_add_to_cart ): void {
		global $product;
		if ( $product = wc_get_product( $suggestion_data[ WpSolrSchema::_FIELD_NAME_PID ] ) ) {

			if ( $is_show_rating ) {
				// Add rating
				ob_start();
				woocommerce_template_loop_rating();
				$suggestion_data['subtitles']['product_rating_html'] = ob_get_contents();
				ob_end_clean();
			}

			if ( $is_show_price ) {
				// Add price
				$suggestion_data['subtitles']['product_price_html'] = $product->get_price_html();
			}

			if ( $is_show_add_to_cart ) {
				// Add to cart
				ob_start();
				woocommerce_template_loop_add_to_cart();
				$suggestion_data['subtitles']['product_add_to_cart_html'] = ob_get_contents();
				ob_end_clean();
			}


		}
	}

	/**
	 * Add visibility filter
	 *
	 * @param WPSOLR_AbstractSearchClient $search_engine_client
	 * @param string[] $visibility_types
	 */
	protected function _search_engine_client_add_filter_visibility( WPSOLR_AbstractSearchClient $search_engine_client, array $visibility_types ): void {

		$search_engine_client->search_engine_client_add_filter(
			sprintf( 'woocommerce %s visibility', implode( ', ', $visibility_types ) ),
			$search_engine_client->search_engine_client_create_or(
				[
					$search_engine_client->search_engine_client_create_filter_not_in_terms(
						WpSolrSchema::_FIELD_NAME_TYPE,
						[ self::POST_TYPE_PRODUCT ]
					),
					$search_engine_client->search_engine_client_create_or(
						[
							$search_engine_client->search_engine_client_create_filter_no_values(
								self::CUSTOM_FIELD_VISIBILITY_STR
							),
							$search_engine_client->search_engine_client_create_filter_not_in_terms(
								self::CUSTOM_FIELD_VISIBILITY_STR,
								$visibility_types
							)
						]
					)
				]
			)
		);
	}

	/**
	 * Decode acf values before indexing.
	 * Get all field values, recursively in containers if necessary, which are not containers, and not files.
	 * Files are treated in attachments code.
	 *
	 * @param $custom_fields
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public
	function filter_custom_fields(
		$custom_fields, $post_id
	) {

		if ( $post = get_post( $post_id ) ) {
			switch ( $post->post_type ) {

				case self::POST_TYPE_SHOP_ORDER:

					if ( $order = wc_get_order( $post->ID ) ) {
						/**
						 * Add order line infos field is indexed
						 */
						$solr_indexing_options = WPSOLR_Service_Container::getOption()->get_option_index_custom_fields();
						if ( ! empty( $solr_indexing_options ) &&
						     ! empty( $solr_indexing_options['shop_order'] ) &&
						     in_array( static::CUSTOM_FIELD_ORDER_LINE_PRODUCT_TITLE_STR, $solr_indexing_options['shop_order'] )
						) {

							/* Add product titles */
							$order_item_product_titles = [];
							foreach ( $order->get_items() as $order_item ) {
								$order_item_product_titles[] = $order_item->get_name();
							}
							$custom_fields[ self::CUSTOM_FIELD_ORDER_LINE_PRODUCT_TITLE ] = $order_item_product_titles;
						}
					}

					break;
			}
		}

		return $custom_fields;
	}

}
