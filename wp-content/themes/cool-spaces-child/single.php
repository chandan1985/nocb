<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Mag_Lite
 */

get_header(); ?>
	<?php 
		$layout_class ='col-8';
		$sidebar_layout = mag_lite_get_option('layout_options'); 
		if( is_active_sidebar('sidebar-1') && 'no-sidebar' !==  $sidebar_layout){
			$layout_class = 'custom-col-8';
		}
		else{
			$layout_class = 'custom-col-12';
		}		
	?>
<div class="page text-left">
<div class="container">			
				<?php
				while ( have_posts() ) : the_post();
					get_template_part( 'template-parts/content', 'single' );
					the_post_navigation();
					// If comments are open or we have at least one comment, load up the comment template.
					/*if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;*/
				endwhile; // End of the loop.
				?>	
		<?php //get_sidebar();?>
</div>
</div>

<?php
get_footer();