<?php 
/**
 * Plugin Name: Related Contents Display Settings
 * Description: Simple settings page for Related Contents Display Settings on article detail page.
 * Version: 1.0
 */
if( !defined('ABSPATH') ) : exit(); endif;

/**
 * Define plugin constants
 */
define( 'RELATED_CONTENT_PATH', trailingslashit( plugin_dir_path(__FILE__) ) );
define( 'RELATED_CONTENT_URL', trailingslashit( plugins_url('/', __FILE__) ) );


/**
 * Include Settings Page
 */
// require_once RELATED_CONTENT_PATH . '/inc/settings/settings.php';


/**
 * Create Settings Menu
 */
function related_contents_display_settings_menu() {

    add_submenu_page(
        'sponsored_content',
        __( 'Related contents display settings', 'related-contents-display-settings' ),
        __( 'Related contents display settings', 'related-contents-display-settings' ),
        'manage_options',
        'related-contents-display-settings',
        'related_contents_display_settings_callback',
        '',
        null
    );

}
add_action('admin_menu', 'related_contents_display_settings_menu');

/**
 * Settings Template Page
 */
function related_contents_display_settings_callback() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

        <form action="options.php" method="post">
            <?php 
                // security field
                settings_fields( 'related-contents-display-settings' );

                // output settings section here
                do_settings_sections('related-contents-display-settings');

                // save settings button
                submit_button( 'Save Settings' );
            ?>
        </form>
    </div>
    <?php 
}

/**
 * Settings Template
 */
function related_contents_display_settings_init() {

    // Setup settings section
    add_settings_section(
        'related_contents_display_settings_section',
        'Set number of related content item.',
        '',
        'related-contents-display-settings'
    );

    // Registe textarea field
    register_setting(
        'related-contents-display-settings',
        'related_contents_select_option',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => '',
            'supplemental' => 'I am underneath!',
        )
    );

     // Add textarea fields
     add_settings_field(
        'related_contents_select_option',
        __( 'Number of Related Content', 'related-content' ),
        'related_contents_select_option_callback',
        'related-contents-display-settings',
        'related_contents_display_settings_section',
        array(
            'description'  => __('Check to remove preset plugin overrides.'),
        )
    );

}
add_action( 'admin_init', 'related_contents_display_settings_init' );


/**
 * textarea template
 */
function related_contents_select_option_callback() {
    $options = get_option('related_contents_select_option');
    ?>
        <input type="text" class="large-text" name="related_contents_select_option" value="<?php echo isset($options) ? esc_attr( $options ) : ''; ?>" />
    <?php 
}