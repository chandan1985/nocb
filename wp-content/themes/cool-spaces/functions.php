<?php
/**
 * Mag Lite functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Mag_Lite
 */
 
//define('', '');
//define('', '');
//require_once(RAIZ_WORDPRESS."/wp-load.php");
//load the file
//include_once ("wp-content/themes/jarida/header.php");




if ( ! function_exists( 'mag_lite_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
function mag_lite_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Mag Lite, use a find and replace
		 * to change 'mag-lite' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'mag-lite', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		//add_image_size( 'post-thumbnails', 880, 400, true );
		
		add_theme_support( 'post-thumbnails');
		add_image_size( 'mag-lite-home-slider', 788, 388, true);
		add_image_size( 'mag-lite-home-promo', 346, 314, true);
		add_image_size( 'mag-lite-home-promo-center', 458, 190, true);
		add_image_size( 'mag-lite-home-promo-default', 360, 202, true);
		add_image_size( 'mag-lite-home-tab123', 370, 300, true);
		add_image_size( 'mag-lite-promo-slider', 845, 432, true);
		add_image_size( 'mag-lite-promo-latest-popular-thumbnail', 90, 90, true);
		add_image_size( 'mag-lite-promo-latest-popular-medium', 330, 191, true);
		add_image_size( 'mag-lite-archive', 330, 241, true);
		add_image_size( 'mag-lite-home-vert', 350, 600, true);

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'menu-1' 		=> esc_html__( 'Primary', 'mag-lite' ),
			'top-menu' 		=> esc_html__( 'Top Menu', 'mag-lite' ),
			'social-media'  => esc_html__( 'Social Media', 'mag-lite' ),
			) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
     
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			) );

		add_theme_support( 'custom-header', array(
			'height'      => 90,
			'width'       => 1300,
			'flex-width'  => true,
			'flex-height' => true,
			) );
		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'mag_lite_custom_background_args', array(
			'default-color' => 'f0f0f0',
			'default-image' => '',
			) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support('customize-selective-refresh-widgets');

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'post-thumbnails', array(
			'height'      => 880,
			'width'       => 6000,
			'flex-width'  => true,
			'flex-height' => true,
			) ); 
		 
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
			) );

		add_theme_support(
			'post-formats', array(
				'image',
				'video'
				) );		
	}
	endif;
	add_action( 'after_setup_theme', 'mag_lite_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
 
 add_action( 'wp_enqueue_scripts', function()
{   
    wp_add_inline_script( 
        'jquery-migrate', 'jQuery.migrateMute = true;',
        'before' 
    );
} );
 
 
 
function mag_lite_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'mag_lite_content_width', 640 );
}
add_action( 'after_setup_theme', 'mag_lite_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
 
 
//define('', '');
//define('', '');
//require_once(RAIZ_WORDPRESS."/wp-load.php");
 
function mag_lite_widgets_init() {

	register_sidebar( array(
		'name'          => esc_html__( 'Header Advertisement', 'mag-lite' ),
		'id'            => 'header-advertisement',
		'description'   => esc_html__( 'Add widgets here.', 'mag-lite' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
		) );
		
		
		

	register_sidebar( array(
		'name'          => esc_html__( 'News Ticker', 'mag-lite' ),
		'id'            => 'news-ticker-section',
		'description'   => esc_html__( 'This sidebar will appear below menu section.', 'mag-lite' ),
		'before_widget' => '<section id="%1$s" class="%2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
		) );


	register_sidebar( array(
		'name'          => esc_html__( 'Home Slider', 'mag-lite' ),
		'id'            => 'home-slider-section',
		'description'   => esc_html__( 'This sidebar will appear below menu section.', 'mag-lite' ),
		'before_widget' => '<section id="%1$s" class="%2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
		) );

	register_sidebar( array(
		'name'          => esc_html__( 'Home Promo', 'mag-lite' ),
		'id'            => 'home-promo-section',
		'description'   => esc_html__( 'This sidebar will appear below Slider section.', 'mag-lite' ),
		'before_widget' => '<section id="%1$s" class="%2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
		) );

	register_sidebar( array(
		'name'          => esc_html__( 'Home Widget Area', 'mag-lite' ),
		'id'            => 'home-page-section',
		'description'   => esc_html__( 'This sidebar will appear in home section.', 'mag-lite' ),
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
		) );

	register_sidebar( array(
		'name'          => esc_html__( 'Home Sidebar Widget', 'mag-lite' ),
		'id'            => 'home-page-sidebar',
		'description'   => esc_html__( 'This sidebar will appear in sidebar of home page.', 'mag-lite' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
		) );			

	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'mag-lite' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'mag-lite' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
		) );

	$footer_before_widget =  '<div id="footer-first" class="footer-widgets-box">';
	$footer_after_widget  =  '</div></div><!-- .widget /-->';
	$footer_before_title  =  '<div class="footer-widget-top"><h4>';
	$footer_after_title   =  '</h4></div>
						<div class="footer-widget-container">';

	register_sidebar( array(
		'name'          => sprintf( esc_html__( 'Footer %d', 'mag-lite' ), 1 ),
		'id'            => 'footer-1',
		'before_widget' => $footer_before_widget , 'after_widget' => $footer_after_widget , 'before_title' => $footer_before_title , 'after_title' => $footer_after_title ,
		) );
		$footer_before_widget =  '<div id="footer-second" class="footer-widgets-box">';
	register_sidebar( array(
		'name'          => sprintf( esc_html__( 'Footer %d', 'mag-lite' ), 2 ),
		'id'            => 'footer-2',
		'before_widget' => $footer_before_widget , 'after_widget' => $footer_after_widget , 'before_title' => $footer_before_title , 'after_title' => $footer_after_title ,
		) );
		$footer_before_widget =  '<div id="footer-third" class="footer-widgets-box">';
	register_sidebar( array(
		'name'          => sprintf( esc_html__( 'Footer %d', 'mag-lite' ), 3 ),
		'id'            => 'footer-3',
		'before_widget' => $footer_before_widget , 'after_widget' => $footer_after_widget , 'before_title' => $footer_before_title , 'after_title' => $footer_after_title ,
		) );


	register_sidebar( array(
		'name'          => sprintf( esc_html__( 'Footer %d', 'mag-lite' ), 4 ),
		'id'            => 'footer-4',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</h2></span>',
		) );
}
add_action( 'widgets_init', 'mag_lite_widgets_init' );



/**
 * Registered parent header and footer template.
 */

add_action('pagelines_after_footer','myfunct');
function myfunct(){
	include "../global_footer.php";
}

add_action('pagelines_after_header','myfunction');
function myfunction(){
	include "../global_header.php";
}

add_action( 'wp_head', 'my_header' );
function my_header() {
    echo '<!-- this is on all sites in the <head> -->';
}

/**
 * Enqueue scripts and styles.
 */
function mag_lite_scripts() {

	$fonts_url = mag_lite_fonts_url();	

	if ( ! empty( $fonts_url ) ) {
		wp_enqueue_style( 'mag-lite-google-fonts', $fonts_url, array(), null );
	}	

	// Load fontawesome
	wp_enqueue_style( 'font-awesome', get_template_directory_uri().'/assest/css/font-awesome.min.css', array(), '4.4.0' );

	// Owl Carousel Assets
	wp_enqueue_style( 'owl-carousel', get_template_directory_uri().'/assest/css/owl.carousel.css', array(), 'v2.2.0' );	

	// Owl Theme meanmenu
	wp_enqueue_style( 'owl-theme', get_template_directory_uri().'/assest/css/owl.theme.css', array(), 'v2.2.0' );

	//meanmenu
	wp_enqueue_style( 'meanmenu', get_template_directory_uri().'/assest/css/meanmenu.css', array(), '2.0.7' );    
    wp_enqueue_style( 'bootstrap', get_template_directory_uri().'/assest/css/bootstrap.css', array(), '2.0.7' );
    wp_enqueue_style( 'customcss', get_template_directory_uri().'/assest/css/custom.css', array(), '2.0.7' );	
    wp_enqueue_style( 'cool-space', get_template_directory_uri().'/assest/css/cool-space.css', array(), '2.0.7' );
    wp_enqueue_style( 'Mobile Menu', get_template_directory_uri().'/assest/css/mobilemenu.css', array(), '2.0.7' );	
	wp_enqueue_style( 'mag-lite-style', get_stylesheet_uri() );
	add_editor_style( get_stylesheet_uri());


	wp_enqueue_script( 'mag-lite-navigation', get_template_directory_uri() . '/assest/js/navigation.js', array(), '20151215', true );
	
	wp_enqueue_script( 'custom-js', get_template_directory_uri() . '/assest/js/core.min.js', array(), '20151215', true );
	wp_enqueue_script( 'script-js', get_template_directory_uri() . '/assest/js/script.js', array(), '20151215', true );
	wp_enqueue_script( 'tie-scripts.js', get_template_directory_uri() . '/assest/js/tie-scripts.js', array(), '20151215', true );
	wp_enqueue_script( 'mobilemenu.js', get_template_directory_uri() . '/assest/js/mobilemenu.js', array(), '20151215', true );

	wp_enqueue_script( 'mag-lite-skip-link-focus-fix', get_template_directory_uri() . '/assest/js/skip-link-focus-fix.js', array(), '20151215', true );

	//owl carousel
	wp_enqueue_script( 'jquery-owl-carousel', get_template_directory_uri() . '/assest/js/owl.carousel.js', array('jquery'), 'v2.2.1', true );

	//Resize Sensor js
	wp_enqueue_script( 'jquery-ResizeSensor', get_template_directory_uri() . '/assest/js/ResizeSensor.js', array('jquery'), '20182301', true );

	//Theia Sticky Sidebar
	wp_enqueue_script( 'jquery-theia-sticky-sidebar', get_template_directory_uri() . '/assest/js/theia-sticky-sidebar.js', array('jquery'), 'v1.7.0', true );		

	//stellar
	wp_enqueue_script( 'jquery-stellar', get_template_directory_uri() . '/assest/js/stellar.js', array('jquery'), 'v0.6.2', true );

	// Jquery News Ticker 
	wp_enqueue_script( 'jquery-newsTicker-js', get_template_directory_uri() . '/assest/js/jquery.newsTicker.js', array('jquery'), '20151215', true );
	
	//Jquery Nice Scroll
	wp_enqueue_script( 'jquery-nice-scroll', get_template_directory_uri() . '/assest/js/jquery.nicescroll.js', array('jquery'), '3.6.8', true );

	//jquery-meanmenu
	wp_enqueue_script( 'jquery-meanmenu', get_template_directory_uri() . '/assest/js/jquery.meanmenu.js', array('jquery'), 'v2.0.8', true );

	//custom
	wp_enqueue_script( 'mag-lite-custom', get_template_directory_uri() . '/assest/js/custom.js', array(), '20170905', true );	

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'mag_lite_scripts' );


/**
 * Load init.
 */
require_once trailingslashit( get_template_directory() ) . '/inc/init.php';


/*-----------------------------------------------------------------------------------*/
# Meta Box for posts custom fields
/*-----------------------------------------------------------------------------------*/
add_action( 'add_meta_boxes', 'cool_post_register_meta_boxes' );
add_action( 'admin_print_styles-post-new.php', 'cool_metabox_admin_style', 11 );
add_action( 'admin_print_styles-post.php', 'cool_metabox_admin_style', 11 );
add_action( 'save_post', 'cool_post_save_meta_box', 10, 3);
add_action( 'admin_menu' , 'remove_post_custom_fields' );
/*
 * Remove Default custom field meta box
 */
function remove_post_custom_fields() {
	remove_meta_box( 'postcustom' , 'post' , 'normal' ); 
}

function cool_post_register_meta_boxes() {
    add_meta_box( 'meta-box-id', __( 'Article Section', 'textdomain' ), 'cool_post_my_display_callback', 'post','normal', 'high', null);
}
/*
 * Meta Box style for posts
 */
function cool_metabox_admin_style() {
    global $post_type;
    if( 'post' == $post_type )
        wp_enqueue_style( 'cool-metabox-admin-style', get_stylesheet_directory_uri() . '/css/cool_metabox-admin.css' );
}
 
/**
 * Meta box display callback.
 */
function cool_post_my_display_callback( $post ) {
    // Display code/markup goes here. Don't forget to include nonces!
    wp_nonce_field(basename(__FILE__), "meta-box-nonce"); ?>
    <div class="field_content">
	<!-- Start Code -->	
   	<div id="place_type" class="field_type-text">
        <p class="label"><label for="place_type">Name of the Place:</label></p>
        <div class="acf-input-wrap">
        	<input name="place_type" type="text" value="<?php echo get_post_meta($post->ID, "place_type", true); ?>">
        </div>	
    </div>
	<div id="phone_type" class="field_type-text">
        <p class="label"><label for="phone_type">Phone number:</label></p>
        <div class="acf-input-wrap">
        	<input name="phone_type" type="text" value="<?php echo get_post_meta($post->ID, "phone_type", true); ?>">
        </div>	
    </div>
	<div id="video_type" class="field_type-text">
        <p class="label"><label for="video_type">Upload Video:</label></p>
		<p> (  Please Upload Video URL  ) </p>
        <div class="acf-input-wrap">
        	<input name="video_type" type="text" value="<?php echo get_post_meta($post->ID, "video_type", true); ?>">
        </div>	
    </div>
	<!-- End Code -->
	</div>
	<div class="field_content">
    <!-- Start Code -->	
	<div id="address_type" class="field_type-text">
        <p class="label"><label for="address_type">Address:</label></p>
        <div class="acf-input-wrap">
        	<input name="address_type" type="text" value="<?php echo get_post_meta($post->ID, "address_type", true); ?>">
        </div>	
    </div>
	
	<div id="website_type" class="field_type-text">
        <p class="label"><label for="website_type">Website:</label></p>
        <div class="acf-input-wrap">
        	<input name="website_type" type="text" value="<?php echo get_post_meta($post->ID, "website_type", true); ?>">
        </div>	
    </div>
	<!-- End Code -->
	</div>
<?php }
/**
 * Save meta box content.
 */
function cool_post_save_meta_box( $post_id, $post, $update ) {
	global $post;
    // Save logic goes here. Don't forget to include nonce checks!
	if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
        return $post_id;
    if(!current_user_can("edit_post", $post_id))
        return $post_id;
    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;
    $slug = "post";
    if($slug != $post->post_type)
    return $post_id;   
	$meta_box_place_type_value = "";
	$meta_box_address_type_value = "";
	$meta_box_phone_type_value = "";
	$meta_box_website_type_value = "";
	$meta_box_video_type_value = "";

	if(isset($_POST["place_type"]))
    {
        $meta_box_place_type_value = $_POST["place_type"];
    }   
    update_post_meta($post_id, "place_type", $meta_box_place_type_value);
	if(isset($_POST["address_type"]))
    {
        $meta_box_address_type_value = $_POST["address_type"];
    }   
    update_post_meta($post_id, "address_type", $meta_box_address_type_value);
	if(isset($_POST["phone_type"]))
    {
        $meta_box_phone_type_value = $_POST["phone_type"];
    }   
    update_post_meta($post_id, "phone_type", $meta_box_phone_type_value);
	if(isset($_POST["website_type"]))
    {
        $meta_box_website_type_value = $_POST["website_type"];
    }   
    update_post_meta($post_id, "website_type", $meta_box_website_type_value);
	if(isset($_POST["video_type"]))
    {
        $meta_box_video_type_value = $_POST["video_type"];
    }   
    update_post_meta($post_id, "video_type", $meta_box_video_type_value);
	
}



/*-----------------------------------------------------------------------------------*/
# Meta Box for posts multiple images upload
/*-----------------------------------------------------------------------------------*/
add_action('admin_init', 'cool_post_gallery');
add_action('admin_head-post.php', 'cool_post_print_scripts');
add_action('admin_head-post-new.php', 'cool_post_print_scripts');
add_action('save_post', 'cool_post_update_gallery', 10, 3 );
function cool_post_gallery()
{
   add_meta_box('post_gallery',	'Image Uploader','cool_post_print_call','post','normal','core');
}
/**
 * Print the Meta Box scripts
 */

function cool_post_print_call()
{
	global $post;
	$gallery_data = get_post_meta( $post->ID, 'gallery_data', true );
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'noncename_image' );
	?>
	<div id="dynamic_form">
		<div id="field_wrap">
		<?php if ( isset( $gallery_data['image_url'] ) ) {
			for( $i = 0; $i < count( $gallery_data['image_url'] ); $i++ ) {	?>
			<div class="field_row">
				<div class="field_left">
					<div class="form_field">
						<label>Image URL</label>
							<input type="text" class="meta_image_url" name="gallery[image_url][]" value="<?php esc_html_e( $gallery_data['image_url'][$i] ); ?>" />
					</div>
				</div>
				<div class="field_right image_wrap">
					<img src="<?php esc_html_e( $gallery_data['image_url'][$i] ); ?>" height="58" width="98" />
				</div>
				<div class="field_right">
					<input class="button" type="button" value="Choose File" onclick="add_image(this)" />
					<input class="button" type="button" value="Remove" onclick="remove_field(this)" />
				</div>
				<div class="clear" /></div> 
			</div>
			<?php } // endif
		} // endforeach
		?>
		</div>
		<div style="display:none" id="master-row">
			<div class="field_row">
				<div class="field_left">
					<div class="form_field">
						<label>Image URL</label>
							<input class="meta_image_url" value="" type="text" name="gallery[image_url][]" />
					</div>
				</div>
				<div class="field_right image_wrap"></div> 
			<div class="field_right"> 
				<input type="button" class="button" value="Choose File" onclick="add_image(this)" />
				<input class="button" type="button" value="Remove" onclick="remove_field(this)" /> 
			</div>
			<div class="clear"></div>
			</div>
		</div>
		<div id="add_field_row">
			<input class="button" type="button" value="Add new image" onclick="add_field_row();" />
		</div>
	</div>
    <?php }
