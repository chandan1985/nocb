<?php
//$html = file_get_contents("http://malw.dev.asentechllc.net");
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Mag_Lite
 */

?><?php
	/**
	 * Hook - mag_lite_action_doctype.
	 *
	 * @hooked mag_lite_doctype -  10
	 */
	do_action( 'mag_lite_action_doctype' );
?>
<head>


	<?php
	/**
	 * Hook - mag_lite_action_head.
	 *
	 * @hooked mag_lite_head -  10
	 */
	do_action( 'mag_lite_action_head' );
	
	
	?>

<?php wp_head(); ?>
<script type='text/javascript' >
//Menus
jQuery(document).ready(function() {
	jQuery('#main-nav ul > li > .mega-menu-block, #main-nav ul > li > ul, #main-nav ul > li > ul > li > ul, #main-nav ul > li > ul > li > ul> li > ul, .top-menu  ul > li > ul, .top-menu  ul > li > ul > li > ul, .top-menu  ul > li > ul > li > ul> li > ul ').parent('li').addClass('parent-list');
	jQuery('.parent-list').find("a:first").append(' <span class="sub-indicator"></span>');
	
	jQuery("#main-nav li , .top-menu li").each(function(){	
		var $sublist = jQuery(this).find('ul:first:not(.mega-menu-content ul.sub-menu), .mega-menu-block');		
		jQuery(this).hover(function(){	
			$sublist.stop().css({overflow:"hidden", height:"auto", display:"none"}).slideDown(200, function(){
				jQuery(this).css({overflow:"visible", height:"auto"});
			});	
		},
		function(){	
			$sublist.stop().slideUp(200, function()	{	
				jQuery(this).css({overflow:"hidden", display:"none"});
			});
		});	
	});
	});


</script>
</head>

<body <?php body_class(); ?>>

	<?php
		/**
		 * Hook - mag_lite_action_before.
		 *
		 * @hooked mag_lite_page_start - 10
		 * @hooked mag_lite_skip_to_content - 15
		 */
		do_action( 'mag_lite_action_before' );
	?>
	
	<?php 
		/**
		 * Hook - mag_lite_action_before_header
		 *
		 * @hooked mag_lite_header_start -10
		 *
		 */
		do_action( 'mag_lite_action_before_header' );
	?>
	
	<?php 
	

		
			

	
	//$url = 'https://somedomain.com/somesite/';
//$content = $html;//file_get_contents($url);
//$first_step = explode( '<header id="theme-header">' , $content );
//$second_step = explode("</header>" , $first_step[1] );

//echo $second_step[0];
	
	
		/**
		 * Hook - mag_lite_action_header
		 *
		 * @hooked mag_lite_header -10
		 *
		 */
		//do_action( 'mag_lite_action_header' );
	?>

	<?php 
	 /**
	  * Hook - mag_lite_action_after_header
	  *
	  * @hooked mag_lite_header_end -10
	  *
	  */
	do_action( 'mag_lite_action_after_header' ); 
	?> 

	<?php
		/**
		 * Hook - mag_lite_action_before_content.
		 *
		 * @hooked mag_lite_content_start - 10
		 */
		do_action( 'mag_lite_action_before_content' );
	?>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css"> 