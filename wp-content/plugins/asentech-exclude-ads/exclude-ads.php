<?php
/*
  Plugin Name: Asentech - Welcome Exclude Ads
  Plugin URI: http://asentechllc.com/
  Description: Allows you to show sidebar Ads on any WordPress site
  Version: 1.0.0
  Author: Asentech
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Exclude_ad {

    public function __construct($file) {
        $this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
        require_once( 'classes/exclude-ads-admin.php' );
    }

}

$start = new Exclude_ad (__FILE__);