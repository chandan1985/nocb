<?php
/*
 * Plugin Name: TDC Add to Home Screen
 * Description: Displays a popup to iOS visitors to add a bookmark on their home page
 * Version: 1.0
 * Author: Dave Long
 * Author URI: http://thedolancompany.com
*/

// Disallow direct access
if (!defined( 'ABSPATH' ))
	die( 'Direct access not allowed.' );

global $add_to_hs;
$add_to_hs = new tdc_add_to_hs();

final class tdc_add_to_hs {

	###  Class Variables ###
	protected $ops;
	protected $admin;

	### Class Constructor ###
	public function __construct() {
		$this->ops = get_option( 'aths_data' );

		// Default any required but missing values
		if( !isset( $this->ops['returning_visitor'] ) )
			$this->ops['returning_visitor'] = true;
		if( !isset( $this->ops['start_delay'] ) )
			$this->ops['start_delay'] = 2000;
		if( !isset( $this->ops['lifespan'] ) )
			$this->ops['lifespan'] = 20000;
		if( !isset( $this->ops['expire'] ) )
			$this->ops['expire'] = 43200;
		if( !isset( $this->ops['page_target'] ) )
			$this->ops['page_target'] = 'home_only';

		if( is_admin() ) {
			// Initialize icon descriptions for the admin
			$this->ops['touch_icon']['57x57']['desc']   = '57x57 touch icon URL (for iPhone 3GS and 2011 iPod Touch).';
			$this->ops['touch_icon']['60x60']['desc']   = '60x60 touch icon URL (for iPhone 3GS and 2011 iPod Touch).';
			$this->ops['touch_icon']['72x72']['desc']   = '72x72 touch icon URL (for 1st generation iPad, iPad 2 and iPad mini).';
			$this->ops['touch_icon']['76x76']['desc']   = '76x76 touch icon URL (for 1st generation iPad, iPad 2 and iPad mini).';
			$this->ops['touch_icon']['114x114']['desc'] = '114x114 touch icon URL (for iPhone 4, 4S, 5 and 2012 iPod Touch).';
			$this->ops['touch_icon']['144x144']['desc'] = '144x144 touch icon URL (for iPhone 4, 4S, 5 and 2012 iPod Touch).';
			$this->ops['touch_icon']['120x120']['desc'] = '120x120 touch icon URL (for iPad 3rd and 4th generation).';
			$this->ops['touch_icon']['128x128']['desc'] = '128x128 touch icon URL (for iPad 3rd and 4th generation).';
			$this->ops['touch_icon']['152x152']['desc'] = '152x152 touch icon URL (for iPad 3rd and 4th generation).';
			$this->ops['touch_icon']['167x167']['desc'] = '167x167 touch icon URL (for iPad 3rd and 4th generation).';
			$this->ops['touch_icon']['180x180']['desc'] = '180x180 touch icon URL (for iPad 3rd and 4th generation).';
			$this->ops['touch_icon']['256x256']['desc'] = '256x256 touch icon URL (for iPad 3rd and 4th generation).';

			$this->ops['icon']['48x48']['desc']   		= '48x48 icon URL (for browsers).';
			$this->ops['icon']['96x96']['desc']   		= '96x96 icon URL (for browsers).';
			$this->ops['icon']['144x144']['desc'] 		= '144x144 icon URL (for browsers).';
			$this->ops['icon']['192x192']['desc'] 		= '192x192 icon URL (for browsers).';
			$this->ops['icon']['256x256']['desc'] 		= '256x256 icon URL (for browsers).';
			$this->ops['icon']['384x384']['desc'] 		= '384x384 icon URL (for browsers).';

			$this->ops['tile_icon']['144x144']['desc']  = '144x144 icon URL (for windows tile).';

			// include admin class file
			require_once( dirname( __FILE__ ) . '/admin.php' );
			$this->admin = new add_to_home_admin( $this->ops );
		}
		else{
			// Set up page load hooks
			add_action( 'wp', array( &$this, 'setup_bookmark_popup' ) );
			// Output iOS meta
			add_action('wp_head', array( &$this, 'output_ios_meta' ), 2 );
		} 
	}

	/**
	 *Sort the array keys
	 *return custom keys array
	*/
	 public function array_reorder_keys(&$array, $keynames){
    	if(empty($array) || !is_array($array) || empty($keynames)) return;
    		if(!is_array($keynames)) $keynames = explode(',',$keynames);
    			if(!empty($keynames)) $keynames = array_reverse($keynames);
				    foreach($keynames as $n){
				        if(array_key_exists($n, $array)){
				            $newarray = array($n=>$array[$n]); //copy the node before unsetting
				            unset($array[$n]); //remove the node
				            $array = $newarray + array_filter($array); //combine copy with filtered array
				        	}
				    	}
		}