/**
 * Print styles
 */
function cool_post_print_scripts()
{
	// Check for correct post_type
	global $post;
	if( 'post' != $post->post_type )// here you can set post type name
		return; ?>  
	<style type="text/css">
	.field_left {float:left;}
	.field_right {float:left;margin-left:10px;}
    .clear {clear:both;}
	#dynamic_form {width:815px;}
	#dynamic_form input[type=text] {width:500px;}
	#dynamic_form .field_row {border:1px solid #999;margin-bottom:10px;padding:10px;}
	#dynamic_form label {padding:0 6px;}
	</style>
	<script type="text/javascript">
	function add_image(obj) {
	var parent=jQuery(obj).parent().parent('div.field_row');
	var inputField = jQuery(parent).find("input.meta_image_url");
	tb_show('', 'media-upload.php?TB_iframe=true');
	window.send_to_editor = function(html) {
	var url = jQuery(html).find('img').attr('src');
	inputField.val(url);
	jQuery(parent).find("div.image_wrap").html('<img src="'+url+'" height="48" width="48" />');
	// inputField.closest('p').prev('.awdMetaImage').html('<img height=120 width=120 src="'+url+'"/><p>URL: '+ url + '</p>'); 
	tb_remove();
	};
	return false;  
	}
	function remove_field(obj) {
	var parent=jQuery(obj).parent().parent();
	//console.log(parent)
	parent.remove();
	}
	function add_field_row() {
	var row = jQuery('#master-row').html();
	jQuery(row).appendTo('#field_wrap');
	}
	</script>
<?php }
/**
 * Save post action, process fields
 */
