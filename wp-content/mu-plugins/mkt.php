<?php

 /*
 * Plugin Name:mkt
 * Description: adds tracking cookies
 * Author: Kris Linnell
 * Version: 1.0.0
*/

add_action('wp_enqueue_scripts', 'enqueue_marketing_bs');
add_action('login_enqueue_scripts', 'enqueue_marketing_bs');

function enqueue_marketing_bs() {
    wp_enqueue_script( 'mkingtbs_head','/wp-content/mu-plugins/mkt/mkt_header.js', array(), false, false );
    wp_enqueue_script( 'mkingtbs_foot','/wp-content/mu-plugins/mkt/mkt_footer.js', array(), false, true );
}
?>