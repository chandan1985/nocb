<?php

namespace wpsolr\pro\extensions\yoast_seo;

use wpsolr\core\classes\engines\WPSOLR_AbstractSearchClient;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\ui\WPSOLR_Query;
use wpsolr\core\classes\ui\WPSOLR_Query_Parameters;
use wpsolr\core\classes\WPSOLR_Db;
use wpsolr\core\classes\WpSolrSchema;

/**
 * Replace sitemaps queries
 */
class WPSOLR_Plugin_YoastSeo_Replace_Query extends WPSOLR_Db {

	const WPSOLR_QUERY_LAST_MODIFIED_GMT_PER_POST_TYPE = 'wpsolr_query_last_modified_gmt_per_post_type';
	const WPSOLR_QUERY_LAST_MODIFIED_GMT_PER_POST_TYPE_AND_CURSOR = 'wpsolr_query_last_modified_gmt_per_post_type_and_cursor';
	const WPSOLR_QUERY_POST_TYPE_COUNT = 'wpsolr_query_post_type_count';
	const WPSOLR_QUERY_SITEMAP_GET_POSTS = 'wpsolr_query_sitemap_get_posts';


	/** @var string */
	protected $sitemap_post_type;

	/** @var int */
	protected $site_map_max_entries;

	/** @var int */
	protected $sitemap_start;

	/** @var int */
	protected $sitemap_limit;

	/** @var string */
	protected $cursor_mark = '';

	/** @var array */
	protected $wpsolr_query_sitemap_get_posts_results;

	/**
	 * @return int
	 */
	public function get_site_map_max_entries(): int {
		return $this->site_map_max_entries;
	}

	/**
	 * @param int $site_map_max_entries
	 */
	public function set_site_map_max_entries( int $site_map_max_entries ): void {
		$this->site_map_max_entries = $site_map_max_entries;
	}

	/**
	 * @inheritDoc
	 */
	protected function _wpsolr_init() {
		add_filter( 'wpseo_typecount_join', [ $this, 'wpsolr_wpseo_typecount_join' ], 10, 2 );
	}


	public function wpsolr_wpseo_typecount_join( $value, $post_type ) {

		$this->sitemap_post_type = $post_type;

		return $value;
	}

	/**
	 * @inheritDoc
	 */
	public function wpsolr_get_is_custom() {
		return true;
	}