function cool_post_update_gallery( $post_id, $post_object) 
{
	// Doing revision, exit earlier **can be removed**
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )  
	return;
	// Doing revision, exit earlier
	if ( 'revision' == $post_object->post_type )
	return;
	// Verify authenticity
	if (isset($_POST['noncename_image']) && !wp_verify_nonce( $_POST['noncename_image'], plugin_basename( __FILE__ ) ) )
	return;
	// Correct post type
	if (isset($_POST['post_type']) && 'post' != $_POST['post_type'] ) // here you can set post type name
	return;
	if ( !empty($_POST['gallery']) ) {
	// Build array for saving post meta
	$gallery_data = array();
	for ($i = 0; $i < count( $_POST['gallery']['image_url'] ); $i++ ) {
		if ( '' != $_POST['gallery']['image_url'][ $i ] ) {
			$gallery_data['image_url'][]  = $_POST['gallery']['image_url'][ $i ];
		}
	}
	if ( $gallery_data ) 
		update_post_meta( $post_id, 'gallery_data', $gallery_data );
	else 
		delete_post_meta( $post_id, 'gallery_data' );
	} 
	// Nothing received, all fields are empty, delete option
	else 
	{
		delete_post_meta( $post_id, 'gallery_data' );
	}
} 

/*-----------------------------------------------------------------------------------*/
# Meta Box for post Video upload field
/*-----------------------------------------------------------------------------------*/
//add_action('admin_init', 'cool_video_gallery');
add_action('admin_head-post.php', 'cool_post_video_scripts');
add_action('admin_head-post-new.php', 'cool_post_video_scripts');
add_action('save_post', 'cool_video_update_gallery', 10, 3 );
add_action( 'post_edit_form_tag', 'update_edit_form' );
function update_edit_form() {
    echo ' enctype="multipart/form-data"';
}

