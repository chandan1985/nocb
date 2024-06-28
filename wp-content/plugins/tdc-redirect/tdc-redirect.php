<?php

/*
 * Plugin Name: TDC Redirect
 * Plugin URI: http://www.thedolancompany.com
 * Description: Manages Redirects through Wordpress
 * Author: Chris Meier
 * Version: 0.1
 * Author URI: http://www.thedolancompany.com
 */

include(dirname(__FILE__) . '/redirect_ajax.php');

$redirect = new TDC_Redirect();

class TDC_Redirect{
	
	function __construct() {
		if(is_admin()) {
			add_action('network_admin_menu', array(&$this, 'redirect_menu'));
			add_action('admin_head', array(&$this, 'redirect_install'));
		}
		else {
			add_action('template_redirect', array(&$this, 'process_redirect'), 1);
		}
	}
	
	function redirect_menu() {
		global $current_user;
		preg_match("/.*\@(.*)\.com/", $current_user->user_email, $matches);
		if($matches[1] == 'thedolancompany' || $matches[1] == 'dolanmedia'){
			if (function_exists('add_menu_page')) {
				add_menu_page('TDC Redirects', 'TDC Redirects', 'manage_options', 'tdc-redirect-top-level', array(&$this, 'tdc_redirect_options'));
			}
			if (function_exists('add_submenu_page')) {
				add_submenu_page('tdc-redirect-top-level', 'TDC Add Redirect', 'TDC Add Redirect', 'manage_options', 'tdc-redirect-add', array(&$this, 'tdc_add_redirect'));
			}
		}
	}
	
	function redirect_install() {
		global $wpdb;
		
		$sql = "CREATE TABLE IF NOT EXISTS `wp_tdc_redirects`(
		redirect_id INT NOT NULL AUTO_INCREMENT,
		source VARCHAR(100) NOT NULL,
		target VARCHAR(100) NOT NULL,
		HTTP_Code INT(3) NOT NULL,
		description VARCHAR(300) NOT NULL,
		sort_order INT NOT NULL, 
		PRIMARY KEY (redirect_id));";
		
