<?php

$op = '';

if (isset($_POST['submit'])) {
        $op['include_stories_mobile'] = $_POST['include_stories_mobile'];
        $op['include_notowned'] = $_POST['include_notowned'];
        $op['include_thumbnail'] = $_POST['include_thumbnail'];
        $op['mobile_display_count'] = $_POST['mobile_display_count'];
		$op['mobile_category_count'] = $_POST['mobile_category_count'];
	$op['rss_language'] = $_POST['rss_language'];
	$op['about_page'] = $_POST['about_page'];
	$op['ap_author'] = $_POST['ap_author'];
	$op['content_order'] = $_POST['content_order'];
	
	update_option("dmc_mobile_feed_options",$op);
}

$op = get_option("dmc_mobile_feed_options");

$include_stories_mobile = $op['include_stories_mobile'];
$include_notowned = $op['include_notowned'];
$include_thumbnail = $op['include_thumbnail'];
$rss_language = $op['rss_language'];
$about_page = $op['about_page'];
$ap_author = $op['ap_author'];
$mobile_display_count = '';
$mobile_category_count = '';
$content_order = '';
if($rss_language == "") { $rss_language = "en"; }
if (is_numeric($op['mobile_display_count']) && $op['mobile_display_count'] > 0 && $op['mobile_display_count'] < 251) {
	$mobile_display_count = $op['mobile_display_count'];
}
else {
	$mobile_display_count = 20;
}
if (is_numeric($op['mobile_category_count']) && $op['mobile_category_count'] > 0 && $op['mobile_category_count'] < 61) {
	$mobile_category_count = $op['mobile_category_count'];
}
else {
	$mobile_category_count = 14;
}
if ($op['content_order'] == 'ASC') {
	$content_order = 'ASC';
}
else if ($op['content_order'] == 'DESC') {
	$content_order = 'DESC';
}
else {
	$content_order = 'DESC';
}

?>

<hr>

<h2 class=wrap><i>Mobile Phone Feed Options</i></h2>

<form method="post" name="app_feeder_config" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

<p>Content to include in mobile feed:<br/>
        <select style="background-color:#eee;"  name="include_stories_mobile">
        <option value="all"<?php if($include_stories_mobile == "all"){echo " selected=\"selected\"";} ?>>All stories</option>
        <option value="sub"<?php if($include_stories_mobile == "sub"){echo " selected=\"selected\"";} ?>>Only subscriber stories</option>
        <option value="free"<?php if($include_stories_mobile == "free"){echo " selected=\"selected\"";} ?>>Only free stories</option>
        </select>
</p>

<p><input type="checkbox" value="1" id="include_notowned" name="include_notowned"<?php if($include_notowned == 1){echo " checked=\"checked\"";}else{echo "";} ?> /> Include content not owned</p>
<p><input type="checkbox" value="1" id="include_thumbnail" name="include_thumbnail"<?php if($include_thumbnail == 1){echo " checked=\"checked\"";}else{echo "";} ?> /> Include thumbnail image</p>


<p># of Mobile Articles to Feed:<br />
        <input id=mobile_display_count name=mobile_display_count type="text" style="width:40px;" size="3" value="<?php echo $mobile_display_count; ?>" />
</p>

<p>Content ordering:<br />
	<input id=content_order name=content_order type=radio value="DESC" <?php if ($content_order == 'DESC') {echo 'checked=checked';} ?>  > New Content at the top, Old content at the bottom<br>
	<input id=content_order name=content_order type=radio value="ASC" <?php if ($content_order == 'ASC') {echo 'checked=checked';} ?>  > Old Content at the top, New Content at the bottom<br> 
</p>

<p>Language Code:<br />
        <input id="rss_language" name="rss_language" type="text" style="width:28px;" size="2" maxlength="2" value="<?php echo $rss_language; ?>" />
</p>

<p>About Page Title:<br />
        <input id="about_page" name="about_page" type="text" style="width:100px;" size="2" value="<?php echo $about_page; ?>" />
</p>

<p>AP Author Name:<br />
        <input id="ap_author" name="ap_author" type="text" style="width:100px;" size="2" value="<?php echo $ap_author; ?>" />
</p>

<hr>

<h2 class=wrap><i>Category Feed</i></h2>

<p># of Days to Include in Category Feed:<br />
        <input id=mobile_category_count name=mobile_category_count type="text" style="width:40px;" size="3" value="<?php echo $mobile_category_count; ?>" />
</p>

<p><input type="submit" name="submit" value="Save" /></p>

<p>
	<?php $today = date('m/j/Y'); $fromDate = date('m/j/Y', strtotime ('-' . $mobile_category_count . ' day' . $today)); ?>
	<?php echo "Your date range is " . $fromDate . " to " . $today; ?>
</p>

</form>
