<?php
/*
 * Template Name: Slideshow Detail
 * Template Post Type: post
 */
?>

<?php get_header(); 

$ids = [];
$id = $post->ID;
$ids[] = $id;


$date = $post->post_date;
$date = date('F j, Y', strtotime($date));
$custom_byline = get_post_meta( $id, 'custom_byline', true );
$author = get_post_meta( $id, 'byline', true );
if(!$custom_byline){
	$author = get_post_meta( $id, 'byline', true );
	if($author){
		$display_name = get_the_author_meta('display_name', $author);
		$author_link = get_author_posts_url($author);
	}else{
		$author = $post->post_author;
		$display_name = get_the_author_meta('display_name', $author);
		$author_link = get_author_posts_url($author);
	}
}elseif($custom_byline[0] == 1){
	$byline_author = get_post_meta( $id, 'byline', true );
	if(!$byline_author){
		$author = $post->post_author;
		$display_name = get_the_author_meta('display_name', $author);
		$author_link = get_author_posts_url($author);
	}
}


//echo "Hello World"; exit();
wp_enqueue_style('ad-gallery-style', get_stylesheet_directory_uri() . '/css/slideshow/jquery.ad-gallery.css');
?>
<?php //tie_setPostViews() ?>

<?php $article_classes = array('post-listing','hnews','hentry','item');?>
<div class="container">
	<div class="row">

        <?php
        
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

		<?php
		$do_not_duplicate = array();

		while ( have_posts() ) : the_post();
			$get_meta = get_post_custom($post->ID);
			if( !empty( $get_meta['tie_review_position'][0] ) ){
				$review_position = $get_meta['tie_review_position'][0] ;
				$rv = $tie_reviews_attr;
			}
			
			if( !empty( $get_meta["tie_sidebar_pos"][0] ) && $get_meta["tie_sidebar_pos"][0] == 'full' ){
				if(tie_get_option( 'columns_num' ) == '2c') $content_width = 955;
				else $content_width = 1160;
			}
		?>
				
		<article id="the-post" <?php if( !empty( $rv['review'] ) ) echo $rv['review']; post_class($article_classes); ?>>			
			<div class="d-block d-sm-block d-md-none mobile-social">
				<?php //echo do_shortcode('[wpsr_button id="7320"]');?> Share
				<?php //echo do_shortcode('[bws_pdfprint display="print"]');?>
				<div class="pdfprnt-buttons">
					<a href="javascript:void();" onclick="window.print();" class="pdfprnt-button pdfprnt-button-print" target="_blank">
						<span class="pdfprnt-button-title pdfprnt-button-print-title">Print</span>
					</a>
				</div>
				
			</div>
			<!--<div class="clearfix"></div>-->
			<div class="post-inner row">
				<div class="col-sm-9">
				<?php $categories = get_the_category(); 
				$vanguard_series = false;
				foreach ($categories as $category) {
					if($category->slug == 'vanguard-series') {
						$vanguard_series = true;
						break;
					}
				}
				?>
				<?php if(!$vanguard_series) { ?>
					<h1 class="name post-title entry-title"><span>PIC PAGE</span></h1>
				<?php } ?>
				<?php if( !$get_meta["tie_hide_title"][0] ){ ?>
					<div class="breadcrumb"><?php asentech_custom_breadcrumb(); ?></div>
					<h2 class="name post-title entry-title" itemprop="itemReviewed" itemscope itemtype="http://schema.org/Thing"><span itemprop="name"><?php the_title(); ?></span></h2>
				<?php } ?>
				<?php if( get_field('sub_heading_title') ) {?>
					<p ><?php echo get_field('sub_heading_title'); ?></p>
				<?php } ?>
				<?php //get_template_part( 'includes/post-meta' ); // Get Post Meta template ?>	
				
					<?php if( !empty( $get_meta['subhead'][0] ) ){ ?>
						<h2 class="subhead"><?php echo $get_meta['subhead'][0]; ?></h2>
					<?php } ?>

					<div class="row">
					<div class="col-sm-9 news-publish-info">
						<?php if($byline_author){
							print '<p><b>'.$byline_author.'</b><span>//</span>'.$date.'</p>';
						}else{
							print '<p><a href="'.$author_link.'"><b>'.$display_name.'</b></a><span>//</span>'.$date.'</p>';
						}?>
					</div>
					<div class="col-sm-3 social-share-links">
						<?php

						$articleURL = urlencode(get_the_permalink($id));
						$articleTitle = htmlspecialchars(urlencode(html_entity_decode(get_the_title($id), ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8');
						$crunchifyTitle = str_replace( ' ', '%20', get_the_title($id));
						$Thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'full' );
						
						?>
							<meta property='og:title' content='<?php print $articleTitle ?>'/>
							<meta property='og:image' content='<?php print $Thumbnail[0] ?>'/>
							<!-- <meta property='og:description' content='Description '/> -->
							<meta property='og:url' content='<?php print $articleURL ?>' />
						<?php
						
						// Construct sharing URL without using any script
						$twitterURL = 'https://twitter.com/intent/tweet?text='.$articleTitle.'&amp;url='.$articleURL;
						// $facebookURL = 'https://www.facebook.com/sharer/sharer.php?u='.$articleURL;
						$facebookURL = 'https://www.facebook.com/sharer.php?u='.$articleURL.'&t='.$articleTitle;
						$facebookURL = urldecode($facebookURL);
						$linkedInURL = 'https://www.linkedin.com/shareArticle?mini=true&url='.$articleURL.'&title='.$articleTitle;
						// $linkedInURL = 'http://www.linkedin.com/shareArticle?mini=true&url='.$articleURL;
						$linkedInURL = urldecode($linkedInURL);
						// print $linkedInURL;

				 
						// Based on popular demand added Pinterest too
						$pinterestURL = 'https://pinterest.com/pin/create/button/?url='.$articleURL.'&amp;media='.$Thumbnail[0].'&amp;description='.$crunchifyTitle;
						$eventContent = wp_trim_words(strip_shortcodes(get_the_content($post_id)), 30, '...')."\r\n You can read the content in details following link ".$articleURL; // get short content of event
						
						echo '<ul class="social-share-links">';
						echo '<li><a class="twitter" href="'. $twitterURL .'" target="_blank"><img src="'.get_template_directory_uri().'/images/twitter.svg" alt="twitter"></a></li>';
						echo '<li><a class="facebook" href="'.$facebookURL.'" target="_blank"><img src="'.get_template_directory_uri().'/images/facebook.svg" alt="facebook"></a></li>';
						echo '<li><a class="linkedin" href="'.$linkedInURL.'" target="_blank"><img src="'.get_template_directory_uri().'/images/linkedin.svg" alt="linkedin"></a></li>';
						echo '<li><a class="crunchify-link crunchify-pinterest" href="'.$pinterestURL.'" data-pin-custom="true" target="_blank"><img src="'.get_template_directory_uri().'/images/pinterest.svg" alt="pinterest"></a></li>';
						echo '<li><a class="email" href="mailto:?subject='.get_the_title($post_id).'&amp;body='.esc_html($eventContent).'" title="Share by Email"><img src="'.get_template_directory_uri().'/images/email.svg" alt="email"></a></li>';
						echo '</ul>';
						?>
					</div>
				</div>
					</br>
					</br>
					<?php //get_template_part( 'includes/post-head' ); // Get Post Head template ?>	
					<div class="entry entry-content">

						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'tie' ), 'after' => '</div>' ) ); ?>
						<?php if( have_rows('ss_slideshow_images') ) {

							/*if ( is_active_sidebar( 'slideshow-ads-code' ) ) :
								ob_start();
								dynamic_sidebar("slideshow-ads-code");
								$dfp_ad_code = ob_get_contents();
								ob_end_clean();
								$dfp_ad_code = trim($dfp_ad_code);
								if($dfp_ad_code != "") $dfp_ad_code = 'div-gpt-ad-'.strtolower($dfp_ad_code);
							endif;*/

							$dfp_ad_code = get_field('ss_slideshow_ads_code');
							$dfp_ad_code = trim($dfp_ad_code);
							if($dfp_ad_code != "") $dfp_ad_code = 'div-gpt-ad-'.strtolower($dfp_ad_code);
							?>
							
							<div id="gallery" class="ad-gallery">
								<div class="ad-image-wrapper">
								</div>
								<div class="ad-controls">
								</div>
								<div class="ad-nav">
									<div class="ad-thumbs">
									<ul class="ad-thumb-list">
										<?php 
											$i = 0;

											//get count of slideshow
											$ad_index = -1;
											if(have_rows('ss_slideshow_images')) {
												$ad_index = 0;
												while( have_rows('ss_slideshow_images') ): the_row(); 
													$ad_index++;
												endwhile;												
												$ad_index = $ad_index / 2;
												$ad_index = ceil($ad_index);
												if($ad_index >= 5){
													$ad_index = 5;
												}
											}

											wp_reset_query();
											while( have_rows('ss_slideshow_images') ): the_row(); 
											$slideshow_ads_code = get_sub_field('ss_ads_code');
											// vars
											$img_title = get_sub_field('ss_image_title');
											$ss_title = !empty($img_title) ? '<h6>'.$img_title.'</h6></br>' : '';
											$img_content = get_sub_field('ss_image_description');										
											$img_image = get_sub_field('ss_image_upload');
											
												
											// vars
											$img_full_url = wp_get_attachment_image_src( $img_image, 'full' );
											// thumbnail
											
											$img_thumb_url = wp_get_attachment_image_src( $img_image, 'slideshow-thumbnail' , true);
											
											if($i == $ad_index && $dfp_ad_code!="")
											{
											?>       
										
												<li style="visibility: hidden; width: 0px; height: 0px" id="google-add">
													<a href="<?php echo $img_full_url[0]; ?>">
														<img src="<?php echo $img_thumb_url[0]; ?>" title="" alt="" class="image1">
													</a>
												</li>
											<?php 
											}
											//echo "HIII123";
											if($img_image != "")
											{ 
										?>       
										
												<li>
													<a href="<?php echo $img_full_url[0];?>">
														<img src="<?php echo $img_thumb_url[0];?>" title="<?php echo !empty($img_title) ? esc_html($img_title) : ''; ?>" alt="<?php echo esc_html($img_content);?>" class="image1">
													</a>
												</li>
											<?php 
												//}
											}
										?>
										<?php
											$i++;
											endwhile;
										?>
										
										
										
									</ul>
									
									</div>
								</div>
								</div>
								<div id="descriptions"></div>
								
							<?php } ?>
							<?php
							$slideshow_second_content = get_field('slideshow_bottom_description');
							if($slideshow_second_content!="")
							{
							?>
								<div class="sub_disc_wrap"><?php echo $slideshow_second_content;?></div>
							<?php
							}
							?>
						

						<?php edit_post_link( __( 'Edit', '' ), '<span class="edit-link">', '</span>' ); ?>
            
          </div><!-- .entry /-->
         
					<?php if( is_active_sidebar('slideshow-send-your-photo' )){ ?>
						<div class="slideshow-send-your-photo-wrap"><?php dynamic_sidebar( 'slideshow-send-your-photo' ) ; ?> </div>
					<?php } ?>

					<div class="related-content-section"><?php asentech_related_contents(); ?></div>				

					<?php the_tags( '<span style="display:none">',' ', '</span>'); ?>
					<span style="display:none" class="updated" title="<?php the_time('Y-m-d');echo 'T';the_time('H:i:s');echo 'Z'; ?>"><?php the_time('g:i a D, F j, Y'); ?></span>
					<?php
					$ap_userids = explode(',', get_option('tdc_jarida_ap_userids'));
					$pub_userids = explode(',', get_option('tdc_jarida_pub_userids'));

					$author_class = 'vcard author';
					if (in_array(get_the_author_meta('ID'),$ap_userids) || in_array(get_the_author_meta('ID'),$pub_userids)) {
						$author_class .= ' source-org';
					} else {
						echo '<span class="source-org vcard" style="display:none;"><a class="org fn" href="'.get_bloginfo('url').'">'.get_bloginfo('name').'</a></span>';
					}

					if (get_option('tdc_jarida_ap_bug')) {
						if (in_array(get_the_author_meta('ID'),$ap_userids)) {
							echo '<p class="ap-license"><a rel="item-license" href="#APRights" id="APRights">Copyright '.date('Y').' The Associated Press. All rights reserved. This material may not be published, broadcast, rewritten, or redistributed.</a></p>';
							echo '<span class="ap-bug" style="display:none;"><img src="http://analytics.apnewsregistry.com/analytics/v2/image.svc/AP/RWS/'.get_option('tdc_jarida_pub_domain').'/MAI/post-'.get_the_ID().'"></span>';
						} else {
							$our_content = get_post_meta($post->ID, 'we_own_it');
							if ($our_content[0] == 'Yes') {
								echo '<span class="ap-bug" style="display:none;"><img src="http://analytics.apnewsregistry.com/analytics/v2/image.svc/'.get_option('tdc_jarida_pub_code').'/RWS/'.get_option('tdc_jarida_pub_domain').'/CAI/post-'.get_the_ID().'"></span>';
							}
						}
					}
					?>
					<?php if ( get_the_author_meta( 'google' ) ){ ?>
					<div style="display:none" class="<?php echo $author_class; ?>" itemprop="author" itemscope itemtype="http://schema.org/Person"><strong class="fn" itemprop="name"><a href="<?php the_author_meta( 'google' ); ?>?rel=author">+<?php echo get_the_author(); ?></a></strong></div>
					<?php }else{ ?>
					<div style="display:none" class="<?php echo $author_class; ?>" itemprop="author" itemscope itemtype="http://schema.org/Person"><strong class="fn" itemprop="name"><?php the_author_posts_link(); ?></strong></div>
					<?php } ?>
					
		
						<?php the_terms( $post->ID, 'issues', '<p class="post-issue post-tag">'.__( 'Issue: ', 'tie' ), ' ', '</p>' ); ?>

						
						
						<?php get_template_part( 'includes/post-related' ); // Get Related Posts template ?>

						<?php //if( !$get_meta["tie_hide_comments"][0] ){ ?>
						<?php //comments_template( '', true ); ?>
						<?php //} ?>
						
				</div> <!--end left div-->

				<div class="col-sm-3 desktop-show">
					<?php dynamic_sidebar('right-sidebar-area'); ?>
				</div> <!-- .right div -->
			</div><!-- .post-inner -->
		</article><!-- .post-listing -->
			

		<?php endwhile;?>

	
	</div><!-- .row -->
	</div> <!-- .container -->


