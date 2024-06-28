<?php require('../../../wp-blog-header.php'); header("HTTP/1.1 200 OK");?>

<?php global $wpdb, $user_ID, $comment_author,$comment_author_email;
$post_id = $_GET['id'];
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
if ( $user_ID) {
	$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$post_id' AND (comment_approved = '1' OR ( user_id = '$user_ID' AND comment_approved = '0' ) )  ORDER BY comment_date");
} else if ( empty($comment_author) ) {
	$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$post_id' AND comment_approved = '1' ORDER BY comment_date");
} else {
	$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$post_id' AND ( comment_approved = '1' OR ( comment_author = '$comment_author' AND comment_author_email = '$comment_author_email' AND comment_approved = '0' ) ) ORDER BY comment_date");
}		
$post_status = $wpdb->get_var("SELECT comment_status FROM $wpdb->posts WHERE ID = '$post_id'");
$post_pass = $wpdb->get_var("SELECT post_password FROM $wpdb->posts WHERE ID = '$post_id'");			
if ( empty($post_pass) ):
	query_posts("p=".$_GET['id']); 
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

						$commentuser = $comment_author;
						$commentemail = $comment_author_email;
						$commenturl = $comment_author_url;
						?>
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
			<?php endif ?>
			<?php endif ?>