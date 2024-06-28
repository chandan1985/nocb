<?php

/*
Plugin Name: TDC CAP Login 
Plugin URI: http://www.thedolancompany.com
Description: Controller for bypassing the paywall based on client IP
Version: 0.5 
Author: Kris 
*/

// Disallow direct access
if( !defined( 'ABSPATH' ) ) {
    die( 'Direct access not allowed.' );
}

$tdc_cap_controller = new tdc_cap_login;


class tdc_cap_login {

	private $permitted_ips;

	public function __construct() {	
		
		// investigate our connection

		// determine elegibility for paywall bypass

		// set hooks to disable paywall or do nothing

		//$this->setup_test_data();

		$raw_permitted_ips = get_option('tdc_cap_login');
	    $this->permitted_ips = $this->get_ips($raw_permitted_ips);
		
		if (!is_admin()) {

			$client_ip = $this->interrogate();

			$this->gatekeeper($client_ip);

		}else {	
		
			add_action('admin_menu', array(&$this,'create_plugin_options_page'));
		
		}

	}

	function create_plugin_options_page() {

		add_action('admin_init', array(&$this,'register_settings'));
		$page = add_options_page('TDC Cap Login', 'TDC Cap Login', 'administrator', 'tdc-cap-login', array(&$this,'build_options_page'));
	}
	
	function register_settings() {
		
		register_setting('tdc_cap_login_data','tdc_cap_login');
		add_settings_section( 'section-one','CAP Account Login by IP',array(&$this,'section_one_callback'), 'tdc-cap-login' );
		add_settings_field('tdc_cap_login', 'Cap IPs', array(&$this,'field_one_callback'),'tdc-cap-login','section-one');
	
	}
	
	function section_one_callback(){
	}
	
	function field_one_callback(){
		$textfield = get_option('tdc_cap_login');

		echo '<textarea form="submitForm" style="width:500px; height:500px; text-align:top;" name="tdc_cap_login">'.$textfield.'</textarea>';
		?>
		<table>
			<tr><td></td><td><span id="ips_instructions"><small><em>Input ips as in a standard config file. One per line. Use # for comments. Whitespace is ignored.</em></small></span></td></tr>
		</table>
		<?php
	}
	
	function build_options_page() {

	?>
		<form id="submitForm" action="options.php" method="POST">
			<?php settings_fields('tdc_cap_login_data'); ?>
			<?php do_settings_sections('tdc-cap-login'); ?>
			<?php submit_button(); ?>
		</form> 
	<?php
	}
	function get_ips( $raw ) {
		
		
		$lines = explode("\r\n", $raw);
		$ips = array();
		//comments = array();
		
		for( $i=0; $i < count($lines); $i++ ){
			
			if (strpos($lines[$i], '#')){
				//$comments[$i] = trim($lines[$i]);
			}
			elseif( ip2long(trim($lines[$i])) !== false ){
				
				$ips[$i] = trim($lines[$i]);
				
			}elseif(count(explode( "-",$lines[$i]  )) == 2  ){
				
				$range = explode( "-" , $lines[$i]  );
				$ip1 = trim($range[0]); 
				$ip2 = trim($range[1]);
				
				if( ip2long($ip1) !== false && ip2long($ip2) !== false ){
										$ips[$i] = $ip1.'-'.$ip2;
				}
			}else{
			}
			
		}
		
		return $ips ;
	}
	
