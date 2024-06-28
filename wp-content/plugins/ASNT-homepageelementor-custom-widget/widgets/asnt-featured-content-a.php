<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Custom Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Asnt_featured_content_a extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Custom widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'asnt_featured_content_a';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Custom widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'ASNT Featured Content A', 'asnt_featured_content_a' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Custom widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-code';
	}

	/**
	 * Get custom help URL.
	 *
	 * Retrieve a URL where the user can get more information about the widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget help URL.
	 */
	public function get_custom_help_url() {
		return 'https://developers.elementor.com/docs/widgets/';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the Custom widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'general' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the Custom widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'custom', 'widget' ];
	}

	/**
	 * Register Custom widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$display_option = array(
			"4" => "4",
			"5" => "5",
		);

		$option = [];
		$terms = get_terms([
			'taxonomy' => 'category',
			'hide_empty' => false,
		]);
		foreach ($terms as $term) {
			$option[$term->term_id] = $term->name;
			
		}
		

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'asnt-featured-content-a' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'list',
			[
				'label' => esc_html__( 'Select Category for first item', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'multiple' => true,
				'options' => $option,
			]
		);

		$this->add_control(
			'list1',
			[
				'label' => esc_html__( 'Select Category for Top Stories', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'multiple' => true,
				'options' => $option,
			]
		);

		$this->add_control(
			'post_to_display',
			[
				'label' => esc_html__( 'Post To Display', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'multiple' => true,
				'options' => $display_option,
			]
		);


		$this->end_controls_section();

	}

	/**
	 * Render Custom widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();
        $post_display = $settings['post_to_display'];
        $Categories = $settings['list'];
        $TopStories_cat = $settings['list1'];
		
		$Category_id = $Categories;
		$term_obj = get_term_by('id', $Category_id, 'category');
		$term_name = $term_obj->name;
		$term_slug = get_category_link($Category_id);

		// $Category_id = $Categories;
		// $TopStories_term_obj = get_term_by('id', $Category_id, 'category');
		// $TopStories_term_name = $term_obj->name;
		// $TopStories_term_slug = get_category_link($Category_id);

		// print $TopStories_cat;

		?>
		<div class="row top-stories-section">	  
			<div class="top-news-section col-sm-7">
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
					'meta_query' => 
					array (
						0 => 
						array (
						'key' => 'is_it_featured',
						'value' => 'yes',
						'compare' => '=',
						'type' => 'CHAR',
						),
					),
				);
				$allposts = get_posts($query);
				foreach ($allposts as $key => $post_id) {
					$id = $post_id->ID;
					$title = get_the_title($id);
					// $short_title = substr($title, 0, 85)."...";
					$content = get_post($id);
					$content = $content->post_content;
					$content = strip_tags($content);
					// $short_content = substr($content, 0, 150)."[...]";
					$slug = get_permalink( $id);
					$featured_image = get_the_post_thumbnail( $post_id->ID, 'top-stories-thumb' );
					$image = get_post_thumbnail_id( $post_id->ID);
					$image_caption = wp_get_attachment_caption( $image );
					$excerpt = get_the_excerpt($id);
					// $short_excerpt = substr($excerpt, 0, 150)."[...]";


					$exclude_id[] = $id;
					?>
					<div class="most-viewed">
						<a href="<?php print $slug; ?>">
						    <?php if($featured_image){ ?>
								<div class="featured-image">
									<?php echo $featured_image ?>
									<p class="image-caption"><?php print $image_caption; ?></p>
								</div>
							<?php } else{ ?>
								<div class="featured-image without-image"></div>
							<?php }	?>
							<div class="description-section">	
								<div class="title"><h2> <?php
									// if(strlen($title) > 85){
									// 	print $short_title;
									// }else{
										print $title;
									// }
									 ?>
								</h2></div>					
								<div class="desc"><p>
									<?php
										if($excerpt){
											// if(strlen($excerpt) > 150){
											// 	print $short_excerpt;
											// }else{
												print $excerpt;
											// }
										}else{
											// if(strlen($content) > 150){
											// 	print $short_content;
											// }else{
												print $content;
											// }
										}
									?>
								</p></div>
							</div>
						</a>
					</div>
				<?php } ?>				
			</div>
			<div class="top-news-section col-sm-5">
				<?php

				$totaldisplay = $post_display - 1;
				$other_stories = array (
					'paged' => 1,
					'posts_per_page' => $totaldisplay,
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
							'terms' => $TopStories_cat,
							// 'terms' => 64870,
							'operator' => 'IN',
							'include_children' => false,
						),
					),
					'post__not_in' => $exclude_id,
				);
				$topposts = get_posts($other_stories); ?>
				<div class="other-top-stories">
					<h2>Other Top Stories</h2>
					<ul>
						<?php
							foreach ($topposts as $post_id) {
								$id = $post_id->ID;
								$title = get_the_title($id);
								$short_title = substr($title, 0, 100)."[...]";
								$slug = get_permalink( $id);
								if(strlen($title) > 100){
									?>
										<li>
											<a href="<?php print $slug; ?>"><?php echo $short_title; ?></a>
										</li>
									<?php
								}else{
									?>
										<li>
											<a href="<?php print $slug; ?>"><?php echo $title; ?></a>
										</li>
									<?php
								}
							
							} ?>
					</ul>
				</div>
			</div> 	
		</div>	
		<?php
	}

}