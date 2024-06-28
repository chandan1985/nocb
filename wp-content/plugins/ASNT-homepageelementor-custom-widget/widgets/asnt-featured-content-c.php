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
class Asnt_featured_content_c extends \Elementor\Widget_Base {

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
		return 'asnt_featured_content_c';
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
		return esc_html__( 'ASNT Featured Content C', 'asnt_featured_content_c' );
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
				'label' => esc_html__( 'Content', 'asnt_featured_content_c' ),
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
		<div class="featured-contents-3b featured-contents">
			<?php

				$query_3b = array (
					'paged' => 1,
					'posts_per_page' => '1',
					'offset' => 0,
					'post_status' => 'publish',
					'ignore_sticky_posts' => 0,
					'orderby' => 'date',
					'order' => 'DESC',
					'post_type' => 
					array (
						'sponsored_content' => 'sponsored_content',
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
				$postsbyid_3b = get_posts($query_3b);
				foreach ($postsbyid_3b as $post_id) {
					$id = $post_id->ID;
					$title = get_the_title($id);
					$short_title = substr($title, 0, 45)."[...]";
					$slug = get_permalink( $id);
					$featured_image = get_the_post_thumbnail( $post_id->ID, 'featured-contents3a-thumb' );
				}
				$end_date = get_post_meta($id, 'meta_sc_end', true);
				if(!$end_date || strtotime($end_date) > strtotime('now')) { //Check end_date is less than current date or not
					if($featured_image){
						?>
						<div class="most-viewed">
							<div class="post-tag-sponsored">
								<h3 class="tag-name"><a href = "<?php print $term_slug; ?>"><?php echo $term_name ?></a></h3>
								<a href="/sponsored-contents"><p class="sponsored">Sponsored</p></a>
							</div>	
							<a href="<?php print $slug; ?>">
								<div class="featured-image"><?php echo $featured_image ?></div>
								<?php if($title || $short_title){ ?>
									<div class="title"><p><?php 
										if(strlen($title) > 45){
											print $short_title;
										}else{
											print $title;
										}
									?></p></div>
								<?php } ?>
							</a>
						</div>
							
						<?php
					}else{					
					?>
						<div class="most-viewed">
							<div class="post-tag-sponsored">
								<h3 class="tag-name"><a href = "<?php print $term_slug; ?>"><?php echo $term_name ?></a></h3>
								<a href="/sponsored-contents"><p class="sponsored">Sponsored</p></a>
							</div>	
							<a href="<?php print $slug; ?>">
								<div class="featured-image without-image"></div>
								<?php if($title || $short_title){ ?>
									<div class="title"><p><?php 
										if(strlen($title) > 45){
											print $short_title;
										}else{
											print $title;
										}
									?></p></div>
								<?php } ?>
							</a>
						</div>
					<?php
					}
				}
				wp_reset_postdata();
			?>
		</div>
		<?php
	}

}