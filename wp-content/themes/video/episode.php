<?php require('../../../wp-blog-header.php'); header("HTTP/1.1 200 OK"); ?>
<?php query_posts("p=".$_GET['id']); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<h1><?php the_title(); ?></h1>
	<?php the_content() ?>
<div id="commenting">
	<?php global $wpdb;
	$post_id = $_GET['id'];
	$post_status = $wpdb->get_var("SELECT comment_status FROM $wpdb->posts WHERE ID = '$post_id'");
	if($post_status == 'closed'):
		comments_number('','<h2>Loading...</h2>','<h2>Loading...</h2>');
	else:?>
		<h2>Loading...</h2>
	<?php endif; ?>
	<div id="comment_preview" style="display:none;"></div>
</div>
<?php endwhile; 
endif; 
	echo 'plink:';
	echo get_permalink($_GET['id']);
?>