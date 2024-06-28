<?php
/*
 * Plugin Name: DMC Shortcode Plugin
 * Plugin URI: http://www.dolanmedia.com
 * Description: Short code definitions 
 * Author: Ray Champagne, Dave Long
 * Version: 0.3
 * Author URI: http://www.dolanmedia.com
 
 Modifications: 
	1/10/2011 - Dave Long:  	-Added support for <script> and <style> tags
	1/11/2011 - Dave Long:  	-Added support for <object> and image maps via [img_map]
*/
 
 
/*========================================================================*/
/*  Define the functions for the shortcodes.  You can have as many as you like.*/
/*========================================================================*/
 
 //[iframe src="http://www.somewhere.com/iframe.html" height="xx" width="xx" style="" frameborder="xx" scrolling=""]
 function iframe_func($atts) {
 	extract(shortcode_atts(array(
 		'src' => '#',
 		'height' => '',
 		'width' => '',
 		'style' => '',
 		'scrolling' => '',
 		'frameborder' => ''
 	), $atts));
 	return '<iframe src="' . $src . '" height="'. $height . '" width="' . $width . '" style="' . $style . '" frameborder="' . $frameborder .'" scrolling="' . $scrolling . '" allowfullscreen></iframe>';
 }

 //[script type="text/javascript" src ="http://www.somewhere.com/somescript.js"] or
 //[script type="text/javascript"] some code here... [/script]
 function script_func($atts, $code = '') {
 	extract(shortcode_atts(array(
 		'type' => '',
		'src' => ''
 	), $atts));
	$find = array( '&#8211;', '&#8212;', '&#215;', '&#8230;', '&#8220;', '&#8217;s', '&#8221;','<br />', '&#8216;', '&#8217;', '&#8242;' );
 	$replace = array( '--', '---', 'x', '...', '"', '\'s', '"','', '\'', '\'', '\'' );
	$code =  str_replace( $find, $replace, $code );
	if(!empty($src))
		return '<script type="' . $type . '" src="' . $src . '"></script>';
	else
		return '<script type="' . $type . '">' . $code . '</script>';
 }
 
 //[style type="text/css" media="screen"] some css here... [/style]
 function css_func($atts, $code = '') {
	extract(shortcode_atts(array(
 		'type' => 'text/css',
		'media' => ''
 	), $atts));
	$find = array( '&#8211;', '&#8212;', '&#215;', '&#8230;', '&#8220;', '&#8217;s', '&#8221;','<br />', '&#8216;', '&#8217;'  );
	$replace = array( '--', '---', 'x', '...', '``', '\'s', '"','', '\'', '\'' );
	$code =  str_replace( $find, $replace, $code );
	return '<style type="' . $type . '" media="'. $media . '">' . $code . '</style>';
 }
  
 //[object height="xx" width="xx" class="" id="" style=""] embed code here... [/object]
 function object_func($atts, $code = '') {
 	extract(shortcode_atts(array(
 		'height' => '',
		'width' => '',
		'class' => '',
		'id' => '',
		'style' => ''
 	), $atts));
	$find = array( '&#8211;', '&#8212;', '&#215;', '&#8230;', '&#8220;', '&#8217;s', '&#8221;','<br />', '&#8216;', '&#8217;'  );
	$replace = array( '--', '---', 'x', '...', '``', '\'s', '"','', '\'', '\'' );
	$code =  str_replace( $find, $replace, $code );
	return '<object height="' . $height . '" width="'. $width . '" class="'. $class . '" id="'. $id . '" style="'. $style . '">' . $code . '</object>';
 }

 //[map name="image_map_name" class="" id="" style=""] area code(s) here... [/map]
 function map_func($atts, $code = '') {
 	extract(shortcode_atts(array(
		'name' => 'image_map',
		'class' => '',
		'id' => '',
		'style' => ''
 	), $atts));
	$find = array( '&#8211;', '&#8212;', '&#215;', '&#8230;', '&#8220;', '&#8217;s', '&#8221;','<br />', '&#8216;', '&#8217;' , '[' , ']' );
	$replace = array( '--', '---', 'x', '...', '``', '\'s', '"','', '\'', '\'', '<', '>' );
	$code =  str_replace( $find, $replace, $code );
	
	return '<map name="' . $name . '" class="'. $class . '" id="'. $id . '" style="'. $style . '">' . $code . '</map>';
 }
 
 //[img_map src="http://www.somewhere.com/image.xxx" alt = "" height="xx" width="xx" style="" usemap="#imagemap_name"]
 function img_map($atts) {
 	extract(shortcode_atts(array(
		'src' => '#',
		'alt' => '',
 		'height' => '',
 		'width' => '',
 		'style' => '',
		'usemap' => ''
 	), $atts));
	
	return'<img src="' . $src . '" alt="'. $alt . '" height="'. $height . '" width="' . $width . '" style="' . $style . '" usemap="' . $usemap . '"/>';
 }
 
 //[img_map src="http://www.somewhere.com/image.xxx" alt = "" height="xx" width="xx" style="" name="image name"]
 function img_name($atts) {
 	extract(shortcode_atts(array(
		'src' => '#',
		'alt' => '',
 		'height' => '',
 		'width' => '',
 		'style' => '',
		'name' => ''
 	), $atts));
	
	return'<img src="' . $src . '" alt="'. $alt . '" height="'. $height . '" width="' . $width . '" style="' . $style . '" name="' . $name . '"/>';
 } 

 
 //[input tyle="submit" value = "some value" class=""  style="" onclick="something to do onclick"]
 function input_func($atts) {
 	extract(shortcode_atts(array(
		'type' => '',
		'value' => '',
		'class' => '',
		'style' => '',
 		'onclick' => ''
 	), $atts));
	
	return'<input type="' . $type . '" value="'. $value . '" class="'. $class . '" style="' . $style . '" onclick="' . $onclick . '"/>';
 }
 
 //[f_embed src="http://www.somewhere.com/objectLocation" scale = "some value" s_type="embed type"  background="#XXXXXX" width="xx" height="xx" allowFullScreen="true/false" allowScriptAccess="value" vars="FlashVars go here"]
 function f_embed_func($atts) {
 	extract(shortcode_atts(array(
		'src' => '',
		'scale' => 'noscale',
		's_type' => '',
		'background' => '',
		'width' => '',
 		'height' => '',
		'allowFullScreen' => 'true', 
		'allowScriptAccess' => 'always',
		'vars' => ''
 	), $atts));
	$find = array( '&#8211;', '&#8212;', '&#215;', '&#8230;', '&#8220;', '&#8217;s', '&#8221;','<br />', '&#8216;', '&#8217;' , '[' , ']', '&amp;' );
	$replace = array( '--', '---', 'x', '...', '``', '\'s', '"','', '\'', '\'', '<', '>', '&' );
	$vars =  str_replace( $find, $replace, $vars );
	return'<embed src="' . $src . '" scale="' . $scale . '" s type="'. $s_type . '" background="'. $background . '" width="' . $width . '" height="' . $height . '" allowFullScreen="'. $allowFullScreen . '" allowScriptAccess="' . $allowScriptAccess . '" FlashVars="' . $vars . '"/>';
 }
 
 function print_menu_shortcode($atts, $content = null) {
    extract(shortcode_atts(array( 'name' => null, ), $atts));
    return wp_nav_menu( array( 'menu' => $name, 'echo' => false ) );
}

 //add more functions here - don't forget to register them at the end of this page!
 
