<?php

namespace wpsolr\pro\classes\engines\algolia;

use wpsolr\core\classes\engines\WPSOLR_AbstractResultsClient;

class WPSOLR_Results_Algolia_Client extends WPSOLR_AbstractResultsClient {
	use WPSOLR_Algolia_Client;

	protected array $facets = [];
	protected array $facets_stats = [];

	/**
	 * WPSOLR_Results_Algolia_Client constructor.
	 *
	 * @param array $results
	 */
	public function __construct( $results ) {

		$this->results = $results['results'];

		foreach ( $this->results as $result ) {
			// Replace facets with excluded facets (if present))
			if ( ! empty( $result['facets'] ) ) {
				$this->facets = array_merge( $this->facets, $result['facets'] );
			}

			// Replace stats with excluded stats (if present))
			if ( ! empty( $result['facets_stats'] ) ) {
				$this->facets_stats = array_merge( $this->facets_stats, $result['facets_stats'] );
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function get_suggestions() {

		$suggests = $this->results[0]['suggest'] ?? [];

		$suggests_array = [];
		if ( isset( $suggests[ WPSOLR_Search_Algolia_Client::SUGGESTER_NAME ] ) ) {
			foreach ( $suggests[ WPSOLR_Search_Algolia_Client::SUGGESTER_NAME ][0]['options'] as $option ) {
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

		foreach ( (array) ( $this->results[0]['hits'] ?? [] ) as $result ) {

			$result['wpsolr_highlight'] = [];
			if ( ! empty( $result['_highlightResult'] ) ) {
				foreach ( $result['_highlightResult'] as $field_name => $highlighting ) {
					if ( ! empty( $highlighting['value'] ) ) {
						$result['wpsolr_highlight'][ $field_name ][] = $highlighting['value'];// add highlight in the document itself
					}
				}
				unset( $result['_highlightResult'] );
			}

			/*
			// Script results (like distance) are in the 'fields' property
			foreach ( (array) ( $result['fields'] ?? [] ) as $field_name => $field_value ) {
				$result[ $field_name ] = is_scalar( $field_value ) ? $field_value : ( empty( $field_value ) ? '' : $field_value[0] );
			}
			*/

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

		return $this->results[0]['nbHits'];
	}

	/**
	 * @inheridoc
	 */
	public function get_nb_rows() {
		return count( $this->results[0]['hits'] );
	}


	/**
	 * @inheritdoc
	 */
	public function get_facet( $facet_name ) {

		return $this->facets[ $facet_name ] ?? [];
	}

	/**
	 * Get highlighting
	 *
	 * @param object $result
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

		$facet_name_date = $this->_convert_field_name_if_date( $facet_name );
		$facet_stats     = $this->facets_stats[ $facet_name_date ] ?? [];

		return empty( $facet_stats ) ? [] :
			[
				sprintf( '%s-%s',
					$facet_stats['min'],
					$facet_stats['max'] )
				=> 1
			];
	}

	/**
	 * @inheritdoc
	 *
	 */
	public function get_top_hits( $agg_name ) {

		$hits = $this->results[0]['hits'] ?? [];

		// Convert.
		$top_hits = [];
		foreach ( $hits as $hit ) {
			$top_hits[ $hit['type'] ][] = $hit;
		}

		return $top_hits;
	}

	/**
	 * @inerhitDoc
	 */
	public function get_event_tracking_query_id() {
		return $this->results[0]['queryID'] ?? '';
	}

}
