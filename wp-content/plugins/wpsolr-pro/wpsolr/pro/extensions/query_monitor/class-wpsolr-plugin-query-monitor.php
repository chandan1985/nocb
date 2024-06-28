<?php

namespace wpsolr\pro\extensions\query_monitor;

use QM_Collectors;
use wpsolr\core\classes\extensions\view\WPSOLR_Option_View;
use wpsolr\core\classes\extensions\WPSOLR_Extension;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\WPSOLR_Events;
use wpsolr\pro\extensions\query_monitor\qm\WPSOLR_Query_Monitor_Collector;
use wpsolr\pro\extensions\query_monitor\qm\WPSOLR_Query_Monitor_Output;

class WPSOLR_Plugin_Query_Monitor extends WPSOLR_Extension {


	/**
	 * Constructor
	 * Subscribe to actions
	 */
	function __construct() {

		WPSOLR_Option_View::backup_current_view_uuid();
		$views = apply_filters( WPSOLR_Events::WPSOLR_FILTER_VIEWS, WPSOLR_Option_View::get_list_default_view(), false, 10, 2 );
		foreach ( $views as $view_uuid => $view ) {
			WPSOLR_Option_View::set_current_view_uuid( $view_uuid );

			if ( WPSOLR_Service_Container::getOption()->get_is_search_log_query_mode_debug_query_monitor() ) {
				$this->register_collector();
				add_filter( 'qm/outputter/html', array( $this, 'register_output' ), 101, 1 );

				break;
			}

		}
		WPSOLR_Option_View::restore_current_view_uuid();
	}

	/**
	 * Register collector
	 *
	 * @return void
	 */
	private function register_collector() {
		QM_Collectors::add( new WPSOLR_Query_Monitor_Collector() );
	}

	/**
	 * Register output
	 *
	 * @param array $output
	 *
	 * @return array
	 */
	public function register_output( $output ) {

		if ( $collector = QM_Collectors::get( 'wpsolr-stats' ) ) {
			$output['wpsolr'] = WPSOLR_Query_Monitor_Output::singleton( $collector );
		}

		return $output;
	}
}
