<?php
	include('build-xml.php');

	$path = $_SERVER['REDIRECT_URL'];

	$op = get_option("dmc_mobile_feed_options");

	//check url for proper path to display feed
	if($path=='/mobile_feed' || $path=='/mobile_feed/') {		
		$type = 'post';
	}
	else if ($path=='/mobile_feed_pages' || $path=='/mobile_feed_pages/') {
		$type = 'page';
	}

	$key_name = 'mobile_feed_' . $type;
	$feed = wp_cache_get($key_name, 'mobile');
	if(!$feed){
		$feed = build_feed($type);
		wp_cache_set($key_name, $feed->asXML(), 'mobile', 720);
	}
	$this->output_feed($type,$feed);
?>