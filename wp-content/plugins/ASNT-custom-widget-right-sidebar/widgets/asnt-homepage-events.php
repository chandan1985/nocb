<?php

class asnt_homepage_events extends WP_Widget {
	function __construct() {
		parent::__construct(
		// widget ID
		'asnt_homepage_events',
		// widget name
		__('ASNT Homepage Events', ' asnt_homepage_events_domain'),
		// widget description
		array( 'description' => __( 'Widget for displaying Homepage Events.', 'asnt_homepage_events_domain' ), )
		);
		}
	
	function form($instance) {

		$instance = wp_parse_args( (array) $instance , array( 
			'title' => '',
			'numitems' => '',
            'cta_text' => '',
            'cta_link' => ''
			) 
		);
	
		$title = strip_tags($instance['title']);
		$numitems = strip_tags($instance['numitems']);
		$cta_text = strip_tags($instance['cta_text']);
		$cta_link = strip_tags($instance['cta_link']);


		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>">Title:</label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		
		<p>
			 <label for="<?php $this->get_field_id('numitems'); ?>">No. of Events to Display</label>
			 <select id="<?php echo $this->get_field_id('numitems'); ?>" name="<?php echo  $this->get_field_name('numitems'); ?>[]" class="widefat">
				<?php
					$display_option = array(
						"1" => __( '1', 'text_domain' ),
						"2" => __( '2', 'text_domain' ),
						"3" => __( '3', 'text_domain' ),
						"4" => __( '4', 'text_domain' ),
						"5" => __( '5', 'text_domain' ),
						"6" => __( '6', 'text_domain' ),
						"7" => __( '7', 'text_domain' ),
						"8" => __( '8', 'text_domain' ),
						"9" => __( '9', 'text_domain' ),
						"10" => __( '10', 'text_domain' ),
					);
					if(!empty($display_option) && is_array($display_option)) {
						foreach ($display_option as $option => $value)  : 
							?>
							 <option value="<?php echo $option; ?>" <?php  selected( $instance['numitems'], $option ); ?>> <?php echo $value; ?>
							 <?php 
						 endforeach;
					  }
					  ?>
            </select>
		</p>

        <p><label for="<?php echo $this->get_field_id('cta_text'); ?>">CTA Text:</label> <input class="widefat" id="<?php echo $this->get_field_id('cta_text'); ?>" name="<?php echo $this->get_field_name('cta_text'); ?>" type="text" value="<?php echo esc_attr($cta_text); ?>" /></p>

        <p><label for="<?php echo $this->get_field_id('cta_link'); ?>">CTA Link:</label> <input class="widefat" id="<?php echo $this->get_field_id('cta_link'); ?>" name="<?php echo $this->get_field_name('cta_link'); ?>" type="text" value="<?php echo esc_attr($cta_link); ?>" /></p>


		 <?php
	}

	function update($new_instance, $old_instance) {
		if( !isset($new_instance['title']) ) // user clicked cancel
		return false;		
		
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['numitems'] = (isset( $new_instance['numitems'] )) ? implode(',', (array) $new_instance['numitems']) : '';
        $instance['cta_text'] = strip_tags($new_instance['cta_text']);
        $instance['cta_link'] = strip_tags($new_instance['cta_link']);
		
		return $instance;
	}

	public function widget( $args, $instance ) {
		$title = apply_filters('widget_title', stripslashes($instance['title']));
		$numitems = isset($instance['numitems']) ? strip_tags($instance['numitems']) : ''; 
		$cta_text = isset($instance['cta_text']) ? strip_tags($instance['cta_text']) : ''; 
		$cta_link = isset($instance['cta_link']) ? strip_tags($instance['cta_link']) : ''; 

        ?>
		<div class="events-and-webinar">
			<?php
			
				$events = array (
					'paged' => 1,
					'posts_per_page' => $numitems,
					'offset' => 0,
					'post_status' => 'publish',
					'ignore_sticky_posts' => 0,
					'post_type' => 
					array (
					'tribe_events' => 'tribe_events',
					),
					'meta_query' => array(
						'relation' => 'AND',
						array(
						  'key' => '_EventStartDate', // Check the start date field
						  'value' => date("Y-m-d"), // Set today's date (note the similar format)
						  'compare' => '>=', // Return the ones greater than today's date
						  'type' => 'DATE' // Let WordPress know we're working with date
						),
						'_EventStartDate__order_by' => array(
							'key' => '_EventStartDate',
							'type' => 'DATE',
							'compare' => '>=',
						)
					),
					'orderby' => array( '_EventStartDate__order_by' => 'ASC' ),
				);

				$events_postsbyid = get_posts($events);
				// print "<pre>"; print_r ($events_postsbyid); print "</pre>";
				print '<h2>EVENTS</h2>';
				foreach ($events_postsbyid as $post_id) {
					$id = $post_id->ID;
					$title = get_the_title($id);
					$evnt_startdate =  tribe_get_start_date( $id, false,"F jS, Y");
					$evnt_starttime =  tribe_get_start_date( $id, false,"g:i A");
					$content_post = get_post($id);
					$content = $content_post->post_content;
					$content = strip_tags($content);	
					$content = preg_replace('/<div id="gallery-1"[^>]*>.*?<\/div>|\[.*?\]|\[figcaption.*\[\/figcaption\]/si', '', $content);	
					$short_content = substr($content, 0, 140)."[...]";
					$slug = get_permalink($id);
					$excerpt = $content_post->post_excerpt;
					$short_excerpt = substr($excerpt, 0, 140)."[...]";
					$comingSoon = get_post_meta( $id, 'coming_in_late_year', true );
					?>
					<div class="event-listing">
						<h4><a href="<?php print $slug; ?>"><?php print $title;?></a></h4>
						<p style="font-weight: 700">
						<?php 
							$comingSoon == "yes" ? 
							print 'Details coming soon!' :
							print $evnt_startdate  .' | '.  $evnt_starttime;
						?>
						</p>
						<div class="desc"><p>
							<?php
								if($excerpt){
									if(strlen($excerpt) > 140){
										print $short_excerpt;
									}else{
										print $excerpt;
									}
								}else{
									if(strlen($content) > 140){
										print $short_content;
									}else{
										print $content;
									}
								}
							?>
						</p></div>
					</div>
					<?php
					}
				?>
				<a href="<?php print $cta_link; ?>" class="btn-custom"><?php print $cta_text; ?></a>
			
		</div>


	    <?php	
	}

}


