<?php

use wpsolr\core\classes\extensions\licenses\OptionLicenses;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\ui\layout\WPSOLR_UI_Layout_Abstract;
use wpsolr\core\classes\utilities\WPSOLR_Escape;
use wpsolr\core\classes\utilities\WPSOLR_Option;

?>

<div style="display:none"
     class="wpsolr-remove-if-hidden wpsolr_facet_type <?php WPSOLR_Escape::echo_esc_attr( WPSOLR_UI_Layout_Abstract::get_css_class_feature_layouts( WPSOLR_UI_Layout_Abstract::FEATURE_SKIN ) ); ?>">


	<?php
	$facet_skin            = WPSOLR_Service_Container::getOption()->get_facets_skin_value( $selected_val );
	$facet_skins_available = ( ! empty( $facet_layout_skins_available ) && ! empty( $facet_layout_skins_available[ $current_layout_id ] ) )
		? $facet_layout_skins_available[ $current_layout_id ]
		: [];
	if ( ! empty( $facet_skins_available ) && empty( $facet_skins_available[ $facet_skin ] ) ) {
		// Add a choice text if no skin is selected
		$facet_skins_available = [ '' => [ 'label' => 'Choose a skin' ] ] + $facet_skins_available;
	}
	?>

	<?php if ( ! empty( $facet_skins_available ) ) { ?>
        <div class="wdm_row" style="top-margin:5px;">
            <div class='col_left'>
				<?php WPSOLR_Escape::echo_escaped( $license_manager->show_premium_link( true, OptionLicenses::LICENSE_PACKAGE_THEME, 'Skin', true ) ); ?>
            </div>
            <div class='col_right'>

                <select name='<?php WPSOLR_Escape::echo_esc_attr( $view_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_FACET_FACETS_SKIN ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $selected_val ); ?>]'
                        class="wpsolr-remove-if-empty"
                        data-wpsolr-empty-value=""
					<?php WPSOLR_Escape::echo_esc_attr( $license_manager->get_license_enable_html_code( OptionLicenses::LICENSE_PACKAGE_THEME ) ); ?>
                >
					<?php foreach ( $facet_skins_available as $skin_id => $skin ) { ?>
                        <option value="<?php WPSOLR_Escape::echo_esc_attr( $skin_id ); ?>" <?php selected( $facet_skin, $skin_id ); ?>><?php WPSOLR_Escape::echo_esc_html( $skin['label'] ); ?></option>
					<?php } ?>
                </select>

            </div>
            <div class="clear"></div>
        </div>
	<?php } ?>
</div>
