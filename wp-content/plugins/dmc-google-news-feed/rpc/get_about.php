<?php
include 'include.php';
$out = '';
$url = ABOUT_FEED;

$xml = download_page($url);
$xml = new SimpleXMLElement($xml);

foreach ($xml->url as $e) {
    $namespaces = $e->getNameSpaces(true);
    $news = $e->children($namespaces['news']);
    $news = $news->news;
    $out .= '<div id="main"><h1>'.end($news->title).'</h1>';
    $out .= '<div class="text-block"><p class="json_content">'.substr($e->content." ", 0, -1).'</p></div></div>';
}

//$out = json_encode($out);
echo $out;

?>
