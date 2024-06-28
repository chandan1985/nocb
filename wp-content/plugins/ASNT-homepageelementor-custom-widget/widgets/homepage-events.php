<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Homepage Events.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Homepage_events extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Homepage Events name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'homepage_events';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Homepage Events title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'ASNT Homepage Events', 'homepage_events' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Homepage Events icon.
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
	 * Retrieve the list of categories the Homepage Events belongs to.
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
	 * Retrieve the list of keywords the Homepage Events belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'custom', 'widget' ];
	}

	/**
	 * Register Homepage Events controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$option = array(
			"1" => "1",
			"2" => "2",
			"3" => "3",
			"4" => "4",
			"5" => "5",
			"6" => "6",
			"7" => "7",
			"8" => "8",
			"9" => "9",
			"10" => "10",
		);

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'homepage_events' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'post_to_display',
			[
				'label' => esc_html__( 'Post To Display', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'multiple' => false,
				'options' => $option,
			]
		);

		$this->add_control(
			'cta_text',
			[
				'type' => \Elementor\Controls_Manager::TEXT,
				'label' => esc_html__( 'CTA text', 'textdomain' ),
			]
		);

		$this->add_control(
			'cta_link',
			[
				'type' => \Elementor\Controls_Manager::TEXT,
				'label' => esc_html__( 'CTA link', 'textdomain' ),
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render Homepage Events output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();
        // $title = $settings['title'];
        $post_display = $settings['post_to_display'];
		$cta_text = $settings['cta_text'];
        $cta_link = $settings['cta_link'];
		


		?>
		<div class="events-and-webinar">
			<?php
				$current_date =  date("d/m/Y"); 
				// $current_date =  strtotime($current_date);
				// print $currentDate;

				$events = array (
					'paged' => 1,
					'posts_per_page' => $post_display,
					'offset' => 0,
					'post_status' => 'publish',
					'ignore_sticky_posts' => 0,
					// 'orderby' => 'date',
					// 'order' => 'ASC',
					'post_type' => 
					array (
					'tribe_events' => 'tribe_events',
					),
					'meta_query' => array(
						'relation' => 'AND',
						array(
						  'key' => '_EventStartDate', // Check the start date field
						  'value' => date("Y-m-d"), // Set today's date (note the similar format)
						  'compare' => '>=', // Return the ones greater than today's date
						  'type' => 'DATE' // Let WordPress know we're working with date
						),
						'_EventStartDate__order_by' => array(
							'key' => '_EventStartDate',
							'type' => 'DATE',
							'compare' => '>=',
						)
					),
					'orderby' => array( '_EventStartDate__order_by' => 'ASC' ),
				);

				$events_postsbyid = get_posts($events);
				// print "<pre>"; print_r ($events_postsbyid); print "</pre>";
				print '<h2>EVENTS</h2>';
				foreach ($events_postsbyid as $post_id) {
					$id = $post_id->ID;
					$title = get_the_title($id);
					$evnt_startdate =  tribe_get_start_date( $id, false,"F jS, Y");
					$evnt_starttime =  tribe_get_start_date( $id, false,"g:i A");
					$content_post = get_post($id);
					$content = $content_post->post_content;
					$content = strip_tags($content);
					$content = preg_replace('/<div id="gallery-1"[^>]*>.*?<\/div>|\[.*?\]|\[figcaption.*\[\/figcaption\]/si', '', $content);
					$short_content = substr($content, 0, 140)."[...]";
					$slug = get_permalink($id);
					$excerpt = $content_post->post_excerpt;
					$short_excerpt = substr($excerpt, 0, 140)."[...]";
					$comingSoon = get_post_meta( $id, 'coming_in_late_year', true );
					?>
					<div class="event-listing">
						<h4><a href="<?php print $slug; ?>"><?php print $title;?></a></h4>
						<p style="font-weight: 700">
						<?php 
							$comingSoon == "yes" ? 
							print 'Details coming soon!' :
							print $evnt_startdate  .' | '.  $evnt_starttime;
						?>
						</p>
						<div class="desc"><p><a href="<?php print $slug; ?>" class='text-white'>
							<?php
								if($excerpt){
									if(strlen($excerpt) > 140){
										print $short_excerpt;
									}else{
										print $excerpt;
									}
								}else{
									if(strlen($content) > 140){
										print $short_content;
									}else{
										print $content;
									}
								}
							?>
						</a></p></div>
					</div>
					<?php
					}
				?>
				<a href="<?php print $cta_link; ?>" class="btn-custom"><?php print $cta_text; ?></a>
		</div>
		<?php
	}

}