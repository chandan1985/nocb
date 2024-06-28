<?php

/**
 * Template Name: DUMMY - Author
 *
 * @package FurnitureToday
 */
get_header();

$author_id = get_the_author_meta("ID");
$first_name = get_the_author_meta( 'first_name' , $author_id );
$last_name = get_the_author_meta( 'last_name' , $author_id );
$display_name = get_the_author_meta( 'display_name' , $author_id );
$roles = get_the_author_meta( 'roles' , $author_id );
$profile_photo = get_avatar_url ($author_id);
$user = get_userdata( $author_id );
$user_email = $user->user_email;
if (trim($first_name) != '' && trim($last_name) != '') {
    $author_name = $first_name . ' ' . $last_name;
} else {
    $author_name =  $display_name;
}
$description = get_user_meta($author_id, 'description', true) ;
// data is coming from plugin
$footer_bottom_ad = get_option('footer_bottom_ad');
$mobile_footer_bottom_ad = get_option('mobile_footer_bottom_ad');
$footer_hide_from_pages = get_option('footer_hide_from_pages');
$footer_hide_from_post_type = get_option('footer_hide_from_post_type');
global $post;
$current_post_type = get_post_type($post);
?>

<div class="breadcrumb-section container">
    <?php asentech_custom_breadcrumb(); ?>
</div>
<div class="heading-section container-fluid">
    <div class="container">
        <div class="author-description ">
            <div class="author-image ">
                <img alt="" src="<?php print $profile_photo; ?> " class="aspect-ratio__element">
            </div>
            <div class="author-details ">
                <h3><?php print $author_name; ?><span class="author-header__separator h2">//</span><span
                        class="author-header__role"><?php print ($roles[0]); ?></span></h3>
                <a href="mailto:<?php echo $user_email; ?>" class="author-header__link"><?php echo $user_email; ?></a>
                <div class="post-content"> <?php print $description; ?> </div>
            </div>

        </div>

    </div>
</div>