		$wpdb->query($sql);

		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('tdc-re', plugins_url('tdc-redirect.js', __FILE__));
		wp_enqueue_style('redirect-admin', plugins_url('redirect.css', __FILE__));
	}
	
	function tdc_add_redirect() {
		global $wpdb;
		$redirect_options = '';
		
		if(isset($_POST['submit'])) {	
			if( isset($_POST['redirect_source']) && isset($_POST['redirect_target']) && isset($_POST['http_code']) && isset($_POST['redirect_description']) ) {
				
				$tdc_source = stripslashes_deep($_POST['redirect_source']);
				
				$tdc_target = stripslashes_deep($_POST['redirect_target']);
				
				$tdc_http_code = $_POST['http_code'];
				
				$tdc_redirect_description = $_POST['redirect_description'];
				
				if(isset($_GET['redirectID'])) {
					$new_redirect = $wpdb->query($wpdb->prepare(
						"	
						UPDATE wp_tdc_redirects
						SET source = %s, target = %s, HTTP_Code = %d, description = %s
						WHERE redirect_id = ". $_GET['redirectID'] ."
						",
						$tdc_source,
						$tdc_target,
						$tdc_http_code,
						$tdc_redirect_description
					));
				}
				else {
					$new_sort = 1;
					
					$sort_query = $wpdb->get_results(
						"
						SELECT sort_order
						FROM wp_tdc_redirects
						ORDER BY sort_order DESC
						LIMIT 1
						"
					);
					
					if($sort_query) {
						foreach($sort_query as $sort) {
							$new_sort = $sort->sort_order;
							$new_sort++;
						}
					}
					
					$new_redirect = $wpdb->query($wpdb->prepare(
						"	
						INSERT INTO wp_tdc_redirects
						(source, target, HTTP_Code, description, sort_order)
						VALUES (%s, %s, %d, %s, %d)
						",
						$tdc_source,
						$tdc_target,
						$tdc_http_code,
						$tdc_redirect_description,
						$new_sort
					));
				}
				
				?>
				<div id="message" class="updated">
					<p><strong><?php _e('Settings saved.') ?></strong></p>
				</div>
				<?php 
			}
		}

		if(isset($_GET['redirectID'])) {
			$redID = $_GET['redirectID'];
			
			$redirect_options = $wpdb->get_row( $wpdb->prepare(
				"SELECT * FROM wp_tdc_redirects where redirect_id = %d",
				$redID
			), ARRAY_A
		);	
		}
		
		if(isset($_GET['deleteID'])) {
			$redirect_removed = $wpdb->query( $wpdb->prepare(
				"DELETE from wp_tdc_redirects where redirect_id = ". $_GET['deleteID'] .""
			));
			
			if($redirect_removed){
				echo "<div id='message' class='updated'><p><strong>The redirect rule for " . $_GET['deleteSource'] . " has been deleted.</strong></p></div>";
			}
			else {
				echo "<div class='wrap'>The redirect rule for " . $_GET['deleteSource'] . " has not been deleted or does not exist.</div>";
			}
		}
		
		?>		
		<div class="wrap">
			<h2>Add New Redirect</h2>
			
			<form method="POST" action="admin.php?page=tdc-redirect-add<?php if(isset($_GET['redirectID'])) { echo "&redirectID=" . $_GET['redirectID']; }?>">
				<ul>
					<li><label for="redirect_source">Source: </label>
						<input type="textbox" id="redirect_source" name="redirect_source" value="<?php if(isset($redirect_options['source'])) { echo $redirect_options['source'];}?>" /></li>    
						
						<li><label for="redirect_target">Target: </label>
							<input type="textbox" id="redirect_target" name="redirect_target" value="<?php if(isset($redirect_options['target'])) { echo $redirect_options['target'];}?>" /></li>
							
							<li><label for="http_code">HTTP Code: </label>
								<input type="textbox" id="http_code" name="http_code" value="<?php if(isset($redirect_options['HTTP_Code'])) { echo $redirect_options['HTTP_Code'];}?>" /></li>
								
								<li><label for="redirect_description">Redirect Description: </label>
									<input type="textbox" id="redirect_description" name="redirect_description" value="<?php if(isset($redirect_options['description'])) { echo $redirect_options['description'];}?>" /></li>
								</ul>
								<?php submit_button(); ?>
							</form>
						</div>
						<?php
					}
					
					function tdc_redirect_options() {
						global $wpdb;
						
						$redirect_rules = $wpdb->get_results(
							"
							SELECT redirect_id, source, target, HTTP_Code, description
							FROM wp_tdc_redirects
							ORDER BY sort_order
							", ARRAY_A
						);
						
						?>
						<div class="wrap">
							<div id="icon-options-general" class="icon32"></div>
							<h2>TDC Redirect Rules</h2>
							
							<p>
								This plugin runs through every redirect rule listed on this page until it either comes back with a match, or doesn't match anything. This means the rules listed on this page need to be in order from most specific(top of list) to least specific(bottom of list) match.
							</p>
							
							<p>
								This page can be reordered by draging and droping each redirect rule. Wordpress then processes every drag and drop on the fly through AJAX and saves the ordering to the database after the mouse drops.
							</p>
							
							<table class="wp-list-table widefat fixed posts redirect-list">
								<thead>
									<tr>
										<th>Source</th>
										<th>Target</th>       
										<th>HTTP Code</th>
										<th>Description</th>
										<th>Action</th>
									</tr>
								</thead>
								<tfoot>
									<tr>
										<th>Source</th>
										<th>Target</th>
										<th>HTTP Code</th>
										<th>Description</th>
										<th>Action</th>
									</tr>
								</tfoot>
								<tbody>
									<?php 
									foreach($redirect_rules as $rule) { ?>
										<tr id = "redirect_item_<?php echo $rule['redirect_id']; ?>" class="redirect_rule">
											<td><?php echo $rule['source']; ?></td>
											<td><?php echo $rule['target']; ?></td>
											<td><?php echo $rule['HTTP_Code']; ?></td>
											<td><?php echo $rule['description']; ?></td>
											<td><a href="admin.php?page=tdc-redirect-add&redirectID=<?php echo $rule['redirect_id'] ?>">Edit</a> / <a href="admin.php?page=tdc-redirect-add&deleteID=<?php echo $rule['redirect_id'] ?>&deleteSource=<?php echo $rule['source'] ?>">Delete</a></td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>

						<?php
					}
					
					function process_redirect() {
						global $wpdb;

						$main_redirects = $wpdb->get_results(
							"
							SELECT source, target, HTTP_Code
							FROM wp_tdc_redirects
							ORDER BY sort_order
							", ARRAY_A
						);
						

						$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
						
						foreach($main_redirects as $tdc_rule) {
							$matches=Array();
							
							$source = $tdc_rule['source'];
							$target = $tdc_rule['target'];
							$status = $tdc_rule['HTTP_Code'];
							
							if(!empty($source)){
								preg_match($source, $url, $matches);
							}
							
							if(isset($matches[1])) {
				//complex regex with mult capture groups
								$c = 0;
								foreach($matches as $match) {
									if($c == 0) {
										$c++;
									}
									else {
										$string = '/\$' . $c . '/';
										$target = preg_replace($string, $match, $target);
									}
								}
								
								wp_redirect($target, $status);
								exit();
								
							}
							elseif(isset($matches[0])) {
				//straight preg replace
								wp_redirect($target, $status);
								exit();
							}
						}
					}
				} 
				?>