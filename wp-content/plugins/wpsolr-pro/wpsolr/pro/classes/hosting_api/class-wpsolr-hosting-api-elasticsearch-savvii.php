<?php

namespace wpsolr\pro\classes\hosting_api;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_Abstract;

class WPSOLR_Hosting_Api_Elasticsearch_Savvii extends WPSOLR_Hosting_Api_Abstract {

	const HOSTING_API_ID = 'savvii_es';

	/**
	 * @inheritdoc
	 */
	public function get_is_disabled() {
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function get_label() {
		return 'Savvii';
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
		return 'https://www.savvii.com/elasticsearch-wordpress/';
	}

	/**
	 * @inheritDoc
	 */
	public function get_documentation_url() {
		return 'https://www.wpsolr.com/guide/configuration-step-by-step-schematic/configure-your-indexes/create-a-savvii-elasticsearch-index/';
	}

	/**
	 * @inheritdoc
	 */
	public function get_ui_fields_child() {

		$result = [
			static::FIELD_NAME_FIELDS_INDEX_LABEL_DEFAULT,
			static::FIELD_NAME_FIELDS_INDEX_PROTOCOL_DEFAULT,
			static::FIELD_NAME_FIELDS_INDEX_HOST_DEFAULT,
			static::FIELD_NAME_FIELDS_INDEX_PORT_ELASTICSEARCH_DEFAULT,
			static::FIELD_NAME_FIELDS_INDEX_ELASTICSEARCH_SHARDS_DEFAULT,
			static::FIELD_NAME_FIELDS_INDEX_ELASTICSEARCH_REPLICAS_DEFAULT,
			static::FIELD_NAME_FIELDS_INDEX_KEY_DEFAULT,
			static::FIELD_NAME_FIELDS_INDEX_SECRET_DEFAULT,
		];

		return $result;
	}
}