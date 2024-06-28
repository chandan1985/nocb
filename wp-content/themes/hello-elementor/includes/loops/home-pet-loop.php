<?php if ( have_posts() ) : while ( have_posts()) : the_post(); ?>
	<?php 
	$activeimage = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), $size = 'medium-image');
	$activeimage = $activeimage[0];
	?>
	<a href="<?php echo get_the_permalink(); ?>" class="m-s-image">
        <img class="feat-image" src="<?php echo $activeimage; ?>" />
    </a>
    <a href="<?php echo get_the_permalink(); ?>" class='m-s-category'><?php echo $animcat; ?></a>
    <a href="<?php echo get_the_permalink(); ?>" class="m-s-title"><?php echo get_the_title(); ?></a>
    <span class="m-s-description"><?php echo limit_words(get_the_excerpt(),30); ?>... 
        <a class="travel" href="<?php echo get_the_permalink(); ?>">Read&nbsp;More&nbsp;&rarr;</a>
    </span>
<?php endwhile; ?>
<?php endif; ?>
<?php wp_reset_query() ?>