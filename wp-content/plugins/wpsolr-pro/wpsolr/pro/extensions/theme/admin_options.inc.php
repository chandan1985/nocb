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

WPSOLR_Extension::require_once_wpsolr_extension( WPSOLR_Extension::OPTION_THEME, true );

$extension_options_name = WPSOLR_Option_View::get_view_uuid_options_name( WPSOLR_Option::OPTION_THEME );
$settings_fields_name   = 'extension_theme_opt';

$options          = WPSOLR_Service_Container::getOption()->get_option_theme();
$is_plugin_active = WPSOLR_Extension::is_plugin_active( WPSOLR_Extension::OPTION_THEME );

?>

<div wdm-vertical-tabs-contentid="extension_groups-options" class="wdm-vertical-tabs-content wpsolr-col-9">
    <form action="options.php" method="POST" id='extension_groups_settings_form'>
		<?php
		settings_fields( $settings_fields_name );
		WPSOLR_Option_View::output_form_view_hidden_fields( $settings_fields_name );
		?>

        <div class='wrapper'>
            <h4 class='head_div'><?php WPSOLR_Escape::echo_escaped( WPSOLR_Option_View::get_views_html( 'Theme' ) ); ?> </h4>

            <div class="wdm_note">
                Customize WPSOLR PRO templates. (more to come soon)
            </div>

            <div class="wdm_row">
                <div class='col_left'>
                    Use this extension
					<?php WPSOLR_Escape::echo_escaped( WPSOLR_Help::get_help( WPSOLR_Help::HELP_ADDON_THEME ) ); ?>
                </div>
                <div class='col_right'>
                    <input type='checkbox' <?php WPSOLR_Escape::echo_escaped( $is_plugin_active ? '' : 'readonly' ); ?>
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[is_extension_active]'
                           value='is_extension_active'
						<?php checked( 'is_extension_active', isset( $options['is_extension_active'] ) ? $options['is_extension_active'] : '' ); ?>>
                </div>
                <div class="clear"></div>
            </div>

            <h4 class='head_div'>Facets</h4>
            <div class="wdm_row">
                <div class='col_left'>
                    Collapse facet hierarchies
					<?php WPSOLR_Escape::echo_escaped( WPSOLR_Help::get_help( WPSOLR_Help::HELP_ADDON_THEME_FACET_COLLAPSING ) ); ?>
                </div>
                <div class='col_right'>
                    <input type='checkbox' <?php WPSOLR_Escape::echo_escaped( $is_plugin_active ? '' : 'readonly' ); ?>
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_THEME_FACET_IS_COLLAPSE ); ?>]'
                           value='x'
						<?php checked( isset( $options[ WPSOLR_Option::OPTION_THEME_FACET_IS_COLLAPSE ] ) ); ?>>
                </div>
                <div class="clear"></div>
            </div>
            <div class="wdm_row">
                <div class='col_left'>
                    Customize your facets with a bit of css
					<?php WPSOLR_Escape::echo_escaped( WPSOLR_Help::get_help( WPSOLR_Help::HELP_ADDON_THEME_FACET_CSS ) ); ?>
                </div>
                <div class='col_right'>
                            <textarea
                                    name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_THEME_FACET_CSS ); ?>]'
                                    placeholder=".wpsolr_facet_checkbox::before { // example }"
                                    rows='10'
                                    cols='100'><?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Service_Container::getOption()->get_option_theme_facet_css() ); ?></textarea>
                </div>
                <div class="clear"></div>
            </div>
            <div class="wdm_row">
                <div class='col_left'>
                    Maximum facet labels shown in settings
                </div>
                <div class='col_right'>
                    <input type='text'
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_THEME_FACET_MAX_LABELS_SHOWN ); ?>]'
                           placeholder="50"
                           value="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Service_Container::getOption()->get_option_theme_facet_max_labels_shown_in_settings() ); ?>">
                    <p>
                        By default, 50 labels per facet are shown in the filters settings.
                    </p>
                </div>
                <div class="clear"></div>
            </div>

            <br/>
            <h4 class='head_div'>Current theme Ajax search</h4>
            <div class="wdm_row">
                <div class='col_left'>
                    Ajax delay
					<?php WPSOLR_Escape::echo_escaped( WPSOLR_Help::get_help( WPSOLR_Help::HELP_ADDON_THEME_AJAX_JQUERY ) ); ?>
                </div>
                <div class='col_right'>
                    <input type='text'
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_THEME_AJAX_DELAY_MS ); ?>]'
                           placeholder=""
                           value="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Service_Container::getOption()->get_option_theme_ajax_delay_ms() ); ?>">
                    <p>
                        Enter a delay in milliseconds before calling Ajax, to allow multi-selections on facets.
                        It can easily lead to empty results, if several incompatible facets are selected.
                        Leave empty to prevent any delay.
                    </p>
                </div>
                <div class="clear"></div>
            </div>
            <div class="wdm_row">
                <div class='col_left'>
                    Ajax overlay container
					<?php WPSOLR_Escape::echo_escaped( WPSOLR_Help::get_help( WPSOLR_Help::HELP_ADDON_THEME_AJAX_JQUERY ) ); ?>
                </div>
                <div class='col_right'>
                    <input type='text'
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_THEME_AJAX_OVERLAY_JQUERY_SELECTOR ); ?>]'
                           placeholder=".mycontainer, #mycontainer"
                           value="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Service_Container::getOption()->get_option_theme_ajax_page_overlay_jquery_selectors( false ) ); ?>">
                    <p>
                        Enter a jQuery selector for your Ajax overlay container. If not set, the results jQuery
                        selector will be used (default value).
                    </p>
                </div>
                <div class="clear"></div>
            </div>
            <div class="wdm_row">
                <div class='col_left'>
                    Search page title container
					<?php WPSOLR_Escape::echo_escaped( WPSOLR_Help::get_help( WPSOLR_Help::HELP_ADDON_THEME_AJAX_JQUERY ) ); ?>
                </div>
                <div class='col_right'>
                    <input type='text'
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_THEME_AJAX_PAGE_TITLE_JQUERY_SELECTOR ); ?>]'
                           placeholder=".mycontainer, #mycontainer"
                           value="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Service_Container::getOption()->get_option_theme_ajax_page_title_jquery_selectors() ); ?>">
                    <p>
                        Enter a jQuery selector for your search page title container.<br/>
                        <b><?php WPSOLR_Escape::echo_esc_html( WPSOLR_OPtion::OPTION_THEME_AJAX_PAGE_TITLE_JQUERY_SELECTOR_DEFAULT ); ?></b>
                        is
                        already added by default, to support WooCommerce and WPSOLR front-end themes.
                    </p>
                </div>
                <div class="clear"></div>
            </div>
            <div class="wdm_row">
                <div class='col_left'>
                    Search page sort list container
					<?php WPSOLR_Escape::echo_escaped( WPSOLR_Help::get_help( WPSOLR_Help::HELP_ADDON_THEME_AJAX_JQUERY ) ); ?>
                </div>
                <div class='col_right'>
                    <input type='text'
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_THEME_AJAX_SORT_JQUERY_SELECTOR ); ?>]'
                           placeholder=".mycontainer, #mycontainer"
                           value="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Service_Container::getOption()->get_option_theme_ajax_sort_jquery_selectors() ); ?>">
                    <p>
                        Enter a jQuery selector for your search sort list container.<br/>
                        <b><?php WPSOLR_Escape::echo_esc_html( WPSOLR_OPtion::OPTION_THEME_AJAX_SORT_JQUERY_SELECTOR_DEFAULT ); ?></b>
                        is already
                        added by default, to support WooCommerce and WPSOLR front-end themes.
                    </p>
                </div>
                <div class="clear"></div>
            </div>
            <div class="wdm_row">
                <div class='col_left'>
                    Search page results container
					<?php WPSOLR_Escape::echo_escaped( WPSOLR_Help::get_help( WPSOLR_Help::HELP_ADDON_THEME_AJAX_JQUERY ) ); ?>
                </div>
                <div class='col_right'>
                    <input type='text'
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_THEME_AJAX_RESULTS_JQUERY_SELECTOR ); ?>]'
                           placeholder=".mycontainer, #mycontainer"
                           value="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Service_Container::getOption()->get_option_theme_ajax_results_jquery_selectors() ); ?>">
                    <p>
                        Enter a jQuery selector for your search page results container.<br/>
                        <b><?php WPSOLR_Escape::echo_esc_html( WPSOLR_OPtion::OPTION_THEME_AJAX_RESULTS_JQUERY_SELECTOR_DEFAULT ); ?></b>
                        is
                        already added by default, to support WooCommerce and WPSOLR front-end themes.
                    </p>
                </div>
                <div class="clear"></div>
            </div>
            <div class="wdm_row">
                <div class='col_left'>
                    Search page pagination container
					<?php WPSOLR_Escape::echo_escaped( WPSOLR_Help::get_help( WPSOLR_Help::HELP_ADDON_THEME_AJAX_JQUERY ) ); ?>
                </div>
                <div class='col_right'>
                    <input type='text'
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_THEME_AJAX_PAGINATION_JQUERY_SELECTOR ); ?>]'
                           placeholder=".mycontainer, #mycontainer"
                           value="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Service_Container::getOption()->get_option_theme_ajax_pagination_jquery_selectors() ); ?>">
                    <p>
                        Enter a jQuery selector for your search page pagination container.<br/>
                        <b><?php WPSOLR_Escape::echo_esc_attr( WPSOLR_OPtion::OPTION_THEME_AJAX_PAGINATION_JQUERY_SELECTOR_DEFAULT ); ?></b>
                        is
                        already added by default, to support WooCommerce and WPSOLR front-end
                        themes.
                    </p>
                </div>
                <div class="clear"></div>
            </div>
            <div class="wdm_row">
                <div class='col_left'>
                    Search page pagination page links
					<?php WPSOLR_Escape::echo_escaped( WPSOLR_Help::get_help( WPSOLR_Help::HELP_ADDON_THEME_AJAX_JQUERY ) ); ?>
                </div>
                <div class='col_right'>
                    <input type='text'
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_THEME_AJAX_PAGINATION_PAGE_JQUERY_SELECTOR ); ?>]'
                           placeholder=".mycontainer, #mycontainer"
                           value="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Service_Container::getOption()->get_option_theme_ajax_pagination_page_jquery_selectors() ); ?>">
                    <p>
                        Enter a jQuery selector for your search page pagination page links.<br/>
                        <b><?php WPSOLR_Escape::echo_esc_html( WPSOLR_OPtion::OPTION_THEME_AJAX_PAGINATION_PAGE_JQUERY_SELECTOR_DEFAULT ); ?></b>
                        is
                        already added by default, to support WooCommerce and WPSOLR front-end
                        themes.
                    </p>
                </div>
                <div class="clear"></div>
            </div>
            <div class="wdm_row">
                <div class='col_left'>
                    Search page results count container
					<?php WPSOLR_Escape::echo_escaped( WPSOLR_Help::get_help( WPSOLR_Help::HELP_ADDON_THEME_AJAX_JQUERY ) ); ?>
                </div>
                <div class='col_right'>
                    <input type='text'
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_THEME_AJAX_RESULTS_COUNT_JQUERY_SELECTOR ); ?>]'
                           placeholder=".mycontainer, #mycontainer"
                           value="<?php WPSOLR_Escape::echo_esc_html( WPSOLR_Service_Container::getOption()->get_option_theme_ajax_results_count_jquery_selectors() ); ?>">
                    <p>
                        Enter a jQuery selector for your results count container.<br/>
                        <b>.woocommerce-result-count</b> is already added by default, to support WooCommerce front-end
                        themes.
                    </p>
                </div>
                <div class="clear"></div>
            </div>
        </div>

        <div class='wdm_row'>
            <div class="submit">
				<?php if ( $license_manager->get_license_is_activated( OptionLicenses::LICENSE_PACKAGE_THEME ) ) { ?>
                    <div class="wpsolr_premium_block_class">
						<?php WPSOLR_Escape::echo_escaped( $license_manager->show_premium_link( true, OptionLicenses::LICENSE_PACKAGE_THEME, OptionLicenses::TEXT_LICENSE_ACTIVATED, true, true ) ); ?>
                    </div>
                    <input
                            name="save_selected_options_res_form"
                            id="save_selected_extension_groups_form" type="submit"
                            class="button-primary wdm-save"
                            value="Save Options"/>
				<?php } else { ?>
					<?php WPSOLR_Escape::echo_escaped( $license_manager->show_premium_link( true, OptionLicenses::LICENSE_PACKAGE_THEME, 'Save Options', true, true ) ); ?>
                    <br/>
				<?php } ?>
            </div>
        </div>
    </form>
</div>

