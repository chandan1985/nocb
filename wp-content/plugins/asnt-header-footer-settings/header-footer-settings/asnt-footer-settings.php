<?php

// Create the settings page
function footer_settings_page() {
    ?>
    <div class="wrap">
        <h1>Footer Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('footer_settings_group');
            do_settings_sections('footer-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register the Footer Settings and fields
function footer_settings_init() {

    add_settings_section(
        'footer_settings_section',
        'Site Settings',
        '',
        'footer-settings'
    );

    register_setting(
        'footer_settings_group',
        'footer_site_logo'
    );
    
    add_settings_field(
        'footer_site_logo',
        'Footer Site Logo',
        'footer_site_logo_callback',
        'footer-settings',
        'footer_settings_section'
    );

    register_setting(
        'footer_settings_group',
        'footer_navigation_menu'
    );

    add_settings_field(
        'footer_navigation_menu',
        'Footer Navigation Menu',
        'footer_navigation_menu_callback',
        'footer-settings',
        'footer_settings_section'
    );

    register_setting(
        'footer_settings_group',
        'site_about_info'
    );

    add_settings_field(
        'site_about_info',
        'About Info',
        'site_about_info_callback',
        'footer-settings',
        'footer_settings_section'
    );

    register_setting(
        'footer_settings_group',
        'my_account_logout_menu'
    );

    add_settings_field(
        'my_account_logout_menu',
        'My Account',
        'my_account_logout_menu_callback',
        'footer-settings',
        'footer_settings_section'
    );

    register_setting(
        'footer_settings_group',
        'contact_menu'
    );

    add_settings_field(
        'contact_menu',
        'Contact Menu',
        'contact_menu_callback',
        'footer-settings',
        'footer_settings_section'
    );

    register_setting(
        'footer_settings_group',
        'label_for_contact_menu'
    );

    add_settings_field(
        'label_for_contact_menu',
        'Widget Heading Text',
        'label_for_contact_menu_callback',
        'footer-settings',
        'footer_settings_section'
    );

    register_setting(
        'footer_settings_group',
        'advertise_menu'
    );

    add_settings_field(
        'advertise_menu',
        'Advertise Menu',
        'advertise_menu_callback',
        'footer-settings',
        'footer_settings_section'
    );

    register_setting(
        'footer_settings_group',
        'label_for_advertise_menu'
    );

    add_settings_field(
        'label_for_advertise_menu',
        'Widget Heading Text',
        'label_for_advertise_menu_callback',
        'footer-settings',
        'footer_settings_section'
    );

    register_setting(
        'footer_settings_group',
        'connect_html'
    );

    add_settings_field(
        'connect_html',
        'Connect',
        'connect_html_callback',
        'footer-settings',
        'footer_settings_section'
    );

    register_setting(
        'footer_settings_group',
        'newsletter_html'
    );

    add_settings_field(
        'newsletter_html',
        'Newsletter',
        'newsletter_html_callback',
        'footer-settings',
        'footer_settings_section'
    );

    register_setting(
        'footer_settings_group',
        'digital_edition_html'
    );

    add_settings_field(
        'digital_edition_html',
        'Digital Edition',
        'digital_edition_html_callback',
        'footer-settings',
        'footer_settings_section'
    );

    register_setting(
        'footer_settings_group',
        'footer_btm_logo'
    );
    
    add_settings_field(
        'footer_btm_logo',
        'Footer BTM Logo',
        'footer_btm_logo_callback',
        'footer-settings',
        'footer_settings_section'
    );

    register_setting(
        'footer_settings_group',
        'copyright_html'
    );

    add_settings_field(
        'copyright_html',
        'Copyright',
        'copyright_html_callback',
        'footer-settings',
        'footer_settings_section'
    );

    
    register_setting(
        'footer_settings_group',
        'footer_bottom_ad'
    );

    add_settings_field(
        'footer_bottom_ad',
        'Footer Bottom Ad',
        'footer_bottom_ad_callback',
        'footer-settings',
        'footer_settings_section'
    );

    register_setting(
        'footer_settings_group',
        'mobile_footer_bottom_ad'
    );

    add_settings_field(
        'mobile_footer_bottom_ad',
        'Mobile Footer Bottom Ad',
        'mobile_footer_bottom_ad_callback',
        'footer-settings',
        'footer_settings_section'
    );

    register_setting(
        'footer_settings_group',
        'footer_hide_from_pages'
    );

    add_settings_field(
        'footer_hide_from_pages',
        'Footer Ads Hide From Pages',
        'footer_hide_from_pages_callback',
        'footer-settings',
        'footer_settings_section'
    );

    register_setting(
        'footer_settings_group',
        'footer_hide_from_post_type'
    );

    add_settings_field(
        'footer_hide_from_post_type',
        'Footer Ads Hide From Post Type',
        'footer_hide_from_post_type_callback',
        'footer-settings',
        'footer_settings_section'
    );
}
add_action('admin_init', 'footer_settings_init');

// Callback function for the site logo field
function footer_site_logo_callback() {
    $footer_site_logo = get_option('footer_site_logo');
    echo '<input type="text" name="footer_site_logo" id="footer_site_logo" value="' . esc_attr($footer_site_logo) . '" />';
    echo '<input type="button" class="button" value="Choose Image" id="upload_logo_button" />';
    echo '<div id="logo_preview"></div>';
    function enqueue_custom_media_scripts() {
        wp_enqueue_media();
    }
    add_action('admin_enqueue_scripts', 'enqueue_custom_media_scripts');
}

// Callback function for the footer navigation menu field
function footer_navigation_menu_callback() {
    $selected_menu = get_option('footer_navigation_menu');
    $menus = wp_get_nav_menus();
    echo '<select name="footer_navigation_menu" id="footer_navigation_menu">';
    echo '<option value="">Select a menu</option>';
    foreach ($menus as $menu) {
        $selected = $selected_menu === $menu->slug ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($menu->slug) . '" ' . $selected . '>' . esc_html($menu->name) . '</option>';
    }
    echo '</select>';
}

// Callback function for the Site About Info text field
function site_about_info_callback() {
    $site_about_info = get_option('site_about_info');
    // echo '<input type="text" name="search_html_box" id="search_html_box" value="' . esc_attr($search_html) . '" />';
    echo '<textarea name="site_about_info" rows="5" cols="100">' . esc_textarea($site_about_info) . '</textarea>';
}


// Callback function for the footer my account Logout HTML field
function my_account_logout_menu_callback() {
    $my_account_logout_menu = get_option('my_account_logout_menu');
    // echo '<input type="text" name="search_html_box" id="search_html_box" value="' . esc_attr($search_html) . '" />';
    echo '<textarea name="my_account_logout_menu" rows="5" cols="100">' . esc_textarea($my_account_logout_menu) . '</textarea>';
}

// Callback function for the contact menu field
function contact_menu_callback() {
    $contact_menu = get_option('contact_menu');
    $menus = wp_get_nav_menus();
    echo '<select name="contact_menu" id="contact_menu">';
    echo '<option value="">Select a menu</option>';
    foreach ($menus as $menu) {
        $selected = $contact_menu === $menu->slug ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($menu->slug) . '" ' . $selected . '>' . esc_html($menu->name) . '</option>';
    }
    echo '</select>';


    function label_for_contact_menu_callback() {
        $label_for_contact_menu = get_option('label_for_contact_menu');
        ?>
        <input type="text" name="label_for_contact_menu" value="<?php echo esc_attr($label_for_contact_menu); ?>">

        <?php
    }
}


// Callback function for the advertise menu field
function advertise_menu_callback() {
    $advertise_menu = get_option('advertise_menu');
    $menus = wp_get_nav_menus();
    echo '<select name="advertise_menu" id="advertise_menu">';
    echo '<option value="">Select a menu</option>';
    foreach ($menus as $menu) {
        $selected = $advertise_menu === $menu->slug ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($menu->slug) . '" ' . $selected . '>' . esc_html($menu->name) . '</option>';
    }
    echo '</select>';
    
    function label_for_advertise_menu_callback() {
        $label_for_advertise_menu = get_option('label_for_advertise_menu');
        ?>
        <input type="text" name="label_for_advertise_menu" value="<?php echo esc_attr($label_for_advertise_menu); ?>">

        <?php
    }
}

// Callback function for the Connect HTML field
function connect_html_callback() {
    $connect_html= get_option('connect_html');
    // echo '<input type="text" name="search_html_box" id="search_html_box" value="' . esc_attr($search_html) . '" />';
    echo '<textarea name="connect_html" rows="5" cols="100">' . esc_textarea($connect_html) . '</textarea>';
}

// Callback function for the Connect HTML field
function newsletter_html_callback() {
    $newsletter_html= get_option('newsletter_html');
    // echo '<input type="text" name="search_html_box" id="search_html_box" value="' . esc_attr($search_html) . '" />';
    echo '<textarea name="newsletter_html" rows="5" cols="100">' . esc_textarea($newsletter_html) . '</textarea>';
}

// Callback function for the Digital Edition HTML field
function digital_edition_html_callback() {
    $digital_edition_html= get_option('digital_edition_html');
    // echo '<input type="text" name="search_html_box" id="search_html_box" value="' . esc_attr($search_html) . '" />';
    echo '<textarea name="digital_edition_html" rows="5" cols="100">' . esc_textarea($digital_edition_html) . '</textarea>';
}


// Callback function for the site logo field
function footer_btm_logo_callback() {
    $footer_btm_logo = get_option('footer_btm_logo');
    echo '<input type="text" name="footer_btm_logo" id="footer_btm_logo" value="' . esc_attr($footer_btm_logo) . '" />';
    echo '<input type="button" class="button" value="Choose Image" id="upload_btm_logo_button" />';
    echo '<div id="footer_btm_logo_preview"></div>';
    function enqueue_custom_media_scripts1() {
        wp_enqueue_media();
    }
    add_action('admin_enqueue_scripts', 'enqueue_custom_media_scripts1');
}

// Callback function for the Copyrght HTML field
function copyright_html_callback() {
    $copyright_html= get_option('copyright_html');
    // echo '<input type="text" name="search_html_box" id="search_html_box" value="' . esc_attr($search_html) . '" />';
    echo '<textarea name="copyright_html" rows="5" cols="100">' . esc_textarea($copyright_html) . '</textarea>';
}



// Callback function for the footer bottom ad field
function footer_bottom_ad_callback() {
    $footer_bottom_ad = get_option('footer_bottom_ad');
    print '<input type="text" name="footer_bottom_ad" value="'. esc_attr($footer_bottom_ad). '" />';
    print '<br>Enter DFP Ad short code here to show on desktop mode.';
}

// Callback function for the footer bottom ad field
function mobile_footer_bottom_ad_callback() {
    $mobile_footer_bottom_ad = get_option('mobile_footer_bottom_ad');
    print '<input type="text" name="mobile_footer_bottom_ad" value="'. esc_attr($mobile_footer_bottom_ad). '" />';
    print '<br>Enter DFP Ad short code here to show on mobile mode.';
}

// Callback function for the hide from pages field
function footer_hide_from_pages_callback() {
    $footer_hide_from_pages = get_option('footer_hide_from_pages');
    $pages = get_pages();
    ?>
    <ul class="footer_hide-from-pages">
        <style>
            ul.footer_hide-from-pages {
                height: 300px;
                overflow-y: scroll;
                max-width: 300px;
                background: #fff;
                padding: 20px;
                border: 1px solid;
            }
            ul.footer_hide-from-pages li{
                list-style: none;
            }
        </style>
        <?php foreach ($pages as $page) : ?>
        <li>
            <label>
            <input type="checkbox" name="footer_hide_from_pages[]" value="<?php echo esc_attr($page->ID); ?>" <?php checked(in_array($page->ID, $footer_hide_from_pages)); ?> />
            <?php echo esc_html($page->post_title); ?>
            </label>
        </li>
        <?php endforeach; ?>
    </ul>
    <p>Select pages from which you want to hide footer bottom ad.</p>
    <?php
}

// Callback function for the hide from post type field
function footer_hide_from_post_type_callback() {
    $footer_hide_from_post_type = get_option('footer_hide_from_post_type');
    $post_types = get_post_types(array('public' => true), 'objects');
    ?>
    <ul class="footer-hide-from-post">
        <style>
            ul.footer-hide-from-post {
                max-width: max-content;
                background: #fff;
                padding: 20px;
                border: 1px solid;
            }
            ul.footer-hide-from-post li{
                list-style: none;
            }
        </style>
        <?php foreach ($post_types as $post_type) : ?>
        <?php if ($post_type->name !== 'attachment') : ?>
            <li>
            <label>
                <input type="checkbox" name="footer_hide_from_post_type[]" value="<?php echo esc_attr($post_type->name); ?>" <?php checked(is_array($footer_hide_from_post_type) && in_array($post_type->name, $footer_hide_from_post_type)); ?> />

                <?php echo esc_html($post_type->label); ?>
            </label>
            </li>
        <?php endif; ?>
        <?php endforeach; ?>
    </ul>
    <p>Select post type from which you want to hide footer bottom ad.</p>
    <?php
}