<?php

class SlideDeckLens_Prime extends SlideDeckLens_Scaffold {

	var $slider_width;
	var $options_model = array(
		'Appearance' => array(
			'accentColor' => array(
				'value' => "#f9e836"
			),
			'titleFont' => array(
				'value' => "lato"
			),
			'show-title-rule' => array(
				'suffix' => 'Shows/Hides the double bar rule behind the title',
				'label' => 'Show Title Rule',
				'type' => 'radio',
				'data' => "boolean",
				'value' => true,
				'weight' => 70
			),
			'show-shadow' => array(
				'suffix' => 'Shows/Hides white drop shadow around content',
				'label' => 'Show Box Shadow',
				'type' => 'radio',
				'data' => "boolean",
				'value' => true,
				'weight' => 80
			),
			'hideSpines' => array(
				'type' => 'hidden',
				'value' => true
			),
			'show_lightbox' => array(
				'type' => 'radio',
				'data' => 'boolean',
				'value' => false,
				'label' => "Show Lightbox",
				'description' => "Show Lightbox option",
				'weight' => 50
			),
			'show_fullscreen' => array(
				'type' => 'radio',
				'data' => 'boolean',
				'value' => false,
				'label' => "Show Fullwidth",
				'description' => "Show Fullwidth option",
				'weight' => 60
			),
		),
		'Navigation' => array(
			'navigation-type' => array(
				'name' => 'navigation-type',
				'type' => 'select',
				'values' => array(
					'number-nav' => 'Numbers',
					'dot-nav' => 'Dots'
				),
				'value' => 'numbers',
				'label' => 'Navigation Type',
				'description' => "Show dots or numbers on the navigation bar",
				'weight' => 20
			),
		),
		'Playback' => array(
			'slideTransition' => array(
				'type' => 'hidden'
			),
			'newTransition' => array(
				'type' => 'select',
				'data' => "string",
				'values' => array(
					'fade' => "Cross-fade",
					'fadeZoom' => "Fade Zoom",
					'blindX' => "Blind X",
					'blindY' => "Blind Y",
					'blindZ' => "Blind Z",
					'cover' => "Cover",
					'curtainX' => "Curtain X",
					'curtainY' => "Curtain Y",
					'growX' => "Grow X",
					'growY' => "Grow Y",
					'scrollUp' => "Scroll up",
					'scrollDown' => "Scroll down",
					'scrollHorz' => "Scroll horz",
					'scrollVert' => "Scroll vert",
					'slideX' => "Slide X",
					'slideY' => "Slide Y",
					'toss' => "Toss",
					'turnUp' => "Turn Up",
					'turnDown' => "Turn Down",
					'turnLeft' => "Turn Left",
					'turnRight' => "Turn Right",
					'uncover' => "Uncover",
					'wipe' => "Wipe",
					'zoom' => "Zoom"
				),
				'value' => 'slide',
				'label' => "Slide Transition",
				'description' => "Choose an animation for transitioning between slides (CSS3 transitions may not work well with videos or in all browsers)",
				'subtext' => "Not all transitions will work with videos or in older browsers. \"Slide\" transition is the only working option for vertical navigation.",
				'weight' => 40
			),
		)
	);

	function __construct()
	{
		parent::__construct();
		add_filter( "{$this->namespace}_iframe_scripts", array( $this, 'slidedeck_iframe_scripts' ), 999, 2 );
		//add_filter( "{$this->namespace}_custom_slide_nodes", array( &$this, 'custom_slidedeck_slide_nodes' ), 20, 3 );
		add_action( 'init', array( &$this, 'slidedeck_thickbox' ), 999, 1 );
		add_action( "{$this->namespace}_dimensions", array( &$this, "_slidedeck_dimensions" ), 19, 5 );
	}

	function _slidedeck_dimensions( $width, $height, $outer_width, $outer_height, $slidedeck )
	{
		$this->slider_width = $width;
	}

