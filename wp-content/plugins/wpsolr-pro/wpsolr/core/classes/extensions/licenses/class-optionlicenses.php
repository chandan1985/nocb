<?php

namespace wpsolr\core\classes\extensions\licenses;

use wpsolr\core\classes\extensions\WPSOLR_Extension;
use wpsolr\core\classes\utilities\WPSOLR_Help;

/**
 * Class OptionLicenses
 *
 * Manage licenses options
 */
class OptionLicenses extends OptionLicenses_Root {

	const LICENSE_PACKAGE_WOOCOMMERCE = 'LICENSE_PACKAGE_WOOCOMMERCE';
	const LICENSE_PACKAGE_ACF = 'LICENSE_PACKAGE_ACF';
	const LICENSE_PACKAGE_TOOLSET_TYPES = 'LICENSE_PACKAGE_TYPES';
	const LICENSE_PACKAGE_TOOLSET_VIEWS = 'LICENSE_PACKAGE_TOOLSET_VIEWS';
	const LICENSE_PACKAGE_WPML = 'LICENSE_PACKAGE_WPML';
	const LICENSE_PACKAGE_POLYLANG = 'LICENSE_PACKAGE_POLYLANG';
	const LICENSE_PACKAGE_GROUPS = 'LICENSE_PACKAGE_GROUPS';
	const LICENSE_PACKAGE_S2MEMBER = 'LICENSE_PACKAGE_S2MEMBER';
	const LICENSE_PACKAGE_BBPRESS = 'LICENSE_PACKAGE_BBPRESS';
	const LICENSE_PACKAGE_EMBED_ANY_DOCUMENT = 'LICENSE_PACKAGE_EMBED_ANY_DOCUMENT';
	const LICENSE_PACKAGE_PDF_EMBEDDER = 'LICENSE_PACKAGE_PDF_EMBEDDER';
	const LICENSE_PACKAGE_GOOGLE_DOC_EMBEDDER = 'LICENSE_PACKAGE_GOOGLE_DOC_EMBEDDER';
	const LICENSE_PACKAGE_TABLEPRESS = 'LICENSE_PACKAGE_TABLEPRESS';
	const LICENSE_PACKAGE_GEOLOCATION = 'LICENSE_PACKAGE_GEOLOCATION';
	const LICENSE_PACKAGE_THEME = 'LICENSE_PACKAGE_THEME';
	const LICENSE_PACKAGE_YOAST_SEO = 'LICENSE_PACKAGE_YOAST_SEO';
	const LICENSE_PACKAGE_ALL_IN_ONE_SEO_PACK = 'LICENSE_PACKAGE_ALL_IN_ONE_SEO_PACK';
	const LICENSE_PACKAGE_WP_ALL_IMPORT_PACK = 'LICENSE_PACKAGE_WP_ALL_IMPORT_PACK';
	const LICENSE_PACKAGE_SCORING = 'LICENSE_PACKAGE_SCORING';
	const LICENSE_PACKAGE_YITH_WOOCOMMERCE_AJAX_SEARCH_FREE = 'LICENSE_PACKAGE_YITH_WOOCOMMERCE_AJAX_SEARCH_FREE';
	const LICENSE_PACKAGE_LISTIFY = 'LICENSE_PACKAGE_LISTIFY';
	const LICENSE_PACKAGE_CRON = 'LICENSE_PACKAGE_CRON';
	const LICENSE_PACKAGE_JOBIFY = 'LICENSE_PACKAGE_JOBIFY';
	const LICENSE_PACKAGE_LISTABLE = 'LICENSE_PACKAGE_LISTABLE';
	const LICENSE_PACKAGE_DIRECTORY2 = 'LICENSE_PACKAGE_DIRECTORY2';
	const LICENSE_PACKAGE_AJAX_SEARCH_PRO = 'LICENSE_PACKAGE_AJAX_SEARCH_PRO';
	const LICENSE_PACKAGE_WP_GOOGLE_MAP_PRO = 'LICENSE_PACKAGE_WP_GOOGLE_MAP_PRO';
	const LICENSE_PACKAGE_MYLISTING = 'LICENSE_PACKAGE_MYLISTING';
	const LICENSE_PACKAGE_FLATSOME = 'LICENSE_PACKAGE_FLATSOME';
	const LICENSE_PACKAGE_LISTINGPRO = 'LICENSE_PACKAGE_LISTINGPRO';
	const LICENSE_PACKAGE_AI_API = 'LICENSE_PACKAGE_AI_API';
	const LICENSE_PACKAGE_CROSS_DOMAIN = 'LICENSE_PACKAGE_CROSS_DOMAIN';
	const LICENSE_PACKAGE_JET_SMART_FILTERS = 'LICENSE_PACKAGE_JET_SMART_FILTERS';
	const LICENSE_PACKAGE_EXTENSION_QUERY_MONITOR = 'LICENSE_PACKAGE_EXTENSION_QUERY_MONITOR';
	const LICENSE_PACKAGE_EXTENSION_WP_ROCKET = 'LICENSE_PACKAGE_EXTENSION_WP_ROCKET';
	const LICENSE_PACKAGE_VIEWS = 'LICENSE_PACKAGE_VIEWS';