	/**
	 * @inheritDoc
	 */
	protected function _wpsolr_get_sql_fragments_per_query_type() {
		$results = [];

		if ( false ) {
			/** Twice faster, but not sure of results. hence not used yet. */

			/**
			 * Get last modified of any post type
			 *
			 * line 500 get_last_modified_gmt( $post_types, $return_all = false ): wp-content/plugins/wordpress-seo/inc/sitemaps/class-sitemaps.php
			 *
			 * SELECT post_type, MAX(post_modified_gmt) AS date
			 * FROM wp_19_posts
			 * WHERE post_status IN ('publish','inherit')
			 * AND post_type IN ('post','page','attachment')
			 * GROUP BY post_type
			 * ORDER BY post_modified_gmt DESC
			 */
			$results[ self::WPSOLR_QUERY_LAST_MODIFIED_GMT_PER_POST_TYPE ] = [
				'SELECT post_type, MAX(post_modified_gmt) AS date',
				'GROUP BY post_type',
				'ORDER BY post_modified_gmt DESC',
			];

			/**
			 * Get last modified of each sitemap
			 *
			 * line 169 get_index_links( $max_entries ): wp-content/plugins/wordpress-seo/inc/sitemaps/class-post-type-sitemap-provider.php
			 *    SELECT post_modified_gmt
			 *    FROM ( SELECT @rownum:=0 ) init
			 *    JOIN {$wpdb->posts} USE INDEX( type_status_date )
			 *    WHERE post_status IN ('" . implode( "','", $post_statuses ) . "')
			 *    AND post_type = %s
			 *    AND ( @rownum:=@rownum+1 ) %% %d = 0
			 *    ORDER BY post_modified_gmt ASC
			 */
			$results[ self::WPSOLR_QUERY_LAST_MODIFIED_GMT_PER_POST_TYPE_AND_CURSOR ] = [
				'SELECT post_modified_gmt',
				'FROM ( SELECT @rownum:=0 )',
				'AND ( @rownum:=@rownum+1 ) %% %d = 0',
			];

			/**
			 * Count post types
			 *
			 * line 347 get_post_type_count( $post_type ): wp-content/plugins/wordpress-seo/inc/sitemaps/class-post-type-sitemap-provider.php
			 * SELECT COUNT({$wpdb->posts}.ID)
			 * FROM {$wpdb->posts}
			 * {$join_filter}
			 * {$where}
			 * {$where_filter}
			 */
			$results[ self::WPSOLR_QUERY_POST_TYPE_COUNT ] = [
				'SELECT COUNT(',
				'_posts.ID)',
			];
		}

		/**
		 * Get post types for one sitemap
		 *
		 * line 532 get_posts( $post_type, $count, $offset ): wp-content/plugins/wordpress-seo/inc/sitemaps/class-post-type-sitemap-provider.php
		 *
		 * SELECT l.ID, post_title, post_content, post_name, post_parent, post_author, post_status, post_modified_gmt, post_date, post_date_gmt
		 * FROM (
		 * SELECT wp_8_posts.ID
		 * FROM wp_8_posts
		 * WHERE wp_8_posts.post_status IN ('publish')
		 * AND wp_8_posts.post_type = 'job_listing'
		 * AND wp_8_posts.post_password = ''
		 * AND wp_8_posts.post_date != '0000-00-00 00:00:00'
		 * ORDER BY wp_8_posts.post_modified ASC LIMIT %d OFFSET %d
		 * )
		 * o JOIN wp_8_posts l ON l.ID = o.ID
		 */
		$results[ self::WPSOLR_QUERY_SITEMAP_GET_POSTS ] = [
			'SELECT l.ID, post_title, post_content, post_name, post_parent, post_author, post_status, post_modified_gmt, post_date, post_date_gmt',
			"post_password = ''",
			"post_date != '0000-00-00 00:00:00'",
			'ON l.ID = o.ID',
		];

		return $results;
	}

	/**
	 * @inheritDoc
	 */
	public function wpsolr_get_is_custom_query( $query ) {
		return $this->wpsolr_get_is_custom_query_from_sql_fragments( $query );
	}

	/**
	 * @inheritdoc
	 */
	protected function wpsolr_custom_prepare( $query, $args ) {

		switch ( $this->wpsolr_query_type ) {
			case static::WPSOLR_QUERY_LAST_MODIFIED_GMT_PER_POST_TYPE:
				// No params
				break;

			case static::WPSOLR_QUERY_LAST_MODIFIED_GMT_PER_POST_TYPE_AND_CURSOR:
				$this->sitemap_post_type    = $args[0]; // 'post'
				$this->site_map_max_entries = $args[1]; // 500
				break;

			case static::WPSOLR_QUERY_SITEMAP_GET_POSTS:
				$this->sitemap_limit = $args[0]; // limit: 500
				$this->sitemap_start = $args[1]; // offset: 0
				break;
		}

		return $query;
	}

