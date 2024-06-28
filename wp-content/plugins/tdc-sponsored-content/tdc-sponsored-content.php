<?php

 /*
	 * Plugin Name: Sponsored Content 
	 * Description: Ads by another name 
	 * Author: The Dolan Company Team
	 * Version: 1.0
*/

defined('ABSPATH') or die("Cannot access pages directly.");

global $sponsored_content;
$sponsored_content = new TDC_sponsored_content;

class TDC_sponsored_content {

	private $filters;

	public function __construct() {

		if (is_admin()) {
			#setup admin stuff
			add_action('add_meta_boxes_sponsored_content', array($this, 'register_metabox'));
			add_action('admin_menu', array($this, 'admin_menu'));
		}
		else {
			
			
			#setup frontend actions & filters
			add_filter('posts_results', array($this, 'abbrakadabra'));
			add_filter('the_title', array($this, 'title_magic'));
			add_filter('single_template', array($this, 'setup_template'), 99);
			add_action('wp_head', array($this, 'robots_no_index'));
			add_action('wp_head', array($this, 'dfp_inject'), 900);
			add_filter('tie_banner', array($this, 'banner_magic'));
			add_filter('tie_banner', array($this, 'banner_magic_b'));
		}

		// general hooks for all occasions
 
		add_action('init', array($this, 'setup_cpt'));
		add_action('widgets_init', array($this, 'setup_widget'));
		add_action('save_post', array($this, 'meta_save'), 999); 
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
	}

	public function setup_template($template) {
		global $post;

		if (isset($post->post_type) && $post->post_type == 'sponsored_content') {
			return( dirname( __FILE__ ) . '/single-template.php');
		}
		return $template;

	}

	public function admin_menu() {
		add_submenu_page('options-general.php', 'Sponsored Content', 'Sponsored Content', 'manage-options', 'tdc-sponsored-content', array($this, 'admin_page'));
	}

	public function admin_page() {
		include dirname(__FILE__) . '/admin.php';
	}

	public function setup_cpt() {

		register_post_type (
			'sponsored_content',
			array(
				'labels' => array (
					'name' => 'Sponsored Content',
					'description' => 'Looks like legitmate content...isnt',
					'singular_name' => 'Sponsored Post',
					'add_new' => 'New Sponsored Post',
					'add_new_item' => 'Add New Sponsored Post',
					'new_item' => 'New Sponsored Content',
					'view_item' => 'View Sponsored Content',
					'all_items' => 'All Sponsored Content'
				 ),
				'public' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'rewrite' => array( 'with_front' => true, 'slug' => false ),
				'capability_type' => 'post',
				'can_export' => false,
				'hierarchical' => false,
				'taxonomies' => array('category'),
				'supports' => array( 'title', 'editor', 'Sponsor', 'excerpt', 'thumbnail')
			)
		);
	}

	public function setup_widget() {

		//widgets 

		register_widget('TDC_sponsored_content_widget');
		register_widget('TDC_sponsored_all_widget');
		register_widget('TDC_sponsored_content_DFP_widget_primary');
		register_widget('TDC_sponsored_content_DFP_widget_narrow');
		register_widget('TDC_sponsored_content_DFP_widget_300x600');

		// Sidebars

		$narrow = array(
			'name' => 'Sponsored Content - Narrow',
			'id' => 'tdc-sponsored-content-narrow',
			'class' => 'sidebar',
			'description' => 'only displayed on Sponsored Content Pages',
			'before_widget' => '<li id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div></li>',
			'before_title'  => '<h4 class="widgettitle">',
			'after_title'   => '</h4><div class="widget-container">' );

		$primary = array(
			'name' => 'Sponsored Content - Primary',
			'id' => 'tdc-sponsored-content-primary',
			'class' => 'sidebar',
			'description' => 'only displayed on Sponsored Content Pages',
			'before_widget' => '<li id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div></li>',
			'before_title'  => '<h4 class="widgettitle">',
			'after_title'   => '</h4><div class="widget-container">' );

		register_sidebar($narrow);
		register_sidebar($primary);
			
	}

	public function enqueue_scripts() {
		wp_enqueue_style('sponsored_content', '/wp-content/plugins/tdc-sponsored-content/sponsored_content.css');
	}

