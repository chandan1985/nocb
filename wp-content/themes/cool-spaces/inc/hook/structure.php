<?php
/**
 * Theme functions related to structure.
 *
 * This file contains structural hook functions.
 *
 * @package Mag_lite
 */
/* ----------------------------------------------------------------------------------- */
# Typography Elements Array
/* ----------------------------------------------------------------------------------- */
$custom_typography = array(
    "body" => "typography_general",
    ".logo h1 a, .logo h2 a" => "typography_site_title",
    ".logo span" => "typography_tagline",
    ".top-nav, .top-nav ul li a, .breaking-news span " => "typography_top_menu",
    "#main-nav, #main-nav ul li a" => "typography_main_nav",
    ".page-title" => "typography_page_title",
    ".post-title" => "typography_post_title",
    "h2.post-box-title, h2.post-box-title a" => "typography_post_title_boxes",
    "h3.post-box-title, h3.post-box-title a" => "typography_post_title2_boxes",
    ".ei-title h2 , .slider-caption h2 a, .content .slider-caption h2 a, .slider-caption h2, .content .slider-caption h2, .content .ei-title h2" => "typography_slider_title",
    "p.post-meta, p.post-meta a" => "typography_post_meta",
    "body.single .entry, body.page .entry" => "typography_post_entry",
    ".widget-top h4, .widget-top h4 a" => "typography_widgets_title",
    ".footer-widget-top h4, .footer-widget-top h4 a" => "typography_footer_widgets_title",
    ".entry h1" => "typography_post_h1",
    ".entry h2" => "typography_post_h2",
    ".entry h3" => "typography_post_h3",
    ".entry h4" => "typography_post_h4",
    ".entry h5" => "typography_post_h5",
    ".entry h6" => "typography_post_h6",
    "h2.cat-box-title, h2.cat-box-title a, .block-head h3, #respond h3, #comments-title, h2.review-box-header  " => "typography_boxes_title",
);


