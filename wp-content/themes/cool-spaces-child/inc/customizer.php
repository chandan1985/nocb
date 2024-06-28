<?php
/**
 * Mag Lite Theme Customizer
 *
 * @package Mag_Lite
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function mag_lite_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
	

	// Load Customize Sanitize.
	require get_template_directory() . '/inc/customizer/sanitize.php';
	
	// Load Theme Option.
	require get_template_directory() . '/inc/customizer/theme-section.php';

	// Load customize control.
	require get_template_directory() . '/inc/customizer/control.php';	

	$wp_customize->register_section_type( 'Mag_lite_Customize_Section_Upsell' );

	// Register sections.
	$wp_customize->add_section(
		new Mag_lite_Customize_Section_Upsell(
			$wp_customize,
			'theme_upsell',
			array(
				'title'    => esc_html__( 'Mag Pro', 'mag-lite' ),
		//		'pro_text' => esc_html__( 'Buy Pro', 'mag-lite' ),
		//		'pro_url'  => 'https://themepalace.com/downloads/mag-pro/',
				'priority' => 1,
			)
		)
	);

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'        => '.site-title a',
			'render_callback' => 'mag_lite_customize_partial_blogname',
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'        => '.site-description',
			'render_callback' => 'mag_lite_customize_partial_blogdescription',
		) );
	}
}
add_action( 'customize_register', 'mag_lite_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function mag_lite_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function mag_lite_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function mag_lite_customize_preview_js() {
	wp_enqueue_script( 'mag-lite-customizer', get_template_directory_uri() . 'assest/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'mag_lite_customize_preview_js' );

/**
 *  Customizer Control 
 */
function mag_lite_customize_backend_scripts() {

	wp_enqueue_style( 'mag-lite-admin-customizer-style', get_template_directory_uri() . '/inc/customizer/css/customizer-style.css' );
	
	wp_enqueue_script( 'mag-lite-admin-customizer', get_template_directory_uri() . '/inc/customizer/js/customizer-scipt.js', array( ), '20151215', true );
}
add_action( 'customize_controls_enqueue_scripts', 'mag_lite_customize_backend_scripts', 10 );
