<?php
/**
 * Sample implementation of the Custom Header feature
 *
 * You can add an optional custom header image to header.php like so ...
 *
	<?php the_header_image_tag(); ?>
 *
 * @link https://developer.wordpress.org/themes/functionality/custom-headers/
 *
 * @package Mag_Lite
 */

/**
 * Set up the WordPress core custom header feature.
 *
 * @uses mag_lite_header_style()
 */
function mag_lite_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'mag_lite_custom_header_args', array(
		'default-image'          => '',
		'default-text-color'     => '000000',
		'width'                  => 1000,
		'height'                 => 250,
		'flex-height'            => true,
		'wp-head-callback'       => 'mag_lite_header_style',
	) ) );
}
add_action( 'after_setup_theme', 'mag_lite_custom_header_setup' );

if ( ! function_exists( 'mag_lite_header_style' ) ) :
	/**
	 * Styles the header image and text displayed on the blog.
	 *
	 * @see mag_lite_custom_header_setup().
	 */
	function mag_lite_header_style() {
		wp_enqueue_style( 'mag-lite-style', get_stylesheet_uri() );
		$header_text_color = get_header_textcolor();
		$position = "absolute";
		$clip ="rect(1px, 1px, 1px, 1px)";
		if ( ! display_header_text() ) {
			$custom_css = '.site-title, .site-branding p{
				position: '.$position.';
				clip: '.$clip.'; 
			}';
		} else{

			$custom_css = '.site-title a, .site-description {
				color: #' . $header_text_color . ';			
			}';
		}
		wp_add_inline_style( 'mag-lite-style', $custom_css );
	}
	add_action( 'wp_enqueue_scripts', 'mag_lite_header_style' );

endif;
