<div class="event-content">
		<div class="event-desc mt-3" style="font-size:16px; line-height:22px;"><?php the_content(); ?></div>
		<div class="event-content__sidebar">
			<h2 class="mt-4">Share this event</h2>
			<?php
				if ($permalink == "") {
					// Get current page URL 
					$eventURL = urlencode(get_the_permalink($event_id));
				} else {
					$eventURL = urlencode($permalink);
				}
				$eventTitle = htmlspecialchars(urlencode(html_entity_decode(get_the_title($event_id), ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8');

				// Construct sharing URL without using any script
				$twitterURL = 'https://twitter.com/intent/tweet?text=' . $eventTitle . '&amp;url=' . $eventURL;
				$facebookURL = 'https://www.facebook.com/sharer/sharer.php?u=' . $eventURL;
				$linkedInURL = 'https://www.linkedin.com/shareArticle?mini=true&url=' . $eventURL . '&amp;title=' . $eventURL;

				// Based on popular demand added Pinterest too
				$pinterestURL = 'https://pinterest.com/pin/create/button/?url=' . $eventURL . '&amp;media=' . $Thumbnail[0] . '&amp;description=' . $crunchifyTitle;

				echo '<ul class="social-share-links">';
				echo '<li><a class="twitter" href="' . $twitterURL . '" target="_blank"><img src="'.get_template_directory_uri().'/images/twitter.svg" alt="twitter"></a></li>';
				echo '<li><a class="facebook" href="' . $facebookURL . '" target="_blank"><img src="'.get_template_directory_uri().'/images/facebook.svg" alt="facebook"></a></li>';
				echo '<li><a class="linkedin" href="' . $linkedInURL . '" target="_blank"><img src="'.get_template_directory_uri().'/images/linkedin.svg" alt="linkedin"></a></li>';
				echo '<li><a class="crunchify-link crunchify-pinterest" href="' . $pinterestURL . '" data-pin-custom="true" target="_blank"><img src="'.get_template_directory_uri().'/images/pinterest.svg" alt="pinterest"></a></li>';
				echo '<li><a class="email" href="mailto:?subject=' . get_the_title($post_id) . '&amp;body=' . esc_html($eventContent) . '" title="Share by Email"><img src="'.get_template_directory_uri().'/images/email.svg" alt="email"></a></li>';
				echo '</ul>';
			?>
		</div> <!-- .event-content__sidebar ends -->
		<?php if(wp_is_mobile()){ ?>
			<div class="event-see-all-events"><a href="/events/">See all events</a></div>
		<?php } ?>
	</div> <!-- .event-content ends -->