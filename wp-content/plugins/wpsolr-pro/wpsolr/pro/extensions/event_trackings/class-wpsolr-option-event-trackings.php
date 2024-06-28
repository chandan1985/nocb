<?php

namespace wpsolr\pro\extensions\event_trackings;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\core\classes\extensions\indexes\WPSOLR_Option_Indexes;
use wpsolr\core\classes\extensions\view\WPSOLR_Option_View;
use wpsolr\core\classes\extensions\WPSOLR_Extension;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\ui\WPSOLR_Query;
use wpsolr\core\classes\ui\WPSOLR_Query_Parameters;
use wpsolr\core\classes\utilities\WPSOLR_Option;
use wpsolr\core\classes\WPSOLR_Events;

class WPSOLR_Option_Event_Trackings extends WPSOLR_Extension {

	/**
	 * Build class from uuid
	 */
	const EVENT_TRACKING_CLASS_PATTERN = 'c%s';

	/**
	 * List of engines supporting events tracking
	 */
	const ENGINE_TRACKING_EVENTS_DEFINITIONS = [
		WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL => [],
		WPSOLR_AbstractEngineClient::ENGINE_ALGOLIA       => [],
	];


	/**
	 * Constructor
	 * Subscribe to actions
	 */

	function __construct() {

		add_filter( WPSOLR_Events::WPSOLR_FILTER_JAVASCRIPT_FRONT_LOCALIZED_PARAMETERS, [
			$this,
			'wpsolr_filter_javascript_front_localized_parameters',
		], 10, 1 );
	}

	/**
	 * Is the current view's index search engine supporting events?
	 * @return bool
	 */
	public static function get_is_view_supported(): bool {
		$option_index = ( new WPSOLR_Option_Indexes() );

		return isset( static::ENGINE_TRACKING_EVENTS_DEFINITIONS[ $option_index->get_index_search_engine( $option_index->get_index( WPSOLR_Option_View::get_current_index_uuid() ) ) ] );
	}

	/**
	 * Add suggestions options
	 *
	 * @param array $parameters
	 *
	 * @return array
	 */
	public function wpsolr_filter_javascript_front_localized_parameters( $parameters ) {
		global $wp_query;

		$parameters['data']['wpsolr_autocomplete_is_active']      = ! WPSOLR_Service_Container::getOption()->get_cross_domain_is_galaxy_slave();
		$parameters['data']['wpsolr_autocomplete_selector']       = $this->get_active_suggestions_js_options();
		$parameters['data']['wpsolr_autocomplete_action']         = WPSOLR_AJAX_AUTO_COMPLETE_ACTION;
		$parameters['data']['wpsolr_autocomplete_nonce_selector'] = ( '#' . WPSOLR_AUTO_COMPLETE_NONCE_SELECTOR );
		$parameters['data']['wpsolr_is_search_admin']             = ( $wp_query instanceof WPSOLR_Query ) && $wp_query->wpsolr_get_is_admin();

		return $parameters;
	}

	/**
	 * Get the type of suggestion by uuid
	 *
	 * @param string $suggestion_uuid
	 *
	 * @return string
	 * @throws \Exception
	 */
	public static function get_suggestion_type( $suggestion_uuid ) {

		$suggestion = self::get_suggestion( $suggestion_uuid );

		return $suggestion[ WPSOLR_Option::OPTION_EVENT_TRACKING_TYPE ];
	}

	/**
	 * Get the type of suggestion by uuid
	 *
	 * @param string $suggestion_uuid
	 *
	 * @return array
	 * @throws \Exception
	 */
	public static function get_suggestion( $suggestion_uuid ) {

		$suggestions = WPSOLR_Service_Container::getOption()->get_option_suggestions_suggestions();
		if ( empty( $suggestions ) || empty( $suggestions[ $suggestion_uuid ] ) ) {
			throw new \Exception( "The suggestion '$suggestion_uuid' is missing in WPSOLR settings 2.3." );
		}
		if ( empty( $suggestions[ $suggestion_uuid ][ WPSOLR_Option::OPTION_EVENT_TRACKING_IS_ACTIVE ] ) ) {
			throw new \Exception( sprintf( "The suggestion '%s' is not active in WPSOLR settings 2.3.", $suggestions[ $suggestion_uuid ][ WPSOLR_Option::OPTION_EVENT_TRACKING_LABEL ] ) );
		}

		/**
		 * Add uuid to $suggestion. Easier to manipulate later.
		 */
		$suggestion                                              = $suggestions[ $suggestion_uuid ];
		$suggestion[ WPSOLR_Option::OPTION_EVENT_TRACKING_UUID ] = $suggestion_uuid;

		return $suggestion;
	}

