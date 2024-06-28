<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php $options = get_option('theme_options'); ?>
<title><?php wp_title('&laquo;', true, 'right'); ?> <?php echo $options['event_title']; ?></title>	
<?php wp_head(); ?>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css"  media="screen"/>
<style type="text/css">
body { background-color:<?php echo $options['background_color']; ?>;}
h1 {font-size: 1.5em; font-weight:bold; text-transform:uppercase; color:<?php echo $options['header_color']; ?>; margin-bottom:5px}
#registration h1, h2 {font-size: 1.5em; font-weight:bold; text-transform:uppercase; color:<?php echo $options['header_color']; ?>; margin-bottom:5px}
h3 {font-size:1.2em; font-weight:bold; margin:15px 0 5px 0}
.sf-menu a, .sf-menu a:visited, .int_wrap a, .sidebox ul li a { color: <?php echo $options['link_color']; ?>; }
.int_wrap h1, .current-winners h1 {font-size:2em; font-weight:bold; border-bottom:1px solid #ccc; color:<?php echo $options['header_color']; ?>; margin:0 20px 0 0;}

.awesome, .awesome:visited {
	background: <?php echo $options['button_color']; ?> url(<?php bloginfo('template_url'); ?>/images/common/alert-overlay.png) repeat-x 0 50%; 
	display: inline-block; 
	padding: 10px; 
	color: <?php echo $options['button_text_color']; ?>; 
	text-decoration: none;
	-moz-border-radius: 5px; 
	-webkit-border-radius: 5px;
	-moz-box-shadow: 0 1px 3px rgba(0,0,0,0.5);
	-webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.5);
	text-shadow: 0 -1px 1px rgba(0,0,0,0.25);
	border-bottom: 1px solid rgba(0,0,0,0.25);
	position: relative;
	cursor: pointer;
	margin:3px 0px 3px 0;
}
.awesome:hover { background-color: <?php echo $options['button_over_color']; ?>; color:<?php echo $options['button_text_color']; ?>; }
.awesome:active	{ top: 1px; }
.awesome, .awesome:visited { font-size: 15px; font-weight: bold; line-height: 1; text-shadow: 0 -1px 1px rgba(0,0,0,0.25); }
.small.awesome, .small.awesome:visited { font-size: 12px; padding: 6px; }
.silver.awesome, .silver.awesome:visited {color:<?php echo $options['secondary_button_text_color']; ?>;background:<?php echo $options['secondary_button_color']; ?> url(<?php bloginfo('template_url'); ?>/images/common/alert-overlay.png) repeat-x 0 50%; width:100px;	text-shadow: none; 	margin:3px 10px 3px 0;}
.silver.awesome:hover { background-color:<?php echo $options['secondary_button_over_color'] ?>;color:<?php echo $options['secondary_button_text_color']; ?>; }
</style>

  <!--[if IE 7]>
  <link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/ie7.css" type="text/css"  media="screen"/>
   
  <![endif]-->
 <!--[if IE 6]>
  <link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/ie6.css" type="text/css"  media="screen"/>
   
  <![endif]-->
	<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/functions.js"></script>
	<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.easing.1.3.js"></script>
	<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.photostack.pack.js"></script>
	<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/superfish.js"></script>
<script> 
 
    $(document).ready(function() { 
        $('ul.sf-menu').superfish({
        autoArrows:  false
        }
        ); 
    }); 
 
</script>

</head>
<body>
<?php if($options['parent_nav']==1) { ?>
<div id="parent-nav">
<a href="<?php echo $options['parent_blog_url']; ?>" class="parent-logo"><img class="tdr-logo" src="<?php echo $options['parent_logo']; ?>" alt="" /></a>
<?php if ( function_exists('dynamic_sidebar'))	dynamic_sidebar('top-navigation'); ?>
</div>
<?php } ?>
<div id="wrapper" <?php if($options['parent_nav']==0) { echo "class=\"no-nav\""; } ?>>
<div id="container-top"></div>
<div id="container">
     <?php $options = get_option('theme_options'); ?>
	<div id="header">
    <a class="logo" href="<?php bloginfo('url'); ?>"><img src="<?php echo $options['logo']; ?>" alt="" /></a>
    <div id="social">
	<div class="st_sharethis"></div>
	<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
	<script type="text/javascript">
	        stLight.options({
	                publisher:'12345'
	        });
	</script>
	<div class='social-icons'>
	<?php
	$options = get_option('theme_options');
	$iconsArray = array();
	for($i=0; $i<=5; $i++){
		if(isset($options['social_icon'.$i]) && $options['social_icon'.$i] == 1) {
			$iconsArray[$i] = array($options['social_icon'.$i], $options['social_icon_link'.$i], $options['social_icon_url'.$i]);			
		}
	}
	$count = 0;
	foreach($iconsArray as $item) {
	?>
		<a href="<?php echo $item[1]; ?>"><img src="<?php echo $item[2] ?>" alt="" /></a>
	<?php
	$count++;
	}
	?>
	</div>
	</div>
	</div>
   <div id="main-content">
   		<ul id="main-nav">
		<!-- setting custom nav bar -->
		<?php if(!empty($options['custom_nav_bar'])): ?>
		<?php
				$count = 1;
				while($count<=5){
				if($options['custom_nav_bar_label'.$count] != ""){
				$class = nav_current_class($options['custom_nav_bar_link'.$count], $count);
		?>
				<li <?php echo $class; ?>><a href="<?php echo $options['custom_nav_bar_link'.$count]; ?>"><span><?php echo $options['custom_nav_bar_label'.$count]; ?></span></a></li>
				<?php 
				}
				$count++;
				} ?>
		<!-- end customer nav bar -->
		<?php else: ?>
   		<li class="first <?php if(is_front_page()){ echo 'current_page_item'; } ?>"><a href="<?php bloginfo('url'); ?>"><span>HOME</span></a></li>
				<?php
				$page = get_page_by_title('home');
				$pageID = $page->ID;
				$page_output = wp_list_pages('exclude='.$pageID.'&echo=0&title_li=&sort_column=menu_order&depth=1');
				$page_output = preg_replace('@\<li([^>]*)>\<a([^>]*)>(.*?)\<\/a>@i', '<li$1><a$2><span>$3</span></a>', $page_output);
				echo $page_output;
				?>
		<?php endif; ?>
        </ul>

        <div id="content">
<?php 
	global $post, $DR_Kids, $true_parent;
	if ( $post->post_parent ) {
		$true_parent = get_post($post->post_parent);
	}
	else { 
		$true_parent = $post;
	}
	$DR_Kids = get_pages('child_of=' . $true_parent->ID);
?>