if (!function_exists('mag_lite_doctype')) :

    /**
     * Doctype Declaration.
     *
     * @since 1.0.0
     */
    function mag_lite_doctype() {
        ?><!DOCTYPE html> <html <?php language_attributes(); ?>><?php
        }

    endif;

    add_action('mag_lite_action_doctype', 'mag_lite_doctype', 10);

    if (!function_exists('mag_lite_head')) :

        /**
         * Header Codes.
         *
         * @since 1.0.0
         */
        function mag_lite_head() {
            global $custom_typography;
            ?>
          <meta charset="<?php bloginfo('charset'); ?>">
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <link rel="profile" href="http://gmpg.org/xfn/11">  

          <link rel='stylesheet' id='Oswald-css'  href='//fonts.googleapis.com/css?family=Oswald%3Aregular%2C700' type='text/css' media='all' />
          <link rel="stylesheet" id="tie-style-css" href="<?php echo network_site_url(); ?>wp-content/themes/jarida/style.css" type="text/css" media="all">
          <link rel="stylesheet" id="tie-style-css" href="<?php echo network_site_url(); ?>wp-content/themes/jarida/css/mobilemenu.css" type="text/css" media="all">
          <style type="text/css" media="screen">
        <?php echo "\n"; ?>
        <?php if (tie_get_option('background_type') == 'pattern'):
            if (tie_get_option('background_pattern') || tie_get_option('background_pattern_color')):
                ?>
                    body {
                <?php if (tie_get_option('background_pattern_color')) { ?> background-color: <?php echo tie_get_option('background_pattern_color') ?> !important; <?php } ?>
                <?php if (tie_get_option('background_pattern')) { ?> background-image : url(<?php echo get_template_directory_uri(); ?>/images/patterns/<?php echo tie_get_option('background_pattern') ?>.png);<?php } ?>
                        background-position: top center;
                    }
            <?php endif; ?>
        <?php elseif (tie_get_option('background_type') == 'custom'):
            $bg = tie_get_option('background');
            if (tie_get_option('background_full')):
                ?>
                    body{background:<?php if (!empty($bg['color'])) echo $bg['color'];
                else echo "#FFF" ?>;}
                    .background-cover{<?php echo "\n"; ?>
                        background-color:<?php echo $bg['color'] ?> !important;
                <?php if (!empty($bg['img'])) { ?>background-image : url('<?php echo $bg['img'] ?>') !important;<?php echo "\n"; ?>
                            filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $bg['img'] ?>',sizingMethod='scale') !important;<?php echo "\n"; ?>
                            -ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $bg['img'] ?>',sizingMethod='scale')" !important;<?php echo "\n";
                } ?>
                    }
            <?php else: ?>
                    body{
                <?php if (!empty($bg['color'])) { ?>background-color:<?php echo $bg['color'] ?> !important; <?php echo "\n";
                } ?>
                <?php if (!empty($bg['img'])) { ?>background-image: url('<?php echo $bg['img'] ?>') !important; <?php echo "\n";
                } ?>
                <?php if (!empty($bg['repeat'])) { ?>background-repeat:<?php echo $bg['repeat'] ?> !important; <?php echo "\n";
                } ?>
                <?php if (!empty($bg['attachment'])) { ?>background-attachment:<?php echo $bg['attachment'] ?> !important; <?php echo "\n";
                } ?>
                <?php if (!empty($bg['hor']) || !empty($bg['ver'])) { ?>background-position:<?php echo $bg['hor'] ?> <?php echo $bg['ver'] ?> !important; <?php echo "\n";
                } ?>
                    }
            <?php endif; ?>
        <?php endif; ?>
        <?php
        foreach ($custom_typography as $selector => $value) {
            $option = tie_get_option($value);
            if ((isset($option['font']) && $option['font']) || (isset($option['color']) && $option['color']) || (isset($option['size']) && $option['size']) || (isset($option['weight']) && $option['weight']) || (isset($option['style']) && $option['style'])):
                echo "\n" . $selector . "{\n";
                ?>
                <?php if (isset($option['font']) && $option['font'])
                    echo "	font-family: " . tie_get_font($option['font']) . ";\n"
                    ?>
                <?php if (isset($option['color']) && $option['color'])
                    echo "	color :" . $option['color'] . " !important;\n"
                    ?>
                <?php if (isset($option['size']) && $option['size'])
                    echo "	font-size : " . $option['size'] . "px !important;\n"
                    ?>
                <?php if (isset($option['weight']) && $option['weight'])
                    echo "	font-weight: " . $option['weight'] . " !important;\n"
                    ?>
                <?php if (isset($option['style']) && $option['style'])
                    echo "	font-style: " . $option['style'] . " !important;\n"
                    ?>
                    }
            <?php endif;
        }
        ?>
        <?php if (tie_get_option('global_color')) tie_theme_color(tie_get_option('global_color')); ?>
        <?php if (tie_get_option('links_color') || tie_get_option('links_decoration')): ?>
                a {
            <?php if (tie_get_option('links_color')) echo 'color: ' . tie_get_option('links_color') . ';'; ?>
            <?php if (tie_get_option('links_decoration')) echo 'text-decoration: ' . tie_get_option('links_decoration') . ';'; ?>
                }
        <?php endif; ?>
        <?php if (tie_get_option('links_color_hover') || tie_get_option('links_decoration_hover')): ?>
                a:hover {
            <?php if (tie_get_option('links_color_hover')) echo 'color: ' . tie_get_option('links_color_hover') . ';'; ?>
            <?php if (tie_get_option('links_decoration_hover')) echo 'text-decoration: ' . tie_get_option('links_decoration_hover') . ';'; ?>
                }
        <?php endif; ?>
        <?php if (tie_get_option('highlighted_color')): ?>
                ::-moz-selection { background: <?php echo tie_get_option('highlighted_color') ?>;}
                ::selection { background: <?php echo tie_get_option('highlighted_color') ?>; }
        <?php endif; ?>
        <?php if (tie_get_option('topbar_background')): ?>
                .top-nav, .top-nav ul ul {background-color:<?php echo tie_get_option('topbar_background'); ?>;}<?php echo "\n"; ?>
        <?php endif; ?>
        <?php if (tie_get_option('topbar_links_color') || tie_get_option('topbar_shadow_color')): ?>
                .top-nav ul li a , .top-nav ul ul a {
            <?php if (tie_get_option('topbar_links_color')) echo 'color: ' . tie_get_option('topbar_links_color') . ' !important;'; ?>
            <?php if (tie_get_option('topbar_shadow_color')) echo 'text-shadow: 0 1px 1px ' . tie_get_option('topbar_shadow_color') . ' !important;'; ?>
                }
        <?php endif; ?>
        <?php if (tie_get_option('topbar_links_color_hover') || tie_get_option('topbar_shadow_color_hover')): ?>
                .top-nav ul li a:hover, .top-nav ul li:hover > a, .top-nav ul :hover > a , .top-nav ul li.current-menu-item a  {
            <?php if (tie_get_option('topbar_links_color_hover')) echo 'color: ' . tie_get_option('topbar_links_color_hover') . ' !important;'; ?>
            <?php if (tie_get_option('topbar_shadow_color_hover')) echo 'text-shadow: 0 1px 1px ' . tie_get_option('topbar_shadow_color_hover') . ' !important;'; ?>
                }
        <?php endif; ?>
        <?php $header_bg = tie_get_option('header_background');
        if (!empty($header_bg['img']) || !empty($header_bg['color'])):
            ?>
                header#theme-header{background:<?php echo $header_bg['color'] ?> <?php if (!empty($header_bg['img'])) { ?>url('<?php echo $header_bg['img'] ?>')<?php } ?> <?php echo $header_bg['repeat'] ?> <?php echo $header_bg['attachment'] ?> <?php echo $header_bg['hor'] ?> <?php echo $header_bg['ver'] ?> !important;}<?php echo "\n"; ?>
        <?php endif; ?>
        <?php if (tie_get_option('nav_background')): ?>
                #main-nav, #main-nav ul ul, #main-nav ul li.mega-menu .mega-menu-block {background-color:<?php echo tie_get_option('nav_background') . ' !important;'; ?>;}<?php echo "\n"; ?>
        <?php endif; ?>
        <?php if (tie_get_option('nav_links_color') || tie_get_option('nav_shadow_color')): ?>
                #main-nav ul li a , #main-nav ul ul a , #main-nav ul.sub-menu a {
            <?php if (tie_get_option('nav_links_color')) echo 'color: ' . tie_get_option('nav_links_color') . ' !important;'; ?>
            <?php if (tie_get_option('nav_shadow_color')) echo 'text-shadow: 0 1px 1px ' . tie_get_option('nav_shadow_color') . ' !important;'; ?>
                }
        <?php endif; ?>
        <?php if (tie_get_option('nav_links_color_hover') || tie_get_option('nav_shadow_color_hover')): ?>
                #main-nav ul li a:hover, #main-nav ul li:hover > a, #main-nav ul :hover > a , #main-nav  ul ul li:hover > a, #main-nav  ul ul :hover > a  {
            <?php if (tie_get_option('nav_links_color_hover')) echo 'color: ' . tie_get_option('nav_links_color_hover') . ' !important;'; ?>
            <?php if (tie_get_option('nav_shadow_color_hover')) echo 'text-shadow: 0 1px 1px ' . tie_get_option('nav_shadow_color_hover') . ' !important;'; ?>
                }
        <?php endif; ?>
        <?php if (tie_get_option('nav_current_links_color') || tie_get_option('nav_current_shadow_color')): ?>
                #main-nav ul li.current-menu-item a  {
            <?php if (tie_get_option('nav_current_links_color')) echo 'color: ' . tie_get_option('nav_current_links_color') . ' !important;'; ?>
            <?php if (tie_get_option('nav_current_shadow_color')) echo 'text-shadow: 0 1px 1px ' . tie_get_option('nav_current_shadow_color') . ' !important;'; ?>
                }
        <?php endif; ?>
        <?php if (tie_get_option('nav_sep1')): ?>
                #main-nav ul li {
                    border-color: <?php echo tie_get_option('nav_sep1'); ?>;
                }
                #main-nav ul ul li, #main-nav ul ul li:first-child {
                    border-top-color: <?php echo tie_get_option('nav_sep1'); ?>;
                }
        <?php endif; ?>
        <?php if (tie_get_option('nav_sep2')): ?>
                #main-nav ul li a {
                    border-left-color: <?php echo tie_get_option('nav_sep2'); ?>;
                }
                #main-nav ul ul li, #main-nav ul ul li:first-child {
                    border-bottom-color: <?php echo tie_get_option('nav_sep2'); ?>;
                }
        <?php endif; ?>
        <?php $content_bg = tie_get_option('main_content_bg');
        if (!empty($content_bg['img']) || !empty($content_bg['color'])):
            ?>
                .wrapper{background:<?php echo $content_bg['color'] ?> <?php if (!empty($content_bg['img'])) { ?>url('<?php echo $content_bg['img'] ?>')<?php } ?> <?php echo $content_bg['repeat'] ?> <?php echo $content_bg['attachment'] ?> <?php echo $content_bg['hor'] ?> <?php echo $content_bg['ver'] ?>;}<?php echo "\n"; ?>
        <?php endif; ?>

        <?php if (tie_get_option('post_links_color') || tie_get_option('post_links_decoration')): ?>
                body.single .post .entry a, body.page .post .entry a {
            <?php if (tie_get_option('post_links_color')) echo 'color: ' . tie_get_option('post_links_color') . ';'; ?>
            <?php if (tie_get_option('post_links_decoration')) echo 'text-decoration: ' . tie_get_option('post_links_decoration') . ';'; ?>
                }
        <?php endif; ?>
        <?php if (tie_get_option('post_links_color_hover') || tie_get_option('post_links_decoration_hover')): ?>
                body.single .post .entry a:hover, body.page .post .entry a:hover {
            <?php if (tie_get_option('post_links_color_hover')) echo 'color: ' . tie_get_option('post_links_color_hover') . ';'; ?>
            <?php if (tie_get_option('post_links_decoration_hover')) echo 'text-decoration: ' . tie_get_option('post_links_decoration_hover') . ';'; ?>
                }
        <?php endif; ?>
        <?php $footer_bg = tie_get_option('footer_background');
        if (!empty($footer_bg['img']) || !empty($footer_bg['color'])):
            ?>
                footer , .footer-bottom{background:<?php echo $footer_bg['color'] ?> <?php if (!empty($footer_bg['img'])) { ?>url('<?php echo $footer_bg['img'] ?>')<?php } ?> <?php echo $footer_bg['repeat'] ?> <?php echo $footer_bg['attachment'] ?> <?php echo $footer_bg['hor'] ?> <?php echo $footer_bg['ver'] ?>;}<?php echo "\n"; ?>
        <?php endif; ?>
        <?php if (tie_get_option('footer_title_color')): ?>
                .footer-widget-top h3 {	<?php if (tie_get_option('footer_title_color')) echo 'color: ' . tie_get_option('footer_title_color') . ';'; ?>
                }
        <?php endif; ?>
        <?php if (tie_get_option('footer_links_color')): ?>
                footer a  {	<?php if (tie_get_option('footer_links_color')) echo 'color: ' . tie_get_option('footer_links_color') . ' !important;'; ?>
                }
        <?php endif; ?>
        <?php if (tie_get_option('footer_links_color_hover')): ?>
                footer a:hover {<?php if (tie_get_option('footer_links_color_hover')) echo 'color: ' . tie_get_option('footer_links_color_hover') . ' !important;'; ?>
                }
        <?php endif; ?>
        <?php
