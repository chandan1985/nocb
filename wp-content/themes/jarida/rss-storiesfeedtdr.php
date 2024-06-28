<?php
/**
 * Template Name: Custom RSS Template - storiesfeedtdr
 */
$postCount=100;
$posts = query_posts('showposts='.$postCount);
$args   =   array(
'post_type'     => 'post',
'meta_query'    => array('relation' => 'AND',array('key' => 'we_own_it', 'value' => 'Yes')),
'compare' => '=',
'paged' => 2,
'posts_per_page'   => 10
);
$the_query = new WP_Query($args);
//wp_reset_postdata();
header('Content-Type:'.feed_content_type('rss-http').'; charset='.get_option('blog_charset'), true);
echo'<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>
<rss version="2.0"
xmlns:content="http://purl.org/rss/1.0/modules/content/"
xmlns:wfw="http://wellformedweb.org/CommentAPI/"
xmlns:dc="http://purl.org/dc/elements/1.1/"
xmlns:atom="http://www.w3.org/2005/Atom"
xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
<?php do_action('rss2_ns'); ?>>
<channel>
<title><?php bloginfo_rss('name'); ?> - Feed</title>
<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
<link><?php bloginfo_rss('url') ?></link>
<description><?php bloginfo_rss('description') ?></description>
<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
<language><?php echo get_option('rss_language'); ?></language>
<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
<?php do_action('rss2_head'); ?>
<?php //while(have_posts()) : the_post(); ?>
<?php if($the_query->have_posts()) : while($the_query->have_posts()) : $the_query->the_post();
//global $post;  
$postID = $post->ID;
global $post;
$postcat = get_the_category( $post->ID );
$skiploop = false;
if ( ! empty( $postcat ) ) {
foreach($postcat as $cat){
$rcat = $cat->name; 
if ( $rcat == "Opinions" || $rcat == "opinions")
{
	$skiploop = true;
	break;
}
} 
} 
if($skiploop)
	continue;
?>
<item>
<title><?php the_title_rss(); ?></title>
<link><?php the_permalink_rss(); ?></link>
<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
<description><![CDATA[<?php echo get_the_excerpt($postID); ?>]]></description>
<?php 
if ( ! empty( $postcat ) ) {
foreach($postcat as $cat){
$rcat = $cat->name; 
if ($rcat == 'Commentary')
{
$rcat = 'Opinions';
}
if ($rcat == 'Featured' || $rcat == 'More News')
{
$rcat = 'News';
}
?>
<category><![CDATA[<?php echo rtrim($rcat);?>]]></category>
<?php } } ?>
<imgurl><?php $feat_image = wp_get_attachment_url( get_post_thumbnail_id($post_id) );echo $feat_image;?></imgurl>
<dc:creator><?php the_author(); ?></dc:creator>
<guid isPermaLink="false"><?php the_guid(); ?></guid>
<?php rss_enclosure(); ?>
<?php do_action('rss2_item'); ?>
</item>
<?php endwhile; ?>
<?php endif; ?>
<?php wp_reset_postdata(); ?>
<?php wp_reset_query(); ?>
</channel>
</rss>