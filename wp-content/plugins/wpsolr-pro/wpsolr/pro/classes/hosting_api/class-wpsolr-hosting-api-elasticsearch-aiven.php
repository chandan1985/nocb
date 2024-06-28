<?php

namespace wpsolr\pro\classes\hosting_api;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_Abstract;

class WPSOLR_Hosting_Api_Elasticsearch_Aiven extends WPSOLR_Hosting_Api_Abstract {

	const HOSTING_API_ID = 'aiven_es';

	/**
	 * @inheritDoc
	 */
	public function get_latest_version() {
		return '21.4';
	}


	/**
	 * @inheritdoc
	 */
	public function get_label() {
		return 'Aiven';
	}

	/**
	 * @inheritdoc
	 */
	public function get_search_engine() {
		return WPSOLR_AbstractEngineClient::ENGINE_ELASTICSEARCH;
	}

	/**
	 * @inheritdoc
	 */
	public function get_url() {
		return 'https://aiven.io/elasticsearch';
	}

	/**
	 * @inheritDoc
	 */
	public function get_documentation_url() {
		return 'https://www.wpsolr.com/guide/configuration-step-by-step-schematic/configure-your-indexes/create-an-aiven-elasticsearch-index/';
	}

	/**
	 * @inheritdoc
	 */
	public function get_is_endpoint_only() {
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function get_ui_fields_child() {

		$result = [
			static::FIELD_NAME_FIELDS_INDEX_LABEL_DEFAULT,
			static::FIELD_NAME_FIELDS_INDEX_ELASTICSEARCH_SHARDS_DEFAULT,
			static::FIELD_NAME_FIELDS_INDEX_ELASTICSEARCH_REPLICAS_DEFAULT,
			[
				self::FIELD_NAME_FIELDS_INDEX_ENDPOINT => [
					self::FIELD_NAME_LABEL                 => 'Service URL',
					self::FIELD_NAME_PLACEHOLDER           => 'Copy your Service URL here',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'your Service URL here',
					],
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_KEY => [
					self::FIELD_NAME_LABEL                 => 'User',
					self::FIELD_NAME_PLACEHOLDER           => 'User here copied from the service connection parameters',
					self::FIELD_NAME_FORMAT_IS_UPDATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'the User here copied from the service connection parameters',
					],
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_SECRET => [
					self::FIELD_NAME_LABEL                 => 'Password',
					self::FIELD_NAME_PLACEHOLDER           => 'Password here copied from the service connection parameters',
					self::FIELD_NAME_FORMAT_IS_UPDATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'the Password here copied from the service connection parameters',
					],
				],
			],
		];

		return $result;
	}

}