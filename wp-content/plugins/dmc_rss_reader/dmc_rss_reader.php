<?php
/*
Plugin Name: DMC RSS Reader
Plugin URI:
Description: Better RSS reader widget
Author: Chuck Lavery
Version: 0.1
Author URI:
*/ 
?>
<?php

require_once( dirname( __FILE__ ) . '/rss.php' );

require_once( ABSPATH . WPINC . '/class-http.php');


	function dmc_rss_fetch_remote_file ($url, $headers = "", $proxy_server="", $proxy_port="8080") {
                // Snoopy is an HTTP client in PHP
                $client = new Snoopy();
                $client->agent = 'WordPress/' . $GLOBALS['wp_version'];;
                $client->read_timeout = 3;
                $client->use_gzip = true;
                if(!empty($proxy_server)) {
                        $client->_isproxy = true;
                        $client->proxy_host = $proxy_server;
                        $client->proxy_port = $proxy_port;
                }
                if (is_array($headers) ) {
                        $client->rawheaders = $headers;
                }

                $client->fetch($url);

                if($client->status >= 200 && $client->status < 300) {
                        $rss = new rss_php;
                        $rss->loadRSS($client->results);
                        return $rss;
                }
                else {
                        return false;
                }

        }

        function dmc_rss_fetch_rss($url, $proxy_server="", $proxy_port="8080") {
                global $dmc_rss_cache_manager;

                $proxy_server = '';
                $proxy_port = '';

                // Check if caching available and if feed is cached
                if(method_exists($dmc_rss_cache_manager, 'get_rss')) {
                        $rss = dmc_rss_cache_manager::get_rss($url);

                        // If no feed available, pull and cache it
                        if ($rss === false) {
                                $resp = dmc_rss_fetch_remote_file($url,"",$proxy_server,$proxy_port);
                                dmc_rss_cache_manager::set_rss($url,$resp);
				echo "<!-- Cached RSS: $url -->";
                                return $resp;
                        }
                        else {
				echo "<!-- Retrieved RSS from Cache -->";
                                return $rss;
                        }
                }
                else {
                        $resp = dmc_rss_fetch_remote_file($url,"",$proxy_server,$proxy_port);
			echo "<!-- No RSS Caching Enabled -->";
                        return $resp;
                }
        }


class DMCRSSReader extends WP_Widget {
    /**
    * Registers the widget and the widget control for use
    */
    function __construct() {
        $widget_ops = array('classname' => 'reader_widget', 'description' => "DMC RSS Widget");
        $control_ops = array('width' => 400, 'height' => 450, 'id_base' => 'dmc-rss-reader');
        parent::__construct('dmc-rss-reader', __('DMC RSS Widget'), $widget_ops, $control_ops);
    }
    
   
            
