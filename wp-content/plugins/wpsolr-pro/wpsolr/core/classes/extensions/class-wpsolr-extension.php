<?php

namespace wpsolr\core\classes\extensions;

use wpsolr\core\classes\ui\layout\WPSOLR_UI_Layout_Abstract;
use wpsolr\core\classes\utilities\WPSOLR_Option;
use wpsolr\pro\extensions\acf\WPSOLR_Plugin_Acf;
use wpsolr\pro\extensions\ai_api\WPSOLR_Option_AI_Api;
use wpsolr\pro\extensions\ajax_search_pro\WPSOLR_Plugin_Ajax_Search_Pro;
use wpsolr\pro\extensions\all_in_one_seo_pack\WPSOLR_Plugin_AllInOneSeoPack;
use wpsolr\pro\extensions\bbpress\WPSOLR_Plugin_BbPress;
use wpsolr\pro\extensions\cron\WPSOLR_Option_Cron;
use wpsolr\pro\extensions\cross_domain\WPSOLR_Option_Cross_domain;
use wpsolr\pro\extensions\embed_any_document\WPSOLR_Plugin_EmbedAnyDocument;
use wpsolr\pro\extensions\geolocation\WPSOLR_Option_GeoLocation;
use wpsolr\pro\extensions\google_doc_embedder\WPSOLR_Plugin_GoogleDocEmbedder;
use wpsolr\pro\extensions\groups\WPSOLR_Plugin_Groups;
use wpsolr\pro\extensions\jet_engine\WPSOLR_Plugin_Jet_Engine;
use wpsolr\pro\extensions\jet_smart_filters\WPSOLR_Plugin_Jet_Smart_Filters;
use wpsolr\pro\extensions\pdf_embedder\WPSOLR_Plugin_PdfEmbedder;
use wpsolr\pro\extensions\polylang\WPSOLR_Plugin_Polylang;
use wpsolr\pro\extensions\query_monitor\WPSOLR_Plugin_Query_Monitor;
use wpsolr\pro\extensions\recommendations\WPSOLR_Option_Recommendations;
use wpsolr\pro\extensions\s2member\WPSOLR_Plugin_S2Member;
use wpsolr\pro\extensions\scoring\WPSOLR_Option_Scoring;
use wpsolr\pro\extensions\tablepress\WPSOLR_Plugin_TablePress;
use wpsolr\pro\extensions\theme\layout\range_regular_checkboxes\WPSOLR_UI_Layout_Range_Regular_Check_box;
use wpsolr\pro\extensions\theme\layout\range_regular_radioboxes\WPSOLR_UI_Layout_Range_Regular_Radio_Box;
use wpsolr\pro\extensions\theme\WPSOLR_Option_Theme;
use wpsolr\pro\extensions\theme_directory2\WPSOLR_Theme_Directory2;
use wpsolr\pro\extensions\theme_flatsome\WPSOLR_Theme_Flatsome;
use wpsolr\pro\extensions\theme_jobify\WPSOLR_Theme_Jobify;
use wpsolr\pro\extensions\theme_listable\WPSOLR_Theme_Listable;
use wpsolr\pro\extensions\theme_listify\WPSOLR_Theme_Listify;
use wpsolr\pro\extensions\theme_listingpro\WPSOLR_Theme_ListingPro;
use wpsolr\pro\extensions\theme_mylisting\WPSOLR_Theme_Mylisting;
use wpsolr\pro\extensions\toolset_types\WPSOLR_Plugin_Toolset_Types;
use wpsolr\pro\extensions\toolset_views\WPSOLR_Plugin_Toolset_Views;
use wpsolr\pro\extensions\woocommerce\WPSOLR_Plugin_WooCommerce;
use wpsolr\pro\extensions\wp_all_import\WPSOLR_Plugin_WPAllImport;
use wpsolr\pro\extensions\wp_google_map_pro\WPSOLR_Plugin_WP_Google_Map_Pro;
use wpsolr\pro\extensions\wp_rocket\WPSOLR_Plugin_WP_Rocket;
use wpsolr\pro\extensions\wpml\WPSOLR_Plugin_Wpml;
use wpsolr\pro\extensions\yoast_seo\WPSOLR_Plugin_YoastSeo;

/**
 * Base class for all WPSOLR extensions.
 * An extension is an encapsulation of a plugin that (if configured) might extend some features of WPSOLR.
 */

