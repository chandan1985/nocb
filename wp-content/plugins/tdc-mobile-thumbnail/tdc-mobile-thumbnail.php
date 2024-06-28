<?php
 /*
 * Plugin Name: TDC Mobile Thumbnail 
 * Description: Creates a mobile thumbnail image size
 * Author: Jerry Milo Johnson, The Dolan Company
 * Version: 1.0
 * Modifications:
            2012-07-25 	Added new plugin  
            2012-08-02 	Added the rest of the mobile image package set
            2012-08-08 	Added opengraph/facebook thumb size
            2012-08-08 	Add thumb metaboxes to post edit page
            2012-08-14  Changed thumb sizes for iPad
            
*/

/*
 * @todo: update mobile thumb images when feature image changes, ajax, without page reload. (like the featured image does)
 * @todo: add settings page to add/delete new image sizes
 */

// Prevent direct file call
if (!defined( 'ABSPATH' ))
	die( 'Direct access not allowed.' );

class tdc_mobile_thumbnail {
	
	function __construct(){
		// Add meta tags to head
		add_action( 'wp_head', array( $this, 'append_meta'),2);
//add_action( 'init', array( 'tdc_mobile_thumbnail', 'fix_wpfbogp'));

		add_action( 'add_meta_boxes', array( $this, 'tdc_mobile_thumbnail_add_custom_box' ));
		add_action( 'after_setup_theme', array( $this, 'mobile_thumbnail_setup' ));
		add_filter('image_size_names_choose',  array( $this, 'custom_image_sizes_choose'));

// add the facebook namespace
		add_filter('language_attributes',array( $this, 'fb_namespace'));
	}

	function mobile_thumbnail_setup() {
			// add thumb support if the theme didn't
		$supportedTypes = get_theme_support( 'post-thumbnails' );
		if( $supportedTypes === false )
			add_theme_support('post-thumbnails');               

	add_image_size( 'mobile-thumbnail',   180, 120, true );  // iphone thumbnail
	add_image_size( 'mobile-article',     600, 400, true );  // iphone article
	add_image_size( 'standard-article',  1296, 864, true );  // ipad article
	add_image_size( 'standard-featured',  972, 648, true );  // ipad featured
	add_image_size( 'standard-thumbnail', 468, 312, true );  // ipad small
	add_image_size( 'opengraph-thumbnail',200, 200, true );  // opengraph square
	add_image_size( 'email-thumbnail',100, 100, true );  // email thumbnail
}

/* Adds a box to the main column on the Post and Page edit screens */
function tdc_mobile_thumbnail_add_custom_box() {
	add_meta_box('postmobilethumbdiv', __('Mobile Image Preview'), array( 'tdc_mobile_thumbnail', 'post_thumbnail_mobile_meta_box'), null, 'side', 'low');
	//add_meta_box('postopengraphthumbdiv', __('Facebook Image Preview'), array( 'tdc_mobile_thumbnail', 'post_thumbnail_opengraph_meta_box'), null, 'side', 'low');

}

// Display post facebook thumbnail meta box.
function post_thumbnail_opengraph_meta_box() {
	global $post;
	echo the_post_thumbnail('opengraph-thumbnail'); 
}

// Display post mobile thumbnail meta box.
public static function post_thumbnail_mobile_meta_box() {
	global $post;
	echo the_post_thumbnail('mobile-thumbnail'); 
}

// Hook for adding meta menus
function append_meta() {
	global $post;
	
	$thumbnail_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'mobile-thumbnail');
	if($thumbnail_url) echo "<meta name='thumbnail' content='".$thumbnail_url[0]."' />\n";

	//echo "\n<!-- thumbnail action -->\n";
	//echo "\n<!-- post-id:".$post->ID.": -->\n";
	//echo "\n<!-- thumb_id:".get_post_thumbnail_id($post->ID).": -->\n";
	//echo "\n<!-- url:".$thumbnail_url[0].": -->\n";

	//$opengraph_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'opengraph-thumbnail');
	//if($opengraph_url) {
	//	echo '<meta property="og:image" content="' . $opengraph_url[0] . '" />' . "\n";
	//	echo '<link rel="image_src" href="' . esc_attr($opengraph_url[0]) . '" />' . "\n";
	//} 

}

// add new sizes to media upload admin
function custom_image_sizes_choose($sizes) {
	$addsizes = array(
		'mobile-thumbnail' => __( 'Mobile Thumbnail'),
		'mobile-article' => __( 'Mobile Article'),
		'standard-article' => __( 'Standard Article'),
		'standard-featured' => __( 'Standard Featured'),
		'standard-thumbnail' => __( 'Standard Thumbnail'),
		'opengraph-thumbnail' => __( 'OpenGraph Thumbnail'),
		'email-thumbnail' => __( 'Email Thumbnail')
	);
	return array_merge($sizes, $addsizes);
}

// add FB namespace per ogp.me schema
function fb_namespace($output) {
	return $output.' xmlns:fb="http://ogp.me/ns/fb#"';
}

// move the op metatags to the top of the head
//function fix_wpfbogp() {
//	if (function_exists('wpfbogp_build_head')) {
//	remove_action('wp_head','wpfbogp_build_head',50);
//	add_action('wp_head','wpfbogp_build_head',2);
//	}       
//}
}

new tdc_mobile_thumbnail();
