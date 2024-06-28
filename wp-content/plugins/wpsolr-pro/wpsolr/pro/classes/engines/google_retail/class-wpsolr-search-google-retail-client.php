<?php

namespace wpsolr\pro\classes\engines\google_retail;

use Google\ApiCore\ValidationException;
use Google\Cloud\Retail\V2\CompleteQueryResponse\CompletionResult;
use Google\Cloud\Retail\V2\CompletionServiceClient;
use Google\Cloud\Retail\V2\Interval;
use Google\Cloud\Retail\V2\SearchRequest\FacetSpec;
use Google\Cloud\Retail\V2\SearchRequest\FacetSpec\FacetKey;
use Google\Cloud\Retail\V2\SearchServiceClient;
use wpsolr\core\classes\engines\WPSOLR_AbstractSearchClient;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\utilities\WPSOLR_Option;
use wpsolr\core\classes\utilities\WPSOLR_Regexp;
use wpsolr\core\classes\WpSolrSchema;
use wpsolr\pro\extensions\scoring\WPSOLR_Option_Scoring;

class WPSOLR_Search_Google_Retail_Client extends WPSOLR_AbstractSearchClient {
	use WPSOLR_Google_Retail_Client;

	const IS_LOG_QUERY_TIME_IMPLEMENTED = true;

	const _FIELD_NAME_FLAT_HIERARCHY = 'flat_hierarchy_'; // field contains hierarchy as a string with separator (filter)
	const _FIELD_NAME_NON_FLAT_HIERARCHY = 'non_flat_hierarchy_'; // field contains hierarchy as an array (facet)

	// Scripts in painless: https://www.elastic.co/guide/en/elasticsearch/reference/current/modules-scripting-painless-syntax.html
	const SCRIPT_LANGUAGE_PAINLESS = 'painless';
	const SCRIPT_PAINLESS_DISTANCE = 'doc[params.field].empty ? params.empty_value : doc[params.field].planeDistance(params.lat,params.lon)*0.001';

	const FIELD_SEARCH_AUTO_COMPLETE = 'autocomplete';
	const FIELD_SEARCH_SPELL = 'spell';
	const SUGGESTER_NAME = 'wpsolr_spellcheck';

	const QUERY_SUGGESTIONS_NOT_SUPPORTED = 'Auto-completion not supported yet.';

	/* @var array */
	protected static $fields_in_settings = [];

	/* @var array $query */
	protected $query;

	// https://www.elastic.co/guide/en/elasticsearch/reference/5.2/query-dsl-query-string-query.html
	/* @var array $query_string */
	protected $query_string;

	/* @var array $query_filters */
	protected $query_filters;

	/* @var array $query_post_filters */
	protected $query_post_filters;

	/* @var array $query_script_fields */
	protected $query_script_fields;

	/* @var array $facets_filters */
	protected $facets_filters;

	/* @var array $facets_ranges */
	protected $facets_ranges;

	/* @var array */
	protected $facets_fields;

	/* @var array $completion $facets_fields */
	protected $completion;

	/* @var bool $is_did_you_mean */
	protected $is_did_you_mean = false;

	/* @var bool $is_query_built */
	protected $is_query_built = false;

	/* @var string $boost_field_values */
	protected $boost_field_values;

	/* @var array $function_score */
	protected $function_score;

	/** @var int */
	protected $random_sort_seed = 0;

	/** @var array */
	protected $highlighting_fields;

	/** @var array */
	protected $source_fields;

	/** @var array */
	protected $search_parameters = [];

	/** @var array */
	protected $sorts;

	/**
	 * @var string[]
	 */
	protected $query_facets = [];

	/**
	 * @var string[]
	 */
	protected $index_facets = [];

	/**
	 * @var string[]
	 */
	protected $excluded_fields = [];

	/**
	 * @var array
	 */
	protected $queries = [];

	/**
	 * @var array
	 */
	protected $filters = [];

	/**
	 * @var string[]
	 */
	protected $filtered_fields = [];

	/**
	 * @var array
	 */
	protected $filters_bool = [];

	/**
	 * @var SearchServiceClient
	 */
	protected $search_engine_client;

	/**
	 * @var string
	 */
	protected string $visitor_id;

	/**
	 * Execute an update query with the client.
	 *
	 * @param SearchServiceClient $search_engine_client
	 *
	 * @return WPSOLR_Results_Google_Retail_Client
	 * @throws \Exception
	 */
	public function search_engine_client_execute( $search_engine_client, $random_score ) {

		//$the_query = isset( $this->completion ) ? $this->completion : $this->query;

		//throw new \Exception( $filters );

		$this->search_engine_client_build_query();

		//$index_name = $this->_get_replica_name_for_sort();

		// Update index facets discovered for current search
		//$results = $this->update_index_facets();

		try {
			$this->search_parameters['branch'] = $this->_get_formatted_branch();
			$this->visitor_id                  = $this->_get_session_visitor_id( true );
			$raw_results                       = $this->search_engine_client->search( $this->_get_formatted_serving_config(),
				$this->visitor_id, $this->search_parameters );

		} catch ( \Exception $e ) {

			// Propagate other errors
			throw $e;
		}

		$results = new WPSOLR_Results_Google_Retail_Client( $raw_results );

		return $results;
	}

