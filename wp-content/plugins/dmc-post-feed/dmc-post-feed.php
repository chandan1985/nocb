<?php
/*
Plugin Name: DMC Post Feed
Plugin URI: http://dolanmedia.com
Description: Displays posts from internal blog and/or category with various display options.
Author: Dave Buchanan
Version: 0.5
Author URI: http://dolanmedia.com
Modifications:	7/21/2010 - Dave Buchanan - Removed post_field_request filter, bug with get_permalink, basically you need to do select * for this to work 100%
				10/21/2010 - Dan Ball - updating the 2.9 version to have the same features as the current lower version widget
				05/17/2011 - Jerry Johnson - added post_meta var output, just for $post_meta_quote_source
				06/09/2011 - Jerry Johnson - added meta_key and value functions
				01/14/2014 - Jerry Johnson - added multiplecats, stickyposts, li cat classes
				03/04/2014 - Chris Meier - Clean up for sticky posts and general plugin niceness
				06/17/2014 - Joy Hein - Fix for ticket 59042
*/ 

				/* Function that registers our widget. */
				function load_dmc_post_feed() {
					register_widget( 'dmc_post_feed' );
				}
				add_action( 'widgets_init', 'load_dmc_post_feed' );

				class dmc_post_feed extends WP_Widget {
					function __construct() {
		// action for ajax calls in widget control 
						add_action('wp_ajax_post_feed_get_cats',array('dmc_post_feed', 'ajax_get_categories'));
						add_action('wp_ajax_post_feed_get_parent_cats',array('dmc_post_feed', 'ajax_get_parent_categories'));

		// widget actual processes
						$widget_ops = array('description' => __('Displays posts from internal blog and/or category with various display options', 'dmc-post-feed'));
						parent::__construct('dmc-post-feed', __('DMC Post Feed'), $widget_ops);

		///availble field mappings to select from when doing get_posts
						$this->available_post_fields = array(
							'title' => 'post_title',
							'date' => 'post_date',
							'link' => 'guid',
							'content' => 'post_content',
							'excerpt' => 'post_content,post_excerpt',
							'author' => 'post_author',
							'first_image' => 'post_content'
						);		
					}	

					function widget($args, $instance) {
						global $fields,$switched,$blog_id, $wp_the_query, $more;
		// outputs the content of the widget
						extract( $args );

						/* User-selected settings. */
						$title = apply_filters('widget_title', stripslashes($instance['title']));
						$title_link = (bool) $instance['title_link'];
						$blogid = isset($instance['blogid']) ? strip_tags($instance['blogid']) : '';
						$blogcat = isset($instance['blogcat']) ? strip_tags($instance['blogcat']) : '';
						$parent_cat = isset($instance['parent_cat']) ? strip_tags($instance['parent_cat']) : '' ;
						$parent_cat_local = isset($instance['parent_cat_local']) ? strip_tags($instance['parent_cat_local']) : '';
						$post_match_meta_current_key = isset($instance['post_match_meta_current_key']) ? stripslashes($instance['post_match_meta_current_key']) : '';
						$post_match_meta_feed_key = isset($instance['post_match_meta_feed_key']) ? stripslashes($instance['post_match_meta_feed_key']) : '';
						$post_match_meta_current_key2 = isset($instance['post_match_meta_current_key2']) ? stripslashes($instance['post_match_meta_current_key2']) : '';
						$post_match_meta_feed_key2 = isset($instance['post_match_meta_feed_key2']) ? stripslashes($instance['post_match_meta_feed_key2']) : '';
						$post_match_meta_current_key3 = isset($instance['post_match_meta_current_key3']) ? stripslashes($instance['post_match_meta_current_key3']) : '';
						$post_match_meta_feed_key3 = isset($instance['post_match_meta_feed_key3']) ? stripslashes($instance['post_match_meta_feed_key3']) : '';
						$hltext = isset($instance['hltext']) ? stripslashes($instance['hltext']) : '';
						$text = isset($instance['text']) ? stripslashes($instance['text']) : '';
						$beforetext = isset($instance['beforetext']) ? stripslashes($instance['beforetext']) : '';
						$aftertext = isset($instance['aftertext']) ? stripslashes($instance['aftertext']) : '';
						$hlnum = isset($instance['hlnum']) ? strip_tags($instance['hlnum']) : '';
						$numitems = isset($instance['numitems']) ? strip_tags($instance['numitems']) : '';
						$hide_empty_widget = isset($instance['hide_empty_widget']) ? (bool) $instance['hide_empty_widget'] : '';

						$use_curcat = (bool) $instance['use_curcat'];
						$curcat = '';

						$use_sticky = (bool) $instance['use_sticky'];

						$orderby = strip_tags($instance['orderby']);
						$orderby_sortorder = strip_tags($instance['orderby_sortorder']);
						$meta_key = strip_tags($instance['meta_key']);
						$meta_compare = strip_tags($instance['meta_compare']);
						$meta_value = strip_tags($instance['meta_value']);
						$post_age_cutoff = strip_tags(stripslashes($instance['post_age_cutoff']));
						$post_age_multiplier = strip_tags(stripslashes($instance['post_age_multiplier']));

						$basic_options = $instance['basic_options'];
						$type = stripslashes($instance['type']);


		//regex used for replacing tokens (or variables) in text with respective values
						$variable_regex = '/\$(\D(?:\w|:)+)(\^)?(?:\[(\w+)\])?/mse';
						$variable_regex4 = '/\$(\D(?:\w|:)+)(\^)?(?:\[(\w+)\])?/ms';
  //  $variable_regex = '%(?<=\[)[^>]+(?=\]</td>)%i';
		//regex for removing extra stuff after variables in string, example: $date[date stuff here] becomes just $date
						$special_regex = '/\[[a-zA-Z0-9 -:,\/]{1,}\]/';

		//if curcat is true then get list of categories for current post or archive
						if ($use_curcat && (is_archive() || is_single())){
							if(is_archive()) {
								$curcat = isset($wp_the_query->query_vars['cat']) ? $wp_the_query->query_vars['cat'] : get_cat_id(single_cat_title("",false));
							}
							else {
								foreach((get_the_category()) as $category) {
									$curcat = (strlen($curcat) > 0) ? $category->cat_ID . ',' . $curcat : $category->cat_ID;
								}
							}
						}		

		//if type is basic then auto generate post formatting//
						if($type=='basic') {
							$hltext = $this->generate_hl_post_format($basic_options);
							$text = $this->generate_post_format($basic_options);
						}
		// save the content to a var
						$widget_content = '';

						/* Before widget (defined by themes). */
						$widget_content .= $before_widget;

        // fix div wraps if in jarida
						if (wp_get_theme() == 'Jarida') {
							$before_title = "";
                // fix the jarida assumptions
							$after_title = '<div class="widget-container">';
							$widget_content .= $before_title;
							$widget_content .= $after_title;
                // clear it in case it is used later
							$after_title = "";
						}
						
		////Set post fields to be selected in query, include highlight items and regular items to output
						$fields = $this->find_fields($hltext.$text); 

		///Get maximum time in seconds to show post age
						if( is_numeric($post_age_cutoff) && is_numeric($post_age_multiplier)  ){
							$post_age_limit = ($post_age_cutoff * $post_age_multiplier);
						}else {
							$post_age_limit = 1;
						}

		////Make the title linkable to the category if selected and if a category is set
						if( $title_link && $blogcat != null) {
			///switch to blog for this widget
							switch_to_blog($blogid);
							$title = '<a href="' . get_category_link($blogcat) . '">' . $title . '</a>';
			///switch back to current blog
							restore_current_blog();
						}
						/* Title of widget (before and after defined by themes). */
						$replacement_array1 = array(
							'title' => $before_title . $title . $after_title
							,'plain_title' => $title
							,'category_permalink' => get_category_link( $blogcat )
						);	
						/** old code **/ 

		//$widget_content .= preg_replace( $variable_regex, '$replacement_array1[\\1]', $beforetext );
						/** new code by asentech preg_replace_callback **/
						$widget_content .= preg_replace_callback($variable_regex4, function($m) use ($replacement_array1) {return (isset($replacement_array1[$m[1]])?$replacement_array1[$m[1]]:''); },$beforetext);;

    //exit;

						if ($parent_cat != '') {
			// get all the cats for THIS post, in the current blog
							$categories = get_the_category();
			// find all of this post's cats that are children of the parent cat
			// check slug then name
							$post_child_cats = array();
							$post_child_cats_name = array();
							foreach ( $categories  as $cat ) {
								$post_child_cats[] = $cat->slug;
								$post_child_cats_name[] = $cat->name;
							}                       
			// get all child categories of the parent cat local
							$categories=get_categories(array( 'parent' => $parent_cat_local ));
							$child_cats = array();       
							$child_cats_name = array();       
			// find all this posts cats in parent_cat_local
							foreach ( $categories  as $cat ) {
								if ( in_array($cat->slug, $post_child_cats) ) {
									$child_cats[]=$cat->slug;
								} 
								if ( in_array($cat->name, $post_child_cats_name) ) {
									$child_cats_name[]=$cat->name;
								}
							}
						}

		// match the current post's meta_key
						if ( $post_match_meta_current_key && $post_match_meta_feed_key ) {
							$hold_vars = array();
							$hold_vars['key'] = $post_match_meta_feed_key;

							$value = get_post_meta(get_the_ID(),$post_match_meta_current_key,true);
							if ( strlen($value) > 0 ) {
								$hold_vars['value'] = $value;
							}
							$pass_vars['meta_query'][] = $hold_vars; 
						}
						if ( $post_match_meta_current_key2 && $post_match_meta_feed_key2 ) {
							$hold_vars = array();
							$hold_vars['key'] = $post_match_meta_feed_key2;

							$value = get_post_meta(get_the_ID(),$post_match_meta_current_key2,true);
							if ( strlen($value) > 0 ) {
								$hold_vars['value'] = $value;
							}
							$pass_vars['meta_query'][] = $hold_vars; 
						}
						if ( $post_match_meta_current_key3 && $post_match_meta_feed_key3 ) {
							$hold_vars = array();
							$hold_vars['key'] = $post_match_meta_feed_key3;

							$value = get_post_meta(get_the_ID(),$post_match_meta_current_key3,true);
							if ( strlen($value) > 0 ) {
								$hold_vars['value'] = $value;
							}
							$pass_vars['meta_query'][] = $hold_vars; 
						}

		// switch to blog for this widget
						if ($blogid != $blog_id) { switch_to_blog($blogid); }

		// run query on posts table
						$pass_vars = array ('showposts' => $numitems);
						if(strlen($blogcat) > 0 && $blogcat != -1) {
							if(strlen($curcat) > 0 && $curcat != -1)
								$pass_vars['category__and'] = explode(',',$blogcat.','.$curcat);
							else
								$pass_vars['cat'] = $blogcat;
						}		
						else if (strlen($curcat) > 0 && $curcat != -1) {
							$pass_vars['cat'] = $curcat;
						}

        // handle the "match parent category to page" option
						if ($parent_cat != '') {
							if ($pass_vars['category__and'] == "") {
				// we were using the cat, move it into the cat_and
								$pass_vars['category__and'] = explode(',',$pass_vars['cat']);
								unset($pass_vars['cat']);
							}
			// get all child categories of the parent cat
							$categories=get_categories(array( 'parent' => $parent_cat ));
							$matching_cats = array();       
							foreach ( $categories  as $cat ) {
								if (in_array($cat->slug, $child_cats)) {$matching_cats[]=$cat->cat_ID;}
								elseif (in_array($cat->name, $child_cats_name)) {$matching_cats[]=$cat->cat_ID;}
							}
			// append this array to the cat_and array
							$pass_vars['category__and'] = array_merge($pass_vars['category__and'], $matching_cats);
						}
						
		// handle the special meta_data and sorting
						if(strlen($orderby) > 0) 
							$pass_vars['orderby'] = $orderby;
						if(strlen($orderby_sortorder) > 0) 
							$pass_vars['order'] = $orderby_sortorder;
						if(strlen($meta_key) > 0) {
							$hold_vars = array();
							$hold_vars['key'] = $meta_key;
							if(strlen($meta_compare) > 0) {
								$hold_vars['compare'] = $meta_compare;
							}
							if(strlen($meta_value) > 0) {
				// adjust for timezone
								if ($meta_value == '$now') $meta_value=((int)time() + ( get_option( 'gmt_offset' ) * 3600 ));
								$hold_vars['value'] = $meta_value;
							}
							$pass_vars['meta_query'][] = $hold_vars; 
						}



						if ($use_sticky) {
							$sticky_args = array(
								'numberposts' => $numitems, 
								'order' => isset($pass_vars['order']) ? $pass_vars['order'] : 'ASC' , 
								'orderby' => isset($pass_vars['orderby']) ? $pass_vars['orderby'] : 'title' , 
								'meta_query' => isset($pass_vars['meta_query']) ? $pass_vars['meta_query'] : '' ,
								'include' => implode(',', get_option('sticky_posts'))
							);

							$non_sticky_args = array(
								'numberposts' => $numitems, 
								'order' => isset($pass_vars['order']) ? $pass_vars['order'] : 'ASC' , 
								'orderby' => isset($pass_vars['orderby']) ? $pass_vars['orderby'] : 'title' , 
								'meta_query' => isset($pass_vars['meta_query']) ? $pass_vars['meta_query'] : '' ,
								'post__not_in'=>get_option('sticky_posts')
							);
			// add category (or) or category__and (and)
							if (!isset($pass_vars['category__and'])) {
								$sticky_args['category'] = $pass_vars['cat'];
								$non_sticky_args['category'] = $pass_vars['cat'];
							} else {
								$sticky_args['category__and'] = $pass_vars['category__and'];
								$non_sticky_args['category__and'] = $pass_vars['category__and'];

							}


							$sticky_post_list = get_posts($sticky_args);
							$non_sticky_post_list = get_posts($non_sticky_args);

							$all_posts_list = array_merge($sticky_post_list, $non_sticky_post_list);
							$all_posts_list = array_slice($all_posts_list, 0, $numitems);
						}				
						else {			
							$all_posts_list = get_posts($pass_vars);
						}

						$ind = 0;
						foreach( $all_posts_list as $post_list_item ){
			// use highlight or regular loop format?
							$temp_text = ($ind < $hlnum) ? $hltext : $text;

							$replacement_array2 = array();

							setup_postdata( $post_list_item );

			//set the global more so automatic excerpts work since we're on a page
							$more = 0;

							if(strpos($temp_text,'$first_image_in_post_content')!==false) $replacement_array2['first_image_in_post_content'] = dmc_post_feed::get_first_image_in_post($post_list_item->post_content);
							if(strpos($temp_text,'$featured_thumbnail')!==false) {$replacement_array2['featured_thumbnail'] = dmc_post_feed::get_post_feed_image($post_list_item->ID, 'thumbnail');}
							if(strpos($temp_text,'$featured_medium')!==false) $replacement_array2['featured_medium'] = dmc_post_feed::get_post_feed_image($post_list_item->ID, 'medium');
							if(strpos($temp_text,'$featured_large')!==false) $replacement_array2['featured_large'] = dmc_post_feed::get_post_feed_image($post_list_item->ID, 'large');
							if(strpos($temp_text,'$featured_full')!==false) {$replacement_array2['featured_full'] = dmc_post_feed::get_post_feed_image($post_list_item->ID, 'full');}
							if(strpos($temp_text,'$mobile_thumbnail')!==false) $replacement_array2['mobile_thumbnail'] = dmc_post_feed::get_post_feed_image($post_list_item->ID, 'mobile-thumbnail' );
							if(strpos($temp_text,'$mobile_article')!==false) $replacement_array2['mobile_article'] = dmc_post_feed::get_post_feed_image($post_list_item->ID, 'mobile-article' );
							if(strpos($temp_text,'$standard_article')!==false) $replacement_array2['standard_article'] = dmc_post_feed::get_post_feed_image($post_list_item->ID, 'standard-article' );
							if(strpos($temp_text,'$standard_featured')!==false) $replacement_array2['standard_featured'] = dmc_post_feed::get_post_feed_image($post_list_item->ID, 'standard-featured' );
							if(strpos($temp_text,'$standard_thumbnail')!==false) $replacement_array2['standard_thumbnail'] = dmc_post_feed::get_post_feed_image($post_list_item->ID, 'standard-thumbnail' );
							if(strpos($temp_text,'$opengraph_thumbnail')!==false) $replacement_array2['opengraph_thumbnail'] = dmc_post_feed::get_post_feed_image($post_list_item->ID, 'opengraph-thumbnail' );
							if(strpos($temp_text,'$tie_small')!==false) $replacement_array2['tie_small'] = dmc_post_feed::get_post_feed_image($post_list_item->ID, 'tie-small' );
							if(strpos($temp_text,'$tie_large')!==false) $replacement_array2['tie_large'] = dmc_post_feed::get_post_feed_image($post_list_item->ID, 'tie-large' );
							if(strpos($temp_text,'$slider')!==false) $replacement_array2['slider'] = dmc_post_feed::get_post_feed_image($post_list_item->ID, 'slider' );

			//Legacy thumbnail variables, don't use these, but keeping them for now not to break already in use plugins
							if(strpos($temp_text,'$full_image')!==false) $replacement_array2['full_image'] = dmc_post_feed::get_post_feed_image($post_list_item->ID, 'full');			
							if(strpos($temp_text,'$thumb_image')!==false) $replacement_array2['thumb_image'] = dmc_post_feed::get_post_feed_image($post_list_item->ID, 'thumbnail');
							if(strpos($temp_text,'$medium_image')!==false) $replacement_array2['medium_image'] = dmc_post_feed::get_post_feed_image($post_list_item->ID,'medium');

							if(strpos($temp_text,'$video_image')!==false) $replacement_array2['video_image'] = dmc_post_feed::get_video_url($post_list_item->ID,0);
							if(strpos($temp_text,'$video_thumb')!==false) $replacement_array2['video_thumb'] = dmc_post_feed::get_video_url($post_list_item->ID,0,'thumbnail');

							if(strpos($temp_text,'$comment_count')!==false) $replacement_array2['comment_count'] = $post_list_item->comment_count;
							if(strpos($temp_text,'$author')!==false) $replacement_array2['author'] = get_the_author();
							if(strpos($temp_text,'$title')!==false) {$replacement_array2['title'] = $post_list_item->post_title;}
							if(strpos($temp_text,'$content')!==false) {$replacement_array2['content'] = wpautop( do_shortcode( $post_list_item->post_content ) );}
							if(strpos($temp_text,'$date')!==false) $replacement_array2['date'] = $this->get_post_time($post_list_item->ID, $post_list_item->post_date_gmt, $post_list_item->post_modified_gmt, $post_age_limit, $this->get_time_format($temp_text));
							if(strpos($temp_text,'$excerpt')!==false) {
								if( has_excerpt($post_list_item->ID) ) {
									$excerpt = $post_list_item->post_excerpt;
								}
								else {
									$excerpt = get_the_excerpt($post_list_item->ID);
								}

								$replacement_array2['excerpt'] = $excerpt;
							}

			// need to make this a generic post_meta_* value
							if(strpos($temp_text,'$post_meta_quote_source')!==false) $replacement_array2['post_meta_quote_source'] = dmc_post_feed::get_post_metadata($post_list_item->ID,0,'quote_source');
							if(strpos($temp_text,'$post_meta_source')!==false) $replacement_array2['post_meta_source'] = dmc_post_feed::get_post_metadata($post_list_item->ID,0,'source');
							if(strpos($temp_text,'$post_meta_url')!==false) $replacement_array2['post_meta_url'] = dmc_post_feed::get_post_metadata($post_list_item->ID,0,'url');
							if(strpos($temp_text,'$post_meta_event_date')!==false) $replacement_array2['post_meta_event_date'] = date('l, F jS, Y g:i a',(int)dmc_post_feed::get_post_metadata($post_list_item->ID,0,'event_date'));

			// categoryclasses for individual posts
							if(strpos($temp_text,'$categoryclasses')!==false) {
								$categoryclasses = '';
								foreach((get_the_category($post_list_item->ID)) as $category) {
									$categoryclasses .= 'category-' . $category->slug . ' ';
								}
								$replacement_array2['categoryclasses'] = $categoryclasses;
							}	

			//special way to get permalink, sometimes get_permalink errors if not enough vars are queried in get_posts
							if(strpos($temp_text,'$link')!==false) {
								try {
									if ($switched) {
										$replacement_array2['link'] = ( strlen($post_list_item->guid) > 0 ) ? $post_list_item->guid : get_bloginfo('url') . '/?p=' . $post_list_item->ID;
									}
									else {
										$replacement_array2['link'] = get_permalink($post_list_item->ID); 
									}
								}
				///if get_permalink  errors use the guid or the post id
								catch(Exception $var) {
									$replacement_array2['link'] = ( strlen($post_list_item->guid) > 0 ) ? $post_list_item->guid : get_bloginfo('url') . '/?p=' . $post_list_item->ID;
								}
							}

							$temp_text = preg_replace( $special_regex, '', $temp_text );
							/** old code **/
      //$widget_content .= preg_replace( $variable_regex, '$replacement_array2[\\1]', $temp_text );
							/** new code by asentech preg_replace_callback **/
							$widget_content .=  preg_replace_callback($variable_regex4, function($m) use ($replacement_array2) {return (isset($replacement_array2[$m[1]])?$replacement_array2[$m[1]]:''); },$temp_text);


			// increment count so we know when to switch from highlight to regular post format
							$ind++;
						}

		//reset post object when done
						wp_reset_postdata();

		///remove the filter so other widgets aren't affected
						remove_filter('post_fields_request', array('dmc_post_feed', 'post_select_fields'));

		///switch back to current blog
						if ($switched) restore_current_blog();

		////Display after text
						/** old code **/
		//$widget_content .= preg_replace( $variable_regex, '$replacement_array1[\\1]', $aftertext );
						/** new code by asentech preg_replace_callback **/
						$widget_content .= preg_replace_callback($variable_regex4, function($m) use ($replacement_array1) {return (isset($replacement_array1[$m[1]])?$replacement_array1[$m[1]]:''); },$aftertext);;


						/* After widget (defined by themes). */
						$widget_content .= $after_widget;

						if ( ( $hide_empty_widget == false ) || ( $hide_empty_widget == true && count($all_posts_list) > 0 ) ) {
							echo $widget_content;
						}
					}

					function update($new_instance, $old_instance) {
		if( !isset($new_instance['title']) ) // user clicked cancel
		return false;

		// processes widget options to be saved
		
		///build array to store basic settings (checkboxes and select boxes)
		if(isset($new_instance['hl_title_cb'])) $tmp['hl_title_cb'] = $new_instance['hl_title_cb'];
		if(isset($new_instance['hl_author_cb'])) $tmp['hl_author_cb'] = $new_instance['hl_author_cb'];
		if(isset($new_instance['hl_excerpt_cb'])) $tmp['hl_excerpt_cb'] = $new_instance['hl_excerpt_cb'];
		if(isset($new_instance['hl_comment_count_cb'])) $tmp['hl_comment_count_cb'] = $new_instance['hl_comment_count_cb'];
		if(isset($new_instance['hl_date_sb'])) $tmp['hl_date_sb'] = $new_instance['hl_date_sb'];
		if(isset($new_instance['hl_image_sb'])) $tmp['hl_image_sb'] = $new_instance['hl_image_sb'];
		
		if(isset($new_instance['d_title_cb'])) $tmp['d_title_cb'] = $new_instance['d_title_cb'];
		if(isset($new_instance['d_author_cb'])) $tmp['d_author_cb'] = $new_instance['d_author_cb'];
		if(isset($new_instance['d_excerpt_cb'])) $tmp['d_excerpt_cb'] = $new_instance['d_excerpt_cb'];
		if(isset($new_instance['d_comment_count_cb'])) $tmp['d_comment_count_cb'] = $new_instance['d_comment_count_cb'];
		if(isset($new_instance['d_date_sb'])) $tmp['d_date_sb'] = $new_instance['d_date_sb'];
		if(isset($new_instance['d_image_sb'])) $tmp['d_image_sb'] = $new_instance['d_image_sb'];
		
		
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['title_link'] = strip_tags($new_instance['title_link']);
		$instance['use_curcat'] = strip_tags($new_instance['use_curcat']);
		$instance['use_sticky'] = strip_tags($new_instance['use_sticky']);
		$instance['blogid'] = strip_tags($new_instance['blogid']);
		$instance['blogcat'] = (isset( $new_instance['blogcat'] )) ? implode(',', (array) $new_instance['blogcat']) : '';
		$instance['parent_cat'] = (isset( $new_instance['parent_cat'] )) ? implode(',', (array) $new_instance['parent_cat']) : '';
		$instance['parent_cat_local'] = (isset( $new_instance['parent_cat_local'] )) ? implode(',', (array) $new_instance['parent_cat_local']) : '';
		$instance['post_match_meta_current_key'] = stripslashes($new_instance['post_match_meta_current_key']);
		$instance['post_match_meta_feed_key'] = stripslashes($new_instance['post_match_meta_feed_key']);
		$instance['post_match_meta_current_key2'] = stripslashes($new_instance['post_match_meta_current_key2']);
		$instance['post_match_meta_feed_key2'] = stripslashes($new_instance['post_match_meta_feed_key2']);
		$instance['post_match_meta_current_key3'] = stripslashes($new_instance['post_match_meta_current_key3']);
		$instance['post_match_meta_feed_key3'] = stripslashes($new_instance['post_match_meta_feed_key3']);
		$instance['hltext'] = stripslashes($new_instance['hltext']);
		$instance['text'] = stripslashes($new_instance['text']);
		$instance['beforetext'] = stripslashes($new_instance['beforetext']);
		$instance['aftertext'] = stripslashes($new_instance['aftertext']);
		$instance['hlnum'] = strip_tags($new_instance['hlnum']);
		$instance['numitems'] = strip_tags($new_instance['numitems']);
		$instance['type'] = strip_tags($new_instance['type']);
		$instance['hide_empty_widget'] = strip_tags($new_instance['hide_empty_widget']);

		$instance['orderby'] = strip_tags($new_instance['orderby']);
		$instance['orderby_sortorder'] = strip_tags($new_instance['orderby_sortorder']);
		$instance['meta_key'] = strip_tags($new_instance['meta_key']);
		$instance['meta_compare'] = strip_tags($new_instance['meta_compare']);
		$instance['meta_value'] = strip_tags($new_instance['meta_value']);
		$instance['post_age_cutoff'] = $new_instance['post_age_cutoff'];
		$instance['post_age_multiplier'] = $new_instance['post_age_multiplier'];


		$instance['basic_options'] = $tmp;
		
		return $instance;
	}
	
	function form($instance) {
		// outputs the widget control form on admin
		include('widget-control-form.php');
	}
	
	/*  ############################### PUBLIC FUNCTIONS ################################################ */
	/*
	 * Function used for posts filter, simply returns fields set in global variable
	 */
	public function post_select_fields() {
		global $fields;
		return $fields;
	}
	/*
	 * Function used in widget control for ajax/jquery method, returns json object of categories
	 */
	public function ajax_get_categories() {

		check_ajax_referer( 'widget_feed_posts' );
		
		$categories = dmc_post_feed::get_blog_categories($_POST['blog_ID']);
		
		$cats_array = array();
		
		foreach ($categories as $cat)
		{
			$cats_array[$cat->term_id] = $cat->name;
		}
		
		die(json_encode($cats_array));
	}
	/*
	 * Function used in widget control for ajax/jquery method, returns json object of categories
	 */
	public function ajax_get_parent_categories() {

		check_ajax_referer( 'widget_feed_posts' );
		
		$parent_categories = dmc_post_feed::get_blog_parent_categories($_POST['blog_ID']);
		
		$cats_array = array();
		
		foreach ($parent_categories as $cat)
		{
			$cats_array[$cat->term_id] = $cat->name;
		}
		
		die(json_encode($cats_array));
	}
	/* ########################## END PUBLIC FUNCTIONS #################################################### */
	
	/* ########################## PRIVATE FUNCTIONS #################################################### */
	/*
	 * Gets the post time
	 * mainly used to calculate relative time, IE time story was published compared to current time
	 */
	private function get_post_time($id, $time, $modified, $age_limit, $format = '') {
		if($format == 'relative'){

			$returndate = '';
			
			if($modified > $time) {
				$timediff = (time() - (date('Z'))) - strtotime( $modified );
			}
			else {
				$timediff = (time() - (date('Z'))) - strtotime( $time );
			}
			
			if(($modified > $time) && ($timediff < $age_limit)) {
				$returndate = 'Updated ';
			}
			
			if ($timediff < $age_limit) {
				if ($timediff < 60) {
					$returndate .= 'Less than a minute ago ';
				}
				else if ($timediff < 3600) { 
					$minutes = floor( $timediff / 60 );
					$returndate .=  $minutes . " minute" . ($minutes >= 2 ? 's' : '') . " ago";
				}
				else if ($timediff < 86400) {
					$hours = floor($timediff / 3600);
					$returndate .= $hours . " hour" . ($hours >= 2 ? 's' : '') . " ago";
				} 
				else if ($timediff < 604800) {
					$days = floor($timediff / 86400);
					$returndate .= $days . " day" . ($days >= 2 ? 's' : '') . " ago";
				} 
				else {
					$returndate = @date(get_option('date_format'), strtotime($time));    
				}
			}
			
			return $returndate;
		}
		else 
			return get_post_time($format,false,$id);
	}
	/*
	 * Builds post formatting, for basic option (highlight text)
	 */
	private function generate_hl_post_format($options) {
		$s = "";
		
		//include image?
		if(isset($options['hl_image_sb']) && strlen($options['hl_image_sb'])>0) {
			$s = '$'.$options['hl_image_sb'].'_image';
		}

		//add list item
		$s .= "<li>";
		
		//include title? (and add link)
		if(isset($options['hl_title_cb']) && $options['hl_title_cb'] == 'on')
			$s .= '<a href="$link" class="post-feed-post-title">$title</a>';
		
		//include author? 
		if(isset($options['hl_author_cb']) && $options['hl_author_cb'] == 'on')
			$s .= '<span class="post-feed-post-author"> by $author </span>';
		
		//include excerpt? 
		if(isset($options['hl_excerpt_cb']) && $options['hl_excerpt_cb'] == 'on')
			$s .= ' $excerpt';
		
		//include comment count? 
		if(isset($options['hl_comment_count_cb']) && $options['hl_comment_count_cb'] == 'on')
			$s .= '<span class="post-feed-post-commentcount"> ($comment_count comments)</span>';
		
		//include date? (and set format) 
		if(isset($options['hl_date_sb']) && strlen($options['hl_date_sb'])>0)
			$s .= '<span class="post-feed-post-date"><br/>$date['.$options['hl_date_sb'].']</span>';

		//close list item
		$s .= "</li>";
		
		return $s; 
	}
	
	/*
	 * Builds post formatting, for basic option (basic text)
	 */
	private function generate_post_format($options) {
		$s = "";
		
		//include image?
		if(isset($options['d_image_sb']) && strlen($options['d_image_sb'])>0) {
			$s = '$'.$options['d_image_sb'].'_image';
		}

		//add list item
		$s .= "<li>";
		
		//include title? (and add link)
		if(isset($options['d_title_cb']) && $options['d_title_cb'] == 'on')
			$s .= '<a href="$link" class="post-feed-post-title">$title</a>';
		
		//include author? 
		if(isset($options['d_author_cb']) && $options['d_author_cb'] == 'on')
			$s .= '<span class="post-feed-post-author"> by $author </span>';
		
		//include excerpt? 
		if(isset($options['d_excerpt_cb']) && $options['d_excerpt_cb'] == 'on')
			$s .= ' $excerpt';
		
		//include comment count? 
		if(isset($options['d_comment_count_cb']) && $options['d_comment_count_cb'] == 'on')
			$s .= '<span class="post-feed-post-commentcount"> ($comment_count comments)</span>';
		
		//include date? (and set format) 
		if(isset($options['d_date_sb']) && strlen($options['d_date_sb'])>0)
			$s .= '<span class="post-feed-post-date"><br/>$date['.$options['d_date_sb'].']</span>';

		//close list item
		$s .= "</li>";
		
		return $s; 
	}
	
	private function get_time_format($txt) {
		//pull out the time format, first look for date variable then pull out string between folling open/close brackets
		$d = '$date';
		//find position of first date variable
		$pos = strpos($txt, $d);
		//find start bracket
		$startb = strpos($txt, '[', $pos);
		//find end bracket
		$endb = strpos($txt, ']', $pos);
		//If we found brackets after date variable pull out substring we want for date format
		//else use whats set for this blog
		$sub = ( strlen($startb)>0 && strlen($endb)>0) ? substr($txt, $startb+1, $endb-$startb-1) : get_option('date_format');

		return $sub;
	}
	
	/*
	 * Returns categories with posts for given blog
	 */
	private function get_blog_categories($blog_id) {
		if(empty($blog_id) || !$this->dmc_blog_exists($blog_id)) {
			return;
		}
		global $wpdb;
		
		$args = array(
			'orderby' => 'name',
			'order' => 'ASC'
		);
		
		switch_to_blog($blog_id);
		$categories = get_categories($args);
		restore_current_blog();
		
		return $categories;
	}

	//if ( ! function_exists( 'dmc_blog_exists' ) ) {

    /**
     * Checks if a blog exists and is not marked as deleted.
     *
     * @link   http://wordpress.stackexchange.com/q/138300/73
     * @param  int $blog_id
     * @param  int $site_id
     * @return bool
     */
    function dmc_blog_exists( $blog_id, $site_id = 0 ) {

    	global $wpdb;
    	static $cache = array ();

    	$site_id = (int) $site_id;

    	if ( 0 === $site_id )
    		$site_id = get_current_site()->id;

    	if ( empty ( $cache ) or empty ( $cache[ $site_id ] ) ) {

            if ( wp_is_large_network() ) // we do not test large sites.
            return TRUE;

            $query = "SELECT `blog_id` FROM $wpdb->blogs
            WHERE site_id = $site_id AND deleted = 0";

            $result = $wpdb->get_col( $query );

            // Make sure the array is always filled with something.
            if ( empty ( $result ) )
            	$cache[ $site_id ] = array ( 'do not check again' );
            else
            	$cache[ $site_id ] = $result;
        }

        return in_array( $blog_id, $cache[ $site_id ] );
    }
//}

	/*
	 * Returns parent categories with posts for given blog
	 */
	private function get_blog_parent_categories($blog_id) {
		global $wpdb;
		
		switch_to_blog($blog_id);

		$args = array(
			'orderby' => 'name',
			'order' => 'ASC',
			'parent' => 0
		);
		$parent_categories = get_categories($args);
		restore_current_blog();
		
		return $parent_categories;
	}
	
	/*
	 * Returns parent categories with posts for given blog
	 */
	private function get_blog_parent_categories_local($blog_id) {
		global $wpdb;
		
		//switch_to_blog($blog_id);

		$args = array(
			'orderby' => 'name',
			'order' => 'ASC',
			'parent' => 0
		);
		$parent_categories = get_categories($args);
		//restore_current_blog();
		
		return $parent_categories;
	}
	
	
	/** 
	  *	Function that simply returns the first image tag found in given string
	**/
	private function get_first_image_in_post($content = '') {
		// Grab the URL of the first image found in the post content
		//preg_match( '/<img[^>]+src="([^"]+)"/', $content, $matches );
		preg_match( '/<img[^>]+src="([^"]+)"[^>]+>/', $content, $matches );
		
		$post_image = (count( $matches ) == 2 && strlen( $matches[1] ) > 0 ? $matches[0] : '');
		
		$post_image = preg_replace('/height="\d+"/','',$post_image);
		$post_image = preg_replace('/width="\d+"/','',$post_image);
		
		return $post_image;
	}
	
	/**
	 * Function to return image tag for given post id
	**/
	private function get_post_feed_image( $post_id, $img_size ) {

		if ( has_post_thumbnail( $post_id ) ) {
			$feed_image = get_the_post_thumbnail( $post_id, $img_size );
		}
		else {
			$feed_image = '';
			$attachment = get_children( array (
				'post_parent' => $post_id,
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'post_status' => null,
				'numberposts' => 1
			));

			foreach($attachment as $attach) {				
				$feed_image = wp_get_attachment_image( $attach->ID, $img_size );
			}
		}		
		return $feed_image;
	}

	private function get_video_url($post_id = 0, $echo = 1, $img_size = 'medium', $params = ''){
		global $switched;
		
		$video_link='';
		// if we asked for the tiny version, try the tiny metadata field
		if ($img_size == 'thumbnail') {
			$video_link = get_post_meta($post_id, 'video_thumb_small_url', true);
		}
		
		// if they asked for medium, or the tiny was missing, use the normal metadata field
		if (!$video_link) {
			$video_link = get_post_meta($post_id, 'video_thumb_url', true);
		}
		
		$new_video_img = "<a href='".get_bloginfo('url').'/?p='.$post_id."'><img class = 'video_img' src='".$video_link."' title='".get_the_title()."'/></a>";

		// hey, if we are switched, cheat and use the guid as the url
		if ($switched) {
			$switched_post_info = get_post($post_id); 
			$new_video_img = "<a href='".$switched_post_info->guid."'><img class = 'video_img' src='".$video_link."' title='".get_the_title()."'/></a>";
		}
		
		if ($echo) {
			echo $new_video_img;
		}
		else {
			return $new_video_img;
		}
	}
	
	private function get_post_metadata($post_id = 0, $echo = 1, $meta_key = 'source', $params = '') {
		$z = get_post_meta($post_id, $meta_key, true);
		if ($echo) {				
			echo $z;
		}
		else {
			return $z;
		}
	}

	private function get_embedded_content($content=''){
		
		// Grab the URL of the first image found in the post content
		$embed_content = htmlspecialchars_decode($content);

		if (strpos($embed_content,'<cut>')):
			$media = substr($embed_content,0,strpos($embed_content,'<cut>')/1);
		elseif (strpos($embed_content,'</object>')):
			$media = substr($embed_content,0,strpos($embed_content,'</object>')/1+9);
		elseif (strpos($embed_content,'</embed>')):
			$media = substr($embed_content,0,strpos($embed_content,'</embed>')/1+8);
		else: 
			$media = '';
		endif;
		
		$embed_cleanup = "<script type='text/javascript'>document.write(wpdetexturize('$media'))</script>";
		
		return $embed_cleanup;
	} 
	
	/*
	 * Returns list of fields to select according to given text
	 */
	private function find_fields($text) {
		$fields = "ID";
		foreach($this->available_post_fields as $key=>$val) {
			//find string in text
			if(strpos($text,$key)>0)
				$fields = ($fields=="") ? $val : $fields . "," . $val;
		}
		///if theres asterik just set it to be just that
		if(strpos($text,'*') > 0)
			$fields = '*';
		///clean up duplicates
		return $fields;
	}
	/* ####################################### END PRIVATE FUNCTIONS ####################################### */
}
?>