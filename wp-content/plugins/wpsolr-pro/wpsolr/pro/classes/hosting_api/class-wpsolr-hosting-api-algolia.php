<?php

namespace wpsolr\pro\classes\hosting_api;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_Abstract;

class WPSOLR_Hosting_Api_Algolia extends WPSOLR_Hosting_Api_Abstract {

	const HOSTING_API_ID = 'algolia';

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
		return 'Algolia';
	}

	/**
	 * @inheritdoc
	 */
	public function get_url() {
		return 'https://www.algolia.com/';
	}

	/**
	 * @inheritDoc
	 */
	public function get_documentation_url() {
		return 'https://www.wpsolr.com/guide/configuration-step-by-step-schematic/configure-your-indexes/create-an-algolia-index/';
	}

	/**
	 * @inheritdoc
	 */
	public function get_search_engine() {
		return WPSOLR_AbstractEngineClient::ENGINE_ALGOLIA;
	}

	/**
	 * @inheritdoc
	 */
	public function get_ui_fields_child() {

		$result = [
			static::FIELD_NAME_FIELDS_INDEX_LABEL_DEFAULT,
			[
				self::FIELD_NAME_FIELDS_INDEX_KEY => [
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_ENTER . 'the Application ID found in https://www.algolia.com/api-keys',
					],
					self::FIELD_NAME_LABEL                 => 'Application ID',
					self::FIELD_NAME_PLACEHOLDER           => 'Copy your Application ID here',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_SECRET => [
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_ENTER . 'the Admin API Key found in https://www.algolia.com/api-keys',
					],
					self::FIELD_NAME_LABEL                 => 'Admin API Key',
					self::FIELD_NAME_PLACEHOLDER           => 'Copy your Admin API Key here',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => false,
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_PUBLIC_KEY => [
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_NOT_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_ENTER . 'the Search-Only API Key found in https://www.algolia.com/api-keys',
					],
					self::FIELD_NAME_LABEL                 => 'Search-Only API Key',
					self::FIELD_NAME_PLACEHOLDER           => 'Copy your Search-Only API Key here',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => false,
				],
			],
		];

		return $result;
	}
}
