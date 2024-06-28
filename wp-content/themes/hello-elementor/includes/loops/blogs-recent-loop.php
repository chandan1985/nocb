<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<a class="blog-recent-links" href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br />
<?php endwhile; ?>
<?php else: ?>
	<p>&nbsp;</p>
	<p>No Recent Posts are Available</p>
<?php endif; ?>
<?php wp_reset_query(); ?>