<?php

namespace wpsolr\core\classes\engines;

use wpsolr\core\classes\models\WPSOLR_Model_Abstract;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\pro\classes\engines\algolia\WPSOLR_Index_Algolia_Client;

abstract class WPSOLR_AbstractEngineClient extends WPSOLR_AbstractEngineClient_Root {

	const ENGINE_ALGOLIA = 'engine_algolia';
	const ENGINE_ALGOLIA_NAME = 'Algolia';
	const ENGINE_AMAZON_CLOUDSEARCH = 'engine_amazon_cloudsearch'; // https://docs.aws.amazon.com/aws-sdk-php/v2/guide/service-cloudsearch.html
	const ENGINE_AMAZON_CLOUDSEARCH_NAME = 'Amazon CloudSearch (coming soon)';
	const ENGINE_SWIFTYPE = 'engine_swiftype';
	const ENGINE_SWIFTYPE_NAME = 'Swiftype (coming soon)';

	const ENGINE_GOOGLE_RETAIL = 'engine_google_retail';
	const ENGINE_GOOGLE_RETAIL_NAME = 'Google Retail Search';
	const ENGINE_RECOMBEE = 'engine_recombee';
	const ENGINE_RECOMBEE_NAME = 'Recombee (Recommendations)';
	const ENGINE_AMAZON_PERSONALIZE = 'engine_amazon_personalize';
	const ENGINE_AMAZON_PERSONALIZE_NAME = 'Amazon Personalize (Recommendations)';

	/**
	 * Cookie
	 */
	const WPSOLR_COOKIE_NAME = 'wpsolr_anonymous_session';
	const WPSOLR_COOKIE_VISITOR_ID = 'visitor_id';
	const WPSOLR_COOKIE_EXPIRY = 'expiry';
	const WPSOLR_COOKIE_HASH = 'hash';

	// Is blog a slave search
	/** @var bool $is_in_galaxy */
	protected $is_in_galaxy;

	// Is blog a master search
	protected $is_galaxy_slave;

	// Galaxy slave filter value
	protected $is_galaxy_master;

	/** @var string $galaxy_slave_filter_value */
	public $galaxy_slave_filter_value;

	/**
	 * @inheridoc
	 */
	protected static function _get_analyser_search_engines(): array {
		return array_merge(
			parent::_get_analyser_search_engines(),
			[
				static::ENGINE_ALGOLIA,
			]
		);
	}

