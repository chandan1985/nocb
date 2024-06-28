<?php 
/*
Template Name: Blank Template
*/
//wp_head(); 
get_header();
?>
<div class="wrapper full-site okc_blank_wrapper">
<div class="container okcblank_page">
<div id="main-content" class="container fade-in animated3 okc_blank_container">
<?php
while ( have_posts() ) : the_post();
    the_content();
endwhile;
//wp_footer()
?>
</div>
</div>
</div>


<?php get_footer(); ?>