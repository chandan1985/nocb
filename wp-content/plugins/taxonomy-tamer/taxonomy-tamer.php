<?php
/*
Plugin Name: Taxonomy Tamer
Plugin URI: http://digitalwilderness.com/plugins/taxonomy-tamer
Description: Provides a report and functions to manage taxonomies with few associated posts
Version: 1.0
Author: Jerry Milo Johnson
Author URI: http://digitalwilderness.com/
License: GPL2
*/

/*  Copyright 2014  Jerry Milo Johnson  (email : jerry.johnson@digitalwilderness.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
@todo: add readme.txt
@todo: uninstall cleanup
@todo: settings page to limit count and rows
@todo: settings page to remove certain taxonomies from list
@todo: move styles into stylesheet.
@todo: change radio to check boxes
@todo: add checkbox for "are you sure" doublecheck
*/

// Prevent direct file call
if (!defined( 'ABSPATH' ))
	die( 'Direct access not allowed.' );

$dw_taxonomy_tamer = new dw_taxonomy_tamer;

class dw_taxonomy_tamer {

	private $max_count=10;
	private $max_rows=5000;

	function __construct() {
		add_action('admin_menu', array( &$this, 'tools_menu' ) );
	}

	function tools_menu() {
		add_management_page('Taxonomy Tamer', 'Taxonomy Tamer', 'manage_options', 'taxonomy-tamer', array( &$this, 'settings_page' ) );
	}

	function settings_page() {
		global $wpdb;
		if ( isset($_GET['settings-updated']) ) {
			echo '<div id="message" class="updated fade"><p><strong>'. _e('Taxonomy Tamer Settings saved.') .'</strong></p></div>';
		}
		
		// handle the request to clean out taxonomies
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'taxonomy_tamer_clean' ) {
			echo '<!-- start clean tick ';
			print date('r');
            echo ' -->';

			$clean_target = explode(":", $_POST['clean_target']);
			$tax_type = $clean_target[0];
			$tag_count = $clean_target[1];

			// check data. dont do > 10
			if ( ($tag_count == 0)  || ($tag_count > $this->max_count) ) {
				// dont do it.
				echo '<div id="message" class="updated fade"><p><strong>'. _e('Bad tag count value.') .'</strong></p></div>';
			} else {
				// check total posts, dont do more than 5000
				// get the tagcounts for just this tax_type
				$query = $wpdb->prepare("
						SELECT TagCount, COUNT(*) AS Total, taxonomy
                                        FROM ( 
                                                select count(*) as TagCount, tt.term_taxonomy_id, taxonomy from wp_1_term_relationships tr
                                                JOIN wp_1_term_taxonomy tt on tt.term_taxonomy_id = tr.term_taxonomy_id
                                                where taxonomy = '%s'
                                                group by tt.term_taxonomy_id, taxonomy
                                                having count(*) = %d
                                                ) AS t
                                        GROUP BY TagCount,taxonomy 
                                        order by taxonomy,tagcount asc;
						",$tax_type,$tag_count);

				$results = $wpdb->get_results($query);
	
				// if it found any
				if ($results[0]->Total > 0) {
					// only pull a safe amount (the total rows allowed / the tag_count (so 1000/10)
					$max_to_pull = $this->max_rows / $tag_count;
					// get the list of ids to delete (but limit it to a safe amount)
					$query = $wpdb->prepare("
							select count(*) as TagCount, tt.term_taxonomy_id, taxonomy from wp_1_term_relationships tr
                                                JOIN wp_1_term_taxonomy tt on tt.term_taxonomy_id = tr.term_taxonomy_id
                                                where taxonomy = '%s'
                                                group by tt.term_taxonomy_id, taxonomy
                                                having count(*) = %d
 												limit %d;
							",$tax_type,$tag_count,$max_to_pull);

					$results = $wpdb->get_results($query);
                    $actual_clean_count = count($results);
					// put the list of ids into a text string
					$id_list = implode( ',',wp_list_pluck( $results, 'term_taxonomy_id' ));

					// clean out relationships for that list of ids
					$query = "delete from wp_1_term_relationships where term_taxonomy_id in ($id_list);";
					$results = $wpdb->get_results($query);

					// clean out taxonomy for that list of ids
					$query = "delete from wp_1_term_taxonomy where term_taxonomy_id in ($id_list);";
					$results = $wpdb->get_results($query);

					// delete any empty terms
					// $query = "delete from wp_1_term_relationships where term_taxonomy_id in ($id_list);";
					// $results = $wpdb->get_results($query);

					echo '<div id="message" class="updated fade"><p><strong>'. $actual_clean_count . ' '.$tax_type.' taxonomies cleaned out.</strong></p></div>';
				}
			}
			echo '<!-- end clean tick ';
			print date('r');
            echo ' -->';
		}

		// main settings display
		$query = "
						SELECT TagCount, COUNT(*) AS Total, taxonomy
                                        FROM ( 
                                                select count(*) as TagCount, tt.term_taxonomy_id, taxonomy from wp_1_term_relationships tr
                                                JOIN wp_1_term_taxonomy tt on tt.term_taxonomy_id = tr.term_taxonomy_id
                                                group by tt.term_taxonomy_id, taxonomy
                                                having count(*) <= $this->max_count 
                                                ) AS t
                                        GROUP BY TagCount,taxonomy 
                                        order by taxonomy,tagcount asc;
				";

		$results = $wpdb->get_results($query);

		echo '<form action="" id="taxonomy-tamer-clean" method="post">';
		echo '<style>';
		echo 'th {background-color: black;color: white;padding: 4px;}';
		echo 'tr.taxhead td {padding: 4px;}';
		echo 'tr.taxhead {background-color: lightgrey;text-transform: uppercase;font-weight: bold;}';
		echo '</style>';

		echo '<div class="wrap">';
		echo '  <h2>';
		echo __('Taxonomy Tamer Settings Page');
		echo '  </h2>';
		echo '<table>';
		echo '<tr><th>Action</th><th>Number of Posts</th><th>Number of taxonomies</th></tr>';
		$cur_taxtype = '';

		foreach ($results as $row) {
			if ($row->taxonomy != $cur_taxtype ) {
				echo '<tr class="taxhead"><td colspan="3">'.$row->taxonomy.'</td></tr>';
				$cur_taxtype = $row->taxonomy;
			}
            echo '<tr><td><input type="radio" name="clean_target" value="'.$row->taxonomy.':'.$row->TagCount.'"></td><td>'.$row->TagCount.'</td><td>'.$row->Total.'</td></tr>';
        }
        echo '</table>';
		echo '<p style="color:red;font-weight:bold;">WARNING: This delete can NOT be undone. Do NOT press this button if you have any doubts.</p>';
        wp_nonce_field('taxonomy_tamer_clean', '_wpnonce');
        echo '	<input type="hidden" name="action" value="taxonomy_tamer_clean"/>';
        echo '	<input class="button-bottom button-primary" type="submit" name="clean-taxonomy" id="clean-taxonomy" value="Clean out Taxonomy" />';
        echo '</form>';
        echo '</div>';
	}

// end class
}
?>