/**
 * Class WPSOLR_Extension
 * @package wpsolr\core\classes\extensions
 */
class WPSOLR_Extension extends WPSOLR_Extension_Root {

	// Extension: Groups
	const EXTENSION_GROUPS = 'Groups';

	// Extension: s2member
	const EXTENSION_S2MEMBER = 'S2Member';

	// Extension: WPML
	const EXTENSION_WPML = 'WPML';

	// Extension: POLYLANG
	const EXTENSION_POLYLANG = 'Polylang';

	// Extension: qTranslate X
	const EXTENSION_QTRANSLATEX = 'qTranslate X';

	// Extension: WooCommerce
	const EXTENSION_WOOCOMMERCE = 'WooCommerce';

	// Extension: Advanced Custom Fields
	const EXTENSION_ACF = 'ACF';

	// Extension: Toolset Types
	const EXTENSION_TOOLSET_TYPES = 'Toolset Types';

	// Extension: Toolset Views
	const EXTENSION_TOOLSET_VIEWS = 'Toolset Views';

	// Extension: bbpress
	const EXTENSION_BBPRESS = 'bbpress';

	// Extension: Embed Any Document
	const EXTENSION_EMBED_ANY_DOCUMENT = 'embed any document';

	// Extension: Pdf Embedder
	const EXTENSION_PDF_EMBEDDER = 'pdf embedder';

	// Extension: Google Doc Embedder
	const EXTENSION_GOOGLE_DOC_EMBEDDER = 'google doc embedder';

	// Extension: TablePress
	const EXTENSION_TABLEPRESS = 'tablepress';

	// Extension Geolocation
	const EXTENSION_GEOLOCATION = 'wpsolr_geolocation';

	// Option: theme
	const OPTION_THEME = 'wpsolr_theme';

	// Extension Yoast seo
	const EXTENSION_YOAST_SEO = 'wpsolr_yoast_seo';

	// Extension All in One SEO Pack
	const EXTENSION_ALL_IN_ONE_SEO = 'wpsolr_all_in_one_seo_pack';

	// Extension WP All Import
	const EXTENSION_WP_ALL_IMPORT = 'wpsolr_wp_all_import';

	// Extension Scoring
	const EXTENSION_SCORING = 'wpsolr_scoring';

	// Theme: Listify
	const EXTENSION_THEME_LISTIFY = 'listify';

	// Extension crons
	const EXTENSION_CRON = 'wpsolr_cron';

	// Theme: Jobify
	const EXTENSION_THEME_JOBIFY = 'jobify';

	// Theme: Listable
	const EXTENSION_THEME_LISTABLE = 'listable';

	// Theme: Directory2
	const EXTENSION_THEME_DIRECTORY2 = 'directory2';

	// Extension: Ajax Search Pro
	const EXTENSION_AJAX_SEARCH_PRO = 'EXTENSION_AJAX_SEARCH_PRO';

	// Extension: WP Google Map Pro - https://www.wpmapspro.com/
	const EXTENSION_WP_GOOGLE_MAP_PRO = 'EXTENSION_WP_GOOGLE_MAP_PRO';

	// Theme: Mylisting
	const EXTENSION_THEME_MYLISTING = 'mylisting';

	// Theme: Flatsome
	const EXTENSION_THEME_FLATSOME = 'flatsome';

	// Theme: ListingPro
	const EXTENSION_THEME_LISTINGPRO = 'listingpro';

	// Option: AI APIs
	const OPTION_AI_API = 'ai_api';

	// Option: cross-domain search
	const OPTION_CROSS_DOMAIN = 'cross_domain';

	// Plugin: JetSmartFilters
	const EXTENSION_JET_SMART_FILTERS = 'jet_smart_filters';

	// Plugin: JetEngine
	const EXTENSION_JET_ENGINE = 'jet_engine';

	// Plugin: Query Monitor
	const EXTENSION_QUERY_MONITOR = 'query_monitor';

	// Extension: WP Rocket
	const EXTENSION_WP_ROCKET = 'wp_rocket';

	// Option: Recommendations
	const OPTION_RECOMMENDATIONS = 'recommendations';

	// Option: event tracking
	const OPTION_EVENT_TRACKINGS = 'event_trackings';

