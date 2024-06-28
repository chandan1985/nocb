<?php

use wpsolr\pro\extensions\event_trackings\WPSOLR_Option_Event_Trackings;
use wpsolr\core\classes\extensions\indexes\WPSOLR_Option_Indexes;
use wpsolr\core\classes\extensions\view\WPSOLR_Option_View;
use wpsolr\core\classes\extensions\WPSOLR_Extension;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\utilities\WPSOLR_Escape;
use wpsolr\core\classes\utilities\WPSOLR_Option;

/**
 * Included file to display admin options
 */
global $license_manager;

WPSOLR_Extension::require_once_wpsolr_extension( WPSOLR_Extension::OPTION_EVENT_TRACKINGS, true );

$extension_options_name = WPSOLR_Option_View::get_view_uuid_options_name( WPSOLR_Option::OPTION_EVENT_TRACKINGS );
$settings_fields_name   = 'extension_event_trackings_opt';

$event_trackings = WPSOLR_Service_Container::getOption()->get_option_event_trackings_event_trackings();
if ( isset( $_POST['wpsolr_new_event_trackings'] ) && ! isset( $crons[ $_POST['wpsolr_new_event_trackings'] ] ) ) {
	$event_trackings = array_merge( [ sanitize_text_field( $_POST['wpsolr_new_event_trackings'] ) => [ 'is_new' => true ] ], $event_trackings );
}

?>


<style>
    .wpsolr_event_trackings_is_new {
        border: 1px solid gray;
        background-color: #e5e5e5;
    }

    .wpsolr-remove-if-hidden {
        display: none;
    }

    #extension_event_trackings_settings_form .col_left {
        width: 10%;
    }

    #extension_event_trackings_settings_form .col_right {
        width: 77%;
    }
</style>

<form id="wpsolr_form_new_event_trackings" method="post">
    <input type="hidden" name="wpsolr_new_event_trackings"
           value="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option_Indexes::generate_uuid() ); ?>"/>
</form>

?>

