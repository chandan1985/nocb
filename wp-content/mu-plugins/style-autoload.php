<?php
 /*
 * Plugin Name:autoload_css
 * Description: autoloads site specific css
 * Author: Max Schlatter
 * Version: 1.0.0
*/

function site_css(){

	/*get site name*/
	$site = get_site_name();
	/*Get the location of this file*/
	$fd = dirname(__FILE__);

	$filelist = wp_cache_get('autoload_css');

	if(!$filelist){
		
		/*create list of files to store*/
		$filelist = array();
		/*If the site folder exists, add common css to the array*/
		$d = $fd.'/site_css/'.$site ;
		if(file_exists($d)){
		$addfile = plugins_url('site_css/common.css', __FILE__);
		$filelist[] =  array('label'=>'site_nonspecific','file'=>$addfile);
		}
		/*If the site and blog folders exist, add each css file in the folder to the array*/
		$b = get_current_blog_id();
		if(file_exists($d.'/'.$b)){
			$blog_css = scandir($d.'/'.$b );
			foreach ($blog_css as $f){
				if(strpos($f, '.css') !== FALSE){
				$addlabel = rtrim($f, '.css');
				$addfile = plugins_url('site_css/'.$site.'/'.$b.'/'.$f, __FILE__);
				$filelist[] =  array('label'=>$addlabel,'file'=>$addfile);
				}
			}
		}
		if (!empty($_SERVER['HTTPS'])){
            
            foreach ($filelist as $key=>$script) {
                if (isset($filelist[$key]['file']) && stripos($filelist[$key]['file'], 'http://', 0) !== FALSE)
                    $filelist[$key]['file'] = str_replace('http://', 'https://', $filelist[$key]['file']);
            }
        }
		
		wp_cache_set('autoload_css', $filelist);
	}
	load_css($filelist);
}
	

function load_css($filelist){
	foreach ($filelist as $cssfile){
		wp_register_style( $cssfile['label'] , $cssfile['file'] );
		wp_enqueue_style($cssfile['label']);
	}	
}
	
	
function get_site_name(){
	/*Get the site name by domain, including environment parameters*/
	$siteurl = site_url();
	$domain_a = explode('.', $siteurl);
	$dev_a = array('azcr'=>'azcapitolreports','azcap'=>'azcapitoltimes','ans'=>'','btm'=>'bridgetowermedia','cpn'=>'carolinaparalegalnews','mke'=>'dailyreporter','djcor'=>'djcoregon','fnc'=>'finance-coemmerce','azgb'=>'azgreenbook','ibr'=>'idahobusinessreview','okc'=>'journalrecord','jrlr'=>'jrlr','lwusa'=>'lawyersusaonline','libn'=>'libn','malw'=>'masslawyersweekly','marules'=>'massrules','meck'=>'mecktimes','milw'=>'milawyersweekly','mnlaw'=>'minnlawyer','mlmcounties'=>'mlmcounties','mlm'=>'molawyersmedia','nclw'=>'nclawyersweekly','neih'=>'newenglandinhouse','nocb'=>'neworleanscitybusiness','lwnewsletters'=>'lwnewsletters','roch'=>'nydailyrecord','pim'=>'politicsinminnesota','rbj'=>'rbj','rilw'=>'rilawyersweekly','sclw'=>'sclawyersweekly','testbed'=>'testbed','tdr'=>'thedailyrecord','tdc'=>'thedolancompany','valw'=>'valawyersweekly','wlj'=>'wislawjournal','ysr'=>'yellowsheetreport');
	if (strpos($siteurl, 'local') !== FALSE && strpos($siteurl, '.dev') !== FALSE){
		$site='djcoregon';
	}elseif (strpos($siteurl, 'staging') !== FALSE){
		$site = $domain_a[1];
	}elseif (strpos($siteurl, 'dolanmediadev') !== FALSE){
		$site = $dev_a[preg_replace ( '/http\:\/\//' , '' , $domain_a[0] )];
	}else{
		$site = ltrim($domain_a[0], 'http://');
		$site = preg_replace ( '/http\:\/\//' , '' , $domain_a[0] );
		
		if (!empty($_SERVER['HTTPS'])){
            
			$site = ltrim($domain_a[0], 'https://');
			$site = preg_replace ( '/https\:\/\//' , '' , $domain_a[0] );
		}

	
	}

	return $site;

}

if(wp_get_theme() == 'Jarida'){
	add_action( 'wp_head', 'site_css', 5, 0 );
}