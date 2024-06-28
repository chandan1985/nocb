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
class Asnt_events_social_links extends \Elementor\Widget_Base {

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
		return 'asnt_events_social_links';
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
		return esc_html__( 'ASNT Events Social Links', 'asnt_events_social_links' );
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

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'asnt_events_social_links' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
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
        ?>

        <div class="event-content__sidebar">
			<h2 class="mt-4">Share this event</h2>
			<?php
				if ($permalink == "") {
					// Get current page URL 
					$eventURL = urlencode(get_the_permalink($event_id));
				} else {
					$eventURL = urlencode($permalink);
				}
				$eventTitle = htmlspecialchars(urlencode(html_entity_decode(get_the_title($event_id), ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8');

				// Construct sharing URL without using any script
				$twitterURL = 'https://twitter.com/intent/tweet?text=' . $eventTitle . '&amp;url=' . $eventURL;
				$facebookURL = 'https://www.facebook.com/sharer/sharer.php?u=' . $eventURL;
				$linkedInURL = 'https://www.linkedin.com/shareArticle?mini=true&url=' . $eventURL . '&amp;title=' . $eventURL;

				// Based on popular demand added Pinterest too
				$pinterestURL = 'https://pinterest.com/pin/create/button/?url=' . $eventURL . '&amp;media=' . $Thumbnail[0] . '&amp;description=' . $crunchifyTitle;

				echo '<ul class="social-share-links">';
				echo '<li><a class="twitter" href="' . $twitterURL . '" target="_blank"><img src="/wp-content/themes/NJBIZ/images/twitter.svg" alt="twitter"></a></li>';
				echo '<li><a class="facebook" href="' . $facebookURL . '" target="_blank"><img src="/wp-content/themes/NJBIZ/images/facebook.svg" alt="facebook"></a></li>';
				echo '<li><a class="linkedin" href="' . $linkedInURL . '" target="_blank"><img src="/wp-content/themes/NJBIZ/images/linkedin.svg" alt="linkedin"></a></li>';
				echo '<li><a class="crunchify-link crunchify-pinterest" href="' . $pinterestURL . '" data-pin-custom="true" target="_blank"><img src="/wp-content/themes/NJBIZ/images/pinterest.svg" alt="pinterest"></a></li>';
				echo '<li><a class="email" href="mailto:?subject=' . get_the_title($post_id) . '&amp;body=' . esc_html($eventContent) . '" title="Share by Email"><img src="/wp-content/themes/NJBIZ/images/email.svg" alt="email"></a></li>';
				echo '</ul>';
			?>
		</div>

        <?php
	}

}