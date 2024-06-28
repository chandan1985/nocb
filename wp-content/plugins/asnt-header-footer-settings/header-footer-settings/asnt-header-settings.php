<?php

// Add a menu item to the admin dashboard
function header_settings_menu() {
    add_menu_page(
        'ASNT Header Footer Configuration',
        'ASNT Header Footer Configuration',
        'manage_options',
        'asnt-header-footer',
        '__return_empty_string', // Set the callback function to return an empty string
        'dashicons-admin-generic',
        99
    );

    add_submenu_page(
        'asnt-header-footer',
        'Header Settings',
        'Header Settings',
        'manage_options',
        'header-settings',
        'header_settings_page'
    );
    
    add_submenu_page(
        'asnt-header-footer',
        'Footer Settings',
        'Footer Settings',
        'manage_options',
        'footer-settings',
        'footer_settings_page'
    );
}
add_action('admin_menu', 'header_settings_menu');

function remove_parent_submenu_link() {
    global $submenu;
    unset($submenu['asnt-header-footer'][0]);
}
add_action('admin_head', 'remove_parent_submenu_link');

// Create the settings page
function header_settings_page() {
    ?>
<div class="wrap">
    <h1>Header Settings</h1>
    <form method="post" action="options.php">
        <?php
            settings_fields('header_settings_group');
            do_settings_sections('header-settings');
            submit_button();
            ?>
    </form>
</div>
<?php
}

