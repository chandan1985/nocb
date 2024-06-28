<?php get_header(); ?>
<?php $withcomments = TRUE;?>
<?php
	// Determine whether or not the user is viewing a category page. If not, don't
	// pre-select a category in the selection list, just show the most recent
	// posts from all categories.
$is_category = (is_category() ? true : false);
$categories = get_the_category();
$category_ID = isset($categories[0]->term_id) ? $categories[0]->term_id : '';
unset( $categories );
?>

<?php	$theid='';
if (!strpos(get_bloginfo('wpurl').'/',$_SERVER["REQUEST_URI"])) :
	if (!empty($_GET['p'])) :
		$theid = $_GET['p'];
	elseif ($tagged!=''):
		query_posts('showposts=1000');
		while (have_posts()) : the_post();
			$posttags = get_the_tags();
			if ($posttags) {
				foreach($posttags as $tag) {
					if ($tag->name == $tagged)
						$theid = $post->ID;
				}
			}
			if ($theid!='')
				break;
		endwhile;
	else:
		query_posts('showposts=1000');
		while (have_posts()) : the_post(); 
			$theid = $post->ID;?>
			<script type="text/javascript">
				var testing1 = '<?php echo 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]?>';
				var testing2 = '<?php echo get_permalink()?>';
			</script>
			<?php 	if (get_permalink()== "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"])
			break;
		endwhile;	
	endif;
else:
	query_posts('showposts=1'); 
	while (have_posts()) : the_post();
		$theid = $post->ID;
	endwhile;
endif;?>
<script type="text/javascript">
	var test1='<?php bloginfo('wpurl') ?>';
	var test2='<?php echo $_SERVER["REQUEST_URI"] ?>';
	var test3='<?php echo isset($_GET['p']) ? $_GET['p'] : ''; ?>';
	var test4='<?php echo $theid ?>';
	var test5='<?php echo $tagged ?>';
	var test6='<?php echo $_SERVER["HTTP_HOST"] ?>';
</script>
<?php query_posts("p=".$theid); 
if (have_posts()) : while (have_posts()) : the_post(); ?>

	<?php $category = get_the_category();
	sanitize_comment_cookies();
	$comment_author = '';
	if ( isset($_COOKIE['comment_author_'.COOKIEHASH]) )
		$comment_author = $_COOKIE['comment_author_'.COOKIEHASH];
	$comment_author_email = '';
	if ( isset($_COOKIE['comment_author_email_'.COOKIEHASH]) )
		$comment_author_email = $_COOKIE['comment_author_email_'.COOKIEHASH];
	$comment_author_url = '';
	if ( isset($_COOKIE['comment_author_url_'.COOKIEHASH]) )
		$comment_author_url = $_COOKIE['comment_author_url_'.COOKIEHASH];
	if ( $user_ID) :
		$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$theid' AND (comment_approved = '1' OR ( user_id = '$user_ID' AND comment_approved = '0' ) )  ORDER BY comment_date");
	elseif ( empty($comment_author) ) :
		$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$theid' AND comment_approved = '1' ORDER BY comment_date");
	else:
		$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$theid' AND ( comment_approved = '1' OR ( comment_author = '$comment_author' AND comment_author_email = '$comment_author_email' AND comment_approved = '0' ) ) ORDER BY comment_date");
	endif;	
	$post_status = $wpdb->get_var("SELECT comment_status FROM $wpdb->posts WHERE ID = '$theid'");
	$post_pass = $wpdb->get_var("SELECT post_password FROM $wpdb->posts WHERE ID = '$theid'");
	$post_status = $post->comment_status;
	$post_pass = $post->post_password;
	if (!function_exists("htmlspecialchars_decode")) {
		function htmlspecialchars_decode($string, $quote_style = ENT_COMPAT) {
			return strtr($string, array_flip(get_html_translation_table(HTML_SPECIALCHARS, $quote_style)));
		}
	}
	$content = htmlspecialchars_decode($post->post_content);

	if (strpos($content,'<cut>')):
		$media = substr($content,0,strpos($content,'<cut>')/1);
		$text = substr($content,strpos($content,'<cut>')/1+5);
	elseif (strpos($content,'</object>')):
		$media = substr($content,0,strpos($content,'</object>')/1+9);
		$text = substr($content,strpos($content,'</object>')/1+9);
	elseif (strpos($content,'</embed>')):
		$media = substr($content,0,strpos($content,'</embed>')/1+8);
		$text = substr($content,strpos($content,'</embed>')/1+8);
	else: 
		$media = '';
		$text = $content;
	endif;

	$insitevideo=0;

	if (strpos($media,'video:')):
		if (file_exists(dirname(__FILE__).'/mediaplayer.swf')): 
			$insitevideo=1;?>
			<script type="text/javascript"> prepareNsiteplayer('<?php echo $media; ?>')</script>
			<?php $media = '<div id="theplayer"></div>';
		else:
			$media='<br /><br />Please check if the "mediaplayer.swf" file exists in the Video theme directory.<br /><br /> For further support please visit the <a href="http://www.quommunication.com/forum/viewforum.php?id=7" title="Quommunication Forum" target="_blank">Quommunication Forum</a>.';
		endif;
	endif;?>
	<div id="stage">
		<div id="mediacontent" class="column_main">
			<h1><?php the_title(); ?></h1>
			<?php if ((strpos($content,'MEDIA=') || strpos($content,'media=') ||strpos($content,'Media=')) && (quonfig(6) == '1')): ?>
			<div id="hidden_content" style="display:none"><?php the_content() ?></div>
			<script type="text/javascript">initpost("<?php the_title()?>")</script>
		<?php else:
			echo $media;
		endif;
		
		if ($insitevideo==1):?>
			<script type="text/javascript">launchNsiteplayer()</script>
		<?php endif;
	endwhile; 
