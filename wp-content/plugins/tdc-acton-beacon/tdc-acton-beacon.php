<?php
 /*             
 * Plugin Name: Act-on Tracking Beacon
 * Description: Enables tracking of page hits for Act-On 
 * Author: Kris 
 * Version: 1.0
*/              

$beacon = new TDC_ActOn_Beacon;
    

class TDC_ActOn_Beacon {

	private $beacon_code; 
	private $enabled = 0;


	public function __construct() {

		$raw = get_option('acton_beacon_code');
		if ($raw && !empty($raw)) {
			$this->beacon_code = $raw;
			$this->enabled = get_option('acton_beacon_enabled');
		}
		else {
			$this->enabled = false;
		}


		if (is_admin()) {
			//deploy menu item
			add_action('admin_menu', array($this, 'menu_hook'));
		}
		else {
			add_action('wp_head', array($this, 'deploy_beacon'), 99);
		}
	}

	public function deploy_beacon() {
		if ($this->enabled == 1) {
			echo '<!-- Start of Act On Beacon Code -->' . "\n";
			if (current_user_can('activate_plugins')) {
				echo '<!-- ActOn Tracking beacon disabled for admin users -->' . "\n";
			}
			else {
				echo $this->beacon_code . "\n";;
			}
			echo '<!-- End of Act On Beacon Code -->' . "\n";
		}
	}

	public function menu_hook() {
		add_submenu_page('options-general.php', "ActOn Beacon", "ActOn Beacon", "administrator", "tdc-acton-beacon", array($this, "admin_page"));
	}

	public function admin_page() {
		
		if ( isset($_POST['beacon']) ) { 
			$this->beacon_code = stripslashes_deep($_POST['beacon']);
			update_option('acton_beacon_code', $this->beacon_code );
		}

		if ( isset($_POST['enabled']) && isset($_POST['submitted']) ) {
			$this->enabled = $_POST['enabled'];
			update_option('acton_beacon_enabled', 1 ); 
		}
		elseif( !isset($_POST['enabled']) && isset($_POST['submitted']) ){
			$this->enabled = 0;
			update_option('acton_beacon_enabled', 0 );			
		}
		elseif ( isset($this->enabled) ) { 
		
		}
		else{
			$this->enabled = 0;
			update_option('acton_beacon_enabled', 0);
		}

		$this->draw();
	}

	private function draw() {
	?>
		<form method=POST>
		Beacon Code<br> 
		<textarea cols=150 rows=20 name=beacon ><?php echo $this->beacon_code; ?></textarea>
		<br><br>
		<input type=checkbox name=enabled value='1' <?php if ($this->enabled == 1) {echo " checked ";} ?> />  Beacon Enabled
		<input type=hidden name=submitted value='1'/>
		<input type=submit style=margin-left:300px; />
		</form>
	<?php
	}
}
