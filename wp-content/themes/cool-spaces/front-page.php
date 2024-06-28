<?php
/**
 * The template for displaying home page.
 * @package Mag_Lite
 */

get_header(); 

global $wp_query;
?>
<section class="section-sm section-sm-mod-3 bg-gray-dark text-center text-md-left">
	<?php if ( 'posts' != get_option( 'show_on_front' ) ){?>
		<?php if ( is_active_sidebar( 'home-promo-section' ) ) { ?>
			<div class="featured-news-container">
				<div class="container">
					<div class="site-main">
						<?php 	
						dynamic_sidebar( 'home-promo-section' ); 
						?>
					</div>
					
				</div>

			</div>
		<?php  } ?>
		<?php
		$template_path = get_post_meta(get_the_ID(), '_wp_page_template', true);	
		$layout_class ='custom-col-8';
		$sidebar_layout = mag_lite_get_option('layout_options'); 
		if( is_active_sidebar('home-page-sidebar') && 'no-sidebar' !==  $sidebar_layout){
			$layout_class = 'custom-col-8';
		}
		else{
			$layout_class = 'custom-col-12';
		}		
		?>	
		
		<?php if ($template_path == 'default') { ?>
			<div class="container">
				<div class="content-section">	
					<h3><?php echo the_title(); ?></h3>
					<p><?php 						
					if (have_posts()) :
						while (have_posts()) :
							the_post();
							the_content();
						endwhile;
					endif;
					?>
				</p>
			</div>
			<?php 	
			if ( is_active_sidebar( 'home-page-section' ) ) {
				dynamic_sidebar( 'home-page-section' ); 
			}
			?>
			<?php get_sidebar( 'home' );?>
		</div>
	<?php } else{ ?>

		<section class="middle-section cool-space-home-page">
			<div class="container">
				<div class="row">
					<div class="article-main">
						<?php
						while ( have_posts() ) : the_post();
							get_template_part( 'template-parts/content', 'single' );
						endwhile; 		
						?>	
					</div>

				</div>
			</div>	
		</section>	
	<?php } ?>


<?php } else{ ?>
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
			<?php
			if ( have_posts() ) : 
				if ( is_home() && ! is_front_page() ) : ?>
					<header>
						<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
					</header>

					<?php
				endif; 
				?>
				
				<?php $opp_layout_class = '';
				$archive_layout = mag_lite_get_option( 'archive_layout' );
				if( 'first-design' == $archive_layout):
					$opp_layout_class = 'opp-image-layout';
				endif;
				?>				
				<div class="post-wrapper <?php echo esc_attr( $opp_layout_class);?> ">
					<header>
						<h3>Coolest Spaces <?php echo date('Y');?></h3>
					</header>

					<?php 

					while ( have_posts() ) : the_post(); 
					/*
					 * Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part('template-parts/content', get_post_format());

				endwhile; ?>
				
			</div>

			<nav>
				<?php if (function_exists("pagination")) {
					pagination($wp_query->max_num_pages);
				} ?>
			</nav>
				<?php //the_posts_navigation();
			else :
				get_template_part( 'template-parts/content', 'none' );
			endif; ?>		
			<?php get_sidebar();?>
		</div>
	</div>
</section>

<?php } ?>
<?php
get_footer();