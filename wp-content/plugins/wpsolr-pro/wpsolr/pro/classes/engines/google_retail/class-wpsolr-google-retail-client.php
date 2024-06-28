<?php

namespace wpsolr\pro\classes\engines\google_retail;

use Google\Cloud\Retail\V2\CatalogAttribute;
use Google\Cloud\Retail\V2\CatalogAttribute\DynamicFacetableOption;
use Google\Cloud\Retail\V2\CatalogAttribute\IndexableOption;
use Google\Cloud\Retail\V2\CatalogAttribute\SearchableOption;
use Google\Cloud\Retail\V2\CatalogServiceClient;
use Google\Cloud\Retail\V2\CustomAttribute;
use Google\Cloud\Retail\V2\ProductServiceClient;
use Google\Cloud\Retail\V2\ServingConfigServiceClient;
use wpsolr\core\classes\engines\WPSOLR_Client;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\utilities\WPSOLR_Error_Handling;
use wpsolr\core\classes\utilities\WPSOLR_Option;
use wpsolr\core\classes\WpSolrSchema;

/**
 * Some common methods of the Google_Retail client.
 *
 */
trait WPSOLR_Google_Retail_Client {
	use WPSOLR_Client;

	/**
	 * @inerhitDoc
	 */
	static public function get_facet_hierarchy_separator(): string {
		return ' > ';
	}

	/**
	 * @var string[] Map some WPSOLR fields to the Retail standard attributes
	 */
	protected static $MAPPING_TO_STANDARD_ATTRIBUTES = [
		//WpSolrSchema::_FIELD_NAME_ID        => 'id',
		WpSolrSchema::_FIELD_NAME_CONTENT   => 'description',
		WpSolrSchema::_FIELD_NAME_TITLE     => 'title',
		'product_tag_str'                   => 'tags',
		'flat_hierarchy_product_cat_str'    => 'categories',
		WpSolrSchema::_FIELD_NAME_PERMALINK => 'uri',
	];

	/**
	 * @var string[] Retrieve some attributes from search
	 */
	protected static $RETRIEVABLE_CUSTOM_FIELD_NAMES = [
		WpSolrSchema::_FIELD_NAME_ID,
		WpSolrSchema::_FIELD_NAME_PID,
		WpSolrSchema::_FIELD_NAME_TYPE,
		WpSolrSchema::_FIELD_NAME_META_TYPE_S,
		WpSolrSchema::_FIELD_NAME_TITLE,
		WpSolrSchema::_FIELD_NAME_NUMBER_OF_COMMENTS,
		WpSolrSchema::_FIELD_NAME_COMMENTS,
		WpSolrSchema::_FIELD_NAME_DISPLAY_DATE . WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING,
		WpSolrSchema::_FIELD_NAME_DISPLAY_MODIFIED . WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING,
		WpSolrSchema::_FIELD_NAME_CATEGORIES_STR,
		WpSolrSchema::_FIELD_NAME_AUTHOR,
		WpSolrSchema::_FIELD_NAME_POST_THUMBNAIL_HREF_STR,
		WpSolrSchema::_FIELD_NAME_POST_HREF_STR,
		WpSolrSchema::_FIELD_NAME_SNIPPET_S,
	];

	/**
	 * @var string[] Copy some fields before they are transformed
	 */
	protected static $COPY_CUSTOM_FIELD_NAMES = [
		WpSolrSchema::_FIELD_NAME_DISPLAY_DATE,
		WpSolrSchema::_FIELD_NAME_DISPLAY_MODIFIED,
	];

	/**
	 * Google retail cannot accept fields starting with "_": we add a prefix to fix it
	 * @var string
	 */
	protected $FIELD_NAME_PREFIX = 'wp_';

	/**
	 * Retail api default values
	 */
	protected static $DEFAULT_API_LOCATION = 'global';
	protected static $DEFAULT_API_CATALOG = 'default_catalog';


	static protected $FIELD_IS_EXIST = 'exist_%s_str';

	static protected $convert_field_name_if_date = [];

	protected $INDEX_REPLICAT_SORT_NAME_PATTERN = '_replica_sort_';

	protected $wpsolr_type = 'wpsolr_types';

	// Unique id to store attached decoded files.
	protected $WPSOLR_DOC_ID_ATTACHMENT = 'wpsolr_doc_id_attachment';

	/** @var ProductServiceClient */
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
	 * @return ProductServiceClient
	 */
	protected
	function create_search_engine_client(
		$config
	) {

		/**
		 * Prevent deprecated errors sent by internal Google Retail API: ImportProductsRequest.php line 168
		 * @trigger_error('request_id is deprecated.', E_USER_DEPRECATED);
		 */
		WPSOLR_Error_Handling::deactivate_deprecated_warnings();


		$client = $this->_get_client( $config );

		$this->set_index_label( empty( $config ) ? '' : $config['index_label'] );

		return $client;
	}

