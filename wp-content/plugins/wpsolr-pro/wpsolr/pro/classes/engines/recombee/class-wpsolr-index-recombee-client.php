<?php

namespace wpsolr\pro\classes\engines\recombee;

use Recombee\RecommApi\Client;
use Recombee\RecommApi\Exceptions\ResponseException;
use Recombee\RecommApi\Requests\Batch;
use Recombee\RecommApi\Requests\DeleteItem;
use Recombee\RecommApi\Requests\SetItemValues;
use wpsolr\core\classes\engines\WPSOLR_AbstractIndexClient;
use wpsolr\core\classes\ui\WPSOLR_Query_Parameters;
use wpsolr\core\classes\WpSolrSchema;

/**
 * Class WPSOLR_IndexRecombeeClient
 *
 * @property Client $search_engine_client
 */
class WPSOLR_Index_Recombee_Client extends WPSOLR_AbstractIndexClient {
	use WPSOLR_Recombee_Client;

	const PIPELINE_INGEST_ATTACHMENT_ID = 'wpsolr_attachment';
	const PIPELINE_INGEST_ATTACHMENT_DEFINITION =
		<<<'TAG'
{
  "description" : "WPSOLR - Ingest attachment pipeline",
  "processors" : [
    {
      "attachment" : {
        "field" : "data"
      }
    }
  ]
}
TAG;

	const FIELD_NAME_GEOLOC = '_geoloc';

	/**
	 * Traking Events
	 */
	const EVENT_TYPE_CLICK = 'click';
	const EVENT_TYPE_CONVERSION = 'conversion';
	const EVENT_TYPE_VIEW = 'view';

	/**
	 * @inheritDoc
	 */
	public function search_engine_client_execute( $search_engine_client, $query ) {
		// Nothing here.
	}


	/**
	 * @param string $index_name
	 * @param array $documents
	 *
	 * @return array
	 */
	protected function search_engine_client_prepare_documents_for_update( array $documents ) {

		$formatted_document = [];

		foreach ( $documents as &$document ) {

			// id is a reserved property
			$id = $document['id'];
			unset( $document['id'] );

			$formatted_document[] = new SetItemValues( $id,
				// Item values
				$document,
				[ 'cascadeCreate' => true, ]
			);
		}

		return $formatted_document;
	}

	/**
	 * @param string $index_name
	 * @param array $documents
	 *
	 * @return array
	 */
	protected function search_engine_client_prepare_events_for_update( $index_name, array $documents ) {

		$formatted_events = [];

		foreach ( $documents as $document ) {

			if ( 'post_type' === $document['meta_type_s'] ) {

				switch ( $document['type'] ) {
					case self::POST_TYPE_WPSOLR_EVENT:
						/**
						 * Event data is stored as JSON in the post type content
						 * We use $post->post_content rather than $document['content'] to prevent escaped JSON format errors
						 */
						if ( $event = json_decode( get_post( $document['id'] )->post_content, ARRAY_A ) ) {
							$event['index']     = $index_name;
							$formatted_events[] = $event;
						} else {
							// Wrong JSON format
							throw new \Exception( sprintf( 'Found wrong JSON format for event %s', get_post( $document['id'] )->post_content ) );
						}
						break;


					case 'shop_order':

						/**
						 * Create an event from this order, if not already done yet
						 */
						$event_indices = get_post_meta( $document['id'], static::CUSTOM_FIELD_NAME_EVENT_INDICES, true ) ?? [];
						if ( empty( $event_indices ) || ( ! in_array( $index_name, $event_indices ) ) ) {

							$event = WPSOLR_AbstractIndexClient::create()->transform_event_tracking( get_post( $document['id'] ), [
								WPSOLR_Query_Parameters::SEARCH_PARAMETER_EVENT_TRACKING_NAME => WPSOLR_Query_Parameters::SEARCH_PARAMETER_EVENT_TRACKING_NAME_PURCHASE_ORDER,
							], false );
							if ( ! empty( $event ) ) {
								$event['index']     = $index_name;
								$formatted_events[] = $event;
							}
						}

						break;
				}
			}

		}

		return $formatted_events;
	}