	function custom_slidedeck_slide_nodes( $slide_nodes, $slide, $slidedeck )
	{

		// trim the content if not HTML slide
		if ( $slide->meta['_slide_type'] !== "html" && $slide->meta['_slide_type'] !== "textonly" ) {
			if ( (int) $this->slider_width <= 500 ) {
				$slide_nodes['title'] = ( strlen( $slide_nodes['title'] ) > 20 ) ? strtolower( substr( $slide_nodes['title'], 0, 20 ) . '...' ) : $slide_nodes['title'];
				$slide_nodes['content'] = ( strlen( $slide_nodes['content'] ) > 20 ) ? strtolower( substr( $slide_nodes['content'], 0, 20 ) . '...' ) : $slide_nodes['content'];
			} else {
				$slide_nodes['title'] = ( strlen( $slide_nodes['title'] ) > 40 ) ? strtolower( substr( $slide_nodes['title'], 0, 40 ) . '...' ) : $slide_nodes['title'];
				$slide_nodes['content'] = ( strlen( $slide_nodes['content'] ) > 40 ) ? strtolower( substr( $slide_nodes['content'], 0, 40 ) . '...' ) : $slide_nodes['content'];
			}
		}

		// check if full screen option is set
		if ( isset( $slidedeck['options']['show_fullscreen'] ) && $slidedeck['options']['show_fullscreen'] ) {
			// add fullscreen url
			$thumbnail_url = $image_url = "";
            if( !isset( $slide->meta['_image_source'] ) ) {
                $slide->meta['_image_source'] = '';
            }
			if ( in_array( $slide->meta['_image_source'], array( "upload", "medialibrary" ) ) ) {
				if ( !empty( $slide->meta['_image_attachment'] ) ) {
					$attachment = $this->get_media_meta( $slide->meta['_image_attachment'] );

					// Determine image size to retrieve (closest size greater to SlideDeck size, or full of image scaling is off)

					$image_src = wp_get_attachment_image_src( $attachment['post']->ID, "full" );

					$image_url = $image_src[0];
					$thumbnail_url = $attachment['src'][0];
				}
			} elseif ( $slide->meta['_image_source'] == "url" ) {
				$thumbnail_url = $image_url = $slide->meta['_image_url'];
			}
			$slide_nodes['full_image'] = $image_url;
		}
		return $slide_nodes;
	}

	/**
	 *  function to add thickbox
	 */
	function slidedeck_thickbox()
	{
		add_thickbox();
	}

	/**
	 *  function remove unused scripts from backend preview
	 * @param type $footer_scripts
	 * @param type $slidedeck
	 * @return type
	 */
	function slidedeck_iframe_scripts( $footer_scripts, $slidedeck )
	{
		if ( $this->is_valid( $slidedeck['lens'] ) ) {
//			if ( ($key = array_search( 'slidedeck-public', $footer_scripts )) !== false ) {
//				unset( $footer_scripts[$key] );
//			}

			array_push( $footer_scripts, 'cycle-all' );

			return $footer_scripts;
		} else {
			return $footer_scripts;
		}
	}

	/**
	 * Get Media Meta
	 * 
	 * Retrieves all relevant meta for any media entries and returns an array of
	 * data keyed on the media ID.
	 * 
	 * @param mixed $media_ids Array of media IDs or single integer for one media ID
	 * 
	 * @uses WP_Query
	 * @uses wp_get_attachment_metadata()
	 * @uses wp_get_attachment_image_src()
	 * 
	 * @return array
	 */
	function get_media_meta( $media_ids )
	{
		global $WPML_media;
		global $sitepress;

		$readd_wpml = $readd_wpml_media = false;

		// Check to see if WPML is enabled and remove the "posts_where" filter from the query.
		// We need to remember which plugin is active, so we re-add the correct filter.
		if ( defined( 'WPML_MEDIA_VERSION' ) ) {
			if ( has_filter( 'posts_where', array( $WPML_media, 'posts_where_filter' ) ) !== false ) {
				$readd_wpml_media = true;
			}
			remove_filter( 'posts_where', array( $WPML_media, 'posts_where_filter' ) );
		}
		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			if ( has_filter( 'posts_where', array( $sitepress, 'posts_where_filter' ) ) !== false ) {
				$readd_wpml = true;
			}
			remove_filter( 'posts_where', array( $sitepress, 'posts_where_filter' ) );
		}

		$single = false;

		if ( !is_array( $media_ids ) ) {
			$media_ids = array( $media_ids );
			$single = true;
		}

		$query_args = array(
			'post__in' => $media_ids,
			'post_type' => 'attachment',
			'post_status' => 'any',
			'nopaging' => true
		);
		$query = new WP_Query( $query_args );

		$media = array();
		foreach ( $media_ids as $media_id ) {
			$image = array(
				'meta' => wp_get_attachment_metadata( $media_id ),
				'src' => wp_get_attachment_image_src( $media_id, array( 96, 96 ) )
			);

			$media_link = get_post_meta( $media_id, "{$this->namespace}_media_link", true );
			if ( empty( $media_link ) )
				$media_link = get_attachment_link( $media_id );

			$image['media_link'] = $media_link;

			foreach ( $query->posts as $post ) {
				if ( $post->ID == $media_id )
					$image['post'] = $post;
			}

			$media[$media_id] = $image;
		}

		// Check to see if we need to re-add the appropriate "posts_where" filter.
		if ( $readd_wpml ) {

			add_filter( 'posts_where', array( $sitepress, 'posts_where_filter' ), 10, 2 );
		}
		if ( $readd_wpml_media ) {
			add_filter( 'posts_where', array( $WPML_media, 'posts_where_filter' ), 10, 2 );
		}

