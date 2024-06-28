<?php

/*
  Plugin Name: Asentech - Daily Alert
  Plugin URI: http://asentechllc.com/
  Description: Allows you to show Daily Alert form on any WordPress site having Jarida Theme
  Version: 1.0.0
  Author: asentech
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Daily_Alert {

    public function __construct($file) {
        $this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
        require_once( 'classes/daily-alert-admin.php' );
        //Add style
		
        add_shortcode( 'asn_daily_alert', array( &$this, 'wpb_demo_shortcode' ), 11, 2 ); 
    }
	
	// function that runs when shortcode is called
	public function wpb_demo_shortcode() { 
	
	wp_enqueue_style('style', esc_url($this->plugin_url . 'css/daily-alert.css'));
	
	$daily_alert = get_option( 'daily-alert' );
	$left   = $daily_alert['left_txt'];
	$right  = $daily_alert['right_txt'];
	$act_on = $daily_alert['act_on'];
	// Things that you want to do. 
	//$acton_code = do_shortcode( '[bluesky_form id="'.$act_on.'"]' );
	
	$message  = do_shortcode( '[one_half]' . $left . '[/one_half]' );
	$message .= do_shortcode( '[one_half_last]' . do_shortcode( '[bluesky_form id="'.$act_on.'"]' ) .$right . '[/one_half_last]' );
	// Output needs to be return
	return $message;
	} 

}

$start = new Daily_Alert(__FILE__);