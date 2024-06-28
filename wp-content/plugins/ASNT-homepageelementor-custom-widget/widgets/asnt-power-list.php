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
	class asnt_power_list extends \Elementor\Widget_Base {

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
			return 'asnt_power_list';
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
			return esc_html__( 'ASNT Power List Section', 'asnt_power_list' );
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
			return [ 'custom', 'widget', 'power', 'list' ];
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

			$select_list = [];
			$powerlist_cat = get_category_by_slug('power-list');
			$powerlist_catId = $powerlist_cat->term_id;
			$select_list[$powerlist_cat->term_id] = $powerlist_cat->name;
			$sub_cats = get_terms( array(
				'taxonomy' => 'category',
				'hide_empty' => false,
				'child_of' => $powerlist_catId // to target not only direct children
			) );
			foreach ($sub_cats as $sub_cat) {
				$select_list[$sub_cat->term_id] = $sub_cat->name;
				// $child_cat =  $sub_cat->term_id;
				// $all_childs = get_terms( array(
				// 	'taxonomy' => 'category',
				// 	'hide_empty' => false,
				// 	'child_of' => $child_cat // to target not only direct children
				// ) );
				// foreach($all_childs as $child){
				// 	$select_list[$child->term_id] = $child->name;
				// }
			}
			

			$this->start_controls_section(
				'content_section',
				[
					'label' => esc_html__( 'Content', 'asnt_power_list' ),
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
					'options' => $select_list,
				]
			);
			$this->add_control(
			'power_list_custom_list_1',
			[
				'type' => \Elementor\Controls_Manager::TEXT,
				'label' => esc_html__( 'Power list custom list 1', 'textdomain' ),
				'placeholder' => esc_html__( 'Please add custom HTML', 'textdomain' ),
			]
			);
			$this->add_control(
			'power_list_custom_list_2',
			[
				'type' => \Elementor\Controls_Manager::TEXT,
				'label' => esc_html__( 'Power list custom list 2', 'textdomain' ),
				'placeholder' => esc_html__( 'Please add custom HTML', 'textdomain' ),
			]
			);
			$this->add_control(
			'power_list_custom_list_3',
			[
				'type' => \Elementor\Controls_Manager::TEXT,
				'label' => esc_html__( 'Power list custom list 3', 'textdomain' ),
				'placeholder' => esc_html__( 'Please add custom HTML', 'textdomain' ),
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
			$category = $settings['list'];

			$power_list_custom_list_1 = $settings['power_list_custom_list_1'];
			$power_list_custom_list_2 = $settings['power_list_custom_list_2'];
			$power_list_custom_list_3 = $settings['power_list_custom_list_3'];


			// print $Categories;
			// print "<pre>"; print_r($select_list); print "</pre>";

			$args = array (
				'paged' => 1,
				'posts_per_page' => '50',
				'offset' => 0,
				'post_status' => 'publish',
				'ignore_sticky_posts' => 0,
				'orderby' => 'menu_order',
				'order' => 'ASC',
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
					'terms' => $category,
					'operator' => 'IN',
					'include_children' => false,
					),
				),
				// 'meta_query' => array(
				// 	'relation' => 'AND',
				// 	'menu_order__order_by' => array(
				// 		'key' => 'menu_order',
				// 		'type' => 'CHAR',
				// 		'compare' => '>=',
				// 	)
				// ),
				// 'orderby' => array( 'menu_order__order_by' => 'ASC' ),
			);
			$powerList_posts = get_posts($args);

			// print "<pre>"; print_r($powerList_posts); print "</pre>";
			// print "<pre>"; print_r($select_list); print "</pre>";
			?>

				<?php //print $power_list_custom_list_1;?>
				<?php //print $power_list_custom_list_2;?>
				<?php //print $power_list_custom_list_3;?>

				<ul class="alpha-pagination">
					<li class="page-item top-10"><a class="page-link active" href="#top-ten">TOP 10</a></li>
					<li class="page-item all"><a class="page-link" href="#all">ALL</a></li>
					<li class="page-item has-item"><?php print $power_list_custom_list_1;?></li>
					<li class="page-item has-item"><?php print $power_list_custom_list_2;?></li>
					<li class="page-item has-item"><?php print $power_list_custom_list_3;?></li>
					<!--<li class="page-item has-item"><a class="page-link" href="#A">A</a></li>
					<li class="page-item has-item"><a class="page-link" href="#B">B</a></li>
					<li class="page-item has-item"><a class="page-link" href="#C">C</a></li>
					<li class="page-item has-item"><a class="page-link" href="#D">D</a></li>
					<li class="page-item has-item"><a class="page-link" href="#E">E</a></li>
					<li class="page-item has-item"><a class="page-link" href="#F">F</a></li>
					<li class="page-item has-item"><a class="page-link" href="#G">G</a></li>
					<li class="page-item has-item"><a class="page-link" href="#H">H</a></li>
					<li class="page-item has-item"><a class="page-link" href="#I">I</a></li>
					<li class="page-item has-item"><a class="page-link" href="#J">J</a></li>
					<li class="page-item has-item"><a class="page-link" href="#K">K</a></li>
					<li class="page-item has-item"><a class="page-link" href="#L">L</a></li>
					<li class="page-item has-item"><a class="page-link" href="#M">M</a></li>
					<li class="page-item has-item"><a class="page-link" href="#N">N</a></li>
					<li class="page-item has-item"><a class="page-link" href="#O">O</a></li>
					<li class="page-item has-item"><a class="page-link" href="#P">P</a></li>
					<li class="page-item has-item"><a class="page-link" href="#Q">Q</a></li>
					<li class="page-item has-item"><a class="page-link" href="#R">R</a></li>
					<li class="page-item has-item"><a class="page-link" href="#S">S</a></li>
					<li class="page-item has-item"><a class="page-link" href="#T">T</a></li>
					<li class="page-item has-item"><a class="page-link" href="#U">U</a></li>
					<li class="page-item has-item"><a class="page-link" href="#V">V</a></li>
					<li class="page-item has-item"><a class="page-link" href="#W">W</a></li>
					<li class="page-item has-item"><a class="page-link" href="#X">X</a></li>
					<li class="page-item has-item"><a class="page-link" href="#Y">Y</a></li>
					<li class="page-item has-item"><a class="page-link" href="#Z">Z</a></li>-->
				</ul>
				<div class="powerlist-wrapper">
					<?php
						foreach ($powerList_posts as $key => $powerList_post) {
							// $unwanted_parts = array("/No./", "/\b[1-9]|10\b/", "/\b0\b/");
							$id = $powerList_post->ID;
							$title = get_the_title($id);
							// $title = preg_replace($unwanted_parts, "", $title);
							$short_title = substr($title, 0, 50)."...";
							$content = get_post($id);
							$content = strip_tags($content->post_content);
							$content = strip_tags($content);
							$short_content = substr($content, 0, 70)."[...]";
							$slug = get_permalink( $id);
							$featured_image = get_the_post_thumbnail( $id, 'top-stories-thumb' );
							$image = get_post_thumbnail_id( $id->ID);
							$image_caption = wp_get_attachment_caption( $image );
							$excerpt = strip_tags(get_the_excerpt($id));
							$short_excerpt = substr($excerpt, 0, 70)."[...]";
							$order = get_post_field( 'menu_order', $id );
							?>
								<div class="powerlist-box <?php if($order > 0 && $order <= 10) print 'top-ten'; ?>">
									<?php
										if($order > 0 ){
											print "<span class='order'>".$order."</span>";
										}
									?>
									<div class="featured-image <?php if(!$featured_image) print 'no-image'; ?>"><?php print $featured_image; ?></div>
									<div class="powerlist-content-wrap">
										<div class="title"><h2> <?php
											if(strlen($title) > 50){
												print $short_title;
											}else{
												print $title;
											} ?>
										</h2></div>
										<!-- <h3 class="power-count">Power 100</h3> -->
										<div class="desc"><p>
											<?php
												if($excerpt){
													if(strlen($excerpt) > 70){
														print $short_excerpt;
													}else{
														print $excerpt;
													}
												}else{
													if(strlen($content) > 70){
														print $short_content;
													}else{
														print $content;
													}
												}
											?>
										</p></div>
									</div>
									
									<a href="<?php print $slug; ?>" class="read-more btn">Read More</a>
								</div>
							<?php
						}
					?>
				</div>
			<?php
		}

	}
?>