<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<a href="<?php the_permalink() ?>" class="dig-edi-title"><?php the_title() ?></a>
<?php endwhile; ?>
<?php endif; ?>
<?php wp_reset_query(); ?>