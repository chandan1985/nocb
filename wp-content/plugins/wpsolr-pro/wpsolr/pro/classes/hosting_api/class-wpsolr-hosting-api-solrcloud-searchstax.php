<?php

namespace wpsolr\pro\classes\hosting_api;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_Abstract;

class WPSOLR_Hosting_Api_Solrcloud_Searchstax extends WPSOLR_Hosting_Api_Abstract {

	const HOSTING_API_ID = 'searchstax_solrcloud';

	/**
	 * @inheritDoc
	 */
	public function get_latest_version() {
		return '21.4';
	}

	/**
	 * @inheritdoc
	 */
	public function get_search_engine() {
		return WPSOLR_AbstractEngineClient::ENGINE_SOLR_CLOUD;
	}

	/**
	 * @inheritdoc
	 */
	public function get_label() {
		return 'SearchStax';
	}

	/**
	 * @inheritdoc
	 */
	public function get_url() {
		return 'https://www.searchstax.com/pricing/';
	}

	/**
	 * @inheritDoc
	 */
	public function get_documentation_url() {
		return 'https://www.wpsolr.com/guide/configuration-step-by-step-schematic/configure-your-indexes/create-a-searchstax-solr-cloud-manager-index/';
	}

	/**
	 * @inheritdoc
	 */
	public function get_data_by_id( $label, $id, $default, $parameters = [] ) {

		switch ( $label ) {

			case self::DATA_PORT:

				$result = '443';
				break;

			case self::DATA_PATH:

				$result = sprintf( '/solr/%s', $id );
				break;

			case self::DATA_SCHEME:

				$result = 'https';
				break;

			default:
				$result = $default;
				break;
		}

		return $result;
	}

	/**
	 * @inheritdoc
	 */
	public function get_ui_fields_child() {

		$result = [
			static::FIELD_NAME_FIELDS_INDEX_LABEL_DEFAULT,
			[
				self::FIELD_NAME_FIELDS_INDEX_ENDPOINT => [
					self::FIELD_NAME_LABEL                 => 'Deployment Solr HTTP Endpoint',
					self::FIELD_NAME_PLACEHOLDER           => 'Copy your Deployment Solr HTTP Endpoint here',
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'your Deployment Solr HTTP Endpoint here',
					],
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
				]
			],
			static::FIELD_NAME_FIELDS_INDEX_SOLR_CLOUD_MAX_SHARDS_NODE_DEFAULT,
			static::FIELD_NAME_FIELDS_INDEX_SOLR_CLOUD_REPLICATION_FACTOR_DEFAULT,
			static::FIELD_NAME_FIELDS_INDEX_SOLR_CLOUD_SHARDS_DEFAULT,
			[
				self::FIELD_NAME_FIELDS_INDEX_KEY => [
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_NOT_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::ERROR_LABEL_EMPTY,
					],
					self::FIELD_NAME_LABEL                 => 'Deployment Security Auth User',
					self::FIELD_NAME_PLACEHOLDER           => 'Optional security user if the index is protected with Http Basic Authentication',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => false,
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_SECRET => [
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_NOT_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::ERROR_LABEL_EMPTY,
					],
					self::FIELD_NAME_LABEL                 => 'Deployment Security Auth ',
					self::FIELD_NAME_PLACEHOLDER           => 'Optional security password if the index is protected with Http Basic Authentication',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => false,
				],
			],
		];

		return $result;
	}

}