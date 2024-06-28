<?php

/**
 * Template part for displaying page content in single-post.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package PETAGE
 */
get_header();

$ids = [];
$id = $post->ID;
$ids[] = $id;

$date = $post->post_date;
$date = date('F j, Y', strtotime($date));
$author = $post->post_author;
$display_name = get_the_author_meta('display_name', $author);
$author_link = get_author_posts_url($author);
$cmpnyId = get_post_meta($id, 'associated_sponsor_id', true);
$show_featured_image = get_post_meta( $id, 'show_featured_image', true );
$is_it_power_list = get_post_meta( $id, 'is_it_power_list', true );
$cmpny_link = get_permalink( $cmpnyId);
$cmpny_linkArray = explode('/',$cmpny_link);
$company_logo= get_the_post_thumbnail($cmpnyId);
$subtitle = get_post_meta( get_the_ID(), 'subhead', true );
$post_type = $post->post_type;
$title = get_the_title(); 

// data is coming from plugin
$footer_bottom_ad = get_option('footer_bottom_ad');
$mobile_footer_bottom_ad = get_option('mobile_footer_bottom_ad');
$footer_hide_from_pages = [];
$footer_hide_from_pages[] = get_option('footer_hide_from_pages');
$footer_hide_from_post_type = [];
$footer_hide_from_post_type[] = get_option('footer_hide_from_post_type');
global $post;
$current_post_type = get_post_type($post);
?>
<?php //if ( is_mobile()){ ?>	
<div class="mobile-specific-data">
	<div class="mobile-news">
		<h2 class="news-page-header"><?php print $title; ?> </h2>
		<div class="news-publish-info">
			<p><a href="<?php print $author_link;?>"><b><?php print $display_name; ?></b></a><span>//</span><?php print $date; ?></p>			
		</div>
	</div>
	<?php if (has_post_thumbnail($post->ID)) : ?>
		<div class="mobile-news-image">
			<?php if($show_featured_image != "no"){ 
					echo get_the_post_thumbnail( $post->ID, 'article-detail-thumb' ); 
					if($image_caption){ ?>
						<p class="image-caption"><?php print $image_caption; ?></p>
					<?php } 
				}
			?>
		</div>
	<?php endif; ?>

</div>

<?php //} ?>

