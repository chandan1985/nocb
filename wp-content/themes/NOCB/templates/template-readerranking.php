<?php

/* Template Name: Reader Ranking */

get_header();

$date = $post->post_date;
$date = date('F j, Y', strtotime($date));
$author = $post->post_author;
$display_name = get_the_author_meta('display_name', $author);
$author_link = get_author_posts_url($author);
$title = get_the_title(); 
?>



<div class="breadcrumb-section container">
    <a href="/">Home</a><span>></span><?php print $title ?><span>
</div>
<div class="template-page heading-section container-fluid">
    <div class="container">
        <h1 class="page-heading"><?php print $title ?></h1>
        <!--<div class="publish-info">
			<a href="<?php print $author_link;?>"><b><?php print $display_name; ?></b></a><span>//</span><?php print $date; ?>		
		</div> -->
    </div>
</div>

<div class="common-page container">

	<!-- Start row -->
	<div class="row">

			<!-- Start col-sm-9 -->
			<div class="col-sm-9">
			<?php the_content(); ?>
			</div>
			<!-- End col-sm-9 -->

			<!-- Start col-sm-3 -->
			<div class="col-sm-3 desktop-show">
				<?php dynamic_sidebar('reader-ranking-sidebar-area'); ?>
			</div>
			<!-- End col-sm-3 -->

	</div>		
	<!-- End row -->
</div>

<?php get_footer(); ?>

