<?php

class asnt_upcoming_webinars extends WP_Widget {
	function __construct() {
		parent::__construct(
		// widget ID
		'asnt_upcoming_webinars',
		// widget name
		__('ASNT Upcoming Webinars', ' asnt_upcoming_webinars_domain'),
		// widget description
		array( 'description' => __( 'Widget for displaying Upcoming Webinars.', 'asnt_upcoming_webinars_domain' ), )
		);
		}
	
	function form($instance) {

		$instance = wp_parse_args( (array) $instance , array( 
			'title' => '',
			'numitems' => ''
			) 
		);
	
		$title = strip_tags($instance['title']);
		$numitems = strip_tags($instance['numitems']);

		?>
<p><label for="<?php echo $this->get_field_id('title'); ?>">Title:</label> <input class="widefat"
        id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>"
        type="text" value="<?php echo esc_attr($title); ?>" /></p>


<p>
    <label for="<?php $this->get_field_id('numitems'); ?>">No. of Events to Display</label>
    <select id="<?php echo $this->get_field_id('numitems'); ?>"
        name="<?php echo  $this->get_field_name('numitems'); ?>[]" class="widefat">
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
        <option value="<?php echo $option; ?>" <?php  selected( $instance['numitems'], $option ); ?>>
            <?php echo $value; ?>
            <?php 
						 endforeach;
					  }
					  ?>
    </select>
</p>

<?php
	}

	function update($new_instance, $old_instance) {
		if( !isset($new_instance['title']) ) // user clicked cancel
		return false;		
		
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['numitems'] = (isset( $new_instance['numitems'] )) ? implode(',', (array) $new_instance['numitems']) : '';
		
		return $instance;
	}

	public function widget( $args, $instance ) {
		$title = apply_filters('widget_title', stripslashes($instance['title']));
		$numitems = isset($instance['numitems']) ? strip_tags($instance['numitems']) : ''; 

		?>
<div class="right-sidebar-top-stories desktop-show" style="margin: 40px 0px;">
    <?php
			$query = array (
				'posts_per_page' => $numitems,
                'paged' => 1,
                'offset' => 0,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 0,
                // 'orderby' => 'date',
                // 'order' => 'ASC',
                'post_type' =>
                array(
                    'post' => 'tribe_events',
                ),
                'meta_query' => array(
                  'relation' => 'AND',
                  array(
                    'key' => 'bridge_tower_media_conferences',
                    'compare' => 'NOT EXISTS' // Meta query to filter events which are not bridge Tower Media
                  ),
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
				'tax_query' => array (
					'taxonomy_category' => array (
						'taxonomy' => 'tribe_events_cat',
						'field' => 'name',
						'terms' => 
						array (
						0 => 'webinars',
						),
						'operator' => 'IN',
						'include_children' => false,
					),
				),
			);
			$topposts = get_posts($query);?>
    <h2 class="main-heading">
        <?php if($title_link == "1"){ ?>
        <a href="<?php print $term_slug; ?>"><?php print $title; ?></a>
        <?php }else{ ?>
        <?php print $title; ?>
        <?php } ?>
    </h2>
    <?php
				foreach ($topposts as $post_id) {
					$id = $post_id->ID;
					$title = get_the_title($id);
					$short_title = substr($title, 0, 120)."[...]";
                    $featured_image = get_the_post_thumbnail( $post_id->ID, 'top-stories-right-sidebar' );
                    // $post_date = $post_id->post_date;
                    // $post_date = date('j/n/Y', strtotime($post_date));
					
					$evnt_startdate =  tribe_get_start_date( $id, false,"l M jS, Y");
					$evnt_starttime =  tribe_get_start_date( $id, false,"g:i a");
					$content_post = get_post($id);
					$content = $content_post->post_content;
					$content = strip_tags($content);		
					$content = substr($content, 0, 200)."[...]";
					$slug = get_permalink($id);
					$excerpt = get_the_excerpt($id);
					$short_excerpt = substr($excerpt, 0, 200)."[...]";
                    
                    if($featured_image){	?>
    <a href="<?php print $slug; ?>">
        <div class="top-stories-area">
            <div class="top-stories-image">
                <?php print $featured_image;?>
            </div>
            <div class="top-stories-description">
                <p class="title">
                    <?php if(strlen($title) > 120){ ?>
                    <?php echo $short_title; ?>
                    <?php }else{ ?>
                    <?php echo $title; ?>
                    <?php } ?>
                </p>
                <p class="post-date"><?php print $evnt_startdate  .' - '.  $evnt_starttime; ?></p>
            </div>
        </div>
    </a>
    <?php }else { ?>
    <a href="<?php print $slug; ?>">
        <div class="top-stories-description">
            <p class="title">
                <?php if(strlen($title) > 120){ ?>
                <?php echo $short_title; ?>
                <?php }else{ ?>
                <?php echo $title; ?>
                <?php } ?>
            </p>
            <p class="post-date"><?php print $evnt_startdate  .' - '.  $evnt_starttime; ?></p>
        </div>
    </a>
    <?php } 
				} ?>

</div>
<?php
	}

}