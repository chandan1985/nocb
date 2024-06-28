<?php
 /*
 * Plugin Name: TDC Dashboard Widgets
 * Description: An assortment of dashboard widgets for Dolan sites
 * Author: Jerry Milo Johnson, The Dolan Company
 * Version: 1.0
 * Modifications:
    
@TODO add warning message to top of page
@TODO db latest time
@TODO gsa most searched, content pile stats, etc
@TODO bluesky stats
@TODO circ reg/add/del
@TODO author counts
 
*/

// Prevent direct file call
if ( !defined( 'ABSPATH' ) )
	die( 'Direct access not allowed.' );

class tdc_dashboard_widgets {

	function __construct() {
		// Hook into wp_dashboard_setup and add our widget
		add_action( 'wp_dashboard_setup', array( &$this, 'tdc_duplicate_wpoptions_widget' ) );
		add_action( 'wp_network_dashboard_setup', array( &$this, 'tdc_duplicate_wpoptions_widget' ) );
	}
	
	function tdc_duplicate_wpoptions_output(){
		global $wpdb, $table_prefix, $table_base_prefix, $blog_id;

		if ( !$table_base_prefix ) {
			$table_base_prefix = preg_replace( "/$blog_id_/si", '', $table_prefix );
		}
		$sql = '
			SELECT DISTINCT blog_id
			FROM ' . $table_base_prefix . 'blogs
			WHERE deleted = 0
			ORDER BY blog_id;
		';
		$blog_list = $wpdb->get_results( $sql );
		$j_this_site = '';
		// step thru each blog, checking for dup 
		foreach ( $blog_list as $blog_row ) {
			$blog_options = $wpdb->get_results('
				SELECT option_name, count(*) AS cnt
				FROM ' . $table_base_prefix . $blog_row->blog_id . '_options
				GROUP BY option_name HAVING cnt > 1
				ORDER BY cnt DESC, option_name;'
			);
			$j_this_row='';
			foreach ( $blog_options as $option_row ) {
				$j_this_row.='<dt>'.$option_row->option_name.'</dt><dd>'.$option_row->cnt.'</dd>'."\n";
			}
			if ( '' != $j_this_row ) {
				$j_this_site .= '<h4>' . $blog_row->blog_id . '</h4>' . "\n";
				$j_this_site .= '<dl>' . "\n";
				$j_this_site .= $j_this_row . "\n";
				$j_this_site .= '</dl>' . "\n";
			};
		}

		// only add div and widget if we HAVE errors
		if ('' != $j_this_site) {
			echo '<style>';
			echo '.tdc-duplicate-wpoptions {background-color: #FFEBE8;border-color: #C00;}';
			echo '.tdc-duplicate-wpoptions h4 {padding: 4px 10px;background-color: lightgray;}';
			echo '.tdc-duplicate-wpoptions dt {padding: 0 15px 0 0;width: 250px;text-align: right;float: left;clear: left;}';
			echo '</style>';
			echo '<div class="tdc-duplicate-wpoptions">'."\n";
			echo $j_this_site."\n";
			echo '</div>'."\n";
		} else {
			echo '<p>wp-options table is OK.</p>';	
		}
	}

	// Create the function that adds the widget
	function tdc_duplicate_wpoptions_widget(){
		// Add our widget
		if( is_network_admin() ) {
			// Add directly to the side column at network level
			$screen = get_current_screen();
			add_meta_box( 'tdc-duplicate-wpoptions', 'Duplicate wp-options', array( &$this, 'tdc_duplicate_wpoptions_output'), $screen, 'side', 'high' );
		}
		else {
			wp_add_dashboard_widget( 'tdc-duplicate-wpoptions', 'Duplicate wp-options', array( &$this, 'tdc_duplicate_wpoptions_output') );
		}
	}
}