<?php
/*
Template Name: Home Page 
*/
get_header();
the_post();

global $DR_Pages;
?>

<div id="left-content">
	<div id="event">
		<?php if(!empty($options['video'])) { ?>
			<?php if($options['videoType'] == "youtube") { ?>
				<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" style="height: 250px; width: 400px"><param name="movie" value="http://www.youtube.com/v/<?php echo $options['video']; ?>?version=3"><param name="wmode" value="transparent" /><param name="allowFullScreen" value="true"><param name="allowScriptAccess" value="always"><embed src="http://www.youtube.com/v/<?php echo $options['video']; ?>?version=3" type="application/x-shockwave-flash" allowfullscreen="true" allowScriptAccess="always" width="400" height="250" wmode="transparent"></object>	
				<?php } else { ?>
					<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="400" height="225" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0"><param name="quality" value="best" />
						<param name="allowfullscreen" value="true" /><param name="scale" value="showAll" /><param name="wmode" value="transparent" /><param name="src" value="http://vimeo.com/moogaloop.swf?clip_id=<?php echo $options['video']; ?>&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=&amp;fullscreen=1&amp;autoplay=0&amp;loop=0" /><embed type="application/x-shockwave-flash" width="400" height="225" src="http://vimeo.com/moogaloop.swf?clip_id=<?php echo $options['video']; ?>&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=&amp;fullscreen=1&amp;autoplay=0&amp;loop=0" wmode="transparent" scale="showAll" allowfullscreen="true" quality="best"></embed></object>				
					<?php } ?>
				<?php } ?>
				<div id="registration">
					<?php 
					the_content(); 
					?>
					<?php if(isset($options['call_to_action']) && $options['call_to_action']==1) { ?>
						<a class="awesome" href="<?php echo $options['cta_link']; ?>"><?php echo $options['cta_label']; ?> &raquo;</a>
					<?php } ?>
				</div>
			</div>
			<div class="title">
				<?php mcb_the_block('EventDescription'); ?>
			</div>
			<div id="btm-buttons">
				<?php
				$buttonsArr = array();
				$options = get_option('theme_options');
				if(isset($options['home_button1'])) {
					$buttonsArr[] = $options['home_button1'];
				}
				if(isset($options['home_button2'])) {
					$buttonsArr[] = $options['home_button2'];
				}
				if(isset($options['home_button3'])) {
					$buttonsArr[] = $options['home_button3'];
				}

				$count = 1;
				foreach($buttonsArr as $item) {
					if($item == 1){
						?>
						<a href="<?php echo $options['home_button_link'.$count]; ?>" class="awesome" style="margin-right:20px;"><?php echo $options['home_button_label'.$count]; ?></a>
						<?php
					}
					$count++;
				}
				?>
			</div>
			
			<div id="past-event" style="clear:both;">
				<div id="col1">
					<?php mcb_the_block('Winners'); ?>
					<form id="page-changer" action="" method="post">
						<select name="winnersNav">
							<option value="">Go to page:</option>
							<?php 
							$category_ID = get_cat_id('Winners');
							query_posts('cat='.$category_ID.'&showposts=200');
							while (have_posts()): the_post();
								?>
								<option value="<?php the_permalink() ?>"><?php the_title(); ?></option>
							<?php endwhile; wp_reset_query();?>
						</select>
						<input type="button" id="winnersNavSubmit" class="awesome" value="Go" />
					</form>
					
					<script type="text/javascript">
						$(function() {
							
							$("#winnersNavSubmit").click(function() {
								window.location = $("#page-changer select option:selected").val();
							})
							
						});
					</script>
					<?php mcb_the_block('NeedPhotos'); ?>
				</div>
				<div id="col2">
					<?php if($options['photo_gallery_title']==""): ?>
						<h1>Event Photos</h1>
						<?php else : ?>
							<h1><?php echo $options['photo_gallery_title'] ?></h1>
						<?php endif; ?>
						<div id="photos">
							<?php 
							$thisPostID = $post->ID;
							$images = get_children( 'post_type=attachment&post_mime_type=image&post_parent=' . $thisPostID );

							
							if ( empty($images) ) {
						// no attachments here
							} else {
								foreach ( $images as $attachment_id => $attachment ) {
									echo wp_get_attachment_image( $attachment_id, 'full' );
								}
							}
							
							?>

						</div>

						
						<ul id="controls">
							<li><a href="#prev" id="prev" class="small silver awesome">&laquo; Previous</a></li>
							<li><a href="#next" id="next" class="small silver awesome">Next &raquo;</a></li>
						</ul>
					</div>
				</div>
			</div>
			
			<?php get_sidebar(); ?>

			<?php get_footer(); ?>