/*========================================================================*/
/* Register the shortcodes.												   */
/*========================================================================*/
 add_shortcode('menu', 'print_menu_shortcode');
 add_shortcode('iframe', 'iframe_func');
 add_shortcode('script', 'script_func');
 add_shortcode('style', 'css_func');
 add_shortcode('object', 'object_func');
 add_shortcode('map', 'map_func');
 add_shortcode('img_map', 'img_map'); // WordPress strips the usemap attribute from img tags
 add_shortcode('img_name', 'img_name'); // WordPress strips the name attribute from img tags
 add_shortcode('input', 'input_func');
 add_shortcode('f_embed', 'f_embed_func');

/*========================================================================*/
/* Copied from RSS Shortcode											   */
/*========================================================================*/
  
 function yoast_rss_shortcode( $atts ) {
	extract(shortcode_atts(array(  
	    "feed" 		=> '',  
		"num" 		=> '5',  
		"excerpt" 	=> true,
		"target"	=> '_self'
	), $atts));
	require_once(ABSPATH.WPINC.'/rss.php');  
	if ( $feed != "" && $rss = fetch_rss( $feed ) ) {
		$content = '<ul>';
		if ( $num !== -1 ) {
			$rss->items = array_slice( $rss->items, 0, $num );
		}
		foreach ( (array) $rss->items as $item ) {
			$content .= '<li>';
			if ($target != '_self')
				$content .= '<a href="'.clean_url( $item['link'] ).'" target="'.$target.'">'.$item['title'] .'</a>';
			else
				$content .= '<a href="'.clean_url( $item['link'] ).'">'. $item['title'] .'</a>';
			if ( $excerpt != false && $excerpt != "false") {
				$content .= '<br/><span class="rss_excerpt">'.$item['summary'] .'</span>';
			}
			$content .= '</li>';
		}
		$content .= '</ul>';
	}
	return $content;
}

