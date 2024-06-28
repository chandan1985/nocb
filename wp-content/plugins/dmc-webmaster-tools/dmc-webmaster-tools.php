<?php
/*
Plugin Name: DMC Webmaster Tools
Version: 1.0
Plugin URI: http://www.dolanmedia.com/
Author: Jerry Johnson
Author URI: http://www.dolanmedia.com
Description: Assign Webmaster verification meta_fields
*/

// Prevent direct file call
if (!defined( 'ABSPATH' ))
	die( 'Direct access not allowed.' );

class dmc_webmaster_tools {

// Hook for adding admin menus
function dmc_webmaster_tools_append_meta() {

	$dmc_webmaster_tools_google = get_site_option('dmc_webmaster_tools_google'); 				
	$dmc_webmaster_tools_yahoo = get_site_option('dmc_webmaster_tools_yahoo'); 				
	$dmc_webmaster_tools_bing = get_site_option('dmc_webmaster_tools_bing'); 				
	$dmc_webmaster_tools_google_sitemap = get_site_option('dmc_webmaster_tools_google_sitemap'); 				
	$dmc_webmaster_tools_akamai = get_site_option('dmc_webmaster_tools_akamai'); 				
	$dmc_webmaster_tools_googleplus = get_site_option('dmc_webmaster_tools_googleplus'); 				
	
	if ($dmc_webmaster_tools_google) {
		echo "\n<meta name='google-site-verification' content='".$dmc_webmaster_tools_google."' />";
	}
	if ($dmc_webmaster_tools_yahoo) {
		echo "\n<meta name='y_key' content='".$dmc_webmaster_tools_yahoo."' />";
	}
	if ($dmc_webmaster_tools_bing) {
		echo "\n<meta name='msvalidate.01' content='".$dmc_webmaster_tools_bing."' />";
	}
	if ($dmc_webmaster_tools_google_sitemap) {
		echo "\n<meta name='verify-v1' content='".$dmc_webmaster_tools_google_sitemap."' />";
	}
	if ($dmc_webmaster_tools_akamai) {
		echo "\n<meta name='akamai' content='".$dmc_webmaster_tools_akamai."' />";
	}
	if ($dmc_webmaster_tools_googleplus) {
		echo "\n<link rel='publisher' href='https://plus.google.com/".$dmc_webmaster_tools_googleplus."' />";
	}
	echo "\n";
}
	
// Hook for adding admin menus
public static function admin_menu() {
	// add admin page in options tab
	add_options_page("Webmaster Tools", "Webmaster Tools", 'manage_options', 'webmaster-tools', array( 'dmc_webmaster_tools', 'display_admin')) ;
}

// settings page
public static function display_admin() {
	$dmc_webmaster_tools_google = get_site_option('dmc_webmaster_tools_google'); 				
	$dmc_webmaster_tools_yahoo = get_site_option('dmc_webmaster_tools_yahoo'); 				
	$dmc_webmaster_tools_bing = get_site_option('dmc_webmaster_tools_bing'); 				
	$dmc_webmaster_tools_google_sitemap = get_site_option('dmc_webmaster_tools_google_sitemap'); 		
	$dmc_webmaster_tools_akamai = get_site_option('dmc_webmaster_tools_akamai'); 		
	$dmc_webmaster_tools_googleplus = get_site_option('dmc_webmaster_tools_googleplus'); 		
	$message = null;
	$message_updated = __("Webmaster Tools Options Updated.", 'dmc_webmaster_tools');

	// update options
	if (isset($_POST['action']) && $_POST['action'] == 'wt_update') {
		$nonce = $_POST['wt-options-nonce'];
		if (!wp_verify_nonce($nonce, 'wt-options-nonce')) die ( 'Security Check - If you receive this in error, log out and back in to WordPress');
       	$message = $message_updated;       
			
		update_site_option('dmc_webmaster_tools_google', $_POST["dmc_webmaster_tools_google"]);  
		update_site_option('dmc_webmaster_tools_yahoo', $_POST["dmc_webmaster_tools_yahoo"]);  
		update_site_option('dmc_webmaster_tools_bing', $_POST["dmc_webmaster_tools_bing"]);  
		update_site_option('dmc_webmaster_tools_google_sitemap', $_POST["dmc_webmaster_tools_google_sitemap"]);  
		update_site_option('dmc_webmaster_tools_akamai', $_POST["dmc_webmaster_tools_akamai"]);  
		update_site_option('dmc_webmaster_tools_googleplus', $_POST["dmc_webmaster_tools_googleplus"]);  
		$dmc_webmaster_tools_google = get_site_option('dmc_webmaster_tools_google'); 				
		$dmc_webmaster_tools_yahoo = get_site_option('dmc_webmaster_tools_yahoo'); 				
		$dmc_webmaster_tools_bing = get_site_option('dmc_webmaster_tools_bing'); 				
		$dmc_webmaster_tools_google_sitemap = get_site_option('dmc_webmaster_tools_google_sitemap'); 				
		$dmc_webmaster_tools_akamai = get_site_option('dmc_webmaster_tools_akamai'); 				
		$dmc_webmaster_tools_googleplus = get_site_option('dmc_webmaster_tools_googleplus'); 				
	}
	?>
	<?php if ($message) : ?>
		<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
	<?php endif; ?>
		<div class="wrap">
			<h2><?php _e('Webmaster Tools Options', 'dmc_webmaster_tools'); ?></h2>
			<h3 class="title">Webmaster Tools Verification <a href="http://en.support.wordpress.com/webmaster-tools/">(?)</a></h3>
			<p>Enter your meta key "content" value to verify your blog with <a href="https://www.google.com/webmasters/tools/">Google Webmaster Tools</a>, <a href="https://siteexplorer.search.yahoo.com/">Yahoo! Site Explorer</a>, and <a href="http://www.bing.com/webmaster">Bing Webmaster Center</a></p>
				
			<form name="dofollow" action="" method="post">
				<table class="form-table">
					<tr valign='top'>
						<td>
							<?php _e('Google Webmaster Tools:', 'dmc_webmaster_tools')?>
						</td>
						<td>
							<input type="text" name="dmc_webmaster_tools_google" value="<?php echo $dmc_webmaster_tools_google; ?>" size="50"/><br />
							<label for='dmc_webmaster_tools_google'>Example: <code>&lt;meta name='google-site-verification' content='<strong>dBw5CvburAxi537Rp9qi5uG2174Vb6JwHwIRwPSLIK8</strong>'&gt;</code></label>
						</td>
					</tr>
					<tr>
						<td>
						<?php _e('Yahoo! Site Explorer:', 'dmc_webmaster_tools')?>
						</td>
						<td>
						<input type="text" name="dmc_webmaster_tools_yahoo" value="<?php echo $dmc_webmaster_tools_yahoo; ?>" size="50"/><br />
						<label for='dmc_webmaster_tools_yahoo'>Example: <code>&lt;meta name='y_key' content='<strong>3236dee82aabe064</strong>'&gt;</code></label>
						</td>
					</tr>
					<tr>
						<td>
						<?php _e('Bing Webmaster Center:', 'dmc_webmaster_tools')?>
						</td>
						<td>
						<input type="text" name="dmc_webmaster_tools_bing" value="<?php echo $dmc_webmaster_tools_bing; ?>" size="50"/><br />
						<label for='dmc_webmaster_tools_bing'>Example: <code>&lt;meta name='msvalidate.01' content='<strong>12C1203B5086AECE94EB3A3D9830B2E</strong>'&gt;</code></label>
						</td>
					</tr>
					<tr>
						<td>
							<?php _e('Google Sitemap:', 'dmc_webmaster_tools')?>
						</td>
						<td>
							<input type="text" name="dmc_webmaster_tools_google_sitemap" value="<?php echo $dmc_webmaster_tools_google_sitemap; ?>" size="50"/><br />
							<label for='dmc_webmaster_tools_google_sitemap'>Example: <code>&lt;meta name='verify-v1' content='<strong>7uiM/3Wjm0pOsRnGIh4TCp3237894317uebswsQsiP16XKUqJPKJkA</strong>'&gt;</code></label>
						</td>
					</tr>
					<tr>
						<td>
							<?php _e('Akamai:', 'dmc_webmaster_tools')?>
						</td>
						<td>
							<input type="text" name="dmc_webmaster_tools_akamai" value="<?php echo $dmc_webmaster_tools_akamai; ?>" size="50"/><br />
							<label for='dmc_webmaster_tools_akamai'>Example: <code>&lt;meta name='akamai' content='<strong>up</strong>'&gt;</code></label>
						</td>
					</tr>
					<tr>
						<td>
							<?php _e('Google Plus:', 'dmc_webmaster_googleplus')?>
						</td>
						<td>
							<input type="text" name="dmc_webmaster_tools_googleplus" value="<?php echo $dmc_webmaster_tools_googleplus; ?>" size="50"/><br />
							<label for='dmc_webmaster_tools_googleplus'>Example: <code>&lt;link rel='publisher' href='https://plus.google.com/<strong>[yourpageID]</strong>'/&gt;</code></label>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="hidden" name="action" value="wt_update" />
					<input type="hidden" name="wt-options-nonce" value="<?php echo wp_create_nonce('wt-options-nonce'); ?>" />
					<input type="submit" name="Submit" value="<?php _e('Update Options', 'dmc_webmaster_tools')?> &raquo;" />
				</p>
			</form>
		</div>
	<?php

	$dmc_webmaster_tools_google = get_site_option('dmc_webmaster_tools_google'); 				
	$dmc_webmaster_tools_yahoo = get_site_option('dmc_webmaster_tools_yahoo'); 				
	$dmc_webmaster_tools_bing = get_site_option('dmc_webmaster_tools_bing'); 				
	$dmc_webmaster_tools_google_sitemap = get_site_option('dmc_webmaster_tools_google_sitemap'); 				
	$dmc_webmaster_tools_akamai = get_site_option('dmc_webmaster_tools_akamai'); 				
	$dmc_webmaster_tools_googleplus = get_site_option('dmc_webmaster_tools_googleplus'); 				
	}
}

// Add meta tags
$obj_dmc_webmaster_tools = new dmc_webmaster_tools();
add_action('wp_head', array($obj_dmc_webmaster_tools, 'dmc_webmaster_tools_append_meta'));


/* Setup the plugin options settings menu*/
add_action('admin_menu', array( 'dmc_webmaster_tools', 'admin_menu'));
?>