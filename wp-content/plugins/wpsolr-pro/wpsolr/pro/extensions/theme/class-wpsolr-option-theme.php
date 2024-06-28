<?php

namespace wpsolr\pro\extensions\theme;

use wpsolr\core\classes\extensions\WPSOLR_Extension;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\ui\layout\checkboxes\WPSOLR_UI_Layout_Check_Box;
use wpsolr\core\classes\ui\layout\select\WPSOLR_UI_Layout_Select;
use wpsolr\core\classes\ui\layout\WPSOLR_UI_Layout_Abstract;
use wpsolr\core\classes\utilities\WPSOLR_Help;
use wpsolr\core\classes\utilities\WPSOLR_Option;
use wpsolr\core\classes\WPSOLR_Events;
use wpsolr\core\classes\WpSolrSchema;
use wpsolr\pro\extensions\theme\layout\color_picker\WPSOLR_UI_Layout_Color_Picker;
use wpsolr\pro\extensions\theme\layout\date_picker\WPSOLR_UI_Layout_Date_Picker;
use wpsolr\pro\extensions\theme\layout\ion_range_slider\WPSOLR_UI_Layout_Ion_Range_slider;
use wpsolr\pro\extensions\theme\layout\radioboxes\WPSOLR_UI_Layout_Radio_Box;
use wpsolr\pro\extensions\theme\layout\range_irregular_checkboxes\WPSOLR_UI_Layout_Range_Irregular_Check_box;
use wpsolr\pro\extensions\theme\layout\range_irregular_radioboxes\WPSOLR_UI_Layout_Range_Irregular_Radio_Box;
use wpsolr\pro\extensions\theme\layout\range_regular_checkboxes\WPSOLR_UI_Layout_Range_Regular_Check_box;
use wpsolr\pro\extensions\theme\layout\range_regular_radioboxes\WPSOLR_UI_Layout_Range_Regular_Radio_Box;
use wpsolr\pro\extensions\theme\layout\rating_stars\WPSOLR_UI_Layout_Rating_Stars;
use wpsolr\pro\extensions\theme\layout\select2\WPSOLR_UI_Layout_Select2;

/**
 * Class WPSOLR_Option_Theme
 * @package wpsolr\pro\extensions\theme
 *
 * Manage theme settings for widgets
 */
class WPSOLR_Option_Theme extends WPSOLR_Extension {

	/**
	 * Remove the top level of the category facet hierarchy on category pages.
	 * top_cat =>
	 * top_cat->current_cat =>
	 * top_cat->current_cat->sub_cat => sub_cat
	 */
	const REGEX_SUB_CATEGORIES = '/%%s%s(.*)/';

	const OVERRIDE_THE_LABEL_OF_EACH_RANGE = 'Customize the label of ranges';

	const FIELD_LAYOUT_SKINS = 'skins';
	const FIELD_LABEL = 'label';
	const FIELD_SKIN_URL = 'url';
	const FIELD_OBJECT_CLASS_NAME = 'object_class_name';
	const FIELD_CSS_CLASS_NAME = 'css_class_name';
	const FIELD_LAYOUT_FILES = 'layout_files';
	const FIELD_CSS_FILES = 'css';
	const FIELD_JS_FILES = 'js';
	const FIELD_JS_HELP = 'js_help';

	// Layout class name parameter enqueued with the js layout script
	const FIELD_JS_LAYOUT_CLASS = 'js_layout_class';
	const FIELD_JS_LAYOUT_FILES = 'js_layout_files';
	const JS_FILE_ENQUEUED_PARAMETERS = 'wpsolr_localize_script_layout';

	// Class of all ion range slider objects
	const ION_RANGE_SLIDER_CLASS = 'wpsolr-ion-range-slider';

	/**
	 * Facet class names for each facet type
	 */
	const WPSOLR_FACET_CHECKBOX_CLASS = 'wpsolr_facet_checkbox';
	const WPSOLR_FACET_RADIOBOX_CLASS = 'wpsolr_facet_radiobox';
	const WPSOLR_FACET_COLOR_PICKER_CLASS = 'wpsolr_facet_color_picker';

