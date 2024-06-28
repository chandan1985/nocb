<?php

use wpsolr\core\classes\extensions\licenses\OptionLicenses;
use wpsolr\core\classes\ui\layout\WPSOLR_UI_Layout_Abstract;
use wpsolr\core\classes\utilities\WPSOLR_Escape;
use wpsolr\core\classes\utilities\WPSOLR_Option;

?>

<div style="display:none"
     class="wpsolr-remove-if-hidden wpsolr_facet_type <?php WPSOLR_Escape::echo_esc_attr( WPSOLR_UI_Layout_Abstract::get_css_class_feature_layouts( WPSOLR_UI_Layout_Abstract::FEATURE_SEO_TEMPLATE ) ); ?>">

    <input type='text'
           class="wpsolr-remove-if-empty"
           placeholder="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::FACET_LABEL_SEO_TEMPLATE ); ?>"
           name='<?php WPSOLR_Escape::echo_esc_attr( $view_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_FACET_FACETS_SEO_PERMALINK_TEMPLATE ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $selected_val ); ?>]'
           value='<?php WPSOLR_Escape::echo_esc_attr( empty( $facet_seo_template ) ? WPSOLR_Option::FACET_LABEL_SEO_TEMPLATE : $facet_seo_template ); ?>'
		<?php WPSOLR_Escape::echo_esc_attr( $license_manager->get_license_enable_html_code( OptionLicenses::LICENSE_PACKAGE_YOAST_SEO ) ); ?>
    />
    <p>
        Define a permalink template for this facet. Use the
        variable <?php WPSOLR_Escape::echo_esc_html( WPSOLR_Option::FACET_LABEL_TEMPLATE_VAR_VALUE ); ?> to replace with
        the current facet item
        localized value.
    </p>

</div>