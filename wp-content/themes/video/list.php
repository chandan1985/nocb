<?php require('../../../wp-blog-header.php'); header("HTTP/1.1 200 OK"); ?>
<ul id="contentlist" class="episode_list">
	<?php if ($_GET['pos']>0) : ?>
	<li id="prev" class="totalfaded">
		<a><b>&laquo; The previous <?php print quonfig(5);?> posts</b></a>
	</li>
	<?php endif; ?>
	<?php $offset = quonfig(5) * $_GET['pos'];
	if ($_GET['tag']) :
		query_posts('tag='.$_GET['tag'].'&showposts='.quonfig(5).'&offset='.$offset); 
	else:
		query_posts('cat='.$_GET['id'].'&showposts='.quonfig(5).'&offset='.$offset); 
	endif;
	if (have_posts()) : while (have_posts()) : the_post();?>
		<li id="Episode<?php the_ID(); ?>" class="totalfaded">
			<a><b><?php the_title(); ?></b>By <?php the_author(); ?> - <?php the_time('F j, Y'); ?> - <?php comments_number('0 Comment','1 Comment','% Comments'); ?></a>
		</li>
	<?php
	endwhile; endif;?>
	<?php $offset = quonfig(5) * ($_GET['pos']+1);
	if ($_GET['tag']) :
		query_posts('tag='.$_GET['tag'].'&showposts=1&offset='.$offset);
	else:
		query_posts('cat='.$_GET['id'].'&showposts=1&offset='.$offset); 
	endif;
	if (have_posts()) : while (have_posts()) : the_post();?>
		<li id="next" class="totalfaded">
			<a><b>The next <?php print quonfig(5);?> posts &raquo;</b></a>
		</li>
	<?php endwhile; endif; ?>
</ul>


