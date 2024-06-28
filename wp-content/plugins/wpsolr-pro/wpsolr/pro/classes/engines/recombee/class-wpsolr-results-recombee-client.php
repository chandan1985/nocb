<?php

namespace wpsolr\pro\classes\engines\recombee;

use wpsolr\core\classes\engines\WPSOLR_AbstractResultsClient;

class WPSOLR_Results_Recombee_Client extends WPSOLR_AbstractResultsClient {
	use WPSOLR_Recombee_Client;

	protected array $facets = [];
	protected array $facets_stats = [];

	/**
	 * WPSOLR_Results_Recombee_Client constructor.
	 *
	 * @param array $results
	 */
	public function __construct( $results ) {

		$this->results = $results;
	}

	/**
	 * @return mixed
	 */
	public function get_suggestions() {
		return [];
	}

	/**
	 * @inheritDoc
	 *
	 * return array
	 */
	public function get_results() {
		$results = [];
		foreach ( $this->results['recomms'] as $result ) {
			$fields       = $result['values'];
			$fields['id'] = $result['id'];
			$results[]    = $fields;
		}

		return $results;
	}


	/**
	 * Get nb of results.
	 *
	 * @return int
	 */
	public function get_nb_results() {
		return $this->get_nb_rows();
	}

	/**
	 * @inheridoc
	 */
	public function get_nb_rows() {
		return count( $this->results['recomms'] );
	}


	/**
	 * @inheritdoc
	 */
	public function get_facet( $facet_name ) {
		return [];
	}

	/**
	 * Get highlighting
	 *
	 * @param object $result
	 *
	 * @return array
	 */
	public function get_highlighting( $result ) {
		return [];
	}

	/**
	 * @inheridoc
	 */
	public function get_stats( $facet_name, array $options = [] ) {

		return [];
	}

	/**
	 * @inheritdoc
	 *
	 */
	public function get_top_hits( $agg_name ) {
		return [];
	}

	/**
	 * @inerhitDoc
	 */
	public function get_event_tracking_query_id() {
		return $this->results['recommid'] ?? '';
	}

}