    /**
    * The widget itself
    */
    function widget($args, $instance) {
        global $content_process_item;
        
        extract( $args, EXTR_SKIP );
        extract( $instance );
        
        // catch variables in formats
        $variable_regex = '/\$(\D(?:\w|:)+)(?:\[limit:([0-9]+)\])?(\^)?(?:\[(\w+)\])?/ms';
        
        // Show title when being called from the admin only. This will display the
        // title for each widget instance and make managing sidebars easier.
        if ($before_title == '%BEG_OF_TITLE%' && $after_title == '%END_OF_TITLE%') {
            echo $before_title . $options[$number]['name'] . $after_title;
            return;
        }
        
        //if the blog is local, fetch the posts via the DB 
        if($is_local) {
            //echo "<BR>RSS: bid(" . $blog_id . ") category_id(". $category_id . ")";
            
            if(function_exists('croer_the_sort')) {
                remove_filter('posts_request','croer_the_sort');
                remove_action('wp_head', 'croer_version');
            }
            
            switch_to_blog($blog_id);
            global $posts;
            
            $temp = $posts;
            if($category_id == -1) {
              $posts = query_posts('feed=rss2&showposts='.$num_items);
            } else {
              $posts = query_posts('feed=rss2&showposts='.$num_items.'&cat='.$category_id);
            }

            include("var_rss.php");

            $rss = new rss_php;
            $rss->loadRSS($myrss->asXML());

            restore_current_blog();

            if(function_exists('croer_the_sort')) {
                add_filter('posts_request','croer_the_sort');   
                add_action('wp_head', 'croer_version');
            }
        } else { // remote rss
            
            try {
                if(!empty($proxy_host)) {
                    if(!empty($proxy_port)) {
                        $rss = dmc_rss_fetch_rss($rss_url, $proxy_host, $proxy_port);	
                    }
                    else { 
                        $rss = dmc_rss_fetch_rss($rss_url, $proxy_host);
                    }
                }
                else {
                    $rss = dmc_rss_fetch_rss($rss_url);
                  
                }	
            }
            catch(exception $e) {
                return false;
            }
            //echo "RSS FEED<HR>";
        }
        
        // Stop processing if the RSS result wasn't proper
        if (!is_object( $rss ))
            return false;
        
        //$items = ($is_local) ? $rss->items : $rss->getItems(true);
        $items = $rss->getItems(true);
        // start output buffering for a little performance
        //ob_start();
        
        echo $before_widget;

        // fix div wraps if in jarida
        if (wp_get_theme() == 'Jarida') {
                $before_title = "";
                // fix the jarida assumptions
                $after_title = '<div class="widget-container">';
                echo $before_title;
                echo $after_title;
                // clear it in case it is used later
                $after_title = "";
        }
        
        // because the callback used in "preg_replace_callback" can't accept additional arguments we
        // must use a global to give it access to the channel information (would be better wrapped up in a class)
        //$content_process_item = ($is_local) ? $rss : $rss->getChannel(true);
        $content_process_item = $rss->getChannel(true);

        //counting total number of items in a RSS feed
        $total_item = 0;
        foreach($items as $item) {
            $total_item++;
        }
        
        if(!empty($pre_feed)) {
            //displaying total number of items in a RSS feed
            $pre_feed = str_replace('$total_item',$total_item, $pre_feed);
            $pre_feed = preg_replace_callback($variable_regex, array($this, 'process_content'), $pre_feed);
            echo $pre_feed;
            
        }
        
        $item_count = 0;
        
		if(!empty($items)){
			foreach($items as $item) {
				if($item_count == $num_items && $num_items !=0)
					break;
				
				//total hack to allow locks to show on posts for local blogs ONLY
				//loop through the $posts array and set the post title to be the one from the 
				//db (which will have the lock image appended to it via DMC_SS plugin)
				if ($is_local) {
					$loopcount = 0;
					foreach($posts as $post) {
						if ($loopcount == $item_count) {
							$item_title = $post->post_title;
							$item['title']['value'] = $item_title;
							$item_excerpt = $post->post_excerpt;
							$item['excerpt']['value'] = $item_excerpt;
						}
						$loopcount++;
					}
				}
				//otherwise, just use the RSS' title as-is
				else {
					$item_title = htmlspecialchars_decode($item['title']['value']);
					//echo "<hr>$item_title<HR>";
				}
			
				$item['use_morelink']['value'] = $use_morelink;
				$item['more_text']['value'] = $more_text;
				$item_link = $item['link']['value'];
				$item_description = (array_key_exists( 'content:encoded', $item ) ? $item['content:encoded']['value'] : $item['content']['value']);
		
				
				if($content_option == 'N') { // Do not display description
					$item_content = '<li><a href="'.$item_link.'">'.$item_title.'</a></li>';
				}
				else if($content_option == 'D') { // Display description
					//TODO: Limit word count for description option
					//$item_description = WordLimiter($item_description,$num_words);//next line removes caption shortcode from the UNFILTERED description
					$item_description = preg_replace("/\[caption.*\[\/caption\]/",'',$item_description);
					$item_content = '<li><a href="'.$item_link.'">'.$item_title.'</a>
					<p>'.$item_description.'</p></li>';
				}
				else if($content_option == 'F') { // filter content
					// put the item into the global variable for use in the callback
					$content_process_item = $item;
					//new dBug($content_process_item);
				
					if($item_count >= $num_highlight) 
						$item_content = preg_replace_callback($variable_regex, array($this, 'process_content'), $pre_content_replace);
					else {
						//special case since local xml version can't use name:point in xml definition
						if($is_local )
						{
							$highlight_replace = str_replace('content:encoded', 'content', $highlight_replace);
							
						}
						$item_content = preg_replace_callback($variable_regex, array($this, 'process_content'), $highlight_replace);
					}
					
					// Replace match variables using custom filter expression
					if(!empty($pre_content_regex) && preg_match( $pre_content_regex, ($item_description) ) > 0) 
						$item_content = (preg_replace($pre_content_regex, $item_content, ($item_description)));
								
					// Replace still-existing match variables ($1, $2, etc.)
					if (preg_match( '/\$[0-9]+/', $item_content ) > 0)
						$item_content = preg_replace( '/\$[0-9]+/', '', $item_content );
				}
				
				
				echo $item_content;
				
		 
				$item_count++;
				
				if(empty($item_content)) {
					echo '<li class="rss_error">No Posts Published Today!</li>';
				}
			}
			
			
		}
		

	
		else{
			echo('<li class="rss_error">No Posts Published Today!</li>');
		}

        $content_process_item = $rss->getChannel(true);
        if(!empty($post_feed)) {
            $post_feed = preg_replace_callback($variable_regex, array($this, 'process_content'), $post_feed);
            echo $post_feed;
        }
        
        echo $after_widget;
        // send the output for this feed to the browser
        //ob_end_flush();
        
    }

