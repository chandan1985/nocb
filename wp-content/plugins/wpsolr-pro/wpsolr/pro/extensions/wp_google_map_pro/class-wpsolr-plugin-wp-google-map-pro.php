<?php

namespace wpsolr\pro\extensions\wp_google_map_pro;

use wpsolr\core\classes\extensions\WPSOLR_Extension;

class WPSOLR_Plugin_WP_Google_Map_Pro extends WPSOLR_Extension {

	/* @var bool */
	protected $is_replace_query = false;

	/**
	 * @inheritDoc
	 */
	public function __construct() {

		add_filter( 'wpgmp_post_args', function ( $args, $map ) {

			$this->is_replace_query = true;

			return $args;

		}, 10, 2 );

		add_filter( 'posts_pre_query', [ $this, 'query' ], 10, 2 );

	}

	/**
	 * Replace query posts with current WPSOLR's posts
	 */
	function query( $retval, $query ) {
		global $wp_query;

		$results = $retval;

		if ( $this->is_replace_query ) {

			$this->is_replace_query = false;
			$results                = $wp_query->get_posts();
		}

		return $results;
	}

}