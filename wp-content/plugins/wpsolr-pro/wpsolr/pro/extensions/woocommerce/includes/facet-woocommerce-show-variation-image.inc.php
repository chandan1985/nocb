<?php

use wpsolr\core\classes\extensions\licenses\OptionLicenses;
use wpsolr\core\classes\utilities\WPSOLR_Escape;
use wpsolr\core\classes\utilities\WPSOLR_Option;

?>

<div class="wpsolr-remove-if-hidden">

	<?php
	$is_show_variation_image = isset( $selected_facets_is_show_variation_image[ $selected_val ] );
	?>

    <div class="wdm_row" style="top-margin:5px;">
        <div class='col_left'>
			<?php WPSOLR_Escape::echo_escaped( $license_manager->show_premium_link( true, OptionLicenses::LICENSE_PACKAGE_PREMIUM, 'Show variations image instead of thumnail image', true ) ); ?>
        </div>
        <div class='col_right'>
            <input type='checkbox'
                   name='<?php WPSOLR_Escape::echo_esc_attr( $view_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_FACET_FACETS_IS_SHOW_VARIATION_IMAGE ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $selected_val ); ?>]'
                   value='1'
				<?php WPSOLR_Escape::echo_esc_attr( $license_manager->get_license_enable_html_code( OptionLicenses::LICENSE_PACKAGE_PREMIUM ) ); ?>
				<?php checked( $is_show_variation_image ); ?>
            />
            If this attribute is used as a filter during the search,
            the products thumbnails are replaced by the variant image with the same attribute value.<br>
            For instance, to show variants yellow shirts images if the visitor filters results with the yellow
            facet.

        </div>
        <div class="clear"></div>
    </div>
</div>
