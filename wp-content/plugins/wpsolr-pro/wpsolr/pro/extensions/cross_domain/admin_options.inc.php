<?php

use wpsolr\core\classes\extensions\indexes\WPSOLR_Option_Indexes;
use wpsolr\core\classes\extensions\licenses\OptionLicenses;
use wpsolr\core\classes\extensions\WPSOLR_Extension;
use wpsolr\core\classes\models\WPSOLR_Model_Builder;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\utilities\WPSOLR_Escape;
use wpsolr\core\classes\utilities\WPSOLR_Help;
use wpsolr\core\classes\utilities\WPSOLR_Option;

global $license_manager;

WPSOLR_Extension::require_once_wpsolr_extension( WPSOLR_Extension::OPTION_CROSS_DOMAIN, true );

$extension_options_name = WPSOLR_Option::OPTION_CROSS_DOMAIN;
$settings_fields_name   = 'extension_cross_domain_opt';

$options     = WPSOLR_Service_Container::getOption()->get_option_cross_domain();
$galaxy_mode = WPSOLR_Service_Container::getOption()->get_cross_domain_galaxy_mode();

$is_plugin_active = WPSOLR_Extension::is_plugin_active( WPSOLR_Extension::OPTION_CROSS_DOMAIN );
?>

<?php
$option_indexes = new WPSOLR_Option_Indexes();
$indexes        = $option_indexes->get_indexes();
$post_types     = WPSOLR_Service_Container::getOption()->get_option_index_post_types();
$models         = WPSOLR_Model_Builder::get_model_type_objects( $post_types );

$ai_apis = WPSOLR_Service_Container::getOption()->get_option_ai_api_apis();
if ( isset( $_POST['wpsolr_new_ai_api'] ) && ! isset( $ai_apis[ $_POST['wpsolr_new_ai_api'] ] ) ) {
	$ai_apis = array_merge( [ sanitize_text_field( $_POST['wpsolr_new_ai_api'] ) => [ 'is_new' => true ] ], $ai_apis );
}

?>

<div wdm-vertical-tabs-contentid="extension_groups-options" class="wdm-vertical-tabs-content wpsolr-col-9">
    <form action="options.php" method="POST" id='extension_groups_settings_form'>
		<?php
		settings_fields( $settings_fields_name );
		?>

        <div class='wrapper'>
            <h4 class='head_div'>Cross-domain search extension</h4>

            <div class="wdm_note">
                Search across several WordPress domains, in a multisites or independant network.
            </div>

            <div class="wdm_row">
                <div class='col_left'>
                    Activate the Cross-domain extension
					<?php WPSOLR_Escape::echo_escaped( WPSOLR_Help::get_help( WPSOLR_Help::HELP_ADDON_CROSS_DOMAIN ) ); ?>
                </div>
                <div class='col_right'>
                    <input type='checkbox' <?php WPSOLR_Escape::echo_escaped( $is_plugin_active ? '' : 'readonly' ); ?>
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[is_extension_active]'
                           value='is_extension_active'
						<?php checked( 'is_extension_active', isset( $options['is_extension_active'] ) ? $options['is_extension_active'] : '' ); ?>>
                </div>
                <div class="clear"></div>
            </div>


            <div class="wdm_row">
                <div class='col_left'>
                    This search is part of a network search
                </div>
                <div class='col_right'>
                    <select
                            name="<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_CROSS_DOMAIN_GALAXY_MODE ); ?>]">
						<?php
						$options = [
							[
								'code'  => '',
								'label' => 'No'
							],
							[
								'code'  => WPSOLR_Option::OPTION_CROSS_DOMAIN_IS_GALAXY_SLAVE,
								'label' => 'Yes, as one of local searches (suggestions will not work)',
							],
							[
								'code'  => WPSOLR_Option::OPTION_CROSS_DOMAIN_IS_GALAXY_MASTER,
								'label' => 'Yes, as the global search (only with ajax)',
							],
						];

						$search_galaxy_mode = WPSOLR_Service_Container::getOption()->get_cross_domain_galaxy_mode();
						foreach ( $options as $option ) {
							$selected = $option['code'] === $search_galaxy_mode ? 'selected' : '';
							$disabled = isset( $option['disabled'] ) ? $option['disabled'] : '';
							?>
                            <option
                                    value="<?php WPSOLR_Escape::echo_esc_attr( $option['code'] ); ?>"
								<?php WPSOLR_Escape::echo_esc_attr( $selected ); ?>
								<?php WPSOLR_Escape::echo_esc_attr( $disabled ); ?>>
								<?php WPSOLR_Escape::echo_esc_html( $option['label'] ); ?>
                            </option>
						<?php } ?>

                    </select>
                    <ul>
                        <li>- The global site searches in all local sites data</li>
                        <li>- Each local site searches in it's own data</li>
                    </ul>
                </div>
                <div class="clear"></div>
            </div>

            <div class='wdm_row'>
                <div class="submit">
					<?php if ( $license_manager->get_license_is_activated( OptionLicenses::LICENSE_PACKAGE_CROSS_DOMAIN ) ) { ?>
                        <div class="wpsolr_premium_block_class">
							<?php WPSOLR_Escape::echo_escaped( $license_manager->show_premium_link( true, OptionLicenses::LICENSE_PACKAGE_CROSS_DOMAIN, OptionLicenses::TEXT_LICENSE_ACTIVATED, true, true ) ); ?>
                        </div>
                        <input
                                name="save_ai_api"
                                id="save_ai_api" type="submit"
                                class="button-primary wdm-save"
                                value="Save Options"/>
                        <span class="wpsolr_err wpsolr_global_error_msg"></span>
					<?php } else { ?>
						<?php WPSOLR_Escape::echo_escaped( $license_manager->show_premium_link( true, OptionLicenses::LICENSE_PACKAGE_CROSS_DOMAIN, 'Save Options', true, true ) ); ?>
                        <br/>
					<?php } ?>
                </div>
            </div>
        </div>

    </form>
</div>