add_shortcode( 'rss', 'yoast_rss_shortcode' );


// example: [year]
if ( !shortcode_exists( 'year' ) ) {
	function tdc_year_shortcode_callback() {
		$year = date('Y');
		return $year;
	}
	add_shortcode('year', 'tdc_year_shortcode_callback');
}

/*function tdc_privacypolicy_shortcode() {
	$sc = '<a href="https://bridgetowermedia.com/ccpa/" target="_blank">YOUR CALIFORNIA PRIVACY RIGHTS/PRIVACY POLICY</a>';
	return $sc;
}
add_shortcode('privacypolicy', 'tdc_privacypolicy_shortcode');

function tdc_cookiepolicy_shortcode() {
	$sc = '<a href="https://bridgetowermedia.com/cookie-policy/" target="_blank">DO NOT SELL MY INFO/COOKIE POLICY</a>';
	return $sc;
}
add_shortcode('cookiepolicy', 'tdc_cookiepolicy_shortcode');*/


function tdc_privacypolicy_shortcode() {
	$sc = '<a href="http://bridgetowermedia.com/privacy-policy/" target="_blank">PRIVACY POLICY</a>';
	return $sc;
}
add_shortcode('privacypolicy', 'tdc_privacypolicy_shortcode');


function tdc_privacypolicy_california_shortcode() {
	$sc = '<a href="https://bridgetowermedia.com/ccpa/" target="_blank">YOUR CALIFORNIA PRIVACY RIGHTS/PRIVACY POLICY</a>';
	return $sc;
}
add_shortcode('privacypolicycalifornia', 'tdc_privacypolicy_california_shortcode');


function tdc_cookiepolicy_shortcode() {
	$sc = '<a href="https://bridgetowermedia.com/cookie-policy/" target="_blank">DO NOT SELL MY INFO/COOKIE POLICY</a>';
	return $sc;
}
add_shortcode('cookiepolicy', 'tdc_cookiepolicy_shortcode');

function tdc_corplogo_shortcode() {
	$sc = '<a href="http://bridgetowermedia.com" style="max-width:140px;"><img alt="bridge tower media logo" src="http://bridgetowermedia.com/files/2016/08/btmlogo140.svg" onerror="this.onerror=null; this.src=\'http://bridgetowermedia.com/files/2016/08/btmlogo140.png\'" scale="0" data-pin-nopin="true" style="max-width:140px;width:100%;"></a>';
	return $sc;
}
add_shortcode('corplogo', 'tdc_corplogo_shortcode');

function tdc_subscriberagreement_shortcode() {
	$sc = '<a href="http://bridgetowermedia.com/subscriber-agreement/">SUBSCRIBER AGREEMENT</a>';
	return $sc;
}
add_shortcode('subscriberagreement', 'tdc_subscriberagreement_shortcode');


