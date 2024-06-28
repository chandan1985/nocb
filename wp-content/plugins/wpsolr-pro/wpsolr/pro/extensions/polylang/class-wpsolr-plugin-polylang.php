<?php

namespace wpsolr\pro\extensions\polylang;

use wpsolr\core\classes\engines\solarium\WPSOLR_SearchSolariumClient;
use wpsolr\core\classes\models\post\WPSOLR_Model_Meta_Type_Post;
use wpsolr\core\classes\models\taxonomy\WPSOLR_Model_Meta_Type_Taxonomy;
use wpsolr\core\classes\models\WPSOLR_Model_Meta_Type_Abstract;
use wpsolr\core\classes\utilities\WPSOLR_Option;
use wpsolr\core\classes\WPSOLR_Events;
use wpsolr\pro\extensions\wpml\WPSOLR_Plugin_Wpml;
use wpsolr\pro\proxies\WPSOLR_Proxy_Polylang;

/**
 * Class WPSOLR_Plugin_Polylang
 * @package wpsolr\pro\extensions\polylang
 *
 * Manage Polylang plugin
 * @link https://polylang.wordpress.com/documentation/
 */
class WPSOLR_Plugin_Polylang extends WPSOLR_Plugin_Wpml {
	use WPSOLR_Proxy_Polylang;

	const _PLUGIN_NAME_IN_MESSAGES = 'Polylang';

	/*
	 * Polylang database constants
	 */
	const TABLE_TERM_RELATION_SHIPS = "term_relationships";


	// Polylang options
	const _OPTIONS_NAME = WPSOLR_Option::OPTION_EXTENSION_POLYLANG;

	/**
	 * Factory
	 *
	 * @return WPSOLR_Plugin_Wpml
	 */
	static function create() {

		return new self();
	}

	/**
	 * Constructor
	 * Subscribe to actions
	 */

	function __construct() {

		parent::__construct();

		add_filter( WPSOLR_Events::WPSOLR_FILTER_SEARCH_PAGE_SLUG, [
			$this,
			'get_search_page_slug',
		], 10, 1 );


	}

	/**
	 * @inherit
	 */
	function set_current_language( $language_code ) {
		// No need. Language is set by Polylang even with Ajax
	}

	/**
	 * Customize the sql query statements.
	 * Add a join with the current indexing language
	 *
	 * @param $sql_statements
	 *
	 * @return mixed
	 */
	function set_sql_query_statement( $sql_statements, $parameters ) {
		global $wpdb;

		// Get the index indexing language
		$language = $this->get_solr_index_indexing_language( $parameters['index_indice'] );

		// Get the languages
		$languages = $this->get_languages();

		// Retrieve the term_id used for this language code
		if ( ! isset( $languages[ $language ]['term_id'] ) ) {
			throw new \ErrorException( sprintf( "The language '%s' is undefined in %s (not in the taxonomy terms).", $language, static::_PLUGIN_NAME_IN_MESSAGES ) );
		}

		/** @var WPSOLR_Model_Meta_Type_Abstract $model_type */
		$model_type = $parameters['model_type'];
		switch ( $model_type::META_TYPE ) {
			case WPSOLR_Model_Meta_Type_Post::META_TYPE:
				$language_term_id = $languages[ $language ]['term_id'];
				$term             = get_term( $language_term_id, 'language' );
				break;

			case WPSOLR_Model_Meta_Type_Taxonomy::META_TYPE:
				$language_term_id = $languages[ $language ]['tl_term_id'];
				$term             = get_term( $language_term_id, 'term_language' );
				break;
		}

		if ( ! isset( $term ) ) {
			throw new \ErrorException( sprintf( "The language '%s' term_id '%s' is undefined in %s (not in the taxonomy terms).", $language, $language_term_id, static::_PLUGIN_NAME_IN_MESSAGES ) );
		}
		$term_taxonomy_id = $term->term_taxonomy_id;

		if ( isset( $language ) ) {

			$sql_joint_statement = $model_type->get_sql_join_on_for_polylang();

			$sql_statements['JOIN'] .= sprintf( $sql_joint_statement, $term_taxonomy_id );
		}

		return $sql_statements;
	}

	/**
	 * Get current language code
	 *
	 * @return string Current language code
	 */
	function get_current_language_code() {

		return $this->pll_current_language( 'slug' );
	}