endif; ?>
</div>
<div class="column_right">
	<?php if (quonfig(3) != "1"):?>
		<ul id="menu" class="episode_menu">
			<?php $cats = get_categories('hide_empty=1&hierarchical=0');
			foreach ((array)$cats as $categ) {
				echo '<li id="cat'.$categ->cat_ID.'" '; 
				if (($category_ID==$categ->cat_ID)&&($tagged=='')){ echo 'class="current" ';} 
				echo '><a>'. $categ->cat_name . '</a></li>';
			}?>
			<div class="clear"></div>
		</ul>
		<?php else: ?>
			<form id="drop_menu" name="category_box">
				Select Channel:
				<select name="category" onchange="javascript: pulldownCat(window.document.category_box.category.value)" tabindex="1">
					<?php $cats = get_categories('hide_empty=1&hierarchical=0');
					if ($tagged != '') 
						echo '<option value="" selected="selected"></option>';
					echo '<option value="" ' . ((!$is_category && $tagged == '') ? 'selected="selected"' : '') . '>All Categories</option>';
					foreach ((array)$cats as $categ) {
						echo '<option value="cat'.$categ->cat_ID.'"';
						if ($is_category && ($category_ID==$categ->cat_ID)&&($tagged=='')) echo 'selected';
						echo' >'. $categ->cat_name . '</option>';
					}?>
				</select>
			</form>
		<?php	endif; 
		if ($tagged != '')
			echo '<p id="show_tags">Posts with the tag: '.$tagged.'</p>' ; ?>
		<div style="display:none">
			<?php		if ($tagged == '') :
				query_posts('cat='.$category_ID.'&showposts=1000'); 
				$postnumber =0;	$tpn=0;
				if ( have_posts() ) : while ( have_posts() ) : the_post();
					$postnumber++;
					if ($post->ID == $theid):
						$tpn = $postnumber;
					endif;
					if (($tpn!=0)&&($tpn+4>=$postnumber)): ?>
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				<?php				elseif (($tpn!=0)&&($tpn+4<$postnumber)):
				break;
			endif; 
		endwhile; endif;
		$offset=$tpn-$tpn%quonfig(5);
	else :
		$offset=0;
	endif;?>