<?php wp_enqueue_script('ad-gallery-js', get_stylesheet_directory_uri() . '/js/slideshow/jquery.ad-gallery.js'); ?>

<script async='async' src='https://www.googletagservices.com/tag/js/gpt.js'></script>

<script type="text/javascript">
	
jQuery( document ).ready(function() {
	var ad_index = <?php echo $ad_index; if(!$ad_index){$ad_index = "test";} ?>;
    //alert(jQuery(".ad-thumb-list").children().length);
	var ad_code = '<?php echo $dfp_ad_code; ?>';
	/* googletag.cmd.push(function() { 
		googletag.display(ad_code); 
	}); */


    var galleries = jQuery('.ad-gallery').adGallery({
		   
		"description_wrapper":jQuery('#descriptions'),
		
		"loader_image": '<?php echo get_stylesheet_directory_uri().'/css/slideshow/loader.gif';?>',
		/* "slideshow": {
                   		"autostart": true                  
                 	 }, */
		"effect": 'fade',			
		"update_window_hash": false,
		"callbacks": {
			"beforeImageVisible": function(new_image, old_image) {
			// Do something wild!
				var current_index = jQuery(".ad-thumb-list").find(".ad-active").parent().index();
				var current_index_image = jQuery(".ad-thumb-list .ad-active").attr('href');

				if(ad_index == current_index && ad_code !="")
				{
					/*jQuery(".ad-image-wrapper .ad-image").html("");
					jQuery(".ad-image-wrapper .ad-image").attr("id", ad_code);*/
					/* var googletag = googletag || {};
						googletag.cmd = googletag.cmd || [];
					googletag.cmd.push(function() {
						googletag.defineSlot('/72381705/RIGHT6', [300, 250], 'div-gpt-ad-1546924352997-0').addService(googletag.pubads());
						googletag.pubads().enableSingleRequest();
						googletag.enableServices();
					}); */

					//refreshAds();

					/*googletag.cmd.push(function () {
						var slot = googletag.display(ad_code);
						googletag.pubads().refresh([slot]);
					});*/

					//jQuery(".slideshow-ad").show();
					jQuery(".slideshow-ad").css('display', 'flex');
					jQuery(".ad-image-wrapper .ad-image").hide();
				}
				else
				{
					jQuery(".slideshow-ad").hide();
					jQuery(".ad-image-wrapper .ad-image").show();
					jQuery(".ad-image-wrapper .ad-image").html('<img src="'+current_index_image+'">');
				}

				jQuery('#descriptions').css("visibility","hidden");
				setTimeout(function(){
					jQuery('#descriptions').css("visibility","visible");
					jQuery('#descriptions').hide();
					jQuery('#descriptions').fadeIn("slow");
				},500);
			},
			"afterImageVisible": function() {
				if(ad_index != -1 && ad_code!="") {
					if(this.current_index < ad_index)
						this.gallery_info.html((this.current_index + 1) +' / '+ (this.images.length - 1));
					else
						this.gallery_info.html((this.current_index) +' / '+ (this.images.length - 1));
				}

				resizeSlideshow();
			},
		}
	});

    //code to show dfp ads in slideshow
  	if(ad_code != "") {

	  	galleries[0].image_wrapper.append('<div id="<?php echo $dfp_ad_code; ?>" class="slideshow-ad"></div>');
	  	googletag.cmd.push(function() {
			googletag.display('<?php echo $dfp_ad_code; ?>');
		});

	  	setTimeout(function() {
			var style = "width: 300px; height: 250px; position: absolute; display: -ms-flexbox; display: flex; -ms-flex-wrap: wrap; flex-wrap: wrap; justify-content: center; align-items: center; -ms-flex-pack: center;";

			var ad_image_wrapper = jQuery(document).find(".ad-image-wrapper");
			var ad_image_wrapper_width = ad_image_wrapper.width();
			var ad_image_wrapper_height = ad_image_wrapper.height();
			
			var image_left_space = ad_image_wrapper_width - 300;
			if(image_left_space < 0) image_left_space = 0;
			style += "left: "+(image_left_space / 2)+"px;";

			var image_top_space = ad_image_wrapper_height - 250;
			if(image_top_space < 0) image_top_space = 0;
			style += "top: "+(image_top_space / 2)+"px;";
		  	
		  	jQuery(".slideshow-ad").attr('style', style);
			jQuery(".slideshow-ad").hide();
		}, 2000);

	}
  
});