	private function interrogate() {

		if ( function_exists( 'apache_request_headers' ) ) {
			$headers = apache_request_headers();
		} 
		else {
			$headers = $_SERVER;
		}

		if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
			$the_ip = $headers['X-Forwarded-For'];
		} 
		elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
			$the_ip = $headers['HTTP_X_FORWARDED_FOR'];
		} 
		else {
			$the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
		}
		return $the_ip;
	}

		private function gatekeeper($client) {
	
		$sucessful_match = false;

		if (!empty($this->permitted_ips)) {
			foreach ($this->permitted_ips as $test_case) {

				if (preg_match('/-/', $test_case)) {
					// ip range
					$client_value = ip2long($client);

					$range = explode('-', $test_case);
					$range[0] = ip2long($range[0]);
					$range[1] = ip2long($range[1]);
					
					sort($range);


					if ($client_value > $range[0] && $client_value < $range[1]) {
						$sucessful_match = true;
					}
				}
				elseif ($client == $test_case) {
					$sucessful_match = true;
				}
			}

			if ($sucessful_match) {

			if(in_array('asentech-ga-user-tracking/ga-user-tracking.php', apply_filters('active_plugins', get_option('active_plugins')))) { 
				global $wpdb;
				$my_fields = get_option('btm_ipcompany_details');
				$name = $my_fields['btm_ipcompany_name'];
				$ip   = $my_fields['btm_ipcompany_ip'];

				$combined = array_combine($name,$ip);
				$final = array_filter($combined, function($value) { return !is_null($value) && $value !== ''; });
		
				$client_value = ip2long($client);

				$company_exist = false;

				foreach ( $final as $key => $value )
				  {
					 $company = $key;
				   	 $get_ip_list = $this->get_ips($value);
				   	 sort($get_ip_list);
				   	 
				   	 foreach ($get_ip_list as $single_ip) {  
				   	 if (! preg_match('/-/', $single_ip)) {
				   	 $compare = ip2long($single_ip);
				   	 }
				   	 
				   	 if (preg_match('/-/', $single_ip)) { // Check if Ip is in range format
				   	 		
				   	 	$range = explode('-', $single_ip);
				   	 	$range[0] = ip2long($range[0]);
						$range[1] = ip2long($range[1]);
						sort($range);

						echo "<!-- clint".$client_value." -->";
						echo "<!-- range0".$range[0]." -->";
						echo "<!-- range1".$range[1]." -->";
							if ($client_value >= $range[0] && $client_value <= $range[1]) {
								$company_exist = true;
								$company_name = $company;
								break;
							}
				   	 	}
				   	   
				   	   elseif ( $compare == $client_value) {
							$company_exist = true;
							$company_name = $company;
							break;
				   	 	}
				   	   else {  }
				   	 } // foreach loop end here
				  // } //size check if end here
				   
				   
				   // else  { 
				   // 	foreach ($get_ip_list as $single_ip) {  //for loop for only ip index (company name is common so excluding it)
				   // 	 $compare = ip2long($single_ip);
				   // 	 	if (preg_match('/-/', $single_ip)) {
				   // 	 		$range = explode('-', $single_ip);
							// $range[0] = ip2long($range[0]);
							// $range[1] = ip2long($range[1]);
							// sort($range);
							// if ($client_value > $range[0] && $client_value < $range[1]) {
							// 	$company_exist = true;
							// 	$company_name = $company;
							// }
				   // 	 	}
				   // 	 	elseif ( $compare == $client_value) {
							// $company_exist = true;
							// $company_name = $company;
				   // 	 	}
				   // 	 	else { $company_exist = false; }
				   // 	 } //foreach loop end here
				   // } // else condition end here 
				} //main foireach loop end here	
				add_filter('paywall-external-bypass', array($this, 'enable_bypass'));
				@setcookie( 'analytics_login', 'IP', time() + 86400, "/", $_SERVER['HTTP_HOST']);
				if($company_exist)
				@setcookie( 'analytics_login_company',$company_name, time() + 86400, "/", $_SERVER['HTTP_HOST']);
			}

			else {
				add_filter('paywall-external-bypass', array($this, 'enable_bypass'));
				setcookie( 'analytics_login', 'IP', time() + 86400, "/", $_SERVER['HTTP_HOST']);
			}
		  }
		}
	}

	public function enable_bypass($stuff_we_ignore) {
		if ( function_exists('batcache_cancel') ) {
			batcache_cancel();
		}
		return true;
	}
	//This is formatting specs for the data which gets passed to the gatekeeper function
	/*private function setup_test_data() {


		$test_array = array();


		$test_array[] = '10.3.6.25';
		$test_array[] = '10.3.6.1-10.3.6.254';
		$test_array[] = '10.3.5.25';		
		$test_array[] = '10.3.7.25';		
		$test_array[] = '10.3.4.1-10.3.7.254';


		add_option('tdc-cap-login', $test_array);

	}*/

}

