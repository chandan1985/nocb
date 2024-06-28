<?php

namespace wpsolr\pro\classes\hosting_api;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_Abstract;

class WPSOLR_Hosting_Api_Elasticsearch_Compose extends WPSOLR_Hosting_Api_Abstract {

	const HOSTING_API_ID = 'compose_es';

	/**
	 * @inheritdoc
	 */
	public function get_latest_version() {
		return '21.3';
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
	public function get_label() {
		return 'Compose';
	}

	/**
	 * @inheritdoc
	 */
	public function get_url() {
		return 'https://www.compose.com/databases/elasticsearch';
	}

	/**
	 * @inheritDoc
	 */
	public function get_documentation_url() {
		return 'https://www.wpsolr.com/guide/configuration-step-by-step-schematic/configure-your-indexes/create-a-compose-elasticsearch-index/';
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
					self::FIELD_NAME_LABEL                 => 'HTTP connection',
					self::FIELD_NAME_PLACEHOLDER           => 'Copy your HTTP connection here',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'your HTTP connection here',
					],
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_KEY => [
					self::FIELD_NAME_LABEL                 => 'Portal User Name',
					self::FIELD_NAME_PLACEHOLDER           => 'Portal User Name here copied from the Portal Users menu',
					self::FIELD_NAME_FORMAT_IS_UPDATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'the Portal User Name here copied from the Portal Users menu',
					],
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_SECRET => [
					self::FIELD_NAME_LABEL                 => 'Portal User Password',
					self::FIELD_NAME_PLACEHOLDER           => 'Portal User Password here copied from the Portal Users menu',
					self::FIELD_NAME_FORMAT_IS_UPDATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'the Portal User Password here copied from the Portal Users menu',
					],
				],
			],
		];

		return $result;
	}

}