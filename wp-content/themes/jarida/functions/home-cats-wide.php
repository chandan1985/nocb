<?php 
function get_home_wide_cat( $cat_data ){ 
	$count = 0 ;
	$Cat_ID = $cat_data['id'];
	$offset = $Posts_num = $order = $rand = '';
	
	if( !empty($cat_data['number']) )
		$Posts_num = $cat_data['number'];
		
	if( !empty($cat_data['order']) )
		$order = $cat_data['order'];
	
	if( !empty($cat_data['offset']) )		
		$offset =  $cat_data['offset'];
		
	if( $order == 'rand') $rand ="&orderby=rand";

	$cat_query = new WP_Query('cat='.$Cat_ID.'&no_found_rows=1&posts_per_page='.$Posts_num.$rand.'&offset='.$offset);
	$cat_title = get_the_category_by_ID($Cat_ID);

	// tdc
	$show_excerpt = $cat_data['excerpt'];
 	$show_excerpt_css = ($show_excerpt == 'y' ? ' show-excerpt' : '' );
 	$after_openx = $cat_data['openx'.$i];
 	$after_html = $cat_data['text'.$i];
	
?>
		<section class="cat-box wide-cat-box tie-cat-<?php echo $Cat_ID ?>">
			<h2 class="cat-box-title"><a href="<?php echo get_category_link( $Cat_ID ); ?>"><?php echo $cat_title ; ?></a></h2>
			<div class="cat-box-content">
				<?php if($cat_query->have_posts()): ?>
				<?php while ( $cat_query->have_posts() ) : $cat_query->the_post(); $count++; ?>
					<div class="wide-news-item<?php if( $count == 4 ) echo ' last-col'; elseif( $count == 2 ) echo ' last-col2'; ?>">
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
					<?php if( $count == 4 ){ echo '<div class="clear"></div>'; $count=0;} ?>
				<?php endwhile;?>
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
<?php } 

function get_home_wide_scroll( $cat_data ){ ?>
	
<?php
    wp_enqueue_script( 'tie-cycle' );

	$Cat_ID = $cat_data['id'];
	$offset = $Posts = $Box_Title = '';
	
	if( !empty($cat_data['number']) )
		$Posts = $cat_data['number'];
		
	if( !empty($cat_data['offset']) )		
		$offset =  $cat_data['offset'];

	if( !empty($cat_data['title']) )		
		$Box_Title =  $cat_data['title'];

	$cat_query = new WP_Query('cat='.$Cat_ID.'&no_found_rows=1&posts_per_page='.$Posts.'&offset='.$offset); 
?>
		<section class="cat-box scroll-box scroll-box-wide tie-cat-<?php echo $Cat_ID ?>">
			<h2 class="cat-box-title"><a href="<?php echo get_category_link( $Cat_ID ); ?>"><?php if( function_exists('icl_t') ) echo icl_t( theme_name , $cat_data['boxid'] , $Box_Title); else echo $Box_Title ; ?></a></h2>

			<div class="cat-box-content">
			
				<?php if($cat_query->have_posts()): ?>
				<div class="group_items-box" id="wslideshow<?php echo $Cat_ID ?>">
				<?php while ( $cat_query->have_posts() ) : $cat_query->the_post()?>
					<div class="scroll-item">
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

						<h3 class="post-box-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
						<p class="post-meta">
							<?php tie_get_time() ?>
						</p>
					</div>
				<?php endwhile;?>
				<div class="clear"></div>
				</div>
				<div class="scroll-nav"><a id="nextw<?php echo $Cat_ID ?>" href="#"><i class="tieicon-right-open"></i></a><a class="prev-scroll" id="prevw<?php echo $Cat_ID ?>" href="#"><i class="tieicon-left-open"></i></a></div>
				<?php endif; ?>
			</div><!-- .cat-box-content /-->
		</section>
		<div class="clear"></div>


<script type="text/javascript">
	jQuery(document).ready(function() {
		var vids = jQuery("#wslideshow<?php echo $Cat_ID ?> .scroll-item");
		for(var i = 0; i < vids.length; i+=4) {
		  vids.slice(i, i+4).wrapAll('<div class="group_items"></div>');
		}
		jQuery(function() {
			jQuery('#wslideshow<?php echo $Cat_ID ?>').cycle({
				fx:     'scrollHorz',
				cleartypeNoBg: true,
				timeout: <?php if(get_option('tdc_jarida_slideshow_timeout')){ echo get_option('tdc_jarida_slideshow_timeout');} else {echo('3000');} ?>,
				//pager:  '#nav<?php echo $Cat_ID ?>',
				slideExpr: '.group_items',
				prev:   '#prevw<?php echo $Cat_ID ?>', 
				next:   '#nextw<?php echo $Cat_ID ?>',
				speed: <?php if(get_option('tdc_jarida_slideshow_speed')){ echo get_option('tdc_jarida_slideshow_speed');} else {echo('300');} ?>,
				pause: true
			});
		});
  });
</script>
	
<?php } 

function get_home_reviews( $cat_data ){ 

	$Posts = $Box_Title = $order = $rand = '';
	
	if( !empty($cat_data['number']) )
		$Posts = $cat_data['number'];
		
	if( !empty($cat_data['order']) )
		$order = $cat_data['order'];
	
	if( !empty($cat_data['title']) )
		$Box_Title =  $cat_data['title'];

	if( !empty($cat_data['link']) )
		$Box_Link =  $cat_data['link'];


	/*
	if( $order == 'rand') $orderby ="rand";
	elseif( $order == 'best') $orderby ="meta_value_num";
	else $orderby ="date";
	*/

	$order = 'date';
?>
	<section class="cat-box cats-review-box">
		<h2 class="cat-box-title"><?php if ($Box_Link) { echo '<a href="'.$Box_Link.'">'; }?><?php echo $Box_Title ; ?><?php if ($Box_Link) { echo '</a>'; }?></h2>

			<div class="cat-box-content">
			<?php $i=0;
				for($i=1 ; $i<=3 ; $i++ ){ 
				$id= 'id'.$i;
				$Cat_ID = $cat_data[$id];

				// tdc
				$show_excerpt = $cat_data['excerpt'];
 	  	 		$show_excerpt_css = ($show_excerpt == 'y' ? ' show-excerpt' : '' );
				$after_openx = isset($cat_data['openx'.$i]) ? $cat_data['openx'.$i] : '';
	 	  	 	$after_html = isset($cat_data['text'.$i]) ? $cat_data['text'.$i] : '';
			?> 
				<div class="reviews-cat">
					<h3><a href="<?php echo get_category_link( $Cat_ID ); ?>"><?php echo get_the_category_by_ID($Cat_ID); ?></a></h3>
					<?php $cat_query = new WP_Query(array('posts_per_page' => $Posts, 'orderby' => $order , 'cat' => $Cat_ID, 'post_status' => 'publish')); ?>
					<ul>
					<?php while ( $cat_query->have_posts() ) : $cat_query->the_post()?>
						<li><h4><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'tie' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h4> <?php tie_get_score( true ); ?></li>
					<?php endwhile;?>
					</ul>
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
				</div>			
				<?php } ?>

			</div><!-- .cat-box-content /-->
		</section>
<?php
}?>