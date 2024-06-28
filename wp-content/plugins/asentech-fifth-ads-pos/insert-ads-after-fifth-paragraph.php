<?php
/*
  Plugin Name: Asentech - Insert ads after fifth paragraph
  Description: Insert ads after fifth paragraph of single post content.
  Version: 1.0.0
  Author: Asentech
  Plugin URL: http://asentechllc.com/
  Text Domain: insert-ads-after-fifth-paragraph
 */

//enqueue admin js to pre-populate the quick-edit fields


function enqueue_front_scripts_and_styles()
{
    //wp_enqueue_script('quick-edit-script', plugin_dir_url(__FILE__) . '/post-ads-after-fifth-script.js', array('jquery'));
}

add_action('wp_enqueue_scripts', 'enqueue_front_scripts_and_styles');
add_filter('the_content', 'cus_asn_intentads_insert_post_ads', 20, 1);

function cus_asn_intentads_insert_post_ads($content)
{
    if (!$content) {
        return $content;
    }
    global $post;
    $id = $post->ID;
    $sponsored_cmpnyId = get_post_meta($id, 'associated_sponsor_id', true);

    global $wp_query;
    $ad_code = $value = get_option('article_paragraph_ad_code', '');
    $ad_code_1 = $value = get_option('article_paragraph_ad_code_1', '');
    $ad_code_2 = $value = get_option('article_paragraph_ad_code_2', '');
    //echo "<pre>";print_r($ad_code);echo "</pre>";
    $category_list = get_option('copyright_message', '');
    if (!empty($category_list)) {
        $category_list = preg_split('/\n|\r/', $category_list, -1, PREG_SPLIT_NO_EMPTY);
        if (has_category($category_list, $post)) {
            //break;
            return $content;
        }
    }
    $current_meta = isset($wp_query->post->ID) ? get_post_meta($wp_query->post->ID, 'article_paragraph_ad', true) : '';
    $dom = new DOMDocument;
    @$dom->loadHTML($content);
    //$dom->loadHTML($content);
    $all_p_tags = $dom->getElementsByTagName('p')->length; // "prints" 2
    if (isset($current_meta['paragraph_number']) && $current_meta['paragraph_number'] != ""){
        $paragraph_id = $current_meta['paragraph_number'];
    } else {
        if($sponsored_cmpnyId != ''){
            $value_para = 3;
        }else{
            $value_para = get_option('article_paragraph_ad_code_default_para');
        }
        if ($value_para) {
            $paragraph_id = $value_para;
            $paragraph_id_1 = $paragraph_id * 2;
            $paragraph_id_2 = $paragraph_id * 3;
        }elseif($sponsored_cmpnyId != ''){
            $paragraph_id = 5;
            // $paragraph_id_1 = $paragraph_id * 2;
            // $paragraph_id_2 = $paragraph_id * 3;
         } else {
            $paragraph_id = 5;
            $paragraph_id_1 = $paragraph_id * 2;
            $paragraph_id_2 = $paragraph_id * 3;
        }
    }
    // print $sponsored_cmpnyId." ->Company ID <br>";
    // print $paragraph_id." ->Paragraph ID";
    if ($paragraph_id > $all_p_tags) {
        $p_position_number = $all_p_tags;
    }elseif($sponsored_cmpnyId != ''){
        $p_position_number = $paragraph_id;
        $p_position_number_1 = '';
        $p_position_number_2 = '';
    }else {
        $p_position_number = $paragraph_id;
        $p_position_number_1 = $paragraph_id_1;
        $p_position_number_2 = $paragraph_id_2;
    }
    // if($sponsored_cmpnyId != ''){
    //     if($all_p_tags < $paragraph_id){
    //         $p_position_number = 3;
    //     }
    // }
    //echo $ad_code;
    if (is_single() && !is_admin() && is_singular( 'post' )) {
        $sp_post_type = get_post_type();
        //return cus_asn_insert_after_paragraph( $ad_code, 3, $content );
        if ((isset($current_meta['hide_ads']) && $current_meta['hide_ads'] == 1 || empty($current_meta)) && $sp_post_type != 'sponsored_content') {

            $ads .= '<div class="article_dfp_ads article_dfp_ads_non_login individual">' . do_shortcode($ad_code) . '</div><br>';

            $closing_p = '</p>';
            $paragraphs = explode($closing_p, $content);
            foreach ($paragraphs as $index => $paragraph) {
                // if (trim($paragraph)) {
                //     $paragraphs[$index] .= $closing_p;
                // }
                if ($p_position_number == $index + 1) {
                    $paragraphs[$index] .= '<div class="article_dfp_ads">' . do_shortcode($ad_code) . '</div>';
                    // $paragraphs[$index] .= '<div class="article_dfp_ads">' . do_shortcode($ad_code_1) . '</div>';
                    // $paragraphs[$index] .= '<div class="article_dfp_ads">' . do_shortcode($ad_code_2) . '</div>';
                }
                elseif($p_position_number_1 == $index +1){
                    $paragraphs[$index] .= '<div class="article_dfp_ads">' . do_shortcode($ad_code_1) . '</div>';
                }
                // elseif($p_position_number_2 == $index + 1){
                //     $paragraphs[$index] .= '<div class="article_dfp_ads">' . do_shortcode($ad_code_2) . '</div>';
                // }
                 else if ($p_position_number > 5 && !$paragraph_id) {
                    $paragraphs[$index] .= '<div class="article_dfp_ads">' . do_shortcode($ad_code) . '</div>';
                    // $paragraphs[$index] .= '<div class="article_dfp_ads">' . do_shortcode($ad_code_1) . '</div>';
                    // $paragraphs[$index] .= '<div class="article_dfp_ads">' . do_shortcode($ad_code_2) . '</div>';
                }
                
            }
            // return implode('', $paragraphs).$paragraph[$index];
            return implode('', $paragraphs);
        } else {
            return $content;
        }
    }
    return $content;
}


