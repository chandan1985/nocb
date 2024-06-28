<?php 
function get_home_cats( $cat_data ){ 
	global $columns ;

	// set the thumb size
	$cur_option = get_option('tdc_jarida_home_widget_thumbnail_size');
	if (!$cur_option) {$cur_option = 'tie-small';}

	$Cat_ID = $cat_data['id'];
	$offset = $Posts_num = $order = $rand = '';

	if( !empty($cat_data['number']) )
		$Posts_num = $cat_data['number'];
		
	if( !empty($cat_data['order']) )
		$order = $cat_data['order'];
		
	if( !empty($cat_data['offset']) )
		$offset =  $cat_data['offset'];

	if( $order == 'rand') $rand ="&orderby=rand";

	/*
	$cat_query = new WP_Query('cat='.$Cat_ID.'&no_found_rows=1&posts_per_page='.$Posts_num.$rand.'&offset='.$offset);
	$cat_title = get_the_category_by_ID($Cat_ID);
	$count = 0;
	$home_layout = $cat_data['style'];
	*/

	/* ---------------------------------------------------- */
	/* Dolan changes to support stickyposts        -------- */
	/* ---------------------------------------------------- */

	$stickies = array();
	// get the sticky posts that match this query
		if ( get_option('sticky_posts') ) {
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
			foreach($stickyposts as $i => $post) {
				if (!empty($post->ID)) $stickies[] = $post->ID;
			}
		}

	// get the main query, but NOT any stickyposts (to avoid duplicates)
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
		if ( get_option('sticky_posts') ) {
			//Only merge if more posts are needed than stickyposts has
			if($stickyposts->post_count > 0) {
				if($Posts_num > $stickyposts->post_count) {
					$cat_query->posts = array_merge($stickyposts->posts, $cat_query->posts);
					$cat_query->post_count = $stickyposts->post_count + $cat_query->post_count;
				}
				else {
					$cat_query = $stickyposts;
				}
			}
		}
	/* ---------------------------------------------------- */


	$cat_title = get_the_category_by_ID($Cat_ID);
	$count = 0;
	$home_layout = isset($cat_data['style']) ? $cat_data['style'] : '';
	$show_excerpt = isset($cat_data['excerpt']) ? $cat_data['excerpt'] : '';
	$show_excerpt_css = ($show_excerpt == 'y' ? ' show-excerpt' : '' );
	$after_openx = isset($cat_data['openx']) ? $cat_data['openx'] : '';
	$after_html = isset($cat_data['text']) ? $cat_data['text'] : '';
	// tdc: if there is a cat image, use it
	$term = get_term( $Cat_ID, 'category' );
	$terms = apply_filters( 'taxonomy-images-get-terms', '', array('term_args' => 'slug='.$term->slug) );
	$cat_image='';
	$image_title_class = '';
	if (!empty($terms[0]->image_id)) {
		$cat_image = wp_get_attachment_image( $terms[0]->image_id, 'full' );
		//$cat_title = $cat_image?$cat_image:$cat_title;
		$image_title_class = ' has-title-image';
	}
	
?>
	<?php if( $home_layout == '2c'):  //************** 2C ****************************************************** ?>
		<?php $columns++; ?>
		<section class="cat-box column2 tie-cat-<?php echo $Cat_ID ?> <?php if($columns == 2) { echo 'last-column'; $columns=0; } ?>">
			<h2 class="cat-box-title<?php echo $image_title_class; ?>"><a href="<?php echo get_category_link( $Cat_ID ); ?>"><?php echo $cat_title ; ?></a></h2>
			<div class="cat-box-content">
			
				<?php if($cat_query->have_posts()): ?>
				<ul>
				<?php while ( $cat_query->have_posts() ) : $cat_query->the_post(); $count ++ ;?>
				<?php if($count == 1) : ?>
					<li class="first-news <?php $ptype=get_post_type();if(!in_array($ptype, array('post','page'))){echo $ptype;} ?> ">
						<?php
							// tdc: draw the section image INDSIDE the first story
 							if ($cat_image != '') {
								echo '<div class="cat-box-title-image"><a href="'.get_category_link( $Cat_ID ).'">'.$cat_image.'</a></div>';
 							}
 						?>
						<div class="inner-content">
						<?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() ) : ?>			
							<div class="post-thumbnail">
								<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark">
									<?php the_post_thumbnail( 'tie-large' ) ; ?>
									<?php tie_get_score( true ); ?>
								</a>
							</div><!-- post-thumbnail /-->
						<?php else: ?>
						<div class="empty-space"></div>
						<?php endif; ?>
					
							<h2 class="post-box-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
							<?php get_template_part( 'includes/boxes-meta' ); ?>
							<div class="entry">
								<p><?php tie_excerpt_home() ?></p>
								<a class="more-link" href="<?php the_permalink() ?>"><?php _e( 'Read More &raquo;', 'tie' ) ?></a>
							</div>
						</div>
					</li><!-- .first-news -->
					<?php else: ?>
					<li class="<?php echo $show_excerpt_css; ?>">
						<?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() ) : ?>			
							<div class="post-thumbnail">
								<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark">
									<?php the_post_thumbnail( $cur_option ); ?>
									<?php tie_get_score(); ?>
								</a>
							</div><!-- post-thumbnail /-->
						<?php endif; ?>			
						<h3 class="post-box-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
						<?php get_template_part( 'includes/boxes-meta' ); ?>
						<?php
						// tdc: optional show excerpt on the rest of the items
						if ($show_excerpt == 'y') : ?>
							<div class="entry non-first">
								<p><?php tie_excerpt_home() ?></p>
								<a class="more-link" href="<?php the_permalink() ?>"><?php _e( 'Read More &raquo;', 'tie' ) ?></a>
							</div>
						<?php endif; ?>
					</li>
					<?php endif; ?>
				<?php endwhile;?>
				</ul>

				<?php endif; ?>

				<?php
				// tdc: openx after widget
				if ($after_openx) {
					echo do_shortcode(htmlspecialchars_decode('[openx zoneid="'.$after_openx.'"]'));
				}
				// tdc: html after widget
				if($after_html) {
					echo '<div class="after-widget-html">'.do_shortcode(htmlspecialchars_decode($after_html)).'</div>';
				}
				?>

			</div><!-- .cat-box-content /-->
		</section> <!-- Two Columns -->
		
		
	<?php elseif( $home_layout == '1c' ):  //************** 1C ******************************************************  ?>
		<section class="cat-box wide-box tie-cat-<?php echo $Cat_ID ?>">
			<h2 class="cat-box-title<?php echo $image_title_class; ?>"><a href="<?php echo get_category_link( $Cat_ID ); ?>"><?php echo $cat_title ; ?></a></h2>
			<div class="cat-box-content">
			
				<?php $count = 0; $count2 = 0; if($cat_query->have_posts()): ?>
				<ul>
				<?php while ( $cat_query->have_posts() ) : $cat_query->the_post(); $count ++ ;?>
				<?php if($count == 1) : ?>
					<li class="first-news <?php $ptype=get_post_type();if(!in_array($ptype, array('post','page'))){echo $ptype;} ?> ">
						<?php
						// tdc: draw the section image INDSIDE the first story
						if ($cat_image != '') {
							echo '<div class="cat-box-title-image"><a href="'.get_category_link( $Cat_ID ).'">'.$cat_image.'</a></div>';
						}
						?>
						<div class="inner-content">
						<?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() ) : ?>			
							<div class="post-thumbnail">
								<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark">
									<?php the_post_thumbnail( 'tie-large' ) ; ?>
									<?php tie_get_score( true ); ?>
								</a>
							</div><!-- post-thumbnail /-->
						<?php else: ?>
						<div class="empty-space"></div>
						<?php endif; ?>
						
							<h2 class="post-box-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
							<?php get_template_part( 'includes/boxes-meta' ); ?>
							<div class="entry">
								<p><?php tie_excerpt_home() ?></p>
								<a class="more-link" href="<?php the_permalink() ?>"><?php _e( 'Read More &raquo;', 'tie' ) ?></a>
							</div>
						</div>
					</li><!-- .first-news -->
					<?php else:  $count2 ++ ; ?>
					<li class="<?php if( $count2 == 2){ echo 'last-column'; $count2=0;} ?><?php echo $show_excerpt_css; ?> <?php $ptype=get_post_type();if(!in_array($ptype, array('post','page'))){echo $ptype;} ?> ">
						<?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() ) : ?>			
							<div class="post-thumbnail">
								<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark">
									<?php the_post_thumbnail( $cur_option ); ?>
									<?php tie_get_score(); ?>
								</a>
							</div><!-- post-thumbnail /-->
						<?php endif; ?>			
						<h3 class="post-box-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
						<?php get_template_part( 'includes/boxes-meta' ); ?>
						<?php
						// tdc: optional show excerpt on the rest of the items
						if ($show_excerpt == 'y') : ?>
							<div class="entry non-first">
								<p><?php tie_excerpt_home() ?></p>
								<a class="more-link" href="<?php the_permalink() ?>"><?php _e( 'Read More &raquo;', 'tie' ) ?></a>
							</div>
						<?php endif; ?>
					</li>
					<?php endif; ?>
				<?php endwhile;?>
				</ul>
				<div class="clear"></div>

					<?php endif; ?>

				<?php
				// tdc: openx after widget
				if ($after_openx) {
					echo do_shortcode(htmlspecialchars_decode('[openx zoneid="'.$after_openx.'"]'));
				}
				// tdc: html after widget
				if($after_html) {
					echo '<div class="after-widget-html">'.do_shortcode(htmlspecialchars_decode($after_html)).'</div>';
				}
				?>

			</div><!-- .cat-box-content /-->
		</section><!-- Wide Box -->

	<?php else :   //************** list **********************************************************************************  ?>
		
		<section class="cat-box list-box tie-cat-<?php echo $Cat_ID ?>">
			<h2 class="cat-box-title<?php echo $image_title_class; ?>"><a href="<?php echo get_category_link( $Cat_ID ); ?>"><?php echo $cat_title ; ?></a></h2>
			<div class="cat-box-content">
			
				<?php if($cat_query->have_posts()): ?>
				<ul>
				<?php while ( $cat_query->have_posts() ) : $cat_query->the_post(); $count ++ ;?>
				<?php if($count == 1) : ?>
					<li class="first-news <?php $ptype=get_post_type();if(!in_array($ptype, array('post','page'))){echo $ptype;} ?> ">
						<?php
						// tdc: draw the section image INDSIDE the first story
 							if ($cat_image != '') {
								echo '<div class="cat-box-title-image"><a href="'.get_category_link( $Cat_ID ).'">'.$cat_image.'</a></div>';
 							}
 						?>
						<?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() ) : ?>			
							<div class="post-thumbnail">
								<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark">
									<?php the_post_thumbnail( 'tie-large' ) ; ?>
									<?php tie_get_score( true ); ?>
								</a>
							</div><!-- post-thumbnail /-->
						<?php else: ?>
						<div class="empty-space"></div>
						<?php endif; ?>
						
							<h2 class="post-box-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
							<?php get_template_part( 'includes/boxes-meta' ); ?>
							<div class="entry">
								<p><?php tie_excerpt_home() ?></p>
								<a class="more-link" href="<?php the_permalink() ?>"><?php _e( 'Read More &raquo;', 'tie' ) ?></a>
							</div>
						</li><!-- .first-news -->
					<?php else: ?>
					<li class="other-news<?php echo $show_excerpt_css; ?> <?php $ptype=get_post_type();if(!in_array($ptype, array('post','page'))){echo $ptype;} ?> ">
						<?php if ( function_exists("has_post_thumbnail") && has_post_thumbnail() ) : ?>			
							<div class="post-thumbnail">
								<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark">
									<?php the_post_thumbnail( $cur_option ); ?>
									<?php tie_get_score(); ?>
								</a>
							</div><!-- post-thumbnail /-->
						<?php endif; ?>			
						<h3 class="post-box-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
						<?php get_template_part( 'includes/boxes-meta' ); ?>
						<?php
						// tdc: optional show excerpt on the rest of the items
 							if ($show_excerpt == 'y') : ?>
								<div class="entry non-first">
									<p><?php tie_excerpt_home() ?></p>
									<a class="more-link" href="<?php the_permalink() ?>"><?php _e( 'Read More &raquo;', 'tie' ) ?></a>
								</div>
						<?php endif; ?>
					</li>
					<?php endif; ?>
				<?php endwhile;?>
				</ul>
				<div class="clear"></div>

					<?php endif; ?>

				<?php
				// tdc: openx after widget
 					if ($after_openx) {
						echo do_shortcode(htmlspecialchars_decode('[openx zoneid="'.$after_openx.'"]'));
					}
 				// tdc: html after widget
 					if($after_html) {
						echo '<div class="after-widget-html">'.do_shortcode(htmlspecialchars_decode($after_html)).'</div>';
 					}
 				?>

			</div><!-- .cat-box-content /-->
		</section><!-- List Box -->

	<?php endif; ?>
	
	
<?php } ?>
