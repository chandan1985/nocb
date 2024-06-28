<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
  	
    <?php
	$fulltitle = get_the_title();
	$activeimage = get_needed_image("medium-image-hard");
	if ((strlen($fulltitle) >= 25)){
    	$shortitle = strpos($fulltitle, ' ', 20);
		$shortitle = substr($fulltitle,0,$shortitle);
	}
	else{
		$shortitle = $fulltitle;
	};
	?>
    <a href="<?php the_permalink(); ?>" class="mod-video">
    
    	<img class="mod-video-image" src="<?php echo $activeimage; ?>" />
    	<span class="mod-video-title"><?php echo $shortitle."..."; ?></span>
    	<img class="mod-video-playicon" src="<?php print IMAGES; ?>/video-play-icon-sml.png" />
    	<div class="mod-video-overshadow"></div>
    </a>  
          
<?php endwhile; ?>
<?php endif; ?>
<?php wp_reset_query() ?>

