<?php
/**
 * Template Name: Optin Webhook
 *
 * @package ThemeScaffold
 */
 	
    $json = file_get_contents('php://input');
	$object = json_decode($json, true); 
	
	$email = $object['lead']['email']; 
	$webhookdb = $object['lead_options']['data'];
	
	if(!empty($email)){
		$_REQUEST['action'] = 'create_acton';
		$_REQUEST['user_email'] = $email;
		$_REQUEST['exit_intent'] = $webhookdb;
	
		$obj = new btmActon('');
		$create_acton = $obj->create_acton(); 
		
		/* $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/json_db.txt", "wb"); 
		fwrite($fp,$create_acton);  
		fclose($fp);  */ 
	}
?>