<?php
/*
Template Name: Past Winners 
*/
get_header(); the_post(); ?>

            <div id="left-content" class="<?php
				$class = get_post_meta(get_the_ID(), 'content_class', true);
				echo $class ? $class : '';
			?>">
				<div class="int_wrap">
	            
					<h1><?php the_title(); ?></h1>
					<?php 	
					$category_ID = get_cat_id('Winners');
					query_posts( 'category='.$category_ID.'&posts_per_page=1' );
					if (have_posts()) : while (have_posts()) : the_post(); ?>
					
							<div id="post-<?php the_ID(); ?>">
								<div class="entry">
									<?php the_content('Read the rest of this entry &raquo;'); ?>
								</div>
							</div>
						<?php endwhile; ?>
					<?php endif; ?>
				</div>
            </div>
			<?php get_sidebar(); ?>
<?php get_footer(); ?>