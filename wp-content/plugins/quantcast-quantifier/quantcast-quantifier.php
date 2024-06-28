<?php
/*
 * Plugin Name: Quantcast Quantifier
 * Version: 1.5.2
 * Plugin URI: http://www.jamesturner.co.nz/other/wordpress/plugin-quantcast-quantifier/
 * Description: Adds the necessary JavaScript code to enable <a href="http://www.quantcast.com/">Quantcast</a>. After enabling this plugin visit <a href="options-general.php?page=quantcast-quantifier.php">the options page</a> and enter your Quantcast code and enable logging. Plugin Based on google-analyticator by Ronald Heft, Jr. Thanks for the template ;-) <a href="http://wordpress.org/extend/plugins/quantcast-quantifier/">Wordpress.org Link</a>
 * Author: James Turner
 * Author URI: http://www.jamesturner.co.nz
 */
/*

UPDATE HISTORY
==============
2011 06 18 - Version 1.5.2
BUG FIX: Cleared the setting/option that was actually printing to the screen.

2011 06 18 - Version 1.5.1
BUG FIX: The default settings were being loaded poorly by me.  Have now fixed and tested.  Thank you to those who reported the error.

2011 06 13 - Version 1.5
EDIT: Header/footer option removed
EDIT: Making it more translation friendly
EDIT: updated ga_ > qq_ (Thanks Vynce)
ADD: Advanced area added, this code is added to the head
ADD: Added Notes at the bottom of the plugin
ADD: Added register/activation hook and function
ADD: nonce when saving settings (Thanks Vynce)

2011 03 03 - Version 1.4
EDIT: add_option hooks updated
EDIT: add_options_page hook updated

2010 11 04 - Version 1.3
EDIT: House keeping.
UPGRADE: Added greater user role control based off google-analyticator

2009 03 10 - Version 1.2
EDIT: House keeping, updating a few links and wording for some things. 2.7+ screenshot.  Added Settings link to plugin page.

2008 10 17 - Version 1.1
TYPO: add_action('wp_head', 'add_quantcast_uantifier'); >>> add_action('wp_head', 'add_quantcast_quantifier'); - Thanks to dlbjeff http://www.drivelineblog.com for pointing it out.

*/
// Constants for enabled/disabled state
define("qq_enabled", "enabled");
define("qq_disabled", "disabled");

// Defaults, etc.
define("key_qq_status", "qq_status");
define("key_qq_admin", "qq_admin_status");
define("key_qq_admin_role", "qq_admin_role");
define("key_qq_tracker_code_advanced", "qq_tracker_code_advanced");
define("key_qq_tracker_code", "qq_tracker_code");

define("qq_status_default", qq_disabled);
define("qq_admin_default", qq_enabled);
define("qq_tracker_code_default", '');
define("qq_tracker_code_advanced_default", '');

// Create the default key and status
add_option(key_qq_status, qq_status_default, '');									// If Quantcast Quantifier logging in turned on or off.
add_option(key_qq_admin, qq_admin_default, '');										// If WordPress admins are counted in Quantcast Quantifier.
add_option(key_qq_admin_role, array('administrator'), '');							// Select which roles are to be excluded in Quantcast Quantifier.
add_option(key_qq_tracker_code_advanced, qq_tracker_code_advanced_default, '');		// Addition Quantcast Quantifier tracking options
add_option(key_qq_tracker_code, qq_tracker_code_default, '');						// Addition Quantcast Quantifier tracking options


// Add settings link to plugin page. 
function qq_plugin_action_links($links, $file) {
	static $this_plugin;
 
	if( !$this_plugin ) $this_plugin = plugin_basename(__FILE__);
 
	if( $file == $this_plugin ){
		$settings_link = '<a href="options-general.php?page=quantcast-quantifier.php">' . __('Settings') . '</a>';
		$links = array_merge($links, array($settings_link)); // after other links
	}
	return $links;
}

// Hook in the options page function
function add_qq_option_page() {
	add_options_page('Quantcast Quantifier Options', 'Quantcast Quantifier', 'manage_options', basename(__FILE__), 'qq_options_page');
}

