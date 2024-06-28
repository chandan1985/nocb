<?php

namespace wpsolr\pro\classes\hosting_api;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_Abstract;

class WPSOLR_Hosting_Api_Amazon_CloudSearch extends WPSOLR_Hosting_Api_Abstract {

	const HOSTING_API_ID = 'amazon_cloudsearch';

	/**
	 * @inheritdoc
	 */
	public function get_is_no_hosting() {
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function get_label() {
		return 'Amazon CloudSearch';
	}

	/**
	 * @inheritdoc
	 */
	public function get_url() {
		return 'https://aws.amazon.com/cloudsearch/';
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
	public function get_search_engine() {
		return WPSOLR_AbstractEngineClient::ENGINE_AMAZON_CLOUDSEARCH;
	}

	/**
	 * @inheritdoc
	 */
	public function get_ui_fields_child() {

		$result = [];

		return $result;
	}
}
