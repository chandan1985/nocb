
<?php 
		////DONT THINK NEED THIS LINE--->add_option('dmc_google_news_feed', '');	
		if (isset($_POST['submit'])) {
			$cats = $_POST['cats'];
			$include_subscriber_stories = $_POST['include_subscriber_stories'];
			$include_stories = $_POST['include_stories'];
			$include_stories_mobile = $_POST['include_stories_mobile'];
			$include_pages = $_POST['include_pages'];
			$include_notowned = $_POST['include_notowned'];
			$hours_back = $_POST['hours_back'];
			$include_thumbnail = $_POST['include_thumbnail'];
			$mobile_display_count = $_POST['mobile_display_count'];
			$mobile_filename = $_POST['mobile_filename'];
			
			//if empty use blog name
                        $sitemap_filename = ($_POST['sitemap_filename'] == "" && strlen(get_bloginfo('name')) > 0) ? sanitize_title(get_bloginfo('name')).'.xml' :  $_POST['sitemap_filename'];
			$sitemap_url = $_POST['sitemap_url'];
			$rss_language = $_POST['rss_language'];
			$delete_feed = isset($_POST['delete_feed']) ? $_POST['delete_feed'] : 0;
			if($delete_feed == 1)
			{
				unlink($sitemap_url.$sitemap_filename);
			}
			else
			{
				$vars = array();
				$catlist = "";
				foreach($cats as $key => $val ) {	
					if(strlen($catlist) > 0){$catlist .= ",";}
					$catlist .= $val;
				} 
				$vars['categories_to_show'] = $catlist;
				///set defaults for what stories to include in feed for main and mobile
				if(strlen($include_stories_mobile)<1)
					$include_stories_mobile = "free";
				$vars['include_stories_mobile'] = $include_stories_mobile;

				if(strlen($include_stories)<1)
					$include_stories = "all";
				$vars['include_stories'] = $include_stories;
				///end feed story defaults

				/* old code DELETE 
				if($include_subscriber_stories != 1)
					$include_subscriber_stories = "0";
				$vars['include_subscriber_stories'] = $include_subscriber_stories;
				*/
				if($include_pages != 1)
					$include_pages = "0";
				$vars['include_pages'] = $include_pages;
				if($include_notowned != 1)
					$include_notowned = "0";
				$vars['include_notowned'] = $include_notowned;
				$vars['include_thumbnail'] = $include_thumbnail;
				$vars['hours_back'] = $hours_back;
				$vars['sitemap_filename'] = $sitemap_filename;
				$vars['sitemap_url'] = $sitemap_url;
				$vars['rss_language'] = $rss_language;
				$vars['mobile_display_count'] = $mobile_display_count;
				$vars['mobile_filename'] = $mobile_filename;
				update_option('dmc_google_news_feed', $vars);
			}
		}
		$op = get_option("dmc_google_news_feed"); 
		$categories_to_show = stripslashes($op['categories_to_show']);
		$include_stories_mobile = stripslashes($op['include_stories_mobile']);
		$include_stories = stripslashes($op['include_stories']);
///delete later old code		$include_subscriber_stories = stripslashes($op['include_subscriber_stories']);
		$include_pages = stripslashes($op['include_pages']);
		$include_notowned = stripslashes($op['include_notowned']);
		$include_thumbnail = stripslashes($op['include_thumbnail']);
		$hours_back = stripslashes($op['hours_back']);
		if($hours_back == "")
			$hours_back = "2";
		$sitemap_filename = stripslashes($op['sitemap_filename']);
		if($sitemap_filename == "" && strlen(get_bloginfo('name')) > 0)
			$sitemap_filename = sanitize_title(get_bloginfo('name')).'.xml'; 
		$sitemap_url = stripslashes($op['sitemap_url']);
		if($sitemap_url == "")
			$sitemap_url = "/wp-files/";
		$rss_language = stripslashes($op['rss_language']);
		if($rss_language == "")
			$rss_language = "en";

		if ($op['mobile_display_count'] == "") { $op['mobile_display_count'] = 20; }
                if ($op['mobile_filename'] == "") { $op['mobile_filename'] = 'mobile_feed.xml'; }
		
		$mobile_display_count = $op['mobile_display_count'];
		$mobile_filename = $op['mobile_filename'];

		?>
<div class="wrap">
<script language="JavaScript">
function toggleCheckboxes()
{
	var chk = document.googlenewsfeed_form.toggle.checked;
	var chks = document.getElementsByName('cats[]');
	if(chk == true)
	{
		for (i = 0; i < chks.length; i++)
		{
			chks[i].checked = true;
		}
	}
	else
	{
		for (i = 0; i < chks.length; i++)
		{
			chks[i].checked = false;
		}
	}
}
</script>
 <h2><?php _e('DMC Google News Feed Settings') ?></h2>
<?php
//print header nav
$obj = new dmc_google_news_feed(); 
$obj->print_header();
?>
<form method="post" name="googlenewsfeed_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<table>
<tr>
	<td valign="top" align="left" style="white-space:nowrap;">
