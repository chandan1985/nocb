<?php

/* Template Name: Sponsored listing */

get_header();

$termObj = get_queried_object();
$category_slug = $termObj->slug;

$term = get_term_by('slug', $category_slug, 'category');
$term_id = $term->term_id;
// print $termObj;
$parent_id = $term->parent;
$parentCatList = get_term($parent_id);
$parent_name = $parentCatList->name;
$parent_permalink = get_category_link($parentCatList);

$catObj = get_category_by_slug($category_slug);
$category_name = $catObj->name;
$termObj = get_queried_object();
$category_slug = $termObj->slug;

// data is coming from plugin
$footer_bottom_ad = get_option('footer_bottom_ad');
$mobile_footer_bottom_ad = get_option('mobile_footer_bottom_ad');
$footer_hide_from_pages = [];
$footer_hide_from_pages[] = get_option('footer_hide_from_pages');
$footer_hide_from_post_type = [];
$footer_hide_from_post_type[] = get_option('footer_hide_from_post_type');
global $post;
$current_post_type = get_post_type($post);

?>

<div class='breadcrumb-section container'>
    <?php asentech_custom_breadcrumb(); ?>
</div>

<div class='heading-section container-fluid'>
    <div class='container'>
        <h1 class='page-heading'>Sponsored Contents</h1>
    </div>
</div>



<div class="sponsored-content-listing container">

	<!-- Start row -->
	<div class="row">

			<!-- Start col-sm-9 -->
			<div class="col-sm-9">
				<?php
				if(wp_is_mobile()){
                    require get_template_directory() . '/sponsored-content-mobile.php';
                }else{ 
					require get_template_directory() . '/sponsored-content.php';
				} ?>
				
			
			</div>
			<!-- End col-sm-9 -->

			<!-- Start col-sm-3 -->
			<div class="col-sm-3 desktop-show">
				<?php dynamic_sidebar('right-sidebar-area'); ?>
			</div>
			<!-- End col-sm-3 -->

	</div>		
	<!-- End row -->

	<?php 
	if(!in_array($current_post_type, $footer_hide_from_post_type) && !in_array(get_the_ID(), $footer_hide_from_pages)){ ?>
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

<?php get_footer(); ?>