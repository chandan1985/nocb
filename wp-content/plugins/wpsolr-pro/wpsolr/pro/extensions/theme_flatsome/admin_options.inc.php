<?php

use wpsolr\core\classes\extensions\licenses\OptionLicenses;
use wpsolr\core\classes\extensions\WPSOLR_Extension;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\utilities\WPSOLR_Escape;
use wpsolr\core\classes\utilities\WPSOLR_Help;
use wpsolr\core\classes\utilities\WPSOLR_Option;

/**
 * Included file to display admin options
 */

global $license_manager;

WPSOLR_Extension::require_once_wpsolr_extension( WPSOLR_Extension::EXTENSION_THEME_FLATSOME, true );

$extension_options_name = WPSOLR_Option::OPTION_THEME_FLATSOME;

$extension_options = WPSOLR_Service_Container::getOption()->get_option_theme_flatsome();
$is_theme_active   = WPSOLR_Extension::is_plugin_active( WPSOLR_Extension::EXTENSION_THEME_FLATSOME );

$theme_name    = "Flatsome";
$theme_link    = "https://flatsome3.uxthemes.com/";
$theme_version = "";
?>

<div wdm-vertical-tabs-contentid="extension_groups-options" class="wdm-vertical-tabs-content wpsolr-col-9">
    <form action="options.php" method="POST" id='extension_groups_settings_form'>
		<?php
		settings_fields( 'extension_theme_flatsome_opt' );
		?>

        <div class='wrapper'>
            <h4 class='head_div'><?php WPSOLR_Escape::echo_esc_html( $theme_name ); ?> Options</h4>

            <div class="wdm_note">

                In this section, you will configure WPSOLR to work
                with <?php WPSOLR_Escape::echo_esc_html( $theme_name ); ?>.<br/>

				<?php if ( ! $is_theme_active ): ?>
                    <p>
                        Status: <a href="<?php WPSOLR_Escape::echo_esc_url( $theme_link ); ?>"
                                   target="_blank"><?php WPSOLR_Escape::echo_esc_html( $theme_name ); ?>
                        </a> is not activated. First, you need to install and
                        activate it to configure WPSOLR.
                    </p>
				<?php else : ?>
                    <p>
                        Status: <a href="<?php WPSOLR_Escape::echo_esc_url( $theme_link ); ?>"
                                   target="_blank"><?php WPSOLR_Escape::echo_esc_html( $theme_name ); ?>
                        </a>
                        is activated. You can now configure WPSOLR to use it.
                    </p>
				<?php endif; ?>

            </div>

            <div class="wdm_row">
                <div class='col_left'>Use WPSOLR with <a
                            href="<?php WPSOLR_Escape::echo_esc_url( $theme_link ); ?>"
                            target="_blank"><?php WPSOLR_Escape::echo_esc_html( $theme_name ); ?><?php WPSOLR_Escape::echo_esc_html( $theme_version ); ?>
                    </a>
					<?php WPSOLR_Escape::echo_escaped( WPSOLR_Help::get_help( WPSOLR_Help::HELP_ADDON_FLATSOME ) ); ?>
                </div>
                <div class='col_right'>
                    <input type='checkbox' <?php WPSOLR_Escape::echo_escaped( $is_theme_active ? '' : 'readonly' ); ?>
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[is_extension_active]'
                           value='is_extension_active'
						<?php checked( isset( $extension_options['is_extension_active'] ) ); ?>>
                    <p>
                        Please also activate the WPSOLR's WooCommerce extension.
                    </p>

                </div>
                <div class="clear"></div>
            </div>

            <div class="wdm_row">
                <div class='col_left'>
                    Speed up Flatsome Infinite scroll with WPSOLR
                </div>
                <div class='col_right'>
                    <input type='checkbox' <?php WPSOLR_Escape::echo_escaped( $is_theme_active ? '' : 'readonly' ); ?>
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_THEME_FLATSOME_IS_REPLACE_INFINITE_SCROLL ); ?>]'
                           value='y'
						<?php checked( isset( $extension_options[ WPSOLR_Option::OPTION_THEME_FLATSOME_IS_REPLACE_INFINITE_SCROLL ] ) ); ?>>
                </div>
                <div class="clear"></div>
            </div>

            <div class="wdm_row">
                <div class='col_left'>
                    Speed up Flatsome "Show blog and pages in search results" with WPSOLR
                </div>
                <div class='col_right'>
                    <input type='checkbox' <?php WPSOLR_Escape::echo_escaped( $is_theme_active ? '' : 'readonly' ); ?>
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_THEME_FLATSOME_IS_REPLACE_SHOW_BLOG_AND_PAGES_IN_SEARCH_RESULTS ); ?>]'
                           value='y'
						<?php checked( isset( $extension_options[ WPSOLR_Option::OPTION_THEME_FLATSOME_IS_REPLACE_SHOW_BLOG_AND_PAGES_IN_SEARCH_RESULTS ] ) ); ?>>
                </div>
                <div class="clear"></div>
            </div>

            <div class='wdm_row'>
                <div class="submit">
					<?php if ( ! $license_manager->is_installed || $license_manager->get_license_is_activated( OptionLicenses::LICENSE_PACKAGE_FLATSOME ) ) { ?>
                        <div
                                class="wpsolr_premium_block_class"><?php WPSOLR_Escape::echo_escaped( $license_manager->show_premium_link( true, OptionLicenses::LICENSE_PACKAGE_FLATSOME, OptionLicenses::TEXT_LICENSE_ACTIVATED, true ) ); ?></div>
                        <input
                                name="save_selected_options_res_form"
                                id="save_selected_extension_groups_form" type="submit"
                                class="button-primary wdm-save"
                                value="Save Options"/>
					<?php } else { ?>
						<?php WPSOLR_Escape::echo_escaped( $license_manager->show_premium_link( true, OptionLicenses::LICENSE_PACKAGE_FLATSOME, 'Save Options', true ) ); ?>
                        <br/>
					<?php } ?>
                </div>
            </div>

        </div>

    </form>
</div>