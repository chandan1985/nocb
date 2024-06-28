<?php 
/**
 * Plugin Name: Custom JS For Header and Footer
 * Plugin URI: https://www.asentechllc.com/
 * Author: Asentech
 * Author URI: https://www.asentechllc.com/
 * Description: A custom setting page for Header and Footer JS.
 * Version: 1.0
 */
if( !defined('ABSPATH') ) : exit(); endif;

/**
 * Define plugin constants
 */
define( 'PLUGIN_PATH', trailingslashit( plugin_dir_path(__FILE__) ) );
define( 'PLUGIN_URL', trailingslashit( plugins_url('/', __FILE__) ) );


/**
 * Include Settings Page
 */
// require_once PLUGIN_PATH . '/inc/settings/settings.php';


/**
 * Create Settings Menu
 */
function custom_js_for_header_and_footer_settings() {

    // add_submenu_page(
    //     'asentech-custom-settings',
    //     __( 'Custom JS For Header and Footer', 'custom-js-for-header-and-footer' ),
    //     __( 'Custom JS For Header and Footer', 'custom-js-for-header-and-footer' ),
    //     'manage_options',
    //     'custom-js-for-header-and-footer',
    //     'custom_js_for_header_and_footer_settings_callback',
    //     '',
    //     null
    // );

    add_menu_page(
        __( 'Custom JS for Header and Footer', 'custom-js-for-header-and-footer' ),
        __( 'Custom JS for Header and Footer', 'custom-js-for-header-and-footer' ),
        'manage_options',
        'custom-js-for-header-and-footer',
        'custom_js_for_header_and_footer_settings_callback',
        '',
        null
    );

}
add_action('admin_menu', 'custom_js_for_header_and_footer_settings');

/**
 * Settings Template Page
 */
function custom_js_for_header_and_footer_settings_callback() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

        <form action="options.php" method="post">
            <?php 
                // security field
                settings_fields( 'custom-js-for-header-and-footer' );

                // output settings section here
                do_settings_sections('custom-js-for-header-and-footer');

                // save settings button
                submit_button( 'Save Settings' );
            ?>
        </form>
    </div>
    <?php 
}

add_action( 'admin_init', 'custom_js_for_header_and_footer_settings_register_fields' );

/**
 * Settings Template
 */
function custom_js_for_header_and_footer_settings_register_fields() {

    // Setup settings section
    add_settings_section(
        'custom_js_for_header_and_footer_settings_section',
        'Set Custom JS for Header and Footer.',
        '',
        'custom-js-for-header-and-footer'
    );

    // Register textarea field
    register_setting(
        'custom-js-for-header-and-footer',
        'custom_js_for_header',
        'esc_attr'
    );

     // Add textarea fields
    add_settings_field(
        'custom_js_for_header',
        __( 'Header JS', 'header-js' ),
        'custom_js_for_header_callback',
        'custom-js-for-header-and-footer',
        'custom_js_for_header_and_footer_settings_section',
    );

    // Registe textarea field
    register_setting(
        'custom-js-for-header-and-footer',
        'custom_js_for_footer',
        'esc_attr'
    );

     // Add textarea fields
    add_settings_field(
        'custom_js_for_footer',
        __( 'Footer JS', 'footer-js' ),
        'custom_js_for_footer_callback',
        'custom-js-for-header-and-footer',
        'custom_js_for_header_and_footer_settings_section',
    );

}



/**
 * textarea template
 */
function custom_js_for_header_callback() {
    $header_js = get_option('custom_js_for_header');
    ?>
        <textarea id="custom_js_for_header" rows="5" cols="100" name="custom_js_for_header" value="" /> <?php print $header_js; ?> </textarea>
    <?php 
}

function custom_js_for_footer_callback() {
    $footer_js = get_option('custom_js_for_footer');
    ?>
        <textarea id="custom_js_for_footer" rows="5" cols="100" name="custom_js_for_footer" value="" /> <?php print $footer_js; ?> </textarea>
    <?php 
}
