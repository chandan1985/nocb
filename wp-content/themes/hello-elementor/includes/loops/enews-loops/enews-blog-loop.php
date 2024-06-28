<?php $featCounter = 1; ?>
<?php if ( have_posts() ) : while ( have_posts() && $featCounter == 1 ) : the_post(); ?>

    <div class="featured-story clearfix">
        <h1><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>
        <div class="feat-image-cont">
        	<?php $activeimage = get_needed_image("large-image");?>
            <a href="<?php the_permalink() ?>">
            	<img class="feat-image" src="<?php echo $activeimage; ?>" />
            </a>
            <span class="feat-summary"><?php the_excerpt(); ?></span>
            
            
            <div class="feat-cat-cont">
                <?php $categories = get_the_category(); ?>
                <?php foreach ($categories as $category){
					if (($category->parent) == 21){
					$categorySlug = str_replace("topic-", "", ($category->slug));
                    echo "<a href='". esc_url( get_category_link( $category->term_id ) ) ."' class='feat-category ".$categorySlug."'>".$category->name."</a>";
					}
                };
                ?>
            </div>
        </div>
    </div>    
   
	<?php $do_not_duplicate[] = $post->ID; ?>
    <?php $featCounter = 2; ?>
<?php endwhile; ?>
<?php endif; ?>