	const WPSOLR_FACET_SLIDER_ION_CLASS = 'wpsolr_facet_slider_ion';
	const WPSOLR_FACET_SLIDER_ION_SKIN_FLAT_CLASS = 'wpsolr_facet_slider_ion_skin_flat';
	const WPSOLR_FACET_SLIDER_ION_SKIN_HTML5_CLASS = 'wpsolr_facet_slider_ion_skin_html5';
	const WPSOLR_FACET_SLIDER_ION_SKIN_MODERN_CLASS = 'wpsolr_facet_slider_ion_skin_modern';
	const WPSOLR_FACET_SLIDER_ION_SKIN_NICE_CLASS = 'wpsolr_facet_slider_ion_skin_nice';
	const WPSOLR_FACET_SLIDER_ION_SKIN_SIMPLE_CLASS = 'wpsolr_facet_slider_ion_skin_simple';

	const WPSOLR_FACET_SELECT2_CLASS = 'wpsolr_facet_select2';
	const WPSOLR_FACET_SELECT2_SKIN_FLAT_CLASS = 'wpsolr_facet_select2_skin_classic';

	const WPSOLR_FACET_SELECT_CLASS = 'wpsolr_facet_select';

	/**
	 * Mapping of layout id to layout class
	 * @var array $layout_classes
	 */
	static protected $layout_classes = [
		WPSOLR_UI_Layout_Check_Box::CHILD_LAYOUT_ID                 => WPSOLR_UI_Layout_Check_Box::class,
		WPSOLR_UI_Layout_Color_Picker::CHILD_LAYOUT_ID              => WPSOLR_UI_Layout_Color_Picker::class,
		WPSOLR_UI_Layout_Date_Picker::CHILD_LAYOUT_ID               => WPSOLR_UI_Layout_Date_Picker::class,
		WPSOLR_UI_Layout_Range_Irregular_Check_box::CHILD_LAYOUT_ID => WPSOLR_UI_Layout_Range_Irregular_Check_box::class,
		WPSOLR_UI_Layout_Range_Irregular_Radio_Box::CHILD_LAYOUT_ID => WPSOLR_UI_Layout_Range_Irregular_Radio_Box::class,
		WPSOLR_UI_Layout_Radio_Box::CHILD_LAYOUT_ID                 => WPSOLR_UI_Layout_Radio_Box::class,
		WPSOLR_UI_Layout_Range_Regular_Radio_Box::CHILD_LAYOUT_ID   => WPSOLR_UI_Layout_Range_Regular_Radio_Box::class,
		WPSOLR_UI_Layout_Range_Regular_Check_box::CHILD_LAYOUT_ID   => WPSOLR_UI_Layout_Range_Regular_Check_box::class,
		WPSOLR_UI_Layout_Select::CHILD_LAYOUT_ID                    => WPSOLR_UI_Layout_Select::class,
		WPSOLR_UI_Layout_Select2::CHILD_LAYOUT_ID                   => WPSOLR_UI_Layout_Select2::class,
		WPSOLR_UI_Layout_Ion_Range_slider::CHILD_LAYOUT_ID          => WPSOLR_UI_Layout_Ion_Range_slider::class,
		WPSOLR_UI_Layout_Rating_Stars::CHILD_LAYOUT_ID              => WPSOLR_UI_Layout_Rating_Stars::class,
	];

