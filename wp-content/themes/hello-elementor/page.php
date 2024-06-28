<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package petage
 */

get_header();
?>
	<div class="breadcrumb-section container">
		<?php asentech_custom_breadcrumb(); ?>
	</div>

	<div class='heading-section container-fluid'>
		<div class='container'>
			<h1 class='page-heading'><?php the_title(); ?></h1>
		</div>
	</div>
	
	<main id="primary" class="site-main container">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'page' );

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
