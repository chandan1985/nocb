<?php
include 'include.php';
$output = array();
$url = STORIES_FEED;

$xml = download_page($url);
$xml = new SimpleXMLElement($xml);

$i = 0;
foreach ($xml->url as $e) {
    $i++;
    $namespaces = $e->getNameSpaces(true);
    $news = $e->children($namespaces['news']);
    $news = $news->news;
    $dmc = $e->children($namespaces['dmc']);
    $dmc = $dmc->dmc;

    $new_story['loc'] = $e->loc;
    $new_story['author'] = end($news->publication->name);
    $new_story['locked'] = isset($news->access);
    $new_story['location'] = ucwords("");
    $new_story['content'] = substr($e->content."", 0, -1);
    ///regex to strip out the caption so it isnt part of the teaser text
    $new_story['snippit'] = preg_replace('#\<p class="wp-caption-text"\>(.+?)\<\/p\>#', "", $new_story['content']);
    $new_story['snippit'] = substr(strip_tags($new_story['snippit']), 0, 200)."...";
//$new_story['snippit'] = strip_tags($new_story['snippit']);/* ("/<img[^>]+\>/i", " ", $new_story['snippit']);  */
    //$new_story['p_url_small'] =  substr(end($news->image)." ", 0, -1);
    //$new_story['p_url_normal'] = end($news->image);
    $new_story['p_url_small'] =  substr(end($dmc->image)." ", 0, -1);
    $new_story['p_url_normal'] = end($dmc->image);
    $new_story['loc'] = end($new_story['loc']);
    $new_story['pub-name'] = end($news->publication->name);
    $new_story['pub-lang'] = end($news->publication->language);
    $new_story['date'] =  end($news->publication_date);
    $new_story['title'] = end($news->title);
    $new_story['keywords'] = explode(",", end($news->keywords));
    $output['stories'][] = $new_story;
}

$output = json_encode($output);
echo $output;
?>
