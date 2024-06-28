<?php

use wpsolr\core\classes\extensions\licenses\OptionLicenses;
use wpsolr\core\classes\utilities\WPSOLR_Escape;
use wpsolr\core\classes\utilities\WPSOLR_Option;
use wpsolr\pro\extensions\theme\layout\color_picker\WPSOLR_UI_Layout_Color_Picker;

?>

<div style="display:none;"
     class="wpsolr-remove-if-hidden wpsolr_facet_type
         <?php WPSOLR_Escape::echo_esc_attr( WPSOLR_UI_Layout_Color_Picker::CHILD_LAYOUT_ID ); ?>
">
    <input type='text' class="wpsolr-remove-if-empty wpsolr-color-picker"
           name='<?php WPSOLR_Escape::echo_esc_attr( $view_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_FACET_FACETS_ITEMS_LABEL ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $selected_val ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $facet_item_label ); ?>]'
           value='<?php WPSOLR_Escape::echo_esc_attr( $facet_label ); ?>'
		<?php WPSOLR_Escape::echo_esc_attr( $license_manager->get_license_enable_html_code( OptionLicenses::LICENSE_PACKAGE_PREMIUM ) ); ?>
    />
    <p>
		<?php if ( empty( $facet_label ) ) { ?>
            Select a color to associate to "<?php WPSOLR_Escape::echo_esc_html( $facet_item_label ); ?>".
		<?php } else { ?>
            Color "<?php WPSOLR_Escape::echo_esc_html( $facet_label ); ?>" is associated to "<?php WPSOLR_Escape::echo_esc_html( $facet_item_label ); ?>".
		<?php } ?>
    </p>
</div>