<?php

defined('ABSPATH') or die("Cannot access pages directly.");

$config = unserialize(get_option('tdc_sponsored_content'));

if (!isset($config['dfp'])) {
	$config['dfp'] = array();
	$config['dfp']['account'] = 'Account #';

	$config['dfp']['leader_name'] = '';
	$config['dfp']['leader_id'] = '';
	$config['dfp']['primary_name'] = '';
	$config['dfp']['primary_id'] = '';
	$config['dfp']['narrow_name'] = '';
	$config['dfp']['narrow_id'] = '';
	// $config['dfp'][' heading_text_on_sponsored_page'] = '';
	$config['layout'] = 'CNP';
}
if ( !empty($_POST)) {

	check_admin_referer('sponsored_content_admin_page');

	$config['dfp']['account'] = $_POST['dfp_account'];

	$config['dfp']['leader_name'] =  $_POST['dfp_slot_leaderboard_name'];
	$config['dfp']['leader_id'] =  $_POST['dfp_slot_leaderboard_id'];
	$config['dfp']['primary_name'] = $_POST['dfp_slot_primary_name'];
	$config['dfp']['primary_id'] = $_POST['dfp_slot_primary_id'];
	$config['dfp']['narrow_name'] = $_POST['dfp_slot_narrow_name'];
	$config['dfp']['narrow_id'] = $_POST['dfp_slot_narrow_id'];
	$config['heading_text_on_sponsored_page'] = $_POST['heading_text_on_sponsored_page'];
	$config['layout'] = $_POST['template_layout'];
	$config['sponsored_option_byline'] = $_POST['sponsored_option_byline'];

	if (current_user_can('manage_options')) {
		update_option('tdc_sponsored_content', serialize($config));
	}
}

?>

<form method=POST>
	<h3>DFP Settings</h3>
	DFP Account number:
	<input name=dfp_account type=textbox size=15 value="<?php if(isset($config['dfp'])) {echo $config['dfp']['account'];} ?>" /><br>

	<br>
	<?php wp_nonce_field('sponsored_content_admin_page'); ?>

	<h4>DFP Leader Board Slot (728x90)</h4>
	Name: <input name=dfp_slot_leaderboard_name type=textbox size=40 value="<?php if (isset($config['dfp'])) {echo $config['dfp']['leader_name'];} ?>" /><br>
	ID: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name=dfp_slot_leaderboard_id type=textbox size=40 value="<?php if (isset($config['dfp'])) {echo $config['dfp']['leader_id'];} ?>" /><br>

	<h4>DFP Primary Slot (300x250)</h4>
	Name: <input name=dfp_slot_primary_name type=textbox size=40 value="<?php if (isset($config['dfp'])) {echo $config['dfp']['primary_name'];} ?>" /><br>
	ID: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name=dfp_slot_primary_id type=textbox size=40 value="<?php if (isset($config['dfp'])) {echo $config['dfp']['primary_id'];} ?>" /><br>

	<h4>DFP Narrow Slot (180x150)</h4>
	Name: <input name=dfp_slot_narrow_name type=textbox size=40 value="<?php if (isset($config['dfp'])) {echo $config['dfp']['narrow_name'];} ?>" /><br>
	ID: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name=dfp_slot_narrow_id type=textbox size=40 value="<?php if (isset($config['dfp'])) {echo $config['dfp']['narrow_id'];} ?>" /><br>
	<h4>Template Layout</h4>
	<select name="template_layout">
	  <option value="NCP" <?php if (isset($config['layout']) && $config['layout'] == "NCP") { echo ' selected="selected" ';} ?>>Narrow - Content - Primary (Default)</option>
	  <option value="CNP" <?php if (isset($config['layout']) && $config['layout'] == "CNP") { echo ' selected="selected" ';} ?>>Content - Narrow - Primary </option>
	  <option value="CPN" <?php if (isset($config['layout']) && $config['layout'] == "CPN") { echo ' selected="selected" ';} ?>>Content - Primary - Narrow</option>	  
	  <option value="PNC" <?php if (isset($config['layout']) && $config['layout'] == "PNC") { echo ' selected="selected" ';} ?>>Primary - Narrow - Content</option>
	  <option value="PCN" <?php if (isset($config['layout']) && $config['layout'] == "PCN") { echo ' selected="selected" ';} ?>>Primary - Content - Narrow</option>
	  <option value="NPC" <?php if (isset($config['layout']) && $config['layout'] == "NPC") { echo ' selected="selected" ';} ?>>Narrow - Primary - Content</option>
	</select>
	<br><br>

	<p style="margin-bottom: 5px;"><b>Heading text on Sponsored page: </b></p> <input name= heading_text_on_sponsored_page type=textbox size=40 value="<?php echo $config['heading_text_on_sponsored_page']; ?>" />
	
	<br><br><br><br>

	<h3>Post Options and Byline Settings</h3>
	Enable Post option and Byline Option:

	<?php $sponsored_option_byline = isset($config['sponsored_option_byline']) ? $config['sponsored_option_byline'] : ''; ?>
	<input name="sponsored_option_byline" type="checkbox" value="yes" <?php checked( 'yes', $sponsored_option_byline  ); ?> />
	<!-- <input name=sponsored_option_byline type=textbox size=15 value="<?php // if(isset($config['sponsored_option_byline'])) {echo $config['sponsored_option_byline'];} ?>" />
	<p>Please use <strong><i>yes</i></strong> or <strong><i>no</i></strong> and avoid empty space</p> -->
	
	<br><br>

	<input type=submit />

</form>

<?php

?>
