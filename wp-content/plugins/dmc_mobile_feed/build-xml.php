<?php

	class SimpleXMLExtended extends SimpleXMLElement {
		public function addCData($cdata_text) {
			$node = dom_import_simplexml($this); 
			$no   = $node->ownerDocument;
			$node->xmlEncoding='UTF-8';
			$node->appendChild($no->createCDATASection($cdata_text)); 
		} 
	}

	function category_where( $where = '' ){
		global $wpdb;
	
		$op = get_option("dmc_mobile_feed_options");
		
		$today = date('Y-m-j H:i:s'); 
		$fromDate = date('Y-m-j', strtotime ('-' . $op['mobile_category_count'] . ' day' . $today)); 
		
		$where .= " AND " . $wpdb->posts . ".post_date >= '" . $fromDate . "'" . " AND " . $wpdb->posts . ".post_date <= '" . $today . "'";
		
		return $where;
	}
	
	function page_where( $where = '' ){
		global $wpdb;
		
		$op = get_option("dmc_mobile_feed_options");
		
		$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $op['about_page'] ) ) . '%\'';
		
		return $where;
	}
	
	function filter_limit( $limit ) {
		$op = get_option("dmc_mobile_feed_options");
		
		return " LIMIT " . $op['mobile_display_count'] . "";
	}
	
	function remove_pagination($content){
		global $post;
		$content = $post->post_content;
		$next = '<!--nextpage-->';
		$replace = '';
		$content = str_ireplace($next, $replace, $content);
		return $content;
	}
	
	function build_feed($type, $catID='') {
		global $wpdb;
        global $blog_id;
		global $match_category;
		
		if($type == 'category'){
			$type = 'post';
		}

		$op = get_option("dmc_mobile_feed_options");

        //Show either Posts or Pages or Both
		$args = array(
			post_type => $type,
			post_status => 'publish',
		);
		
		if ($catID > 0 && $catID != ''){
			$args['cat'] = $catID; 
			$args['posts_per_page'] = -1;
		}
							
		if ((stripslashes($op['include_notowned']) != '1') AND ($type <> 'page')) {
			$args['meta_query'][] = array(
				key => 'we_own_it',
				value => 'yes'
			);
		}
							
		if (stripslashes($op['include_stories_mobile']) == 'sub') {
			$args['meta_query'][] = array(
				key => 'dmcss_security_policy',
				value => 'Subscriber Only'
			);
		}
		else if (stripslashes($op['include_stories_mobile']) == 'free') {
			$args['meta_query'][] = array(
				key => 'dmcss_security_policy',
				value => 'Always Free'
			);
		}
			
		if ($op['content_order'] == 'ASC') {
			$args['order'] = 'ASC';
			$args['orderby'] = 'date';
		}
		else { 
			$args['order'] = 'DESC';
			$args['orderby'] = 'date';
		}
		
		// Fetch options from database
		//$pluginurl = get_option('siteurl') .'/wp-content/plugins/'. basename(dirname(__FILE__));
        $pluginurl =  get_option('siteurl') . plugins_url();
		
		$nameSpace = array(
			'default' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
			'news' => 'http://www.google.com/schemas/sitemap-news/0.9',
			'dmc' => ''.$pluginurl.'/'.dmc_mobile_feed::CUSTOMXSD.''
		);

		$nameSpace = (object) $nameSpace;
				
		$xmlOutput = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?>'.'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9" xmlns:dmc="'.$pluginurl.'/'.dmc_mobile_feed::CUSTOMXSD.'"/>');
		if ($catID > 0 && $catID != ''){
			add_filter( 'posts_where', 'category_where' );
			$feed_query = new WP_Query( $args );
			remove_filter( 'posts_where', 'category_where' );
		}
		elseif ($type == 'page') {			
			add_filter( 'posts_where', 'page_where' );
			$feed_query = new WP_Query( $args );
			remove_filter( 'posts_where', 'page_where' );
		}
		else {	
			add_filter( 'post_limits', 'filter_limit' );
			$feed_query = new WP_Query( $args );
			remove_filter( 'post_limits', 'filter_limit' );
		}
		
		if($feed_query->have_posts()) :
			while($feed_query->have_posts()) : $feed_query->the_post();		
				$URLinfo = $xmlOutput->addChild('url');	
					$URLinfo->addChild('loc', get_permalink());
					$newsInfo = $URLinfo->addChild('news', '', $nameSpace->news);
						$newsPub = $newsInfo->addChild('publication', '', $nameSpace->news);
							$newsPub->addChild('name', get_blog_option($blog_id, 'blogname'), $nameSpace->news);
							$newsPub->addChild('language', $op['rss_language'], $nameSpace->news);
						$subscriber = (get_post_meta(get_the_ID(), 'dmcss_security_policy', true));
						if($subscriber == "Subscriber Only") {
							$newsInfo->addChild('access', 'Subscription', $nameSpace->news);
						}
						$newsInfo->addChild('publication_date', get_the_date($d='Y-m-d H:i:s'), $nameSpace->news); 
						$title = str_replace('&nbsp;', '', strip_tags(get_the_title()));
						$newsInfo->title=$title; //Using assignment operator for title to take care of character encoding for us
						$newsInfo->addChild('keywords', htmlentities(get_category_keywords(get_the_ID())), $nameSpace->news);
					$dolanInfo = $URLinfo->addChild('dmc', '', $nameSpace->dmc);
						$dolanInfo->addChild('id', get_the_ID(), $nameSpace->dmc);
						$dolanInfo->addChild('author', get_the_author(), $nameSpace->dmc);
						if($op['include_thumbnail']==1) {
                            $img = get_first_post_image(get_the_ID(),1,0,'thumbnail',get_the_content());
                            if(strlen($img)>0) {
								$dolanInfo->addChild('image', $img, $nameSpace->dmc);
							}
                        }						
                   	    // mobile thumbnails
						if($op['include_thumbnail']==1) {
							$img = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'mobile-thumbnail');
							if($img[3] != false AND ($img[1] == 180 AND $img[2] == 120)) $dolanInfo->addChild('mobile_thumbnail', $img[0], $nameSpace->dmc);
							$img = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'mobile-article');
							if($img[3] != false AND ($img[1] == 600 AND $img[2] == 400)) $dolanInfo->addChild('mobile_article', $img[0], $nameSpace->dmc);
							$img = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'standard-article');
							if($img[3] != false AND ($img[1] == 1296 AND $img[2] == 864)) $dolanInfo->addChild('standard_article', $img[0], $nameSpace->dmc);
							elseif($img[1] == 1296 AND $img[2] == 864) $dolanInfo->addChild('standard_article', $img[0], $nameSpace->dmc);
							$img = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'standard-featured');
							if($img[3] != false AND ($img[1] == 972 AND $img[2] == 648)) $dolanInfo->addChild('standard_featured', $img[0], $nameSpace->dmc);
							$img = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'standard-thumbnail');
							if($img[3] != false AND ($img[1] == 468 AND $img[2] == 312)) $dolanInfo->addChild('standard_thumbnail', $img[0], $nameSpace->dmc);
						}
						
					add_filter('the_content', 'remove_pagination', 1);
					$content = get_the_content();
					$content = apply_filters('the_content', $content);
					
					preg_match('/<div id="attachment_(.*?)"[^>]*>.*?<\/div>/i', $content, $matches);
					
					$content = preg_replace("/<table>.*<\/table>/msxi", "", $content);
					
					if ($catID > 0 && $catID != ''){
						$content = preg_replace("/<div[^>]*>.*?<\/div>/i", "", $content);
						if (!empty($matches)) {
							$caption = $matches[0];
							$content = $caption . $content;
						}
						$content = preg_replace("/<a[^>]+\><img[^>]+\><\/a>/i", "", $content);
						$content = preg_replace("/<img[^>]+\>/i", "", $content);
						$content = preg_replace('/(<a href="([^"]+)\.jpg")[^>]*><\/a>/i', "", $content);
					}
					
					$cat_featured = (get_post_meta(get_the_ID(), 'iPad_category_featured_story', true));
					if($cat_featured == 'Yes') {
						$content = '<!--##featured##-->' . $content;   
					}
					
					$over_featured = (get_post_meta(get_the_ID(), 'iPad_overall_featured_story', true));
					if($over_featured == 'Yes') {
						$content = '<!--##overallFeatured##-->' . $content;   
					}
					
					$author_name = get_the_author();
					if (($op['ap_author'] != '') AND (strpos($author_name, $op['ap_author']) !== false) AND (stripslashes($op['include_notowned']) != '0')) {
						$content = add_AP_copyright($content);
					}
					
					$contentInfo = $URLinfo->addChild('content');
					$contentInfo->addCData($content);
			endwhile;
		endif;

		return $xmlOutput;
	}
		
	function add_AP_copyright($content){
		global $post;
		$post_id = $post->ID;
		
		$site = get_site_url();
		$siteClean = preg_replace('#^https?://#', '', $site);
		
		$content .= "<br><small><a rel='item-license' href='#APRights' id='APRights'>
						Copyright " .date('Y'). " The Associated Press. All rights reserved. This material may not be published, broadcast, rewritten, or redistributed.
					</a></small>		
					<img src='http://analytics.apnewsregistry.com/analytics/v2/image.svc/AP/RWS/".$siteClean."/MAI/post-".$post_id.">";

		return $content;		
	}
		
	function get_category_keywords($newsID) { 
		$i = 0;
										
		foreach(get_the_category($newsID) as $category) {
			if ($i>0) {
				$catNames.= ", ";
			}
			$catNames .= $category->cat_name;
			$i++;
		}
		
        return $catNames; //Return post category names as keywords
	}
		
	function get_first_post_image($post_id = 0, $index = 1, $echo = 1, $img_size = 'thumbnail',$content, $params = '') {
		$thumbID = get_post_thumbnail_id($post_id);
		$image= wp_get_attachment_image_src($thumbID, $img_size);
		
		if(!empty($image)){
			$z = $image[0];
		}
		// if there is no attachment
		else{
			$first_img = '';
			ob_start();
			ob_end_clean();
			$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches);
			$first_img = $matches [1] [0];

			if(!empty($first_img)){
				$related_thumbnail = $first_img;
			}
			else{
				return false;						
			}
			
			$z = $related_thumbnail;
		}

		if ($echo) {				
			echo $z;
		}
		else {
			return $z;
		}
	}

?>