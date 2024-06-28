<?php

/*
 * Plugin Name: TDC Warning message
 * Plugin URI: http://www.dolanmedia.com
 * Description: Just display a message in the admin
 * Author: Dave Buchanan
 * Version: 0.1
 * Author URI: http://www.dolanmedia.com
 */

class tdc_lab_warning {
	public function __construct() {
		add_action('admin_head', array(&$this,'action_init') );
	}
	public function action_init() {
		echo "<div class='error'><strong>WARNING:</strong> This is a place to try out new plugins and theme setting changes. Be aware that changes might become conflicted with another editors' changes. And if things break here, all that can be done is to refresh from an earlier version. If there's a particular plugin that you'd like on  your site, please put in a ticket, servicedesk@thedolancompany.com.</div>";
	}
}
