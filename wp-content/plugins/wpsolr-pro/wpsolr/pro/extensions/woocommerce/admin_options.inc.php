<?php

use wpsolr\core\classes\extensions\licenses\OptionLicenses;
use wpsolr\core\classes\extensions\view\WPSOLR_Option_View;
use wpsolr\core\classes\extensions\WPSOLR_Extension;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\utilities\WPSOLR_Escape;
use wpsolr\core\classes\utilities\WPSOLR_Help;
use wpsolr\core\classes\utilities\WPSOLR_Option;

/**
 * Included file to display admin options
 */

global $license_manager;

WPSOLR_Extension::require_once_wpsolr_extension( WPSOLR_Extension::EXTENSION_WOOCOMMERCE, true );

$extension_options_name = WPSOLR_Option_View::get_view_uuid_options_name( WPSOLR_Option::OPTION_EXTENSION_WOOCOMMERCE );
$settings_fields_name   = 'solr_extension_woocommerce_options';

$extension_options = WPSOLR_Service_Container::getOption()->get_option_plugin_woocommerce();
$is_plugin_active  = WPSOLR_Extension::is_plugin_active( WPSOLR_Extension::EXTENSION_WOOCOMMERCE );

$plugin_name    = "WooCommerce";
$plugin_link    = "https://wordpress.org/plugins/woocommerce/";
$plugin_version = "(>= 2.4.10)";
?>

