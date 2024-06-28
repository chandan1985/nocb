<?php $do_not_duplicate[] = ""; ?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<?php $currpostid = $post->ID ?>
<?php if (!in_array($currpostid, $do_not_duplicate, true)) {?>
	<div class="landing-substory dir-list bor-defaultcat bor-recipe clearfix">        
        <a href="<?php the_permalink(); ?>" class="landing-sub-image bor-recipe bor-<?php echo $categoryCSS; ?>">
        	<?php $activeimage = get_needed_image("category-image-hard");?>
            <?php if ($activeimage != ""){ ?>
            	<img class="dir-details-image" src="<?php echo $activeimage; ?>" />
            <?php }
			?>
        </a>
        <a href="<?php the_permalink(); ?>" class="landing-sub-title recipe-sub-title"><?php the_title(); ?></a>
        <p>&nbsp;</p>
        <p class="landing-sub-summary dir-details"><?php $excerpt = get_the_excerpt(); echo $excerpt; ?><a class="read-more-link read-more-link-prod" href="<?php the_permalink(); ?>"> View&nbsp;Webinar&nbsp;&rarr;</a></p>
    	<?php $do_not_duplicate[] = $post->ID; ?>
	</div>
<?php } ?>    
<?php endwhile; ?>
<?php endif; ?>