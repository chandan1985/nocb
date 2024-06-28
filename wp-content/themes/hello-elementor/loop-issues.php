<?php
global $wp_query;
$id_array = wp_list_pluck( $wp_query->posts, 'ID' );

//$issue_catList=array(29,8,24,7,10,149,222,35,52,33,47,56,11,3);
//$issue_misc_label = 'Miscellaneous';
$issue_catList = explode(',', get_option('tdc_jarida_issue_catids'));
$issue_misc_label = get_option('tdc_jarida_issue_misc_label');
?>

<?php if ( ! have_posts() ) : ?>
	<div id="post-0" class="post not-found post-listing">
		<h2 class="post-title"><?php _e( 'Not Found', 'tie' ); ?></h2>
		<div class="entry">
			<p><?php _e( 'Apologies, but no results were found for the requested issue. Perhaps searching will help find a related post.', 'tie' ); ?></p>
			<?php get_search_form(); ?>
		</div>
	</div>
<?php else : $count = 0;?>
<div class="post-listing issues <?php if( tie_get_option( 'blog_display' ) == '2col' ) echo"archives_2col" ?>">

<?php
// Walk through each category, listing each post in that cat
foreach ($issue_catList as $cat) {
	// only look for more posts in categories if there are post ids left in the "to-be-done" array
	if ($id_array) {
		//get the posts from the idlist in this category
		$myposts = get_posts(array('post__in' => $id_array,'category' => $cat,'posts_per_page' => 500, 'numberposts' => 500));
		if ($myposts) {
			$cat_posts_array=array();
			echo '<h3 class="category">'.get_cat_name( $cat ).'</h3>';
			foreach($myposts as $post) :   setup_postdata($post);
				$cat_posts_array[] = $post->ID;
				$featured_image = get_the_post_thumbnail($post->ID, 'category-list-thumb');
				// print $featured_image;
		 	?>
			<article class="item-list article-listing  <?php echo'item_'.$count; ?>">
				<div class="article-detail-listing">
					<div class="image-section">
						<?php print $featured_image;  ?>
					</div>
					<div class="content-section">
						<p class="post-meta">
							<?php tie_get_time() ?>
							<span class="post-comments"><?php comments_popup_link( __( '0', 'tie' ), __( '1 Comment', 'tie' ), __( '% Comments', 'tie' ) ); ?></span>
							<?php echo tie_views(); ?>
						</p>
						<h2 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
					</div>
				</div>

			</article><!-- .item-list -->
			<?php $count++; ?>
			<?php endforeach;
			//remove the ids we displayed
			$id_array=array_diff($id_array,$cat_posts_array);
		}
	}
}

// Walk through the leftovers, grouping them if desired
if ($id_array) {
	//get all the posts left
	$myposts = get_posts(array('post__in' => $id_array,'posts_per_page' => 500, 'numberposts' => 500));
	if ($myposts) {
		$cat_posts_array=array();
		echo '<h3 class="category">'.$issue_misc_label.'</h3>';
		foreach($myposts as $post) :   setup_postdata($post);
			$cat_posts_array[] = $post->ID;
			?>

			<article class="item-list article-listing <?php echo'item_'.$count; ?>">
				<div class="article-detail-listing">
					<div class="image-section"></div>
					<div class="content-section">
						<h2 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
						<p class="post-meta">
							<?php tie_get_time() ?>
							<span class="post-comments"><?php comments_popup_link( __( '0', 'tie' ), __( '1 Comment', 'tie' ), __( '% Comments', 'tie' ) ); ?></span>
							<?php echo tie_views(); ?>
						</p>
					</div>
				</div>
			</article><!-- .item-list -->
		<?php endforeach; ?>
		<?php
	}
}
?>
</div>
<?php endif; ?>