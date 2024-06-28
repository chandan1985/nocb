<?php if ( have_posts() ) : while ( have_posts()) : the_post(); ?>
        <a href="<?php the_permalink() ?>" class="mod-blog clearfix">
        	<?php $activeimage = get_needed_image("medium-image-hard");?>
        	<div class="mod-blog-label"><?php the_title() ?> </div>
        	<img class="mod-blog-img"  src="<?php echo $activeimage; ?>" />
        	<div class="mod-blog-title"><?php the_excerpt() ?></div>
            <div class="mod-blog-readblog">Take&nbsp;Quiz&nbsp;&rarr;</div>
        </a>    
<?php endwhile; ?>
<?php endif; ?>
<?php wp_reset_query() ?>
<br /><br /><br />