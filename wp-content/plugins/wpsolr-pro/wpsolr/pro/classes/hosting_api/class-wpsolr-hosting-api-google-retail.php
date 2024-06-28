<?php

namespace wpsolr\pro\classes\hosting_api;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_Abstract;

class WPSOLR_Hosting_Api_Google_Retail extends WPSOLR_Hosting_Api_Abstract {

	const HOSTING_API_ID = 'google_retail';

	/**
	 * @inerhitDoc
	 */
	public function get_is_endpoint_only() {
		return true;
	}

	/**
	 * @param array $current_index
	 * @param string $menu
	 *
	 * @return string
	 */
	public function get_account_dashboard_url( $current_index, $menu ) {
		return sprintf( $this->_get_google_retail_project_home_url( $current_index ), $menu );
	}

	/**
	 * @param array $current_index
	 *
	 * @return string
	 */
	protected function _get_google_retail_project_home_url( $current_index ) {
		$project_id = json_decode( $current_index['index_key_json'], true )['project_id'];

		return sprintf( 'https://console.cloud.google.com/ai/retail/catalogs/default_catalog/%%s?project=%s', $project_id );
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
		return 'Google Cloud';
	}

	/**
	 * @inheritdoc
	 */
	public function get_url() {
		return 'https://cloud.google.com/solutions/retail-product-discovery';
	}

	/**
	 * @inheritDoc
	 */
	public function get_documentation_url() {
		return 'https://www.wpsolr.com/guide/configuration-step-by-step-schematic/configure-your-indexes/create-a-google-retail-index/';
	}

	/**
	 * @inheritdoc
	 */
	public function get_search_engine() {
		return WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL;
	}

	/**
	 * @inheritdoc
	 */
	public function get_ui_fields_child() {

		$result = [
			[
				self::FIELD_NAME_FIELDS_INDEX_KEY_JSON => [
					self::FIELD_NAME_LABEL                 => 'Service account JSON key of the Google Project you authorized the Retail API',
					self::FIELD_NAME_PLACEHOLDER           => 'Service account JSON key',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => false,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_ENTER . 'your service account JSON key',
					],
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_CATALOG_BRANCH => [
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => 'Please choose a branch of your catalog in: 0, 1, 2',
					],
					self::FIELD_NAME_DEFAULT_VALUE         => '0',
					self::FIELD_NAME_LABEL                 => 'Branch',
					self::FIELD_NAME_PLACEHOLDER           => '0, 1, 2',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_LANGUAGE_CODE => [
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => 'Please choose a language',
					],
					self::FIELD_NAME_DEFAULT_VALUE         => 'en-US',
					self::FIELD_NAME_LABEL                 => <<<'EOF'
Language of the title/description and other string attributes.
<br>Use language tags defined by <a href="https://www.rfc-editor.org/rfc/bcp/bcp47.txt" target="_new">BCP 47</a>.
EOF
					,
					self::FIELD_NAME_PLACEHOLDER           => 'en-US',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
				],
			],
		];

		return $result;
	}
}