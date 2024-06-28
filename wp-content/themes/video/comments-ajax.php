<?php
require_once('../../../wp-load.php');

global $comment, $comments, $post, $wpdb, $user_ID, $user_identity, $user_email, $user_url;

function fail($s) {
	/*header('HTTP/1.0 500 Internal Server Error');*/
	echo $s;
	exit;
}

$comment_post_ID = $_GET['id'];

$post_status = $wpdb->get_var("SELECT comment_status FROM $wpdb->posts WHERE ID = '$comment_post_ID'");

$req = get_option('require_name_email');

if ( empty($post_status) ) {
	do_action('comment_id_not_found', $comment_post_ID);
	fail('The post you are trying to comment on does not curently exist in the database.');
} 

$comment_author       = trim($_GET['auth']);
$comment_author_email = trim($_GET['mail']);
$comment_author_url   = trim($_GET['url']);
$comment_content      = trim($_GET['content']);

// If the user is logged in
$current_user = wp_get_current_user();

$comment_type = '';

$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'user_ID');

$new_comment_ID = wp_new_comment($commentdata);

$comment = $wpdb->get_row("SELECT * FROM {$wpdb->comments} WHERE comment_ID = " . $new_comment_ID);


setcookie('comment_author_' . COOKIEHASH, $comment->comment_author, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
setcookie('comment_author_email_' . COOKIEHASH, $comment->comment_author_email, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
setcookie('comment_author_url_' . COOKIEHASH, clean_url($comment->comment_author_url), time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);

echo $new_comment_ID;
?>
<c0>
	<?php query_posts("p=".$_GET['id']); 
	while (have_posts()) : the_post(); 
		comments_number('Say Something','1 Comment','% Comments'); ?>
		<c1>
			<b><?php the_title(); ?></b>By <?php the_author(); ?> - <?php the_time('F j, Y'); ?> - <?php comments_number('0 Comment','1 Comment','% Comments'); 
		endwhile; ?>
		<c2>
			<b><?php comment_author_link() ?></b><span class="meta"> on <?php comment_date('F j, Y') ?> at <?php comment_date('G:i') ?>
			<?php if ($comment->comment_approved == '0') : ?>
				<span class="moderation"> - Awaiting moderation!</span>
				<?php endif; ?></span>
				<?php comment_text() ?>
				<c3>
					<div class="comment_author"><img src="<?php bloginfo('stylesheet_directory');?>/images/avatar.jpg" /></div>
					<div class="comment">
						<form id="comment_form" onsubmit="sendata(this);return false;" method="post" >
							<fieldset>
								<label for="comauthor">Your Name</label>
								<div class="field"><input type="text" name="comauthor" id="comauthor" value="<?php echo $comment_author; ?>" class="input" tabindex="1" />
								</div>
								<div class="meta"><span id="author_error" class="error totalfaded">Check Again</span></div>
							</fieldset>
							<fieldset>
								<label for="email">Your Email</label>
								<div class="field"><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" class="input" tabindex="2" /></div>
								<div class="meta"><span id="mail_error" class="error totalfaded">Check Again</span></div>
							</fieldset>
							<fieldset>
								<label for="url">Your Website</label>
								<div class="field"><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" class="input" tabindex="3" /></div>
							</fieldset>
							<fieldset>
								<label for="text">Your Comment</label>
								<div class="field"><textarea name="comment" id="comment" cols="20" rows="10" class="textarea" tabindex="4"></textarea></div>
							</fieldset>
							<input name="submit" type="submit" id="submit" class="button" value="Post Comment" tabindex="5" disabled="disabled" />
							<input type="hidden" name="comment_post_ID" value="<?php echo $comment_post_ID ?>" />
							<input type="hidden" name="namemail_required" value="<?php echo $req; ?>" />
							<?php do_action('comment_form', $comment_post_ID); ?>
						</form>
					</div>
					<div class="clear"></div>