	public function register_metabox() {

		add_meta_box( 'meta_sc_info', 'Sponsored Content Operational Information', array($this, 'meta_info'));
		add_meta_box( 'meta_sc_start', 'Date to begin displaying Content', array($this, 'meta_start'));
		add_meta_box( 'meta_sc_end', 'Date to end displaying Content', array($this, 'meta_end'));
		add_meta_box( 'meta_sc_ad_tags', 'Google DFP advertiser tags (year and month will be automatically provided)', array($this, 'meta_tags'));
		// 
		add_meta_box( 'meta_sc_ad_leaderboard', 'Google DFP Leaderboard AD-Unit', array($this, 'meta_tag_leaderboard'));
		add_meta_box( 'meta_sc_ad_bottom_leaderboard', 'Google DFP Bottom Leaderboard AD-Unit', array($this, 'meta_tag_bottom_leaderboard'));
		add_meta_box( 'meta_sc_ad_large', 'Google DFP 300x250 AD-Unit', array($this, 'meta_tag_large'));
		add_meta_box( 'meta_sc_ad_small', 'Google DFP small AD-Unit', array($this, 'meta_tag_small'));
		add_meta_box( 'meta_sc_ad_300x600', 'Google DFP 300x600 AD-Unit', array($this, 'meta_tag_300x600'));
		
		add_meta_box( 'meta_sc_sponsor', 'To Which Sponsor does this ad belong?', array($this, 'meta_sponsor'));
		add_meta_box( 'meta_sc_sponsor_logo', 'Sponsor Logo (optional)', array($this, 'meta_sponsor_logo'));
		add_meta_box( 'meta_sc_sponsor_blurb', 'Sponsor Blurb (optional)', array($this, 'meta_sponsor_blurb'));

	}

	public function meta_info() {

		echo "
			<ul style='list-style-type:circle'>
			<li><i>Start</i> and <i>End</i> dates determine when a particular sponsored article will be displayed in sidebar and widget content<br>
			*note* these sponsored content articles will remain forever (as long as this plugin is enabled anyway).</li><br>

			<li>The 'Advertiser Tags' box will *Automatically* add or update Year and Month tags upon save.</li><br>

			<li>The Sponsor box below is *Very* important.  the Sponsor name *must* match between articles from the same sponsor<br>
			or they *WILL NOT* properly show up in the 'Related Content from [Sponsor Name]' Box near the end of the article page.</li><br>

			<li>The Sponsor Logo and Blurb boxes will appear in place of the Author box (on a standard post).</li><br> 
			</ul>
		";
	}

	public function meta_start($post) {

		$prev = get_post_meta($post->ID, 'meta_sc_start', true);

		$prev = '"' . $prev . '"';

		echo "<input id='meta_sc_start' name='meta_sc_start' type=date value=" . $prev . " />";
	}
	public function meta_end($post) {

		$prev = get_post_meta($post->ID, 'meta_sc_end', true);

		$prev = '"' . $prev . '"';

		echo "<input id='meta_sc_end' name='meta_sc_end' type=date value=" . $prev . " />";
	}
	public function meta_sponsor($post) {

		$prev = get_post_meta($post->ID, 'meta_sc_sponsor', true);

		$prev = '"' . $prev . '"';

		echo "<input id='meta_sc_sponsor' name='meta_sc_sponsor' size=50 type=text value=" . $prev . " />";
	}
	public function meta_tags($post) {

		$prev = get_post_meta($post->ID, 'meta_sc_ad_tags', true);

		$prev = '"' . $prev . '"';

		echo "<input id='meta_sc_ad_tags' name='meta_sc_ad_tags' size=50 type=text value=" . $prev . " />";
	}
	
	public function meta_tag_leaderboard($post) {

		$prev = get_post_meta($post->ID, 'meta_sc_ad_leaderboard', true);

		$prev = '"' . $prev . '"';

		echo "<input id='meta_sc_ad_leaderboard' name='meta_sc_ad_leaderboard' size=50 type=text value=" . $prev . " />";
	}

