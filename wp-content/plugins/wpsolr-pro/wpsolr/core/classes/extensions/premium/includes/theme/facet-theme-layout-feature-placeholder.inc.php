<?php

use wpsolr\core\classes\extensions\licenses\OptionLicenses;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\ui\layout\WPSOLR_UI_Layout_Abstract;
use wpsolr\core\classes\utilities\WPSOLR_Escape;
use wpsolr\core\classes\utilities\WPSOLR_Option;

?>

<div style="display:none"
     class="wpsolr-remove-if-hidden wpsolr_facet_type <?php WPSOLR_Escape::echo_esc_attr( WPSOLR_UI_Layout_Abstract::get_css_class_feature_layouts( WPSOLR_UI_Layout_Abstract::FEATURE_PLACEHOLDER ) ); ?>">

	<?php
	$facet_placeholder = WPSOLR_Service_Container::getOption()->get_facets_placeholder_value( $selected_val );
	?>

    <div class="wdm_row" style="top-margin:5px;">
        <div class='col_left'>
			<?php WPSOLR_Escape::echo_escaped( $license_manager->show_premium_link( true, OptionLicenses::LICENSE_PACKAGE_PREMIUM, 'Placeholder', true ) ); ?>
        </div>
        <div class='col_right'>

           <textarea
                   name='<?php WPSOLR_Escape::echo_esc_attr( $view_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_FACET_FACETS_PLACEHOLDER ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $selected_val ); ?>]'
                   class="wpsolr-remove-if-empty"
                   data-wpsolr-empty-value=""
	           <?php WPSOLR_Escape::echo_esc_attr( $license_manager->get_license_enable_html_code( OptionLicenses::LICENSE_PACKAGE_PREMIUM ) ); ?>
           ><?php WPSOLR_Escape::echo_esc_textarea( $facet_placeholder ); ?></textarea>

            Text displayed when no value is selected in the select box.

        </div>
        <div class="clear"></div>
    </div>
</div>
