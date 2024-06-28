<?php if ( have_posts() ) : while ( have_posts()) : the_post(); ?>
        <a href="<?php the_permalink() ?>" class="mod-blog clearfix">
        	<?php
        	$categories = get_the_category();
			foreach ($categories as $category){
				if (($category->parent) == 109){
					$catname = ($category->name);
				};
			};
            ?>
        	<div class="mod-blog-label"><?php echo str_replace('Category','',$catname) ?> </div>
        	<img class="mod-blog-img"  src="<?php load_blog_image() ?>" />
        	<div class="mod-blog-title"><?php the_title() ?></div>
            <div class="mod-blog-readblog">Read Letter &rarr;</div>
        </a>    
<?php endwhile; ?>
<?php endif; ?>
<?php wp_reset_query() ?>