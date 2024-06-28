<?php
 /*
 * Plugin Name: Googleplussharefix
 * Description: Adds a single metatag in compliance with Google Structured Data Crawling
 * Author: Max Schlatter
 * Version: 1.0.0
*/

add_action( 'wp_head', 'googleplussharefix', 10, 0 );

function googleplussharefix(){
echo('<!-- Fix google Plus Sharing MU plugin tag -->
<meta itemscope itemtype="http://schema.org/WebPage" id="mainContentOfPage"/>');
}

?>