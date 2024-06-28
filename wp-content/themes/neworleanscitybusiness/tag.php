<?php

/**
 * Template part for displaying page content in category.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package furnituretoday
 */
get_header();

$termObj = get_queried_object();
$category_slug = $termObj->slug;

$term = get_term_by('slug', $category_slug, 'category');
$term_id = $term->term_id;
$parent_id = $term->parent;
$parentCatList = get_term($parent_id);
$parent_name = $parentCatList->name;
$parent_permalink = get_category_link($parentCatList);

$catObj = get_category_by_slug($category_slug);
$category_name = $catObj->name;
$termObj = get_queried_object();
$category_slug = $termObj->slug;


?>

<div class='breadcrumb-section container'>
    <?php asentech_custom_breadcrumb(); ?>
</div>

<div class='heading-section container-fluid'>
    <div class='container'>
        <h1 class='page-heading'><?php print $category_name;?></h1>
    </div>
</div>

<main class='categorised-content-listing container'>

    <!-- Start row -->
    <div class='row'>
        <!-- Start col-sm-9 -->
        <div class='col-sm-9'>
            <?php
                if(wp_is_mobile()){
                    require get_template_directory() . '/tag-mobile.php';
                }else{

                    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                    $start = ($paged - 1) * 12;

                    // print $start;

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
                            'post' => 'post',
                            'sponsored_content' => 'sponsored_content',
                        ),
                        'tax_query' =>
                        array(
                            'taxonomy_category' =>
                            array(
                                'taxonomy' => 'category',
                                'field' => 'id',
                                'terms' => $term_id,
                                'operator' => 'IN',
                                'include_children' => false,
                            ),
                        ),
                    );

                    $wp_query = new WP_Query($aboutPosts);
                    $postsbyid = get_posts($aboutPosts);

                    $article_id = [];
                    foreach ($postsbyid as $key => $post_id) {
                        $id = $post_id->ID;
                        $post_type = $post_id->post_type;
                        $title = get_the_title($id);
                        $Short_title = substr($title, 0, 80) . ' [...]';
                        $slug = get_permalink($id);
                        // $post_date = get_the_date('h:i A', $id);
                        // $post_time = get_the_time('g:i a T', $id);
                        $date = $post_id->post_date;
                        $post_date = date('M j, Y', strtotime($date));
                        $post_time = date('g:i a T', strtotime($date));
                        $featured_image = get_the_post_thumbnail($post_id->ID, 'category-list-thumb');
                        $content_post = get_post($id);
                        $content = $content_post->post_content;
                        $content = strip_tags($content);
                        $content = preg_replace('/<iframe.*?\/iframe>/i','', $content);
                        $short_content = substr($content, 0, 400)."[...]";
                        $short_content_mobile = substr($content, 0, 120)."[...]";

                        $excerpt = get_the_excerpt($id);
                        $short_excerpt = substr($excerpt, 0, 400)."[...]";
                        $short_excerpt_mobile = substr($excerpt, 0, 120)."[...]";

                        $sponsored_cmpnyId = get_post_meta($id, 'associated_sponsor_id', true);
                        $cmpny_link = get_permalink( $sponsored_cmpnyId);
                        $option_arrays = get_option('DFP_Ads_Settings');
                        $option_array = $option_arrays['dfp_property_code'];

                    ?>
                        <div class='article-listing'>
                            <?php if ($featured_image) {
                            ?>
                                <div class='article-detail-listing'>
                                    <div class='image-section'>
                                        <a href="<?php print $slug; ?>"><?php print $featured_image; ?></a>
                                        <?php
                                            if( $post_type == "sponsored_content"){ ?>
                                                    <p class="btn-sponsored">Sponsored</p>
                                                <?php
                                            }
                                        ?>
                                    </div>
                                    <div class='content-section'>
                                        <span><?php echo $post_date;?></span>
                                        <h2><a href="<?php print $slug; ?>"><?php echo $title; ?></a></h2>
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
                                <?php
                                    if( $post_type == "sponsored_content"){ ?>
                                            <p class="btn-sponsored">Sponsored</p>
                                        <?php
                                    }
                                ?>
                                <span><?php echo $post_date; ?></span>
                                <h2><a href="<?php print $slug; ?>"><?php echo $title;?></a></h2>
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
        <div class='col-sm-3'>
            <?php dynamic_sidebar('category-sidebar-area');?>
        </div>
        <!-- End col-sm-3 -->

    </div>
    <!-- End row -->

	<div class="row">
			<div class="col-sm-9"><div class='footer-bottom-ad-section'><?php dynamic_sidebar('footer-adboard');  ?></div></div>
			<div class="col-sm-3"></div>
	</div>

</main>


<?php get_footer();
