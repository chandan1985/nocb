<?php

use wpsolr\core\classes\extensions\indexes\WPSOLR_Option_Indexes;
use wpsolr\core\classes\extensions\licenses\OptionLicenses;
use wpsolr\core\classes\extensions\view\WPSOLR_Option_View;
use wpsolr\core\classes\extensions\WPSOLR_Extension;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\utilities\WPSOLR_Admin_Utilities;
use wpsolr\core\classes\utilities\WPSOLR_Escape;
use wpsolr\core\classes\utilities\WPSOLR_Option;

/**
 * Included file to display admin options
 */
global $license_manager;

WPSOLR_Extension::require_once_wpsolr_extension( WPSOLR_Extension::OPTION_VIEWS, true );

$extension_options_name = WPSOLR_Option::OPTION_VIEW;
$settings_fields_name   = 'extension_views_opt';

$options          = WPSOLR_Service_Container::getOption()->get_option_view();
$is_plugin_active = WPSOLR_Extension::is_plugin_active( WPSOLR_Extension::OPTION_VIEWS );
?>

<?php
WPSOLR_Option_View::clean_deleted_views();

$views = WPSOLR_Service_Container::getOption()->get_option_view_views();
if ( isset( $_POST['wpsolr_new_view'] ) && ! isset( $views[ $_POST['wpsolr_new_view'] ] ) ) {
	$views = array_merge( [ sanitize_text_field( $_POST['wpsolr_new_view'] ) => [] ], $views );
}
?>

<form id="wpsolr_form_new_view" method="post">
    <input type="hidden" name="wpsolr_new_view"
           value="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option_Indexes::generate_uuid() ); ?>"/>
</form>

<div wdm-vertical-tabs-contentid="extension_groups-options" class="wdm-vertical-tabs-content wpsolr-col-9">
    <form action="options.php" method="POST" id='extension_groups_settings_form'>
		<?php
		settings_fields( $settings_fields_name );
		?>

        <div class='wrapper'>
            <h4 class='head_div'>Views Pack</h4>

            <div class="wdm_note">
                Create views, and use them anywhere with shortcodes or <a
                        href="<?php WPSOLR_Escape::echo_esc_url( WPSOLR_Admin_Utilities::get_admin_url( '&tab=solr_plugins&subtab=extension_elementor_opt' ) ); ?>">Elementor
                    widgets</a>.<br><br>
                <ol>
                    <li>A view is a set of configurations that define a self-contained search: index, extensions, query,
                        facets,
                        suggestions, sort,
                        pagination, results ....
                    </li>
                    <li>After creation, Views must be configured in tabs <a
                                href="<?php WPSOLR_Escape::echo_esc_url( WPSOLR_Admin_Utilities::get_admin_url( '&tab=solr_option' ) ); ?>">2.x</a>
                    </li>
                    <li>You can drag & drop Views to rearrange their priority: if two views are eligible for a search,
                        the
                        first in the list will be selected.
                    </li>
                </ol>
            </div>

            <div class="wdm_row">
                <div class='col_left'>
                    Activate the Views Pack
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
                </div>
                <div class='col_right'>
                    <input type="button"
                           name="add_view"
                           id="add_view"
                           class="button-primary"
                           value="Add a view"
                           onclick="jQuery('#wpsolr_form_new_view').submit();"
                    />
                    (<?php WPSOLR_Escape::echo_esc_html( count( $views ) ); ?> already)
                </div>
                <div class="clear"></div>
            </div>

            <div class="ui-sortable">
				<?php foreach (
					$views

					as $view_uuid => $view
				) {
					$view_label  = isset( $view[ WPSOLR_Option::OPTION_VIEW_LABEL ] ) ? $view[ WPSOLR_Option::OPTION_VIEW_LABEL ] : 'rename me';
					$is_disabled = isset( $view[ WPSOLR_Option::OPTION_VIEW_IS_DISABLED ] );
					?>
                    <div class="wpsolr_view <?php WPSOLR_Escape::echo_escaped( ( 'rename me' === $view_label ) ? 'wpsolr_view_new' : '' ); ?>"
                         data-wpsolr-view-label="<?php WPSOLR_Escape::echo_esc_attr( $view_label ); ?>">
                        <h4 class='head_div'><?php WPSOLR_Escape::echo_esc_html( $view_label ); ?></h4>

                        <div class="wdm_row">
                            <div class='col_left'>
                                Label
                                <input type="button"
                                       style="float:right;"
                                       name="<?php WPSOLR_Escape::echo_esc_attr( $view_label ); ?>"
                                       class="wpsolr-view-delete-button button-secondary"
                                       value="Delete"
                                       onclick="jQuery(this).closest('.wpsolr_view').remove();"
                                />
                            </div>
                            <div class='col_right'>
                                <input id="<?php WPSOLR_Escape::echo_esc_attr( $view_uuid ); ?>"
                                       type='text'
                                       name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_VIEW_VIEWS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $view_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_VIEW_LABEL ); ?>]'
                                       placeholder="Enter a label"
                                       value="<?php WPSOLR_Escape::echo_esc_attr( $view_label ); ?>">

                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="wdm_row">
                            <div class='col_left'>
                                Disabled
                            </div>
                            <div class='col_right'>
                                <input type='checkbox'
                                       class="wpsolr_collapser"
                                       name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_VIEW_VIEWS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $view_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_VIEW_IS_DISABLED ); ?>]'
                                       value="1"
									<?php checked( $is_disabled ); ?>
                                >
                                <span class="wpsolr_collapsed">This View is not eligible for any search</span>

                            </div>
                            <div class="clear"></div>
                        </div>

                    </div>
				<?php } ?>
            </div>

            <div class='wdm_row'>
                <div class="submit">
					<?php if ( $license_manager->get_license_is_activated( OptionLicenses::LICENSE_PACKAGE_VIEWS ) ) { ?>
                        <div class="wpsolr_premium_block_class">
							<?php WPSOLR_Escape::echo_escaped( $license_manager->show_premium_link( true, OptionLicenses::LICENSE_PACKAGE_VIEWS, OptionLicenses::TEXT_LICENSE_ACTIVATED, true, true ) ); ?>
                        </div>
                        <input
                                name="save_selected_options_res_form"
                                id="save_selected_extension_groups_form" type="submit"
                                class="button-primary wdm-save"
                                value="Save Options"/>
					<?php } else { ?>
						<?php WPSOLR_Escape::echo_escaped( $license_manager->show_premium_link( true, OptionLicenses::LICENSE_PACKAGE_VIEWS, 'Save Options', true, true ) ); ?>
                        <br/>
					<?php } ?>
                </div>
            </div>
        </div>

    </form>
</div>