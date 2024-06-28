<?php

namespace wpsolr\pro\extensions\toolset_views;

use WP_Query;
use wpsolr\core\classes\engines\solarium\WPSOLR_SearchSolariumClient;
use wpsolr\core\classes\engines\WPSOLR_AbstractSearchClient;
use wpsolr\core\classes\extensions\WPSOLR_Extension;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\ui\WPSOLR_Query;
use wpsolr\core\classes\utilities\WPSOLR_Regexp;
use wpsolr\core\classes\WPSOLR_Events;
use wpsolr\core\classes\WpSolrSchema;
use wpsolr\pro\extensions\toolset_views\filter\WPSOLR_Toolset_Views_Filter_Builder;

/**
 * Class WPSOLR_Plugin_Toolset_Views
 * @package wpsolr\pro\extensions\toolset_views
 *
 * Manage "Toolset Views" plugin
 * @link https://toolset.com/documentation/user-guides/display-lists-of-posts-and-create-custom-searches/
 */
class WPSOLR_Plugin_Toolset_Views extends WPSOLR_Extension {

	const WPV_POST_SEARCH = 'wpv_post_search';

	/** @var bool */
	protected $is_ajax_processing = false;

	/** @var string[] */
	protected $view_post_types = [];

	/** @var string */
	protected $view_id;

	/** @var WPSOLR_Query */
	protected $wpsolr_query;

	/** @var bool */
	protected $is_replace_filter_query = false;

	/** @var array */
	protected $view_not_archive_settings;

	/** @var \WP_Post[] */
	protected $posts;

	/** @var \WP_Query */
	protected $wp_query;

	/**
	 * Factory
	 *
	 * @return WPSOLR_Plugin_Toolset_Views
	 */
	static function create() {

		return new self();
	}

	/**
	 * Constructor
	 * Subscribe to actions
	 */

	function __construct() {


		if ( WPSOLR_Service_Container::getOption()->get_search_is_replace_default_wp_search() ) {

			// Deactivate anoying Toolset Views caching
			add_filter( 'wpv_filter_disable_caching', [
				$this,
				'wpv_filter_disable_caching',
			], 10, 3 );

			// Intercept get_products() in Ajax
			add_filter( 'posts_pre_query', [ $this, 'posts_pre_query' ], 10, 2 );

			add_action( WPSOLR_Events::WPSOLR_FILTER_IS_REPLACE_BY_WPSOLR_QUERY, [
				$this,
				'wpsolr_filter_is_replace_by_wpsolr_query',
			], 10, 1 );

			add_action( WPSOLR_Events::WPSOLR_ACTION_PRE_EXECUTE_QUERY, [
				$this,
				'wpsolr_action_pre_execute',
			], 10, 2 );

			add_action( WPSOLR_Events::WPSOLR_ACTION_URL_PARAMETERS, [
				$this,
				'wpsolr_filter_url_parameters',
			], 10, 2 );

			add_action( WPSOLR_Events::WPSOLR_ACTION_SOLARIUM_QUERY, [
				$this,
				'wpsolr_action_query',
			], 10, 1 );

			add_action( WPSOLR_Events::WPSOLR_FILTER_POST_TYPES, [
				$this,
				'wpsolr_filter_post_types',
			], 10, 2 );

			add_filter( WPSOLR_Events::WPSOLR_FILTER_JAVASCRIPT_FRONT_LOCALIZED_PARAMETERS, [
				$this,
				'wpsolr_filter_javascript_front_localized_parameters',
			], 10, 1 );

			/**
			 * Toolset Archives shortcodes
			 */
			add_filter( 'wpv_filter_wpv_get_post_query', [ $this, 'wpv_get_post_query' ] );
			add_filter( 'wpv_filter_wpv_get_max_pages', [ $this, 'wpv_filter_wpv_get_max_pages' ] );
			add_filter( 'wpv_filter_wpv_get_current_page_number', [ $this, 'wpv_filter_wpv_get_current_page_number' ] );

			/**
			 * Toolset Views
			 */
			//add_action( 'wp_ajax_nopriv_wpv_get_view_query_results', [ $this, 'wpv_get_view_query_results' ] ); // Ajax
			add_filter( 'wpv_filter_query', [ $this, 'wpcf_views_post_query' ], 12, 2 ); // after custom fields.
			//add_filter( 'wpv_filter_taxonomy_query', 'wpcf_views_term_query', 42, 2 ); // after termmeta fields.
			//add_filter( 'wpv_filter_user_query', 'wpcf_views_user_query', 72, 2 ); // after usermeta fields.

		}

	}

