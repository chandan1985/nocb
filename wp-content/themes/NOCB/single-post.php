<?php

/**
 * Template part for displaying page content in single-post.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package nocb
 */
get_header();
if(have_rows('ss_slideshow_images')){
	wp_enqueue_style('ad-gallery-style', get_stylesheet_directory_uri() . '/css/slideshow/jquery.ad-gallery.css');
}

$ids = [];
$id = $post->ID;
$ids[] = $id;

$date = $post->post_date;
$date = date('F j, Y', strtotime($date));
$author = $post->post_author;
$display_name = get_the_author_meta('display_name', $author);
$author_link = get_author_posts_url($author);
$byline_author = get_post_meta( $id, 'author_byline', true );
if (is_array($byline_author)){
	$byline_author_name = $byline_author['name'][0];
}

$subtitle = get_post_meta( get_the_ID(), 'subhead', true );
$show_featured_image = get_post_meta( $id, 'show_featured_image', true );
$post_type = $post->post_type;
$title = get_the_title(); 
$image = get_post_thumbnail_id($id);
$image_caption = wp_get_attachment_caption( $image );

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
		<p class="subtitle-text mobile-show" style="font-size: 1.125rem; line-height: 1.625rem;"><?php print $subtitle; ?> </p>
		<div class="news-publish-info">
            <?php
				if($byline_author_name){
					?>
            <p><b><?php print $byline_author_name; ?></b><span>//</span><?php print $date; ?></p>
            <?php
				}else{
					?>
            <p><a
                    href="<?php print $author_link;?>"><b><?php print $display_name; ?></b></a><span>//</span><?php print $date; ?>
            </p>
            <?php
				}
			?>
        </div>
	</div>
	<?php
        if ( wp_is_mobile()){
            if (has_post_thumbnail($post->ID)) : ?>
                <div class="news-image">
                    <?php if($show_featured_image == "yes"){
                            echo get_the_post_thumbnail( $post->ID, 'article-detail-thumb' ); 
                            if($image_caption){ ?>
                                <p class="image-caption"><?php print $image_caption; ?></p>
                            <?php } 
                        }
                    ?>
                </div>
            <?php endif;
        }
	?>
	
</div>

<?php //} ?>

<div class="article-detail-page container">

	<!-- Start row -->
	<div class="row">
		<!-- Start col-sm-9 -->
		<div class="col-sm-9">

			<div class="newsdetail-wrapper">
				<div class="breadcrumb"><?php asentech_custom_breadcrumb(); ?></div>
				<div class="slideshow desktop-show">
				    <?php
                        if (has_post_thumbnail($post->ID)) : ?>
                            <div class="news-image">
                                <?php if($show_featured_image == "yes"){
                                        echo get_the_post_thumbnail( $post->ID, 'article-detail-thumb' ); 
                                        if($image_caption){ ?>
                                            <p class="image-caption"><?php print $image_caption; ?></p>
                                        <?php } 
                                    }
                                ?>
                            </div>
                        <?php endif;
					?>
                </div>

					
				<h1 class="news-page-header"><?php print $title; ?> </h1>
				<p class="subtitle-text desktop-show" style="font-size: 1.125rem; line-height: 1.625rem;"><?php print $subtitle; ?> </p>
				<div class="row">
				<div class="col-sm-9 news-publish-info">
                        <?php
						if($byline_author_name){
							?>
                        <p><b><?php print $byline_author_name; ?></b><span>//</span><?php print $date; ?></p>
                        <?php
						}else{
							?>
                        <p><a
                                href="<?php print $author_link;?>"><b><?php print $display_name; ?></b></a><span>//</span><?php print $date; ?>
                        </p>
                        <?php
						}
					?>
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
						echo '<li><a class="twitter" href="'. $twitterURL .'" target="_blank"><img src="'.get_stylesheet_directory_uri().'/images/twitter.svg" alt="twitter"></a></li>';
						echo '<li><a class="facebook" href="'.$facebookURL.'" target="_blank"><img src="'.get_template_directory_uri().'/images/facebook.svg" alt="facebook"></a></li>';
						echo '<li><a class="linkedin" href="'.$linkedInURL.'" target="_blank"><img src="'.get_template_directory_uri().'/images/linkedin.svg" alt="linkedin"></a></li>';
						echo '<li><a class="crunchify-link crunchify-pinterest" href="'.$pinterestURL.'" data-pin-custom="true" target="_blank"><img src="'.get_template_directory_uri().'/images/pinterest.svg" alt="pinterest"></a></li>';
						echo '<li><a class="email" href="mailto:?subject='.get_the_title($post_id).'&amp;body='.esc_html($eventContent).'" title="Share by Email"><img src="'.get_template_directory_uri().'/images/email.svg" alt="email"></a></li>';
						echo '</ul>';
						?>
					</div>
				</div>
				<div class="news-detail"><?php the_content(); ?></div>
				<?php wp_reset_postdata(); ?>


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
						echo '<li><a class="twitter" href="'. $twitterURL .'" target="_blank"><img src="'.get_stylesheet_directory_uri().'/images/twitter.svg" alt="twitter"></a></li>';
						echo '<li><a class="facebook" href="'.$facebookURL.'" target="_blank"><img src="'.get_template_directory_uri().'/images/facebook.svg" alt="facebook"></a></li>';
						echo '<li><a class="linkedin" href="'.$linkedInURL.'" target="_blank"><img src="'.get_template_directory_uri().'/images/linkedin.svg" alt="linkedin"></a></li>';
						echo '<li><a class="crunchify-link crunchify-pinterest" href="'.$pinterestURL.'" data-pin-custom="true" target="_blank"><img src="'.get_template_directory_uri().'/images/pinterest.svg" alt="pinterest"></a></li>';
						echo '<li><a class="email" href="mailto:?subject='.get_the_title($post_id).'&amp;body='.esc_html($eventContent).'" title="Share by Email"><img src="'.get_template_directory_uri().'/images/email.svg" alt="email"></a></li>';
						echo '</ul>';
						?>
					</div>
					<div class="right-sidebar-mobile">
						<?php
							if(wp_is_mobile()){
								dynamic_sidebar('article-right-sidebar-mobile'); 
							}
						?>
					</div>
				</div>
			    
			</div>			
				<div class="related-content-section"><?php  asentech_related_contents(); ?></div>				

        </div>
			<!-- End col-sm-9 -->
			<!-- Start col-sm-3 -->
			<div class="col-sm-3 desktop-show">
				<?php dynamic_sidebar('right-sidebar-area'); ?>
			</div>
			<!-- End col-sm-3 -->

    </div>
		
		<!-- End row -->


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


<?php get_footer(); ?>