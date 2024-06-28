<?php

namespace wpsolr\pro\classes\hosting_api;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_Abstract;

class WPSOLR_Hosting_Api_Swiftype extends WPSOLR_Hosting_Api_Abstract {

	const HOSTING_API_ID = 'swiftype';

	/**
	 * @inheritDoc
	 */
	public function get_is_disabled() {
		return true;
	}

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
		return WPSOLR_AbstractEngineClient::ENGINE_SWIFTYPE_NAME;
	}

	/**
	 * @inheritdoc
	 */
	public function get_url() {
		return 'https://app.swiftype.com/settings/account';
	}

	/**
	 * @inheritDoc
	 */
	public function get_documentation_url() {
		return 'https://www.wpsolr.com/guide/configuration-step-by-step-schematic/configure-your-indexes/create-a-swiftype-index/';
	}

	/**
	 * @inheritdoc
	 */
	public function get_search_engine() {
		return WPSOLR_AbstractEngineClient::ENGINE_SWIFTYPE;
	}

	/**
	 * @inheritdoc
	 */
	public function get_ui_fields_child() {

		$result = [
			[
				self::FIELD_NAME_FIELDS_INDEX_LABEL => [
					self::FIELD_NAME_LABEL                 => 'Swiftype Engine name',
					self::FIELD_NAME_PLACEHOLDER           => 'Engine name in Swiftype dashboard, like "myengine" or "my-engine". Only characters and "-", no white spaces.',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_ENTER . 'an engine name',
					],
				]
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_KEY => [
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_ENTER . 'the API key found in your https://app.swiftype.com/settings/account',
					],
					self::FIELD_NAME_LABEL                 => 'API key',
					self::FIELD_NAME_PLACEHOLDER           => 'Copy your API key here',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_LANGUAGE_CODE => [
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_NOT_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::ERROR_LABEL_EMPTY,
					],
					self::FIELD_NAME_LABEL                 => sprintf( 'Language code <br><a href="%s" target="__language">List of supported language codes</a>', 'https://swiftype.com/documentation/site-search/overview#language-optimization' ),
					self::FIELD_NAME_PLACEHOLDER           => 'Enter a language code among the documentation\'s list, or leave empty.',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
				],
			],
		];

		return $result;
	}

}