	/**
	 * Deactivate caching
	 *
	 * @param string $view_id
	 * @param array $view_settings
	 * @param array $view_attributes
	 *
	 * @return bool
	 */
	function wpv_filter_disable_caching( $view_id, $view_settings, $view_attributes ) {

		return ! WPSOLR_Service_Container::getOption()->get_option_toolset_is_caching_views();
	}

	function wpcf_views_post_query( $query, $view_settings ) {

		$this->view_not_archive_settings = $view_settings;

		if ( ! empty( $this->view_not_archive_settings['post_type'] ) ) {
			$this->view_post_types = $this->view_not_archive_settings['post_type'];
		}

		return $query;
	}

	/**
	 * Prevent using default post type archive filter
	 *
	 *
	 * @param array $post_types
	 * @param WPSOLR_Query $wpsolr_query
	 *
	 * @return array
	 */
	public
	function wpsolr_filter_post_types(
		$post_types, $wpsolr_query
	) {

		return $post_types;
	}

	/**
	 * Set Ajax parameters according to the Toolset View Ajax parameters
	 *
	 * @param array $parameters
	 *
	 * @return array
	 */
	public
	function wpsolr_filter_javascript_front_localized_parameters(
		$parameters
	) {
		global $WPV_view_archive_loop, $WP_Views;

		if ( isset( $WPV_view_archive_loop ) && $this->is_replace_filter_query ) {

			$is_pagination_ajaxed = isset( $WPV_view_archive_loop->wpa_settings['pagination'] )
			                        && isset( $WPV_view_archive_loop->wpa_settings['pagination']['type'] )
			                        && ( 'ajaxed' === $WPV_view_archive_loop->wpa_settings['pagination']['type'] );

			$is_search_ajaxed = isset( $WPV_view_archive_loop->wpa_settings['dps'] )
			                    && isset( $WPV_view_archive_loop->wpa_settings['dps']['ajax_results_submit'] )
			                    && ( 'ajaxed' === $WPV_view_archive_loop->wpa_settings['dps']['ajax_results_submit'] );

			$parameters['data']['is_ajax']                    = $is_pagination_ajaxed || $is_search_ajaxed;
			$parameters['data']['css_ajax_container_results'] = '.js-wpv-view-layout';

		} else if ( isset( $WP_Views ) && ! empty( $current_view_id = $WP_Views->current_view ) ) {

			$parameters['data']['is_ajax'] = true;

			// Store the current views in html
			$parameters['data']['id']                         = sprintf( 'wpsolr_view_%s', $current_view_id );
			$parameters['data']['css_ajax_container_results'] = sprintf( '#wpv-view-layout-%s-TCPID%s',
				$current_view_id, $WP_Views->top_current_page->ID );
		}

		return $parameters;
	}

	public function wpsolr_action_query( $parameters ) {

		/* @var WPSOLR_AbstractSearchClient $search_engine_client */
		$search_engine_client = $parameters[ WPSOLR_Events::WPSOLR_ACTION_SOLARIUM_QUERY__PARAM_SOLARIUM_CLIENT ];

		/**
		 ** Add post types filter
		 */
		if ( ! empty( $this->view_post_types ) ) {

			$search_engine_client->search_engine_client_add_filter_in_terms( 'toolset views loop post types', WpSolrSchema::_FIELD_NAME_TYPE, $this->view_post_types );
		}

		/**
		 * Add view filters
		 */
		WPSOLR_Toolset_Views_Filter_Builder::build_filters( $this->view_id, $search_engine_client, $this->_get_view_settings(), $this->wp_query );
	}

