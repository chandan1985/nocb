<?php 
function get_home_news_pic( $cat_data ){ 
	$Posts_num = 9;
	$Cat_ID = $cat_data['id'];
	$Box_Title = $offset = '';

	if( !empty($cat_data['title']) )
		$Box_Title = $cat_data['title'];
		
	if( !empty($cat_data['offset']) )
		$offset = $cat_data['offset'];
		
	//$cat_query = new WP_Query('cat='.$Cat_ID.'&no_found_rows=1&posts_per_page='.$Posts_num.'&offset='.$offset);

	/* ---------------------------------------------------- */
	/* Dolan changes to support stickyposts        -------- */
	/* ---------------------------------------------------- */

	// get the sticky posts that match this query
		$args = array(
			'posts_per_page' => $Posts_num,
			'post__in'       => get_option('sticky_posts'),
			'no_found_rows'  => true
		);
	// do a cat in if we want to hide this category's children
	if ($cat_data['cat_hide_children'] == 'y')
		$args = array_merge($args, array('category__in'   => $Cat_ID,));
	else
		$args = array_merge($args, array('cat'   => $Cat_ID,));
	$stickyposts = new WP_Query($args);

	// build a list of the ids, to strip from the main query
	$stickies = array();
	foreach($stickyposts as $i => $post) {
		if ($post->ID) $stickies[] = $post->ID;
	}

	// get the main query, but NOT any stickyposts (to avoid duplicates)
		// do a cat in if we want to hide this category's children
			$args = array(
				'posts_per_page' => $Posts_num,
				'post__not_in'   => $stickies,
				'no_found_rows'  => true
			);
	// do a cat in if we want to hide this category's children
	if ($cat_data['cat_hide_children'] == 'y')
		$args = array_merge($args, array('category__in'   => $Cat_ID,));
	else
		$args = array_merge($args, array('cat'   => $Cat_ID,));

	// add the rand order to the args array
	if( $order == 'rand') $args = array_merge($args, array('orderby' => 'rand'));

	$cat_query = new WP_Query($args);
	// push the stickyposts onto the top of the posts
	$cat_query->posts = array_merge($stickyposts->posts, $cat_query->posts);
	/* ---------------------------------------------------- */
?>
		<section class="cat-box pic-box clear tie-cat-<?php echo $Cat_ID ?>">
			<h2 class="cat-box-title"><a href="<?php echo get_category_link( $Cat_ID ); ?>"><?php if( function_exists('icl_t') ) echo icl_t( theme_name , $cat_data['boxid'] , $Box_Title); else echo $Box_Title ; ?></a></h2>

			<div class="cat-box-content">
				<?php if($cat_query->have_posts()): $count=0; ?>
				<ul>
				<?php while ( $cat_query->have_posts() ) : $cat_query->the_post(); $count ++ ;?>
				<?php if($count == 1 ) : ?>
					<li class="first-pic">
						<?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() ) : ?>			
							<div class="post-thumbnail">
								<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_post_thumbnail( 'tie-large' ) ; ?></a>
							</div><!-- post-thumbnail /-->
						<?php endif; ?>
					
					</li><!-- .first-pic -->
					<?php else: ?>
					<li>
						<?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() ) : ?>			
							<div class="post-thumbnail">
								<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="ttip"><?php the_post_thumbnail( array(65, 65 ) ); ?></a>
							</div><!-- post-thumbnail /-->
						<?php endif; ?>			
					</li>
					<?php endif; ?>
				<?php endwhile;?>
				</ul>
				<div class="clear"></div>

					<?php endif; ?>
			</div><!-- .cat-box-content /-->
		</section>
<?php } ?>