<?php

namespace wpsolr\pro\classes\engines\elastic_site_search_php;

use wpsolr\core\classes\engines\elastic_site_search_php\WPSOLR_SearchElasticsearchClient;
use wpsolr\core\classes\engines\elasticsearch_php\WPSOLR_ResultsElasticsearchClient;

/**
 * Class WPSOLR_ResultsElasticsearchClient
 *
 * @property \Elastica\ResultSet $results
 */
class WPSOLR_Results_Elastic_Site_Search_Client extends WPSOLR_ResultsElasticsearchClient {
	use WPSOLR_Elastic_Site_Search_Client;

	/**
	 * WPSOLR_ResultsElasticsearchClient constructor.
	 *
	 * @param array $results
	 */
	public function __construct( $results ) {

		$this->results = $results;

		$error_message = $this->_get_error_message();
		if ( $error_message && ! empty( $error_message ) ) {
			throw new \Exception( $error_message );
		}

	}

	/**
	 * @return string
	 */
	function _get_error_message() {
		return empty( $this->results['errors'] ) ? '' : wp_json_encode( $this->results['errors'] );
	}

	/**
	 * @return mixed
	 */
	public function get_suggestions() {

		$suggests = $this->results['suggest'] ?? [];

		$suggests_array = [];
		if ( isset( $suggests[ WPSOLR_SearchElasticsearchClient::SUGGESTER_NAME ] ) ) {
			foreach ( $suggests[ WPSOLR_SearchElasticsearchClient::SUGGESTER_NAME ][0]['options'] as $option ) {
				array_push( $suggests_array, [ 'text' => $option['text'] ] );
			}
		}

		return $suggests_array;
	}

	/**
	 * @inheritDoc
	 *
	 * return array
	 */
	public function get_results() {

		$results = [];

		foreach ( (array) ( $this->results['records'][ $this->_get_index_doc_type() ] ?? [] ) as $hit ) {
			$result = $hit;
			//$result['wpsolr_highlight'] = $hit['highlight'] ?? []; // add highlight in the document itself

			// Script results (like distance) are in the 'fields' property
			/*foreach ( (array) ( $hit['fields'] ?? [] ) as $field_name => $field_value ) {
				$result[ $field_name ] = is_scalar( $field_value ) ? $field_value : ( empty( $field_value ) ? '' : $field_value[0] );
			}*/

			$results[] = (object) $result;
		}

		return $results;
	}


	/**
	 * Get nb of results.
	 *
	 * @return int
	 */
	public function get_nb_results() {

		$total = 0;
		foreach ( $this->results['info'] as $type_info ) {
			$total += $type_info['total_result_count'];
		}

		return $total;
	}

	/**
	 * Get nb of results.
	 *
	 * @return int
	 */
	public function get_nb_rows() {
		return $this->results['record_count'];
	}

	/**
	 * @inheritDoc
	 */
	public function get_cursor_mark() {
		return $this->results['_scroll_id'] ?? '';
	}

	protected function _get_buckets( $agg_name ) {
		$aggregations = $this->results['aggregations'] ?? [];

		$buckets = [];
		if ( isset( $aggregations['buckets'] ) ) {

			$buckets = $aggregations['buckets'];

		} elseif ( isset( $aggregations[ $agg_name ] ) && isset( $aggregations[ $agg_name ] ) && isset( $aggregations[ $agg_name ][ $agg_name ]['buckets'] ) ) {

			$buckets = $aggregations[ $agg_name ][ $agg_name ]['buckets'];
		}

		return $buckets;
	}

	/**
	 * @inheritdoc
	 */
	public function get_facet( $facet_name ) {
		try {

			$buckets = $this->_get_buckets( $facet_name );

			// Convert.
			$facets = [];
			foreach ( $buckets as $bucket ) {
				// $bucket['key'] contains the range
				$facets[ $bucket['key'] ] = $bucket['doc_count'];
			}

			return $facets;

		} catch ( \Exception $e ) {
			// Prevent the error.
			return null;
		}
	}

	/**
	 * Get highlighting
	 *
	 * @param \Elastica\Result $result
	 *
	 * @return array
	 */
	public function get_highlighting( $result ) {
		return $result->wpsolr_highlight ?? [];
	}

	/**
	 * @inheridoc
	 */
	public function get_stats( $facet_name, array $options = [] ) {

		$aggregation = $this->results['aggregations'][ $facet_name ] ?? [];

		return [ sprintf( '%s-%s', $aggregation[ $facet_name ]['min'], $aggregation[ $facet_name ]['max'] ) => $aggregation[ $facet_name ]['count'] ];
	}

	/**
	 * @inheritdoc
	 *
	 * @link https://www.elastic.co/guide/en/elasticsearch/reference/6.6/search-aggregations-metrics-top-hits-aggregation.html
	 */
	public function get_top_hits( $agg_name ) {

		// Can emit sometimes the error "This result set does not contain an aggregation named type." (seen during Bonsai tests)
		// Caught and retried in wdm_return_solr_rows()

		$buckets = $this->_get_buckets( $agg_name );

		// Convert.
		$top_hits = [];
		foreach ( $buckets as $bucket ) {
			foreach ( $bucket['top_hits']['hits']['hits'] as $hit ) {
				$top_hits[ $bucket['key'] ][] = $hit['_source'];
			}
		}

		return $top_hits;
	}
}