<p>Which categories to include:<br />
<input type="checkbox" id="toggle" name="toggle" value="1" onClick="toggleCheckboxes();" /> Check/Uncheck All<br />
<?php
	$args = array(
		'type' => 'post',
		'child_of' => 0,
		'orderby' => 'name',
		'order'  => 'ASC',
		'hide_empty' => 0,
		'hierarchical' => 1,
		'taxonomy' => 'category',
		'pad_counts' => false
	);
	$categories = get_categories( $args );
	$cat_array = explode(",", $categories_to_show);
	foreach ($categories as $category) {
		$checked_text = "";
		if(in_array($category->cat_ID, $cat_array))
			$checked_text = " checked=\"checked\"";
		echo '<br /><input type="checkbox" name="cats[]" value="'.$category->cat_ID.'"'.$checked_text.' /> '.$category->cat_name;
	}
?>
</p>
	</td>
	<td>&nbsp;</td>
	<td valign="top" align="left" style="white-space:nowrap;">
<p>&nbsp;</p>
<p >Content to include in main feed:<br/>
	<select  name="include_stories">
	<option value="all"<?php if($include_stories == "all"){echo " selected=\"selected\"";} ?>>All stories</option>
	<!-- DISABLED FOR NOW as this would mess up mobile feed which needs to be free only for now, DAVE B
		<option value="sub"<?php if($include_stories == "sub"){echo " selected=\"selected\"";} ?>>Only subscriber stories</option>
	-->
	<option value="free"<?php if($include_stories == "free"){echo " selected=\"selected\"";} ?>>Only free stories</option>
	</select>
</p>
<p>Content to include in mobile feed:<br/>
	<select style="background-color:#eee;"  name="include_stories_mobile">
	<option value="all"<?php if($include_stories_mobile == "all"){echo " selected=\"selected\"";} ?>>All stories</option>
	<option value="sub"<?php if($include_stories_mobile == "sub"){echo " selected=\"selected\"";} ?>>Only subscriber stories</option>
	<option value="free"<?php if($include_stories_mobile == "free"){echo " selected=\"selected\"";} ?>>Only free stories</option>
	</select>
</p>
<p><input type="checkbox" value="1" id="include_pages" name="include_pages"<?php if($include_pages == 1){echo " checked=\"checked\"";}else{echo "";} ?> /> Include page content</p>
<p><input type="checkbox" value="1" id="include_notowned" name="include_notowned"<?php if($include_notowned == 1){echo " checked=\"checked\"";}else{echo "";} ?> /> Include content not owned</p>
<p><input type="checkbox" value="1" id="include_thumbnail" name="include_thumbnail"<?php if($include_thumbnail == 1){echo " checked=\"checked\"";}else{echo "";} ?> /> Include thumbnail image</p>
<p>How many hours back:<br />
	<select name="hours_back">
	<option value="1"<?php if($hours_back == "1"){echo " selected=\"selected\"";} ?>>24</option>
	<option value="2"<?php if($hours_back == "2"){echo " selected=\"selected\"";} ?>>48</option>
	<option value="3"<?php if($hours_back == "3"){echo " selected=\"selected\"";} ?>>72</option>
	<option value="80"<?php if($hours_back == "80"){echo " selected=\"selected\"";} ?>>672</option>
	</select>
</p>
<p>Path to Sitemap & Mobile Feed:<br />
	<input id="sitemap_url" name="sitemap_url" type="text" value="<?php echo $sitemap_url; ?>" />
</p>
<p>Sitemap Filename:<br />
	<input id="sitemap_filename" name="sitemap_filename" type="text" value="<?php echo $sitemap_filename; ?>" />
</p>

<p>Mobile Filename:<br />
        <input id="mobile_filename" name="mobile_filename" type="text" value="<?php echo $mobile_filename; ?>" />
</p>
<p># of Mobile Articles to Feed:<br />
	<select name="mobile_display_count">
        <option value="10"<?php if($mobile_display_count == "10"){echo " selected=\"selected\"";} ?>>10</option>
        <option value="20"<?php if($mobile_display_count == "20"){echo " selected=\"selected\"";} ?>>20</option>
        <option value="30"<?php if($mobile_display_count == "30"){echo " selected=\"selected\"";} ?>>30</option>
        </select>

</p>


<p>Language Code:<br />
	<input id="rss_language" name="rss_language" type="text" style="width:28px;" size="2" maxlength="2" value="<?php echo $rss_language; ?>" />
</p>
<p><input type="submit" name="submit" value="Save" /></p>
	</td>
	<td>&nbsp;</td>
	<td valign="top" align="left">
	<p>&nbsp;</p>
	<p>This plugin creates a valid XML feed for consumption by Google News according to Google's requirements as documented within the <a href="http://www.google.com/support/news_pub/bin/topic.py?topic=11666">Google News documentation</a></p>
	<p>The news feed will automatically update every time a story is added, updated or deleted, as well any time the news feed options are changed.</p>
	<p>To submit this Google News Feed to Google News, follow the instructions <a href="http://www.google.com/support/news_pub/bin/answer.py?hl=en&answer=74289">from Google</a></p>
	<p><b>Deleting the current feed:</b></p>
	<p><input type="checkbox" value="1" id="delete_feed" name="delete_feed" /> Delete the currently stored sitemap.</p>
	</td>
</tr>
</table>
</form>
</div>
