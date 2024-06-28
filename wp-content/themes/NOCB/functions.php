<?php
/**
 * nocb functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package nocb
 */
add_action( 'wp_enqueue_scripts', 'hello_elementor__child_theme_enqueue_styles' );
function hello_elementor__child_theme_enqueue_styles() {
	wp_dequeue_style("styles");
	// wp_enqueue_style( 'child-style', get_template_directory_uri().'/style.css' );
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
		get_template_directory_uri() . '/style.css',
		array('parent-style'),
		wp_get_theme()->get('Version')
	);
}
if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function nocb_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on nocb, use a find and replace
		* to change 'nocb' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'nocb', get_template_directory() . '/languages' );
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
	add_theme_support( 'post-thumbnails' );
	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'nocb' ),
		)
	);
	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);
	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'nocb_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);
	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );
	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}


/***** Add Theme Support for post formats ******/
add_theme_support('post-formats', array('link', 'quote', 'gallery'));
// add_theme_support( 'post-thumbnails' );



add_action( 'after_setup_theme', 'nocb_setup' );
add_image_size( 'article-detail-thumb', 875, 548, true);
add_image_size( 'top-stories-thumb', 400, 305, true);
add_image_size( 'homepage-categories-thumb', 304, 162, true);
add_image_size( 'article-list-thumb', 304, 175, true);
add_image_size( 'article-list-thumb-mobile', 354, 200, true);
add_image_size( 'featured-contents3a-thumb', 200, 160);
add_image_size( 'featured-contents3c-thumb', 408, 265);
add_image_size( 'conference-list-thumb', 280, 125);
add_image_size( 'category-list-thumb', 200 ,142);
add_image_size( 'webinar-list-thumb', 'auto' ,150);
add_image_size( 'search-list-thumb', 120, 110);
add_image_size( 'top-stories-right-sidebar', 50, 50);
add_image_size( 'digital=edition-thumb', 270, 370);
/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function nocb_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'nocb_content_width', 640 );
}
add_action( 'after_setup_theme', 'nocb_content_width', 0 );
/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function nocb_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Top Billboard', 'nocb' ),
			'id'            => 'top-billboard',
			'description'   => esc_html__( 'Add widgets here.', 'nocb' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);	
	register_sidebar(
		array(
			'name'          => esc_html__( 'Mobile Pop-out Menu Area', 'nocb' ),
			'id'            => 'mobile-pop-out-menu-area',
			'description'   => esc_html__( 'Add widgets here.', 'nocb' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);	
	register_sidebar( array(
		'name' => __( 'Content Inline Ads 1', 'nocb' ),
		'id' => 'content-inline-ad-1',
		'description'   => esc_html__( 'Add widgets here.', 'nocb' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title' => '<h2 class="widget-title">',
		'after_title' => '</h2>',
		)
    );
	register_sidebar( array(
		'name' => __( 'Content Inline Ads 2', 'nocb' ),
		'id' => 'content-inline-ad-2',
		'description'   => esc_html__( 'Add widgets here.', 'nocb' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title' => '<h2 class="widget-title">',
		'after_title' => '</h2>',
		)
    );
	register_sidebar( array(
		'name' => __( 'Category inline Ad Row 1 Mobile', 'nocb' ),
		'id' => 'category-inline-mobile-ad-row1',
		'description'   => esc_html__( 'Add widgets here.', 'nocb' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title' => '<h2 class="widget-title">',
		'after_title' => '</h2>',
		)
    );
	register_sidebar( array(
		'name' => __( 'Category inline Ad Row 2 Mobile', 'nocb' ),
		'id' => 'category-inline-mobile-ad-row2',
		'description'   => esc_html__( 'Add widgets here.', 'nocb' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title' => '<h2 class="widget-title">',
		'after_title' => '</h2>',
		)
    );
	register_sidebar(
		array(
			'name'          => esc_html__( 'General Page Right Sidebar Area', 'nocb' ),
			'id'            => 'right-sidebar-area',
			'description'   => esc_html__( 'Add widgets here.', 'nocb' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);	
	register_sidebar(
		array(
			'name'          => esc_html__( 'Mobile Article Right Sidebar Area ', 'nocb' ),
			'id'            => 'article-right-sidebar-mobile',
			'description'   => esc_html__( 'Add widgets here.', 'nocb' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);	
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sponsored Article right sidebar', 'nocb' ),
			'id'            => 'sponsored-article-sidebar-area',
			'description'   => esc_html__( 'Add widgets here.', 'nocb' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);		
	register_sidebar(
		array(
			'name'          => esc_html__( 'Event Right Sidebar', 'nocb' ),
			'id'            => 'event-right-sidebar',
			'description'   => esc_html__( 'Add widgets here.', 'nocb' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Reader Ranking Sidebar', 'nocb' ),
			'id'            => 'reader-ranking-sidebar-area',
			'description'   => esc_html__( 'Add widgets here.', 'nocb' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
    register_sidebar(
		array(
			'name'          => esc_html__( 'Sponsored Ad After Content', 'nocb' ),
			'id'            => 'sponsored-ad-after-content',
			'description'   => esc_html__( 'Add widgets here.', 'nocb' ),
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Article Ad After Content', 'nocb' ),
			'id'            => 'article-ad-after-content',
			'description'   => esc_html__( 'Add widgets here.', 'nocb' ),
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Event Right Side Ad Area', 'nocb' ),
			'id'            => 'event-right-side-ad-area',
			'description'   => esc_html__( 'Add widgets here.', 'nocb' ),
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
		)
	);
	// register_sidebar(
	// 	array(
	// 		'name'          => esc_html__( 'Digital Edition Content', 'nocb' ),
	// 		'id'            => 'digital-edition-content',
	// 		'description'   => esc_html__( 'Add widgets here.', 'nocb' ),
	// 		'before_title'  => '<h2 class="widget-title">',
	// 		'after_title'   => '</h2>',
	// 		'before_widget' => '<div id="%1$s" class="widget %2$s">',
	// 		'after_widget'  => '</div>',
	// 	)
	// );
	// register_sidebar(
	// 	array(
	// 		'name'          => esc_html__( 'Digital Edition Archive Content ', 'nocb' ),
	// 		'id'            => 'digital-edition-archive-content',
	// 		'description'   => esc_html__( 'Add widgets here.', 'nocb' ),
	// 		'before_title'  => '<h2 class="widget-title">',
	// 		'after_title'   => '</h2>',
	// 		'before_widget' => '<div id="%1$s" class="widget %2$s">',
	// 		'after_widget'  => '</div>',
	// 	)
	// );
	// register_sidebar(
	// 	array(
	// 		'name'          => esc_html__( 'Project Center - Center Column ', 'nocb' ),
	// 		'id'            => 'project-center-center-column',
	// 		'description'   => esc_html__( 'Add widgets here.', 'nocb' ),
	// 		'before_title'  => '<h2 class="widget-title">',
	// 		'after_title'   => '</h2>',
	// 		'before_widget' => '<div id="%1$s" class="widget %2$s">',
	// 		'after_widget'  => '</div>',
	// 	)
	// );
}
add_action( 'widgets_init', 'nocb_widgets_init' );
/**
 * Enqueue scripts and styles.
 */
function nocb_scripts() {
	$current_time = date('h.i.s.m.d.Y');
	wp_enqueue_style( 'nocb-cdn', get_template_directory_uri() . '/css/bootstrap.min.css', array(), $current_time );
	wp_enqueue_style( 'nocb-layout', get_template_directory_uri() . '/css/layout.css', array(), $current_time );
	wp_enqueue_style( 'nocb-theme', get_template_directory_uri() . '/css/theme.css', array(), $current_time );
	wp_enqueue_style( 'nocb-header', get_template_directory_uri() . '/css/header.css', array(), $current_time );
	wp_enqueue_style( 'nocb-author-page', get_template_directory_uri() . '/css/author-page.css', array(), $current_time );
	wp_enqueue_style( 'nocb-article', get_template_directory_uri() . '/css/article-page.css', array(), $current_time );
	wp_enqueue_style( 'nocb-category', get_template_directory_uri() . '/css/category-page.css', array(), $current_time );
	wp_enqueue_style( 'nocb-trends-and-news', get_template_directory_uri() . '/css/trends-and-news.css', array(), $current_time );
	wp_enqueue_style( 'nocb-event', get_template_directory_uri() . '/css/event.css', array(), $current_time );
	wp_enqueue_style( 'nocb-footer', get_template_directory_uri() . '/css/footer.css', array(), $current_time );
	wp_enqueue_style( 'nocb-mobile', get_template_directory_uri() . '/css/mobile.css', array(), $current_time );
	wp_enqueue_style( 'nocb-ipad', get_template_directory_uri() . '/css/ipad.css', array(), $current_time );
	wp_enqueue_style( 'nocb-slick', get_template_directory_uri() . '/css/slick.css', array(), $current_time );
	wp_enqueue_style( 'nocb-slick-theme', get_template_directory_uri() . '/css/slick-theme.css', array(), $current_time );
	wp_enqueue_style( 'nocb-jquery.fancybox', get_template_directory_uri() . '/css/jquery.fancybox.css', array(), $current_time );
	wp_enqueue_style( 'nocb-jquery-ui', get_template_directory_uri() . '/css/jquery-ui.css', array(), $current_time );
	wp_enqueue_script( 'nocb-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION.".".$current_time, true );
	wp_enqueue_script( 'nocb-bootstrap-bundle', get_template_directory_uri() . '/js/bootstrap.bundle.min.js', array(), _S_VERSION.".".$current_time, true );
	wp_enqueue_script( 'nocb-simple-js', get_template_directory_uri() . '/js/simple.js', array(), _S_VERSION.".".$current_time, true );
	wp_enqueue_script( 'nocb-slick-min-js', get_template_directory_uri() . '/js/slick.min.js', array(), _S_VERSION.".".$current_time, true );
	wp_enqueue_script( 'nocb-gallery-js', get_template_directory_uri() . '/js/gallery.js', array(), _S_VERSION.".".$current_time, true );
    wp_enqueue_script( 'nocb-jquery-ui', get_template_directory_uri() . '/js/jquery-ui.js', array(), _S_VERSION.".".$current_time, true );
}
add_action( 'wp_enqueue_scripts', 'nocb_scripts' );
function nocb_child_scripts() {
	$current_time = date('h.i.s.m.d.Y');
	wp_enqueue_style( 'nocb-style', get_stylesheet_directory_uri() . '/css/nocb.css', array(), $current_time );
	wp_enqueue_style( 'global-ipad-style', get_stylesheet_directory_uri() . '/css/global-ipad.css', array(), $current_time );
	wp_enqueue_style( 'nocb-mobile-style', get_stylesheet_directory_uri() . '/css/nocb-mobile.css', array(), $current_time );
	wp_enqueue_script( 'nocb-js', get_stylesheet_directory_uri() . '/js/nocb.js', array(), _S_VERSION.".".$current_time, true );
	wp_enqueue_script( 'nocb-slideshow-js', get_stylesheet_directory_uri() . '/js/slideshow/jquery.ad-gallery.js', array(), _S_VERSION.".".$current_time, true );
}
add_action( 'wp_enqueue_scripts', 'nocb_child_scripts' );
// Useful global constants.
define( 'nocb_VERSION', '0.1.4' );
define( 'nocb_TEMPLATE_URL', get_template_directory_uri() );
define( 'nocb_PATH', get_template_directory() . '/' );
define( 'nocb_INC', nocb_PATH . 'includes/' );
define( 'nocb_panel', nocb_PATH . 'panel/' );

// Category page
require_once nocb_INC . 'ajaxLoader/ajax-category-mobile.php';
// Tag page
require_once nocb_INC . 'ajaxLoader/ajax-tag-mobile.php';
// Sponsored Content Page
require_once nocb_INC . 'ajaxLoader/ajax-sponsored-content-mobile.php';
// Author page for mobile
require_once nocb_INC . 'ajaxLoader/ajax-author-mobile.php';
// Load more events
require_once nocb_INC . 'eventsmoreload/eventsload.php';
//Get functions
include (nocb_INC . 'breadcrumbs.php');
include (nocb_INC . 'pagenavi.php');
include (nocb_INC . 'functions/theme-functions.php');
include (nocb_INC . 'functions/banners.php');
include (nocb_INC . 'functions/home-cat-scroll.php');
include (nocb_INC . 'functions/home-cats-wide.php');
include (nocb_panel . 'custom-slider.php');
// Custom breadcrumb function
function asentech_custom_breadcrumb() {
	// to get current-page-url
	global $wp;
	$url =  home_url( $wp->request );
	$values = parse_url($url);
	$path = explode('/',$values['path']);
	// to get current-path
	$category_page = array_search("category",$path);
	$tag_page = array_search("tag",$path);
	$issue_page = array_search("issues",$path);
	$author_page = array_search("author",$path);
	$power_list_page = array_search("power-lists",$path);

	// $path_1 = $_SERVER['REQUEST_URI'];
	// $path_1 = implode('/',$path_1);
	// print($path_1)."path-1";
	// $power_list_page = array_search("power-lists",$path);

	$event_page = array_search("events",$path);
	$webinars_page = array_search("events && webinars",$path);
	$event_detail_page = array_search("event",$path);
	$sponsored_content = array_search("sponsored-contents",$path);
	$sponsored_detail_page = array_search("sponsored_content",$path);
	$digital_guide = array_search("digital_guide",$path);

	if(is_singular('post')){
		// $wpseo_primary_term = new WPSEO_Primary_Term( 'category', get_the_id() );
		// $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
		$wpseo_primary_term = get_primary_category_id(get_the_id());
		$term = get_term( $wpseo_primary_term );
		$term_name = $term->name;
		$term_id = $term->term_id;
		$parent_id = $term->parent;
		$parentCatList = get_term($parent_id);
		$parent_name = $parentCatList->name;
		$parent_permalink = get_category_link($parentCatList);
		$permalink = get_category_link($term_id);
		$title = get_the_title();

		// get the first selected category from the post.
        $categories = get_the_category(get_the_id());
        $categories = array_shift(array_slice($categories,0,1));
        $category_id =  $categories->term_id;
        $category_name =  $categories->name;
        $category_slug =  $category->slug;

        if($wpseo_primary_term){
            $term_name = $term->name;
            $permalink = get_category_link($term_id);
        }else{
            $term_name = $category_name;
            $permalink = get_category_link($category_id);  
        }


		if($term_name == "Power List"){
		?>
			<a href="/">Home</a><span>></span><a href="/power-lists/">Power List</a><span>></span><p><?php print $title; ?></p>
		<?php }elseif($parent_id){ ?>
			<a href="/">Home</a><span>></span><a href="<?php print $parent_permalink; ?>"><?php print $parent_name; ?></a><span>></span><a href="<?php print $permalink; ?>"><?php print $term_name; ?></a><span>></span><p><?php print $title; ?></p>
		<?php }
		else { ?>
			<a href="/">Home</a><span>></span><a href="<?php print $permalink; ?>"><?php print $term_name; ?></a><span>></span><p><?php print $title; ?></p>
		<?php } 
	}elseif(is_singular('product')){
		// $wpseo_primary_term = new WPSEO_Primary_Term( 'product_category', get_the_id() );
		// $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
		$wpseo_primary_term = get_primary_category_id(get_the_id());
		$term = get_term( $wpseo_primary_term );
		$term_name = $term->name;
		$term_id = $term->term_id;
		$parent_id = $term->parent;
		$parentCatList = get_term($parent_id);
		$parent_name = $parentCatList->name;
		$parent_permalink = get_category_link($parentCatList);
		$permalink = get_category_link($term_id);
		$title = get_the_title();

		// get the first selected category from the post.
		$categories = get_the_terms( get_the_id(), 'product_category' );
        $categories = array_shift(array_slice($categories,0,1));
        $category_id =  $categories->term_id;
        $category_name =  $categories->name;
        $category_slug =  $category->slug;

        if($wpseo_primary_term){
            $term_name = $term->name;
            $permalink = get_category_link($term_id);
        }else{
            $term_name = $category_name;
            $permalink = get_category_link($category_id);  
        }

		if($parent_id){ ?>
			<a href="/">Home</a><span>></span><a href="<?php print $parent_permalink; ?>"><?php print $parent_name; ?></a><span>></span><a href="<?php print $permalink; ?>"><?php print $term_name; ?></a><span>></span><p><?php print $title; ?></p>
		<?php }
		else { ?>
			<a href="/">Home</a><span>></span><a href="<?php print $permalink; ?>"><?php print $term_name; ?></a><span>></span><p><?php print $title; ?></p>
		<?php } 
	}elseif(is_singular('digital_guides')){
		// $wpseo_primary_term = new WPSEO_Primary_Term( 'digital_guide', get_the_id() );
		// $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
		$wpseo_primary_term = get_primary_category_id(get_the_id());
		$term = get_term( $wpseo_primary_term );
		$term_name = $term->name;
		$term_id = $term->term_id;
		$parent_id = $term->parent;
		$parentCatList = get_term($parent_id);
		$parent_name = $parentCatList->name;
		$parent_permalink = get_category_link($parentCatList);
		$permalink = get_category_link($term_id);
		$title = get_the_title();

		// get the first selected category from the post.
		$categories = get_the_terms( get_the_id(), 'digital_guide' );
        $categories = array_shift(array_slice($categories,0,1));
        $category_id =  $categories->term_id;
        $category_name =  $categories->name;
        $category_slug =  $category->slug;

        if($wpseo_primary_term){
            $term_name = $term->name;
            $permalink = get_category_link($term_id);
        }else{
            $term_name = $category_name;
            $permalink = get_category_link($category_id);  
        }

		if($parent_id){ ?>
			<a href="/">Home</a><span>></span><a href="<?php print $parent_permalink; ?>"><?php print $parent_name; ?></a><span>></span><a href="<?php print $permalink; ?>"><?php print $term_name; ?></a><span>></span><p><?php print $title; ?></p>
		<?php }
		else { ?>
			<a href="/">Home</a><span>></span><a href="<?php print $permalink; ?>"><?php print $term_name; ?></a><span>></span><p><?php print $title; ?></p>
		<?php } 
	}elseif($category_page){
		$termObj = get_queried_object();
		$category_slug = $termObj->slug;
		$term = get_term_by('slug', $category_slug, 'category');
		$term_id = $term->term_id;
		$parent_id = $term->parent;
		$parentCatList = get_term($parent_id);
		$parent_name = $parentCatList->name;
		$parent_permalink = get_category_link($parentCatList);
		$catObj = get_category_by_slug($category_slug);
		$category_name = $catObj->name;
		$termObj = get_queried_object();
		$category_slug = $termObj->slug;
		if($parent_id){ ?>
			<a href="/">Home</a><span>></span><a href="<?php print $parent_permalink; ?>"><?php print $parent_name; ?></a><span>></span><span class="name"><?php print $category_name; ?></span>
		<?php } 
		else { ?>
			<a href="/">Home</a><span>></span><span class="name"><?php print $category_name; ?></span>
		<?php } 
	}
	elseif($tag_page){
		$termObj = get_queried_object();
		$category_slug = $termObj->slug;
		$term = get_term_by('slug', $category_slug, 'post_tag');
		$term_id = $term->term_id;
		$term_name = $term->name;
		?>
        	<a href="/">Home</a><span>></span><span class="name"><?php print $term_name; ?></span> 
	<?php }
	elseif($event_page){ ?>
		<a href="/">Home</a><span>></span>Events<span>
	<?php }
	elseif($sponsored_content){ ?>
		<a href="/">Home</a><span>></span>Sponsored Contents<span>
	<?php }
	elseif($issue_page){
		global $wp_query;
		$term = $wp_query->get_queried_object();
		$taxtitle = $term->name;
	?>
		<a href="/">Home</a><span>></span><span class="name"><?php print $taxtitle; ?></span> 
	<?php }
	elseif($event_page){ ?>
		<a href="/">Home</a><span>></span>Events<span>
	<?php }
	elseif($sponsored_content){ ?>
		<a href="/">Home</a><span>></span>Sponsored Contents<span>
	<?php }
	elseif($sponsored_detail_page){ 
		// $wpseo_primary_term = new WPSEO_Primary_Term( 'sponsoredcontent_category', get_the_id() );
		// $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
		$wpseo_primary_term = get_primary_category_id(get_the_id());
		$term = get_term( $wpseo_primary_term );
		$term_name = $term->name;
		$parent_id = $term->parent;
		$parentCatList = get_term($parent_id);
		$parent_name = $parentCatList->name;
		print_r($term_name);
		$parent_permalink = get_category_link($parentCatList);
		$permalink = get_category_link($term);
		$title = get_the_title();

		// get the first selected category from the post.
        $categories = get_the_terms( get_the_id(), 'category' );
		if($categories){
			$categories = array_shift(array_slice($categories,0,1));
			$category_id =  $categories->term_id;
			$category_name =  $categories->name;
			$category_slug =  $category->slug;
		}

        if($wpseo_primary_term){
            $term_name = $term->name;
            $permalink = get_category_link($term_id);
        }else{
            $term_name = $category_name;
            $permalink = get_category_link($category_id);  
        }


		if($parent_id){ ?>
			<a href="/">Home</a><span>></span><a href="<?php print $parent_permalink; ?>"><?php print $parent_name; ?></a><span>></span><a href="<?php print $permalink; ?>"><?php print $term_name; ?></a><span>></span><p><?php print $title; ?></p>
		<?php } 
			else { 
				if($term_name){?>
					<a href="/">Home</a><span>></span><a href="/sponsored-contents">Sponsored content</a><span>></span><p><?php print $title; ?></p>
				<?php } else {?>
					<a href="/">Home</a><span>></span><a href="/sponsored-contents">Sponsored content</a><span>></span><p><?php print $title; ?></p>
				<?php } 
			 }
	}
	elseif($event_detail_page){
		$event_id = get_the_ID();
		$term_list = wp_get_post_terms($event_id, Tribe__Events__Main::TAXONOMY);
		$event_cats = '';
		foreach ($term_list as $term_single) {
			$single_term_id = $single_term_link = '';
			$single_term_id = $term_single->term_id;
			$single_term_link = get_term_link(
				$single_term_id,
				Tribe__Events__Main::TAXONOMY
			);
			$event_cats_name = $term_single->name;
			$event_cats_link = get_category_link($term_single);
		}
		?>
			<a href="/">Home</a><span>></span><a href="/events">Events</a><span>></span><?php
			if ($event_cats_name) { ?>
				<a href="<?php echo $event_cats_link; ?>"><?php echo $event_cats_name; ?></a><span>></span><?php } the_title(); 
			?>
	<?php }
	elseif(is_page()){ ?>
		<a href="/">Home</a><span>></span><?php the_title(); ?>
	<?php }
	elseif($webinars_page){ ?>
		<a href="/">Home</a><span>></span>Webinars
	<?php }elseif($digital_guide){ ?>
		<a href="/">Home</a><span>></span><?php the_title(); ?>
	<?php }
	elseif($author_page){ 
		// $author_id = get_the_author_meta("ID");
		global $wp;
		$url =  home_url( $wp->request );
		$url_param = end(explode('/', $url));
		$author_slug = $url_param ;
		$author = get_user_by('slug', $author_slug);
		$author_id = $author->ID;
		$first_name = get_the_author_meta( 'first_name' , $author_id );
		$last_name = get_the_author_meta( 'last_name' , $author_id );
		$display_name = get_the_author_meta( 'display_name' , $author_id );
		if (trim($first_name) != '' && trim($last_name) != '') {
			$author_name = $first_name . ' ' . $last_name;
		} else {
			$author_name =  $display_name;
		}
		?>
		<a href="/">Home</a><span>></span><?php  print $author_name; ?><span>
	<?php }

}
// add_action( 'init', 'asentech_custom_breadcrumb' );
add_action( 'plugins_loaded', 'asentech_custom_breadcrumb' );

require_once nocb_INC . 'related-contents.php';
// Reader Ranking page hide autocomplete_solr JS file 
function get_template_name () {
    // List all available template names for current theme
    $available_templates = wp_get_theme()->get_page_templates();

    // Get filename of page template we are on
    $template_filename = basename(get_page_template());

    // Return the template name for the currently active file
    return $available_templates[$template_filename];
}
function wpsolr_pro_enqueue_script() {
	$template_slug = get_page_template_slug();
	$template_name = basename($template_slug, '.php');
	if ( $template_name == "template-readerranking" )
	{
		wp_dequeue_script( 'autocomplete', plugins_url( 'js/autocomplete_solr.js', __FILE__ ),
		$is_autocomplete ? [ 'solr_auto_js1', 'urljs' ] : [ 'urljs' ],
		WPSOLR_PLUGIN_VERSION, true );
	}
	
}
add_action( 'wp_enqueue_scripts', 'wpsolr_pro_enqueue_script' ,100);
function wp_is_ipad() {
    $ipad = strpos($_SERVER['HTTP_USER_AGENT'],'iPad');
    if ($ipad === false) {
        return false;
    } else {
        return true;
    }
}
function set_homepage_template() {
// Set the template name you want to assign to the homepage
$newTemplate = 'templates/template-homepage.php';
// Get the ID of the homepage
$homepageID = get_option('page_on_front');
// Update the post meta value for the homepage
update_post_meta($homepageID, '_wp_page_template', $newTemplate);
}
add_action('init', 'set_homepage_template');
function tie_get_feeds( $feed , $number = 10 ){
	include_once(ABSPATH . WPINC . '/feed.php');

	$rss = @fetch_feed( $feed );
	if (!is_wp_error( $rss ) ){
		$maxitems = $rss->get_item_quantity($number);
		$rss_items = $rss->get_items(0, $maxitems);
	}
	if ($maxitems == 0) {
		$out = "<ul><li>". __( 'No items.', 'tie' )."</li></ul>";
	}else{
		$out = "<ul>";
		foreach ( $rss_items as $item ) :
			$out .= '<li><a href="'. esc_url( $item->get_permalink() ) .'" title="'.  __( "Posted ", "tie" ).$item->get_date("j F Y | g:i a").'">'. esc_html( $item->get_title() ) .'</a></li>';
		endforeach;
		$out .='</ul>';
	}
	return $out;
}
/*-----------------------------------------------------------------------------------*/
# Get the post time
/*-----------------------------------------------------------------------------------*/
function tie_get_time(){
	global $post ;
	if( tie_get_option( 'time_format' ) == 'none' ){
		return false;
	}elseif( tie_get_option( 'time_format' ) == 'modern' ){
		$to = current_time('timestamp'); //time();
		$from = get_the_time('U') ;

		$diff = (int) abs($to - $from);
		if ($diff <= 3600) {
			$mins = round($diff / 60);
			if ($mins <= 1) {
				$mins = 1;
			}
			$since = sprintf(_n('%s min', '%s mins', $mins), $mins) .' '. __( 'ago' , 'tie' );
		}
		else if (($diff <= 86400) && ($diff > 3600)) {
			$hours = round($diff / 3600);
			if ($hours <= 1) {
				$hours = 1;
			}
			$since = sprintf(_n('%s hour', '%s hours', $hours), $hours) .' '. __( 'ago' , 'tie' );
		}
		elseif ($diff >= 86400) {
			$days = round($diff / 86400);
			if ($days <= 1) {
				$days = 1;
				$since = sprintf(_n('%s day', '%s days', $days), $days) .' '. __( 'ago' , 'tie' );
			}
			elseif( $days > 29){
				$since = get_the_time(get_option('date_format'));
			}
			else{
				$since = sprintf(_n('%s day', '%s days', $days), $days) .' '. __( 'ago' , 'tie' );
			}
		}
	}else{
		$since = get_the_time(get_option('date_format'));
		if (tie_get_option( 'show_time' )) {
			$since = $since.'</span>'."\n".'<span class="tie-time">'.get_the_time();
		}
	}
	echo '<span class="tie-date">'.$since.'</span>';
}
// function to display number of posts.
function tie_views( $postID = '' ){
	if( !tie_get_option( 'post_views' ) ) return false;

	global $post;
	if( empty($postID) ) $postID = $post->ID ;
	
    $count_key = 'tie_views';
    $count = get_post_meta($postID, $count_key, true);
	$count = @number_format($count);
    if( empty($count) ){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, 0 );
        $count = 0;
    }
    return '<span class="post-views">'.$count.' '.__( 'Views' , 'tie' ).'</span> ';
}
/*-----------------------------------------------------------------------------------*/
# Get Theme Options
/*-----------------------------------------------------------------------------------*/
function tie_get_option( $name ) {
	$get_options = get_option( 'tie_options' );

	if( !empty( $get_options[$name] ))
		return $get_options[$name];

	return false ;
}
?>