<?php

namespace wpsolr\pro\extensions\jet_engine;

use WP_Query;
use wpsolr\core\classes\engines\solarium\WPSOLR_SearchSolariumClient;
use wpsolr\core\classes\engines\WPSOLR_AbstractSearchClient;
use wpsolr\core\classes\extensions\WPSOLR_Extension;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\ui\WPSOLR_Query;
use wpsolr\core\classes\WPSOLR_Events;
use wpsolr\core\classes\WpSolrSchema;

class WPSOLR_Plugin_Jet_Engine extends WPSOLR_Extension {


	/**
	 * JetEngine Meta field types
	 */
	const JETENGINE_FIELD_TYPE_CONTENT_FILE = 'media';
	const JETENGINE_FIELD_TYPE_LAYOUT_REPEATER = 'repeater';
	const JETENGINE_TYPE_GOOGLE_MAP = '???';
	const JETENGINE_FIELD_TYPE_DATE_PICKER = 'date';
	const JETENGINE_FIELD_TYPE_CHECKBOX = 'checkbox';


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
	protected $view_query_args;

	/** @var \WP_Post[] */
	protected $posts;

	/** @var \WP_Query */
	protected $wp_query;


	/**
	 * @var array JetEngine Definition of a post type
	 */
	protected $jet_post_type_def = [];

	/**
	 * Constructor
	 * Subscribe to actions
	 */
	function __construct() {
		
		add_filter( WPSOLR_Events::WPSOLR_FILTER_POST_CUSTOM_FIELDS, [
			$this,
			'filter_custom_fields',
		], 10, 2 );

		add_filter( 'jet-engine/listing/grid/posts-query-args', [ $this, 'add_query_args' ], 10, 2 );


		// Intercept get_products()
		add_filter( 'posts_pre_query', [ $this, 'posts_pre_query' ], 10, 2 );


		// Add custom filters
		add_action( WPSOLR_Events::WPSOLR_ACTION_SOLARIUM_QUERY, [
			$this,
			'wpsolr_action_query',
		], 10, 1 );

		/*
		add_action( WPSOLR_Events::WPSOLR_ACTION_PRE_EXECUTE_QUERY, [
			$this,
			'wpsolr_action_pre_execute',
		], 10, 2 );

		add_action( WPSOLR_Events::WPSOLR_ACTION_URL_PARAMETERS, [
			$this,
			'wpsolr_filter_url_parameters',
		], 10, 2 );

		*/

	}


	/**
	 * Catch query params
	 *
	 * @param array $args
	 */
	public function add_query_args( $args = [], $widget = null ) {
		$this->view_query_args = $args;

		return $args;
	}

