<?php

namespace wpsolr\pro\extensions\ai_api;

use wpsolr\core\classes\engines\WPSOLR_AbstractIndexClient;
use wpsolr\core\classes\extensions\WPSOLR_Extension;
use wpsolr\core\classes\utilities\WPSOLR_Option;
use wpsolr\core\classes\WPSOLR_Events;
use wpsolr\core\classes\WpSolrSchema;
use wpsolr\pro\classes\ai_api\WPSOLR_AI_Api_Abstract;

class WPSOLR_Option_AI_Api extends WPSOLR_Extension {

	/** @var string Post custom field name storing the information that the post has already beed extracted by a ai_api uuid */
	protected const WPSOLR_AI_API_CACHED_FIELD_NAME = 'wpsolr_cached_ai_api';

	/** @var array $ai_apis */
	protected $ai_apis;

	/**
	 * Constructor
	 * Subscribe to actions/filters
	 **/
	function __construct() {

		$this->ai_apis = $this->get_container()->get_service_option()->get_option_ai_api_apis();

		add_filter( WPSOLR_Events::WPSOLR_FILTER_SOLARIUM_DOCUMENT_FOR_UPDATE, [
			$this,
			'add_fields_to_document_for_update',
		], 10, 5 );

		if ( is_admin() ) {

			$this->init_default_events();
		}

	}

	protected function get_default_custom_fields() {

		$results = [];

		$services_extracted_fields = [];
		foreach ( $this->ai_apis as $ai_api_uuid => $ai_api ) {
			try {

				if ( ! empty( $ai_api[ WPSOLR_Option::OPTION_AI_API_IS_ACTIVE ] ) ) {
					$service_id                    = $this->get_ai_api_service_id( $ai_api_uuid );
					$service_extracted_field_names = WPSOLR_AI_Api_Abstract::get_ai_api_service_by_id( $service_id )->get_extracted_fields();
					foreach ( $service_extracted_field_names as $service_extracted_field_name ) {
						$results[ $service_extracted_field_name ] = [
							self::_FIELD_POST_TYPES                                                   => $this->get_ai_api_index_model_types( $ai_api_uuid, '' ),
							WPSOLR_Option::OPTION_INDEX_CUSTOM_FIELD_PROPERTY_SOLR_TYPE               => WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING,
							WPSOLR_Option::OPTION_INDEX_CUSTOM_FIELD_PROPERTY_CONVERSION_ERROR_ACTION => WPSOLR_Option::OPTION_INDEX_CUSTOM_FIELD_PROPERTY_CONVERSION_ERROR_ACTION_IGNORE_FIELD,
						];
					}
				}

			} catch ( \Exception $e ) {
				// Do nothing. Service deprecated?
			}
		}

		return $results;
	}

	/**
	 * @param string $ai_api_uuid
	 * @param string $property_name
	 *
	 * @return string|array
	 */
	protected function get_ai_api_property( $ai_api_uuid, $property_name, $property_default_value = '' ) {

		return ( ! empty( $this->ai_apis ) && ! empty( $this->ai_apis[ $ai_api_uuid ] ) && ! empty( $this->ai_apis[ $ai_api_uuid ][ $property_name ] ) )
			? $this->ai_apis[ $ai_api_uuid ][ $property_name ]
			: $property_default_value;
	}

	/**
	 * @param string $ai_api_uuid
	 *
	 * @return string
	 */
	protected function get_ai_api_label( $ai_api_uuid ) {
		return $this->get_ai_api_property( $ai_api_uuid, WPSOLR_Option::OPTION_AI_API_LABEL, '' );
	}

	/**
	 * @param string $provider_id
	 *
	 * @return string
	 */
	protected function get_ai_api_provider_id( $ai_api_uuid ) {
		return $this->get_ai_api_property( $ai_api_uuid, WPSOLR_Option::OPTION_AI_API_PROVIDER, '' );
	}

	/**
	 * @param string $service_id
	 *
	 * @return string
	 */
	protected function get_ai_api_service_id( $ai_api_uuid ) {
		return $this->get_ai_api_property( $ai_api_uuid, WPSOLR_Option::OPTION_AI_API_SERVICE, '' );
	}