// Register the Header Settings and fields
function header_settings_init() {

    add_settings_section(
        'header_settings_section',
        'Site Settings',
        '',
        'header-settings'
    );

    register_setting(
        'header_settings_group',
        'top_billboard'
    );

    add_settings_field(
        'top_billboard',
        'Top Billboard Ads',
        'top_billboard_callback',
        'header-settings',
        'header_settings_section'
    );

    register_setting(
        'header_settings_group',
        'top_billboard_hide_from_pages'
    );

    add_settings_field(
        'top_billboard_hide_from_pages',
        'Top Billboard hide from pages',
        'top_billboard_hide_from_pages_callback',
        'header-settings',
        'header_settings_section'
    );

    register_setting(
        'header_settings_group',
        'top_billboard_hide_from_post_type'
    );

    add_settings_field(
        'top_billboard_hide_from_post_type',
        'Top Billboard hide from post types',
        'top_billboard_hide_from_post_type_callback',
        'header-settings',
        'header_settings_section'
    );

    register_setting(
        'header_settings_group',
        'btm_logo'
    );

    add_settings_field(
        'btm_logo',
        'BTM Logo',
        'btm_logo_callback',
        'header-settings',
        'header_settings_section'
    );
  
    register_setting(
        'header_settings_group',
        'selected_region_and_brand_menu'
    );

    add_settings_field(
        'selected_region_and_brand_menu',
        'Select Region and Brand Menu',
        'selected_region_and_brand_menu_callback',
        'header-settings',
        'header_settings_section'
    );

    register_setting(
        'header_settings_group',
        'account_menu'
    );

    add_settings_field(
        'account_menu',
        'Select Account Menu',
        'account_menu_callback',
        'header-settings',
        'header_settings_section'
    );

    register_setting(
        'header_settings_group',
        'signin_box'
    );

    add_settings_field(
        'signin_box',
        'Sign In Link',
        'signin_box_callback',
        'header-settings',
        'header_settings_section'
    );

    register_setting(
        'header_settings_group',
        'signout_box'
    );

    add_settings_field(
        'signout_box',
        'Sign Out Link',
        'signout_box_callback',
        'header-settings',
        'header_settings_section'
    );

    register_setting(
        'header_settings_group',
        'site_logo'
    );
    
    add_settings_field(
        'site_logo',
        'Site Logo',
        'site_logo_callback',
        'header-settings',
        'header_settings_section'
    );
    register_setting(
        'header_settings_group',
        'search_icon'
    );
    
    add_settings_field(
        'search_icon',
        'Mobile search icon',
        'search_icon_callback',
        'header-settings',
        'header_settings_section'
    );
    
    register_setting(
        'header_settings_group',
        'upcoming_event'
    );
    
    add_settings_field(
        'upcoming_event',
        'Upcoming Event',
        'upcoming_event_callback',
        'header-settings',
        'header_settings_section'
    );

    register_setting(
        'header_settings_group',
        'newsletter_and_subscribe'
    );

    add_settings_field(
        'newsletter_and_subscribe',
        'Newsletter and Subscribe Button',
        'newsletter_and_subscribe_callback',
        'header-settings',
        'header_settings_section'
    );
  
    register_setting(
        'header_settings_group',
        'mobile_newsletter_and_subscribe'
    );

    add_settings_field(
        'mobile_newsletter_and_subscribe',
        'Mobile Newsletter and Subscribe Button',
        'mobile_newsletter_and_subscribe_callback',
        'header-settings',
        'header_settings_section'
    );

    register_setting(
        'header_settings_group',
        'selected_popup_menu'
    );

    add_settings_field(
        'selected_popup_menu',
        'Select Popout Menu',
        'selected_popup_menu_callback',
        'header-settings',
        'header_settings_section'
    );
  
    register_setting(
        'header_settings_group',
        'selected_mobile_popup_menu'
    );

    add_settings_field(
        'selected_mobile_popup_menu',
        'Select Mobile Popout Menu',
        'selected_mobile_popup_menu_callback',
        'header-settings',
        'header_settings_section'
    );

    register_setting(
        'header_settings_group',
        'popout_html_box'
    );

    add_settings_field(
        'popout_html_box',
        'Hamburger menu ICON',
        'popout_html_box_callback',
        'header-settings',
        'header_settings_section'
    );

    register_setting(
        'header_settings_group',
        'selected_menu'
    );

    add_settings_field(
        'selected_menu',
        'Select Main Navigation Menu',
        'selected_menu_callback',
        'header-settings',
        'header_settings_section'
    );

    register_setting(
        'header_settings_group',
        'search_html_box'
    );

    add_settings_field(
        'search_html_box',
        'Search Box',
        'search_html_box_callback',
        'header-settings',
        'header_settings_section'
    );

    register_setting(
        'header_settings_group',
        'header_bottom_ad'
    );

    add_settings_field(
        'header_bottom_ad',
        'Header Bottom Ad',
        'header_bottom_ad_callback',
        'header-settings',
        'header_settings_section'
    );

    register_setting(
        'header_settings_group',
        'mobile_header_bottom_ad'
    );

    add_settings_field(
        'mobile_header_bottom_ad',
        'Mobile Header Bottom Ad',
        'mobile_header_bottom_ad_callback',
        'header-settings',
        'header_settings_section'
    );

    register_setting(
        'header_settings_group',
        'hide_from_pages'
    );

    add_settings_field(
        'hide_from_pages',
        'Hide From Pages',
        'hide_from_pages_callback',
        'header-settings',
        'header_settings_section'
    );

    register_setting(
        'header_settings_group',
        'hide_from_post_type'
    );

    add_settings_field(
        'hide_from_post_type',
        'Hide From Post Type',
        'hide_from_post_type_callback',
        'header-settings',
        'header_settings_section'
    );
    
    register_setting(
        'header_settings_group',
        'welcome_ad'
    );

    add_settings_field(
        'welcome_ad',
        'Welcome Ad Code',
        'welcome_ad_callback',
        'header-settings',
        'header_settings_section'
    );

    register_setting(
        'header_settings_group',
        'hide_welcome_ad'
    );

    add_settings_field(
        'hide_welcome_ad',
        'HIde Welcome Ad Code',
        'hide_welcome_ad_callback',
        'header-settings',
        'header_settings_section'
    );
    
}
add_action('admin_init', 'header_settings_init');

// Callback function for the header bottom ad field
function top_billboard_callback() {
    $top_billboard = get_option('top_billboard');
    print '<input type="text" name="top_billboard" value="'. esc_attr($top_billboard). '" />';
    print '<br>Enter DFP Ad short code here to show top billboard.';
}

