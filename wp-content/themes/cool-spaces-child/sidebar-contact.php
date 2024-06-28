<?php
/**
 * The sidebar containing the main widget area in contact page
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Mag_Lite
 */

if ( ! is_active_sidebar( 'contact-page-sidebar' ) ) {
	return;
}
?>


	<div id="secondary" class="custom-col-5"><!-- secondary starting from here -->

		<?php dynamic_sidebar( 'contact-page-sidebar' ); ?>
		
	</div><!-- #secondary -->

