<!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ WIDGET FORM @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->
<!-- used for display settings for dmc-post-feed widget -->
<?php
 /* @todo
  * turn sort fields into radio/select boxes (ASC|DESC, =|!=|>|>=|<|<=, title|date|...|meta_value) etc
  * build list of valid meta_keys to pick from
  *
  */
 ?>
 <?php
	//include javascript for the jquery stuff
 include('js.php');
 
	// defaults for this instance
 $instance = wp_parse_args( (array) $instance 
 	, array( 'title' => ''
 		, 'title_link' => 'false'
 		, 'blogid' => '1'
 		, 'blogcat' => ''
 		, 'parent_cat' => ''
 		, 'use_curcat' => false
 		, 'use_sticky' => false
 		, 'hltext' => '$thumb_image<li><a href="$link">$post_title</a> - $excerpt</li>'
 		, 'hlnum' => '1'
 		, 'numitems' => '5'
 		, 'text' => '<li><a href="$link">$title</a></li>' 
 		, 'beforetext' => '$title <ul>' 
 		, 'aftertext' => '</ul>' 
 		, 'type' => 'basic'
 		, 'orderby' => ''
 		, 'orderby_sortorder' => ''
 		, 'meta_key' => ''
 		, 'meta_compare' => ''
 		, 'meta_value' => ''
 		, 'post_age_cutoff' => ''
 		, 'post_age_multiplier' => ''
 		, 'basic_options' => array('hl_title_cb'=>'on'
 			,'hl_excerpt_cb'=>'on'
 			,'hl_date_cb'=>'on'
 			,'hl_author_cb'=>''
 			,'hl_comment_count_cb'=>''
 			,'hl_date_sb'=> get_option('date_format')
 			,'hl_image_sb'=>'thumb'
 			,'d_title_cb'=>'on'
 			,'d_excerpt_cb'=>''
 			,'d_date_cb'=>''
 			,'d_author_cb'=>''
 			,'d_comment_count_cb'=>''
 			,'d_date_sb'=> ''
 			,'d_image_sb'=>'') 
 	) 
 );
 
 $title = strip_tags($instance['title']);
 $title_link = (bool) strip_tags($instance['title_link']);
 $use_curcat = (bool) strip_tags($instance['use_curcat']);
 $use_sticky = (bool) strip_tags($instance['use_sticky']);
 $blogid = strip_tags($instance['blogid']);
 $blogcat = strip_tags($instance['blogcat']);
 $parent_cat = strip_tags($instance['parent_cat']);
 $parent_cat_local = isset($instance['parent_cat_local']) ? strip_tags($instance['parent_cat_local']) : '';
 $post_match_meta_current_key = isset($instance['post_match_meta_current_key']) ? strip_tags($instance['post_match_meta_current_key']) : '';
 $post_match_meta_feed_key = isset($instance['post_match_meta_feed_key']) ? strip_tags($instance['post_match_meta_feed_key']) : '';
 $post_match_meta_current_key2 = isset($instance['post_match_meta_current_key2']) ? strip_tags($instance['post_match_meta_current_key2']) : '';
 $post_match_meta_feed_key2 = isset($instance['post_match_meta_feed_key2']) ? strip_tags($instance['post_match_meta_feed_key2']) : '';
 $post_match_meta_current_key3 = isset($instance['post_match_meta_current_key3']) ? strip_tags($instance['post_match_meta_current_key3']) : '';
 $post_match_meta_feed_key3 = isset($instance['post_match_meta_feed_key3']) ? strip_tags($instance['post_match_meta_feed_key3']) : '';
 $hltext = stripslashes($instance['hltext']);
 $hlnum = strip_tags($instance['hlnum']);
 $numitems = strip_tags($instance['numitems']);
 $text = stripslashes($instance['text']);
 $type = stripslashes($instance['type']);
 $hide_empty_widget = isset($instance['hide_empty_widget']) ? stripslashes($instance['hide_empty_widget']) : '';
 $beforetext = stripslashes($instance['beforetext']);
 $aftertext = stripslashes($instance['aftertext']);
 $orderby = strip_tags($instance['orderby']);
 $orderby_sortorder = strip_tags($instance['orderby_sortorder']);
 $meta_key = strip_tags($instance['meta_key']);
 $meta_compare = strip_tags($instance['meta_compare']);
 $meta_value = strip_tags($instance['meta_value']);
 $post_age_cutoff = strip_tags(stripslashes($instance['post_age_cutoff']));
 $post_age_multiplier = strip_tags(stripslashes($instance['post_age_multiplier']));
 $basic_options = $instance['basic_options'];
 
	///date formats
 $dateformats = array( 	 array('name' => date(get_option('date_format')), 'value'=> get_option('date_format'))
 	,array('name'=> date('m/d/Y'), 'value'=>'m/d/Y') 
 	,array('name'=> date('l, F d, Y'), 'value'=>'l, F d, Y') 
 	,array('name'=> date('g:i a'), 'value'=>'g:i a') 
 	,array('name'=> date('g:i a F d, Y'), 'value'=>'g:i a F d, Y') 
 	,array('name'=> '1 minute ago (relative)', 'value'=>'relative') 
 	,array('name'=> "Don't display date", 'value'=>'') 
 );
	///image formats
 $imageformats = array( 	 array('name' => 'No image', 'value'=> '')
 	,array('name'=> 'Thumbnail', 'value'=>'thumb') 
 	,array('name'=> 'Medium', 'value'=>'medium') 
 	,array('name'=> 'Full Size', 'value'=>'full') 
 );
 
 $post_age_multiplers = array(
 	60 => 'minute(s)',
 	3600 => 'hour(s)',
 	86400 => 'day(s)'
 );
 
 ?>

 <p><label for="<?php echo $this->get_field_id('title'); ?>">Title:</label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
 <p> <label for="<?php echo $this->get_field_id('blogid'); ?>">Blog:</label>
 	<select id="<?php echo $this->get_field_id('blogid'); ?>" name="<?php echo  $this->get_field_name('blogid'); ?>" onChange="load_blog_categories( this, '<?php echo $this->get_field_id('blogcat'); ?>' );load_blog_parent_categories( this, '<?php echo $this->get_field_id('parent_cat'); ?>' );">
 		<?php $blogs = get_sites(); ?>
 		<?php foreach($blogs as $blog) : ?>
 			<option value="<?php echo $blog->blog_id; ?>" <?php if($blog->blog_id == $blogid) echo 'selected=true'; ?>><?php echo $blog->domain . $blog->path; ?></option>
 		<?php endforeach; ?>
 	</select>
 </p>
 <p>
 	<label for="<?php echo $this->get_field_id('blogcat'); ?>">Category:</label>
 	
 	<select multiple style="height:60px;" size="6" id="<?php echo $this->get_field_id('blogcat'); ?>" name="<?php echo  $this->get_field_name('blogcat'); ?>[]" >
 		<?php if ($blogid != '') : ?>
 			<?php  $categories = dmc_post_feed::get_blog_categories($blogid); ?>
 			<?php $catList = explode( ',', $blogcat ); ?>
 			<?php 
 			if(!empty($categories) && is_array($categories)) {
 				foreach($categories as $cat) : ?>
 					<option value="<?php echo $cat->term_id; ?>" <?php if (in_array($cat->term_id,$catList)) echo 'selected=true'; ?>> <?php echo $cat->name; ?>
 					<?php 
 				endforeach;
 			}
 			?>
 		<?php endif; ?>
 		<option value="-1">[None]</option>
 	</select>
 </p>
 <p>
 	<label for="<?php echo $this->get_field_id('title_link'); ?>">
 		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('title_link'); ?>" name="<?php echo $this->get_field_name('title_link'); ?>"<?php checked( (bool) $title_link, true ); ?> />
 		<?php _e( 'Link title to category page.' ); ?>
 	</label>
 </p>
 <p>
 	<label for="<?php echo $this->get_field_id('use_curcat'); ?>">
 		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('use_curcat'); ?>" name="<?php echo $this->get_field_name('use_curcat'); ?>"<?php checked( (bool) $use_curcat, true ); ?> />
 		<?php _e( 'Limit posts to categories being viewed. (applies only to post, category, or archive pages)' ); ?>
 	</label>
 </p>
 <p>
 	<label for="<?php echo $this->get_field_id('use_sticky'); ?>">
 		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('use_sticky'); ?>" name="<?php echo $this->get_field_name('use_sticky'); ?>"<?php checked( (bool) $use_sticky, true ); ?> />
 		<?php _e( 'Honor stickyposts' ); ?>
 	</label>
 </p>
 <p>
 	<label for="<?php echo $this->get_field_id('hide_empty_widget'); ?>">
 		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hide_empty_widget'); ?>" name="<?php echo $this->get_field_name('hide_empty_widget'); ?>"<?php checked( (bool) $hide_empty_widget, true ); ?> />
 		<?php _e( 'Hide empty widget' ); ?>
 	</label>
 </p>
 <p>
 	<strong><label for="<?php $this->get_field_id('numitems'); ?>">Posts to Display</label></strong>
 	<input size="4" maxlength="4" id="<?php echo $this->get_field_id('numitems'); ?>" name="<?php echo $this->get_field_name('numitems'); ?>" type="text" value="<?php echo esc_attr($numitems); ?>" />
 	<br/>Posts to highlight <input size="2" maxlength="2" id="<?php echo $this->get_field_id('hlnum'); ?>" name="<?php echo $this->get_field_name('hlnum'); ?>" type="text" value="<?php echo esc_attr($hlnum); ?>" />
 </p>
 <p>
 	<strong><label for="<?php echo $this->get_field_id('post_age_cutoff'); ?>">Post Age Display Cutoff</label></strong><br>
 	<input type="text" name="<?php echo $this->get_field_name('post_age_cutoff'); ?>" id="<?php echo $this->get_field_id('post_age_cutoff'); ?>" value="<?php echo $post_age_cutoff; ?>" size="3" maxlength="2" />
 	<select name="<?php echo $this->get_field_name('post_age_multiplier'); ?>" id="<?php echo $this->get_field_id('post_age_multiplier'); ?>">
 		<?php foreach ($post_age_multiplers as $key => $value) : ?>
 			<option value="<?php echo $key; ?>" <?php echo ($post_age_multiplier == $key ? 'selected="selected"' : ''); ?>><?php echo $value; ?></option>
 		<?php endforeach; ?>
 	</select>
 </p>

 <!--@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ BASIC POST SETTINGS @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@-->
 <hr />	
 <input type="radio" <?php if($type == 'basic') echo "checked='true'"; ?> value="basic" name="<?php echo $this->get_field_name('type'); ?>" onclick="set_state('<?php echo $this->get_field_id('basic'); ?>','<?php echo $this->get_field_id('custom'); ?>', false)"/> Basic


 <div id="<?php echo $this->get_field_id('basic'); ?>" <?php if($type != 'basic') echo "style='display:none;'"; ?>>	
 	<strong>Highlighted Posts Display</strong> <BR>
 	<input type="checkbox" name="<?php echo $this->get_field_name('hl_title_cb'); ?>" <?php if(isset($basic_options['hl_title_cb']) && $basic_options['hl_title_cb'] == 'on') echo "checked='true'"; ?>> Title 
 	<input type="checkbox" name="<?php echo $this->get_field_name('hl_author_cb'); ?>" <?php if(isset($basic_options['hl_author_cb']) && $basic_options['hl_author_cb'] == 'on') echo "checked='true'"; ?>> Author 
 	<input type="checkbox" name="<?php echo $this->get_field_name('hl_excerpt_cb'); ?>" <?php if(isset($basic_options['hl_excerpt_cb']) && $basic_options['hl_excerpt_cb'] == 'on') echo "checked='true'"; ?>> Excerpt 
 	<BR><input type="checkbox" name="<?php echo $this->get_field_name('hl_comment_count_cb'); ?>" <?php if(isset($basic_options['hl_comment_count_cb']) && $basic_options['hl_comment_count_cb'] == 'on') echo "checked='true'"; ?>> Comment Count 
 	<BR>Date:
 	<select name="<?php echo $this->get_field_name('hl_date_sb'); ?>">
 		<?php foreach($dateformats as $dates) : ?>
 			<option value="<?php echo $dates['value']; ?>" <?php if($dates['value'] == $basic_options['hl_date_sb']) echo "selected='true'"; ?>><?php echo $dates['name']; ?></option>
 		<?php endforeach; ?>
 	</select> 
 	<br>
 	Image:
 	<select name="<?php echo $this->get_field_name('hl_image_sb'); ?>">
 		<?php foreach($imageformats as $images) : ?>
 			<option value="<?php echo $images['value']; ?>" <?php if($images['value'] == $basic_options['hl_image_sb']) echo "selected='true'"; ?>><?php echo $images['name']; ?></option>
 		<?php endforeach; ?>
 	</select>
 	<BR><BR><strong>Posts Display</strong> 
 	<BR>
 	<input type="checkbox" name="<?php echo $this->get_field_name('d_title_cb'); ?>" <?php if(isset($basic_options['d_title_cb']) && $basic_options['d_title_cb'] == 'on') echo "checked='true'"; ?>>Title 
 	<input type="checkbox" name="<?php echo $this->get_field_name('d_author_cb'); ?>" <?php if(isset($basic_options['d_author_cb']) && $basic_options['d_author_cb'] == 'on') echo "checked='true'"; ?>>Author
 	<input type="checkbox" name="<?php echo $this->get_field_name('d_excerpt_cb'); ?>" <?php if(isset($basic_options['d_excerpt_cb']) && $basic_options['d_excerpt_cb'] == 'on') echo "checked='true'"; ?>>Excerpt
 	<BR><input type="checkbox" name="<?php echo $this->get_field_name('d_comment_count_cb'); ?>" <?php if(isset($basic_options['d_comment_count_cb']) && $basic_options['d_comment_count_cb'] == 'on') echo "checked='true'"; ?>>Comment Count  
 	<BR>
 	Date:
 	<select name="<?php echo $this->get_field_name('d_date_sb'); ?>">
 		<?php foreach($dateformats as $dates) : ?>
 			<option value="<?php echo $dates['value']; ?>" <?php if($dates['value'] == $basic_options['d_date_sb']) echo "selected='true'"; ?>><?php echo $dates['name']; ?></option>
 		<?php endforeach; ?>
 	</select> 
 	<br>
 	Image:
 	<select name="<?php echo $this->get_field_name('d_image_sb'); ?>">
 		<?php foreach($imageformats as $images) : ?>
 			<option value="<?php echo $images['value']; ?>" <?php if($images['value'] == $basic_options['d_image_sb']) echo "selected='true'"; ?>><?php echo $images['name']; ?></option>
 		<?php endforeach; ?>
 	</select>
 </div>
 <br />
 <hr />

 <!--@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ CUSTOM POST SETTINGS @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@-->	
 <input type="radio" <?php if($type == 'custom') echo "checked='true'"; ?> value="custom" name="<?php echo $this->get_field_name('type'); ?>" onclick="set_state('<?php echo $this->get_field_id('custom'); ?>','<?php echo $this->get_field_id('basic'); ?>', false)"/> Custom

 <div id="<?php echo $this->get_field_id('custom'); ?>" <?php if($type != 'custom') echo "style='display:none;'"; ?>>	
 	<p>
 		
 		<div>
 			
 			<br/>Highlight Posts Format*
 			<textarea name="<?php echo $this->get_field_name('hltext'); ?>" id="<?php echo $this->get_field_id('hltext'); ?>" rows="3" cols="25"><?php echo $hltext; ?></textarea>

 			<br/><BR/>Posts Format*			
 			<textarea name="<?php echo $this->get_field_name('text'); ?>" id="<?php echo $this->get_field_id('text'); ?>" rows="3" cols="25"><?php echo $text; ?></textarea>
 		</div></p>
 		<div><BR/>*<em>Available variables for post display: $title, $link, $content, $excerpt, $author, $featured_thumbnail, $featured_medium, $featured_large, $featured_full, $first_image_in_post_content, $date, $date[relative], $date[ANY DATE FORMAT STRING]), $comment_count</em></div>
 		<br />
 		<hr />
 	</div>
 	<br />

 	<BR/>
 	<a href="javascript:set_more('<?php echo $this->get_field_id('more'); ?>')">More formatting</a>
 	<div id="<?php echo $this->get_field_id('more'); ?>" style='display:none;'>		
 		<p>
 			<strong>Before/After Formatting</strong>
 			
 			<br/>Before Text**
 			<textarea name="<?php echo $this->get_field_name('beforetext'); ?>" id="<?php echo $this->get_field_id('beforetext'); ?>" rows="3" cols="25"><?php echo $beforetext; ?></textarea>

 			<br/><BR/>After Text**			
 			<textarea name="<?php echo $this->get_field_name('aftertext'); ?>" id="<?php echo $this->get_field_id('aftertext'); ?>" rows="3" cols="25"><?php echo $aftertext; ?></textarea>
 			<div>**<em>Available variables for display: $title - this widget's title, $plain_title (no before/after widget style), $category_permalink</em></div>
 		</p>

 		<p><strong>Post Meta Matching</strong></p>
 		<p>
 			<label for="<?php echo $this->get_field_id('parent_cat'); ?>">Parent Category Match:</label>
 			
 			<select id="<?php echo $this->get_field_id('parent_cat'); ?>" name="<?php echo $this->get_field_name('parent_cat'); ?>[]" >
 				<option value="" <?php if ($parent_cat == '') echo 'selected=true'; ?>>[None]</option>
 				<?php if ($blogid != '') : ?>
 					<?php $categories = dmc_post_feed::get_blog_parent_categories($blogid); ?>
 					<?php $catList = explode( ',', $parent_cat ); ?>
 					<?php foreach($categories as $cat) : ?>
 						<option value="<?php echo $cat->term_id; ?>" <?php if (in_array($cat->term_id,$catList)) echo 'selected=true'; ?>> <?php echo $cat->name; ?>
 					<?php endforeach; ?>
 				<?php endif; ?>
 			</select>
 		</p>
 		<p>
 			<label for="<?php echo $this->get_field_id('parent_cat_local'); ?>">Local Version of Parent Category to match:</label>
 			
 			<select id="<?php echo $this->get_field_id('parent_cat_local'); ?>" name="<?php echo $this->get_field_name('parent_cat_local'); ?>[]" >
 				<option value="" <?php if ($parent_cat_local == '') echo 'selected=true'; ?>>[None]</option>
 				<?php if ($blogid != '') : ?>
 					<?php $categories = dmc_post_feed::get_blog_parent_categories_local($blogid); ?>
 					<?php $catList = explode( ',', $parent_cat_local ); ?>
 					<?php foreach($categories as $cat) : ?>
 						<option value="<?php echo $cat->term_id; ?>" <?php if (in_array($cat->term_id,$catList)) echo 'selected=true'; ?>> <?php echo $cat->name; ?>
 					<?php endforeach; ?>
 				<?php endif; ?>
 			</select>
 		</p>
 		<p><label for="<?php echo $this->get_field_id('post_match_meta_current_key'); ?>">Current Post Meta Key:</label> <input class="widefat" id="<?php echo $this->get_field_id('post_match_meta_current_key'); ?>" name="<?php echo $this->get_field_name('post_match_meta_current_key'); ?>" type="text" value="<?php echo esc_attr($post_match_meta_current_key); ?>" /></p>
 		<p><label for="<?php echo $this->get_field_id('post_match_meta_feed_key'); ?>">Feed Post Meta Key:</label> <input class="widefat" id="<?php echo $this->get_field_id('post_match_meta_feed_key'); ?>" name="<?php echo $this->get_field_name('post_match_meta_feed_key'); ?>" type="text" value="<?php echo esc_attr($post_match_meta_feed_key); ?>" /></p>
 		<hr>
 		<p><label for="<?php echo $this->get_field_id('post_match_meta_current_key2'); ?>">Current Post Meta Key:</label> <input class="widefat" id="<?php echo $this->get_field_id('post_match_meta_current_key2'); ?>" name="<?php echo $this->get_field_name('post_match_meta_current_key2'); ?>" type="text" value="<?php echo esc_attr($post_match_meta_current_key2); ?>" /></p>
 		<p><label for="<?php echo $this->get_field_id('post_match_meta_feed_key2'); ?>">Feed Post Meta Key:</label> <input class="widefat" id="<?php echo $this->get_field_id('post_match_meta_feed_key2'); ?>" name="<?php echo $this->get_field_name('post_match_meta_feed_key2'); ?>" type="text" value="<?php echo esc_attr($post_match_meta_feed_key2); ?>" /></p>
 		<hr>
 		<p><label for="<?php echo $this->get_field_id('post_match_meta_current_key3'); ?>">Current Post Meta Key:</label> <input class="widefat" id="<?php echo $this->get_field_id('post_match_meta_current_key3'); ?>" name="<?php echo $this->get_field_name('post_match_meta_current_key3'); ?>" type="text" value="<?php echo esc_attr($post_match_meta_current_key3); ?>" /></p>
 		<p><label for="<?php echo $this->get_field_id('post_match_meta_feed_key3'); ?>">Feed Post Meta Key:</label> <input class="widefat" id="<?php echo $this->get_field_id('post_match_meta_feed_key3'); ?>" name="<?php echo $this->get_field_name('post_match_meta_feed_key3'); ?>" type="text" value="<?php echo esc_attr($post_match_meta_feed_key3); ?>" /></p>

 		<p><strong>Sort Order and Filtering</strong></p>
 		<p><label for="<?php echo $this->get_field_id('orderby'); ?>">Order By:</label> <input class="widefat" id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>" type="text" value="<?php echo esc_attr($orderby); ?>" /></p>
 		<p><label for="<?php echo $this->get_field_id('orderby_sortorder'); ?>">Sort Order:</label> <input class="widefat" id="<?php echo $this->get_field_id('orderby_sortorder'); ?>" name="<?php echo $this->get_field_name('orderby_sortorder'); ?>" type="text" value="<?php echo esc_attr($orderby_sortorder); ?>" /></p>
 		<p><label for="<?php echo $this->get_field_id('meta_key'); ?>">Meta Key:</label> <input class="widefat" id="<?php echo $this->get_field_id('meta_key'); ?>" name="<?php echo $this->get_field_name('meta_key'); ?>" type="text" value="<?php echo esc_attr($meta_key); ?>" /></p>
 		<p><label for="<?php echo $this->get_field_id('meta_compare'); ?>">Meta Compare:</label> <input class="widefat" id="<?php echo $this->get_field_id('meta_compare'); ?>" name="<?php echo $this->get_field_name('meta_compare'); ?>" type="text" value="<?php echo esc_attr($meta_compare); ?>" /></p>
 		<p><label for="<?php echo $this->get_field_id('meta_value'); ?>">Meta Value:</label> <input class="widefat" id="<?php echo $this->get_field_id('meta_value'); ?>" name="<?php echo $this->get_field_name('meta_value'); ?>" type="text" value="<?php echo esc_attr($meta_value); ?>" /></p>

 	</div>