	/**
	 * Return the event types in the options page select box
	 *
	 * @return array
	 */
	static function get_type_definitions() {
		global $license_manager;

		$index_uuid     = WPSOLR_Service_Container::getOption()->get_view_index_uuid();
		$option_indexes = WPSOLR_Service_Container::getOption()->get_option_indexes();
		$search_engine  = '';
		if ( isset( $option_indexes[ WPSOLR_Option::OPTION_INDEXES_INDEXES ][ $index_uuid ] ) ) {
			$search_engine = $option_indexes[ WPSOLR_Option::OPTION_INDEXES_INDEXES ][ $index_uuid ][ WPSOLR_AbstractEngineClient::ENGINE ];
		}

		$definitions = [
			/**
			 * Algolia event types: https://www.algolia.com/doc/api-client/methods/insights/
			 */
			[
				'code'    => WPSOLR_Option::OPTION_EVENT_TRACKING_EVENT_TYPE_ALGOLIA_CLICKEDOBJECTIDSAFTERSEARCH,
				'label'   => 'Clicked Object IDs After Search: Send a click event to capture a query and its clicked items and positions',
				'disabled' => false,
				'engines' => [
					WPSOLR_AbstractEngineClient::ENGINE_ALGOLIA,
				],
			],
			[
				'code'    => WPSOLR_Option::OPTION_EVENT_TRACKING_EVENT_TYPE_ALGOLIA_CLICKEDOBJECTIDS,
				'label'   => 'Clicked Object IDs: Send a click event to capture clicked items',
				'engines' => [
					WPSOLR_AbstractEngineClient::ENGINE_ALGOLIA,
				],
			],
			[
				'code'    => WPSOLR_Option::OPTION_EVENT_TRACKING_EVENT_TYPE_ALGOLIA_CLICKEDFILTERS,
				'label'   => 'Clicked Filters: Send a click event to capture the filters a user clicks on',
				'engines' => [
					WPSOLR_AbstractEngineClient::ENGINE_ALGOLIA,
				],
			],
			[
				'code'    => WPSOLR_Option::OPTION_EVENT_TRACKING_EVENT_TYPE_ALGOLIA_CONVERTEDOBJECTIDSAFTERSEARCH,
				'label'   => 'Converted Object IDs After Search: Send a conversion event to capture a query and its clicked items',
				'engines' => [
					WPSOLR_AbstractEngineClient::ENGINE_ALGOLIA,
				],
			],
			[
				'code'    => WPSOLR_Option::OPTION_EVENT_TRACKING_EVENT_TYPE_ALGOLIA_CONVERTEDOBJECTIDS,
				'label'   => 'Converted Object IDs: Send a conversion event to capture clicked items',
				'engines' => [
					WPSOLR_AbstractEngineClient::ENGINE_ALGOLIA,
				],
			],
			[
				'code'    => WPSOLR_Option::OPTION_EVENT_TRACKING_EVENT_TYPE_ALGOLIA_CONVERTEDFILTERS,
				'label'   => 'Converted Filters: Send a conversion event to capture the filters a user uses when converting',
				'engines' => [
					WPSOLR_AbstractEngineClient::ENGINE_ALGOLIA,
				],
			],
			[
				'code'    => WPSOLR_Option::OPTION_EVENT_TRACKING_EVENT_TYPE_ALGOLIA_VIEWEDOBJECTIDS,
				'label'   => 'Viewed Object IDs: Send a view event to capture viewed item',
				'engines' => [
					WPSOLR_AbstractEngineClient::ENGINE_ALGOLIA,
				],
			],
			[
				'code'    => WPSOLR_Option::OPTION_EVENT_TRACKING_EVENT_TYPE_ALGOLIA_VIEWEDFILTERS,
				'label'   => 'Viewed Filters: Send a view event captures the filters a user sees when viewing filtered content without having actively filtered it',
				'engines' => [
					WPSOLR_AbstractEngineClient::ENGINE_ALGOLIA,
				],
			],

			/**
			 * Google Retail event types: https://cloud.google.com/retail/docs/reference/rest/v2/projects.locations.catalogs.userEvents
			 */
			[
				'code'    => WPSOLR_Option::OPTION_EVENT_TRACKING_EVENT_TYPE_GOOGLE_RETAIL_ADD_TO_CART,
				'label'   => 'add-to-cart: Products being added to cart',
				'engines' => [
					WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL,
				],
			],
			[
				'code'    => WPSOLR_Option::OPTION_EVENT_TRACKING_EVENT_TYPE_GOOGLE_RETAIL_CATEGORY_PAGE_VIEW,
				'label'   => 'category-page-view: Special pages such as sale or promotion pages viewed',
				'engines' => [
					WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL,
				],
			],
			[
				'code'    => WPSOLR_Option::OPTION_EVENT_TRACKING_EVENT_TYPE_GOOGLE_RETAIL_DETAIL_PAGE_VIEW,
				'label'   => 'detail-page-view: Products detail page viewed',
				'engines' => [
					WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL,
				],
			],
			[
				'code'    => WPSOLR_Option::OPTION_EVENT_TRACKING_EVENT_TYPE_GOOGLE_RETAIL_HOME_PAGE_VIEW,
				'label'   => 'home-page-view: Homepage viewed',
				'engines' => [
					WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL,
				],
			],
			[
				'code'    => WPSOLR_Option::OPTION_EVENT_TRACKING_EVENT_TYPE_GOOGLE_RETAIL_PROMOTION_OFFERED,
				'label'   => 'promotion-offered: Promotion is offered to a user',
				'engines' => [
					WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL,
				],
			],
			[
				'code'    => WPSOLR_Option::OPTION_EVENT_TRACKING_EVENT_TYPE_GOOGLE_RETAIL_PROMOTION_NOT_OFFERED,
				'label'   => 'promotion-not-offered: Promotion is not offered to a user',
				'engines' => [
					WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL,
				],
			],
			[
				'code'    => WPSOLR_Option::OPTION_EVENT_TRACKING_EVENT_TYPE_GOOGLE_RETAIL_PURCHASE_COMPLETE,
				'label'   => 'purchase-complete: User finishing a purchase',
				'engines' => [
					WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL,
				],
			],
			[
				'code'    => WPSOLR_Option::OPTION_EVENT_TRACKING_EVENT_TYPE_GOOGLE_RETAIL_SEARCH,
				'label'   => 'search: Product search',
				'disabled' => false,
				'engines' => [
					WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL,
				],
			],
			[
				'code'    => WPSOLR_Option::OPTION_EVENT_TRACKING_EVENT_TYPE_GOOGLE_RETAIL_SHOPPING_CART_PAGE_VIEW,
				'label'   => 'shopping-cart-page-view: User viewing a shopping cart',
				'engines' => [
					WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL,
				],
			],
		];

		$results = [];
		foreach ( $definitions as $definition ) {
			if ( in_array( $search_engine, $definition['engines'] ) ) {
				$results[] = $definition;
			}
		}

		return $results;
	}

