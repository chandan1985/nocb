<?php

namespace wpsolr\pro\classes\hosting_api;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_Abstract;

class WPSOLR_Hosting_Api_Elasticsearch_Wodby extends WPSOLR_Hosting_Api_Abstract {

	const HOSTING_API_ID = 'wodby_es';

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
		return 'Wodby';
	}

	/**
	 * @inheritdoc
	 */
	public function get_url() {
		return 'https://cloud.wodby.com/stacks/elasticsearch';
	}

	/**
	 * @inheritDoc
	 */
	public function get_documentation_url() {
		return 'https://www.wpsolr.com/guide/configuration-step-by-step-schematic/configure-your-indexes/create-a-wodby-elasticsearch-index/';
	}

	/**
	 * @inheritdoc
	 */
	public function get_is_disabled() {
		return true;
	}

}