</div>
<a  id="linkdown" class="inactive" onclick="javascript: initSlidingContent('list_container','down')"></a>		
<div id="list_container">

	<ul id="contentlist" class="episode_list">
		<?php			if ($offset>0) : ?>	
			<li id="prev" class="totalfaded">
				<a><b>&laquo; The previous <?php print quonfig(5);?> posts</b></a>
			</li>
		<?php			endif; 
		if ($tagged != '') :
			query_posts('tag='.$tagged.'&showposts='.quonfig(5).'&offset='.$offset); 
		elseif ($is_category) :
			query_posts('cat='.$category_ID.'&showposts='.quonfig(5).'&offset='.$offset);
		else :
			query_posts('showposts='.quonfig(5).'&offset='.$offset);
		endif;
		if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<li id="Episode<?php the_ID(); ?>" class="totalfaded <?php if ($post->ID == $theid):?> current <?php endif; ?>" >
				<a><b><?php the_title(); ?></b>By <?php the_author(); ?> - <?php the_time('F j, Y'); ?> - <?php comments_number('0 Comment','1 Comment','% Comments'); ?></a>
			</li>
		<?php			endwhile; endif; 
		$offset = quonfig(5)+$offset;
		if ($tagged != '') :
			query_posts('tag='.$tagged.'&showposts=1&offset='.$offset); 
		else :
					// Only query with the category ID if we're not viewing "All Categories"
			query_posts('cat='.($is_category ? $category_ID : '').'&showposts=1&offset='.$offset); 
		endif;
		if ( have_posts() ) : ?>
			<li id="next" class="totalfaded">
				<a><b>The next <?php print quonfig(5);?> posts &raquo;</b></a>
			</li>
		<?php endif; ?>
	</ul>
</div>
<a id="linkup" class="totalfaded" onclick="javascript: initSlidingContent('list_container','up')"></a>
</div>
<div class="clear"></div>
<script type="text/javascript">initList(<?php echo $theid; ?>,<?php echo ($offset/quonfig(5)-1); ?> ); cat= <?php echo ($is_category ? $category_ID : "''"); ?>
</script>
<div class="stage_footer">
	<div class="column_main"><p>You can <a href="<?php bloginfo('rss2_url'); ?>">subscribe via RSS</a>, <a href="http://www.apple.com/itunes/contentproviders/">iTunes</a> or <a href="http://www.getmiro.com/create/">Miro</a> to the content on this site.</p></div>
	<div class="column_right"><p><a id="linker" href="<?php echo get_permalink($theid); ?>">Click here</a> to get the link of this video in a new page.</p></div>
	<div class="clear"></div>
