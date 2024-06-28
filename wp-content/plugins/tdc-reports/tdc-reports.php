<?php 

/*
* Plugin Name: TDC Reports
* Description: jobtrac search and solr functions and custom post types and filters
* Author: Jerry Milo Johnson and Dave Buchanan
* Version: 0.9
*/

/*
@todo: control startdate and enddate
@todo: list renewals in money totals
@todo: unsubs
@todo: graph adds/renews/unsubs over time. sparkline graphs 
@todo: category/tags from pages
@todo: shorten urls by removing domain name/go with relative urls
@todo: link to bill's charts on google.
@todo: no page/activation if no paywall setup
@todo: handle multiple pubcodes for a given site
@todo: widget on post/page edit, showing subs on that page over time/totals
@todo: top performing pages. (pages/posts with most subs)
@todo: top performing authors (pages/posts with most subs)
*/

// Prevent direct file call
if (!defined( 'ABSPATH' ))
	die( 'Direct access not allowed.' );

$tdc_reports = new tdc_reports();

class tdc_reports {
	private $base_api_url = 'http://subscribe.thedailyrecord.com/_cp2admin/_service/accts.cfm';

	function __construct() { 
		add_action('admin_menu', array( &$this, 'tools_menu' ) );
	}

	function tools_menu() {
		add_management_page('TDC Reports', 'TDC Reports', 'manage_options', __FILE__, array( &$this, 'settings_page' ) );
	}

