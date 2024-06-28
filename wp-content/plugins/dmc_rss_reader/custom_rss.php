<?php 
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; 

remove_filter( 'the_content', 'wptexturize' );
remove_filter( 'the_excerpt', 'wptexturize' );
remove_filter( 'comment_text', 'wptexturize' );
?>

<rss version="2.0"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:wfw="http://wellformedweb.org/CommentAPI/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:atom="http://www.w3.org/2005/Atom"
    <?php do_action('rss2_ns'); ?>
>

<channel>
    <title><?php bloginfo_rss('name'); wp_title_rss(); ?></title>
    <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
    <link><?php bloginfo_rss('url') ?></link>
    <description><?php bloginfo_rss("description") ?></description>
    <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></pubDate>
    <?php the_generator( 'rss2' ); ?>
    <language><?php echo get_option('rss_language'); ?></language>
    <?php do_action('rss2_head'); ?>
    <?php while( have_posts()) : the_post(); ?>
    <item>
        <title><?php strip_tags(the_title_rss()) ?></title>
        <link><?php the_permalink_rss() ?></link>
        <comments><?php comments_link(); ?></comments>
        <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
        <dc:creator><?php the_author() ?></dc:creator>
        <?php the_category_rss() ?>

        <guid isPermaLink="false"><?php the_guid(); ?></guid>
<?php if (get_option('rss_use_excerpt')) : ?>
        <description><![CDATA[<?php the_excerpt_rss() ?>]]></description>
<?php else : ?>
        <description><![CDATA[<?php the_excerpt_rss() ?>]]></description>
        <content:encoded><![CDATA[<?php the_content();
        global $post;
        if (get_post_meta( $post->ID, 'image', true ))
            echo ' <!-- <img src="' . get_post_meta( $post->ID, 'image', true ) . '" /> -->'; ?>
        ]]></content:encoded>
<?php endif; ?>
        <wfw:commentRss><?php echo get_post_comments_feed_link(); ?></wfw:commentRss>
<?php rss_enclosure(); ?>
    <?php do_action('rss2_item'); ?>
    </item>
    <?php endwhile; ?>
</channel>
</rss>
<?php
add_filter( 'the_content', 'wptexturize' );
add_filter( 'the_excerpt', 'wptexturize' );
add_filter( 'comment_text', 'wptexturize' );
?>