// START DATAJOE ECOMMERCE RBJ
function datajoe_rbj_shortcode(){
$djostartdiv = '<div id="DJO_CONTENT">';
//OPTIONAL VARIABLES - for remote authentication
$djoP1 = "&p1=";
$djoP2 = "&p2=";
//REQUIRED VARIABLES
//determines where to get your content
$djoDomain = 'http://ecom.datajoe.com/ecom/exec_redirect.php';
//your encrypted client id
$djoCid = '?djocid=23D46256F858BA1616340E0F2EDC';
//our session cookie that we create for your users browser
$djoSS = ($_COOKIE['djoss']?"&PHPSESSID=".$_COOKIE['djoss']:"");
//the browser's querystring
$djoQS = "&".$_SERVER['QUERY_STRING'];
//GENERATE THE CONTENT
//start curl
$ch = curl_init();
//set the url
curl_setopt($ch, CURLOPT_URL, $djoDomain.$djoCid.$djoSS.$djoQS.$djoP1.$djoP2);
//set the transfer options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// grab URL, and return output
$return_djo_curl = curl_exec($ch);
$djoenddiv =' </div>';
return $djoestartdiv.$return_djo_curl.$djoenddiv;
}
add_shortcode('datajoe_rbj', 'datajoe_rbj_shortcode');
// FINISH DATAJOE ECOMMERCE RBJ