	public function meta_tag_bottom_leaderboard($post) {

		$prev = get_post_meta($post->ID, 'meta_sc_ad_bottom_leaderboard', true);

		$prev = '"' . $prev . '"';

		echo "<input id='meta_sc_ad_bottom_leaderboard' name='meta_sc_ad_bottom_leaderboard' size=50 type=text value=" . $prev . " />";
	}
	
	public function meta_tag_large($post) {

		$prev = get_post_meta($post->ID, 'meta_sc_ad_large', true);

		$prev = '"' . $prev . '"';

		echo "<input id='meta_sc_ad_large' name='meta_sc_ad_large' size=50 type=text value=" . $prev . " />";
	}
	
	public function meta_tag_small($post) {

		$prev = get_post_meta($post->ID, 'meta_sc_ad_small', true);

		$prev = '"' . $prev . '"';

		echo "<input id='meta_sc_ad_small' name='meta_sc_ad_small' size=50 type=text value=" . $prev . " />";
	}
	public function meta_tag_300x600($post) {

		$prev = get_post_meta($post->ID, 'meta_sc_ad_300x600', true);

		$prev = '"' . $prev . '"';

		echo "<input id='meta_sc_ad_300x600' name='meta_sc_ad_300x600' size=50 type=text value=" . $prev . " />";
	}
	
	public function meta_sponsor_logo($post) {

		$prev = get_post_meta($post->ID, 'meta_sc_sponsor_logo', true);

		$prev = '"' . $prev . '"';

		echo "<input id='meta_sc_sponsor_logo' name='meta_sc_sponsor_logo' size=100 type=text value=" . $prev . " />";
	}
	public function meta_sponsor_blurb($post) {

		$prev = get_post_meta($post->ID, 'meta_sc_sponsor_blurb', true);

		$prev = '"' . $prev . '"';

		echo "<input id='meta_sc_sponsor_blurb' name='meta_sc_sponsor_blurb' size='100' type='text' value=" . $prev . " />";
	}

	public function meta_save($post_id) {
		global $post;

		if (isset($_POST['meta_sc_sponsor'])) {
			update_post_meta($post_id, 'meta_sc_sponsor', $_POST['meta_sc_sponsor']);
		}

		if (isset($_POST['meta_sc_start'])) {
			update_post_meta($post_id, 'meta_sc_start', $_POST['meta_sc_start']);
		}

		if (isset($_POST['meta_sc_end'])) {
			update_post_meta($post_id, 'meta_sc_end', $_POST['meta_sc_end']);
		}
		if (isset($_POST['meta_sc_ad_tags'])) {
			$new = $this->tag_cleanup( $post_id, $_POST['meta_sc_ad_tags'] );
			update_post_meta($post_id, 'meta_sc_ad_tags', $new);
		}
		
		if (isset($_POST['meta_sc_ad_leaderboard'])) {
			update_post_meta($post_id, 'meta_sc_ad_leaderboard', $_POST['meta_sc_ad_leaderboard']);
		}
		
		if (isset($_POST['meta_sc_ad_bottom_leaderboard'])) {
			update_post_meta($post_id, 'meta_sc_ad_bottom_leaderboard', $_POST['meta_sc_ad_bottom_leaderboard']);
		}
		
		if (isset($_POST['meta_sc_ad_large'])) {
			update_post_meta($post_id, 'meta_sc_ad_large', $_POST['meta_sc_ad_large']);
		}
		
		if (isset($_POST['meta_sc_ad_small'])) {
			update_post_meta($post_id, 'meta_sc_ad_small', $_POST['meta_sc_ad_small']);
		}
		
		if (isset($_POST['meta_sc_ad_300x600'])) {
			update_post_meta($post_id, 'meta_sc_ad_300x600', $_POST['meta_sc_ad_300x600']);
		}
		
		
		
		
		
		if (isset($_POST['meta_sc_sponsor_logo'])) {
			update_post_meta($post_id, 'meta_sc_sponsor_logo', $_POST['meta_sc_sponsor_logo']);
		}
		if (isset($_POST['meta_sc_sponsor_blurb'])) {
			update_post_meta($post_id, 'meta_sc_sponsor_blurb', $_POST['meta_sc_sponsor_blurb']);
		}

		// only hardcode these
		if (isset($post->post_type) && $post->post_type == 'sponsored_content') {
			update_post_meta($post_id, 'dmcss_security_policy', 'Always Free');
			update_post_meta($post_id, 'we_own_it', 'No');
		}
	}

