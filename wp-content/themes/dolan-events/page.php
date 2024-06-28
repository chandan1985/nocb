<?php get_header(); the_post(); ?>

            <div id="left-content" class="<?php
				$class = get_post_meta(get_the_ID(), 'content_class', true);
				echo $class ? $class : '';
			?>">
				<div class="int_wrap">
	            
					<h1><?php
						global $true_parent, $DR_Kids;
						if ( get_the_ID() != $true_parent->ID )
							echo apply_filters('the_title', $true_parent->post_title) . ' : ';
						the_title(); 
					?></h1>
					<?php the_content(); ?>
					
				</div>
            </div>
			
			<?php get_sidebar(); ?>
            
<?php get_footer(); ?>