<?php

namespace wpsolr\pro\classes\hosting_api;

use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_RediSearch_None;

class WPSOLR_Hosting_Api_RediSearch_RedisLabsCloud extends WPSOLR_Hosting_Api_RediSearch_None {

	const HOSTING_API_ID = 'redislabscloud_redisearch';

	/**
	 * @inheritDoc
	 */
	public function get_is_disabled() {
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function get_label() {
		return 'Redis Labs Cloud';
	}

	/**
	 * @inheritdoc
	 */
	public function get_url() {
		return 'https://app.redislabs.com/#/essentials-pro-selection';
	}

	/**
	 * @inheritDoc
	 */
	public function get_documentation_url() {
		return 'https://www.wpsolr.com/guide/configuration-step-by-step-schematic/configure-your-indexes/create-an-elastic-elasticsearch-index/';
	}

}