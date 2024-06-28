<?php
add_action( 'wptouch_theme_init', 'classic_ipad_init' );
add_action( 'wptouch_theme_language', 'classic_ipad_language' );

add_filter( 'wptouch_body_classes', 'classic_ipad_body_classes' );
add_filter( 'pre_get_posts', 'classic_exclude_categories' );
add_filter( 'wptouch_has_post_thumbnail', 'classic_has_post_thumbnail' );
add_filter( 'wptouch_the_post_thumbnail', 'classic_the_post_thumbnail' );
add_filter( 'excerpt_length', 'classic_ipad_excerpt' );

//--Device Theme Functions for Classic --//

function classic_ipad_init() {

	if ( !is_admin() ) {
		wp_deregister_script( 'jquery' );
 		wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js', false, '1.4.4' );
 		wp_enqueue_script( 'jquery' );
	}

	$minfile = WPTOUCH_DIR . '/themes/classic/ipad/ipad.min.js';

	if ( file_exists( $minfile ) ) {
		wp_enqueue_script( 'classic-ipad-js', wptouch_get_bloginfo('template_directory') . '/ipad.min.js', array('jquery'), wptouch_refreshed_files() );
	} else {
		wp_enqueue_script( 'classic-ipad-js', wptouch_get_bloginfo('template_directory') . '/ipad.js', array('jquery'), wptouch_refreshed_files() );
	}
	
	wp_enqueue_script( 'iscroll-js', wptouch_get_bloginfo('template_directory') . '/iscroll-min.js', array('classic-ipad-js'), wptouch_refreshed_files() );
	wp_enqueue_script('jquery-form');

	if ( isset( $_GET['classic_include_dynamic'] ) ) {
		header( 'Content-type: text/css' );
		include( 'dynamic-style.php' );
		die;
	}
		
}

function classic_ipad_language( $locale ) {
	// In a user theme a language file should be loaded here for text translation
}

// Add background image name and post icon type for styling diffs
function classic_ipad_body_classes( $body_classes ) {
	$settings = wptouch_get_settings();
	
	$is_idevice = strstr( $_SERVER['HTTP_USER_AGENT'], 'iPad' );

	// iPad
	$body_classes[] = $settings->classic_icon_type;

	$body_classes[] = $settings->classic_ipad_content_bg;

	$body_classes[] = $settings->classic_ipad_sidebar_bg;

	$body_classes[] = $settings->classic_ipad_theme_color;
	
	$body_classes[] = $settings->classic_ipad_text_justification;

	if ( isset( $_COOKIE['wptouch-ipad-orientation'] ) ) {
		if ( $_COOKIE['wptouch-ipad-orientation'] == 'portrait' ) {
			$body_classes[] = 'portrait';	
		} else if ( $_COOKIE['wptouch-ipad-orientation'] == 'landscape' ) {
			$body_classes[] = 'landscape';	
		}
	}

	if ( isset( $_COOKIE['web-app-mode'] ) ) {
		$body_classes[] = 'web-app-mode';	
	}


	if ( $is_idevice ) {
		$body_classes[] = 'idevice';
	}

	// Shared
	$body_classes[] = $settings->classic_icon_type;

	$body_classes[] = $settings->classic_calendar_icon_bg;
		
	if ( !$settings->enable_menu_icons ) {
		$body_classes[] = 'no-icons';
	}
	
	if ( $settings->classic_webapp_status_bar_color == 'black-translucent' ) {
		$body_classes[] = $settings->classic_webapp_status_bar_color;
	}

	if ( $settings->classic_enable_persistent ) {
		$body_classes[] = 'loadsaved';
	}

	return $body_classes;
}

// iPad Blog Excerpt
function classic_ipad_excerpt( $length ) {
	return 50;
}

function classic_ipad_show_home_button() {
	$settings = wptouch_get_settings();
	if ( $settings->classic_ipad_home_button ) {
		return true;
	}
}

function classic_ipad_show_blog_button() {
	$settings = wptouch_get_settings();
	if ( $settings->classic_ipad_blog_button &&
	( classic_ipad_show_popover_recent() || classic_ipad_show_popover_popular() || classic_ipad_show_popover_tags() || classic_ipad_show_popover_cats() ) ) {
		return true;
	}
}

function classic_ipad_show_popover_recent() {
	$settings = wptouch_get_settings();
	if ( $settings->classic_ipad_recent_posts ) {
		return true;
	}
}

function classic_ipad_show_popover_popular() {
	$settings = wptouch_get_settings();
	if ( $settings->classic_ipad_popular_posts ) {
		return true;
	}
}

