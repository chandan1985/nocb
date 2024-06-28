<?php
/*
 * Plugin Name: TDC Jarida Custom Functions
 * Plugin URI: http://www.thedolancompany.com
 * Description: Adds all the TDC custom function mods.
 * Author: Jerry Johnson
 * Version: 0.9
 * Author URI: http://www.thedolancompany.com
 */

// Prevent direct file call
if (!defined( 'ABSPATH' ))
	die( 'Direct access not allowed.' );

// Add specific CSS class by filter.

add_filter( 'body_class', function( $classes ) {
	$cats = get_the_category();
	$cat_names = array();
	foreach($cats as $i){
		$cat_names[] = $i->slug;
	}
	if( !empty($classes) && is_array($classes) ){
		return array_merge( $classes, $cat_names);
	}
    
} );

//define('MAGPIE_FETCH_TIME_OUT', 10);
add_filter( 'http_request_host_is_external', function() { return true; });
add_filter('http_request_reject_unsafe_urls','__return_false');

// replace "get shortlink" button on post edit page
add_filter( 'get_shortlink', function( $shortlink ) {return $shortlink;} );

// allow all html in author bio field
remove_filter('pre_user_description', 'wp_filter_kses');

//add sanitization for WordPress posts
add_filter( 'pre_user_description', 'wp_filter_post_kses');

function tdc_filter_gettext( $translated, $original, $domain ) {

    // This is an array of original strings
    // and what they should be replaced with
    $strings = array(
        'Posted by: ' => 'By: ',
        // Add some more strings here
    );

    // See if the current string is in the $strings array
    // If so, replace it's translation
    if ( isset( $strings[$original] ) ) {
        // This accomplishes the same thing as __()
        // but without running it through the filter again
        $translations = get_translations_for_domain( 'tie' );
        $translated = $translations->translate( $strings[$original] );
    }

    return $translated;
}

add_filter( 'gettext', 'tdc_filter_gettext', 20, 3 );

/**
 * Recent_Posts_nocache widget class
 *
 * @since 2.8.0
 */
class TDC_Widget_Recent_Posts extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_recent_entries_nocache', 'description' => __( "The most recent posts on your site (no cache)") );
		parent::__construct('recent-posts-nocache', __('TDC Recent Posts (nocache)'), $widget_ops);
		$this->alt_option_name = 'widget_recent_entries_nocache';

	}

	function widget($args, $instance) {
		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		ob_start();
		$priority = 10;
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Posts') : $instance['title'], $instance, $this->id_base);
		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
 			$number = 10;
		$show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

		// hack to bypass the search killing content filter.
		// save all the filters
		global $wp_filter;
		$j_wpf = $wp_filter['posts_where'];
		remove_all_filters( 'posts_where', $priority );
		
		$r = new WP_Query( apply_filters( 'widget_posts_nocache_args', array( 'posts_per_page' => $number, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true ) ) );
		
		// restore the filters
		$wp_filter['posts_where'] = $j_wpf;

		if ($r->have_posts()) :
?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<ul>
		<?php while ( $r->have_posts() ) : $r->the_post(); ?>
			<li>
				<a href="<?php the_permalink() ?>" title="<?php echo esc_attr( get_the_title() ? get_the_title() : get_the_ID() ); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a>
			<?php if ( $show_date ) : ?>
				<span class="post-date"><?php echo get_the_date(); ?></span>
			<?php endif; ?>
			</li>
		<?php endwhile; ?>
		</ul>
		<?php echo $after_widget; ?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;

	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_date'] = (bool) $new_instance['show_date'];

		return $instance;
	}

	function form( $instance ) {
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

		<p><input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?' ); ?></label></p>
<?php
	}
}


/**
 * Text simple no wrap widget class
 *
 * @since 2.8.0
 */