	public static function get_themes_tabs() {
		return array_merge( parent::get_themes_tabs(), [
			'extension_theme_directory2_opt' => [
				'name'  => '>> Directory+',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_DIRECTORY2, WPSOLR_Extension::EXTENSION_THEME_DIRECTORY2 ),
			],
			'extension_theme_flatsome_opt'   => [
				'name'  => '>> Flatsome',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_FLATSOME, WPSOLR_Extension::EXTENSION_THEME_FLATSOME ),
			],
			'extension_theme_jobify_opt'     => [
				'name'  => '>> Jobify',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_JOBIFY, WPSOLR_Extension::EXTENSION_THEME_JOBIFY ),
			],
			'extension_theme_listable_opt'   => [
				'name'  => '>> Listable',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_LISTABLE, WPSOLR_Extension::EXTENSION_THEME_LISTABLE ),
			],
			'extension_theme_listify_opt'    => [
				'name'  => '>> Listify',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_LISTIFY, WPSOLR_Extension::EXTENSION_THEME_LISTIFY ),
			],
			/*
			'extension_theme_listingpro_opt' => [
				'name'  => '>> ListingPro',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_LISTINGPRO, WPSOLR_Extension::EXTENSION_THEME_LISTINGPRO ),
			],*/
			'extension_theme_mylisting_opt'  => [
				'name'  => '>> MyListing',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_MYLISTING, WPSOLR_Extension::EXTENSION_THEME_MYLISTING ),
			],
		] );
	}

	public static function get_plugins_tabs() {
		return array_merge( parent::get_plugins_tabs(), [
			'extension_acf_opt'                               => [
				'name'  => '>> ACF',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_ACF, WPSOLR_Extension::EXTENSION_ACF ),
			],
			'extension_scoring_opt'                           => [
				'name'  => '>> Advanced scoring',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_SCORING, WPSOLR_Extension::EXTENSION_SCORING ),
			],
			/*'extension_ajax_search_pro'                       => [
				'name'  => '>> Ajax Search Pro',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_AJAX_SEARCH_PRO, WPSOLR_Extension::EXTENSION_AJAX_SEARCH_PRO ),
			],*/
			'extension_all_in_one_seo_opt'                    => [
				'name'  => '>> All in One SEO',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_ALL_IN_ONE_SEO_PACK, WPSOLR_Extension::EXTENSION_ALL_IN_ONE_SEO ),
			],
			'extension_bbpress_opt'                           => [
				'name'  => '>> bbPress',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_BBPRESS, WPSOLR_Extension::EXTENSION_BBPRESS ),
			],
			'extension_cross_domain_opt'                      => [
				'name'  => '>> Cross-domain search',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_CROSS_DOMAIN, WPSOLR_Extension::OPTION_CROSS_DOMAIN ),
			],
			'extension_cron_opt'                              => [
				'name'  => '>> Cron scheduling',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_CRON, WPSOLR_Extension::EXTENSION_CRON ),
			],
			'extension_embed_any_document_opt'                => [
				'name'  => '>> Embed Any Document',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_EMBED_ANY_DOCUMENT, WPSOLR_Extension::EXTENSION_EMBED_ANY_DOCUMENT ),
			],
			'extension_geolocation_opt'                       => [
				'name'  => '>> Geolocation',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_GEOLOCATION, WPSOLR_Extension::EXTENSION_GEOLOCATION ),
			],
			'extension_google_doc_embedder_opt'               => [
				'name'  => '>> Google Doc Embedder',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_GOOGLE_DOC_EMBEDDER, WPSOLR_Extension::EXTENSION_GOOGLE_DOC_EMBEDDER ),
			],
			'extension_groups_opt'                            => [
				'name'  => '>> Groups',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_GROUPS, WPSOLR_Extension::EXTENSION_GROUPS ),
			],
			/*
			'extension_jet_engine_opt'                        => [
				'name'  => '>> Jet Engine',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::EXTENSION_JET_ENGINE, WPSOLR_Extension::EXTENSION_JET_ENGINE ),
			],
			'extension_jet_smart_filters_opt'                 => [
				'name'  => '>> Jet Smart Filters',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::EXTENSION_JET_SMART_FILTERS, WPSOLR_Extension::EXTENSION_JET_SMART_FILTERS ),
			],
			*/
			'extension_ai_api_opt'                            => [
				'name'  => '>> Text, Image, Video AI',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_AI_API, WPSOLR_Extension::OPTION_AI_API ),
			],
			'extension_pdf_embedder_opt'                      => [
				'name'  => '>> PDF Embedder',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_PDF_EMBEDDER, WPSOLR_Extension::EXTENSION_PDF_EMBEDDER ),
			],
			'extension_polylang_opt'                          => [
				'name'  => '>> Polylang',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_POLYLANG, WPSOLR_Extension::EXTENSION_POLYLANG ),
			],
			'extension_query_monitor_opt'                     => [
				'name'  => '>> Query Monitor',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_EXTENSION_QUERY_MONITOR, WPSOLR_Extension::EXTENSION_QUERY_MONITOR ),
			],
			'extension_s2member_opt'                          => [
				'name'  => '>> s2Member',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_S2MEMBER, WPSOLR_Extension::EXTENSION_S2MEMBER ),
			],
			// It seems impossible to map qTranslate X structure (1 post/many languages) in WPSOLR's (1 post/1 language)
			/* 'extension_qtranslatex_opt' => 'qTranslate X', */
			'extension_tablepress_opt'                        => [
				'name'  => '>> TablePress',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_TABLEPRESS, WPSOLR_Extension::EXTENSION_TABLEPRESS ),
			],
			'extension_theme_opt'                             => [
				'name'  => '>> Theme',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_THEME, WPSOLR_Extension::OPTION_THEME ),
			],
			'extension_toolset_types_opt'                     => [
				'name'  => '>> Toolset Types',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_TOOLSET_TYPES, WPSOLR_Extension::EXTENSION_TOOLSET_TYPES ),
			],
			'extension_toolset_views_opt'                     => [
				'name'  => '>> Toolset Views',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_TOOLSET_VIEWS, WPSOLR_Extension::EXTENSION_TOOLSET_VIEWS ),
			],
			'extension_views_opt'                             => [
				'name'  => '>> Views',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_VIEWS, WPSOLR_Extension::OPTION_VIEWS ),
			],
			'extension_yith_woocommerce_ajax_search_free_opt' => [
				'name'  => '>> YITH Ajax Search (Free)',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_YITH_WOOCOMMERCE_AJAX_SEARCH_FREE, WPSOLR_Extension::EXTENSION_YITH_WOOCOMMERCE_AJAX_SEARCH_FREE ),
			],
			'extension_yoast_seo_opt'                         => [
				'name'  => '>> Yoast SEO',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_YOAST_SEO, WPSOLR_Extension::EXTENSION_YOAST_SEO ),
			],
			'extension_woocommerce_opt'                       => [
				'name'  => '>> WooCommerce',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_WOOCOMMERCE, WPSOLR_Extension::EXTENSION_WOOCOMMERCE ),
			],
			'extension_wpml_opt'                              => [
				'name'  => '>> WPML',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_WPML, WPSOLR_Extension::EXTENSION_WPML ),
			],
			'extension_wp_all_import_opt'                     => [
				'name'  => '>> WP All Import',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::EXTENSION_WP_ALL_IMPORT, WPSOLR_Extension::EXTENSION_WP_ALL_IMPORT ),
			],
			'extension_wp_google_map_pro_opt'                 => [
				'name'  => '>> WP Google Map Pro',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_WP_GOOGLE_MAP_PRO, WPSOLR_Extension::EXTENSION_WP_GOOGLE_MAP_PRO ),
			],
			'extension_wp_rocket_opt'                         => [
				'name'  => '>> WP Rocket',
				'class' => wpsolr_get_extension_tab_class( OptionLicenses::LICENSE_PACKAGE_EXTENSION_WP_ROCKET, WPSOLR_Extension::EXTENSION_WP_ROCKET ),
			],
		] );
	}

	/**
	 * Return all license types
	 */
	static function get_license_types() {

		return array_merge( parent::get_license_types(), [
			self::LICENSE_PACKAGE_WOOCOMMERCE                       => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_WOOCOMMERCE,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_woocommerce',
				self::FIELD_LICENSE_TITLE              => 'WooCommerce',
				self::FIELD_DESCRIPTION                => 'WooCommerce Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Index product attributes/variations',
					'Search in product attributes/variations',
					'Create facets on product attributes/variations',
					WPSOLR_Help::get_help( WPSOLR_Help::HELP_SEARCH_ORDERS ) . 'Replace admin orders search with WPSOLR search',
				],
			],
			self::LICENSE_PACKAGE_ACF                               => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_ACF,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_acf',
				self::FIELD_LICENSE_TITLE              => 'ACF',
				self::FIELD_DESCRIPTION                => 'ACF Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Replace facet names with their ACF label',
					'Decode ACF field values before indexing a post',
					'Index ACF field files content inside the post',
					'Group ACF repeater rows under one single facet field (requires ACF Pro 5.0.0)',
					WPSOLR_Help::get_help( WPSOLR_Help::HELP_ACF_REPEATERS_AND_FLEXIBLE_CONTENT_LAYOUTS ) . 'Manage ACF Repeaters and Flexible Content Layouts',
					WPSOLR_Help::get_help( WPSOLR_Help::HELP_ACF_GOOGLE_MAP ) . 'Manage ACF Google Map fields (requires ACF Pro 5.0.0, and our Geolocation Pack )',
				],
			],
			self::LICENSE_PACKAGE_TOOLSET_TYPES                     => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_TOOLSET_TYPES,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_types',
				self::FIELD_LICENSE_TITLE              => 'Toolset Types',
				self::FIELD_DESCRIPTION                => 'Toolset Types Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Replace facet names with their Toolset Types label',
				],
			],
			self::LICENSE_PACKAGE_TOOLSET_VIEWS                     => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_TOOLSET_VIEWS,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_toolset_views',
				self::FIELD_LICENSE_TITLE              => 'Toolset Views',
				self::FIELD_DESCRIPTION                => 'Toolset Views Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Replace Toolset Archive Views queries with WPSOLR queries',
				],
			],
			self::LICENSE_PACKAGE_WPML                              => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_WPML,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_wpml',
				self::FIELD_LICENSE_TITLE              => 'WPML',
				self::FIELD_DESCRIPTION                => 'WPML Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'User can associate WPML languages to their own Solr index',
					'Indexing process send each data to it\'s language related Solr index',
					'Search results are displayed in each WPML languages',
				],
			],
			self::LICENSE_PACKAGE_POLYLANG                          => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_POLYLANG,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_polylang',
				self::FIELD_LICENSE_TITLE              => 'Polylang',
				self::FIELD_DESCRIPTION                => 'Polylang Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'User can associate Polylang languages to their own Solr index',
					'Indexing process send each data to it\'s language related Solr index',
					'Search results are displayed in each Polylang languages',
				],
			],
			self::LICENSE_PACKAGE_GROUPS                            => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_GROUPS,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_groups',
				self::FIELD_LICENSE_TITLE              => 'Groups',
				self::FIELD_DESCRIPTION                => 'Groups Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Results are indexed and filtered with Groups user\'s groups/capabilities',
				],
			],
			self::LICENSE_PACKAGE_S2MEMBER                          => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_S2MEMBER,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_s2member',
				self::FIELD_LICENSE_TITLE              => 's2Member',
				self::FIELD_DESCRIPTION                => 's2Member Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Results are indexed and filtered with s2Member user\'s levels/capabilities capabilities',
				],
			],
			self::LICENSE_PACKAGE_BBPRESS                           => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_BBPRESS,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_bbpress',
				self::FIELD_LICENSE_TITLE              => 'bbPress',
				self::FIELD_DESCRIPTION                => 'bbPress Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Benefit from the Solr search features (speed, relevancy, partial match, fuzzy match ...), while keeping your current bbPress theme.',
				],
			],
			self::LICENSE_PACKAGE_EMBED_ANY_DOCUMENT                => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_EMBED_ANY_DOCUMENT,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_embed_any_document',
				self::FIELD_LICENSE_TITLE              => 'Embed Any Document',
				self::FIELD_DESCRIPTION                => 'Embed Any Document Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Automatically index and search embedded documents with the plugin shortcode.',
				],
			],
			self::LICENSE_PACKAGE_PDF_EMBEDDER                      => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_PDF_EMBEDDER,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_pdf_embedder',
				self::FIELD_LICENSE_TITLE              => 'Pdf Embedder',
				self::FIELD_DESCRIPTION                => 'Pdf Embedder Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Automatically index and search embedded pdfs with the plugin shortcode.',
				],
			],
			self::LICENSE_PACKAGE_GOOGLE_DOC_EMBEDDER               => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_GOOGLE_DOC_EMBEDDER,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_google_doc_embedder',
				self::FIELD_LICENSE_TITLE              => 'Google Doc Embedder',
				self::FIELD_DESCRIPTION                => 'Google Doc Embedder Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Automatically index and search embedded documents with the plugin shortcode.',
				],
			],
			self::LICENSE_PACKAGE_TABLEPRESS                        => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_TABLEPRESS,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_tablepress',
				self::FIELD_LICENSE_TITLE              => 'TablePress',
				self::FIELD_DESCRIPTION                => 'TablePress Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Index TablePress shortcodes content',
					'Format TablePress shortcodes content to remove html tags, before indexing',
				],
			],
			self::LICENSE_PACKAGE_GEOLOCATION                       => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_GEOLOCATION,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_geolocation',
				self::FIELD_LICENSE_TITLE              => 'Geolocation',
				self::FIELD_DESCRIPTION                => 'Geolocation Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Work with latitude and longitude locations (a product\'s store coordinates)',
					'A location is simply a custom field containing a string "latitude,longitude"',
					'Can manage multi-locations configurations (a product with several stores)',
					'Automatic gathering of visitor\'s location',
					'Sort results by distance from the visitor\'s location',
					'Add distance(s) from the visitor\'s location to results\' locations',
					//'Filter results by distance from the visitor\'s location',
				],
			],
			self::LICENSE_PACKAGE_THEME                             => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::OPTION_THEME,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_theme',
				self::FIELD_LICENSE_TITLE              => 'Theme',
				self::FIELD_DESCRIPTION                => 'Theme Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Collapse/Uncollapse facets',
				],
			],
			self::LICENSE_PACKAGE_YOAST_SEO                         => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_YOAST_SEO,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_yoast_seo',
				self::FIELD_LICENSE_TITLE              => 'Yoast SEO',
				self::FIELD_DESCRIPTION                => 'Yoast SEO Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Replace search urls with beautiful permalinks',
					'Add metas to search pages'
				],
			],
			self::LICENSE_PACKAGE_ALL_IN_ONE_SEO_PACK               => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_ALL_IN_ONE_SEO,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_all_in_one_seo_pack',
				self::FIELD_LICENSE_TITLE              => 'All in One SEO Pack',
				self::FIELD_DESCRIPTION                => 'All in One SEO Pack Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Replace search urls with beautiful permalinks',
					'Add metas to search pages'
				],
			],
			self::LICENSE_PACKAGE_WP_ALL_IMPORT_PACK                => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_WP_ALL_IMPORT,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_wp_all_import_pack',
				self::FIELD_LICENSE_TITLE              => 'WP All Import Pack',
				self::FIELD_DESCRIPTION                => 'WP All Import Pack Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Fix posts not removed from the search engine index while deleted by import',
				],
			],
			self::LICENSE_PACKAGE_SCORING                           => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_SCORING,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_scoring',
				self::FIELD_LICENSE_TITLE              => 'Advanced scoring',
				self::FIELD_DESCRIPTION                => 'Advanced scoring Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Add advanced scoring functions to get absolutly crazy search results',
				],
			],
			self::LICENSE_PACKAGE_YITH_WOOCOMMERCE_AJAX_SEARCH_FREE => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_YITH_WOOCOMMERCE_AJAX_SEARCH_FREE,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_yith_woocommerce_ajax_search_free',
				self::FIELD_LICENSE_TITLE              => 'YITH WooCommerce Ajax Search (Free)',
				self::FIELD_DESCRIPTION                => '',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Replace product suggestions template with YITH WooCommerce Ajax Search\'s template',
				],
			],
			self::LICENSE_PACKAGE_LISTIFY                           => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_THEME_LISTIFY,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_listify',
				self::FIELD_LICENSE_TITLE              => 'Listify Theme',
				self::FIELD_DESCRIPTION                => 'Listify Theme Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					WPSOLR_Help::get_help( WPSOLR_Help::HELP_SEARCH_ORDERS ) . 'Replace admin orders search with WPSOLR search',
				],
			],
			self::LICENSE_PACKAGE_CRON                              => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_CRON,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_cron',
				self::FIELD_LICENSE_TITLE              => 'Cron',
				self::FIELD_DESCRIPTION                => 'Cron Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Define one, or several crons, to index your data',
					'Each cron is called with it\'s own REST url. cURL command is provided',
					'Each cron REST url is protected by a Basic authentication',
					'Each cron REST url returns a JSON detailing how many documents where sent, agregated by index',
					'Call sequentially any index in each cron. Reorder the sequence by drag&drop',
					'Call crons in parallel',
				],
			],
			self::LICENSE_PACKAGE_JOBIFY                            => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_THEME_JOBIFY,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_jobify',
				self::FIELD_LICENSE_TITLE              => 'Jobify Theme',
				self::FIELD_DESCRIPTION                => 'Jobify Theme Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
				],
			],
			self::LICENSE_PACKAGE_LISTABLE                          => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_THEME_LISTABLE,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_listable',
				self::FIELD_LICENSE_TITLE              => 'Listable Theme',
				self::FIELD_DESCRIPTION                => 'Listable Theme Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
				],
			],
			self::LICENSE_PACKAGE_DIRECTORY2                        => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_THEME_DIRECTORY2,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_directory2',
				self::FIELD_LICENSE_TITLE              => 'Directory+ Theme',
				self::FIELD_DESCRIPTION                => 'Directory+ Theme Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
				],
			],
			self::LICENSE_PACKAGE_AJAX_SEARCH_PRO                   => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_AJAX_SEARCH_PRO,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_ajax_search_pro',
				self::FIELD_LICENSE_TITLE              => 'Ajax Search Pro',
				self::FIELD_DESCRIPTION                => '',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Replace product suggestions template with Ajax Search Pro\'s template',
				],
			],
			self::LICENSE_PACKAGE_WP_GOOGLE_MAP_PRO                 => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_WP_GOOGLE_MAP_PRO,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_wp_google_map_pro',
				self::FIELD_LICENSE_TITLE              => 'WP Google Map Pro',
				self::FIELD_DESCRIPTION                => '',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Replace map SQL queriy with Elasticsearch/Solr query',
				],
			],
			self::LICENSE_PACKAGE_MYLISTING                         => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_THEME_MYLISTING,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_mylisting',
				self::FIELD_LICENSE_TITLE              => 'MyListing Theme',
				self::FIELD_DESCRIPTION                => 'MyListing Theme Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
				],
			],
			self::LICENSE_PACKAGE_FLATSOME                          => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_THEME_FLATSOME,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_flatsome',
				self::FIELD_LICENSE_TITLE              => 'Flatsome Theme',
				self::FIELD_DESCRIPTION                => 'Flatsome Theme Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
				],
			],
			self::LICENSE_PACKAGE_LISTINGPRO                        => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_THEME_LISTINGPRO,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_listingpro',
				self::FIELD_LICENSE_TITLE              => 'ListingPro Theme',
				self::FIELD_DESCRIPTION                => 'ListingPro Theme Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
				],
			],
			self::LICENSE_PACKAGE_AI_API                            => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::OPTION_AI_API,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_ai_api',
				self::FIELD_LICENSE_TITLE              => 'Text, Image, Video AI',
				self::FIELD_DESCRIPTION                => 'Natural Language description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Collapse/Uncollapse facets',
				],
			],
			self::LICENSE_PACKAGE_CROSS_DOMAIN                      => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::OPTION_CROSS_DOMAIN,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_cross_domain',
				self::FIELD_LICENSE_TITLE              => 'Cross-domain search',
				self::FIELD_DESCRIPTION                => 'Search across several WordPress domains',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
				],
			],
			self::EXTENSION_JET_SMART_FILTERS                       => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_JET_SMART_FILTERS,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_jet_smart_filters',
				self::FIELD_LICENSE_TITLE              => 'JetSmartFilters',
				self::FIELD_DESCRIPTION                => 'JetSmartFilters Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Replace JetSmartFilters queries with WPSOLR queries',
				],
			],
			self::EXTENSION_JET_ENGINE                              => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_JET_ENGINE,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_jet_engine',
				self::FIELD_LICENSE_TITLE              => 'JetEngine',
				self::FIELD_DESCRIPTION                => 'JetEngine Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Replace JetEngine queries with WPSOLR queries',
				],
			],
			self::LICENSE_PACKAGE_EXTENSION_QUERY_MONITOR           => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_QUERY_MONITOR,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_query_monitor',
				self::FIELD_LICENSE_TITLE              => 'Query Monitor',
				self::FIELD_DESCRIPTION                => 'Query Monitor Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Show WPSOLR queries in Query Monitor',
				],
			],
			self::LICENSE_PACKAGE_EXTENSION_WP_ROCKET               => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::EXTENSION_WP_ROCKET,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_wp_rocket',
				self::FIELD_LICENSE_TITLE              => 'WP Rocket',
				self::FIELD_DESCRIPTION                => 'WP Rocket Extension description',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
					'Fix deferred JS error',
				],
			],
			self::LICENSE_PACKAGE_VIEWS                             => [
				self::LICENSE_EXTENSION                => WPSOLR_Extension::OPTION_VIEWS,
				self::FIELD_LICENSE_MATCHING_REFERENCE => 'wpsolr_package_views',
				self::FIELD_LICENSE_TITLE              => 'Views',
				self::FIELD_DESCRIPTION                => '',
				self::FIELD_ORDERS_URLS                => [
					[
						self::FIELD_ORDER_URL_BUTTON_LABEL => self::FIELD_ORDER_URL_BUTTON_LABEL_BESPOKE,
						self::FIELD_ORDER_URL_TEXT         => 'Order a pack now',
						self::FIELD_ORDER_URL_LINK         => self::ORDER_LINK_URL_BESPOKE,
					],
				],
				self::FIELD_FEATURES                   => [
					self::FEATURE_ZENDESK_SUPPORT,
					self::FEATURE_FREE_UPGRADE_ONE_YEAR,
				],
			],
		] );

	}
}

// Register Ajax events
add_action( 'wp_ajax_' . OptionLicenses::AJAX_ACTIVATE_LICENCE, [
	OptionLicenses::class,
	OptionLicenses::AJAX_ACTIVATE_LICENCE
] );

add_action( 'wp_ajax_' . OptionLicenses::AJAX_DEACTIVATE_LICENCE, [
	OptionLicenses::class,
	OptionLicenses::AJAX_DEACTIVATE_LICENCE
] );

add_action( 'wp_ajax_' . OptionLicenses::AJAX_VERIFY_LICENCE, [
	OptionLicenses::class,
	OptionLicenses::AJAX_VERIFY_LICENCE
] );
