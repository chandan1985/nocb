<?php get_header(); 

$ids = [];
$id = $post->ID;
$ids[] = $id;
$date = $post->post_date;
$date = date('F j, Y', strtotime($date));
$title = get_the_title();

$author = $post->post_author;
$display_name = get_the_author_meta('display_name', $author);
$author_link = get_author_posts_url($author);

$config = unserialize(get_option('tdc_sponsored_content'));
$sponsored_content_text = $config['heading_text_on_sponsored_page'];
$sponsor = get_post_meta($post->ID, 'meta_sc_sponsor', true);
$img = get_post_meta($post->ID, 'meta_sc_sponsor_logo', true);
$blurb = get_post_meta($post->ID, 'meta_sc_sponsor_blurb', true);
// data is coming from plugin
$footer_bottom_ad = get_option('footer_bottom_ad');
$mobile_footer_bottom_ad = get_option('mobile_footer_bottom_ad');
$footer_hide_from_pages = get_option('footer_hide_from_pages');
$footer_hide_from_post_type = get_option('footer_hide_from_post_type');
global $post;
$current_post_type = get_post_type($post);
?>


<div class="mobile-specific-data">
	<div class="sponsored-content-heading mobile-show"><?php print $sponsored_content_text; ?></div>
	<div class="mobile-news">
		<h2 class="news-page-header"><?php print $title; ?> </h2>
	</div>
	<?php if (has_post_thumbnail($post->ID)) : ?>
		<div class="mobile-news-image"><?php echo get_the_post_thumbnail( $post->ID, 'article-detail-thumb' ); ?></div>
	<?php endif; ?>

</div>



