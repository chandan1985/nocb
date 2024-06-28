<?php 
/*
Template Name: Covid 19 Coverage Template
*/
get_header(); ?>
<?php $article_classes = array('post-listing','post');?>
<div class="content-wrap">
	<div class="content">
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
		
		<?php /*
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
		} */
		?>

		<article <?php if( !empty( $rv['review'] ) ) echo $rv['review']; post_class($article_classes); ?>>
			<?php get_template_part( 'includes/post-head' ); // Get Post Head template ?>
			<div class="post-inner">
				<?php if( !$get_meta["tie_hide_title"][0] ){ ?>
					<h1 class="name post-title entry-title" itemprop="itemReviewed" itemscope itemtype="http://schema.org/Thing"><span itemprop="name"><?php the_title(); ?></span></h1>
				<?php } ?>
				<p class="post-meta"></p>
				<div class="clear"></div>
				<div class="entry">
					<?php if( !empty( $review_position ) && ( $review_position == 'top' || $review_position == 'both'  ) ) tie_get_review('review-top'); ?>

					<?php the_content(); ?>
					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'tie' ), 'after' => '</div>' ) ); ?>
					
					
				</div><!-- .entry /-->	
				
				
			</div><!-- .post-inner -->
		</article><!-- .post-listing -->
		<?php endwhile; 
		wp_reset_postdata();
		
		?>
		
		
		<?php 
		$posts_per_page = (get_option('posts_per_page')) ? get_option('posts_per_page') : 11; 
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;	
		$args = array(
		'posts_per_page' => -1,
		'category_name' => 'covid-19-coverage',
		'post_status' => 'publish',
		'order'         => 'DESC',
		'paged' =>$paged
		);	
		$all_posts = get_posts($args);
		$post_count = count($all_posts);
		$num_pages = ceil($post_count / $posts_per_page);
		if($paged > $num_pages || $paged < 1){
			$paged = $num_pages;
		}

		$args = array(
			'posts_per_page'   => $posts_per_page,
			'category_name' => 'covid-19-coverage',
			'post_status' => 'publish',
			'order'         => 'DESC',
			'paged' =>$paged
		 );
		$my_posts = get_posts($args);
		if(! empty($my_posts)){
				// The Loop
				
				foreach($my_posts as $post){ ?>
				<article class="item-list">
					<h2 class="post-title"><a href="<?php echo $post->guid; ?>" rel="bookmark"><?php echo $post->post_title; ?></a></h2>
					<?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() ) : ?>			
					<div class="post-thumbnail">
						<a href="<?php echo $post->guid; ?>" rel="bookmark">
							<?php echo get_the_post_thumbnail( $post->ID, 'tie-large' ); ?>
							<?php // tie_get_score( true ); ?>
						</a>
					</div><!-- post-thumbnail /-->
						<?php endif; ?>
					<div class="entry">
						<p><?php echo $post->post_excerpt; ?></p>

						<?php if( tie_get_option( 'post_tags' ) ) the_tags( '<p class="post-tag">'.__( 'Tagged with: ', 'tie' )  ,' ', '</p>'); ?>

						<a class="more-link" href="<?php the_permalink() ?>"><?php _e( 'Read More &raquo;', 'tie' ) ?></a>
					</div>
					<div class="clear"></div>
				</article><!-- .item-list -->
				<?php
				}
								 
				if($post_count > $posts_per_page ){

						$current_page = max(1, get_query_var('paged'));
								echo '
								<div class="pagination">
									<span class="pages">Page '.$current_page.' of '. $num_pages.' </span>';
									if($current_page >1){
									echo '<a class="page" href="'.get_pagenum_link(($current_page-1 > 0 ? $current_page-1 : 1)).'">&laquo;</a></span>';
									}
									echo '<span class="pagenavi-ones">';
									 // <span class="curent">'.$current_page.'</span>';
									
									for($i=1;$i<=$num_pages;$i++)
										if($i== $current_page){
										echo '<a title='.$i.' class="curent">'.$i.'</a>';
										}else{
										echo '<a title='.$i.' class="page" href="'.get_pagenum_link($i).'">'.$i.'</a>';
										}
																
									if($current_page != $num_pages){	
									echo '<span id="tie-next-page">
									<a class="page" href="'.get_pagenum_link(($current_page+1 <= $num_pages ? $current_page+1 : $num_pages)).'">&raquo;</a></span>';
									}
									echo '<span class="pagenavi-tens"></span></div>	';
					}
					
				}?>
				
				<?php 
				/*
					$orig_query = $wp_query; // fix for pagination to work
					$wp_query = $the_query1;
					if ($wp_query->max_num_pages > 1) {
						echo tie_pagenavi(); 
					}
					$wp_query = $orig_query;
				*/	
					?>
				
				<?php wp_reset_postdata();?>
				
				<?php edit_post_link( __( 'Edit', 'tie' ), '<span class="edit-link">', '</span>' ); ?>
					

		<?php if( !$get_meta["tie_hide_comments"][0] ){ ?>
			<?php if( !function_exists('bp_current_component') || (function_exists('bp_current_component') && !bp_current_component() ) )  comments_template( '', true );  ?>
		<?php } ?>
	</div><!-- .content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>