<?php
/**
 * neworleanscitybusiness functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package neworleanscitybusiness
 */

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
function neworleanscitybusiness_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on neworleanscitybusiness, use a find and replace
		* to change 'neworleanscitybusiness' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'neworleanscitybusiness', get_template_directory() . '/languages' );

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
			'menu-1' => esc_html__( 'Primary', 'neworleanscitybusiness' ),
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
			'neworleanscitybusiness_custom_background_args',
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
add_action( 'after_setup_theme', 'neworleanscitybusiness_setup' );


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

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function neworleanscitybusiness_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'neworleanscitybusiness_content_width', 640 );
}
add_action( 'after_setup_theme', 'neworleanscitybusiness_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function neworleanscitybusiness_widgets_init() {
	// mobile header region starts here
	register_sidebar(
		array(
			'name'          => esc_html__( 'Mobile Pop-out Menu Area', 'neworleanscitybusiness' ),
			'id'            => 'mobile-pop-out-menu-area',
			'description'   => esc_html__( 'Add widgets here.', 'neworleanscitybusiness' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
	// mobile header region ends here
	// footer-region starts here	
	register_sidebar( array(
		'name' => __( 'Content Inline Ads 1', 'neworleanscitybusiness' ),
		'id' => 'content-inline-ad-1',
		'description'   => esc_html__( 'Add widgets here.', 'neworleanscitybusiness' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title' => '<h2 class="widget-title">',
		'after_title' => '</h2>',
		)
    );
	register_sidebar( array(
		'name' => __( 'Content Inline Ads 2', 'neworleanscitybusiness' ),
		'id' => 'content-inline-ad-2',
		'description'   => esc_html__( 'Add widgets here.', 'neworleanscitybusiness' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title' => '<h2 class="widget-title">',
		'after_title' => '</h2>',
		)
    );
	register_sidebar( array(
		'name' => __( 'Category inline Ad Row 1 Mobile', 'neworleanscitybusiness' ),
		'id' => 'category-inline-mobile-ad-row1',
		'description'   => esc_html__( 'Add widgets here.', 'neworleanscitybusiness' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title' => '<h2 class="widget-title">',
		'after_title' => '</h2>',
		)
    );
	register_sidebar( array(
		'name' => __( 'Category inline Ad Row 2 Mobile', 'neworleanscitybusiness' ),
		'id' => 'category-inline-mobile-ad-row2',
		'description'   => esc_html__( 'Add widgets here.', 'neworleanscitybusiness' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title' => '<h2 class="widget-title">',
		'after_title' => '</h2>',
		)
    );
	register_sidebar(
		array(
			'name'          => esc_html__( 'General Page Right Sidebar Area', 'neworleanscitybusiness' ),
			'id'            => 'right-sidebar-area',
			'description'   => esc_html__( 'Add widgets here.', 'neworleanscitybusiness' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Mobile Article Page Right Sidebar Area', 'neworleanscitybusiness' ),
			'id'            => 'article-right-sidebar-mobile',
			'description'   => esc_html__( 'Add widgets here.', 'neworleanscitybusiness' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sponsored Article right sidebar', 'neworleanscitybusiness' ),
			'id'            => 'sponsored-article-sidebar-area',
			'description'   => esc_html__( 'Add widgets here.', 'neworleanscitybusiness' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);		
	register_sidebar(
		array(
			'name'          => esc_html__( 'Event Right Sidebar', 'neworleanscitybusiness' ),
			'id'            => 'event-right-sidebar',
			'description'   => esc_html__( 'Add widgets here.', 'neworleanscitybusiness' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Reader Ranking Sidebar', 'neworleanscitybusiness' ),
			'id'            => 'reader-ranking-sidebar-area',
			'description'   => esc_html__( 'Add widgets here.', 'neworleanscitybusiness' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sponsored Ad After Content', 'neworleanscitybusiness' ),
			'id'            => 'sponsored-ad-after-content',
			'description'   => esc_html__( 'Add widgets here.', 'neworleanscitybusiness' ),
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Content Top', 'neworleanscitybusiness' ),
			'id'            => 'content-top',
			'description'   => esc_html__( 'Add widgets here.', 'neworleanscitybusiness' ),
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Event Right Side Ad Area', 'neworleanscitybusiness' ),
			'id'            => 'event-right-side-ad-area',
			'description'   => esc_html__( 'Add widgets here.', 'neworleanscitybusiness' ),
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
		)
	);

}
add_action( 'widgets_init', 'neworleanscitybusiness_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function neworleanscitybusiness_scripts() {

	$current_time = date('h.i.s.m.d.Y');

	wp_enqueue_style( 'neworleanscitybusiness-cdn', get_template_directory_uri() . '/css/bootstrap.min.css', array(), $current_time );
	wp_enqueue_style( 'neworleanscitybusiness-layout', get_template_directory_uri() . '/css/layout.css', array(), $current_time );
	wp_enqueue_style( 'neworleanscitybusiness-theme', get_template_directory_uri() . '/css/theme.css', array(), $current_time );
	wp_enqueue_style( 'neworleanscitybusiness-header', get_template_directory_uri() . '/css/header.css', array(), $current_time );
	wp_enqueue_style( 'neworleanscitybusiness-author-page', get_template_directory_uri() . '/css/author-page.css', array(), $current_time );
	wp_enqueue_style( 'neworleanscitybusiness-article', get_template_directory_uri() . '/css/article-page.css', array(), $current_time );
	wp_enqueue_style( 'neworleanscitybusiness-category', get_template_directory_uri() . '/css/category-page.css', array(), $current_time );
	wp_enqueue_style( 'neworleanscitybusiness-trends-and-news', get_template_directory_uri() . '/css/trends-and-news.css', array(), $current_time );
	wp_enqueue_style( 'neworleanscitybusiness-event', get_template_directory_uri() . '/css/event.css', array(), $current_time );
	wp_enqueue_style( 'neworleanscitybusiness-footer', get_template_directory_uri() . '/css/footer.css', array(), $current_time );
	wp_enqueue_style( 'neworleanscitybusiness-mobile', get_template_directory_uri() . '/css/mobile.css', array(), $current_time );
	wp_enqueue_style( 'neworleanscitybusiness-ipad', get_template_directory_uri() . '/css/ipad.css', array(), $current_time );
	wp_enqueue_style( 'neworleanscitybusiness-slick', get_template_directory_uri() . '/css/slick.css', array(), $current_time );
	wp_enqueue_style( 'neworleanscitybusiness-slick-theme', get_template_directory_uri() . '/css/slick-theme.css', array(), $current_time );
	wp_enqueue_style( 'neworleanscitybusiness-jquery.fancybox', get_template_directory_uri() . '/css/jquery.fancybox.css', array(), $current_time );
	wp_enqueue_style( 'neworleanscitybusiness-jquery-ui', get_template_directory_uri() . '/css/jquery-ui.css', array(), $current_time );

	wp_enqueue_script( 'neworleanscitybusiness-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION.".".$current_time, true );
	wp_enqueue_script( 'neworleanscitybusiness-bootstrap-bundle', get_template_directory_uri() . '/js/bootstrap.bundle.min.js', array(), _S_VERSION.".".$current_time, true );
	wp_enqueue_script( 'neworleanscitybusiness-simple-js', get_template_directory_uri() . '/js/simple.js', array(), _S_VERSION.".".$current_time, true );
	wp_enqueue_script( 'neworleanscitybusiness-slick-min-js', get_template_directory_uri() . '/js/slick.min.js', array(), _S_VERSION.".".$current_time, true );
	wp_enqueue_script( 'neworleanscitybusiness-gallery-js', get_template_directory_uri() . '/js/gallery.js', array(), _S_VERSION.".".$current_time, true );
    wp_enqueue_script( 'neworleanscitybusiness-jquery-ui', get_template_directory_uri() . '/js/jquery-ui.js', array(), _S_VERSION.".".$current_time, true );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'neworleanscitybusiness_scripts' );

// exclude auto solr JS file
function childtheme_prefix_remove_scripts() {
    if ( is_page(178406) ) {
		wp_dequeue_script("script");
		wp_dequeue_script( 'autocomplete', plugins_url( 'js/autocomplete_solr.js', __FILE__ ));
        //wp_dequeue_script( 'jquery.autocomplete' );
		wp_deregister_script( 'autocomplete', plugins_url( 'js/autocomplete_solr.js', __FILE__ ));
    }
}
add_action('wp_enqueue_scripts', 'childtheme_prefix_remove_scripts', 999);


// Useful global constants.
define( 'NOBC_VERSION', '0.1.4' );
define( 'NOBC_TEMPLATE_URL', get_template_directory_uri() );
define( 'NOBC_PATH', get_template_directory() . '/' );
define( 'NOBC_INC', NOBC_PATH . 'includes/' );
// Category page
require_once NOBC_INC . 'ajaxLoader/ajax-category-mobile.php';
// Sponsored Content Page
require_once NOBC_INC . 'ajaxLoader/ajax-sponsored-content-mobile.php';
// Author page for mobile
require_once NOBC_INC . 'ajaxLoader/ajax-author-mobile.php';
// Load more events
require_once NOBC_INC . 'eventsmoreload/eventsload.php';
require_once NOBC_INC . 'core.php';

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
	$author_page = array_search("author",$path);
	$event_page = array_search("events",$path);
	$event_detail_page = array_search("event",$path);
	$sponsored_content = array_search("sponsored_content",$path);
	$sponsored_detail_page = array_search("sponsored-contents",$path);


	if(is_singular('post')){
		$wpseo_primary_term = new WPSEO_Primary_Term( 'category', get_the_id() );
		$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
		$term = get_term( $wpseo_primary_term );
		$term_name = $term->name;
		
		$term_id =  $term->term_id;
		$pri_permalink = get_category_link( $term_id );
		$primary_link = site_url().'/blog'.'/'.'category/'.$term->slug;
		$categories = get_the_category($post);
		$cat_name = $categories[0]->name;
		$cat_id = $categories[0]->term_id;
		if(empty($term_name)){
			$term_name = $cat_name;
		}
		
		$parent_id = $term->parent;
		$parentCatList = get_term($parent_id);
		$parent_name = $parentCatList->name;
		$parent_permalink = get_category_link($parentCatList);
		$permalink = get_category_link($cat_id);
		$title = get_the_title(); 		
		if($parent_id){ ?>
			<a href="/">Home</a><span>></span><a href="<?php print $parent_permalink; ?>"><?php print $parent_name; ?></a><span>></span><a href="<?php print $pri_permalink; ?>"><?php print $term_name; ?></a><span>></span><p><?php print $title; ?></p>
		<?php } 
		else { 
			if($term_name){?>
				<a href="/">Home</a><span>></span><a href="<?php print $pri_permalink; ?>"><?php print $term_name; ?></a><span>></span><p><?php print $title; ?></p>
			<?php } else {?>
				<a href="/">Home</a><span>></span><a href="/blog/category/news/">News</a><span>></span><p><?php print $title; ?></p>
			<?php } 
		} 
	}
	elseif($category_page){
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
	}elseif($tag_page){

		$termObj = get_queried_object();
		$category_slug = $termObj->slug;

		$term = get_term_by('slug', $category_slug, 'tag');
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
	elseif($event_page){ ?>
		<a href="/">Home</a><span>></span>Events<span>
	<?php }
	elseif($sponsored_detail_page){ ?>
		<a href="/">Home</a><span>></span>Sponsored Contents<span>
	<?php }
	elseif($sponsored_content){ 
		$wpseo_primary_term = new WPSEO_Primary_Term( 'category', get_the_id() );
		$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
		$term = get_term( $wpseo_primary_term );
		$term_name = $term->name;
		$parent_id = $term->parent;
		$parentCatList = get_term($parent_id);
		$parent_name = $parentCatList->name;
		$parent_permalink = get_category_link($parentCatList);
		$permalink = get_category_link($term);
		$title = get_the_title();
		if($parent_id){ ?>
			<a href="/">Home</a><span>></span><a href="<?php print $parent_permalink; ?>"><?php print $parent_name; ?></a><span>></span><a href="<?php print $permalink; ?>"><?php print $term_name; ?></a><span>></span><p><?php print $title; ?></p>
		<?php } 
		else { 
			if($term_name){?>
				<a href="/">Home</a><span>></span><a href="<?php print $permalink; ?>"><?php print $term_name; ?></a><span>></span><p><?php print $title; ?></p>
			<?php } else {?>
				<a href="/">Home</a><span>></span><a href="/blog/category/news/">News</a><span>></span><p><?php print $title; ?></p>
			<?php } 
		}?>
	<?php }
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
			<a href="/">Home</a><span>></span><a href="/new-orleans-citybusiness-events">Events</a><span>></span><?php
			if ($event_cats_name) { ?>
				<a href="<?php echo $event_cats_link; ?>"><?php echo $event_cats_name; ?></a><span>></span><?php } the_title(); 
			?>
	<?php }
	elseif(is_page()){ ?>
		<a href="/">Home</a><span>></span><?php the_title(); ?><span>
	<?php }
	elseif($author_page){ 
		$author_id = get_the_author_meta("ID");
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

require_once NOBC_INC . 'related-contents.php';
//----------------------------------------
function wp_is_ipad() {
    $ipad = strpos($_SERVER['HTTP_USER_AGENT'],'iPad');
    if ($ipad === false) {
        return false;
    } else {
        return true;
    }
}