	/**
	 * Get feature layouts
	 * @return array[]
	 */
	public static function get_feature_layouts_ids(): array {
		return array_merge( parent::get_feature_layouts_ids(), [
				WPSOLR_UI_Layout_Abstract::FEATURE_IS_HIDE_IF_NO_CHOICE      =>
					[
						WPSOLR_UI_Layout_Radio_Box::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Check_Box::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Rating_Stars::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Range_Regular_Check_box::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Range_Irregular_Check_box::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Color_Picker::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Range_Regular_Radio_Box::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Range_Irregular_Radio_Box::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Date_Picker::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Select::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Select2::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Ion_Range_slider::CHILD_LAYOUT_ID,
					],
				WPSOLR_UI_Layout_Abstract::FEATURE_GRID                      =>
					[
						WPSOLR_UI_Layout_Radio_Box::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Check_Box::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Rating_Stars::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Range_Regular_Check_Box::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Range_Irregular_Check_Box::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Color_Picker::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Range_Regular_Radio_Box::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Date_Picker::CHILD_LAYOUT_ID,
					],
				WPSOLR_UI_Layout_Abstract::FEATURE_EXCLUSION                 =>
					[
						WPSOLR_UI_Layout_Radio_Box::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Check_Box::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Rating_Stars::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Range_Regular_Check_box::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Range_Irregular_Check_box::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Color_Picker::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Range_Regular_Radio_Box::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Range_Irregular_Radio_Box::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Date_Picker::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Select::CHILD_LAYOUT_ID,
						WPSOLR_UI_Layout_Select2::CHILD_LAYOUT_ID,
					],
				WPSOLR_UI_Layout_Abstract::FEATURE_HIERARCHY                 => [
					WPSOLR_UI_Layout_Check_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Radio_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Select::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Select2::CHILD_LAYOUT_ID,
				],
				WPSOLR_UI_Layout_Abstract::FEATURE_OR                        => [
					WPSOLR_UI_Layout_Check_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Color_Picker::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Range_Irregular_Check_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Range_Regular_Check_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Rating_Stars::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Select::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Select2::CHILD_LAYOUT_ID,
				],
				WPSOLR_UI_Layout_Abstract::FEATURE_SORT_ALPHABETICALLY       => [
					WPSOLR_UI_Layout_Check_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Radio_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Color_Picker::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Select::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Select2::CHILD_LAYOUT_ID,

				],
				WPSOLR_UI_Layout_Abstract::FEATURE_LOCALIZATION              => [
					WPSOLR_UI_Layout_Radio_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Check_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Rating_Stars::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Range_Regular_Check_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Range_Irregular_Check_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Color_Picker::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Range_Regular_Radio_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Date_Picker::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Select::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Select2::CHILD_LAYOUT_ID,
				],
				WPSOLR_UI_Layout_Abstract::FEATURE_LOCALIZATION_FIELD        => [
					WPSOLR_UI_Layout_Range_Regular_Radio_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Range_Irregular_Check_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Range_Regular_Check_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Check_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Radio_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Range_Irregular_Radio_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Select::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Select2::CHILD_LAYOUT_ID,
				],
				WPSOLR_UI_Layout_Abstract::FEATURE_SEO_TEMPLATE              => [
					WPSOLR_UI_Layout_Radio_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Check_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Rating_Stars::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Color_Picker::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Date_Picker::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Select::CHILD_LAYOUT_ID, // does not work
					WPSOLR_UI_Layout_Select2::CHILD_LAYOUT_ID,
				],
				WPSOLR_UI_Layout_Abstract::FEATURE_SEO_TEMPLATE_LOCALIZATION => [
					WPSOLR_UI_Layout_Color_Picker::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Range_Regular_Radio_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Range_Irregular_Check_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Range_Regular_Check_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Check_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Radio_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Range_Irregular_Radio_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Select::CHILD_LAYOUT_ID, // does not work
					WPSOLR_UI_Layout_Select2::CHILD_LAYOUT_ID,
				],
				WPSOLR_UI_Layout_Abstract::FEATURE_SEO_TEMPLATE_RANGE        => [
					WPSOLR_UI_Layout_Range_Regular_Check_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Range_Regular_Radio_Box::CHILD_LAYOUT_ID,
				],
				WPSOLR_UI_Layout_Abstract::FEATURE_JAVASCRIPT                => [
					WPSOLR_UI_Layout_Ion_Range_slider::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Select2::CHILD_LAYOUT_ID,
				],
				WPSOLR_UI_Layout_Abstract::FEATURE_MULTIPLE                  => [
					WPSOLR_UI_Layout_Select::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Select2::CHILD_LAYOUT_ID,
				],
				WPSOLR_UI_Layout_Abstract::FEATURE_PLACEHOLDER               => [
					WPSOLR_UI_Layout_Select::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Select2::CHILD_LAYOUT_ID,
				],
				WPSOLR_UI_Layout_Abstract::FEATURE_SKIN                      => [
					WPSOLR_UI_Layout_Ion_Range_slider::CHILD_LAYOUT_ID,
				],
				WPSOLR_UI_Layout_Abstract::FEATURE_RANGE_IRREGULAR           => [
					WPSOLR_UI_Layout_Range_Irregular_Check_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Range_Irregular_Radio_Box::CHILD_LAYOUT_ID,
				],
				WPSOLR_UI_Layout_Abstract::FEATURE_RANGE_REGULAR             => [
					WPSOLR_UI_Layout_Range_Regular_Check_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Range_Regular_Radio_Box::CHILD_LAYOUT_ID,
				],
				WPSOLR_UI_Layout_Abstract::FEATURE_SIZE                      => [
					WPSOLR_UI_Layout_Select::CHILD_LAYOUT_ID,
				],
				WPSOLR_UI_Layout_Abstract::FEATURE_SIZE_SHOW_ALL_LINK        => [
					WPSOLR_UI_Layout_Check_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Radio_Box::CHILD_LAYOUT_ID,
					WPSOLR_UI_Layout_Color_Picker::CHILD_LAYOUT_ID,
				],
			]
		);
	}

