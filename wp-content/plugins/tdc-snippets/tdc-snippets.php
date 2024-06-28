<?php

/*
* Plugin Name: TDC Snippets
* Description: Plugin to create cpt-driven php snippets as shortcodes and content filters
* Author: Jerry Milo Johnson
* Version: 0.9
*/

if (!defined("ABSPATH"))
    die ('Direct Access not allowed!');

global $tdc_snippets;

$tdc_snippets = new tdc_snippets();

class tdc_snippets
{
    // class constructor
    public function __construct() {
        // If we are in the admin let's do this
        if (is_admin()) {

        } else {
			add_filter('the_content',  array( &$this, 'tdc_snippet_cpt_filter'), 10);
			add_shortcode('tdc-snippet', array( &$this, 'tdc_snippet'));
        }
    }

    /**
     * Creates snippet shortcode
     */
	function tdc_snippet( $atts, $content = null ) { 
		extract(shortcode_atts( array( 
			'snippet' => '',
            'show_comments' => 1,
        ), $atts, 'tdc-snippet' ) );

		if ( $show_comments ) $content .= '<!-- ' . $snippet . ' Snippet Start -->';

		$args=array(
		  'name' => $snippet,
		  'post_type' => 'snippet',
		  'post_status' => 'publish',
		  'showposts' => 1,
		  'ignore_sticky_posts'=> 1,
 		);
		$my_posts = new WP_Query( $args );
		if( $my_posts ) {
			while ( $my_posts->have_posts() ) : $my_posts->the_post();
				$code = get_post_meta(get_the_ID(), 'code', true);
				$content .= $this->evaluate( $code, $atts );
			endwhile;
		} else {
			// Throw a 404 page error.
			$content.= "<!-- That code snippet not found -->";
		}
		wp_reset_postdata();
        if ( $show_comments ) $content .= '<!-- ' . $snippet . ' Snippet End    -->';
		return $content;
	}

    /**
     * Content filter to replace cpt content with snippet loops
     */
	function tdc_snippet_cpt_filter($content='') {
		global $post;
		
		if (is_single()) {
			$snippet = get_post_type().'-single';
			$content .= '<!-- ' . $snippet . ' CPT Filter Start -->';

			$args=array(
			  'name' => $snippet,
			  'post_type' => 'snippet',
			  'post_status' => 'publish',
			  'showposts' => 1,
			  'ignore_sticky_posts '=> 1,
			);
			$my_posts = new WP_Query( $args );
			if( $my_posts ) {
				while ( $my_posts->have_posts() ) : $my_posts->the_post();
					$code = get_post_meta(get_the_ID(), 'code', true);
					$content .= $this->evaluate( $code );
				endwhile;
			} else {
				// Throw a 404 page error.
				$content.= "<!-- That code snippet not found -->";
			}
			$content .= '<!-- ' . $snippet . ' CPT Filter End    -->';
			wp_reset_postdata();
		}
		return $content;
	}

	public static function evaluate($string = '', $atts = array())
	{
		// if we have no php in the content, just return as is
		if(!$string || stripos($string, 'php') === FALSE)
			return $string; // Saves time.

		// handle [php] shortcodes
		if(stripos($string, '[php]') !== FALSE) // PHP shortcode tags?
			$string = str_ireplace(array('[php]', '[/php]'), array('<?php ', ' ?>'), $string);

		// fix wp-kses introduced errors
		if(stripos($string, '< ?php') !== FALSE) // WP `force_balance_tags()` does this.
			$string = str_ireplace('< ?php', '<?php ', $string); // Quick fix.

		// here we go...
		ob_start(); // Output buffer PHP code execution to collect echo/print calls.
		eval('?>'.trim($string).'<?php '); // Evaluate PHP tags (the magic happens here).
		$string = ob_get_clean(); // Collect output buffer.

		if(stripos($string, '!php') !== FALSE) // PHP code samples; e.g. <!php !> tags.
			$string = preg_replace(array('/\< ?\!php(\s+)/i', '/(\s+)\!\>/'), array('<?php${1}', '${1}?>'), $string);

		return $string;
	}

} // end class
?>