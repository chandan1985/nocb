<?php if ( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] == 0 ) { ?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php wptouch_bloginfo('html_type'); ?>; charset=<?php wptouch_bloginfo('charset'); ?>" />
		<title><?php wptouch_title(); ?></title>
		<?php wptouch_head(); ?>
	</head>		
	<body class="<?php wptouch_body_classes(); ?>">
	<noscript>
		<div id="noscript">
			<h2><?php _e( "Notice", "wptouch-pro" ); ?></h2>
			<p><?php _e( "JavaScript is currently off.", "wptouch-pro" ); ?></p>
			<p><?php _e( "Turn it on in browser settings to view this mobile website.", "wptouch" ); ?></p>
		</div>
	</noscript>

			<?php include_once('menubar.php'); ?>
			<?php include_once( 'popovers.php' ); ?>
			
			<!-- The Landscape Sidebar ( menu is dynamically attatched here by js ) -->
			<?php if ( wptouch_has_menu() ) { ?>
				<div id="main-menu">
					<?php if ( classic_has_ipad_logo() ) { ?>
						<div id="logo-area">
							<?php classic_ipad_logo_image(); ?>
						</div>
					<?php } ?>
				</div>
			<?php } ?>

			<?php //do_action( 'wptouch_advertising_top' ); ?>

			<?php do_action( 'wptouch_body_top' ); ?>
<?php } ?>

			<!-- Rock N' Scroll! -->
 			<div id="iscroll-wrapper">
				<div id="iscroll-content">
					<div id="content">
							<?php if ( wptouch_has_welcome_message() && !isset( $_COOKIE['wptouch_welcome'] ) && !isset( $_COOKIE['web-app-mode'] ) ) { ?>
								<div id="welcome-message">
									<?php wptouch_the_welcome_message(); ?>
									<a href="<?php wptouch_the_welcome_message_dismiss_url(); ?>" id="close-msg" class="button"><?php _e( "Close Message", "wptouch-pro" ); ?></a>	
								</div>
							<?php } ?>