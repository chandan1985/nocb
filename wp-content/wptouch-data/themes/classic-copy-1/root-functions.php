<?php

//-- Global Theme Filters & Actions --//

add_action( 'wptouch_theme_init', 'classic_theme_initialization' );
add_action( 'wptouch_post_head', 'classic_iphone_meta' );
add_action( 'wptouch_ajax_instapaper', 'classic_instapaper' );
add_action( 'wptouch_post_head', 'classic_compat_css' );

add_filter( 'wptouch_supported_device_classes', 'classic_supported_devices' );
add_filter( 'wptouch_custom_templates', 'classic_custom_templates' );
add_filter( 'wptouch_default_settings', 'classic_default_settings' );
add_filter( 'wptouch_theme_menu', 'classic_admin_menu' );
add_filter( 'wptouch_localize_scripts', 'classic_localize_scripts' );
add_filter( 'wptouch_setting_filter_classic_custom_user_agents', 'classic_user_agent_filter' );

//-- Global Functions For Classic Mobile + iPad --//

function classic_theme_initialization() {
	wptouch_persisitence_mode();

	$settings = wptouch_get_settings();
	if ( $settings->classic_show_attached_image ) {
		add_filter( 'wptouch_the_content', 'classic_show_attached_image_filter' );	
	}
	// Un-comment and reload to delete all theme cookies
	//wptouch_classic_delete_cookie();
}

// Eat all the cookies for lunch
function wptouch_classic_delete_cookie() {
	if ( isset( $_SERVER['HTTP_COOKIE'] ) ) {
	    $cookies = explode( ';', $_SERVER['HTTP_COOKIE'] );
		$url_path = str_replace( array( 'http://' . $_SERVER['SERVER_NAME'] . '','https://' . $_SERVER['SERVER_NAME'] . '' ), '', wptouch_get_bloginfo( 'url' ) . '/' );
	    foreach( $cookies as $cookie ) {
	        $parts = explode( '=', $cookie );
	        $name = trim( $parts[0] );
	        setcookie( $name, '', time()-1000 );
	        setcookie( $name, '', time()-1000, $url_path );
	    }
	}
}

function wptouch_persisitence_mode() {
 if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'iPhone' ) || strpos( $_SERVER['HTTP_USER_AGENT'], 'iPod' ) || strpos( $_SERVER['HTTP_USER_AGENT'], 'iPad' ) ) {
	$settings = wptouch_get_settings();
		if ( $settings->classic_enable_persistent && defined( 'WP_USE_THEMES' ) && !is_admin() ) {
			if ( isset( $_COOKIE['wptouch-load-last-url'] ) && !isset( $_COOKIE['web-app-mode'] ) && strpos( $_SERVER['HTTP_USER_AGENT'], 'Safari/' ) === false ) {
				$saved_url = $_COOKIE['wptouch-load-last-url'];
				$page_url = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
				$time = time()+60*60*24*365;
				$url_path = str_replace( array( 'http://' . $_SERVER['SERVER_NAME'] . '','https://' . $_SERVER['SERVER_NAME'] . '' ), '', get_bloginfo( 'url' ) . '/' );
				setcookie( 'web-app-mode', 'on', 0, $url_path );
				setcookie( 'wptouch-load-last-url', $page_url,  $time, $url_path );
				if ( $saved_url != $page_url ) {
					header( 'Location: ' . $saved_url );
					die;
				}
			}
		}
	}
}
	

function classic_compat_css() {
	$settings = wptouch_get_settings();
	$version_string = md5( WPTOUCH_VERSION );
	if ( $settings->classic_use_compat_css ) {
		echo "<link rel='stylesheet' type='text/css' href='" . wptouch_get_bloginfo('template_directory') . "/compat.css?ver=" . wptouch_refreshed_files() . "' /> \n";		
	}

	echo "<link rel='stylesheet' type='text/css' media='screen' href='" . wptouch_get_bloginfo('url' ) . "/?classic_include_dynamic=1&amp;version=" . wptouch_refreshed_files() . "' /> \n";
}

// This spits out all the meta tags for iPhone/iPod touch/iPad stuff 
// (web-app, startup img, device width, status bar style)
function classic_iphone_meta() {
	$settings = wptouch_get_settings();
	$ipad = strstr( $_SERVER['HTTP_USER_AGENT'],'iPad' );

	if ( $ipad ) {	
		$status_type = 'default';
	} else {
		$status_type = $settings->classic_webapp_status_bar_color;	
	}
	
// lock the viewport as 1:1, no zooming
	echo "<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no' /> \n";

	if ( $settings->classic_webapp_enabled ) {
		echo "<meta name='apple-mobile-web-app-status-bar-style' content='" . $status_type . "' /> \n";	
		echo "<meta name='apple-mobile-web-app-capable' content='yes' /> \n";
	}
	
	if ( $settings->classic_webapp_use_loading_img && !$settings->classic_webapp_loading_img_location && !$ipad ) {
// default iphone
		echo "<link rel='apple-touch-startup-image' href='" . wptouch_get_bloginfo('template_directory') . "/images/startup.png' /> \n";

// default ipad
	} elseif ( $settings->classic_webapp_use_loading_img && !$settings->classic_ipad_webapp_loading_img_location && $ipad ) {

		echo "<link rel='apple-touch-startup-image' href='" . wptouch_get_bloginfo('template_directory') . "/images/startup.png' /> \n";

	} elseif ( $settings->classic_webapp_use_loading_img && $settings->classic_webapp_loading_img_location && !$ipad ) {
// custom iPhone
		echo "<link rel='apple-touch-startup-image' href='" . $settings->classic_webapp_loading_img_location . "' /> \n";

	} elseif ( $settings->classic_webapp_use_loading_img && $settings->classic_ipad_webapp_loading_img_location && $ipad ) {
//custom iPad
		echo "<link rel='apple-touch-startup-image' href='" . $settings->classic_ipad_webapp_loading_img_location . "' /> \n";
	} 
}

function classic_show_attached_image_filter( $content ) {
	if ( is_single() && !is_page() ) {
		global $post;
		$photos = get_children( 
			array( 
				'post_parent' => $post->ID, 
				'post_status' => 'inherit', 
				'post_type' => 'attachment', 
				'post_mime_type' => 'image', 
				'order' => 'ASC', 
				'orderby' => 'menu_order ID'
			)
		);
	
		$attachment_html = false;	
		if ( $photos ) {
			// Grab the first photo, may show more than one eventually			
			foreach( $photos as $photo ) {
				$attachment_html = apply_filters( 'wptouch_image_attachment', '<div class="wptouch-image-attachment">' . wp_get_attachment_image( $photo->ID, 'large' ) . '</div>' );
				break;	
			}	
		}
		
		if ( $attachment_html ) {
			$can_show_attachment = true;
			
			// Make sure the image isn't already in the post content
			if ( preg_match( '#src=\"(.*)\"#iU', $attachment_html, $matches ) ) {
				$image_url = str_replace( wptouch_get_bloginfo( 'home' ), '', $matches[1] );
				
				if ( strpos( $content, $image_url ) !== false ) {
					$can_show_attachment = false;	
				}	
			}
			
			if ( $can_show_attachment ) {			
				$settings = wptouch_get_settings();
				switch( $settings->classic_show_attached_image_location ) {
					case 'above':
						$content = $attachment_html . $content;
						break;
					case 'below':
						$content = $content . $attachment_html;
						break;	
				}
			}
		}
	}	
	
	return $content;
}

function classic_instapaper() {
	if ( !class_exists( 'WP_Http' ) ) {
		include_once( ABSPATH . WPINC. '/class-http.php' );
	}
	
	$url = 'http://www.instapaper.com/api/add?url=' . urlencode( wptouch_get_ajax_param( 'url' ) ) . '&title=' . urlencode( wptouch_get_ajax_param( 'title' ) ) . '&username=' . wptouch_get_ajax_param( 'username' ) . '&password=' . wptouch_get_ajax_param( 'password' );
	
	$request = new WP_Http;
	$response = $request->request( $url );
	
	$success = false;
	if ( !is_wp_error( $response ) ) {
		if ( isset( $response['response']['code'] ) && $response['response']['code'] == 201 ) {
			$success = true;
		}
	}
	if ( $success ) { echo '1'; } else { echo '0'; }
}

// Remove whitespace from beginning and end of user agents
function classic_user_agent_filter( $agents ) {
	return trim( $agents );	
}

function classic_localize_scripts( $localize_info ) {
	$localize_info['loading_text'] = __( 'Loading...', 'wptouch-pro' );
	$localize_info['external_link_text'] = __( 'This is an external link.', 'wptouch-pro' );
	$localize_info['open_browser_text'] = __( 'Do you want to open it in Safari?', 'wptouch-pro' );
	$localize_info['instapaper_saved'] = __( 'Saved to Instapaper!', 'wptouch-pro' );
	$localize_info['instapaper_try_again'] = __( 'Please try again', 'wptouch-pro' );
	$localize_info['instapaper_username'] = __( 'Username or E-Mail', 'wptouch-pro' );
	$localize_info['instapaper_password'] = __( 'Password (if you use one)', 'wptouch-pro' );
	$localize_info['classic_post_desc'] = __( 'Enter Description for Post', 'wptouch-pro' );
	$localize_info['leave_a_comment'] = __( 'Leave a comment', 'wptouch-pro' );
	$localize_info['leave_a_reply'] = __( 'Leave a reply to', 'wptouch-pro' );
	$localize_info['comment_failure'] = __( 'Comment publication failed. Please check your comment details and try again.', 'wptouch-pro' );
	$localize_info['comment_success'] = __( 'Your comment was published.', 'wptouch-pro' );
	$localize_info['prowl_failure'] = __( 'Direct messaging failed. Please check your message details and try again.', 'wptouch-pro' );
	$localize_info['validation_message'] = __( 'One or more fields were not completed.', 'wptouch-pro' );
	$localize_info['leave_webapp'] = __( 'Visiting this link will cause you to leave Web-App mode.  Are you sure?', 'wptouch-pro' );

	return $localize_info;	
}