	/**
	 * Add included front end css & js
	 *
	 * @return void
	 */	
	public function import_includes() {
		// Register the stylesheet
		wp_register_style( 'adhs_css', plugins_url('/includes/add2home.css', __FILE__) );
		// Enqueue the stylesheet
		wp_enqueue_style( 'adhs_css' );
		// Register the script:
		wp_register_script( 'adhs_js', plugins_url('/includes/add2home.js', __FILE__) );
		// Enqueue the script:
		wp_enqueue_script( 'adhs_js' );
	}

	/**
	 * Add front end css & js if needed, otherwise do nothing
	 *
	 * @return void
	 */	
	public function output_configuration_script() {
		$custom_js = '<script type="text/javascript">var addToHomeConfig = {';
		// Only override defaults we have settings for
		if ( isset( $this->ops['message'] ) ) {
			$custom_js .= 'message:\'' . addslashes( $this->ops['message'] ) . '\',';
		}
		if ( $this->ops['returning_visitor'] == true ) {
			$custom_js .= 'returningVisitor: \'true\',';
		}
		if ( isset( $this->ops['animation_in'] ) ) {
			$custom_js .= 'animationIn: \'' . $this->ops['animation_in'] . '\',';
		}
		if ( isset( $this->ops['animation_out'] ) ) {
			$custom_js .= 'animationOut: \'' . $this->ops['animation_out'] . '\',';
		}
		if ( isset( $this->ops['start_delay'] ) ) {
			$custom_js .= 'startdelay: \'' . $this->ops['start_delay'] . '\',';
		}
		if ( isset( $this->ops['lifespan'] ) ) {
			$custom_js .= 'lifespan: \'' . $this->ops['lifespan'] . '\',';
		}
		if ( isset( $this->ops['expire'] ) ) {
			$custom_js .= 'expire: \'' . $this->ops['expire'] . '\',';
		}
		// Check if we have an icon before setting to show it
		if (isset($this->ops['show_touch_icon']) && true == $this->ops['show_touch_icon']  && isset($this->ops['touch_icon']) && is_array( $this->ops['touch_icon'] ) ) {
			$custom_js .= 'touchIcon: \'true\',';
		}
		$custom_js .= '};</script>';
		echo( $custom_js . "\n" );
	}

	/**
	 * Output apple mobile meta
	 * - Title & iOS bookmark icons
	 *
	 * @return void
	 */	
	  
	   public function output_ios_meta() {
		// Always output moble-web-app-title
		echo( '<meta name="apple-mobile-web-app-title" content="' . wp_title( '', false ) . "\">\n" );

		// Output any touch icons we have; skip otherwise
		if(isset($this->ops['touch_icon']) && is_array( $this->ops['touch_icon'] ) ) {
			$this->array_reorder_keys($this->ops['touch_icon'], '57x57,60x60,72x72,76x76,114x114,144x144,120x120,128x128,152x152,167x167,180x180,256x256'); 
			
			foreach( $this->ops['touch_icon'] as $key => $val ) {
				if( !empty( $val['url'] ) ) {
					if( $this->ops['icon_precomposed'] )
						echo( '<link rel="apple-touch-icon-precomposed" sizes="' . $key . '" href="' . $val['url'] . "\" />\n" );
					else 
						echo( '<link rel="apple-touch-icon" sizes="' . $key . '" href="' . $val['url'] . "\" />\n" );
				}
			}
		}

		if(isset($this->ops['icon']) && is_array( $this->ops['icon'] ) ) {
			$this->array_reorder_keys($this->ops['icon'], '48x48,96x96,144x144,192x192,256x256,384x384');

			foreach( $this->ops['icon'] as $key => $val ) {
				if( !empty( $val['url'] ) ) {
					echo( '<link rel="icon" sizes="' . $key . '" href="' . $val['url'] . "\" />\n" );
				}
			}
		}

		if(isset($this->ops['tile_icon']) && is_array( $this->ops['tile_icon'] ) ) {
			foreach( $this->ops['tile_icon'] as $key => $val ) {
				if( !empty( $val['url'] ) ) {
						echo( '<meta name="msapplication-TileImage" content="' . $val['url'] . "\" />\n" );
				}
			}
		}
	}

	/**
	 * Check page_target & setup script & styles if needed
	 *
	 * @return void
	 */	
	public function setup_bookmark_popup() {
		// Check if home target & home page or if active on all pages
		if ( ( $this->ops['page_target'] == 'home_only' && ( is_home() || is_front_page() ) ) || $this->ops['page_target'] == 'all_pages' ) {
			// Output admin config variables
			add_action( 'wp_head', array( &$this, 'output_configuration_script' ), 8 );
			// Also enqueue included styles & script
			add_action( 'wp_enqueue_scripts', array( &$this, 'import_includes' ), 10 );
		}
	}
}