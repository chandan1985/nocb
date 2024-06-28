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
$term_slug = $term->slug;
$parent_id = $term->parent;
$parentCatList = get_term($parent_id);
$parent_name = $parentCatList->name;
$parent_permalink = get_category_link($parentCatList);

$catObj = get_category_by_slug($category_slug);
$category_name = $catObj->name;
$termObj = get_queried_object();
$category_slug = $termObj->slug;

$power_list_page = $_SERVER['REQUEST_URI'];

// data is coming from plugin
$footer_bottom_ad = get_option('footer_bottom_ad');
$mobile_footer_bottom_ad = get_option('mobile_footer_bottom_ad');
$footer_hide_from_pages = [];
$footer_hide_from_post_type = [];
$footer_hide_from_pages[] = get_option('footer_hide_from_pages');
$footer_hide_from_post_type[] = get_option('footer_hide_from_post_type');
global $post;
$current_post_type = get_post_type($post);
// print "<pre>"; print_r($footer_hide_from_pages); print "</pre>";
?>

<div class='breadcrumb-section container'>
	<?php

	if($power_list_page == "/power-lists/"){
		print "<a href='/'>Home</a><span>></span><a href='/power-lists'>Power lists</a>";
	}elseif($parent_id){ ?>
		<a href="/">Home</a><span>></span><a href="<?php print $parent_permalink; ?>"><?php print $parent_name; ?></a><span>></span><span class="name"><?php print $category_name; ?></span>
	<?php } else { ?>
		<a href="/">Home</a><span>></span><span class="name"><?php print $category_name; ?></span>
	<?php } ?>
</div>

<div class='heading-section container-fluid'>
    <div class='container'>
        <h1 class='page-heading'><?php print $category_name;?></h1>
    </div>
</div>

<?php if($term_slug == "executive-moves"){ ?>
    <div class='intro-section container-fluid'>
            <p  class='container'><b class='intro-heading'>Welcome to our Executive Moves section. To submit your info for the page, <a href="/submit-an-executive-moves/"><u>click here.</u></a><br><a href="/faq-executive-moves/"><u>FAQ</u></a></b></p>
    </div>
<?php }?>

<main class='categorised-content-listing container'>

    <!-- Start row -->
    <div class='row'>
        <!-- Start col-sm-9 -->
        <div class='col-sm-9'>
            <?php
                if(wp_is_mobile() && !wp_is_ipad()){
                    require get_template_directory() . '/category-mobile.php';
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
                        $content = preg_replace('/(https?:\/\/[^\s]+)/i', '', $content);
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
                        <?php 
                          if( wp_is_ipad()){
                            $key === 3 ? "<div class='content-inline-ad-section'>".dynamic_sidebar('category-inline-mobile-ad-row1')."</div>" : null ;
                            $key === 7 ? "<div class='content-inline-ad-section'>".dynamic_sidebar('category-inline-mobile-ad-row2')."</div>" : null ;
                        }
                           else{
                            $key === 3 ? "<div class='content-inline-ad-section'>".dynamic_sidebar('content-inline-ad-1')."</div>" : null ;
                            $key === 7 ? "<div class='content-inline-ad-section'>".dynamic_sidebar('content-inline-ad-2')."</div>" : null ;
                           }            
 
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
            <?php						
               $url =  home_url( $wp->request );
               $values = parse_url($url);
               $path = explode('/',$values['path']);
               $central_penn = array_search("central-penn-parent",$path);
                if($central_penn){
                    dynamic_sidebar('central-penn-parent-right-sidebar');
                }else{
                    dynamic_sidebar('right-sidebar-area');
                }
            ?>
        </div>
        <!-- End col-sm-3 -->

    </div>
    <!-- End row -->

	<?php if(!in_array($current_post_type, $footer_hide_from_post_type) && !in_array(get_the_ID(), $footer_hide_from_pages)){ ?>
			<div class="row">
				<div class="col-sm-9">
					<div class='footer-bottom-ad-section'>
					<?php
						if(wp_is_mobile() && !wp_is_ipad()){
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

</main>


<?php get_footer();
