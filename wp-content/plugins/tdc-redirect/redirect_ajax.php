<?php

function save_redirect_order() {
	
	global $wpdb;
			
	$new_order = $_POST['redirect_item'];
	if(is_array($new_order)) {
		$new_list = array();
		
		foreach($new_order as $key => $value) {
			$wpdb->query($wpdb->prepare(
				"	
					UPDATE wp_tdc_redirects
					SET sort_order = %d
					WHERE redirect_id = %d
				",
				$key,
				$value
			));
		}
	}
}
add_action('wp_ajax_update_redirect_order', 'save_redirect_order');
?>