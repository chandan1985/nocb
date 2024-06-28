<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

get_header(); 
?>

<div class="content-wrap">
	<div class="content searchpage">
		<?php 
		//Above Post Banner
		if( empty( $get_meta["tie_hide_above"][0] ) ){
			if( !empty( $get_meta["tie_banner_above"][0] ) ) echo '<div class="e3lan-post">' .htmlspecialchars_decode($get_meta["tie_banner_above"][0]) .'</div>';
			else tie_banner('banner_above' , '<div class="e3lan-post">' , '</div>' );
		}
		?>
		<?php tie_breadcrumbs() ?>
		<?php $paged = get_query_var('page') ; ?>
		<div class="page-head">
			<h1 class="page-title"></h1>
		</div>
		<?php get_search_form(); ?>

		<?php if ( is_active_sidebar( 'search_page_sidebar' ) ) { ?>
			<?php dynamic_sidebar('search_page_sidebar'); ?>
		<?php } ?>

		<?php if ( ! have_posts() ) { ?>
			<div id="post-0" class="post not-found post-listing">
				<h1 class="post-title"><?php _e( 'Not Found', 'tie' ); ?></h1>
				<div class="entry">
					<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'tie' ); ?></p>
				</div>
			</div>
			<?php 
		} else {  
			while ( have_posts() ) { 
				the_post();  
				?>	
				<article class="item-list">
					<?php if ( has_post_thumbnail() ) { ?>			
						<div class="post-thumbnail single-post-thumb archive-wide-thumb">
							<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_post_thumbnail( 'medium' ); ?>
							<?php tie_get_score( true ); ?></a>
						</div>
					<?php } ?>

					<h2 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
					<?php get_template_part( 'includes/archives-meta' ); ?>					
					<div class="entry">
						<?php 
						if ( get_the_author_meta( 'byline' ) ) {
							$author = get_the_author_meta('byline' );
						} else { 
							$author = get_the_author();
						}
						$date = get_the_date();
						?>
						<div class="date">Posted by:<em><?php echo( $author ); ?></em> on <?php echo( $date ); ?></div>

						<p><?php echo substr(get_the_excerpt(), 0, 100) . '....'; ?></p>
						<a class="more-link" href="<?php the_permalink() ?>"><?php _e( 'Read More &raquo;', 'tie' ) ?></a>
					</div>
					<div class="clear"></div>
				</article><!-- .item-list -->
				<?php 
			}
			if ($wp_query->max_num_pages > 1) {
				tie_pagenavi();
			} 
		} 
		?>

		<?php //Below Post Banner
		if( empty( $get_meta["tie_hide_below"][0] ) ){
			if( !empty( $get_meta["tie_banner_below"][0] ) ) echo '<div class="e3lan-post">' .htmlspecialchars_decode($get_meta["tie_banner_below"][0]) .'</div>';
			else tie_banner('banner_below' , '<div class="e3lan-post">' , '</div>' );
		}
		?>
	</div><!-- .content -->

	<?php get_sidebar(); ?>
	<?php get_footer(); ?>