<div wdm-vertical-tabs-contentid="extension_groups-options" class="wdm-vertical-tabs-content wpsolr-col-9">
    <form action="options.php" method="POST" id='extension_event_trackings_settings_form'>
		<?php
		WPSOLR_Option_View::output_form_view_hidden_fields( $settings_fields_name );
		?>

        <div class='wrapper'>
            <h4 class='head_div'><?php WPSOLR_Escape::echo_escaped( WPSOLR_Option_View::get_views_html( 'Events tracking' ) ); ?> </h4>

			<?php
			if ( ! WPSOLR_Option_Event_Trackings::get_is_view_supported() ) {
				?>
                <div class="wdm_note">
                    <span>This search engine does not support events.</span>
                </div>
				<?php
			} else {
			?>
            <div class="wdm_note">
                In this section, you will configure events tracking.
                <ol>
                    <li>
                        (soon) Define on which UI elements events will be collected and used by the search, with CSS
                        selectors.
                    </li>
                </ol>
            </div>

            <div class="wdm_row">
                <div class='col_left'>
                    <input type="button"
                           name="add_event_tracking"
                           id="add_event_tracking"
                           class="button-primary"
                           value="Configure new event tracking"
                           onclick="jQuery('#wpsolr_form_new_event_trackings').submit();"
                    />
                </div>
                <div class='col_right'>
                </div>
                <div class="clear"></div>
            </div>

            <ul class="ui-sortable">
				<?php foreach (
					$event_trackings as $event_tracking_uuid => $event_tracking
				) {
					$is_new                         = isset( $event_tracking['is_new'] );
					$event_tracking_label           = isset( $event_tracking[ WPSOLR_Option::OPTION_EVENT_TRACKING_LABEL ] ) ? $event_tracking[ WPSOLR_Option::OPTION_EVENT_TRACKING_LABEL ] : 'rename me';
					$event_tracking_jquery_selector = isset( $event_tracking[ WPSOLR_Option::OPTION_EVENT_TRACKING_JQUERY_SELECTOR ] ) ? $event_tracking[ WPSOLR_Option::OPTION_EVENT_TRACKING_JQUERY_SELECTOR ] : '';
					$event_tracking_type            = isset( $event_tracking[ WPSOLR_Option::OPTION_EVENT_TRACKING_TYPE ] ) ? $event_tracking[ WPSOLR_Option::OPTION_EVENT_TRACKING_TYPE ] : '';
					$event_tracking_is_active       = isset( $event_tracking[ WPSOLR_Option::OPTION_EVENT_TRACKING_IS_ACTIVE ] );
					?>
                    <li class="wpsolr_event_trackings wpsolr-sorted ui-sortable-handle <?php WPSOLR_Escape::echo_escaped( $is_new ? 'wpsolr_event_trackings_is_new' : '' ); ?>">
						<?php if ( $is_new ) { ?>
                            <input type="hidden"
                                   id="wpsolr_event_trackings_new_uuid"
                                   value="<?php WPSOLR_Escape::echo_esc_attr( $event_tracking_uuid ); ?>"
                            />
						<?php } ?>

                        <div data-wpsolr-event_trackings-label="<?php WPSOLR_Escape::echo_esc_attr( $event_tracking_label ); ?>">
                            <input type="button"
                                   style="float:right;"
                                   name="delete_event_trackings"
                                   class="c_<?php WPSOLR_Escape::echo_esc_attr( $event_tracking_uuid ); ?> wpsolr-event_trackings-delete-button button-secondary"
                                   value="Delete"
                                   onclick="jQuery(this).closest('.wpsolr_event_trackings').remove();"
                            />
                            <h4 class='head_div'>"Events
                                tracking: <?php WPSOLR_Escape::echo_esc_html( $event_tracking_label ); ?> </h4>


                            <div class="wdm_row">
                                <div class='col_left'>
                                    Status
                                </div>
                                <div class='col_right'>
                                    <label>
                                        <input type='checkbox'
                                               name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_EVENT_TRACKINGS_EVENT_TRACKINGS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $event_tracking_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_EVENT_TRACKING_IS_ACTIVE ); ?>]'
                                               value='is_active'
											<?php checked( $event_tracking_is_active ); ?>>
                                        Is active
                                    </label>
                                </div>
                                <div class="clear"></div>
                            </div>

                            <!--
                            <div class="wdm_row">
                                <div class='col_left'>
                                    Event label
                                </div>
                                <div class='col_right'>
                                    <input type='text'
                                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_EVENT_TRACKINGS_EVENT_TRACKINGS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $event_tracking_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_EVENT_TRACKING_LABEL ); ?>]'
                                           placeholder="Enter a Number"
                                           value="<?php WPSOLR_Escape::echo_esc_attr( $event_tracking_label ); ?>"
                                    >

                                </div>
                                <div class="clear"></div>
                            </div>
                            -->

                            <div class="wdm_row">
                                <div class='col_left'>
                                    Tracked event
                                </div>
                                <div class='col_right'>
                                    <select name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_EVENT_TRACKINGS_EVENT_TRACKINGS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $event_tracking_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_EVENT_TRACKING_TYPE ); ?>]'
                                    >
										<?php
										$options = WPSOLR_Option_Event_Trackings::get_type_definitions();

										foreach ( $options as $option ) {
											$selected = ( $option['code'] === $event_tracking_type ) ? 'selected' : '';
											$disabled = ( $option['disabled'] ?? true ) ? 'disabled' : '';
											?>
                                            <option
                                                    value="<?php WPSOLR_Escape::echo_esc_attr( $option['code'] ); ?>"
												<?php WPSOLR_Escape::echo_esc_attr( $selected ); ?>
												<?php WPSOLR_Escape::echo_esc_attr( $disabled ); ?>
                                            >
												<?php WPSOLR_Escape::echo_esc_html( $option['label'] ); ?>
                                            </option>
										<?php } ?>

                                    </select>
                                </div>
                                <div class="clear"></div>
                            </div>

                            <div class="wdm_row">
                                <div class='col_left'>
                                    UI objects tracking the event
                                </div>
                                <div class='col_right'>
                                    <input type='text'
                                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_EVENT_TRACKINGS_EVENT_TRACKINGS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $event_tracking_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_EVENT_TRACKING_JQUERY_SELECTOR ); ?>]'
                                           placeholder="Enter a CSS selector: .class1, a.class2, .class3.class4"
                                           value="<?php WPSOLR_Escape::echo_esc_attr( $event_tracking_jquery_selector ); ?>">

                                </div>
                                <div class="clear"></div>
                            </div>

                    </li>
				<?php } ?>
            </ul>

            <div class='wdm_row'>
                <div class="submit">
                    <input id="save_event_trackings"
                           type="submit"
                           class="button-primary wdm-save"
                           value="Save Events tracking"/>
                </div>
            </div>
        </div>
	<?php
	}
	?>

    </form>
</div>