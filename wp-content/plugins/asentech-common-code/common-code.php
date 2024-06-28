<?php

/*
  Plugin Name: Asentech - Common Code
  Plugin URI: http://asentechllc.com/
  Description: Used to add all custom code in one place
  Version: 1.0.0
  Author: asentech
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * filter to change the gravity form upload path in email
 * @param path_info
 * @param form_id
 * @return string
 */
add_filter( 'gform_upload_path', 'change_upload_path', 10, 2 );
function change_upload_path( $path_info, $form_id ) {

	if( class_exists('Zephr') ){
		$zephr_general_fields = get_option('asentech_zephr_general');
		$domain = $zephr_general_fields['zephr_api_url'];
		$form_email_link = !empty($domain) ? $domain : site_url();
	}else {
		$form_email_link = GRAVITY_FORM_EMAIL_LINK ? GRAVITY_FORM_EMAIL_LINK : site_url();
	}
   $new_path = explode('gravity_forms', $path_info['url']);
   $path_info['url'] = $form_email_link . '/files/gravity_forms' . $new_path[1];
   return $path_info;
}


add_filter( 'gform_pre_send_email', function ( $email, $message_format ) {
  if ( $message_format != 'html' ) {
      return $email;
  }
$email['message'] = str_replace(DOMAIN_CURRENT_SITE,DOMAIN_LIVE_URL,$email['message']);	
if (defined('DOMAIN_Z_URL'))
    $email['message'] = str_replace(DOMAIN_Z_URL,DOMAIN_LIVE_URL,$email['message']);

  return $email;
}, 10, 2 );
