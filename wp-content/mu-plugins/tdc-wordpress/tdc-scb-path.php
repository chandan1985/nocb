<?php
/*
 * SCB Path setup TDC WordPress
 *
 * Include file must be placed in the root of the mu-plugins directory to enable this
 * plugin.
 */

if ( !defined( 'ABSPATH' ) )
	die( 'Direct access not allowed.' );

class tdc_scb {

	/*
	 * Class constructor
	 * Build class & set up action to change admin bar color
	 */
	function __construct(){
		add_action( 'wpmu_new_blog', array( &$this, 'tdc_scb_path_setup' ), 10, 6 );
	}

	/*
	 * Fix and add SCB path vars on new blog setup
	 */
	function tdc_scb_path_setup( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

		// try to get it from main blog setting.
		$default_site_domain = $domain;
		switch_to_blog(1);
		
		$upload_path = get_option( 'upload_path' );
		$domain_folder = $domain;
		restore_current_blog();

		if ($upload_path) {
			$j = preg_match("\/shared\/(.*)\/wp-content\/.*",$upload_path, $results);
			if ($results[1]) { $domain_folder = $results[1]; }
		}
		
		switch_to_blog($blog_id);

		// save option into the database
		update_option( 'upload_path', '/shared/'.$domain_folder.'/wp-content/blogs.dir/'.$blog_id.'/files');
		update_option( 'upload_url_path', 'http://'.$domain.$path.'files');

		restore_current_blog();

	}
}
?>