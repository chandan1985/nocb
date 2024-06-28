<?php

class asnt_header_latest_event extends WP_Widget {
	function __construct() {
		parent::__construct(
		// widget ID
		'asnt_header_latest_event',
		// widget name
		__('ASNT Header Latest Event', ' asnt_header_latest_event_domain'),
		// widget description
		array( 'description' => __( 'Widget for displaying Header Latest Event.', 'asnt_header_latest_event_domain' ), )
		);
		}
	
	function form($instance) {

		$instance = wp_parse_args( (array) $instance , array( 
			'title' => '',
			) 
		);
	
		$title = strip_tags($instance['title']);


		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>">Title:</label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>


		 <?php
	}

	function update($new_instance, $old_instance) {
		if( !isset($new_instance['title']) ) // user clicked cancel
		return false;		
		
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		
		return $instance;
	}

	public function widget( $args, $instance ) {
		$title = apply_filters('widget_title', stripslashes($instance['title']));

        ?>
		<div class="header-latest-event">
			<?php
			
				$events = array (
					'paged' => 1,
					'posts_per_page' => 1,
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
				foreach ($events_postsbyid as $post_id) {
					$id = $post_id->ID;
					$title = get_the_title($id);
                    $slug = get_permalink($id);
					?>
					<div class="event-listing">
                        <h6 class="event-heading">Upcoming Event</h6>
						<p class="event-title"><a href="<?php print $slug; ?>"><?php print $title;?></a></p>
					</div>
					<?php
					}
				?>
			
		</div>


	    <?php	
	}

}