	/**
	 * Build the query.
	 *
	 */
	public function search_engine_client_build_query() {

		$queries = [];

		/**
		 * Fix the filters syntax
		 **/
		if ( ! empty( $filters_str = $this->_fix_filters_syntax( $this->filters ) ) ) {
			$this->search_parameters['filter'] = str_replace( WpSolrSchema::FACET_HIERARCHY_SEPARATOR, static::get_facet_hierarchy_separator(), $filters_str );
		}

		$this->search_parameters['query'] = $this->query_string['query'] ?? '';

		//$queries[] = $query;

		// Add excluded facets in a secondary excluded query
		/*foreach ( $this->excluded_fields as $filter_name => $facet_name ) {
			if ( ! empty( $excluded_query = $this->create_excluded_query( $facet_name, $filter_name ) ) ) {
				$queries[] = $excluded_query;
			}
		}*/

		// Set query facets
		foreach ( $this->facets_fields ?? [] as $field_name => $facet ) {
			$facet_key_array                  = $facet['facet_key'] ?? [];
			$facet_key_array['key']           = $this->_get_formatted_attribute_name( $field_name );
			$facet_key_array['order_by']      = $facet_key_array['order_by'] ?? 'count desc';
			$facet['facet_spec']['facet_key'] = new FacetKey( $facet_key_array );

			$this->search_parameters['facetSpecs'][] = new FacetSpec( $facet['facet_spec'] );
		}

		// Sort: https://cloud.google.com/retail/docs/filter-and-order#order
		$sort_by = [];
		foreach ( $this->sorts ?? [] as $field_name => $sort ) {
			switch ( $sort ) {
				case static::SORT_DESC:
					$sort_by[] = sprintf( '%s %s', $this->_get_formatted_attribute_name( $field_name ), $sort );
					break;

				default:
					$sort_by[] = $this->_get_formatted_attribute_name( $field_name );
					break;
			}
		}
		if ( ! empty( $sort_by ) ) {
			$this->search_parameters['orderBy'] = implode( ', ', $sort_by );
		}

		// Set Query expansion
		//$this->search_parameters['queryExpansionSpec'] = new QueryExpansionSpec( [ 'condition' => Condition::AUTO ] );

		// Spell correction
		//$this->search_parameters['spellCorrectionSpec'] = new SpellCorrectionSpec( [ 'mode' => Mode::AUTO ] );
	}

	/**
	 * Does index exists ?
	 *
	 * @param $is_throw_error
	 *
	 * @return bool
	 * @throws \Exception
	 */
	protected function admin_is_index_exists( $is_throw_error = false ) {

		// this methods throws an error if index is not responding.

		try {

			// No ping available : use a query on products.
			$this->visitor_id = $this->_get_session_visitor_id( true );
			$results          = $this->search_engine_client->search( $this->_get_formatted_serving_config(), $this->visitor_id, [ 'pageSize' => 1 ] );

		} catch ( \Exception $e ) {
			// Send error without using $is_throw_error, as if the index is not existing it cannot be created with the API
			throw new \Exception( $e->getBasicMessage() );
		}

		// No exception: index exists
		return true;
	}

	/**
	 * @param array|string $data
	 *
	 * @throws \Exception
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
	 * Create a query select.
	 *
	 * @return  array
	 */
	public function search_engine_client_create_query_select() {

		$this->query = [];

		$this->query_string = [];

		return $this->query;
	}

	/**
	 * @inheritDoc
	 *
	 * From \Elastica\Util::escapeTerm
	 *
	 */
	public
	function search_engine_client_escape_keywords(
		$keywords
	) {

		// Simple quotes are escaped by WP: unescape them
		$result = str_replace( "\'", "'", $keywords );

		return $result;
	}

	/**
	 * Set keywords of a query select.
	 *
	 * @param $keywords
	 *
	 * @return string
	 */
	public function search_engine_client_set_query_keywords( $keywords ) {

		$keywords = WPSOLR_Regexp::extract_parenthesis( $keywords );

		$this->query_string['query'] = ( '*' === $keywords ) ? '' : $keywords;
	}

	/**
	 * @inheritDoc
	 */
	public function search_engine_client_set_default_operator( $operator = 'AND' ) {
		// No equivallent parameter apparently
	}

	/**
	 * @inheritDoc
	 */
	public function search_engine_client_set_start( $start ) {
		$this->search_parameters['offset'] = $start;
	}

	/**
	 * @inheritDoc
	 */
	public function search_engine_client_set_rows( $rows ) {
		$this->search_parameters['pageSize'] = $rows;
	}

