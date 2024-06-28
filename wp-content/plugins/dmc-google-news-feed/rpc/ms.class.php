<?php

class MySOAP {
var $client = null;
var $soapUrl = SOAP_URL;
var $options = array(); 

/**
 * 
 * Class: MySOAP - Construct Method
 * 
 */

function __construct()
{
$this->client = new SoapClient($this->soapUrl, $this->options);
//Insert Additional Constructor Code
}

/**
 * 
 * Class: MySOAP - Destruct Method
 * 
 */

function __destruct()
{
unset ($this->client);
//Insert Destructor Code
}



function getLogin($webCredentialsID , $rowguid ){
	try {
		$funcRet = $this->client->getLogin($webCredentialsID , $rowguid );
	} catch ( Exception $e ) {
		echo '(getLogin) SOAP Error: - ' . $e->getMessage ();
	}
	return $funcRet; 
}



function wsProcessLogin($pubcode , $loginUserNameEmail , $loginPassword , $token , $tokenOverwrite ){
	try {
		$funcRet = $this->client->wsProcessLogin($pubcode , $loginUserNameEmail , $loginPassword , $token , $tokenOverwrite );
	} catch ( Exception $e ) {
		echo '(wsProcessLogin) SOAP Error: - ' . $e->getMessage ();
	}
	return $funcRet; 
}



function wsDeleteToken($token , $webCredentialsID ){
	try {
		$funcRet = $this->client->wsDeleteToken($token , $webCredentialsID );
	} catch ( Exception $e ) {
		echo '(wsDeleteToken) SOAP Error: - ' . $e->getMessage ();
	}
	return $funcRet; 
}



function wsValidateToken($token , $webCredentialsID , $returnAllValidPubCodes ){
	try {
		$funcRet = $this->client->wsValidateToken($token , $webCredentialsID , $returnAllValidPubCodes );
	} catch ( Exception $e ) {
		echo '(wsValidateToken) SOAP Error: - ' . $e->getMessage ();
	}
	return $funcRet; 
}



function wsInheritToken($pubCode , $token ){
	try {
		$funcRet = $this->client->wsInheritToken($pubCode , $token );
	} catch ( Exception $e ) {
		echo '(wsInheritToken) SOAP Error: - ' . $e->getMessage ();
	}
	return $funcRet; 
}


		
}

?>