	public function wpsolr_action_query( $parameters ) {

		/* @var WPSOLR_AbstractSearchClient $search_engine_client */
		$search_engine_client = $parameters[ WPSOLR_Events::WPSOLR_ACTION_SOLARIUM_QUERY__PARAM_SOLARIUM_CLIENT ];

		/**
		 ** Post status
		 */
		if ( ! empty( $statuses = $this->view_query_args['post_status'] ) ) {

			$search_engine_client->search_engine_client_add_filter_in_terms( 'JetEngine post status',
				WpSolrSchema::_FIELD_NAME_STATUS_S,
				$statuses
			);
		}

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

		if ( $this->is_ajax_processing ) {
			// Prevent recursive calls
			return $retval;

		} elseif ( ! isset( $this->view_query_args ) ) {
			// Not a Jet query
			return $retval;
		}

		$this->wp_query = $query;

		// To prevent recursive infinite calls
		$this->is_ajax_processing = true;

		$wpsolr_query = $this->update_query_parameters();

		// Retrieve only ids of posts
		$wpsolr_query->query_vars['fields'] = 'ids';
		$this->posts                        = $wpsolr_query->get_posts();

		if ( isset( $this->view_query_args ) ) {
			$this->is_ajax_processing = false; // else archive-view pagination disapears during ajax refresh
		}

		unset( $this->view_query_args );

		// Return $results, which prevents standard $wp_query to execute its SQL.
		$query->found_posts   = $wpsolr_query->found_posts;
		$query->post_count    = $wpsolr_query->post_count;
		$query->max_num_pages = $wpsolr_query->max_num_pages;

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

		return isset( $this->view_query_args ) ? $this->view_query_args : $WPV_view_archive_loop->wpa_settings;

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

		if ( is_null( $wpsolr_query ) ) {
			$wpsolr_query = WPSOLR_Service_Container::get_query(); // No parameters to prevent executing the query
		}

		/**
		 ** Post types
		 */
		if ( ! empty( $this->view_query_args['post_type'] ) ) {

			$wpsolr_query->wpsolr_set_post_types( [ $this->view_query_args['post_type'] ] );
		}

		/**
		 * Current page
		 */
		$wpsolr_query->set_wpsolr_paged( $_REQUEST['paged'] ?? ( $_REQUEST['page'] ?? '1' ) );

		/**
		 * Limit
		 */
		$wpsolr_query->wpsolr_set_nb_results_by_page( $this->view_query_args['posts_per_page'] ?? '10' );


		/**
		 * Sorts
		 */
		if ( ! empty( $orderings = $this->_get_ordering( $this->wp_query ) ) ) {

			$order_by = [];
			foreach ( $orderings as $order_by_parameter => $order ) {

				// Convert values
				if ( 'meta_value_num' === $order_by_parameter ) {
					// Custom fields
					$order_by_parameter = $this->wp_query->query_vars['meta_key'];
				}
				$order = ( strtolower( $order ) === 'asc' ) ? WpSolrSchema::SORT_ASC : WpSolrSchema::SORT_DESC;

				// RAND(124890) => rand
				if ( preg_match( '/^RAND\(/', $order_by_parameter, $match ) ) {
					$order_by_parameter = 'rand';
				}

				switch ( trim( $order_by_parameter ) ) {
					case '':
						break;

					case 'none':
					case 'relevance':
						break;

					case 'ID':
						$order_by[] = ( $order === WpSolrSchema::SORT_ASC ) ? WPSOLR_SearchSolariumClient::SORT_CODE_BY_PID_ASC : WPSOLR_SearchSolariumClient::SORT_CODE_BY_PID_DESC;
						break;

					case 'comment_count':
						$order_by[] = ( $order === WpSolrSchema::SORT_ASC ) ? WPSOLR_SearchSolariumClient::SORT_CODE_BY_NUMBER_COMMENTS_ASC : WPSOLR_SearchSolariumClient::SORT_CODE_BY_NUMBER_COMMENTS_DESC;
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
						$order_by_parameter_cleaned = $order_by_parameter . WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING;

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

		}

		return $orderings;
	}


	/**
	 * @param \WP_Post $post
	 *
	 * @return array
	 */
	protected function _get_jetengine_post_type_meta_fields( $post ) {

		$post_type = $post->post_type;

		if ( ! isset( $this->jet_post_type_def ) ||
		     ! isset( $this->jet_post_type_def[ $post_type ] )
		) {

			$all_meta_fields = jet_engine()->meta_boxes->meta_fields;
			if ( $all_meta_fields &&
			     is_array( $all_meta_fields ) &&
			     ! empty( $post_type_meta_fields = $all_meta_fields[ $post_type ] )
			) {
				$this->jet_post_type_def[ $post_type ] = $post_type_meta_fields;
			}

		}


		return $this->jet_post_type_def[ $post_type ] ?? [];
	}

	/**
	 * Decode JetEngine values before indexing.
	 * Get all field values, recursively in containers if necessary, which are not containers, and not files.
	 * Files are treated in attachments code.
	 *
	 * @param $custom_fields
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public
	function filter_custom_fields(
		$custom_fields, $post_id
	) {

		if ( ! isset( $custom_fields ) ) {
			$custom_fields = [];
		}


		// Get post ACF field objects
		$fields_set = [];
		$post       = get_post( $post_id );
		$this->get_fields_all_levels(
			$fields_set,
			$this->_get_jetengine_post_type_meta_fields( $post ),
			[], // We want All files
			[
				self::JETENGINE_FIELD_TYPE_CONTENT_FILE, // But we don't want files. They are dealt with attachments.
			]
		);

		if ( $fields_set ) {

			$is_first = [];

			foreach ( $fields_set as $field_name => $fields ) {

				foreach ( $fields as $field ) {

					switch ( $field['type'] ) {
						case static::JETENGINE_FIELD_TYPE_CHECKBOX:
							$current_custom_fields           = $custom_fields[ $field['name'] ];
							$custom_fields[ $field['name'] ] = []; // Replace old values
							foreach ( unserialize( $current_custom_fields[0] ) as $field_value => $field_selected ) {
								if ( 'true' === $field_selected ) {
									$custom_fields[ $field['name'] ][] = $field_value;
								}
							}
							break;

						default:
							break;
					}
				}
			}
		}

		return $custom_fields;
	}

	/**
	 * Get subfields of fields recursively
	 *
	 * @param array $all_fields
	 * @param array $fields
	 * @param array $field_types
	 * @param array $excluded_field_types
	 *
	 */
	public
	function get_fields_all_levels(
		&$all_fields, $fields, $field_types, $excluded_field_types
	) {

		if ( empty( $fields ) ) {
			// Nothing to do.
			return;
		}

		foreach ( $fields as $field ) {

			switch ( $field['type'] ) {
				case self::JETENGINE_FIELD_TYPE_LAYOUT_REPEATER:
					/*
					foreach ( $field['sub_fields'] as $sub_field ) {

						// Copy sub_field value(s)
						foreach ( $field['value'] as $value ) {

							if ( ! empty( $value[ $sub_field['name'] ] ) ) {
								$sub_field['value'] = $value[ $sub_field['name'] ];

								$this->get_fields_all_levels( $all_fields, [ $sub_field['name'] => $sub_field ], $field_types, $excluded_field_types );
							}
						}
					}
					*/
					break;

				default:
					// This is a non-recursive type, with value(s). Add it to results.
					if (
						( empty( $field_types ) || in_array( $field['type'], $field_types, true ) ) // Field type is in included types
						&& ( empty( $excluded_field_types ) || ! in_array( $field['type'], $excluded_field_types, true ) ) // And field type is not in excluded types
					) {
						$all_fields[ $field['name'] ][] = $field;
					}
					break;
			}
		}

	}


}
