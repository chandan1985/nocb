<?php $recallcounter = 1; ?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<?php if($recallcounter == 1){ ?>
    <a href="<?php echo get_the_permalink(); ?>" class="product-recalls clearfix">
        <h2>Recall Alert:</h2>
        <h3><?php echo get_the_title(); ?></h3>
        <?php $activeimage = get_needed_image("medium-image");?>
        <img src="<?php echo $activeimage;?>" />
        <p><?php echo limit_words(get_the_excerpt(),15); ?>...</p>
    </a>
    <?php } ?>
    <?php if($recallcounter == 2){ ?>
    <a href="<?php echo get_the_permalink(); ?>" class="product-recalls product-recalls-past clearfix">
        <h2>Recall Alert:</h2>
        <h3><?php echo get_the_title(); ?> <span class="fa fa-caret-right"></span></h3>
    </a>
    <?php } ?>
    <?php if($recallcounter == 3){ ?>
    <a href="<?php echo get_the_permalink(); ?>" class="product-recalls product-recalls-past product-recalls-past-last clearfix">
        <h2>Recall Alert:</h2>
        <h3><?php echo get_the_title(); ?> <span class="fa fa-caret-right"></span></h3>
    </a>
    <?php }; ?>
    <?php $recallcounter++; ?>
<?php endwhile; ?>
<?php endif; ?>
<?php wp_reset_query() ?>