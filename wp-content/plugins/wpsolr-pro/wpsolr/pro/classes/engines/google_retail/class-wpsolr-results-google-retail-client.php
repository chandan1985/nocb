<?php

namespace wpsolr\pro\classes\engines\google_retail;

use Google\ApiCore\PagedListResponse;
use Google\Cloud\Retail\V2\CustomAttribute;
use Google\Cloud\Retail\V2\SearchResponse;
use Google\Cloud\Retail\V2\SearchResponse\Facet;
use Google\Cloud\Retail\V2\SearchResponse\SearchResult;
use wpsolr\core\classes\engines\google_retail\WPSOLR_Search_Recombee_Client;
use wpsolr\core\classes\engines\WPSOLR_AbstractResultsClient;
use wpsolr\core\classes\utilities\WPSOLR_Regexp;
use wpsolr\core\classes\WpSolrSchema;

class WPSOLR_Results_Google_Retail_Client extends WPSOLR_AbstractResultsClient {
	use WPSOLR_Google_Retail_Client;

	/**
	 * @var SearchResponse
	 */
	protected $results;

	protected array $facets = [];
	protected array $facets_stats = [];

	/**
	 * WPSOLR_Results_Google_Retail_Client constructor.
	 *
	 * @param PagedListResponse $results
	 */
	public function __construct( $results ) {

		$this->results = $results->getPage()->getResponseObject();

		/** @var Facet $facet */
		foreach ( $this->results->getFacets() as $facet ) {
			$values = [];
			$stats  = [];
			foreach ( $facet->getValues() as $value ) {
				if ( $value->hasInterval() ) {
					// Min-max
					$stats = [
						'min' => $value->getMinValue(),
						'max' => $value->getMaxValue()
					];

					// Ranges
					$interval                                                                                = $value->getInterval();
					$values[ sprintf( '%s-%s', $interval->getMinimum(), $interval->getExclusiveMaximum() ) ] = $value->getCount();


					foreach ( $value->getInterval() as $interval ) {
						$t = 1;
					}
				}

				if ( $value->hasValue() ) {
					$values[ $value->getValue() ] = $value->getCount();
				}
			}

			if ( ! empty( $values ) ) {
				$this->facets[ $this->_get_unformatted_attribute_name( $facet->getKey() ) ] = $values;
			}
			if ( ! empty( $stats ) ) {
				$this->facets_stats[ $this->_get_unformatted_attribute_name( $facet->getKey() ) ] = $stats;
			}
		}

		/*
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
		*/
	}

	/**
	 * Raw results
	 * @return mixed
	 */
	public function get_raw_results() {
		return $this->results->serializeToJsonString();
	}

	/**
	 * @return mixed
	 */
	public function get_suggestions() {

		$suggests = $this->results[0]['suggest'] ?? [];

		$suggests_array = [];
		if ( isset( $suggests[ WPSOLR_Search_Recombee_Client::SUGGESTER_NAME ] ) ) {
			foreach ( $suggests[ WPSOLR_Search_Recombee_Client::SUGGESTER_NAME ][0]['options'] as $option ) {
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

		/** @var SearchResult $response */
		foreach ( $this->results->getResults() as $response ) {

			$result = [];

			/*
			$result['wpsolr_highlight'] = [];
			if ( ! empty( $result['_highlightResult'] ) ) {
				foreach ( $result['_highlightResult'] as $field_name => $highlighting ) {
					if ( ! empty( $highlighting['value'] ) ) {
						$result['wpsolr_highlight'][ $field_name ][] = $highlighting['value'];// add highlight in the document itself
					}
				}
				unset( $result['_highlightResult'] );
			}
			*/

			/*
			// Script results (like distance) are in the 'fields' property
			foreach ( (array) ( $result['fields'] ?? [] ) as $field_name => $field_value ) {
				$result[ $field_name ] = is_scalar( $field_value ) ? $field_value : ( empty( $field_value ) ? '' : $field_value[0] );
			}
			*/

			$product = $response->getProduct();
			if ( empty( $product->getId() ) ) {
				// Google Retail issue with retrievable attributes: let's retrieve it from database instead
				// Example: projects/23708114026/locations/global/catalogs/default_catalog/branches/0/products/32 => 32

				/** @var \WP_Post $post */
				$post = get_post( WPSOLR_Regexp::extract_last_separator( $product->getName(), '/' ) );

				$result[ WpSolrSchema::_FIELD_NAME_ID ]  = $post->ID;
				$result[ WpSolrSchema::_FIELD_NAME_PID ] = $post->ID;
				//$result[ WpSolrSchema::_FIELD_NAME_DISPLAY_MODIFIED ] = $post->post_date;
				$result[ WpSolrSchema::_FIELD_NAME_TITLE ] = $post->post_title;
				//$result[ WpSolrSchema::_FIELD_NAME_PERMALINK ]        = get_permalink( $post );
				$result[ WpSolrSchema::_FIELD_NAME_TYPE ] = $post->post_type;

			} else {


				// $result[ WpSolrSchema::_FIELD_NAME_ID ]               = $product->getId(); no id returned by the search! Using the attribute instead
				$result[ WpSolrSchema::_FIELD_NAME_DISPLAY_MODIFIED ] = $product->getPublishTime();
				// $result[ WpSolrSchema::_FIELD_NAME_CONTENT ] = $product->getDescription(  );
				$result[ WpSolrSchema::_FIELD_NAME_TITLE ] = $product->getTitle();
				//$result['product_tag_str'] = $product->getTags(  );
				//$result['flat_hierarchy_product_cat_str'] = $product->getCategories();
				//$result['_price_f'] = $product->getPriceInfo( )['price'];
				$result[ WpSolrSchema::_FIELD_NAME_PERMALINK ] = $product->getUri();


				foreach ( $product->getAttributes() as $attribute_name => $attribute ) {
					/** @var CustomAttribute $attribute */
					$values = [];
					foreach ( $attribute->getText() as $value ) {
						$values[] = $value;
					}
					foreach ( $attribute->getNumbers() as $value ) {
						$values[] = $value;
					}

					$unformatted_attribute_name = $this->_get_unformatted_attribute_name( $attribute_name );
					switch ( $unformatted_attribute_name ) {
						case WpSolrSchema::_FIELD_NAME_CATEGORIES_STR:
							// This is the only retrievable field which is actually an array
							$returned_value = $values;
							break;

						default:
							// Retrievable attributes are all arrays, but the corresponding WPSOLR field is not
							$returned_value = $values[0];

							break;
					}

					foreach ( static::$COPY_CUSTOM_FIELD_NAMES as $original_copied_field_name ) {
						if ( $original_copied_field_name . WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING === $unformatted_attribute_name ) {
							// Store the copied field on the original field
							$unformatted_attribute_name = $original_copied_field_name;
							break;
						}
					}
					$result[ $unformatted_attribute_name ] = $returned_value;
				}
			}

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
		return $this->results->getTotalSize();
	}

	/**
	 * @inheridoc
	 */
	public function get_nb_rows() {
		return $this->results->getResults()->count();
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

		//$facet_name_date = $this->_convert_field_name_if_date( $facet_name );
		$facet_stats = $this->facets_stats[ $facet_name ] ?? [];

		return empty( $facet_stats ) ? [] :
			[
				sprintf( '%s-%s',
					$facet_stats['min'],
					$facet_stats['max'] )
				=> 1
			];
	}

	/**
	 * @inerhitDoc
	 */
	public function get_event_tracking_query_id() {
		return $this->results[0]['queryID'] ?? '';
	}

}
