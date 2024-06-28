<?php

use wpsolr\core\classes\extensions\licenses\OptionLicenses;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\utilities\WPSOLR_Escape;

?>

<div class="wpsolr-metabox-row-content">
    <label for="<?php WPSOLR_Escape::echo_esc_attr( self::METABOX_FIELD_IS_DO_INDEX_ACF_FIELD_FILES ); ?>">
        <input type="checkbox"
               name="<?php WPSOLR_Escape::echo_esc_attr( self::METABOX_FIELD_IS_DO_INDEX_ACF_FIELD_FILES ); ?>"
               id="<?php WPSOLR_Escape::echo_esc_attr( self::METABOX_FIELD_IS_DO_INDEX_ACF_FIELD_FILES ); ?>"
               value="<?php WPSOLR_Escape::echo_esc_attr( self::METABOX_CHECKBOX_YES ); ?>" <?php if ( isset ( $post_meta[ self::METABOX_FIELD_IS_DO_INDEX_ACF_FIELD_FILES ] ) ) {
			checked( $post_meta[ self::METABOX_FIELD_IS_DO_INDEX_ACF_FIELD_FILES ][0], self::METABOX_CHECKBOX_YES );
		} ?>
			<?php WPSOLR_Escape::echo_esc_html( $license_manager->get_license_enable_html_code( OptionLicenses::LICENSE_PACKAGE_ACF ) ); ?>
        />
		<?php
		$label = WPSOLR_Service_Container::getOption()->get_plugin_acf_is_index_all_file_fields() ? "Don't search in ACF fields file" : "Search in ACF fields file";
		WPSOLR_Escape::echo_escaped( $license_manager->show_premium_link( true, OptionLicenses::LICENSE_PACKAGE_ACF, _x( $label, 'wpsolr' ), true, true ) );
		?>
    </label>
</div>
