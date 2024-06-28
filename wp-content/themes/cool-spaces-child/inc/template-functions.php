<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Mag_Lite
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function mag_lite_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

    // Add class for Slidebar layout.
    $sidebar_layout = mag_lite_get_option( 'layout_options' ); 
    $sidebar_layout = apply_filters( 'mag_lite_filter_theme_global_layout', $sidebar_layout );
    $classes[] = 'global-layout-' . esc_attr( $sidebar_layout );  

    if( is_archive() || is_home() ):
        // Add class for Archive layout.
        $archive_layout = mag_lite_get_option( 'archive_layout' ); 
        $sidebar_layout = apply_filters( 'mag_lite_filter_theme_archive_layout', $archive_layout );
        $classes[] = 'archive-' . esc_attr( $archive_layout ); 
        
    endif;       

    return $classes; 
	
}
add_filter( 'body_class', 'mag_lite_body_classes' );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function mag_lite_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'mag_lite_pingback_header' );

if ( ! function_exists( 'mag_lite_the_excerpt' ) ) :

    /**
     * Generate excerpt.
     *
     * @since 1.0.0
     *
     * @param int     $length Excerpt length in words.
     * @param WP_Post $post_obj WP_Post instance (Optional).
     * @return string Excerpt.
     */
    function mag_lite_the_excerpt( $length = 0, $post_obj = null ) {

        global $post;

        if ( is_null( $post_obj ) ) {
            $post_obj = $post;
        }

        $length = absint( $length );

        if ( 0 === $length ) {
            return;
        }

        $source_content = $post_obj->post_content;

        if ( ! empty( $post_obj->post_excerpt ) ) {
            $source_content = $post_obj->post_excerpt;
        }

        $source_content = preg_replace( '`\[[^\]]*\]`', '', $source_content );
        $trimmed_content = wp_trim_words( $source_content, $length, '&hellip;' );
        return $trimmed_content;

    }

endif;



/**
 *  Mag Lite Breadcrumb
 *
 *
 */
if ( ! function_exists( 'mag_lite_breadcrumb' ) ) :

    /**
     * Simple breadcrumb.
     *
     * @since 1.0.0
     *
     * @link: https://gist.github.com/melissacabral/4032941
     *
     * @param  array $args Arguments
     */
    function mag_lite_breadcrumb( $args = array() ) {
        // Bail if Home Page.
        if ( is_front_page() || is_home() ) {
            return;
        }

        if ( ! function_exists( 'breadcrumb_trail' ) ) {
            require_once get_template_directory() . '/inc/breadcrumbs.php';
        }

        $breadcrumb_args = array(
            'container'   => 'div',
            'show_browse' => false,
        );
        breadcrumb_trail( $breadcrumb_args );
       
    }

endif;

/*------------------------------------------------------------------------------------------------*/
/**
 * Generate darker color
 * Source: http://stackoverflow.com/questions/3512311/how-to-generate-lighter-darker-color-with-php
 */
if ( ! function_exists( 'mag_lite_hover_color' ) ) :
    function mag_lite_hover_color( $hex, $steps ) {
        // Steps should be between -255 and 255. Negative = darker, positive = lighter
        $steps = max( - 255, min( 255, $steps ) );

        // Normalize into a six character long hex string
        $hex = str_replace( '#', '', $hex );
        if ( strlen( $hex ) == 3 ) {
            $hex = str_repeat( substr( $hex, 0, 1 ), 2 ) . str_repeat( substr( $hex, 1, 1 ), 2 ) . str_repeat( substr( $hex, 2, 1 ), 2 );
        }

        // Split into three parts: R, G and B
        $color_parts = str_split( $hex, 2 );
        $return      = '#';

        foreach ( $color_parts as $color ) {
            $color = hexdec( $color ); // Convert to decimal
            $color = max( 0, min( 255, $color + $steps ) ); // Adjust color
            $return .= str_pad( dechex( $color ), 2, '0', STR_PAD_LEFT ); // Make two char hex code
        }

        return $return;
    }
endif;


/**
 * Adds custom contain in head sections
 */
if ( ! function_exists( 'mag_lite_categories_color' ) ):
    function mag_lite_categories_color() {     

        $get_categories = get_terms( 'category', array( 'hide_empty' => false ) );

        $cat_color_css = '';

        foreach ( $get_categories as $category ) {

            $cat_color       = mag_lite_get_option( 'mag_lite_category_color_'.esc_html( strtolower($category->name) ).'' );

            $cat_hover_color = esc_attr( mag_lite_hover_color( $cat_color, '-50' ) );
            $cat_id          = absint( $category->term_id );

            if ( ! empty( $cat_color ) ) {
                $cat_color_css .= ".cat-links.mag-lite-cat-" . $cat_id . " a { background: " . $cat_color . "}\n";      
                $cat_color_css .= ".cat-links.mag-lite-cat-" . $cat_id . " a:hover { background: " . $cat_hover_color . "}\n";         
            }

               
        }

        ?>
        <style type="text/css">
            <?php
                if( !empty( $cat_color_css ) ) {
                    echo wp_kses_post( $cat_color_css );

                }
            ?>
        </style>
        <?php
    }
endif;
add_action( 'wp_head', 'mag_lite_categories_color' );


/**
 * Register the required plugins for this theme.
 * 
 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
 */
function mag_lite_register_required_plugins() {
    /*
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(

        array(
            'name'      => esc_html__( 'One Click Demo Import', 'mag-lite' ), //The plugin name
            'slug'      => 'one-click-demo-import',  // The plugin slug (typically the folder name)
            'required'  => false,  // If false, the plugin is only 'recommended' instead of required.
            'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
        ),
        
        array(
            'name'      => esc_html__( 'Contact Form 7', 'mag-lite' ), //The plugin name
            'slug'      => 'contact-form-7',  // The plugin slug (typically the folder name)
            'required'  => false,  // If false, the plugin is only 'recommended' instead of required.
            'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
        ),

    );

    $config = array(
        'id'           => 'mag-lite',        // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '',                      // Default absolute path to bundled plugins.     
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
        );

    tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'mag_lite_register_required_plugins' );


function mag_lite_svg_upload($svg) {
  $svg['svg'] = 'image/svg+xml';
  return $svg;
}
add_filter('upload_mimes', 'mag_lite_svg_upload');
