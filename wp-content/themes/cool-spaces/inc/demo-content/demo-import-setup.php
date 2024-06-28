<?php
/**
 * Functions to provide support for the One Click Demo Import plugin (wordpress.org/plugins/one-click-demo-import)
 *
 * @package Mag_Lite
 */
/**
* Remove branding
*/


// add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );

/*Import demo data*/
if ( ! function_exists( 'mag_lite_demo_import_files' ) ) :
    function mag_lite_demo_import_files() {
        return array(
            array(
                'import_file_name'             => 'Mag Lite',                
                'local_import_file'            => trailingslashit( get_template_directory() ) . 'inc/demo-content/deom-mag-lite.xml',
                'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'inc/demo-content/demo-test-widgets.wie',
                'local_import_customizer_file' => trailingslashit( get_template_directory() ) . 'inc/demo-content/mag-lite-export.dat',
                'import_notice'                => esc_html__( 'Please waiting for a few minutes, do not close the window or refresh the page until the data is imported.It may take around 6-30 minutes depending upon your hosting.', 'mag-lite' ),
                 'preview_url'                  => 'https://demo.rigorousthemes.com/mag-lite/',
            ),
        );  
    }
  //  add_filter( 'pt-ocdi/import_files', 'mag_lite_demo_import_files' );
endif;

/**
 * Action that happen after import
 */
if ( ! function_exists( 'mag_lite_after_demo_import' ) ) :
function mag_lite_after_demo_import( $selected_import ) {
    
        //Set Menu
        $primary_menu = get_term_by('name', 'Primary Menu', 'nav_menu'); 
        $social_menu = get_term_by('name', 'Social Menu', 'nav_menu');     
        set_theme_mod( 'nav_menu_locations' , array( 
              'menu-1' => $primary_menu->term_id,
              'top-menu' => $top_menu->term_id,
              'social-menu' => $social_menu->term_id, 


             ) 
        );

    // Set Up the Front page
        $front_page = get_page_by_title( 'Home' );
        $blog_page  = get_page_by_title( 'Blog' );

        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $front_page -> ID );
        update_option( 'page_for_posts', $blog_page -> ID );
  
    
}
// add_action( 'pt-ocdi/after_import', 'mag_lite_after_demo_import' );
endif;