	private function tag_cleanup( $post_id, $raw ) {

		$chopped = array();

		if (preg_match('/,/', $raw)) {
			$chopped = preg_split('/\s*,\s*/', $raw);
		}
		else {
			$chopped[0] = $raw;
		}

		foreach ($chopped as $index => $value) {
			if (preg_match('/^Year|Month/', $value)) { unset($chopped[$index]); }
		}
	
		$meta = get_post_meta($post_id, 'meta_sc_start', true);
		$date = preg_split('/-/', $meta); 

		$year = 'Year' . $date[0];
		array_push($chopped, $year);
		$month = 'Month' . $date[1];
		array_push($chopped, $month);

		$return =  implode(', ', $chopped);

		return $return;
	}

	public function robots_no_index() {
		global $post;
		if (isset($post->post_type) && $post->post_type == 'sponsored_content') {
			echo "\n <!--- Sponsored Content disable indexing --->\n"; 
			echo '<meta name="robots" content="noindex">' . "\n";;
		}
	}

	public function dfp_inject() {

		$config = unserialize(get_option('tdc_sponsored_content'));

		global $post;
		if ( empty($post->post_type) || $post->post_type != 'sponsored_content' || !isset($config['dfp']) ) { return; }

		$rawtag = get_post_meta($post->ID, 'meta_sc_ad_tags', true); 
		//meta_sc_ad_leaderboard,meta_sc_ad_large,meta_sc_ad_small,meta_sc_ad_300x600
		$rawtag_leaderboard = get_post_meta($post->ID, 'meta_sc_ad_leaderboard', true); 
		$rawtag_bottom_leaderboard = get_post_meta($post->ID, 'meta_sc_ad_bottom_leaderboard', true); 
		$rawtag_large = get_post_meta($post->ID, 'meta_sc_ad_large', true); 
		$rawtag_small = get_post_meta($post->ID, 'meta_sc_ad_small', true); 
		$rawtag_300x600 = get_post_meta($post->ID, 'meta_sc_ad_300x600', true); 
		
		$rawtag_leaderboard = (!empty($rawtag_leaderboard))?$rawtag_leaderboard:$config['dfp']['leader_name']; 
		$rawtag_large = (!empty($rawtag_large))?$rawtag_large:$config['dfp']['primary_name'];  
		$rawtag_small = (!empty($rawtag_small))?$rawtag_small:$config['dfp']['narrow_name']; 
		$rawtag_300x600 = (!empty($rawtag_300x600))?$rawtag_300x600:$config['dfp']['primary_name']; 
		$rawtag_bottom_leaderboard = (!empty($rawtag_bottom_leaderboard))?$rawtag_bottom_leaderboard:''; 
		
		
		
		$chopped = preg_split('/\s*,\s*/', $rawtag);
		$tagarray = array(); 
		foreach ( $chopped as $tag) {
			preg_replace( '/[\'"]/', '', $tag);
			array_push($tagarray, '"' . $tag . '"');
		}
		
		$tagstring = implode(', ', $tagarray);

		//baseline inject;
		
		if(!empty($rawtag_leaderboard))
			$rawtag_leaderboard = "googletag.defineSlot('/" . $config['dfp']['account'] . '/' . $rawtag_leaderboard . "', [728, 90], '" . $config['dfp']['leader_id'] . "').addService(googletag.pubads());";
		if(!empty($rawtag_large))
			$rawtag_large ="googletag.defineSlot('/" . $config['dfp']['account'] . '/' . $rawtag_large . "', [300, 250], '" . $config['dfp']['primary_id'] . "').addService(googletag.pubads());";
		if(!empty($rawtag_small))
			$rawtag_small ="googletag.defineSlot('/" . $config['dfp']['account'] . '/' . $rawtag_small . "', [180, 150], '" . $config['dfp']['narrow_id'] . "').addService(googletag.pubads());";
		if(!empty($rawtag_300x600))
			$rawtag_300x600 ="googletag.defineSlot('/" . $config['dfp']['account'] . '/' . $rawtag_300x600 . "', [300, 600], 'div-gpt-ad-sponsored_content300x600').addService(googletag.pubads());";
		if(!empty($rawtag_bottom_leaderboard))
			$rawtag_bottom_leaderboard ="googletag.defineSlot('/" . $config['dfp']['account'] . '/' . $rawtag_bottom_leaderboard . "', [728, 90], 'div-gpt-ad-sp_bottom728x90').addService(googletag.pubads());";
		
		if(!empty($rawtag_leaderboard) || !empty($rawtag_large) || !empty($rawtag_small) || !empty($rawtag_300x600) || !empty($rawtag_bottom_leaderboard) )
		echo "
		<script async='async' src='https://www.googletagservices.com/tag/js/gpt.js'></script>
		<script>
			var googletag = googletag || {};
			googletag.cmd = googletag.cmd || [];

			googletag.cmd.push(function() {
				
				$rawtag_leaderboard
				$rawtag_large
				$rawtag_small
				$rawtag_300x600
				$rawtag_bottom_leaderboard
				googletag.pubads().setTargeting( 'SponsoredContent', [" . $tagstring . "] );
				googletag.pubads().setTargeting( 'sponconid', ['" . $post->ID . "'] );
				googletag.pubads().enableSingleRequest();
				googletag.enableServices();
			});
		</script>
		";


	}

