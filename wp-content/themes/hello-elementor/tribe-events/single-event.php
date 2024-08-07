<?php

/**
   Event details template for the theme.
 *
 */

get_header(); ?>

<?php //while ( have_posts() ) : the_post(); // loop starts
$event_id = get_the_ID(); // get event id
$featured_img_url = get_the_post_thumbnail_url($event_id);
$event_arr = tribe_get_event_meta($event_id);  //echo "<pre>";  echo "<pre>"; print_r($event_arr);

//echo $event_arr['_EventEndDate'][0];

if (count($event_arr) > 0) {
	$_EventStartDate = explode(" ", $event_arr['_EventStartDate'][0]);
	$_EventEndDate   = explode(" ", $event_arr['_EventEndDate'][0]);
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
					<div class="event-date-time col-sm-4">
						<?php if (has_post_thumbnail($event_id)) { ?>
							<div class="event-image"><img src="<?php echo $featured_img_url; ?>" alt="<?php echo $title; ?>" title="<?php echo $title; ?>" /></div>
						<?php } ?>
					</div>
					<div class="event-venue col-sm-8">
				<?php } else{?>
					<div class="event-venue col-sm-12">
				<?php } ?>
					<div class="date-time">
						<h2>Date &amp; Time</h2>
						<?php
							$EventStartDate = date("d", strtotime($_EventStartDate[0]));
							$EventEndDate = date("d", strtotime($_EventEndDate[0]));
						?>
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
							<?php echo date("l", strtotime($_EventEndDate[0])); ?>
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
							<?php } ?>
						
						</b></div>
						<?php if (!tribe_event_is_all_day()) { ?>
							<div class="location-meta tail event-start-date"><?php echo tribe_get_start_date($event_id, false, $format = 'g:i A') . '&nbsp;:&nbsp;' . tribe_get_end_date($event_id, false, $format = 'g:i A') . " " . Tribe__Events__Timezones::get_event_timezone_abbr($event_id); ?> </div>
						<?php } ?>
					</div>
					<div class="event-location">
						<h2>Event Location</h2>
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

					<?php
					$website = tribe_get_event_meta( get_the_ID(), '_EventURL', true );
					// Event Website
					if ( ! empty( $website ) ) :  
					?>
					<div class="register-now-button"><a href="<?php echo $website; ?>" class="btn-custom" title="Register Now">Register Now</a></div>
					<?php endif; ?>
					
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

		<?php
		$sponsorship_opportunities_available = get_field('sponsorship_opportunities_available'); //get_post_meta( $event_id, 'sponsorship_opportunities_available', true );
		if($sponsorship_opportunities_available){ ?>
		
		<div class="row">
          <div class="col-lg-12 tribe-events-single-sponsorship-title sopnser-cont-text">
            <h3 class="sponsor-header">Sponsorship Opportunities Available</h3>
			<div class="sponsor-describe"><?php print $sponsorship_opportunities_available ;?></div>
          </div>
        </div>
		<hr>
		<?php } ?>

		

		<?php
		$event_registration_form_code = get_field('event_registration_form_code'); //get_post_meta( $event_id, 'event_registration_form_code', true );
		if($event_registration_form_code){ ?>
		
		<div class="row">
          <div class="col-lg-12">
			<div class="event_registration_form_code_box"><?php print $event_registration_form_code ;?></div>
          </div>
        </div>
		<hr>	
		<?php } ?>

		<?php
		$nomination_deadline = get_field('nomination_deadline'); //get_post_meta( $event_id, 'nomination_deadline', true );
		if($nomination_deadline){ ?>
		
		<div class="row">
          <div class="col-lg-12">
			<h3 style="text-align: center;"><span style="color: #ff0000;">NOMINATION DEADLINE: <?php print date('m/d/Y', strtotime($nomination_deadline)) ;?></span></h3>
          </div>
        </div>
		<hr>	
		<?php } ?>

		<?php
		$event_nomination_form_code =  get_field('event_nomination_form_code'); //get_post_meta( $event_id, 'event_nomination_form_code', true );
		if($event_nomination_form_code){ ?>		
		<div class="row">
          <div class="col-lg-12">
			<div class="event_nomination_form_code_box"><?php print $event_nomination_form_code ;?></div>
          </div>
        </div>
		<hr>	
		<?php } ?>
		

		


        <div class="row event-sponser">
          <?php 
          $contact_name = get_field('contact_name');
          $contact_email = get_field('contact_email');
          $contact_company = get_field('contact_company');
          $contact_phone = get_field('contact_phone');

          //print_r($sponsor_title);
          $twitter_name = get_field('twitter_name');
          $twitter_user_name = get_field('twitter_user_name');
          ?>    
              <div class="col-lg-6">
              
              <?php if($contact_name!=""){?>
                <h5 class="tribe-events-single-contact-title"><?php echo 'Contact Information'; ?></h5>
                <h5 class="contact-info"><?php echo $contact_name;?></h5>
              <?php } ?>
              <?php if($contact_email!=""){?>
                <a class="mail-to" href="mailto:<?php echo $contact_email;?>" target="_blank"><?php echo $contact_email;?> </a>
              <?php } ?>
              <?php if($contact_company!=""){?>
                <p class="comp-name"><?php echo $contact_company;?></p>
              <?php } ?>
              <?php if($contact_phone!=""){?>
                <p class="comp-name"><?php echo $contact_phone;?></p>
              <?php } ?>
              </div>
              <div class="col-lg-6">
              <?php if( $twitter_name !=""){?>
                <h5 class="tribe-events-single-twitter-title twitter-title"><?php echo 'Follow Us On Twitter'; ?></h5>
                <a class="twitter-btn" href="http://twitter.com/<?php echo $twitter_name;?>" target="_blank">follow<?php echo $twitter_name;?> </a>
              <?php } ?>
              <?php if($twitter_user_name!=""){?>
                <p class="user-name"><?php echo $twitter_user_name;?></p>
              <?php } ?>
              </div>
        </div>
        <hr>


		<?php
			if ( have_rows('presenting_sponsor')  ){ ?>
				<div class="sponsore-box presenting-sponsor">
				<h5 class="tribe-events-single-presenting-sponsor-title"><?php echo 'Presenting Sponsor'; ?></h5>
				<div class="row sponser-img-content">
				  <?php 
					  $i=0;
					  while( have_rows('presenting_sponsor') ): the_row(); 
					  //$slideshow_ads_code = get_sub_field('ss_ads_code');
					  // vars
					  $sponsor_title = get_sub_field('pst_sponsor_title');
					  $sponsor_content = get_sub_field('pst_sponsor_url');										
					  $sponsor_image = get_sub_field('pst_sponsor_image');
					  
					  // vars
					  $img_full_url = wp_get_attachment_image_src( $sponsor_image, 'full' );
					  // thumbnail
					  $img_thumb_url = wp_get_attachment_image_src( $sponsor_image, true);
					  
					  if($sponsor_image!="")
					  { 
					  if (!preg_match("@^https?://@i", $sponsor_content) && !preg_match("@^ftps?://@i", $sponsor_content)) {
						$url = $sponsor_content; ?>       
						  <div class="col-sm-4">
							<a href="<?php echo $url;?>" target="_blank">
							  <img src="<?php echo $img_thumb_url[0];?>" title="<?php echo $sponsor_title;?>" alt="<?php echo $sponsor_content;?>">
							</a>
						  </div>
					  <?php } 
						else { $url = $sponsor_content; ?>
						  <div class="col-sm-4">
							<a href="<?php echo $url;?>" target="_blank">
							  <img src="<?php echo $img_thumb_url[0];?>" title="<?php echo $sponsor_title;?>" alt="<?php echo $sponsor_content;?>">
							</a>
						  </div>
					 <?php } } ?>
					 <?php $i++;   endwhile; ?>
				</div>
				</div>

				<?php } if ( have_rows('major_sponsor')  ){ ?>
				<div class="sponsore-box major-sponsor">
				<h5 class="tribe-events-single-sponsor-title major-spons"><?php echo 'Major Sponsor'; ?></h5>
				<div class="row sponser-img-content">
				<?php 
					$i=0;
					while( have_rows('major_sponsor') ): the_row(); 
					// vars
					$sponsor_title = get_sub_field('sponsor_title');
					$sponsor_content = get_sub_field('sponsor_url');	
					$sponsor_image = get_sub_field('sponsor_image');
					
					// vars
					$img_full_url = wp_get_attachment_image_src( $sponsor_image, 'full' );
					// thumbnail
					$img_thumb_url = wp_get_attachment_image_src( $sponsor_image, true);
					
					if($sponsor_image!="")
					{ 
					if (!preg_match("@^https?://@i", $sponsor_content) && !preg_match("@^ftps?://@i", $sponsor_content)) {
						$url = "http://" . $sponsor_content; ?>       
						<div class="col-sm-4">
							<a href="<?php echo $url;?>" target="_blank">
							<img src="<?php echo $img_thumb_url[0];?>" title="<?php echo $sponsor_title;?>" alt="<?php echo $sponsor_content;?>">
							</a>
						</div>
					<?php }
						else { $url = $sponsor_content; ?>
						<div class="col-sm-4">
							<a href="<?php echo $url;?>" target="_blank">
							<img src="<?php echo $img_thumb_url[0];?>" title="<?php echo $sponsor_title;?>" alt="<?php echo $sponsor_content;?>">
							</a>
						</div>
					<?php } } ?>
					<?php $i++; endwhile; ?>
				</div>
				</div>
				
				<?php } if ( have_rows('supporting_sponsors')  ){ ?>
				<div class="sponsore-box supporting-sponsor">
				<h5 class="tribe-events-single-sponsor-title supporting-sponsors"><?php echo 'Supporting Sponsor'; ?></h5>
				<div class="row sponser-img-content">
				<?php 
					$i=0;
					while( have_rows('supporting_sponsors') ): the_row(); 
					// vars
					$sponsor_title = get_sub_field('sponsor_title');
					$sponsor_content = get_sub_field('sponsor_url');	
					$sponsor_image = get_sub_field('sponsor_image');
					
					// vars
					$img_full_url = wp_get_attachment_image_src( $sponsor_image, 'full' );
					// thumbnail
					$img_thumb_url = wp_get_attachment_image_src( $sponsor_image, true);
						
					if($sponsor_image!="")
					{ 
					if (!preg_match("@^https?://@i", $sponsor_content) && !preg_match("@^ftps?://@i", $sponsor_content)) {
						$url = "http://" . $sponsor_content;  ?>       
						<div class="col-sm-4">
							<a href="<?php echo $url;?>" target="_blank">
							<img src="<?php echo $img_thumb_url[0];?>" title="<?php echo $sponsor_title;?>" alt="<?php echo $sponsor_content;?>">
							</a>
						</div>
					<?php }
						else { $url = $sponsor_content; ?>
						<div class="col-sm-4">
							<a href="<?php echo $url;?>" target="_blank">
							<img src="<?php echo $img_thumb_url[0];?>" title="<?php echo $sponsor_title;?>" alt="<?php echo $sponsor_content;?>">
							</a>
						</div>
					<?php } } ?>
					<?php $i++; endwhile; ?>
				</div>
				</div>
				
				<?php } if ( have_rows('social_media_sponsor')  ){ ?>
				<div class="sponsore-box social_media-sponsor">
				<h5 class="tribe-events-single-sponsor-title supporting-sponsors"><?php echo 'Social Media Sponsor'; ?></h5>
				<div class="row sponser-img-content">
				<?php 
					$i=0;
					while( have_rows('social_media_sponsor') ): the_row(); 
					// vars
					$sponsor_title = get_sub_field('sponsor_title');
					$sponsor_content = get_sub_field('sponsor_url');	
					$sponsor_image = get_sub_field('sponsor_image');
					
					// vars
					$img_full_url = wp_get_attachment_image_src( $sponsor_image, 'full' );
					// thumbnail
					$img_thumb_url = wp_get_attachment_image_src( $sponsor_image, true);
						
					if($sponsor_image!="")
					{ 
					if (!preg_match("@^https?://@i", $sponsor_content) && !preg_match("@^ftps?://@i", $sponsor_content)) {
						$url = "http://" . $sponsor_content;  ?>       
						<div class="col-sm-4">
							<a href="<?php echo $url;?>" target="_blank">
							<img src="<?php echo $img_thumb_url[0];?>" title="<?php echo $sponsor_title;?>" alt="<?php echo $sponsor_content;?>">
							</a>
						</div>
					<?php }
						else { $url = $sponsor_content; ?>
						<div class="col-sm-4">
							<a href="<?php echo $url;?>" target="_blank">
							<img src="<?php echo $img_thumb_url[0];?>" title="<?php echo $sponsor_title;?>" alt="<?php echo $sponsor_content;?>">
							</a>
						</div>
					<?php } } ?>
					<?php $i++; endwhile; ?>
				</div>
				</div>
				
				<?php
				} if ( have_rows('supporters')  ){ ?>
				<div class="sponsore-box supporters">
				<h5 class="tribe-events-single-sponsor-title supporter"><?php echo 'Supporters'; ?></h5>
				<div class="row sponser-img-content">
				<?php 
					$i=0;
					while( have_rows('supporters') ): the_row(); 
					// vars
					$sponsor_title = get_sub_field('sponsor_title');
					$sponsor_content = get_sub_field('sponsor_url');	
					$sponsor_image = get_sub_field('sponsor_image');
					
					// vars
					$img_full_url = wp_get_attachment_image_src( $sponsor_image, 'full' );
					// thumbnail
					$img_thumb_url = wp_get_attachment_image_src( $sponsor_image, true);
					
					if($sponsor_image!="")
					{ 
					if (!preg_match("@^https?://@i", $sponsor_content) && !preg_match("@^ftps?://@i", $sponsor_content)) {
						$url = "http://" . $sponsor_content; ?>       
						<div class="col-sm-4">
							<a href="<?php echo $url;?>" target="_blank">
							<img src="<?php echo $img_thumb_url[0];?>" title="<?php echo $sponsor_title;?>" alt="<?php echo $sponsor_content;?>">
							</a>
						</div>
					<?php }
						else { $url = $sponsor_content; ?>
						<div class="col-sm-4">
							<a href="<?php echo $url;?>" target="_blank">
							<img src="<?php echo $img_thumb_url[0];?>" title="<?php echo $sponsor_title;?>" alt="<?php echo $sponsor_content;?>">
							</a>
						</div>
					<?php } } ?>
					<?php $i++; endwhile; ?>
				</div>
				</div>
				
				<?php } if ( have_rows('celebration_sponsor')  ){ ?>
				<div class="sponsore-box celebrating-sponsor">
				<h5 class="tribe-events-single-celebration-sponsor-title"><?php echo 'Celebration Sponsor'; ?></h5>
				<div class="row sponser-img-content">
				<?php 
					$i=0;
					while( have_rows('celebration_sponsor') ): the_row(); 
					//$slideshow_ads_code = get_sub_field('ss_ads_code');
					// vars
					$sponsor_title = get_sub_field('sponsor_title');
					$sponsor_content = get_sub_field('sponsor_url');										
					$sponsor_image = get_sub_field('sponsor_image');
					
					// vars
					$img_full_url = wp_get_attachment_image_src( $sponsor_image, 'full' );
					// thumbnail
					$img_thumb_url = wp_get_attachment_image_src( $sponsor_image, true);
					
					if($sponsor_image!="")
					{ 
					if (!preg_match("@^https?://@i", $sponsor_content) && !preg_match("@^ftps?://@i", $sponsor_content)) {
						$url = $sponsor_content; ?>       
						<div class="col-sm-4">
							<a href="<?php echo $url;?>" target="_blank">
							<img src="<?php echo $img_thumb_url[0];?>" title="<?php echo $sponsor_title;?>" alt="<?php echo $sponsor_content;?>">
							</a>
						</div>
					<?php } 
						else { $url = $sponsor_content; ?>
						<div class="col-sm-4">
							<a href="<?php echo $url;?>" target="_blank">
							<img src="<?php echo $img_thumb_url[0];?>" title="<?php echo $sponsor_title;?>" alt="<?php echo $sponsor_content;?>">
							</a>
						</div>
					<?php } } ?>
					<?php $i++;   endwhile; ?>
				</div>	
				</div>	
				
				
				<?php } if ( have_rows('sponsor_generic_for_panels')  ){ ?>
				<div class="sponsore-box sponsor-cmpny">
				<h5 class="tribe-events-single-sponsor-title sponsor"><?php echo 'Sponsor'; ?></h5>
				<div class="row sponser-img-content">
				<?php 
					$i=0;
					while( have_rows('sponsor_generic_for_panels') ): the_row(); 
					// vars
					$sponsor_title = get_sub_field('sponsor_title');
					$sponsor_content = get_sub_field('sponsor_url');	
					$sponsor_image = get_sub_field('sponsor_image');
					
					// vars
					$img_full_url = wp_get_attachment_image_src( $sponsor_image, 'full' );
					// thumbnail
					$img_thumb_url = wp_get_attachment_image_src( $sponsor_image, true);
					
					if($sponsor_image!="")
					{ 
					if (!preg_match("@^https?://@i", $sponsor_content) && !preg_match("@^ftps?://@i", $sponsor_content)) {
						$url = "http://" . $sponsor_content; ?>       
						<div class="col-sm-4">
							<a href="<?php echo $url;?>" target="_blank">
							<img src="<?php echo $img_thumb_url[0];?>" title="<?php echo $sponsor_title;?>" alt="<?php echo $sponsor_content;?>">
							</a>
						</div>
					<?php }
						else { $url = $sponsor_content; ?>
						<div class="col-sm-4">
							<a href="<?php echo $url;?>" target="_blank">
							<img src="<?php echo $img_thumb_url[0];?>" title="<?php echo $sponsor_title;?>" alt="<?php echo $sponsor_content;?>">
							</a>
						</div>
					<?php } } ?>
					<?php  $i++;  endwhile; ?>
				</div>
				</div>
				
				<?php } if ( have_rows('venue_sponsor')  ){ ?>
				<div class="sponsore-box venue-sponsor">
				<h5 class="tribe-events-single-sponsor-title venue-sponsor"><?php echo 'Venue Sponsor'; ?></h5>
				<div class="row sponser-img-content">
				<?php 
					$i=0;
					while( have_rows('venue_sponsor') ): the_row(); 
					// vars
					$sponsor_title = get_sub_field('sponsor_title');
					$sponsor_content = get_sub_field('sponsor_url');	
					$sponsor_image = get_sub_field('sponsor_image');
					
					// vars
					$img_full_url = wp_get_attachment_image_src( $sponsor_image, 'full' );
					// thumbnail
					$img_thumb_url = wp_get_attachment_image_src( $sponsor_image, true);
					
					if($sponsor_image!="")
					{ 
					if (!preg_match("@^https?://@i", $sponsor_content) && !preg_match("@^ftps?://@i", $sponsor_content)) {
						$url = "http://" . $sponsor_content; ?>       
						<div class="col-sm-4">
							<a href="<?php echo $url;?>" target="_blank">
							<img src="<?php echo $img_thumb_url[0];?>" title="<?php echo $sponsor_title;?>" alt="<?php echo $sponsor_content;?>" width="50%">
							</a>
						</div>
					<?php }
						else { $url = $sponsor_content; ?>
						<div class="col-sm-4">
							<a href="<?php echo $url;?>" target="_blank">
							<img src="<?php echo $img_thumb_url[0];?>" title="<?php echo $sponsor_title;?>" alt="<?php echo $sponsor_content;?>" width="50%">
							</a>
						</div>
					<?php } } ?>
					<?php $i++; endwhile; ?>
				</div>
				</div>
				
				<?php }
		?>




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