	/**
	 * {"index":{"_type":"wpsolr_types","_id":3264}}
	 * {"id":3264,"PID":3264,"type":"job_listing","meta_type_s":"post_type","displaymodified":"2018-12-10T19:37:20Z","title":"Typing Room","title_s":"Typing Room","permalink":"http:\/\/src-wpsolr-search-engine.test\/listing\/typing-room\/","post_status_s":"publish","content":"Typing Room is located in East London’s Town Hall Hotel, built in 1910, and is named after the building’s original typing room in which all communications from the mayoral, council and judicial system were put to ink.","post_author_s":"1","author":"admin","menu_order_i":-1,"PID_i":3264,"author_s":"http:\/\/src-wpsolr-search-engine.test\/author\/admin\/","displaydate":"2018-12-10T19:37:20Z","displaydate_dt":"2018-12-10T19:37:20Z","date":"2018-12-10T19:37:20Z","displaymodified_dt":"2018-12-10T19:37:20Z","modified":"2018-12-10T19:37:20Z","modified_y_i":2018,"modified_ym_i":12,"modified_yw_i":50,"modified_yd_i":344,"modified_md_i":10,"modified_wd_i":2,"modified_dh_i":19,"modified_dm_i":37,"modified_ds_i":20,"displaydate_y_i":2018,"displaydate_ym_i":12,"displaydate_yw_i":50,"displaydate_yd_i":344,"displaydate_md_i":10,"displaydate_wd_i":2,"displaydate_dh_i":19,"displaydate_dm_i":37,"displaydate_ds_i":20,"displaydate_dt_y_i":2018,"displaydate_dt_ym_i":12,"displaydate_dt_yw_i":50,"displaydate_dt_yd_i":344,"displaydate_dt_md_i":10,"displaydate_dt_wd_i":2,"displaydate_dt_dh_i":19,"displaydate_dt_dm_i":37,"displaydate_dt_ds_i":20,"displaymodified_dt_y_i":2018,"displaymodified_dt_ym_i":12,"displaymodified_dt_yw_i":50,"displaymodified_dt_yd_i":344,"displaymodified_dt_md_i":10,"displaymodified_dt_wd_i":2,"displaymodified_dt_dh_i":19,"displaymodified_dt_dm_i":37,"displaymodified_dt_ds_i":20,"comments":[],"numcomments":0,"categories_str":[],"categories":["Restaurants","London","Accepts Credit Cards","Bike Parking","Coupons","Parking Street","Smoking Allowed","Wireless Internet","51.530675","-0.054321","9.5","place","1"],"flat_hierarchy_categories_str":[],"non_flat_hierarchy_categories_str":[],"tags":[],"job_listing_category_str":["Restaurants"],"flat_hierarchy_job_listing_category_str":["Restaurants"],"non_flat_hierarchy_job_listing_category_str":["Restaurants"],"region_str":["London"],"flat_hierarchy_region_str":["London"],"non_flat_hierarchy_region_str":["London"],"case27_job_listing_tags_str":["Accepts Credit Cards","Bike Parking","Coupons","Parking Street","Smoking Allowed","Wireless Internet"],"flat_hierarchy_case27_job_listing_tags_str":["Accepts Credit Cards","Bike Parking","Coupons","Parking Street","Smoking Allowed","Wireless Internet"],"non_flat_hierarchy_case27_job_listing_tags_str":["Accepts Credit Cards","Bike Parking","Coupons","Parking Street","Smoking Allowed","Wireless Internet"],"geolocation_lat_s":["51.530675"],"geolocation_lat_str":["51.530675"],"geolocation_long_s":["-0.054321"],"geolocation_long_str":["-0.054321"],"_case27_average_rating_f":[9.5],"_case27_average_rating_str":[9.5],"_case27_listing_type_str":["place"],"_featured_i":[1],"_featured_str":[1],"wpsolr_mylisting_geolocation_ll":"51.530675,-0.054321"}
	 * {"index":{"_type":"wpsolr_types","_id":3275}}
	 * {"id":3275,"PID":3275,"type":"job_listing","meta_type_s":"post_type","displaymodified":"2018-12-10T19:54:21Z","title":"The Ledbury","title_s":"The Ledbury","permalink":"http:\/\/src-wpsolr-search-engine.test\/listing\/the-ledbury\/","post_status_s":"publish","content":"At distant inhabit amongst by. Appetite welcomed interest the goodness boy not. Estimable education for disposing pronounce her. John size good gay plan sent old roof own. Inquietude saw understood his friendship frequently yet. Nature his marked ham wished","post_author_s":"1","author":"admin","menu_order_i":0,"PID_i":3275,"author_s":"http:\/\/src-wpsolr-search-engine.test\/author\/admin\/","displaydate":"2018-12-10T19:54:21Z","displaydate_dt":"2018-12-10T19:54:21Z","date":"2018-12-10T19:54:21Z","displaymodified_dt":"2018-12-10T19:54:21Z","modified":"2018-12-10T19:54:21Z","modified_y_i":2018,"modified_ym_i":12,"modified_yw_i":50,"modified_yd_i":344,"modified_md_i":10,"modified_wd_i":2,"modified_dh_i":19,"modified_dm_i":54,"modified_ds_i":21,"displaydate_y_i":2018,"displaydate_ym_i":12,"displaydate_yw_i":50,"displaydate_yd_i":344,"displaydate_md_i":10,"displaydate_wd_i":2,"displaydate_dh_i":19,"displaydate_dm_i":54,"displaydate_ds_i":21,"displaydate_dt_y_i":2018,"displaydate_dt_ym_i":12,"displaydate_dt_yw_i":50,"displaydate_dt_yd_i":344,"displaydate_dt_md_i":10,"displaydate_dt_wd_i":2,"displaydate_dt_dh_i":19,"displaydate_dt_dm_i":54,"displaydate_dt_ds_i":21,"displaymodified_dt_y_i":2018,"displaymodified_dt_ym_i":12,"displaymodified_dt_yw_i":50,"displaymodified_dt_yd_i":344,"displaymodified_dt_md_i":10,"displaymodified_dt_wd_i":2,"displaymodified_dt_dh_i":19,"displaymodified_dt_dm_i":54,"displaymodified_dt_ds_i":21,"comments":[],"numcomments":0,"categories_str":[],"categories":["Restaurants","London","Accepts Credit Cards","Bike Parking","Coupons","Parking Street","Smoking Allowed","Wireless Internet","51.535627","-0.183318","8.5","place","0","0"],"flat_hierarchy_categories_str":[],"non_flat_hierarchy_categories_str":[],"tags":[],"job_listing_category_str":["Restaurants"],"flat_hierarchy_job_listing_category_str":["Restaurants"],"non_flat_hierarchy_job_listing_category_str":["Restaurants"],"region_str":["London"],"flat_hierarchy_region_str":["London"],"non_flat_hierarchy_region_str":["London"],"case27_job_listing_tags_str":["Accepts Credit Cards","Bike Parking","Coupons","Parking Street","Smoking Allowed","Wireless Internet"],"flat_hierarchy_case27_job_listing_tags_str":["Accepts Credit Cards","Bike Parking","Coupons","Parking Street","Smoking Allowed","Wireless Internet"],"non_flat_hierarchy_case27_job_listing_tags_str":["Accepts Credit Cards","Bike Parking","Coupons","Parking Street","Smoking Allowed","Wireless Internet"],"geolocation_lat_s":["51.535627"],"geolocation_lat_str":["51.535627"],"geolocation_long_s":["-0.183318"],"geolocation_long_str":["-0.183318"],"_case27_average_rating_f":[8.5],"_case27_average_rating_str":[8.5],"_case27_listing_type_str":["place"],"_featured_i":["0","0"],"_featured_str":["0","0"],"wpsolr_mylisting_geolocation_ll":"51.535627,-0.183318"}
	 */

