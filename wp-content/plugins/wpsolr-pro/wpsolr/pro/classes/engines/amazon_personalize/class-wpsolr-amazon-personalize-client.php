<?php

namespace wpsolr\pro\classes\engines\amazon_personalize;

use Aws\Personalize\PersonalizeClient;
use Aws\PersonalizeEvents\PersonalizeEventsClient;
use wpsolr\core\classes\engines\WPSOLR_Client;
use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_Abstract;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\utilities\WPSOLR_Option;
use wpsolr\core\classes\WpSolrSchema;

/**
 * Some common methods of the Amazon_Personalize client.
 *
 */
trait WPSOLR_Amazon_Personalize_Client {
	use WPSOLR_Client;

	static protected $FIELD_IS_EXIST = 'exist_%s_str';

	static protected $convert_field_name_if_date = [];

	protected $INDEX_REPLICAT_SORT_NAME_PATTERN = '_replica_sort_';

	protected $wpsolr_type = 'wpsolr_types';

	// Unique id to store attached decoded files.
	protected $WPSOLR_DOC_ID_ATTACHMENT = 'wpsolr_doc_id_attachment';

	/** @var PersonalizeClient */
	protected $search_engine_client;

	/** @var string */
	protected $index_label;

	/** @var SearchIndex[] */
	protected $search_indexes;

	/**
	 * @var PersonalizeEventsClient
	 */
	protected $search_engine_client_events;

	// Index conf files
	protected $FILE_CONF_INDEX_5 = 'wpsolr_index_5.json';
	protected $FILE_CONF_INDEX_6 = 'wpsolr_index_6.json';
	protected $FILE_CONF_INDEX_7 = 'wpsolr_index_7.json';

	protected static $DATASET_TYPE_INTERACTIONS = 'INTERACTIONS';
	protected static $DATASET_TYPE_ITEMS = 'ITEMS';
	protected static $DATASET_TYPE_USERS = 'USERS';

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
	 * @return PersonalizeClient
	 */
	protected
	function create_search_engine_client(
		$config
	) {

		$client = new PersonalizeClient( $this->_extract_aws_credentials_from_config( $config ) );

		$this->set_index_label( empty( $config ) ? '' : $config['index_label'] );

		return $client;
	}

	/**
	 * @return PersonalizeEventsClient
	 */
	protected function _create_search_engine_client_events() {
		if ( ! isset( $this->search_engine_client_events ) ) {
			$this->search_engine_client_events = new PersonalizeEventsClient( $this->_extract_aws_credentials_from_config( $this->config ) );
		}

		return $this->search_engine_client_events;
	}

	/**
	 * Retrieve the live Amazon_Personalize version
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
			throw new \Exception( sprintf( 'WPSOLR works only with Amazon_Personalize >= 5. Your version is %s.', $version ) );
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
	 * https://www.amazon_personalize.com/doc/guides/managing-results/refine-results/sorting/how-to/sort-by-attribute/
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

			// https://www.amazon_personalize.com/doc/guides/managing-results/refine-results/sorting/how-to/sort-an-index-alphabetically/
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
	 * @href https://www.amazon_personalize.com/doc/api-reference/api-methods/set-settings/ https://www.amazon_personalize.com/doc/api-reference/settings-api-parameters/
	 *
	 * @param array $index_parameters
	 */
	protected function admin_create_index( &$index_parameters ) {

		$dataset_group_arn = '';

		/**
		 * Try to find a dataset group with the same name
		 *
		 * https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-personalize-2018-05-22.html#listdatasetgroups
		 */
		$result = $this->search_engine_client->listDatasetGroups( [
			'maxResults' => 99, // Max 100
		] );
		foreach ( $result->get( 'datasetGroups' ) as $dataset_group ) {
			if ( $this->get_index_label() === $dataset_group['name'] ) {
				// Found it: stop
				$dataset_group_arn = $dataset_group['datasetGroupArn'];

				break;
			}
		}


		if ( empty( $dataset_group_arn ) ) {
			/**
			 * https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-personalize-2018-05-22.html#createdatasetgroup
			 */
			$result            = $this->search_engine_client->createDatasetGroup( [
				'name' => $this->get_index_label(),
			] );
			$dataset_group_arn = $result->get( 'datasetGroupArn' );

			/**
			 * Wait for ACTIVE status
			 */
			for ( $nb_checks = 1; $nb_checks <= 10; $nb_checks ++ ) {
				/**
				 * https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-personalize-2018-05-22.html#describedatasetgroup
				 */
				$result = $this->search_engine_client->describeDatasetGroup( [
					'datasetGroupArn' => $dataset_group_arn,
				] );
				if ( 'ACTIVE' === $result->get( 'datasetGroup' )['status'] ) {
					break;
				}

				// Retry after a delay
				sleep( 10 );
			}

		}

		// Save the dataset group Arn
		$this->config['extra_parameters']['dataset_group_arn'] = $dataset_group_arn;
		$index_parameters['dataset_group_arn']                 = $dataset_group_arn;

		// Create datasets for this group
		$this->_create_datasets( $index_parameters, $dataset_group_arn );

	}