/** added function for display it in general settings */
add_filter('admin_init', 'nj_general_intentads_settings_register_fields');

function nj_general_intentads_settings_register_fields()
{
    register_setting('general', 'article_paragraph_ad_code', 'esc_attr');
    add_settings_field('article_paragraph_ad_code', '<label for="article_paragraph_ad_code">' . __('Article paragraph Ad 1', 'article_paragraph_ad_code') . '</label>', 'nj_general_settings_intentads_fields_html', 'general');

    register_setting('general', 'article_paragraph_ad_code_1', 'esc_attr');
    add_settings_field('article_paragraph_ad_code_1', '<label for="article_paragraph_ad_code_1">' . __('Article paragraph Ads 2', 'article_paragraph_ad_code_1') . '</label>', 'nj_general_settings_intentads_fields_html_1', 'general');

    // register_setting('general', 'article_paragraph_ad_code_2', 'esc_attr');
    // add_settings_field('article_paragraph_ad_code_2', '<label for="article_paragraph_ad_code_2">' . __('Article paragraph Ads 3', 'article_paragraph_ad_code_2') . '</label>', 'nj_general_settings_intentads_fields_html_2', 'general');

    register_setting('general', 'article_paragraph_ad_code_enable_non_login', 'esc_attr');
    add_settings_field('article_paragraph_ad_code_enable_non_login', '<label for="article_paragraph_ad_code_enable_non_login">' . __('Article paragraph Enable Non Logged In User', 'article_paragraph_ad_code_enable_non_login') . '</label>', 'nj_general_settings_intentads_fields_html_enable_non_logged_in', 'general');

    register_setting('general', 'article_paragraph_ad_code_default_para', 'esc_attr');
    add_settings_field('article_paragraph_ad_code_default_para', '<label for="article_paragraph_ad_code_default_para">' . __('Article paragraph default paragraph', 'article_paragraph_ad_code_default_para') . '</label>', 'nj_general_settings_intentads_fields_html_default_para', 'general');
}


function nj_general_settings_intentads_fields_html_default_para()
{
    $value = get_option('article_paragraph_ad_code_default_para');
    if ($value) {
        echo '<input type="text" name="article_paragraph_ad_code_default_para" value="' . $value . '" />';
    } else {
        echo '<input type="text" name="article_paragraph_ad_code_default_para" value="5" />';
    }
}

function nj_general_settings_intentads_fields_html()
{
    $value = get_option('article_paragraph_ad_code', '');
    echo '<textarea id="article_paragraph_ad_code" rows="5" cols="80" name="article_paragraph_ad_code" value="" />' . $value . ' </textarea>';
}

function nj_general_settings_intentads_fields_html_1()
{
    $value = get_option('article_paragraph_ad_code_1', '');
    echo '<textarea id="article_paragraph_ad_code_1" rows="5" cols="80" name="article_paragraph_ad_code_1" value="" />' . $value . ' </textarea>';
}

// function nj_general_settings_intentads_fields_html_2()
// {
//     $value = get_option('article_paragraph_ad_code_2', '');
//     echo '<textarea id="article_paragraph_ad_code_2" rows="5" cols="80" name="article_paragraph_ad_code_2" value="" />' . $value . ' </textarea>';
// }

function nj_general_settings_intentads_fields_html_enable_non_logged_in()
{
    $value = get_option('article_paragraph_ad_code_enable_non_login', '');
    //echo '<radio id="article_paragraph_ad_code_enable_non_login" rows="10" cols="80" name="article_paragraph_ad_code_enable_non_login" value="" />' . $value . ' </textarea>';
?>

    <input type="radio" name="article_paragraph_ad_code_enable_non_login" value="1" checked>Yes
    <input type="radio" name="article_paragraph_ad_code_enable_non_login" value="0" <?php if ($value === '0') echo 'checked'; ?>>No
<?php
}
/** added function for display it in general settings */

