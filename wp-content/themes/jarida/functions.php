<?php
$themename = "Jarida";
$themefolder = "jarida";

define ('theme_name', $themename );
define ('theme_ver' , 0.1 );

// Notifier Info
$notifier_name = $themename;
$notifier_url = "http://themes.tielabs.com/xml/".$themefolder.".xml";

//Docs Url
$docs_url = "http://themes.tielabs.com/docs/".$themefolder;

// Constants for the theme name, folder and remote XML url
define( 'MTHEME_NOTIFIER_THEME_NAME', $themename );
define( 'MTHEME_NOTIFIER_THEME_FOLDER_NAME', $themefolder );
define( 'MTHEME_NOTIFIER_XML_FILE', $notifier_url );
define( 'MTHEME_NOTIFIER_CACHE_INTERVAL', 43200 );
// WooCommerce
define('WOOCOMMERCE_USE_CSS', false);

add_action('woocommerce_before_main_content', 'my_theme_wrapper_start', 1);
function my_theme_wrapper_start() {
	if(tie_get_option( 'columns_num' ) != '2c')
		echo '<div class="content-wrap content-wrap-wide">';
	else	
		echo '<div class="content-wrap">';
}
add_action('woocommerce_archive_description', 'my_theme_wrapper_start2', 1);
function my_theme_wrapper_start2() {
  echo '<div class="clear"></div>';
}


global $pagenow;
if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' )
	add_action( 'init', 'tie_woocommerce_image_dimensions', 1 );

function tie_woocommerce_image_dimensions() {
  	$catalog = array(
		'width' 	=> '400',	// px
		'height'	=> '400',	// px
		'crop'		=> 1 		// true
	);
 
	$single = array(
		'width' 	=> '600',	// px
		'height'	=> '600',	// px
		'crop'		=> 1 		// true
	);
 
	$thumbnail = array(
		'width' 	=> '200',	// px
		'height'	=> '200',	// px
		'crop'		=> 1 		// false
	);
 
	// Image sizes
	update_option( 'shop_catalog_image_size', $catalog ); 		// Product category thumbs
	update_option( 'shop_single_image_size', $single ); 		// Single product image
	update_option( 'shop_thumbnail_image_size', $thumbnail ); 	// Image gallery thumbs
}


//for default password functionality
add_filter( 'wp_mail_from', 'sender_email' );
function sender_email( $original_email_address ) {
return 'asentech@thedolancompany.com';
}
//END



// Custom Functions 
include (TEMPLATEPATH . '/custom-functions.php');

// Get Functions
include (TEMPLATEPATH . '/functions/home-cats.php');
include (TEMPLATEPATH . '/functions/home-cats-wide.php');
include (TEMPLATEPATH . '/functions/home-cat-scroll.php');
include (TEMPLATEPATH . '/functions/home-cat-pic.php');
include (TEMPLATEPATH . '/functions/home-recent-box.php');
include (TEMPLATEPATH . '/functions/theme-functions.php');
include (TEMPLATEPATH . '/functions/common-scripts.php');
include (TEMPLATEPATH . '/functions/banners.php');
include (TEMPLATEPATH . '/functions/tie-views.php');
include (TEMPLATEPATH . '/functions/widgetize-theme.php');
include (TEMPLATEPATH . '/functions/default-options.php');
include (TEMPLATEPATH . '/functions/updates.php');

include (TEMPLATEPATH . '/includes/pagenavi.php');
include (TEMPLATEPATH . '/includes/breadcrumbs.php');
include (TEMPLATEPATH . '/includes/wp_list_comments.php');
include (TEMPLATEPATH . '/includes/widgets.php');

// TIE-Panel
include (TEMPLATEPATH . '/panel/shortcodes/shortcode.php');
if (is_admin()) {
	include (TEMPLATEPATH . '/panel/mpanel-ui.php');
	include (TEMPLATEPATH . '/panel/mpanel-functions.php');
	include (TEMPLATEPATH . '/panel/post-options.php');
	include (TEMPLATEPATH . '/panel/custom-slider.php');
	include (TEMPLATEPATH . '/panel/category-options.php');
	include (TEMPLATEPATH . '/panel/notifier/update-notifier.php');
	include (TEMPLATEPATH . '/panel/importer/tie-importer.php');
}

/*-----------------------------------------------------------------------------------*/
# Custom Admin Bar Menus
/*-----------------------------------------------------------------------------------*/
function tie_admin_bar() {
	global $wp_admin_bar;
	
	if ( current_user_can( 'switch_themes' ) ){
		$wp_admin_bar->add_menu( array(
			'parent' => 0,
			'id' => 'mpanel_page',
			'title' => theme_name ,
			'href' => admin_url( 'admin.php?page=panel')
		) );
	}
	
}
add_action( 'wp_before_admin_bar_render', 'tie_admin_bar' );

// with activate istall option
if ( is_admin() && isset($_GET['activated'] ) && $pagenow == 'themes.php' ) {

	if( !get_option('tie_active') ){
		tie_save_settings( $default_data );
		update_option( 'tie_active' , theme_ver );
	}
   //header("Location: admin.php?page=panel");
   
}
//RSS feed url
/**
  *  Custom feed 
  */
  add_action('init', 'customRSStdr');