	/**
	 * Delete the index and all its replicas in batch
	 *
	 * https://www.amazon_personalize.com/doc/guides/sending-and-managing-data/manage-your-indices/how-to/deleting-multiple-indices/
	 *
	 * @throws \Exception
	 */
	public function admin_delete_index() {

		throw new \Exception( "Amazon_Personalize' API does not provide a delete database endpoint. Please login to your Amazon_Personalize's dashboard to do it manually." );
	}

	/**
	 * Add a configuration to the index if missing.
	 */
	protected function admin_index_update( &$index_parameters ) {

		// Create datasets for this group
		$this->_create_datasets( $index_parameters, $this->config['extra_parameters']['dataset_group_arn'] );
	}

	/**
	 * Date fields usable are the unix timestamp version
	 * https://www.amazon_personalize.com/doc/guides/managing-results/refine-results/sorting/how-to/sort-an-index-by-date/
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
	 * https://www.amazon_personalize.com/doc/guides/managing-results/refine-results/sorting/how-to/sort-an-index-by-date/
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
	 * @href https://docs.amazon_personalize.com/_static/php/classes/Amazon_Personalize-RecommApi-Requests-AddItemProperty.html
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
				$type = 'string'; // No difference between string and text with Amazon_Personalize
				break;

			default:
				throw new \Exception( "$field_type type not implemented for field $field_name" );
		}

		return $type;
	}

	/**
	 * @param array $config
	 *
	 * @return array
	 */
	protected function _extract_aws_credentials_from_config( $config ): array {
		return [
			'credentials' => [
				'key'    => $config['aws_access_key_id'],
				'secret' => $config['aws_secret_access_key'],
			],
			'region'      => $config['aws_region'],
			'version'     => 'latest',
		];
	}