    /*
     * callback for variable interpolation used with formats
     * 
     */
    function process_content($input) {
        global $content_process_item;
        
        if(is_array($input)) {
            if($input[4]) {
                
                // Replace dollar signs to prevent confusion with regex replacement
                $content_process_item[$input[1]]['properties'][$input[4]] = str_replace( '$', '&#036;', $content_process_item[$input[1]]['properties'][$input[4]] );
                
                //new dBug($input);
                if($input[3])
                //only strip out the tags if this is an external feed
                    if(!$is_local) {
                        $output = strip_tags($content_process_item[$input[1]]['properties'][$input[4]]);
                    }
                    else {
                        $output = $content_process_item[$input[1]]['properties'][$input[4]];
                    }
                else
                    $output = $content_process_item[$input[1]]['properties'][$input[4]];
            } else {
                //remove caption code from the content - we always want to do this
                $content_process_item[$input[1]]['value'] = preg_replace('/\\[caption.*\\/caption]/','',$content_process_item[$input[1]]['value'] );
                // Replace dollar signs to prevent confusion with regex replacement
                $content_process_item[$input[1]]['value'] = str_replace( '$', '&#036;', $content_process_item[$input[1]]['value'] );
                
                if($input[3])
                    //only strip out the tags if this is an external feed
                    if(!$is_local) {
                        $output = strip_tags($content_process_item[$input[1]]['value']);
                    }
                    else {
                        $output = $content_process_item[$input[1]]['value'];
                    }
                else
                    $output = $content_process_item[$input[1]]['value'];
            }
            
            // Trim the length of the string if a word limit is provided.
            if ($input[2]) {
                $excerpt_length = $input[2];
                $output = strip_tags($output,'<br>,<img>,<p>');
                $words = explode( ' ', $output, ($excerpt_length + 1) );
                
                // Shorten the output if it's over the max length
                if (count( $words ) > $excerpt_length) {
                    array_pop( $words );
                    //build a link if the 'more link' option is selected
                    if ($content_process_item['use_morelink']['value'] == 1) {
                        $truncated_string = '<a href=';
                        $truncated_string = $truncated_string . $content_process_item['link']['value'];
                        $truncated_string = $truncated_string . ' class="dmcrss_moretextlink">';
                        $truncated_string = $truncated_string . $content_process_item['more_text']['value'];
                        $truncated_string = $truncated_string .'</a>';
                    }
                    else {
                        $truncated_string = '[...]';
                    }
                    array_push( $words, $truncated_string );
                    $output = implode( ' ', $words );
                } 
            }
            //new dBug($output);//die;
            return $output;
        }
    }

