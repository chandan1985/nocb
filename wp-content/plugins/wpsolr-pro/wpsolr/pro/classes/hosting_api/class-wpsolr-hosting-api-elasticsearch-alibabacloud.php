<?php

namespace wpsolr\pro\classes\hosting_api;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_Abstract;

/**
 * Ticket https://workorder-intl.console.aliyun.com/#/ticket/detail/?ticketId=ET1FXK3P to open Elasticsearch service to internet
 */
class WPSOLR_Hosting_Api_Elasticsearch_Alibabacloud extends WPSOLR_Hosting_Api_Abstract {

	const HOSTING_API_ID = 'alibabacloud_es';

	/**
	 * @inheritDoc
	 */
	public function get_latest_version() {
		return '21.5';
	}

	/**
	 * @inheritdoc
	 */
	public function get_label() {
		return 'Alibaba Cloud';
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
		return 'https://www.alibabacloud.com/product/elasticsearch';
	}

	/**
	 * @inheritDoc
	 */
	public function get_documentation_url() {
		return 'https://www.wpsolr.com/guide/configuration-step-by-step-schematic/configure-your-indexes/create-an-alibaba-cloud-elasticsearch-index/';
	}

	/**
	 * @inheritdoc
	 */
	public function get_ui_fields_child() {

		$result = [
			static::FIELD_NAME_FIELDS_INDEX_LABEL_DEFAULT,
			static::FIELD_NAME_FIELDS_INDEX_PROTOCOL_DEFAULT,
			static::FIELD_NAME_FIELDS_INDEX_HOST_DEFAULT,
			[
				self::FIELD_NAME_FIELDS_INDEX_PORT => [
					self::FIELD_NAME_LABEL                 => 'Port',
					self::FIELD_NAME_PLACEHOLDER           => '9200 is the default port with http. Or 443 with https. Or any other port.',
					self::FIELD_NAME_DEFAULT_VALUE         => '9200',
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_INTEGER_MINIMUM_2_DIGITS,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_ENTER . 'a valid port',
					],
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => false,
				],
			],
			static::FIELD_NAME_FIELDS_INDEX_ELASTICSEARCH_SHARDS_DEFAULT,
			static::FIELD_NAME_FIELDS_INDEX_ELASTICSEARCH_REPLICAS_DEFAULT,
			[
				self::FIELD_NAME_FIELDS_INDEX_KEY => [
					self::FIELD_NAME_LABEL                 => 'User',
					self::FIELD_NAME_PLACEHOLDER           => 'Copy your User here',
					self::FIELD_NAME_DEFAULT_VALUE         => 'elastic',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => false,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_ENTER . 'the User of your cluster',
					],
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_SECRET => [
					self::FIELD_NAME_LABEL                 => 'Password',
					self::FIELD_NAME_PLACEHOLDER           => 'Copy your Password here',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => false,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_ENTER . 'the Password of your cluster',
					],
				],
			],
		];

		return $result;
	}
}