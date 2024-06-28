
<?php 
		////DONT THINK NEED THIS LINE--->add_option('dmc_google_news_feed', '');	
		if (isset($_POST['submit'])) {
			$pages = $_POST['pages'];
			$include_subscriber_stories = $_POST['include_subscriber_stories'];
			$include_stories = $_POST['include_stories'];
			$include_stories_mobile = $_POST['include_stories_mobile'];
			$hours_back = $_POST['hours_back'];
			$include_thumbnail = $_POST['include_thumbnail'];
			
			//if empty use blog name, plus '-pages'
                        $sitemap_filename = ($_POST['sitemap_filename'] == "" && strlen(get_bloginfo('name')) > 0) ? sanitize_title(get_bloginfo('name')).'-pages.xml' :  $_POST['sitemap_filename'];
			$sitemap_url = $_POST['sitemap_url'];
			$rss_language = $_POST['rss_language'];
			$delete_feed = $_POST['delete_feed'];
			if($delete_feed == 1)
			{
				unlink($sitemap_url.$sitemap_filename);
			}
			else
			{
				$vars = array();
				$pagelist = "";
				while (list ($key,$val) = @each ($pages)) {
					if(strlen($pagelist) > 0){$pagelist .= ",";}
					$pagelist .= $val;
				} 
				$vars['pages_to_show'] = $pagelist;
				///set defaults for what stories to include in feed for main and mobile
				if(strlen($include_stories_mobile)<1)
					$include_stories_mobile = "free";
				$vars['include_stories_mobile'] = $include_stories_mobile;

				if(strlen($include_stories)<1)
					$include_stories = "all";
				$vars['include_stories'] = $include_stories;
				///end feed story defaults

				$vars['include_thumbnail'] = $include_thumbnail;
				$vars['sitemap_filename'] = $sitemap_filename;
				$vars['sitemap_url'] = $sitemap_url;
				$vars['rss_language'] = $rss_language;
				update_option('dmc_google_news_feed_pages', $vars);
			}
		}
		$op = get_option("dmc_google_news_feed_pages"); 
		$pages_to_show = stripslashes($op['pages_to_show']);
		$include_stories_mobile = stripslashes($op['include_stories_mobile']);
		$include_stories = stripslashes($op['include_stories']);
		$include_thumbnail = stripslashes($op['include_thumbnail']);
		$sitemap_filename = stripslashes($op['sitemap_filename']);
		if($sitemap_filename == "" && strlen(get_bloginfo('name')) > 0)
			$sitemap_filename = sanitize_title(get_bloginfo('name')).'-pages.xml'; 
		$sitemap_url = stripslashes($op['sitemap_url']);
		if($sitemap_url == "")
			$sitemap_url = "/wp-files/";
		$rss_language = stripslashes($op['rss_language']);
		if($rss_language == "")
			$rss_language = "en";
		?>
<div class="wrap">
<script language="JavaScript">
function toggleCheckboxes()
{
	var chk = document.googlenewsfeed_form.toggle.checked;
	var chks = document.getElementsByName('pages[]');
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
dmc_google_news_feed::print_header();
?>
<form method="post" name="googlenewsfeed_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<table>
<tr>
	<td valign="top" align="left" style="white-space:nowrap;">
<p>Which pages to include:<br />
<input type="checkbox" id="toggle" name="toggle" value="1" onClick="toggleCheckboxes();" /> Check/Uncheck All<br />
<?php
	$args = array(
		'post_status' => 'publish'
	);
	$pages = get_pages( $args );
	$page_array = explode(",", $pages_to_show);
	foreach ($pages as $page) {
		$checked_text = "";
		if(in_array($page->ID, $page_array))
			$checked_text = " checked=\"checked\"";
		echo '<br /><input type="checkbox" name="pages[]" value="'.$page->ID.'"'.$checked_text.' /> '.$page->post_title;
	}
?>
</p>
	</td>
	<td>&nbsp;</td>
	<td valign="top" align="left" style="white-space:nowrap;">
<p>&nbsp;</p>
<p >Content to include in main feed:<br/>
	<select  name="include_stories">
	<option value="all"<?php if($include_stories == "all"){echo " selected=\"selected\"";} ?>>All pages</option>
	<!-- DISABLED FOR NOW as this would mess up mobile feed which needs to be free only for now, DAVE B
		<option value="sub"<?php if($include_stories == "sub"){echo " selected=\"selected\"";} ?>>Only subscriber pages</option>
	-->
	<option value="free"<?php if($include_stories == "free"){echo " selected=\"selected\"";} ?>>Only free pages</option>
	</select>
</p>
<p>Content to include in mobile feed:<br/>
	<select style="background-color:#eee;" disabled="true" name="include_stories_mobile">
	<option value="all"<?php if($include_stories_mobile == "all"){echo " selected=\"selected\"";} ?>>All pages</option>
	<option value="sub"<?php if($include_stories_mobile == "sub"){echo " selected=\"selected\"";} ?>>Only subscriber pages</option>
	<option value="free"<?php if($include_stories_mobile == "free"){echo " selected=\"selected\"";} ?>>Only free pages</option>
	</select>
</p>
<p><input type="checkbox" value="1" id="include_thumbnail" name="include_thumbnail"<?php if($include_thumbnail == 1){echo " checked=\"checked\"";}else{echo "";} ?> /> Include thumbnail image</p>
<p>Sitemap Filename:<br />
	<input id="sitemap_filename" name="sitemap_filename" type="text" value="<?php echo $sitemap_filename; ?>" />
</p>
<p>Path to Sitemap:<br />
	<input id="sitemap_url" name="sitemap_url" type="text" value="<?php echo $sitemap_url; ?>" />
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