	public function banner_magic($banner) {

		$config = unserialize(get_option('tdc_sponsored_content'));
		global $post;

		if ( isset($post->post_type) && $post->post_type == 'sponsored_content' && $banner == 'banner_top' && !empty($config['dfp']['leader_id']) ) {
		
			$ad_text = "
				<div id='" . $config['dfp']['leader_id'] . "' class='sc_dfp_banner'>
					<script>
						googletag.cmd.push(function() { googletag.display('" . $config['dfp']['leader_id'] . "'); });
					</script>
				</div>
			";
			return $ad_text;
		}
		
		return $banner;
	}


	public function banner_magic_b($banner) {

		$config = unserialize(get_option('tdc_sponsored_content'));
		global $post;

		if ( isset($post->post_type) && $post->post_type == 'sponsored_content' && $banner == 'banner_bottom' && isset($config['dfp']) ) {
			if($rawtag_bottom_leaderboard = get_post_meta($post->ID, 'meta_sc_ad_bottom_leaderboard', true)){
			
				$ad_text = "<div class='e3lan-bottom'><div class='desktop-bottom-lb'>		
					<div id='div-gpt-ad-sp_bottom728x90' class='sc_dfp_banner'>
						<script>
							googletag.cmd.push(function() { googletag.display('div-gpt-ad-sp_bottom728x90'); });
						</script>
					</div></div><div class='mobile-bottom-lb' style=''></div></div>
				";
				return $ad_text;
			}
			
		}
		
		return $banner;
	}


	public function title_magic( $title, $id = null ) {


		if ($id == null) { 
			global $post;
			$id = isset($post->ID) ? $post->ID : '';
		}

		if ( get_post_type($id) == 'sponsored_content' ) { 
			$r = '<span class="sponsoredContent">' . $title . '</span>';
			return $r;
		}
	
		return $title;

	}
	
	public function array_swap(&$array,$swap_a,$swap_b) {
						list($array[$swap_a],$array[$swap_b]) = array($array[$swap_b],$array[$swap_a]);
					}

