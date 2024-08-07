<?php if ( show_webapp_notice() ) { ?>
	<div id="web-app-overlay">
		<img src="<?php wptouch_bloginfo( 'template_directory' ); ?>/images/bubble-arrow.png" alt="bubble-arrow" id="bubble-arrow" />
		<a href="#" id="close-wa-overlay">X</a>
		<img src="<?php  echo wptouch_get_site_menu_icon( WPTOUCH_ICON_BOOKMARK ); ?>" alt="bookmark-icon" />
		<h2><?php wptouch_bloginfo( 'site_title' ); ?></h2>
		<h3><?php _e( "is now web-app enabled!", "wptouch-pro" ); ?></h3>
		<p><?php echo sprintf( __( "Save %s as a web-app on your Home Screen.", "wptouch-pro" ), wptouch_get_bloginfo( 'site_title' ) ); ?></p>
		<p><?php _e( "Tap the button above, then Add to Home Screen.", "wptouch-pro" ); ?></p>
	</div>
<?php } ?>