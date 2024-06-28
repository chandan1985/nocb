<?php
/**
 * Plugin Name: ASNT Custom Widget Right Sidebar
 * Description: We are adding custom widgets to Widgets.
 * Author:      Asentech
 * Author URI:  https://www.asentechllc.com/
 */

//for Category wise slideshow posts on right Sidebar.
function register_asnt_category_wise_slideshow_posts_sidebar() {
	require_once( __DIR__ . '/widgets/asnt-category-wise-slideshow-posts-sidebar.php' );
	register_widget( 'asnt_category_wise_slideshow_posts_sidebar' );
}
add_action( 'widgets_init', 'register_asnt_category_wise_slideshow_posts_sidebar' );



//for Category wise posts listing on rigth Sidebar.
function register_asnt_category_wise_posts_listing_sidebar() {
	require_once( __DIR__ . '/widgets/asnt-category-wise-posts-listing-sidebar.php' );
	register_widget( 'asnt_category_wise_posts_listing_sidebar' );
}
add_action( 'widgets_init', 'register_asnt_category_wise_posts_listing_sidebar' );



//for Category wise single post on rigth Sidebar.
function register_asnt_category_wise_single_post_sidebar() {
	require_once( __DIR__ . '/widgets/asnt-category-wise-single-post-sidebar.php' );
	register_widget( 'asnt_category_wise_single_post_sidebar' );
}
add_action( 'widgets_init', 'register_asnt_category_wise_single_post_sidebar' );



//for Homepage Events on Mobile.
function register_asnt_homepage_events() {
	require_once( __DIR__ . '/widgets/asnt-homepage-events.php' );
	register_widget( 'asnt_homepage_events' );
}
add_action( 'widgets_init', 'register_asnt_homepage_events' );



//for Header Latest Event.
function register_asnt_header_latest_event() {
	require_once( __DIR__ . '/widgets/asnt-header-latest-event.php' );
	register_widget( 'asnt_header_latest_event' );
}
add_action( 'widgets_init', 'register_asnt_header_latest_event' );



//for Upcoming Events.
function register_asnt_upcoming_events() {
	require_once( __DIR__ . '/widgets/asnt-upcoming-events.php' );
	register_widget( 'asnt_upcoming_events' );
}
add_action( 'widgets_init', 'register_asnt_upcoming_events' );

//for Upcoming Webinars.
function register_asnt_upcoming_webinars() {
	require_once( __DIR__ . '/widgets/asnt-upcoming-webinars.php' );
	register_widget( 'asnt_upcoming_webinars' );
}
add_action( 'widgets_init', 'register_asnt_upcoming_webinars' );