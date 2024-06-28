<?php

namespace wpsolr\pro\extensions\recommendations;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\core\classes\extensions\indexes\WPSOLR_Option_Indexes;
use wpsolr\core\classes\extensions\view\WPSOLR_Option_View;
use wpsolr\core\classes\extensions\WPSOLR_Extension;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\ui\WPSOLR_Query;
use wpsolr\core\classes\ui\WPSOLR_Query_Parameters;
use wpsolr\core\classes\utilities\WPSOLR_Option;
use wpsolr\core\classes\WPSOLR_Events;

class WPSOLR_Option_Recommendations extends WPSOLR_Extension {


	const IS_RECOMMENDATIONS_IMPLEMENTED = false;

	// Default keyword redirection pattern (can be updated in recommendation option for WooCommerce, or bbPress ...)
	const RECOMMENDATION_REDIRECTION_PATTERN_DEFAULT = '/?s=%s';


	const CLASS_RECOMMENDATION_TYPE = 'wpsolr_recommendation_type';
	const CLASS_RECOMMENDATION_LAYOUT = 'wpsolr_recommendation_layout';
	const CLASS_RECOMMENDATION_GROUPS = 'wpsolr_recommendation_groups';

	const RECOMMENDATION_LAYOUTS = [];

	/**
	 * Folder containing all the templates, under plugin or theme.
	 */
	const TEMPLATE_ROOT_DIR = 'wpsolr-templates';
	const DIR_PHP = 'php';
	const DIR_TWIG = 'twig';
	const TEMPLATE_BUILDER = 'wpsolr_template_builder';

	/**
	 * Predefined template argements
	 */
	const TEMPLATE_RECOMMENDATIONS_ARGS_NAME = 'recommendations';

	/**
	 * Fancy templates
	 */
	const OPTION_RECOMMENDATION_LAYOUT_ID_KEYWORDS_FANCY = 'layout_id_keywords_fancy';
	const TEMPLATE_RECOMMENDATIONS_KEYWORDS_FANCY = 'recommendations/fancy/recommendations-keywords.twig';
	const OPTION_RECOMMENDATION_LAYOUT_ID_CONTENT_FLAT_FANCY = 'layout_id_content_fancy';
	const TEMPLATE_RECOMMENDATIONS_CONTENT_FANCY = 'recommendations/fancy/recommendations-content.twig';
	const OPTION_RECOMMENDATION_LAYOUT_ID_CONTENT_GROUPED_FANCY = 'layout_id_content_grouped_fancy';
	const TEMPLATE_RECOMMENDATIONS_CONTENT_GROUPED_FANCY = 'recommendations/fancy/recommendations-content-grouped.twig';
	const OPTION_RECOMMENDATION_LAYOUT_ID_QUESTIONS_ANSWERS_FANCY = 'layout_id_questions_answers_fancy';
	const TEMPLATE_RECOMMENDATIONS_QUESTIONS_ANSWERS_FANCY = 'recommendations/fancy/recommendations-questions-answers.twig';


	const TEMPLATE_FACETS = 'search/facets.twig';
	const TEMPLATE_FACETS_ARGS_NAME = 'facets';

	const TEMPLATE_SEARCH = 'search/search.twig';
	const TEMPLATE_SEARCH_ARGS_NAME = 'search';

	const TEMPLATE_SORT_LIST = 'search/sort.twig';
	const TEMPLATE_SORT_LIST_ARGS_NAME = 'sort';

	const TEMPLATE_RESULTS_INFINISCROLL = 'search/results-infiniscroll.twig';
	const TEMPLATE_RESULTS_INFINISCROLL_ARGS_NAME = 'search';

	/**
	 * Name of variable containing the template data
	 */
	const TEMPLATE_ARGS = 'wpsolr_template_data';

	/**
	 * Build class from uuid
	 */
	const RECOMMENDATION_CLASS_PATTERN = 'c%s';

