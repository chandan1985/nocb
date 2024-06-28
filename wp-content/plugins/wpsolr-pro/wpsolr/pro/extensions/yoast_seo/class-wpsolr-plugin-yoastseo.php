<?php

namespace wpsolr\pro\extensions\yoast_seo;

use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\pro\extensions\seo\WPSOLR_Option_Seo;

/**
 * Class WPSOLR_Plugin_YoastSeo
 *
 */
class WPSOLR_Plugin_YoastSeo extends WPSOLR_Option_Seo {

	/**
	 * @var WPSOLR_Plugin_YoastSeo_Replace_Query
	 */
	protected $replace_query;

	/**
	 * Constructor
	 * Subscribe to actions/filters
	 **/
	function __construct() {

		// Mandatory init
		$this->init();

		// To active the Sitemaps optimizations
		add_filter( 'wpseo_sitemap_entries_per_page', [ $this, 'get_sitemap_entries_per_page' ], 10 );

	}

	public function get_sitemap_entries_per_page( $sitemap_entries_per_page ) {

		$wpsolr_sitemap_entries_per_page     = WPSOLR_Service_Container::getOption()->get_option_seo_sitemap_nb_entries_per_page( $this->get_option_name( $this->get_extension_name() ) );
		$new_wpsolr_sitemap_entries_per_page = empty( $wpsolr_sitemap_entries_per_page ) ? $sitemap_entries_per_page : $wpsolr_sitemap_entries_per_page;

		/**
		 * Sitemaps queries
		 */
		if ( WPSOLR_Service_Container::getOption()->get_option_seo_common_is_speedup_sitemaps( $this->get_option_name( $this->get_extension_name() ) ) ) {

			$this->replace_query = WPSOLR_Plugin_YoastSeo_Replace_Query::wpsolr_replace_wpdb( $this );
			$this->replace_query->set_site_map_max_entries( $new_wpsolr_sitemap_entries_per_page );
		}

		return $new_wpsolr_sitemap_entries_per_page;
	}

	/**
	 *
	 * @param string $metadesc
	 *
	 * @return string
	 */
	public function wpseo_metadesc( $metadesc ) {

		return $this->generate_meta_description( $metadesc );
	}

	/**
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	public function wpseo_title( $title ) {

		return $this->generate_meta_title( $title );
	}

	/**
	 *
	 * @param string $robots
	 *
	 * @return string
	 */
	public function wpseo_robots( $robots ) {

		return $this->generate_meta_robots( $robots );
	}

	/**
	 * @return string
	 */
	protected function get_extension_name() {
		return self::EXTENSION_YOAST_SEO;
	}

	/**
	 * Add an open graph image to permalinks
	 *
	 * @param \WPSEO_OpenGraph_Image $wpseo_ogi
	 */
	public function wpseo_add_opengraph_images( \WPSEO_OpenGraph_Image $wpseo_ogi ) {
		$url = $this->generate_open_graph_image_url();
		if ( ! empty( $url ) && ( false !== $url ) ) {
			$wpseo_ogi->add_image( $url );
		}
	}

	/**
	 * Open graph url
	 *
	 * @param string $url
	 *
	 * @return string Url
	 */
	public function wpseo_opengraph_url( $url ) {
		return $this->generate_open_graph_url();
	}

	/**
	 * Open graph canonical url
	 *
	 * @param string $url
	 *
	 * @return string Url
	 */
	public function wpseo_canonical( $url ) {
		return $this->generate_open_graph_url();
	}

	/**
	 * Register the corresponding seo plugin filters
	 *
	 * @return mixed
	 */
	function add_seo_filters() {

		add_filter( 'wpseo_metadesc', [ $this, 'wpseo_metadesc' ], 100, 1 );
		add_filter( 'wpseo_title', [ $this, 'wpseo_title' ], 100, 1 );
		add_filter( 'wpseo_robots', [ $this, 'wpseo_robots' ], 100, 1 );
		add_action( 'wpseo_add_opengraph_images', [ $this, 'wpseo_add_opengraph_images' ], 100, 1 );
		add_action( 'wpseo_opengraph_url', [ $this, 'wpseo_opengraph_url' ], 100, 1 );
		add_filter( 'wpseo_canonical', [ $this, 'wpseo_canonical' ], 100, 1 );
	}

	/**
	 * @inherit
	 */
	function add_seo_filters_robots() {
		add_filter( 'wpseo_robots', [ $this, 'wpseo_robots' ], 100, 1 );
	}

}
