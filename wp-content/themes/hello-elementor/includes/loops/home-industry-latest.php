<?php $indcounter = 1; ?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<div class="industry-latest-num"><?php echo $indcounter; ?></div>
    <div class="industry-latest-story">
        <a class="industry-latest-title" href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a>
        <p><?php echo get_the_excerpt(); ?>...</p>
    </div>
    <a class="industry-latest-arrow fa fa-caret-right" href="<?php echo get_the_permalink(); ?>"></a>
	<div class="industry-divider"></div>
    <?php $indcounter++; ?>
<?php $do_not_duplicate[] = $post->ID; ?>
<?php endwhile; ?>
<?php endif; ?>
<?php wp_reset_query() ?>