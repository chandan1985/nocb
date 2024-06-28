<?php
class SlideDeckSource_zenfolio extends SlideDeck {
    var $label = "Zenfolio";
    var $name = "zenfolio";
    var $taxonomies = array( 'images' );
    var $default_lens = "tool-kit";
    
    var $options_model = array(
        'Setup' => array(
            'zenfolio_username' => array(
                'value' => "slidedeck",
                'data' => 'string'
            ),
            'zenfolio_password' => array(
                'value' => "pass@321",
                'data' => 'passowrd'
            ),
	    'zenfolio_galleries' => array(
                'value' => ''
            )
        )
    );
        
    function add_hooks() { 
       $this->slidedeck_namespace = SlideDeckPlugin::$st_namespace;
        
       $slidedeck_namespace = $this->slidedeck_namespace; 
       add_action( 'wp_ajax_update_zenfolio_activation_info', array( &$this, 'wp_ajax_update_zenfolio_info' ) );
       add_action( "{$slidedeck_namespace}_form_content_source", array( &$this, "slidedeck_form_content_source" ), 10, 2 );
    }
    

    /**
     * Ajax function to get the user's playlists
     * 
     * @return string A <select> element containing the playlists.
     */
    function wp_ajax_update_zenfolio_info() {
        $zenfolio_username = isset($_REQUEST['zenfolio_username'])?$_REQUEST['zenfolio_username']:"";
	$zenfolio_password = isset($_REQUEST['zenfolio_password'])?$_REQUEST['zenfolio_password']:"";
	        
	echo $this->get_zenfolio_galleries_from_token( $zenfolio_username,$zenfolio_password );

        die();
    }

	
    function get_zenfolio_galleries_from_token($zenfolio_username,$zenfolio_password,$slidedeck = null){
	
	$galleries = false;
		require_once("inc/Zenfolio.php");
		if( !isset( $zenfolio ) ){	
			$zenfolio = new Zenfolio(); 
		}

		if($zenfolio_username != $slidedeck['options']['zenfolio_username']){
			delete_transient("slidedeck_".$slidedeck['id']."_zenfolio_token_transient");
		}
						
		$zenfolio_authenticate_token = get_transient("slidedeck_".$slidedeck['id']."_zenfolio_token_transient");
		
				
		if(!isset($zenfolio_authenticate_token) &&  empty($zenfolio_authenticate_token)){ 
			$zenfolio_authenticate_token = $zenfolio->authenticatePlain($zenfolio_username,$zenfolio_password,$slidedeck);
            		
			set_transient( "slidedeck_".$slidedeck['id']."_zenfolio_token_transient", $zenfolio_authenticate_token, 60*60*24 );

		}    			
			$publicProfile = $zenfolio->loadPublicProfile($zenfolio_username);
			if($publicProfile === false) {
				$valid['userName'] = '';
			}
			$valid['userName'] = $publicProfile->LoginName;

			$hierarchy = $zenfolio->loadGroupHierarchy($valid['userName']); 	
	
			$this->get_group_recursively($hierarchy->Elements, $galleries ); 
	
	// Zenfolio User gallery Call
        $galleries_select = array();
		
        if( $galleries ){
            foreach( $galleries as $gallery ){
                $galleries_select[ $gallery['id'] ] = $gallery['title'];
            }
        }

        $html_input = array(
            'type' => 'select',
            'label' => "Zenfolio Galleries",
            'attr' => array( 'class' => 'fancy' ),
            'values' =>$galleries_select
        );

		$firstKey = $galleries_select;
	reset($firstKey);
    	$firstKey = key($firstKey);
	$slidedeck_id = $slidedeck['id'];
	update_option("slidedeck_".$slidedeck['id']."_zenfolio_firstkey",$firstKey);
	
		
        return slidedeck2_html_input( 'options[zenfolio_galleries]', $slidedeck['options']['zenfolio_galleries'], $html_input, false );				

    }
	