function customRSStdr(){
        add_feed('storiesfeedtdr', 'customRSSFunctdr');
}
function customRSSFunctdr(){
        get_template_part('rss', 'storiesfeedtdr');
}
//custom feed close


/** code start for local */
/*if(!isset($asn_position_association_array)||empty($asn_position_association_array)){
$asn_position_association_array=array(
   "homepage"=>array(
    163336=>array("pos","atf"), 
    163337=>array("pos",""),
    163334=>array("pos",""),
    163348=>array("pos","atf"),
    163349=>array("pos","stf"),
    163350=>array("pos","btf"),
    163345=>array("pos","videoslot"),
    163347=>array("pos","btf"),
    163375=>array("category","construction and realestate"),
    163376=>array("category","law"),
    163378=>array("category","govt and politics"),
    163377=>array("category","education and nonprofits"), 
    163379=>array("category","labor"),
    163380=>array("category","trade and transportation"),
    163381=>array("category","dining"),
    163382=>array("category","retail"),
    163383=>array("category","money"),
    163384=>array("category","technology"),
    163385=>array("category","leisure"),
    163386=>array("category","health"),
  ),   
  "category"=>array(
    163363=>array("pos","atf"),
    163364=>array("pos","btf"),
    163365=>array("pos",""),
    163366=>array("pos","atf"), 
    163367=>array("pos","stf"),
    163368=>array("pos","btf"),
    163369=>array("pos","videoslot"),
  ),
  "reader_ranking"=>array(
    163370=>array("pos","atf"), 
    163371=>array("pos","a"),
    163372=>array("pos","b"),
    163373=>array("pos","c"),
    163374=>array("pos","d"),
  ),
  "single_article"=>array(
    163355=>array("pos","atf"), 
    163357=>array("pos","btf"),
    163358=>array("pos",""),
    163359=>array("pos","atf"),
    163360=>array("pos","stf"),
    163361=>array("pos","btf"),
    163362=>array("pos","videoslot"),
  )
  );
}*/

/** code end for local */

/** code start for stage */
if(!isset($asn_position_association_array)||empty($asn_position_association_array)){
	$asn_position_association_array=array(
	   "homepage"=>array(
		169147=>array("pos","atf"), 
		169148=>array("pos",""),
		169151=>array("pos","atf"),
		169152=>array("pos","stf"),
		169153=>array("pos","btf"),
		169149=>array("pos","videoslot"),
		169150=>array("pos","btf"),
		169171=>array("category","construction and realestate"),
		163376=>array("category","law"),
		163378=>array("category","govt and politics"),
		169172=>array("category","education and nonprofits"), 
		163379=>array("category","labor"),
		163380=>array("category","trade and transportation"),
		169173=>array("category","dining"),
		169174=>array("category","retail"),
		169175=>array("category","money"),
		169176=>array("category","technology"),
		169177=>array("category","leisure"),
		169178=>array("category","health"),
	  ),  
	  "category"=>array(
		169160=>array("pos","atf"),
		169161=>array("pos","btf"),
		169162=>array("pos",""),
		169163=>array("pos","atf"), 
		169164=>array("pos","stf"),
		169165=>array("pos","btf"),
		169166=>array("pos","videoslot"),
	  ),
	  "reader_ranking"=>array(
		169167=>array("pos","atf"), 
		169168=>array("pos","a"),
		163372=>array("pos","b"),
		169169=>array("pos","c"),
		169170=>array("pos","d"),
	  ),
	  "single_article"=>array(
		169154=>array("pos","atf"), 
		169155=>array("pos","btf"),
		169156=>array("pos",""),
		169157=>array("pos","atf"),
		163360=>array("pos","stf"),
		169158=>array("pos","btf"),
		169159=>array("pos","videoslot"),
	  )
	  );
	}
	
	/** code end for stage */	

	add_filter( 'wpe_heartbeat_allowed_pages', function( $pages ) {
		global $pagenow;
		$pages[] =  $pagenow;
		return $pages;
	});

/* After login redirect to home page */
add_filter( 'login_redirect', 'login_redirect', 10, 3 );
function login_redirect( $redirect_to, $request, $user ){
    $role = $user->roles[0];
    $dashboard = admin_url();
    if(get_current_blog_id()==1){
         if( $role == 'administrator'  ) {
      	    $redirect_to = $dashboard;
      	 
    	} else {
      	     $redirect_to = home_url();
         }	
    } else {
       	$redirect_to = $dashboard;
    }
    
    return $redirect_to;
}

/* Login Form Content changes for technavia */
add_action('wp_head', 'technavia_css');
function technavia_css(){

	if(isset($_GET['tpi']) && $_GET['tpi'] == 'login'){
?>
	<style type="text/css">
		.login-desc .technavia-login{display: block !important;}
		.login-desc .normal-login{display: none !important;}
	</style>
<?php
	}	
}
?>