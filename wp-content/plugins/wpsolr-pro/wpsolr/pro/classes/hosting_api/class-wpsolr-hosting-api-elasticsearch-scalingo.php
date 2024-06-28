<?php

namespace wpsolr\pro\classes\hosting_api;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_Abstract;

/**
 * No Certificate Authority for SSL
 */
class WPSOLR_Hosting_Api_Elasticsearch_Scalingo extends WPSOLR_Hosting_Api_Abstract {

	const HOSTING_API_ID = 'scalingo_es';

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
		return 'Scalingo';
	}

	/**
	 * @inheritdoc
	 */
	public function get_url() {
		return 'https://scalingo.com/databases/elasticsearch';
	}

	/**
	 * @inheritDoc
	 */
	public function get_documentation_url() {
		return 'https://www.wpsolr.com/guide/configuration-step-by-step-schematic/configure-your-indexes/create-a-scalingo-elasticsearch-index/';
	}

	/**
	 * @inheritdoc
	 */
	public function get_is_disabled() {
		return true;
	}

}