<?php

/**
   Event details template for the theme.
 *
 */

get_header(); ?>

<?php //while ( have_posts() ) : the_post(); // loop starts
$event_id = get_the_ID(); // get event id
$featured_img_url = get_the_post_thumbnail_url($event_id);
// $event_arr = tribe_get_event_meta($event_id);  // echo "<pre>";  print_r($event_arr); echo "</pre>"; 
$event_arr = get_post_meta($event_id);

//echo $event_arr['_EventEndDate'][0];

if (count($event_arr) > 0) {
	$_EventStartDate = explode(" ", $event_arr['_EventStartDate'][0]);
	$_EventEndDate   = explode(" ", $event_arr['_EventEndDate'][0]);
	$_EventURl   = explode(" ", $event_arr['_EventURL'][0]);
}

$event_cat = tribe_get_event_categories($event_id);  //echo "<br>"; print_r($event_cat);
$term_list = wp_get_post_terms($event_id, 'tribe_events_cat');
foreach ($term_list as $term_single) {
	$single_term_id = $single_term_link = $event_cats = "";
	$single_term_id = $term_single->term_id;
	$single_term_link = get_term_link($single_term_id, 'tribe_events_cat');
	//echo $single_term_link;
	$event_cats .= '<a href="#" class="tag">' . $term_single->name . '</a>';
}
?>
<div class="container">
	<div class="event-item--details">
		<div class="event-item__content">
			<div class="event-item__details">
				<?php if (has_post_thumbnail($event_id)) { ?>
					<div class="event-date-time col-sm-6">
						<?php if (has_post_thumbnail($event_id)) { ?>
							<div class="event-image"><img src="<?php echo $featured_img_url; ?>" alt="<?php echo $title; ?>" title="<?php echo $title; ?>" /></div>
						<?php } ?>
					</div>
					<div class="event-venue col-sm-6">
				<?php } else{?>
					<div class="event-venue col-sm-12">
				<?php } ?>
					<div class="date-time">
						<h2>Date &amp; Time</h2>

						<?php
						$EventStartDate = date("d", strtotime($_EventStartDate[0]));
						$EventEndDate = date("d", strtotime($_EventEndDate[0]));
						?>
					<?php   
						$comingSoon = get_post_meta( $event_id, 'coming_in_late_year', true );
							if( $comingSoon == "yes"){
						?>
							<span><b>Details coming soon!</b></span>

            		 <?php } else{  ?>
						<div class="location-meta tail"><b> <?php echo date("l", strtotime($_EventStartDate[0])); ?>
							<?php if (count($event_arr['_EventStartDate']) > 0) {
								echo date("M", strtotime($_EventStartDate[0]));
							}
							?> <?php
							if (count($event_arr['_EventStartDate']) > 0) {
								echo date("d", strtotime($_EventStartDate[0]));
							}
							?>, <?php
							if (count($event_arr['_EventStartDate']) > 0) {
								echo date("Y", strtotime($_EventStartDate[0]));
							}
							?>
							<?php
							if($EventStartDate != $EventEndDate){		
							?>
							- <?php echo date("l", strtotime($_EventEndDate[0])); ?>
							<?php
							if (count($event_arr['_EventEndDate']) > 0) {
								echo date("M", strtotime($_EventEndDate[0]));
							}
							?>
							<?php
							if (count($event_arr['_EventEndDate']) > 0) {
								echo date("d", strtotime($_EventEndDate[0]));
							}
							?> ,
							<?php
							if (count($event_arr['_EventEndDate']) > 0) {
								echo date("Y", strtotime($_EventEndDate[0]));
							}
							?>
							<?php
							}	
							?>
						
						</b></div>
						<?php 
						$EventStartTime =  tribe_get_start_date($event_id, false, $format = 'g:i A');
						$EventEndTime =  tribe_get_end_date($event_id, false, $format = 'g:i A');
						
						if (!tribe_event_is_all_day()) { ?>
							<?php if ($EventStartTime == $EventEndTime) { ?>
								<div class="d-none">
							<?php } else{?>
									<div class="location-meta tail event-start-date">
								   <?php } ?>
									<?php echo tribe_get_start_date($event_id, false, $format = 'g:i A') . '&nbsp;:&nbsp;' . tribe_get_end_date($event_id, false, $format = 'g:i A') . " " . Tribe__Events__Timezones::get_event_timezone_abbr($event_id); ?> 
								</div>
						<?php } ?>
					<?php } ?>
					</div>

					
					<div class="event-location">
						<?php
						$event_venue = tribe_get_venue($event_id);
						if($event_venue){
							print "<h2>Event Location</h2>";
						}
						?>						
						<div class="location-meta tail"><b><?php echo tribe_get_venue($event_id); ?></b></div>
						<?php

						$_EventCity = tribe_get_city($event_id); // get venue city

						$event_id_venue = tribe_get_venue_id($event_id);
						if (tribe_get_country($event_id_venue) == __('United States', 'tribe-events-calendar')) {
							$_EventState = tribe_get_state($event_id);
						} else {
							$_EventState = tribe_get_province($event_id);
						}

						$_EventZip = tribe_get_zip($event_id); // get venue zipcode
						$_EventCountry = tribe_get_country($event_id);

						$_Eventmaplink = esc_url(tribe_get_map_link($event_id)); // get google map link
						?>
						<div class="location-meta tail"><b>

							<?php if ($_EventCity) : ?><?php echo $_EventCity; ?><?php endif; ?><?php if ($_EventState) : ?>, <?php echo $_EventState; ?><?php endif; ?><br />
							<?php if ($_EventCountry) : ?><?php echo $_EventCountry; ?><?php endif; ?>

							</b></div>
						<?php if ($_Eventmaplink) : ?>
							<div class="location-meta tail"><a href="<?php echo $_Eventmaplink; ?>" target="_blank" title="View Map">View Map</a></div>
						<?php endif; ?>
					</div>
					<div class="event-item__cta">
						<?php $eventLearnMore = get_post_meta( $event_id, 'learn_more_url', true ); ?>
						<?php if($eventLearnMore){ ?>
							<a href="<?php print $eventLearnMore; ?>">Learn More</a>
						<?php } ?>
					</div>
				</div>
			</div> <!-- .event-item__details ends -->
			<?php
			$event_email = get_post_meta($event_id, "_Event_Email", true);
			if ($event_email) {
				echo "<p>&nbsp;</p>";
				echo '<div class="event-item__details"><div>';
				echo '<div class="event-label">Additional Information</div>';
				echo '<div class="location-meta tail">Email: <a href="mailto:' . $event_email . '">' . $event_email . '</a></div>';
				echo '</div></div>';
			} ?>
		</div>
	</div> <!-- .event-item--details ends -->
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
</div> <!-- .container ends -->