	/**
	 * Constructor
	 * Subscribe to actions/filters
	 **/
	function __construct() {


		add_action( 'wp_enqueue_scripts', [ $this, 'wpsolr_enqueue_script' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'wpsolr_enqueue_script' ] );

		add_filter( WPSOLR_Events::WPSOLR_FILTER_FACETS_CSS, [ $this, 'wpsolr_filter_facets_css' ], 10, 1 );

		add_filter( WPSOLR_Events::WPSOLR_FILTER_FACET_TYPE, [ $this, 'wpsolr_filter_facet_type' ], 10, 2 );

		add_filter( WPSOLR_Events::WPSOLR_FILTER_FACET_ITEMS, [
			$this,
			'get_facet_items',
		], 10, 3 );

		add_filter( WPSOLR_Events::WPSOLR_FILTER_FACET_ITEMS_MAX_LABELS_SHOWN, [
			$this,
			'get_facet_items_max_labels_shown_in_settings',
		], 10, 1 );

		add_action( WPSOLR_Events::WPSOLR_FILTER_LAYOUT_OBJECT, [
			$this,
			'wpsolr_filter_layout_object',
		], 10, 2 );

		add_filter( WPSOLR_Events::WPSOLR_FILTER_GET_FIELD_TYPE_LAYOUTS, [
			$this,
			'get_layouts_for_field_type',
		], 10, 2 );

		add_filter( WPSOLR_Events::WPSOLR_FILTER_FACET_LAYOUT_SKINS, [
			$this,
			'get_facet_layout_skins',
		], 10, 1 );

		add_filter( WPSOLR_Events::WPSOLR_FILTER_INCLUDE_FILE, [ $this, 'wpsolr_filter_include_file' ], 10, 1 );

		add_filter( WPSOLR_Events::WPSOLR_FILTER_FACET_FEATURE_LAYOUTS, [
			$this,
			'wpsolr_filter_facet_feature_layouts'
		], 10, 2 );

		add_action( WPSOLR_Events::WPSOLR_FILTER_FACETS_CONTENT_TO_DISPLAY, [
			$this,
			'wpsolr_filter_facets_content_to_display',
		], 10, 4 );

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

			case WPSOLR_Help::HELP_FACET_THEME_SKIN_TEMPLATE:
				$file_name = 'facet-theme-layout-feature-skin.inc.php';
				break;

			case WPSOLR_Help::HELP_FACET_THEME_JS_TEMPLATE:
				$file_name = 'facet-theme-layout-feature-javascript.inc.php';
				break;

			case WPSOLR_Help::HELP_FACET_THEME_MULTIPLE_TEMPLATE:
				$file_name = 'facet-theme-layout-feature-multiple.inc.php';
				break;

			case WPSOLR_Help::HELP_FACET_THEME_PLACEHOLDER_TEMPLATE:
				$file_name = 'facet-theme-layout-feature-placeholder.inc.php';
				break;

			case WPSOLR_Help::HELP_FACET_THEME_COLOR_PICKER_TEMPLATE:
				$file_name = 'facet-theme-layout-color-picker.inc.php';
				break;

			case WPSOLR_Help::HELP_FACET_THEME_COLOR_PICKER_TEMPLATE_LOCALIZATION:
				$file_name = 'facet-theme-layout-localizations-color-picker.inc.php';
				break;

			case WPSOLR_Help::HELP_FACET_THEME_RANGE_REGULAR_TEMPLATE:
				$file_name = 'facet-theme-layout-range-regular.inc.php';
				break;

			case WPSOLR_Help::HELP_FACET_THEME_RANGE_IRREGULAR_TEMPLATE:
				$file_name = 'facet-theme-layout-range-irregular.inc.php';
				break;

			case WPSOLR_Help::HELP_ACF_FIELD_FILE:
				$file_name = 'acf-field-file.inc.php';
				break;

			case WPSOLR_Help::HELP_TOOLSET_FIELD_FILE:
				$file_name = 'toolset-field-file.inc.php';
				break;

		}

