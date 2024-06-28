<?php 

/*
Plugin Name: Asentech JSON-LD for Article
Description: JSON-LD for Article is simply the easiest solution to add valid schema.org microdata as a JSON-LD script to your blog posts or articles.
Version:     0.1.1
Author:      Asentech
Plugin URI: https://www.asentechllc.com/
 */
function page_loop() {
        global $wp_query;
        $loop = 'notfound';
        if ($wp_query->is_page) {
            $loop = is_front_page() ? 'front' : 'page';
        } elseif ($wp_query->is_home) {
            $loop = 'home';
        } elseif ($wp_query->is_single) {
            $loop = ( $wp_query->is_attachment ) ? 'attachment' : 'single';
        } elseif ($wp_query->is_category) {
            $loop = 'category';
        } elseif ($wp_query->is_tag) {
            $loop = 'tag';
        } elseif ($wp_query->is_tax) {
            $loop = 'tax';
        } elseif ($wp_query->is_archive) {
            if ($wp_query->is_day) {
                $loop = 'day';
            } elseif ($wp_query->is_month) {
                $loop = 'month';
            } elseif ($wp_query->is_year) {
                $loop = 'year';
            } elseif ($wp_query->is_author) {
                $loop = 'author';
            } else {
                $loop = 'archive';
            }
        } elseif ($wp_query->is_search) {
            $loop = 'search';
        } elseif ($wp_query->is_404) {
            $loop = 'notfound';
        } else {
            $loop = 'notfound';
        }
        return $loop;
    }
  function add_markup($post_id, $only_name = false){
 
//
// Get the data needed for building the JSON-LD
//

$pagename = page_loop();

if ($pagename == 'single') {

        $id = get_the_ID();
		$articletitle=get_post_field( 'post_title', $id, 'raw' );
		$guestauthor = get_post_meta($id, 'post_guest_author', true);
		$byline = get_post_meta( $id, 'byline', true);
		
		if ($guestauthor != "" && $guestauthor != NULL){
			$byline = $guestauthor;
			$articleauthor=$guestauthor;
		} elseif($byline != "" && $byline != NULL){
			if(is_numeric($byline)) {
				$byline = get_the_author_meta( 'display_name', $byline ); 
				$byline = $byline;
				$articleauthor=$byline;
			} else {
				$byline = $byline;
				$articleauthor=$byline;
			}
		} else{
			$articleauthor=get_the_author();
			$byline = $articleauthor;
		}
		$articlepublished=get_the_date('c');
        $articlepublisher=get_bloginfo('name');
        $articleurl=get_permalink();
        $articlesection=get_the_category();
		$articlemodified=get_the_modified_date('c');
        $articlecommentcount=get_comments_number();
		
		$art_keyword = array();
		foreach($articlesection as $articlekeyword)
		{
			$art_keyword[] = $articlekeyword->name;
		}
		$articlesection_prime = $art_keyword[0];
		//$art_keyword = implode(', ', $art_keyword);
								
        if (has_post_thumbnail()) {
                $thumbnailurl=wp_get_attachment_url(get_post_thumbnail_id());
        }
		$author = array('@type' => 'Person',
                        'name'  => $articleauthor);
		
        $authorByline = array('@type' => 'Person',
                        'name'  => $byline);
		
		$pub   = array ('@type' => 'Organization',
                        'name'  => $articlepublisher);
 
 
        $arr=array(     '@context' => 'http://schema.org',
                        '@type'    => 'NewsArticle',
                        'headline' => $articletitle,
                        'author'   => $author,
						'authorByline' => $authorByline,
                        'datePublished' => $articlepublished,
                        'articleSection' => $articlesection_prime,
                        'url'      => $articleurl,
						'image'  => $thumbnailurl,
                        'publisher' => $pub,
						'thumbnailUrl' => $thumbnailurl,
						'dateCreated' => $articlepublished,
						'dateModified' => $articlemodified,
						'creator' => $author,
						'keywords' => $art_keyword,
						'mainEntityOfPage' => array(
						  '@type' => 'WebPage',
						  '@id' => $articleurl
						),
					);
		//}
         echo '<script type="application/ld+json">'.json_encode($arr,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT).'</script>';
    } //end if single
		
if ($pagename == 'home' || $pagename == 'front') {
        $articletitle="Home";
        $articleauthor=get_the_author();
        $articlepublished=get_the_date('c');
        $articlepublisher=get_bloginfo('name');
        $articleurl= get_site_url();
         
        if (has_post_thumbnail()) {
                $thumbnailurl=wp_get_attachment_url(get_post_thumbnail_id());
        }
 
        $author = array('@type' => 'Person',
                        'name'  => $articleauthor);
 
        $pub   = array ('@type' => 'Organization',
                        'name'  => $articlepublisher);
  
        $arr=array(     '@context' => 'http://schema.org',
                        '@type'    => 'WebPage',
                        'headline' => $articletitle,
                        'url'      => $articleurl,
					);
        echo '<script type="application/ld+json">'.json_encode($arr,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT).'</script>';
    } //end if single

if ($pagename == 'category') {
        $articletitle=get_the_title();
        $articleauthor=get_the_author();
        $articlepublished=get_the_date('c');
        $articlepublisher=get_bloginfo('name');
        $articleurl=get_permalink();
         
        if (has_post_thumbnail()) {
                $thumbnailurl=wp_get_attachment_url(get_post_thumbnail_id());
        }
 
        $author = array('@type' => 'Person',
                        'name'  => $articleauthor);
 
        $pub   = array ('@type' => 'Organization',
                        'name'  => $articlepublisher);
  
        $arr=array(     '@context' => 'http://schema.org',
                        '@type'    => 'category',
                        'headline' => $articletitle,
                        'url'      => $articleurl
					);
        echo '<script type="application/ld+json">'.json_encode($arr,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT).'</script>';
    } //end if category

if ($pagename == 'tag') {
        $articletitle=get_the_title();
        $articleauthor=get_the_author();
        $articlepublished=get_the_date('c');
        $articlepublisher=get_bloginfo('name');
        $articleurl=get_permalink();
        
        if (has_post_thumbnail()) {
                $thumbnailurl=wp_get_attachment_url(get_post_thumbnail_id());
        }
 
        $author = array('@type' => 'Person',
                        'name'  => $articleauthor);
 
        $pub   = array ('@type' => 'Organization',
                        'name'  => $articlepublisher);
  
        $arr=array(     '@context' => 'http://schema.org',
                        '@type'    => 'tag',
                        'headline' => $articletitle,
                        'url'      => $articleurl
					);
        echo '<script type="application/ld+json">'.json_encode($arr,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT).'</script>';
    } //end if tag

if ($pagename == 'tax') {
        $articletitle=get_the_title();
        $articleauthor=get_the_author();
        $articlepublished=get_the_date('c');
        $articlepublisher=get_bloginfo('name');
        $articleurl=get_permalink();
        
        if (has_post_thumbnail()) {
                $thumbnailurl=wp_get_attachment_url(get_post_thumbnail_id());
        }
 
        $author = array('@type' => 'Person',
                        'name'  => $articleauthor);
 
        $pub   = array ('@type' => 'Organization',
                        'name'  => $articlepublisher);
  
        $arr=array(     '@context' => 'http://schema.org',
                        '@type'    => 'tax',
                        'headline' => $articletitle,
                        'url'      => $articleurl
					);
        echo '<script type="application/ld+json">'.json_encode($arr,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT).'</script>';
    } //end if tax

if ($pagename == 'archive') {
        $articletitle=get_the_title();
        $articleauthor=get_the_author();
        $articlepublished=get_the_date('c');
        $articlepublisher=get_bloginfo('name');
        $articleurl=get_permalink();
        
        if (has_post_thumbnail()) {
                $thumbnailurl=wp_get_attachment_url(get_post_thumbnail_id());
        }
 
        $author = array('@type' => 'Person',
                        'name'  => $articleauthor);
 
        $pub   = array ('@type' => 'Organization',
                        'name'  => $articlepublisher);
  
        $arr=array(     '@context' => 'http://schema.org',
                        '@type'    => 'archive',
                        'headline' => $articletitle,
                        'url'      => $articleurl
					);
        echo '<script type="application/ld+json">'.json_encode($arr,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT).'</script>';
    } //end if archive
	
if ($pagename == 'search') {
        $articletitle=get_the_title();
        $articleauthor=get_the_author();
        $articlepublished=get_the_date('c');
        $articlepublisher=get_bloginfo('name');
        $articleurl=get_permalink();
        
        if (has_post_thumbnail()) {
                $thumbnailurl=wp_get_attachment_url(get_post_thumbnail_id());
        }
 
        $author = array('@type' => 'Person',
                        'name'  => $articleauthor);
 
        $pub   = array ('@type' => 'Organization',
                        'name'  => $articlepublisher);
  
        $arr=array(     '@context' => 'http://schema.org',
                        '@type'    => 'search',
                        'headline' => $articletitle,
                        'url'      => $articleurl
					);
        echo '<script type="application/ld+json">'.json_encode($arr,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT).'</script>';
    } //end if search
	
if ($pagename == 'tribe_event') {
        $articletitle=get_the_title();
        $articleauthor=get_the_author();
        $articlepublished=get_the_date('c');
        $articlepublisher=get_bloginfo('name');
        $articleurl=get_permalink();
        
        if (has_post_thumbnail()) {
                $thumbnailurl=wp_get_attachment_url(get_post_thumbnail_id());
        }
 
        $author = array('@type' => 'Person',
                        'name'  => $articleauthor);
 
        $pub   = array ('@type' => 'Organization',
                        'name'  => $articlepublisher);
  
        $arr=array(     '@context' => 'http://schema.org',
                        '@type'    => 'tribe_event',
                        'headline' => $articletitle,
                        'url'      => $articleurl
					);
        echo '<script type="application/ld+json">'.json_encode($arr,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT).'</script>';
    } //end if tribe_event
	
if ($pagename == 'page') {

        $id = get_the_ID();
        $articletitle=get_post_field( 'post_title', $id, 'raw' );
		$guestauthor = get_post_meta($id, 'post_guest_author', true);
		$byline = get_post_meta( $id, 'byline', true);
		
		if ($guestauthor != "" && $guestauthor != NULL){
			$byline = $guestauthor;
			$articleauthor=$guestauthor;
		} elseif($byline != "" && $byline != NULL){
			if(is_numeric($byline)) {
				$byline = get_the_author_meta( 'display_name', $byline ); 
				$byline = $byline;
                $articleauthor=$byline;
			} else {
				$byline = $byline;
				$articleauthor=$byline;
			}
		}else{
			$articleauthor=get_the_author();
			$byline = $articleauthor;
		}

		$articlepublished=get_the_date('c');
        $articlepublisher=get_bloginfo('name');
        $articleurl=get_permalink();
        $articlesection=get_the_category();
		$articlemodified=get_the_modified_date('c');
        $articlecommentcount=get_comments_number();
		
		$art_keyword = array();
		foreach($articlesection as $articlekeyword)
		{
			$art_keyword[] = $articlekeyword->name;
		}
		$articlesection_prime = $art_keyword[0];
		$art_keyword = $art_keyword;//implode(', ', $art_keyword);
								
        if (has_post_thumbnail()) {
                $thumbnailurl=wp_get_attachment_url(get_post_thumbnail_id());
        }
		
		$author = array('@type' => 'Person',
                        'name'  => $articleauthor);
		
        $authorByline = array('@type' => 'Person',
                        'name'  => $byline);
		
		$pub   = array ('@type' => 'Organization',
                        'name'  => $articlepublisher);
 
 
        $arr=array(     '@context' => 'http://schema.org',
                        '@type'    => 'Page',
                        'headline' => $articletitle,
                        'author'   => $author,
						'authorByline' => $authorByline,
                        'datePublished' => $articlepublished,
                        'articleSection' => $articlesection_prime,
                        'url'      => $articleurl,
						'image'  => $thumbnailurl,
                        'publisher' => $pub,
						'thumbnailUrl' => $thumbnailurl,
						'dateCreated' => $articlepublished,
						'dateModified' => $articlemodified,
						'creator' => $author,
						'keywords' => $art_keyword,
						'mainEntityOfPage' => array(
						  '@type' => 'WebPage',
						  '@id' => $articleurl
						),
					);
		//}
         echo '<script type="application/ld+json">'.json_encode($arr,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT).'</script>';
    } //end if page
		
	
} // end function
 
 
add_action ('wp_footer','add_markup');