	public function abbrakadabra($posts = null) {

		if (isset($posts[2])) {
			$stack = debug_backtrace();
			array_shift($stack);
			array_shift($stack);

			$filterables = array();

			foreach ($stack as $frame) {
				$filterables['file'][basename($frame['file'])] = $frame;;
				if (isset($frame['class']) && !is_null($frame['class'])) { $filterables['class'][$frame['class']] = $frame; }
			}

			if (isset( $filterables['class']['categort_posts'])) {
				if ( isset($filterables['file']['single-template.php']) ) {
					// skip injection and title redraw on interior sponsored content page
					remove_filter('the_title', array($this, 'title_magic'));
					return $posts;
				}
				$widget_cat = $filterables['class']['categort_posts']['args'][1]['cats_id'];
				$today = date('Y-m-d');
				$args = array(
					'post_type'    => 'sponsored_content',
					'no_found_rows' => true,
					'category__in' => $widget_cat,
					'orderby' => 'random',
					'meta_key'  => 'meta_sc_end', // your ACF Date & Time Picker field
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key'     => 'meta_sc_start',
							'value'   =>  $today, 
							'compare' => '<=',
             				 'type'    => 'DATE'
						),
						array(
							'key'     => 'meta_sc_end',
							'value'   =>  $today,
							'compare' => '>=',
              				'type'    => 'DATE'
						)
					)
					
				);
				$query = new WP_Query( $args );
				if (isset($query->posts[0])) {
					//$posts[2] = $query->posts[0];

					// Commenting 
					
					// $a = $posts;
					// $t = array();
					
					// array_pop($a);	//preserve length of array
					// array_unshift($t, array_shift($a));
					// array_unshift($t, array_shift($a));
					// $t[] = $query->posts[0];

					// $posts = array_merge($t, $a);
					
										
					// Modification
					$a = $posts;
					$t = array();
	 
					array_pop($a);	//preserve length of array
					//array_unshift($t, array_shift($a));
					array_unshift($t, array_shift($a));
					$t[] = $query->posts[0];

					$posts = array_merge($t, $a);
					$this->array_swap($posts,1,2);
					
				}
				wp_reset_query();
				wp_reset_postdata();
			}
			elseif (isset($filterables['file']['home-cats.php']) || isset($filterables['file']['home-cats-wide.php'])) {
				$cat_slug = '';
				if(isset($filterables['file']['query.php'])){
					$cat_slug = $filterables['file']['query.php']['object']->query_vars['category_name'];
				}
				elseif(isset($filterables['file']['class-wp-query.php']))
				{
					$cat_slug = $filterables['file']['class-wp-query.php']['object']->query_vars['category_name'];
					
				}
				else
				{
					return $posts;
					
				}
				$category = get_category_by_slug($cat_slug);
				$cat_id = is_object($category) ? $category->cat_ID : 2;
				$today = date('Y-m-d');
				$args = array(
							'post_type'    => 'sponsored_content',
							'category__in' => $cat_id,
							'no_found_rows' => true,
							'meta_query' => array(
		           'relation' => 'AND',
								array(
									'key'     => 'meta_sc_start',
									'value'   =>  $today, 
									'compare' => '<=',
		              'type'    => 'DATE'
								),
								array(
									'key'     => 'meta_sc_end',
									'value'   =>  $today,
									'compare' => '>=',
		              'type'    => 'DATE'
								)
							),
		          'orderby' => 'rand', // order by date
		          'meta_key'  => 'meta_sc_end', // your ACF Date & Time Picker field
		          'order'  => 'ASC'
							
				);
				$query = new WP_Query( $args );

				if (isset($query->posts[0])) {
					//$posts[2] = $query->posts[0];

					// Commenting 
					
					// $a = $posts;
					// $t = array();
					// array_pop($a);	//preserve length of array
					// array_unshift($t, array_shift($a));			
					// array_unshift($t, array_shift($a));
					// $t[] = $query->posts[0];
					// $posts = array_merge($t, $a);
					
					
					// Modification					
					$a = $posts;
					$t = array();
					array_pop($a);	//preserve length of array
					//array_unshift($t, array_shift($a));
					array_unshift($t, array_shift($a));
					$t[] = $query->posts[0];
					$posts = array_merge($t, $a);
					$this->array_swap($posts,1,2);
				
				}
				wp_reset_query();
				wp_reset_postdata();
				
			}
			else {
			}
		}

		return $posts;
	}
}


class TDC_sponsored_content_widget extends WP_Widget {

	public function __construct() {
		parent::__construct('TDC_sponsored_content_widget', 'Sponsored Content Widget'); 
	}