</div>
</div>
<script type="text/javascript"> var plinker="<?php echo get_permalink($theid)?>";</script>
<div id="content">
	<div id="postcontent" class="column_main">
		<h2>Now Watching</h2><p>
			<?php if (quonfig(4)=="1"): ?>
				<span id="digg_span">
					<script type="text/javascript"> digg_url = plinker </script>
					<script src="http://digg.com/tools/diggthis.js" type="text/javascript"></script>
				</span>
				<script type="text/javascript"> PrepareDigg();</script>
			<?php endif; ?>
			<?php echo $text;?></p>	
			<div id="commenting">
				<?php if ( empty($post_pass) ):

					query_posts("p=".$theid); 
					if (have_posts()) : 
						while (have_posts()) : the_post(); 
							if ($post_status == 'open'):?>
								<h2><?php comments_number('Say Something','1 Comment','% Comments');?><?php if ( get_option('comment_registration')&&(!$user_ID)) : echo ' - you must be logged in to comment'; endif ?></h2>
								<?php		elseif($post_status == 'closed'):?>
									<?php comments_number('','<h2>1 Comment</h2>','<h2>% Comments</h2>');?>
								<?php		endif;		
							endwhile; 
						endif; 
						if ( $comments ) : 
							foreach ($comments as $comment) : ?>
								<div id="comment-<?php comment_ID() ?>" class="comment_author"><img src="http://www.gravatar.com/avatar.php?gravatar_id=<?php echo md5($comment->comment_author_email)?>&s=75&r=any&default=<?php bloginfo('stylesheet_directory');?>/images/avatar.jpg" />
								</div>
								<div class="comment">
									<b><?php comment_author_link() ?></b><span class="meta"> on <?php comment_date('F j, Y') ?> at <?php comment_date('G:i') ?>
									<?php if ($comment->comment_approved == '0') : ?>
										<span class="moderation"> - Awaiting moderation!</span>
										<?php endif; ?></span>
										<?php comment_text() ?>
									</div>
									<div class="clear"></div>
								<?php endforeach; ?>
							<?php endif; 

							global $comment, $comments, $post, $wpdb, $user_ID, $user_identity, $user_email, $user_url, $req;
							if ($post_status == 'open') :?>
								<?php $req = get_option('require_name_email'); ?>

								<div id="comment_preview" style="display:none;">
									<div class="comment_author">
										<img src="<?php bloginfo('stylesheet_directory');?>/images/avatar.jpg" />
									</div>
									<div class="comment">
									</div>
									<div class="clear"></div>
								</div>
								<div id="leave_comment" <?php if ( get_option('comment_registration')&&(!$user_ID)) echo 'style="display:none;"'; else echo 'class="leave_comment"'; ?>>
									<div class="comment_author"><img src="<?php bloginfo('stylesheet_directory');?>/images/avatar.jpg" /></div>
									<div class="comment">
										<?php $current_user = wp_get_current_user();
										if (!$user_identity) : 
											$commentuser = $comment_author;
											$commentemail = $comment_author_email;
											$commenturl = $comment_author_url;
										else : 
											$commentuser = $user_identity;
											$commentemail = $user_email;
											$commenturl = $user_url;
										endif; ?>
										<form id="comment_form" onsubmit="sendata(this);return false;" method="post" >
											<fieldset>
												<label for="comauthor">Your Name</label>
												<div class="field"><input type="text" name="comauthor" id="comauthor" value="<?php echo $commentuser; ?>" class="input" tabindex="1" />	</div>
												<div class="meta"><span id="author_error" class="error totalfaded">Check Again</span></div>
											</fieldset>
											<fieldset>
												<label for="email">Your Email</label>
												<div class="field"><input type="text" name="email" id="email" value="<?php echo $commentemail; ?>" class="input" tabindex="2" /></div>
												<div class="meta"><span id="mail_error" class="error totalfaded">Check Again</span></div>
											</fieldset>
											<fieldset>
												<label for="url">Your Website</label>
												<div class="field"><input type="text" name="url" id="url" value="<?php echo $commenturl; ?>" class="input" tabindex="3" /></div>
											</fieldset>
											<fieldset>
												<label for="text">Your Comment</label>
												<div class="field"><textarea name="comment" id="comment" cols="20" rows="10" class="textarea" tabindex="4"></textarea></div>
											</fieldset>
											<input name="submit" <?php if ( get_option('comment_registration')&&(!$user_ID)) echo 'type="hidden"'; else echo 'type="submit"'; ?> id="submit" class="button" value="Post Comment" tabindex="5" disabled="disabled" />
											<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
											<input type="hidden" name="namemail_required" value="<?php echo $req; ?>" />
											<?php do_action('comment_form', $post->ID); ?>
										</form>
									</div>
									<div class="clear"></div>
								</div>
								<script type="text/javascript"> initLivePreview();
							</script>
						<?php endif ?>
					<?php endif ?>
				</div>
			</div>
			<!--googleoff: all-->
			<?php get_sidebar(); ?>
			<!--googleon: all-->
		</div>
		<?php get_footer(); ?>