	/**
	 * @inheritdoc
	 */
	protected function wpsolr_custom_get_results( $query = null, $output = OBJECT ) {
		if ( in_array( $this->sitemap_post_type, WPSOLR_Service_Container::getOption()->get_option_index_post_types() ) ) {

			return $this->get_search_engine_results();

		} else {

			return parent::wpsolr_custom_get_results( $query, $output );
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function wpsolr_custom_get_col( $query = null, $x = 0 ) {
		if ( in_array( $this->sitemap_post_type, WPSOLR_Service_Container::getOption()->get_option_index_post_types() ) ) {

			return $this->get_search_engine_results();

		} else {

			return parent::wpsolr_custom_get_col( $query, $x );
		}
	}

	/**
	 * @inheritDoc
	 */
	public function wpsolr_custom_get_var( $query = null, $x = 0, $y = 0 ) {
		if ( in_array( $this->sitemap_post_type, WPSOLR_Service_Container::getOption()->get_option_index_post_types() ) ) {
			// Indexed, use
			return $this->get_search_engine_results();

		} else {
			// Not indexed, use default query.
			return parent::wpsolr_custom_get_var( $query, $x, $y );
		}
	}


	protected
	function get_search_engine_results() {


		$results      = [];
		$wpsolr_query = WPSOLR_Query_Parameters::CreateQuery();

		switch ( $this->wpsolr_query_type ) {

			case static::WPSOLR_QUERY_POST_TYPE_COUNT:

				$results = $this->_get_post_type_count( $wpsolr_query, $this->sitemap_post_type );
				break;

			case static::WPSOLR_QUERY_LAST_MODIFIED_GMT_PER_POST_TYPE:

				// Last date modified
				$wpsolr_query->set_wpsolr_sort( WPSOLR_AbstractSearchClient::SORT_CODE_BY_LAST_MODIFIED_DESC )
				             ->wpsolr_set_nb_results_by_page( 1 );

				foreach ( WPSOLR_Service_Container::getOption()->get_option_index_post_types() as $post_type ) {

					// For this post type
					$wpsolr_query->wpsolr_set_post_types( [ $post_type ] );

					$data = WPSOLR_Service_Container::get_solr_client( false )->get_results_data( $wpsolr_query, [], false );

					if ( ! empty( $data['results']['items'] ) && ( $post = get_post( $data['results']['items'][0][ WpSolrSchema::_FIELD_NAME_PID ] ) ) ) {
						$results[] = (object) [ 'post_type' => $post_type, 'date' => $post->post_modified_gmt ];
					}
				}
				break;

			case static::WPSOLR_QUERY_LAST_MODIFIED_GMT_PER_POST_TYPE_AND_CURSOR:


				$total_count = $this->_get_post_type_count( $wpsolr_query, $this->sitemap_post_type );
				$max_pages   = 1;
				$max_entries = $this->site_map_max_entries;
				if ( $total_count > $max_entries ) {
					$max_pages = (int) ceil( $total_count / $max_entries );
				}

				// Last date modified of a filemap containing $this->site_map_max_entries
				$wpsolr_query->wpsolr_set_post_types( [ $this->sitemap_post_type ] )
				             ->set_wpsolr_sort( WPSOLR_AbstractSearchClient::SORT_CODE_BY_LAST_MODIFIED_ASC )
				             ->wpsolr_set_nb_results_by_page( 1 );

				// Loop through all the index, and retrieve the last modified date of each first document of pagination (sitemap)
				// 500 is maximum sitemaps accepted by google
				$cursor_mark = ''; // start cursor
				for ( $i = 0; $i < $max_pages; $i ++ ) {

					// For this post type and this file map
					//$wpsolr_query->wpsolr_set_start( $i * $this->site_map_max_entries )
					$wpsolr_query->wpsolr_set_cursor_mark( $cursor_mark );

					$data = WPSOLR_Service_Container::get_solr_client( false )->get_results_data( $wpsolr_query, [], false );

					if ( ! empty( $data['results']['items'] ) && ( $post = get_post( $data['results']['items'][0][ WpSolrSchema::_FIELD_NAME_PID ] ) ) ) {

						$results[]   = $post->post_modified_gmt;
						$cursor_mark = $data['results']['cursor_mark'] ?? null;
					}
				}
				break;

			case static::WPSOLR_QUERY_SITEMAP_GET_POSTS:

				if ( ! isset( $this->wpsolr_query_sitemap_get_posts_results ) ) {
					/** Requires ES index max_result_window > #of post types */
					$wpsolr_query->wpsolr_set_post_types( [ $this->sitemap_post_type ] )
					             ->set_wpsolr_sort( WPSOLR_AbstractSearchClient::SORT_CODE_BY_LAST_MODIFIED_ASC )
					             ->wpsolr_set_start( $this->sitemap_start )
					             ->wpsolr_set_nb_results_by_page( $this->site_map_max_entries ) // Returns all sitemap entries at one
					             ->wpsolr_set_fields( [
							WpSolrSchema::_FIELD_NAME_TITLE,
							WpSolrSchema::_FIELD_NAME_STATUS_S,
							WpSolrSchema::_FIELD_NAME_DISPLAY_MODIFIED,
							WpSolrSchema::_FIELD_NAME_POST_DATE,
						] );

					$data = WPSOLR_Service_Container::get_solr_client( false )->get_results_data( $wpsolr_query, [], false );

					// Cache results
					$this->wpsolr_query_sitemap_get_posts_results = $data['results']['items'];
				}

				/* Version with a cursor: horribly slow
					$wpsolr_query->wpsolr_set_post_types( [ $this->sitemap_post_type ] )
								 ->set_wpsolr_sort( WPSOLR_AbstractSearchClient::SORT_CODE_BY_LAST_MODIFIED_ASC )
								 ->wpsolr_set_nb_results_by_page( $this->sitemap_limit )
								 ->wpsolr_set_cursor_mark( '' ); // start scrolling

					// Scroll until the selected sitemap
					for ( $i = 0; $i <= $this->sitemap_start; $i ++ ) {
						$data = WPSOLR_Service_Container::get_solr_client( false )->get_results_data( $wpsolr_query, [], false );

						$cursor_mark = $data['results']['cursor_mark'] ?? null;
						$wpsolr_query->wpsolr_set_cursor_mark( $cursor_mark );
					}
					*/

				/*
				$results = get_posts(
					[
						//'is_wpsolr'           => true,
						'numberposts'         => count( $data['results']['items'] ),
						//'post_type'           => $indexed_post_types,
						//'post_status'         => [ 'any', 'trash' ],
						// Added 'trash' for admin archives. 'any' does not retrieve 'trash'.
						'post__in'            => array_column( $data['results']['items'], WpSolrSchema::_FIELD_NAME_PID ),
						'orderby'             => 'post__in',
						'exclude_from_search' => false,
						// Get posts in same order as documents in Solr results.
					]
				);*/

				/**
				 * Get a 100 max slice of the sitemap entries
				 */
				for ( $i = 0; $i < $this->sitemap_limit; $i ++ ) {

					if ( isset( $this->wpsolr_query_sitemap_get_posts_results[ $i ] ) ) {

						$document  = $this->wpsolr_query_sitemap_get_posts_results[ $i ];
						$results[] = (object) [
							'ID'                => $document[ WpSolrSchema::_FIELD_NAME_PID ],
							'post_title'        => $document[ WpSolrSchema::_FIELD_NAME_TITLE ],
							'post_content'      => null,
							'post_name'         => null,
							'post_parent'       => null,
							'post_author'       => null,
							'post_status'       => $document[ WpSolrSchema::_FIELD_NAME_STATUS_S ],
							'post_modified_gmt' => $document[ WpSolrSchema::_FIELD_NAME_DISPLAY_MODIFIED ],
							'post_date'         => $document[ WpSolrSchema::_FIELD_NAME_POST_DATE ],
							'post_date_gmt'     => $document[ WpSolrSchema::_FIELD_NAME_POST_DATE ],
						];
					}

				}

				break;
		}


		// Reset
		unset( $this->wpsolr_query_type );

		return $results;
	}

	/**
	 * @param WPSOLR_Query $wpsolr_query
	 * @param string $sitemap_post_type
	 *
	 * @return int
	 * @throws \Exception
	 */
	protected function _get_post_type_count( WPSOLR_Query $wpsolr_query, string $sitemap_post_type ) {

		// Just a COUNT
		$wpsolr_query->wpsolr_set_post_types( [ $this->sitemap_post_type ] )->wpsolr_set_nb_results_by_page( 0 );

		// Query now
		$data = WPSOLR_Service_Container::get_solr_client( false )->get_results_data( $wpsolr_query, [], false );

		// Nb results
		return (int) $data['results']['nb_results'];
	}
}