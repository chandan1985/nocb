<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package neworleanscitybusiness
 */
// data is coming from plugin
$footer_bottom_ad = get_option('footer_bottom_ad');
$mobile_footer_bottom_ad = get_option('mobile_footer_bottom_ad');
$footer_hide_from_pages = get_option('footer_hide_from_pages');
$footer_hide_from_post_type = get_option('footer_hide_from_post_type');
global $post;
$current_post_type = get_post_type($post);
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <!-- Start row -->
    <div class="row">
        <?php if(!is_active_sidebar('right-sidebar-area')){ ?>
        <div class="col-sm-12">
            <?php } else{?>
            <!-- Start col-sm-9 -->
            <div class="col-sm-9">
                <?php } ?>
                <div class="entry-content">
                    <?php
						the_content();
						wp_link_pages(
							array(
								'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'neworleanscitybusiness' ),
								'after'  => '</div>',
							)
						);
						?>
                </div><!-- .entry-content -->
                <?php dynamic_sidebar('content-bottom'); ?>

                <?php // If comments are open or we have at least one comment, load up the comment template.
					$current_url = $_SERVER['REQUEST_URI'];
					$current_url =  str_replace("/","",$current_url);
					
						if ($current_url == 'about'){
						?>
                <div class='comment-section'>
                    <?php }	else{ ?>
                    <div class='comment-section d-none'>
                        <?php } ?>

                        <?php
							if ( comments_open() || get_comments_number() ) :
								comments_template();
							endif; 
							?>
                    </div>
                </div>
                <!-- End col-sm-9 -->
                <!-- Start col-sm-3 -->
                <div class="col-sm-3">
                    <?php  dynamic_sidebar('right-sidebar-area'); ?>
                </div>
                <!-- End col-sm-3 -->
                <?php if (isset($footer_hide_from_post_type) && is_array($footer_hide_from_post_type) &&
          isset($footer_hide_from_pages) && is_array($footer_hide_from_pages) &&
          !empty($footer_hide_from_post_type) && !empty($footer_hide_from_pages) &&
          !in_array($current_post_type, $footer_hide_from_post_type) &&
          !in_array(get_the_ID(), $footer_hide_from_pages)){ ?>
                <div class="row">
                    <div class="col-sm-9">
                        <div class='footer-bottom-ad-section'>
                            <?php
							if(wp_is_mobile()){
								print do_shortcode($mobile_footer_bottom_ad); 
							}else{
								print do_shortcode($footer_bottom_ad); 
							} 
						?>
                        </div>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
                <?php } ?>
            </div>
            <!-- End row -->
</article><!-- #post-<?php the_ID(); ?> -->