<?php

namespace wpsolr\pro\extensions\toolset_views\filter;

use wpsolr\core\classes\utilities\WPSOLR_Escape;
use wpsolr\core\classes\WpSolrSchema;

class WPSOLR_Toolset_Views_Filter_Post_Category extends WPSOLR_Toolset_Views_Filter_Builder {

	const TAXONOMY_ARGS = [ 'hierarchical' => true ];
	const POST_TAXONOMY_NAME = 'category';

	const TAX_RELATIONSHIP = 'tax_%s_relationship';
	const TAXONOMY_ATTRIBUTE_URL = 'taxonomy-%s-attribute-url';
	const TAXONOMY_ATTRIBUTE_OPERATOR = 'taxonomy-%s-attribute-operator';
	const TAXONOMY_ATTRIBUTE_URL_FORMAT = 'taxonomy-%s-attribute-url-format';
	const POST_FILTER_TYPE = 'post_category';
	const POST_FILTER_POST_TYPE = 'post_type';
	const FILTER_FIELD_NAME = WpSolrSchema::_FIELD_NAME_CATEGORIES_STR;


	/**
	 * @inheritdoc
	 */

	static protected function _add_filters() {

		// WPSOLR_Escape::echo_esc_escaped(   '<br>0: ' . wp_json_encode( self::$view_settings, JSON_PRETTY_PRINT ));
		// WPSOLR_Escape::echo_esc_escaped(   '<br>0: ' . wp_json_encode( get_taxonomies(), JSON_PRETTY_PRINT ));

		parse_str( $_SERVER['QUERY_STRING'], $url_parameters );

		// WPSOLR_Escape::echo_esc_escaped(   '<br>0.1: ' . wp_json_encode( $url_parameters, JSON_PRETTY_PRINT ));

		foreach ( get_taxonomies( static::TAXONOMY_ARGS ) as $taxonomy ) {

			// WPSOLR_Escape::echo_esc_escaped(   '<br>1: ' . $taxonomy);

			$tax_relationship              = sprintf( static::TAX_RELATIONSHIP, $taxonomy );
			$taxonomy_attribute_url        = sprintf( static::TAXONOMY_ATTRIBUTE_URL, $taxonomy );
			$taxonomy_attribute_operator   = sprintf( static::TAXONOMY_ATTRIBUTE_OPERATOR, $taxonomy );
			$taxonomy_attribute_url_format = sprintf( static::TAXONOMY_ATTRIBUTE_URL_FORMAT, $taxonomy );

			// WPSOLR_Escape::echo_esc_escaped(   '<br>1.1: ' . $tax_relationship);

			if ( ! empty( $relationship = self::get_wpa_setting( $tax_relationship ) ) ) {

				// WPSOLR_Escape::echo_esc_escaped(   '<br>2: ' . $taxonomy);

				if ( 'FROM URL' === $relationship ) {

					// WPSOLR_Escape::echo_esc_escaped(   '<br>3: ' . $taxonomy);

					if ( empty( $taxonomy_names = $url_parameters[ self::get_wpa_setting( $taxonomy_attribute_url ) ] ) ) {
						// No categories in url

						// WPSOLR_Escape::echo_esc_escaped(   '<br>4: ' . $taxonomy);
						// WPSOLR_Escape::echo_esc_escaped(   '<br>4: ' . $taxonomy_attribute_url);
						// WPSOLR_Escape::echo_esc_escaped(   '<br>4: ' . wp_json_encode( $url_parameters, JSON_PRETTY_PRINT ));

						continue;
					}

					$include_method = self::get_wpa_setting( $taxonomy_attribute_operator );

					if ( ( 'slug' === self::get_wpa_setting( $taxonomy_attribute_url_format )[0] ) ) {

						// WPSOLR_Escape::echo_esc_escaped(   '<br>5: ' . $taxonomy);

						$taxonomy_names = static::_get_taxonomies( [
								'slug'     => $taxonomy_names,
								'fields'   => 'names',
								'taxonomy' => $taxonomy,
							]
						);
					}

				} else {

					$taxonomy_ids = self::get_wpa_setting( static::POST_FILTER_TYPE );

					if ( empty( $taxonomy_ids ) ) {
						// No taxonomies
						continue;
					}

					$include_method = self::get_wpa_setting( $tax_relationship );

					$taxonomy_names = static::_get_taxonomies( [
						'include'  => $taxonomy_ids,
						'fields'   => 'names',
						'taxonomy' => $taxonomy,
					] );

				}

				if ( static::_is_taxonomy( $taxonomy_names ) ) {
					// Do not use this filter on the same categories archive
					// WPSOLR_Escape::echo_esc_escaped(   '<br>6: ' . $taxonomy);

					continue;
				}

				$category_field_name = ( static::POST_TAXONOMY_NAME === $taxonomy ) ? static::FILTER_FIELD_NAME : sprintf( '%s%s', $taxonomy, WpSolrSchema::_SOLR_DYNAMIC_TYPE_STRING );
				///$category_field_name = self::$search_engine_client->get_facet_hierarchy_name( WpSolrSchema::_FIELD_NAME_FLAT_HIERARCHY, $category_field_name );

				$category_field_name = self::$search_engine_client->get_facet_hierarchy_name( WpSolrSchema::_FIELD_NAME_NON_FLAT_HIERARCHY, $category_field_name );

				// WPSOLR_Escape::echo_esc_escaped(   '<br>7: ' . $category_field_name . ' ' . $include_method . ' ' . implode( ', ', $taxonomy_names ));
				static::_add_filter_taxonomy( $category_field_name, $include_method, $taxonomy_names );

			}

		}

	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	protected static function _get_taxonomies( $args = [] ) {
		return get_categories( $args );;
	}

	/**
	 * @param $taxonomy_names
	 *
	 * @return bool
	 */
	protected static function _is_taxonomy( $taxonomy_names ) {
		return is_category( $taxonomy_names );;
	}

	/**
	 * @param string $field_name
	 * @param $include_method
	 * @param string[] $taxonomies
	 */
	protected static function _add_filter_taxonomy( $field_name, $include_method, $taxonomies ) {

		switch ( $include_method ) {
			case 'IN':

				self::$search_engine_client->search_engine_client_add_filter_in_terms( 'Toolset views post in any ' . static::POST_FILTER_TYPE,
					$field_name,
					$taxonomies );

				break;

			case 'NOT IN':

				self::$search_engine_client->search_engine_client_add_filter_not_in_terms( 'Toolset views post not in any ' . static::POST_FILTER_TYPE,
					$field_name,
					$taxonomies );

				break;

			case 'AND':

				self::$search_engine_client->search_engine_client_add_filter_in_all_terms( 'Toolset views post in all ' . static::POST_FILTER_TYPE,
					$field_name,
					$taxonomies );

				break;
		}

	}

}