function classic_supported_devices( $devices ) {
	if ( isset( $devices['iphone'] ) ) {
		$settings = wptouch_get_settings();

		if ( strlen( $settings->classic_custom_user_agents  ) ) {
		
			// get user agents
			$agents = explode( "\n", str_replace( "\r\n", "\n", $settings->classic_custom_user_agents ) );
			if ( count( $agents ) ) {	
				// add our custom user agents
				$devices['iphone'] = array_merge( $devices['iphone'], $agents );
			}
		}
	}
	
	return $devices;	
}

function classic_custom_templates( $templates ) {
	$settings = wptouch_get_settings();

	if ( $settings->classic_show_archives ) {
		$templates[ __( 'Archives', 'wptouch-pro' ) ] = array( 'wptouch-archives' );
	}

	if ( $settings->classic_show_links ) {
		$templates[ __( 'Links', 'wptouch-pro' ) ] = array( 'wptouch-links' );
	}
	
	if ( $settings->classic_show_flickr_rss && function_exists( 'get_flickrRSS' ) ) {
		$templates[ __( 'Photos', 'wptouch-pro' ) ] = array( 'wptouch-flickr-photos' );
	}

	return $templates;
}

function classic_was_redirect_target() {
	return ( isset( $_GET['wptouch_custom_redirect'] ) );
}

// Previous + Next Post Functions For Single Post Pages
function classic_get_previous_post_link() {
	$prev_post = get_previous_post(); 
	if ( $prev_post ) {
		$prev_post = get_previous_post( false ); 
		$prev_url = get_permalink( $prev_post->ID ); 
		echo '<a href="' . $prev_url . '" class="nav-back ajax-link">' . __( "Prev", "wptouch-pro" ) . '</a>';
	}
}

function classic_get_next_post_link() {
	$next_post = get_next_post(); 
	if ( $next_post ) {
		$next_post = get_next_post( false );
		$next_url = get_permalink( $next_post->ID ); 
		echo '<a href="' . $next_url . '" class="nav-fwd ajax-link">'. __( "Next", "wptouch-pro" ) . '</a>';
	}
}

// Dynamic archives heading text for archive result pages, and search
function classic_archive_text() {
	global $wp_query;
	$total_results = $wp_query->found_posts;

	if ( !is_home() ) {
		echo '<div class="archive-text">';
	}
	if ( is_search() ) {
		echo sprintf( __( "Search results &rsaquo; %s", "wptouch-pro" ), get_search_query() );
		echo '&nbsp;(' . $total_results . ')';
	} if ( is_category() ) {
		echo sprintf( __( "Categories &rsaquo; %s", "wptouch-pro" ), single_cat_title( "", false ) );
	} elseif ( is_tag() ) {
		echo sprintf( __( "Tags &rsaquo; %s", "wptouch-pro" ), single_tag_title(" ", false ) );
	} elseif ( is_day() ) {
		echo sprintf( __( "Archives &rsaquo; %s", "wptouch-pro" ),  get_the_time( 'F jS, Y' ) );
	} elseif ( is_month() ) {
		echo sprintf( __( "Archives &rsaquo; %s", "wptouch-pro" ),  get_the_time( 'F, Y' ) );
	} elseif ( is_year() ) {
		echo sprintf( __( "Archives &rsaquo; %s", "wptouch-pro" ),  get_the_time( 'Y' ) );
	} elseif ( is_404() ) {
		echo( __( "404 Not Found", "wptouch-pro" ) );
	}
	if ( !is_home() ) {
		echo '</div>';
	}
}

// If Ajax load more is turned off, this shows
function classic_archive_navigation_back() {
	if ( is_search() ) {
		previous_posts_link( __( 'Back in Search', "wptouch-pro" ) );
	} elseif ( is_category() ) {
		previous_posts_link( __( 'Back in Category', "wptouch-pro" ) );
	} elseif ( is_tag() ) {
		previous_posts_link( __( 'Back in Tag', "wptouch-pro" ) );
	} elseif ( is_day() ) {
		previous_posts_link( __( 'Back One Day', "wptouch-pro" ) );
	} elseif ( is_month() ) {
		previous_posts_link( __( 'Back One Month', "wptouch-pro" ) );
	} elseif ( is_year() ) {
		previous_posts_link( __( 'Back One Year', "wptouch-pro" ) );
	}
}

// If Ajax load more is turned off, this shows
function classic_archive_navigation_next() {
	if ( is_search() ) {
		next_posts_link( __( 'Next in Search', "wptouch-pro" ) );
	} elseif ( is_category() ) {		  
		next_posts_link( __( 'Next in Category', "wptouch-pro" ) );
	} elseif ( is_tag() ) {
		next_posts_link( __( 'Next in Tag', "wptouch-pro" ) );
	} elseif ( is_day() ) {
		next_posts_link( __( 'Next One Day', "wptouch-pro" ) );
	} elseif ( is_month() ) {
		next_posts_link( __( 'Next One Month', "wptouch-pro" ) );
	} elseif ( is_year() ) {
		next_posts_link( __( 'Next One Year', "wptouch-pro" ) );
	}
}

function classic_wp_comments_nav_on() {
	if ( get_option( 'page_comments' ) ) {
		return true;
	} else {
		return false;
	}
}

function classic_show_comments_on_pages() {
	$settings = wptouch_get_settings();
	if ( comments_open() ) {
		return $settings->classic_show_comments_on_pages;
	} else {
		return false;
	}
}

function show_webapp_notice() {
	$settings = wptouch_get_settings();
	if ( $settings->classic_webapp_enabled && 
	$settings->classic_show_webapp_notice && 
	!isset( $_COOKIE['notice-bubble'] ) ) {
		return true;
	} else {
		return false;
	}
}

function classic_is_ajax_enabled() {
	$settings = wptouch_get_settings();
	return $settings->classic_ajax_mode_enabled;
}

function classic_use_calendar_icons() {
	$settings = wptouch_get_settings();
	return $settings->classic_icon_type == 'calendar';
}

function classic_use_thumbnail_icons() {
	$settings = wptouch_get_settings();
	return ( $settings->classic_icon_type != 'calendar' && $settings->classic_icon_type != 'none' );
}

function classic_show_admin_menu_link() {
	$settings = wptouch_get_settings();
	if ( classic_show_account_tab() ) {
		if ( $settings->classic_show_admin_menu_link ) {
			return true;
		} else {
			return false;
		}
	}
}

function classic_show_account_tab() {
	$settings = wptouch_get_settings();
	if ( get_option( 'comment_registration' ) || get_option( 'users_can_register' ) || $settings->classic_show_account ) {
		return true;
	} else {
		return false;
	}
}

function classic_show_profile_menu_link() {
	$settings = wptouch_get_settings();
	if ( classic_show_account_tab() ) {
		if ( $settings->classic_show_profile_menu_link ) {
			return true;
		} else {
			return false;
		}
	}
}

function classic_show_author_in_posts() {
	$settings = wptouch_get_settings();
	return $settings->classic_show_post_author;
}

function classic_show_categories_in_posts() {
	$settings = wptouch_get_settings();
	return $settings->classic_show_post_categories;
}

function classic_show_tags_in_posts() {
	$settings = wptouch_get_settings();
	return $settings->classic_show_post_tags;
}

function classic_show_date_in_posts() {
	$settings = wptouch_get_settings();
	return $settings->classic_show_post_date;
}

function classic_exclude_categories( $query ) {
	$settings = wptouch_get_settings();
	$cats = $settings->classic_excluded_categories;
	
	if ( $cats ) {
		$icats = explode( ",", $cats );
		$new_cats = array();
		
		foreach( $icats as $icat ) {
			$new_cats[] = "-" . $icat;
		}
	
		$cats = implode( ",",  $new_cats );
		if ( ( $query->is_home || $query->is_search || $query->is_archive || $query->is_feed ) && !$query->is_category ) {
			$query->set( 'cat', $cats );
		}
	}
	
	return $query;
}

// Check what order comments are displayed, governs whether 'load more comments' link uses previous_ or next_ function
function classic_comments_newer() {
	if ( get_option( 'default_comments_page' ) == 'newest' ) {
			return true;
		} else {
			return false;
		}
}

// Thumbnail stuff added in 2.0.4
function classic_has_post_thumbnail() {
	global $post;
	
	$settings = wptouch_get_settings();
	
	$has_post_thumbnail = false;
	
	switch( $settings->classic_icon_type ) {
		case 'thumbnails':
			$has_post_thumbnail = function_exists( 'has_post_thumbnail' ) && has_post_thumbnail();
			break;
		case 'simple_thumbs':
			$has_post_thumbnail = function_exists( 'p75GetThumbnail' ) && p75HasThumbnail( $post->ID );
			break;
		case 'custom_thumbs':
			$has_post_thumbnail = get_post_meta( $post->ID, $settings->classic_custom_field_thumbnail_name, true ) || get_post_meta( $post->ID, 'Thumbnail', true ) || get_post_meta( $post->ID, 'thumbnail', true );
			break;
	}

	return $has_post_thumbnail;
}

