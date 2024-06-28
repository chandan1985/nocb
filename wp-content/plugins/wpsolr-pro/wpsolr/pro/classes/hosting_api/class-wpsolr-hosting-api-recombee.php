<?php

namespace wpsolr\pro\classes\hosting_api;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_Abstract;

class WPSOLR_Hosting_Api_Recombee extends WPSOLR_Hosting_Api_Abstract {

	const HOSTING_API_ID = 'recombee';

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
		return 'Recombee';
	}

	/**
	 * @inheritdoc
	 */
	public function get_url() {
		return 'https://www.recombee.com/';
	}

	/**
	 * @inheritDoc
	 */
	public function get_documentation_url() {
		return 'https://www.wpsolr.com/guide/configuration-step-by-step-schematic/configure-your-indexes/create-a-recombee-index/';
	}

	/**
	 * @inheritdoc
	 */
	public function get_search_engine() {
		return WPSOLR_AbstractEngineClient::ENGINE_RECOMBEE;
	}

	/**
	 * @inheritdoc
	 */
	public function get_ui_fields_child() {

		$result = [
			[
				self::FIELD_NAME_FIELDS_INDEX_KEY => [
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_ENTER . 'the API Identifier found in your https://admin.recombee.com/databases/{{database-name}}/settings',
					],
					self::FIELD_NAME_LABEL                 => 'API Identifier',
					self::FIELD_NAME_PLACEHOLDER           => 'Copy your API Identifier. This is the same as your database name.',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_SECRET => [
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_ENTER . 'the API Private Token found in your https://admin.recombee.com/databases/{{database-name}}/settings',
					],
					self::FIELD_NAME_LABEL                 => 'API Private Token',
					self::FIELD_NAME_PLACEHOLDER           => 'Copy your API Private Token here',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
				],
			],
		];

		return $result;
	}
}
