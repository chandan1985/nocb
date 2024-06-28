<?php
class SlideDeckSource_Dribbble extends SlideDeck {
    var $label = "Dribbble";
    var $name = "dribbble";
    var $taxonomies = array( 'images' );
    var $default_lens = "tool-kit";
    
    var $options_model = array(
        'Setup' => array(
            'dribbble_shots_or_likes' => array(
                'value' => "shots",
                'data' => 'string'
            ),
            'dribbble_username' => array(
                'value' => "moonspired",
                'data' => 'string'
            )
        )
    );
    
    function add_hooks() {
        add_action( "{$this->namespace}_form_content_source", array( &$this, "slidedeck_form_content_source" ), 10, 2 );
    }
    
     /**
     * Get Dribbble Author Information
     * 
     * 
     * @param string $feed_url The URL of the gplus feed with a JSON response
     * @param integer $slidedeck_id The ID of the deck (for caching)
     * 
     * @return array An array of arrays containing the author of dribbble user and various meta.
     */
    function slidedeck_author_info($slidedeck){
	$args = array(
            	'sslverify' => false
        	);
	//$access_token = 'a6ea878508dc295bc15ded4cc0514cb19046ee5320dc0fe18c70bf068c202a7d';
	$access_token= get_option($this->namespace .'_last_saved_dribbble_api_key');
	$feed_url = 'https://api.dribbble.com/v1/users/' . $slidedeck['options']['dribbble_username'] . '/?&access_token=' .  $access_token;

	 // Create a cache key
        $cache_key = $slidedeck['id'] . $feed_url . $slidedeck['options']['cache_duration'] . $this->name;
	    	   

        // Attempt to read the cache
        $response = slidedeck2_cache_read( $cache_key );
	 
	
        // If cache doesn't exist
        if( !$response ){
            $response = wp_remote_get( $feed_url, $args );
            if( !is_wp_error( $response ) ) {
                // Write the cache
                slidedeck2_cache_write( $cache_key, $response, $slidedeck['options']['cache_duration'] );
            }
        }
	$author = array();
	if( !is_wp_error( $response )) {
            $response_json = json_decode( $response['body'],true );
			$author['author_name'] = $response_json['name'];
                	$author['author_username'] = $response_json['username'];
                	$author['author_avatar'] = $response_json['avatar_url'];
                	$author['author_url'] = 'http://dribbble.com/' . $response_json['username'];

	}else
	{
		return false;
	}
		 
	return $author;
	
}

