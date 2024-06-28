<?php 


get_header(); 
?>

            <div id="left-content" class="<?php
				$class = get_post_meta(get_the_ID(), 'content_class', true);
				echo $class ? $class : '';
			?>">
				<div class="int_wrap">
	            
					
					<?php 	if (have_posts()) : ?>
				
						<?php while (have_posts()) : the_post(); ?>
							<h1><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
								
							<div id="post-<?php the_ID(); ?>">
								<!--<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>-->
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