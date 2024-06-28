<?php
/*
Plugin Name: DMC Bluesky Embed
Version: 0.1
Plugin URI: http://www.thedolancompany.com/
Author: Dave Long
Author URI: http://www.thedolancompany.com
Description: Securely embed Publicaster opt-in forms
*/

// Business rule: Access to admin panel limited to dolanmedia & thedolancompany email domains

if (!defined( 'ABSPATH' ))
	die( 'Direct access not allowed.' );

$dmc_bluesky_embed = new dmc_bluesky_embed();

class dmc_bluesky_embed {

	
	// Build class set up action or shortcode based on page type
	function __construct() {
		//add_action('wp_enqueue_scripts', array(&$this,'dmcblueskyembed_enqueue_scripts'));
		if(is_admin())
			add_action('admin_menu', array(&$this,'dmcblueskyembed_admin_menuitem'));
		else
			add_shortcode('bluesky_form', array(&$this,'bluesky_shortcode'));
	}
	
	function dmcblueskyembed_enqueue_scripts(){
		    wp_register_script( 'dmc-bluesky-front-js', plugin_dir_url( __FILE__ ) . 'js/dmc-front.js', array(), false, true );
	}

// Add option page to WP admin panel if current user has a dolanmedia or thedolancompany email
function dmcblueskyembed_admin_menuitem() 
{
global $current_user;
preg_match("/.*\@(.*)\.com/", $current_user->user_email, $matches);
/* Modify Code 24-3-2022 */
if($current_user->user_email == 'dfurnas@bridgetowermedia.com' || $current_user->user_email == 'bredmond@studiobtm.com' || $current_user->user_email == 'drowe@studiobtm.com' || $current_user->user_email == 'cschultz@bridgetowermedia.com' || $matches[1] == 'thedolancompany' || $matches[1] == 'dolanmedia' || $matches[1] == 'asentech' || $matches[1] == 'asentechllc' || $matches[1] == 'btmadmin') 
{
if (function_exists('add_options_page'))
{
add_options_page('BlueSky Forms', 'BlueSky Forms', 'manage_options', basename(__FILE__), array(&$this,'dmcblueskyembed_adminsection'));
}
}
}
	
	// Invoke form via shortcode: [bluesky_form id="XX"]
	function bluesky_shortcode($atts) {
		extract(shortcode_atts(array(
			'id' => ''
		), $atts));
		$form_code = '';
		if (isset($id)) {
			//wp_enqueue_script( 'dmc-bluesky-front-js');
			$my_form = get_option('dmc_bluesky_embed_'.$id,'');
			$form_code = stripslashes($my_form['code']);
		}

		return $form_code;
	}

	// Build widget admin pages
	function dmcblueskyembed_adminsection() {
		global $wpdb;
		?>
		<div class="wrap">
			<h2>DMC BlueSky Embed Settings</h2>
		<?php
		$action = isset($_GET['action']) ? $_GET['action'] : '';
		$form_no = isset($_GET['form']) ? $_GET['form'] : '';

		switch($action) {
			// Delete specific form
			case 'delete_form' : 
				if(isset($form_no)) {
					delete_option('dmc_bluesky_embed_'.$form_no);
					echo('<div class="updated fade below-h2"><p style="font-weight:bold">BlueSky form ' . $form_no . ' deleted.</p></div>');
					echo('<h4><a href="options-general.php?page=dmc-bluesky-embed.php">Back to list</a></h4>');
				} else {
					echo('<div class="error"><p style="font-weight:bold">No form defined.</p></div>');
					echo('<h4><a href="options-general.php?page=dmc-bluesky-embed.php">Back to list</a></h4>');
				}			
			break;
			
			// Add new forms or edit existing ones
			case 'edit_form' : 
				if(isset($_GET['form'])){
					$form_no = $_GET['form'];
				} else {
					$form_no = 1;
				}
					
				if(isset($_POST['dmc_bluesky_embed_'.$form_no])){
					$settings = array();		
					if(is_array($_POST['dmc_bluesky_embed_'.$form_no])){			
						$settings = $_POST['dmc_bluesky_embed_'.$form_no];
						$settings['code'] = stripslashes($settings['code']);
						if(get_option('dmc_bluesky_embed_'.$form_no) !== false){
							update_option('dmc_bluesky_embed_'.$form_no, $settings);
						}
						else{
							add_option('dmc_bluesky_embed_'.$form_no, $settings);
						}
						echo('<div class="updated fade below-h2">
							<p style="font-weight:bold">Options saved.</p></div>');
					}
				}else
					$settings = get_option('dmc_bluesky_embed_'.$form_no,array('name' => 'Publicaster Form', 'id' => $form_no, 'code' => ''));?>

				<h4><a href="options-general.php?page=dmc-bluesky-embed.php">DMC BlueSky Embed</a> > Form <?php echo($form_no) ?></h4>		
				<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
					<?php wp_nonce_field('update-options'); ?>
					<h4>Name of this instance</h4>
					<input type=textfield size="60" name="dmc_bluesky_embed_<?php echo($settings['id']); ?>[name]" id="name" value="<?php echo($settings['name']); ?>"/>
					<input type="hidden" name="dmc_bluesky_embed_<?php echo($settings['id']); ?>[id]" value="<?php echo($settings['id']); ?>" />
					<h4>Form Code</h4>
					<textarea  name="dmc_bluesky_embed_<?php echo($settings['id']); ?>[code]" cols="110" rows="20"><?php echo($settings['code']); ?></textarea>
					<div class="submit">
						<input class="button-primary" type="submit" name="Submit" value="Save Changes" />
					</div>
				</form>
				<p class="subsubsub mf" style="float:none;"><a href="options-general.php?page=dmc-bluesky-embed.php">Back to list</a></p>
			<?php break;

			// Default case - output table of currently configured forms
			default: ?>
				<p>Listed below are the Publicaster opt-in forms configured for use on this site. </p>
				<table class="widefat">
					<thead>
						<tr>
							<th>ID</th>
							<th>Name</th>
							<th>Short Code</th>
							<th>Actions</th>					
						</tr>
					</thead>				
					<?php 
					global $blog_id;
					$bluesky_forms = $wpdb->get_results("select option_name from wp_".$blog_id."_options where option_name like 'dmc_bluesky_embed%'");
					if(!empty($bluesky_forms)){
						$output = array();
						foreach($bluesky_forms as $form){
							$cur_form = get_option($form->option_name);
							$output[$cur_form['id']] = $cur_form;
						}
						ksort($output);
						foreach($output as $settings){
							$next_form = $settings['id'] + 1; ?>
							<tr>
								<td><?php echo($settings['id']); ?></td>
								<td><?php echo($settings['name']); ?></td>
								<td><?php echo(htmlspecialchars('[bluesky_form id="'.$settings['id'].'"]')); ?></td>
								<td><a href="options-general.php?page=dmc-bluesky-embed.php&action=edit_form&form=<?php echo($settings['id']); ?>">Edit</a> | 
									<a href="options-general.php?page=dmc-bluesky-embed.php&action=delete_form&form=<?php echo($settings['id']); ?>">Delete</a> 
								</td>
							</tr><?php
						}
					}else{
						$next_form = 1;
					?>
					<tr>
						<td colspan="4" class="alternate">There are no forms defined</td>
					</tr>
					<?php } ?>
				</table>
				<form method="post" action="options-general.php?page=dmc-bluesky-embed.php&action=edit_form&form=<?php echo($next_form); ?>">
					<p><input class="button-primary" type="submit" value="Add new form"></p>
				</form>
		<?php } ?>
		</div>
		<?php
	}
}
?>