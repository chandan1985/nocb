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

WPSOLR_Extension::require_once_wpsolr_extension( WPSOLR_Extension::EXTENSION_THEME_MYLISTING, true );

$extension_options_name = WPSOLR_Option::OPTION_THEME_MYLISTING;

$extension_options = WPSOLR_Service_Container::getOption()->get_option_theme_mylisting();
$is_theme_active   = WPSOLR_Extension::is_plugin_active( WPSOLR_Extension::EXTENSION_THEME_MYLISTING );

$theme_name    = "MyListing";
$theme_link    = "https://themeforest.net/item/mylisting-directory-listing-wordpress-theme/20593226";
$theme_version = "";
?>

<div wdm-vertical-tabs-contentid="extension_groups-options" class="wdm-vertical-tabs-content wpsolr-col-9">
    <form action="options.php" method="POST" id='extension_groups_settings_form'>
		<?php
		settings_fields( 'extension_theme_mylisting_opt' );
		?>

        <div class='wrapper'>
            <h4 class='head_div'><?php WPSOLR_Escape::echo_esc_html( $theme_name  ); ?> Options</h4>

            <div class="wdm_note">

                In this section, you will configure WPSOLR to work with <?php WPSOLR_Escape::echo_esc_html( $theme_name  ); ?>.<br/>

				<?php if ( ! $is_theme_active ): ?>
                    <p>
                        Status: <a href="<?php WPSOLR_Escape::echo_esc_url( $theme_link  ); ?>"
                                   target="_blank"><?php WPSOLR_Escape::echo_esc_html( $theme_name  ); ?>
                        </a> is not activated. First, you need to install and
                        activate it to configure WPSOLR.
                    </p>
				<?php else : ?>
                    <p>
                        Status: <a href="<?php WPSOLR_Escape::echo_esc_url( $theme_link  ); ?>"
                                   target="_blank"><?php WPSOLR_Escape::echo_esc_html( $theme_name  ); ?>
                        </a>
                        is activated. You can now configure WPSOLR to use it.
                    </p>
				<?php endif; ?>
            </div>

            <div class="wdm_row">
                <div class='col_left'>Use <a
                            href="<?php WPSOLR_Escape::echo_esc_url( $theme_link  ); ?>"
                            target="_blank"><?php WPSOLR_Escape::echo_esc_html( $theme_name  ); ?>[<?php WPSOLR_Escape::echo_esc_html( $theme_version  ); ?>]
                    </a>
                    to perform search.
					<?php WPSOLR_Escape::echo_escaped( WPSOLR_Help::get_help( WPSOLR_Help::HELP_ADDON_MYLISTING ) ); ?>
                </div>
                <div class='col_right'>
                    <input type='checkbox' <?php WPSOLR_Escape::echo_escaped( $is_theme_active ? '' : 'readonly'); ?>
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[is_extension_active]'
                           value='is_extension_active'
						<?php checked( isset( $extension_options['is_extension_active'] ) ); ?>>
                </div>
                <div class="clear"></div>
            </div>

            <div class="wdm_row">
                <div class='col_left'>
                    Speed up listing search
                </div>
                <div class='col_right'>
                    <input class="wpsolr_collapser"
                           type='checkbox' <?php WPSOLR_Escape::echo_escaped( $is_theme_active ? '' : 'readonly'); ?>
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_THEME_MYLISTING_IS_REPLACE_LISTING_SEARCH); ?>]'
                           value='y'
						<?php checked( isset( $extension_options[ WPSOLR_Option::OPTION_THEME_MYLISTING_IS_REPLACE_LISTING_SEARCH ] ) ); ?>>
                    <span class="wpsolr_collapsed">Replace your explore pages search with super fast queries, including: geolocation with radius, dates, sliders, lists, and recurring events.</span>
                </div>
                <div class="clear"></div>
            </div>

            <div class="wdm_row">
                <div class='col_left'>
                    Speed up display of home search bar and filters side bar
                </div>
                <div class='col_right'>
                    <input class="wpsolr_collapser"
                           type='checkbox' <?php WPSOLR_Escape::echo_escaped( $is_theme_active ? '' : 'readonly'); ?>
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_THEME_MYLISTING_IS_REPLACE_LISTING_SIDE_BAR_QUERIES); ?>]'
                           value='y'
						<?php checked( isset( $extension_options[ WPSOLR_Option::OPTION_THEME_MYLISTING_IS_REPLACE_LISTING_SIDE_BAR_QUERIES ] ) ); ?>>
                    <span class="wpsolr_collapsed">Replace the dropdowns and sliders queries of home search bar and filters side with super fast queries.</span>
                </div>
                <div class="clear"></div>
            </div>

            <div class="wdm_row">
                <div class='col_left'>
                    Speed up similar listings
                </div>
                <div class='col_right'>
                    <input class="wpsolr_collapser"
                           type='checkbox' <?php WPSOLR_Escape::echo_escaped( $is_theme_active ? '' : 'readonly'); ?>
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_THEME_MYLISTING_IS_REPLACE_SIMILAR_LISTINGS); ?>]'
                           value='y'
						<?php checked( isset( $extension_options[ WPSOLR_Option::OPTION_THEME_MYLISTING_IS_REPLACE_SIMILAR_LISTINGS ] ) ); ?>>
                    <span class="wpsolr_collapsed">Replace similar listings widget queries with super fast queries.</span>
                </div>
                <div class="clear"></div>
            </div>

            <div class='wdm_row'>
                <div class="submit">
					<?php if ( ! $license_manager->is_installed || $license_manager->get_license_is_activated( OptionLicenses::LICENSE_PACKAGE_MYLISTING ) ) { ?>
                        <div
                                class="wpsolr_premium_block_class"><?php WPSOLR_Escape::echo_escaped( $license_manager->show_premium_link( true, OptionLicenses::LICENSE_PACKAGE_MYLISTING, OptionLicenses::TEXT_LICENSE_ACTIVATED, true ) ); ?></div>
                        <input
                                name="save_selected_options_res_form"
                                id="save_selected_extension_groups_form" type="submit"
                                class="button-primary wdm-save"
                                value="Save Options"/>
					<?php } else { ?>
						<?php WPSOLR_Escape::echo_escaped( $license_manager->show_premium_link( true, OptionLicenses::LICENSE_PACKAGE_MYLISTING, 'Save Options', true ) ); ?>
                        <br/>
					<?php } ?>
                </div>
            </div>

        </div>

    </form>
</div>