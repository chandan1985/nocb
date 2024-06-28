<?php $counter = 0 ?>
<?php if ($categoryCSS == 'contests'){$linktext = "Enter&nbsp;Contest&nbsp;&rarr;";}elseif($categoryCSS == 'videos'){$linktext = "Watch&nbsp;Video&nbsp;&rarr;";}else{$linktext = "Read&nbsp;More&nbsp;&rarr;";} ?>
<?php if ( have_posts() ) : while ( have_posts() && $counter < 1) : the_post(); ?>
<?php $currpostid = $post->ID ?>
<?php if (!in_array($currpostid, $do_not_duplicate, true)) {?>
        
        <a href="<?php the_permalink(); ?>" class="landing-sub-image bor-defaultcat bor-<?php echo $categoryCSS; ?>">
        	<?php $activeimage = get_needed_image("medium-image-hard");?>
            <?php if ($activeimage != ""){ ?>
            	<img src="<?php echo $activeimage; ?>" />
            <?php }
			else { ?>
            	<img src="/wp-content/themes/PetAge/images/category-fallback/fallback-<?php echo $categoryCSS; ?>.jpg" />
            <?php }; ?>
        </a>
        <a href="<?php the_permalink(); ?>" class="landing-sub-title"><?php the_title(); ?></a>
        <p class="landing-sub-summary"><?php $excerpt = get_the_excerpt(); echo $excerpt; ?><a class="read-more-link" href="<?php the_permalink(); ?>"> <?php echo $linktext; ?></a></p>
    	<?php $do_not_duplicate[] = $post->ID; ?>
        <?php $counter++; ?>
        
<?php } ?>    
<?php endwhile; ?>
<?php $page = get_query_var('paged'); ?>
<?php endif; ?>