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
class Asnt_featured_content_b extends \Elementor\Widget_Base {

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
		return 'asnt_featured_content_b';
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
		return esc_html__( 'ASNT Featured Content B', 'asnt_featured_content_b' );
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
				'label' => esc_html__( 'Content', 'asnt_featured_content_b' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'list',
			[
				'label' => esc_html__( 'Select Category', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'multiple' => true,
				'options' => $option,
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
        $Categories = $settings['list'];
		
		$Category_id = $Categories;
		$term_obj = get_term_by('id', $Category_id, 'category');
		$term_name = $term_obj->name;
		$term_slug = get_category_link($Category_id);

		?>
		<div class="featured-contents-3a featured-contents">
			<?php

				$query_3a = array (
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

				$postsbyid_3a = get_posts($query_3a);
				foreach ($postsbyid_3a as $post_id) {
					$id_3a = $post_id->ID;
					$title_3a = get_the_title($id_3a);
					// $short_title_3a = substr($title_3a, 0, 45)."[...]";
					$slug_3a = get_permalink( $id_3a);
					$featured_image_3a = get_the_post_thumbnail( $post_id->ID, 'featured-contents3a-thumb' );
				}

				
				if($featured_image_3a){
					?>
					<div class="most-viewed">
						<!-- <h3 class="tag-name"><a href = "<?php print $term_slug; ?>"><?php echo $term_name ?></a></h3> -->
						<a href="<?php print $slug_3a; ?>">
							<div class="featured-image"><?php echo $featured_image_3a ?></div>
							<?php if($title_3a || $short_title_3a){ ?>
								<div class="title"><p><?php 
									// if(strlen($title_3a) > 45){
									// 	print $short_title_3a;
									// }else{
										print $title_3a;
									// }
								?></p></div>
							<?php } ?>
						</a>
					</div>
						
					<?php
				}else{					
				?>
					<div class="most-viewed">
						<!-- <h3 class="tag-name"><a href = "<?php print $term_slug; ?>"><?php echo $term_name ?></a></h3> -->
						<a href="<?php print $slug_3a; ?>">
							<div class="featured-image without-image"></div>
							<?php if($title_3a || $short_title_3a){ ?>
								<div class="title"><p><?php 
									// if(strlen($title_3a) > 45){
									// 	print $short_title_3a;
									// }else{
										print $title_3a;
									// }
								?></p></div>
							<?php } ?>
						</a>
					</div>
				<?php
				}
				wp_reset_postdata();
			?>
		</div>
		<?php
	}

}