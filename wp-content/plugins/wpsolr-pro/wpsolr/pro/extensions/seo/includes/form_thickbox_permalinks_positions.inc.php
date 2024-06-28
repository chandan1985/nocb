<?php

use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\utilities\WPSOLR_Escape;
use wpsolr\core\classes\utilities\WPSOLR_Option;

$wpsolr_permalinks_positions = WPSOLR_Service_Container::getOption()->get_facets_seo_permalink_positions();
?>

<script>

    jQuery(document).ready(function () {

        // Activate drag&drop
        jQuery("#id_sortable_permalinks_positions").sortable({
            update: function (event, ui) {
                // Update results after each drop
                var results = '';
                jQuery('#id_sortable_permalinks_positions li input').each(function () {
                    results += '{{' + jQuery(this).val() + '}} '
                });
                jQuery('#id_permalinks_positions_result').html(results);
            }

        })
        ;

        jQuery(document).on("click", "#id_button_close_form_permalinks_positions", function (e) {
            tb_remove(); // Close the thickbox
        });

    });
</script>


<div id="form_permalinks_positions" style="display:none;" class="wdm-vertical-tabs-content">

    <div class="wpsolr_form_license">

        <div class='wrapper wpsolr_license_popup'><h4 class='head_div'>Position your facets in permalink urls</h4>
            <div class="wdm_note">
                Do you prefer /red-tshirt or /tshirt-red for your permalink ?<br/>
                <ol>
                    <li>Drag & Drop your facets to setup the permalink positions</li>
                    <li>A facet on top will have a position "0", which is the left part of the permalink url</li>
                </ol>

            </div>

            <hr/>
            <div class="wdm_row">

                <ul id="id_sortable_permalinks_positions" class="wdm_ul connectedSortable">
					<?php
					$permalinks_positions_results = [];

					// Already positioned
					foreach ( $wpsolr_permalinks_positions as $selected_val ) { ?>
						<?php $permalinks_positions_results[] = $selected_val; ?>
                        <li class="ui-state-default facets">
                            <input type='hidden'
                                   name='<?php WPSOLR_Escape::echo_esc_attr( $view_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_FACET_FACETS_SEO_PERMALINK_POSITION ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $selected_val ); ?>]'
                                   value='<?php WPSOLR_Escape::echo_esc_attr( $selected_val ); ?>'/>
							<?php WPSOLR_Escape::echo_esc_html( $selected_val ); ?>
                        </li>
					<?php } ?>

					<?php
					// Not yet positioned among the selected facets
					foreach ( $selected_array as $selected_val ) { ?>
						<?php if ( empty( $wpsolr_permalinks_positions[ $selected_val ] ) ) { ?>
							<?php $permalinks_positions_results[] = $selected_val; ?>
                            <li class="ui-state-default facets">
                                <input type='hidden'
                                       name='<?php WPSOLR_Escape::echo_esc_attr( $view_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_FACET_FACETS_SEO_PERMALINK_POSITION ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $selected_val ); ?>]'
                                       value='<?php WPSOLR_Escape::echo_esc_attr( $selected_val ); ?>'/>
								<?php WPSOLR_Escape::echo_esc_html( $selected_val ); ?>
                            </li>
						<?php } ?>
					<?php } ?>
                </ul>

                <br/>
                Your permalinks will be ordered like:
                <div id="id_permalinks_positions_result" class="wdm_note">
					<?php
					foreach ( $permalinks_positions_results as $selected_val ) {
						WPSOLR_Escape::echo_escaped( sprintf( ' {{%s}}', WPSOLR_Escape::esc_html( $selected_val ) ) );
					}
					?>
                </div>
                <br/>

            </div>
            <div class="clear"></div>
            <input id="id_button_close_form_permalinks_positions" type="button" class="button-primary" value="Close"/>

        </div>


    </div>

</div>