	/**
	 * @param string $service_id
	 *
	 * @return array
	 */
	protected function get_ai_api_indexes( $ai_api_uuid ) {
		return $this->get_ai_api_property( $ai_api_uuid, WPSOLR_Option::OPTION_AI_API_INDEXES, [] );
	}

	/**
	 * @param string $ai_api_uuid
	 * @param string $index_uuid
	 *
	 * @return string[]
	 */
	protected function get_ai_api_index_model_types( $ai_api_uuid, $index_uuid ) {

		$results = [];

		$ai_api_indexes = $this->get_ai_api_indexes( $ai_api_uuid );

		foreach ( $ai_api_indexes as $ai_api_index_uuid => $ai_api_index ) {

			if ( empty( $index_uuid ) || ( $index_uuid === $ai_api_index_uuid ) ) {
				// Get model types of all indexes, or of selected index only id not empty

				if ( ! empty( $ai_api_index[ WPSOLR_Option::OPTION_AI_API_INDEX_IS_SELECTED ] ) &&
				     ! empty( $ai_api_index[ WPSOLR_Option::OPTION_AI_API_INDEX_POST_TYPES ] ) ) {
					$results = array_merge( $results, array_keys( $ai_api_index[ WPSOLR_Option::OPTION_AI_API_INDEX_POST_TYPES ] ) );
				}
			}
		}

		return $results;
	}

	/**
	 * Add extracted fields to a document
	 *
	 * @param array $document_before_update
	 * @param $solr_indexing_options
	 * @param $post
	 * @param $attachment_body
	 * @param WPSOLR_AbstractIndexClient $search_engine_client
	 *
	 * @return array Document updated with fields
	 * @throws \Exception
	 */
	function add_fields_to_document_for_update( array $document_before_update, $solr_indexing_options, $post, $attachment_body, WPSOLR_AbstractIndexClient $search_engine_client ) {

		//delete_post_meta( $post->ID, self::WPSOLR_AI_API_CACHED_FIELD_NAME );

		// Get the service(s) to call for this document
		$ai_apis_cached     = [];
		$ai_apis_not_cached = [];
		$ai_apis_all        = $this->_get_index_model_type_ai_apis(
			$search_engine_client->get_index_uuid(),
			$document_before_update[ WpSolrSchema::_FIELD_NAME_TYPE ]
		);

		$post_cached_ai_apis = [];
		if ( $post instanceof \WP_Post ) {
			// retrieve ai_apis already called on this post
			if ( empty( $post_cached_ai_apis = get_post_meta( $post->ID, static::WPSOLR_AI_API_CACHED_FIELD_NAME, true ) ) ) {
				$post_cached_ai_apis = [];
			}

			// Remove the services already cached for this document
			foreach ( $ai_apis_all as $ai_api_uuid => $ai_api ) {
				if ( empty( $ai_api[ WPSOLR_Option::OPTION_AI_API_IS_CACHED ] ) || ! in_array( $ai_api_uuid, $post_cached_ai_apis ) ) {
					$ai_apis_not_cached[ $ai_api_uuid ] = $ai_api;
				} else {
					$ai_apis_cached[ $ai_api_uuid ] = $ai_api;
				}
			}
		} else {
			$ai_apis_not_cached = $ai_apis_all;
		}

		// Call the service(s) on this document text
		$ai_apis_fields = empty( $ai_apis_not_cached ) ? [] : $this->_call_services( $ai_apis_not_cached, $document_before_update );


		// Update the document with the extracted fields
		$current_post_ai_apis = [];
		foreach ( $this->_get_services_extracted_fields( $ai_apis_not_cached ) as $ai_api_uuid => $ai_api_fields_names ) {
			foreach ( $ai_api_fields_names as $ai_api_field_name ) {

				$ai_api_field_values = ( isset( $ai_apis_fields[ $ai_api_uuid ] ) && isset( $ai_apis_fields[ $ai_api_uuid ][ $ai_api_field_name ] ) ) ?
					$ai_apis_fields[ $ai_api_uuid ][ $ai_api_field_name ] : [];

				$indexed_field_name = WpSolrSchema::replace_field_name_extension(
					$ai_api_field_name . WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING
				);

				if ( ! empty( $ai_api_field_values ) ) {
					$document_before_update[ $indexed_field_name ] = $ai_api_field_values;
				} else {
					unset( $document_before_update[ $indexed_field_name ] );
				}

				if ( $post instanceof \WP_Post ) {
					// Save service fields in post custom fields

					$service_field_name_without_str = WpSolrSchema::get_field_without_str_ending( $ai_api_field_name );

					// Add existing metas first
					/** @var  \WP_Post $post */
					delete_post_meta( $post->ID, $service_field_name_without_str );
					foreach ( $ai_api_field_values as $ai_api_field_value ) {
						add_post_meta( $post->ID, $service_field_name_without_str, $ai_api_field_value );
					}
				}

				if ( ! empty( $ai_apis_not_cached[ $ai_api_uuid ][ WPSOLR_Option::OPTION_AI_API_IS_CACHED ] ) ) {
					// Mark the document as extracted with this AI Api
					$current_post_ai_apis[ $ai_api_uuid ] = 'y';
				}
			}
		}

		if ( ! empty( $current_post_ai_apis ) ) {
			update_post_meta( $post->ID, self::WPSOLR_AI_API_CACHED_FIELD_NAME, array_merge( $post_cached_ai_apis, array_keys( $current_post_ai_apis ) ) );
		}

		// Add a notice message for each service
		$notices = [];
		foreach ( $ai_apis_not_cached as $ai_api_uuid => $ai_api ) {
			$notices[] = sprintf( 'Pre-processed by AI Api "%s".', $ai_api[ WPSOLR_Option::OPTION_AI_API_LABEL ] );
		}
		foreach ( $ai_apis_cached as $ai_api_uuid => $ai_api ) {
			$notices[] = sprintf( 'Ignored cached AI Api "%s".', $ai_api[ WPSOLR_Option::OPTION_AI_API_LABEL ] );
		}
		if ( ! empty( $notices ) ) {
			$search_engine_client->add_notice_message( implode( ' ', $notices ) );
		}

		return $document_before_update;
	}

