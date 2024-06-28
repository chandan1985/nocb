<?php

use wpsolr\core\classes\extensions\licenses\OptionLicenses;
use wpsolr\core\classes\ui\layout\WPSOLR_UI_Layout_Abstract;
use wpsolr\core\classes\utilities\WPSOLR_Escape;
use wpsolr\core\classes\utilities\WPSOLR_Option;

?>

<div style="display:none"
     class="wpsolr-remove-if-hidden wpsolr_facet_type <?php WPSOLR_Escape::echo_esc_attr( WPSOLR_UI_Layout_Abstract::get_css_class_feature_layouts( WPSOLR_UI_Layout_Abstract::FEATURE_SEO_TEMPLATE_LOCALIZATION ) ); ?>">

    <input type='text' class="wpsolr-remove-if-empty"
           placeholder="<?php WPSOLR_Escape::echo_esc_attr( $facet_seo_template ); ?>"
           name='<?php WPSOLR_Escape::echo_esc_attr( $view_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_FACET_FACETS_SEO_PERMALINK_ITEMS_TEMPLATE ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $selected_val ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $facet_item_label ); ?>]'
           value='<?php WPSOLR_Escape::echo_esc_attr( $facet_item_seo_template ); ?>'
		<?php WPSOLR_Escape::echo_esc_attr( $license_manager->get_license_enable_html_code( OptionLicenses::LICENSE_PACKAGE_YOAST_SEO ) ); ?>
    />
    <p>
        Define a permalink just for "<?php WPSOLR_Escape::echo_esc_html( $facet_item_label ); ?>".
    </p>
</div>