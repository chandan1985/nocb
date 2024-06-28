<?php
/*
Template Name: New Zephr Subscribe Page
*/
?>
<?php
if ( $_GET['source'] == 'PNtop' || $_GET['source'] == 'PNdetail' ) {
global $wpdb;            
$results = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'options WHERE option_name = "tdc_paywall_data" ', OBJECT );
$unserialize_array =  unserialize($results['0']->option_value); 
$public_notice_URL = $unserialize_array['public_notice_URL'];
@setcookie( 'site_referrer', $public_notice_URL , time()+4200 , '/', $_SERVER['HTTP_HOST']);
}
?>
<?php
wp_register_script( 'subscribe_template', get_template_directory_uri().'/js/subscribe_template.js', $in_footer = true);
wp_enqueue_script('subscribe_template');
wp_register_style( 'subscribe_template', get_template_directory_uri().'/css/subscribe_template.css');
wp_enqueue_style('subscribe_template');
$data = get_option('tdc_paywall_data');
if(shortcode_exists('subscribe_source')){
	$passval = do_shortcode('[subscribe_source]');
}
if(get_field('left_promocode')){$leftpromo = 'promocode='.get_field('left_promocode');}
if(get_field('center_promocode')){$centerpromo = 'promocode='.get_field('center_promocode');}
if(get_field('right_promocode')){$rightpromo = 'promocode='.get_field('right_promocode');}
?>
<?php get_header(); ?>
<style>
.wrapper.full-site, .wrapper.layout-2c.full-site { overflow: hidden; }
.content {width: auto;}
.paperDate {display: none;}
</style>
<hr style="border: 2px solid #bbb; margin-bottom: 2%; margin-top: -2%;">
	<div class="content-wrap Zephr_main_div">
	<div class="content Zephr_div">
	<?php while (have_posts()) : the_post(); ?> <!--Because the_content() works only inside a WP Loop -->
	<div id="subscribe_cover Zephr_section">	
	<?php the_content(); ?>	
	</div>

	<?php 
	endwhile; //resetting the page loop
	wp_reset_query();
	?>

	</div><!-- .post-inner -->

	</div><!-- .content -->
<?php
$key_1_value = get_post_meta( get_the_ID(), 'manage_account_link', true );
if ( ! empty( $key_1_value ) ) { ?>
<script type="text/javascript">jQuery("#menu-item-67215 a").attr("href", "<?php echo $key_1_value;?>")</script>
<?php } ?>
<?php get_footer(); ?>
