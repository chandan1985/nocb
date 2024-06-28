<?php
/**
 * The template for displaying home page.
 * @package Mag_Lite
 */
get_header();
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
		$layout_class ='custom-col-8';
		$sidebar_layout = mag_lite_get_option('layout_options'); 
		if( is_active_sidebar('home-page-sidebar') && 'no-sidebar' !==  $sidebar_layout){
			$layout_class = 'custom-col-8';
		}
		else{
			$layout_class = 'custom-col-12';
		}		
	?>	
  


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
						<h3>Coolest Spaces <?php echo date(Y);?></h3>
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
<?php //if (function_exists("pagination")) {
    //pagination($custom_query->max_num_pages);
//} ?>
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