<?php
/*
Plugin Name: DMC Mobile Feeder
Plugin URI: http://thedolancompany.com
Description: Handles Feeding data out to Mobile apps 
Author: Chris Meier 
Version: 1.0
Author URI: http://thedolancompany.com
Modifications: 
*/ 
class dmc_mobile_feed {
	
	const CUSTOMXSD = "custom.xsd";

	function init_feeds() {
		$path = isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : '';
		if ($path == '/mobile_feed' || $path == '/mobile_feed/' || $path == '/mobile_feed_pages' || $path == '/mobile_feed_pages/') {		
			$this->mobile_feed();
		}
		if ($path == '/mobile_auth' || $path == '/mobile_auth/') {
			$this->authorize_mobile();
		}
		if (isset($_GET['feed_type']) && $_GET['feed_type'] == 'mobile') {
			$this->category_feed();
        }
	}

	function authorize_mobile() {

		$pubcodes=array();
		$options = get_option( 'tdc_paywall_data' );
				if( $options && isset( $options['publications'] ) ) {
					foreach( $options['publications'] as $publication ) {
						array_push( $pubcodes,$publication['code'] );
					}
				}
		$url = $options['ws_url'];

		$pubcode = implode(',',$pubcodes);

		if ($_GET['txt1']) {
			$username = $_GET['txt1'];
			$password = $_GET['txt2'];
		}
		else if ($_POST['txt1']) {
			$username = $_POST['txt1'];
			$password = $_POST['txt2'];
		}
		else {
			echo "Login Failed:  No Credentials provided.";
			exit;
		}

		$result = $this->login_via_wsdl($url,$username,$password,$pubcode);

		if ($result[0] == '' and $result[1] ==1) {
			echo '1';
		}
		else {
			echo 'Login Failed: ' . $result[0];
		}

		exit;
	}

	function mobile_feeder_menu() {
        if (function_exists('add_submenu_page')) {
            add_submenu_page('options-general.php', 'Mobile Feed Settings', 'Mobile Feed', 'manage_options', __FILE__,  array( 'dmc_mobile_feed', 'mobile_feed_menu_options' ) );
        }
    }

	public static function mobile_feed_menu_options() {
		include(dirname( __FILE__ ) .'/admin.php');
	}

	function mobile_feed() {
		include('wordpress-2-mobile-feed.php');
		exit();		
	}

     function category_feed() {
		include('wordpress-2-mobile-category-feed.php');
		exit();	
	}
		
	function output_feed($type,$xml) {
        ///if we want json returned instead of xml---USED BY MOBILE APPs
        if(isset($_GET['json']) && $type == 'page') {
			$this->output_json_pages($xml);
        }
        else if(isset($_GET['json']) && $type == 'post') {
           	$this->output_json_posts($xml);
        }
		
        ///output XML
		if(is_object($xml)) {
			header ("content-type: text/xml; charset=utf-8");
			echo html_entity_decode($xml->asXML(), ENT_QUOTES, 'UTF-8');  
		}
		else {
			header ("content-type: text/xml; charset=utf-8");
			echo html_entity_decode($xml, ENT_QUOTES, 'UTF-8');
		}
		exit();
	}
	
	function output_json_pages($xml) {
		$out = '';
		if(is_object($xml)) {
			$xml_new = new SimpleXMLElement(html_entity_decode($xml->asXML(), ENT_QUOTES, 'UTF-8'));
		}
		else {
			$xml_new = simplexml_load_string(html_entity_decode($xml, ENT_QUOTES, 'UTF-8'));
		}
		
		foreach ($xml_new->url as $e) {
		    $namespaces = $e->getNameSpaces(true);
		    $news = $e->children($namespaces['news']);
		    $news = $news->news;
		    $out .= '<div id="main"><h1>'.end($news->title).'</h1>';
		    $out .= '<div class="text-block"><p class="json_content">'.substr($e->content." ", 0, -1).'</p></div></div>';
		}

		//$out = json_encode($out);
		echo $out;
		exit();
	}
	
