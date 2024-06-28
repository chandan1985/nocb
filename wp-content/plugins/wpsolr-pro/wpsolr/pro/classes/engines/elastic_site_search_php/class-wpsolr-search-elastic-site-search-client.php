<?php

namespace wpsolr\pro\classes\engines\elastic_site_search_php;

use Elastic\SiteSearch\Client\Client;
use wpsolr\core\classes\engines\elasticsearch_php\WPSOLR_SearchElasticsearchClient;
use wpsolr\core\classes\WpSolrSchema;

class WPSOLR_Search_Elastic_Site_Search_Client extends WPSOLR_SearchElasticsearchClient {
	use WPSOLR_Elastic_Site_Search_Client;

	const IS_LOG_QUERY_TIME_IMPLEMENTED = true;

	/**
	 *
	 * @param Client $search_engine_client
	 *
	 * @return WPSOLR_Results_Elastic_Site_Search_Client
	 * @throws \Exception
	 */
	public function search_engine_client_execute( $search_engine_client, $random_score ) {

		$this->search_engine_client_build_query();

		$the_query = array_merge( $this->query_url_params, $this->_create_query( isset( $this->completion ) ? $this->completion : $this->query ) );

		try {

			$this->the_query = $the_query;
			$results         = $this->search_engine_client->search( $this->get_index_label(), '', $the_query );

		} catch ( \Exception $e ) {
			// Log the query
			error_log( sprintf( 'WPSOLR Elastisearch query: %s', wp_json_encode( $the_query['body'] ) ) );

			throw ( $e );
		}

		return new WPSOLR_Results_Elastic_Site_Search_Client( $results );
	}

	/**
	 * @inheritDoc
	 */
	protected function admin_create_index( &$index_parameters ) {

		$this->search_engine_client->createEngine( $this->get_index_label(),
			isset( $this->config['extra_parameters']['index_language_code'] ) && ! empty( $this->config['extra_parameters']['index_language_code'] ) ?
				$this->config['extra_parameters']['index_language_code'] :
				null );
	}

	/**
	 * @inheritDoc
	 */
	public function admin_delete_index() {
		$response = $this->search_engine_client->deleteEngine( $this->get_index_label() );
	}

	/**
	 * @inheritDoc
	 */
	protected function admin_is_index_exists( $is_throw_error = false ) {

		// this methods throws an error if index is not responding.
		try {

			$stats = $this->get_index_stats();

		} catch ( \Exception $e ) {

			if ( $is_throw_error ) {
				throw $e;
			}

			return false;
		}

		$this->throw_exception_if_error( $stats );

		// Index exists.
		return true;
	}

	/**
	 * @inheritDoc
	 */
	protected function admin_index_update( &$index_parameters ) {
		// Nothing. All settings are in Swiftype admin panel
	}

	/**
	 * @inheritDoc
	 */
	protected function throw_exception_if_error( $data ) {

		if ( is_string( $data ) ) {
			// Elasticpress returns a string

			$error = $data;

		} elseif ( ! empty( $data ) && ! empty( $data['error'] ) ) {

			$error = $data['error'];
		}

		if ( ! empty( $error ) ) {
			// Connexion error: cannot be recovered. For instance, AWS security not set properly.
			throw new \Exception( "Problem while connecting to your index :<br><br> \"{$error}\"" );
		}

	}

	/**
	 * @inheritDoc
	 */
	protected function _log_query_as_string() {
		return wp_json_encode( $this->the_query, JSON_PRETTY_PRINT );
	}


	/**
	 * @inheritDoc
	 * https://swiftype.com/documentation/site-search/searching/pagination
	 */
	public function search_engine_client_set_start( $start ) {
		// Not used.
	}

	/**
	 * @inheritDoc
	 * https://swiftype.com/documentation/site-search/searching/pagination
	 */
	public function search_engine_client_set_rows( $rows ) {
		$this->query['per_page'] = $rows;
	}

	/**
	 * @inheritDoc
	 * https://swiftype.com/documentation/site-search/searching/pagination
	 */
	public function search_engine_client_set_paged( $page ) {
		$this->query['page'] = $page + 1; // starts at 1, not 0
	}

	/**
	 * @inheritDoc
	 * https://swiftype.com/documentation/site-search/searching/fetch-fields
	 */
	public
	function search_engine_client_set_fields(
		$fields
	) {

		$fields_swiftype = [
			WpSolrSchema::_FIELD_NAME_ID,
			WpSolrSchema::_FIELD_NAME_PID,
			WpSolrSchema::_FIELD_NAME_TYPE,
			WpSolrSchema::_FIELD_NAME_META_TYPE_S,
			WpSolrSchema::_FIELD_NAME_TITLE,
			WpSolrSchema::_FIELD_NAME_NUMBER_OF_COMMENTS,
			//WpSolrSchema::_FIELD_NAME_COMMENTS,
			WpSolrSchema::_FIELD_NAME_DISPLAY_DATE,
			WpSolrSchema::_FIELD_NAME_DISPLAY_MODIFIED,
			//'*' . WpSolrSchema::_FIELD_NAME_CATEGORIES_STR,
			WpSolrSchema::_FIELD_NAME_AUTHOR,
			//'*' . WpSolrSchema::_FIELD_NAME_POST_THUMBNAIL_HREF_STR,
			//'*' . WpSolrSchema::_FIELD_NAME_POST_HREF_STR,
			//WpSolrSchema::_FIELD_NAME_SNIPPET_S,
		];

		$this->source_fields                                         = $fields_swiftype;
		$this->query['fetch_fields'][ $this->_get_index_doc_type() ] = $fields_swiftype;
	}

}