	/**
	 * Use Tika to extract a file content.
	 *
	 * @param $file
	 *
	 * @return string
	 * @throws \Exception
	 */
	protected function search_engine_client_extract_document_content( $file ) {
		return '';
	}

	/**
	 * @param array[] $documents
	 *
	 * @return int|mixed
	 * @throws \Exception
	 */
	public function send_posts_or_attachments_to_solr_index( $documents, $is_error = false ) {

		$formatted_docs = $this->search_engine_client_prepare_documents_for_update( $documents );

		try {

			$response = $this->search_engine_client->send( new Batch( $formatted_docs ) );

		} catch ( \Exception $e ) {

			throw new \Exception( sprintf( "(Recombee) \"%s\"\n", $e->getMessage() ) );
		}

		if ( $this->_has_error( $response )
		) {

			$error_msg = $this->_get_error( $response );

			try {
				if ( false !== strpos( $error_msg, 'item property' ) ) {
					// Update the index schema before retrying
					$this->_add_index_fields_definitions( $this->get_all_fields( $documents ) );

					// At last, retry
					$response = $this->search_engine_client->send( new Batch( $formatted_docs ) );

				} else {
					throw new \Exception( sprintf( "(Recombee) \"%s\"\n", $error_msg ) );
				}

			} catch ( \Exception $e ) {
				throw new \Exception( sprintf( "(Recombee) \"%s\"\n", $e->getMessage() ) );
			}

		}

		return true;
	}

	/**
	 * @param array $response
	 *
	 * @return bool
	 */
	protected function _has_error( $response ) {

		return ( 200 !== ( $response[0]['code'] ?? - 1 ) );
	}

	/**
	 * @param array $response
	 *
	 * @return string
	 */
	protected function _get_error( $response ) {

		if ( $this->_has_error( $response ) &&
		     isset( $response[0] ) &&
		     isset( $response[0]['json'] ) &&
		     isset( $response[0]['json']['error'] )
		) {
			return $response[0]['json']['error'];
		}

		return '';
	}

