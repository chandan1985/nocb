<?php

use wpsolr\core\classes\extensions\indexes\WPSOLR_Option_Indexes;
use wpsolr\core\classes\extensions\view\WPSOLR_Option_View;
use wpsolr\core\classes\extensions\WPSOLR_Extension;
use wpsolr\core\classes\hosting_api\WPSOLR_Hosting_Api_Abstract;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\utilities\WPSOLR_Escape;
use wpsolr\core\classes\utilities\WPSOLR_Option;
use wpsolr\pro\extensions\recommendations\WPSOLR_Option_Recommendations;
use wpsolr\pro\classes\ui\shortcode\WPSOLR_Shortcode_Recommendation;

/**
 * Included file to display admin options
 */
global $license_manager;

WPSOLR_Extension::require_once_wpsolr_extension( WPSOLR_Extension::OPTION_RECOMMENDATIONS, true );

$extension_options_name = WPSOLR_Option_View::get_index_uuid_options_name( WPSOLR_Option::OPTION_RECOMMENDATIONS );
$settings_fields_name   = 'extension_recommendations_opt';

$recommendations = WPSOLR_Service_Container::getOption()->get_option_recommendations_recommendations();
if ( isset( $_POST['wpsolr_new_recommendations'] ) && ! isset( $crons[ $_POST['wpsolr_new_recommendations'] ] ) ) {
	$recommendations = array_merge( [ sanitize_text_field( $_POST['wpsolr_new_recommendations'] ) => [ 'is_new' => true ] ], $recommendations );
}

WPSOLR_Extension::require_once_wpsolr_extension( WPSOLR_Extension::OPTION_INDEXES, true );
$option_indexes = new WPSOLR_Option_Indexes();
$solr_indexes   = $option_indexes->get_indexes();

$index_has_recommendation = false;
$current_index            = $option_indexes->get_current_index();
if ( ! empty( $current_index ) ) {
	$index_label              = $current_index[ WPSOLR_Hosting_Api_Abstract::FIELD_NAME_FIELDS_INDEX_LABEL ];
	$index_hosting_api        = WPSOLR_Hosting_Api_Abstract::get_hosting_api_by_id( $current_index['index_hosting_api_id'] );
	$index_has_recommendation = $index_hosting_api->get_has_recommendation();
	$index_engine             = $index_hosting_api->get_search_engine();
	$index_engine_name        = $option_indexes->get_search_engine_name( $index_engine );
}


/* Include the current engine's recommendation admin js */
if ( $index_has_recommendation ) {
	require_once( sprintf( '%s/admin_options.inc.js.php', $index_engine ) );
} ?>


<style>
    .wpsolr_recommendations_is_new {
        border: 1px solid gray;
        background-color: #e5e5e5;
    }

    .wpsolr-remove-if-hidden {
        display: none;
    }

    #extension_recommendations_settings_form .col_left {
        width: 10%;
    }

    #extension_recommendations_settings_form .col_right {
        width: 77%;
    }
</style>

<form id="wpsolr_form_new_recommendations" method="post">
    <input type="hidden" name="wpsolr_new_recommendations"
           value="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option_Indexes::generate_uuid() ); ?>"/>
</form>