    /* Function to trim a string of text into X number of words - used 
        for the description with no filter option in the widget output */
    function WordLimiter($text,$limit){
        $explode = explode(' ',$text);
        $string  = '';

        $dots = '...';
        if(count($explode) <= $limit){
            $dots = '';
        }
        for($i=0;$i<$limit;$i++){
            $string .= $explode[$i]." ";
        }
        if ($dots) {
            $string = substr($string, 0, strlen($string));
        }

        return $string.$dots;
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        
        $instance['name'] = strip_tags($new_instance['name']);
        $instance['rss_url'] = strip_tags($new_instance['rss_url']);
        $instance['num_items'] = (int)strip_tags($new_instance['num_items']);
        $instance['num_words'] = (int)strip_tags($new_instance['num_words']);
        $instance['pre_content_regex'] = $new_instance['pre_content_regex'];
        $instance['pre_content_replace'] = $new_instance['pre_content_replace'];
        $instance['pre_feed'] = $new_instance['pre_feed'];
        $instance['post_feed'] = $new_instance['post_feed'];
        $instance['content_option'] = strip_tags($new_instance['content_option']);
        $instance['num_highlight'] = (int)strip_tags($new_instance['num_highlight']);
        $instance['highlight_replace'] = $new_instance['highlight_replace'];
        $instance['proxy_host'] = $new_instance['proxy_host'];
        $instance['proxy_port'] = $new_instance['proxy_port'];
        $instance['is_local'] = strip_tags($new_instance['is_local']);
        $instance['blog_id'] = (int)strip_tags($new_instance['blog_id']);
        $instance['category_id'] = (int)strip_tags($new_instance['category_id']);
        $instance['use_morelink'] = strip_tags($new_instance['use_morelink']);
        $instance['more_text'] = $new_instance['more_text'];
        
        return $instance;
    }

