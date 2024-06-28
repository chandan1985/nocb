<?php 
class SlideDeckSource_Tumblr extends SlideDeck {
    var $label = "Tumblr";
    var $name = "tumblr";
    var $taxonomies = array( 'images','videos' );
     
    var $default_lens = "tool-kit";
    
    var $options_model = array(
        'Setup' => array(
            'total_slides' => array(
                'value' => 5,
                'data' => 'integer'
            ),
            'tumblr_domain_name' => array(
                'value' => 'slidedeck'
            ),
            'tumblr_post_type' => array(
                'value' => 'photos'
            ),
        )
    );
    
    function add_hooks() {
        global $SlideDeckPlugin;
        $slidedeck_namespace = $SlideDeckPlugin->namespace;
        
        add_action( "{$this->namespace}_form_content_source", array( &$this, "slidedeck_form_content_source" ), 10, 2 );
    }
    
        
    function slidedeck_form_content_source( $slidedeck, $source ) {
        // Fail silently if the SlideDeck is not this type or source
        if( !$this->is_valid( $source ) ) {
            return false;
        }

        $namespace = $this->namespace;  
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
           $baseurl = SLIDEDECK2_PROFESSIONAL_URLPATH . '/sources/' . basename( dirname( __FILE__ ) );
        }
        