	/**
	 * Get js options for each suggestion
	 * @return string[]
	 */
	public function get_active_suggestions_js_options() {
		global $wp_query;

		$default_selector = '.' . WPSOLR_Option::OPTION_SEARCH_SUGGEST_CLASS_DEFAULT;
		$results          = [];
		$archive_filters  = $wp_query->get_archive_filter_query_fields();
		foreach ( WPSOLR_Service_Container::getOption()->get_option_suggestions_suggestions() as $suggestion_uuid => $suggestion ) {

			if ( isset( $suggestion[ WPSOLR_Option::OPTION_EVENT_TRACKING_IS_ACTIVE ] ) ) {

				$result = [
					'view_uuid'                                                   => WPSOLR_Option_View::get_current_view_uuid(),
					'suggestion_uuid'                                             => $suggestion_uuid,
					'suggestion_class'                                            => sprintf( self::EVENT_TRACKING_CLASS_PATTERN, $suggestion_uuid ),
					'jquery_selector'                                             => empty( $suggestion[ WPSOLR_Option::OPTION_EVENT_TRACKING_JQUERY_SELECTOR ] )
						? $default_selector
						: $suggestion[ WPSOLR_Option::OPTION_EVENT_TRACKING_JQUERY_SELECTOR ],
					WPSOLR_Query_Parameters::SEARCH_PARAMETER_AJAX_URL_PARAMETERS =>
						( ( $wp_query instanceof WPSOLR_Query ) && ( ( $wp_query instanceof WPSOLR_Query && $wp_query->wpsolr_get_is_admin() ) || isset( $suggestion[ WPSOLR_Option::OPTION_EVENT_TRACKING_IS_ARCHIVE ] ) ) && ! empty( $archive_filters ) ) ?
							build_query( [ WPSOLR_Query_Parameters::SEARCH_PARAMETER_FQ => $archive_filters ] )
							: '',
				];

				$results[] = $result;
			}
		}

		return $results;
	}

}
