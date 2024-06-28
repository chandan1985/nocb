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
class Asnt_category_wise_slideshow_posts extends \Elementor\Widget_Base {

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
		return 'asnt_category_wise_slideshow_posts';
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
		return esc_html__( 'ASNT Category wise slideshow posts', 'asnt_category_wise_slideshow_posts' );
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
			"5" => "5",
			"6" => "6",
			"7" => "7",
			"8" => "8",
			"9" => "9",
			"10" => "10",
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
				'label' => esc_html__( 'Content', 'asnt-category-wise-slideshow-posts' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
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

		$this->add_control(
			'link_title',
			[
				'label' => esc_html__( 'Link Heading to category page', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'textdomain' ),
				'label_off' => esc_html__( 'No', 'textdomain' ),
				'return_value' => 'yes',
				'default' => 'no',
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
		$title_link = $settings['link_title'];
		
		$Category_id = $Categories;
		$term_obj = get_term_by('id', $Category_id, 'category');
		$term_name = $term_obj->name;
		$term_slug = get_category_link($Category_id);
		?>

		<div class="featured-contents-row">
			<div class="column-right featured-contents">
				<?php
				$query = array (
						'paged' => 1,
						'posts_per_page' => $post_display,
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
					$postsbyid = get_posts($query);?>
					<h2 class="main-heading">
						<?php if($title_link == "yes"){ ?>
							<a href="<?php print $term_slug; ?>" ><?php print $term_name; ?></a>
						<?php }else{ ?>
							<?php print $term_name; ?>
						<?php } ?>
					</h2>
					<div class="featuredContent_slideshow" style="display:none">
					<?php

					foreach ($postsbyid as $post_id) {
						$id = $post_id->ID;
						$title = get_the_title($id);
						// $short_title = substr($title,0,70) . "[...]";
						$slug = get_permalink( $id);
						$featured_image = get_the_post_thumbnail( $post_id->ID, 'homepage-categories-thumb' );
						$content = get_the_excerpt($id);
						$content = strip_tags($content);
						// $short_content = substr($content,0,130) . "[...]";
							?>
							<div class="slideshowContents">
							<?php if($featured_image){	?>
							<div class="most-viewed">
							<?php }else { ?>
							<div class="most-viewed no-image">
							<?php } ?>
									<a href="<?php print $slug; ?>">
										<?php if($featured_image){	?>
											<div class="featured-image"><?php print $featured_image;?></div>
										<?php }else { ?>
										<div class="featured-image without-image"></div>
										<?php } ?>
										<div class="slideshow-content">
										<div class="title"><h2> <?php
											// if(strlen($title) > 70){
											// 	print $short_title;
											// }else{
												print $title;
											// }
											 ?>
										</h2></div>
										<div class="desc"><p> <?php
											// if(strlen($content) > 130){
											// 	print $short_content;
											// }else{
												print $content;
											// } 
											?>
										</p></div>
										</div>
									</a>
								</div>
							</div>
						<?php
						}
					?>
				</div>
			</div>
		</div>
	<?php		
	}

}