	/**
	 * Call services on a document
	 *
	 * @param array $ai_apis
	 * @param array $document_for_update
	 *
	 * @return array
	 * @throws \Exception
	 */
	protected
	function _call_services(
		$ai_apis, array $document_for_update
	) {
		$results = [];

		foreach ( $ai_apis as $ai_api_uuid => $ai_api ) {
			$ai_api['ai_api_uuid']   = $ai_api_uuid;
			$results[ $ai_api_uuid ] = WPSOLR_AI_Api_Abstract::get_ai_api_service_by_id( $this->get_ai_api_service_id( $ai_api_uuid ) )
			                                                 ->call_service( $ai_api, $document_for_update );
		}

		return $results;
	}

	/**
	 * Get all services extracted fields
	 *
	 * @param array $ai_apis
	 *
	 * @return array
	 * @throws \Exception
	 */
	protected
	function _get_services_extracted_fields(
		$ai_apis
	) {
		$results = [];

		foreach ( $ai_apis as $ai_api_uuid => $ai_api ) {
			$results[ $ai_api_uuid ] = WPSOLR_AI_Api_Abstract::get_ai_api_service_by_id( $this->get_ai_api_service_id( $ai_api_uuid ) )->get_extracted_fields();
		}

		return $results;
	}

	/**
	 * Get the ai_apis associated to a model type
	 *
	 * @param string $index_uuid
	 * @param string $model_type
	 * @param string[] $excluded_ai_api_uuids
	 *
	 * @return array
	 */
	private
	function _get_index_model_type_ai_apis(
		$index_uuid, $model_type
	) {

		$results = [];

		foreach ( $this->ai_apis as $ai_api_uuid => $ai_api ) {

			if ( ! empty( $ai_api[ WPSOLR_Option::OPTION_AI_API_IS_ACTIVE ] ) &&
			     in_array( $model_type, $this->get_ai_api_index_model_types( $ai_api_uuid, $index_uuid ) )
			) {
				$results[ $ai_api_uuid ] = $ai_api;
			}

		}

		return $results;
	}

}
