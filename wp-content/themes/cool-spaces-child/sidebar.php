<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Mag_Lite
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>
<?php /* $sidebar_layout = mag_lite_get_option('layout_options'); 

if ( 'no-sidebar' !== $sidebar_layout ) { ?>
	<div id="secondary" class="custom-col-4"><!-- secondary starting from here -->

		<div class="theiaStickySidebar">

			<?php dynamic_sidebar( 'sidebar-1' ); ?>

		</div>
		
	</div><!-- #secondary -->

<?php } */ ?>