	/**
	 * Load the content of a conf file.
	 *
	 * @href https://www.google_retail.com/doc/api-reference/api-parameters/searchableAttributes/
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

			// https://www.google_retail.com/doc/api-reference/api-parameters/searchableAttributes/
			'searchableAttributes'  => $all_fields,

			// https://www.google_retail.com/doc/api-reference/api-parameters/attributesForFaceting/
			'attributesForFaceting' => $all_fields,

			// https://www.google_retail.com/doc/guides/managing-results/refine-results/grouping/how-to/grouping-by-attribute/
			'attributeForDistinct'  => WpSolrSchema::_FIELD_NAME_TYPE,
			'distinct'              => false,
			// false by default. Set to true on grouped suggestions at query time

			// https://www.google_retail.com/doc/api-reference/api-parameters/indexLanguages/
			'indexLanguages'        => [ $index_analyser_id ],

			// https://www.google_retail.com/doc/api-reference/api-parameters/queryLanguages/
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
	 * Retrieve the live Google_Retail version
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
			throw new \Exception( sprintf( 'WPSOLR works only with Google_Retail >= 5. Your version is %s.', $version ) );
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

		if ( is_int( $date_str ) ) {

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
	 * https://www.google_retail.com/doc/guides/managing-results/refine-results/sorting/how-to/sort-by-attribute/
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
	 * Create the index
	 *
	 * @href https://www.google_retail.com/doc/api-reference/api-methods/set-settings/ https://www.google_retail.com/doc/api-reference/settings-api-parameters/
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
	 * https://www.google_retail.com/doc/guides/sending-and-managing-data/manage-your-indices/how-to/deleting-multiple-indices/
	 *
	 * @throws \Exception
	 */
	public function admin_delete_index() {

		throw new \Exception( "Google Retail API cannot delete the index, as the index is the Google Cloud project itself. Please login to your Google Retail API dashboard to manage the project." );
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
	 *
	 * @param string $field_name
	 *
	 * @return string
	 */
	protected function _convert_field_name_if_date( $field_name ): string {

		return $field_name;

		/*
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
		*/
	}

	/**
	 * Date fields usable are the unix timestamp version
	 * https://www.google_retail.com/doc/guides/managing-results/refine-results/sorting/how-to/sort-an-index-by-date/
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
		return [
			'' => [ 'label' => 'None', 'is_default' => true, ],
		];
	}

	/**
	 * @param string[] $field_names
	 *
	 * @throws \Exception
	 */
	protected function _add_index_fields_definitions( array $field_names ): void {

		$fields_not_indexed = [ WpSolrSchema::_FIELD_NAME_ID, WpSolrSchema::_FIELD_NAME_BASE64, ];

		$index_fields_definitions = $this->search_engine_client->send( new ListItemProperties() );
		foreach ( $field_names as $field_name ) {
			if ( in_array( $field_name, $fields_not_indexed ) ) {
				// id is a reserved property name
				continue;
			}

			$is_field_exists = false;

			if ( ( false !== strpos( $field_name, '%' ) ) ) {
				continue;
			}

			foreach ( $index_fields_definitions as $index_field_definition ) {
				if ( $field_name === $index_field_definition['name'] ) {
					// Field exists: do not add it
					$is_field_exists = true;
					break;
				}
			}

			if ( ! $is_field_exists ) {
				$index_fields_definitions = $this->_add_index_field_definition( $field_name );
			}

		}
	}

	/**
	 * @href https://www.semi.technology/developers/weaviate/current/restful-api-references/schema.html#add-a-property
	 * Field does not exist in index yet: add it
	 *
	 * @param string $field_name
	 *
	 * @return array
	 * @throws \Exception
	 */
	protected function _add_index_field_definition( string $field_name ): array {
		$this->search_engine_client->send( new AddItemProperty( $field_name, $this->_get_field_data_type( $field_name ) ) );

		return $this->search_engine_client->send( new ListItemProperties() );
	}

	/**
	 * @href https://docs.google_retail.com/_static/php/classes/Google_Retail-RecommApi-Requests-AddItemProperty.html
	 *
	 * @param string $field_name
	 *
	 * @return string
	 * @throws \Exception
	 */
	protected function _get_field_data_type( string $field_name ): string {

		$field_type = WpSolrSchema::DEFAULT_FIELD_TYPES[ $field_name ] ?? WpSolrSchema::get_custom_field_dynamic_type( $field_name );

		// ['int'] => 'int'
		$field_type = is_array( $field_type ) ? $field_type[0] : $field_type;

		switch ( $field_type ) {
			case WpSolrSchema::_SOLR_DYNAMIC_TYPE_INTEGER:
			case WpSolrSchema::_SOLR_DYNAMIC_TYPE_INTEGER_LONG:
				$type = 'int';
				break;

			case WpSolrSchema::_SOLR_DYNAMIC_TYPE_DATE:
				$type = 'timestamp';
				break;

			case WpSolrSchema::_SOLR_DYNAMIC_TYPE_FLOAT:
			case WpSolrSchema::_SOLR_DYNAMIC_TYPE_FLOAT_DOUBLE:
				$type = 'double';
				break;

			case WpSolrSchema::_SOLR_DYNAMIC_TYPE_S:
			case WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING:
			case WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING1:
				$type = 'string';
				break;

			case WpSolrSchema::_SOLR_DYNAMIC_TYPE_TEXT:
				$type = 'string'; // No difference between string and text with Google_Retail
				break;

			default:
				throw new \Exception( "$field_type type not implemented for field $field_name" );
		}

		return $type;
	}

	/**
	 * Get the Retail API formatted branch
	 *
	 * @return string
	 */
	protected function _get_formatted_branch(): string {
		return $this->search_engine_client->branchName( $this->_get_project_id(), static::$DEFAULT_API_LOCATION, static::$DEFAULT_API_CATALOG, $this->_get_catalog_branch() );
	}

	/**
	 * Get the Retail API formatted default catalog
	 *
	 * @return string
	 */
	protected function _get_formatted_default_catalog(): string {
		return ( new CatalogServiceClient( $this->_get_credentials( $this->config ) ) )->catalogName( $this->_get_project_id(), static::$DEFAULT_API_LOCATION, static::$DEFAULT_API_CATALOG );
	}

	/**
	 * Get the Retail API formatted branch
	 *
	 * @param string $product_id
	 *
	 * @return string
	 */
	protected function _get_formatted_product( string $product_id ): string {
		return $this->search_engine_client->productName( $this->_get_project_id(),
			static::$DEFAULT_API_LOCATION, static::$DEFAULT_API_CATALOG,
			$this->_get_catalog_branch(), (string) $product_id );
	}

	/**
	 * Get the Retail API formatted branch
	 *
	 * @param int $product_id
	 *
	 * @return string
	 */
	protected function _get_formatted_serving_config(): string {
		return ( new ServingConfigServiceClient( $this->_get_credentials( $this->config ) ) )->servingConfigName(
			$this->_get_project_id(), static::$DEFAULT_API_LOCATION, static::$DEFAULT_API_CATALOG,
			WPSOLR_Service_Container::getOption()->get_search_serving_config_id()
		);
	}

	/**
	 * Get the Retail API formatted location
	 *
	 * @return string
	 */
	protected function _get_formatted_location(): string {
		return $this->search_engine_client->locationName( $this->_get_project_id(), static::$DEFAULT_API_LOCATION );
	}

	/**
	 * Extract Google project_id from the JSON key of the index settings
	 * @return string
	 */
	protected function _get_project_id() {
		return $this->_get_credentials()['credentials']['project_id'];
	}

	/**
	 * Extract the catalog branch from the index settings
	 * @return string
	 */
	protected function _get_catalog_branch() {
		return empty( $this->config['extra_parameters']['index_catalog_branch'] ) ? "0" : $this->config['extra_parameters']['index_catalog_branch'];
	}

	/**
	 * @param array $config
	 *
	 * @return array
	 */
	protected function _get_credentials( array $config = null ): array {
		return [
			'credentials' => json_decode( ( $config ?? $this->config )['extra_parameters']['index_key_json'], true ),
		];
	}

	/**
	 * Get client for catalog
	 * @return CatalogServiceClient
	 * @throws \Google\ApiCore\ValidationException
	 */
	protected function _get_client_catalog(): CatalogServiceClient {
		return new CatalogServiceClient( $this->_get_credentials() );
	}

	/**
	 * @param array $config
	 *
	 * @return mixed
	 * @throws \Google\ApiCore\ValidationException
	 */
	protected function _get_client( array $config ): object {
		throw new \Exception( 'Method _get_client() undefined.' );
	}

	/**
	 * @inerhitDoc
	 */
	public function get_has_exists_filter(): bool {
		return false;
	}

	/**
	 * Google retail attributes cannot start with "_': add a prefix
	 *
	 * @param string $field_name
	 *
	 * @return string
	 */
	protected function _get_formatted_field_name( string $field_name ): string {
		return sprintf( '%s%s', $this->FIELD_NAME_PREFIX, $field_name );
	}

	/**
	 *
	 * @param string[] $field_names
	 *
	 * @return array
	 */
	protected function _get_formatted_field_names( array $field_names ): array {
		$results = [];
		foreach ( $field_names as $field_name ) {
			$results[] = $this->_get_formatted_field_name( $field_name );
		}

		return $results;
	}

	/**
	 * Google retail attributes cannot start with "_': add a prefix
	 *
	 * @param string $field_name
	 *
	 * @return string
	 */
	protected function _get_formatted_attribute_name( string $field_name ): string {
		return static::$MAPPING_TO_STANDARD_ATTRIBUTES[ $field_name ] ??
		       sprintf( 'attributes.%s', $this->_get_formatted_field_name( $field_name ) );
	}

	/**
	 * Remove Google retail attribute.*_ prefix to get a WPSOLR custom field name
	 *
	 * @param string $attribute_name
	 *
	 * @return string
	 */
	protected function _get_unformatted_attribute_name( string $attribute_name ): string {
		return ( false !== ( $standard_attribute = array_search( $attribute_name, static::$MAPPING_TO_STANDARD_ATTRIBUTES ) ) ) ?
			$standard_attribute :
			str_replace( $this->FIELD_NAME_PREFIX, '', str_replace( sprintf( 'attributes.%s', $this->FIELD_NAME_PREFIX ), '', $attribute_name ) );
	}

	/**
	 * Manage attributes at catalog level: https://cloud.google.com/retail/docs/reference/rest/v2beta/projects.locations.catalogs.attributesConfig#catalogattribute
	 *
	 * @param string[] $custom_field_names
	 *
	 * @return void
	 */
	protected function _update_catalog_custom_attributes( array $custom_field_names, $is_retrievable = false ): void {

		if ( empty( $custom_field_names ) ) {
			return;
		}

		$project_id           = $this->_get_project_id();
		$option_catalogs_name = WPSOLR_Option::OPTION_INDEX_CATALOGS;
		$option_catalogs      = get_option( $option_catalogs_name, [] );
		$option_catalog       = $option_catalogs[ $project_id ] ?? [];


		/*
		$client                 = $this->_get_client_catalog();
		$attributes_config_name = $client->attributesConfigName( $this->_get_project_id(),
			static::$DEFAULT_API_LOCATION, static::$DEFAULT_API_CATALOG );
		$catalog_attributes     = $client->getAttributesConfig( $attributes_config_name );
		throw new \Exception( $catalog_attributes-> );
		*/

		/** @var CustomAttribute $custom_attribute */
		$is_catalog_updated = false;
		foreach ( $custom_field_names as $custom_field_name ) {

			$catalog_attribute_name = "attributes.$custom_field_name";

			if ( ! in_array( $catalog_attribute_name, $option_catalog ) ) {
				/**
				 * This attribute name is not in the local stored catalog already: continue
				 */

				/**
				 * Attributes from the online catalog are not to be created: add them to the stored catalog
				 */

				if ( ! isset( $client ) ) {
					$client                 = $this->_get_client_catalog();
					$attributes_config_name = $client->attributesConfigName( $this->_get_project_id(),
						static::$DEFAULT_API_LOCATION, static::$DEFAULT_API_CATALOG );
					$catalog_new_attribute  = ( new CatalogAttribute() )
						->setIndexableOption( IndexableOption::INDEXABLE_ENABLED )
						->setSearchableOption( SearchableOption::SEARCHABLE_ENABLED )
						->setDynamicFacetableOption( DynamicFacetableOption::DYNAMIC_FACETABLE_DISABLED );
				}

				foreach ( $client->getAttributesConfig( $attributes_config_name )->getCatalogAttributes() as $key => $catalog_attribute ) {
					//$client->removeCatalogAttribute( $attributes_config_name, $key );
					if ( ( false !== strpos( $key, 'attributes.' ) ) && ! in_array( $key, $option_catalog ) ) {
						$is_catalog_updated = true;

						$option_catalog[] = $key;
					}
				}

				if ( ! in_array( $catalog_attribute_name, $option_catalog ) ) {
					$is_catalog_updated = true;
					$option_catalog[]   = $catalog_attribute_name;

					/**
					 * This attribute name is not in the online catalog already: add it
					 */
					$client->addCatalogAttribute( $attributes_config_name, $catalog_new_attribute->setKey( $catalog_attribute_name ) );
				}

			}
		}

		if ( $is_catalog_updated ) {
			$option_catalogs[ $project_id ] = $option_catalog;
			update_option( $option_catalogs_name, $option_catalogs );
		}

	}

}
