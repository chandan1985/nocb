<?php

namespace wpsolr\core\classes\hosting_api;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Algolia;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Amazon_CloudSearch;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Amazon_Personalize;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Elasticsearch_Aiven;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Elasticsearch_Alibabacloud;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Elasticsearch_Amazon;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Elasticsearch_Bonsai;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Elasticsearch_Clevercloud;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Elasticsearch_Cloudways;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Elasticsearch_Compose;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Elasticsearch_Elastic;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Elasticsearch_Elasticpress;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Elasticsearch_Objectrocket;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Elasticsearch_Qbox;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Elasticsearch_Savvii;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Elasticsearch_Scalingo;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Elasticsearch_Searchly;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Elasticsearch_Wodby;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Google_Retail;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_OpenSearch_Aiven;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_OpenSearch_Amazon;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Recombee;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_RediSearch_RedisLabsCloud;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Solr_Opensolr;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Solr_Solrfarm;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Solrcloud_Searchstax;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Swiftype;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Vespa_Cloud;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Weaviate_Semi;

abstract class WPSOLR_Hosting_Api_Abstract extends WPSOLR_Hosting_Api_Abstract_Root {

	protected static function _get_hosting_apis() {
		return array_merge(
			parent::_get_hosting_apis(),
			[
				/** Algolia */
				WPSOLR_Hosting_Api_Algolia::class,
				/** Google Retail */
				WPSOLR_Hosting_Api_Google_Retail::class,
				/** Recombee */
				WPSOLR_Hosting_Api_Recombee::class,
				/** Amazon Personalize */
				WPSOLR_Hosting_Api_Amazon_Personalize::class,
				/** Solr */
				WPSOLR_Hosting_Api_Solr_Opensolr::class,
				WPSOLR_Hosting_Api_Solr_Solrfarm::class,
				/** SolrCloud */
				WPSOLR_Hosting_Api_Solrcloud_Searchstax::class,
				/** Elasticsearch */
				WPSOLR_Hosting_Api_Elasticsearch_Aiven::class,
				WPSOLR_Hosting_Api_Elasticsearch_Alibabacloud::class,
				WPSOLR_Hosting_Api_Elasticsearch_Amazon::class,
				WPSOLR_Hosting_Api_Elasticsearch_Bonsai::class,
				WPSOLR_Hosting_Api_Elasticsearch_Clevercloud::class,
				WPSOLR_Hosting_Api_Elasticsearch_Cloudways::class,
				WPSOLR_Hosting_Api_Elasticsearch_Compose::class,
				WPSOLR_Hosting_Api_Elasticsearch_Elastic::class,
				WPSOLR_Hosting_Api_Elasticsearch_Elasticpress::class,
				WPSOLR_Hosting_Api_Elasticsearch_Objectrocket::class,
				WPSOLR_Hosting_Api_Elasticsearch_Qbox::class,
				WPSOLR_Hosting_Api_Elasticsearch_Savvii::class,
				WPSOLR_Hosting_Api_Elasticsearch_Scalingo::class,
				WPSOLR_Hosting_Api_Elasticsearch_Searchly::class,
				//WPSOLR_Hosting_Api_Elasticsearch_Searchstax::class,
				WPSOLR_Hosting_Api_Elasticsearch_Wodby::class,
				/** OpenSearch */
				WPSOLR_Hosting_Api_OpenSearch_Aiven::class,
				WPSOLR_Hosting_Api_OpenSearch_Amazon::class,
				/** Weaviate */
				WPSOLR_Hosting_Api_Weaviate_Semi::class,
				/** Vespa */
				WPSOLR_Hosting_Api_Vespa_Cloud::class,
				/** RediSearch */
				WPSOLR_Hosting_Api_RediSearch_RedisLabsCloud::class,
				/** Swiftype */
				WPSOLR_Hosting_Api_Swiftype::class,
				/** Amazon CloudSearch */
				WPSOLR_Hosting_Api_Amazon_CloudSearch::class,
			]
		);
	}


	protected static function _get_all_ui_fields() {

		return array_merge( parent::_get_all_ui_fields(),
			[
				WPSOLR_AbstractEngineClient::ENGINE_ALGOLIA            => [ self::FIELD_NAME_DEFAULT_API => WPSOLR_Hosting_Api_Algolia::HOSTING_API_ID ],
				WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL      => [ self::FIELD_NAME_DEFAULT_API => WPSOLR_Hosting_Api_Google_Retail::HOSTING_API_ID ],
				WPSOLR_AbstractEngineClient::ENGINE_RECOMBEE           => [ self::FIELD_NAME_DEFAULT_API => WPSOLR_Hosting_Api_Recombee::HOSTING_API_ID ],
				WPSOLR_AbstractEngineClient::ENGINE_AMAZON_PERSONALIZE => [ self::FIELD_NAME_DEFAULT_API => WPSOLR_Hosting_Api_Amazon_Personalize::HOSTING_API_ID ],
				WPSOLR_AbstractEngineClient::ENGINE_SWIFTYPE           => [ self::FIELD_NAME_DEFAULT_API => WPSOLR_Hosting_Api_Swiftype::HOSTING_API_ID ],
				WPSOLR_AbstractEngineClient::ENGINE_REDISEARCH         => [ self::FIELD_NAME_DEFAULT_API => WPSOLR_Hosting_Api_RediSearch_None::HOSTING_API_ID ],
				WPSOLR_AbstractEngineClient::ENGINE_AMAZON_CLOUDSEARCH => [ self::FIELD_NAME_DEFAULT_API => WPSOLR_Hosting_Api_Amazon_CloudSearch::HOSTING_API_ID ],
			] );
	}

	/**
	 * @return bool
	 */
	public function get_is_engine_algolia() {
		return ( WPSOLR_AbstractEngineClient::ENGINE_ALGOLIA === $this->get_search_engine() );
	}

	/**
	 * @return bool
	 */
	public function get_is_engine_google_retail() {
		return ( WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL === $this->get_search_engine() );
	}

	/**
	 * @return bool
	 */
	public function get_is_engine_recombee() {
		return ( WPSOLR_AbstractEngineClient::ENGINE_RECOMBEE === $this->get_search_engine() );
	}

	/**
	 * @return bool
	 */
	public function get_is_engine_amazon_personalize() {
		return ( WPSOLR_AbstractEngineClient::ENGINE_AMAZON_PERSONALIZE === $this->get_search_engine() );
	}

	/**
	 * @return bool
	 */
	public function get_is_engine_swiftype() {
		return ( WPSOLR_AbstractEngineClient::ENGINE_SWIFTYPE === $this->get_search_engine() );
	}

}
