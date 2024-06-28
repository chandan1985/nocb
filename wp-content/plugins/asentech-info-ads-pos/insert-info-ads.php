<?php
/*
  Plugin Name: Asentech - Insert infolink ads
  Description: Hide infolink ads on exclude section.
  Version: 1.0.0
  Author: Asentech
  Plugin URL: http://asentechllc.com/
  Text Domain: infolink ads 
 */

  function enqueue_front_scripts_and_styles_info() {
  	wp_enqueue_script('quick-edit-script', plugin_dir_url(__FILE__) . '/post-ads-after-fifth-script.js', array('jquery'));
  } 
  add_action('wp_enqueue_scripts', 'enqueue_front_scripts_and_styles_info');

  add_action( 'wp_footer', 'list_comment_filters' );
  function list_comment_filters()
  {
  	global $wp_filter;
  	$comment_filters = array ();
  	$ad_code = $value = get_option( 'article_paragraph_ad_code_info', '' );
  	$sp_post_type = get_post_type( get_the_ID() );
  	$page_info_link = get_post_meta( get_the_ID(), 'info_link', true );
  	$result = false;
  	if ( is_single() )
  	{
  		$categories = get_the_category();
  		$output = array();
  		if ( ! empty( $categories ) ) 
  		{
  			foreach( $categories as $category ) 
  			{
  				$output[] .=  esc_html( $category->slug );
  			}		
  			$checked = get_option( 'copyright_message_info' );
  			$checked_url = get_option( 'copyright_message_info_url_exclude' );
  			$checked_url1  = explode(PHP_EOL, $checked_url);
  			$checked1  = explode(PHP_EOL, $checked);
  			$current_url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
  			$curURL = explode('/', $_SERVER['REQUEST_URI']);
  			$var_url =  esc_html($curURL[1]);
            //echo trim($var_url);
  			foreach ($checked1 as $checkednew)
  			{
  				if (trim($checkednew))
  					if(in_array(trim($checkednew), $output)):
  						$result = false;
  						break;
  					else :
  						$result = true;
  					endif;
  				} 

  				/* code for url exclude */
  				$checkedurlnewvar = array();
  				foreach ($checked_url1 as $checkedurlnew)
  				{
  					if (trim($checkedurlnew))
                //echo trim($checkedurlnew);
  						$checkedurlnewvar[] = $checkedurlnew;
  					if(is_array($checkedurlnewvar) && in_array(trim($var_url), $checkedurlnewvar)):
  						$outputresult = 'no';
				//break;
  					else :
  						$outputresult = 'yes';
  					endif;
  				}    		

  			}		
  		}

  		if (get_post_type() === 'post' && $sp_post_type != 'sponsored_content' && $page_info_link != 'no' && $result === true && $outputresult == 'yes') 
  		{		
  			echo "<div class='info_link_footer_div'>".htmlspecialchars_decode( $ad_code)."</div>";	
  		} 

  		if (is_archive()) 
  		{
  			$checked_url_archive = get_option( 'copyright_message_info_url_exclude' );
  			$checked_url1_archive  = explode(PHP_EOL, $checked_url_archive);
  			$checked1  = explode(PHP_EOL, $checked);
  			$current_url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
  			$curURL_archive = explode('/', $_SERVER['REQUEST_URI']);
  			$var_url_archive =  esc_html($curURL_archive[1]);	
  			$checkedurlnewvar_archive = array();
  			foreach ($checked_url1_archive as $checkedurlnew_archive)
  			{
  				if (trim($checkedurlnew_archive))
			//echo trim($checkedurlnew);
  					$checkedurlnewvar_archive[] = $checkedurlnew_archive;
  				if(in_array(trim($var_url_archive), $checkedurlnewvar_archive)):
  					$outputresult_archive = 'no';
			//break;
  				else :
  					$outputresult_archive = 'yes';
  				endif;
  			}

  			if ($outputresult_archive == 'yes') 
  			{		
  				echo "<div class='info_link_footer_div'>".htmlspecialchars_decode( $ad_code)."</div>";	
  			} 
  		}



  		else if (get_post_type() === 'page') 
  		{ 	
  			if($page_info_link == 'no')
  			{
  			}
  			else
  			{
  				echo "<div class='info_link_footer_div'>".htmlspecialchars_decode( $ad_code)."</div>";	
  			}	

  		}



  	}




  	add_filter('admin_init', 'nj_general_intentads_settings_register_fields_info');

  	function nj_general_intentads_settings_register_fields_info()
  	{
  		register_setting('general', 'article_paragraph_ad_code_info', 'esc_attr');
  		add_settings_field('article_paragraph_ad_code_info', '<label for="article_paragraph_ad_code_info">'.__('InfoLink Javascript Code' , 'article_paragraph_ad_code_info' ).'</label>' , 'nj_general_settings_intentads_fields_html_info', 'general');    
  	}

  	function nj_general_settings_intentads_fields_html_info()
  	{
  		$value = get_option( 'article_paragraph_ad_code_info', '' );
  		echo '<textarea id="article_paragraph_ad_code_info" rows="10" cols="80" name="article_paragraph_ad_code_info" value="" />' . $value . ' </textarea>';

  	}


  	/* Added textarea for infolink URL Exclude*/
  	add_filter('admin_init', 'nj_general_intentads_settings_register_fields_info_URL_exclude'); 
  	function nj_general_intentads_settings_register_fields_info_URL_exclude()
  	{
  		register_setting('general', 'copyright_message_info_url_exclude', 'esc_attr');
  		add_settings_field('copyright_message_info_url_exclude', '<label for="copyright_message_info_url_exclude">'.__('Article URLs to exclude InfoLink ads' , 'copyright_message_info_url_exclude' ).'</label><span class="note_content" style="float: left; font-size: 12px; color: red; font-weight: normal;">( Enter the article urls on which the InfoLink ads will be suppressed. Enter only one url per line. )</span>' , 'nj_general_settings_intentads_fields_html_info_url_exclude', 'general');    
  	}

  	function nj_general_settings_intentads_fields_html_info_url_exclude()
  	{
    /*$value = get_option( 'article_paragraph_ad_code_info_url_exclude', '' );
    echo '<textarea id="article_paragraph_ad_code_info_url_exclude" rows="10" cols="80" name="article_paragraph_ad_code_info_url_exclude" value="" />' . $value . ' </textarea>';*/


    $copyright_message_info_url_exclude = get_option( 'copyright_message_info_url_exclude'); 
    echo "<textarea rows='7' cols='100' name='copyright_message_info_url_exclude' id='copyright_message_info_url_exclude'>". $copyright_message_info_url_exclude ."</textarea>"; 
    $user_ids = array();
    $options = get_option( 'copyright_message_info_url_exclude',array(),true);

}