//Custom Categories and Posts Colors.
        global $post;
        $cat_bg = $cat_color = $cat_full = '';
        if (is_category() || is_singular() || ( function_exists('is_woocommerce') && is_woocommerce() )):
            if (is_category()) {
                $category_id = get_query_var('cat');
                $cat_options = get_option("tie_cat_$category_id");

                if (!empty($cat_options['cat_background']))
                    $cat_bg = $cat_options['cat_background'];

                if (!empty($cat_options['cat_color']))
                    $cat_color = $cat_options['cat_color'];

                if (!empty($cat_options['cat_background_full']))
                    $cat_full = $cat_options['cat_background_full'];
            }
            if (is_singular() || ( function_exists('is_woocommerce') && is_woocommerce() )) {
                $current_ID = $post->ID;
                if (function_exists('is_woocommerce') && is_woocommerce())
                    $current_ID = woocommerce_get_page_id('shop');

                $get_meta = get_post_custom($current_ID);

                if (!empty($get_meta['post_color'][0]))
                    $cat_color = $get_meta["post_color"][0];

                if (!empty($get_meta['post_background'][0]))
                    $cat_bg = unserialize($get_meta["post_background"][0]);

                if (!empty($get_meta['post_background_full'][0]))
                    $cat_full = $get_meta['post_background_full'][0];

                if (is_single()) {
                    $categories = get_the_category($post->ID);
                    $category_id = $categories[0]->term_id;
                    $cat_options = get_option("tie_cat_$category_id");

                    if (empty($cat_color) && !empty($cat_options['cat_color']))
                        $cat_color = $cat_options['cat_color'];
                    if (empty($cat_full) && !empty($cat_options['cat_background_full']))
                        $cat_full = $cat_options['cat_background_full'];
                    if (empty($cat_bg['color']) && empty($cat_bg['img']) && !empty($cat_options['cat_background']))
                        $cat_bg = $cat_options['cat_background'];
                }
            }

            if (!empty($cat_bg['color']) || !empty($cat_bg['img'])):
                if ($cat_full):
                    ?>
                        .background-cover{<?php echo "\n"; ?>
                            background-color:<?php echo $cat_bg['color'] ?> !important;
                    <?php if (!empty($cat_bg['img'])) { ?>background-image : url('<?php echo $cat_bg['img'] ?>') !important;<?php echo "\n"; ?>
                                filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $cat_bg['img'] ?>',sizingMethod='scale') !important;<?php echo "\n"; ?>
                                -ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $cat_bg['img'] ?>',sizingMethod='scale')" !important;<?php echo "\n";
                    } ?>
                        }
                <?php else: ?>
                        body{
                    <?php if (!empty($cat_bg['color'])) { ?>background-color:<?php echo $cat_bg['color'] ?> !important; <?php echo "\n";
                    } ?>
                    <?php if (!empty($cat_bg['img'])) { ?>background-image: url('<?php echo $cat_bg['img'] ?>') !important; <?php echo "\n";
                    } ?>
                    <?php if (!empty($cat_bg['repeat'])) { ?>background-repeat:<?php echo $cat_bg['repeat'] ?> !important; <?php echo "\n";
                    } ?>
                    <?php if (!empty($cat_bg['attachment'])) { ?>background-attachment:<?php echo $cat_bg['attachment'] ?> !important; <?php echo "\n";
                    } ?>
                    <?php if (!empty($cat_bg['hor']) || !empty($cat_bg['ver'])) { ?>background-position:<?php echo $cat_bg['hor'] ?> <?php echo $cat_bg['ver'] ?> !important; <?php echo "\n";
                    } ?>
                        }<?php echo "\n"; ?>
                        .background-cover{<?php echo "\n"; ?>
                            background-color: transparent !important;
                            background-image : none !important;<?php echo "\n"; ?>
                            filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='none',sizingMethod='scale') !important;<?php echo "\n"; ?>
                            -ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='none',sizingMethod='scale')" !important;<?php echo "\n"; ?>
                        }
                <?php
                endif;
            endif;
            if (!empty($cat_color))
                tie_theme_color($cat_color);
            ?>
        <?php endif; ?>
        <?php $css_code = str_replace("<pre>", "", htmlspecialchars_decode(tie_get_option('css')));
        echo $css_code = str_replace("</pre>", "", $css_code), "\n";
        ?>
        <?php if (tie_get_option('css_tablets')) : ?>
                @media only screen and (max-width: 985px) and (min-width: 768px){
            <?php $css_code1 = str_replace("<pre>", "", htmlspecialchars_decode(tie_get_option('css_tablets')));
            echo $css_code1 = str_replace("</pre>", "", $css_code1), "\n";
            ?>
                }
          <?php endif; ?>
          <?php if (tie_get_option('css_wide_phones')) : ?>
                @media only screen and (max-width: 767px) and (min-width: 480px){
              <?php $css_code2 = str_replace("<pre>", "", htmlspecialchars_decode(tie_get_option('css_wide_phones')));
              echo $css_code2 = str_replace("</pre>", "", $css_code2), "\n";
              ?>
                }
          <?php endif; ?>
          <?php if (tie_get_option('css_phones')) : ?>
                @media only screen and (max-width: 479px) and (min-width: 320px){
              <?php $css_code3 = str_replace("<pre>", "", htmlspecialchars_decode(tie_get_option('css_phones')));
              echo $css_code3 = str_replace("</pre>", "", $css_code3), "\n";
              ?>
                }
          <?php endif; ?>

          <?php
          if (is_home() && tie_get_option('on_home') == 'boxes' && tie_get_option('homepage_cats_colors')) {
              $categories_obj = get_categories('hide_empty=0');
              foreach ($categories_obj as $pn_cat) {
                  $category_id = $pn_cat->cat_ID;
                  $cat_options = get_option("tie_cat_$category_id");

                  if (!empty($cat_options['cat_color'])) {
                      $cat_custom_color = $cat_options['cat_color'];
                      ?>
                        .tie-cat-<?php echo $category_id ?> .cat-box-title, .tie-cat-<?php echo $category_id ?> .scroll-nav a, .tie-cat-<?php echo $category_id ?> a.more-link{background-color:<?php echo $cat_custom_color; ?> !important;}
                        .tie-cat-<?php echo $category_id ?> {border-top-color:<?php echo $cat_custom_color; ?> !important; }
                      <?php
                  }
              }
          }
          ?>
          </style>

        <?php
    }

