<?php
/*
Plugin Name: DMC Google News Feed
Plugin URI: http://dolanmedia.com
Description: Creates an xml feed for consumption by Google News
Author: Dan Ball
Version: 1.0
Author URI: http://dolanmedia.com
Modifications: 
*/ 
class dmc_google_news_feed {
	
	const CUSTOMXSD = "dmccustom.xsd";

	function google_news_feed_menu() {
		if (function_exists('add_submenu_page')) {
			add_submenu_page('options-general.php', 'Google News Feed Options', 'Google News Feed', 'administrator', __FILE__,  array($this, 'google_news_feed_options' ) );
		}
	}

	function google_news_feed_options() {
		$hours_back = "2";
		$sitemap_filename = "main.xml";
		$sitemap_url = "/wp-files/";
		$rss_language = "en";
		$delete_feed = "";
		
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		
		if(isset($_GET['gnf_section']) && $_GET['gnf_section'] == 'pages') 
			include(dirname( __FILE__ ) .'/page_options.php');
		else
			include(dirname( __FILE__ ) .'/post_options.php');
	}

	function get_category_keywords($newsID)
	{
		global $wpdb;
		//Check for new >2.3 Wordpress taxonomy	
		if (function_exists("get_taxonomy") && function_exists("get_terms"))
		{
			//Get categoy names
			$categories = $wpdb->get_results("
				SELECT $wpdb->terms.name FROM $wpdb->term_relationships,  $wpdb->term_taxonomy,  $wpdb->terms
				WHERE $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
				AND $wpdb->term_taxonomy.term_id =  $wpdb->terms.term_id
				AND $wpdb->term_relationships.object_id = $newsID
				AND $wpdb->term_taxonomy.taxonomy = 'category'");
			$i = 0;
			$categoryKeywords = "";
			foreach ($categories as $category)
			{
					if ($i>0){$categoryKeywords.= ", ";} //Comma seperator
					$categoryKeywords.= $category->name; //ammed string
					$i++;
				}
				
			//Get tags				
				$tags = $wpdb->get_results("
					SELECT $wpdb->terms.name FROM $wpdb->term_relationships,  $wpdb->term_taxonomy,  $wpdb->terms
					WHERE $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
					AND $wpdb->term_taxonomy.term_id =  $wpdb->terms.term_id
					AND $wpdb->term_relationships.object_id = $newsID
					AND $wpdb->term_taxonomy.taxonomy = 'post_tag'");
				$i = 0;
				$tagKeywords = "";
				foreach ($tags as $tag)
				{
					if ($i>0){$tagKeywords.= ", ";} //Comma seperator
					$tagKeywords.= $tag->name; //ammed string
					$i++;
				}
			}
		//Old Wordpress database <2.3
			else
			{
				$categories = $wpdb->get_results("SELECT category_id FROM $wpdb->post2cat WHERE post_id=$newsID");
				$i = 0;
				$categoryKeywords = "";
				foreach ($categories as $category)
				{
				if ($i>0){$categoryKeywords.= ", ";} //Comma seperator
				$categoryKeywords.= get_catname($category->category_id); //ammed string
				$i++;
			}
		}
		return $categoryKeywords; //Return post category names as keywords
	}
	//POSTS: this function is triggered whenever plugin's options are changed from wp admin	
	function update_option() {
		//THIS IS HERE FOR FUTURE CHANGES AS NEEDED
		
		//update the news feed
		dmc_google_news_feed::write_google_news_feed();

	}
	//PAGES: this function is triggered whenever plugin's options are changed from wp admin	
	function update_option_pages() {
		//THIS IS HERE FOR FUTURE CHANGES AS NEEDED
		
		//update the pages feed
		dmc_google_news_feed::write_google_news_feed_pages();
	}
	
	//updates main google news feed index file 
	function write_index_feed() {
		global $wpdb, $blog_id;

		//update the main sitemap index file
		//----> http://www.google.com/support/webmasters/bin/answer.py?hl=en&answer=71453&from=35655&rd=1
		$xmlOutput = '<?xml version="1.0" encoding="UTF-8"?>
		<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		//start new file for googlenews.xml (in wp-files)
		$index_file = 'googlenews.xml';

		/////loop through all blogs 
		//query for list of blog ids
		$query = "SELECT blog_id,domain,path,last_updated from wp_blogs /* Get Blogs DMC Google News Feed */";
		$blogs = $wpdb->get_results($query);
		$plugin_dir = plugin_basename(__FILE__); 

		$tmp_blogid = $blog_id; 

		//loop
		foreach($blogs as $b) {
			if ($blog_id != $b->blog_id) switch_to_blog($b->blog_id);
			//is plugin active for this blog?
			if(is_plugin_active($plugin_dir)){
				//build link to this blog's google news feed file 
				$op = get_option('dmc_google_news_feed');
				$fn =isset($op['sitemap_filename']) ? stripslashes($op['sitemap_filename']) : '';
				if (strpos(get_blog_option( $blog_id, 'upload_path' ), 'shared') !== FALSE) {
					$xmlLoc = get_blog_option( $blog_id, 'upload_url_path' ).'/'.$fn;
				} else {
					$xmlLoc='http://'.$b->domain.'/wp-files/'.$fn;
				}
				//add to index feed file
				$xmlOutput .= '<sitemap>
				<loc>'.$xmlLoc.'</loc>
				<lastmod>'.dmc_google_news_feed::w3cDate(strtotime($b->last_updated)).'</lastmod>
				</sitemap>';
			} //end if for plugin active 
		}
		//end loop through blogs

		if (strpos(get_blog_option( $blog_id, 'upload_path' ), 'shared') !== FALSE) {
			$main_blog_id = defined( 'BLOG_ID_CURRENT_SITE' )? BLOG_ID_CURRENT_SITE : 1;
			switch_to_blog($main_blog_id);
			$xmlFile = get_blog_option( $blog_id, 'upload_path' ).'/'.$index_file;
		} else {
			$sitemap_path = $_SERVER['DOCUMENT_ROOT'] . '/wp-files/';
			$xmlFile = $sitemap_path . $index_file;
		}

                ///switch back to current blog
		switch_to_blog($tmp_blogid);

                //write file, close
		$xmlOutput .= '</sitemapindex>';
		$fp = fopen($xmlFile, "w+");
		fwrite($fp, $xmlOutput);
		fclose($fp);
		
         //Ignore output of xml feed if your in quick edit mode
		$currentUrl = $_SERVER['PHP_SELF'];
		
        //var_dump ($currentUrl);

		if (preg_match('/.*ajax\.php.*/', $currentUrl)) {
			
        	//do nothing
		}
		else {
			
        //	echo '<div id="message" class="updated fade">Published Index Feed:'.$xmlFile;
	//		echo ' <a href="'.get_bloginfo('wpurl').'/wp-files/'.$index_file.'">View it</a>';
        //       echo '</div>';
			
		}	

	}
	function write_google_news_feed() 
	{
		global $wpdb;
		global $blog_id;
		$xmlOutput = '';
		$includeMe = '';	
		$last_wrote = get_option('dmc_google_news_feed_write_time');

		#disable writing the feed if the feed has been written in the last 60 seconds (fix for multiple hooks consecutivly firing the write function)

		if ($last_wrote) {
			$t = time();
			if (($t - $last_wrote) < 60) {
				return true;
			}
			else {
				update_option('dmc_google_news_feed_write_time',time());
			}
		}
		else {
			update_option('dmc_google_news_feed_write_time',time());
		}

		// first update the main index feed		
		dmc_google_news_feed::write_index_feed(); 

		// Fetch options from database
		$pluginurl  = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name='siteurl'") .'wp-content/plugins/'. basename(dirname(__FILE__));
		// Output XML header
		// Begin urlset			
		$xmlOutput.= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:news=\"http://www.google.com/schemas/sitemap-news/0.9\" xmlns:dmc=\"".$pluginurl."/".dmc_google_news_feed::CUSTOMXSD."\">\n";
		//Show either Posts or Pages or Both
		$op = get_option("dmc_google_news_feed"); 
		if (stripslashes($op['include_pages']) == '1')
			$includeMe = "AND (p.post_type='page' OR p.post_type = 'post') ";
		elseif (stripslashes($op['include_pages']) == '1')
			$includeMe = "AND p.post_type='page' ";
		$sql = "SELECT DISTINCT p.ID, p.post_date, p.post_date_gmt, p.post_title, u.user_login, m1.meta_value AS security, m2.meta_value AS ownership ";
		$sql .= "FROM ".$wpdb->posts." p ";
		$sql .= "INNER JOIN ".$wpdb->users." u ";
		$sql .= "ON p.post_author = u.id ";
		$sql .= "INNER JOIN ".$wpdb->term_relationships." t ";
		$sql .= "ON p.ID = t.object_id ";
		$sql .= "INNER JOIN ".$wpdb->term_taxonomy." tt ";
		$sql .= "on t.term_taxonomy_id = tt.term_taxonomy_id AND tt.term_id IN (".stripslashes($op['categories_to_show']).") ";
		$sql .= "LEFT OUTER JOIN ".$wpdb->postmeta." m1 ";
		$sql .= "ON p.ID = m1.post_id AND m1.meta_key = 'dmcss_security_policy' ";
		$sql .= "LEFT OUTER JOIN ".$wpdb->postmeta." m2 ";
		$sql .= "ON p.ID = m2.post_id AND m2.meta_key = 'we_own_it' ";
		$sql .= "WHERE p.post_status='publish' ";
		$sql .= "AND (DATEDIFF(CURDATE(), p.post_date_gmt)<=".stripslashes($op['hours_back']).") ";
		$sql .= $includeMe;
		$sql .= "ORDER BY p.post_date_gmt DESC";
		$rows = $wpdb->get_results($sql);

		//$xmlOutput .= $sql;	
		// Output sitemap data
		foreach($rows as $row){
			$showstory = 1;
			if(stripslashes($op['include_stories']) == 'free' && strtolower($row->security) == "subscriber only")
				$showstory = 0;
			else if (stripslashes($op['include_stories']) == 'sub' && strtolower($row->security) != "subscriber only")
				$showstory = 0;
			if(stripslashes($op['include_notowned']) != '1' && strtolower($row->ownership) != "yes")
				$showstory = 0;
			if($showstory == 1)
			{
				$xmlOutput.= "\t<url>\n";
				$xmlOutput.= "\t\t<loc>";
				$xmlOutput.= get_permalink($row->ID);
				$xmlOutput.= "</loc>\n";
				
				$xmlOutput.= "\t\t<news:news>\n";
				$xmlOutput.= "\t\t\t<news:publication>\n";
				$xmlOutput.= "\t\t\t\t<news:name>";
				$xmlOutput.= get_blog_option($blog_id, 'blogname');
				//$xmlOutput.= htmlspecialchars(stripslashes($op['blogname']));
				$xmlOutput.= "</news:name>\n";
				$xmlOutput.= "\t\t\t\t<news:language>";
				$xmlOutput.= stripslashes($op['rss_language']);
				$xmlOutput.= "</news:language>\n";
				$xmlOutput.= "\t\t\t</news:publication>\n";
				if($row->security == "Subscriber Only")
					$xmlOutput.= "\t\t\t<news:access>Subscription</news:access>\n";
				$xmlOutput.= "\t\t\t<news:publication_date>";
				$thedate = substr($row->post_date_gmt, 0, 10);
				$xmlOutput.= $thedate;
				$xmlOutput.= "</news:publication_date>\n";
				$xmlOutput.= "\t\t\t<news:title>";
				$xmlOutput.= htmlspecialchars($row->post_title);
				$xmlOutput.= "</news:title>\n";
				$xmlOutput.= "\t\t\t<news:keywords>";
				$xmlOutput.= htmlentities(dmc_google_news_feed::get_category_keywords($row->ID));
				$xmlOutput.= "</news:keywords>\n"; 
				$xmlOutput.= "\t\t</news:news>\n";

                // @todo: comment out for now, replace with custom file for google news and mobile app if still used
				if (0 == 1) {
                    //CUSTOM DMC FIELDS: post id and image
					$xmlOutput .= "\t\t<dmc:dmc>\n";
					$xmlOutput .= "\t\t\t<dmc:id>";
					$xmlOutput .= $row->ID;
					$xmlOutput .= "</dmc:id>\n";

                    ///output post author
					$xmlOutput .= "\t\t\t<dmc:author>";
					$userob = (function_exists('get_userdatabylogin')) ? get_userdatabylogin($row->user_login) : '';
					$author = $userob->display_name;
					$xmlOutput .= (!empty($author)) ? $author : $row->user_login;
					$xmlOutput .= "</dmc:author>\n";

					if ($op['include_thumbnail'] == 1) {
						$img = dmc_google_news_feed::get_post_image($row->ID, 1, 0, 'thumbnail');
						if (strlen($img) > 0) $xmlOutput .= "\t\t\t<dmc:image>" . $img[0] . "</dmc:image>\n";
					}

					$xmlOutput .= "\t\t</dmc:dmc>\n";
                    //END CUSTOM DMC FIELDS
				}

				$xmlOutput.= "\t</url>\n";
			}
		}

		// End urlset
		$xmlOutput.= "</urlset>\n";

		$xmlOutput.= "<!-- Last build time: ".date("F j, Y, g:i a")."-->";
		
		$sitemap_filename = stripslashes($op['sitemap_filename']);
		$sitemap_url = $_SERVER['DOCUMENT_ROOT'] . stripslashes($op['sitemap_url']);

		if (strpos(get_blog_option( $blog_id, 'upload_path' ), 'shared') !== FALSE) {
			$xmlFile = get_blog_option( $blog_id, 'upload_path' ).'/'.$sitemap_filename;
		} else {
			$xmlFile = $sitemap_url.$sitemap_filename;
		}

		$fp = fopen($xmlFile, "w+");
		fwrite($fp, $xmlOutput);
		fclose($fp);
		
		//attempt to regenerate the mobile feed after each news feed update;
		//dmc_google_news_feed::write_mobile_feed(); 
	}

	function write_mobile_feed()
	{
		global $wpdb;
		global $blog_id;
		
		#disable writing the feed if the feed has been written in the last minute (fix for multiple hooks consecutivly firing the write function)

		$last_wrote = get_option('dmc_google_news_feed_mobile_write_time');

		if ($last_wrote) {
			$t = time();
			if (($t - $last_wrote) < 60) {
				return true;
			}
			else {
				update_option('dmc_google_news_feed_mobile_write_time',time()); 
			}
		}
		else {
			update_option('dmc_google_news_feed_mobile_write_time',time()); 
		}


                // first update the main index feed
                // dmc_google_news_feed::write_index_feed();

		$op = get_option("dmc_google_news_feed");

                //end testing

                // Fetch options from database
		$pluginurl  = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name='siteurl'") .'wp-content/plugins/'. basename(dirname(__FILE__));
                // Output XML header
                // Begin urlset
		$xmlOutput.= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:news=\"http://www.google.com/schemas/sitemap-news/0.9\" xmlns:dmc=\"".$pluginurl."/".dmc_google_news_feed::CUSTOMXSD."\">\n";
                //Show either Posts or Pages or Both
		if (stripslashes($op['include_pages']) == '1')
			$includeMe = "AND (p.post_type='page' OR p.post_type = 'post') ";
		elseif (stripslashes($op['include_pages']) == '1')
			$includeMe = "AND p.post_type='page' ";
		$sql = "SELECT DISTINCT p.ID, p.post_date, p.post_date_gmt, p.post_title, u.user_login, m1.meta_value AS security, m2.meta_value AS ownership ";
		$sql .= "FROM ".$wpdb->posts." p ";
		$sql .= "INNER JOIN ".$wpdb->users." u ";
		$sql .= "ON p.post_author = u.id ";
		$sql .= "INNER JOIN ".$wpdb->term_relationships." t ";
		$sql .= "ON p.ID = t.object_id ";
		$sql .= "INNER JOIN ".$wpdb->term_taxonomy." tt ";
		$sql .= "on t.term_taxonomy_id = tt.term_taxonomy_id AND tt.term_id IN (".stripslashes($op['categories_to_show']).") ";
		$sql .= "LEFT OUTER JOIN ".$wpdb->postmeta." m1 ";
		$sql .= "ON p.ID = m1.post_id AND m1.meta_key = 'dmcss_security_policy' ";
		$sql .= "LEFT OUTER JOIN ".$wpdb->postmeta." m2 ";
		$sql .= "ON p.ID = m2.post_id AND m2.meta_key = 'we_own_it' ";
		$sql .= "WHERE p.post_status='publish' ";
		if (stripslashes($op['include_notowned']) != '1') {
			$sql .= " AND m2.meta_value = 'yes' ";
		}
		$sql .= $includeMe;
		$sql .= "ORDER BY p.post_date_gmt DESC";
		$sql .= " LIMIT " . $op['mobile_display_count'];

		#useful for debugging.
                # echo $sql . "<br><br>";
		$rows = $wpdb->get_results($sql);

                //$xmlOutput .= $sql;
                // Output sitemap data
		foreach($rows as $row){
			$showstory = 1;
			if(stripslashes($op['include_stories']) == 'free' && strtolower($row->security) == "subscriber only")
				$showstory = 0;
			else if (stripslashes($op['include_stories']) == 'sub' && strtolower($row->security) != "subscriber only")
				$showstory = 0;
			if(stripslashes($op['include_notowned']) != '1' && strtolower($row->ownership) != "yes")
				$showstory = 0;
			if($showstory == 1)
			{
				$xmlOutput.= "\t<url>\n";
				$xmlOutput.= "\t\t<loc>";
				$xmlOutput.= get_permalink($row->ID);
				$xmlOutput.= "</loc>\n";

				$xmlOutput.= "\t\t<news:news>\n";
				$xmlOutput.= "\t\t\t<news:publication>\n";
				$xmlOutput.= "\t\t\t\t<news:name>";
				$xmlOutput.= get_blog_option($blog_id, 'blogname');
                                //$xmlOutput.= htmlspecialchars(stripslashes($op['blogname']));
				$xmlOutput.= "</news:name>\n";
				$xmlOutput.= "\t\t\t\t<news:language>";
				$xmlOutput.= stripslashes($op['rss_language']);
				$xmlOutput.= "</news:language>\n";
				$xmlOutput.= "\t\t\t</news:publication>\n";
				if($row->security == "Subscriber Only")
					$xmlOutput.= "\t\t\t<news:access>Subscription</news:access>\n";
				$xmlOutput.= "\t\t\t<news:publication_date>";
				$thedate = substr($row->post_date_gmt, 0, 10);
				$xmlOutput.= $thedate;
				$xmlOutput.= "</news:publication_date>\n";
				$xmlOutput.= "\t\t\t<news:title>";
				$xmlOutput.= htmlspecialchars($row->post_title);
				$xmlOutput.= "</news:title>\n";
				$xmlOutput.= "\t\t\t<news:keywords>";
				$xmlOutput.= htmlentities(dmc_google_news_feed::get_category_keywords($row->ID));
				$xmlOutput.= "</news:keywords>\n";
				$xmlOutput.= "\t\t</news:news>\n";

                            // @todo: comment out for now, replace with custom file for google news and mobile app if still used
				if (0 == 1) {
                                //CUSTOM DMC FIELDS: post id and image
					$xmlOutput .= "\t\t<dmc:dmc>\n";
					$xmlOutput.= "\t\t\t<dmc:id>";
					$xmlOutput.= $row->ID;
					$xmlOutput.= "</dmc:id>\n";

                                ///output post author
					$xmlOutput.= "\t\t\t<dmc:author>";
					$userob = (function_exists('get_userdatabylogin')) ? get_userdatabylogin($row->user_login) : '';
					$author = $userob->display_name;
					$xmlOutput.= (!empty($author)) ? $author : $row->user_login;
					$xmlOutput.= "</dmc:author>\n";

					if($op['include_thumbnail']==1) {
						$img = dmc_google_news_feed::get_post_image($row->ID,1,0,'thumbnail');
						if(strlen($img)>0) $xmlOutput .= "\t\t\t<dmc:image>".$img[0]."</dmc:image>\n";
					}

					$xmlOutput.= "\t\t</dmc:dmc>\n";
                                //END CUSTOM DMC FIELDS
				}

				$xmlOutput.= "\t</url>\n";
			}
		}
                // End urlset
		$xmlOutput.= "</urlset>\n";

		$xmlOutput.= "<!-- Last build time: ".date("F j, Y, g:i a")."-->";

            	// default if still blank
		if ($op['mobile_filename'] == "") { $op['mobile_filename'] = 'mobile_feed.xml'; }

		if(strlen($op['mobile_filename'])>1) {
					//Get path to current feed			
			$mobile_filename = stripslashes($op['mobile_filename']);
			$sitemap_url = $_SERVER['DOCUMENT_ROOT'] . stripslashes($op['sitemap_url']);
			$xmlFile = $sitemap_url.$mobile_filename;
			$fp = fopen($xmlFile, "w+");
			fwrite($fp, $xmlOutput);
			fclose($fp);
		}
	}



	/*
	 * Same as above except unique for pages
	 **/
	function write_google_news_feed_pages() 
	{
		global $wpdb;
		global $blog_id;
		
		// Output XML header
		// Begin urlset			
		$pluginurl  = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name='siteurl'") .'wp-content/plugins/'. basename(dirname(__FILE__));
		$xmlOutput.= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:news=\"http://www.google.com/schemas/sitemap-news/0.9\" xmlns:dmc=\"".$pluginurl."/".dmc_google_news_feed::CUSTOMXSD."\">\n";
		//Show Pages only
		$op = get_option("dmc_google_news_feed_pages"); 
		$sql = "SELECT DISTINCT p.ID, p.post_date_gmt, p.post_title, m1.meta_value AS security ";
		$sql .= "FROM ".$wpdb->posts." p ";
		$sql .= "LEFT OUTER JOIN ".$wpdb->postmeta." m1 ";
		$sql .= "ON p.ID = m1.post_id AND m1.meta_key = 'dmcss_security_policy' ";
		$sql .= "WHERE p.post_status='publish' ";
		$sql .= "AND p.post_type='page' ";
		$sql .= "AND p.ID IN (".stripslashes($op['pages_to_show']).") ";
		$sql .= "ORDER BY p.post_date_gmt DESC";
		$rows = $wpdb->get_results($sql);
		// Output sitemap data
		foreach($rows as $row){
			$showstory = 1;
			if(stripslashes($op['include_stories']) == 'free' && strtolower($row->security) == "subscriber only")
				$showstory = 0;
			else if (stripslashes($op['include_stories']) == 'sub' && strtolower($row->security) != "subscriber only")
				$showstory = 0;
			if($showstory == 1)
			{
				$xmlOutput.= "\t<url>\n";
				$xmlOutput.= "\t\t<loc>";
				$xmlOutput.= get_permalink($row->ID);
				$xmlOutput.= "</loc>\n";
                // @todo: comment out for now, replace with custom file for google news and mobile app if still used
				if (0 == 1) {
                    //CUSTOM DMC FIELDS: post id
					$xmlOutput .= "\t\t<dmc:dmc>\n";
					$xmlOutput .= "\t\t\t<dmc:id>";
					$xmlOutput .= $row->ID;
					$xmlOutput .= "</dmc:id>\n";
					$xmlOutput .= "\t\t</dmc:dmc>\n";
				}

				$xmlOutput.= "\t\t<news:news>\n";
				$xmlOutput.= "\t\t\t<news:publication>\n";
				$xmlOutput.= "\t\t\t\t<news:name>";
				$xmlOutput.= get_blog_option($blog_id, 'blogname');
				//$xmlOutput.= htmlspecialchars(stripslashes($op['blogname']));
				$xmlOutput.= "</news:name>\n";
				$xmlOutput.= "\t\t\t\t<news:language>";
				$xmlOutput.= stripslashes($op['rss_language']);
				$xmlOutput.= "</news:language>\n";
				$xmlOutput.= "\t\t\t</news:publication>\n";
				if($row->security == "Subscriber Only")
					$xmlOutput.= "\t\t\t<news:access>Subscription</news:access>\n";
				$xmlOutput.= "\t\t\t<news:publication_date>";
				$thedate = substr($row->post_date_gmt, 0, 10);
				$xmlOutput.= $thedate;
				$xmlOutput.= "</news:publication_date>\n";
				$xmlOutput.= "\t\t\t<news:title>";
				$xmlOutput.= htmlspecialchars($row->post_title);
				$xmlOutput.= "</news:title>\n";
				$xmlOutput.= "\t\t\t<news:keywords>";
				$xmlOutput.= htmlentities(dmc_google_news_feed::get_category_keywords($row->ID));
				$xmlOutput.= "</news:keywords>\n"; 
				if($op['include_thumbnail']==1) {
					$img = dmc_google_news_feed::get_post_image($row->ID,1,0,'thumbnail');
					if(strlen($img)>0) $xmlOutput .= "\t\t\t<news:image>".$img[0]."</news:image>\n";
				}
				$xmlOutput.= "\t\t</news:news>\n";
				$xmlOutput.= "\t</url>\n";
			}
		}
		// End urlset
		$xmlOutput.= "</urlset>\n";
		$xmlOutput.= "<!-- Last build time: ".date("F j, Y, g:i a")."-->";
		

		$sitemap_filename = stripslashes($op['sitemap_filename']);
		$sitemap_url = $_SERVER['DOCUMENT_ROOT'] . stripslashes($op['sitemap_url']);
		$xmlFile = $sitemap_url.$sitemap_filename;
		
		 //Ignore output of xml feed if your in quick edit mode
		
		$currentUrl = $_SERVER['PHP_SELF'];
		
        //var_dump ($currentUrl);

		if (preg_match('/.*ajax\.php.*/', $currentUrl)) {
			
        	//do nothing
		}
		else {
			
        //	echo '<div id="message" class="updated fade">Published Index Feed:'.$xmlFile;
	//		echo ' <a href="'.get_bloginfo('wpurl').'/wp-files/'.$index_file.'">View it</a>';
        //       echo '</div>';
			
		}	

		$fp = fopen($xmlFile, "w+");
		fwrite($fp, $xmlOutput);
		fclose($fp);
	}
	/******* Private functions **************/
	private function w3cDate($time=NULL) 
	{	 
		if (empty($time)) 
			$time = time(); 
		$offset = date("O",$time); 
		return date("Y-m-d\TH:i:s",$time).substr($offset,0,3).":".substr($offset,-2); 
	} 
	/**
	 * Function prints out navigation menu for options page 
	 **/
	private function print_header() {
		$url = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['PHP_SELF'] .'?page='.$_GET['page'];
		echo '<ul class="subsubsub">';
		echo '<li><a href="'.$url.'&gnf_section=posts">Posts Feed</a> |</li>';
		echo '<li><a href="'.$url.'&gnf_section=pages">Pages Feed</a></li>';
		echo '</ul>';
	}/**
         * Function to return image tag for given post id
        **/
	private function get_post_image($post_id = 0, $index = 1, $echo = 1, $img_size = 'thumbnail', $params = '') {
		global $wpdb;
		$q = "SELECT id,post_title,guid " .
		"FROM $wpdb->posts " .
		"WHERE post_type = 'attachment' " .
		"AND (post_mime_type = 'image/jpeg' OR post_mime_type = 'image/gif' OR post_mime_type = 'image/png')" .
		"AND post_parent = ".$post_id." ORDER BY menu_order ASC";
		$attach = $wpdb->get_results($q);

		if (count($attach) < $index) {
			return false;
		}
		$index--;
		$z= wp_get_attachment_image_src($attach[$index]->id, $img_size);
                // 2012-02-29 jmj	wp3 returns an array rather than a string. get just the string if its an array.
		if (is_array($z)) {$z= $z[0];}
		
		if ($echo) {
			echo $z;
		}
		else {
			return $z;
		}
	}

	private function output_json_pages($xml) {
		$xml = new SimpleXMLElement(html_entity_decode($xml->asXML(), ENT_QUOTES, 'UTF-8'));
		$out = '';
		foreach ($xml->url as $e) {
			$namespaces = $e->getNameSpaces(true);
			$news = $e->children($namespaces['news']);
			$news = $news->news;
			$out .= '<div id="main"><h1>'.end($news->title).'</h1>';
			$out .= '<div class="text-block"><p class="json_content">'.substr($e->content." ", 0, -1).'</p></div></div>';
		}

		//$out = json_encode($out);
		echo $out;
		exit();

	}
	private function output_json_posts($xml) {

		$output = array();
		$xml = new SimpleXMLElement(html_entity_decode($xml->asXML(), ENT_QUOTES, 'UTF-8'));

		$i = 0;
		foreach ($xml->url as $e) {
			$i++;
			$namespaces = $e->getNameSpaces(true);
			$news = $e->children($namespaces['news']);
			$news = $news->news;
			$dmc = $e->children($namespaces['dmc']);
			$dmc = $dmc->dmc;

			$new_story['loc'] = $e->loc;
			$new_story['author'] = end($dmc->author);
			$new_story['locked'] = isset($news->access);
			$new_story['location'] = ucwords("");
			$new_story['content'] = substr($e->content."", 0, -1);
		    ///regex to strip out the caption so it isnt part of the teaser text
			$new_story['snippit'] = preg_replace('#\<p class="wp-caption-text"\>(.+?)\<\/p\>#', "", $new_story['content']);
			$new_story['snippit'] = substr(strip_tags($new_story['snippit']), 0, 200)."...";
		//$new_story['snippit'] = strip_tags($new_story['snippit']);/* ("/<img[^>]+\>/i", " ", $new_story['snippit']);  */
		    //$new_story['p_url_small'] =  substr(end($news->image)." ", 0, -1);
		    //$new_story['p_url_normal'] = end($news->image);
			$new_story['p_url_small'] =  substr(end($dmc->image)." ", 0, -1);
			$new_story['p_url_normal'] = end($dmc->image);
			$new_story['loc'] = end($new_story['loc']);
			$new_story['pub-name'] = end($news->publication->name);
			$new_story['pub-lang'] = end($news->publication->language);
			$new_story['date'] =  end($news->publication_date);
			$new_story['title'] = end($news->title);
			$new_story['keywords'] = explode(",", end($news->keywords));
			$output['stories'][] = $new_story;
		}

		$output = json_encode($output);
		echo $output;
		exit();
	}

	function print_feed() {
		global $wpdb;

		$path = isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : $_SERVER['REQUEST_URI'];
		//parse the url to get variables in url
		////parse_str($p['query']);
		////TO DO: Validate token sent
		///dont do anything unless this is in the URL///
		if($path=='/subscriber_feed' || $path=='/subscriber_feed/' || $path=='/subscriber_feed_pages' || $path=='/subscriber_feed_pages/') {

		dmc_google_news_feed::write_mobile_feed();		#writer is smart enough to only write once an hour
		
		//check url for proper path to display feed
		if($path=='/subscriber_feed' || $path=='/subscriber_feed/') {
			$op = get_option("dmc_google_news_feed");
			$type = 'posts';
		}
		else if ($path=='/subscriber_feed_pages' || $path=='/subscriber_feed_pages/') {
			$op = get_option("dmc_google_news_feed_pages");
			$type = 'pages';
		}
		if(strlen($op['mobile_filename'])>1) {
			//Get path to current feed			
			$mobile_filename = stripslashes($op['mobile_filename']);
			$sitemap_url = $_SERVER['DOCUMENT_ROOT'] . stripslashes($op['sitemap_url']);
			$xmlFile = $sitemap_url.$mobile_filename;
			$xml = simplexml_load_file($xmlFile,'SimpleXMLElement');

			//get list of IDs in order to query for full content
			$idarr = $xml->xpath('.//dmc:id');
			$id_list = implode(',',$idarr);
			
			//get content, and add to xml
			$q = "/* DMC Google News Feed pull */ Select post_content from $wpdb->posts where id IN ($id_list) order by post_date desc";
			$cc = $wpdb->get_col($wpdb->prepare($q)); 
			//iterate through xml, walk backwards as doing unset messes with indexes
			for($x=count($xml)-1; $x >= 0; $x--) {
				$access = $xml->url[$x]->xpath('.//news:access');

				//HACK: if feed set to only be free then pull out subscriber content, else add in full content to story
				if($op['include_stories_mobile']=='free' && $access[0] == "Subscription")
					unset($xml->url[$x]); 
				else{		
					$xml->url[$x]->addChild('content','<![CDATA['.apply_filters('the_content', ($cc[$x])).']]>');
				}
			}
			///if we want json returned instead of xml---USED BY MOBILE APPs
			if(isset($_GET['json']) && $type == 'pages') { 
				$this->output_json_pages($xml);

			}
			else if(isset($_GET['json']) && $type == 'posts') { 
				$this->output_json_posts($xml);
			}
			///output XML
			header ("content-type: text/xml"); 
			echo html_entity_decode($xml->asXML(), ENT_QUOTES, 'UTF-8');
			exit();
		}
	}
}
}
$googlenewsfeed = new dmc_google_news_feed();
if(function_exists('add_action')) //Stop error when directly accessing the PHP file
{
	if(is_admin()) {

		add_action('admin_menu', array($googlenewsfeed, 'google_news_feed_menu'));
		add_action('publish_post', array($googlenewsfeed, 'write_google_news_feed'));
		add_action('save_post', array($googlenewsfeed, 'write_google_news_feed'));
		add_action('delete_post', array($googlenewsfeed, 'write_google_news_feed'));
		add_action('transition_post_status', array($googlenewsfeed, 'write_google_news_feed'),10, 3); //Future scheduled post action fix
		add_action('update_option_dmc_google_news_feed', array($googlenewsfeed, 'update_option'), 10, 2);
		//this feed only is updated from the options page, as pages need to be selected manually
		add_action('update_option_dmc_google_news_feed_pages', array($googlenewsfeed, 'update_option_pages'), 10, 2);
	}
	else {	
		//for dynamic build of feed
		add_action('init', array($googlenewsfeed, 'print_feed'));
	}
}
?>