   function get_group_recursively($hierarchy_elements, &$galleries ){
	
	for($index = 0 ; $index < count($hierarchy_elements); $index++)
	{
		if(isset($hierarchy_elements[$index]->Elements))
		{
			$this->get_group_recursively($hierarchy_elements[$index]->Elements, $galleries );
		}
		else if($hierarchy_elements[$index]->Type == "Gallery"){
			$galleries[] = array('id'=> $hierarchy_elements[$index]->Id,
										'title'=> $hierarchy_elements[$index]->Title,
										'type' => $hierarchy_elements[$index]->Type );
		}
		else if($hierarchy_elements[$index]->Type == "Collection"){
			$galleries[] = array('id'=> $hierarchy_elements[$index]->Id,
										'title'=> $hierarchy_elements[$index]->Title,
										'type' => $hierarchy_elements[$index]->Type );
		}	
	}
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
     * Get Zenfolio Image Feed
     * 
     * Fetches a Zenfolio feed, caches it and returns the 
     * cached result or the results after caching them.
     * 
     * @param string $feed_url The URL of the gplus feed with a JSON response
     * @param integer $slidedeck_id The ID of the deck (for caching)
     * 
     * @return array An array of arrays containing the images and various meta.
     */
    function get_slides_nodes( $slidedeck ){

	require_once("inc/Zenfolio.php");       

		$zenfolio = new Zenfolio();		
		$id = $slidedeck['options']['zenfolio_galleries'];
		$zenfolio_authenticate_token = get_transient("slidedeck_".$slidedeck['id']."_zenfolio_token_transient");
		if(empty($zenfolio->token) && empty($zenfolio_authenticate_token)){
		   $zenfolio_username =$slidedeck['options']['zenfolio_username'] ; 
		   $zenfolio_password = $slidedeck['options']['zenfolio_password'];
		   $zenfolio_authenticate_token = $zenfolio->authenticatePlain($zenfolio_username,$zenfolio_password,$slidedeck);	 
		   $zenfolio->token = $zenfolio_authenticate_token;
		}
		
		if(empty($zenfolio->token)){
			$zenfolio->token = $zenfolio_authenticate_token;
		}
		
		if(empty($id)){
			$id = get_option("slidedeck_".$slidedeck['id']."_zenfolio_firstkey"); 
		}
		$images = array();
			$photoSet = $zenfolio->loadPhotoSet($id,'LEVEL1',true);
			$photos = $photoSet->Photos; 
			
			unset( $this->current_slidedeck );
        		   
			foreach($photos as $index => $photo)
			{			
				$images[ $index ]['title'] = $photo->Title;
			        $images[ $index ]['width'] = $photo->Width;
			        $images[ $index ]['height'] = $photo->Height;
			        $images[ $index ]['created_at'] = $photo->TakenOn->Value;
			        $images[ $index ]['image'] = $photo->OriginalUrl;
			        $images[ $index ]['thumbnail'] = $photo->OriginalUrl."?sn=".$photo->Sequence;
			        $images[ $index ]['permalink'] = $photo->PageUrl;
			        $images[ $index ]['content'] = $photo->Caption;
			        $images[ $index ]['author_name'] = $photo->Owner;
			        $images[ $index ]['author_url'] = "http://".$photo->UrlHost;
			}
        return $images;
    }
            
    function slidedeck_form_content_source( $slidedeck, $source ) {
        // Fail silently if the SlideDeck is not this type or source
        if( !$this->is_valid( $source ) ) {
            return false;
        }	
	$galleries_select = $this->get_zenfolio_galleries_from_token( $slidedeck['options']['zenfolio_username'],$slidedeck['options']['zenfolio_password'],$slidedeck );
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
	            
	            $slide_nodes['source'] = $slide['source'];
	            $slide_nodes['type'] = $slide['type'];
                
                // Build an in-line style tag if needed
                if( !empty( $slide_styles ) ) {
                    foreach( $slide_styles as $property => $value ) {
                        $slide['styles'] .= "{$property}:{$value};";
                    }
                }
                
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

    /**
     * Hook into wp_feed_options action
     * 
     * Hook into the SimplePie feed options object to modify parameters when looking up
     * feeds for RSS based feed SlideDecks.
     */
    function wp_feed_options( $feed, $url ) {
        $feed->set_cache_duration( $this->current_slidedeck['options']['cache_duration'] );
    }
}