	/**
	 * Template type definitions
	 */
	const RECOMMENDATION_TEMPLATE_TYPE_DEFINITIONS = [
		/**
		 * Recombee: https://docs.recombee.com/api.html#recommendations
		 */
		WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_RECOMBEE_ITEMS_TO_USER  =>
			[
				'fields'        => [
					WPSOLR_Option::OPTION_RECOMMENDATION_RECOMBEE_SCENARIO,
					WPSOLR_Option::OPTION_RECOMMENDATION_NB,
					WPSOLR_Option::OPTION_RECOMMENDATION_IS_SHOW_TEXT,
					WPSOLR_Option::OPTION_RECOMMENDATION_IMAGE_WIDTH_PCT,
					WPSOLR_Option::OPTION_RECOMMENDATION_CUSTOM_CSS,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODELS,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODEL_PRODUCT_IS_SHOW_RATING,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODEL_PRODUCT_IS_SHOW_PRICE,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODEL_PRODUCT_IS_SHOW_ADD_TO_CART,
				],
				'template_args' => self::TEMPLATE_RECOMMENDATIONS_ARGS_NAME,
			],
		WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_RECOMBEE_ITEMS_TO_ITEM  =>
			[
				'fields'        => [
					WPSOLR_Option::OPTION_RECOMMENDATION_RECOMBEE_SCENARIO,
					WPSOLR_Option::OPTION_RECOMMENDATION_NB,
					WPSOLR_Option::OPTION_RECOMMENDATION_IS_SHOW_TEXT,
					WPSOLR_Option::OPTION_RECOMMENDATION_IMAGE_WIDTH_PCT,
					WPSOLR_Option::OPTION_RECOMMENDATION_CUSTOM_CSS,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODELS,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODEL_PRODUCT_IS_SHOW_RATING,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODEL_PRODUCT_IS_SHOW_PRICE,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODEL_PRODUCT_IS_SHOW_ADD_TO_CART,
				],
				'template_args' => self::TEMPLATE_RECOMMENDATIONS_ARGS_NAME,
			],
		/**
		 * Algolia: https://www.algolia.com/doc/api-client/methods/recommend/
		 */
		WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_ALGOLIA_TRENDING_FACETS =>
			[
				'fields'        => [
					WPSOLR_Option::OPTION_RECOMMENDATION_NB,
					WPSOLR_Option::OPTION_RECOMMENDATION_IS_SHOW_TEXT,
					WPSOLR_Option::OPTION_RECOMMENDATION_IMAGE_WIDTH_PCT,
					WPSOLR_Option::OPTION_RECOMMENDATION_CUSTOM_CSS,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODELS,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODEL_PRODUCT_IS_SHOW_RATING,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODEL_PRODUCT_IS_SHOW_PRICE,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODEL_PRODUCT_IS_SHOW_ADD_TO_CART,
				],
				'template_args' => self::TEMPLATE_RECOMMENDATIONS_ARGS_NAME,
			],
		WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_ALGOLIA_RELATED_ITEMS   =>
			[
				'fields'        => [
					WPSOLR_Option::OPTION_RECOMMENDATION_NB,
					WPSOLR_Option::OPTION_RECOMMENDATION_IS_SHOW_TEXT,
					WPSOLR_Option::OPTION_RECOMMENDATION_IMAGE_WIDTH_PCT,
					WPSOLR_Option::OPTION_RECOMMENDATION_CUSTOM_CSS,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODELS,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODEL_PRODUCT_IS_SHOW_RATING,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODEL_PRODUCT_IS_SHOW_PRICE,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODEL_PRODUCT_IS_SHOW_ADD_TO_CART,
				],
				'template_args' => self::TEMPLATE_RECOMMENDATIONS_ARGS_NAME,
			],
		WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_ALGOLIA_BOUGHT_TOGETHER =>
			[
				'fields'        => [
					WPSOLR_Option::OPTION_RECOMMENDATION_NB,
					WPSOLR_Option::OPTION_RECOMMENDATION_IS_SHOW_TEXT,
					WPSOLR_Option::OPTION_RECOMMENDATION_IMAGE_WIDTH_PCT,
					WPSOLR_Option::OPTION_RECOMMENDATION_CUSTOM_CSS,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODELS,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODEL_PRODUCT_IS_SHOW_RATING,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODEL_PRODUCT_IS_SHOW_PRICE,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODEL_PRODUCT_IS_SHOW_ADD_TO_CART,
				],
				'template_args' => self::TEMPLATE_RECOMMENDATIONS_ARGS_NAME,
			],
		WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_ALGOLIA_TRENDING_ITEMS  =>
			[
				'fields'        => [
					WPSOLR_Option::OPTION_RECOMMENDATION_NB,
					WPSOLR_Option::OPTION_RECOMMENDATION_IS_SHOW_TEXT,
					WPSOLR_Option::OPTION_RECOMMENDATION_IMAGE_WIDTH_PCT,
					WPSOLR_Option::OPTION_RECOMMENDATION_CUSTOM_CSS,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODELS,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODEL_PRODUCT_IS_SHOW_RATING,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODEL_PRODUCT_IS_SHOW_PRICE,
					WPSOLR_Option::OPTION_RECOMMENDATION_MODEL_PRODUCT_IS_SHOW_ADD_TO_CART,
				],
				'template_args' => self::TEMPLATE_RECOMMENDATIONS_ARGS_NAME,
			],
	];