    /**
     * Get Dribbble Image Feed
     * 
     * Fetches a Dribbble feed, caches it and returns the 
     * cached result or the results after caching them.
     * 
     * @param string $feed_url The URL of the gplus feed with a JSON response
     * @param integer $slidedeck_id The ID of the deck (for caching)
     * 
     * @return array An array of arrays containing the images and various meta.
     */
    function get_slides_nodes( $slidedeck ){
	$api = '';	        
	$args = array(
            'sslverify' => false
        );
        //$access_token = 'a6ea878508dc295bc15ded4cc0514cb19046ee5320dc0fe18c70bf068c202a7d';
	$access_token= get_option($this->namespace .'_last_saved_dribbble_api_key');
        switch( $slidedeck['options']['dribbble_shots_or_likes'] ){
            case 'shots':
                $feed_url = 'https://api.dribbble.com/v1/users/' . $slidedeck['options']['dribbble_username'] . '/shots?per_page=' . $slidedeck['options']['total_slides'] . '&access_token=' . $access_token;
		$api = 'shots';
            break;
            case 'likes':
                $feed_url = 'https://api.dribbble.com/v1/users/' . $slidedeck['options']['dribbble_username'] . '/likes?per_page=' . $slidedeck['options']['total_slides'] . '&access_token=' . $access_token;
		$api = 'likes';
            break;
        }
        // Create a cache key
        $cache_key = $slidedeck['id'] . $feed_url . $slidedeck['options']['cache_duration'] . $this->name;
	    	   

        // Attempt to read the cache
        $response = slidedeck2_cache_read( $cache_key );
	 
	
        // If cache doesn't exist
        if( !$response ){
            $response = wp_remote_get( $feed_url, $args );
            if( !is_wp_error( $response ) ) {
                // Write the cache
                slidedeck2_cache_write( $cache_key, $response, $slidedeck['options']['cache_duration'] );
            }
        }
        $data;
   	
        $images = array();
        if( !is_wp_error( $response ) && isset( $response['body'] ) ) 
	{
            $response_json = json_decode( $response['body'] );
            //Check whether data return or not.
            if( isset( $response_json->message ) ) {
                return false;
            }
	    if($api === 'shots')
	    {
            	foreach( $response_json as $index => $entry )
		{		
		        $images[ $index ]['title'] = $entry->title;
	                $images[ $index ]['width'] = $entry->width;
	                $images[ $index ]['height'] = $entry->height;
	                $images[ $index ]['created_at'] = strtotime( $entry->created_at );
			if(isset($entry->images->hidpi))
			{
	                	$images[ $index ]['image'] = preg_replace( '/^(http:|https:)/', '', $entry->images->hidpi );
			}
			else
			{
				$images[ $index ]['image'] = preg_replace( '/^(http:|https:)/', '', $entry->images->normal );
			}
	                $images[ $index ]['thumbnail'] = preg_replace( '/^(http:|https:)/', '', $entry->images->teaser );
	                $images[ $index ]['permalink'] = $entry->html_url;
	                $images[ $index ]['comments_count'] = $entry->comments_count;
	                $images[ $index ]['likes_count'] = $entry->likes_count;
         
			$author = $this->slidedeck_author_info($slidedeck);
		
			$images[ $index ]['author_name'] = $author['author_name'];
	                $images[ $index ]['author_username'] = $author['author_username'];
	                $images[ $index ]['author_avatar'] = $author['author_avatar'] ;
	                $images[ $index ]['author_url'] =  $author['author_url'];     
		}
	}		
	else if($api === 'likes')
	{
		foreach( $response_json as $index => $entry )
		{		
		   $images[ $index ]['title'] = $entry->shot->title;
	          $images[ $index ]['width'] = $entry->shot->width;
	                $images[ $index ]['height'] = $entry->shot->height;
	                $images[ $index ]['created_at'] = strtotime( $entry->shot->created_at );
			if(isset($entry->images->hidpi))
			{
	                	$images[ $index ]['image'] = preg_replace( '/^(http:|https:)/', '', $entry->shot->images->hidpi );
			}
			else
			{
				$images[ $index ]['image'] = preg_replace( '/^(http:|https:)/', '', $entry->shot->images->normal );
			}
	                $images[ $index ]['thumbnail'] = preg_replace( '/^(http:|https:)/', '', $entry->shot->images->teaser );
	                $images[ $index ]['permalink'] = $entry->shot->html_url;
	                $images[ $index ]['comments_count'] = $entry->shot->comments_count;
	                $images[ $index ]['likes_count'] = $entry->shot->likes_count;
			
			$author = $this->slidedeck_author_info($slidedeck);
		
			$images[ $index ]['author_name'] = $author['author_name'];
        	        $images[ $index ]['author_username'] = $author['author_username'];
        	        $images[ $index ]['author_avatar'] = $author['author_avatar'] ;
        	        $images[ $index ]['author_url'] =  $author['author_url'];

               	}
	}	
		
        } 
	else 
	{
            return false;
        }
        
        return $images;
    }
    
    function slidedeck_form_content_source( $slidedeck, $source ) {
        // Fail silently if the SlideDeck is not this type or source
        if( !$this->is_valid( $source ) ) {
            return false;
        }
        
        include( dirname( __FILE__ ) . '/views/show.php' );
    }
    
    /**
     * Hook into slidedeck_get_source_file_basedir filter
     * 
     * Modifies the source's basedir value for relative file referencing
     * 
     * @param string $basedir The defined base directory
     * @param string $source_slug The slug of the source being requested
     * 
     * @uses SlideDeck::is_valid()
     * 
     * @return string
     */
    function slidedeck_get_source_file_basedir( $basedir, $source_slug ) {
        if( $this->is_valid( $source_slug ) ) {
            $basedir = dirname( __FILE__ );
        }
        
        return $basedir;
    }
    
