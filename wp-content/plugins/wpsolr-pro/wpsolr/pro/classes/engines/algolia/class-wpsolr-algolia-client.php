<?php

namespace wpsolr\pro\classes\engines\algolia;

use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use wpsolr\core\classes\engines\WPSOLR_Client;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\utilities\WPSOLR_Option;
use wpsolr\core\classes\WpSolrSchema;

/**
 * Some common methods of the Algolia client.
 *
 */
trait WPSOLR_Algolia_Client {
	use WPSOLR_Client;

	static protected $FIELD_IS_EXIST = 'exist_%s_str';

	static protected $convert_field_name_if_date = [];

	protected $INDEX_REPLICAT_SORT_NAME_PATTERN = '_replica_sort_';

	protected $wpsolr_type = 'wpsolr_types';

	// Unique id to store attached decoded files.
	protected $WPSOLR_DOC_ID_ATTACHMENT = 'wpsolr_doc_id_attachment';

	/** @var SearchClient */
	protected $search_engine_client;

	/** @var string */
	protected $index_label;

	/** @var SearchIndex[] */
	protected $search_indexes;

	// Index conf files
	protected $FILE_CONF_INDEX_5 = 'wpsolr_index_5.json';
	protected $FILE_CONF_INDEX_6 = 'wpsolr_index_6.json';
	protected $FILE_CONF_INDEX_7 = 'wpsolr_index_7.json';

