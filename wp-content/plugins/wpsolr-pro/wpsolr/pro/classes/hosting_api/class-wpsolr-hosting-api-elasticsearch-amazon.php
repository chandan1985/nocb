<?php

namespace wpsolr\pro\classes\hosting_api;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_Abstract;

class WPSOLR_Hosting_Api_Elasticsearch_Amazon extends WPSOLR_Hosting_Api_Abstract {

	const HOSTING_API_ID = 'amazon_es';

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
		return 'Amazon AWS';
	}

	/**
	 * @inheritdoc
	 */
	public function get_url() {
		return 'https://aws.amazon.com/elasticsearch-service/';
	}

	/**
	 * @inheritDoc
	 */
	public function get_documentation_url() {
		return 'https://www.wpsolr.com/guide/configuration-step-by-step-schematic/configure-your-indexes/create-an-amazon-elasticsearch-index/';
	}

	/**
	 * @inheritdoc
	 */
	public function get_ui_fields_child() {

		$result = [
			static::FIELD_NAME_FIELDS_INDEX_LABEL_DEFAULT,
			static::FIELD_NAME_FIELDS_INDEX_ELASTICSEARCH_SHARDS_DEFAULT,
			static::FIELD_NAME_FIELDS_INDEX_ELASTICSEARCH_REPLICAS_DEFAULT,
			static::FIELD_NAME_FIELDS_INDEX_ENDPOINT_DEFAULT,
			[
				self::FIELD_NAME_FIELDS_INDEX_KEY => [
					self::FIELD_NAME_LABEL                 => 'AWS access key ID',
					self::FIELD_NAME_PLACEHOLDER           => 'Optional aws access key id here if the domain/index is protected with an AWS account',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => false,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_NOT_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::ERROR_LABEL_EMPTY,
					],
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_SECRET => [
					self::FIELD_NAME_LABEL                 => 'AWS secret access key',
					self::FIELD_NAME_PLACEHOLDER           => 'Optional aws secret access key here if the domain/index is protected with an AWS account',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => false,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_NOT_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::ERROR_LABEL_EMPTY,
					],
				],
			],
			self::FIELD_NAME_FIELDS_INDEX_AWS_REGION_DEFAULT,
		];

		return $result;
	}

}