	public function widget($args, $instance) {
		$today = date('Y-m-d');
		$args = array(
			'post_type'    => 'sponsored_content',
			'orderby' => 'rand',
			'order' => 'DESC',
			'meta_key' => 'meta_sc_sponsor',
			'no_found_rows' => true,
			'posts_per_page' => 4,
			'paged' => 0,
			'meta_query' => array(
				 'relation' => 'AND',
		        	array(
							'key'     => 'meta_sc_start',
							'value'   =>  $today, 
							'compare' => '<=',
              				'type'    => 'DATE'
						),
						array(
							'key'     => 'meta_sc_end',
							'value'   =>  $today,
							'compare' => '>=',
              				'type'    => 'DATE'
						)
     			)
  		);
		remove_filter('posts_results', array($this, 'abbrakadabra'));
  		$query = new WP_Query( $args );
		add_filter('posts_results', array($this, 'abbrakadabra'));

		$group = array();

		foreach ( $query->posts as $post) {
			//$this->unixtime($post->post_date) . '<br>';

			$sponsor = get_post_meta($post->ID, 'meta_sc_sponsor', true);

			if (isset($group[$sponsor])) {
				if ($this->unixtime($post->post_date) > $this->unixtime($group[$sponsor]->post_date)) {
					$group[$sponsor] = $post;
				}
			}
			else {
				$group[$sponsor] = $post;
			}
		}

		echo '<div class="widget sc_widget">';
		echo '<div class="widget-top"><h4>'; 
		_e('From our Partners', 'tdc_sc'); 
		echo '</h4></div>';
		echo '<ul class="sc_list">';

		foreach ($group as  $post) {
			echo '<li class="list-box">';
			$thumbnail =  get_the_post_thumbnail($post);
			if (!empty($thumbnail)) {
				echo '<div class="post-thumbnail sc_thumbnail">';
				echo $thumbnail;
				echo '</div>';
			}
			echo '<h3 class="post-title-box">';
			echo '<a href="' . get_permalink($post) . '" rel="nofollow">' . $post->post_title . '</a>'; 
			echo '</h3>';
			echo '</li>';
		}
		echo '</ul>';
		echo '</div>';
	}

	public function form( $instance ) {
		echo "<b><i>This widget doesn't have any options</i><b><br>";
	}

	public function update ($new_instance, $old_instance) {
		// nothing to do.
		return $new_instance;
	}

	public function unixtime( $date_string ) {
		$date = DateTime::createFromFormat('Y-m-d G:i:s', $date_string); 
		return $date->getTimestamp();
	}

}

class TDC_sponsored_content_DFP_widget_primary extends WP_Widget {

	public function __construct() {
		parent::__construct('TDC_sponsored_content_DFP_widget_primary', 'Sponsored Content Sponsor Ad - Primary'); 
	}

	public function widget($args, $instance) {

		$config = unserialize(get_option('tdc_sponsored_content'));
		global $post;

		if ( $post && $post->post_type == 'sponsored_content' && isset($config['dfp']) ) {
		
			$ad_text = "
				<div id='" . $config['dfp']['primary_id'] . "' class='sc_dfp_primary'>
					<script>
						googletag.cmd.push(function() { googletag.display('" . $config['dfp']['primary_id'] . "'); });
					</script>
				</div>
			";
			echo $ad_text;
		}
	}

	public function form( $instance ) {
		echo "<i>This widget does not have options as it is controlled by the indivdual Sponsored Content Page</i><br>";
	}

	public function update ($new_instance, $old_instance) {
		// nothing to do.
		return $new_instance;
	}

}

class TDC_sponsored_content_DFP_widget_narrow extends WP_Widget {

	public function __construct() {
		parent::__construct('TDC_sponsored_content_DFP_widget_narrow', 'Sponsored Content Sponsor Ad - Narrow'); 
	}

	public function widget($args, $instance) {
		$config = unserialize(get_option('tdc_sponsored_content'));
		global $post;

		if ( $post && $post->post_type == 'sponsored_content' && isset($config['dfp']) ) {
		
			$ad_text = "
				<div id='" . $config['dfp']['narrow_id'] . "' class='sc_dfp_primary'>
					<script>
						googletag.cmd.push(function() { googletag.display('" . $config['dfp']['narrow_id'] . "'); });
					</script>
				</div>
			";
			echo $ad_text;
		}
	}

	public function form( $instance ) {
		echo "<i>This widget does not have options as it is controlled by the indivdual Sponsored Content Page</i><br>";
	}

	public function update ($new_instance, $old_instance) {
		// nothing to do.
		return $new_instance;
	}

}

class TDC_sponsored_content_DFP_widget_300x600 extends WP_Widget {

