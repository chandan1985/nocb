<?php

class asnt_category_wise_single_post_sidebar extends WP_Widget {
	function __construct() {
		parent::__construct(
		// widget ID
		'asnt_category_wise_single_post_sidebar',
		// widget name
		__('ASNT Category wise with single post', ' asnt_category_wise_single_post_sidebar_domain'),
		// widget description
		array( 'description' => __( 'Widget for displaying latest post of particular category..', 'asnt_category_wise_single_post_sidebar_domain' ), )
		);
		}
	
	function form($instance) {

		$instance = wp_parse_args( (array) $instance , array( 
			'title' => '',
			'title_link' => false,
			'category' => '',
			) 
		);
	
	$title = strip_tags($instance['title']);
	$title_link = (bool) strip_tags($instance['title_link']);
	$category = strip_tags($instance['category']);

		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>">Title:</label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p>
			 <label for="<?php echo $this->get_field_id('title_link'); ?>">
				 <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('title_link'); ?>" name="<?php echo $this->get_field_name('title_link'); ?>"<?php checked( (bool) $title_link, true ); ?> />
				 <?php _e( 'Link title to category page.' ); ?>
			 </label>
		 </p>

		<p>
			 <label for="<?php echo $this->get_field_id('category'); ?>">Select Category:</label>
			 
			 <select  id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo  $this->get_field_name('category'); ?>[]" class="widefat">
				
					 <?php	
						$terms = get_terms([
							'taxonomy' => 'category',
							'hide_empty' => false,
						]);  
					  if(!empty($terms) && is_array($terms)) {
						foreach ($terms as $term)  : 
							$term_name = $term->name;
							$term_id = $term->term_id;?>

							 <option value="<?php echo $term_id; ?>" <?php  selected( $instance['category'], $term->term_id ); ?>> <?php echo $term_name; ?>
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
		
		return $instance;
	}

	public function widget( $args, $instance ) {
		$title = apply_filters('widget_title', stripslashes($instance['title']));
		$title_link = (bool) $instance['title_link'];
		$category = isset($instance['category']) ? strip_tags($instance['category']) : '';

		$Category_id = $category;
		$term_obj = get_term_by('id', $Category_id, 'category');
		$term_name = $term_obj->name;
		$term_slug = get_category_link($Category_id);

		?>
		<div class="latest-category-section desktop-show">
			<?php
			$query = array (
				'paged' => 1,
				'posts_per_page' => 1,
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
                    <a href="<?php print $term_slug; ?>" ><?php print $title; ?></a>
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
					$featured_image = get_the_post_thumbnail( $post_id->ID, 'homepage-categories-thumb' );
					$content = get_the_excerpt($id);
					$content = strip_tags($content);
					$short_content = substr($content,0,90) . "[...]";	
					?>
					<div class="most-viewed">
						<a href="<?php print $slug; ?>">
							<?php if($featured_image){	?>
								<div class="featured-image"><?php print $featured_image;?></div>
							<?php }else { ?>
							<div class="featured-image without-image"></div>
							<?php } ?>
							<div class="title"><h2> <?php
								if(strlen($title) > 80){
									print $short_title;
								}else{
									print $title;
								} ?>
							</h2></div>
							<div class="desc"><p> <?php
								if(strlen($content) > 110){
									print $short_content;
								}else{
									print $content;
								} ?>
							</p></div>
						</a>
					</div>
				
				<?php } 
		?>
							
		</div>	
	<?php
	}

}


