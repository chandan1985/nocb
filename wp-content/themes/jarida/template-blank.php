<?php
/*
Template Name: Blank Template
*/
ob_start();
get_header();
ob_end_clean();
ob_start();
while ( have_posts() ) : the_post();
    echo do_shortcode($post->post_content);
endwhile;
$j_content=ob_get_contents();
ob_end_clean();
ob_start();
get_footer();
ob_end_clean();
echo $j_content;
?>