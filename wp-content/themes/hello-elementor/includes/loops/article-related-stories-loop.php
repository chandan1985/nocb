<?php if ( have_posts() ) : while ( have_posts()) : the_post(); ?>
		<div class="art-related-story">
        	<?php $activeimage = get_needed_image("medium-image-hard");?>
        	<a href="<?php the_permalink(); ?>"><img src="<?php echo $activeimage; ?>" class="art-related-image" /></a>
            <a href="<?php the_permalink(); ?>" class="art-related-title"><?php the_title(); ?></a>
            <span class="art-related-byline"><?php the_author(); ?></span>
        </div>
	<?php $do_not_duplicate[] = $post->ID; ?>
<?php endwhile; ?>
<?php endif; ?>
<?php wp_reset_query() ?>