	function settings_page() {
		$type = 'xml';
		?>
		<style>
			div.circ-report .maintotal {background-color: lightgrey;border-top: #6bbcff solid 4px;border-bottom: #6bbcff solid 4px;padding: 5px 30px;text-align: center;margin: auto;width: 80%;line-height: 1.4;}
			div.circ-report .maintotal p.mt-count {font-size: 60px;margin: 0;}
			div.circ-report .maintotal p.mt-title {font-size: 24px;margin: 0;}
			div.circ-report .count-box {float: left;width: 30%;padding: 0 40px;}
			div.circ-report .graph-box {float: left;text-align: center;width: 60%;}
			div.circ-report .count-table td {text-align:right;}
			div.circ-report .count-table td.label {text-align:left;}
			div.circ-report .count-table td.label.section {font-weight: bold;border-bottom: 1px solid grey;}
			div.circ-report .count-table tr.total td {padding-bottom:10px;}
			div.circ-report .count-table tr.total td {border-top: 1px solid black;}
			div.circ-report .count-table tr.total td.label {border:none;}
			div.circ-report .ns-table {clear:both;padding-top: 20px;}
			div.circ-report .ns-table th {text-align: left;background-color: lightgrey;border-top: #6bbcff solid 4px;border-bottom: #6bbcff solid 4px;}
			div.circ-report .ns-table .ns-my {font-style:italic;font-size:10px;}
			div.circ-report .ns-table td.nowrap {white-space: nowrap;}
			div.circ-report .ns-table td.url {white-space: nowrap;overflow: hidden;text-overflow: ellipsis;max-width: 550px;}
			div.circ-report .count-box table.count-table {padding-left: 40px;margin: 10px;background: url(<?php echo plugin_dir_url( __FILE__ );?>images/cartblack.png) no-repeat;}
		</style>

		<div class="wrap">
			<h2><?php echo __('TDC Reports'); ?></h2>
			<?php
			$gpc = dmcss_wp::get_publication_code();
			$pubcode = array_shift( $gpc );
			if ( $pubcode ) {
				$url = $this->base_api_url . '?pubCode=' . $pubcode;
			} else {
				$url = $this->base_api_url;
			}

	//$result = wp_remote_get( 'http://courthouseprofile.com/circ-report-data.xml' );
			$result = wp_remote_get( $url , array( 'timeout' => 20, 'httpversion' => '1.1' ));

			if ( is_wp_error( $result ) ) {
		// try it again, just in case it was a blip
				$result = wp_remote_get( $url , array( 'timeout' => 60, 'httpversion' => '1.1' ));
			}
			$error_message = '';
			if ( is_wp_error( $result ) ) {
		// none of the retries worked either
				$error_message = $result->get_error_message();
				echo "Failed to connect for data: $error_message";
				exit();
			}

			if ( $type == 'json') {
				$remote_data = json_decode( $result['body'], true );
			// bail if json error
				if ( $remote_data === false ) { $value = $error_message;$remote_data->{$var} = array();}
			} else {
				$body = wp_remote_retrieve_body($result);
				libxml_use_internal_errors(true);
				$remote_data  = simplexml_load_string($body);
			// bail if xml error
				if ( $remote_data === false ) { 
					$value = $error_message;
					if(!empty($var)){
						$remote_data->{$var} = array();
					}
				}
			}

			$var = 'weborders';
			$loop_var = isset($remote_data->{$var}) ? $remote_data->{$var} : '';

			$total_money = 0;
			if(!empty($loop_var) && is_array($loop_var)){
				foreach( $loop_var as $oo ) {
					foreach( $oo as $row ) {
						$total_money = $total_money + $row->ordertotal;
					}
				}
			}
			

			echo '<hr>';
			echo '<div class="circ-report">';
			echo '<div class="count-box">';
			echo '<div class="maintotal"><p class="mt-count">' . (isset($remote_data->counts->weborders) ? $remote_data->counts->weborders : '') . '</p><p class="mt-title">New subscriptions</p></div>';
			echo '<table class="count-table">';
			echo '<tr><td class="section label" colspan="2">Total Carts</td></tr>';
			echo '<tr><td class="label">Carts</td><td>' . (isset($remote_data->counts->carts) ? $remote_data->counts->carts : '') . '</td></tr>';
			echo '<tr><td class="label">Carts Abandoned</td><td> - ' . (isset($remote_data->counts->cartsabandons) ? $remote_data->counts->cartsabandons : '') . '</td></tr>';
			echo '<tr class="total"><td class="label">Carts Completed</td><td>' . (isset($remote_data->counts->cartscompleted) ? $remote_data->counts->cartscompleted : '') . '</td></tr>';
			echo '<tr><td class="label section" colspan="2">Carts Completed with Referral</td></tr>';
			echo '<tr><td class="label">Carts Completed</td><td>' . (isset($remote_data->counts->cartscompletedrefer) ? $remote_data->counts->cartscompletedrefer : '') . '</td></tr>';
			echo '<tr><td class="label">Registers</td><td> - ' . (isset($remote_data->counts->registers) ? $remote_data->counts->registers : '') . '</td></tr>';
			echo '<tr class="total"><td class="label">New Subscriptions</td><td>' . (isset($remote_data->counts->weborders) ? $remote_data->counts->weborders : '') . '</td></tr>';
			echo '<tr><td class="label section" colspan="2">Bottom Line</td></tr>';
			echo '<tr><td class="label">Money</td><td>$' .  number_format((float) $total_money, 2 ) . '</td></tr>';
			echo '</table>';
			echo '</div>';
			echo '<div class="graph-box">';
			echo '<p><img src="' . plugin_dir_url( __FILE__ ) . 'images/graph-fake.png"></p>';
			echo '</div>';

			$startdate = isset($remote_data->query->startdate) ? $remote_data->query->startdate : '';
			$enddate = isset($remote_data->query->enddate) ? $remote_data->query->enddate : '';
			echo '<hr>';
			echo '<div class="ns-table">';
			echo '<h3>New Subscriptions referred from identifiable pages</h3>';
			echo '<h4><i>' . date("Y/m/d",strtotime($startdate)) . ' - ' . date("Y/m/d",strtotime($enddate)) . '</i></h4>';


			$cnt = 1;
			echo '<table>';
			echo '<tr>';
			echo '<th>Day</th>';
			echo '<th>Time</th>';
			echo '<th>Source</th>';
			echo '<th>Referer</th>';
			echo '<th>Total</th>';
			echo '<th>Promocode</th>';
			echo '<th>SubrateID</th>';
			echo '<th>City</th>';
			echo '</tr>';
			$current_month = '';
			if(!empty($loop_var) && is_array($loop_var)) {
				foreach( $loop_var as $oo ) {
					foreach( $oo as $row ) {
						$dc = strtotime($row->datecompleted);
						if ($current_month != date("F, Y", $dc)) {
							$current_month = date("F, Y", $dc);
							echo '<tr class="ns-my"><td colspan="7">' . $current_month . '</td></tr>';
						}
						echo '<tr>';
						echo '<td>' . date("d", $dc) . '</td>';
						echo '<td>' . date("H:i", $dc) . '</td>';
						echo '<td class="nowrap">' . $row->source . '</td>';
						echo '<td class="url"><a href="' . $row->referer . '" title="' . $row->referer . '">' . $row->referer . '</a></td>';
						echo '<td style="text-align:right;">$' . number_format((float) $row->ordertotal, 2 ) . '</td>';
						echo '<td>' . $row->promocode . '</td>';
						echo '<td>' . $row->subrateid . '</td>';
						echo '<td class="nowrap">' . $row->city . ', ' . $row->state . '</td>';
						echo '</tr>';
			//echo '<hr><br><br>';
					}
					$cnt++;
				}
			}
			echo '</table>';
			echo '</div>';
			echo '</div>';


			?>
		</div>
		<?php
	}

}
?>