//Shortcode for NOPG Sugar/ActOn marketing form
function new_orleans_sugar_acton_form(){
    $act_on_form = '<!-- ======================================================================================= -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="//news.neworleanscitybusiness.com/acton/formNegCap.css">
<script type="text/javascript" src="//news.neworleanscitybusiness.com/acton/form/22741/0004/form.js">
</script>
<!-- ======================================================================================= -->
<!-- place exactly one copy of everything above these lines in the head section of your page -->
<!-- place everything below these lines in the body section of your page -->
<!-- ======================================================================================= -->
<form id="form_0004" class="nopg_marketing_form" method="post" enctype="multipart/form-data" action="//news.neworleanscitybusiness.com/acton/forms/userSubmit.jsp" accept-charset="UTF-8">
<input type="hidden" name="ao_a" value="22741" >
<input type="hidden" name="ao_f" value="0004" >
<input type="hidden" name="ao_d" value="0004:d-0001" >
<input type="hidden" name="ao_p" id="ao_p" value="0" >
<input type="hidden" name="ao_jstzo" id="ao_jstzo" value="" >
<input type="hidden" name="ao_cuid" value="" >
<input type="hidden" name="ao_srcid" value="" >
<input type="hidden" name="ao_bot" id="ao_bot" value="yes" >
<input type="hidden" name="ao_camp" value="" >
<link rel="stylesheet" type="text/css" href="//news.neworleanscitybusiness.com/acton/form/22741/0004/form.css">
<div id="ao_alignment_container" class="aoFormContainer" align="center">
<table class="ao_tbl_container" border="0" cellspacing="0" cellpadding="0">
<tr>
<td class="ao_tbl_cell" style="padding-left: 10px; padding-right: 10px" align="center">
<div class="formField">
<div class="formSectionDescription">
<p>
Please fill out the information below so we can help you develop a strategic marketing plan.</p>
</div>
</div>
</td>
</tr>
<tr>
<td class="ao_tbl_cell" style="padding-left: 10px; padding-right: 10px" align="center">
<div align="left">
<div class="formField">
<div class="formFieldLabel" id="form_0004_fld_1-Label">
<label for = "form_0004_fld_1">
First Name</label>
<b style="color: #FF0000; cursor: default" title="Required Field">
*</b>
</div>
<input type="text" class="formFieldText formFieldLarge" id="form_0004_fld_1" name="First Name" >
</div>
<script type="text/javascript">
if (typeof(addRequiredField) != \'undefined\') { addRequiredField (\'form_0004_fld_1\'); } </script>
</div>
</td>
</tr>
<tr>
<td class="ao_tbl_cell" style="padding-left: 10px; padding-right: 10px" align="center">
<div align="left">
<div class="formField">
<div class="formFieldLabel" id="form_0004_fld_2-Label">
<label for = "form_0004_fld_2">
Last Name</label>
<b style="color: #FF0000; cursor: default" title="Required Field">
*</b>
</div>
<input type="text" class="formFieldText formFieldLarge" id="form_0004_fld_2" name="Last Name" >
</div>
<script type="text/javascript">
if (typeof(addRequiredField) != \'undefined\') { addRequiredField (\'form_0004_fld_2\'); } </script>
</div>
</td>
</tr>
<tr>
<td class="ao_tbl_cell" style="padding-left: 10px; padding-right: 10px" align="center">
<div align="left">
<div class="formField">
<div class="formFieldLabel" id="form_0004_fld_3-Label">
<label for = "form_0004_fld_3">
Email</label>
<b style="color: #FF0000; cursor: default" title="Required Field">
*</b>
</div>
<input type="text" class="formFieldText formFieldLarge" id="form_0004_fld_3" name="Email" >
</div>
<script type="text/javascript">
if (typeof(addRequiredField) != \'undefined\') { addRequiredField (\'form_0004_fld_3\'); } </script>
</div>
</td>
</tr>
<tr>
<td class="ao_tbl_cell" style="padding-left: 10px; padding-right: 10px" align="center">
<div align="left">
<div class="formField">
<div class="formFieldLabel" id="form_0004_fld_4-Label">
<label for = "form_0004_fld_4">
Company Name</label>
</div>
<input type="text" class="formFieldText formFieldLarge" id="form_0004_fld_4" name="Company Name" >
</div>
</div>
</td>
</tr>
<tr>
<td class="ao_tbl_cell" style="padding-left: 10px; padding-right: 10px" align="center">
<div align="left">
<div class="formField">
<div class="formFieldLabel" id="form_0004_fld_5-Label">
<label for = "form_0004_fld_5">
Job Title</label>
</div>
<input type="text" class="formFieldText formFieldLarge" id="form_0004_fld_5" name="Field 2" >
</div>
</div>
</td>
</tr>
<tr>
<td class="ao_tbl_cell" style="padding-left: 10px; padding-right: 10px" align="center">
<div align="left">
<div class="formField">
<div class="formFieldLabel" id="form_0004_fld_6-Label">
<label for = "form_0004_fld_6">
Phone Number</label>
</div>
<input type="text" class="formFieldText formFieldLarge" id="form_0004_fld_6" name="PhoneNumber" onBlur="singleCheck (\'form_0004_fld_6\', \'ANYPHONE\', \'form_0004_fld_6-Label\')">
</div>
<script type="text/javascript">
if (typeof(addFieldToValidate) != \'undefined\') { addFieldToValidate (\'form_0004_fld_6\', \'ANYPHONE\'); addFieldToValidate (\'form_0004_fld_6\', \'LENGTH\', 110); } </script>
</div>
</td>
</tr>
<tr>
<td class="ao_tbl_cell" style="padding-left: 10px; padding-right: 10px" align="center">
<div align="left">
<div class="formField">
<div class="formFieldLabel">
Comments</div>
<textarea class="formTextArea formTextAreaLarge formTextAreaWidthLarge" id="form_0004_fld_7" name="Comments">
</textarea>
</div>
</div>
</td>
</tr>
<tr>
<td class="ao_tbl_cell" style="padding-left: 10px; padding-right: 10px" align="center">
<div align="left">
<input type="hidden" id="form_0004_fld_9" name="lead_source" value="' . get_bloginfo( "name" ) . '">
</div>
</td>
</tr>
<tr>
<td class="ao_tbl_cell" style="padding-left: 10px; padding-right: 10px" align="center">
<div align="left">
<input type="hidden" id="form_0004_fld_10" name="lead_source_description" value="' . wp_strip_all_tags(get_the_title()) . '">
</div>
</td>
</tr>
<tr>
<td class="ao_tbl_cell" style="padding-left: 10px; padding-right: 10px" align="center">
<div align="left">
<input type="hidden" id="form_0004_fld_10" name="Organization" value="NOPG">
</div>
</td>
</tr>
<!-- BUTTONS -->
<tr>
<td>
&nbsp;</td>
</tr>
<tr>
<td style="padding-bottom: 10px" align="center" id="form_0004_ao_submit_button">
<input id="form_0004_ao_submit_input" type="button" name="Submit" value="Submit" onClick="doSubmit(document.getElementById(\'form_0004\'))">
</td>
</tr>
<tr class="formNegCap">
<td>
<input type="text" id="ao_form_neg_cap" name="ao_form_neg_cap" value="">
</td>
</tr>
</table>
</div>
<img src=\'//news.neworleanscitybusiness.com/acton/form/22741/0004:d-0001/pgend.gif\' width=\'1\' height=\'1\'>
</form>';
    return $act_on_form;
}
add_shortcode('new_orleans_general_marketing_form', 'new_orleans_sugar_acton_form')

?>