<div wdm-vertical-tabs-contentid="extension_groups-options" class="wdm-vertical-tabs-content wpsolr-col-9">
    <form action="options.php" method="POST" id='extension_groups_settings_form'>
		<?php
		WPSOLR_Option_View::output_form_view_hidden_fields( $settings_fields_name );
		?>

        <div class='wrapper'>
            <h4 class='head_div'><?php WPSOLR_Escape::echo_escaped( WPSOLR_Option_View::get_views_html( $plugin_name . ' plugin Options' ) ); ?> </h4>

            <div class="wdm_note">

                In this section, you will configure WPSOLR to work
                with <?php WPSOLR_Escape::echo_esc_html( $plugin_name ); ?>.<br/>

				<?php if ( ! $is_plugin_active ): ?>
                    <p>
                        Status: <a href="<?php WPSOLR_Escape::echo_esc_url( $plugin_link ); ?>"
                                   target="_blank"><?php WPSOLR_Escape::echo_esc_html( $plugin_name ); ?>
                            plugin</a> is not activated. First, you need to install and
                        activate it to configure WPSOLR.
                    </p>
                    <p>
                        You will also need to re-index all your data if you activated
                        <a href="<?php WPSOLR_Escape::echo_esc_url( $plugin_link ); ?>"
                           target="_blank"><?php WPSOLR_Escape::echo_esc_html( $plugin_name ); ?>
                            plugin</a>
                        after you activated WPSOLR.
                    </p>
				<?php else : ?>
                    <p>
                        Status: <a href="<?php WPSOLR_Escape::echo_esc_url( $plugin_link ); ?>"
                                   target="_blank"><?php WPSOLR_Escape::echo_esc_html( $plugin_name ); ?>
                            plugin</a>
                        is activated. You can now configure WPSOLR to use it.
                    </p>
				<?php endif; ?>
            </div>

            <div class="wdm_row">
                <div class='col_left'>Use the <a
                            href="<?php WPSOLR_Escape::echo_esc_url( $plugin_link ); ?>"
                            target="_blank"><?php WPSOLR_Escape::echo_esc_html( $plugin_name ); ?> <?php WPSOLR_Escape::echo_esc_html( $plugin_version  ); ?>
                        plugin</a>
                    to filter search results.
					<?php WPSOLR_Escape::echo_escaped( WPSOLR_Help::get_help( WPSOLR_Help::HELP_ADDON_WOOCOMMERCE ) ); ?>

                    <br/>Think of re-indexing all your data if <a
                            href="<?php WPSOLR_Escape::echo_esc_url( $plugin_link ); ?>"
                            target="_blank"><?php WPSOLR_Escape::echo_esc_html( $plugin_name ); ?>
                        plugin</a> was installed after WPSOLR.

                </div>
                <div class='col_right'>
                    <input type='checkbox' <?php WPSOLR_Escape::echo_escaped( $is_plugin_active ? '' : 'readonly' ); ?>
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[is_extension_active]'
                           value='is_extension_active'
						<?php checked( 'is_extension_active', isset( $extension_options['is_extension_active'] ) ? $extension_options['is_extension_active'] : '' ); ?>>
                </div>
                <div class="clear"></div>
            </div>

            <div class="wdm_row">
                <div class='col_left'>
                    Replace WooCommerce orders search by WPSOLR's orders search.
                </div>
                <div class='col_right'>
                    <input type='checkbox' <?php WPSOLR_Escape::echo_escaped( $is_plugin_active ? '' : 'readonly' ); ?>
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_PLUGIN_WOOCOMMERCE_IS_REPLACE_ADMIN_ORDERS_SEARCH); ?>]'
                           value='is_extension_active'
						<?php checked( 'is_extension_active', isset( $extension_options[ WPSOLR_Option::OPTION_PLUGIN_WOOCOMMERCE_IS_REPLACE_ADMIN_ORDERS_SEARCH ] ) ? $extension_options[ WPSOLR_Option::OPTION_PLUGIN_WOOCOMMERCE_IS_REPLACE_ADMIN_ORDERS_SEARCH ] : '' ); ?>>
                </div>
                <div class="clear"></div>
            </div>

            <div class="wdm_row">
                <div class='col_left'>
                    Replace WooCommerce drop-down list sort content with WPSOLR's.
                </div>
                <div class='col_right'>
                    <input type='checkbox' <?php WPSOLR_Escape::echo_escaped( $is_plugin_active ? '' : 'readonly' ); ?>
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_PLUGIN_WOOCOMMERCE_IS_REPLACE_SORT_ITEMS); ?>]'
                           value='is_extension_active'
						<?php checked( 'is_extension_active', isset( $extension_options[ WPSOLR_Option::OPTION_PLUGIN_WOOCOMMERCE_IS_REPLACE_SORT_ITEMS ] ) ? $extension_options[ WPSOLR_Option::OPTION_PLUGIN_WOOCOMMERCE_IS_REPLACE_SORT_ITEMS ] : '' ); ?>>
                </div>
                <div class="clear"></div>
            </div>

            <div class="wdm_row">
                <div class='col_left'>
                    Replace WooCommerce category and shop search with WPSOLR's.
                </div>
                <div class='col_right'>
                    <input type='checkbox' <?php WPSOLR_Escape::echo_escaped( $is_plugin_active ? '' : 'readonly' ); ?>
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_PLUGIN_WOOCOMMERCE_IS_REPLACE_PRODUCT_CATEGORY_SEARCH); ?>]'
                           value='is_extension_active'
						<?php checked( 'is_extension_active', isset( $extension_options[ WPSOLR_Option::OPTION_PLUGIN_WOOCOMMERCE_IS_REPLACE_PRODUCT_CATEGORY_SEARCH ] ) ? $extension_options[ WPSOLR_Option::OPTION_PLUGIN_WOOCOMMERCE_IS_REPLACE_PRODUCT_CATEGORY_SEARCH ] : '' ); ?>>
                </div>
                <div class="clear"></div>
            </div>

            <div class="wdm_row">
                <div class='col_left'>
                    Index and search products downloadable files
                </div>
                <div class='col_right'>
                    <input type='checkbox' <?php WPSOLR_Escape::echo_escaped( $is_plugin_active ? '' : 'readonly' ); ?>
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_PLUGIN_WOOCOMMERCE_IS_INDEX_DOWNLOADABLE_FILES); ?>]'
                           value='is_extension_active'
						<?php checked( 'is_extension_active', isset( $extension_options[ WPSOLR_Option::OPTION_PLUGIN_WOOCOMMERCE_IS_INDEX_DOWNLOADABLE_FILES ] ) ? $extension_options[ WPSOLR_Option::OPTION_PLUGIN_WOOCOMMERCE_IS_INDEX_DOWNLOADABLE_FILES ] : '' ); ?>>
                    Display products in search with downloadable file content matching the query
                </div>
                <div class="clear"></div>
            </div>

            <div class='wdm_row'>
                <div class="submit">
					<?php if ( ! $license_manager->is_installed || $license_manager->get_license_is_activated( OptionLicenses::LICENSE_PACKAGE_WOOCOMMERCE ) ) { ?>
                        <div
                                class="wpsolr_premium_block_class"><?php WPSOLR_Escape::echo_escaped( $license_manager->show_premium_link( true, OptionLicenses::LICENSE_PACKAGE_WOOCOMMERCE, OptionLicenses::TEXT_LICENSE_ACTIVATED, true ) ); ?></div>
                        <input
                                name="save_selected_options_res_form"
                                id="save_selected_extension_groups_form" type="submit"
                                class="button-primary wdm-save"
                                value="Save Options"/>
					<?php } else { ?>
						<?php WPSOLR_Escape::echo_escaped( $license_manager->show_premium_link( true, OptionLicenses::LICENSE_PACKAGE_WOOCOMMERCE, 'Save Options', true ) ); ?>
                        <br/>
					<?php } ?>
                </div>
            </div>

        </div>

    </form>
</div>