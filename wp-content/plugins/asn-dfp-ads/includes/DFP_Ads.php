<?php
/**
 * Class DFP_Ads
 *
 * @link       http://www.chriwgerber.com/dfp-ads/
 * @since      0.0.1
 *
 * @package    WordPress
 * @subpackage DFP-Ads
 */
namespace DFP_Ads;

Class DFP_Ads {

	/**
	 * Loads Google Ads JS to header
	 *
	 * @since  0.0.1
	 * @access public
	 *
	 * @var string $google_ad_script_name
	 */
	public $google_ad_script_name = 'google_ad_js';

	/**
	 * Name of the javascript file.
	 *
	 * @since  0.0.1
	 * @access public
	 *
	 * @var string $script_name
	 */
	public $script_name = 'dfp_ads';

	/**
	 * DFP Account ID. Includes the two slashes
	 *
	 * @since  0.0.1
	 * @access public
	 * @var string $account_id
	 */
	public $account_id;

	/**
	 * Setting for whether to load an ad as asynchronous
	 * or synchronous
	 *
	 * @since  0.3.1
	 * @access public
	 * @var bool $account_id
	 */
	public $asynch;

	/**
	 * Stores the URI of the directory
	 *
	 * @since  0.0.1
	 * @access public
	 *
	 * @var string $dir_uri
	 */
	public $dir_uri;

	/**
	 * Ad Positions - Array
	 *
	 * @since  0.0.1
	 * @access public
	 *
	 * @var Position DFP_Ads Position
	 */
	public $positions;

	/**
	 * Sets page level targeting
	 *
	 * @access public
	 * @since  0.0.1
	 *
	 * @var array
	 */
	public $page_targeting = array(
		'Page'     => array(),
		'categoryid' => array(),
		//'pagetag'      => array(),
		'pgtype'      => array(),
		'articleid'      => array(),
	);

	/**
	 * PHP5 Constructor
	 *
	 * @since  0.0.1
	 * @access public
	 */
	public function __construct() {
		/** Creates DFP_Ads Shortcode */
		add_shortcode( 'dfp_ads', array( $this, 'shortcode' ) );
	}

	/**
	 * Set DFP Property Code
	 *
	 * Sets the DFP Property Code. An 8-digit integer
	 *
	 * @since  0.0.1
	 * @access public
	 *
	 * @param $id int Code ID Number
	 *
	 * @return bool|string
	 */
	public function set_account_id( $id ) {
		$this->account_id = '/' . $id ;

		return ( isset( $this->account_id ) ? $this->account_id : false );
	}

	/**
	 * Set Asynchronous Loading
	 *
	 * Sets the flag for how the ads should load. By default, the setting is off,
	 * so it will send 'on' when it's set to load synchronously, rather than
	 * the normal, correct way. This is because asynchronous is default and some
	 * people want to be able to turn it off.
	 *
	 * @since  0.3.1
	 * @access public
	 *
	 * @param string $val
	 *
	 * @return bool
	 */
	public function set_asynchronous_loading( $val ) {
		$this->asynch = ( $val == 'on' ? false : true );

		return ( isset( $this->asynch ) ? $this->asynch : false );
	}

	/**
	 * Sets all ad targeting
	 *
	 * @since  0.0.1
	 * @access public
	 *
	 * @return mixed
	 */
	public function set_targeting() {
		// Page Title
		//$this->page_targeting['Page'] = $this->get_page_targeting();
		// Categories
		$option_array = get_option( 'DFP_Ads_Settings' );
		if($option_array['dfp_category_remove_homepage']=='1' && (is_home() || is_front_page()))
		{
			$this->page_targeting['categoryid'] = '';
		}
		else
		{
			$this->page_targeting['categoryid'] = $this->get_category_targeting();
		}
		// Tags
		// $this->page_targeting['pagetag'] = $this->get_tag_targeting();
		// page targeting
		$this->page_targeting['pgtype'] = $this->get_pgtype_targeting();
		$this->page_targeting['articleid'] = $this->get_article_targeting();
		$this->page_targeting['Page'] = $this->get_page_targeting();
	}

	/**
	 * @param DFP_Ads $dfp_ads
	 *
	 * @return DFP_Ads
	 */
	public function send_ads_to_js( $dfp_ads ) {
		// Copy the original
		$object = clone $this;

		$object->set_targeting();
		$object->positions   = dfp_get_ad_positions();
		$object->script_name = null;
		$object->dir_uri     = null;

		return $object;
	}

	/**
	 * Adds URL sections to targeting
	 *
	 * This function will return an array of page directories without the URL.
	 *
	 * Example: [ '2015', '10', '11', 'post_slug' ]
	 *
	 * @since  0.0.1
	 * @access protected
	 *
	 * @return array|string
	 */
	/**
	 * Loads Ad Position
	 *
	 * @param {Array} positions - Array of ad positions
	 */
	function load_ad_positions(&$htmlscript, $positions, $acct_id)
	{
		global $pbm_out_of_page_ads;
		//echo "<pre>";
		//echo print_r($positions);
		//echo "</pre>";
		// Run through positions
		//for ($ad_pos = 0, $len = count($positions); $ad_pos < $len; $ad_pos++) {
		$count = 0;
		foreach ($positions as $k => $v) {
			$ad_id_int = $v->post_id;
			$pbm_position_association_array = get_page_related_ad_array();
			if(is_array($pbm_out_of_page_ads)){
				if (isset($pbm_position_association_array[$ad_id_int]) || in_array($ad_id_int, $pbm_out_of_page_ads)) {
					$count++;
					$this->define_ad_slot($htmlscript, $v, $acct_id, $count);
				} 
			}else {
				continue;
			}
		}

		//~ global $adsdivArr;
		//~ print_r($adsdivArr);
		//~ die("I am here");

	}

	/**
	 * Loads Ad Position
	 *
	 * @param {Object} position - Array of ad positions
	 */
	function define_ad_slot(&$htmlscript, $position, $acct_id, $count)
	{
		$defineSlot = "defineSlot";
		$position_sizes = ",";
		$site_id = get_current_blog_id();
		if ($position->out_of_page) {
			$defineSlot = "defineOutOfPageSlot";
		}
		if (!empty($position->sizes)) {
			$position_sizes = ',' . json_encode($position->sizes) . ',';
		}
		$htmlscript .= '
	   //console.log(jQuery("#' . $position->position_tag . '").size());
	   
	   //if(jQuery(".' . $position->position_tag . '").size()){
	   ';

		if (!empty($position->ad_name))
			$acct_id = $acct_id . '/' . $position->ad_name;
		// if( $site_id == 1 ){    
			//$htmlscript .= 'adSlot' . $count . ' = googletag.' . $defineSlot . '("' . $acct_id . '"' . $position_sizes . '"' . $position->position_tag . '").addService(googletag.pubads())';
		// }else{
		// 	$htmlscript .= '
		// 		googletag.'.$defineSlot.'("'.$acct_id.'"'.$position_sizes.'"'.$position->position_tag.'").addService(googletag.pubads())';
		// }		
		foreach ($position->targeting as $key => $value) {
			$htmlscript .= '.setTargeting("' . $key . '","' . $value . '")';
		}

		$htmlscript .= ';';

		$htmlscript .= '
		  
		  
		  
		 // }
		  
		  ';

		//console.log(position.ad_name);
	}

	/**
	 * Sets Page level targeting
	 * @param {object} targeting
	 */
	function page_set_targeting(&$htmlscript, $targeting)
	{
		foreach ($targeting as $key => $value) {
			if ($key != "DT_lay") {
				$key = strtolower($key);
			}
			if (is_array($value)) {
				$htmlscript .= '
		 googletag.pubads().setTargeting("' . $key . '",' . json_encode($value) . ')';
			} else {
				$htmlscript .= '
		 googletag.pubads().setTargeting("' . $key . '","' . $value . '")';
			}
			unset($key);
		}
	}

	/**
	 * Adds URL sections to targeting
	 *
	 * This function will return an array of page directories without the URL.
	 *
	 * Example: [ '2015', '10', '11', 'post_slug' ]
	 *
	 * @since  0.0.1
	 * @access protected
	 *
	 * @return array|string
	 */
	protected function get_page_targeting() {
		global $wp;
		/*
		 * WP Core replacement for the URL parsing being done before.
		 */
		if ( $wp->request != null ) {
			$current_url = $wp->request;
			$array       = explode( '/', $current_url );
		} else {
			$current_url = $wp->query_string;
			$url_parts   = explode( '=', $current_url );
			if ( count( $url_parts ) >= 2 ) {
				$array[ $url_parts[0] ] = $url_parts[1];
			} else {
				$array = array();
			}

		}

		return ( count( $array ) < 1 ? array( 'Home' ) : $array );
	}

	/**
	 * Sets the category targeting on the object
	 *
	 * @since  0.0.1
	 * @access protected
	 *
	 * @return array|string
	 */
	protected function get_category_targeting() {
		global $post;
		$cus_post_id = isset($post->ID) ? $post->ID : '';
		if(empty($cus_post_id)) {
			return '';
		}
		$targets = array();	
		if (is_archive()) {
			
			$post_type = get_queried_object();

			$targets[] = $post_type->slug;
			//exit;
		}		
		else if ( $post->post_type == 'post' ) {
			$categories = get_the_category( $cus_post_id );			
			foreach ( $categories as $c ) {
				$cat       = get_category( $c );
				$targets[] = $cat->name;
			}			
		}
		return ( count( $targets ) < 1 ? '' : $targets );
	}

	/**
	 * Sets the tag targeting on the object
	 *
	 * @since  0.0.1
	 * @access protected
	 *
	 * @return array|string
	 */
	protected function get_tag_targeting() {
		global $post;
		$targets = array();
		if ( $post ) {
			$tags = get_the_tags( $post->ID );
			if ( $tags ) {
				foreach ( $tags as $tag ) {
					$tagname = str_replace(' ', '', strtolower($tag->name));
					$targets[] = $tagname;
				}
			}
		}
		return ( count( $targets ) < 1 ? '' : $targets );
	}

	/**
	 * Sets the page targeting on the object
	 *
	 * @since  0.0.1
	 * @access protected
	 *
	 * @return array|string
	 */	
	protected function get_pgtype_targeting() {
		global $post,$wp;
		if(empty($post->ID)) {
			return '';
		}
		$targets = '';		
		/*
		 * WP Core replacement for the URL parsing being done before.
		 */

		$current_path = $_SERVER['REQUEST_URI'];
		$path_array = explode('/', $current_path);
				
		if (is_home() || is_front_page()) {
			$targets = 'homepage';
		}
		if(is_author()){
			$targets = 'author';
		}
		if (is_single($post->ID)) {
			$ptype = get_post_type($post->ID);
			if ($ptype == "post") {
				$targets = 'article';
			}elseif($ptype == "sponsored_content"){
				$targets = 'sponsored_content';
			}elseif($ptype == "pbm-video-gallery"){
				$targets = 'video';
			}elseif($ptype == "tribe_events"){
				$targets = 'event';
			}		
		}
		elseif (is_category()) {
			$targets = 'category';
		}
		elseif (in_array('readerrankings', $path_array)){
			$targets = 'readerrankings';
		}
		elseif (in_array('new-orleans-citybusiness-events', $path_array)){
			$targets = 'event';
		}
		elseif (is_page()) {
			if ( $wp->request != null ) {
				$current_url = $wp->request;
				$targets = explode( '/', $current_url );
			} else {
				$current_url = $wp->query_string;				
				$url_parts   = explode( '=', $current_url );
				if ( count( $url_parts ) >= 2 ) {
					$targets = $url_parts[1];
				} else {
					$targets = array();
					if (is_home() || is_front_page()) {
					$targets = 'homepage';
					}
				}
	
			}
		}
		elseif (is_archive()) {
			$targets = 'category';
		}

		return $targets;
	}

	protected function get_article_targeting() {
		global $post;
		$cus_post_id = isset($post->ID) ? $post->ID : '';
		if(empty($cus_post_id)) {
			return '';
		}
		$targets = array();	
		if (is_single($post->ID)) {
			
			$post_type = get_queried_object();

			$targets[] = $post_type->ID;
			//exit;
		}		
		
		return ( count( $targets ) < 1 ? '' : $targets );
	}

	/**
	 * Registers Scripts. Localizes data to interstitial_ad.js
	 *
	 * @access public
	 * @since  0.0.1
	 *
	 * @return mixed
	 */
	public function scripts_and_styles() {
		if ( defined( 'DFP_CONCAT_SCRIPTS' ) && true === DFP_CONCAT_SCRIPTS ) {
			$gads_script_url    = $this->dir_uri . '/assets/js/google-ads.min.js';
			$dfp_ads_script_url = $this->dir_uri . '/assets/js/dfp-ads.min.js';
		} else {
			$gads_script_url    = $this->dir_uri . '/assets/js/google-ads.js';
			$dfp_ads_script_url = $this->dir_uri . '/assets/js/dfp-ads.js';
		}
		// Google Ads JS Script
		wp_register_script(
			$this->google_ad_script_name,
			$gads_script_url,
			array( 'jquery' ),
			false,
			false
		);
		/* Get the Final Ad Positions */
		
		//$ad_positions = apply_filters( 'pre_dfp_ads_to_js', $this );
		$option_array = get_option( 'DFP_Ads_Settings' );
		$loginpage_dfp = isset($option_array['dfp_remove_loginpage']) ? $option_array['dfp_remove_loginpage'] : '';
	    $loging_url = isset($_SERVER["REQUEST_URI"]) ? array(trim($_SERVER["REQUEST_URI"])) : '';
		if ($loginpage_dfp == 0)
		{
		$ad_positions = apply_filters( 'pre_dfp_ads_to_js', $this );
		}
		elseif ($loginpage_dfp == 1)
		{
		  $loging_url = array(trim($_SERVER["REQUEST_URI"]));
		  if($loging_url[0] == "/?dmcss=login")
		  {
		    $ad_positions = '';
		  }
		  else
		  {
		    $ad_positions = apply_filters( 'pre_dfp_ads_to_js', $this );
		  }
		}

		foreach($ad_positions->positions as $k=>$adunit)
		{
			// print "<pre>"; print_r($adunit->excludepage); print "</pre>";
			// print $ad_positions->page_targeting['pgtype']."<br>";

			// if(!is_array($ad_positions->page_targeting['pgtype']) && in_array($ad_positions->page_targeting['pgtype'], $adunit->excludepage))
			// 	unset($ad_positions->positions[$k]);
			// elseif(is_array($ad_positions->page_targeting['pgtype']) && in_array($ad_positions->page_targeting['pgtype'][0], $adunit->excludepage))
			// 	unset($ad_positions->positions[$k]);
				
			// if(!is_array($ad_positions->page_targeting['pgtype']) && (!empty($adunit->includepage) && !in_array($ad_positions->page_targeting['pgtype'], $adunit->includepage)))
			// 	unset($ad_positions->positions[$k]);
			// elseif(is_array($ad_positions->page_targeting['pgtype']) && (!empty($adunit->includepage) && !in_array($ad_positions->page_targeting['pgtype'][0], $adunit->includepage)))
			// 	unset($ad_positions->positions[$k]);

			if($adunit->includepage[0] != "" && !in_array($ad_positions->page_targeting['pgtype'], $adunit->includepage)){
				unset($ad_positions->positions[$k]);
			}

			if(in_array($ad_positions->page_targeting['pgtype'], $adunit->excludepage)){
				unset($ad_positions->positions[$k]);
			}

			if($adunit->device_name == "mobile" && !wp_is_mobile()){
				unset($ad_positions->positions[$k]);
			}
			if($adunit->device_name == "desktop" && wp_is_mobile()){
				unset($ad_positions->positions[$k]);
			}
			
		}
		
		$ad_positions->positions = array_values($ad_positions->positions);	
		// Send data to front end.
		wp_localize_script( $this->google_ad_script_name, 'dfp_ad_object', array( $ad_positions ) );
		wp_enqueue_script( $this->google_ad_script_name );
		// Preps the script
		wp_register_script(
			$this->script_name,
			$dfp_ads_script_url,
			array( $this->google_ad_script_name, 'jquery' ),
			false,
			false
		);
		wp_enqueue_script( $this->script_name );

		$htmlscript = '//I am here
				
		var googletag = googletag || {};
		var adSlot1, adSlot2, adSlot3, adSlot4, adSlot5, adSlot6, adSlot7, adSlot8, adSlot9, adSlot10, adSlot11, adSlot12, adSlot13, adSlot14, adSlot15, adSlot16, adSlot17, adSlot18;
		googletag.cmd = googletag.cmd || [];
		googletag.async = true;
		/**
		 * Ad Position Creation
		 */
		googletag.cmd.push(function () {
		// Object from Ajax
		';

		$dfp_ad_data = $ad_positions;
		//echo"<pre>";print_r($dfp_ad_data);echo"</pre>"; 
		$acct_id = $dfp_ad_data->account_id;

		// Generates Ad Slots

		$this->load_ad_positions($htmlscript, $dfp_ad_data->positions, $acct_id);

		$htmlscript .= '// Collapse Empty Divs


		// Targeting';

		//googletag.pubads().collapseEmptyDivs(true);
		$this->page_set_targeting($htmlscript, $dfp_ad_data->page_targeting);

		if ($dfp_ad_data->asynch === true) {
			$htmlscript .= '// Asynchronous Loading
		googletag.pubads().enableAsyncRendering();';
		}
		$htmlscript .= '// Asynchronous Loading
		googletag.pubads().enableSingleRequest();
		// Go
		googletag.pubads().collapseEmptyDivs(true);
		googletag.enableServices();
		});';

		wp_add_inline_script($this->google_ad_script_name, $htmlscript);
	}

	/**
	 * Display Shortcode
	 *
	 * @since  0.0.1
	 * @access public
	 *
	 * @param $atts array
	 *
	 * @return mixed Returns HTML data for the position
	 */
	public function shortcode( $atts ) {
		$position = dfp_get_ad_position( $atts['id'] );

		return ($position)?$position->get_position():'';
	}

}