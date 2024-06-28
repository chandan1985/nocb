<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package petage
 */

get_header();
?>

<main class="search-page container">

<div class="row">
	<!-- Start col-sm-9 -->
		<div class="col-sm-9">
			<div class="search-wrapper">

				<h1 class="page-heading">
					<?php				
						/* translators: %s: search query. */
						// printf( esc_html__( 'Search Results for %s', 'furnituretoday' ), '<span>"'. get_search_query() . '"</span>' );
						if(get_search_query()){
							printf( esc_html__( 'Search Results for %s', 'hello-elementor' ), '<span id="search_query"></span>' );
						}
						else{
							print('Search Results');
						}
					?>
					<script>	
						var search_query = "<?php echo get_search_query(); ?>";
						var clean_query = search_query.replace(/(&quot;|&#039;|['"])+/g, '');
						console.log(clean_query);
						document.getElementById("search_query").innerHTML = '"'+clean_query+'"';
					</script>
				</h1>

				<div class="search-form-container"><?php get_search_form(); ?></div>
				
					<?php if ( have_posts() ) : ?>


						<?php
						/* Start the Loop */
						while ( have_posts() ) :
							the_post();

							/**
							 * Run the loop for the search to output the results.
							 * If you want to overload this in a child theme then include a file
							 * called content-search.php and that will be used instead.
							 */
							get_template_part( 'template-parts/content', 'search' );

						endwhile;

						// the_posts_navigation();
					?>
					<div class="pagenavi"><?php echo wp_pagenavi();?></div>
					<?php
					else :

						get_template_part( 'template-parts/content', 'none' );

					endif;
					?>

			</div>
		</div>
		<!-- Start col-sm-3 -->
			<div class="col-sm-3 desktop-show">
				<?php dynamic_sidebar('right-sidebar-area'); ?>
			</div>
		<!-- End col-sm-9 -->

</div>
<!-- End row -->

</main><!-- #main -->

<?php

get_footer();

?>