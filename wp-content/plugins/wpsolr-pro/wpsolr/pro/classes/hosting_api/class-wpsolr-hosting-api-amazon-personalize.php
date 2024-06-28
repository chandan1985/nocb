<?php

namespace wpsolr\pro\classes\hosting_api;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_Abstract;

class WPSOLR_Hosting_Api_Amazon_Personalize extends WPSOLR_Hosting_Api_Abstract {

	const HOSTING_API_ID = 'amazon_personalize';

	/**
	 * @param array $current_index
	 * @param string $menu
	 *
	 * @return string
	 */
	public function get_account_dashboard_url( $current_index, $menu ) {
		switch ( $menu ) {
			case 'campaigns':
				$url = sprintf( '%s/campaigns/', $this->_get_aws_group_home_url( $current_index ) );
				break;

			case 'solutions':
				$url = sprintf( '%s/solutionsAndRecipes/', $this->_get_aws_group_home_url( $current_index ) );
				break;
		}

		return $url ?? '';
	}

	/**
	 * @param array $current_index
	 *
	 * @return string
	 */
	protected function _get_aws_group_home_url( $current_index ) {
		$index_dataset_group_arn = $current_index[ static::FIELD_NAME_FIELDS_INDEX_DATASET_GROUP_ARN ];
		$index_aws_region        = $current_index[ static::FIELD_NAME_FIELDS_INDEX_AWS_REGION ];

		return sprintf( 'https://%s.console.aws.amazon.com/personalize/home?region=%s#%s', $index_aws_region, $index_aws_region, str_replace( '/', '$', $index_dataset_group_arn ) );
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
		return 'Amazon AWS';
	}

	/**
	 * @inheritdoc
	 */
	public function get_url() {
		return 'https://aws.amazon.com/personalize/';
	}

	/**
	 * @inheritDoc
	 */
	public function get_documentation_url() {
		return 'https://www.wpsolr.com/guide/configuration-step-by-step-schematic/configure-your-indexes/create-an-amazon-personalize-index/';
	}

	/**
	 * @inheritdoc
	 */
	public function get_search_engine() {
		return WPSOLR_AbstractEngineClient::ENGINE_AMAZON_PERSONALIZE;
	}

	/**
	 * @inheritdoc
	 */
	public function get_ui_fields_child() {

		$result = [
			[
				self::FIELD_NAME_FIELDS_INDEX_LABEL => [
					self::FIELD_NAME_LABEL                 => 'Dataset group name',
					self::FIELD_NAME_PLACEHOLDER           => 'The dataset group name must have 1-63 characters with no spaces. Valid characters: a-z, A-Z, 0-9, and _ - (hyphen).',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_ENTER . 'a dataset group',
					],
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_KEY => [
					self::FIELD_NAME_LABEL                 => 'AWS access key ID',
					self::FIELD_NAME_PLACEHOLDER           => 'Optional aws access key id here if the domain/index is protected with an AWS account',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => false,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_NOT_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::ERROR_LABEL_EMPTY,
					],
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_SECRET => [
					self::FIELD_NAME_LABEL                 => 'AWS secret access key',
					self::FIELD_NAME_PLACEHOLDER           => 'Optional aws secret access key here if the domain/index is protected with an AWS account',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => false,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_NOT_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::ERROR_LABEL_EMPTY,
					],
				],
			],
			self::FIELD_NAME_FIELDS_INDEX_AWS_REGION_DEFAULT,
			[
				self::FIELD_NAME_FIELDS_INDEX_DATASET_GROUP_ARN => [
					self::FIELD_NAME_LABEL                 => 'Dataset Group Arn',
					self::FIELD_NAME_PLACEHOLDER           => 'Will be set automatically at the index creation',
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_READONLY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::ERROR_LABEL_EMPTY,
					],
					self::FIELD_NAME_FORMAT_IS_UPDATE_ONLY => true,
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_DATASET_ITEMS_ARN => [
					self::FIELD_NAME_LABEL                 => 'Dataset Items Arn',
					self::FIELD_NAME_PLACEHOLDER           => 'Will be set automatically at the index creation',
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_READONLY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::ERROR_LABEL_EMPTY,
					],
					self::FIELD_NAME_FORMAT_IS_UPDATE_ONLY => true,
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_DATASET_EVENTS_ARN => [
					self::FIELD_NAME_LABEL                 => 'Dataset Events Arn',
					self::FIELD_NAME_PLACEHOLDER           => 'Will be set automatically at the index creation',
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_READONLY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::ERROR_LABEL_EMPTY,
					],
					self::FIELD_NAME_FORMAT_IS_UPDATE_ONLY => true,
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_DATASET_USERS_ARN => [
					self::FIELD_NAME_LABEL                 => 'Dataset Users Arn',
					self::FIELD_NAME_PLACEHOLDER           => 'Will be set automatically at the index creation',
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_READONLY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::ERROR_LABEL_EMPTY,
					],
					self::FIELD_NAME_FORMAT_IS_UPDATE_ONLY => true,
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
				],
			],
		];

		return $result;
	}
}
