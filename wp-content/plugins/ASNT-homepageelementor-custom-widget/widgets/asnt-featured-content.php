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
class asnt_featured_content extends \Elementor\Widget_Base {

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
		return 'asnt_featured_content';
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
		return esc_html__( 'ASNT Featured Contents', 'asnt_featured_content' );
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
		return [ 'custom', 'widget', 'asnt' ];
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
				<?php
					$query = array (
						'paged' => 1,
						'posts_per_page' => 3,
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
					// print "<pre>"; print_r($allposts); print "</pre>";
					$featured_a = array_slice($allposts,0,1);
					$featured_b = array_slice($allposts,1,1);
					$featured_c = array_slice($allposts,2,1);
					// $top_stories = array_slice($allposts,3,3);
					// $exclude_array = array_merge($featured_a,$featured_b,$featured_c);
				?>
				<div id="featured-content" class="featured-content">
					<div class="top-stories">
						<?php
							$exclude_id = [];
							foreach ($allposts as $key => $post_id) {
								$exclude_id[] = $post_id->ID;
							}
							if (!empty($top_stories) && is_array($top_stories)){
								$count = count($top_stories);
							}
							
							// print "Count: ".$count;
							if($count == 0 || $count<3){
								$queryFor_Topstories = array (
									'paged' => 1,
									'posts_per_page' => 3,
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
									'post__not_in' => $exclude_id,
								);
								$topStories_nonfeatured = get_posts($queryFor_Topstories);
								// $total_topStories = array_merge($top_stories,$topStories_nonfeatured);
								// print "<pre>"; print_r($total_topStories); print "</pre>";
								?>
									<div class="other-top-stories">
										<h2>Other Top Stories</h2>
										<ul>
											<?php
												foreach ($topStories_nonfeatured as $post_id) {
													$id = $post_id->ID;
													$title = get_the_title($id);
													// $short_title = substr($title, 0, 80)."[...]";
													$slug = get_permalink( $id);
													?>
														<li>
															<a href="<?php print $slug; ?>"><?php echo $title; ?></a>
														</li>
													<?php
												} ?>
										</ul>
									</div>
								<?php

							}
						?>
					</div>
					<div class="featured_a">
						<?php
							// print "<pre>"; print_r($featured_a); print "</pre>";
							foreach ($featured_a as  $ids_a) {
								$id_a = $ids_a ->ID;
								$title = get_the_title($id_a);
								// $short_title = substr($title, 0, 85)."...";
								$content = get_post($id_a);
								$content = $content->post_content;
								$content = strip_tags($content);
								// $short_content = substr($content, 0, 150)."[...]";
								$slug = get_permalink( $id_a);
								$featured_image = get_the_post_thumbnail( $ids_a->ID, 'top-stories-thumb' );
								$image = get_post_thumbnail_id( $ids_a->ID);
								$image_caption = wp_get_attachment_caption( $image );
								$excerpt = get_the_excerpt($id_a);
								// $short_excerpt = substr($excerpt, 0, 150)."[...]";
							}
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
					</div>
					<div class="featured_b_and_c">
						<div class="featured_b">
							<?php
								foreach ($featured_b as $ids_b) {
									$id_b = $ids_b->ID;
									$title_b = get_the_title($id_b);
									// $short_title_b = substr($title_b, 0, 45)."[...]";
									$slug_b = get_permalink($id_b);
									$featured_image_b = get_the_post_thumbnail( $ids_b->ID, 'featured-contents3a-thumb' );
								}
								// print "<pre>"; print_r($featured_b); print "</pre>";
								if($featured_image_b){
									?>
									<div class="most-viewed">
										<!-- <h3 class="tag-name"><a href = "<?php //print $term_slug; ?>"><?php //echo $term_name ?></a></h3> -->
										<a href="<?php print $slug_b; ?>">
											<div class="featured-image"><?php echo $featured_image_b ?></div>
											<?php if($title_b || $short_title_b){ ?>
												<div class="title"><p><?php 
													// if(strlen($title_b) > 45){
													// 	print $short_title_b;
													// }else{
														print $title_b;
													// }
												?></p></div>
											<?php } ?>
										</a>
									</div>
										
									<?php
								}else{					
								?>
									<div class="most-viewed">
										<!-- <h3 class="tag-name"><a href = "<?php //print $term_slug; ?>"><?php //echo $term_name ?></a></h3> -->
										<a href="<?php print $slug_b; ?>">
											<div class="featured-image without-image"></div>
											<?php if($title_b || $short_title_b){ ?>
												<div class="title"><p><?php 
													// if(strlen($title_b) > 45){
													// 	print $short_title_b;
													// }else{
														print $title_b;
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
						<div class="featured_c">
							<?php
								foreach ($featured_c as $ids_c) {
									$id_c = $ids_c->ID;
									$title_c = get_the_title($id_c);
									// $short_title_c = substr($title_c, 0, 45)."[...]";
									$slug_c = get_permalink( $id_c);
									$featured_image_c = get_the_post_thumbnail( $ids_c->ID, 'featured-contents3a-thumb' );
								}
								if($featured_image_c){
									?>
									<div class="most-viewed">
										<!-- <div class="post-tag-sponsored">
											<h3 class="tag-name"><a href = "<?php //print $term_slug; ?>"><?php //echo $term_name ?></a></h3>
											<a href="/sponsored-contents"><p class="sponsored">Sponsored</p></a>
										</div>	 -->
										<a href="<?php print $slug_c; ?>">
											<div class="featured-image"><?php echo $featured_image_c ?></div>
											<?php if($title_c || $short_title_c){ ?>
												<div class="title"><p><?php 
													// if(strlen($title_c) > 45){
													// 	print $short_title_c;
													// }else{
														print $title_c;
													// }
												?></p></div>
											<?php } ?>
										</a>
									</div>
										
									<?php
								}else{					
								?>
									<div class="most-viewed">
										<!-- <div class="post-tag-sponsored">
											<h3 class="tag-name"><a href = "<?php //print $term_slug; ?>"><?php //echo $term_name ?></a></h3>
											<a href="/sponsored-contents"><p class="sponsored">Sponsored</p></a>
										</div>	 -->
										<a href="<?php print $slug_c; ?>">
											<div class="featured-image without-image"></div>
											<?php if($title_c || $short_title_c){ ?>
												<div class="title"><p><?php 
													// if(strlen($title_c) > 45){
													// 	print $short_title_c;
													// }else{
														print $title_c;
													// }
												?></p></div>
											<?php } ?>
										</a>
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