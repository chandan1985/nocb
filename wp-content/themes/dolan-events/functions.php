<?php
/*
function addFarbtastic() {
	if ( is_admin() ) {
		$directory_file=get_bloginfo('template_directory');
		
//		wp_enqueue_script('farbtastic', get_bloginfo('template_url') . '/lib/scripts/farbtastic/farbtastic.js', array('jquery'));

	    wp_register_script( 'farbTastic', $directory_file.'/functions/js/farbtastic.js');
	    wp_enqueue_script( 'farbTastic', array('jquery') );
	}    
}
add_action('init', 'addFarbtastic');
*/
function column_shortcode( $atts, $content = null ) {
	return '<div class="column">' . $content . '</div>';
}
add_shortcode( 'column', 'column_shortcode' );


function addOptionsCSS() {
	$directory_file=get_bloginfo('template_directory');
	wp_enqueue_style("farbtastic", $directory_file."/functions/css/farbtastic.css", false, "1.0", "all");
}

add_action('init', 'addOptionsCSS');

require_once ( TEMPLATEPATH . '/functions/theme-options.php' );

//ini_set('display_errors', 'On');
//ini_set('error_reporting', E_ALL & ~E_NOTICE);
global $DR_Pages, $DR_Sponsorship;


$options = get_option('theme_options');

$admin_email = $options['sales_rep'];



if (isset($_POST['_tdr_form']) && sizeof($_POST['_tdr_form']) ) {
	$DR_Sponsorship = array();
	if ( !is_email($_POST['_tdr_email']) ) {
		$DR_Sponsorship['error'] = 'A valid email address is required';
	}
	if ( empty($DR_Sponsorship['error']) ) {
		if ( mail( $admin_email, 'Sponsorship Info Request - ' . get_bloginfo('url'), "Sponsorship information request from {$_POST['_tdr_email']}", "From: " . $admin_email . "\n") ) {
			$DR_Sponsorship['success'] = '<strong>Thank you.</strong><br />Your request has been sent.';
		}
	}
	
}

function dolan_events_register_sidebars(){
	register_sidebar(array(
		'name' => 'custom',
        'id'            => 'custom',
		'description' => 'Widgets in this area will be shown on the right-hand side.',
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '<h1>',
		'after_title' => '</h1>'
	));

	register_sidebar( array( 
		'name' => 'top-navigation',
        'id'            => 'top-navigation',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
	));
}

add_action('widgets_init','dolan_events_register_sidebars');

function nav_current_class($currentLink, $count){
	$class = "";
	if($currentLink == get_permalink() && $count == '1'){
		$class = "class = ' first current_page_item'";
	}
	else if($currentLink == get_permalink()){
		$class = "class = 'current_page_item'";
	}
	return $class;
}
?>