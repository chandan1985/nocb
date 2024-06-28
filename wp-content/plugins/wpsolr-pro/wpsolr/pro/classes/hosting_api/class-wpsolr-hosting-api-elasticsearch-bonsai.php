<?php

namespace wpsolr\pro\classes\hosting_api;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_Abstract;

class WPSOLR_Hosting_Api_Elasticsearch_Bonsai extends WPSOLR_Hosting_Api_Abstract {

	const HOSTING_API_ID = 'bonsai_es';

	/**
	 * @inheritDoc
	 */
	public function get_latest_version() {
		return '22.0';
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
		return 'Bonsai';
	}

	/**
	 * @inheritdoc
	 */
	public function get_url() {
		return 'https://bonsai.io/';
	}

	/**
	 * @inheritDoc
	 */
	public function get_documentation_url() {
		return 'https://www.wpsolr.com/guide/configuration-step-by-step-schematic/configure-your-indexes/create-a-bonsai-elasticsearch-index/';
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
					self::FIELD_NAME_LABEL                 => 'Cluster url',
					self::FIELD_NAME_PLACEHOLDER           => 'Copy your cluster url here',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'your cluster url here',
					],
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_KEY => [
					self::FIELD_NAME_LABEL                 => 'Access Key',
					self::FIELD_NAME_PLACEHOLDER           => 'Access Key here copied from the cluster access tab',
					self::FIELD_NAME_FORMAT_IS_UPDATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'the Access Key here copied from the cluster access tab',
					],
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_SECRET => [
					self::FIELD_NAME_LABEL                 => 'Access Secret',
					self::FIELD_NAME_PLACEHOLDER           => 'Access Secret here copied from the cluster access tab',
					self::FIELD_NAME_FORMAT_IS_UPDATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'the Access Secret here copied from the cluster access tab',
					],
				],
			],
		];

		return $result;
	}
}