		if ( $single )
			return reset( $media );
		else
			return $media;
	}

	/**
	 * Modify Slide title to wrap in spans for stlying
	 * 
	 * @param array $nodes $nodes Various information nodes available to use in the template file
	 * 
	 * @return array
	 */
	function slidedeck_slide_nodes( $nodes, $slidedeck )
	{
		if ( $this->is_valid( $slidedeck['lens'] ) ) {

			$temp_title = $nodes['title'];
			$title_parts = explode( " ", $temp_title );
			$new_title = "";
			$count = 1;
			foreach ( $title_parts as $title_part ) {
				if ( $count == 1 ) {
					$new_title .= '<span class="first">' . $title_part . '</span> ';
				} else {
					$new_title .= '<span>' . $title_part . '</span> ';
				}
				$count++;
			}
			$nodes['title'] = $new_title;

			if ( in_array( 'twitter', $slidedeck['source'] ) ) {

				$url_regex = '/((https?|ftp|gopher|telnet|file|notes|ms-help):((\/\/)|(\\\\))+[\w\d:#@%\/\;$()~_?\+-=\\\.&]*)/';

				/**
				 * This preg split takes a tweet (URLs, words, hashtags, usernames) and breaks it up wherever
				 * there is already a html tag (the input has <a> tags wrapped around the aforementioned) and breaks it up.
				 * This gives us an array with strings, and links broken up into elements.
				 * 
				 * This allow us to break each word and "linkified" words in their own spans.
				 */
				$split_html = preg_split( '/<\/?\w+((\s+\w+(\s*=\s*(?:\".*?\\\"|.*?|[^">\s]+))?)+\s*|\s*)\/?>/s', $nodes['excerpt'] );

				// Reset the excerpt node for appending to.
				$nodes['excerpt'] = '';
				foreach ( $split_html as $segment ) {
					if ( preg_match( $url_regex, $segment ) ) {
						// If the current segment looks like a URL, wrap and append it.
						$nodes['excerpt'] .= '<span><a class="accent-color" href="' . $segment . '" target="_blank">' . $segment . '</a></span>';
					} elseif ( preg_match( '/(\@([a-zA-Z0-9_]+))|(\#([a-zA-Z0-9_]+))/', $segment ) ) {
						// If the current segment looks like a mention or hashtag, wrap and append it. 
						$nodes['excerpt'] .= '<span><a class="accent-color" href="http://twitter.com/search?q=' . $segment . '" target="_blank">' . $segment . '</a></span>';
					} else {
						/**
						 * If the current segment is neither, then we can reasonably assume it's a string of words.
						 * Here we'll run the existing split and wrap code.
						 */
						if ( !empty( $segment ) ) {
							$segment = trim( $segment );
							$temp_excerpt = strip_tags( $segment );
							$excerpt_parts = explode( " ", $temp_excerpt );
							$new_excerpt = "";
							$count = 1;
							foreach ( $excerpt_parts as $excerpt_part ) {
								if ( $count == 1 ) {
									$new_excerpt .= '<span class="first">' . $excerpt_part . '</span> ';
								} else {
									$new_excerpt .= '<span>' . $excerpt_part . '</span> ';
								}
							}
							$new_excerpt = preg_replace( $url_regex, '<a href="$1" target="_blank">' . "$1" . '</a>', $new_excerpt );
							$new_excerpt = preg_replace( array(
								'/\@([a-zA-Z0-9_]+)/',
								'/\#([a-zA-Z0-9_]+)/'
									), array(
								'<a href="http://twitter.com/$1" target="_blank">@$1</a>',
								'<a href="http://twitter.com/search?q=%23$1" target="_blank">#$1</a>'
									), $new_excerpt );

							$nodes['excerpt'] .= $new_excerpt;
						} // if( !empty( $segment ) )
					} // else (is a string)
				} // foreach( $split_html as $segment )
			} // if the source is twitter
		} // if is a valid lens

		return $nodes;
	}

	function slidedeck_dimensions( &$width, &$height, &$outer_width, &$outer_height, &$slidedeck )
	{
		if ( $this->is_valid( $slidedeck['lens'] ) ) {
			// Add 44px for the bottom navigation on the lens.
			$height = $height - 44;
		}
	}

	/**
	 * Add appropriate classes for this Lens to the SlideDeck frame
	 * 
	 * @param array $slidedeck_classes Classes to be applied
	 * @param array $slidedeck The SlideDeck object being rendered
	 * 
	 * @return array
	 */
	function slidedeck_frame_classes( $slidedeck_classes, $slidedeck )
	{
		if ( $this->is_valid( $slidedeck['lens'] ) ) {
			$slidedeck_classes[] = $this->prefix . $slidedeck['options']['navigation-type'];
		}

		return $slidedeck_classes;
	}

	function slidedeck_render_slidedeck_after( $html, $slidedeck )
	{
		if ( $this->is_valid( $slidedeck['lens'] ) ) {
			$html .= '<div class="prime-nav-wrapper"><div class="accent-bar-top accent-color-background">&nbsp;</div><div id="prime-custom-pager" class="center external"></div></div>';
		}
		return $html;
	}

}
