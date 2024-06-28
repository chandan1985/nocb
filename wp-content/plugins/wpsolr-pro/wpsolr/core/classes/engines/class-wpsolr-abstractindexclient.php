<?php

namespace wpsolr\core\classes\engines;

use wpsolr\pro\classes\engines\algolia\WPSOLR_Index_Algolia_Client;
use wpsolr\pro\classes\engines\amazon_personalize\WPSOLR_Index_Amazon_Personalize_Client;
use wpsolr\pro\classes\engines\elastic_site_search_php\WPSOLR_Index_Elastic_Site_Search_Client;
use wpsolr\pro\classes\engines\google_retail\WPSOLR_Index_Google_Retail_Client;
use wpsolr\pro\classes\engines\recombee\WPSOLR_Index_Recombee_Client;

abstract class WPSOLR_AbstractIndexClient extends WPSOLR_AbstractIndexClient_Root {

	/**
	 * @inheridoc
	 */
	protected static function _create( array $config, $solr_index_indice, $post_language ) {

		switch ( ! empty( $config['index_engine'] ) ? $config['index_engine'] : static::ENGINE_SOLR ) {

			case static::ENGINE_ALGOLIA:
				return new WPSOLR_Index_Algolia_Client( $config, $solr_index_indice, $post_language );
				break;

			case static::ENGINE_RECOMBEE:
				return new WPSOLR_Index_Recombee_Client( $config, $solr_index_indice, $post_language );
				break;

			case static::ENGINE_AMAZON_PERSONALIZE:
				return new WPSOLR_Index_Amazon_Personalize_Client( $config, $solr_index_indice, $post_language );
				break;

			case static::ENGINE_GOOGLE_RETAIL:
				return new WPSOLR_Index_Google_Retail_Client( $config, $solr_index_indice, $post_language );
				break;

			case static::ENGINE_SWIFTYPE:
				return new WPSOLR_Index_Elastic_Site_Search_Client( $config, $solr_index_indice, $post_language );
				break;

			case static::ENGINE_AMAZON_CLOUDSEARCH:
				throw new \Exception( sprintf( '%s not yet ready!', static::ENGINE_AMAZON_CLOUDSEARCH_NAME ) );
				break;

			default:
				return parent::_create( $config, $solr_index_indice, $post_language );
				break;
		}
	}

	/**
	 * @inheridoc
	 */
	static public function get_search_engine_type_analysers( $search_engine ) {

		$results = [];
		switch ( $search_engine ) {
			case static::ENGINE_ALGOLIA:
				$results = WPSOLR_Index_Algolia_Client::get_analysers();
				break;

			case static::ENGINE_GOOGLE_RETAIL:
				$results = WPSOLR_Index_Google_Retail_Client::get_analysers();
				break;

			case static::ENGINE_RECOMBEE:
				$results = WPSOLR_Index_Recombee_Client::get_analysers();
				break;

			case static::ENGINE_AMAZON_PERSONALIZE:
				$results = WPSOLR_Index_Amazon_Personalize_Client::get_analysers();
				break;

			default:
				$results = parent::get_search_engine_type_analysers( $search_engine );
				break;

		}

		return $results;
	}

	/**
	 * @inheridoc
	 */
	protected function get_site_id(): string {
		return $this->is_galaxy_slave ? $this->galaxy_slave_filter_value : '';
	}

	/**
	 * @return string
	 */
	protected function get_site_id_for_delete(): string {
		if ( $this->is_in_galaxy ) {
			// Delete only current site content

			return $this->galaxy_slave_filter_value;
		}

		return '';
	}

}
