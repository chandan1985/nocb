<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
	
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<meta name="description" content="<?php bloginfo('description'); ?>" />
	<meta name="keywords" content="<?php bloginfo('name'); ?>" />
	<meta name="author" content="<?php bloginfo('name'); ?>" />
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen, projection" />
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<?php wp_head(); ?>	
	<title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>
	<script type="text/javascript">	var themedir = '<?php bloginfo('stylesheet_directory')?>'; 
									var page = '';
									var maxlistheight='<?php quonfig(1)?>';
									var numepisodeshown='<?php quonfig(2)?>';
									var dropmenu='<?php print quonfig(3)?>';
									var diggbuttons='<?php print quonfig(4)?>';
									var wdtube='<?php print quonfig(6)?>';
									var tagged='';
									var mpl='0';
									<?php if ((file_exists('wp-content/themes/video/mediaplayer.swf'))||(quonfig(7)=='1')): 
										echo 'var mpl=\'1\'';
										echo '</script><script type="text/javascript" src="';
										bloginfo('stylesheet_directory');
										echo '/scripts/swfobject.js">';
									endif;
									if ((file_exists('wp-content/themes/video/scripts/wmvplayer.js'))||(quonfig(8)=='1')):
										echo '</script>';
										echo '<script type="text/javascript">var mpl=\'1\'';
										echo '</script><script type="text/javascript" src="';
										bloginfo('stylesheet_directory'); 
										echo '/scripts/wmvplayer.js"></script><script type="text/javascript" src="';
										bloginfo('stylesheet_directory');
										echo '/scripts/silverlight.js">';
									endif; ?>
	</script>
	<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/scripts/md5.js">
	</script>
	<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/scripts/java.js">
	</script>
	
	<?php wp_enqueue_script( 'jquery' ); ?>
	
<?php
 	global $tagged;
	$tagged='';
	$tagged = single_tag_title("", false);
	if ($tagged) :?>
		<script type="text/javascript">	tagged="<?php echo $tagged ?>";
		</script>
	<?php endif; ?>
</head>
<body>
	<div class="container">
		<!--googleoff: all-->
		<div id="header">
			<?php 
				$headerlink = quonfig( 9 );
				$headerlogo = quonfig( 10 );
				$background = quonfig( 11 );
				if( !empty( $headerlink ) && !empty( $headerlogo ) ) : ?>
					<a class="homeLink" title="The Daily Record" href="<?php echo( $headerlink ); ?>"><img src="<?php echo( $headerlogo ); ?>"></a>
				<?php endif;
			?>
			<a href="<?php echo get_option('home'); ?>" title="<?php bloginfo('name'); ?>" class="logo" style="background:url(<?php echo( $background ); ?>)"></a>
			<div class="clear"></div>
		</div>
		<div class="nav">
			<?php dynamic_sidebar('Main Menu') ?>
		</div>	
		<!--googleon: all-->