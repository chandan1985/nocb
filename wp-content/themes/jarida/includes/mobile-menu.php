<?php 
wp_enqueue_style( 'mobilemenucss' );
wp_enqueue_script( 'mobilemenujs');
?>
<div class="mobile-menu-container">
	<?php
	/*get the logo */	
	if( tie_get_option( 'logo' ) ) $logo = tie_get_option( 'logo' );
			else $logo = get_stylesheet_directory_uri().'/images/logo.png';
	/*write the logo html */			
	?>
	<?php /*
	<div id="mobile-menu-logo">
		<a title="<?php bloginfo('name'); ?>" href="<?php echo $mastheadlink; ?>/">
					<img onerror="this.src='<?php echo $svg_fixer; ?>';this.onerror=null;" src="<?php echo $logo ; ?>" alt="<?php bloginfo('name'); ?>" />
				</a>
	</div>
	/* ?>
	<?php
	/*get the mobile and mobile footer menus */
		wp_nav_menu( array('theme_location'=>'mobile', 'depth'=>3, 'container_class'=>'mobile_menu', 'menu_id'=>'mobile_menu') );
	 ?>
	
	<?php
	if ( has_nav_menu( 'mobile-footer' ) ) {
			wp_nav_menu( array('theme_location'=>'mobile-footer', 'depth'=>1, 'container_class'=>'mobile_footer_menu', 'menu_id'=>'mobile_footer_menu') );
	}
	?>
	<div id="mobile-menu-footer-close-container">
		<a href="javascript:;" id="mobile-menu-footer-close" onclick="toggleMobileMenu();"></a>
	</div>
</div> <!-- .mobile-menu-container -->