function classic_the_post_thumbnail( $thumbnail ) {
	global $post;
	
	$settings = wptouch_get_settings();	
	$custom_field_name = $settings->classic_custom_field_thumbnail_name;
	
	switch( $settings->classic_icon_type ) {
		case 'thumbnails':
			if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail() ) {
				return $thumbnail;	
			}
			break;
		case 'simple_thumbs':
			if ( function_exists( 'p75GetThumbnail' ) && p75HasThumbnail( $post->ID ) ) {
				return p75GetThumbnail( $post->ID );	
			}
			break;
		case 'custom_thumbs':
			if ( get_post_meta( $post->ID, $custom_field_name, true ) ) {
				return get_post_meta( $post->ID, $custom_field_name, true );
			} else if ( get_post_meta( $post->ID, 'Thumbnail', true ) ) {
				return get_post_meta( $post->ID, 'Thumbnail', true );
			} else if ( get_post_meta( $post->ID, 'thumbnail', true ) ) {
				return get_post_meta( $post->ID, 'thumbnail', true );
			}
			
			break;
	}		
	// return default if none of those exist
	return wptouch_get_bloginfo( 'template_directory' ) . '/images/default-thumbnail.png';
}

function classic_thumbs_on_single() {
	$settings = wptouch_get_settings();	
	if ( $settings->classic_thumbs_on_single ) {
		return true;
	} else {
		return false;
	}
}

function classic_thumbs_on_pages() {
	$settings = wptouch_get_settings();	
	if ( $settings->classic_thumbs_on_pages && classic_has_post_thumbnail() ) {
		return true;
	} else {
		return false;
	}
}

//Single Post Page
function classic_show_date_single() {
	$settings = wptouch_get_settings();
	return $settings->classic_show_post_date_single;
}

function classic_show_author_single() {
	$settings = wptouch_get_settings();
	return $settings->classic_show_post_author_single;
}

function classic_show_cats_single() {
	$settings = wptouch_get_settings();
	return $settings->classic_show_post_cats_single;
}

function classic_show_tags_single() {
	$settings = wptouch_get_settings();
	return $settings->classic_show_post_tags_single;
}

function classic_show_share_single() {
	$settings = wptouch_get_settings();
	return $settings->classic_show_share_save;
}

//-- Default Settings --//

// All default settings must be added to the $settings object here
// All settings should be properly namespaced, i.e. theme_name_my_setting instead of just my_setting
function classic_default_settings( $settings ) {

//General Settings
	$settings->classic_ajax_mode_enabled = true;
	$settings->classic_use_compat_css = true;
	$settings->classic_excluded_categories = '';
	
//Style and Appearance
	$settings->classic_header_img_location = '';
	$settings->classic_retina_header_img_location = '';
	$settings->classic_header_shading_style = 'glossy';
	$settings->classic_header_font = 'Helvetica-Bold';
	$settings->classic_header_title_font_size = '19px';
	$settings->classic_header_color_style = 'classic-black';
	$settings->classic_show_header_icon = true;

	$settings->classic_general_font = 'Helvetica';
	$settings->classic_general_font_size = '13px';
	$settings->classic_general_font_color = '333333';

	$settings->classic_post_title_font = 'Helvetica-Bold';
	$settings->classic_post_title_font_size = '15px';
	$settings->classic_post_title_font_color = '333333';

	$settings->classic_post_body_font = 'Helvetica';
	$settings->classic_post_body_font_size = '13px';
	
	$settings->classic_text_justification = 'left-justify';

	$settings->classic_link_color = '006bb3';
	$settings->classic_context_headers_color = '475d79';
	$settings->classic_footer_text_color = '666666';
	$settings->classic_text_shade_color = 'light';

	$settings->classic_background_image = 'ipad-thatch-light';
	$settings->classic_background_repeat	 = 'repeat';
	$settings->classic_background_color = 'CCCCCC';
	$settings->classic_custom_background_image = '';

//Post Icon Settings
	$settings->classic_icon_type = 'calendar';
	$settings->classic_calendar_icon_bg = 'cal-colors';
	$settings->classic_custom_cal_icon_color = '';
	$settings->classic_custom_field_thumbnail_name = 'thumbnail';
	$settings->classic_thumbs_on_single = false;
	$settings->classic_thumbs_on_pages = false;

//Menu Settings
	$settings->classic_use_menu_icon = true;
	$settings->make_menu_relative = true;
	$settings->classic_show_categories = true;
	$settings->classic_show_tags = true;
	$settings->classic_show_account = false;
	$settings->classic_show_admin_menu_link = true;
	$settings->classic_show_profile_menu_link = true;
	$settings->classic_show_archives = false;
	$settings->classic_show_links = false;
	$settings->classic_show_flickr_rss = false;
	$settings->classic_show_search = true;

//Post Settings
	$settings->classic_show_post_author = true;
	$settings->classic_show_post_categories = true;
	$settings->classic_show_post_tags = true;
	$settings->classic_show_post_date = true;
	$settings->classic_show_excerpts = 'excerpts-hidden';
	
// Single Post Settings
	$settings->classic_show_post_author_single = true;
	$settings->classic_show_post_date_single = true;
	$settings->classic_show_post_cats_single = true;
	$settings->classic_show_post_cats_single = true;
	$settings->classic_show_post_tags_single = true;
	$settings->classic_show_share_save = true;
	$settings->classic_hide_responses = false;
	$settings->classic_show_attached_image = false;
	$settings->classic_show_attached_image_location = 'above';

//Page Options
	$settings->classic_show_comments_on_pages = false;

//UA Settings
	$settings->classic_custom_user_agents = '';

//WebApp Settings
	$settings->classic_webapp_enabled = true;
	$settings->classic_webapp_use_loading_img = false;
	$settings->classic_webapp_status_bar_color = 'default';
	$settings->classic_enable_persistent = true;
	$settings->classic_show_webapp_notice = false;
	$settings->classic_webapp_loading_img_location = '';
	$settings->classic_ipad_webapp_loading_img_location = '';
	
// iPad Settings
	//Style
	$settings->classic_ipad_theme_color = 'grey';
	$settings->classic_ipad_logo_image = '';
	$settings->classic_ipad_content_bg = 'ipad-content-default';
	$settings->classic_ipad_content_bg_custom = '';
	$settings->classic_ipad_sidebar_bg = 'ipad-sidebar-default';
	$settings->classic_ipad_sidebar_bg_custom = '';

	$settings->classic_ipad_general_font = $settings->classic_general_font;
	$settings->classic_ipad_general_font_size = '15px';
	$settings->classic_ipad_general_font_color = $settings->classic_general_font_color;
	
	$settings->classic_ipad_post_title_font = $settings->classic_post_title_font;
	$settings->classic_ipad_post_title_font_size = '22px';
	$settings->classic_ipad_post_title_font_color = '333333';

	$settings->classic_ipad_post_body_font = $settings->classic_post_body_font;
	$settings->classic_ipad_post_body_font_size = '15px';
	
	$settings->classic_ipad_text_justification = 'left-justify';

	$settings->classic_ipad_link_color = $settings->classic_link_color;
	$settings->classic_ipad_active_link_color = '000';
	$settings->classic_ipad_context_headers_color = $settings->classic_context_headers_color;
	$settings->classic_ipad_footer_text_color = $settings->classic_footer_text_color;
	$settings->classic_ipad_text_shade_color = $settings->classic_text_shade_color;

	//General
	$settings->classic_ipad_home_button = true;
	$settings->classic_ipad_blog_button = true;
	$settings->classic_ipad_recent_posts = true;
	$settings->classic_ipad_popular_posts = true;
	$settings->classic_ipad_popover_tags = true;
	$settings->classic_ipad_popover_cats = true;
	$settings->classic_ipad_account_button = false;
	$settings->classic_ipad_show_flickr_button = false;
	$settings->classic_ipad_search_button = true;

	return $settings;
}

function classic_theme_thumbnail_options() {
	$thumbnail_options = array();

	//Calendar Icons
	$thumbnail_options['calendar'] = __( 'Calendar', 'wptouch-pro' );

	// WordPress 2.9+ thumbs
	if ( function_exists( 'add_theme_support' ) ) {
		$thumbnail_options['thumbnails'] = __( 'WordPress Thumbnails/Featured Images', 'wptouch-pro' );
	}	

	// 'thumbnail' Custom field thumbnails
	$thumbnail_options['custom_thumbs'] = __( 'Custom Field Thumbnails', 'wptouch-pro' );

	// Simple Post Thumbnails Plugin
	if (function_exists('p75GetThumbnail')) { 
		$thumbnail_options['simple_thumbs'] = __( 'Simple Post Thumbnails Plugin', 'wptouch-pro' );
	}
	
	// Show nothing!
	$thumbnail_options['none'] = __( 'None', 'wptouch-pro' );	
	
	return $thumbnail_options;
}

// The administrational page for the classic theme is constructed here:

