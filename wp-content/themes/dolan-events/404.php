<?php get_header(); the_post(); ?>

            <div id="left-content" class="<?php
				$class = get_post_meta(get_the_ID(), 'content_class', true);
				echo $class ? $class : '';
			?>">
				<div class="int_wrap">
	            
					<h1>Page Not Found</h1>
					<p>Sorry, the page you were looking for was not found.</p>
					
				</div>
            </div>
			
			<?php get_sidebar(); ?>
            
<?php get_footer(); ?>