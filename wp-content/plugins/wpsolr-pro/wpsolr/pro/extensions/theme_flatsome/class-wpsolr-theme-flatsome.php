<?php

namespace wpsolr\pro\extensions\theme_flatsome;

use wpsolr\core\classes\extensions\WPSOLR_Extension;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\ui\WPSOLR_Query;

/**
 * Class WPSOLR_Theme_Flatsome
 *
 * Manage Flatsome theme
 */
class WPSOLR_Theme_Flatsome extends WPSOLR_Extension {

	protected bool $is_replace_show_blog_and_pages_in_search_results = false;
	protected bool $is_processing_replace_show_blog_and_pages_in_search_results = false;

	/**
	 * Constructor.
	 */
	public function __construct() {

		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 99 );

			/**
			 * structure-wc-global.php
			 */
			if ( $this->_is_replace_search_in_results() ) {
				add_filter( 'woocommerce_after_main_content', [
					$this,
					'flatsome_pages_in_search_results',
				], 9 );
				add_filter( 'posts_pre_query', [ $this, 'query' ], 10, 2 );
			}

		}

	}

	/**
	 * Enqueue scripts
	 */
	public function wp_enqueue_scripts() {

		if ( WPSOLR_Service_Container::getOption()->get_theme_flatsome_is_replace_infinite_scroll() ) {
			wp_enqueue_script( 'wpsolr-flatsome-infinite-scroll.js', plugins_url( 'js/wpsolr-flatsome-infinite-scroll.js', __FILE__ ), [ 'jquery' ], WPSOLR_PLUGIN_VERSION, true );
		}

	}

	/**
	 * This is a Flatsome posts & pages in search results
	 */
	public function flatsome_pages_in_search_results() {
		// From structure-wc-global.php
		$this->is_replace_show_blog_and_pages_in_search_results = true;
	}


	/**
	 * Stop WordPress performing a DB query for its main loop.
	 *
	 * As of WordPress 4.6, it is possible to bypass the main WP_Query entirely.
	 * This saves us one unnecessary database query! :)
	 *
	 * @param null $retval Current return value for filter.
	 * @param WP_Query $query Current WordPress query object.
	 *
	 * @return null|array
	 * @since 2.7.0
	 *
	 */
	function query( $retval, $query ) {
		if ( ! get_search_query() ||
		     ! is_search() ||
		     ! $this->_is_replace_search_in_results() ||
		     ! $this->is_replace_show_blog_and_pages_in_search_results ||
		     $this->is_processing_replace_show_blog_and_pages_in_search_results ) {
			// This is not a Flatsome query, or it's a recurse call.
			return $retval;
		}

		// To prevent recursive infinite calls
		$this->is_processing_replace_show_blog_and_pages_in_search_results = true;


		$wpsolr_query                          = new WPSOLR_Query(); // Potential recurse here
		$wpsolr_query->query['post_type']      = $query->query_vars['post_type'][0] ?? 'post';
		$wpsolr_query->query['s']              = $query->query['s'];
		$wpsolr_query->query['posts_per_page'] = get_option( 'posts_per_page' );
		$results                               = $wpsolr_query->get_posts();

		// 2 calls from flatsome: first for posts, 2nd for pages. Stop after last call.
		$this->is_processing_replace_show_blog_and_pages_in_search_results = ( 'page' === $wpsolr_query->query['post_type'] );

		// Return $results, which prevents standard $wp_query to execute it's SQL.
		return $results;
	}

	/**
	 * @return bool
	 */
	protected function _is_replace_search_in_results(): bool {
		return WPSOLR_Service_Container::getOption()->get_theme_flatsome_is_replace_show_blog_and_pages_in_search_results() &&
		       get_theme_mod( 'search_result', 1 );
	}

}