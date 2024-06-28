<?php

namespace wpsolr\pro\classes\engines\elastic_site_search_php;

use Elastic\SiteSearch\Client\Client;
use wpsolr\core\classes\engines\elasticsearch_php\WPSOLR_IndexElasticsearchClient;
use wpsolr\core\classes\WpSolrSchema;

/**
 * @property Client $search_engine_client
 */
class WPSOLR_Index_Elastic_Site_Search_Client extends WPSOLR_IndexElasticsearchClient {
	use WPSOLR_Elastic_Site_Search_Client;

	/**
	 * @var string[]
	 */
	protected static $_cache_map_fields_types = [];

	/**
	 * @inheritdoc
	 */
	protected function search_engine_client_get_count_document( $site_id = '' ) {

		$bool_queries = [];

		// Filter out the attachment document
		$bool_queries['must_not'] = [ 'term' => [ WpSolrSchema::_FIELD_NAME_INTERNAL_ID => $this->WPSOLR_DOC_ID_ATTACHMENT ] ];

		if ( ! empty( $site_id ) ) {

			$bool_queries['must'] = [ 'term' => [ WpSolrSchema::_FIELD_NAME_BLOG_NAME_STR => $site_id ] ];
		}

		$params = $this->_create_bool_query( $bool_queries );

		$nb_documents = $this->search_engine_client->search( $this->get_index_label(), '', null );

		$total = 0;
		foreach ( $nb_documents['info'] as $type_info ) {
			$total += $type_info['total_result_count'];
		}

		return $total;
	}

	/**
	 * @param array[] $documents
	 *
	 * @return int|mixed
	 * @throws \Exception
	 */
	public function send_posts_or_attachments_to_solr_index( $documents, $is_error = false ) {

		$formatted_docs = $this->search_engine_client_prepare_documents_for_update( $documents );

		try {

			$response = $this->search_engine_client->createOrUpdateDocuments(
				$this->get_index_label(), $this->_get_index_doc_type(), $formatted_docs
			);

		} catch ( \Exception $e ) {

			if ( ! $is_error && $this->_try_to_fix_error_doc_type( $e->getMessage(), $documents, $formatted_docs ) ) {

				// Retry once
				return $this->send_posts_or_attachments_to_solr_index( $documents, true );
			}

			throw $e;
		}

		if ( $this->_has_error( $response )
		) {

			$error_msg = $this->_get_error( $response );
			if ( ! $is_error && $this->_try_to_fix_error_doc_type( $error_msg ) ) {

				// Retry once
				return $this->send_posts_or_attachments_to_solr_index( $documents, true );
			}

			throw new \Exception( $error_msg );
		}

		return true;
	}

	/**
	 * @param array $response
	 *
	 * @return bool
	 */
	protected function _has_error( $response ) {

		foreach ( $response as $response_value ) {
			if ( is_string( $response_value ) ) {
				// String error message. Should be the bool 'true'
				return true;
			}
		}

		return false;
	}

	/**
	 * @param array $response
	 *
	 * @return string
	 */
	protected function _get_error( $response ) {

		if ( $this->_has_error( $response ) ) {
			foreach ( $response as $response_value ) {
				if ( is_string( $response_value ) ) {
					// String error message. Show only the first error in the batch.
					return sprintf( 'Error message from Swiftype API: "%s"', $response_value );
				}
			}
		}

		return '';
	}

	/**
	 * https://swiftype.com/documentation/site-search/guides/schema-design
	 * @param array $documents
	 */
	protected function search_engine_client_prepare_documents_for_update( array $documents ) {

		$formatted_documents = [];

		foreach ( $documents as $document ) {

			$formatted_document                = [];
			$formatted_document['external_id'] = $document['id'];
			$formatted_document['fields']      = [];
			foreach ( $document as $field_name => $field_value ) {

				if ( 'id' !== $field_name ) {
					// Not 'id', as it is a reserved Swiftype name

					$type = 'string';
					if ( isset( static::$_cache_map_fields_types[ $field_name ] ) ) {

						$type = static::$_cache_map_fields_types[ $field_name ];

					} else {

						switch ( WpSolrSchema::get_custom_field_dynamic_type( $field_name ) ) {
							case WpSolrSchema::_SOLR_DYNAMIC_TYPE_TEXT:
								$type = 'text';
								break;

							case WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING:
							case WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING1:
							case WpSolrSchema::_SOLR_DYNAMIC_TYPE_S:
								$type = 'string';
								break;

							case WpSolrSchema::_SOLR_DYNAMIC_TYPE_INTEGER:
							case WpSolrSchema::_SOLR_DYNAMIC_TYPE_INTEGER_LONG:
								$type = 'integer';
								break;

							case WpSolrSchema::_SOLR_DYNAMIC_TYPE_FLOAT:
							case WpSolrSchema::_SOLR_DYNAMIC_TYPE_FLOAT_DOUBLE:
								$type = 'float';
								break;

							case WpSolrSchema::_SOLR_DYNAMIC_TYPE_DATE:
								$type = 'date';
								break;
						}

						// Add to cache
						static::$_cache_map_fields_types[ $field_name ] = $type;
					}

					if ( ! is_array( $field_value ) || ! empty( $field_value ) ) {
						// Empty array are converted as {} and cause the error:
						// "Invalid field value: Don't know how to safely convert '{}' of class ActionController::Parameters to a String"

						$formatted_document['fields'][] = [
							'name'  => $field_name,
							'value' => $field_value,
							'type'  => $type
						];
					}
				}
			}

			$formatted_documents[] = $formatted_document;
		}

		return $formatted_documents;
	}


	/**
	 * @inheritdoc
	 */
	protected function search_engine_client_delete_all_documents( $post_types = null, $site_id = '' ) {

		try {

			$this->search_engine_client->deleteDocumentType( $this->get_index_label(), $this->_get_index_doc_type() );

		} catch ( \Exception $e ) {

			if ( false !== strpos( $e->getMessage(), 'Record not found' ) ) {
				// False error, just continue

			} else {
				// Real error
				throw $e;
			}
		}

	}

}