class TDC_Widget_Text extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'tdc_widget_text', 'description' => __('Just text or HTML, with sidebars, no wrapper'));
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct('tdc_text', __('TDC Simple Text'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$text = do_shortcode(apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance ));
		?>
		<?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		$instance['filter'] = isset($new_instance['filter']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags($instance['title']);
		$text = esc_textarea($instance['text']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>

		<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs'); ?></label></p>
<?php
	}
}

function tdc_custom_widgets_init() {
	if ( !is_blog_installed() )
		return;

	register_widget('TDC_Widget_Recent_Posts');
	register_widget('TDC_Widget_Text');
	do_action('widgets_init');
}

add_action('init', 'tdc_custom_widgets_init', 1);

// add some more mimetype totals for media page
function tdc_modify_post_mime_types($post_mime_types) {
    $post_mime_types['application/pdf'] = array(__( 'mimeframe' ), __('Manage PDF'), _n_noop( 'PDF <span class="count">(%s)</span>', 'PDF <span class="count">(%s)</span>'));
    $post_mime_types['text/html'] = array(__( 'mimeframe' ), __('Manage Html'), _n_noop( 'Html <span class="count">(%s)</span>', 'Html <span class="count">(%s)</span>'));
    $post_mime_types['application/zip'] = array(__( 'mimeframe' ), __('Manage Zip'), _n_noop( 'Zip <span class="count">(%s)</span>', 'Zip <span class="count">(%s)</span>'));
    return $post_mime_types;
}
add_filter('post_mime_types', 'tdc_modify_post_mime_types');

// create a custom taxonomy to handle the issue tags
add_action( 'init', 'register_taxonomy_issues' );

function register_taxonomy_issues() {

    $labels = array( 
        'name' => _x( 'Issues', 'issues' ),
        'singular_name' => _x( 'Issue', 'issues' ),
        'search_items' => _x( 'Search Issues', 'issues' ),
        'popular_items' => _x( 'Popular Issues', 'issues' ),
        'all_items' => _x( 'All Issues', 'issues' ),
        'parent_item' => _x( 'Parent Issue', 'issues' ),
        'parent_item_colon' => _x( 'Parent Issue:', 'issues' ),
        'edit_item' => _x( 'Edit Issue', 'issues' ),
        'update_item' => _x( 'Update Issue', 'issues' ),
        'add_new_item' => _x( 'Add New Issue', 'issues' ),
        'new_item_name' => _x( 'New Issue', 'issues' ),
        'separate_items_with_commas' => _x( 'Separate issues with commas', 'issues' ),
        'add_or_remove_items' => _x( 'Add or remove Issues', 'issues' ),
        'choose_from_most_used' => _x( 'Choose from most used Issues', 'issues' ),
        'menu_name' => _x( 'Issues', 'issues' ),
    );

    $args = array( 
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_tagcloud' => true,
        'show_admin_column' => true,
        'hierarchical' => false,

        'rewrite' => true,
        'query_var' => true
    );

    register_taxonomy( 'issues', array('post'), $args );
}

function issue_custom_query_archive_display ( $query ) {
    if (($query->is_main_query()) && (is_tax('issues'))) {
	    $query->set( 'posts_per_page', '500' );
    	$query->set( 'orderby', 'title' );
    	$query->set( 'order', 'ASC' );
    }
}

//Hook the function
add_action( 'pre_get_posts', 'issue_custom_query_archive_display' );

add_action('admin_menu', 'tdc_jarida_create_menu');

function tdc_jarida_create_menu() {
	//create new settings page
	add_options_page(__('TDC Jarida Settings'), __('TDC Jarida'), 'manage_options', __FILE__, 'tdc_jarida_settings_page');
	
	//call register settings function
	add_action( 'admin_init', 'tdc_jarida_register_mysettings' );
}

function tdc_jarida_register_mysettings() {
	//register our settings
	register_setting( 'tdc-jarida-settings-group', 'tdc_jarida_ap_userids' );
	register_setting( 'tdc-jarida-settings-group', 'tdc_jarida_pub_userids' );
	register_setting( 'tdc-jarida-settings-group', 'tdc_jarida_pub_domain' );
	register_setting( 'tdc-jarida-settings-group', 'tdc_jarida_pub_code' );
	register_setting( 'tdc-jarida-settings-group', 'tdc_jarida_ap_bug' );
	register_setting( 'tdc-jarida-settings-group', 'tdc_jarida_issue_catids' );
	register_setting( 'tdc-jarida-settings-group', 'tdc_jarida_issue_misc_label' );
	register_setting( 'tdc-jarida-settings-group', 'tdc_jarida_slideshow_speed' );
	register_setting( 'tdc-jarida-settings-group', 'tdc_jarida_slideshow_timeout' );
	register_setting( 'tdc-jarida-settings-group', 'tdc_text_home' );
	register_setting( 'tdc-jarida-settings-group', 'tdc_jarida_slideshow_excerpt_max_chars' );
	register_setting( 'tdc-jarida-settings-group', 'tdc_jarida_home_widget_thumbnail_size' );
}

function tdc_jarida_settings_page() {
?>
	<?php if ( isset($_GET['settings-updated']) ) : ?>
		<div id="message" class="updated fade"><p><strong><?php _e('TDC Jarida Settings saved.') ?></strong></p></div>
	<?php endif; ?>

	<div class="wrap">
		<h2><?php echo __('TDC Jarida Settings Page'); ?></h2>

		<form method="post" action="options.php">
			<?php settings_fields( 'tdc-jarida-settings-group' ); ?>
			<?php do_settings_fields( __FILE__ ,'tdc-jarida-settings-group'); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('AP User IDs:') ?></th>
					<td><input type="text" name="tdc_jarida_ap_userids" value="<?php echo get_option('tdc_jarida_ap_userids'); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Pub User IDs:') ?></th>
					<td><input type="text" name="tdc_jarida_pub_userids" value="<?php echo get_option('tdc_jarida_pub_userids'); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('AP Pub Domain:') ?></th>
					<td><input type="text" name="tdc_jarida_pub_domain" value="<?php echo get_option('tdc_jarida_pub_domain'); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('AP Pub Code:') ?></th>
					<td><input type="text" name="tdc_jarida_pub_code" value="<?php echo get_option('tdc_jarida_pub_code'); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Draw AP Tracking Bug?:') ?></th>
					<td><input type="checkbox" name="tdc_jarida_ap_bug" value="1" <?php if (get_option('tdc_jarida_ap_bug') == 1) echo 'checked="checked"'; ?> /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Issue Category IDs:') ?></th>
					<td><input type="text" name="tdc_jarida_issue_catids" value="<?php echo get_option('tdc_jarida_issue_catids'); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Issue Misc Label:') ?></th>
					<td><input type="text" name="tdc_jarida_issue_misc_label" value="<?php echo get_option('tdc_jarida_issue_misc_label'); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Slideshow Speed:') ?></th>
<td><input type="text" name="tdc_jarida_slideshow_speed" value="<?php if(get_option('tdc_jarida_slideshow_speed')){ echo get_option('tdc_jarida_slideshow_speed');} else {echo('300');} ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Slideshow Timout:') ?></th>
					<td><input type="text" name="tdc_jarida_slideshow_timeout" value="<?php if(get_option('tdc_jarida_slideshow_timeout')){ echo get_option('tdc_jarida_slideshow_timeout');} else {echo('3000');} ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Slideshow Excerpt Maximum Characters:') ?></th>
					<td><input type="text" name="tdc_jarida_slideshow_excerpt_max_chars" value="<?php if(get_option('tdc_jarida_slideshow_excerpt_max_chars')){ echo get_option('tdc_jarida_slideshow_excerpt_max_chars');} else {echo('100');} ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Change ads-home to text-home?:') ?></th>
					<td><input type="checkbox" name="tdc_text_home" value="1" <?php if (get_option('tdc_text_home') == 1) echo 'checked="checked"'; ?> /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e("Which thumbnail size to use on home widgets?:") ?></th>
					<td>
					<?php
					$thumb_sizes = get_intermediate_image_sizes();
					$cur_option = get_option('tdc_jarida_home_widget_thumbnail_size');
					if (!$cur_option) {$cur_option = 'tie_thumb';}

						echo '<select name="tdc_jarida_home_widget_thumbnail_size">';
							foreach ($thumb_sizes as $key => $value) {

							if ($cur_option == $value) {
								$selected = ' selected';
							} else {
								$selected = '';
							}
							echo '<option value="' . $value . '"' . $selected . '>' . $value . '</option>';
							}
						echo '</select>';
					?>
					</td>
				</tr>
			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>

		</form>
	</div>
<?php
}

// example: [year]
if ( !shortcode_exists( 'year' ) ) {
	function tdc_year_shortcode() {
		$year = date('Y');
		return $year;
	}
	add_shortcode('year', 'tdc_year_shortcode');
}

function tdc_author_metatag() 
{ 
	if (is_single()) {
		echo "\n".'<meta name="author" content="'.get_the_author_meta('display_name',get_queried_object()->post_author).'" />'."\n";	
	}	
} 
add_action('wp_head', 'tdc_author_metatag');

// example: [openx zoneID="60"]
function tdc_openx_shortcode( $atts ) {

	extract( shortcode_atts( array('zoneid' => '','id' => '','closex' => '', 'drawx' => ''), $atts , 'openx' ) );

	$basecode = '';
	$drawid = $id ? ' id="'.$id.'"' : '';
	if ($closex) {
		$drawx = '<img class="close-ad-image" src="/wp-content/themes/jarida/images/solid-close-square.png" />';
	}
	if ($zoneid) {
		if (class_exists('DMC_OpenX2')) {
			// retrieve our global widget options and settings
			$location = stripslashes(get_option('openxwpwidget_url2openx'));
			$dmc_obj = new DMC_OpenX2();
			$basecode = $dmc_obj->_openxwpwidget_get_invocation($location, $zoneid);
			if ($basecode != '') {
				$basecode = '<div class="openx shortcode"'.$drawid.'>'.$drawx.$basecode.'</div>';
			}                
		}
	}
	return $basecode;
}
add_shortcode('openx', 'tdc_openx_shortcode');

// Custom Header for category pages
// Add term page
function tdc_taxonomy_add_custom_header_field() {
	// this will add the custom meta field to the add new term page
	?>
	<div class="form-field">
		<label for="term_meta[custom_header]">Custom Header</label>
		<textarea name="term_meta[custom_header]" id="term_meta[custom_header]" rows="5" cols="40"><?php echo $term_meta['custom_header']; ?></textarea>
		<p class="description">Custom Category Page Header. Use with extreme caution.</p>
	</div>
<?php
}
add_action( 'category_add_form_fields', 'tdc_taxonomy_add_custom_header_field', 10, 2 );


// Edit term page
function tdc_taxonomy_edit_custom_header_field($term) {
 	// put the term ID into a variable
	$t_id = $term->term_id;
 
	// retrieve the existing value(s) for this meta field. This returns an array
	$term_meta = get_option( "taxonomy_$t_id" ); ?>
	<tr class="form-field">
	<th scope="row" valign="top"><label for="term_meta[custom_header]">Custom Header</label></th>
		<td>
			<textarea name="term_meta[custom_header]" id="term_meta[custom_header]" rows="5" cols="40"><?php echo stripslashes( $term_meta['custom_header'] ); ?></textarea>
			<p class="description">Custom Category Page Header. Use with extreme caution.</p>
		</td>
	</tr>
<?php
}
add_action( 'category_edit_form_fields', 'tdc_taxonomy_edit_custom_header_field', 10, 2 );


/// Save extra taxonomy fields callback function.
function save_taxonomy_custom_meta( $term_id ) {
	if ( isset( $_POST['term_meta'] ) ) {
		$t_id = $term_id;
		$term_meta = get_option( "taxonomy_$t_id" );
		$cat_keys = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if ( isset ( $_POST['term_meta'][$key] ) ) {
				$term_meta[$key] = $_POST['term_meta'][$key];
			}
		}
		// Save the option array.
		update_option( "taxonomy_$t_id", $term_meta );
	}
}  
add_action( 'edited_category', 'save_taxonomy_custom_meta', 10, 2 );  
add_action( 'create_category', 'save_taxonomy_custom_meta', 10, 2 );