	/**
	 * @var array
	 */
	private static $cached_recommendations = [];


	/**
	 * Constructor
	 * Subscribe to actions
	 */

	function __construct() {

		add_action( WPSOLR_Events::WPSOLR_FILTER_POST_TYPES, [
			$this,
			'wpsolr_filter_post_types',
		], 10, 2 );

		add_filter( WPSOLR_Events::WPSOLR_FILTER_JAVASCRIPT_FRONT_LOCALIZED_PARAMETERS, [
			$this,
			'wpsolr_filter_javascript_front_localized_parameters',
		], 10, 1 );
	}

	/**
	 * Add recommendations options
	 *
	 * @param array $parameters
	 *
	 * @return array
	 */
	public function wpsolr_filter_javascript_front_localized_parameters( $parameters ) {
		global $wp_query;

		$parameters['data']['wpsolr_recommendation_selector']       = $this->get_active_recommendations_js_options();
		$parameters['data']['wpsolr_recommendation_action']         = WPSOLR_AJAX_RECOMMENDATION_ACTION;
		$parameters['data']['wpsolr_recommendation_nonce_selector'] = ( '#' . WPSOLR_AJAX_RECOMMENDATION_NONCE_SELECTOR );

		return $parameters;
	}

	/**
	 * Filter post types according to the recommendation
	 *
	 * @param string[] $post_types
	 * @param WPSOLR_Query $wpsolr_query
	 *
	 * @return array
	 */
	public
	function wpsolr_filter_post_types(
		$post_types, $wpsolr_query
	) {

		$recommendation = $wpsolr_query->wpsolr_get_recommendation();
		if ( ! empty( $recommendation ) ) {
			switch ( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_TYPE ] ) {
				default:

					/**
					 * Filter by types selected on the recommendation.
					 * If none selected, then use all indexed types.
					 */
					if ( empty( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_MODELS ] ) ) {

						$post_types = WPSOLR_Service_Container::getOption()->get_option_index_post_types();

					} else {

						$post_types = [];
						foreach ( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_MODELS ] as $post_type => $model_def ) {
							if ( isset( $model_def[ WPSOLR_Option::OPTION_RECOMMENDATION_MODEL_ID ] ) ) {
								$post_types[] = $post_type;
							}
						}

					}

