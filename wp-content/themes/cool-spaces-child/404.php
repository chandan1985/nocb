<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Mag_Lite
 */

get_header(); ?>
<div class="container">
	<div class="row">

		<div id="primary" class="content-area custom-col-12">
			<main id="main" class="site-main">

				<section class="error-404 not-found">
					<figure class="error-icon">
						<img src="<?php echo esc_url( get_template_directory_uri())?>/assest/img/error-img.png" alt="">
					</figure>
					<div class="entry-content">
						<a href="<?php echo home_url(); ?>"><?php echo esc_html__( 'BACK to HOMEPAGE', 'mag-lite' );?></a>
					</div>
				</section>

			</main><!-- #main -->
		</div><!-- #primary -->
	</div>

</div>

<?php
get_footer();
