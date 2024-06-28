<?php $do_not_duplicate[] = ""; ?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<?php $currpostid = $post->ID ?>
<?php if (!in_array($currpostid, $do_not_duplicate, true)) {?>
	<div class="landing-substory dir-list bor-defaultcat clearfix">        
        <a href="<?php the_permalink(); ?>" class="landing-sub-image landing-sub-image-blogs bor-<?php echo $categoryCSS; ?>">
        	<?php $activeimage = get_needed_image("category-image-hard");?>
            <?php if ($activeimage != ""){ ?>
            	<img class="dir-details-image" src="<?php echo $activeimage; ?>" />
            <?php }
			else{ ?>
				<img class="dir-details-image" src="<?php load_blog_image(); ?>" />
			<?php };
			?>
        </a>
        <a href="<?php the_permalink(); ?>" class="landing-sub-title"><?php the_title(); ?></a>
        <p>&nbsp;</p>
        <p class="landing-sub-summary dir-details"><?php $excerpt = get_the_excerpt(); echo $excerpt; ?><a class="read-more-link" href="<?php the_permalink(); ?>"><?php echo $linktext; ?></a></p>
        <a href="<?php the_permalink(); ?>" class="landing-mf-button bkg-defaultcat bkg-health bkg-defaultcat-link bkg-health-link blog-button">Read More</a>
    	<?php $do_not_duplicate[] = $post->ID; ?>
	</div>
<?php } ?>    
<?php endwhile; ?>
<?php include "pagination.php"; ?>
<?php endif; ?>