// Callback function for the hide from pages field
function top_billboard_hide_from_pages_callback() {
    $top_billboard_hide_from_pages = get_option('top_billboard_hide_from_pages');
    $pages = get_pages();
    ?>
    <ul class="hide-from-pages">
        <style>
            ul.hide-from-pages {
                height: 300px;
                overflow-y: scroll;
                max-width: 300px;
                background: #fff;
                padding: 20px;
                border: 1px solid;
            }
            ul.hide-from-pages li{
                list-style: none;
            }
        </style>
        <?php foreach ($pages as $page) : ?>
        <li>
            <label>
            <?php $checked = is_array($top_billboard_hide_from_pages) && in_array($page->ID, $top_billboard_hide_from_pages); ?>
            <input type="checkbox" name="top_billboard_hide_from_pages[]" value="<?php echo esc_attr($page->ID); ?>" <?php checked($checked); ?> />

            <?php echo esc_html($page->post_title); ?>
            </label>
        </li>
        <?php endforeach; ?>
    </ul>
    <p>Select pages from which you want to hide top billborad ad.</p>
    <?php
}

// Callback function for the hide from post type field
function top_billboard_hide_from_post_type_callback() {
    $top_billboard_hide_from_post_type = get_option('top_billboard_hide_from_post_type');
    echo "string". $top_billboard_hide_from_post_type;
    $post_types = get_post_types(array('public' => true), 'objects');
    ?>
    <ul class="hide-from-post">
        <style>
            ul.hide-from-post {
                max-width: max-content;
                background: #fff;
                padding: 20px;
                border: 1px solid;
            }
            ul.hide-from-post li{
                list-style: none;
            }
        </style>
        <?php foreach ($post_types as $post_type) : ?>
        <?php if ($post_type->name !== 'attachment') : ?>
            <li>
            <label>
            <input type="checkbox" name="top_billboard_hide_from_post_type[]" value="<?php echo esc_attr($post_type->name); ?>" <?php if (is_array($top_billboard_hide_from_post_type) && in_array($post_type->name, $top_billboard_hide_from_post_type)) echo 'checked'; ?> />
            <?php echo esc_html($post_type->label); ?>
            </label>
            </li>
        <?php endif; ?>
        <?php endforeach; ?>
    </ul>
    <p>Select post type from which you want to hide top billborad ad.</p>
    <?php
}


// Callback function for the btm logo field
function btm_logo_callback() {
    $btm_logo = get_option('btm_logo');
    echo '<input type="text" name="btm_logo" id="btm_logo" value="' . esc_attr($btm_logo) . '" />';
    echo '<input type="button" class="button" value="Choose Image" id="upload_btm_logo" />';
    echo '<div id="btm_logo_preview"></div>';
    function enqueue_custom_media_scripts1() {
        wp_enqueue_media();
    }
    add_action('admin_enqueue_scripts', 'enqueue_custom_media_scripts1');
}

// Callback function for the selected region and brand menu field
function selected_region_and_brand_menu_callback() {
    $selected_menu = get_option('selected_region_and_brand_menu');
    $menus = wp_get_nav_menus();
    echo '<select name="selected_region_and_brand_menu" id="selected_region_and_brand_menu">';
    echo '<option value="">Select PopUp menu</option>';
    foreach ($menus as $menu) {
        $selected = $selected_menu === $menu->slug ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($menu->slug) . '" ' . $selected . '>' . esc_html($menu->name) . '</option>';
    }
    echo '</select>';
}

// Callback function for the selected account menu field
function account_menu_callback() {
    $selected_menu = get_option('account_menu');
    $menus = wp_get_nav_menus();
    echo '<select name="account_menu" id="account_menu">';
    echo '<option value="">Select a menu</option>';
    foreach ($menus as $menu) {
        $selected = $selected_menu === $menu->slug ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($menu->slug) . '" ' . $selected . '>' . esc_html($menu->name) . '</option>';
    }
    echo '</select>';
}

// Callback function for the selected signin field
function signin_box_callback() {
    $signin_link = get_option('signin_box');
    echo '<textarea name="signin_box" id="signin_box" rows="2" cols="50">' . esc_attr($signin_link) .'</textarea>';
}

// Callback function for the selected signout field
function signout_box_callback() {
    $signout_link = get_option('signout_box');
    echo '<textarea name="signout_box" id="signout_box" rows="2" cols="50">' . esc_attr($signout_link) .'</textarea>';
}