    /**
     * Hook into slidedeck_get_source_file_baseurl filter
     * 
     * Modifies the source's basedir value for relative file referencing
     * 
     * @param string $baseurl The defined base directory
     * @param string $source_slug The slug of the source being requested
     * 
     * @uses SlideDeck::is_valid()
     * 
     * @return string
     */
    function slidedeck_get_source_file_baseurl( $baseurl, $source_slug ) {
        if( $this->is_valid( $source_slug ) ) {
           $baseurl = SLIDEDECK2_URLPATH . '/sources/' . basename( dirname( __FILE__ ) );
        }
        
        return $baseurl;
    }
    
    /**
     * Render slides for SlideDecks of this type
     * 
     * Loads the slides associated with this SlideDeck if it matches this Deck type and returns
     * a string of HTML markup.
     * 
     * @param array $slides_arr Array of slides
     * @param object $slidedeck SlideDeck object
     * 
     * @global $SlideDeckPlugin
     * 
     * @uses SlideDeckPlugin::process_slide_content()
     * @uses Legacy::get_slides()
     * 
     * @return string
     */
    function slidedeck_get_slides( $slides, $slidedeck ) {
        global $SlideDeckPlugin;
        
        // Fail silently if not this Deck type
        if( !$this->is_valid( $slidedeck['source'] ) ) {
            return $slides;
        }
        
        // How many decks are on the page as of now.
        $deck_iteration = 0;
        if( isset( $SlideDeckPlugin->SlideDeck->rendered_slidedecks[ $slidedeck['id'] ] ) )
        	$deck_iteration = $SlideDeckPlugin->SlideDeck->rendered_slidedecks[ $slidedeck['id'] ];
        
        // Slides associated with this SlideDeck
        $slides_nodes = $this->get_slides_nodes( $slidedeck );
        $slide_counter = 1;
		// set the first slide flag
		$SlideDeckPlugin->is_first_slide = true;
        if( is_array( $slides_nodes ) ){
            foreach( $slides_nodes as &$slide_nodes ) {
                $slide = array(
                	'source' => $this->name,
                    'title' => $slide_nodes['title'],
                    'classes' => array( 'has-image' ),
                    'thumbnail' => (string) $slide_nodes['thumbnail'],
                    'created_at' => $slide_nodes['created_at'],
                    'type' => "image"
                );
                $slide = array_merge( $this->slide_node_model, $slide );
                
                // Build an in-line style tag if needed
                if( !empty( $slide_styles ) ) {
                    foreach( $slide_styles as $property => $value ) {
                        $slide['styles'] .= "{$property}:{$value};";
                    }
                }
	            
	            $slide_nodes['source'] = $slide['source'];
	            $slide_nodes['type'] = $slide['type'];
                
                $slide['title'] = $slide_nodes['title'] = slidedeck2_stip_tags_and_truncate_text( $slide_nodes['title'], $slidedeck['options']['titleLengthWithImages'], "&hellip;" );
                $slide_nodes['content'] = isset( $slide_nodes['description'] ) ? $slide_nodes['description'] : "";
                $slide_nodes['excerpt'] = slidedeck2_stip_tags_and_truncate_text( $slide_nodes['content'], $slidedeck['options']['excerptLengthWithImages'], "&hellip;" );
                
                if( !empty( $slide_nodes['title'] ) ) {
                    $slide['classes'][] = "has-title";
                } else {
                    $slide['classes'][] = "no-title";
                }
                
                if( !empty( $slide_nodes['description'] ) ) {
                    $slide['classes'][] = "has-excerpt";
                } else {
                    $slide['classes'][] = "no-excerpt";
                }
                
                // Set link target node
                $slide_nodes['target'] = $slidedeck['options']['linkTarget'];
                
                $slide['content'] = $SlideDeckPlugin->Lens->process_template( $slide_nodes, $slidedeck );
                
                $slide_counter++;
                
                $slides[] = $slide;
				// set the first slide flag
				$SlideDeckPlugin->is_first_slide = false;
            }
        }
        
        return $slides;
    }
}