<main class='author-content-listing container'>

    <!-- Start row -->
    <div class='row'>
        <!-- Start col-sm-9 -->
        <div class='col-sm-9'>
            <?php
            if(wp_is_mobile() && !wp_is_ipad()){
                ?>
            <div class="section-title">
                <h2 class="river-heading section-title__heading">Recent Articles from <?php print $author_name; ?></h2>
            </div>
            <?php
                require get_template_directory() . '/author-mobile.php';
            }else{
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                $start = ($paged - 1) * 8;

                $aboutPosts = array(
                    'paged' => $paged,
                    'posts_per_page' => '12',
                    'offset' => $start,
                    'post_status' => 'publish',
                    'ignore_sticky_posts' => 0,
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'post_type' =>
                    array(
                        'sponsored_content' => 'sponsored_content',
                        'post' => 'post',
                    ),
                    'author'  => $author_id, 
                );

                // $wp_query = new WP_Query($aboutPosts);
                $postsbyid = get_posts($aboutPosts);
                ?>

            <div class="section-title">
                <h2 class="river-heading section-title__heading">Recent Articles from <?php print $author_name; ?></h2>
            </div>

            <?php
                $article_id = [];
                foreach ($postsbyid as $key => $post_id) {
                    $id = $post_id->ID;
                    $title = get_the_title($id);
                    $Short_title = substr($title, 0, 80) . ' [...]';
                    $slug = get_permalink($id);
                    // $post_date = get_the_date('h:i A', $id);
                    // $post_time = get_the_time('g:i a T', $id);
                    $date = $post_id->post_date;
                    $post_date = date('F j, Y', strtotime($date));
                    $post_time = date('g:i a T', strtotime($date));
                    $featured_image = get_the_post_thumbnail($post_id->ID, 'webinar-list-thumb');
                    $content_post = get_post($id);
                    $content = $content_post->post_content;
                    $content = strip_tags($content);
                    $short_content = substr($content, 0, 300)."[...]";
                    $short_content_mobile = substr($content, 0, 180)."[...]";

                    $excerpt = get_the_excerpt($id);
                    $short_excerpt = substr($excerpt, 0, 400)."[...]";
                    $short_excerpt_mobile = substr($excerpt, 0, 120)."[...]";

                    $wpseo_primary_term = new WPSEO_Primary_Term( 'category', $id );
                    $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
                    $term = get_term( $wpseo_primary_term );
                    $term_name = $term->name;
                    $term_link = get_category_link($term);

                    ?>
            <div class='article-listing'>
                <?php if ($featured_image) {
                        ?>
                <div class='article-detail-listing'>
                    <div class='image-section'>
                        <a href="<?php print $slug; ?>"><?php print $featured_image; ?></a>
                    </div>
                    <div class='content-section'>
                        <div class="content-time-data">
                            <?php
                                            if($term_name){
                                                ?>
                            <a href="<?php print $term_link; ?>" class="btn-custom"><?php print $term_name; ?></a>
                            <?php
                                            }else{
                                                ?>
                            <a href="/blog/category/news" class="btn-custom">News</a>
                            <?php
                                            }
                                        ?>
                            <span><?php echo $post_date;?></span>
                        </div>
                        <h2 class="desktop-show"><a href="<?php print $slug; ?>"><?php echo $title; ?></a></h2>
                        <p class="article-content desktop-show">
                            <?php
                                            if($excerpt){
                                                if(strlen($excerpt) > 400){
                                                    print $short_excerpt;
                                                }else{
                                                    print $excerpt;
                                                }
                                            }else{
                                                if(strlen($content) > 400){
                                                    print $short_content;
                                                }else{
                                                    print $content;
                                                }
                                            }
                                        ?>
                        </p>
                    </div>
                </div>
                <?php } else {
                        ?>
                <div class='content-section'>
                    <div class="content-time-data">
                        <?php
                                    if($term_name){
                                        ?>
                        <a href="<?php print $term_link; ?>" class="btn-custom"><?php print $term_name; ?></a>
                        <?php
                                    }else{
                                        ?>
                        <a href="/blog/category/news" class="btn-custom">News</a>
                        <?php
                                    }
                                ?>

                        <span><?php echo $post_date;?></span>
                    </div>
                    <h2 class="desktop-show"><a href="<?php print $slug; ?>"><?php echo $title; ?></a></h2>
                    <p class="article-content desktop-show">
                        <?php
                                    if($excerpt){
                                        if(strlen($excerpt) > 400){
                                            print $short_excerpt;
                                        }else{
                                            print $excerpt;
                                        }
                                    }else{
                                        if(strlen($content) > 400){
                                            print $short_content;
                                        }else{
                                            print $content;
                                        }
                                    }
                                ?>
                    </p>
                </div>
                <?php }
                        ?>

            </div>
            <?php $key === 3 ? "<div class='content-inline-ad-section'>".dynamic_sidebar('content-inline-ad-1')."</div>" : null ?>
            <?php $key === 7 ? "<div class='content-inline-ad-section'>".dynamic_sidebar('content-inline-ad-2')."</div>" : null ?>
            <?php
                }
                ?>
            <div class="pagenavi"><?php echo wp_pagenavi();?></div>
            <?php
                    wp_reset_postdata();
            }
        ?>
        </div>
        <!-- End col-sm-9 -->
        <!-- Start col-sm-3 -->
        <div class='col-sm-3 desktop-show'>
            <?php dynamic_sidebar('right-sidebar-area');
            ?>
        </div>
        <!-- End col-sm-3 -->

    </div>
    <!-- End row -->
    <?php
    if (
    !is_array($footer_hide_from_post_type) || !is_array($footer_hide_from_pages) ||
    !in_array($current_post_type, $footer_hide_from_post_type) ||
    !in_array(get_the_ID(), $footer_hide_from_pages)
    ) {
    ?>
        <div class="row">
            <div class="col-sm-9">
                <div class='footer-bottom-ad-section'>
                    <?php
                    if (wp_is_mobile() && !wp_is_ipad()) {
                        echo do_shortcode($mobile_footer_bottom_ad);
                    } else {
                        echo do_shortcode($footer_bottom_ad);
                    }
                    ?>
                </div>
            </div>
            <div class="col-sm-3"></div>
        </div>
    <?php
    }
    ?>

    
</main>

<?php get_footer(); ?>