<div class="article-detail-page container">

	<!-- Start row -->
	<div class="row">
		<!-- Start col-sm-9 -->
		<div class="col-sm-9">

			<div class="newsdetail-wrapper">
				
				<div class="breadcrumb"><?php asentech_custom_breadcrumb(); ?></div>

				<div class="sponsored-content-heading desktop-show"><?php print $sponsored_content_text; ?></div>

				<?php
				$show_featured_image = get_post_meta( $id, 'show_featured_image', true );
				?>
				<div class="news-image">
					<?php echo get_the_post_thumbnail( $post->ID, 'article-detail-thumb' ); ?>
				</div>


				<h1 class="news-page-header"><?php print $title; ?> </h1>
				<div class="row">
					<div class="col-sm-9 news-publish-info">
						<p><a href="<?php print $author_link;?>"><b><?php print $display_name; ?></b></a><span>//</span><?php print $date; ?></p>
					</div>
					<div class="col-sm-3 social-share-links">
						<?php

						$articleURL = urlencode(get_the_permalink($id));
						$articleTitle = htmlspecialchars(urlencode(html_entity_decode(get_the_title($id), ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8');
						
						// Construct sharing URL without using any script
						$twitterURL = 'https://twitter.com/intent/tweet?text='.$articleTitle.'&amp;url='.$articleURL;
						$facebookURL = 'https://www.facebook.com/sharer/sharer.php?u='.$articleURL;
						$linkedInURL = 'https://www.linkedin.com/shareArticle?mini=true&url='.$articleURL.'&amp;title='.$articleURL;
				 
						// Based on popular demand added Pinterest too
						$pinterestURL = 'https://pinterest.com/pin/create/button/?url='.$articleURL.'&amp;media='.$Thumbnail[0].'&amp;description='.$crunchifyTitle;
						$eventContent = wp_trim_words(strip_shortcodes(get_the_content($post_id)), 30, '...')."\r\n You can read the content in details following link ".$articleURL; // get short content of event
						$eventContent = strip_tags($eventContent);
						$post_title = get_the_title($post_id);
						$post_title = strip_tags($post_title);

						echo '<ul class="social-share-links">';
						echo '<li><a class="twitter" href="'. $twitterURL .'" target="_blank"><img src="/wp-content/themes/neworleanscitybusiness/images/twitter.svg" alt="twitter"></a></li>';
						echo '<li><a class="facebook" href="'.$facebookURL.'" target="_blank"><img src="/wp-content/themes/neworleanscitybusiness/images/facebook.svg" alt="facebook"></a></li>';
						echo '<li><a class="linkedin" href="'.$linkedInURL.'" target="_blank"><img src="/wp-content/themes/neworleanscitybusiness/images/linkedin.svg" alt="linkedin"></a></li>';
						echo '<li><a class="crunchify-link crunchify-pinterest" href="'.$pinterestURL.'" data-pin-custom="true" target="_blank"><img src="/wp-content/themes/neworleanscitybusiness/images/pinterest.svg" alt="pinterest"></a></li>';
						echo '<li><a class="email" href="mailto:?subject='.esc_html($post_title).'&amp;body='.esc_html($eventContent).'" title="Share by Email"><img src="/wp-content/themes/neworleanscitybusiness/images/email.svg" alt="email"></a></li>';
						echo '</ul>';
						?>
					</div>
				</div>
				<div class="news-detail"><?php the_content(); ?></div>

			<?php  if(!wp_is_mobile()){ ?>	
				<?php if($sponsor){ ?>
					<section class="print-related-content">
						<div class="section-title">
							<h2 class="river-heading section-title__heading">Brought to you by our Sponsor: 
								<?php 
									global $post;
									echo '<i>' . $sponsor . '</i>';
								?> 
							</h2>
							<div class="tdc_SC_sponsor_identity">
							<?php
								global $post;
								if (!empty($img)) {
									echo '<img src="' . $img . '" class="tdc_SC_sponsor_logo"/>';
								}
								if (!empty($blurb)) {
									echo '<div class="blurb">' . $blurb . '</div>';
								}
							?>
							</div>
						</div>
					</section><!-- print-related-content -->
				<?php } ?>

				<div class="related-content-section">
					<?php	
						global $post;

						$args = array(
							'posts_per_page' => 3,
							'paged' => 0,
							'post_type'    => 'sponsored_content',
							'orderby' => 'date',
							'order' => 'DESC',
							// 'no_found_rows' => true,
							'post__not_in' => array( $post->ID ),
							'meta_query' => array(
								'relation' => 'AND',
								array(
									'key' => 'meta_sc_sponsor',
									'value' => $sponsor,
									'compare' => 'LIKE' // Meta query to filter events which are not bridge Tower Media
								),
							)
						);
						$query = new WP_Query($args);

						if ($query->found_posts > 0) {
							if($sponsor){
								echo '<h2 class="river-heading section-title__heading">More content from: <i>'.$sponsor.'</i></h2>';
							}else{
								echo '<h2 class="river-heading section-title__heading">More content from:</h2>';
							}
							echo '<div class="top-news-section related-content">';

									foreach ($query->posts as $sc_post) {
										$id = $sc_post->ID;
										$title = get_the_title($id);
										$short_title = substr($title, 0, 100)."...";
										$slug = get_permalink( $id);
										$featured_image = get_the_post_thumbnail( $sc_post->ID, 'article-list-thumb' );
										$content = get_the_content($id);
										$content = substr($content, 0, 150);
										$content = strip_tags($content);
										$short_content = substr($content, 0, 110)."[...]";
										$excerpt = get_the_excerpt($id);
										$short_excerpt = substr($excerpt, 0, 110)."[...]";
										$post_date = $sc_post->post_date;
                						$post_date = date('F j, Y', strtotime($post_date));
										?>

										<div class="content-box">
											<?php
												if ($featured_image) { 
												?>
												<a href="<?php print $slug; ?>"><div class="rc-wrapper">
												<?php } else { ?>
													<a href="<?php print $slug; ?>"><div class="rc-wrapper without-image">
													<?php } ?>
															<div class="rc-img">
																<?php print $featured_image; ?>
															</div>
															<div class="rc-content-wrap">
																	<?php
																	if(strlen($title) > 100){
																		print '<div class="rc_title_wrap"><h3 class="rc-img-title">'.$short_title.'</h3></div>';
																	}else{
																		print '<div class="rc_title_wrap"><h3 class="rc-img-title">'.$title.'</h3></div>';
																	}
																?>
																<p class="rc-img-content">
																	<?php
																	if($excerpt){
																		if(strlen($excerpt) > 110){
																			print $short_excerpt;
																		}else{
																			print $excerpt;
																		}
																	}else{
																		if(strlen($content) > 110){
																			print $short_content;
																		}else{
																			print $content;
																		}
																	}
																?></p>
																<p><b><?php print $post_date; ?></b></p>
															</div>
														</div>
													
												</a>
										</div> 

										<?php
									}

							echo '</div>';
						}			
					?>
				</div>
			<?php } ?>

				<div class="mobile-specific-data">

				<?php if(wp_is_mobile()){ ?> 
					<div class="sponsored-ad-after-content"><?php dynamic_sidebar('sponsored-ad-after-content'); ?></div> 
				<?php } ?>

					<div class="print-related-content">
						<div class="section-title" style="display:none;">
							<h2 class="river-heading section-title__heading">Brought to you by our sponsor:</h2>
						</div>
						<div class="content-bottom-sponsored-page"><?php dynamic_sidebar('content-bottom-sponsored-page'); ?></div>
					</div>	

					<?php  if(wp_is_mobile()){ ?>
						<?php if($sponsor){ ?>
							<section class="print-related-content">
								<div class="section-title">
									<h2 class="river-heading section-title__heading">Brought to you by our Sponsor: 
										<?php 
											global $post;
											echo '<i>' . $sponsor . '</i>';
										?> 
									</h2>
									<div class="tdc_SC_sponsor_identity">
									<?php
										global $post;
										if (!empty($img)) {
											echo '<img src="' . $img . '" class="tdc_SC_sponsor_logo"/>';
										}
										if (!empty($blurb)) {
											echo '<div class="blurb">' . $blurb . '</div>';
										}
									?>
									</div>
								</div>
							</section><!-- print-related-content -->
						<?php } ?>

						<div class="related-content-section">
					<?php	
						global $post;

						$args = array(
							'posts_per_page' => 3,
							'paged' => 0,
							'post_type'    => 'sponsored_content',
							'orderby' => 'date',
							'order' => 'DESC',
							// 'no_found_rows' => true,
							'post__not_in' => array( $post->ID ),
							'meta_query' => array(
								'relation' => 'AND',
								array(
									'key' => 'meta_sc_sponsor',
									'value' => $sponsor,
									'compare' => 'LIKE' // Meta query to filter events which are not bridge Tower Media
								),
							)
						);
						$query = new WP_Query($args);

						if ($query->found_posts > 0) {

							if($sponsor){
								echo '<h2 class="river-heading section-title__heading">More content from: <i>'.$sponsor.'</i></h2>';
							}else{
								echo '<h2 class="river-heading section-title__heading">More content from:</h2>';
							}


							echo '<div class="top-news-section related-content">';

									foreach ($query->posts as $sc_post) {
										$id = $sc_post->ID;
										$title = get_the_title($id);
										$short_title = substr($title, 0, 100)."...";
										$slug = get_permalink( $id);
										$featured_image = get_the_post_thumbnail( $sc_post->ID, 'article-list-thumb' );
										$content = get_the_content($id);
										$content = substr($content, 0, 150);
										$content = strip_tags($content);
										$short_content = substr($content, 0, 110)."[...]";
										$excerpt = get_the_excerpt($id);
										$short_excerpt = substr($excerpt, 0, 110)."[...]";
										$post_date = $sc_post->post_date;
                						$post_date = date('F j, Y', strtotime($post_date));
										?>

										<div class="content-box">
											<?php
												if ($featured_image) { 
												?>
												<a href="<?php print $slug; ?>"><div class="rc-wrapper">
												<?php } else { ?>
													<a href="<?php print $slug; ?>"><div class="rc-wrapper without-image">
													<?php } ?>
															<div class="rc-img">
																<?php print $featured_image; ?>
															</div>
															<div class="rc-content-wrap">
																	<?php
																	if(strlen($title) > 100){
																		print '<div class="rc_title_wrap"><h3 class="rc-img-title">'.$short_title.'</h3></div>';
																	}else{
																		print '<div class="rc_title_wrap"><h3 class="rc-img-title">'.$title.'</h3></div>';
																	}
																?>
																<p class="rc-img-content">
																	<?php
																	if($excerpt){
																		if(strlen($excerpt) > 110){
																			print $short_excerpt;
																		}else{
																			print $excerpt;
																		}
																	}else{
																		if(strlen($content) > 110){
																			print $short_content;
																		}else{
																			print $content;
																		}
																	}
																?></p>
																<p><b><?php print $post_date; ?></b></p>
															</div>
														</div>
													
												</a>
										</div> 

										<?php
									}

							echo '</div>';
						}			
					?>
				</div>
					<?php } ?>

					<div class="share-links">
						<?php
						if($permalink==""){
						// Get current page URL 
							$articleURL = urlencode(get_the_permalink($id));
						}else{
							$articleURL = urlencode($permalink);
						}
						$articleTitle = htmlspecialchars(urlencode(html_entity_decode(get_the_title($id), ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8');
						
						// Construct sharing URL without using any script
						$twitterURL = 'https://twitter.com/intent/tweet?text='.$articleTitle.'&amp;url='.$articleURL;
						$facebookURL = 'https://www.facebook.com/sharer/sharer.php?u='.$articleURL;
						$linkedInURL = 'https://www.linkedin.com/shareArticle?mini=true&url='.$articleURL.'&amp;title='.$articleURL;
				 
						// Based on popular demand added Pinterest too
						$pinterestURL = 'https://pinterest.com/pin/create/button/?url='.$articleURL.'&amp;media='.$Thumbnail[0].'&amp;description='.$crunchifyTitle;
						$eventContent = wp_trim_words(strip_shortcodes(get_the_content($post_id)), 30, '...')."\r\n You can read the content in details following link ".$articleURL; // get short content of event
						$eventContent = strip_tags($eventContent);
						$post_title = get_the_title($post_id);
						$post_title = strip_tags($post_title);
						

						?>
						<p class="share">Share this!</p>
						<?php
						echo '<ul class="social-share-links">';
						echo '<li><a class="twitter" href="'. $twitterURL .'" target="_blank"><img src="/wp-content/themes/neworleanscitybusiness/images/twitter.svg" alt="twitter"></a></li>';
						echo '<li><a class="facebook" href="'.$facebookURL.'" target="_blank"><img src="/wp-content/themes/neworleanscitybusiness/images/facebook.svg" alt="facebook"></a></li>';
						echo '<li><a class="linkedin" href="'.$linkedInURL.'" target="_blank"><img src="/wp-content/themes/neworleanscitybusiness/images/linkedin.svg" alt="linkedin"></a></li>';
						echo '<li><a class="crunchify-link crunchify-pinterest" href="'.$pinterestURL.'" data-pin-custom="true" target="_blank"><img src="/wp-content/themes/neworleanscitybusiness/images/pinterest.svg" alt="pinterest"></a></li>';
						echo '<li><a class="email" href="mailto:?subject='.esc_html($post_title).'&amp;body='.esc_html($eventContent).'" title="Share by Email"><img src="/wp-content/themes/neworleanscitybusiness/images/email.svg" alt="email"></a></li>';
						echo '</ul>';
						?>
					</div>
					<div class="right-sidebar-mobile"><?php 
					if(wp_is_mobile()){
						dynamic_sidebar('article-right-sidebar-mobile'); 	
					}
						?></div>
				</div>

							    
			</div>


		</div>
			<!-- End col-sm-9 -->

			<!-- Start col-sm-3 -->
			<div class="col-sm-3">
				<?php 
				 if(!wp_is_mobile()){
				 dynamic_sidebar('sponsored-article-sidebar-area'); 
				 }
				 ?>
			</div>
			<!-- End col-sm-3 -->

		</div>
		
		<!-- End row -->

		<?php if(isset($footer_hide_from_post_type) && is_array($footer_hide_from_post_type) &&
          isset($footer_hide_from_pages) && is_array($footer_hide_from_pages) && !in_array($current_post_type, $footer_hide_from_post_type) && !in_array(get_the_ID(), $footer_hide_from_pages)){ ?>
			<div class="row">
				<div class="col-sm-9">
					<div class='footer-bottom-ad-section'>
						<?php
								if(wp_is_mobile()){
									print do_shortcode($mobile_footer_bottom_ad); 
								}else{
									print do_shortcode($footer_bottom_ad); 
								} 
							?>
					</div>
				</div>
				<div class="col-sm-3"></div>
			</div>
		<?php } ?>


</div>

<?php get_footer(); ?>


