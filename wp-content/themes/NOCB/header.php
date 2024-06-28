<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package nocb
 */

$btm_logo = get_option('btm_logo');
$selected_region_and_brand_menu = get_option('selected_region_and_brand_menu');
$selected_region_and_brand_menu = array(
	'menu' => $selected_region_and_brand_menu,
	'menu_class' => 'menu',
);
$username = get_option('username');
$account_menu = get_option('account_menu');
$account_menu = array(
	'menu' => $account_menu,
	'menu_class' => 'menu',
);
$signin_box = get_option('signin_box');
$signout_box = get_option('signout_box');
$site_logo = get_option('site_logo');
$search_icon = get_option('search_icon');
$upcoming_event = get_option('upcoming_event');
$newsletter_and_subscribe = get_option('newsletter_and_subscribe');
$mobile_newsletter_and_subscribe = get_option('mobile_newsletter_and_subscribe');
$popout_html_box = get_option('popout_html_box');
$selected_popup_menu = get_option('selected_popup_menu');
$selected_popup_menu = array(
	'menu' => $selected_popup_menu,
	'menu_class' => 'menu',
);
$selected_menu = get_option('selected_menu');
$selected_menu = array(
	'menu' => $selected_menu,
	'menu_class' => 'menu',
);
$search_html_box = get_option('search_html_box');
$selected_mobile_popup_menu = get_option('selected_mobile_popup_menu');
$selected_mobile_popup_menu = array(
	'menu' => $selected_mobile_popup_menu,
	'menu_class' => 'menu',
);
$header_bottom_ad = get_option('header_bottom_ad');
$header_bottom_ad_mobile = get_option('mobile_header_bottom_ad');
// $hide_from_pages = [];
$hide_from_pages = get_option('hide_from_pages');
// $hide_from_posts = [];
$hide_from_posts = get_option('hide_from_post_type');
$welcome_ad = get_option('welcome_ad');
$hide_welcome_ad = [];
$hide_welcome_ad[] = get_option('hide_welcome_ad');
global $post;
$current_post_type = get_post_type($post);

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
    <?php
        $header_js = get_option('custom_js_for_header');
        print html_entity_decode($header_js, ENT_QUOTES);
    ?>
</head>

<body <?php body_class(); echo "stringg".get_current_blog_id(); ?>>
    <?php do_action( 'after_body' ); ?>
    <?php wp_body_open(); ?>

    <?php

if($_REQUEST['tpi'] == "login"){
	$display_none = "d-none";
}else{
	$display_none = "";
}

$current_path = $_SERVER['REQUEST_URI'];
$path_array = explode('/', $current_path);

if($current_path == "/subscribe/" || $current_path == "/subscribe-2/" || in_array('print-digital', $path_array) || $current_path == "/thank-you/" || $current_path == "/thank-you-subscribe/" ){
	$hide_section = "d-none";
	$dynamic_padding = "padding: 1rem 0px;";
}else{
	$hide_section = "";
	$dynamic_padding = "";
}