<div wdm-vertical-tabs-contentid="extension_groups-options" class="wdm-vertical-tabs-content wpsolr-col-9">
    <form action="options.php" method="POST" id='extension_recommendations_settings_form'>
		<?php
		WPSOLR_Option_View::output_form_index_hidden_fields( $settings_fields_name );
		?>

        <div class='wrapper'>
            <h4 class='head_div'><?php WPSOLR_Escape::echo_esc_html( WPSOLR_Option_View::get_indexes_html( 'Recommendations',
					[
						'is_show_default'              => true,
						'default_label'                => 'Choose a Recommendation index',
						'is_show_recommendations_only' => true,
					] ) ); ?> </h4>

			<?php if ( $index_has_recommendation ) { ?>
                <div class="wdm_note">
                    In this section, you will configure recommendations. Also named autocompletion.
                    <ol>
                        <li>
                            Define on which search box(es) recommendations will appear, with jQuery selectors.
                        </li>
                        <li>
                            Select the recommendations type: keywords, flat results, grouped results.
                        </li>
                        <li>
                            Select the recommendations layout, or create your own to match your theme style.
                        </li>
                    </ol>

                    You can define several recommendation definitions, and order them by drag&drop. The first definition
                    that
                    matches your search box(es) jQuery selector will be activated.
                </div>

                <div class="wdm_row">
                    <div class='col_left'>
                        <input type="button"
                               name="add_recommendation"
                               id="add_recommendation"
                               class="button-primary"
                               value="Configure new recommendations"
                               onclick="jQuery('#wpsolr_form_new_recommendations').submit();"
                        />
                    </div>
                    <div class='col_right'>
                    </div>
                    <div class="clear"></div>
                </div>

                <ul class="ui-sortable">
					<?php foreach (
						$recommendations

						as $recommendation_uuid => $recommendation
					) {
						$is_new                           = isset( $recommendation['is_new'] );
						$recommendation_label             = isset( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_LABEL ] ) ? $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_LABEL ] : 'rename me';
						$recommendation_jquery_selector   = isset( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_JQUERY_SELECTOR ] ) ? $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_JQUERY_SELECTOR ] : '';
						$recommendation_scenario          = isset( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_RECOMBEE_SCENARIO ] ) ? $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_RECOMBEE_SCENARIO ] : '';
						$recommendation_serving_config_id = isset( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_GOOGLE_RETAIL_SERVING_CONFIG_ID ] ) ? $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_GOOGLE_RETAIL_SERVING_CONFIG_ID ] : '';
						$recommendation_type              = isset( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_TYPE ] ) ? $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_TYPE ] : WPSOLR_Option::OPTION_SEARCH_SUGGEST_CONTENT_TYPE_NONE;
						$recommendation_layout            = isset( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_LAYOUT_ID ] ) ? $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_LAYOUT_ID ] : WPSOLR_Option_Recommendations::OPTION_RECOMMENDATION_LAYOUT_ID_KEYWORDS_FANCY;
						$recommendation_nb                = isset( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_NB ] ) ? $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_NB ] : '10';
						$recommendation_image_width_pct   = isset( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_IMAGE_WIDTH_PCT ] ) ? $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_IMAGE_WIDTH_PCT ] : '10';
						$recommendation_custom_file       = isset( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_CUSTOM_TEMPLATE_FILE ] ) ? $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_CUSTOM_TEMPLATE_FILE ] : '';
						$recommendation_is_active         = isset( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_IS_ACTIVE ] );
						$recommendation_order_by          = isset( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_ORDER_BY ] ) ? $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_ORDER_BY ] : WPSOLR_Option::OPTION_RECOMMENDATION_ORDER_BY_CONTENT;
						$recommendation_is_archive        = isset( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_IS_ARCHIVE ] );
						$recommendation_is_show_text      = isset( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_IS_SHOW_TEXT ] );
						$recommendation_custom_css        = ! empty( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_CUSTOM_CSS ] ) ? $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_CUSTOM_CSS ] :
							sprintf( '<!-- <style> .c%s li a {color: red;} </style> -->', $recommendation_uuid );
						?>
                        <li class="wpsolr_recommendations wpsolr-sorted ui-sortable-handle <?php WPSOLR_Escape::echo_escaped( $is_new ? 'wpsolr_recommendations_is_new' : '' ); ?>">
							<?php if ( $is_new ) { ?>
                                <input type="hidden"
                                       id="wpsolr_recommendations_new_uuid"
                                       value="<?php WPSOLR_Escape::echo_esc_attr( $recommendation_uuid ); ?>"
                                />
							<?php } ?>

                            <div data-wpsolr-recommendations-label="<?php WPSOLR_Escape::echo_esc_attr( $recommendation_label ); ?>">
                                <input type="button"
                                       style="float:right;"
                                       name="delete_recommendations"
                                       class="c_<?php WPSOLR_Escape::echo_esc_attr( $recommendation_uuid ); ?> wpsolr-recommendations-delete-button button-secondary"
                                       value="Delete"
                                       onclick="jQuery(this).closest('.wpsolr_recommendations').remove();"
                                />
                                <h4 class='head_div'>
                                    Recommendations: <?php WPSOLR_Escape::echo_esc_html( $recommendation_label ); ?> </h4>


								<?php if ( ! $is_new ) { ?>
                                    <div class="wdm_row">
                                        <div class='col_left'>
                                            Shortcode
                                        </div>
                                        <div class='col_right'>

                                            <input type='text' readonly
                                                   value="<?php WPSOLR_Escape::echo_escaped( WPSOLR_Shortcode_Recommendation::get_shortcode_html( $recommendation_uuid, $recommendation_label, true ) ); ?>"
                                            >

                                        </div>
                                        <div class="clear"></div>
                                    </div>
								<?php } ?>

                                <div class="wdm_row">
                                    <div class='col_left'>
                                        Status
                                    </div>
                                    <div class='col_right'>
                                        <label>
                                            <input type='checkbox'
                                                   name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATIONS_RECOMMENDATIONS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $recommendation_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_IS_ACTIVE ); ?>]'
                                                   value='is_active'
												<?php checked( $recommendation_is_active ); ?>>
                                            Is active
                                        </label>
                                    </div>
                                    <div class="clear"></div>
                                </div>

                                <div class="wdm_row">
                                    <div class='col_left'>
                                        Label
                                    </div>
                                    <div class='col_right'>
                                        <input type='text'
                                               name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATIONS_RECOMMENDATIONS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $recommendation_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_LABEL ); ?>]'
                                               placeholder="Enter a label"
                                               value="<?php WPSOLR_Escape::echo_esc_attr( $recommendation_label ); ?>"
                                        >

                                    </div>
                                    <div class="clear"></div>
                                </div>

                                <div class="wdm_row">
                                    <div class='col_left'>
                                        jQuery selectors
                                    </div>
                                    <div class='col_right'>
                                        <input type='text'
                                               name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATIONS_RECOMMENDATIONS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $recommendation_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_JQUERY_SELECTOR ); ?>]'
                                               placeholder="Enter a jQuery"
                                               value="<?php WPSOLR_Escape::echo_esc_attr( $recommendation_jquery_selector ); ?>">

                                    </div>
                                    <div class="clear"></div>
                                </div>


								<?php
								/* Include the current engine's recommendation admin template */
								require( sprintf( '%s/admin_options.inc.php', $index_engine ) );
								?>

                            </div>
                        </li>
					<?php } ?>
                </ul>

                <div class='wdm_row'>
                    <div class="submit">
                        <input id="save_recommendations"
                               type="submit"
                               class="button-primary wdm-save"
                               value="Save Recommendations"/>
                    </div>
                </div>
			<?php } else { ?>
                <div class="wdm_note">
					<?php if ( ! empty( $current_index ) ) { ?>
                        <p><?php WPSOLR_Escape::echo_esc_html( $index_engine_name ); ?> indices does not support
                            recommendations.</p>
					<?php } ?>
                    <p>Please select or create an index from a recommendation engine.</p>
                </div>
			<?php } ?>

        </div>

    </form>
</div>