/**
 * Print the video Meta Box scripts
 */


/**
 * Print styles
 */
function cool_post_video_scripts()
{
	// Check for correct post_type
	global $post;
	if( 'post' != $post->post_type )// here you can set post type name
		return; ?>  
	<style type="text/css">
	.description{font-weight: bold;}
	</style>
<?php }
/**
 * Save post action, process fields
 */


function __filter_rewrite_rules( $rules )
{
    $_rules = array();
    foreach ( $rules as $rule => $rewrite )
        $_rules[ str_replace( 'attachment/', 'media/', $rule  ) ] = $rewrite;
    return $_rules;
}
add_filter( 'rewrite_rules_array', '__filter_rewrite_rules' );

function wpse65629_change_row_title( $url, $post_id, $context )
{
    if ( 'edit-post' !== get_current_screen()->id )
        return;

    return '#';
}
//add_filter( 'get_edit_post_link', 'wpse65629_change_row_title', 10, 3 );

//remove comments from pages
add_action('init', 'remove_comment_support', 100);

function remove_comment_support() {
remove_post_type_support( 'page', 'comments' );
}
// numbered pagination
function pagination($pages = '', $range = 4)
{  
     $showitems = ($range * 2)+1;  
 
     global $paged;
     if(empty($paged)) $paged = 1;
 
     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }   
 
     if(1 != $pages)
     {
         echo "<div class=\"pagination\"><span>Page ".$paged." of ".$pages."</span>";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo; First</a>";
         if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo; Previous</a>";
 
         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".$i."</a>";
             }
         }
 
         if ($paged < $pages && $showitems < $pages) echo "<a href=\"".get_pagenum_link($paged + 1)."\">Next &rsaquo;</a>";  
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>Last &raquo;</a>";
         echo "</div>\n";
     }
}