<?php

namespace wpsolr\pro\classes\hosting_api;

use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_Vespa_None;

class WPSOLR_Hosting_Api_Vespa_Cloud extends WPSOLR_Hosting_Api_Vespa_None {

	const HOSTING_API_ID = 'vespa_cloud';

	/**
	 * @inheritDoc
	 */
	public function get_is_disabled() {
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function get_label() {
		return 'Vespa Cloud';
	}

	/**
	 * @inheritdoc
	 */
	public function get_url() {
		return 'https://cloud.vespa.ai/';
	}

	/**
	 * @inheritDoc
	 */
	public function get_documentation_url() {
		return 'https://www.wpsolr.com/guide/configuration-step-by-step-schematic/configure-your-indexes/create-a-vespa-cloud-index/';
	}

	/**
	 * @inheritdoc
	 */
	public function get_ui_fields_child() {

		$result = [
			static::FIELD_NAME_FIELDS_INDEX_LABEL_DEFAULT,
			[
				self::FIELD_NAME_FIELDS_INDEX_ENDPOINT => [
					self::FIELD_NAME_LABEL                 => 'Cluster URL',
					self::FIELD_NAME_PLACEHOLDER           => 'Copy your WCS cluster URL here, like https://my-cluster.semi.technology',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'your WCS cluster URL here',
					],
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_KEY => [
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_ENTER . 'the account E-mail',
					],
					self::FIELD_NAME_LABEL                 => 'Account E-mail',
					self::FIELD_NAME_PLACEHOLDER           => 'Copy your account E-mail here',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => false,
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_SECRET => [
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_ENTER . 'the account password',
					],
					self::FIELD_NAME_LABEL                 => 'Account password',
					self::FIELD_NAME_PLACEHOLDER           => 'Copy your account password here',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => false,
				],
			],
			self::FIELD_NAME_FIELDS_INDEX_TOKEN_DEFAULT,
		];

		return $result;
	}

}