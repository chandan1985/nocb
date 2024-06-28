<?php          
	include('build-xml.php');
    
	$path = $_SERVER['REDIRECT_URL'];
	
	$path = preg_replace('/^\//','',$path);
 	$path = preg_replace('/\/$/','',$path);
	
	$expath = explode('/',$path);
	
	$cat_slug = array_pop($expath);
	
	$cat = get_category_by_slug(''.$cat_slug.'');	
	
	$type = 'category';

	$key_name = 'mobile_category_' . $cat->name;
	$feed = wp_cache_get($key_name, 'mobile');
	if(!$feed){
		$feed = build_feed($type, $cat->cat_ID);
		wp_cache_set($key_name, $feed->asXML(), 'mobile', 720);
	}
	$this->output_feed($type,$feed);

	/*	set 404 header and crash
		header("Status: 404 Not Found");
		echo 'No Category Found Here';
		exit(); 
	*/
	
?>