?>

    <div id="page" class="site">

        <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'nocb' ); ?></a>



        <?php if(wp_is_mobile() && ! wp_is_ipad()){ ?>
        <!-- Mobile header start -->
        <div class="mobile-whole-header">
            <div class="mobile-top-bar">
                <div class="select-region-or-brand-menu">
                    <h2 class="widget-title">Select Region or Brand</h2>
                    <?php wp_nav_menu($selected_region_and_brand_menu); ?>
                </div>
                <div class="widget sign_in">
                    <?php print $signin_box; ?>
                </div>
                <div class="widget sign_out">
                    <?php print do_shortcode($signout_box);  ?>
                </div>
            </div>
            <div class="mobile-header-top">
                <div class="mobile-date-menu-section">
                    <?php print $popout_html_box; ?>
                </div>
                <div class="site-logo">
                    <a href="/">
                        <img src="<?php print $site_logo; ?>" alt>
                    </a>
                </div>
                <div class="search-icon">
                    <a href="/?s=">
						<img src="<?php print $search_icon; ?>" alt>
                    </a>
                </div>
            </div>

            <div class="mobile-header-bottom">
                <div class="container">
                    <?php print $mobile_newsletter_and_subscribe; ?>
                </div>
            </div>

            <?php if ( $header_bottom_ad ) : ?>
            <div class="mobile-header-bottom-adboard-area">
                <div class="container">
                    <?php
                        // print do_shortcode($header_bottom_ad_mobile); 
                        // if(!in_array($current_post_type, $hide_from_posts) && !in_array(get_the_ID(), $hide_from_pages)){
                        //     print do_shortcode($header_bottom_ad); 
                        // }
                        // if(!in_array(get_the_ID(), $hide_welcome_ad)){
                        //     print do_shortcode($welcome_ad); 
                        // }

                        if(is_array($hide_from_posts) == '1' && is_single()){
                            if(!in_array($current_post_type, $hide_from_posts)){
                                print do_shortcode($header_bottom_ad); 
                            }
                        }elseif(is_array($hide_from_pages) == '1' && is_page()){
                            if(!in_array(get_the_ID(), $hide_from_pages)){
                                print do_shortcode($header_bottom_ad); 
                            }
                        }else{
                            print do_shortcode($header_bottom_ad); 
                        }

                        if(!in_array(get_the_ID(), $hide_welcome_ad)){
                            print do_shortcode($welcome_ad); 
                        }
                    ?>

                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="mobile-pop-out-menu">
            <div class="mobile-pop-out-menu-area">
                <?php print $search_html_box; ?>
                <?php wp_nav_menu($selected_mobile_popup_menu); ?>
                <?php dynamic_sidebar('mobile-pop-out-menu-area'); ?>
            </div>
        </div>
        <!-- Mobile header end -->
        <?php } else{ ?>
        <!-- Desktop header start -->
        <div class="whole-header">

            <div class="top-billboard-area <?php print $hide_section; ?>">
                <div class="container"><?php dynamic_sidebar('top-billboard'); ?></div>
            </div>

            <header id="masthead" class="site-header">
                <div class="btm-header">
                    <div class="container">
                        <div class="row btm-header-section">
                            <div class="btm-header-left col-sm-6">
                                <img src="<?php print $btm_logo; ?>" alt>
                                <div class="select-region-or-brand-menu">
                                    <h2>Select Region or Brand</h2>
                                    <?php wp_nav_menu($selected_region_and_brand_menu); ?>
                                </div>
                            </div>
                            <div class="btm-header-right col-sm-6">
                                <div class="username-class">
                                    <?php print do_shortcode($username); ?>
                                </div>
                                <div class="manage-account">
                                    <?php wp_nav_menu($account_menu); ?>
                                </div>
                                <div class="widget sign_in">
                                    <?php print $signin_box; ?>
                                </div>
                                <div class="widget sign_out">
                                    <?php print do_shortcode($signout_box);  ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container main-header">
                    <div class="header-top-area">
                        <div class="row">
                            <div class="header-top-left col-sm-9" style="<?php print $dynamic_padding; ?>">
                                <a href="/">
                                    <img src="<?php print $site_logo; ?>" alt>
                                </a>
                                <div class="header-top-middle <?php print $hide_section; ?>">
                                    <?php print do_shortcode($upcoming_event); ?>
                                </div>
                            </div>
                            <div class="header-top-right col-sm-3 <?php print $hide_section; ?>">
                                <?php print $newsletter_and_subscribe; ?>
                            </div>
                        </div>
                    </div>
                    <div class="header-bottom-area <?php print $hide_section; ?>">
                        <div class="row">
                            <div class="header-bottom-left col-sm-10">
                                <div class="date-menu-section">
                                    <?php print $popout_html_box; ?>
                                </div>
                                <?php wp_nav_menu($selected_menu); ?>
                            </div>
                            <div class="header-bottom-right col-sm-2"><?php print $search_html_box; ?></div>
                        </div>
                    </div>
                </div>
        </div>

        </header><!-- #masthead -->

        <?php if ( $header_bottom_ad ) : ?>
        <?php
					$event_id = get_the_ID();
					$header_ad_board = get_post_meta( $event_id, 'header_adboard', true );
					// print  get_post_type( $event_id);
					if(get_post_type( $event_id) == 'tribe_events'  && is_single()){
						if ( $header_ad_board == 'Yes' ) {
							?>
        <div class="header-bottom-adboard-area <?php print $hide_leaderboard; ?>">
            <div class="container">
                <?php
                    // if(!in_array($current_post_type, $hide_from_posts) && !in_array(get_the_ID(), $hide_from_pages)){
                    //     print do_shortcode($header_bottom_ad);
                    // }
                    // if(!in_array(get_the_ID(), $hide_welcome_ad)){
                    //     print do_shortcode($welcome_ad);
                    // }

                    if(is_array($hide_from_posts) == '1' && is_single()){
                        if(!in_array($current_post_type, $hide_from_posts)){
                            print do_shortcode($header_bottom_ad); 
                        }
                    }elseif(is_array($hide_from_pages) == '1' && is_page()){
                        if(!in_array(get_the_ID(), $hide_from_pages)){
                            print do_shortcode($header_bottom_ad); 
                        }
                    }else{
                        print do_shortcode($header_bottom_ad); 
                    }

                    if(!in_array(get_the_ID(), $hide_welcome_ad)){
                        print do_shortcode($welcome_ad); 
                        // print "sdfghsdrfkdbfvdsf";
                    }

                ?>
            </div>
        </div>
        <?php
						}
					}else{
						?>
        <div class="header-bottom-adboard-area <?php print $hide_leaderboard; ?>">
            <div class="container">
                <?php 
                    // if(!in_array($current_post_type, $hide_from_posts) && !in_array(get_the_ID(), $hide_from_pages)){
                    //     print do_shortcode($header_bottom_ad); 
                    // }
                    // if(!in_array(get_the_ID(), $hide_welcome_ad)){
                    //     print do_shortcode($welcome_ad); 
                    // }

                    if(is_array($hide_from_posts) == '1' && is_single()){
                        if(!in_array($current_post_type, $hide_from_posts)){
                            print do_shortcode($header_bottom_ad); 
                        }
                    }elseif(is_array($hide_from_pages) == '1' && is_page()){
                        if(!in_array(get_the_ID(), $hide_from_pages)){
                            print do_shortcode($header_bottom_ad); 
                        }
                    }else{
                        print do_shortcode($header_bottom_ad); 
                    }

                    if(!in_array(get_the_ID(), $hide_welcome_ad)){
                        print do_shortcode($welcome_ad); 
                    }

                ?>
            </div>
        </div>
        <?php
					}
				?>
        <?php endif; ?>



    </div>
    <div class="pop-out-menu-area" id="pop-out-menu-region">
        <div class="pop-out-menu"><div class="widget_nav_menu"><?php wp_nav_menu($selected_popup_menu); ?></div></div>
    </div>
    <!-- Desktop header end -->
    <?php } 

?>