// Callback function for the site logo field
function site_logo_callback() {
    $site_logo = get_option('site_logo');
    echo '<input type="text" name="site_logo" id="site_logo" value="' . esc_attr($site_logo) . '" />';
    echo '<input type="button" class="button" value="Choose Image" id="upload_logo_button" />';
    echo '<div id="logo_preview"></div>';
    function enqueue_custom_media_scripts() {
        wp_enqueue_media();
    }
    add_action('admin_enqueue_scripts', 'enqueue_custom_media_scripts');
}
// Callback function for the mobile search icon field
function search_icon_callback() {
    $search_icon = get_option('search_icon');
    echo '<input type="text" name="search_icon" id="search_icon" value="' . esc_attr($search_icon) . '" />';
    echo '<input type="button" class="button" value="Choose Image" id="upload_search_icon_button" />';
    echo '<div id="search_icon_preview"></div>';
    function enqueue_custom_media_script() {
        wp_enqueue_media();
    }
    add_action('admin_enqueue_scripts', 'enqueue_custom_media_script');
}

// Callback function for the selected upcoming event field
function upcoming_event_callback() {
    $upcoming_event = get_option('upcoming_event');
    echo '<textarea name="upcoming_event" id="upcoming_event" rows="2" cols="50">' . esc_attr($upcoming_event) .'</textarea>';
}

// Callback function for the selected newsletter and subscribe field
function newsletter_and_subscribe_callback() {
    $news_subscribe = get_option('newsletter_and_subscribe');
    echo '<textarea name="newsletter_and_subscribe" rows="5" cols="50">' . esc_textarea($news_subscribe) . '</textarea>';
}

// Callback function for the selected mobile newsletter and subscribe field
function mobile_newsletter_and_subscribe_callback() {
    $mobile_newsletter_and_subscribe = get_option('mobile_newsletter_and_subscribe');
    echo '<textarea name="mobile_newsletter_and_subscribe" rows="5" cols="50">' . esc_textarea($mobile_newsletter_and_subscribe) . '</textarea>';
}

// Callback function for the selected popup menu field
function selected_popup_menu_callback() {
    $selected_menu = get_option('selected_popup_menu');
    $menus = wp_get_nav_menus();
    echo '<select name="selected_popup_menu" id="selected_popup_menu">';
    echo '<option value="">Select PopUp menu</option>';
    foreach ($menus as $menu) {
        $selected = $selected_menu === $menu->slug ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($menu->slug) . '" ' . $selected . '>' . esc_html($menu->name) . '</option>';
    }
    echo '</select>';
}

// Callback function for the selected signin field
function popout_html_box_callback() {
    $popout_html_box = get_option('popout_html_box');
    echo '<textarea name="popout_html_box" rows="5" cols="50">'. esc_attr($popout_html_box) . '</textarea>';
}

// Callback function for the selected popup menu field
function selected_mobile_popup_menu_callback() {
    $selected_menu = get_option('selected_mobile_popup_menu');
    $menus = wp_get_nav_menus();
    echo '<select name="selected_mobile_popup_menu" id="selected_mobile_popup_menu">';
    echo '<option value="">Select Mobile PopUp menu</option>';
    foreach ($menus as $menu) {
        $selected = $selected_menu === $menu->slug ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($menu->slug) . '" ' . $selected . '>' . esc_html($menu->name) . '</option>';
    }
    echo '</select>';
}

// Callback function for the selected menu field
function selected_menu_callback() {
    $selected_menu = get_option('selected_menu');
    $menus = wp_get_nav_menus();
    echo '<select name="selected_menu" id="selected_menu">';
    echo '<option value="">Select a menu</option>';
    foreach ($menus as $menu) {
        $selected = $selected_menu === $menu->slug ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($menu->slug) . '" ' . $selected . '>' . esc_html($menu->name) . '</option>';
    }
    echo '</select>';
}

// Callback function for the selected search html box field
function search_html_box_callback() {
    $search_html = get_option('search_html_box');
    echo '<textarea name="search_html_box" rows="5" cols="50">' . esc_textarea($search_html) . '</textarea>';
}

// Callback function for the header bottom ad field
function header_bottom_ad_callback() {
    $header_bottom_ad = get_option('header_bottom_ad');
    print '<input type="text" name="header_bottom_ad" value="'. esc_attr($header_bottom_ad). '" />';
    print '<br>Enter DFP Ad short code here to show on desktop mode.';
}

