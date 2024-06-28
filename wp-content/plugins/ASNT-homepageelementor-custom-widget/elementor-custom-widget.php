<?php
/**
 * Plugin Name: ASNT Homepage Elementor Custom Widget
 * Description: Auto embed any embbedable content from external URLs into Elementor.
 * Plugin URI:  https://elementor.com/
 * Version:     1.0.0
 * Author:      Asentech
 * Author URI:  https://www.asentechllc.com/
 *
 * Elementor tested up to: 3.7.0
 * Elementor Pro tested up to: 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register oEmbed Widget.
 *
 * Include widget file and register widget class.
 *
 * @since 1.0.0
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 * @return void
 */


// for featured content A coming on homepage.
function register_asnt_featured_content_a( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/asnt-featured-content-a.php' );

	$widgets_manager->register( new \Asnt_featured_content_a() );

}
add_action( 'elementor/widgets/register', 'register_asnt_featured_content_a' );


// for Featured content B coming on homepage.
function register_asnt_featured_content_b( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/asnt-featured-content-b.php' );

	$widgets_manager->register( new \Asnt_featured_content_b() );

}
add_action( 'elementor/widgets/register', 'register_asnt_featured_content_b' );


//  for Featured content C coming on homepage.
function register_asnt_featured_content_c( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/asnt-sponsored-featured-content-c.php' );

	$widgets_manager->register( new \Asnt_featured_content_c() );

}
add_action( 'elementor/widgets/register', 'register_asnt_featured_content_c' );


// for Category wise grid post listing coming on homepage
function register_asnt_category_wise_grid_post_listing( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/asnt-category-wise-grid-post-listing.php' );

	$widgets_manager->register( new \ASNT_category_wise_grid_post_listing() );

}
add_action( 'elementor/widgets/register', 'register_asnt_category_wise_grid_post_listing' );



//  for Category wise posts listing on Homepage.
function register_asnt_category_wise_posts_listing( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/asnt-category-wise-posts-listing.php' );

	$widgets_manager->register( new \Asnt_category_wise_posts_listing() );

}
add_action( 'elementor/widgets/register', 'register_asnt_category_wise_posts_listing' );



// for Category wise slideshow posts on homepage.
function register_asnt_category_wise_slideshow_posts( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/asnt-category-wise-slideshow-posts.php' );

	$widgets_manager->register( new \Asnt_category_wise_slideshow_posts() );

}
add_action( 'elementor/widgets/register', 'register_asnt_category_wise_slideshow_posts' );


//  for Category wise with single post on Homepage.
function register_asnt_category_wise_single_post( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/asnt-category-wise-single-post.php' );

	$widgets_manager->register( new \Asnt_category_wise_single_post() );

}
add_action( 'elementor/widgets/register', 'register_asnt_category_wise_single_post' );



//  for Listing with 300*250, 728*90 Ads on homepage.
function register_asnt_listing_with_ads( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/asnt-listing-with-ads.php' );

	$widgets_manager->register( new \Asnt_listing_with_ads() );

}
add_action( 'elementor/widgets/register', 'register_asnt_listing_with_ads' );



//  for events section coming on homepage
function register_homepage_events( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/homepage-events.php' );

	$widgets_manager->register( new \Homepage_events() );

}
add_action( 'elementor/widgets/register', 'register_homepage_events' );



// for Sponsored Content Listing on homepage
function register_asnt_sponsored_category_listing( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/asnt-sponsored-category-listing.php' );

	$widgets_manager->register( new \ASNT_sponsored_category_listing() );

}
add_action( 'elementor/widgets/register', 'register_asnt_sponsored_category_listing' );



// for Newsletter Form  on homepage
function register_asnt_newsletter_form( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/asnt-newsletter-form.php' );

	$widgets_manager->register( new \Asnt_newsletter_form() );

}
add_action( 'elementor/widgets/register', 'register_asnt_newsletter_form' );




// for Newsletter Form for mobile on homepage
function register_asnt_newsletter_form_mobile( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/asnt-newsletter-form-mobile.php' );

	$widgets_manager->register( new \Asnt_newsletter_form_mobile() );

}
add_action( 'elementor/widgets/register', 'register_asnt_newsletter_form_mobile' );



// for Newsletter Form for mobile on homepage
function register_asnt_events_social_links( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/asnt-events-social-links.php' );

	$widgets_manager->register( new \Asnt_events_social_links() );

}
add_action( 'elementor/widgets/register', 'register_asnt_events_social_links' );

// for Power List
function register_asnt_power_list( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/asnt-power-list.php' );

	$widgets_manager->register( new \asnt_power_list() );

}
add_action( 'elementor/widgets/register', 'register_asnt_power_list' );

// for Featured Contents
function register_asnt_featured_content( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/asnt-featured-content.php' );
	$widgets_manager->register( new \asnt_featured_content() );
}
add_action( 'elementor/widgets/register', 'register_asnt_featured_content' );
