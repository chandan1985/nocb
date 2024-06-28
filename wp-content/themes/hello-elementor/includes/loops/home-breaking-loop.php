<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<div class="hp-breaking">
        <div class="hp-breaking-bar"><a class="industry-latest-title" href="<?php echo get_the_permalink(); ?>"><b>Breaking News: </b><?php echo get_the_title(); ?>&nbsp;&nbsp;&nbsp;</a>
        </div>
    </div>
<?php endwhile; ?>
<?php endif; ?>
<?php wp_reset_query() ?>