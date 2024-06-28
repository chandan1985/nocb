<?php $counter = 0 ?>
<?php if ($categoryCSS == 'contests'){$linktext = "Enter&nbsp;Contest&nbsp;&rarr;";}elseif($categoryCSS == 'videos'){$linktext = "Watch&nbsp;Video&nbsp;&rarr;";}else{$linktext = "Read&nbsp;More&nbsp;&rarr;";} ?>
<?php rewind_posts(); ?>
<?php if ( have_posts() ) : while ( have_posts() && $counter < 1) : the_post(); ?>

		<div class="landing-mf-content">
            <span class="landing-mf-label">Featured:</span>
            <a href="<?php the_permalink(); ?>" class="landing-mf-title"><?php the_title(); ?></a>
            <p class="landing-mf-summary"><?php $excerpt = get_the_excerpt(); echo $excerpt; ?></p>
            <a href="<?php the_permalink(); ?>" class="landing-mf-button bkg-defaultcat bkg-<?php echo $categoryCSS; ?> bkg-defaultcat-link bkg-<?php echo $categoryCSS; ?>-link"><?php echo $linktext; ?></a>
        </div>
                    
        <a href="<?php the_permalink(); ?>" class="landing-mf-image">
        	<?php $activeimage = get_needed_image("category-image-hard");?>
            <?php if ($activeimage != ""){ ?>
            	<img src="<?php echo $activeimage; ?>" />
            <?php }
			else { ?>
            	<img src="/wp-content/themes/PetAge/images/category-fallback/fallback-<?php echo $categoryCSS; ?>.jpg" />
            <?php }; ?>
        </a>
        <?php $counter++; ?>
        <?php $do_not_duplicate[] = $post->ID; ?>
<?php endwhile; ?>
<?php endif; ?>