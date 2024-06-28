<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<a class="nav-box-featured-image" href="<?php the_permalink() ?>">
        <?php 
		$activeimage = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), $size = 'medium-image');
		$activeimage = $activeimage[0];
		?>
        <img class="feat-image" src="<?php echo $activeimage; ?>" />
    </a>
    <a class="nav-box-featured-title" href="<?php the_permalink() ?>" class="nav-box-featured-title">
		<?php if( get_post_type() == 'digitaledition'){echo'Read: ';}; the_title(); ?>
    </a>        
<?php endwhile; ?>
<?php endif; ?>
<?php wp_reset_query() ?>