<?php
/*
Template Name: Cache Page
*/
?>

<!-- code for no cache -->
<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!-- code for no cache -->

<?php get_header(); ?>

<div id="primary" class="site-content">
    <div id="content" role="main">

           <?php while (have_posts()) : the_post(); ?> <!--Because the_content() works only inside a WP Loop -->
               <div class="form-content-page" >
				
				<h1 class="subpage-title"><?php the_title(); ?></h1>
				<hr style="border: 1px solid #bbb">
				
                <?php the_content(); ?> <!-- Page Content -->
                </div><!-- .entry-content-page -->

			    <?php
			endwhile; //resetting the page loop
			wp_reset_query(); //resetting the page query
			?>

    </div><!-- #content -->
  </div><!-- #primary -->

<?php get_footer(); ?>