function classic_admin_menu( $menu ) {
	if ( function_exists( 'get_flickrRSS' ) ) {
		$flickr_mobile_rss_option = array( 'checkbox', 'classic_show_flickr_rss', __( 'Use WPtouch Photos template', "wptouch-pro" ), __( "Shows the latest 20 photos from your Flickr RSS feed.", "wptouch-pro"  ) );
		$flickr_ipad_rss_option = array( 'checkbox', 'classic_ipad_show_flickr_button', __( 'Show Flickr button', "wptouch-pro" ), __( "Requires the FlickrRSS plugin to be installed.", "wptouch-pro"  ) );	
	} else {
		$flickr_mobile_rss_option = array( 'checkbox-disabled', 'classic_show_flickr_rss', __( 'Use WPtouch Photos template', "wptouch-pro" ), __( "Requires the FlickrRSS plugin to be installed.", "wptouch-pro"  ) );		
		$flickr_ipad_rss_option = array( 'checkbox-disabled', 'classic_ipad_show_flickr_button', __( 'Show Flickr button', "wptouch-pro" ), __( "Requires the FlickrRSS plugin to be installed.", "wptouch-pro"  ) );	
	}
	
	$menu = array(
		__( "Theme General", "wptouch-pro" ) => array ( 'general', 
			array(
				array( 'section-start', 'misc-options', __( 'Miscellaneous Options', "wptouch-pro" ) ),
				array( 'copytext', 'ipad-copytext-info', 'Blue dot settings are shared between both Mobile + iPad.', "wptouch-pro" ),
				array( 'checkbox', 'classic_ajax_mode_enabled', __( 'Enable AJAX "Load More" link for posts and comments', "wptouch-pro" ), __( 'Posts and comments will be appended to existing content with an AJAX "Load More..." link. If unchecked regular post/comment pagination will be used.', "wptouch-pro" ), array( 'ipad' ) ),
				array( 'checkbox', 'classic_use_compat_css', __( 'Use compatibility CSS', "wptouch-pro" ), __( 'Add the compat.css file from the theme folder. Contains various CSS declarations for a variety of plugins.', "wptouch-pro" ), array( 'ipad' ) ),
				array( 'text', 'classic_excluded_categories', __( 'Excluded Categories (Comma list of category IDs)', "wptouch-pro" ), __( 'Posts in these categories will not be shown in WPtouch. (e.g. 3,4,5)', "wptouch-pro" ), array( 'ipad' ) ),
				array( 'section-end' ),
				array( 'spacer' ),
				array( 'section-start', 'web-app-settings', __( 'Web-App Mode', "wptouch-pro" ) ),	
				array( 'checkbox', 'classic_webapp_enabled', __( 'Enable Web-App Mode', "wptouch-pro" ), __( 'When checked WPtouch will allow iPhone, iPod touch and iPad visitors to bookmark your site to their home-screens as a web application.', "wptouch-pro" ), array( 'ipad' ) ),
				array( 'checkbox', 'classic_enable_persistent', __( 'Enable persistence', "wptouch-pro" ), __( 'When checked WPtouch will remember and load the last visited page or post for a visitor when entering Web-App Mode.', "wptouch-pro" ), array( 'ipad' ) ),
				array( 'checkbox', 'classic_webapp_use_loading_img', __( 'Use startup splash screen', "wptouch-pro" ), __( 'When checked your website will show a startup image in web-app mode.', "wptouch-pro" ), array( 'ipad' ) ),
				array( 'list', 'classic_webapp_status_bar_color', __( 'Status Bar Color', "wptouch-pro" ), __( 'Choose between grey (default), black or black-translucent.', "wptouch-pro" ), 
					array( 
						'default' => __( 'Default (Grey)', 'wptouch-pro' ), 
						'black' => __( 'Black', 'wptouch-pro' ), 
						'black-translucent' => __( 'Black Translucent', 'wptouch-pro' )
					)
				),
				array( 'checkbox', 'classic_show_webapp_notice', __( 'Show a notice bubble for iPhone, iPod touch & iPad visitors about web-app mode', "wptouch-pro" ), __( 'When checked WPtouch will show a notice bubble on first visit letting users know about your web-app enabled mobile website.', "wptouch-pro" ), array( 'ipad' ) ),
				array( 'spacer' ),
				array( 'text', 'classic_webapp_loading_img_location', __( 'URL for iPhone startup image (320px by 460px .png)', "wptouch-pro" ), __( 'If no path is specified the default loading image will be used (if enabled).', "wptouch-pro" ) ),
				array( 'text', 'classic_ipad_webapp_loading_img_location', __( 'URL for iPad startup image (768px by 1004px .png)', "wptouch-pro" ), __( 'If no path is specified the default loading image will be used (if enabled).', "wptouch-pro" ) ),
				array( 'copytext', 'webapp-copytext-info', sprintf( __( '%sNOTE: Changing the Startup Image setting will require you to re-add the home-screen icon on an iOS device for the change to take effect.%s', "wptouch-pro" ), '<small>', '</small>' ) ),
				array( 'section-end' )
				) 
			),	
		__( "Menu, Posts and Pages", "wptouch-pro" ) => array ( 'post-theme', 
			array(		
				array( 'section-start', 'menu-options', __( 'Theme Menu', "wptouch-pro" ) ),
				array( 'checkbox', 'make_menu_relative', __( 'Make menu drop-down relatively positioned', "wptouch-pro" ), __( 'Will make the menu push the content below it down the page. Fixes issues with videos/YouTube overlaying the content of the menu, and may improve menu performance on some devices.', "wptouch-pro"  ) ),
				array( 'checkbox', 'classic_use_menu_icon', __( 'Use menu icon for menu button', "wptouch-pro" ), __( 'If unchecked the word "Menu" will be shown instead of an icon.', "wptouch-pro"  ) ),
				array( 'checkbox', 'classic_show_categories', __( 'Show Categories in tab-bar', "wptouch-pro" ) ),
				array( 'checkbox', 'classic_show_tags', __( 'Show Tags in tab-bar', "wptouch-pro" ) ),
				array( 'checkbox', 'classic_show_account', __( 'Show Account in tab-bar', "wptouch-pro" ), __( 'Will always show account login/links in tab bar, even if registration for your website is not allowed.', "wptouch-pro"  ) ),
				array( 'checkbox', 'classic_show_search', __( 'Show Search in tab-bar', "wptouch-pro" ) ),
				array( 'checkbox', 'classic_show_admin_menu_link', __( 'Show "Admin" in Account tab links', "wptouch-pro" ), __( 'Shows an "Admin" menu link for logged in users that have edit posts capability.', "wptouch-pro"  ), array( 'ipad' ) ),
				array( 'checkbox', 'classic_show_profile_menu_link', __( 'Show "Profile" in Account tab links', "wptouch-pro" ), __( 'Show a "Profile" link for all logged in users.', "wptouch-pro"  ), array( 'ipad' ) ),
				array( 'spacer' ),
				array( 'copytext', 'copytext-info-push', __( 'The push message and account tabs are shown/hidden automatically.', "wptouch-pro" ), '', array( 'ipad' ) ),
				array( 'section-end' )	,
				array( 'spacer' ),
				array( 'section-start', 'template-options', __( 'Theme Templates', "wptouch-pro" ) ),
				array( 'copytext', 'copytext-info-templates', __( 'These templates are custom to WPtouch. They trigger a new menu item which can be configured in the menu settings once activated here.', "wptouch-pro" ), '', array( 'ipad' ) ),
				array( 'checkbox', 'classic_show_archives', __( 'Use WPtouch Archives template', "wptouch-pro" ), '', array( 'ipad' ) ),
				array( 'checkbox', 'classic_show_links', __( 'Use WPtouch Links template', "wptouch-pro" ), '', array( 'ipad' ) ),
				$flickr_mobile_rss_option,
				array( 'section-end' )	,
				array( 'spacer' ),
				array( 'section-start', 'post-options', __( 'Blog Listings', "wptouch-pro" ) ),
				array( 'copytext', 'copytext-info-post-opts', __( 'These settings affect the display of posts on the WPtouch blog, blog archive & search pages.', "wptouch-pro" ) ),
				array( 'checkbox', 'classic_show_post_author', __( 'Show author name', "wptouch-pro" ), '', array( 'ipad' ) ),
				array( 'checkbox', 'classic_show_post_categories', __( 'Show categories', "wptouch-pro" ), '', array( 'ipad' ) ),
				array( 'checkbox', 'classic_show_post_tags', __( 'Show tags', "wptouch-pro" ), '', array( 'ipad' ) ),
				array( 'checkbox', 'classic_show_post_date', __( 'Show date', "wptouch-pro" ), __( 'Will show the date in post listings where thumbnails or none are selected in the post icon settings. Does not affect calendar icons.', "wptouch-pro" ), array( 'ipad' ) ),
				array( 'list', 'classic_show_excerpts', __( 'Excerpt/Content Options', "wptouch-pro" ), __( 'Choose how excerpts are handled in the blog. Search and archive templates always use excerpts.', "wptouch-pro" ), 
					array( 
						'excerpts-hidden' => __( 'Excerpts hidden', 'wptouch-pro' ), 
						'excerpts-shown' => __( 'Excerpts shown', 'wptouch-pro' ), 
						'full-hidden' => __( 'Full posts hidden', 'wptouch-pro' ),	
						'full-shown' => __( 'Full posts shown', 'wptouch-pro' ),	
						'first-full-hidden' => __( 'First w/ full post shown, others excerpted and hidden', 'wptouch-pro' ), 
						'first-full-shown' => __( 'First w/ full post shown, others excerpted and shown', 'wptouch-pro' ) 
					) 
				),	
				array( 'section-end' )	,
				array( 'spacer' ),
				array( 'section-start', 'single-post-options', __( 'Single Posts', "wptouch-pro" ) ),
				array( 'checkbox', 'classic_show_post_author_single', __( 'Show author in post header', "wptouch-pro" ), '', array( 'ipad' ) ),
				array( 'checkbox', 'classic_show_post_date_single', __( 'Show date in post header', "wptouch-pro" ), '', array( 'ipad' ) ),
				array( 'checkbox', 'classic_show_post_cats_single', __( 'Show categories post footer', "wptouch-pro" ), '', array( 'ipad' ) ),
				array( 'checkbox', 'classic_show_post_tags_single', __( 'Show tags post footer', "wptouch-pro" ), '', array( 'ipad' ) ),
				array( 'checkbox', 'classic_show_share_save', __( 'Show "Share/Save" button', "wptouch-pro" ), __('The "Share/Save" button allows visitors to bookmark your site, share on popular services and via e-mail, or save to Instapaper. You may want to disable it if you use another sharing plugin.', "wptouch-pro" ), array( 'ipad' ) ),
				array( 'checkbox', 'classic_hide_responses', __( 'Hide Responses', "wptouch-pro" ), __('Hides comments, trackbacks and pingbacks by default, until a visitor clicks to show them. Speeds up load times if hidden.', "wptouch-pro" ) ),
				array( 'list', 'classic_show_attached_image_location', __( 'Attached image location in content', 'wptouch-pro' ), '', 
					array(
						'above' => __( 'Above content', 'wptouch-pro' ),
						'below' => __( 'Below content', 'wptouch-pro' )
					), array( 'ipad' )
				),				
				array( 'checkbox', 'classic_show_attached_image', __( 'Show attached image in post content', 'wptouch-pro' ), __( 'This option can be used to include an attached image in the post content.  The image is only included if it doesn\'t already exist in the post content.', 'wptouch-pro' ), array( 'ipad' ) ),
				array( 'section-end' )	,
				array( 'spacer' ),
				array( 'section-start', 'page-options', __( 'Pages', "wptouch-pro" ) ),
				array( 'checkbox', 'classic_show_comments_on_pages', __( 'Show comments on pages', "wptouch-pro" ), __( 'Enabling this setting will cause comments to be shown on pages, if they are enabled in the WordPress settings.', "wptouch-pro" ), array( 'ipad' ) ),
				array( 'section-end' )	
			)
		),
		__( 'Style / Appearance', "wptouch-pro" ) => array( 'style-options',
			array(
				array( 'section-start', 'header-style-settings', __( 'Header Styling', "wptouch-pro" ) ),	
				array( 'text', 'classic_header_img_location', __( 'URL to a custom header logo', "wptouch-pro" ), __( 'Should be 270px (width) by 44px (height). Transparent .PNG is recommended. If no image is specified here the default Site Icon and Site Title will be used.', "wptouch-pro" ) ),
				array( 'text', 'classic_retina_header_img_location', __( 'URL to a custom header logo (Retina Sized @ 2x)', "wptouch-pro" ), __( 'Should be 540px (width) by 88px (height). Transparent .PNG is recommended.', "wptouch-pro" ) ),
				array( 'list', 'classic_header_font', __( 'Header title font', "wptouch-pro" ), '', 
					array( 
						'ArialMT' => __( 'ArialMT', 'wptouch-pro' ),
						'Arial-BoldMT' => __( 'ArialMT (Bold)', 'wptouch-pro' ),
						'Baskerville' => __( 'Baskerville', 'wptouch-pro' ),
						'Baskerville-Bold' => __( 'Baskerville (Bold)', 'wptouch-pro' ),
						'Cochin' => __( 'Cochin', 'wptouch-pro' ),
						'Cochin-Bold' => __( 'Cochin (Bold)', 'wptouch-pro' ),
						'Courier' => __( 'Courier', 'wptouch-pro' ),
						'Futura-Medium' => __( 'Futura', 'wptouch-pro' ),
						'Georgia' => __( 'Georgia', 'wptouch-pro' ),
						'Georgia-Bold' => __( 'Georgia (Bold)', 'wptouch-pro' ),
						'Helvetica' => __( 'Helvetica', 'wptouch-pro' ), 
						'Helvetica-Bold' => __( 'Helvetica (Bold)', 'wptouch-pro' ), 
						'HelveticaNeue' => __( 'Helvetica Neue', 'wptouch-pro' ),
						'HelveticaNeue-Bold' => __( 'Helvetica Neue (Bold)', 'wptouch-pro' ),
						'Palatino-Roman' => __( 'Palatino', 'wptouch-pro' ),
						'Thonburi' => __( 'Thonburi', 'wptouch-pro' ),
						'Thonburi-Bold' => __( 'Thonburi (Bold)', 'wptouch-pro' ),
						'TimesNewRomanPSMT' => __( 'Times New Roman', 'wptouch-pro' ),
						'TrebuchetMS' => __( 'Trebuchet MS', 'wptouch-pro' ),
						'TrebuchetMS-Bold' => __( 'Trebuchet MS (Bold)', 'wptouch-pro' ),
						'Verdana' => __( 'Verdana', 'wptouch-pro' ),
						'Verdana-Bold' => __( 'Verdana (Bold)', 'wptouch-pro' )
					) 
				),
				array( 'list', 'classic_header_title_font_size', __( 'Header title font size', "wptouch-pro" ), '', 
					array( 
						'16px' => __( '16px', 'wptouch-pro' ), 
						'17px' => __( '17px', 'wptouch-pro' ), 
						'18px' => __( '18px', 'wptouch-pro' ), 
						'19px' => __( '19px', 'wptouch-pro' ),
						'20px' => __( '20px', 'wptouch-pro' ),
						'21px' => __( '21px', 'wptouch-pro' ),
						'22px' => __( '22px', 'wptouch-pro' ),
						'23px' => __( '23px', 'wptouch-pro' ),
						'24px' => __( '24px', 'wptouch-pro' )
					) 
				),
				array( 'list', 'classic_header_color_style', __( 'Header Color Group', "wptouch-pro" ), __( 'Choose between a variety of color package header styles.', "wptouch-pro" ), 
					array( 
						'classic-black' => __( 'Classic Black (Default)', 'wptouch-pro' ), 
						'silver-sheen' => __( 'Silver Sheen', 'wptouch-pro' ),
						'blue-ocean' => __( 'Blue Ocean', 'wptouch-pro' ),
						'red-bull' => __( 'Red Bull', 'wptouch-pro' ),
						'green-planet' => __( 'Green Planet', 'wptouch-pro' ),
						'sunkissed-orange' => __( 'Sunkissed Orange', 'wptouch-pro' ),
						'violet-purple' => __( 'Violet Purple', 'wptouch-pro' )
					) 
				),
				array( 'list', 'classic_header_shading_style', __( 'Header Shading Gradient Style', "wptouch-pro" ), __( 'Changes the default glossy look to other styles.', "wptouch-pro" ), 
					array( 
						'glossy' => __( 'Default (Glossy)', 'wptouch-pro' ), 
						'matte' => __( 'Matte', 'wptouch-pro' ),
						'grainy' => __( 'Grainy', 'wptouch-pro' ),
						'none' => __( 'None', 'wptouch-pro' )
					) 
				),
				array( 'checkbox', 'classic_show_header_icon', __( 'Show header icon', "wptouch-pro" ), __( 'Show/hide the header site icon beside your site title. If you use a custom logo image this setting will not apply.', "wptouch-pro" ) ),
				array( 'section-end' ),
				array( 'spacer' ),
				array( 'section-start', 'body-style-settings', __( 'Body and Post Styling', "wptouch-pro" ) ),	
				array( 'list', 'classic_general_font', __( 'General site font', "wptouch-pro" ), '', 
					array( 
						'ArialMT' => __( 'ArialMT', 'wptouch-pro' ),
						'Arial-BoldMT' => __( 'ArialMT (Bold)', 'wptouch-pro' ),
						'Baskerville' => __( 'Baskerville', 'wptouch-pro' ),
						'Baskerville-Bold' => __( 'Baskerville (Bold)', 'wptouch-pro' ),
						'Cochin' => __( 'Cochin', 'wptouch-pro' ),
						'Cochin-Bold' => __( 'Cochin (Bold)', 'wptouch-pro' ),
						'Courier' => __( 'Courier', 'wptouch-pro' ),
						'Futura-Medium' => __( 'Futura', 'wptouch-pro' ),
						'Georgia' => __( 'Georgia', 'wptouch-pro' ),
						'Georgia-Bold' => __( 'Georgia (Bold)', 'wptouch-pro' ),
						'Helvetica' => __( 'Helvetica', 'wptouch-pro' ), 
						'Helvetica-Bold' => __( 'Helvetica (Bold)', 'wptouch-pro' ), 
						'HelveticaNeue' => __( 'Helvetica Neue', 'wptouch-pro' ),
						'HelveticaNeue-Bold' => __( 'Helvetica Neue (Bold)', 'wptouch-pro' ),
						'Palatino-Roman' => __( 'Palatino', 'wptouch-pro' ),
						'Thonburi' => __( 'Thonburi', 'wptouch-pro' ),
						'Thonburi-Bold' => __( 'Thonburi (Bold)', 'wptouch-pro' ),
						'TimesNewRomanPSMT' => __( 'Times New Roman', 'wptouch-pro' ),
						'TrebuchetMS' => __( 'Trebuchet MS', 'wptouch-pro' ),
						'TrebuchetMS-Bold' => __( 'Trebuchet MS (Bold)', 'wptouch-pro' ),
						'Verdana' => __( 'Verdana', 'wptouch-pro' ),
						'Verdana-Bold' => __( 'Verdana (Bold)', 'wptouch-pro' )
					) 
				),
				array( 'list', 'classic_general_font_size', __( 'General site font size', "wptouch-pro" ), '', 
					array( 
						'11px' => __( '11px', 'wptouch-pro' ), 
						'12px' => __( '12px', 'wptouch-pro' ),
						'13px' => __( '13px', 'wptouch-pro' ),
						'14px' => __( '14px', 'wptouch-pro' ),
						'15px' => __( '15px', 'wptouch-pro' ),
						'16px' => __( '16px', 'wptouch-pro' )
					) 
				),
				array( 'list', 'classic_post_title_font', __( 'Post title font', "wptouch-pro" ), '', 
					array( 
						'ArialMT' => __( 'ArialMT', 'wptouch-pro' ),
						'Arial-BoldMT' => __( 'ArialMT (Bold)', 'wptouch-pro' ),
						'Baskerville' => __( 'Baskerville', 'wptouch-pro' ),
						'Baskerville-Bold' => __( 'Baskerville (Bold)', 'wptouch-pro' ),
						'Cochin' => __( 'Cochin', 'wptouch-pro' ),
						'Cochin-Bold' => __( 'Cochin (Bold)', 'wptouch-pro' ),
						'Courier' => __( 'Courier', 'wptouch-pro' ),
						'Futura-Medium' => __( 'Futura', 'wptouch-pro' ),
						'Georgia' => __( 'Georgia', 'wptouch-pro' ),
						'Georgia-Bold' => __( 'Georgia (Bold)', 'wptouch-pro' ),
						'Helvetica' => __( 'Helvetica', 'wptouch-pro' ), 
						'Helvetica-Bold' => __( 'Helvetica (Bold)', 'wptouch-pro' ), 
						'HelveticaNeue' => __( 'Helvetica Neue', 'wptouch-pro' ),
						'HelveticaNeue-Bold' => __( 'Helvetica Neue (Bold)', 'wptouch-pro' ),
						'Palatino-Roman' => __( 'Palatino', 'wptouch-pro' ),
						'Thonburi' => __( 'Thonburi', 'wptouch-pro' ),
						'Thonburi-Bold' => __( 'Thonburi (Bold)', 'wptouch-pro' ),
						'TimesNewRomanPSMT' => __( 'Times New Roman', 'wptouch-pro' ),
						'TrebuchetMS' => __( 'Trebuchet MS', 'wptouch-pro' ),
						'TrebuchetMS-Bold' => __( 'Trebuchet MS (Bold)', 'wptouch-pro' ),
						'Verdana' => __( 'Verdana', 'wptouch-pro' ),
						'Verdana-Bold' => __( 'Verdana (Bold)', 'wptouch-pro' )
					) 
				),
				array( 'list', 'classic_post_title_font_size', __( 'Post title font size', "wptouch-pro" ), '', 
					array( 
						'14px' => __( '14px', 'wptouch-pro' ), 
						'15px' => __( '15px', 'wptouch-pro' ), 
						'16px' => __( '16px', 'wptouch-pro' ), 
						'17px' => __( '17px', 'wptouch-pro' ), 
						'18px' => __( '18px', 'wptouch-pro' ), 
						'19px' => __( '19px', 'wptouch-pro' ),
						'20px' => __( '20px', 'wptouch-pro' ),
						'21px' => __( '21px', 'wptouch-pro' ),
						'22px' => __( '22px', 'wptouch-pro' )
					) 
				),
				array( 'list', 'classic_post_body_font', __( 'Post body font', "wptouch-pro" ), '', 
					array( 
						'ArialMT' => __( 'ArialMT', 'wptouch-pro' ),
						'Arial-BoldMT' => __( 'ArialMT (Bold)', 'wptouch-pro' ),
						'Baskerville' => __( 'Baskerville', 'wptouch-pro' ),
						'Baskerville-Bold' => __( 'Baskerville (Bold)', 'wptouch-pro' ),
						'Cochin' => __( 'Cochin', 'wptouch-pro' ),
						'Cochin-Bold' => __( 'Cochin (Bold)', 'wptouch-pro' ),
						'Courier' => __( 'Courier', 'wptouch-pro' ),
						'Futura-Medium' => __( 'Futura', 'wptouch-pro' ),
						'Georgia' => __( 'Georgia', 'wptouch-pro' ),
						'Georgia-Bold' => __( 'Georgia (Bold)', 'wptouch-pro' ),
						'Helvetica' => __( 'Helvetica', 'wptouch-pro' ), 
						'Helvetica-Bold' => __( 'Helvetica (Bold)', 'wptouch-pro' ), 
						'HelveticaNeue' => __( 'Helvetica Neue', 'wptouch-pro' ),
						'HelveticaNeue-Bold' => __( 'Helvetica Neue (Bold)', 'wptouch-pro' ),
						'Palatino-Roman' => __( 'Palatino', 'wptouch-pro' ),
						'Thonburi' => __( 'Thonburi', 'wptouch-pro' ),
						'Thonburi-Bold' => __( 'Thonburi (Bold)', 'wptouch-pro' ),
						'TimesNewRomanPSMT' => __( 'Times New Roman', 'wptouch-pro' ),
						'TrebuchetMS' => __( 'Trebuchet MS', 'wptouch-pro' ),
						'TrebuchetMS-Bold' => __( 'Trebuchet MS (Bold)', 'wptouch-pro' ),
						'Verdana' => __( 'Verdana', 'wptouch-pro' ),
						'Verdana-Bold' => __( 'Verdana (Bold)', 'wptouch-pro' )
					) 
				),
				array( 'list', 'classic_post_body_font_size', __( 'Post body font size', "wptouch-pro" ), '', 
					array( 
						'11px' => __( '11px', 'wptouch-pro' ), 
						'12px' => __( '12px', 'wptouch-pro' ),
						'13px' => __( '13px', 'wptouch-pro' ),
						'14px' => __( '14px', 'wptouch-pro' ),
						'15px' => __( '15px', 'wptouch-pro' )
					) 
				),
				array( 'list', 'classic_text_justification', __( 'Text justification in post listings, single posts / comments, and pages', "wptouch-pro" ), '',
					array( 
						'left-justify' => __( 'Left', 'wptouch-pro' ),
						'full-justify' => __( 'Full', 'wptouch-pro' ),
						'right-justify' => __( 'Right RTL (experimental)', 'wptouch-pro' )
					) 
				),	
				array( 'text', 'classic_general_font_color', __( 'Sitewide font color', "wptouch-pro" ), __( 'e.g. FFFFFF, (Hex without #)', "wptouch-pro"  ) ),
				array( 'text', 'classic_post_title_font_color', __( 'Sitewide post title color', "wptouch-pro" ) ),
				array( 'text', 'classic_link_color', __( 'Sitewide link color', "wptouch-pro" ) ),
				array( 'text', 'classic_context_headers_color', __( 'Context and label headings color', "wptouch-pro" ), __( 'The context header shows for results pages (e.g. Search Results, Leave A Reply) and other labels and headings.', "wptouch-pro"  ) ),
				array( 'text', 'classic_footer_text_color', __( 'Footer text color', "wptouch-pro" ), __( 'This will govern the color of all text in the footer, except for links.', "wptouch-pro"  ) ),
				array( 'list', 'classic_text_shade_color', __( 'Text shading for headers, footer text', "wptouch-pro" ), __( 'Use "dark" for dark backgrounds, "light" for light backgrounds', "wptouch-pro" ), 
					array( 
						'light' => __( 'Light', 'wptouch-pro' ), 
						'dark' => __( 'Dark', 'wptouch-pro' )
					) 
				),
				array( 'copytext', 'copytext-colorpicker', sprintf( __( '%sOpen colorpicker.com Color Picker%s', "wptouch-pro" ), '<a href="http://www.colorpicker.com/" class="ajax-button" id="color-picker">', '</a>' ) ),
				array( 'section-end' )	,
				array( 'spacer' ),
				array( 'section-start', 'background-options', __( 'Background', "wptouch-pro" ) ),
				array( 'text', 'classic_background_color', __( 'Background hex color (without #)', "wptouch-pro" ), __( 'If background images are used, the background color is still included.', "wptouch-pro" ) ),
				array( 'list', 'classic_background_image', __( 'Background tile', "wptouch-pro" ), __( 'Choose a background tile for your theme. Will be repeated vertically and horizontally.', "wptouch-pro" ), 
					array( 
						'ipad-thatch-light' => __( 'Thatch (default)', 'wptouch-pro' ),
						'ipad-thatch' => __( 'Thatch (dark)', 'wptouch-pro' ), 
						'thinstripes' => __( 'Thin Stripes', 'wptouch-pro' ), 
						'thickstripes' => __( 'Thick Stripes', 'wptouch-pro' ), 
						'pinstripes-blue' => __( 'Pinstripes Vertical (Blue)', 'wptouch-pro' ), 
						'pinstripes-grey' => __( 'Pinstripes Vertical (Grey)', 'wptouch-pro' ), 
						'pinstripes-horizontal' => __( 'Pinstripes Horizontal', 'wptouch-pro' ), 
						'pinstripes-diagonal' => __( 'Pinstripes Diagonal', 'wptouch-pro' ), 
						'skated-concrete' => __( 'Skated Concrete', 'wptouch-pro' ), 
						'grainy' => __( 'Grainy', 'wptouch-pro' ), 
						'cog-canvas' => __( 'Cog Canvas', 'wptouch-pro' ), 
						'dark-grey-thatch' => __( 'Dark Grey Thatch', 'wptouch-pro' ), 
						'none' => __( 'None', 'wptouch-pro' ) 
					)
				),	
				array( 'text', 'classic_custom_background_image', __( 'URL path to a custom background', "wptouch-pro" ) ),
				array( 'list', 'classic_background_repeat', __( 'Custom background image repeat type', "wptouch-pro" ), '', 
					array( 
						'repeat' => __( 'Repeat Both', 'wptouch-pro' ),
						'repeat-x' => __( 'Repeat Horizontally', 'wptouch-pro' ), 
						'repeat-y' => __( 'Repeat Vertically', 'wptouch-pro' ),
						'no-repeat' => __( 'Repeat None', 'wptouch-pro' )
					)
				),	
				array( 'section-end' ),
				array( 'spacer' ),
				array( 'section-start', 'post-icon-options', __( 'Calendar/Thumbnail Icons', "wptouch-pro" ) ),
				array( 'list', 'classic_icon_type', __( 'Post icon type', "wptouch-pro" ), __( 'You can choose between calendar icons, WordPress thumbnails, custom field thumbnails, or if activated, the Simple Post Thumbnails plugin.', "wptouch-pro" ), classic_theme_thumbnail_options(), array( 'ipad' ) ),	
				array( 'text', 'classic_custom_field_thumbnail_name', __( 'Custom field name for thumbnails', 'wptouch-pro' ), __( 'Enter the name of the custom field used for your custom post thumbnails.', 'wptouch-pro' ), array( 'ipad' ) ),					
	
				array( 'list', 'classic_calendar_icon_bg', __( 'Calendar icons background color', "wptouch-pro" ), __( 'Choose the appearance of your Calendar icons.', "wptouch-pro" ), 
					array( 
						'cal-blue' => __( 'Classic Blue', 'wptouch-pro' ), 
						'cal-colors' => __( 'Various Colors', 'wptouch-pro' ), 
						'cal-ltg' => __( 'Light Grey', 'wptouch-pro' ),	
						'cal-dkg' => __( 'Dark Grey', 'wptouch-pro' ),
						'cal-custom' => __( 'Custom', 'wptouch-pro' )
					), array( 'ipad' )
				),	
				array( 'text', 'classic_custom_cal_icon_color', __( 'Custom calendar icon color (Hex without #)', 'wptouch-pro' ), '', array( 'ipad' ) ),					
				array( 'checkbox', 'classic_thumbs_on_single', __( 'Show thumbnails on single post pages next to the post title', "wptouch-pro" ), '', array( 'ipad' ) ),
				array( 'checkbox', 'classic_thumbs_on_pages', __( 'Prefer thumbnails on pages over page icons (WordPress thumbs only)', "wptouch-pro" ), __( 'Will show a page thumbnail or featured image instead of the page icon used in the menu. If no thumbnail is specified, the page icon will be used instead.', "wptouch-pro" ), array( 'ipad' ) ),
				array( 'text', 'post_thumbnails_new_image_size', __( 'Size (in px) for Classic thumbnails', 'wptouch-pro' ), __( 'Changing this setting will not affect existing post thumbnails.', 'wptouch-pro' ), array( 'ipad' ) ),
				array( 'copytext', 'regenerate-copytext-info', sprintf( __( '<small>NOTE: You can regenerate your WordPress thumbnails using the %sRegenerate Thumbnails%s plugin.<br />This will tell wordpress to make new thumbnails for WPtouch this size.</small>', "wptouch-pro" ), '<a target="_blank" href="http://wordpress.org/extend/plugins/regenerate-thumbnails/">', '</a>' ) ),
				array( 'section-end' )	
			)
		),
		__( 'iPad Settings', "wptouch-pro" ) => array( 'ipad-theme-settings',
			array(
				array( 'section-start', 'ipad-info', __( 'Enable/Disable iPad Support', "wptouch-pro" ) ),	
				array( 
				'list', 
				'ipad_support', __( 'iPad Support', 'wptouch-pro' ), 
				'', 
				array(
					'none' => __( 'Disabled', 'wptouch-pro' ),
					'full' => __( 'Enabled', 'wptouch-pro' )				
//					'partial' => __( 'Menu bar on desktop theme only', 'wptouch-pro' ),
				)
			),	
				array( 'section-end' ),
				array( 'spacer' ),				
				array( 'section-start', 'ipad-style-settings', __( 'Style and Appearance', "wptouch-pro" ) ),
				array( 'copytext', 'ipad-copytext-info-2', 'Settings below are unique to your iPad theme, and will not affect your mobile theme.', "wptouch-pro" ),
				array( 'list', 'classic_ipad_theme_color', __( 'Header bar, pop-overs and general theme style', "wptouch-pro" ), __( 'Unique to iPad, all colors, gradients & shading are done in CSS, optimized for maximum speed', "wptouch-pro" ),
					array( 
						'grey' => __( 'Default (Grey)', 'wptouch-pro' ), 
						'deep-blue' => __( 'Deep Blue', 'wptouch-pro' ),
						'black' => __( 'Black', 'wptouch-pro' )
					),
				),
				array( 'text', 'classic_ipad_logo_image', __( 'URL for iPad logo Image shown in the landscape menu', "wptouch-pro" ), __( 'If no path is specified no image will be used, and the WPtouch Pro menu will be full height. (300px by 185px transparent .png recommended)', "wptouch-pro" ) ),
				array( 'spacer' ),				
				array( 'list', 'classic_ipad_content_bg', __( 'Content Background', "wptouch-pro" ), '',
					array( 
						'ipad-content-default' => __( 'Subtle Noise (default)', 'wptouch-pro' ),
						'thinstripes' => __( 'Thin Stripes', 'wptouch-pro' ), 
						'thickstripes' => __( 'Thick Stripes', 'wptouch-pro' ), 
						'pinstripes-blue' => __( 'Pinstripes Vertical (Blue)', 'wptouch-pro' ), 
						'pinstripes-grey' => __( 'Pinstripes Vertical (Grey)', 'wptouch-pro' ), 
						'pinstripes-horizontal' => __( 'Pinstripes Horizontal', 'wptouch-pro' ), 
						'pinstripes-diagonal' => __( 'Pinstripes Diagonal', 'wptouch-pro' ), 
						'skated-concrete' => __( 'Skated Concrete', 'wptouch-pro' ), 
						'grainy' => __( 'Grainy', 'wptouch-pro' ), 
						'cog-canvas' => __( 'Cog Canvas', 'wptouch-pro' )
					),
				),
				array( 'text', 'classic_ipad_content_bg_custom', __( 'URL to a custom content background Image', "wptouch-pro" ) ),
				array( 'list', 'classic_ipad_sidebar_bg', __( 'Sidebar Background', "wptouch-pro" ), __( 'This background shows in the landscape menu.', "wptouch-pro" ),
					array( 
						'ipad-sidebar-default' => __( 'iPad thatch (default)', 'wptouch-pro' ),
						'ipad-sidebar-blue' => __( 'Deep Blue', 'wptouch-pro' ),
						'ipad-sidebar-circles' => __( 'Dark Grey Circles', 'wptouch-pro' ),
						'ipad-sidebar-canvas' => __( 'Dark Grey Canvas', 'wptouch-pro' ),
						'ipad-sidebar-dots' => __( 'Bevelled Dots', 'wptouch-pro' )
					),
				),
				array( 'text', 'classic_ipad_sidebar_bg_custom', __( 'URL to a custom sidebar background Image', "wptouch-pro" ) ),
				array( 'spacer' ),				
				array( 'list', 'classic_ipad_general_font', __( 'General site font', "wptouch-pro" ), '', 
					array( 
						'ArialMT' => __( 'ArialMT', 'wptouch-pro' ),
						'Arial-BoldMT' => __( 'ArialMT (Bold)', 'wptouch-pro' ),
						'Baskerville' => __( 'Baskerville', 'wptouch-pro' ),
						'Baskerville-Bold' => __( 'Baskerville (Bold)', 'wptouch-pro' ),
						'Cochin' => __( 'Cochin', 'wptouch-pro' ),
						'Cochin-Bold' => __( 'Cochin (Bold)', 'wptouch-pro' ),
						'Courier' => __( 'Courier', 'wptouch-pro' ),
						'Futura-Medium' => __( 'Futura', 'wptouch-pro' ),
						'Georgia' => __( 'Georgia', 'wptouch-pro' ),
						'Georgia-Bold' => __( 'Georgia (Bold)', 'wptouch-pro' ),
						'GillSans' => __( 'Gill Sans', 'wptouch-pro' ),
						'GillSans-Bold' => __( 'Gill Sans (Bold)', 'wptouch-pro' ),
						'Helvetica' => __( 'Helvetica', 'wptouch-pro' ), 
						'Helvetica-Bold' => __( 'Helvetica (Bold)', 'wptouch-pro' ), 
						'HelveticaNeue' => __( 'Helvetica Neue', 'wptouch-pro' ),
						'HelveticaNeue-Bold' => __( 'Helvetica Neue (Bold)', 'wptouch-pro' ),
						'Optima-Regular' => __( 'Optima', 'wptouch-pro' ),
						'Optima-Bold' => __( 'Optima (Bold)', 'wptouch-pro' ),
						'Palatino-Roman' => __( 'Palatino', 'wptouch-pro' ),
						'Thonburi' => __( 'Thonburi', 'wptouch-pro' ),
						'Thonburi-Bold' => __( 'Thonburi (Bold)', 'wptouch-pro' ),
						'TimesNewRomanPSMT' => __( 'Times New Roman', 'wptouch-pro' ),
						'TrebuchetMS' => __( 'Trebuchet MS', 'wptouch-pro' ),
						'TrebuchetMS-Bold' => __( 'Trebuchet MS (Bold)', 'wptouch-pro' ),
						'Verdana' => __( 'Verdana', 'wptouch-pro' ),
						'Verdana-Bold' => __( 'Verdana (Bold)', 'wptouch-pro' )
					) 
				),
				array( 'list', 'classic_ipad_general_font_size', __( 'General site font size', "wptouch-pro" ), '', 
					array( 
						'13px' => __( '13px', 'wptouch-pro' ),
						'14px' => __( '14px', 'wptouch-pro' ),
						'15px' => __( '15px', 'wptouch-pro' ),
						'16px' => __( '16px', 'wptouch-pro' ),
						'17px' => __( '17px', 'wptouch-pro' ),
						'18px' => __( '18px', 'wptouch-pro' ),
						'19px' => __( '19px', 'wptouch-pro' )
					) 
				),
				array( 'list', 'classic_ipad_post_title_font', __( 'Post title font', "wptouch-pro" ), '', 
					array( 
						'ArialMT' => __( 'ArialMT', 'wptouch-pro' ),
						'Arial-BoldMT' => __( 'ArialMT (Bold)', 'wptouch-pro' ),
						'Baskerville' => __( 'Baskerville', 'wptouch-pro' ),
						'Baskerville-Bold' => __( 'Baskerville (Bold)', 'wptouch-pro' ),
						'Cochin' => __( 'Cochin', 'wptouch-pro' ),
						'Cochin-Bold' => __( 'Cochin (Bold)', 'wptouch-pro' ),
						'Courier' => __( 'Courier', 'wptouch-pro' ),
						'Futura-Medium' => __( 'Futura', 'wptouch-pro' ),
						'Georgia' => __( 'Georgia', 'wptouch-pro' ),
						'Georgia-Bold' => __( 'Georgia (Bold)', 'wptouch-pro' ),
						'GillSans' => __( 'Gill Sans', 'wptouch-pro' ),
						'GillSans-Bold' => __( 'Gill Sans (Bold)', 'wptouch-pro' ),
						'Helvetica' => __( 'Helvetica', 'wptouch-pro' ), 
						'Helvetica-Bold' => __( 'Helvetica (Bold)', 'wptouch-pro' ), 
						'HelveticaNeue' => __( 'Helvetica Neue', 'wptouch-pro' ),
						'HelveticaNeue-Bold' => __( 'Helvetica Neue (Bold)', 'wptouch-pro' ),
						'Optima-Regular' => __( 'Optima', 'wptouch-pro' ),
						'Optima-Bold' => __( 'Optima (Bold)', 'wptouch-pro' ),
						'Palatino-Roman' => __( 'Palatino', 'wptouch-pro' ),
						'Thonburi' => __( 'Thonburi', 'wptouch-pro' ),
						'Thonburi-Bold' => __( 'Thonburi (Bold)', 'wptouch-pro' ),
						'TimesNewRomanPSMT' => __( 'Times New Roman', 'wptouch-pro' ),
						'TrebuchetMS' => __( 'Trebuchet MS', 'wptouch-pro' ),
						'TrebuchetMS-Bold' => __( 'Trebuchet MS (Bold)', 'wptouch-pro' ),
						'Verdana' => __( 'Verdana', 'wptouch-pro' ),
						'Verdana-Bold' => __( 'Verdana (Bold)', 'wptouch-pro' )
					) 
				),
				array( 'list', 'classic_ipad_post_title_font_size', __( 'Post title font size', "wptouch-pro" ), '', 
					array( 
						'22px' => __( '22px', 'wptouch-pro' ),
						'23px' => __( '23px', 'wptouch-pro' ),
						'24px' => __( '24px', 'wptouch-pro' ),
						'25px' => __( '25px', 'wptouch-pro' ),
						'26px' => __( '26px', 'wptouch-pro' ),
						'27px' => __( '27px', 'wptouch-pro' ),
						'28px' => __( '28px', 'wptouch-pro' ),
						'29px' => __( '29px', 'wptouch-pro' ),
						'30px' => __( '30px', 'wptouch-pro' )
					) 
				),
				array( 'list', 'classic_ipad_post_body_font', __( 'Post body font', "wptouch-pro" ), '', 
					array( 
						'ArialMT' => __( 'ArialMT', 'wptouch-pro' ),
						'Arial-BoldMT' => __( 'ArialMT (Bold)', 'wptouch-pro' ),
						'Baskerville' => __( 'Baskerville', 'wptouch-pro' ),
						'Baskerville-Bold' => __( 'Baskerville (Bold)', 'wptouch-pro' ),
						'Cochin' => __( 'Cochin', 'wptouch-pro' ),
						'Cochin-Bold' => __( 'Cochin (Bold)', 'wptouch-pro' ),
						'Courier' => __( 'Courier', 'wptouch-pro' ),
						'Futura-Medium' => __( 'Futura', 'wptouch-pro' ),
						'Georgia' => __( 'Georgia', 'wptouch-pro' ),
						'Georgia-Bold' => __( 'Georgia (Bold)', 'wptouch-pro' ),
						'GillSans' => __( 'Gill Sans', 'wptouch-pro' ),
						'GillSans-Bold' => __( 'Gill Sans (Bold)', 'wptouch-pro' ),
						'Helvetica' => __( 'Helvetica', 'wptouch-pro' ), 
						'Helvetica-Bold' => __( 'Helvetica (Bold)', 'wptouch-pro' ), 
						'HelveticaNeue' => __( 'Helvetica Neue', 'wptouch-pro' ),
						'HelveticaNeue-Bold' => __( 'Helvetica Neue (Bold)', 'wptouch-pro' ),
						'Optima-Regular' => __( 'Optima', 'wptouch-pro' ),
						'Optima-Bold' => __( 'Optima (Bold)', 'wptouch-pro' ),
						'Palatino-Roman' => __( 'Palatino', 'wptouch-pro' ),
						'Thonburi' => __( 'Thonburi', 'wptouch-pro' ),
						'Thonburi-Bold' => __( 'Thonburi (Bold)', 'wptouch-pro' ),
						'TimesNewRomanPSMT' => __( 'Times New Roman', 'wptouch-pro' ),
						'TrebuchetMS' => __( 'Trebuchet MS', 'wptouch-pro' ),
						'TrebuchetMS-Bold' => __( 'Trebuchet MS (Bold)', 'wptouch-pro' ),
						'Verdana' => __( 'Verdana', 'wptouch-pro' ),
						'Verdana-Bold' => __( 'Verdana (Bold)', 'wptouch-pro' )
					) 
				),
				array( 'list', 'classic_ipad_post_body_font_size', __( 'Post body font size', "wptouch-pro" ), '', 
					array( 
						'13px' => __( '13px', 'wptouch-pro' ),
						'14px' => __( '14px', 'wptouch-pro' ),
						'15px' => __( '15px', 'wptouch-pro' ),
						'16px' => __( '16px', 'wptouch-pro' ),
						'17px' => __( '17px', 'wptouch-pro' ),
						'18px' => __( '18px', 'wptouch-pro' ),
						'19px' => __( '19px', 'wptouch-pro' )
					) 
				),
				array( 'list', 'classic_ipad_text_justification', __( 'Text justification in post listings, single posts / comments, and pages', "wptouch-pro" ), '',
					array( 
						'left-justify' => __( 'Left', 'wptouch-pro' ),
						'full-justify' => __( 'Full', 'wptouch-pro' ),
						'right-justify' => __( 'Right RTL (experimental)', 'wptouch-pro' )
					) 
				),	
				array( 'spacer' ),				
				array( 'text', 'classic_ipad_general_font_color', __( 'Sitewide font color', "wptouch-pro" ), __( 'e.g. FFFFFF, (Hex without #)', "wptouch-pro"  ) ),
				array( 'text', 'classic_ipad_post_title_font_color', __( 'Sitewide post title color', "wptouch-pro" ) ),
				array( 'text', 'classic_ipad_link_color', __( 'Sitewide link color', "wptouch-pro" ) ),
				array( 'text', 'classic_ipad_active_link_color', __( 'Content area active link color', "wptouch-pro" ) ),
				array( 'text', 'classic_ipad_context_headers_color', __( 'Context and label headings color', "wptouch-pro" ), __( 'The context header shows for results pages (e.g. Search Results, Leave A Reply) and other labels and headings.', "wptouch-pro"  ) ),
				array( 'text', 'classic_ipad_footer_text_color', __( 'Footer text color', "wptouch-pro" ), __( 'This will govern the color of all text in the footer, except for links.', "wptouch-pro"  ) ),
				array( 'list', 'classic_ipad_text_shade_color', __( 'Text shading for headers, footer text', "wptouch-pro" ), __( 'Use "dark" for dark backgrounds, "light" for light backgrounds', "wptouch-pro" ), 
					array( 
						'light' => __( 'Light', 'wptouch-pro' ), 
						'dark' => __( 'Dark', 'wptouch-pro' )
					) 
				),
				array( 'copytext', 'copytext-ipad-colorpicker', sprintf( __( '%sOpen colorpicker.com Color Picker%s', "wptouch-pro" ), '<a href="http://www.colorpicker.com/" class="ajax-button" id="color-picker" target="_blank">', '</a>' ) ),
				array( 'section-end' ),
				array( 'spacer' ),				
				array( 'section-start', 'ipad-menubar-settings', __( 'Header Buttons and Blog Popover', "wptouch-pro" ) ),
				array( 'checkbox', 'classic_ipad_home_button', __( 'Show home	button', "wptouch-pro" ) ),
				array( 'checkbox', 'classic_ipad_blog_button', __( 'Show blog button', "wptouch-pro" ) ),
				array( 'checkbox', 'classic_ipad_recent_posts', __( 'Show recent posts in blog popover', "wptouch-pro" ), __( 'Will include 12 recent posts.', "wptouch-pro" ) ),
				array( 'checkbox', 'classic_ipad_popular_posts', __( 'Show popular posts in blog popover', "wptouch-pro" ), __( 'Will include 12 popular posts, ranked by comments.', "wptouch-pro" ) ),
				array( 'checkbox', 'classic_ipad_popover_tags', __( 'Show tags in blog popover', "wptouch-pro" ), __( 'Will include up to 30 tags, alphabetically.', "wptouch-pro" ) ),
				array( 'checkbox', 'classic_ipad_popover_cats', __( 'Show categories in blog popover', "wptouch-pro" ), __( 'Will include up to 30 categories ranked by post count.', "wptouch-pro" ) ),
				$flickr_ipad_rss_option,
				array( 'checkbox', 'classic_ipad_account_button', __( 'Show account button', "wptouch-pro" ), __( 'Will be shown automcatically if you require accounts.', "wptouch-pro" ) ),
				array( 'checkbox', 'classic_ipad_search_button', __( 'Show search button', "wptouch-pro" ) ),
				array( 'section-end' )
			)
		),
		__( 'Mobile User Agents', "wptouch-pro" ) => array( 'user-agents',
			array(
				array( 'section-start', 'smartphone-devices', __( 'Default Mobile User Agents', "wptouch-pro" ) ),	
				array( 'user-agents'),
				array( 'section-end' ),
				array( 'spacer' ),				
//				array( 'section-start', 'tablet-devices', __( 'Default iPad & Tablet User Agents', "wptouch-pro" ) ),	
//				array( 'tablet-user-agents'),
//				array( 'section-end' ),
//				array( 'spacer' ),				
				array( 'section-start', 'custom-user-agents', __( 'Custom Mobile User Agents', "wptouch-pro" ) ),
				array( 'textarea', 'classic_custom_user_agents', __( 'Enter additional user agents on separate lines, not device names or other information.', 'wptouch-pro' ) . '<br />' . sprintf( __( 'Visit %sWikipedia%s for a list of device user agents', 'wptouch-pro' ), '<a href="http://en.wikipedia.org/wiki/List_of_user_agents_for_mobile_phones" target="_blank">', '</a>' ) ),	
				array( 'section-end' )
			)				
		)	
	);	
	
	return $menu;
}