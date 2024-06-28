<?php

/**
Theme Name: Cool Spaces Child Theme
Theme URI: http://rigorousthemes.com/downloads/mag-lite
Author: Rigorous Themes
Author URI: http://rigorousthemes.com/
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Mag_Lite
 */
get_header(); 
?>
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
<div class="container">
	<div class="row">
	
	 

		<div id="primary" class="content-area <?php echo esc_attr( $layout_class)?>">
		
		    
			<div class="theiaStickySidebar">
				<main id="main" class="site-main">

					

						<?php
						if ( have_posts() ) : 
							if ( is_home() && ! is_front_page() ) : ?>
								<header>
									<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
								</header>

							<?php
							endif; ?>
							<?php $opp_layout_class = '';
								$archive_layout = mag_lite_get_option( 'archive_layout' );
								if( 'first-design' == $archive_layout):
									$opp_layout_class = 'opp-image-layout';
								endif;
							?>

							<div class="post-wrapper <?php echo esc_attr( $opp_layout_class);?>">

								
								<?php while ( have_posts() ) : the_post(); 
									/*
									 * Include the Post-Format-specific template for the content.
									 * If you want to override this in a child theme, then include a file
									 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
									 */
									get_template_part( 'template-parts/content', get_post_format() );

								endwhile; ?>

							</div>

							<?php the_posts_navigation();

						else :

							get_template_part( 'template-parts/content', 'none' );

						endif; ?>

					

				</main><!-- #main -->
			</div>	
		</div><!-- #primary -->
		<?php get_sidebar();?>
	</div>
</div>

<?php
 get_footer();
