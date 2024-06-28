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
class Asnt_listing_with_ads extends \Elementor\Widget_Base {

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
		return 'asnt_listing_with_ads';
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
		return esc_html__( 'ASNT Listing with 300*250, 728*90 Ads', 'asnt_listing_with_ads' );
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
				'label' => esc_html__( 'Content', 'asnt-listing-with-ads' ),
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

		$this->add_control(
			'300x250_ad',
			[
				'type' => \Elementor\Controls_Manager::TEXT,
				'label' => esc_html__( '300 x 250 Ad', 'textdomain' ),
				'placeholder' => esc_html__( 'Please enter Ad code here', 'textdomain' ),
			]
		);

		$this->add_control(
			'720x90_Ad',
			[
				'type' => \Elementor\Controls_Manager::TEXT,
				'label' => esc_html__( '720 x 90 Ad', 'textdomain' ),
				'placeholder' => esc_html__( 'Please enter Ad code here', 'textdomain' ),
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
		// $html = wp_oembed_get( $settings['url'] );
        // $title = $settings['title'];
        $Categories = $settings['list'];
		$small_ad = $settings['300x250_ad'];
        $big_ad = $settings['720x90_Ad'];
		
		$Category_id = $Categories;
		$term_obj = get_term_by('id', $Category_id, 'category');
		$term_name = $term_obj->name;
		$term_slug = get_category_link($Category_id);

		

		?>
			<div class="news-landing-page ns-landing-page">
			<!-- <div class="container"> -->

			<?php if(wp_is_mobile() && !wp_is_ipad()){ ?>
				<div class="content-section">
					<div class="row">
						<div class="col-sm-3 left-cs">
						<a href="<?php print $term_slug;?>"><h2 class="title"><?php print $term_name;?></h2></a>
						<?php
						$postsbycategory = array (
							'paged' => 1,
							'posts_per_page' => '6',
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
						$allposts = get_posts($postsbycategory);
						$exclude_ids = [];
						foreach ($allposts as $post_id) {
							$id = $post_id->ID;
							$exclude_ids[] = $id;
							$post_title = get_the_title($id);
							// $short_title = substr($post_title, 0, 105)."[...]";
							$slug = get_permalink($id);
							?>
								<div class="content-box">
									<?php 
										// if(strlen($post_title) > 105){
										// 	print '<p class="post-title"><a href='.$slug.'>'.$short_title.'</a></p>';
										// }else{
											print '<p class="post-title"><a href='.$slug.'>'.$post_title.'</a></p>';
										// }
									?>
								</div>
							<?php
						}
						?>
						</div>
						<div class="col-sm-9">
						<div class="right-cs" style="display:none;">
							<?php
								$postsbycategory = array (
									'paged' => 1,
									'posts_per_page' => '5',
									'offset' => 6,
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
									'meta_query' => array(
										array(
										 'key' => '_thumbnail_id',
										 'compare' => 'EXISTS'
										),
									),
								);
								$allposts = get_posts($postsbycategory);
								foreach ($allposts as $key => $post_id) {
									$id = $post_id->ID;
									$title = get_the_title($id);
									// $short_title = substr($title, 0, 100)."...";
									$slug = get_permalink( $id);
									$featured_image = get_the_post_thumbnail( $post_id->ID, 'article-list-thumb' );
									$content_post = get_post($id);
									$content = $content_post->post_content;
									$content = strip_tags($content);

									$excerpt = get_the_excerpt($id);
									// $short_excerpt = substr($excerpt, 0, 150)."[...]";

									// $short_content = substr($content,0,150) . " [...]";
									$sponsored_cmpnyId = get_post_meta($id, 'associated_sponsor_id', true);
									$cmpny_link = get_permalink( $sponsored_cmpnyId);

									if($featured_image){
										?>
											<div class="content-box">
												<?php
													if($sponsored_cmpnyId ){
														print '<a class="sponsored-cmpny" href="'.$cmpny_link.'"><p class="sponsored">Sponsored</p></a>';
													}
												?>
												<a href="<?php print $slug; ?>">
													<div class="featured-image"><?php print $featured_image;?></div>
													<?php
														// if(strlen($title) > 100){
														// 	print '<div class="title"><h2 class="rc-img-title">'.$short_title.'</h2></div>';
														// }else{
															print '<div class="title"><h2 class="rc-img-title">'.$title.'</h2></div>';
														// }
													?>
													<div class="desc"><p>
														<?php 
														if($excerpt){
															print $excerpt;
														}else{
															// if(strlen($content) > 150){
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
											<?php
												if($sponsored_cmpnyId ){
													print '<a class="sponsored-cmpny" href="'.$cmpny_link.'"><p class="sponsored">Sponsored</p></a>';
												}
											?>
											<a href="<?php print "post/".$slug ?>">
												<div class="featured-image without-image">
													<?php
														// if(strlen($title) > 100){
														// 	print '<div class="title"><h2 class="rc-img-title">'.$short_title.'</h2></div>';
														// }else{
															print '<div class="title"><h2 class="rc-img-title">'.$title.'</h2></div>';
														// }
													?>
													<div class="desc"><p>
													<?php 
														if($excerpt){
															print $excerpt;
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
									<?php
									}
								}
							?>
							</div>
						</div>
					</div>
				</div>

			<?php } else{ ?>

				<div class="content-section">
					<div class="row">
						<div class="col-sm-3 left-cs">
						<a href="<?php print $term_slug;?>"><h2 class="title"><?php print $term_name;?></h2></a>
						<?php
						$postsbycategory = array (
							'paged' => 1,
							'posts_per_page' => '8',
							'offset' => 5,
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
						$allposts = get_posts($postsbycategory);
						$exclude_ids = [];
						foreach ($allposts as $post_id) {
							$id = $post_id->ID;
							$exclude_ids[] = $id;
							$post_title = get_the_title($id);
							$slug = get_permalink($id);
							?>
								<div class="content-box">
									<p class="post-title"><a href="<?php print $slug;?>"><?php print $post_title;?></a></p>
								</div>
							<?php
						}
						?>
						</div>
						<div class="col-sm-9">
						<div class="right-cs">
							<?php
								$postsbycategory = array (
									'paged' => 1,
									'posts_per_page' => '5',
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
								$allposts = get_posts($postsbycategory);
								foreach ($allposts as $key => $post_id) {
									$id = $post_id->ID;
									$title = get_the_title($id);
									$slug = get_permalink( $id);
									$featured_image = get_the_post_thumbnail( $post_id->ID, 'article-list-thumb' );
									$content_post = get_post($id);
									$content = $content_post->post_content;
									$excerpt = get_the_excerpt($id);
									$content = strip_tags($content);
									$content = preg_replace('/<iframe.*?\/iframe>|https?:\/\/[^\s]+|(\[feed url=".*?" number="\d+"\]|\[.*?\])/', '', $content);
									$sponsored_cmpnyId = get_post_meta($id, 'associated_sponsor_id', true);
									$cmpny_link = get_permalink( $sponsored_cmpnyId);

									if($featured_image){
										?>
											<div class="content-box">
												<?php
													if($sponsored_cmpnyId ){
														print '<a class="sponsored-cmpny" href="'.$cmpny_link.'"><p class="sponsored">Sponsored</p></a>';
													}
												?>
												<a href="<?php print $slug; ?>">
													<div class="featured-image"><?php print $featured_image;?></div>
													<?php
															print '<div class="title"><h2 class="rc-img-title">'.$title.'</h2></div>';
													?>
													<div class="desc"><p class="news-description">
													<?php 
														if($excerpt){
															print $excerpt;
														}else{
															print $content;
														}
														?>	
													</p></div>
												</a>
											</div>
										<?php
									}else{
									?>	
										<div class="content-box">
											<?php
												if($sponsored_cmpnyId ){
													print '<a class="sponsored-cmpny" href="'.$cmpny_link.'"><p class="sponsored">Sponsored</p></a>';
												}
											?>
											<a href="<?php print "post/".$slug ?>">
												<div class="featured-image without-image">
													<?php
														print '<div class="title"><h2 class="rc-img-title">'.$title.'</h2></div>';
													?>
													<div class="desc"><p>
													<?php 
														if($excerpt){
															print $excerpt;
														}else{
															print $content;
														}
														?>	
													</p></div>
												</div>
											</a>
										</div>
									<?php
									}
									if($key == 1){
										?>
											<div class="ad-section"><?php print $small_ad;?></div>
											<div class="separator"></div>
										<?php
									}
								}
							?>
							</div>
							<div class="more-stories"><a href="<?php print $term_slug?>">More stories</a></div>
							<div class="leaderboard-ad-section"><?php print $big_ad;?></div>
						</div>
					</div>
				</div>

			<?php } ?>
			</div>
			<!-- </div> -->
		<?php
	}

}