	/**
	 * Inject WPSOLR search results in Ajax search
	 * wpv-archive-loop.php line 1942 : $archive_query        = new WP_Query( $query_args );
	 *
	 * @param null $retval Current return value for filter.
	 * @param WP_Query $query Current WordPress query object.
	 *
	 * @return null|array
	 */
	function posts_pre_query( $retval, $query ) {
		global $WPV_view_archive_loop;

		if ( $this->is_ajax_processing ) {
			// Prevent recursive calls
			return $retval;
		}

		$this->wp_query = $query;

		if ( ! isset( $this->view_not_archive_settings ) ) {
			// not a non-archive shortcode

			if (
				// Not the Toolset Archive Ajax call
				! ( isset( $_REQUEST['action'] ) && ( 'wpv_get_archive_query_results' === $_REQUEST['action'] ) && isset( $query->query_vars['wpv_fake_archive_loop'] ) )
				// Not the Toolset Archive non-ajax call
				&& ! (isset( $WPV_view_archive_loop ) && isset($WPV_view_archive_loop->wpa_id))
			) {

				return $retval;
			}

		} else {

			if ( WPSOLR_Service_Container::getOption()->get_option_toolset_is_caching_views() ) {
				// Cached views
				return $retval;
			}
		}

		// To prevent recursive infinite calls
		$this->is_ajax_processing = true;

		$wpsolr_query = $this->update_query_parameters();

		// Retrieve only ids of posts
		$wpsolr_query->query_vars['fields'] = 'ids';
		$this->posts                        = $wpsolr_query->get_posts();

		if ( isset( $this->view_not_archive_settings ) ) {
			$this->is_ajax_processing = false; // else archive-view pagination disapears during ajax refresh
		}

		unset( $this->view_not_archive_settings );

		// Return $results, which prevents standard $wp_query to execute it's SQL.
		return $this->posts;
	}

	/**
	 * Return the query to show found_posts in shortcode [wpv-found-count] and [wpv-items-count].
	 * See wpv-filter-embedded.php line 1519
	 *
	 * @param WP_Query $query
	 *
	 * @return WP_Query
	 */
	function wpv_get_post_query( $query = null ) {
		return isset( $this->wpsolr_query ) ? $this->wpsolr_query : $query;
	}

	/**
	 * Return max pages in shortcode. For [wpv-pager-archive-current-page].
	 * See wpv-pagination-embedded.php line 444
	 *
	 * @param int $current_max_pages
	 *
	 * @return int
	 */
	function wpv_filter_wpv_get_max_pages( $current_max_pages ) {
		return isset( $this->wpsolr_query ) ? $this->wpsolr_query->max_num_pages : $current_max_pages;
	}

	/**
	 * Return current page in shortcode. For [wpv-pager-archive-total-pages].
	 * See wpv-pagination-embedded.php line 451
	 *
	 * @param int $current_pages
	 *
	 * @return int
	 */
	function wpv_filter_wpv_get_current_page_number( $current_pages ) {
		return isset( $this->wpsolr_query ) ? $this->wpsolr_query->get_wpsolr_paged() : $current_pages;
	}