/**
    add_filter('admin_init', 'my_general_settings_register_fields'); 
    function my_general_settings_register_fields() { 
    register_setting('general', 'copyright_message', 'esc_attr'); 
    add_settings_field('copyright_message', '<label for="copyright_message">'.__('Categories to exclude In-Content ads' , 'copyright_message' ).'</label><span class="note_content" style="float: left; font-size: 12px; color: red; font-weight: normal;">( Enter the slugs of the categories on which the in-content ads will be suppressed. Enter only one slug per line. )</span>' , 'my_general_copyright_message', 'general'); } 
 */

function my_general_copyright_message()
{
    /*foreach($categories as $category) {
   //echo $category->slug;
   $category_list_name  = explode(PHP_EOL, $category->slug);
   print_r($checked1[1]);
    
}*/

    $copyright_message = get_option('copyright_message');
    echo "<textarea rows='4' cols='50' name='copyright_message' id='copyright_message'>" . $copyright_message . "</textarea>";
    $user_ids = array();
    $options = get_option('copyright_message', array(), true);


    $categories = get_categories();
    //$copyright_message = $_REQUEST[ 'copyright_message' ];
    $checked = get_option('copyright_message');
    $checked1  = explode(PHP_EOL, $checked);
    $categoryar = array();
    foreach ($categories as $category) {
        $categoryar[] = $category->slug;
    }
    foreach ($checked1 as $checkednew) {
        if (trim($checkednew))
            if (in_array(trim($checkednew), $categoryar)) :
                echo "";
            else :
                echo "<br>";
                echo "<div class='category_error'> <span class='category_error_div' style='float: left; font-size: 12px; color: red; font-weight: normal;'> Invalid Category : " . $checkednew . "</span></div>";
            endif;
    }
}



/** added custom fields for enable or disable ads between paragraph */
function add_article_intentads_paragraph_ad_meta_box()
{
    add_meta_box(
        'article_paragraph_ad_meta_box', // $id
        'Article Paragraph DFP Ads', // $title
        'show_article_intentads_paragraph_ad_meta_box', // $callback
        'post', // $screen
        'normal', // $context
        'high' // $priority
    );
}
add_action('add_meta_boxes', 'add_article_intentads_paragraph_ad_meta_box');
function show_article_intentads_paragraph_ad_meta_box()
{
    global $post;

    $meta = get_post_meta($post->ID, 'article_paragraph_ad', true); ?>

    <input type="hidden" name="your_meta_box_nonce" value="<?php echo wp_create_nonce(basename(__FILE__)); ?>">
    <p>
        <label for="article_paragraph_ad[hide_ads]">Display Ads on this article?
            <input type="radio" name="article_paragraph_ad[hide_ads]" value="1" checked>Yes
            <input type="radio" name="article_paragraph_ad[hide_ads]" value="0" <?php if (isset($meta['hide_ads']) && $meta['hide_ads'] === '0') echo 'checked'; ?>>No
        </label>
    </p>

    <p>
        <label for="article_paragraph_ad[paragraph_number]">Number Of Paragraph</label>
        <br>
        <input type="text" name="article_paragraph_ad[paragraph_number]" id="article_paragraph_ad[paragraph_number]" class="regular-text" value="<?php if (is_array($meta) && isset($meta['paragraph_number'])) {
                                                                                                                                                        echo $meta['paragraph_number'];
                                                                                                                                                    } ?>">(Please add paragraph number after which paragraph need to display Ads.)
    </p>

    <p>
        <label for="article_paragraph_ad[article_paragraph_ad_code_enable_non_login]">Article Paragraph Enable Non Logged In User</label>
        <br>
        <input type="radio" name="article_paragraph_ad[article_paragraph_ad_code_enable_non_login]" value="1" 
        <?php 
        if (!empty($meta['article_paragraph_ad_code_enable_non_login'])) {
        if ($meta['article_paragraph_ad_code_enable_non_login'] === '1'){
         echo 'checked'; } }?>>Yes
        <input type="radio" name="article_paragraph_ad[article_paragraph_ad_code_enable_non_login]" value="0" 
        <?php 
        if (!empty($meta['article_paragraph_ad_code_enable_non_login'])) {
        if ($meta['article_paragraph_ad_code_enable_non_login'] === '0') { echo 'checked'; } }?>>No
    </p>

<?php }
function save_article_intentads_paragraph_ad_meta($post_id)
{
    // verify nonce
    if (
        isset($_POST['your_meta_box_nonce'])
        && !wp_verify_nonce($_POST['your_meta_box_nonce'], basename(__FILE__))
    ) {
        return $post_id;
    }
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    // check permissions
    if (isset($_POST['post_type'])) { //Fix 2
        if ('page' === $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            } elseif (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        }
    }

    $old = get_post_meta($post_id, 'article_paragraph_ad', true);
    if (isset($_POST['article_paragraph_ad'])) { //Fix 3
        $new = $_POST['article_paragraph_ad'];
        if ($new && $new !== $old) {
            update_post_meta($post_id, 'article_paragraph_ad', $new);
        } elseif ('' === $new && $old) {
            delete_post_meta($post_id, 'article_paragraph_ad', $old);
        }
    }
}
add_action('save_post', 'save_article_intentads_paragraph_ad_meta');
/** added custom fields for enable or disable ads between paragraph */
?>