<div class="article-detail-page container">
	<?php if($is_it_power_list == "Yes"){ ?>


	<!-- Start row -->
	<div class="row">
		<!-- Start col-sm-9 -->
		<div class="col-sm-12">

			<div class="newsdetail-wrapper">
				<div class="breadcrumb"><?php asentech_custom_breadcrumb(); ?></div>

				<?php if (has_post_thumbnail($post->ID)) : ?>
					<div class="news-image">
						<?php if($show_featured_image != "no"){ 
								echo get_the_post_thumbnail( $post->ID, 'article-detail-thumb' ); 
								if($image_caption){ ?>
									<p class="image-caption"><?php print $image_caption; ?></p>
								<?php } 
							}
						?>
					</div>
				<?php endif; ?>

				<h1 class="news-page-header"><?php print $title; ?> </h1>
				<p class="subtitle-text" style="font-size: 18px; line-height: 26px;"><?php print $subtitle; ?> </p>
				<div class="row">
					<div class="col-sm-9 news-publish-info">
						<p><a href="<?php print $author_link;?>"><b><?php print $display_name; ?></b></a><span>//</span><?php print $date; ?></p>
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
						echo '<li><a class="twitter" href="'. $twitterURL .'" target="_blank"><img src="/wp-content/themes/PETAGE/images/twitter.svg" alt="twitter"></a></li>';
						echo '<li><a class="facebook" href="'.$facebookURL.'" target="_blank"><img src="/wp-content/themes/PETAGE/images/facebook.svg" alt="facebook"></a></li>';
						echo '<li><a class="linkedin" href="'.$linkedInURL.'" target="_blank"><img src="/wp-content/themes/PETAGE/images/linkedin.svg" alt="linkedin"></a></li>';
						echo '<li><a class="crunchify-link crunchify-pinterest" href="'.$pinterestURL.'" data-pin-custom="true" target="_blank"><img src="/wp-content/themes/PETAGE/images/pinterest.svg" alt="pinterest"></a></li>';
						echo '<li><a class="email" href="mailto:?subject='.get_the_title($post_id).'&amp;body='.esc_html($eventContent).'" title="Share by Email"><img src="/wp-content/themes/PETAGE/images/email.svg" alt="email"></a></li>';
						echo '</ul>';
						?>
					</div>
				</div>
				<div class="news-detail"><?php the_content(); ?></div>


				<div class="mobile-specific-data">
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

						?>
						<p class="share">Share this!</p>
						<?php
						echo '<ul class="social-share-links">';
						echo '<li><a class="twitter" href="'. $twitterURL .'" target="_blank"><img src="/wp-content/themes/PETAGE/images/twitter.svg" alt="twitter"></a></li>';
						echo '<li><a class="facebook" href="'.$facebookURL.'" target="_blank"><img src="/wp-content/themes/PETAGE/images/facebook.svg" alt="facebook"></a></li>';
						echo '<li><a class="linkedin" href="'.$linkedInURL.'" target="_blank"><img src="/wp-content/themes/PETAGE/images/linkedin.svg" alt="linkedin"></a></li>';
						echo '<li><a class="crunchify-link crunchify-pinterest" href="'.$pinterestURL.'" data-pin-custom="true" target="_blank"><img src="/wp-content/themes/PETAGE/images/pinterest.svg" alt="pinterest"></a></li>';
						echo '<li><a class="email" href="mailto:?subject='.get_the_title($post_id).'&amp;body='.esc_html($eventContent).'" title="Share by Email"><img src="/wp-content/themes/PETAGE/images/email.svg" alt="email"></a></li>';
						echo '</ul>';
						?>
					</div>
					<div class="right-sidebar-mobile"><?php dynamic_sidebar('article-right-sidebar-mobile'); ?></div>
				</div>
			    
			</div>

			<div class="related-content-section"><?php asentech_related_contents(); ?></div>				

			</div>
			<!-- End col-sm-9 -->
		</div>
		
		<!-- End row -->




	<?php  }else{ ?>

	<!-- Start row -->
	<div class="row">
		<!-- Start col-sm-9 -->
		<div class="col-sm-9">

			<div class="newsdetail-wrapper">
				<div class="breadcrumb"><?php asentech_custom_breadcrumb(); ?></div>

				<?php if (has_post_thumbnail($post->ID)) : ?>
					<div class="news-image">
					<?php
						if($show_featured_image != "No"){
							//echo get_the_post_thumbnail( $post->ID, 'article-detail-thumb' );
						}
					?>
					</div>
				<?php endif; ?>

				<h1 class="news-page-header"><?php print $title; ?> </h1>
				<p class="subtitle-text" style="font-size: 18px; line-height: 26px;"><?php print $subtitle; ?> </p>
				<div class="row">
					<div class="col-sm-9 news-publish-info">
						<p><a href="<?php print $author_link;?>"><b><?php print $display_name; ?></b></a><span>//</span><?php print $date; ?></p>
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
						echo '<li><a class="twitter" href="'. $twitterURL .'" target="_blank"><img src="/wp-content/themes/PETAGE/images/twitter.svg" alt="twitter"></a></li>';
						echo '<li><a class="facebook" href="'.$facebookURL.'" target="_blank"><img src="/wp-content/themes/PETAGE/images/facebook.svg" alt="facebook"></a></li>';
						echo '<li><a class="linkedin" href="'.$linkedInURL.'" target="_blank"><img src="/wp-content/themes/PETAGE/images/linkedin.svg" alt="linkedin"></a></li>';
						echo '<li><a class="crunchify-link crunchify-pinterest" href="'.$pinterestURL.'" data-pin-custom="true" target="_blank"><img src="/wp-content/themes/PETAGE/images/pinterest.svg" alt="pinterest"></a></li>';
						echo '<li><a class="email" href="mailto:?subject='.get_the_title($post_id).'&amp;body='.esc_html($eventContent).'" title="Share by Email"><img src="/wp-content/themes/PETAGE/images/email.svg" alt="email"></a></li>';
						echo '</ul>';
						?>
					</div>
				</div>
				<div class="news-detail"><?php the_content(); ?></div>


				<div class="mobile-specific-data">
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

						?>
						<p class="share">Share this!</p>
						<?php
						echo '<ul class="social-share-links">';
						echo '<li><a class="twitter" href="'. $twitterURL .'" target="_blank"><img src="/wp-content/themes/PETAGE/images/twitter.svg" alt="twitter"></a></li>';
						echo '<li><a class="facebook" href="'.$facebookURL.'" target="_blank"><img src="/wp-content/themes/PETAGE/images/facebook.svg" alt="facebook"></a></li>';
						echo '<li><a class="linkedin" href="'.$linkedInURL.'" target="_blank"><img src="/wp-content/themes/PETAGE/images/linkedin.svg" alt="linkedin"></a></li>';
						echo '<li><a class="crunchify-link crunchify-pinterest" href="'.$pinterestURL.'" data-pin-custom="true" target="_blank"><img src="/wp-content/themes/PETAGE/images/pinterest.svg" alt="pinterest"></a></li>';
						echo '<li><a class="email" href="mailto:?subject='.get_the_title($post_id).'&amp;body='.esc_html($eventContent).'" title="Share by Email"><img src="/wp-content/themes/PETAGE/images/email.svg" alt="email"></a></li>';
						echo '</ul>';
						?>
					</div>
					<div class="right-sidebar-mobile"><?php dynamic_sidebar('article-right-sidebar-mobile'); ?></div>
				</div>
			    
			</div>

			<div class="related-content-section"><?php asentech_related_contents(); ?></div>				

			</div>
			<!-- End col-sm-9 -->
			<!-- Start col-sm-3 -->
			<div class="col-sm-3 desktop-show">
				<?php dynamic_sidebar('right-sidebar-area'); ?>
			</div>
			<!-- End col-sm-3 -->

		</div>
		
		<!-- End row -->

		<?php  } ?>

		<?php if(!in_array($current_post_type, $footer_hide_from_post_type) && !in_array(get_the_ID(), $footer_hide_from_pages)){ ?>
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
</div>

<?php get_footer();