	/**
	 *
	 * Replace WP query when the search is replaced with a view.
	 *
	 * @param bool $is_replace_by_wpsolr_query
	 *
	 * @return bool
	 */
	public function wpsolr_filter_is_replace_by_wpsolr_query( $is_replace_by_wpsolr_query ) {
		global $WPV_view_archive_loop;

		if ( ! isset( $WPV_view_archive_loop ) || is_admin() || ! ( is_home() || is_archive() || is_search() ) ) {
			return $this->is_replace_filter_query = false;
		}

		if ( isset( $_REQUEST['action'] ) && ( 'wpv_get_archive_query_results' === $_REQUEST['action'] ) ) {
			// This is a Toolset archive Ajax search
			return $this->is_replace_filter_query = true;
		}


		$wpv_post_types_for_archive_loop = $WPV_view_archive_loop->wpv_settings->get( 'wpv_post_types_for_archive_loop' );

		if ( is_home() && $this->view_id = $WPV_view_archive_loop->wpv_settings->get( 'view_home-blog-page' ) ) {

			$this->view_post_types =
				! empty( $wpv_post_types_for_archive_loop['native']['home'] )
					? $wpv_post_types_for_archive_loop['native']['home']
					: [ 'post' ];

			return $this->is_replace_filter_query = true;
		}

		if ( is_search() && $this->view_id = $WPV_view_archive_loop->wpv_settings->get( 'view_search-page' ) ) {
			$post_types           = get_post_types( array( 'public' => true ), 'objects' );
			$post_types_in_search = wp_list_filter( $post_types, array( 'exclude_from_search' => 1 ), 'NOT' );

			$this->view_post_types =
				! empty( $wpv_post_types_for_archive_loop['native']['search'] )
					? $wpv_post_types_for_archive_loop['native']['search']
					: array_keys( $post_types_in_search );

			return $this->is_replace_filter_query = $is_replace_by_wpsolr_query;
		}

		if ( is_author() && $this->view_id = $WPV_view_archive_loop->wpv_settings->get( 'view_author-page' ) ) {

			$this->view_post_types =
				! empty( $wpv_post_types_for_archive_loop['native']['author'] )
					? $wpv_post_types_for_archive_loop['native']['author']
					: [ 'post' ];

			return $this->is_replace_filter_query = true;
		}

		if ( is_year() && $this->view_id = $WPV_view_archive_loop->wpv_settings->get( 'view_year-page' ) ) {

			$this->view_post_types =
				! empty( $wpv_post_types_for_archive_loop['native']['year'] )
					? $wpv_post_types_for_archive_loop['native']['year']
					: [ 'post' ];

			return $this->is_replace_filter_query = true;
		}

		if ( is_month() && $this->view_id = $WPV_view_archive_loop->wpv_settings->get( 'view_month-page' ) ) {

			$this->view_post_types =
				! empty( $wpv_post_types_for_archive_loop['native']['month'] )
					? $wpv_post_types_for_archive_loop['native']['month']
					: [ 'post' ];

			return $this->is_replace_filter_query = true;
		}

		if ( is_day() && $this->view_id = $WPV_view_archive_loop->wpv_settings->get( 'view_day-page' ) ) {

			$this->view_post_types =
				! empty( $wpv_post_types_for_archive_loop['native']['day'] )
					? $wpv_post_types_for_archive_loop['native']['day']
					: [ 'post' ];

			return $this->is_replace_filter_query = true;
		}

		if ( is_category() && $this->view_id = $WPV_view_archive_loop->wpv_settings->get( 'view_taxonomy_loop_category' ) ) {

			$this->view_post_types =
				! empty( $wpv_post_types_for_archive_loop['taxonomy']['category'] )
					? $wpv_post_types_for_archive_loop['taxonomy']['category']
					: [ 'post' ];

			return $this->is_replace_filter_query = true;
		}

		if ( is_tag() && $this->view_id = $WPV_view_archive_loop->wpv_settings->get( 'view_taxonomy_loop_post_tag' ) ) {

			$this->view_post_types =
				! empty( $wpv_post_types_for_archive_loop['taxonomy']['post_tag'] )
					? $wpv_post_types_for_archive_loop['taxonomy']['post_tag']
					: [ 'post' ];

			return $this->is_replace_filter_query = true;
		}

		return $this->is_replace_filter_query = false;
	}

	/**
	 * Map Toolset Views url search parameters with  WPSOLR's
	 *
	 * @param WPSOLR_Query $wpsolr_query
	 * @param array $url_parameters
	 *
	 */
	public
	function wpsolr_filter_url_parameters(
		WPSOLR_Query $wpsolr_query, $url_parameters
	) {

		if ( isset( $url_parameters[ self::WPV_POST_SEARCH ] ) ) {
			$wpsolr_query->set_wpsolr_query( $url_parameters[ self::WPV_POST_SEARCH ] );
		}

	}

	/**
	 * Update query before it is executed
	 *
	 * @param WPSOLR_Query $wpsolr_query
	 */
	public function wpsolr_action_pre_execute( WPSOLR_Query $wpsolr_query ) {

		// For Toolset Views in wpv-archive-loop.php. Else $WPV_view_archive_loop is not set, and standard search is displays without the View.
		do_action_ref_array( 'pre_get_posts', array( &$wpsolr_query ) );

		$this->update_query_parameters( $wpsolr_query );
	}

	/**
	 * Return view (archive or not) settings
	 *
	 * @return array
	 */
	protected function _get_view_settings() {
		global $WPV_view_archive_loop;

		return isset( $this->view_not_archive_settings ) ? $this->view_not_archive_settings : $WPV_view_archive_loop->wpa_settings;

	}

