<?php

class asnt_category_wise_posts_listing_sidebar extends WP_Widget {
	function __construct() {
		parent::__construct(
		// widget ID
		'asnt_category_wise_posts_listing_sidebar',
		// widget name
		__('ASNT Category wise posts listing', ' asnt_category_wise_posts_listing_sidebar_domain'),
		// widget description
		array( 'description' => __( 'Widget for displaying Top stories form selected category.', 'asnt_category_wise_posts_listing_sidebar_domain' ), )
		);
		}
	
	function form($instance) {

		$instance = wp_parse_args( (array) $instance , array( 
			'title' => '',
			'title_link' => false,
			'category' => '',
			'numitems' => ''
			) 
		);
	
	$title = strip_tags($instance['title']);
	$title_link = (bool) strip_tags($instance['title_link']);
	$category = strip_tags($instance['category']);
	$numitems = strip_tags($instance['numitems']);

		?>
<p><label for="<?php echo $this->get_field_id('title'); ?>">Title:</label> <input class="widefat"
        id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>"
        type="text" value="<?php echo esc_attr($title); ?>" /></p>

<p>
    <label for="<?php echo $this->get_field_id('title_link'); ?>">
        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('title_link'); ?>"
            name="<?php echo $this->get_field_name('title_link'); ?>" <?php checked( (bool) $title_link, true ); ?> />
        <?php _e( 'Link title to category page.' ); ?>
    </label>
</p>

<p>
    <label for="<?php echo $this->get_field_id('category'); ?>">Select Category:</label>

    <select id="<?php echo $this->get_field_id('category'); ?>"
        name="<?php echo  $this->get_field_name('category'); ?>[]" class="widefat">

        <?php	
						$terms = get_terms([
							'taxonomy' => 'category',
							'hide_empty' => false,
						]);  
					  if(!empty($terms) && is_array($terms)) {
						foreach ($terms as $term)  : 
							$term_name = $term->name;
							$term_id = $term->term_id;?>

        <option value="<?php echo $term_id; ?>" <?php  selected( $instance['category'], $term->term_id ); ?>>
            <?php echo $term_name; ?>
            <?php 
						 endforeach;
					  }
					 ?>
    </select>
</p>

<p>
    <label for="<?php $this->get_field_id('numitems'); ?>">Posts to Display</label>
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
		$instance['title_link'] = strip_tags($new_instance['title_link']);
		$instance['category'] = (isset( $new_instance['category'] )) ? implode(',', (array) $new_instance['category']) : '';
		$instance['numitems'] = (isset( $new_instance['numitems'] )) ? implode(',', (array) $new_instance['numitems']) : '';
		
		return $instance;
	}

	public function widget( $args, $instance ) {
		$title = apply_filters('widget_title', stripslashes($instance['title']));
		$title_link = (bool) $instance['title_link'];
		$numitems = isset($instance['numitems']) ? strip_tags($instance['numitems']) : ''; 
		$category = isset($instance['category']) ? strip_tags($instance['category']) : '';

		$Category_id = $category;
		$term_obj = get_term_by('id', $Category_id, 'category');
		$term_name = $term_obj->name;
		$term_slug = get_category_link($Category_id);

		?>
<div class="right-sidebar-top-stories desktop-show" style="margin: 40px 0px;">
    <?php
			$query = array (
				'paged' => 1,
				'posts_per_page' => $numitems,
				'offset' => 0,
				'post_status' => 'publish',
				'ignore_sticky_posts' => 0,
				'orderby' => 'date',
				'order' => 'DESC',
				'post_type' => 
				array (
				'post' => 'post',
				),
				'tax_query' =>
				array (
					'taxonomy_category' => 
					array (
						'taxonomy' => 'category',
						'field' => 'id',
						'terms' => $Category_id,
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
					$short_title = substr($title, 0, 70)."[...]";
					$slug = get_permalink( $id);
                    $featured_image = get_the_post_thumbnail( $post_id->ID, 'top-stories-right-sidebar' );
                    $post_date = $post_id->post_date;
                    $post_date = date('j/n/Y', strtotime($post_date));   
					if(!$featured_image){
						$short_title = substr($title, 0, 90)."[...]";
					}
                    if($featured_image){	?>
    <a href="<?php print $slug; ?>">
        <div class="top-stories-area">
            <div class="top-stories-image">
                <?php print $featured_image;?>
            </div>
            <div class="top-stories-description">
                <p class="title">
                    <?php if(strlen($title) > 70){ ?>
                    <?php echo $short_title; ?>
                    <?php }else{ ?>
                    <?php echo $title; ?>
                    <?php } ?>
                </p>
                <p class="post-date"><?php print $post_date; ?></p>
            </div>
        </div>
    </a>
    <?php }else { ?>
    <a href="<?php print $slug; ?>">
        <div class="top-stories-description">
            <p class="title">
                <?php if(strlen($title) > 105){ ?>
                <?php echo $short_title; ?>
                <?php }else{ ?>
                <?php echo $title; ?>
                <?php } ?>
            </p>
            <p class="post-date"><?php print $post_date; ?></p>
        </div>
    </a>
    <?php } 
				} ?>

</div>
<?php
	}

}