endif;

add_action('mag_lite_action_head', 'mag_lite_head', 10);

function tie_get_option($name) {
    //return get_blog_option(1, $arg1 );
    $get_options = get_blog_option(1, 'tie_options');

    if (!empty($get_options[$name]))
        return $get_options[$name];

    return false;
}

/* ----------------------------------------------------------------------------------- */
# Get Font Name
/* ----------------------------------------------------------------------------------- */

function tie_get_font($got_font) {
    if ($got_font) {
        $font_pieces = explode(":", $got_font);
        $font_name = $font_pieces[0];
        $font_name = str_replace('&quot;', '"', $font_pieces[0]);
        if (strpos($font_name, ',') !== false)
            return $font_name;
        else
            return "'" . $font_name . "'";
    }
}

function tie_theme_color($color) {
    ?>
      #main-nav ul li.current-menu-item a,
      #main-nav ul li.current-menu-item a:hover,
      #main-nav ul li.current-menu-parent a,
      #main-nav ul li.current-menu-parent a:hover,
      #main-nav ul li.current-page-ancestor a,
      #main-nav ul li.current-page-ancestor a:hover,
      .pagination span.current,
      .ei-slider-thumbs li.ei-slider-element,
      .breaking-news span,
      .ei-title h2,h2.cat-box-title,
      a.more-link,.scroll-nav a,
      .flex-direction-nav a,
      .tagcloud a:hover,
      #tabbed-widget ul.tabs li.active a,
      .slider-caption h2, .full-width .content .slider-caption h2,
      .review-percentage .review-item span span,.review-final-score,
      .woocommerce span.onsale, .woocommerce-page span.onsale ,
      .woocommerce .widget_price_filter .ui-slider .ui-slider-handle, .woocommerce-page .widget_price_filter .ui-slider .ui-slider-handle,
      .button,a.button,#main-content input[type="submit"],#main-content input[type="submit"]:focus, span.onsale,
      .mejs-container .mejs-controls .mejs-time-rail .mejs-time-current,
      #reading-position-indicator {
      background-color:<?php echo $color; ?> !important;
      }
      ::-webkit-scrollbar-thumb{background-color:<?php echo $color; ?> !important;}
      #main-nav,.top-nav {border-bottom-color: <?php echo $color; ?>;}
      .cat-box , .footer-bottom .container{border-top-color: <?php echo $color; ?>;}
      .page-head.category-template{border-color: <?php echo $color; ?> !important;}
                  <?php
              }

              if (!function_exists('mag_lite_page_start')) :

                  /**
                   * Page Start.
                   *
                   * @since 1.0.0
                   */
                  function mag_lite_page_start() {
                      ?>
          <div id="page" class="wrapper full-site">
            <div class="top-nav fade-in animated1 ">
              <div class="container">
                <div class="top-menu">
                  <ul id="menu-subscribe" class="menu">
                    <li id="menu-item-263105" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-263105"><a href="<?php echo network_site_url('/subscribe/', 'https'); ?>">Subscribe</a></li>
                    <li id="menu-item-481124" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-481124"><a href="<?php echo network_site_url('/manage-account/', 'https'); ?>">Manage Account</a></li>
                    <li><a href="<?php echo network_site_url(); ?>?dmcss=login">LOG IN</a></li>
                  </ul>
                </div>
                <div class="top-menu-liney" style="display:none;">
                  <a class="top-menu-footer-liney" href="javascript:;" onclick="toggleMobileMenu();">
                  </a>
                </div>


              </div>
            </div>
            <!-- .top-menu /-->
            <div class="container">
              <header id="theme-header">
                <div class="header-content fade-in animated1">
                    <?php $logo_margin = '';
                    if (tie_get_option('logo_margin')) $logo_margin = ' style="margin-top:' . tie_get_option('logo_margin') . 'px"'; ?>
                  <div class="logo"<?php echo $logo_margin ?>>
                    <?php
                    $site_identity = mag_lite_get_option('site_identity');
                    $title = get_bloginfo('name', 'display');
                    $description = get_bloginfo('description', 'display');

                    if ('logo-only' == $site_identity) {

                        if (has_custom_logo()) {

                            the_custom_logo();
                        }
                    } elseif ('logo-text' == $site_identity) {

                        if (has_custom_logo()) {

                            the_custom_logo();
                        }

                        if ($description) {
                            echo '<p class="site-description">' . esc_attr($description) . '</p>';
                        }
                    } elseif ('title-only' == $site_identity && $title) {
                        ?>

                        <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
            <?php
        } elseif ('title-text' == $site_identity) {

            if ($title) {
                ?>

                            <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
                            <?php
                        }

                        if ($description) {

                            echo '<p class="site-description">' . esc_attr($description) . '</p>';
                        }
                    }
                    ?>


                    <?php
                    if (!$site_identity):

                        if (!is_singular())
                            echo '<h1>';
                        else
                            echo '<h2>';
                        ?>

            <?php
            // masthead url
            if (!tie_get_option('masthead_url')) {
                $cur_blog_id = defined('BLOG_ID_CURRENT_SITE') ? BLOG_ID_CURRENT_SITE : 1;
                $mastheadlink = get_site_url();
            } else {
                $mastheadlink = home_url();
            }
            ?>
                          <?php if (tie_get_option('logo_setting') == 'title'): ?>
                            <a  href="<?php echo $mastheadlink; ?>/"><?php bloginfo('name'); ?></a>
                            <span><?php bloginfo('description'); ?></span>
                          <?php else : ?>
                          <?php
                          if (tie_get_option('logo'))
                              $logo = tie_get_option('logo');
                          else
                              $logo = get_stylesheet_directory_uri() . '/images/logo.png';
                          // custom post header image
                          if (is_singular()) {
                              $get_meta = get_post_custom($post->ID);
                              if (!empty($get_meta['tdc_custom_header'][0])) {
                                  $logo = $get_meta['tdc_custom_header'][0];
                              }
                          }

                          $svg_fixer = str_replace('.svg', '.png', $logo);
                          ?>
                            <a title="<?php bloginfo('name'); ?>" href="<?php echo $mastheadlink; ?>/">
                              <img onerror="this.src='<?php echo $svg_fixer; ?>';this.onerror=null;" src="<?php echo $logo; ?>" alt="<?php bloginfo('name'); ?>" /><strong><?php bloginfo('name'); ?> <?php bloginfo('description'); ?></strong>
                            </a>
                        <?php endif; ?>
                        <?php if (!is_singular()) echo '</h1>';
                        else echo '</h2>'; ?>
                    <?php endif; ?>
                  </div><!-- .logo /-->
        <?php if (tie_get_option('logo_retina') && tie_get_option('logo_retina_width') && tie_get_option('logo_retina_height')): ?>
                      <script type="text/javascript">
                          jQuery(document).ready(function ($) {
                              var retina = window.devicePixelRatio > 1 ? true : false;
                              if (retina) {
                                  jQuery('#theme-header .logo img').attr('src', '<?php echo tie_get_option('logo_retina'); ?>');
                                  jQuery('#theme-header .logo img').attr('width', '<?php echo tie_get_option('logo_retina_width'); ?>');
                                  jQuery('#theme-header .logo img').attr('height', '<?php echo tie_get_option('logo_retina_height'); ?>');
                              }
                          });
                      </script>
              <?php endif; ?>
              <?php /* BEGIN CUSTOM MOBILE MENU IMPLEMENTATION */ ?>
              <?php if ($mobile_menu = 1) { ?>
                      <div class="overlay" id="mobilemenu" >
                  <?php
                  get_template_part('template-parts/mobile-menu');
                  ?>
                      </div>
              <?php } ?>
              <?php /* BEGIN CUSTOM MOBILE MENU IMPLEMENTATION */ ?>
                  <div class="e3lan-top">
              <?php if (is_active_sidebar('header-advertisement')) : ?>
                        <div class="hgroup-right"> <!-- hgroup right starting from here -->
                          <div class="ads-section">
                            <figure>
                  <?php dynamic_sidebar('header-advertisement'); ?>
                            </figure>
                          </div>
                        </div> <!-- hgroup right ends here -->
              <?php endif; ?>
                  </div>
                  <div class="clear"></div>
                </div>
                <nav id="main-nav" class="fade-in animated2 fixed-enabled">
                  <div class="container">

        <?php
        wp_multisite_nav_menu(array('container_class' => 'main-menu', 'theme_location' => 'primary', 'fallback_cb' => 'tie_nav_fallback', 'walker' => new tie_mega_menu_walker()));
        //wp_nav_menu( array( 'container_class' => 'main-menu', 'theme_location' => 'primary' ,'fallback_cb' => 'tie_nav_fallback',  'walker' => new tie_mega_menu_walker()  ) );
        ?>

                  </div>
                </nav>
                <!-- .main-nav /-->
              </header>
			  </div>
              <!-- #header /-->
          <?php
      }

  endif;
  add_action('mag_lite_action_before', 'mag_lite_page_start');

  function wp_multisite_nav_menu($args = array(), $origin_id = 1) {

      global $blog_id;
      $origin_id = absint($origin_id);

      if (!is_multisite() || $origin_id == $blog_id) {
          wp_nav_menu($args);
          return;
      }

      switch_to_blog($origin_id);
      wp_nav_menu($args);
      restore_current_blog();
  }

  if (!function_exists('mag_lite_page_end')) :

      /**
       * Page End.
       *
       * @since 1.0.0
       */
      function mag_lite_page_end() {
          ?>

            </div>
          </div><!-- #page --><?php
      }

  endif;

  add_action('mag_lite_action_after', 'mag_lite_page_end');

  if (!function_exists('mag_lite_content_start')) :

      /**
       * Content Start.
       *
       * @since 1.0.0
       */
      function mag_lite_content_start() {
          ?><div class="page"><?php
    }

