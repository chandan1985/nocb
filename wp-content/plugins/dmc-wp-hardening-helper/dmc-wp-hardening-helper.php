<?php
/*
Plugin Name: dmc wp hardening helper
Plugin URI: http://thedolancompany.com
Description: Plugin to harden wordpress and help security
Author: Jerry Johnson
Version: 0.9.1
Author URI:
*/

// Prevent direct file call
if (!defined( 'ABSPATH' ))
	die( 'Direct access not allowed.' );

// create the table_base_prefix var if it doesnt exist
global $table_prefix,$table_base_prefix;
if (!$table_base_prefix) {$table_base_prefix=preg_replace("/".$blog_id."_/si", "", $table_prefix);}


	// add a non-versioned generator meta tag back in
	function dmc_generator() {
		echo '<meta name="generator" content="WordPress" />';
	}

// remove the version number from the generator meta tag, but leave Wordpress as the generator (for wp stats)
remove_action('wp_head', 'wp_generator');
add_action('wp_head', 'dmc_generator');

// remove the wlwmanifest meta tag
remove_action('wp_head', 'wlwmanifest_link');

if (!get_option('dmcwphh_enable_xmlrpc') ) {
	add_filter( 'xmlrpc_enabled', '__return_false' );
}

function dmcwphh_menus() {
	add_management_page('WP Hardening Helper', 'WP Hardening Helper', 'administrator', 'dmcwphh', 'dmcwphh_admin_screen');
	add_action( 'admin_init', 'dmcwphh_register_mysettings' );
}
add_action('admin_menu', 'dmcwphh_menus');

function dmcwphh_register_mysettings() {
	//register our settings
	register_setting( 'dmcwphh-settings-group', 'dmcwphh_enable_xmlrpc' );
}

function dmcwphh_admin_screen() {
	global $wpdb,$wp_version,$table_base_prefix;

	if ( isset($_GET['settings-updated']) ) {
		echo '<div id="message" class="updated fade"><p><strong>'. _e('Hardening Helper Settings saved.') .'</strong></p></div>';
	}
	
	$page_action = isset($_POST["form_requested_step"]) ? $_POST["form_requested_step"] : '';
	Switch($page_action) {
		// ****************************************************
		//  Populate table with unique archive author values
		// ****************************************************
		Case "fixadminloginname":
			$user_info = get_userdata(1);
			if ($user_info->user_login == 'admin') {
				$j_sql="UPDATE wp_users SET user_login = 'tdc-admin-owner' WHERE user_login = 'Admin';";
				$wpdb->query($j_sql);
			}
			break;	
	}       

	$x = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	
	echo '<div class="wrap">';
	echo '	<h2>WP Hardening Helper</h2>';

	echo '<table>';
	echo '<tr><td>WP Version Number removed from generator metatag</td><td><img src="'.$x.'images/green-led.gif" alt="ok" /></td></tr>';
	echo '<tr><td>WP generator metatag removed.</td><td><img src="'.$x.'images/yellow-led.gif" alt="caution" /></td></tr>';
	echo '<tr><td>WLWManifest Link metatag removed.</td><td><img src="'.$x.'images/green-led.gif" alt="ok" /></td></tr>';
	
	echo '<tr><td>XMLRPC Disabled.</td>';
	if ( get_option('dmcwphh_enable_xmlrpc') ) {
		echo '<td><img src="'.$x.'images/red-led.gif" alt="error" /></td>';
	} else {
		echo '<td><img src="'.$x.'images/green-led.gif" alt="ok" /></td>';
	}
	echo '</tr>';
	
	echo '';
	echo '<tr><td>Is admin renamed?</td>';
	$user_info = get_userdata(1);
	if ($user_info->user_login == 'admin') {
		echo '<td><img src="'.$x.'images/red-led.gif" alt="error" /></td>';
		?>
		<td>
		<form action="" method="post">
			<input type="hidden" value="fixadminloginname" name="form_requested_step" />
			<input type="submit" value="fix admin login_name" title="fix admin login_name" />
		</form>
		</td>
		<?php 
		
	} else {
		echo '<td><img src="'.$x.'images/green-led.gif" alt="ok" /></td>';
	}
	echo '</tr>';

	echo '<tr><td>Is wp-config.php outside webroot?</td>';
	if (fs_get_wp_config_path()) {
		echo '<td><img src="'.$x.'images/red-led.gif" alt="error" /></td>';
	} else {
		echo '<td><img src="'.$x.'images/green-led.gif" alt="ok" /></td>';
	}
	echo '</tr>';
	
	echo '<tr><td>WP Version</td>';
	if ($wp_version == '2.6.5' || $wp_version == '2.9.2') {
		echo '<td><img src="'.$x.'images/red-led.gif" alt="error" /></td>';
	} 
	elseif ($wp_version == '3.3.1') {
		echo '<td><img src="'.$x.'images/green-led.gif" alt="ok" /></td>';
	}
	else {
		echo '<td><img src="'.$x.'images/yellow-led.gif" alt="ok" /> (version:'.$wp_version.')</td>';
	}
	echo '</tr>';

	echo '<tr><td>DB Prefix</td>';
	if ($table_base_prefix   == 'wp_') {
		echo '<td><img src="'.$x.'images/red-led.gif" alt="error" /></td>';
	} 
	else {
		echo '<td><img src="'.$x.'images/green-led.gif" alt="ok" /> ('.$table_base_prefix  .')</td>';
	}
	echo '</tr>';
	echo '<tr><td>SuperPassword?.</td><td><img src="'.$x.'images/green-led.gif" alt="error" /></td></tr>';
	echo '<tr><td>wp-config htaccess block?.</td><td></td></tr>';
	echo '<tr><td>install.php?.</td><td></td></tr>';
	echo '<tr><td>filepermissions?.</td><td></td></tr>';
	echo '</table>';
	echo '</div>';

//wp_update_user( array ('ID' => $user_id, 'user_url' => $website) ) ;
?>

	<div class="wrap">
		<h2><?php echo __('Hardening Helper Settings Page'); ?></h2>

		<form method="post" action="options.php">
			<?php settings_fields( 'dmcwphh-settings-group' ); ?>
			<?php do_settings_fields( __FILE__ ,'dmcwphh-settings-group'); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Enable XML-RPC:') ?></th>
					<td><input type="checkbox" name="dmcwphh_enable_xmlrpc" value="1" <?php if (get_option('dmcwphh_enable_xmlrpc') == 1) echo 'checked="checked"'; ?>) /></td>
				</tr>
			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>

		</form>
	</div>

<?php
	
}


function fs_get_wp_config_path()
{
    $base = dirname(__FILE__);
    $path = false;

    if (@file_exists(dirname(dirname($base))."/wp-config.php"))
    {
        $path = dirname(dirname($base))."/wp-config.php";
    }
    else
    if (@file_exists(dirname(dirname(dirname($base)))."/wp-config.php"))
    {
        $path = dirname(dirname(dirname($base)))."/wp-config.php";
    }
    else
    $path = false;

    if ($path != false)
    {
        $path = str_replace("\\", "/", $path);
    }
    return $path;
}
?>