<?php
/**
 * Load files.
 *
 * @package Mag_Lite
 */
/**
 * Include default theme options.
 */
require_once trailingslashit( get_template_directory() ) . 'inc/customizer/default.php';


/**
 * Load hooks.
 */
require_once trailingslashit( get_template_directory() ) . 'inc/hook/structure.php';
require_once trailingslashit( get_template_directory() ) . 'inc/hook/basic.php';
require_once trailingslashit( get_template_directory() ) . 'inc/hook/custom.php';

/**
 * Implement the Custom Header feature.
 */
require_once trailingslashit( get_template_directory() ) . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require_once trailingslashit( get_template_directory() ) . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require_once trailingslashit( get_template_directory() ) . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require_once trailingslashit( get_template_directory() ) . '/inc/customizer.php';

/**
 * Widget Register
 */
require_once trailingslashit( get_template_directory() ) . '/inc/widgets/widgets.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require_once trailingslashit( get_template_directory() ) . '/inc/jetpack.php';
}
/**
 * Plugin Activation Section.
 */
require trailingslashit( get_template_directory() ) . '/inc/mag-lite-plugin-activation.php';

/**
 *  Demo Import Post
 */
require_once trailingslashit( get_template_directory() ) . '/inc/demo-content/demo-import-setup.php';





