<?php

namespace wpsolr\pro\extensions\toolset_views\filter;

use wpsolr\core\classes\WpSolrSchema;

class WPSOLR_Toolset_Views_Filter_Post_Tag extends WPSOLR_Toolset_Views_Filter_Post_Category {

	const TAXONOMY_ARGS = [ 'hierarchical' => false ];
	const POST_TAXONOMY_NAME = 'post_tag';

	const POST_FILTER_TYPE = 'tax_input_post_tag';
	const FILTER_FIELD_NAME = WpSolrSchema::_FIELD_NAME_TAGS;

	/**
	 * @inheritdoc
	 */
	protected static function _get_taxonomies( $args = [] ) {
		//$args['taxonomy'] = 'post_tag';

		return get_terms( $args );;
	}

	/**
	 * @inheritdoc
	 */
	protected static function _is_taxonomy( $taxonomy_names ) {
		return is_tag( $taxonomy_names );
	}

}