        return $baseurl;
    }
        
    /**
     * Get Feed
     * 
     * Fetches a feed, caches it and returns the 
     * cached result or the results after caching them.
     * 
     * @param string $feed_url The URL of the gplus feed with a JSON response
     * @param integer $slidedeck_id The ID of the deck (for caching)
     * 
     * @return array An array of arrays containing the images and various meta.
     */
    function get_slides_nodes( $slidedeck ){ 
        global $SlideDeckPlugin;
        
        $args = array(
            'sslverify' => false,
            'timeout' => 10
        );
		
        $tumblr_domain_name = $slidedeck['options']['tumblr_domain_name'];
	$tumblr_domain_id = $slidedeck['options']['tumblr_domain_name'] . ".tumblr.com";
        $post_type = 'photos';
        switch( $slidedeck['options']['tumblr_post_type'] ) {
            case 'photos':
                $post_type = 'photo';
		$slidedeck['type'] = "image";
            break;
            case 'videos':
                $post_type = 'video';
		$slidedeck['type'] = "video";
            break;
        }		
		// Get the appropriate Parent ID of the post, in case this is an autosave or revision - used for proper meta storage
        $slidedeck_id = $SlideDeckPlugin->SlideDeck->get_parent_id( $slidedeck['id'] );
		
		// use our slidedeck to fetch token
		
                $last_saved_tumblr_api_key = get_option( $this->namespace . '_last_saved_tumblr_api_key' );

	$valid_response = true;
	$this->current_slidedeck = $slidedeck;
	$results = array();

        $author_url = "https://api.tumblr.com/v2/blog/".$tumblr_domain_id."/avatar";
	$feed_url = "http://api.tumblr.com/v2/blog/".$tumblr_domain_id."/posts/".$post_type."?api_key=".$last_saved_tumblr_api_key."&limit=".$slidedeck['options']['total_slides'] ;

        $cache_key = $slidedeck_id . $feed_url . $slidedeck['options']['cache_duration'] . $this->name;
			
        $response = slidedeck2_cache_read( $cache_key );
            
        if( !$response ) {
        	$response = wp_remote_get( $feed_url, $args );
                if( !is_wp_error( $response ) ) {
                    // Write the cache if a valid response
                    if( !empty( $response ) ) {
            	        slidedeck2_cache_write( $cache_key, $response, $slidedeck['options']['cache_duration'] );
                    }
                }
        }

        if( is_wp_error( $response ) ){ 
        	$valid_response = false;
                return false;
        }	

	if($valid_response){
		$response_body = $response['body'];
        	// Prep. response for use
        	$response_json = json_decode( $response_body );
		$tumblrposts = $response_json->response->posts; 

        	if($valid_response && !empty($tumblrposts) ){
			$slide_counter = 1;
        	    	foreach($tumblrposts as $index => $post){
				if($slidedeck['type'] == 'image'){	
					$results[ $index ]['title'] = $post->caption;
					$photo_image = $post->photos[0];
					$photo_thumbnail = $photo_image -> alt_sizes[2];		
				        $results[ $index ]['width'] = $photo_image -> original_size->width;
				        $results[ $index ]['height'] = $photo_image -> original_size ->height;
				        $results[ $index ]['created_at'] = $post->date;
				        $results[ $index ]['image'] = $photo_image -> original_size -> url;
				        $results[ $index ]['thumbnail'] = $photo_thumbnail -> url;
				        $results[ $index ]['permalink'] = $post->image_permalink;
				        //$results[ $index ]['content'] = ;
					$results[ $index ]['author_name'] = $post->blog_name;
					$results[ $index ]['author_url'] = $response_json->response->blog->url;	
					$results[ $index ]['author_avatar'] = $author_url;
				}
				else if($slidedeck['type'] == 'video'){
					$results[ $index ]['title'] = $post->caption;	
			        	$results[ $index ]['width'] = $post -> thumbnail_width;
			        	$results[ $index ]['height'] = $post -> thumbnail_height;
			        	$results[ $index ]['created_at'] = $post->date;
        	                        $results[ $index ]['slide_counter']=$results[ $index ]['deck_iteration'] = $slide_counter;
					$results[ $index ]['video_meta'] = array();
					$results[ $index ]['video_meta']['id']= $post->id;
					$results[ $index ]['video_meta']['service'] = 'tumblr';

					$permalink = (isset($post->video_url)) ? $post->video_url : $post->permalink_url;
					if(isset($post->permalink_url)){
						if(strpos($post->permalink_url,"vimeo" )){
							$permalink = str_replace("vimeo.com","player.vimeo.com/video",$post->permalink_url);
						}
						else if(strpos($post->permalink_url,"youtube" )){
							$permalink = str_replace("watch?v=","embed/",$post->permalink_url);
						}	
					}
				
	                        	$results[ $index ]['video_meta']['permalink'] = $permalink;
					$results[ $index ]['video_meta']['full_image'] = $post->thumbnail_url;
			        	$results[ $index ]['thumbnail'] = $post->thumbnail_url;
			        	$results[ $index ]['permalink'] = $post->video_url;
					$results[ $index ]['author_name'] = $post->blog_name;
					$results[ $index ]['author_url'] = $response_json->response->blog->url;	
					$results[ $index ]['author_avatar'] = $author_url;
					$slide_counter++;
				}        
			}	
		}	 
	}
        return $results;
    }

     /**
     * Register scripts used by Decks
     * 
     * @uses wp_register_script()
     */
    function register_scripts() {
        // Fail silently if this is not a sub-class instance
        if( !isset( $this->name ) ) {
            return false;
        }
        wp_register_script( "slidedeck-deck-{$this->name}-admin", SLIDEDECK2_PROFESSIONAL_URLPATH . '/sources/' . $this->name . '/source.js', array( 'jquery', 'slidedeck-admin' ), SLIDEDECK2_PROFESSIONAL_VERSION, true );
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
                    'created_at' => $slide_nodes['created_at']
                );
                $slide = array_merge( $this->slide_node_model, $slide );
                
				
                // Build an in-line style tag if needed
                if( !empty( $slide_styles ) ) {
                    foreach( $slide_styles as $property => $value ) {
                        $slide['styles'] .= "{$property}:{$value};";
                    }
                }
                
                $slide['title'] = $slide_nodes['title'] = slidedeck2_stip_tags_and_truncate_text( $slide_nodes['title'], $slidedeck['options']['titleLengthWithImages'], "&hellip;" );
                $slide_nodes['content'] = isset( $slide_nodes['description'] ) ? $slide_nodes['description'] : "";
                $slide_nodes['excerpt'] = slidedeck2_stip_tags_and_truncate_text( $slide_nodes['content'], $slidedeck['options']['excerptLengthWithImages'], "&hellip;" );
                
                if( ($slidedeck['options']['tumblr_post_type'] == "photos" ) ) {
                    $slide['classes'][] = "has-image";
                    $slide['type'] = "image";
                    $slide['thumbnail'] = $slide_nodes['thumbnail'];
                } else if($slidedeck['options']['tumblr_post_type'] == "videos"){
		    $slide['type'] = "video";
		    $slide['thumbnail'] = $slide_nodes['thumbnail'];		
		}
                
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
                
                $slide_nodes['source'] = $slide['source'];
                $slide_nodes['type'] = $slide['type'];
				
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