endif;
add_action('mag_lite_action_before_content', 'mag_lite_content_start');


if (!function_exists('mag_lite_content_end')) :

    /**
     * Content End.
     *
     * @since 1.0.0
     */
    function mag_lite_content_end() {
        ?>

          </div></div><!-- #content --><?php
      }

  endif;
  add_action('mag_lite_action_after_content', 'mag_lite_content_end');


  if (!function_exists('mag_lite_header_start')) :

      /**
       * Header Start
       *
       * @since 1.0.0
       */
      function mag_lite_header_start() {
          ?><header id="masthead" class="site-header"> <!-- header starting from here --><?php
    }

endif;

add_action('mag_lite_action_before_header', 'mag_lite_header_start', 10);


if (!function_exists('mag_lite_header_end')) :

    /**
     * Header End
     *
     * @since 1.0.0
     */
    function mag_lite_header_end() {
        ?></header><!-- header ends here --><?php
    }

endif;
add_action('mag_lite_action_after_header', 'mag_lite_header_end', 10);

if (!function_exists('mag_lite_footer_start')) :

    /**
     * Footer Start.
     *
     * @since 1.0.0
     */
    function mag_lite_footer_start() {
        ?><strong><footer id="colophon" class="fade-in animated4"> <!-- footer starting from here --> 
        <?php
    }

