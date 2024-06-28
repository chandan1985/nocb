<?php

namespace wpsolr\pro\extensions\query_monitor\qm;

use QM_Collector;

class WPSOLR_Query_Monitor_Collector extends QM_Collector {

	public $id = 'wpsolr-stats';

	/**
	 * @return string
	 */
	public function name() {
		return esc_html__( 'WPSOLR queries', 'wpsolr' );
	}

	/**
	 * @return void
	 */
	public function process() {
	}

}