	/**
	 * Get feature layouts
	 * @return array[]
	 */
	public static function get_feature_layouts_ids(): array {
		return array_merge( parent::get_feature_layouts_ids(), [
			WPSOLR_UI_Layout_Abstract::FEATURE_SEO_TEMPLATE_RANGE => [
				WPSOLR_UI_Layout_Range_Regular_Check_Box::CHILD_LAYOUT_ID,
				WPSOLR_UI_Layout_Range_Regular_Radio_Box::CHILD_LAYOUT_ID,
			],
		] );
	}

	/*
	 * Extensions configuration
	 * @return array[]
	 */
	public static function get_extensions_def(): array {
		/**
		 * Views must be loaded first, else during activation the list of views is not filled
		 */
		return array_merge( parent::get_extensions_def(), [
			self::EXTENSION_GROUPS              =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_Groups::class,
					self::_CONFIG_PLUGIN_CLASS_NAME                 => 'Groups_WordPress',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'groups/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'groups/class-plugingroups.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'groups/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_EXTENSION_GROUPS,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_S2MEMBER            =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_S2Member::class,
					self::_CONFIG_PLUGIN_CLASS_NAME                 => 'c_ws_plugin__s2member_utils_s2o',
					self::_CONFIG_EXTENSION_DIRECTORY               => 's2member/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 's2member/class-plugins2member.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 's2member/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_EXTENSION_S2MEMBER,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_WPML                =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_Wpml::class,
					self::_CONFIG_PLUGIN_CLASS_NAME                 => 'SitePress',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'wpml/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'wpml/class-pluginwpml.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'wpml/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_EXTENSION_WPML,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_POLYLANG            =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_Polylang::class,
					self::_CONFIG_PLUGIN_FUNCTION_NAME              => 'pll_get_post',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'polylang/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'polylang/class-pluginpolylang.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'polylang/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_EXTENSION_POLYLANG,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_WOOCOMMERCE         =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_WooCommerce::class,
					self::_CONFIG_PLUGIN_CLASS_NAME                 => 'WooCommerce',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'woocommerce/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'woocommerce/class-pluginwoocommerce.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'woocommerce/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_EXTENSION_WOOCOMMERCE,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_ACF                 =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_Acf::class,
					self::_CONFIG_PLUGIN_CLASS_NAME                 => 'acf',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'acf/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'acf/class-pluginacf.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'acf/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_EXTENSION_ACF,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_TOOLSET_TYPES       =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_Toolset_Types::class,
					self::_CONFIG_PLUGIN_CLASS_NAME                 => 'WPCF_Field',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'toolset_types/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'toolset_types/class-plugintypes.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'toolset_types/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_EXTENSION_TOOLSET_TYPES,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_TOOLSET_VIEWS       =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_Toolset_Views::class,
					self::_CONFIG_PLUGIN_CONSTANT_NAME              => 'WPV_VERSION',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'toolset_views/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'toolset_views/class-plugintypes.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'toolset_views/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_EXTENSION_TOOLSET_VIEWS,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_BBPRESS             =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_BbPress::class,
					self::_CONFIG_PLUGIN_CLASS_NAME                 => 'bbPress',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'bbpress/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'bbpress/class-pluginbbpress.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'bbpress/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_EXTENSION_BBPRESS,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_EMBED_ANY_DOCUMENT  =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_EmbedAnyDocument::class,
					self::_CONFIG_PLUGIN_CLASS_NAME                 => 'Awsm_embed',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'embed_any_document/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'embed_any_document/class-pluginembedanydocument.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'embed_any_document/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_EXTENSION_EMBED_ANY_DOCUMENT,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_PDF_EMBEDDER        =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_PdfEmbedder::class,
					self::_CONFIG_PLUGIN_CLASS_NAME                 => 'pdfemb_basic_pdf_embedder',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'pdf_embedder/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'pdf_embedder/class-pluginpdfembedder.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'pdf_embedder/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_EXTENSION_PDF_EMBEDDER,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_GOOGLE_DOC_EMBEDDER =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_GoogleDocEmbedder::class,
					self::_CONFIG_PLUGIN_CONSTANT_NAME              => 'GDE_PLUGIN_DIR',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'google_doc_embedder/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'google_doc_embedder/class-plugingoogledocembedder.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'google_doc_embedder/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_EXTENSION_GOOGLE_DOC_EMBEDDER,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_TABLEPRESS          =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_TablePress::class,
					self::_CONFIG_PLUGIN_CLASS_NAME                 => 'TablePress',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'tablepress/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'tablepress/class-plugintablepress.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'tablepress/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_EXTENSION_TABLEPRESS,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_GEOLOCATION         =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Option_GeoLocation::class,
					self::_CONFIG_PLUGIN_IS_AUTO_ACTIVE             => true,
					self::_CONFIG_EXTENSION_DIRECTORY               => 'geolocation/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'geolocation/class-option-geolocation.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'geolocation/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_EXTENSION_GEOLOCATION,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::OPTION_THEME                  =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Option_Theme::class,
					self::_CONFIG_PLUGIN_IS_AUTO_ACTIVE             => true,
					self::_CONFIG_EXTENSION_DIRECTORY               => 'theme/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'theme/class-optiontheme.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'theme/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_THEME,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_YOAST_SEO           =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_YoastSeo::class,
					self::_CONFIG_PLUGIN_CONSTANT_NAME              => 'WPSEO_FILE',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'yoast_seo/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'yoast_seo/plugin-yoast-seo.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'yoast_seo/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_YOAST_SEO,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_ALL_IN_ONE_SEO      =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_AllInOneSeoPack::class,
					self::_CONFIG_PLUGIN_CONSTANT_NAME              => 'AIOSEO_VERSION', // used to be 'AIOSEOP_VERSION'
					self::_CONFIG_EXTENSION_DIRECTORY               => 'all_in_one_seo_pack/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'all_in_one_seo_pack/class-pluginallinoneseopack.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'all_in_one_seo_pack/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_ALL_IN_ONE_SEO_PACK,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_WP_ALL_IMPORT       =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_WPAllImport::class,
					self::_CONFIG_PLUGIN_CONSTANT_NAME              => 'PMXI_VERSION',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'wp_all_import/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'wp_all_import/class-pluginwpallimport.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'wp_all_import/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_WP_ALL_IMPORT,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_SCORING             =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Option_Scoring::class,
					self::_CONFIG_PLUGIN_IS_AUTO_ACTIVE             => true,
					self::_CONFIG_EXTENSION_DIRECTORY               => 'scoring/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'scoring/class-option-scoring.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'scoring/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_SCORING,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_THEME_LISTIFY       =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Theme_Listify::class,
					self::_CONFIG_PLUGIN_CLASS_NAME                 => 'Listify_Activation',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'theme_listify/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'theme_listify/class-wpsolr-theme-listify.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'theme_listify/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_THEME_LISTIFY,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_THEME_LISTABLE      =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Theme_Listable::class,
					self::_CONFIG_PLUGIN_FUNCTION_NAME              => 'listable_setup',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'theme_listable/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'theme_listable/class-wpsolr-theme-listable.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'theme_listable/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_THEME_LISTABLE,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_THEME_DIRECTORY2    =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Theme_Directory2::class,
					self::_CONFIG_PLUGIN_CONSTANT_NAME              => 'AIT_THEME_ACTIVE_DIRECTORY2',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'theme_directory2/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'theme_directory2/class-wpsolr-theme-directory2.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'theme_directory2/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_THEME_DIRECTORY2,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_CRON                =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Option_Cron::class,
					self::_CONFIG_PLUGIN_IS_AUTO_ACTIVE             => true,
					self::_CONFIG_EXTENSION_DIRECTORY               => 'cron/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'cron/class-option-cron.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'cron/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_CRON,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_THEME_JOBIFY        =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Theme_Jobify::class,
					self::_CONFIG_PLUGIN_CLASS_NAME                 => 'Jobify_Activation',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'theme_jobify/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'theme_jobify/class-wpsolr-theme-jobify.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'theme_jobify/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_THEME_JOBIFY,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_AJAX_SEARCH_PRO     =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_Ajax_Search_Pro::class,
					self::_CONFIG_PLUGIN_CONSTANT_NAME              => 'ASP_PLUGIN_NAME',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'ajax_search_pro/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'ajax_search_pro/class-wpsolr-plugin-ajax-search-pro.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'ajax_search_pro/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_EXTENSION_AJAX_SEARCH_PRO,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_WP_GOOGLE_MAP_PRO   =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_WP_Google_Map_Pro::class,
					self::_CONFIG_PLUGIN_CONSTANT_NAME              => 'WPGMP_VERSION',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'wp_google_map_pro/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'wp_google_map_pro/class-wpsolr-plugin-wp-google-map-pro.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'wp_google_map_pro/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_EXTENSION_WP_GOOGLE_MAP_PRO,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_THEME_MYLISTING     =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Theme_Mylisting::class,
					self::_CONFIG_PLUGIN_FUNCTION_NAME              => 'mylisting',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'theme_mylisting/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'theme_mylisting/class-wpsolr-theme-mylisting.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'theme_mylisting/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_THEME_MYLISTING,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_THEME_FLATSOME      =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Theme_Flatsome::class,
					self::_CONFIG_PLUGIN_CLASS_NAME                 => 'Flatsome_Upgrade',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'theme_flatsome/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'theme_flatsome/class-wpsolr-theme-flatsome.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'theme_flatsome/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_THEME_FLATSOME,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_THEME_LISTINGPRO    =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Theme_ListingPro::class,
					self::_CONFIG_PLUGIN_CONSTANT_NAME              => 'CRIDIO_API_URL',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'theme_listingpro/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'theme_listingpro/class-wpsolr-theme-listingpro.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'theme_listingpro/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_THEME_LISTINGPRO,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::OPTION_AI_API                 =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Option_AI_Api::class,
					self::_CONFIG_PLUGIN_IS_AUTO_ACTIVE             => true,
					self::_CONFIG_EXTENSION_DIRECTORY               => 'ai_api/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'ai_api/class-wpsolr-option-ai-api.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'ai_api/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_AI_API,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::OPTION_CROSS_DOMAIN           =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Option_Cross_domain::class,
					self::_CONFIG_PLUGIN_IS_AUTO_ACTIVE             => true,
					self::_CONFIG_EXTENSION_DIRECTORY               => 'cross_domain/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'cross_domain/class-wpsolr-option-cross-domain.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'cross_domain/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_CROSS_DOMAIN,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_JET_SMART_FILTERS   =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_Jet_Smart_Filters::class,
					self::_CONFIG_PLUGIN_FUNCTION_NAME              => 'jet_smart_filters',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'jet_smart_filters/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'jet_smart_filters/class-wpsolr-plugin-jet-smart-filters.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'jet_smart_filters/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_EXTENSION_JET_SMART_FILTERS,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_JET_ENGINE          =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_Jet_Engine::class,
					self::_CONFIG_PLUGIN_FUNCTION_NAME              => 'jet_engine',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'jet_engine/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'jet_engine/class-wpsolr-plugin-jet-smart-filters.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'jet_engine/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_EXTENSION_JET_ENGINE,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_QUERY_MONITOR       =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_Query_Monitor::class,
					self::_CONFIG_PLUGIN_CLASS_NAME                 => 'QM_Collectors',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'query_monitor/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'query_monitor/class-wpsolr-plugin-query-monitor.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'query_monitor/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_EXTENSION_QUERY_MONITOR,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			self::EXTENSION_WP_ROCKET           =>
				[
					self::_CONFIG_IS_PRO                            => true,
					self::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Plugin_WP_Rocket::class,
					self::_CONFIG_PLUGIN_CONSTANT_NAME              => 'WP_ROCKET_VERSION',
					self::_CONFIG_EXTENSION_DIRECTORY               => 'wp_rocket/',
					self::_CONFIG_EXTENSION_FILE_PATH               => 'wp_rocket/class-plugin_wp_rocket.php',
					self::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'wp_rocket/admin_options.inc.php',
					self::_CONFIG_OPTIONS                           => [
						self::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_EXTENSION_WP_ROCKET,
						self::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
			static::OPTION_RECOMMENDATIONS      =>
				[
					static::_CONFIG_IS_PRO                            => false,
					static::_CONFIG_EXTENSION_CLASS_NAME              => WPSOLR_Option_Recommendations::class,
					static::_CONFIG_PLUGIN_IS_AUTO_ACTIVE             => WPSOLR_Option_Recommendations::IS_RECOMMENDATIONS_IMPLEMENTED,
					static::_CONFIG_EXTENSION_DIRECTORY               => 'recommendations/',
					static::_CONFIG_EXTENSION_FILE_PATH               => 'recommendations/class-wpsolr-option-recommendations.php',
					static::_CONFIG_EXTENSION_ADMIN_OPTIONS_FILE_PATH => 'recommendations/admin_options.inc.php',
					static::_CONFIG_OPTIONS                           => [
						static::_CONFIG_OPTIONS_DATA                 => WPSOLR_Option::OPTION_RECOMMENDATIONS,
						static::_CONFIG_OPTIONS_IS_ACTIVE_FIELD_NAME => 'is_extension_active',
					],
				],
		] );
	}

}