	function output_json_posts($xml) {
		$output = array();
		if(is_object($xml)) {
			$xml_new = new SimpleXMLElement(html_entity_decode($xml->asXML(), ENT_QUOTES, 'UTF-8'));
		}
		else {
			$xml_new = simplexml_load_string(html_entity_decode($xml, ENT_QUOTES, 'UTF-8'));
		}
		
		$i = 0;
		foreach ($xml_new->url as $e) {
		    $i++;
		    $namespaces = $e->getNameSpaces(true);
		    $news = $e->children($namespaces['news']);
		    $news = $news->news;
		    $dmc = $e->children($namespaces['dmc']);
		    $dmc = $dmc->dmc;

		    $new_story['loc'] = $e->loc;
		    $new_story['author'] = end($dmc->author);
		    $new_story['locked'] = isset($news->access);
		    $new_story['location'] = ucwords("");
		    $new_story['content'] = substr($e->content."", 0, -1);
		    ///regex to strip out the caption so it isnt part of the teaser text
		    $new_story['snippit'] = preg_replace('#\<p class="wp-caption-text"\>(.+?)\<\/p\>#', "", $new_story['content']);
		    $new_story['snippit'] = substr(strip_tags($new_story['snippit']), 0, 200)."...";
		    $new_story['p_url_small'] =  substr(end($news->image)." ", 0, -1);
		    $new_story['p_url_normal'] = end($news->image);
		    $new_story['mobile_thumbnail'] =  substr(end($dmc->mobile_thumbnail)." ", 0, -1);
		    $new_story['mobile_article'] =  substr(end($dmc->mobile_article)." ", 0, -1);
		    $new_story['standard_article'] =  substr(end($dmc->standard_article)." ", 0, -1);
		    $new_story['standard_featured'] =  substr(end($dmc->standard_featured)." ", 0, -1);
		    $new_story['standard_thumbnail'] =  substr(end($dmc->standard_thumbnail)." ", 0, -1);
		    $new_story['p_url_small'] =  substr(end($dmc->image)." ", 0, -1);
		    $new_story['p_url_normal'] = end($dmc->image);
		    $new_story['loc'] = end($new_story['loc']);
		    $new_story['pub-name'] = end($news->publication->name);
		    $new_story['pub-lang'] = end($news->publication->language);
		    $new_story['date'] =  end($news->publication_date);
		    $new_story['title'] = end($news->title);
		    $new_story['keywords'] = explode(",", end($news->keywords));
		    $output['stories'][] = $new_story;
		}

		$output = json_encode($output);
		echo $output;
		exit();
	}

	private function login_via_wsdl($url,$username,$password,$pubcode){
		// Pull in the NuSOAP code
		require_once('nusoap.php');
	
		// Create the client instance
		$client = new nusoap_client($url,true);
		
		// Check for an error
		$err = $client->getError();
	
		if ($err){
			// An error occurred trying to set up a client of the web service; save the error
			$wsError = 'Constructor Error: ' . $err;
		}
		else {
			// Call the SOAP method
			$result = $client->call('wsProcessLoginMobile',array('pubcode' => $pubcode,'loginUserNameEmail' => $username,'loginPassword' => $password));
	
			// Check for a fault
			if ($client->fault){
				// A fault occurs if the web service method was invoked improperly
				$wsError = 'Fault: ' . $result['faultstring'];
			}
			else {
				// Check for errors
				$err = $client->getError();
	
				if ($err){
					// Some error occurred within the web service method?
					$wsError = 'Error: ' . $err;
				} else {
					// We received a successful response, do nothing for now...?
				}
			}
		}
		//send back array
		return $result;
	}	
} // end of class

$mobilefeed = new dmc_mobile_feed();

if(function_exists('add_action')) {  //wordpress page load: set hooks and be done 

	if(is_admin()) {
		add_action('admin_menu', array($mobilefeed, 'mobile_feeder_menu'));
	}
	else {	
		// check for feed request and display
		add_action('init', array($mobilefeed, 'init_feeds'));
	}
}
?>