					break;
			}
		}

		return $post_types;
	}

	/**
	 * Get the default layout of a recommendation type
	 *
	 * @param $recommendation_type
	 *
	 * @return string
	 */
	public static function get_type_default_layout( $recommendation_type ) {

		$result = self::OPTION_RECOMMENDATION_LAYOUT_ID_KEYWORDS_FANCY;

		foreach ( self::get_type_definitions() as $type_definition ) {
			if ( $recommendation_type === $type_definition['code'] ) {
				$result = $type_definition['default_layout'];
				break;
			}
		}

		return $result;
	}

	/**
	 * Get the file template of a recommendation  by uuid
	 *
	 * @param string $recommendation_uuid
	 *
	 * @return string[]
	 * @throws \Exception
	 */
	public static function get_recommendation_layout_file( $recommendation_uuid ) {

		$result = [];

		$recommendation = self::get_recommendation( $recommendation_uuid );

		foreach ( self::get_template_definitions() as $layout_definition ) {
			if ( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_LAYOUT_ID ] === $layout_definition['code'] ) {
				$result = [
					'template_file' => $layout_definition['template_file'],
					'template_args' => $layout_definition['template_args'],
				];
				break;
			}
		}

		if ( empty( $result ) ) {
			throw new \Exception( "The recommendation '$recommendation_uuid' has no selected template in WPSOLR settings 2.3." );
		}

		return $result;
	}

	/**
	 * Get the type of recommendation by uuid
	 *
	 * @param string $recommendation_uuid
	 *
	 * @return string
	 * @throws \Exception
	 */
	public static function get_recommendation_type( $recommendation_uuid ) {

		$recommendation = self::get_recommendation( $recommendation_uuid );

		return $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_TYPE ];
	}

	/**
	 * Get the redirection pattern of recommendation
	 *
	 * @param array $recommendation
	 *
	 * @return string
	 */
	public static function get_recommendation_redirection_pattern( $recommendation ) {

		return empty( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_RECOMBEE_SCENARIO ] ) ? self::RECOMMENDATION_REDIRECTION_PATTERN_DEFAULT : $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_RECOMBEE_SCENARIO ];
	}

	/**
	 * Get the type of recommendation by uuid
	 *
	 * @param string $recommendation_uuid
	 *
	 * @return array
	 * @throws \Exception
	 */
	public static function get_recommendation( $recommendation_uuid ) {

		if ( ! empty( $recommendation_uuid ) ) {

			if ( ! empty( static::$cached_recommendations[ $recommendation_uuid ] ) ) {
				// Use cache
				return static::$cached_recommendations[ $recommendation_uuid ];
			}

			$indexes = WPSOLR_Service_Container::getOption()->get_option_indexes();
			foreach ( $indexes[ WPSOLR_Option::OPTION_INDEXES_INDEXES ] ?? [] as $index_uuid => $index ) {
				WPSOLR_Option_View::set_current_index_uuid( $index_uuid );
				$recommendations = WPSOLR_Service_Container::getOption()->get_option_recommendations_recommendations();
				foreach ( $recommendations as $uuid => $recommendation ) {
					if ( $recommendation_uuid === $uuid ) {

						if ( empty( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_IS_ACTIVE ] ) ) {
							throw new \Exception( sprintf( "The recommendation '%s' is not active in WPSOLR settings 2.3.", $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_LABEL ] ) );
						}

						/**
						 * Add uuid to $recommendation. Easier to manipulate later.
						 */
						$recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_UUID ] = $recommendation_uuid;
						$recommendation[ WPSOLR_Option_View::INDEX_UUID ]            = $index_uuid;

						// Set the cache
						static::$cached_recommendations[ $recommendation_uuid ] = $recommendation;

						// Use cache
						return static::$cached_recommendations[ $recommendation_uuid ];
					}
				}
			}
		}

		throw new \Exception( "The recommendation '$recommendation_uuid' is missing in WPSOLR settings 2.3." );
	}

	/**
	 * Return the recommendations types in the options page select box
	 *
	 *
	 * @return array
	 */
	static function get_type_definitions() {

		$index_uuid         = WPSOLR_Option_View::get_current_index_uuid();
		$option_indexes     = WPSOLR_Service_Container::getOption()->get_option_indexes();
		$search_engine      = '';
		$search_engine_name = '';
		if ( isset( $option_indexes[ WPSOLR_Option::OPTION_INDEXES_INDEXES ][ $index_uuid ] ) ) {
			$search_engine      = $option_indexes[ WPSOLR_Option::OPTION_INDEXES_INDEXES ][ $index_uuid ][ WPSOLR_AbstractEngineClient::ENGINE ];
			$search_engine_name = ( new WPSOLR_Option_Indexes() )->get_search_engine_name( $search_engine );
		}

		$definitions = [];
		switch ( $search_engine ) {
			case WPSOLR_AbstractEngineClient::ENGINE_RECOMBEE:
				/**
				 * https://docs.recombee.com/api.html#recommendations
				 * (v4) https://api-clients-automation.netlify.app/specs/recommend#tag/recommendations
				 */
				$definitions = [
					[
						'code'           => WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_RECOMBEE_ITEMS_TO_USER,
						'label'          => 'Recommend items to user',
						'default_layout' => self::OPTION_RECOMMENDATION_LAYOUT_ID_KEYWORDS_FANCY,
						'engines'        => [
							WPSOLR_AbstractEngineClient::ENGINE_RECOMBEE,
						]
					],
					[
						'code'           => WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_RECOMBEE_ITEMS_TO_ITEM,
						'label'          => 'Recommend items to item',
						'default_layout' => self::OPTION_RECOMMENDATION_LAYOUT_ID_KEYWORDS_FANCY,
						'engines'        => [
							WPSOLR_AbstractEngineClient::ENGINE_RECOMBEE,
						]
					],
					[
						'code'           => WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_RECOMBEE_USERS_TO_USER,
						'label'          => 'Recommend users to user',
						'default_layout' => self::OPTION_RECOMMENDATION_LAYOUT_ID_KEYWORDS_FANCY,
						'engines'        => [
							// WPSOLR_AbstractEngineClient::ENGINE_RECOMBEE,
						]
					],
				];
				break;

			case WPSOLR_AbstractEngineClient::ENGINE_ALGOLIA:
				$definitions = [
					/**
					 * Algolia: https://www.algolia.com/doc/api-client/methods/recommend/
					 *
					 */
					[
						'code'           => WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_ALGOLIA_BOUGHT_TOGETHER,
						'label'          => 'Frequently bought together',
						'default_layout' => self::OPTION_RECOMMENDATION_LAYOUT_ID_KEYWORDS_FANCY,
						'engines'        => [
							WPSOLR_AbstractEngineClient::ENGINE_ALGOLIA,
						]
					],
					[
						'code'           => WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_ALGOLIA_RELATED_ITEMS,
						'label'          => 'Related products or content',
						'default_layout' => self::OPTION_RECOMMENDATION_LAYOUT_ID_KEYWORDS_FANCY,
						'engines'        => [
							WPSOLR_AbstractEngineClient::ENGINE_ALGOLIA,
						]
					],
					[
						'code'           => WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_ALGOLIA_TRENDING_ITEMS,
						'label'          => 'Trending items',
						'default_layout' => self::OPTION_RECOMMENDATION_LAYOUT_ID_KEYWORDS_FANCY,
						'engines'        => [
							WPSOLR_AbstractEngineClient::ENGINE_ALGOLIA,
						]
					],
					[
						'code'           => WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_ALGOLIA_TRENDING_FACETS,
						'label'          => 'Trending facets value',
						'default_layout' => self::OPTION_RECOMMENDATION_LAYOUT_ID_KEYWORDS_FANCY,
						'engines'        => [
							WPSOLR_AbstractEngineClient::ENGINE_ALGOLIA,
						]
					],
				];
				break;

			case WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL:
				$definitions = [
					/**
					 * Google retail: https://cloud.google.com/retail/docs/models
					 *
					 */
					[
						'code'           => WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_GOOGLE_RETAIL_BUY_IT_AGAIN,
						'label'          => 'Buy it Again',
						'default_layout' => self::OPTION_RECOMMENDATION_LAYOUT_ID_KEYWORDS_FANCY,
						'engines'        => [
							WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL,
						]
					],
					[
						'code'           => WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_GOOGLE_RETAIL_FREQUENTLY_BOUGHT_TOGETHER,
						'label'          => 'Frequently Bought Together (shopping cart expansion)',
						'default_layout' => self::OPTION_RECOMMENDATION_LAYOUT_ID_KEYWORDS_FANCY,
						'engines'        => [
							WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL,
						]
					],
					[
						'code'           => WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_GOOGLE_RETAIL_OTHERS_YOU_MAY_LIKE,
						'label'          => 'Others You May Like',
						'default_layout' => self::OPTION_RECOMMENDATION_LAYOUT_ID_KEYWORDS_FANCY,
						'engines'        => [
							WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL,
						]
					],
					[
						'code'           => WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_GOOGLE_RETAIL_PAGE_OPTIMIZATION,
						'label'          => 'Page-Level Optimization',
						'default_layout' => self::OPTION_RECOMMENDATION_LAYOUT_ID_KEYWORDS_FANCY,
						'engines'        => [
							WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL,
						]
					],
					[
						'code'           => WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_GOOGLE_RETAIL_RECENTLY_VIEWED,
						'label'          => 'Recently Viewed',
						'default_layout' => self::OPTION_RECOMMENDATION_LAYOUT_ID_KEYWORDS_FANCY,
						'engines'        => [
							WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL,
						]
					],
					[
						'code'           => WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_GOOGLE_RETAIL_SIMILAR_ITEMS,
						'label'          => 'Similar Items',
						'default_layout' => self::OPTION_RECOMMENDATION_LAYOUT_ID_KEYWORDS_FANCY,
						'engines'        => [
							WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL,
						]
					],
					[
						'code'           => WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_GOOGLE_RETAIL_RECOMMENDED_FOR_YOU,
						'label'          => 'Recommended for You',
						'default_layout' => self::OPTION_RECOMMENDATION_LAYOUT_ID_KEYWORDS_FANCY,
						'engines'        => [
							WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL,
						]
					],
				];
				break;

			/**
			 * https://weaviate.io/developers/weaviate/current/graphql-references/get.html#vector-search-operators
			 */
			case WPSOLR_AbstractEngineClient::ENGINE_WEAVIATE:
				$definitions = [
					[
						'code'           => WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_WEAVIATE_NEAR_OBJECT,
						'label'          => 'Similar items',
						'default_layout' => self::OPTION_RECOMMENDATION_LAYOUT_ID_KEYWORDS_FANCY,
						'engines'        => [
							WPSOLR_AbstractEngineClient::ENGINE_WEAVIATE,
						]
					],
					[
						'code'           => WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_WEAVIATE_NEAR_IMAGE,
						'label'          => 'Similar images',
						'default_layout' => self::OPTION_RECOMMENDATION_LAYOUT_ID_KEYWORDS_FANCY,
						'engines'        => [
							WPSOLR_AbstractEngineClient::ENGINE_WEAVIATE,
						]
					],
				];
				break;
		}


		foreach ( $definitions as &$definition ) {
			if ( isset( $definition['not_engines'] ) && in_array( $search_engine, $definition['not_engines'] ) ) {
				// Disable this definition
				$definition['label']    = sprintf( '%s - Not available with %s.', $definition['label'], $search_engine_name );
				$definition['disabled'] = true;
				$engine_names           = [];
				foreach ( $definition['not_engines'] as $engine ) {
					if ( $search_engine !== $engine ) {
						$engine_names[] = ( new WPSOLR_Option_Indexes() )->get_search_engine_name( $engine );
					}
				}
				if ( ! empty( $engine_names ) ) {
					$definition['label'] = sprintf( '%s Nor with %s.', $definition['label'], implode( ' or ', $engine_names ) );
				}
			} elseif ( isset( $definition['engines'] ) && ! in_array( $search_engine, $definition['engines'] ) ) {
				// Disable this definition
				$definition['label']    = sprintf( '%s - Not available with %s.', $definition['label'], $search_engine_name );
				$definition['disabled'] = true;
				$engine_names           = [];
				foreach ( $definition['engines'] as $engine ) {
					if ( $search_engine !== $engine ) {
						$engine_names[] = ( new WPSOLR_Option_Indexes() )->get_search_engine_name( $engine );
					}
				}
				if ( ! empty( $engine_names ) ) {
					$definition['label'] = sprintf( '%s Only with %s.', $definition['label'], implode( ' or ', $engine_names ) );
				}
			} else {
				$definition['disabled'] = false;
			}
		}

		return $definitions;
	}

	/**
	 * Return the template in the options page select box
	 *
	 * @return array
	 */
	static function get_template_definitions() {


		/**
		 * Here one can add his own template definition
		 */
		$definitions = apply_filters( WPSOLR_Events::WPSOLR_FILTER_RECOMMENDATIONS_TEMPLATES,
			[
				/**
				 * Fancy templates
				 */
				[
					'code'          => self::OPTION_RECOMMENDATION_LAYOUT_ID_KEYWORDS_FANCY,
					'label'         => 'WPSOLR - Default - Recommended Items',
					'type'          => WPSOLR_Option::OPTION_RECOMMENDATION_TYPE_RECOMBEE_ITEMS_TO_USER,
					'template_file' => self::TEMPLATE_RECOMMENDATIONS_CONTENT_FANCY,
				],
			],
			10, 1
		);

		/**
		 * Expand the template definitions with the template type properties
		 */
		foreach ( $definitions as &$definition ) {
			$definition['fields']        = self::RECOMMENDATION_TEMPLATE_TYPE_DEFINITIONS[ $definition['type'] ]['fields'];
			$definition['template_args'] = self::RECOMMENDATION_TEMPLATE_TYPE_DEFINITIONS[ $definition['type'] ]['template_args'];
		}


		return $definitions;
	}

	/**
	 * Return the order by in the options page select box
	 *
	 * @return array
	 */
	static function get_order_by_definitions() {

		return [
			[
				'code'     => WPSOLR_Option::OPTION_RECOMMENDATION_ORDER_BY_GROUP_CONTENT_MAX_RELEVANCY,
				'label'    => 'Groups with the best recommendation',
				'type'     => WPSOLR_Option::OPTION_SEARCH_SUGGEST_CONTENT_TYPE_CONTENT_GROUPED,
				'disabled' => false,
			],
			[
				'code'     => WPSOLR_Option::OPTION_RECOMMENDATION_ORDER_BY_GROUP_POSITION,
				'label'    => 'Groups with their position defined below by drag&drop',
				'type'     => WPSOLR_Option::OPTION_SEARCH_SUGGEST_CONTENT_TYPE_CONTENT_GROUPED,
				'disabled' => false,
			],
			[
				'code'     => WPSOLR_Option::OPTION_RECOMMENDATION_ORDER_BY_GROUP_CONTENT_AVERAGE_RELEVANCY,
				'label'    => 'Groups with the best recommendations in average (not implemented)',
				'type'     => WPSOLR_Option::OPTION_SEARCH_SUGGEST_CONTENT_TYPE_CONTENT_GROUPED,
				'disabled' => true,
			],
		];
	}

	/**
	 * Get js options for each recommendation
	 * @return string[]
	 */
	public function get_active_recommendations_js_options() {
		global $wp_query;

		$default_selector = '.' . WPSOLR_Option::OPTION_SEARCH_SUGGEST_CLASS_DEFAULT;
		$results          = [];
		$archive_filters  = $wp_query->get_archive_filter_query_fields();
		foreach ( WPSOLR_Service_Container::getOption()->get_option_recommendations_recommendations() as $recommendation_uuid => $recommendation ) {

			if ( isset( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_IS_ACTIVE ] ) ) {

				$result = [
					'view_uuid'                                                   => WPSOLR_Option_View::get_current_view_uuid(),
					'recommendation_uuid'                                         => $recommendation_uuid,
					'recommendation_class'                                        => sprintf( self::RECOMMENDATION_CLASS_PATTERN, $recommendation_uuid ),
					'jquery_selector'                                             => empty( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_JQUERY_SELECTOR ] )
						? $default_selector
						: $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_JQUERY_SELECTOR ],
					WPSOLR_Query_Parameters::SEARCH_PARAMETER_AJAX_URL_PARAMETERS =>
						( ( $wp_query instanceof WPSOLR_Query ) && ( ( $wp_query instanceof WPSOLR_Query && $wp_query->wpsolr_get_is_admin() ) || isset( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_IS_ARCHIVE ] ) ) && ! empty( $archive_filters ) ) ?
							build_query( [ WPSOLR_Query_Parameters::SEARCH_PARAMETER_FQ => $archive_filters ] )
							: '',
				];

				$results[] = $result;
			}
		}

		return $results;
	}

	/**
	 * Migrate old jQuery selectors prior to version 21.5
	 */
	public static function migrate_data_from_v21_4() {

		$old_jquery_selectors = WPSOLR_Service_Container::getOption()->get_search_suggest_jquery_selector_before_version_21_5();
		if ( ! empty( $old_jquery_selectors ) ) {
			// Migrate to a new recommendations
			$recommendations_options = WPSOLR_Service_Container::getOption()->get_option_recommendations();

			if ( empty( $recommendations_options ) ) {
				$old_recommendations_content_type                                                 = WPSOLR_Service_Container::getOption()->get_search_suggest_content_type_before_version_21_5();//update_option( WPSOLR_Option::OPTION_RECOMMENDATIONS, $recommendations_options );
				$recommendations_options[ WPSOLR_Option::OPTION_RECOMMENDATIONS_RECOMMENDATIONS ] = [
					WPSOLR_Option_Indexes::generate_uuid() => [
						WPSOLR_Option::OPTION_RECOMMENDATION_IS_ACTIVE       => 'is_active',
						WPSOLR_Option::OPTION_RECOMMENDATION_JQUERY_SELECTOR => $old_jquery_selectors,
						WPSOLR_Option::OPTION_RECOMMENDATION_TYPE            => $old_recommendations_content_type,
						WPSOLR_Option::OPTION_RECOMMENDATION_LAYOUT_ID       => self::get_type_default_layout( $old_recommendations_content_type ),
						WPSOLR_Option::OPTION_RECOMMENDATION_NB              => 10,
					]
				];
				update_option( WPSOLR_Option::OPTION_RECOMMENDATIONS, $recommendations_options );
			}


			// Erase old recommendations options
			$search_options = WPSOLR_Service_Container::getOption()->get_option_search();
			unset( $search_options[ WPSOLR_Option::OPTION_SEARCH_SUGGEST_JQUERY_SELECTOR ] );
			unset( $search_options[ WPSOLR_Option::OPTION_SEARCH_SUGGEST_CONTENT_TYPE ] );
			update_option( WPSOLR_Option::OPTION_SEARCH, $search_options );
		}

	}

}
