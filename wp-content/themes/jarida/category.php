<?php get_header(); ?>
<div class="content-wrap">
	<div class="content">
	<?php tie_breadcrumbs() ?>
		<?php $category_id = get_query_var('cat') ; ?>
		<?php
		 	$cat_image = apply_filters( 'taxonomy-images-queried-term-image', '', array('image_size' => 'full') );
 			$cat_title = $cat_image?$cat_image:'<div>'.single_cat_title( '', false ).'</div>';
 		?>
		<div class="page-head category-template<?php echo $cat_image?' has_category_image':''; ?>">
			<h2 class="page-title">
				<?php echo $cat_title; ?>
			</h2>
			<?php if( tie_get_option( 'category_rss' ) ): ?>
			<a class="rss-cat-icon ttip" title="<?php _e( 'Feed Subscription', 'tie' ); ?>" href="<?php echo get_category_feed_link($category_id) ?>"></a>
			<?php endif; ?>
			<div class="stripe-line"></div>

			<?php
			if( tie_get_option( 'category_desc' ) ):	
				$category_description = category_description();
				if ( ! empty( $category_description ) )
				echo '<div class="clear"></div><div class="archive-meta">' . $category_description . '</div>';
			endif;
			?>
		</div>
		<?php
			$term_meta = get_option( "taxonomy_$category_id" );
			if ( !empty($term_meta['custom_header']) ) { echo do_shortcode( stripslashes( $term_meta['custom_header'] ) ); }
		?>

		<?php get_template_part( 'includes/slider-category' ) ?>
		<?php get_template_part( 'loop', 'category' );	?>
		<?php if ($wp_query->max_num_pages > 1) tie_pagenavi(); ?>
		
	</div> <!-- .content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>