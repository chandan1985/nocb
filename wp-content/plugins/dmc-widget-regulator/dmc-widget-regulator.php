<?php

/*
 * Plugin Name: DMC Widget Regulator
 * Plugin URI: http://www.dolanmedia.com
 * Description: Stores and restores widget/sidebar configuration backups. Also locks editing of widgets admin to first user, avoiding conflicts. This is a known bug with WP 2.8 - WP 3.0 sites.
 * Author: Dave Buchanan
 * Version: 0.1
 * Author URI: http://www.dolanmedia.com
 */

// Prevent direct file call
if (!defined( 'ABSPATH' ))
	die( 'Direct access not allowed.' );

if(is_admin()) {
	$regulate = new dmc_widget_regulator();
	// Init - create table(s)
	add_action('admin_head', array($regulate,'install'));
	// Hook to lock widgets page for editing 
	add_action('admin_notices', array($regulate,'check_lock'));
	// Hook to backup widget settings when saved
	add_action('update_option_sidebars_widgets', array($regulate,'save_widgets'));
	// Hook for adding admin menus
	add_action('admin_menu', array($regulate, 'admin_menu'));
}

class dmc_widget_regulator {
	
	function __construct() {
		//constructor
		global $wpdb;		
		$this->tbl = $wpdb->prefix . 'widget_backups';
	}
	function install() {
		global $wpdb;
		
		//create table if doesn't exist yet			
		if($wpdb->get_var("SHOW TABLES LIKE '" . $this->tbl . "'") != $this->tbl) {
			
			$sql = "CREATE TABLE `$this->tbl` (`widget_id` bigint(20) NOT NULL auto_increment, `widget_value` longtext collate utf8_unicode_ci NOT NULL, `user_id` bigint(20) NOT NULL default 0, `create_date` timestamp NOT NULL default CURRENT_TIMESTAMP, PRIMARY KEY  (`widget_id`), KEY (`user_id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$wpdb->query($sql);
		}

	}
	function insert_backup($new_value) {
		global $wpdb;

		////weird appending that WP does to the sidebars_widgets option (wp-includes/widgets.php)
		unset($new_value['array_version']);

		///serialize this same as how WP does in update_option function
		$new_value = maybe_serialize( $new_value );

		///don't insert if its same as last one
		$last_value = $wpdb->get_col("SELECT widget_value FROM `".$this->tbl."` ORDER BY widget_id desc limit 1");
		///var_dump($last_value); echo $new_value . "<HR>". $last_value[0];die;		
		$last_value = $last_value[0];
		if($last_value !== $new_value) {
			$sql = "INSERT INTO `".$this->tbl."`(widget_value, create_date, user_id) VALUES ('".$new_value."', CURRENT_TIMESTAMP, " . get_current_user_id() . ")";
			$wpdb->query($sql);
		}
	}
	function save_widgets($newvalue) {
		//store backup of widgets
		$this->insert_backup($newvalue);
	}
	private function update_lock($bool = true) {
		$tmp = array();
		$tmp['lock'] = $bool;
		$tmp['owner'] = get_current_user_id();
		$tmp['time'] = time();
		update_option('dmcwr_widget_lock', $tmp);
	}
	function check_lock() {
		global $notices, $messages;
		// Get current page name
		$script_name = basename( $_SERVER['PHP_SELF'] );

		// Get current lock status (locked:true/false and owner:userid)
		$op = get_option("dmcwr_widget_lock"); 
		$usr = get_userdata( $op['owner'] );
		
		// if widgets page, lock it for editing
		if ($script_name == 'widgets.php') {
			
			if(isset($_GET['widget_lock']) && $_GET['widget_lock'] == "false")
				$this->update_lock(false);
			if(isset($_GET['widget_lock']) && $_GET['widget_lock'] == "true")
				$this->update_lock(true); 
			
			if($op['lock']) {
				echo "<div class='error'>Widgets are locked by '" . $usr->user_login."' on " . date('h:i a m/d/y ',$op['time']).". <a href='?widget_lock=false'>Unlock?</a> (WARNING: Unlocking could lead to conflicts and losing widgets)</div>";
				
				///kill if user isn't same as owner
				if($op['owner'] != get_current_user_id())
					die;
			}
			if(!isset($_GET['widget_lock'])) {
				$this->update_lock();
			}
		}
		/// user has left widgets page, ok to unlock it 
		else if (get_current_user_id() == $op['owner']) { 
			$this->update_lock(false);
		}
	}
	function admin_menu() {
		add_submenu_page( 'wpmu-admin.php', 'Widget Backup and Restore', 'Widget Backup and Restore', 'manage_options', 'widget-backup-and-restore', array($this, 'admin_page') );
		add_submenu_page( 'wpmu-admin.php', 'Delete Inactive Widgets', 'Delete Inactive Widgets', 'manage_options', 'delete-inactive-widgets', array($this, 'delete_admin_page') );
	}
	function admin_page() {
		global $wpdb;
		if(isset($_POST['restore'])) {
			//options table where record gets updated
			$options_tbl = $wpdb->prefix . "options";
			//Run update query, directly select value from widget backup table
			$sql_sel = "SELECT widget_value FROM `".$this->tbl."` WHERE widget_id = " . $_POST['restore'];
			$sql_up = "UPDATE `".$options_tbl."` SET option_value = (" . $sql_sel . ") WHERE option_name = 'sidebars_widgets'";
			$wv = $wpdb->query($sql_up);
 
			///output message
			echo '<div style="overflow: visible; display: block; background-color: rgb(255, 251, 204);" id="message" class="updated fade"><p>Sidebar Widgets restored....</p></div>'; 
		}
		/// Get data for displaying in form
		// get current sidebar config
		$cur = get_option('sidebars_widgets');
		
		// get last x records from widget backups
		$sql = "SELECT * FROM `".$this->tbl."` ORDER BY create_date desc limit 10";
		$rows = $wpdb->get_results($sql);
		include dirname( __FILE__ ) . '/admin-restore-form.php';
	}

	function delete_admin_page() {
                global $wpdb;

                if(isset($_POST['delete_widgets'])) {
                        $widgets = get_option( 'sidebars_widgets' );
                        $widgets['wp_inactive_widgets'] = array();
                        wp_set_sidebars_widgets( $widgets );
                        //$tmp_wids = get_option('sidebars_widgets');
                        //unset($tmp_wids['wp_inactive_widgets']);
                        //$tmp_wids['wp_inactive_widgets'] = array();
                        //var_dump($tmp_wids);
                        //update_option('sidebars_widgets',$tmp_wids);
                        echo "<p class='error'>Deleted All Inactive Widgets!</p>";
                }
                ?>
                <div class="wrap">
                <h2>Delete All Inactive Widgets</h2>
                <p class="message">WARNING! Clicking on the button below permanently deletes all <strong>INACTIVE</strong> widgets from this blog.</p>
        <p>
                <?php
                $wids = get_option('sidebars_widgets');
        //      var_dump($wids['wp_inactive_widgets']);
                echo "<p>There are currently " . count($wids['wp_inactive_widgets']) . " inactive widgets.</p>";
                ?>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=delete-inactive-widgets">
                <input type="hidden" name="delete_widgets" value="true"/>
                <p class="submit">
                         <input type="submit" name="Submit" value="<?php _e('Delete Inactive Widgets') ?>" />
                </p>
                </form>
                <?php
        }

}
?>
