<?php get_header(); ?>
<?php 
    global $wp_query;
    $term = $wp_query->get_queried_object();
    $taxtitle = $term->name;
?>
<div class="content-wrap">
	<div class="content">
		<?php tie_breadcrumbs() ?>
		
		<div class="page-head">
			<?php if ( have_posts() ) the_post(); ?>
			<h2 class="page-title">
				<?php echo $taxtitle; ?>
			</h2>
			<div class="stripe-line"></div>
		</div>

				
		<?php
		rewind_posts();
		get_template_part( 'loop', 'issues' );	?>
	</div>
	<?php get_sidebar(); ?>
<?php get_footer(); ?>