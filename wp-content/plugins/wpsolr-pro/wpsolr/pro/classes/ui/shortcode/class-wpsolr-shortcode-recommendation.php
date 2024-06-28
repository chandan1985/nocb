<?php

namespace wpsolr\pro\classes\ui\shortcode;

use wpsolr\core\classes\extensions\view\WPSOLR_Option_View;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\ui\shortcode\WPSOLR_Shortcode_Abstract;
use wpsolr\core\classes\ui\WPSOLR_Query_Parameters;
use wpsolr\pro\extensions\recommendations\WPSOLR_Option_Recommendations;

class WPSOLR_Shortcode_Recommendation extends WPSOLR_Shortcode_Abstract {

	const SHORTCODE_NAME = 'wpsolr_recommendation';
	const ATTRIBUTE_RECOMMENDATION_ID = 'id';
	const ATTRIBUTE_RECOMMENDATION_LABEL = 'label';

	/**
	 * @inheritdoc
	 */
	public static function get_html( $attributes = [] ) {

		try {
			$recommendation_uuid = $attributes[ static::ATTRIBUTE_RECOMMENDATION_ID ];

			$recommendation = WPSOLR_Option_Recommendations::get_recommendation( $recommendation_uuid );
			$index_uuid     = $recommendation[ WPSOLR_Option_View::INDEX_UUID ];
			WPSOLR_Option_View::set_current_index_uuid( $index_uuid );

			WPSOLR_Option_View::backup_current_view_uuid();
			WPSOLR_Option_View::set_current_view_uuid( 'B198F0141F49CCA682D85C1F54B2B8F6' );

			$wpsolr_query = WPSOLR_Query_Parameters::CreateQuery();
			$wpsolr_query->wpsolr_set_view_uuid( 'B198F0141F49CCA682D85C1F54B2B8F6' );
			$html = WPSOLR_Service_Container::get_solr_client( false, $index_uuid )->get_recommendations_html( $recommendation, $wpsolr_query );
			WPSOLR_Option_View::restore_current_view_uuid();

			return $html;

		} catch ( \Exception $e ) {
			return $e->getMessage();
		}

	}

	/**
	 * @param string $recommendation_uuid
	 * @param string $recommendation_label
	 *
	 * @return string
	 */
	public static function get_shortcode_html( $recommendation_uuid, $recommendation_label, $is_escape = false ) {
		$result = sprintf( '[%s %s="%s" %s="%s" /]',
			static::SHORTCODE_NAME,
			static::ATTRIBUTE_RECOMMENDATION_ID,
			$recommendation_uuid,
			static::ATTRIBUTE_RECOMMENDATION_LABEL,
			$recommendation_label );

		return $is_escape ? esc_html( $result ) : $result;
	}

}