/* Added textarea for infolink */
add_filter('admin_init', 'my_general_settings_register_fields_info'); 
function my_general_settings_register_fields_info() { 
	register_setting('general', 'copyright_message_info', 'esc_attr'); 
	add_settings_field('copyright_message_info', '<label for="copyright_message_info">'.__('Categories to exclude InfoLink ads' , 'copyright_message_info' ).'</label><span class="note_content" style="float: left; font-size: 12px; color: red; font-weight: normal;">( Enter the slugs of the categories on which the InfoLink ads will be suppressed. Enter only one slug per line. )</span>' , 'my_general_copyright_message_info', 'general'); }

	function my_general_copyright_message_info() { 

		$copyright_message_info = get_option( 'copyright_message_info'); 
		echo "<textarea rows='4' cols='50' name='copyright_message_info' id='copyright_message_info'>". $copyright_message_info ."</textarea>"; 
		$user_ids = array();
		$options = get_option( 'copyright_message_info',array(),true);


		$categories = get_categories();
//$copyright_message = $_REQUEST[ 'copyright_message' ];
		$checked = get_option( 'copyright_message_info' );
		$checked1  = explode(PHP_EOL, $checked);
		$categoryar = array();
		foreach($categories as $category) 
		{
			$categoryar[] = $category->slug;
		}
		foreach ($checked1 as $checkednew)
		{
			if (trim($checkednew))
				if(in_array(trim($checkednew), $categoryar)):
					echo "";
				else :
					echo "<br>";
					echo "<div class='category_error'> <span class='category_error_div' style='float: left; font-size: 12px; color: red; font-weight: normal;'> Invalid Category : ".$checkednew."</span></div>";
				endif;
			}
		}
		?>