function qq_options_page() {
	if (get_option('qq_tracker_code_advanced') == "qq_tracker_code_advanced_default") {
		update_option('qq_tracker_code_advanced', '');
	}
	
	if (get_option('qq_tracker_code') == "disabled") {
		update_option('qq_tracker_code', '');
	}
	// If we are a postback, store the options
 	if (isset($_POST['info_update'])) {
		if (check_admin_referer('quantcast-quantifier-nonce')) {
			// Update the status
			$qq_status = $_POST[key_qq_status];
			if (($qq_status != qq_enabled) && ($qq_status != qq_disabled))
				$qq_status = qq_status_default;
			update_option(key_qq_status, $qq_status);

			// Update the admin logging
			$qq_admin = $_POST[key_qq_admin];
			if (($qq_admin != qq_enabled) && ($qq_admin != qq_disabled))
				$qq_admin = qq_admin_default;
			update_option(key_qq_admin, $qq_admin);

			// Update the admin level
			$qq_admin_role = $_POST[key_qq_admin_role];
			update_option(key_qq_admin_role, $qq_admin_role);
			
			// Update the tracking code advanced for head
			$qq_tracker_code_advanced = $_POST[key_qq_tracker_code_advanced];
			update_option(key_qq_tracker_code_advanced, $qq_tracker_code_advanced);
			
			// Update the extra tracking code
			$qq_tracker_code = $_POST[key_qq_tracker_code];
			update_option(key_qq_tracker_code, $qq_tracker_code);
			
			// Give an updated message
			echo "<div class='updated fade'><p><strong>Quantcast Quantifier settings saved.</strong></p></div>";
		} // if (check_admin_referer('quantcast-quantifier-nonce'))
	}
	// Output the options page
	?>

	<div class="wrap">
		<form method="post" action="options-general.php?page=quantcast-quantifier.php">
		<?php wp_nonce_field('quantcast-quantifier-nonce'); ?>
			<h2><?php _e('Quantcast Quantifier Options', 'quantcast-quantifier'); ?></h2>
			<h3><?php _e('Basic Options', 'quantcast-quantifier'); ?></h3>
			<?php if (get_option(key_qq_status) == qq_disabled) { ?>
				<div style="margin:10px auto; border:3px #f00 solid; background-color:#fdd; color:#000; padding:10px; text-align:center;">
				<?php _e('Quantcast Quantifier integration is currently <strong>DISABLED</strong>.', 'quantcast-quantifier'); ?>
				</div>
			<?php } ?>
			<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
				<tr>
					<th width="30%" valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_qq_status ?>"><?php _e('Quantcast Quantifier logging is', 'quantcast-quantifier'); ?>:</label>
					</th>
					<td>
						<?php
						echo "<select name='".key_qq_status."' id='".key_qq_status."'>\n";
						
						echo "<option value='".qq_enabled."'";
						if(get_option(key_qq_status) == qq_enabled)
							echo " selected='selected'";
						echo ">Enabled</option>\n";
						
						echo "<option value='".qq_disabled."'";
						if(get_option(key_qq_status) == qq_disabled)
							echo" selected='selected'";
						echo ">Disabled</option>\n";
						
						echo "</select>\n";
						?>
					</td>
				</tr>
			</table>
			<h3><?php _e('Advanced Options', 'quantcast-quantifier'); ?></h3>
				<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
				<tr>
					<th width="30%" valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_qq_admin ?>"><?php _e('Track all logged in WordPress users', 'quantcast-quantifier'); ?>:</label>
					</th>
					<td>
						<?php
						echo "<select name='".key_qq_admin."' id='".key_qq_admin."'>\n";
						
						echo "<option value='".qq_enabled."'";
						if(get_option(key_qq_admin) == qq_enabled)
							echo " selected='selected'";
						echo ">" . __('Yes', 'quantcast-quantifier') . "</option>\n";
						
						echo "<option value='".qq_disabled."'";
						if(get_option(key_qq_admin) == qq_disabled)
							echo" selected='selected'";
						echo ">" . __('No', 'quantcast-quantifier') . "</option>\n";
						
						echo "</select>\n";
						
						?>
						<br><span class="description"><?php _e('Selecting "no" to this option will prevent logged in WordPress users from showing up on your Quantcast reports. This setting will prevent yourself or other users from showing up in your Quantcast reports. Use the next setting to determine which user roles to exclude.', 'quantcast-quantifier'); ?></span>
					</td>
				</tr>
				<tr>
					<th width="30%" valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_qq_admin_role ?>"><?php _e('User roles to not track', 'quantcast-quantifier'); ?>:</label>
					</th>
					<td>
						<?php						
						global $wp_roles;
						$roles = $wp_roles->get_names();
						$selected_roles = get_option(key_qq_admin_role);
						if ( !is_array($selected_roles) ) $selected_roles = array();
						
						# Loop through the roles
						foreach ( $roles AS $role => $name ) {
							echo '<input type="checkbox" value="' . $role . '" name="' . key_qq_admin_role . '[]"';
							if ( in_array($role, $selected_roles) )
								echo " checked='checked'";
							$name_pos = strpos($name, '|');
							$name = ( $name_pos ) ? substr($name, 0, $name_pos) : $name;
							echo ' /> ' . _x($name, 'User role') . '<br />';
						}
						?>
						<br><span class="description"><?php _e('Specifies the user roles to not include in your WordPress Quantcast report. If a user is logged into WordPress with one of these roles, they will not show up in your Quantcast report.', 'quantcast-quantifier'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_qq_tracker_code_advanced; ?>"><?php _e('Advanced (Optional)', 'quantcast-quantifier'); ?>:</label>
					</th>
					<td>
						<?php
						echo "<textarea cols='100' rows='8' ";
						echo "name='".key_qq_tracker_code_advanced."' ";
						echo "id='".key_qq_tracker_code_advanced."'>";
						echo stripslashes(get_option(key_qq_tracker_code_advanced))."</textarea>\n";
						?><br>
						<span class="description">
							<li><?php _e('This section will be placed before the &lt;/head&gt; tag.', 'quantcast-quantifier'); ?></li>
							<li><?php _e('Leave this field empty or you can put tracking code here for the header.', 'quantcast-quantifier'); ?></li>
							<li><?php _e('Advanced users can optimize the performance of the Quancast asynchronous tag by splitting the tag into two components.', 'quantcast-quantifier'); ?></li>
							<li><?php printf(__('Please check the %1$sQuantcast Help%2$s file for more info.'), '<a href="http://www.quantcast.com/learning-center/guides/webmeasurement" target="_blank" title="Open Quantcast site">', '</a>'); ?></li>
						</span>
					</td>
				</tr>
				<tr>
					<th valign="top" style="padding-top: 10px;">
						<label for="<?php echo key_qq_tracker_code; ?>"><?php _e('Tracking code (Required)', 'quantcast-quantifier'); ?>:</label>
					</th>
					<td>
						<?php
						echo "<textarea cols='100' rows='8' ";
						echo "name='".key_qq_tracker_code."' ";
						echo "id='".key_qq_tracker_code."'>";
						echo stripslashes(get_option(key_qq_tracker_code))."</textarea>\n";
						?><br>
						<span class="description">
							<li><?php _e('This secction will be placed before the &lt;/body&gt; tag.', 'quantcast-quantifier'); ?></li>
							<li><?php _e('Leave this field empty or you can put tracking code here for the footer.', 'quantcast-quantifier'); ?></li>
							<li><?php printf(__('You will need a %1$s Quantcast account%2$s and use the "Quantify a Site" Wizard on the right hand side of the Quantcast website to generate your tracking code.', 'quantcast-quantifier'), '<a href="http://www.quantcast.com/" target="_blank" title="Open Quantcast site">', '</a>'); ?></li>  
							<li><?php _e('Be sure to use _qevents, not _qoptions::, this is important if you are using the Advanced option.', 'quantcast-quantifier'); ?></li>
						</span>
					</td>
				</tr>
				</table>
			<p class="submit">
				<input type='submit' name='info_update' value='Save Changes' />
			</p>
		</form>
	</div>
	<div style="margin: 10px; padding: 0 20px 30px 20px; border: 1px solid;">
		<p>
			<div style="float: right;"><img src="<?php echo WP_PLUGIN_URL; ?>/quantcast-quantifier/images/quantcast-quantifier-logo.png" alt="Quantcast-Quantifier Logo"></div>
			<strong><?php _e('Notes:', 'quantcast-quantifier'); ?></strong>
			<li><?php _e('I\'m not associated with Quantcast, other than being a user of the service, and this is not an official Quantcast product.', 'quantcast-quantifier'); ?></li>
			<li>I maintain this plugin as a hobby.</li>
			<li><a href="http://wordpress.org/extend/plugins/quantcast-quantifier/" target="_blank" title="Wordpress Plugin Page">http://wordpress.org/extend/plugins/quantcast-quantifier/</a></li>
			<li><a href="http://www.jamesturner.co.nz/other/wordpress/plugin-quantcast-quantifier/" target="_blank" title="Author Plugin Page">http://www.jamesturner.co.nz/other/wordpress/plugin-quantcast-quantifier/</a></li>
			<li>If you find value in this plugin and you would like to make a donation, <a href="http://www.jamesturner.co.nz/other/donate/" target="_blank" alt="Donation Page">you can do so here</a>, Thank you.
			<li>Enjoy</li>
		</p>
	</div>
<?php
}

// The guts of the Quantcast Quantifier script to the Head
function add_quantcast_quantifier_head() {
	$extra = stripslashes(get_option(key_qq_tracker_code_advanced));
	
	// If QQ is enabled and has a valid key
	if (get_option(key_qq_status) != qq_disabled) {
		
		// Track if admin tracking is enabled or disabled and the user role
		if ((get_option(key_qq_admin) == qq_enabled) || ((get_option(key_qq_admin) == qq_disabled) && ( !qq_current_user_is(get_option(qq_admin_role)) ))) {
			// Insert tracker code
			if ( '' != $extra ) {
				echo "<!-- START Quantcast By WP-Plugin: Quantcast-Quantifier http://wordpress.org/extend/plugins/quantcast-quantifier/ -->\n";
				echo $extra . "\n";
				echo "<!-- END Quantcast-Quantifier -->";
			}
		}
	}
}

// The guts of the Quantcast Quantifier script to the Foot
function add_quantcast_quantifier_foot() {
	$extra = stripslashes(get_option(key_qq_tracker_code));
	
	// If QQ is enabled and has a valid key
	if (get_option(key_qq_status) != qq_disabled) {
		
		// Track if admin tracking is enabled or disabled and the user role
		if ((get_option(key_qq_admin) == qq_enabled) || ((get_option(key_qq_admin) == qq_disabled) && ( !qq_current_user_is(get_option(qq_admin_role)) ))) {
			// Insert tracker code
			if ( '' != $extra ) {
				echo "<!-- START Quantcast By WP-Plugin: Quantcast-Quantifier http://wordpress.org/extend/plugins/quantcast-quantifier/ -->\n";
				echo $extra . "\n";
				echo "<!-- END Quantcast-Quantifier -->";
			}
		}
	}
}

/**
 * Determines if a specific user fits a role
 **/
function qq_current_user_is($roles)
{
	if ( !$roles ) return false;

	global $current_user;
	get_currentuserinfo();
	$user_id = intval( $current_user->ID );

	if ( !$user_id ) {
		return false;
	}
	$user = new WP_User($user_id); // $user->roles
	
	foreach ( $roles as $role )
		if ( in_array($role, $user->roles) ) return true;
	
	return false;
}

/**
 * http://codex.wordpress.org/Function_Reference/register_activation_hook
 **/
function qq_activate() {
	// This removes the old setting from the DB.  No longer needed from version 1.5
	if (get_option('qq_footer')) {
		delete_option('qq_footer');
	}
	
	if (get_option('qq_tracker_code_advanced') == "qq_tracker_code_advanced_default") {
		update_option('qq_tracker_code_advanced', '');
	}
	
	if (get_option('qq_tracker_code') == "disabled") {
		update_option('qq_tracker_code', '');
	}
}

// Add Settings link to plugin page
add_filter('plugin_action_links', 'qq_plugin_action_links', 10, 2);

// Create a option page for settings
add_action('admin_menu', 'add_qq_option_page');

// Add the script to the site
add_action('wp_head', 'add_quantcast_quantifier_head');
add_action('wp_footer', 'add_quantcast_quantifier_foot');

// register/activation hook
register_activation_hook( __FILE__, 'qq_activate' );
?>