<?php

namespace wpsolr\pro\classes\hosting_api;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_Abstract;

class WPSOLR_Hosting_Api_Elasticsearch_Cloudways extends WPSOLR_Hosting_Api_Abstract {

	const HOSTING_API_ID = 'cloudways_es';

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
		return 'Cloudways';
	}

	/**
	 * @inheritdoc
	 */
	public function get_url() {
		return 'https://www.cloudways.com/en/elasticsearch-hosting.php?id=175652';
	}

	/**
	 * @inheritDoc
	 */
	public function get_documentation_url() {
		return 'https://www.wpsolr.com/guide/configuration-step-by-step-schematic/configure-your-indexes/create-a-cloudways-elasticsearch-index/';
	}

	/**
	 * @inheritdoc
	 */
	public function get_is_disabled() {
		return false;
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
				self::FIELD_NAME_FIELDS_INDEX_PORT => [
					self::FIELD_NAME_LABEL                 => 'Port',
					self::FIELD_NAME_PLACEHOLDER           => '8983 is the default port with http. Or 443 with https. Or any other port.',
					self::FIELD_NAME_DEFAULT_VALUE         => '9200',
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_INTEGER_MINIMUM_2_DIGITS,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_ENTER . 'a valid port',
					],
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
					self::FIELD_NAME_FORMAT_IS_UPDATE_ONLY => true,
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_HOST => [
					self::FIELD_NAME_LABEL                 => 'Host',
					self::FIELD_NAME_PLACEHOLDER           => "localhost or ip adress or hostname. No 'http', no '/', no ':'",
					self::FIELD_NAME_DEFAULT_VALUE         => 'localhost',
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_ENTER . 'an index host',
					],
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
					self::FIELD_NAME_FORMAT_IS_UPDATE_ONLY => true,
				]
			]
		];

		return $result;
	}

}