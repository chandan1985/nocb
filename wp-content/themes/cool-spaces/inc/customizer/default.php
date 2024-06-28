<?php
/**
 * Default theme options.
 *
 * @package Mag_Lite
 */

if ( ! function_exists( 'mag_lite_get_default_theme_options' ) ) :

	/**
	 * Get default theme options.
	 *
	 * @since 1.0.0
	 *
	 * @return array Default theme options.
	 */
function mag_lite_get_default_theme_options() {

	$defaults = array();

	$defaults['site_identity']						= 'title-text';
	$defaults['enable_top_header']					= true;
	$defaults['top_header_left']					= 'current-date';
	$defaults['top_header_right']					= 'menu';
	$defaults['header_address']						= '';
	$defaults['header_email']						= '';
	$defaults['header_number']						= '';


	/*********************** Archive Setting *****************************************/
	$defaults['archive_layout']						= 'first-design';
	

	/*********************** General Setting *****************************************/
	$defaults['layout_options']						= 'right';
	
	/*********************** Breadcrumb Setting *****************************************/
	$defaults['enable_breadcrumb']					= true;

	/*********************** Google Map Setting *****************************************/
	$defaults['google_map_address']					= '';

	/*********************** Categories Color Setting *****************************************/
	$categories = get_terms( 'category' ); // Get all Categories
	$wp_category_list = array();

	foreach ( $categories as $category_list ) {
	$defaults['mag_lite_category_color_'.esc_html( strtolower($category_list->name) ).''] = '#4fbbbd';

	}

	/*********************** Footer Setting *****************************************/
	$defaults['subscription_page']					= 0;
	$defaults['copyright_text']						= '';
	$defaults['enable_footer_menu']					= true;

	// Pass through filter.
	$defaults = apply_filters( 'mag_lite_filter_default_theme_options', $defaults );
	return $defaults;
}

endif;

/**
*  Get theme options
*/
if ( ! function_exists( 'mag_lite_get_option' ) ) :

	/**
	 * Get theme option
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Option key.
	 * @return mixed Option value.
	 */
	function mag_lite_get_option( $key ) {

		$default_options = mag_lite_get_default_theme_options();

		if ( empty( $key ) ) {
			return;
		}

		$theme_options = (array)get_theme_mod( 'theme_options' );
		$theme_options = wp_parse_args( $theme_options, $default_options );

		$value = null;

		if ( isset( $theme_options[ $key ] ) ) {
			$value = $theme_options[ $key ];
		}

		return $value;

	}

endif;