function classic_ipad_show_popover_tags() {
	$settings = wptouch_get_settings();
	if ( $settings->classic_ipad_popover_tags ) {
		return true;
	}
}

function classic_ipad_show_popover_cats() {
	$settings = wptouch_get_settings();
	if ( $settings->classic_ipad_popover_cats ) {
		return true;
	}
}

function classic_ipad_show_flickr_button() {
	$settings = wptouch_get_settings();
	if ( $settings->classic_ipad_show_flickr_button && function_exists( 'get_flickrRSS' ) ) {
		return true;
	}
}

function classic_ipad_show_account_button() {
	$settings = wptouch_get_settings();
	if ( $settings->classic_ipad_account_button ) {
		return true;
	}
}

function classic_ipad_show_search_button() {
	$settings = wptouch_get_settings();
	if ( $settings->classic_ipad_search_button ) {
		return true;
	}
}

// show lost password/register links if comment registration or accounts are enabled
function classic_ipad_accounts_enabled() {
if ( get_option( 'comment_registration' ) || get_option( 'users_can_register' ) ) {
		return true;
	}
}

// Custom iPad Comments Title
function classic_ipad_comments_title() {
	if ( !function_exists( 'id_activate_hooks' ) || !function_exists( 'dsq_is_installed' ) ) {
		$comment_string1 = __( 'No Responses', 'wptouch-pro' );
		$comment_string2 = __( '1 Response', 'wptouch-pro' );
		$comment_string3 = __( '% Responses', 'wptouch-pro' );
		comments_number( $comment_string1, $comment_string2, $comment_string3 );
	}
}

// Custom iPad Comments
// Custom callback to list comments in the your-theme style
function classic_ipad_custom_comments( $comment, $args, $depth ) {
	$GLOBALS[ 'comment' ] = $comment;
	$GLOBALS[ 'comment_depth' ] = $depth;
	include( 'comment-content.php' );
} // end custom_comments

// Produces an avatar image with the hCard-compliant photo class
function classic_ipad_commenter_link() {
	$commenter = get_comment_author_link();
	if ( ereg( '<a[^>]* class=[^>]+>', $commenter ) ) {
		$commenter = ereg_replace( '(<a[^>]* class=[\'"]?)', '\\1url ' , $commenter );
	} else {
		$commenter = ereg_replace( '(<a )/', '\\1class="url "' , $commenter );
	}
	
	$avatar_email = get_comment_author_email();
	$avatar = str_replace( "class='avatar", "class='photo avatar", get_avatar( $avatar_email, 68 ) );
	
	echo '<span class="avatar-wrap">' . $avatar . '</span> <div class="comment-content"><span class="fn n">' . $commenter . '</span>';
} // end commenter_link

// iPad Custom logo
function classic_has_ipad_logo() {
	$settings = wptouch_get_settings();
	return $settings->classic_ipad_logo_image;
}

function classic_ipad_logo_image() {
	$settings = wptouch_get_settings();
	echo "<img src='" . $settings->classic_ipad_logo_image . "' alt='logo-image' /> \n";
}

// iPad Popular Posts
function classic_ipad_pop_posts( $num ) {
	global $wpdb;
	
	$posts = $wpdb->get_results( $wpdb->prepare( "SELECT comment_count, ID, post_title, post_date FROM $wpdb->posts WHERE post_type='post' AND post_status = 'publish' ORDER BY comment_count DESC LIMIT 0, %d", $num ) );
	
	if ( $posts and count( $posts ) ) {
		foreach ($posts as $post) {
			setup_postdata($post);
			$id = $post->ID;
			$title = $post->post_title;
			$count = $post->comment_count;
			if ( $count != 0 ) {
				$popular_posts .= '<li><span>' . date( "M j - Y", strtotime( $post->post_date ) ) . ' &bull; '.$count.' comments</span><a href="' . get_permalink( $id ) . '" title="' . $title . '">' . $title . '</a></li>';
			}
		}
	}
	
	return $popular_posts;
}

// iPad Recent Posts
function classic_ipad_recent_posts( $num ) {
	global $wpdb;
	
	$posts = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title, post_date FROM {$wpdb->posts} WHERE post_type='post' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 0, %d", $num ) );
	
	if ( $posts and count( $posts ) ) {
		foreach ( $posts as $post ) {
			setup_postdata( $post );
			$id = $post->ID;
			$title = $post->post_title;
			$count = $post->comment_count;			
			$recent_posts .= '<li><span>' . date( "M j", strtotime( $post->post_date ) ) . '</span><a href="' . get_permalink( $id ) . '" title="' . $title . '">' . $title . '</a></li>';
		}
	}
	
	return $recent_posts;
}