// Callback function for the header bottom ad field
function mobile_header_bottom_ad_callback() {
    $mobile_header_bottom_ad = get_option('mobile_header_bottom_ad');
    print '<input type="text" name="mobile_header_bottom_ad" value="'. esc_attr($mobile_header_bottom_ad). '" />';
    print '<br>Enter DFP Ad short code here to show on mobile view mode.';
}

// Callback function for the hide from pages field
function hide_from_pages_callback() {
    $hide_from_pages = get_option('hide_from_pages');
    $pages = get_pages();
    ?>
    <ul class="hide-from-pages">
        <style>
            ul.hide-from-pages {
                height: 300px;
                overflow-y: scroll;
                max-width: 300px;
                background: #fff;
                padding: 20px;
                border: 1px solid;
            }
            ul.hide-from-pages li{
                list-style: none;
            }
        </style>
        <?php foreach ($pages as $page) : ?>
        <li>
            <label>
            <input type="checkbox" name="hide_from_pages[]" value="<?php echo esc_attr($page->ID); ?>" <?php checked(in_array($page->ID, $hide_from_pages)); ?> />
            <?php echo esc_html($page->post_title); ?>
            </label>
        </li>
        <?php endforeach; ?>
    </ul>
    <p>Select pages from which you want to hide header bottom ad.</p>
    <?php
}

// Callback function for the hide from post type field
function hide_from_post_type_callback() {
    $hide_from_post_type = get_option('hide_from_post_type');
    $post_types = get_post_types(array('public' => true), 'objects');
    ?>
    <ul class="hide-from-post">
        <style>
            ul.hide-from-post {
                max-width: max-content;
                background: #fff;
                padding: 20px;
                border: 1px solid;
            }
            ul.hide-from-post li{
                list-style: none;
            }
        </style>
        <?php foreach ($post_types as $post_type) : ?>
        <?php if ($post_type->name !== 'attachment') : ?>
            <li>
            <label>
            <?php
                $post_type_names = is_array($hide_from_post_type) ? $hide_from_post_type : array();
                $checked = in_array($post_type->name, $post_type_names);
                ?>
                <input type="checkbox" name="hide_from_post_type[]" value="<?php echo esc_attr($post_type->name); ?>" <?php checked($checked); ?> />

                <?php echo esc_html($post_type->label); ?>
            </label>
            </li>
        <?php endif; ?>
        <?php endforeach; ?>
    </ul>
    <p>Select post type from which you want to hide header bottom ad.</p>
    <?php
}

// Callback function for the Welcome ads code html box field
function welcome_ad_callback() {
    $welcome_ad = get_option('welcome_ad');
    echo '<textarea name="welcome_ad" rows="5" cols="50">' . esc_textarea($welcome_ad) . '</textarea>';
}

// Callback function for the hide from pages field
function hide_welcome_ad_callback() {
    $hide_welcome_ad = get_option('hide_welcome_ad');
    $pages = get_pages();
    ?>
    <ul class="hide_welcome_ad">
        <style>
            ul.hide_welcome_ad {
                height: 300px;
                overflow-y: scroll;
                max-width: 300px;
                background: #fff;
                padding: 20px;
                border: 1px solid;
            }
            ul.hide_welcome_ad li{
                list-style: none;
            }
        </style>
        <?php foreach ($pages as $page) : ?>
        <li>
            <label>
            <input type="checkbox" name="hide_welcome_ad[]" value="<?php echo esc_attr($page->ID); ?>" <?php checked(is_array($hide_welcome_ad) && in_array($page->ID, $hide_welcome_ad)); ?> />

            <?php echo esc_html($page->post_title); ?>
            </label>
        </li>
        <?php endforeach; ?>
    </ul>
    <p>Select pages from which you want to hide Welcome ad.</p>
    <?php
}

// Register and enqueue necessary scripts
function custom_settings_enqueue_scripts() {
    wp_enqueue_media();
    wp_enqueue_script('custom-settings-script', plugins_url('custom-settings-script.js', __FILE__), array('jquery'), '1.0', true);
}
add_action('admin_enqueue_scripts', 'custom_settings_enqueue_scripts');

