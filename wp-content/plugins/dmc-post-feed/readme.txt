::::: DMC POST FEED Version 0.5 :::::::

Contributors: Dave Buchanan
Link: http://dolanmedia.com
Requires at least: 2.8
Tested up to: 2.8.6
Modifications:
	12/23/2009 - Added basic and custom formatting for ease of use (checkboxes versus text areas)
	12/28/2009 - Added date format options including relative time (1 minute ago, 1 hour ago etc)
	By: Dave Buchanan

::::::Summary:::::::
This is using widget API so only compatible with WP 2.8+. Parts of this widget were copied from the dmc-rss-reader widget. DMC-Post-Feed is a WPMU Widget that can display articles from any subblog and category. Post formatting is open with options for displaying link, title, excerpt, thumbnail, medium or full sized image, comment count, etc. Two flavors are available for formatting posts within the widget so you can have highlighted post(s) that displays more info than other posts. 

:::::Example Custom Post Formatting (and default):::::::::
$thumb_image<li><a href="$post_permalink">$post_title</a> - $post_excerpt</li>

::::Widget Walkthrough::::::::
There is also formatting for the before and after text in this widget. Here is how the widget outputs...

$before_widget (defined by theme)

$before_text (defined per widget) 
	* Can include $title variable which will output $before_title . $title . $after_title 
	* or $simple_title which outputs just $title

Loop through posts
	Display highlighted post formatting (if applicable)
	Display regular post formatting
End Loop through posts

$after_text (defined per widget)

$after_widget (defined by theme)	

:::::Notes::::::::::::::::
This widget is designed for performance. Thus it uses the WP 'posts_fields_request' filter eliminating selecting unused fields. (No more select *'s when using get_posts)