	/**
	 * Get default language code
	 *
	 * @return string Default language code
	 */
	function get_default_language_code() {

		return $this->pll_default_language( 'slug' );
	}

	/**
	 * Get the language of a post
	 *
	 * @return string Post language code
	 */
	function filter_get_post_language( $language_code, $post ) {

		$post_language = isset( $post ) ? $this->pll_get_post_language( $post->ID, 'slug' ) : null;

		return $post_language;
	}

	/**
	 * Get active language codes
	 *
	 * @return array Language codes
	 */
	function get_languages() {

		/*
		if ( isset( $this->languages ) ) {
			// Use value
			return $this->languages;
		}*/

		$result = [];

		// Retrieve Polylang active languages
		$languages = $this->pll_languages_list( [ 'fields' => '' ] );

		// Fill the result
		if ( ! empty( $languages ) ) {

			foreach ( $languages as $language ) {

				if ( ! is_null( $language ) ) {
					$result[ $language->slug ] = [
						'language_code' => $language->slug,
						'active'        => true,
						'term_id'       => $language->term_id,
						'tl_term_id'    => $language->tl_term_id,
					];
				}

			}
		}


		return $result;
	}


	/**
	 * Define the search page url for the current language
	 *
	 * @param $default_search_page_id
	 * @param $default_search_page_url
	 *
	 * @return string
	 */
	function set_search_page_url( $default_search_page_url, $default_search_page_id = null ) {

		if ( ! function_exists( 'pll_get_post' ) ) {
			// This extension is active, but the Polylang plugin is not. Prevent errors.
			return '';
		}

		$current_language_code = $this->get_current_language_code();

		// Get search page in current language
		$default_search_page_id_translated = $this->pll_get_post( $default_search_page_id, $current_language_code );

		if ( ! $default_search_page_id_translated ) {

			// Create a new search page for the translation
			$default_search_page = WPSOLR_SearchSolariumClient::create_default_search_page();

			// Retrieve current search page translations
			$translations = \PLL()->post->get_translations( $default_search_page_id );

			// Add current translation to translations
			$translations[ $current_language_code ] = $default_search_page->ID;

			// Save translations
			$this->pll_save_post_translations( $translations );

		}

		$result = ( $default_search_page_id === $default_search_page_id_translated ) ? $default_search_page_url : get_permalink( $default_search_page_id_translated );

		if ( \PLL()->model->get_links_model()->using_permalinks ) {
			// Necessary to counteract effects of filter 'get_search_form' in polylang/frontend/frontend-filters-search.php.
			// Else, action form will get /fr/fr rather than /fr.
			// => we remove the /fr here, which will be added back in the polylang filter (no other way !)
			$result = str_replace( \PLL()->model->get_language( $current_language_code )->search_url, \PLL()->model->get_links_model()->home . '/', $result );
		}


		return $result;
	}

	/**
	 * @param string $slug
	 *
	 * @return string
	 */
	function get_search_page_slug( $slug = null ) {

		// POLYLANG cannot accept 2 pages with the same slug.
		// So, add the language to the slug.
		return $slug . "-" . $this->get_current_language_code();
	}

	/**
	 * Register translation strings to translatable strings
	 *
	 * @param $parameters ["translations" => [ ["domain" => "wpsolr facel label", "name" => "categories", "text" => "my categories"]
	 */
	function register_translation_strings( $parameters ) {

		foreach ( $parameters['translations'] as $text_to_add ) {

			$this->pll_register_string( $text_to_add['name'], $text_to_add['text'], $text_to_add['domain'], false );
		}

		return;
	}

	/**
	 * Add translation strings to translatable strings
	 *
	 * @param array $parameter ["domain" => "wpsolr facel label", "name" => "categories", "text" => "my categories"]
	 *
	 * @return string
	 */
	function get_translation_string( $string, $parameter ) {

		if ( empty( $parameter['language'] ) ) {

			// Translate with current language
			$result = $this->pll__( $parameter['text'] );

		} else {

			// Translate with parameter language
			$result = $this->pll_translate_string( $parameter['text'], $parameter['language'] );
		}

		return $result;
	}

	/**
	 * @inherit
	 */
	public
	function wpsolr_filter_javascript_front_localized_parameters(
		$parameters
	) {
		$parameters['data']['lang'] = pll_current_language();

		return $parameters;
	}
}
