<?php

namespace wpsolr\pro\extensions\query_monitor\qm;

use QM_Collector;
use QM_Output_Html;
use wpsolr\core\classes\utilities\WPSOLR_Escape;

class WPSOLR_Query_Monitor_Output extends QM_Output_Html {

	/**
	 * @var QM_Collector
	 */
	protected static $singleton;


	static function singleton( QM_Collector $collector ) {

		if ( ! isset( static::$singleton ) ) {
			static::$singleton = new self( $collector );
		}

		return static::$singleton;
	}

	/**
	 * @param QM_Collector $collector
	 */
	public function __construct( QM_Collector $collector ) {
		parent::__construct( $collector );

		add_filter( 'qm/output/menus', [ $this, 'admin_menu' ], 101 );
		add_filter( 'qm/output/title', [ $this, 'admin_title' ], 101 );
		add_filter( 'qm/output/menu_class', [ $this, 'admin_class' ] );
	}

	/**
	 * Echoes the Query Manager compatible output
	 * @return void
	 */
	public function output() {
		global /** @var array $wpsolr_query_logs */
		$wpsolr_query_logs;;

		WPSOLR_Escape::echo_escaped( '<div class="qm" id="' . WPSOLR_Escape::esc_attr( $this->collector->id() ) . '">' );
		WPSOLR_Escape::echo_escaped( '<table cellspacing="0">' );
		$first        = true;
		$header_names = [
			'level'             => 'Level',
			//'url'               => 'Url',
			'index_label'       => 'Index',
			'nb_rows'           => 'Rows',
			'nb_results'        => 'Results',
			'time_ms'           => 'Time (ms)',
			'query_as_string'   => 'Query',
			'results_as_string' => 'Results',
		];
		foreach ( $wpsolr_query_logs ?? [] as $log ) {

			if ( $first ) {
				/**
				 * Set the table header
				 */
				WPSOLR_Escape::echo_escaped( '<thead>' );
				WPSOLR_Escape::echo_escaped( '<tr>' );

				foreach ( $header_names as $log_label => $log_name ) {
					WPSOLR_Escape::echo_escaped( '<th>' );
					WPSOLR_Escape::echo_esc_html( $log_name );
					WPSOLR_Escape::echo_escaped( '</th>' );
				}

				WPSOLR_Escape::echo_escaped( '</tr>' );
				WPSOLR_Escape::echo_escaped( '</thead>' );
				$first = false;
			}

			WPSOLR_Escape::echo_escaped( '<tr>' );
			foreach ( $header_names as $log_label => $log_name ) {
				WPSOLR_Escape::echo_escaped( '<td>' );
				WPSOLR_Escape::echo_esc_html( $log[ $log_label ] ?? 'Not found' );
				WPSOLR_Escape::echo_escaped( '</td>' );
			}
			WPSOLR_Escape::echo_escaped( '</tr>' );
		}

		WPSOLR_Escape::echo_escaped( '</table>' );
		WPSOLR_Escape::echo_escaped( '</div>' );
	}

	/**
	 * Adds QM WPSOLR stats to admin panel
	 *
	 * @param array $title Array of QM admin panel titles
	 *
	 * @return array
	 */
	public
	function admin_title(
		array $title
	) {
		global $wp_object_cache;

		/*$title[] = sprintf(
			esc_html__( 'Cache %d/%d', 'query-monitor' ),
			intval( $wp_object_cache->stats['get'] ),
			intval( $wp_object_cache->stats['add'] )
		);*/

		return $title;
	}

	/**
	 * Add WPSOLR class
	 *
	 * @param array $classes Array of QM classes
	 *
	 * @return array
	 */
	public
	function admin_class(
		array $class
	) {
		$class[] = 'qm-wpsolr-stats';

		return $class;
	}

	/**
	 * Adds WPSOLR stats item to Query Monitor Menu
	 *
	 * @param array $menu Array of QM admin menu items
	 *
	 * @return array
	 */
	public
	function admin_menu(
		array $menu
	) {
		$menu[] = $this->menu( array(
			'id'    => 'qm-wpsolr-stats',
			'href'  => '#qm-wpsolr-stats',
			'title' => esc_html__( 'WPSOLR queries', 'query-monitor' )
		) );

		return $menu;
	}

}
