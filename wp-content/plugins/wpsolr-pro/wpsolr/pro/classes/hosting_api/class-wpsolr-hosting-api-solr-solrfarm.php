<?php

namespace wpsolr\pro\classes\hosting_api;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_Abstract;

class WPSOLR_Hosting_Api_Solr_Solrfarm extends WPSOLR_Hosting_Api_Abstract {

	const HOSTING_API_ID = 'solrfarm_solr';

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
		return WPSOLR_AbstractEngineClient::ENGINE_SOLR;
	}

	/**
	 * @inheritdoc
	 */
	public function get_label() {
		return 'SolrFarm';
	}

	/**
	 * @inheritDoc
	 */
	public function get_documentation_url() {
		return 'https://www.wpsolr.com/guide/configuration-step-by-step-schematic/configure-your-indexes/create-a-solrfarm-solr-index/';
	}

	/**
	 * @inheritdoc
	 */
	public function get_url() {
		return 'https://solrfarm.com/';
	}

	/**
	 * @inheritdoc
	 */
	public function get_is_endpoint_only() {
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function get_ui_fields_child() {

		$result = [
			[
				self::FIELD_NAME_FIELDS_INDEX_ENDPOINT => [
					self::FIELD_NAME_LABEL                 => 'Deployment Solr HTTP Endpoint',
					self::FIELD_NAME_PLACEHOLDER           => 'Copy your Deployment Solr HTTP Endpoint here',
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'your Deployment Solr HTTP Endpoint here',
					],
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
				]
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_KEY => [
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_NOT_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::ERROR_LABEL_EMPTY,
					],
					self::FIELD_NAME_LABEL                 => 'User',
					self::FIELD_NAME_PLACEHOLDER           => 'Optional security user if the index is protected with Http Basic Authentication',
					self::FIELD_NAME_FORMAT_IS_UPDATE_ONLY => true,
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_SECRET => [
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_NOT_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::ERROR_LABEL_EMPTY,
					],
					self::FIELD_NAME_LABEL                 => 'Password',
					self::FIELD_NAME_PLACEHOLDER           => 'Optional security password if the index is protected with Http Basic Authentication',
					self::FIELD_NAME_FORMAT_IS_UPDATE_ONLY => true,
				],
			],
		];

		return $result;
	}


}