	public function __construct() {
		parent::__construct('TDC_sponsored_content_DFP_widget_300x600', 'Sponsored Content Sponsor Ad - 300x600'); 
	}

	public function widget($args, $instance) {
		$config = unserialize(get_option('tdc_sponsored_content'));
		global $post;

		if ( $post && $post->post_type == 'sponsored_content' && isset($config['dfp']) ) {
		
			$ad_text = "
				<div id='div-gpt-ad-sponsored_content300x600' class='sc_dfp_primary'>
					<script>
						googletag.cmd.push(function() { googletag.display('div-gpt-ad-sponsored_content300x600'); });
					</script>
				</div>
			";
			echo $ad_text;
		}
	}

	public function form( $instance ) {
		echo "<i>This widget does not have options as it is controlled by the indivdual Sponsored Content Page</i><br>";
	}

	public function update ($new_instance, $old_instance) {
		// nothing to do.
		return $new_instance;
	}

}


class TDC_sponsored_all_widget extends WP_Widget {

	public function __construct() {
		parent::__construct('TDC_sponsored_all_widget', 'Show All Sponsored Content Widget'); 
	}

	public function widget($args, $instance) {
		$today = date('Y-m-d');
		
		$title = apply_filters('widget_title', $instance['title']);
		$numberOfListings = ($instance['numberOfListings'])?$instance['numberOfListings']:1;
		$args = array(
			'post_type'    => 'sponsored_content',
			'orderby' => 'date',
			'order' => 'DESC',
			'meta_key' => 'meta_sc_sponsor',
			'no_found_rows' => true,
			'posts_per_page' => $numberOfListings,
			'paged' => 0,
			'meta_query' => array(
				 'relation' => 'AND',
		        	array(
							'key'     => 'meta_sc_start',
							'value'   =>  $today, 
							'compare' => '<=',
              				'type'    => 'DATE'
						),
						array(
							'key'     => 'meta_sc_end',
							'value'   =>  $today,
							'compare' => '>=',
              				'type'    => 'DATE'
						)
     			)
  		);
		//remove_filter('posts_results', array($this, 'abbrakadabra'));
  		$query = new WP_Query( $args );
		//add_filter('posts_results', array($this, 'abbrakadabra'));

		$group = array();

		foreach ( $query->posts as $post) {
			//echo $this->unixtime($post->post_date) . '<br>';

			//$sponsor = get_post_meta($post->ID, 'meta_sc_sponsor', true);
			$group[] = $post;
			
		}

		echo '<div class="widget sc_widget">';
		echo '<div class="widget-top"><h4>'; 
		_e($title, 'tdc_sc'); 
		echo '</h4></div>';
		echo '<ul class="sc_list">';

		foreach ($group as  $post) {
			echo '<li class="list-box">';
			$thumbnail =  get_the_post_thumbnail($post);
			if (!empty($thumbnail)) {
				echo '<div class="post-thumbnail sc_thumbnail">';
				echo $thumbnail;
				echo '</div>';
			}
			echo '<h3 class="post-title-box">';
			echo '<a href="' . get_permalink($post) . '" rel="nofollow">' . $post->post_title . '</a>'; 
			echo '</h3>';
			echo '</li>';
		}
		echo '</ul>';
		echo '</div>';
	}

	public function form( $instance ) {
		if( $instance) {
		$title = esc_attr($instance['title']);
		$numberOfListings = esc_attr($instance['numberOfListings']);
	} else {
		$title = '';
		$numberOfListings = '';
	} 
	
	?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

        <p><label for="<?php echo $this->get_field_id('numberOfListings'); ?>"><?php _e('Number of posts to show:'); ?></label>
        <input id="<?php echo $this->get_field_id('numberOfListings'); ?>" name="<?php echo $this->get_field_name('numberOfListings'); ?>" type="text" value="<?php echo $numberOfListings; ?>" size="3" /></p>
<?php
	
	
	}

	public function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['numberOfListings'] = strip_tags($new_instance['numberOfListings']);
		return $instance;
	}

	public function unixtime( $date_string ) {
		$date = DateTime::createFromFormat('Y-m-d G:i:s', $date_string); 
		return $date->getTimestamp();
	}

}
