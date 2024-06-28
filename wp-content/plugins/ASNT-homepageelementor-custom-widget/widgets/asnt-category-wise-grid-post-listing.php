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
class ASNT_category_wise_grid_post_listing extends \Elementor\Widget_Base {

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
		return 'asnt_category_wise_grid_post_listing';
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
		return esc_html__( 'ASNT Category Wise Grid Post Listing', 'asnt_category_wise_grid_post_listing' );
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
				'label' => esc_html__( 'Content', 'asnt_category_wise_grid_post_listing' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		// $this->add_control(
		// 	'category_slug',
		// 	[
		// 		'label' => esc_html__( 'Select Category', 'textdomain' ),
		// 		'type' => \Elementor\Controls_Manager::SELECT2,
		// 		'label_block' => true,
		// 		'multiple' => true,
		// 		//'maximumSelectionLength' => 6,
		// 		'options' => $option,
		// 	]
		// );
		$this->add_control(
			'category_slug1',
			[
				'label' => esc_html__( 'Enter Category Slug Comma Separated', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'label_block' => true,
				'multiple' => true,
				//'maximumSelectionLength' => 6,
				'options' => $option,
			]
		);

		$this->add_control(
			'300x250_ads_mobile',
			[
				'label' => esc_html__( '300*250 Ad for mobile', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Please enter Ad code here', 'textdomain' ),
				'label_block' => true,
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
        // $Category = $settings['category_slug'];
		if(!empty($Category) && is_array($Category) ){
			$Categories = array_slice($Category, 0, 6);
		}
		
		$small_ad = $settings['300x250_ads_mobile'];

		// $Category_id = $Categories;
		// $term_obj = get_term_by('id', $Category_id, 'category');
		// $term_name = $term_obj->name;
		// $term_slug = get_category_link($Category_id);

		$category_slugs = $settings['category_slug1'];
		$category_array = explode(',', $category_slugs);
		if(!empty($Category) && is_array($Category) ){
			$category_array = array_slice($category_array, 0, 6);
		}
		
		$term_ids = [];
		foreach($category_array as $single_category){
			$term = get_term_by('slug', $single_category, 'category');
			$term_ids[] = $term->term_id;
		}
		// print $category_slugs;
		// print "<pre>"; print_r($term_ids); print "</pre>";
		?>
		<div class="homepage-categories-section">

			<?php
			// foreach($Category_id as $key => $catid) {
			foreach($term_ids as $key => $catid) {
				$term_obj = get_term_by('id', $catid, 'category');
				$term_name = $term_obj->name;
				$term_slug = get_category_link($catid);

				$query = array (
					'paged' => 1,
					'posts_per_page' => '1',
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
						'terms' => $catid,
						'operator' => 'IN',
						'include_children' => false,
						),
					),
				); 
				$postsbyid = get_posts($query);
				foreach ($postsbyid as $post_id) {
					$id = $post_id->ID;
					$title = get_the_title($id);
					// $short_title = substr($title, 0, 58)."[...]";
					$content_post = get_post($id);
					$content = $content_post->post_content;
					$content = strip_tags($content);		
					// $short_content = substr($content, 0, 100)."[...]";
					$excerpt = get_the_excerpt($id);
					// $short_excerpt = substr($excerpt, 0, 100)."[...]";
					$slug = get_permalink( $id);
					$featured_image = get_the_post_thumbnail( $post_id->ID, 'homepage-categories-thumb' );
				
					if($featured_image){
						?>
							<div class="content-box">
								<h2 class="category-title"><a href="<?php print $term_slug; ?>"><?php print $term_name; ?></a></h2>
								<a href="<?php print $slug; ?>">
									<div class="featured-image"><?php print $featured_image;?></div>
									<?php
										// if(strlen($title) > 58){
										// 	print '<h2 class="rc-img-title">'.$short_title.'</h2>';
										// }else{
											print '<h2 class="rc-img-title">'.$title.'</h2>';
										// }
									?>
									<div class="desc"><p>
									<?php
										if($excerpt){
											// if(strlen($excerpt) > 100){
											// 	print $short_excerpt;
											// }else{
												print $excerpt;
											// }
										}else{
											// if(strlen($content) > 100){
											// 	print $short_content;
											// }else{
												print $content;
											// }
										}
									?>
									</p></div>
								</a>
							</div>
						<?php
					}else{
					?>	
						<div class="content-box">
							<h2 class="category-title"><a href="<?php print $term_slug; ?>"><?php print $term_name; ?></a></h2>
							<a href="<?php print $slug ?>">
								<div class="featured-image without-image">
									<?php
										// if(strlen($title) > 58){
										// 	print '<h2 class="rc-img-title">'.$short_title.'</h2>';
										// }else{
											print '<h2 class="rc-img-title">'.$title.'</h2>';
										// }
									?>
									<div class="desc"><p>
									<?php
										if($excerpt){
											// if(strlen($excerpt) > 100){
											// 	print $short_excerpt;
											// }else{
												print $excerpt;
											// }
										}else{
											// if(strlen($content) > 100){
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
						<?php
					}
				}
				if(wp_is_mobile()){
					if($key == 2){
						?>
							<div class="ads-section"><?php print $small_ad;?></div>
						<?php
					} 
				}
			} ?>
		</div> <?php		
	}

}
