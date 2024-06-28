<?php

/* Template Name: Common */

get_header();

$date = $post->post_date;
$date = date('F j, Y', strtotime($date));
$author = $post->post_author;
$display_name = get_the_author_meta('display_name', $author);
$author_link = get_author_posts_url($author);
$title = get_the_title(); 

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

<div class="breadcrumb-section container">
    <a href="/">Home</a><span>></span><?php print $title ?><span>
</div>
<div class="template-page heading-section container-fluid">
    <div class="container">
        <h1 class="page-heading"><?php print $title ?></h1>
        <div class="publish-info">
			<a href="<?php print $author_link;?>"><b><?php print $display_name; ?></b></a><span>//</span><?php print $date; ?>		
		</div>
    </div>
</div>

<div class="common-page container">

	<!-- Start row -->
	<div class="row">

			<!-- Start col-sm-9 -->
			<div class="col-sm-9">
			<?php the_content(); ?>.


			<?php if(!in_array($current_post_type, $footer_hide_from_post_type) && !in_array(get_the_ID(), $footer_hide_from_pages)){ ?>
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
			<!-- End col-sm-9 -->

			<!-- Start col-sm-3 -->
			<div class="col-sm-3 desktop-show" >
				<?php dynamic_sidebar('right-sidebar-area'); ?>
			</div>
			<!-- End col-sm-3 -->

	</div>		
	<!-- End row -->
</div>

<?php get_footer(); ?>

