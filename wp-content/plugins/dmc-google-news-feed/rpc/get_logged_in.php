<?
include('include.php');
$soap = new MySOAP();

$pubcode = "azcap";
//$loginUserNameEmail = "brent.mitchell@thedolancompany.com";
//$loginPassword = "testLogin";
$loginUserNameEmail = $_GET['txt1'];
$loginPassword = $_GET['txt2'];
$token = "abcdefg1234567890";
$tokenOverwrite = true;
$res = $soap->wsProcessLogin($pubcode , $loginUserNameEmail , $loginPassword , $token , $tokenOverwrite );

if(strlen($res[0])>4)
	echo $res[0];
else
	echo $res[6]['VALUE'];
?>
