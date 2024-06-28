<?
$url = $_SERVER['HTTP_HOST'];
define("STORIES_FEED","http://".$url."/subscriber_feed"); // this value will be replaced by bundler script
define("ABOUT_FEED","http://".$url."/subscriber_feed_pages"); // this value will be replaced by bundler script
define("SOAP_URL","http://subscribe.".$url."/_cp2admin/_cfcs/webservice.cfc?wsdl"); // this value will be replaced by bundler script
?>
