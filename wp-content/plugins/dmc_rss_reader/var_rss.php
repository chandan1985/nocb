<?php
	
	remove_filter( 'the_content', 'wptexturize' );
	remove_filter( 'the_excerpt', 'wptexturize' );
	remove_filter( 'comment_text', 'wptexturize' );

	$xml = '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; 
	$myrss = new SimpleXMLElement($xml.'<rss></rss>');
	$channel = $myrss->addChild('channel');
	$channel->addChild('title', get_bloginfo_rss('name') . ' ' . get_wp_title_rss());
	$channel->addChild('description' ,  get_bloginfo_rss("description"));
	$channel->addChild('link', get_bloginfo_rss('url'));
	$channel->addChild('pubdate', mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false));
	 
	?>
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<?php 
			$item = $channel->addChild('item');
			$item->addChild('title', get_the_title_rss());
			$item->addChild('link', get_the_guid());
			//$item->addChild('pubdate', mysql2date('D, d M Y H:i:s +0000', $post->post_date, false) );
			$item->addChild('dc:creator', get_the_author() );
			//$item->addChild('description', htmlspecialchars(get_the_excerpt()) );
			
			if (get_post_meta( get_the_id(), 'image', true ))
				$tempcontent =  get_the_content() . ' <!-- <img src="' . get_post_meta( get_the_id(), 'image', true ) . '" /> -->';
			else
				$tempcontent = get_the_content();
			
			$item->addChild('content', htmlspecialchars($tempcontent) );
		?>
	<?php endwhile; else: ?>

	<?php endif; ?>
	
	<?php 
	add_filter( 'the_content', 'wptexturize' );
	add_filter( 'the_excerpt', 'wptexturize' );
	add_filter( 'comment_text', 'wptexturize' );
		
?>