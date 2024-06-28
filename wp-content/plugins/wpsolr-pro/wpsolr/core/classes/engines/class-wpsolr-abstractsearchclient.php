<?php

namespace wpsolr\core\classes\engines;

use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\ui\WPSOLR_Query;
use wpsolr\core\classes\utilities\WPSOLR_Option;
use wpsolr\core\classes\WpSolrSchema;
use wpsolr\pro\classes\engines\algolia\WPSOLR_Search_Algolia_Client;
use wpsolr\pro\classes\engines\amazon_personalize\WPSOLR_Search_Amazon_Personalize_Client;
use wpsolr\pro\classes\engines\elastic_site_search_php\WPSOLR_Search_Elastic_Site_Search_Client;
use wpsolr\pro\classes\engines\google_retail\WPSOLR_Search_Google_Retail_Client;
use wpsolr\pro\classes\engines\recombee\WPSOLR_Search_Recombee_Client;
use wpsolr\pro\extensions\recommendations\WPSOLR_Option_Recommendations;

abstract class WPSOLR_AbstractSearchClient extends WPSOLR_AbstractSearchClient_Root {

	/**
	 * @inheridoc
	 */
	static function create_from_config( $config ) {

		switch ( $config['index_engine'] ) {

			case static::ENGINE_SWIFTYPE:
				return new WPSOLR_Search_Elastic_Site_Search_Client( $config );

			case static::ENGINE_ALGOLIA:
				return new WPSOLR_Search_Algolia_Client( $config );

			case static::ENGINE_RECOMBEE:
				return new WPSOLR_Search_Recombee_Client( $config );

			case static::ENGINE_GOOGLE_RETAIL:
				return new WPSOLR_Search_Google_Retail_Client( $config );

			case static::ENGINE_AMAZON_PERSONALIZE:
				return new WPSOLR_Search_Amazon_Personalize_Client( $config );

			default:
				return parent::create_from_config( $config );
				break;

		}
	}

