<?php
/**
 * Core setup, site hooks and filters.
 *
 * @package Pbm
 */

namespace Pbm\Core;

use Pbm\Helpers;


/**
 * Set up theme defaults and register supported WordPress features.
 *
 * @return void
 */
function setup() {

	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};	
	// Register post-to-post relationships via the content connect plugin.
	add_action( 'tenup-content-connect-init', $n( 'register_content_connections' ) );
	
}


/**
 * Enqueue scripts for front-end.
 *
 * @return void
 */
function scripts() {

	wp_enqueue_script(
		'frontend',
		PBM_TEMPLATE_URL . '/dist/js/frontend.min.js',
		[],
		PBM_VERSION,
		true
	);

	// wp_enqueue_script(
	// 	'stickyfill',
	// 	PBM_TEMPLATE_URL . '/assets/js/frontend/stickyfill.js',
	// 	[],
	// 	PBM_VERSION,
	// 	true
	// );
}

/**
 * Enqueue styles for front-end.
 *
 * @return void
 */
function styles() {
    $blog_id = get_current_blog_id();
	wp_enqueue_style(
		'styles',
		PBM_TEMPLATE_URL . '/dist/css/style.min.css',
		[],
		PBM_VERSION
	);
        wp_enqueue_style(
		'override-styles',
		PBM_TEMPLATE_URL . '/override.css',
		[],
		PBM_VERSION
	);
        wp_enqueue_style(
		'temp_style',
		PBM_TEMPLATE_URL . '/dist/css/temp_style'.$blog_id.'.css',
		[],
		PBM_VERSION
	);

	if ( is_page_template( 'templates/page-styleguide.php' ) ) {
		wp_enqueue_style(
			'styleguide',
			PBM_TEMPLATE_URL . '/dist/css/styleguide.min.css',
			[],
			PBM_VERSION
		);
	}
}

/**
 * Register post-to-post relationships via the content connect plugin.
 *
 * Note: Plugin generates a PHP warning @see https://github.com/10up/wp-content-connect/issues/23
 *
 * @since 1.0.0
 * @return void
 */
function register_content_connections( $registry ) {

	$args = [
		'from' => [
			'enable_ui' => true,
			'sortable'  => true,
			'labels'    => [
				'name' => esc_html__( 'Related Products', 'pbm' ),
			],
		],
		'to' => [
			'enable_ui' => true,
			'sortable'  => true,
			'labels'    => [
				'name' => esc_html__( 'Related Companies', 'pbm' ),
			],
		],
	];

	$relationship = $registry->define_post_to_post(
		'pbm-company',
		'pbm-product',
		'company-product',
		$args
	);
        $args_catalog = [
		'from' => [
			'enable_ui' => true,
			'sortable'  => true,
			'labels'    => [
				'name' => esc_html__( 'Related Ecatalogs', 'pbm' ),
			],
		],
		'to' => [
			'enable_ui' => true,
			'sortable'  => true,
			'labels'    => [
				'name' => esc_html__( 'Related Companies', 'pbm' ),
			],
		],
	];

	$relationship = $registry->define_post_to_post(
		'pbm-company',
		'pbm-ecatalog',
		'company-ecatalog',
		$args_catalog
	);
}



/**
 * Replace core gallery output with our own gallery.
 *
 * Note: The pbm-photo-gallery shortcode uses the similar
 *       {@see pbm_get_photo_gallery()} template tag.
 *
 * @since 1.0.0
 * @param string $output   The gallery output. Default empty.
 * @param array  $atts     Attributes of the gallery shortcode.
 * @param int    $instance Unique numeric ID of this gallery shortcode instance.
 * @return string Gallery HTML
 */
function replace_post_gallery( $output, $atts, $instance ) {

	if ( empty( $atts['ids'] ) ) {
		return '';
	}

	$image_ids   = explode( ',', $atts['ids'] );
	$attachments = Helpers\get_photo_gallery_attachments_by_id( $image_ids );

	if ( ! $attachments ) {
		return;
	}

	$is_core = true; // Flag is used to determine whether the gallery is inserted via the core gallery function.

	ob_start();

	/*
	 * Using locate_template in stead of get_template_part
	 * to be able to pass variables.
	 */
	include locate_template( 'partials/gallery-shortcode.php' );

	return ob_get_clean();
}

/**
 * Make tribe events category taxonomy private.
 *
 * @since 1.0.0
 * @param array $taxonomy_args List of taxonomy arguments.
 * @return array
 */
function filter_tribe_events_cat( $taxonomy_args ) {

	$taxonomy_args['public']  = false;
	$taxonomy_args['rewrite'] = false;

	return $taxonomy_args;
}

/**
 * Remove tribe event category rewrite rules.
 *
 * @since 1.0.0
 * @param array $rules The compiled array of rewrite rules.
 * @return array
 */
function remove_tribe_category_rewrite_rules( $rules ) {

	foreach ( $rules as $rule => $rewrite ) {
		if ( false !== strpos( $rule, '(?:events)/(?:category)' ) || false !== strpos( $rule, '(.*)events/category/' ) ) {
			unset( $rules[ $rule ] );
		}
	}

	return $rules;

}

/**
 * Custom Image Sizes
 *
 * @uses add_image_size() to add image sizes.
 *
 * @since 0.1.0
 *
 * @return void
 */
if ( function_exists( 'add_image_size' ) ) {
	add_image_size( 'pbm-900', 900 );
	add_image_size( 'pbm-1800', 1800 );
}

/**
 * Our friends from Modern Tribe add a dashboard widget via the events plugin, let's not.
 *
 * @since 1.0.0
 * @return void
 */
function remove_tribe_dashboard_widget() {
	remove_meta_box( 'tribe_dashboard_widget', 'dashboard', 'normal' );
}

// Adding class to Next and Previous link


// add_filter('next_posts_link_attributes', 'posts_link_attributes');
// add_filter('previous_posts_link_attributes', 'posts_link_attributes');

  
// function posts_link_attributes() {
//     return 'class="style-cta"';
// }

/* additional field for event custom post types */


// Register fields via the fieldmanager plugin.

add_action( 'fm_post_tribe_events', __NAMESPACE__ . '\register_fields_events' );

function register_fields_events() {

	$events = new \Fieldmanager_Group( [
		'name'           => 'bridge_tower_media',
		'default_value'  => [],
		'serialize_data' => false,
		'children'       => [
			'conferences' => new \Fieldmanager_Checkbox(
				esc_html__( 'PBM Event', 'pbm' )
			),
		],
	] );
	
	
	$events->add_meta_box( esc_html__( 'Bridge Tower Media', 'pbm' ), 'tribe_events', 'side');
	
  // We own it fields created for article
	$we_own_it = new \Fieldmanager_Select( array(
        'name' => 'we_own_it',
        'options' => array(
            'yes' => 'Yes',
            'no' => 'No',
        ),
    ) );
    $we_own_it->add_meta_box( esc_html__( 'We Own It', 'pbm' ), 'tribe_events', 'side', 'high'  );
	
}
