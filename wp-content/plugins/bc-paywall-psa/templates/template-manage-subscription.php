<?php
/*
  Template Name: Manage Subscription Page
 */
?>
<?php
wp_register_script('subscribe_template', plugin_dir_url(__DIR__) . 'templates/js/subscribe_template.js', $in_footer = true);
wp_enqueue_script('subscribe_template');
wp_register_style('subscribe_template', plugin_dir_url(__DIR__) . 'templates/css/subscribe_template.css');
wp_enqueue_style('subscribe_template');

$data = get_option('tdc_paywall_data');

if (shortcode_exists('subscribe_source')) {
    $passval = do_shortcode('[subscribe_source]');
}
if (get_field('left_promocode')) {
    $leftpromo = 'promocode=' . get_field('left_promocode');
}
if (get_field('center_promocode')) {
    $centerpromo = 'promocode=' . get_field('center_promocode');
}
if (get_field('right_promocode')) {
    $rightpromo = 'promocode=' . get_field('right_promocode');
}
?>
<?php get_header(); ?>
<style>
#main-nav{
    display:none !important;
}
.content {
    float: none !important;
    width: 60% !important;
    margin: 0 auto !important;
}
.sidebar-narrow-left .content-wrap, .sidebar-narrow-left .content-wrap .sidebar-narrow {
    display: table;
    width: 100%;
    margin: 0 auto;
}
.sidebar-narrow-left .sidebar, .sidebar-narrow-left .content-wrap .content {
    float: none;
}
.sidebar, .sidebar-narrow{
    display: none !important;
}
 
#form_opening {
 border: 1px solid #d9dade;
    padding: 25px 20px 20px;
}
.subpage-title {
    font-size: 28px;
    font-weight: normal;
    line-height: 34px;
    margin: 0 0 20px;
    padding: 0;
}
.paperDate {
	 display: none;
}

.sharedaddy{
	 display: none;
}
</style>
<script type="javascript/text" src=""></script>
<hr style="border: 1px solid #b1b1b1; margin-bottom: 2%;">
<div class="content-wrap">
    <div class="content">
        <div id="form_opening" >
        <?php while (have_posts()) : the_post(); ?> <!--Because the_content() works only inside a WP Loop -->
            <div class="form-content-page" >
			
			<h1 class="subpage-title"><?php the_title(); ?></h1>
			<hr style="border: 1px solid #bbb">
			
            <?php the_content(); ?> <!-- Page Content -->
            </div><!-- .entry-content-page -->
        <?php
            endwhile; 
            wp_reset_query();
        ?>
        </div>

    </div><!-- .post-inner -->

</div><!-- .content -->
<?php get_footer(); ?>