	/**
	 * @inheritdoc
	 */
	public function search_engine_client_add_sort( $sort, $sort_by, $args = [] ) {
		if ( empty( $this->sorts[ $sort ] ) ) {
			$this->sorts[ $sort ] = $sort_by;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function search_engine_client_set_sort( $sort, $sort_by ) {
		$this->sorts = [];
		$this->search_engine_client_add_sort( $sort, $sort_by );
	}

	/**
	 * @inheritDoc
	 */
	public function search_engine_client_add_filter_term( $filter_name, $field_name, $facet_is_or, $field_value, $filter_tag = '' ) {

		$term = $this->search_engine_client_create_filter_in_terms( $field_name, [ $field_value ] );

		$this->search_engine_client_add_filter_any( $filter_name, $field_name, $facet_is_or, $term, $filter_tag );
	}

	/**
	 * Add a negative filter on terms.
	 *
	 * @param string $filter_name
	 * @param string $field_name
	 * @param array $field_values
	 *
	 * @param string $filter_tag
	 *
	 */
	public
	function search_engine_client_add_filter_not_in_terms(
		$filter_name, $field_name, $field_values, $filter_tag = ''
	) {

		$terms = $this->search_engine_client_create_filter_in_terms( $field_name, $field_values );

		$this->_add_filter_query( $field_name, $this->search_engine_client_create_not( $terms ) );
	}

	/**
	 * @param string $field_name
	 * @param string $query
	 *
	 * @return string
	 */
	protected function _add_filter_query( $field_name, $query ) {

		if ( ! is_null( $field_name ) ) {

			if ( ! in_array( $field_name, $this->filtered_fields ) ) {
				$this->filtered_fields[] = $field_name;
			}

			// Main query contains all fields, excluded or not
			$this->filters[ $field_name ] = sprintf( ' %s', $query );
		}

		return $query;
	}

	/**
	 * @inheritdoc
	 */
	public
	function search_engine_client_add_filter_not_in_terms_of_other_sites(
		$filter_name, $field_name, $field_values, $site_id
	) {

		$terms_not     = $this->search_engine_client_create_filter_not_in_terms( $field_name, $field_values );
		$terms_site_id = $this->search_engine_client_create_filter_in_terms( WpSolrSchema::_FIELD_NAME_BLOG_NAME_STR, [ $site_id ] );

		// not terms OR site_id
		$this->_add_filter_query( $field_name, $this->search_engine_client_create_or( [
			$terms_not,
			$terms_site_id
		] ) );
	}

	/**
	 * @inheritDoc
	 */
	public function search_engine_client_add_filter_in_terms( $filter_name, $field_name, $field_values, $filter_tag = '' ) {

		$this->_add_filter_query( $field_name, $this->search_engine_client_create_filter_in_terms( $field_name, $field_values ) );
	}

	/**
	 * @inherit
	 *
	 * https://www.google_retail.com/doc/api-reference/api-parameters/filters/
	 *
	 * @return string
	 */
	public function search_engine_client_create_filter_in_terms( $field_name, $field_values ) {

		return $this->_create_filter_terms( $field_name, $field_values, 'OR' );
	}

	/**
	 *
	 * @param string $field_name
	 * @param array $field_values
	 * @param string $operator 'OR', 'AND'
	 *
	 * @return string
	 */
	protected function _create_filter_terms( $field_name, $field_values, $operator ) {

		if ( empty( $field_values ) ) {
			return '';
		}

		$is_numeric = WpSolrSchema::get_custom_field_is_numeric_type( $field_name );

		$results = [];
		foreach ( $field_values as $field_value ) {
			/*
			 * https://cloud.google.com/retail/docs/filter-and-order#filter
			 * ( textual_field, ":", "ANY", "(", literal, { ",", literal }, ")"
			 * NOT categories: ANY(\"Pixel > featured accessories\")
			 * (categories: ANY(\"Nest > speakers and displays\")) AND (price: IN(80.0i, *))
			 */
			$results[] = sprintf( $is_numeric ? '(%s = %s)' : '(%s: ANY("%s"))', $this->_get_formatted_attribute_name( $field_name ), $this->_get_escaped_query( $field_value ) );
		}

		return sprintf( '(%s)', implode( sprintf( ' %s ', $operator ), $results ) );
	}

	/**
	 * @inheritdoc
	 *
	 * @return string
	 */
	public function search_engine_client_create_filter_wildcard( $field_name, $field_value ) {
		// Not used. The "LIKE" search filter is not supported by Google_Retail.
		return '';
	}

	/**
	 * @inheritdoc
	 *
	 * @return string
	 */
	public function search_engine_client_create_filter_wildcard_not( $field_name, $field_value ) {
		// Not used. The "NOT LIKE" search filter is not supported by Google_Retail.
		return '';
	}

	/**
	 * @inheritdoc
	 *
	 */
	public function search_engine_client_add_filter_in_all_terms( $filter_name, $field_name, $field_values, $filter_tag = '' ) {

		$this->_add_filter_query( $field_name, $this->search_engine_client_create_filter_in_all_terms( $field_name, $field_values ) );
	}

	/**
	 * @inheritdoc
	 */
	public function search_engine_client_create_filter_in_all_terms( $field_name, $field_values ) {

		return $this->_create_filter_terms( $field_name, $field_values, 'AND' );
	}


	/**
	 * @inheritdoc
	 *
	 * @return string
	 */
	public function search_engine_client_create_filter_not_in_terms( $field_name, $field_values ) {

		return $this->search_engine_client_create_not( $this->search_engine_client_create_filter_in_terms( $field_name, $field_values ) );
	}

	/**
	 * @inheritdoc
	 *
	 * @return array
	 */
	public function search_engine_client_create_filter_lt( $field_name, $field_values ) {

		return $this->_create_filter_range_terms( $field_name, $field_values, '<' );
	}

	/**
	 * @inheritdoc
	 *
	 * @return array
	 */
	public function search_engine_client_create_filter_lte( $field_name, $field_values ) {

		return $this->_create_filter_range_terms( $field_name, $field_values, '<=' );
	}

	/**
	 * @inheritdoc
	 *
	 * @return array
	 */
	public function search_engine_client_create_filter_gt( $field_name, $field_values ) {

		return $this->_create_filter_range_terms( $field_name, $field_values, '>' );
	}

	/**
	 * @inheritdoc
	 *
	 * @return array
	 */
	public function search_engine_client_create_filter_gte( $field_name, $field_values ) {

		return $this->_create_filter_range_terms( $field_name, $field_values, '>=' );
	}

	/**
	 * @inheritdoc
	 *
	 * @return string
	 */
	public function search_engine_client_create_filter_between( $field_name, $field_values ) {

		return $this->_create_filter_range( '<=', sprintf( 'between %s', $field_name ), $field_name, false, $field_values[0], $field_values[1], false );
	}

	/**
	 * @inheritdoc
	 *
	 * @return string
	 */
	public function search_engine_client_create_filter_not_between( $field_name, $field_values ) {

		return $this->search_engine_client_create_not(
			$this->search_engine_client_create_filter_between( $field_name, $field_values )
		);
	}

	/**
	 *
	 * @param string $field_name
	 * @param array $field_values
	 * @param string $operator
	 *
	 * @return array
	 */
	protected function _create_filter_range_terms( $field_name, $field_values, $operator ) {

		$results = [];

		foreach ( $field_values as $field_value ) {
			$results[] = sprintf( '(%s %s %s)', $field_name, $operator, $field_value );
		}

		return $this->search_engine_client_create_and( $results );
	}

	/**
	 * Create a 'only numbers' filter.
	 *
	 * @param string $field_name
	 *
	 * @return array
	 */
	public function search_engine_client_create_filter_only_numbers( $field_name ) {
		return $this->search_engine_client_create_not( [ 'regexp' => [ $field_name => '[^0-9]*' ] ] );
	}

	/**
	 * Create a 'empty or absent' filter.
	 *
	 * @param string $field_name
	 *
	 * @return string
	 */
	public function search_engine_client_create_filter_no_values( $field_name ) {
		// Not supported by Google_Retail: https://www.google_retail.com/doc/guides/managing-results/refine-results/filtering/how-to/filter-by-null-or-missing-attributes/
		return '';
	}

	/**
	 * @inheritDoc
	 *
	 * @param array $queries
	 *
	 * @return string
	 */
	public function search_engine_client_create_or( $queries ) {

		$queries = $this->_remove_empty_queries( $queries );

		return sprintf( count( $queries ) > 1 ? '(%s)' : '%s', implode( ' OR ', $queries ) );
	}

	/**
	 * Remove empty queries
	 *
	 * @param string[] $queries
	 *
	 * @return string[]
	 */
	public function _remove_empty_queries( $queries ) {

		$results = [];

		foreach ( $queries as $query ) {
			if ( ! empty( trim( $query ) ) ) {
				$results[] = $query;
			}
		}

		return $results;
	}


	/**
	 * @inheritdoc
	 *
	 * @param string $query
	 *
	 * @return string
	 */
	public function search_engine_client_create_not( $query ) {
		// Google retail forbids NOT (A AND B), just (NOT A OR NOT B)

		$query_transformed = WPSOLR_Regexp::extract_parenthesis( $query );
		$query_transformed = str_replace( ' OR ', ' __OR__ ', $query_transformed );
		$query_transformed = str_replace( ' AND ', ' OR NOT ', $query_transformed );
		$query_transformed = str_replace( ' __OR__ ', ' AND NOT ', $query_transformed );

		$query_transformed = sprintf( '(NOT %s)', $query_transformed );

		return str_replace( 'NOT NOT', '', $query_transformed );
	}

	/**
	 * @inheritDoc
	 */
	public function search_engine_client_add_filter( $filter_name, $filter ) {
		$this->_add_filter_query( $filter_name, $filter );
	}

	/**
	 * Create a 'AND' from filters.
	 *
	 * @param array $queries
	 *
	 * @return string
	 */
	public function search_engine_client_create_and( $queries ) {

		$queries = $this->_remove_empty_queries( $queries );

		return sprintf( count( $queries ) > 1 ? '(%s)' : '%s', implode( ' AND ', $queries ) );
	}

	/**
	 * Add a filter on: empty or in terms.
	 *
	 * @param string $filter_name
	 * @param string $field_name
	 * @param array $field_values
	 * @param string $filter_tag
	 *
	 */
	public function search_engine_client_add_filter_empty_or_in_terms( $filter_name, $field_name, $field_values, $filter_tag = '' ) {

		// 'IN' terms
		$in_terms = $this->search_engine_client_create_filter_in_terms( $field_name, $field_values );

		// 'empty': not exists
		$empty = $this->search_engine_client_create_not( $this->search_engine_client_create_filter_exists( $field_name ) );

		// 'empty' OR 'IN'
		$this->_add_filter_query(
			$field_name, $this->search_engine_client_create_or(
			[
				$empty,
				$in_terms
			]
		)
		);
	}

	/**
	 * @inheritdoc
	 */
	public function search_engine_client_add_filter_exists( $filter_name, $field_name ) {

		// Add 'exists'
		// does not work $this->_add_filter_query( $field_name, $this->search_engine_client_create_filter_exists( $field_name ) );
	}

	/**
	 * @inheritdoc
	 */
	public function search_engine_client_create_filter_exists( $field_name ) {

		return $this->search_engine_client_create_filter_not_in_terms( $field_name, [ static::FIELD_VALUE_UNDEFINED ] );
	}

	/**
	 * Set highlighting.
	 *
	 * @param string[] $field_names
	 * @param string $prefix
	 * @param string $postfix
	 * @param int $fragment_size
	 */
	public
	function search_engine_client_set_highlighting(
		$field_names, $prefix, $postfix, $fragment_size
	) {

		// https://www.google_retail.com/doc/api-reference/api-parameters/attributesToHighlight/

		$this->search_parameters['attributesToHighlight'] = []; // Must be set empty if no highlighting, or else default <em> is used

		if ( ! empty( $field_names ) && ! empty( $prefix ) && ! empty( $postfix ) && ! empty( $fragment_size ) ) {

			$this->search_parameters['attributesToHighlight'] = $field_names;
			$this->search_parameters['highlightPreTag']       = $prefix;
			$this->search_parameters['highlightPostTag']      = $postfix;

			$field_snippets = [];
			foreach ( $field_names as $field_name ) {
				$field_snippets[] = sprintf( '%s:%s', $field_name, $fragment_size );
			}
			$this->search_parameters['attributesToHighlight'] = $field_snippets;
		}

	}

	/**
	 * @inheritDoc
	 *
	 * https://www.google_retail.com/doc/api-reference/api-parameters/facets/
	 */
	protected
	function &get_or_create_facets_field(
		$facet_name
	) {

		//toto$facet_name = $this->_convert_field_name_if_date( $facet_name );
		$this->add_attribute_for_faceting( $facet_name );

		return $facet_name;
	}


	/**
	 * @inheritDoc
	 */
	public
	function search_engine_client_set_facets_min_count(
		$facet_name, $min_count
	) {
		// Not supported
	}

	/**
	 * Create a facet field.
	 *
	 * @param $facet_name
	 * @param $field_name
	 *
	 * @return void
	 * @internal param $exclusion
	 */
	public
	function search_engine_client_add_facet_field(
		$facet_name, $field_name
	) {

		//$this->get_or_create_facets_field( $field_name );
	}

	/**
	 * Set facets limit.
	 *
	 * @param $facet_name
	 * @param int $limit
	 */
	public
	function search_engine_client_set_facets_limit(
		$facet_name, $limit
	) {
		$this->facets_fields[ $facet_name ]['facet_spec']['limit'] = $limit;
	}

	/**
	 * @inheritDoc
	 */
	public
	function search_engine_client_set_facet_sort_alphabetical(
		$facet_name
	) {
		$this->facets_fields[ $facet_name ]['facet_key']['order_by'] = ''; // "Natural" order
	}

	/**
	 * Set facet field excludes.
	 *
	 * @param string $facet_name
	 * @param string $exclude
	 */
	public
	function search_engine_client_set_facet_excludes(
		$facet_name, $exclude
	) {
		$this->facets_fields[ $facet_name ]['facet_spec']['excluded_filter_keys'][] = $this->_get_formatted_attribute_name( $exclude );
	}

	/**
	 * @inheritDoc
	 */
	public function search_engine_client_set_fields( $fields ) {
		// Retrievable is a product attribute, not a search parameter
	}

	/**
	 * Get suggestions from the engine.
	 *
	 * @inheritdoc
	 *
	 * @return WPSOLR_Results_Google_Retail_Client
	 * @throws \Exception
	 */
	public function search_engine_client_get_suggestions_keywords( $suggestion, $query, $contexts, $is_error = false ) {

		$completion_client = new CompletionServiceClient( $this->_get_credentials( $this->config ) );

		$this->visitor_id = $this->_get_session_visitor_id( true );
		$options          = [ 'visitorId' => $this->visitor_id, 'dataset' => 'cloud-retail', ];
		$raw_results      = $completion_client->completeQuery( $this->_get_formatted_default_catalog(), $query, $options );

		$results = [];
		/** @var CompletionResult $completion_result */
		foreach ( $raw_results->getCompletionResults() as $completion_result ) {
			$results[] = $completion_result->getSuggestion();
		}


		throw new \Exception( static::QUERY_SUGGESTIONS_NOT_SUPPORTED );
	}


	/**
	 * Get suggestions for did you mean.
	 *
	 * @param string $keywords
	 *
	 * @return string Did you mean keyword
	 */
	public function search_engine_client_get_did_you_mean_suggestions( $keywords ) {

		$this->is_did_you_mean = true;

		$results = $this->search_engine_client_execute( $this->search_engine_client, null );

		$suggestions = $results->get_suggestions();

		return ! empty( $suggestions ) ? $suggestions[0]['text'] : '';
	}


	/**
	 * https://www.google_retail.com/doc/guides/managing-results/relevance-overview/in-depth/ranking-criteria/#geo-if-applicable
	 *
	 * @inheritDoc
	 */
	public function search_engine_client_add_sort_geolocation_distance( $field_name, $geo_latitude, $geo_longitude ) {
		// Automatically sorted with geo distance if geo filter is used. Nothing to do here.
		$t = 1;
	}

	/**
	 * Generate a distance script for a field, and name the query
	 *
	 * @param $field_prefix
	 * @param $field_name
	 * @param $geo_latitude
	 * @param $geo_longitude
	 *
	 * @return string
	 *
	 */
	public function get_named_geodistance_query_for_field( $field_prefix, $field_name, $geo_latitude, $geo_longitude ) {
		// Google_Retail does not provide a distance
		return null;
	}

	/**
	 * Replace default query field by query fields, with their eventual boost.
	 *
	 * @param array $query_fields
	 */
	public function search_engine_client_set_query_fields( array $query_fields ) {
		$this->query_string['fields'] = $query_fields;
	}

	/**
	 * Set boosts field values.
	 *
	 * @param array $boost_field_values
	 */
	public function search_engine_client_set_boost_field_values( $boost_field_values ) {
		// Store it. Will be added to the query later.

		// Add 'OR' condition, else empty results if boost value is not found.
		$this->boost_field_values = sprintf( ' OR (%s) ', $boost_field_values );
	}


	/**
	 * Get facet terms.
	 *
	 * @param string $facet_name
	 * @param int $range_start
	 * @param int $range_end
	 * @param int $range_gap
	 *
	 * @return array
	 */
	protected
	function get_or_create_facets_range(
		$facet_name, $range_start, $range_end, $range_gap
	) {

		// Not found. Create the facet.
		$intervals = [];

		// Add a range for values before start
		$intervals[] = ( new Interval() )->setExclusiveMaximum( (float) $range_start );

		// No gap parameter. We build the ranges manually.
		foreach ( range( $range_start, $range_end, $range_gap ) as $start ) {
			if ( count( $intervals ) >= 29 ) {
				// Google retail supports 30 intervals maximum: stop now!
				break;
			}

			if ( $start < $range_end ) {
				$intervals[] = ( new Interval() )->setMinimum( (float) $start )->setExclusiveMaximum( (float) ( $start + $range_gap ) );
			}
		}

		// Add a range for values after end
		$intervals[] = ( new Interval() )->setMinimum( (float) $range_end );

		$this->facets_fields[ $facet_name ]['facet_key']['intervals'] = $intervals;

		//$this->facets_fields[ $facet_name ]['facet_spec']['excluded_filter_keys'][] = $this->_get_formatted_attribute_name( $facet_name );

		return [];
	}

	/**
	 * Create a facet range regular.
	 *
	 * @param $facet_name
	 * @param $field_name
	 *
	 * @param string $range_start
	 * @param string $range_end
	 * @param string $range_gap
	 */
	public function search_engine_client_add_facet_range_regular( $facet_name, $field_name, $range_start, $range_end, $range_gap ) {

		$this->get_or_create_facets_range( $field_name, $range_start, $range_end, $range_gap );
	}

	/**
	 * Get facet grouped by.
	 *
	 * @param string $facet_name
	 * @param int $size
	 *
	 * @return array
	 * @link https://www.google_retail.com/doc/api-reference/api-parameters/distinct/
	 *
	 */
	protected
	function get_or_create_distinct(
		$facet_name, $size
	) {
		// $facet_name is already selected at indexing time in the index settings
		$this->search_parameters['distinct'] = $size;
	}

	/**
	 * @@inheritdoc
	 */
	public function search_engine_client_add_facet_top_hits( $facet_name, $size ) {

		$this->get_or_create_distinct( $facet_name, $size );
	}

	/**
	 * Add a filter.
	 *
	 * @param string $filter_name
	 * @param string $field_name
	 * @param bool $facet_is_or
	 * @param string[] $filter
	 * @param string $filter_tag
	 */
	public function search_engine_client_add_filter_any( $filter_name, $field_name, $facet_is_or, $filter, $filter_tag = '' ) {

		if ( ! isset( $this->filters[ $field_name ] ) ) {
			$this->filters[ $field_name ] = [];
		}

		if ( ! is_array( $this->filters[ $field_name ] ) ) {
			// Copy string value in array
			$old_value                                                       = $this->filters[ $field_name ];
			$this->filters[ $field_name ]                                    = [];
			$this->filters[ $field_name ][ $facet_is_or ? ' OR ' : ' AND ' ] = [ $old_value ];
		}

		$this->filters[ $field_name ][ $facet_is_or ? ' OR ' : ' AND ' ][] = $filter;
	}

	/**
	 * @inheritdoc
	 */
	public function search_engine_client_add_filter_range_upper_strict( $filter_name, $field_name, $facet_is_or, $range_start, $range_end, $is_date, $filter_tag = '' ) {

		if ( $range_start === $range_end ) {

			$this->_add_filter_range( '<=', $filter_name, $field_name, $facet_is_or, $range_start, $range_end, $is_date, $filter_tag = '' );

		} else {

			$this->_add_filter_range( '<', $filter_name, $field_name, $facet_is_or, $range_start, $range_end, $is_date, $filter_tag = '' );
		}
	}

	/**
	 * @inheritdoc
	 */
	public function search_engine_client_add_filter_range_upper_included( $filter_name, $field_name, $facet_is_or, $range_start, $range_end, $is_date, $filter_tag = '' ) {

		$this->_add_filter_range( '<=', $filter_name, $field_name, $facet_is_or, $range_start, $range_end, $is_date, $filter_tag = '' );
	}

	/**
	 *
	 */
	public function _add_filter_range( $upper_operation, $filter_name, $field_name, $facet_is_or, $range_start, $range_end, $is_date, $filter_tag = '' ) {

		$range = $this->_create_filter_range( $upper_operation, $filter_name, $field_name, $facet_is_or, $range_start, $range_end, $is_date, $filter_tag = '' );

		$this->search_engine_client_add_filter_any( $filter_name,
			$field_name,
			$facet_is_or,
			$range,
			$filter_tag );

	}

	/**
	 * https://cloud.google.com/retail/docs/filter-and-order#filter
	 */
	public function _create_filter_range( $upper_operation, $filter_name, $field_name, $facet_is_or, $range_start, $range_end, $is_date, $filter_tag = '' ) {

		$field_name = $this->_convert_field_name_if_date( $field_name );
		$field_name = $this->_get_formatted_attribute_name( $field_name );

		$range_start = $this->_convert_to_unix_time_if_date( $range_start );
		$range_end   = $this->_convert_to_unix_time_if_date( $range_end );

		$range_values = [];
		if ( ( '*' !== $range_start ) && ( '*' !== $range_end ) ) {

			if ( $range_start != $range_end ) {
				$range_values[] = sprintf( '%s: IN(%s%s, %s%s)',
					$field_name, $range_start, 'i', $range_end, ( '<' === $upper_operation ) ? 'e' : 'i' );
			} else {
				$range_values[] = sprintf( '%s = %s', $field_name, $range_start );
			}

		} elseif ( '*' !== $range_start ) {

			$range_values[] = sprintf( '%s >= %s', $field_name, $range_start );

		} elseif ( '*' !== $range_end ) {
			$range_values[] = sprintf( '%s %s %s', $field_name, $upper_operation, $range_end );
		}

		$range = sprintf( '(%s)', implode( $facet_is_or ? ' OR ' : ' AND ', $range_values ) );

		return $range;
	}

	/**
	 * Add decay functions to the search query
	 *
	 * @param array $decays
	 *
	 */
	public function search_engine_client_add_decay_functions( array $decays ) {

		if ( empty( $decays ) ) {
			// Nothing to do
			return;
		}

		if ( is_null( $this->function_score ) ) {
			$this->function_score = [];
		}

		foreach ( $decays as $decay_def ) {

			$origin = $decay_def['origin'];
			if ( WPSOLR_Option::OPTION_SCORING_DECAY_ORIGIN_DATE_NOW === $decay_def['origin'] ) {
				$origin = 'now';
			}

			switch ( $decay_def['unit'] ) {
				case WPSOLR_Option_Scoring::DECAY_DATE_UNIT_DAY:
					$unit = 'd';
					break;

				case WPSOLR_Option_Scoring::DECAY_DATE_UNIT_KM:
					$unit = 'km';
					break;

				case WPSOLR_Option_Scoring::DECAY_DATE_UNIT_NONE:
					$unit = '';
					break;

				default:
					throw new \Exception( sprintf( 'Unit %s not recognized for field %s.', $decay_def['unit'], $decay_def['field'] ) );
					break;
			}

			$this->function_score['function_score']['functions'][] = [
				$decay_def['function'] =>
					[
						$decay_def['field'] => // displaydate_dt
							[
								'origin' => $origin, // 'now', '0', 'lat,long'
								'scale'  => sprintf( '%s%s', $decay_def['scale'], $unit ), // '10d', '10', '10km'
								'offset' => sprintf( '%s%s', $decay_def['offset'], $unit ), // '2d', '2', '2km'
								'decay'  => $decay_def['decay'], // '0.5'
							]
					]
			];

		}
	}

	/**
	 * Add a geo distance filter.
	 *
	 * @param $field_name
	 * @param $geo_latitude
	 * @param $geo_longitude
	 *
	 */
	public function search_engine_client_add_filter_geolocation_distance( $field_name, $geo_latitude, $geo_longitude, $distance ) {

		// https://www.google_retail.com/doc/guides/managing-results/refine-results/geolocation/how-to/filter-results-around-a-location/
		// https://www.google_retail.com/doc/api-reference/api-parameters/aroundRadius/

		$this->search_parameters['aroundRadius'] = empty( $distance ) ? 'All' : 1000 * $distance; // convert distance in meters
		$this->search_parameters['aroundLatLng'] = sprintf( '%s, %s', $geo_latitude, $geo_longitude );
	}

	/**
	 * Create a facet stats.
	 *
	 * @param string $facet_name
	 * @param string $exclude
	 */
	public function search_engine_client_add_facet_stats( $facet_name, $exclude ) {
		$this->facets_fields[ $facet_name ]['facet_key']['return_min_max'] = true; // "Natural" order
		$this->facets_fields[ $facet_name ]['facet_key']['intervals']      = [ ( new Interval() ) ];
	}


	/**
	 * Build the outer aggs from its inner content
	 *
	 * @param array $inner_aggs
	 *
	 * @return array
	 */
	protected function _create_outer_aggs( $inner_aggs ) {
		return [
			'filter' => [
				'match_all' => new \stdClass(),
			],
			'aggs'   => $inner_aggs,
		];
	}

	/**
	 * @inheritDoc
	 */
	protected function property_exists( $document, $field_name ) {
		return property_exists( $document, $field_name );
	}

	/**
	 * Add field as a 'attributesForFaceting' settings
	 * https://www.google_retail.com/doc/api-reference/api-parameters/attributesForFaceting/
	 *
	 * @param $field_name
	 */
	protected function add_attribute_for_faceting( $field_name ) {

		if ( ! in_array( $field_name, $this->query_facets ) ) {
			// For the query
			$this->query_facets[] = $field_name;

			if ( ! in_array( $field_name, $this->_get_index_fields_in_settings() ) ) {
				// For the index settings
				$this->index_facets[] = $field_name;
			}
		}
	}

	/**
	 * UPdate the index facets settings
	 */
	protected function update_index_facets() {

		if ( ! empty( $this->index_facets ) ) {
			// Fields not already declared as facets in the master index

			// Update the facets on the index settings (and all its replicas)
			$fields_for_settings = array_values( array_unique(
				array_merge( $this->_get_index_fields_in_settings(), $this->index_facets ) ) ); // union with deduplicate

			$this->get_search_index()->setSettings(
				[
					'attributesForFaceting' => $fields_for_settings,
				],
				[
					'forwardToReplicas' => true,
				] )->wait();

			// Remember facets
			$fields_in_settings[ $this->get_search_index()->getIndexName() ] = $fields_for_settings;
			WPSOLR_Service_Container::getOption()->set_option_index_filtered_fields( $fields_in_settings );
		}

	}

	/**
	 * Get the fields already set on the index settings
	 *
	 * @return array
	 */
	protected function _get_index_fields_in_settings() {

		$index_name = $this->get_search_index()->getIndexName();

		// In cache?
		if ( isset( static::$fields_in_settings[ $index_name ] ) ) {
			return static::$fields_in_settings[ $index_name ];
		}

		// Retrieve it and put it in cache
		$fields_in_settings = WPSOLR_Service_Container::getOption()->get_option_index_filtered_fields();

		static::$fields_in_settings[ $index_name ] = empty( $fields_in_settings[ $index_name ] ) ? [] : $fields_in_settings[ $index_name ];

		return static::$fields_in_settings[ $index_name ];
	}

	/**
	 *
	 * Add a disjontive query to exclude facets
	 *
	 * @param string $facet_name
	 * @param string $filter_name
	 *
	 * @return array
	 */
	protected function create_excluded_query( $facet_name, $filter_name ) {

		if ( ! isset( $this->filters[ $filter_name ] ) ) {
			// The filter is not excluded: do not create an exclusion query
			return [];
		}

		// Add all filtered field as filterOnly facets
		// Only excluded fields are normal facets and returned in results
		$facets = [];
		foreach ( $this->filtered_fields as $filtered_field ) {
			$facets[] = ( $filter_name === $filtered_field ) ? $facet_name : sprintf( 'filterOnly(%s)', $filtered_field );
		}

		$result = [
			'facets'                => $facets,

			// Those parameters are set to minimize the work of the engine. We don't
			// care about the results, we only care about the list of facets.
			'hitsPerPage'           => 0,
			'page'                  => 0,
			'attributesToRetrieve'  => [],
			'attributesToHighlight' => [],
			'attributesToSnippet'   => [],
			'analytics'             => false,
			'clickAnalytics'        => false,
		];

		// Remove the filter
		if ( ! empty( $filters_excluded_str = $this->_fix_filters_syntax( $this->filters, $filter_name ) ) ) {
			$result['filters'] = $filters_excluded_str;
		}

		return $result;
	}

	/**
	 * Fix the filters syntax
	 *
	 * @param string $filter_excluded_name
	 * @param array $filters
	 *
	 * @return string
	 */
	protected function _fix_filters_syntax( $filters, $filter_excluded_name = '' ) {

		$filters_str = '';

		// Explode the filters, but remove the eventual excluded filter query
		foreach ( $filters as $filter_name => $filter_query ) {
			if ( $filter_name !== $filter_excluded_name ) {
				if ( is_array( $filter_query ) ) {
					$filters_str .= sprintf( '(%s)', implode( key( $filter_query ), $filter_query[ key( $filter_query ) ] ) );
				} else {
					$filters_str .= $filter_query;
				}
			}
		}

		$filters_str = str_replace( ') (', ')(', $filters_str );
		$filters_str = str_replace( ')(', ') AND (', $filters_str );

		return trim( $filters_str );
	}

	/**
	 * Generate boolean filters
	 *
	 * @param string $filters_str
	 *
	 * @return string
	 */
	protected function _generate_bool_filters( $filters_str ) {

		foreach ( $this->filters_bool as $field_name => $filter_bool ) {
			$filter_bool_query_str = '';
			foreach ( $filter_bool as $bool => $bool_filters ) {
				$filter_bool_query_str .= sprintf( 'AND (%s)', implode( $bool, $bool_filters ) );
			}

			if ( ! empty( $filter_bool_query_str ) ) {
				$filters_str .= $filter_bool_query_str;

				// Add to the filtered query
				$this->_add_filter_query( $field_name, $filter_bool_query_str );
			}

		}

		return $filters_str;
	}

	/**
	 * @inheritDoc
	 */
	protected function resort_numeric_by_alphabetical_order( $facet_name, array $facet_values ) {
		if ( ! empty( $facet_values ) && $this->is_facet_sorted_alphabetically( $facet_name )
		     && WpSolrSchema::get_custom_field_is_numeric_type( $facet_name ) ) {
			ksort( $facet_values );
		}

		return $facet_values;
	}

	/**
	 * @inheritDoc
	 */
	protected function _log_query_as_string() {
		return wp_json_encode( [
			'visitor_id' => $this->visitor_id,
			'options'    => $this->search_parameters,
		], JSON_PRETTY_PRINT );
	}

	/**
	 *
	 * @param WPSOLR_Results_Google_Retail_Client $results
	 *
	 * @return string
	 * @throws \Exception
	 */
	protected function _log_results_as_string( $results ) {
		return $results->get_raw_results();
	}

	/**
	 * @inerhitDoc
	 */
	public
	function search_engine_client_event_tracking_set_is_return_query_id(
		$is_return_query_id
	) {
		// https://www.google_retail.com/doc/api-reference/api-parameters/clickAnalytics/
		$this->search_parameters['clickAnalytics'] = $is_return_query_id;
	}

	/**
	 * @inerhitDoc
	 */
	public
	function search_engine_client_event_tracking_set_user_is_personalization(
		$is_personalization
	) {
		// https://www.google_retail.com/doc/api-reference/api-parameters/enablePersonalization/
		$this->search_parameters['enablePersonalization'] = $is_personalization;

		// https://www.google_retail.com/doc/api-reference/api-parameters/personalizationImpact/
		//$this->search_parameters['personalizationImpact'] = 20;

		// https://www.google_retail.com/doc/api-reference/api-parameters/analyticsTags/
		//$this->search_parameters['analyticsTags'] = [
		//    'front_end',
		//    'website2'
		//  ];

		// https://www.google_retail.com/doc/api-reference/api-parameters/enableReRanking/
	}

	/**
	 * @inerhitDoc
	 */
	public function search_engine_client_event_tracking_set_user_token( $user_token ) {
		// https://www.google_retail.com/doc/api-reference/api-parameters/userToken/
		$this->search_parameters['userToken'] = $user_token;
	}

	/**
	 * @param array $config
	 *
	 * @return SearchServiceClient
	 * @throws ValidationException
	 */
	protected function _get_client( array $config ): SearchServiceClient {
		return new SearchServiceClient( $this->_get_credentials( $config ) );
	}

	protected function _get_escaped_query( $field_value ) {
		return str_replace( '"', '\"', $field_value );
	}

}