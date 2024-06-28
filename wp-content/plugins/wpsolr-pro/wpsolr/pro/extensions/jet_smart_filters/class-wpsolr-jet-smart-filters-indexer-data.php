<?php

namespace wpsolr\pro\extensions\jet_smart_filters;

/**
 * Include the external class, we never know
 */
require jet_smart_filters()->plugin_path( 'includes/indexer/data.php' );

/**
 * Rewrite Jet Filters indexer methods to use WPSOLR facets instead of indexer table
 */
class WPSOLR_Jet_Smart_Filters_Indexer_Data extends \Jet_Smart_Filters_Indexer_Data {

	/**
	 * Return all counters for different providers
	 *
	 * @return array|mixed
	 */
	public function prepare_provider_counts() {

		$providers_counts     = array();
		$providers_post_types = $this->get_providers_post_types();
		$this->indexed_data   = $this->get_data();

		foreach ( $providers_post_types as $provider => $post_type ) {
			foreach ( $post_type as $key => $current_posts ) {
				$providers_counts = $this->get_posts_counts( $providers_counts, $provider, $current_posts );
			}
		}

		return $providers_counts;

	}

	/**
	 * Return counters for prepared provider
	 *
	 * @param $posts_number
	 * @param $provider
	 * @param $current_posts
	 *
	 * @return mixed
	 */
	public function get_posts_counts( $posts_number, $provider, $current_posts ) {

		foreach ( $this->indexed_data as $query_type => $posts ) {

			$args    = explode( '/', $query_type );
			$row_key = $this->raw_key( array( $provider, $args[2], $args[3], ) );

			$posts_number[ $row_key ] += count( array_intersect( $posts, $current_posts ) );

		}

		return $posts_number;

	}

	/**
	 * Return providers post types
	 *
	 * @return array
	 */
	public function get_providers_post_types() {

		/**
		 * {"jet-engine\/wpsolr-jet-listing-1":{"post_status":["publish"],"post_type":"projects","posts_per_page":"5","paged":"1"},"jet-engine\/wpsolr-jet-listing-2":{"post_status":["publish"],"post_type":"tasks","posts_per_page":"5","paged":"1"}}
		 */
		$providers_args = $this->get_providers_query_args();
		$posts          = array();

		foreach ( $providers_args as $key => $args ) {
			if ( isset( $args['post_type'] ) ) {
				$post_type              = $args['post_type'];
				$args['fields']         = 'ids';
				$args['posts_per_page'] = - 1;
				unset( $args['jet_smart_filters'] );

				if ( is_array( $post_type ) ) {
					foreach ( $post_type as $type ) {
						$posts[ $key ][ $type ] = get_posts( $args );
					}
				} else {
					$posts[ $key ][ $post_type ] = get_posts( $args );
				}
			}
		}

		return $posts;

	}

}