	/**
	 * Create the datasets of the dataset group
	 *
	 * @param array $index_parameters
	 */
	protected function _create_datasets( &$index_parameters, $dataset_group_arn ) {

		/**
		 * 3 dataset types to create
		 * https://docs.aws.amazon.com/personalize/latest/dg/custom-datasets-and-schemas.html#dataset-requirements
		 */
		foreach (
			[
				static::$DATASET_TYPE_ITEMS        => [
					'option_name' => WPSOLR_Hosting_Api_Abstract::FIELD_NAME_FIELDS_INDEX_DATASET_ITEMS_ARN,
					'schema'      => [
						'name'   => 'Items',
						'fields' => [
							[
								'name' => 'ITEM_ID',
								'type' => 'string',
							],
							[
								'name' => 'CREATION_TIMESTAMP',
								'type' => 'long',
							],
							[
								'name'    => 'DESCRIPTION',
								'type'    => [ 'null', 'string' ],
								'textual' => true, // Used for training
							],
							[
								'name'        => 'GENRE',
								'type'        => [ 'null', 'string' ],
								'categorical' => true, // Used for training. 'Action|Adventure' for Action>Adventure
							],
						],
					],
				],
				static::$DATASET_TYPE_USERS        => [
					'option_name' => WPSOLR_Hosting_Api_Abstract::FIELD_NAME_FIELDS_INDEX_DATASET_USERS_ARN,
					'schema'      => [
						'name'   => 'Users',
						'fields' => [
							[
								'name' => 'USER_ID',
								'type' => 'string',
							],
							[
								'name' => 'AGE',
								'type' => [ 'null', 'int' ],
							],
							[
								'name' => 'GENDER',
								'type' => [ 'null', 'string' ],
							],
						],
					],
				],
				static::$DATASET_TYPE_INTERACTIONS => [
					'option_name' => WPSOLR_Hosting_Api_Abstract::FIELD_NAME_FIELDS_INDEX_DATASET_EVENTS_ARN,
					'schema'      => [
						'name'   => 'Interactions',
						'fields' => [
							[
								'name' => 'USER_ID',
								'type' => 'string',
							],
							[
								'name' => 'ITEM_ID',
								'type' => 'string',
							],
							[
								'name' => 'TIMESTAMP',
								'type' => 'long',
							],
							[
								'name' => 'EVENT_TYPE',
								'type' => 'string',
							],
							[
								'name' => 'EVENT_VALUE',
								'type' => [ 'null', 'float' ],
							],
							[
								'name' => 'IMPRESSION',
								'type' => [ 'null', 'string' ],
							],
							[
								'name' => 'RECOMMENDATION_ID',
								'type' => [ 'null', 'string' ],
							],
						],
					],
				],
			] as $dataset_type => $dataset_def
		) {

			$dataset_arn = '';

			/**
			 * Get datasets of this group
			 *
			 * https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-personalize-2018-05-22.html#listdatasets
			 */
			$result = $this->search_engine_client->listDatasets( [
				'datasetGroupArn' => $dataset_group_arn,
				'maxResults'      => 99, // Max 100
			] );

			foreach ( $result->get( 'datasets' ) as $dataset_group ) {
				if ( $dataset_type === $dataset_group['datasetType'] ) {
					// Found it: stop
					$dataset_arn = $dataset_group['datasetArn'];
					break;
				}
			}

			if ( empty( $dataset_arn ) ) {

				$schema_name = sprintf( '%s-%s-schema', $this->get_index_label(), $dataset_type );
				$schema_arn  = '';

				/**
				 * https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-personalize-2018-05-22.html#listschemas
				 */
				$result = $this->search_engine_client->listSchemas( [
					'maxResults' => 99, // Max 100
				] );
				foreach ( $result->get( 'schemas' ) as $schema ) {
					if ( $schema_name === $schema['name'] ) {
						// Found it: stop
						$schema_arn = $schema['schemaArn'];

						/**
						 * https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-personalize-2018-05-22.html#deleteschema
						 */
						$this->search_engine_client->deleteSchema( [
							'schemaArn' => $schema_arn,
						] );
						break;
					}
				}

				/**
				 * https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-personalize-2018-05-22.html#createschema
				 */
				$schema_base = [
					'type'      => 'record',
					'namespace' => 'com.amazonaws.personalize.schema',
					'version'   => '1.0',
				];
				$schema      = $this->search_engine_client->createSchema( [
					'name'   => $schema_name,
					//'domain' => '', //ECOMMERCE|VIDEO_ON_DEMAND
					'schema' => wp_json_encode( array_merge( $schema_base, $dataset_def['schema'] ) ),
				] );
				$schema_arn  = $schema['schemaArn'];

				/**
				 * https://docs.aws.amazon.com/personalize/latest/dg/API_CreateDataset.html
				 */
				$result      = $this->search_engine_client->createDataset( [
					'name'            => sprintf( '%s-%s', $this->get_index_label(), $dataset_type ),
					'datasetGroupArn' => $dataset_group_arn,
					'datasetType'     => $dataset_type,
					'schemaArn'       => $schema_arn,
				] );
				$dataset_arn = $result->get( 'datasetArn' );
			}

			// Save the dataset Arn
			$this->config['extra_parameters'][ $dataset_def['option_name'] ] = $dataset_arn;
			$index_parameters[ $dataset_def['option_name'] ]                 = $dataset_arn;
		}

	}


}