		$result = ! empty( $file_name ) ? sprintf( '%s/includes/%s', dirname( __FILE__ ), $file_name ) : $help_id;

		return $result;
	}


	/**
	 * @param int $max_labels_shown
	 *
	 * @return int|mixed
	 */
	public function get_facet_items_max_labels_shown_in_settings( $max_items = 0 ) {
		$results = WPSOLR_Service_Container::getOption()->get_option_theme_facet_max_labels_shown_in_settings();

		return ! empty( $results ) ? $results : $max_items;
	}

	/**
	 * Return all facet values
	 * @return array
	 */
	public function get_facet_items( $facet_items, $field_name, $facet_name ) {

		if ( ! isset( $facet_name ) ) {
			return $facet_items;
		}

		$facet_type = $this->wpsolr_filter_facet_type( null, $facet_name );

		switch ( $facet_type ) {
			case WPSOLR_Option::OPTION_FACET_FACETS_TYPE_RANGE:
				$range_start = WPSOLR_Service_Container::getOption()->get_facets_range_regular_start( $facet_name );
				$range_end   = WPSOLR_Service_Container::getOption()->get_facets_range_regular_end( $facet_name );
				$range_gap   = WPSOLR_Service_Container::getOption()->get_facets_range_regular_gap( $facet_name );

				// Add the range before
				array_push( $facet_items, sprintf( '*-%s', $range_start ) );

				for ( $i = $range_start; $i < $range_end; $i += $range_gap ) {
					array_push( $facet_items, sprintf( '%s-%s', $i, $i + $range_gap ) );
				}

				// Add the range after
				array_push( $facet_items, sprintf( '%s-*', $range_end ) );

				break;
		}

		return $facet_items;
	}


	function wpsolr_enqueue_script() {

		/**
		 * Facets Collapsing
		 */
		if ( WPSOLR_Service_Container::getOption()->get_option_theme_facet_is_collapse() ) {
			/**
			 * Collapsing library: http://webcloud.se/jQuery-Collapse/
			 */
			wp_enqueue_script( 'jquery-collapse', plugins_url( 'js/jquery-collapse/jquery.collapse.js', __FILE__ ), [], WPSOLR_PLUGIN_VERSION, true );
			wp_enqueue_script( 'jquery-collapse-storage', plugins_url( 'js/jquery-collapse/jquery.collapse_storage.js', __FILE__ ), [], WPSOLR_PLUGIN_VERSION, true );

			/**
			 * Font awesome required for collapsing icons
			 */
			wp_enqueue_style( 'font-awesome', plugins_url( 'css/font-awesome-4.7.0/css/font-awesome.min.css', __FILE__ ), [], WPSOLR_PLUGIN_VERSION );

			/**
			 * css and js for collapsing secret sauce !.
			 */
			wp_enqueue_script( 'wpsolr-facet-hierarchy-js', plugins_url( 'template/facet-hierarchy/wpsolr-facet-hierarchy.js', __FILE__ ), [], WPSOLR_PLUGIN_VERSION, true );
			wp_enqueue_style( 'wpsolr-facet-hierarchy-css', plugins_url( 'template/facet-hierarchy/wpsolr-facet-hierarchy.css', __FILE__ ), [], WPSOLR_PLUGIN_VERSION );
		}

		/**
		 * css and js for facet range
		 */
		wp_enqueue_script( 'wpsolr-facet-range-js', plugins_url( 'template/facet-range/wpsolr-facet-range.js', __FILE__ ), [], WPSOLR_PLUGIN_VERSION, true );
		wp_enqueue_style( 'wpsolr-facet-range-css', plugins_url( 'template/facet-range/wpsolr-facet-range.css', __FILE__ ), [], WPSOLR_PLUGIN_VERSION );


		/**
		 * Color picker in front-end
		 */
		wp_enqueue_script( 'wpsolr-facet-color-picker-js', plugins_url( 'template/facet-color-picker/wpsolr-facet-color-picker.js', __FILE__ ), [], WPSOLR_PLUGIN_VERSION, true );
		wp_enqueue_style( 'wpsolr-facet-color-picker-css', plugins_url( 'template/facet-color-picker/wpsolr-facet-color-picker.css', __FILE__ ), [], WPSOLR_PLUGIN_VERSION );

		/**
		 * Color picker in admin
		 * http://tutsnare.com/add-color-picker-to-wordpress-theme-or-admin-panel/
		 */
		if ( is_admin() ) {

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), [
				'jquery-ui-draggable',
				'jquery-ui-slider',
				'jquery-touch-punch'
			], false, 1 );
			wp_enqueue_script( 'wp-color-picker', admin_url( 'js/color-picker.min.js' ), [ 'iris' ], false, 1 );
			$colorpicker_l10n = [
				'clear'         => __( 'Clear' ),
				'defaultString' => __( 'Default' ),
				'pick'          => __( 'Select Color' ),
			];
			wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );
		}

	}

	/**
	 * Insert custom css before the facets HTML
	 * @return string
	 */
	function wpsolr_filter_facets_css( $empty_html ) {

		$css = trim( WPSOLR_Service_Container::getOption()->get_option_theme_facet_css() );

		return empty( $css ) ? '' : sprintf( '<!-- wpsolr, custom css --><style>%s</style>', WPSOLR_Service_Container::getOption()->get_option_theme_facet_css() );
	}

	/**
	 * Remove the top level of the category facet hierarchy on category pages.
	 * top_cat =>
	 * top_cat->current_cat =>
	 * top_cat->current_cat->sub_cat => sub_cat
	 *
	 * @param array $facets_content
	 * @param bool $is_engine_indexing_force_html_encoding
	 *
	 * @return array
	 */
	public function wpsolr_filter_facets_content_to_display( array $facets_content, $is_engine_indexing_force_html_encoding, $facet_hierarchy_separator ) {

		if ( empty( $facets_content ) ) {
			return [];
		}


		if ( ( $taxonomy = get_queried_object() ) && term_exists( $taxonomy->name ) && property_exists( $taxonomy, 'taxonomy' ) ) {

			$regexp_sub_category = sprintf( static::REGEX_SUB_CATEGORIES, $facet_hierarchy_separator );

			$taxonomy_name = ( $taxonomy->taxonomy === 'category' ) ? WpSolrSchema::_FIELD_NAME_CATEGORIES : $taxonomy->taxonomy;
			foreach ( $facets_content as $facet_name => &$facet ) {

				if ( ! empty( $facet ) && ( $taxonomy_name === WpSolrSchema::get_field_without_str_ending( $facet_name ) ) ) {

					/**
					 * Algolia is converting HTML entities in special characters: "&amp;" => "&"
					 * Convert the terms containing HTML entities the same way.
					 */
					$escaped_term_name = $is_engine_indexing_force_html_encoding ? html_entity_decode( $taxonomy->name ) : $taxonomy->name;

					foreach ( $facet['values'] as $index => &$facet_value ) {

						$value_without_top_level_hierarchy = preg_replace( sprintf( $regexp_sub_category, preg_quote( $escaped_term_name, '/' ) ), '$2', $facet_value['value'] );

						if ( $facet_value['value'] !== $value_without_top_level_hierarchy ) {

							$facet_value['value'] = substr( $facet_value['value'], strlen( $value_without_top_level_hierarchy . $facet_hierarchy_separator . $escaped_term_name ) );
							$facet_value['hierarchy_root'] = $value_without_top_level_hierarchy . $escaped_term_name . $facet_hierarchy_separator;

						} else {

							unset( $facet['values'][ $index ] );
						}
					}
				}
			}
		}

		return $facets_content;
	}

}
