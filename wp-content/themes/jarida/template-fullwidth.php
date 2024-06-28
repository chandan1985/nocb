<?php
/*
Template Name: Fullwidth
 */
?>
<?php get_header(); ?>
<?php $article_classes = array('post-listing','post');?>
<div class="content-wrap row">
	<div class="page-content col-lg-12">
		<?php
			if( !get_post_meta($post->ID,'tie_hide_breadcrumbs',true) ){
				tie_breadcrumbs();
			} else {
				$article_classes[] = 'hide-breadcrumbs';
			}

			if( get_post_meta($post->ID,'tie_hide_title',true) ){
				$article_classes[] = 'hide-title';
			}
		?>
		
		<?php if ( ! have_posts() ) : ?>
			<div id="post-0" class="post not-found post-listing">
				<h1 class="post-title"><?php _e( 'Not Found', 'tie' ); ?></h1>
				<div class="entry">
					<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'tie' ); ?></p>
					<?php get_search_form(); ?>
				</div>
			</div>
		<?php endif; ?>
		
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
		
		<?php 
			if( function_exists('bp_current_component') && bp_current_component() ) $current_id = get_queried_object_id();
			else $current_id = $post->ID;
			$get_meta = get_post_custom( $current_id );
			
			if( !empty( $get_meta['tie_review_position'][0] ) ){
				$review_position = $get_meta['tie_review_position'][0] ;
				$rv = $tie_reviews_attr;
			}
			
			if( !empty( $get_meta["tie_sidebar_pos"][0] ) && $get_meta["tie_sidebar_pos"][0] == 'full' ){
				if(tie_get_option( 'columns_num' ) == '2c') $content_width = 955;
				else $content_width = 1160;
			}
		?>
		<?php //Above Post Banner
		if( empty( $get_meta["tie_hide_above"][0] ) ){
			if( !empty( $get_meta["tie_banner_above"][0] ) ) echo '<div class="e3lan-post">' .do_shortcode(htmlspecialchars_decode($get_meta["tie_banner_above"][0])) .'</div>';
			else tie_banner('banner_above' , '<div class="e3lan-post">' , '</div>' );
		}
		?>

		<article <?php if( !empty( $rv['review'] ) ) echo $rv['review']; post_class($article_classes); ?>>
			<?php get_template_part( 'includes/post-head' ); // Get Post Head template ?>
			<div class="post-inner">

				<?php if( empty($get_meta["tie_hide_title"][0]) ){ ?>
					<h1 class="name post-title entry-title" itemprop="itemReviewed" itemscope itemtype="http://schema.org/Thing"><span itemprop="name"><?php the_title(); ?></span></h1>
				<?php } ?>
				<p class="post-meta"></p>
				<div class="clear"></div>
				<div class="entry">
					<?php if( !empty( $review_position ) && ( $review_position == 'top' || $review_position == 'both'  ) ) tie_get_review('review-top'); ?>

					<?php the_content(); ?>
					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'tie' ), 'after' => '</div>' ) ); ?>
					
					<?php if( !empty( $review_position ) && ( $review_position == 'bottom' || $review_position == 'both' ) ) tie_get_review('review-bottom'); ?>

					<?php //edit_post_link( __( 'Edit', 'tie' ), '<span class="edit-link">', '</span>' ); ?>
				</div><!-- .entry /-->	
				<span style="display:none" class="updated"><?php the_time( 'Y-m-d' ); ?></span>
				<?php if ( get_the_author_meta( 'google' ) ){ ?>
				<div style="display:none" class="vcard author" itemprop="author" itemscope itemtype="http://schema.org/Person"><strong class="fn" itemprop="name"><a href="<?php the_author_meta( 'google' ); ?>?rel=author">+<?php echo get_the_author(); ?></a></strong></div>
				<?php }else{ ?>
				<div style="display:none" class="vcard author" itemprop="author" itemscope itemtype="http://schema.org/Person"><strong class="fn" itemprop="name"><?php the_author_posts_link(); ?></strong></div>
				<?php } ?>
				
			</div><!-- .post-inner -->
		</article><!-- .post-listing -->
		<?php endwhile; wp_reset_postdata(); ?>

		
		<?php //Below Post Banner
		if( empty( $get_meta["tie_hide_below"][0] ) ){
			if( !empty( $get_meta["tie_banner_below"][0] ) ) echo '<div class="e3lan-post">' .do_shortcode(htmlspecialchars_decode($get_meta["tie_banner_below"][0])) .'</div>';
			else tie_banner('banner_below' , '<div class="e3lan-post">' , '</div>' );
		}
		?>
		<?php if( empty($get_meta["tie_hide_comments"][0]) ){ ?>
			<?php if( !function_exists('bp_current_component') || (function_exists('bp_current_component') && !bp_current_component() ) )  comments_template( '', true );  ?>
		<?php } ?>
	</div><!-- .content -->


        </div>
		<?php if(isset($_GET['dmcss']) && $_GET['dmcss']=="login"){?>
<style type="text/css">
.entry-title{display:none;}
#login_div .entry-title{display:block;}

</style>
<?php } ?>
<?php get_footer(); ?>