endif;
add_action('mag_lite_action_before_footer', 'mag_lite_footer_start');


if (!function_exists('mag_lite_footer_end')) :

    /**
     * Footer End.
     *
     * @since 1.0.0
     */
    function mag_lite_footer_end() {
        exit;
        ?>	 	</footer><div class="clear"></div>

        </strong><!-- #colophon --><?php
    }

endif;
add_action('mag_lite_action_after_footer', 'mag_lite_footer_end');

class tie_mega_menu_walker extends Walker_Nav_Menu {

    private $curItem, $megaMenu;

    function tie_mega_start() {
        $sub_class = $last = '';
        $count = 0;

        if ($this->curItem->object == 'category' && empty($this->curItem->menu_item_parent)) {
            $cat_id = $this->curItem->object_id;
            $cat_options = get_blog_option(1, "tie_cat_$cat_id");
            //print_r($cat_id);exit;
            if (!empty($cat_options['cat_mega_menu'])) {
                @$output .= "\n<div class=\"mega-menu-block\"><div class=\"container\"><div class=\"mega-menu-content\">\n";
                $cat_query = new WP_Query('cat=' . $cat_id . '&no_found_rows=1&posts_per_page=3');
                while ($cat_query->have_posts()) {
                    $count++;
                    if ($count == 3)
                        $last = 'last-column';
                    $cat_query->the_post();
                    $output .= '<div class="mega-menu-item ' . $last . '">';
                    if (function_exists("has_post_thumbnail") && has_post_thumbnail())
                        $output .= '<a class="mega-menu-link" href="' . get_permalink() . '" title="' . get_the_title() . '"><img width="300" height="160" src="' . tie_thumb_src('tie-large') . '" /></a>';
                    $output .= '<h3 class="post-box-title"><a class="mega-menu-link" href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3></div>';
                }
                return $output .= "\n</div><!-- .mega-menu-content --> \n";
            }
        }
    }

