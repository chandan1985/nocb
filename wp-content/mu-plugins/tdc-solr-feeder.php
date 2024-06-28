<?php
 /*
 * Plugin Name: Solr Feeder 
 * Description: Feeds Posts/pages to solr for indexing. now with delete's ! 
 * Author: Kris Linnell 
 * Version: 2.0.0
*/

if (is_admin()) {
        add_action('save_post', 'tdc_solr_feed');
        add_action('untrash_post', 'tdc_solr_feed');

        add_action('delete_post', 'tdc_solr_delete');
        add_action('wp_trash_post', 'tdc_solr_delete');
}

function tdc_solr_feed($post_id) {

	$status = get_post_status($post_id);
	$type = get_post_type($post_id);

	if ( ($status == 'publish' || $status == 'future') && ($type != 'nav_menu_item' || $type != 'snippet') )   {

		global $blog_id;
		global $wpdb;

		$wpdb->query("INSERT INTO search.feed (db, blog, post, action) VALUES ('" . DB_NAME ."',  $blog_id,  $post_id, 'add')");
	}
}

function tdc_solr_delete($post_id) {

		global $blog_id;
		global $wpdb;

		$wpdb->query("INSERT INTO search.feed (db, blog, post, action) VALUES ('" . DB_NAME ."',  $blog_id,  $post_id, 'delete')");
}