	/**
	 * @inheritdoc
	 */
	protected function search_engine_client_delete_all_documents( $post_types = null, $site_id = '' ) {

		if ( ( is_null( $post_types ) || empty( $post_types ) ) && ( empty( $site_id ) ) ) {

			$this->get_search_index()->clearObjects();

		} else {

			/**
			 * https://www.recombee.com/doc/api-reference/api-methods/delete-by/#examples
			 * https://www.recombee.com/doc/api-reference/api-parameters/filters/#examples
			 **/
			$bool_queries = [];

			if ( ! ( is_null( $post_types ) || empty( $post_types ) ) ) {
				$filters = [];
				foreach ( $post_types as $post_type ) {
					$filters[] = sprintf( '%s:"%s"', WpSolrSchema::_FIELD_NAME_TYPE, $post_type );
				}
				$bool_queries[] = sprintf( '(%s)', implode( ' OR ', $filters ) );
			}

			if ( ! empty( $site_id ) ) {
				$bool_queries[] = sprintf( '%s:"%s"', WpSolrSchema::_FIELD_NAME_BLOG_NAME_STR, $site_id );
			}

			$params = [ 'filters' => implode( ' AND ', $bool_queries ) ];

			$this->get_search_index()->deleteBy( $params );
		}

	}

	/**
	 * @inheritDoc
	 *
	 */
	protected function search_engine_client_delete_document( $document_id, $model = null ) {

		try {
			$this->search_engine_client->send( new DeleteItem( $document_id ) );
		} catch ( ResponseException $e ) {
			if ( false === strpos( $e->getMessage(), 'not exist' ) ) {
				// Send error if not an "Item does not exist" error
				throw new \Exception( $e->getMessage() );
			}
		}
	}

	/**
	 * https://www.recombee.com/doc/guides/managing-results/refine-results/geolocation/how-to/filter-results-around-a-location/#dataset
	 *
	 * @inheritDoc
	 */
	public function get_geolocation_field_value( $field_name, $lat, $long ) {

		return [
			'field_name'  => static::FIELD_NAME_GEOLOC, // Recombee uses a default field geolocation
			'field_value' => [ 'lat' => (float) $lat, 'lng' => (float) $long ],
		];
	}

	/**
	 * @param array $document_for_update
	 * @param string $field_name
	 * @param string $is_exists 'y' or 'n'
	 */
	public function set_field_is_exist( array &$document_for_update, string $field_name, string $is_exists ) {
		// Manage the 'not exists'
		$document_for_update[ sprintf( static::$FIELD_IS_EXIST, $field_name ) ] = $is_exists;
	}

	/***************************************************************************************************
	 *
	 * Index tracking events
	 *
	 ***************************************************************************************************/

	/**
	 * @inerhitDoc
	 */
	protected function _transform_event_tracking( \WP_Post $post, array $event, string $event_label ) {
		/**
		 * Transform: https://www.recombee.com/doc/api-reference/api-methods/send-events/
		 */

		$target_event = [
			'eventName' => $event_label,
			'timestamp' => current_time( 'timestamp', 1 ) * 1000, // unix timestamp in ms,
		];


		if ( ! empty( $query_id = $event[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_RESULTS_QUERY_ID ] ) ) {
			$target_event['queryID'] = $query_id;
		}

		if ( ! empty( $user_token = $event[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_USER_TOKEN ] ) ) {
			$target_event['userToken'] = $user_token;
		}

		return $target_event;
	}

	/**
	 * @inerhitDoc
	 */
	protected function _transform_event_tracking_click_search_result( \WP_Post $post, array $event, array $transformed_event ) {
		$transformed_event['eventType'] = self::EVENT_TYPE_CLICK;
		$transformed_event['objectIDs'] = [ (string) $post->ID ];
		// TODO: pagination (position 3 on page 2 => position 13)
		$position                       = 1 + intval( $event[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_RESULTS_POSITION ] ?? 0 ); // Recombee's position starts at '1'
		$transformed_event['positions'] = [ $position ];

		return $transformed_event;
	}

	/**
	 * @inerhitDoc
	 */
	protected function _transform_event_tracking_click_search_filter( \WP_Post $post, array $event, array $transformed_event ) {
		$transformed_event['eventType'] = self::EVENT_TYPE_CLICK;
		$transformed_event['filters']   = [ 'type:post' ];

		return $transformed_event;
	}

	/**
	 * @inerhitDoc
	 */
	protected function _transform_event_tracking_purchase_order( \WP_Post $post, array $event, array $transformed_event ) {
		$transformed_event['eventType'] = self::EVENT_TYPE_CONVERSION;
		$transformed_event['objectIDs'] = $event[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_OBJECT_IDS ];
		$transformed_event['userToken'] = (string) $event[ WPSOLR_Query_Parameters::SEARCH_PARAMETER_USER_TOKEN ];
		$transformed_event['timestamp'] = get_post_timestamp( $post, 'date' ) * 1000; // order's creation date

		return $transformed_event;
	}

}