    function start_lvl(&$output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);
        $output .= $this->tie_mega_start();
        $output .= "\n$indent<ul class=\"sub-menu\">\n";
    }

    function end_lvl(&$output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul> <!--End Sub Menu -->\n";
        if ($this->megaMenu == 'y' && $depth == 0) {
            $output .= "\n</div></div> <!-- .mega-menu-block & container --> \n";
        }
    }

    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        global $wp_query;
        $this->curItem = $item;

        $indent = ( $depth ) ? str_repeat("\t", $depth) : '';
        $class_names = $value = $mega = '';
        $classes = empty($item->classes) ? array() : (array) $item->classes;

        if ($item->object == 'category' && empty($item->menu_item_parent)) {
            $cat_id = $this->curItem->object_id;
            $cat_options = get_blog_option(1, "tie_cat_$cat_id");
            if (!empty($cat_options['cat_mega_menu'])) {
                $this->megaMenu = 'y';
                $mega = 'mega-menu';
                if (empty($args->has_children))
                    $mega .= ' full-mega-menu';
            }
        }

        if (empty($item->menu_item_parent) && empty($mega))
            $this->megaMenu = 'n';

        $class_names = join(" $mega ", apply_filters('nav_menu_css_class', array_filter($classes), $item));
        $class_names = ' class="' . esc_attr($class_names) . '"';

        $output .= $indent . '<li id="menu-item-' . $item->ID . '"' . $value . $class_names . '>';

        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID);
        $item_output .= $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        if (!empty($mega) && empty($args->has_children)) {
            $item_output .= $this->tie_mega_start();
            $item_output .= "\n</div></div> <!-- .mega-menu-block & container --> \n";
        }
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args, $id);
    }

    function display_element($element, &$children_elements, $max_depth, $depth = 0, $args = array(), &$output) {
        $id_field = $this->db_fields['id'];
        if (is_object($args[0])) {
            $args[0]->has_children = !empty($children_elements[$element->$id_field]);
        }
        return parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }

}