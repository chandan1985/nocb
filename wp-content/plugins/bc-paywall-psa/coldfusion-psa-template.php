<?php 
if(isset($_POST['coldfusion_psa_data']))
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$url = trim($_POST['PSA_url']);

    $postFields = "token=".trim($_POST['token'])."&email=".trim($_POST['email'])."&password=".trim($_POST['password'])."&customer_number=".trim($_POST['customer_number'])."&zip=".trim($_POST['zip'])."&PUB_CODE=".trim($_POST['PUB_CODE']);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields); 
    $result = curl_exec($curl);
    print_r($result);
}
?>