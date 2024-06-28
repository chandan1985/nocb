<?php
/*
Template Name: Home Page - No Video
*/
get_header();
the_post();

global $DR_Pages;
?>
	
		<div id="left-content">
           <div id="event">
		           <?php 
            			the_content(); 
            		?>
            	<?php if($options['call_to_action']==1) { ?>
				<a class="awesome" href="<?php echo $options['cta_link']; ?>"><?php echo $options['cta_label']; ?> &raquo;</a>
				<?php } ?>

                </div>
                <div class="title">
                <?php the_block('EventDescription'); ?>
                </div>
                <div id="btm-buttons">
					<?php
					$options = get_option('theme_options');
					$buttonsArr = array($options['home_button1'], $options['home_button2'], $options['home_button3']);
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
	                <?php the_block('Winners'); ?>
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
                <?php the_block('NeedPhotos'); ?>
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
					$images =& get_children( 'post_type=attachment&post_mime_type=image&post_parent=' . $thisPostID );

					
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