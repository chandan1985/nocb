<?php

namespace wpsolr\pro\classes\hosting_api;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_Abstract;

class WPSOLR_Hosting_Api_Elasticsearch_Elasticpress extends WPSOLR_Hosting_Api_Abstract {

	const HOSTING_API_ID = 'elasticpress_es';

	/**
	 * @return string
	 */
	public function get_latest_version() {
		return '21.8';
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
	public function get_data_by_id( $label, $id, $default, $parameters = [] ) {
		$result = $default;

		switch ( $label ) {

			case self::DATA_INDEX_LABEL:
				//Index label must be created as subscription_id-index_label
				$result = trim( sprintf( '%s-%s', trim( $id['user_name'] ), $this->escape_index_label( $id['index_label'] ) ) );

				break;
		}

		return $result;
	}

	/**
	 * @inheritdoc
	 */
	public function get_label() {
		return 'Elasticpress';
	}

	/**
	 * @inheritdoc
	 */
	public function get_url() {
		return 'https://elasticpress.io';
	}

	/**
	 * @inheritdoc
	 */
	public function get_documentation_url() {
		return 'https://www.wpsolr.com/guide/configuration-step-by-step-schematic/configure-your-indexes/create-an-elasticpress-io-index/';
	}

	/**
	 * @inheritdoc
	 */
	public function get_ui_fields_child() {

		$result = [
			static::FIELD_NAME_FIELDS_INDEX_LABEL_DEFAULT,
			static::FIELD_NAME_FIELDS_INDEX_ENDPOINT_DEFAULT,
			[
				self::FIELD_NAME_FIELDS_INDEX_KEY => [
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_ENTER . 'the Subscription ID of your Elasticpress.io account',
					],
					self::FIELD_NAME_LABEL                 => 'Subscription ID',
					self::FIELD_NAME_PLACEHOLDER           => 'Copy your account Subscription ID here',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_SECRET => [
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_ENTER . 'the Token of your Elasticpress.io account',
					],
					self::FIELD_NAME_LABEL                 => 'Token',
					self::FIELD_NAME_PLACEHOLDER           => 'Copy your account TOKEN here',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => false,
				],
			],
		];

		return $result;
	}

}