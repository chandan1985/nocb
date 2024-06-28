<?php

namespace wpsolr\pro\classes\engines\elastic_site_search_php;

use Elastic\SiteSearch\Client\Client;
use Elastic\SiteSearch\Client\ClientBuilder;

/**
 * Some common methods of the client.
 *
 */
trait WPSOLR_Elastic_Site_Search_Client {

	protected $wpsolr_type = 'wpsolr_types';

	// Unique id to store attached decoded files.
	protected $WPSOLR_DOC_ID_ATTACHMENT = 'wpsolr_doc_id_attachment';

	/** @var Client */
	protected $search_engine_client;

	/** @var string */
	protected $index_label;

	// Index conf files
	protected $FILE_CONF_INDEX_5 = 'wpsolr_index_5.json';
	protected $FILE_CONF_INDEX_6 = 'wpsolr_index_6.json';
	protected $FILE_CONF_INDEX_7 = 'wpsolr_index_7.json';

	/**
	 * Engine type
	 *
	 * @return string
	 */
	protected function _get_index_doc_type() {
		return 'wpsolr-type';
	}

	/**
	 * Try to fix the current index configuration before retrying
	 *
	 * @param $error_msg
	 *
	 * @return bool
	 */
	protected function _try_to_fix_error_doc_type( $error_msg, $documents = [], $formatted_docs = [] ) {

		if ( false !== strpos( $error_msg, 'DocumentType' ) ) {
			$this->_fix_error_doc_type( '', $this->_get_index_doc_type() );

			// Fixed
			return true;
		}

		// Not fixed
		return false;
	}


	/**
	 * Fix the current index configuration with the guessed doc type
	 *
	 * @param string $index_property
	 * @param string $doc_type
	 *
	 * @return void
	 */
	protected
	function _fix_error_doc_type(
		$index_property, $doc_type
	) {
		$this->search_engine_client->createDocumentType( $this->get_index_label(), $doc_type );
	}

	/**
	 * @return array
	 */
	public
	function get_index() {

		$params = [ 'index' => $this->index_label ];

		if ( $this->index && ! empty( $this->_get_index_doc_type() ) ) {
			$params['type'] = $this->_get_index_doc_type();
		}

		return $params;
	}

	/**
	 * @param string $index_label
	 */
	public
	function set_index_label(
		$index_label
	) {
		$this->index_label = $index_label;
	}

	/**
	 * @return string
	 */
	public
	function get_index_label() {
		return $this->index_label;
	}

	/**
	 * @param $config
	 *
	 * @return Client
	 */
	protected
	function create_search_engine_client(
		$config
	) {

		$client = ClientBuilder::create( $config['username'] );

		$this->set_index_label( empty( $config ) ? '' : $config['index_label'] );

		return $client->build();
	}

	/**
	 * Retrieve the live Elasticsearch version
	 *
	 * @return string
	 * @throws \Exception
	 */
	protected
	function get_version() {

		$status      = $this->search_engine_client->getStatus();
		$status_data = $status->getResponse()->getData();
		if ( ! empty( $status_data ) && ! empty( $status_data['message'] ) ) {
			throw new \Exception( $status_data['message'] );
		}

		$version = $this->search_engine_client->getVersion();

		if ( version_compare( $version, '5', '<' ) ) {
			throw new \Exception( sprintf( 'WPSOLR works only with Elasticsearch >= 5. Your version is %s.', $version ) );
		}

		return $version;
	}

	/**
	 * Transform a string in a date.
	 *
	 * @param $date_str String date to convert from.
	 *
	 * @return string
	 */
	public
	function search_engine_client_format_date(
		$date_str
	) {

		if ( is_int( $date_str ) ) {

			$timestamp = $date_str;

		} else {

			$timestamp = strtotime( $date_str );
		}

		$string = date( 'Y-m-d\TH:i:s\Z', $timestamp );

		return $string;
	}

	protected
	function get_index_stats() {
		return $this->search_engine_client->getEngine( $this->get_index_label() );
	}

	/**
	 * Create a match_all query
	 *
	 * @return array
	 */
	protected
	function _create_match_all_query() {

		$params         = $this->get_index();
		$params['body'] = [ 'query' => [ 'match_all' => new \stdClass() ] ];

		return $params;
	}

	/**
	 * Create a bool query
	 *
	 * @param array $bool_query
	 *
	 * @return array
	 */
	protected
	function _create_bool_query(
		$bool_query
	) {

		$params         = $this->get_index();
		$params['body'] = [ 'query' => [ 'bool' => $bool_query ] ];

		return $params;
	}

	/**
	 * Create a query
	 *
	 * @param array $query
	 *
	 * @return array
	 */
	protected
	function _create_query(
		$query
	) {
		return $query;
	}
}
