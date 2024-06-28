<?php get_header(); ?>
<div id="content">
	<div class="column_main">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<h2><?php the_title(); ?></h2>
		<?php the_content(); ?>
		<?php edit_post_link('edit this page', '<p><br />', '</p>'); ?>
		<?php endwhile; endif; ?>
	</div>
	<!--googleoff: all-->
	<?php get_sidebar(); ?>
	<!--googleon: all-->
</div>
<?php get_footer(); ?>