function btm_limit_body_class( $wp_classes, $extra_classes )
{
	// List of the only WP generated classes allowed
	$whitelist = array( 'home', 'blog', 'archive', 'single', 'category', 'tag', 'error404', 'logged-in', 'admin-bar' );

	// List of the only WP generated classes that are not allowed
	$blacklist = array( 'sidebar','breaking-news' );

	// Filter the body classes
	// Whitelist result: (comment if you want to blacklist classes)
	# $wp_classes = array_intersect( $wp_classes, $whitelist );
	// Blacklist result: (uncomment if you want to blacklist classes)
	if(!empty($wp_classes) && is_array($wp_classes)){
		$wp_classes = array_diff( $wp_classes, $blacklist );
		// Add the extra classes back untouched
		return array_merge( $wp_classes, (array) $extra_classes );
	}
	
	
}
add_filter( 'body_class', 'btm_limit_body_class', 10, 2 );

//Clear Sticky Posts Cache
function btm_delete_options_from_cache () {
wp_cache_delete( 'alloptions', 'options' );
}
add_action( 'update_option_sticky_posts', 'btm_delete_options_from_cache' );

function btm_subscribe_page_fields(){
if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_subscribe',
		'title' => 'Subscribe',
		'fields' => array (
			array (
				'key' => 'field_58864b3z7536f',
				'label' => 'Subscribe Url',
				'name' => 'subscribe_url',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => '',
				'maxlength' => '',
			),
			array (
				'key' => 'field_58864b3b4536f',
				'label' => 'Main Header',
				'name' => 'top_main',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_58864b3b4537f',
				'label' => 'Header Subtext',
				'name' => 'top_sub',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_58864b3b4535f',
				'label' => 'Left Value',
				'name' => 'left_value',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_58865e57e8b4d',
				'label' => 'Middle Value',
				'name' => 'middle_value',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_58865e5ae8b4e',
				'label' => 'Right Value',
				'name' => 'right_value',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_58864bd045360',
				'label' => 'Left Option',
				'name' => 'left_option',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_58865e5fe8b4f',
				'label' => 'Middle Option',
				'name' => 'middle_option',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_58865e60e8b50',
				'label' => 'Right Option',
				'name' => 'right_option',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_58864c0945361',
				'label' => 'Left Image',
				'name' => 'left_image',
				'type' => 'image',
				'save_format' => 'url',
				'preview_size' => 'thumbnail',
				'library' => 'all',
			),
			array (
				'key' => 'field_58865e63e8b51',
				'label' => 'Middle Image',
				'name' => 'middle_image',
				'type' => 'image',
				'save_format' => 'url',
				'preview_size' => 'thumbnail',
				'library' => 'all',
			),
			array (
				'key' => 'field_58865e64e8b52',
				'label' => 'Right Image',
				'name' => 'right_image',
				'type' => 'image',
				'save_format' => 'url',
				'preview_size' => 'thumbnail',
				'library' => 'all',
			),
			array (
				'key' => 'field_58865aed9500c',
				'label' => 'Left Mobile Price',
				'name' => 'left_mobile_price',
				'type' => 'textarea',
				'instructions' => 'New lines are converted to break tags',
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'rows' => '',
				'formatting' => 'br',
			),
			array (
				'key' => 'field_58865e69e8b53',
				'label' => 'Middle Mobile Price',
				'name' => 'middle_mobile_price',
				'type' => 'textarea',
				'instructions' => 'New lines are converted to break tags',
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'rows' => '',
				'formatting' => 'br',
			),
			array (
				'key' => 'field_58865e6de8b54',
				'label' => 'Right Mobile Price',
				'name' => 'right_mobile_price',
				'type' => 'textarea',
				'instructions' => 'New lines are converted to break tags',
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'rows' => '',
				'formatting' => 'br',
			),
			array (
				'key' => 'field_58864c4445362',
				'label' => 'Left Text Title',
				'name' => 'left_text_title',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_58865e6fe8b55',
				'label' => 'Middle Text Title',
				'name' => 'middle_text_title',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_58865e70e8b56',
				'label' => 'Right Text Title',
				'name' => 'right_text_title',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_58864c6e45363',
				'label' => 'Left Text Price',
				'name' => 'left_text_price',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_58865e72e8b57',
				'label' => 'Middle Text Price',
				'name' => 'middle_text_price',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_58865e72e8b58',
				'label' => 'Right Text Price',
				'name' => 'right_text_price',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_58864cb245364',
				'label' => 'Left Text Body',
				'name' => 'left_text_body',
				'type' => 'textarea',
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'rows' => '',
				'formatting' => 'html',
			),
			array (
				'key' => 'field_58865e74e8b59',
				'label' => 'Middle Text Body',
				'name' => 'middle_text_body',
				'type' => 'textarea',
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'rows' => '',
				'formatting' => 'html',
			),
			array (
				'key' => 'field_58865e75e8b5a',
				'label' => 'Right Text Body',
				'name' => 'right_text_body',
				'type' => 'textarea',
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'rows' => '',
				'formatting' => 'html',
			),
			array (
				'key' => 'field_58864c6e45546',
				'label' => 'Left Promocode',
				'name' => 'left_promocode',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => '',
				'maxlength' => '',
			),
			array (
				'key' => 'field_58865e72e8547',
				'label' => 'Center Promocode',
				'name' => 'center_promocode',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => '',
				'maxlength' => '',
			),
			array (
				'key' => 'field_58865e72e8548',
				'label' => 'Right Promocode',
				'name' => 'right_promocode',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => '',
				'maxlength' => '',
			),
		),
		// 'location' => array (
		// 	array (
		// 		array (
		// 			'param' => 'page_template',
		// 			'operator' => '==',
		// 			'value' => 'template-subscribe.php',
		// 			'order_no' => 0,
		// 			'group_no' => 0,
		// 		),
		// 	),
		// ),
		

		'location' => array (
        array (
            array (
                'param' => 'page_template',
					'operator' => '==',
					'value' => 'template-subscr-psa.php',
					'order_no' => 0,
					'group_no' => 0,
            ),
        ),
        array (
            array (
					'param' => 'page_template',
					'operator' => '==',
					'value' => 'template-subscribe.php',
					'order_no' => 0,
					'group_no' => 0,
				),
        ),
    ),

		
		
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
				0 => 'discussion',
				1 => 'revisions',
				2 => 'format',
				3 => 'featured_image',
				4 => 'send-trackbacks',
			),
		),
		'menu_order' => 0,
	));
}
}
add_action( 'subscribe_page_fields', 'btm_subscribe_page_fields', 10, 0 );
do_action('subscribe_page_fields');

function subscribe_add_query_vars_filter( $vars ){
$vars[] = "source";
return $vars;
}
add_filter( 'query_vars', 'subscribe_add_query_vars_filter' );

function tdc_subscribe_source_shortcode() {
$source = get_query_var( 'source', '' );
if ($source) $source = '&source=' . $source;
return $source;
}

add_shortcode('subscribe_source', 'tdc_subscribe_source_shortcode');

?>