jQuery( window ).resize(function() {

	resizeSlideshow();

});

function resizeSlideshow() {

	var ad_image_wrapper = jQuery(document).find(".ad-image-wrapper");
	var ad_image_wrapper_width = ad_image_wrapper.width();
	var ad_image_wrapper_height = ad_image_wrapper.height();

	if(jQuery(".slideshow-ad").is(":visible")) {

		var slideshow_ad = jQuery(document).find(".slideshow-ad");

		var image_left_space = ad_image_wrapper_width - 300;
		if(image_left_space < 0) image_left_space = 0;
		slideshow_ad.css("left", (image_left_space / 2));

		var image_top_space = ad_image_wrapper_height - 250;
		if(image_top_space < 0) image_top_space = 0;
		slideshow_ad.css("top", (image_top_space / 2));

	} else {

		var ad_image = jQuery(document).find(".ad-image");

		var ad_image_width = ad_image.width();
		var ad_image_height = ad_image.height();

		var image_left_space = ad_image_wrapper_width - ad_image_width;
		if(image_left_space < 0) image_left_space = 0;
		ad_image.css("left", (image_left_space / 2));

		var image_top_space = ad_image_wrapper_height - ad_image_height;
		if(image_left_space < 0) image_left_space = 0;
		ad_image.css("top", (image_top_space / 2));

	}

}
</script>
<?php get_footer(); ?>