	/**
	 * Try to fix the current index configuration before retrying
	 *
	 * @param $error_msg
	 *
	 * @return bool
	 */
	protected function _try_to_fix_error_doc_type( $error_msg ) {

		if ( false !== strpos( $error_msg, 'the final mapping would have more than 1 type' ) ) {
			// No type required (ES >= 7.x)
			$this->_fix_error_doc_type( 'index_doc_type', '' );

			// Fixed
			return true;

		} else if ( false !== strpos( $error_msg, 'type is missing' ) ) {
			// Type required (ES < 7.x)
			$this->_fix_error_doc_type( 'index_doc_type', $this->wpsolr_type );

			// Fixed
			return true;

		} else if ( false !== strpos( $error_msg, "suggester [autocomplete] doesn't expect any context" ) ) {
			// Index does not support suggester contexts: deactivate contexts in next request
			$this->_fix_error_doc_type( WPSOLR_Option::OPTION_INDEXES_VERSION_SUGGESTER_HAS_CONTEXT, null );

			// Fixed
			return true;

		} else if ( false !== strpos( $error_msg, "Missing mandatory contexts" ) ) {
			// Index does support suggester contexts: activate contexts in next request
			$this->_fix_error_doc_type( WPSOLR_Option::OPTION_INDEXES_VERSION_SUGGESTER_HAS_CONTEXT, '1' );

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

		// To be able to retry now, save it on current object index
		$this->index[ $index_property ] = $doc_type;

		$option_indexes = WPSOLR_Service_Container::getOption()->get_option_indexes();

		if ( isset( $option_indexes[ WPSOLR_Option::OPTION_INDEXES_INDEXES ][ $this->index_indice ] ) ) {
			// To prevent retry later, save it in the index options

			if ( is_null( $doc_type ) ) {
				// null value means "unset"

				unset( $option_indexes[ WPSOLR_Option::OPTION_INDEXES_INDEXES ][ $this->index_indice ][ $index_property ] );

			} else {

				$option_indexes[ WPSOLR_Option::OPTION_INDEXES_INDEXES ][ $this->index_indice ][ $index_property ] = $doc_type;
			}

			// Save it now
			update_option( WPSOLR_Option::OPTION_INDEXES, $option_indexes );
		}

	}

	/**
	 * @param string $index_label
	 *
	 * @return SearchIndex
	 */
	public
	function get_search_index(
		$index_label = ''
	) {

		$index_label = empty( $index_label ) ? $this->index_label : $index_label;

		if ( ! isset( $this->search_indexes[ $index_label ] ) ) {
			$this->search_indexes[ $index_label ] = $this->search_engine_client->initIndex( $index_label );
		}

		return $this->search_indexes[ $index_label ];
	}

	/**
	 * This index has the deprecated "type"?
	 *
	 * @return bool
	 */
	protected
	function _get_index_doc_type() {
		return $this->index['index_doc_type'] ?? $this->wpsolr_type;
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
	 * @return SearchClient
	 */
	protected
	function create_search_engine_client(
		$config
	) {

		$client = SearchClient::create(
			$config['username'],
			$config['password']
		);

		$this->set_index_label( empty( $config ) ? '' : $config['index_label'] );

		return $client;
	}

	/**
	 * Load the content of a conf file.
	 *
	 * @href https://www.algolia.com/doc/api-reference/api-parameters/searchableAttributes/
	 *
	 * @return array
	 */
	protected
	function get_index_settings() {

		$all_fields = WpSolrSchema::get_all_fields();

		$index_analyser_id = $this->config['extra_parameters'][ WPSOLR_Option::OPTION_INDEXES_ANALYSER_ID ];
		$index_analyser_id = empty( trim( $index_analyser_id ) ) ? 'en' : $index_analyser_id;

		return [
			// Propagate settings to replicas
			//'forwardToReplicas'     => true,

			// https://www.algolia.com/doc/api-reference/api-parameters/searchableAttributes/
			'searchableAttributes'  => $all_fields,

			// https://www.algolia.com/doc/api-reference/api-parameters/attributesForFaceting/
			'attributesForFaceting' => $all_fields,

			// https://www.algolia.com/doc/guides/managing-results/refine-results/grouping/how-to/grouping-by-attribute/
			'attributeForDistinct'  => WpSolrSchema::_FIELD_NAME_TYPE,
			'distinct'              => false, // false by default. Set to true on grouped suggestions at query time

			// https://www.algolia.com/doc/api-reference/api-parameters/indexLanguages/
			'indexLanguages'        => [ $index_analyser_id ],

			// https://www.algolia.com/doc/api-reference/api-parameters/queryLanguages/
			'queryLanguages'        => [ $index_analyser_id ],
			'removeStopWords'       => true,
			'ignorePlurals'         => true,

			/*
			'attributesForFaceting' => [
				WpSolrSchema::_FIELD_NAME_TYPE,
				//'filterOnly(attribute2)',
				//'searchable(type)'
			],

			//'customRanking'        => [ 'desc(followers)' ],
			*/
		];

	}

	/**
	 * Retrieve the live Algolia version
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
			throw new \Exception( sprintf( 'WPSOLR works only with Algolia >= 5. Your version is %s.', $version ) );
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
		$result = false;

		if ( is_null( $date_str ) || is_int( $date_str ) ) {

			$result = $date_str;

		} else {

			$timestamp = strtotime( $date_str );

			if ( is_int( $timestamp ) ) {
				$result = date( 'Y-m-d\TH:i:s\Z', $timestamp );
			}

		}

		return $result;
	}

	/**
	 * Create a match_all query
	 *
	 * @return array
	 */
	protected
	function _create_match_all_query() {

		$params         = $this->get_search_index();
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

		$params         = $this->get_search_index();
		$params['body'] = [ 'query' => [ 'bool' => $bool_query ] ];

		return $params;
	}

	/**
	 * Generate a replicate name for each combinaison of sort
	 *
	 * https://www.algolia.com/doc/guides/managing-results/refine-results/sorting/how-to/sort-by-attribute/
	 **/
	protected function _get_replica_name_for_sort() {

		$replica_name = '';

		if ( ! empty( $this->sorts ) ) {
			$replica_names = [];

			foreach ( $this->sorts as $field_name => $order ) {
				if ( WpSolrSchema::get_custom_field_is_date_type( $field_name ) ) {
					// Date fields usable are the unix timestamp version
					$field_name .= wpsolrschema::_SOLR_DYNAMIC_TYPE_INTEGER;
				}
				$replica_names[] = sprintf( '%s_%s', $field_name, $order );
			}

			if ( ! empty( $replica_names ) ) {
				$replica_name = sprintf( '%s%s%s',
					$this->get_search_index()->getIndexName(),
					$this->INDEX_REPLICAT_SORT_NAME_PATTERN,
					implode( '_sort_', $replica_names ) );
			}

		}

		return $replica_name;
	}

	/**
	 * An error happened while searching with a new sort on a replica index not yet created.
	 * Let's create the replica index.
	 *
	 * @param $index_name
	 *
	 */
	protected function _create_new_replica( $index_name ) {

		$custom_ranking = []; // for others
		foreach ( $this->sorts as $field_name => $order ) {

			$field_name = $this->_convert_field_name_if_date( $field_name );

			$custom_ranking[] = sprintf( '%s(%s)', $order, $field_name );
			$ranking[]        = sprintf( '%s(%s)', $order, $field_name );

			// https://www.algolia.com/doc/guides/managing-results/refine-results/sorting/how-to/sort-an-index-alphabetically/
		}

		// Create the replica
		$original_replicas   = $this->get_search_index()->getSettings()['replicas'] ?? [];
		$original_replicas[] = $index_name;
		$this->get_search_index()->setSettings( [
			'replicas' => $original_replicas
		] )->wait();

		// Set sort ranking for the replica index
		$this->get_search_index( $index_name )->setSettings( [ 'customRanking' => $custom_ranking ] )->wait();

	}

	/**
	 * Create the index
	 *
	 * @href https://www.algolia.com/doc/api-reference/api-methods/set-settings/ https://www.algolia.com/doc/api-reference/settings-api-parameters/
	 *
	 * @param array $index_parameters
	 */
	protected function admin_create_index( &$index_parameters ) {
		$settings = $this->get_index_settings();

		$this->get_search_index()->setSettings( $settings )->wait();
	}

	/**
	 * Delete the index and all its replicas in batch
	 *
	 * https://www.algolia.com/doc/guides/sending-and-managing-data/manage-your-indices/how-to/deleting-multiple-indices/
	 *
	 * @throws \Exception
	 */
	public function admin_delete_index() {

		$current_index_name = $this->get_search_index()->getIndexName();

		$indices           = $this->search_engine_client->listIndices();
		$delete_operations = [];

		// Find indexes which are either the current index or one of its replicas
		foreach ( $indices['items'] as $index ) {
			$index_name = $index['name'];
			if ( ( $index_name === $current_index_name ) ||
			     strpos( sprintf( '%s%s', $index_name, $this->INDEX_REPLICAT_SORT_NAME_PATTERN ), $current_index_name ) !== false ) {
				$delete_operations[] = [
					'indexName' => $index_name,
					'action'    => 'delete',
				];
			}
		}

		if ( ! empty( $delete_operations ) ) {
			$this->search_engine_client->multipleBatch( $delete_operations )->wait();
		}
	}

	/**
	 * Add a configuration to the index if missing.
	 */
	protected function admin_index_update( &$index_parameters ) {

		/*
		try {

			$mapping = $this->search_engine_client->indices()->getMapping( $this->get_index() );

		} catch ( \Exception $e ) {

			// Since 5.5.1, no type mapping yet triggers an exception. We continue anyway.
		}

		if ( empty( $mapping ) || empty( $mapping[ $this->get_index_label() ] ) || empty( $mapping[ $this->get_index_label() ]['mappings'] ) ) {

			$this->search_engine_client->indices()->close( $this->get_index() );
			$this->search_engine_client->indices()->putSettings( $this->get_and_decode_configuration_file() );
			$this->search_engine_client->indices()->open( $this->get_index() );
		}*/

	}

	/**
	 * Date fields usable are the unix timestamp version
	 * https://www.algolia.com/doc/guides/managing-results/refine-results/sorting/how-to/sort-an-index-by-date/
	 *
	 * @param string $field_name
	 *
	 * @return string
	 */
	protected function _convert_field_name_if_date( $field_name ): string {

		if ( ! empty( static::$convert_field_name_if_date[ $field_name ] ) ) {
			return static::$convert_field_name_if_date[ $field_name ];
		}

		$new_field_name = $field_name;

		if ( WpSolrSchema::get_custom_field_is_date_type( $field_name ) ) {
			$new_field_name .= wpsolrschema::_SOLR_DYNAMIC_TYPE_INTEGER;
		}

		// save
		static::$convert_field_name_if_date[ $field_name ] = $new_field_name;

		return $new_field_name;
	}

	/**
	 * Date fields usable are the unix timestamp version
	 * https://www.algolia.com/doc/guides/managing-results/refine-results/sorting/how-to/sort-an-index-by-date/
	 *
	 * @param string $value
	 *
	 * @return int|string
	 */
	protected function _convert_to_unix_time_if_date( $value ) {

		if ( ! is_numeric( $value ) ) {

			$converted_value = 1000 * strtotime( $value ); // ms
			$value           = ( false === $converted_value ) ? $value : $converted_value;
		}

		return $value;
	}

	/**
	 * Get the analysers available
	 * @return array
	 */
	static public function get_analysers() {
		/*
		 * https://www.algolia.com/doc/api-reference/api-parameters/queryLanguages/
		 */

		return [
			'af'    => [ 'label' => 'Afrikaans' ],
			'ar'    => [ 'label' => 'Arabic' ],
			'az'    => [ 'label' => 'Azerbaijani' ],
			'bg'    => [ 'label' => 'Bulgarian' ],
			'bn'    => [ 'label' => 'Bengali' ],
			'ca'    => [ 'label' => 'Catalan' ],
			'cs'    => [ 'label' => 'Czech' ],
			'cy'    => [ 'label' => 'Welsh' ],
			'da'    => [ 'label' => 'Danish' ],
			'de'    => [ 'label' => 'German' ],
			'el'    => [ 'label' => 'Greek' ],
			'en'    => [ 'label' => 'English', 'is_default' => true, ],
			'eo'    => [ 'label' => 'Esperanto' ],
			'es'    => [ 'label' => 'Spanish' ],
			'et'    => [ 'label' => 'Estonian' ],
			'eu'    => [ 'label' => 'Basque' ],
			'fa'    => [ 'label' => 'Persian (Farsi)' ],
			'fi'    => [ 'label' => 'Finnish' ],
			'fo'    => [ 'label' => 'Faroese' ],
			'fr'    => [ 'label' => 'French' ],
			'ga'    => [ 'label' => 'Irish' ],
			'gl'    => [ 'label' => 'Galician' ],
			'he'    => [ 'label' => 'Hebrew' ],
			'hi'    => [ 'label' => 'Hindi' ],
			'hu'    => [ 'label' => 'Hungarian' ],
			'hy'    => [ 'label' => 'Armenian' ],
			'id'    => [ 'label' => 'Indonesian' ],
			'is'    => [ 'label' => 'Icelandic' ],
			'it'    => [ 'label' => 'Italian' ],
			'ja'    => [ 'label' => 'Japanese' ],
			'ka'    => [ 'label' => 'Georgian' ],
			'kk'    => [ 'label' => 'Kazakh' ],
			'ko'    => [ 'label' => 'Korean' ],
			'ku'    => [ 'label' => 'Kurdish' ],
			'ky'    => [ 'label' => 'Kirghiz' ],
			'lt'    => [ 'label' => 'Lithuanian' ],
			'lv'    => [ 'label' => 'Latvian' ],
			'mi'    => [ 'label' => 'Maori' ],
			'mn'    => [ 'label' => 'Mongolian' ],
			'mr'    => [ 'label' => 'Marathi' ],
			'ms'    => [ 'label' => 'Malay' ],
			'mt'    => [ 'label' => 'Maltese' ],
			'nb'    => [ 'label' => 'Norwegian Bokmål' ],
			'nl'    => [ 'label' => 'Dutch' ],
			'no'    => [ 'label' => 'Norwegian' ],
			'ns'    => [ 'label' => 'Northern Sotho' ],
			'pl'    => [ 'label' => 'Polish' ],
			'ps'    => [ 'label' => 'Pashto' ],
			'pt'    => [ 'label' => 'Portuguese' ],
			'pt-br' => [ 'label' => 'Brazilian' ],
			'qu'    => [ 'label' => 'Quechua' ],
			'ro'    => [ 'label' => 'Romanian' ],
			'ru'    => [ 'label' => 'Russian' ],
			'sk'    => [ 'label' => 'Slovak' ],
			'sq'    => [ 'label' => 'Albanian' ],
			'sv'    => [ 'label' => 'Swedish' ],
			'sw'    => [ 'label' => 'Swahili' ],
			'ta'    => [ 'label' => 'Tamil' ],
			'te'    => [ 'label' => 'Telugu' ],
			'th'    => [ 'label' => 'Thai' ],
			'tl'    => [ 'label' => 'Tagalog' ],
			'tn'    => [ 'label' => 'Tswana' ],
			'tr'    => [ 'label' => 'Turkish' ],
			'tt'    => [ 'label' => 'Tatar' ],
			'uk'    => [ 'label' => 'Ukranian' ],
			'ur'    => [ 'label' => 'Urdu' ],
			'uz'    => [ 'label' => 'Uzbek' ],
			'zh'    => [ 'label' => 'Chinese' ],
		];
	}

}