	/**
	 * Trigger post
	 *
	 * @param WP_Query $wp_query
	 * @param WPSOLR_Query $wpsolr_query
	 *
	 * @return WPSOLR_Query
	 */
	public function update_query_parameters( WPSOLR_Query $wpsolr_query = null ) {
		global $wp_query, $WPV_view_archive_loop;

		$wpa_settings = isset( $this->view_not_archive_settings ) ? $this->view_not_archive_settings : $WPV_view_archive_loop->wpa_settings;

		if ( isset( $_SERVER['QUERY_STRING'] ) ) {
			parse_str( $_SERVER['QUERY_STRING'], $url_parameters );
		}

		if ( is_null( $wpsolr_query ) ) {
			$wpsolr_query = WPSOLR_Service_Container::get_query(); // No parameters to prevent executing the query
		}

		/**
		 * Current page
		 */
		if ( isset( $wp_query->query ) && isset( $wp_query->query['paged'] ) && ! empty( $wp_query->query['paged'] ) ) {
			$wpsolr_query->set_wpsolr_paged( $wp_query->query['paged'] );

		} elseif ( isset( $_GET['wpv_paged'] ) && ! empty( $_GET['wpv_paged'] ) ) {
			// Non-archive Ajax

			$wpsolr_query->set_wpsolr_paged( $_GET['wpv_paged'] );
		}

		/**
		 * Search keywords
		 */
		if ( isset( $wp_query->query_vars ) && isset( $wp_query->query_vars['s'] ) && ! empty( $wp_query->query_vars['s'] ) ) {
			// Archive
			$wpsolr_query->set_wpsolr_query( $wp_query->query_vars['s'] );

		} elseif ( isset( $url_parameters ) && isset( $url_parameters['wpv_post_search'] ) && ! empty( $url_parameters['wpv_post_search'] ) ) {
			// Non-archive non-ajax
			$wpsolr_query->set_wpsolr_query( $url_parameters['wpv_post_search'] );

		} elseif ( isset( $_GET['wpv_post_search'] ) && ! empty( $_GET['wpv_post_search'] ) ) {
			// Non-archive Ajax
			$wpsolr_query->set_wpsolr_query( $_GET['wpv_post_search'] );
		}

		/**
		 * Limit
		 */
		if ( isset( $WPV_view_archive_loop->wpv_settings ) ) {

			if ( isset( $wpa_settings['pagination'] ) &&
			     ( 'disabled' !== $wpa_settings['pagination']['type'] ) &&
			     ! empty( $wpa_settings['pagination']['posts_per_page'] )
			) {
				// Rows per page is ot part of the url: add it from Toolset settings.
				$wpsolr_query->wpsolr_set_nb_results_by_page( $wpa_settings['pagination']['posts_per_page'] );

			} elseif ( ! empty( $wpa_settings['limit'] ) &&
			           ( - 1 !== $wpa_settings['limit'] )
			) {
				// Max results in total
				$wpsolr_query->wpsolr_set_nb_results_by_page( $wpa_settings['limit'] );
			}


		}

		/**
		 * Sorts
		 */
		if ( ! empty( $orderings = $this->_get_ordering( $wp_query ) ) ) {

			$order_by = [];
			foreach ( $orderings as $order_by_parameter => $order ) {

				// Convert values
				if ( 'meta_value' === $order_by_parameter ) {
					// Custom fields
					$order_by_parameter = $wp_query->query_vars['meta_key'];
				}
				$order = ( strtolower( $order ) === 'asc' ) ? WpSolrSchema::SORT_ASC : WpSolrSchema::SORT_DESC;

				switch ( trim( $order_by_parameter ) ) {
					case '':
						break;

					case 'post_date':
					case 'date':
						$order_by[] = ( $order === WpSolrSchema::SORT_ASC ) ? WPSOLR_SearchSolariumClient::SORT_CODE_BY_DATE_ASC : WPSOLR_SearchSolariumClient::SORT_CODE_BY_DATE_DESC;
						break;

					case 'post_title':
					case 'title':
						$order_by[] = ( $order === WpSolrSchema::SORT_ASC ) ? WPSOLR_SearchSolariumClient::SORT_CODE_BY_TITLE_S_ASC : WPSOLR_SearchSolariumClient::SORT_CODE_BY_TITLE_S_DESC;
						break;

					case 'ID':
						$order_by[] = ( $order === WpSolrSchema::SORT_ASC ) ? WPSOLR_SearchSolariumClient::SORT_CODE_BY_PID_ASC : WPSOLR_SearchSolariumClient::SORT_CODE_BY_PID_DESC;
						break;

					case 'post_author':
					case 'author':
						$order_by[] = ( $order === WpSolrSchema::SORT_ASC ) ? WPSOLR_SearchSolariumClient::SORT_CODE_BY_AUTHOR_ID_ASC : WPSOLR_SearchSolariumClient::SORT_CODE_BY_AUTHOR_ID_DESC;
						break;

					case 'post_type':
					case 'type':
						$order_by[] = ( $order === WpSolrSchema::SORT_ASC ) ? WPSOLR_SearchSolariumClient::SORT_CODE_BY_POST_TYPE_ASC : WPSOLR_SearchSolariumClient::SORT_CODE_BY_POST_TYPE_DESC;
						break;

					case 'modified':
						$order_by[] = ( $order === WpSolrSchema::SORT_ASC ) ? WPSOLR_SearchSolariumClient::SORT_CODE_BY_LAST_MODIFIED_ASC : WPSOLR_SearchSolariumClient::SORT_CODE_BY_LAST_MODIFIED_DESC;
						break;

					case 'menu_order':
						$order_by[] = ( $order === WpSolrSchema::SORT_ASC ) ? WPSOLR_SearchSolariumClient::SORT_CODE_BY_MENU_ORDER_ASC : WPSOLR_SearchSolariumClient::SORT_CODE_BY_MENU_ORDER_DESC;
						break;

					case 'rand':
						$order_by[] = WPSOLR_SearchSolariumClient::SORT_CODE_BY_RANDOM;
						break;

					default:

						$custom_fields = WPSOLR_Service_Container::getOption()->get_option_index_custom_fields( true );

						// 'field-my-field-name' => my-field-name
						$order_by_parameter_cleaned = WPSOLR_Regexp::remove_string_at_the_begining( $order_by_parameter, 'field-' ) . WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING;

						if ( false !== array_search( $order_by_parameter_cleaned, $custom_fields ) ) {
							// The sort_by is a custom field

							$order_by[] = $order_by_parameter_cleaned . '_' . $order;
						}
						break;
				}

			}

			if ( ! empty( $order_by ) ) {
				// Main sort
				$wpsolr_query->set_wpsolr_sort( $order_by[0] );
			}

			if ( count( $order_by ) > 1 ) {
				// Secondary sort
				$wpsolr_query->set_wpsolr_sort_secondary( $order_by[1] );
			}

		}

		$this->wpsolr_query = $wpsolr_query;

		return $wpsolr_query;
	}