	/**
	 * Get the analysers available for a search engine type
	 *
	 * @param string $search_engine
	 *
	 * @return array
	 */
	static public function get_search_engine_type_analysers( $search_engine ) {

		$results = [];
		switch ( $search_engine ) {
			case static::ENGINE_ALGOLIA:
				$results = WPSOLR_Index_Algolia_Client::get_analysers();
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
	static function get_engines_definitions(): array {

		return array_merge(
			parent::get_engines_definitions(),
			[
				WPSOLR_AbstractEngineClient::ENGINE_ALGOLIA       => [
					'name'                    => WPSOLR_AbstractEngineClient::ENGINE_ALGOLIA_NAME,
					'is_active'               => true,
					'has_search'              => true,
					'has_personalized_search' => true,
					'has_recommendations'     => true,
				],
				/*
				WPSOLR_AbstractEngineClient::ENGINE_AMAZON_PERSONALIZE => [
					'name'                    => WPSOLR_AbstractEngineClient::ENGINE_AMAZON_PERSONALIZE_NAME,
					'is_active'               => false,
					'has_search'              => false,
					'has_personalized_search' => false,
					'has_recommendations'     => true,
				],
				*/
				WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL => [
					'name'                    => WPSOLR_AbstractEngineClient::ENGINE_GOOGLE_RETAIL_NAME,
					'is_active'               => true,
					'has_search'              => true,
					'has_personalized_search' => true,
					'has_recommendations'     => true,
				],
				/*
				WPSOLR_AbstractEngineClient::ENGINE_RECOMBEE           => [
					'name'                    => WPSOLR_AbstractEngineClient::ENGINE_RECOMBEE_NAME,
					'is_active'               => false,
					'has_search'              => false,
					'has_personalized_search' => false,
					'has_recommendations'     => true,
				],
				*/
				/*
				WPSOLR_AbstractEngineClient::ENGINE_SWIFTYPE           => [
					'name'                    => WPSOLR_AbstractEngineClient::ENGINE_SWIFTYPE_NAME,
					'is_active'               => false,
					'has_search'              => true,
					'has_personalized_search' => false,
					'has_recommendations'     => false,
				],
				WPSOLR_AbstractEngineClient::ENGINE_AMAZON_CLOUDSEARCH => [
					'name'                    => WPSOLR_AbstractEngineClient::ENGINE_AMAZON_CLOUDSEARCH_NAME,
					'is_active'               => false,
					'has_search'              => true,
					'has_personalized_search' => false,
					'has_recommendations'     => false,
				],*/
			] );
	}

	/**
	 * Init details
	 *
	 * @param $config
	 */
	protected function init( $config = null ) {

		parent::init( $config );

		$this->init_galaxy();
	}

	/**
	 * Init galaxy details
	 */
	protected function init_galaxy() {

		$this->is_in_galaxy     = WPSOLR_Service_Container::getOption()->get_cross_domain_is_galaxy_mode();
		$this->is_galaxy_slave  = WPSOLR_Service_Container::getOption()->get_cross_domain_is_galaxy_slave();
		$this->is_galaxy_master = WPSOLR_Service_Container::getOption()->get_search_is_galaxy_master();

		// After
		$this->galaxy_slave_filter_value = get_bloginfo( 'blogname' );
	}

	/**
	 * Get the current session visitor id
	 *
	 * @param bool $is_engine_require_visitor_id
	 *
	 * @return string
	 */
	protected function _get_session_visitor_id( bool $is_engine_require_visitor_id ): string {

		if ( ! WPSOLR_Service_Container::getOption()->get_is_search_use_first_party_cookie() ) {
			return $is_engine_require_visitor_id ? wp_generate_uuid4() : '';
		}

		return $this->_get_session_cookie_data()[ static::WPSOLR_COOKIE_VISITOR_ID ];
	}

	/**
	 * Get the session cookie.
	 *
	 * @return array
	 */
	public function _get_session_cookie_data(): array {

		// Get cookie json string
		$cookie_json = isset( $_COOKIE[ static::WPSOLR_COOKIE_NAME ] ) ? wp_unslash( $_COOKIE[ static::WPSOLR_COOKIE_NAME ] ) : '';

		// Convert json to array
		$cookie_data = \json_decode( $cookie_json, true ) ?? [];

		$is_store_cookie = false;
		if ( empty( $cookie_data ) || empty( $cookie_data[ static::WPSOLR_COOKIE_VISITOR_ID ] ?? '' ) ) {
			// Generate a new visitor_id if not in cookie or no cookie
			$is_store_cookie = true;
			$cookie_data     = [ static::WPSOLR_COOKIE_VISITOR_ID => wp_generate_uuid4() ];
		}

		if ( time() > ( $cookie_data[ static::WPSOLR_COOKIE_EXPIRY ] ?? 0 ) + 60 * 60 * 47 ) {
			// We are less than 47 hours from expiry date: need to extend the cookie expiry
			$is_store_cookie                             = true;
			$cookie_data[ static::WPSOLR_COOKIE_EXPIRY ] = time() + ( 1 * 365 * 24 * 60 * 60 ); // 1 year from now
		}

		$string_to_hash = $cookie_data[ static::WPSOLR_COOKIE_VISITOR_ID ] . '|' . $cookie_data[ static::WPSOLR_COOKIE_EXPIRY ];
		$string_hashed  = hash_hmac( 'md5', $string_to_hash, wp_hash( $string_to_hash ) );

		// Validate the hash for existing cookie
		if ( ! $is_store_cookie &&
		     ( empty( $cookie_data[ static::WPSOLR_COOKIE_HASH ] ) || ! hash_equals( $string_hashed, $cookie_data[ static::WPSOLR_COOKIE_HASH ] ) ) ) {
			// Wrong hash: recreate a cookie
			$is_store_cookie = true;
		}

		if ( $is_store_cookie ) {
			// Store the new/updated cookie

			/*
			 * Add the security hash to the cookie
			 */
			$cookie_data[ static::WPSOLR_COOKIE_HASH ] = $string_hashed;

			setcookie(
				static::WPSOLR_COOKIE_NAME,
				wp_json_encode( $cookie_data ),
				$cookie_data[ static::WPSOLR_COOKIE_EXPIRY ],
				COOKIEPATH,
				COOKIE_DOMAIN,
				is_ssl(),
				true
			);
		}


		return $cookie_data;

	}

	/**
	 * @return bool
	 */
	public function get_is_in_galaxy() {
		return $this->is_in_galaxy;
	}

	/**
	 * @return string
	 */
	public function get_galaxy_slave_filter_value() {
		return $this->galaxy_slave_filter_value;
	}

	/**
	 * @inheridoc
	 */
	protected function _get_mode_id( WPSOLR_Model_Abstract $model ): string {
		if ( ! $this->is_in_galaxy ) {
			return parent::_get_mode_id( $model );
		}

		// Create a unique id by adding the galaxy name to the $id
		return sprintf( '%s_%s', $this->galaxy_slave_filter_value, $model->get_id() );
	}

}