	/**
	 * @inheridoc
	 */
	public function get_recommendations_html( $recommendation, $wpsolr_query ) {

		$template_data = $this->get_recommendations_items_data( $wpsolr_query, $recommendation );

		/**
		 * Build the recommendations HTML from the template
		 */
		return WPSOLR_Service_Container::get_template_builder()->load_template(
			WPSOLR_Option_Recommendations::get_recommendation_layout_file( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_UUID ] ),
			$template_data
		);
	}

	/****************************************************************************************
	 *
	 * User events related methods (analytics, personalization, recommendations, AI reranking, ...)
	 *
	 ****************************************************************************************/

	/**
	 * @param WPSOLR_Query $wpsolr_query
	 *
	 * @return void
	 */
	protected function _set_event_tracking( $wpsolr_query ) {

		if ( WPSOLR_Service_Container::getOption()->get_search_is_event_tracking() ) {
			$this->search_engine_client_event_tracking_set_is_return_query_id( true );
			$this->search_engine_client_event_tracking_set_user_is_personalization( true );
			$this->search_engine_client_event_tracking_set_user_token( 'user-id' );
		}
	}

	/**
	 * Returns a query id used by event tracking
	 *
	 * @param bool $is_return_query_id
	 */
	public
	function search_engine_client_event_tracking_set_is_return_query_id(
		$is_return_query_id
	) {
		// Override in children
	}


	/**
	 * Activate search personalization
	 *
	 * @param bool $is_personalization
	 *
	 * @return
	 */
	public function search_engine_client_event_tracking_set_user_is_personalization( $is_personalization ) {
		// Override in children
	}

	/**
	 * Set user token
	 *
	 * @param string $user_token
	 *
	 * @return
	 */
	public function search_engine_client_event_tracking_set_user_token( $user_token ) {
		// Override in children
	}

	/**
	 * Get recommendation items data from request.
	 *
	 * @param string $index_uuid
	 * @param WPSOLR_Query $wpsolr_query
	 * @param array $recommendation
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function get_recommendations_items_data( WPSOLR_Query $wpsolr_query, array $recommendation ): array {

		/**
		 * Retrieve suggestions
		 */
		//$wpsolr_query->set_wpsolr_query( $query );
		$wpsolr_query->set_wpsolr_query_cache_key( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_UUID ] );
		$wpsolr_query->wpsolr_set_recommendation( $recommendation );
		$wpsolr_query->wpsolr_set_nb_results_by_page( $recommendation[ WPSOLR_Option::OPTION_SUGGESTION_NB ] );
		$recommendations = $this->get_results_data( $wpsolr_query, [], false );

		/**
		 * Build the template data from results
		 */
		$template_data            = [];
		$template_data['results'] = [];
		foreach ( $recommendations['results']['items'] as $results ) {

			$template_data['results'][] = $results;
		}

		/*
		 * Build the template data from $recommendation
		 */
		$template_data['settings'] = $recommendation;

		return $template_data;
	}

	/**
	 *
	 * @return array
	 */
	public function get_recommendation() {
		return $this->recommendation ?? [];
	}

	/**
	 * @param array $recommendation
	 *
	 * @return void
	 */
	public function set_recommendation( $recommendation ) {
		$this->recommendation = $recommendation;
	}

	/**
	 * A@inheridoc
	 *
	 */
	protected function add_ids_excluded_from_search_filter_query_fields( WPSOLR_Query $wpsolr_query ) {

		$excluded_ids_from_searching = WPSOLR_Service_Container::getOption()->get_option_index_post_excludes_ids_from_searching();

		if ( ! empty( $excluded_ids_from_searching ) ) {

			if ( ! $this->is_galaxy_master ) {

				$this->search_engine_client_add_filter_not_in_terms( 'all ids excluded from searching',
					WpSolrSchema::_FIELD_NAME_PID,
					$excluded_ids_from_searching
				);

			} else {

				$this->search_engine_client_add_filter_not_in_terms_of_other_sites( 'slave ids excluded from searching',
					WpSolrSchema::_FIELD_NAME_PID,
					$excluded_ids_from_searching,
					$this->galaxy_slave_filter_value
				);

			}

		}
	}

	/**
	 * @inheridoc
	 */
	protected
	function get_post_thumbnail(
		$document, $post_id
	) {

		if ( $this->is_galaxy_master ) {

			// Master sites must get thumbnails from the index, as the $post_id is not in local database
			$field_name_post_thumbnail_href_str = WpSolrSchema::_FIELD_NAME_POST_THUMBNAIL_HREF_STR;
			$results                            = $this->property_exists( $document, $field_name_post_thumbnail_href_str ) ? $document->$field_name_post_thumbnail_href_str : [];

		} else {

			// $post_id is in local database, use the standard way
			$results = wp_get_attachment_image_src( ( 'attachment' === get_post_type( $post_id ) ) ? $post_id : get_post_thumbnail_id( $post_id ) );
		}

		return ! empty( $results ) ? ( is_array( $results ) ? $results[0] : $results ) : null;
	}

	/**
	 * @inheridoc
	 */
	protected
	function get_post_url(
		$url_is_edit, $model, $document, $post_id
	) {

		if ( $this->is_galaxy_master ) {

			// Master sites must get thumbnails from the index, as the $post_id is not in local database
			$result = ! empty( $document->post_href_str ) ? ( is_array( $document->post_href_str ) ? $document->post_href_str[0] : $document->post_href_str ) : null;

		} else {

			// $post_id is in local database, use the standard way
			$result = $model->get_permalink( $url_is_edit, $post_id );
		}

		return $result;
	}

	/**
	 * @inheridoc
	 * @throws \Exception
	 */
	protected function _get_posts_from_pids(): array {

		if ( ! $this->is_galaxy_master ) {
			// Local search: return posts from local database
			$results = parent::_get_posts_from_pids();

		} else {

			// Create pseudo posts from Solr results
			$results = [ 'posts' => [], 'documents' => [] ];
			foreach ( $this->results as $document ) {

				unset( $current_post );
				$current_post         = new \stdClass();
				$current_post->ID     = $document->id;
				$current_post->filter = 'raw';

				$wp_post = new \WP_Post( $current_post );

				array_push( $results['posts'], $wp_post );
				array_push( $results['documents'], $document );
			}

		}

		return $results;
	}

}
