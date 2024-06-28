<?php
/**
 * Theme functions related to structure.
 *
 * This file contains structural hook functions.
 *
 * @package Mag_lite
 */
/* ----------------------------------------------------------------------------------- */
# Typography Elements Array
/* ----------------------------------------------------------------------------------- */


if (!function_exists('mag_lite_doctype')) :

    /**
     * Doctype Declaration.
     *
     * @since 1.0.0
     */
    function mag_lite_doctype() {
        ?><!DOCTYPE html> <html <?php language_attributes(); ?>><?php
        }

    endif;

  add_action('mag_lite_action_doctype', 'mag_lite_doctype', 10);
  add_action('mag_lite_action_before', 'mag_lite_page_start');
  
  
  

				if (!function_exists('mag_lite_content_start')) :				
				function mag_lite_content_start() {			
				//$url = 'http://cpbj2.qa.asentechdev1.com/coolest-main-page/';
				
                $url = 'http://10.3.3.67/coolest-main-page/';               			
				$domain = str_replace("staging","www",DOMAIN_CURRENT_SITE);				
				$args = array( 
				  'headers' => array( 
					'Host' => $domain
				  ) 
				);
				$result = wp_remote_get( $url,$args );
				
				$str = $result['body'];
				$str = explode("<!--coolest-main-page->",$str);
				remove_filter( 'the_content', '<p>coolest-main-page</p>' );				
				echo  $str[0];
				}
				endif;
				add_action('mag_lite_action_before_content', 'mag_lite_content_start');



				if (!function_exists('mag_lite_page_end')) :    
				function mag_lite_page_end() {
				//$url = 'http://cpbj2.qa.asentechdev1.com/coolest-main-page/';
				$url = 'http://10.3.3.67/coolest-main-page/';             
				$domain = str_replace("staging","www",DOMAIN_CURRENT_SITE);				
				$args = array( 
				  'headers' => array( 
					'Host' => $domain
				  ) 
				);
				$result = wp_remote_get( $url,$args );
				$str = $result['body'];
				$str = explode("<!--coolest-main-page->",$str);
				echo  $str[1];
				}
				endif;
				add_action('mag_lite_action_after', 'mag_lite_page_end');

				if (!function_exists('mag_lite_content_end')) :    
				function mag_lite_content_end() {
				}
				endif;
				add_action('mag_lite_action_after_content', 'mag_lite_content_end');


				if (!function_exists('mag_lite_header_start')) :
				function mag_lite_header_start() { ?>
				<header id="masthead" class="site-header"><?php}
				endif;
				add_action('mag_lite_action_before_header', 'mag_lite_header_start', 10);


				if (!function_exists('mag_lite_header_end')) :
				function mag_lite_header_end() {
				?></header><!-- header ends here --><?php } 
				endif;
				add_action('mag_lite_action_after_header', 'mag_lite_header_end', 10);

			if (!function_exists('mag_lite_footer_start')) :   
			function mag_lite_footer_start() {
			?><strong><footer id="colophon" class="fade-in animated4"> <!-- footer starting from here --> 
			<?php
			}
			endif;
			add_action('mag_lite_action_before_footer', 'mag_lite_footer_start');


			if (!function_exists('mag_lite_footer_end')) :   
			function mag_lite_footer_end() {
			exit;
			?>	 	</footer><div class="clear"></div>

			</strong><!-- #colophon --><?php
			}
			endif;
			add_action('mag_lite_action_after_footer', 'mag_lite_footer_end');
			
			
			
// Menu section 

			