    /**
    * widget control
    */
    function form($instance) {
		$instance = wp_parse_args((array)$instance, array(
			'name' => 'Default Name',
            'rss_url' => '',
			'num_items' => 0,
            'num_words' => 50,
            'pre_content_regex' => '',
            'pre_content_replace' => '',
            'pre_feed' => '',
            'post_feed' => '',
            'content_option' => 'N',
            'num_highlight' => 0,
            'highlight_replace' => '',
            'proxy_host' => '',
            'proxy_port' => '',
            'is_local' => false,
            'blog_id' => -1,
            'category_id' => -1,
            'use_morelink' => 0,
            'more_text' => '[...]'
		));
        extract($instance);
        
        ?>
        <p>
            <p>By default this feed will display titles for each item in the feed linked to its URL (<i>No Description</i>). Use the <i>Display Description</i> option to also output the items description, optionally restricting it to a certain number of words. The <i>Filtering</i> option allows you to format the output of the feed as well as (optionally) filter the description element of the item to capture parts of interest and use the captured elements in the format. All three options allow you to insert html before and after the feed display. This may be used to display RSS channel information or to insert CSS/Javascript for use in the item formats.</p>
            <hr/>
            <label for="<?php echo $this->get_field_id('name'); ?>">Name (Unique name or description for this feed)</label><br/><input type="text" id="<?php echo $this->get_field_id('name'); ?>" name="<?php echo $this->get_field_name('name'); ?>" type="text" size="50" value="<?php echo esc_attr($name) ?>"><br/>
            <label for="<?php echo $this->get_field_id('rss_url'); ?>">RSS URL</label><br/><input type="text" id="<?php echo $this->get_field_id('rss_url'); ?>" name="<?php echo $this->get_field_name('rss_url'); ?>" type="text" size="50" value="<?php echo esc_attr($rss_url) ?>"><br/>
            <label for="<?php echo $this->get_field_id('proxy_host'); ?>">Proxy Host (leave blank for no proxy)</label><br/><input type="text" id="<?php echo $this->get_field_id('proxy_host'); ?>" name="<?php echo $this->get_field_name('proxy_host'); ?>" type="text" size="50" value="<?php echo esc_attr($proxy_host) ?>"><br/>
            <label for="<?php echo $this->get_field_id('proxy_port'); ?>">Proxy Port (leave blank for no proxy or default 8080)</label><br/><input type="text" id="<?php echo $this->get_field_id('proxy_port'); ?>" name="<?php echo $this->get_field_name('proxy_port'); ?>" type="text" value="<?php echo esc_attr($proxy_port) ?>"><br/>
            <label for="<?php echo $this->get_field_id('num_items'); ?>">Number of Items</label><br/><input type="text" id="<?php echo $this->get_field_id('num_items'); ?>" name="<?php echo $this->get_field_name('num_items'); ?>" type="text" value="<?php echo esc_attr($num_items) ?>"><br/>
            <hr/>
            <p>Use these configuration options if you prefer to retrieve the feed from a local blog with an optional category constraint. Note: Checking this box causes the reader to ignore the URL/Proxy options above.</p>
            <label for="<?php echo $this->get_field_id('is_local'); ?>">Use Local Blog</label><br/><input id="<?php echo $this->get_field_id('is_local'); ?>" name="<?php echo $this->get_field_name('is_local'); ?>" type="checkbox" value="true" <?php if($is_local): ?>checked<?php endif; ?>><br/>
            <label for="<?php echo $this->get_field_id('blog_id'); ?>">Blog ID</label><br/><input type="text" id="<?php echo $this->get_field_id('blog_id'); ?>" name="<?php echo $this->get_field_name('blog_id'); ?>" type="text" value="<?php echo esc_attr($blog_id) ?>"><br/>
            <label for="<?php echo $this->get_field_id('category_id'); ?>">Category ID</label><br/><input type="text" id="<?php echo $this->get_field_id('category_id'); ?>" name="<?php echo $this->get_field_name('category_id'); ?>" type="text" value="<?php echo esc_attr($category_id) ?>"><br/>
            <hr/>
            <input type="radio" id="<?php echo $this->get_field_id('content_option_1'); ?>" name="<?php echo $this->get_field_name('content_option'); ?>" value="N" <?php if($content_option == 'N') echo "CHECKED"; ?>><label for="<?php echo $this->get_field_id('content_option_1'); ?>"><b>Do Not Display Description</b></label><br/>
            <hr/>
            <input type="radio" id="<?php echo $this->get_field_id('content_option_2'); ?>" name="<?php echo $this->get_field_name('content_option'); ?>" value="D" <?php if($content_option == 'D') echo "CHECKED"; ?>><label for="<?php echo $this->get_field_id('content_option_2'); ?>"><b>Display Description</b></label><br/>
            <label for="<?php echo $this->get_field_id('num_words'); ?>">Number of words (use 0 for the entire description)</label> <input type="text" id="<?php echo $this->get_field_id('num_words'); ?>" name="<?php echo $this->get_field_name('num_words'); ?>" type="text" value="<?php echo esc_attr($num_words) ?>"><br/>
            <hr/>
            <input type="radio" id="<?php echo $this->get_field_id('content_option_3'); ?>" name="<?php echo $this->get_field_name('content_option'); ?>" value="F" <?php if($content_option == 'F') echo "CHECKED"; ?>><label for="<?php echo $this->get_field_id('content_option_3'); ?>"><b>Filtering</b></label><br/>
            <label for="<?php echo $this->get_field_id('pre_content_regex'); ?>">Description Filter Regex. This regex will be where the item format will be inserted and also provides a means to capture groups of text from the description to use in the item format. Ensure that the regex matches the entire description if you wish to only use capture groups in the item format, otherwise the item format will only replace what this regex matches. (leave blank to use the item format without using capture groups. In that case only the item format below will be used as the description and the description can be accessed using the $description variable)</label><br/><input type="text" id="<?php echo $this->get_field_id('pre_content_regex'); ?>" name="<?php echo $this->get_field_name('pre_content_regex'); ?>" size="50" value="<?php echo str_replace( array( '&', '"' ), array( '&amp;', '&quot;' ), $pre_content_regex ); ?>"><br/>
            <label for="<?php echo $this->get_field_id('pre_content_replace'); ?>">Item Format. Use capture variables: $1, $2, etc, if using an Item Filter Regex to insert text previously captured in the regex. Use $element to display the <i>value</i> of any element in this item, i.e. $link, $title, $dc:author, etc. Use [limit:X] to limit the length of any variable, where X is the maximum number of words you wish to show, i.e. $description[limit:10]. Use the format $element[attribute] to use the <i>attribute</i> of an element, i.e. $media:content[url] to extract the URL of the content associated with this item. End a variable with a carat "^" if you wish to strip html tags from the element, i.e. $description^</label><br/><textarea cols="45" rows="6" id="<?php echo $this->get_field_id('pre_content_replace'); ?>" name="<?php echo $this->get_field_name('pre_content_replace'); ?>" ><?php echo esc_attr($pre_content_replace) ?></textarea><br/>
            <b>Highlight Items</b><br/>
            <label for="<?php echo $this->get_field_id('num_highlight'); ?>">Number of items to highlight (must be equal or less than total items above). 0 to highlight none and use normal format.</label> <input type="text" id="<?php echo $this->get_field_id('num_highlight'); ?>" name="<?php echo $this->get_field_name('num_highlight'); ?>" type="text" value="<?php echo esc_attr($num_highlight) ?>"><br/>
            <label for="<?php echo $this->get_field_id('highlight_replace'); ?>">Highlight Format. You may use the same capturing and element variables as the normal Item Format.</label><br/><textarea cols="45" rows="6" id="<?php echo $this->get_field_id('highlight_replace'); ?>" name="<?php echo $this->get_field_name('highlight_replace'); ?>" ><?php echo esc_attr($highlight_replace) ?></textarea><br/>
            <input type="hidden" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
            <hr/>
            <b>Pre/Post Feed</b><br/>
            <label for="<?php echo $this->get_field_id('pre_feed'); ?>"><b>Pre-feed.</b> This will be inserted before the feed. You may use any element availiable within the channel element (eg. $title for the RSS feed title) or the custom element $total_item to display total number of items in the RSS feed.</label><br/><textarea cols="45" rows="6" id="<?php echo $this->get_field_id('pre_feed'); ?>" name="<?php echo $this->get_field_name('pre_feed'); ?>" ><?php echo $pre_feed ?></textarea><br/>
            <label for="<?php echo $this->get_field_id('post_feed'); ?>"><b>Post-feed.</b> This will be after the feed. You may use any element availiable within the channel element (eg. $title for the RSS feed title)</label><br/><textarea cols="45" rows="6" id="<?php echo $this->get_field_id('post_feed'); ?>" name="<?php echo $this->get_field_name('post_feed'); ?>" ><?php echo esc_attr($post_feed) ?></textarea><br/>
            <hr/>
            <b>"More" Link Text</b><br/>
            <input type="radio" id="<?php echo $this->get_field_id('use_morelink_1'); ?>" name="<?php echo $this->get_field_name('use_morelink'); ?>" value="0" "CHECKED"><label for="<?php echo $this->get_field_id('use_morelink_1'); ?>"><b>Do Not Display "More" Link</b> (defaults to nonlinked '[...]')</label><br/>
            <input type="radio" id="<?php echo $this->get_field_id('use_morelink_2'); ?>" name="<?php echo $this->get_field_name('use_morelink'); ?>" value="1" <?php if($use_morelink == '1') echo "CHECKED"; ?>><label for="<?php echo $this->get_field_id('use_morelink_2'); ?>"><b>Display "More" Link</b> (uses text entered below to create a link)</label><br/>
            <input type="text" id="<?php echo $this->get_field_id('more_text'); ?>" name="<?php echo $this->get_field_name('more_text'); ?>" type="text" size="20" value="<?php echo esc_attr($more_text) ?>"><br/>
            
        </p>
        <?php
    }
}

function register_dmcrssreader() {
    register_widget('DMCRSSReader');
}
add_action( 'init', 'register_dmcrssreader', 1 );
?>