	protected function _get_ordering( $wp_query ) {

		$orderings = [];

		if ( isset( $wp_query->query_vars ) && isset( $wp_query->query_vars['orderby'] ) ) {

			if ( ! is_array( $wp_query->query_vars['orderby'] ) ) {
				// Main sort in a string. No second sort.

				$orderings[ $wp_query->query_vars['orderby'] ] = $wp_query->query_vars['order'];

			} else {
				// Main sort and second sort in an array

				$orderings = $wp_query->query_vars['orderby'];
			}

		} else {
			// Non-archive

			if ( ! empty( $_GET['wpv_sort_orderby'] ) && ! empty( $_GET['wpv_sort_order'] ) ) {
				// Ajax
				$orderings[ $_GET['wpv_sort_orderby'] ] = $_GET['wpv_sort_order'];

			} elseif ( ! empty( $this->view_not_archive_settings['orderby'] ) && ! empty( $this->view_not_archive_settings['order'] ) ) {
				// Not ajax
				$orderings[ $this->view_not_archive_settings['orderby'] ] = $this->view_not_archive_settings['order'];
			}

			if ( ! empty( $_GET['wpv_sort_orderby_second'] ) && ! empty( $_GET['wpv_sort_order_second'] ) ) {
				// Ajax
				$orderings[ $_GET['wpv_sort_orderby_second'] ] = $_GET['wpv_sort_order_second'];

			} elseif ( ! empty( $this->view_not_archive_settings['orderby_second'] ) && ! empty( $this->view_not_archive_settings['order_second'] ) ) {
				// Not Ajax
				$orderings[ $this->view_not_archive_settings['orderby_second'] ] = $this->view_not_archive_settings['order_second'];
			}
		}

		return $orderings;
	}

}
