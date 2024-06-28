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
class ASNT_sponsored_category_listing extends \Elementor\Widget_Base {

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
		return 'asnt_sponsored_category_listing';
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
		return esc_html__( 'ASNT Sponsored Category Listing', 'asnt_sponsored_category_listing' );
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
			"3" => "3",
			"4" => "4",
			"5" => "5",
		);

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'asnt_sponsored_category_listing' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$this->add_control(
			'post_to_display',
			[
				'label' => esc_html__( 'Post to Display', 'textdomain' ),
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
        $title = $settings['title'];
		$post_to_display = $settings['post_to_display'];
		?>
        <div class="sponsored-section ">
            <h2><a href="/sponsored-contents/"><?php print $title; ?></a></h2>
            <div class="sponsored-related-content d-flex">

			    <?php 
                    $query = array (
                        'paged' => 1,
                        'posts_per_page' => $post_to_display,
                        'offset' => 0,
                        'post_status' => 'publish',
                        'ignore_sticky_posts' => 0,
                        'orderby' => 'date',
                        'order' => 'DESC',
                        'post_type' => 
                        array (
                            'sponsored_content' => 'sponsored_content',
                        ),
						// 'meta_query' => array(
						// 	'relation' => 'AND',
							// array(
							// 	'key' => 'meta_sc_start', // Check the start date field
							// 	'value' => date("Y-m-d"), // Set today's date (note the similar format)
							// 	'compare' => '>=', // Return the ones greater than today's date
							// 	'type' => 'DATE' // Let WordPress know we're working with date
							// ),
							// array(
							//   'key' => 'meta_sc_end', // Check the start date field
							//   'value' => date("Y-m-d"), // Set today's date (note the similar format)
							//   'compare' => '>=', // Return the ones less than today's date
							//   'type' => 'DATE' // Let WordPress know we're working with date
							// )
						// ),
                    ); 
                    $postsbyid = get_posts($query);
                    foreach ($postsbyid as $post_id) {
                        $id = $post_id->ID;
                        $title = get_the_title($id);
                        $short_title = substr($title, 0, 55)."...";
                        $slug = get_permalink( $id);
                        $post_date = $post_id->post_date;
                        $post_date = date('F j, Y', strtotime($post_date));
                        $featured_image = get_the_post_thumbnail( $post_id->ID, 'article-list-thumb' );
                        $content = get_the_content($id);
                        $content = substr($content, 0, 150);
                        $content = strip_tags($content);
                        $short_content = substr($content, 0, 80)."[...]";
                        $excerpt = get_the_excerpt($id);
                        $short_excerpt = substr($excerpt, 0, 80)."[...]";
						$end_date = get_post_meta($id, 'meta_sc_end', true);

						// $wpseo_primary_term = new WPSEO_Primary_Term( 'category', $id );
						// $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
						$wpseo_primary_term = get_primary_category_id($id);
						if($wpseo_primary_term){
							$terms = get_term( $wpseo_primary_term );
						}else{
							$terms = get_the_category($id);
							$terms = reset($terms);
						}
                        // $terms = get_the_category($id);
						$term_name = $terms->name;
						$term_slug = $terms->slug;
						$term_id = $terms->term_id;
						$term_permalink = get_category_link($term_id);

						// print "<pre>"; print_r($terms); print "</pre>";
						if(!$end_date || strtotime($end_date) > strtotime('now')) { //Check end_date is less than current date or not
							if($featured_image){
								?>
									<div class="content-box">
										<h3 class="tag-name"><a href = "<?php print $term_permalink; ?>"><?php echo $term_name ?></a></h3>
										<a href="<?php print $slug; ?>">
											<div class="featured-image">
												<?php print $featured_image;?>
											</div>
											<?php
												if(strlen($title) > 100){
													print '<div class="rc-img-title"><h2>'.$short_title.'</h2></div>';
												}else{
													print '<div class="rc-img-title"><h2>'.$title.'</h2></div>';
												}
											?>
											<div class="desc"><p>
											<?php
												if($excerpt){
													if(strlen($excerpt) > 80){
														print $short_excerpt;
													}else{
														print $excerpt;
													}
												}else{
													if(strlen($content) > 80){
														print $short_content;
													}else{
														print $content;
													}
												}
											?>
											</p></div>
										</a>
									</div>
								<?php
							}else{
							?>	
								<div class="content-box">
									<h3 class="tag-name"><a href = "<?php print $term_slug; ?>"><?php echo $term_name ?></a></h3>
									<a href="<?php print $slug ?>">
										<div class="featured-image without-image">
											<p class="sponsored">Sponsored</p>
											<?php
												if(strlen($title) > 100){
													print '<div class="rc-img-title"><h2>'.$short_title.'</h2></div>';
												}else{
													print '<div class="rc-img-title"><h2>'.$title.'</h2></div>';
												}
											?>
											<div class="desc"><p>
											<?php
												if($excerpt){
													if(strlen($excerpt) > 80){
														print $short_excerpt;
													}else{
														print $excerpt;
													}
												}else{
													if(strlen($content) > 80){
														print $short_content;
													}else{
														print $content;
													}
												}
											?>
											</p></div>
										</div>
									</a>
								</div>
								<?php
							}
						}
                    }
                ?>

            </div>
		</div> <?php		
	}

}