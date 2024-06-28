<?php
/*
Template Name: OLD PSA Subscribe Page
*/
?>

<?php
if (isset($_GET['source']) && ($_GET['source'] == 'PNtop' || $_GET['source'] == 'PNdetail' )) {
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
$passval = '';
if(shortcode_exists('subscribe_source')){
	$passval = do_shortcode('[subscribe_source]');
}

$rightpromo = $leftpromo = $centerpromo = '';
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
<div class="content-wrap">
	<div class="content">
<div id="subscribe_cover">
	<div class="subscribe_top">
		<div class="top_main">
		<span>
		<?php if( get_field('top_main') ): the_field('top_main'); endif; ?>
		</span>
		</br>
		</div>
	</div>
	<div id="subscribe_three_column">
			<div id="sub_c_1" class="subscribe_c">
				<div class="topbox">
				<?php $left_subscribe_url = get_post_meta( get_the_ID(), 'left_subscribe_url', true );
					if ( ! empty( $left_subscribe_url ) ) { ?>
					<a href="<?php echo($left_subscribe_url.'?'.$leftpromo.$passval); ?>" class="ancr"><div></div></a>
					<?php } ?>
				
					<div class="value_cont">
						<div class="value_txt">
							<span><?php if( get_field('left_value') ): the_field('left_value'); endif; ?></span>
						</div>
					</div>
					<div class="option_title">
						<span><?php if( get_field('left_option') ): the_field('left_option'); endif; ?></span>
					</div>
					<div class="img_cont">
						<img src="<?php if( get_field('left_value') ): the_field('left_image'); endif; ?>"></img>
					</div>
					<div class="btn_lab_cont">
						<div class="btn_lab">
						<a href="<?php echo($left_subscribe_url.'?'.$leftpromo.$passval); ?>">
							<div class="btn_text">
								<span><i class="turnarrow"></i><b class="turnarrowlabel">Subscribe Now</b></span>
							</div>
						</a>
						</div>
					</div>
					<div class="dets_cont">
						<div id="dets1" class="dets">
							<span class="detstext dets"></span>
						</div>
					</div>
					<div class="mob_txt_sub_cont">
						<div class="mob_txt_sub">
							<span><?php if( get_field('left_mobile_price') ): the_field('left_mobile_price'); endif; ?></span>
						</div>
					</div>
				</div>
				<div class="lower_txt" id="lower_txt_1">
					<div class="txt_title">
						<span><?php if( get_field('left_text_title') ): the_field('left_text_title'); endif; ?></span>
						<span class="txt_title_sub"><?php if( get_field('left_text_price') ): the_field('left_text_price'); endif; ?></span>
					</div>
					<div class="body_txt">
					<p><?php if( get_field('left_text_body') ): the_field('left_text_body'); endif; ?> </p>
					</div>
				</div>
				<div class="lst_btn_cont">
				    <?php $left_subscribe_url = get_post_meta( get_the_ID(), 'left_subscribe_url', true );
					   if ( ! empty( $left_subscribe_url ) ) { ?>
					   <a href="<?php echo($left_subscribe_url.'?'.$leftpromo.$passval); ?>">
					<?php } ?>
					
					<div class="lst_btn">
						<span>Subscribe Now</span>
					</div>
					</a>
				</div>
			</div>
			<div id="sub_c_2" class="subscribe_c">
				<div class="topbox">
				
				<?php $middle_subscribe_url = get_post_meta( get_the_ID(), 'middle_subscribe_url', true );
					if ( ! empty( $middle_subscribe_url ) ) { ?>
					<a href="<?php echo($middle_subscribe_url.'?'.$centerpromo.$passval); ?>" class="ancr"><div></div></a>
					<?php } ?>
					<div class="value_cont">
						<div class="value_txt">
							<span><b><?php if( get_field('middle_value') ): the_field('middle_value'); endif; ?></b></span>
						</div>
					</div>
					<div class="option_title">
						<span><?php if( get_field('middle_option') ): the_field('middle_option'); endif; ?></span>
					</div>
					<div class="img_cont">
						<img src="<?php if( get_field('middle_image') ): the_field('middle_image'); endif; ?>"></img>
					</div>
					<div class="btn_lab_cont">
						<div class="btn_lab">
						<a href="<?php echo($middle_subscribe_url.'?'.$centerpromo.$passval); ?>">
							<div class="btn_text">
								<span><b>Subscribe Now</b></span>
							</div>
						</a>
						</div>
					</div>
					<div class="dets_cont">
						<div id="dets2" class="dets">
							<span class="detstext dets"></span>
						</div>
					</div>
					<div class="mob_txt_sub_cont">
						<div class="mob_txt_sub">
							<span><?php if( get_field('middle_mobile_price') ): the_field('middle_mobile_price'); endif; ?></span>
						</div>
					</div>
				</div>
				<div class="lower_txt" id="lower_txt_2">
					<div class="txt_title">
						<span><?php if( get_field('middle_text_title') ): the_field('middle_text_title'); endif; ?></span>
						<span class="txt_title_sub"><?php if( get_field('middle_text_price') ): the_field('middle_text_price'); endif; ?></span>
					</div>
					<div class="body_txt">
					<p><?php if( get_field('middle_text_body') ): the_field('middle_text_body'); endif; ?></p>
					</div>
				</div>
				<div class="lst_btn_cont">
				<?php $middle_subscribe_url = get_post_meta( get_the_ID(), 'middle_subscribe_url', true );
					if ( ! empty( $middle_subscribe_url ) ) { ?>
						<a href="<?php echo($middle_subscribe_url.'?'.$centerpromo.$passval); ?>">
				<?php } ?>
					
					<div class="lst_btn">
						<span>Subscribe Now</span>
					</div>
					</a>
				</div>
			</div>
			<div id="sub_c_3" class="subscribe_c">
				<div class="topbox">
					<?php $right_subscribe_url = get_post_meta( get_the_ID(), 'right_subscribe_url', true );
					if ( ! empty( $right_subscribe_url ) ) { ?>
					<a href="<?php echo($right_subscribe_url.'?'.$rightpromo.$passval); ?>" class="ancr"><div></div></a>
					<?php } ?>
				
					<div class="value_cont">
						<div class="value_txt">
							<span><?php if( get_field('right_value') ): the_field('right_value'); endif; ?></span>
						</div>
					</div>
					<div class="option_title">
						<span><?php if( get_field('right_option') ): the_field('right_option'); endif; ?></span>
					</div>
					<div class="img_cont">
						<img src="<?php if( get_field('right_image') ): the_field('right_image'); endif; ?>"></img>
					</div>
					<div class="btn_lab_cont">
						<div class="btn_lab">
						<?php $right_subscribe_url = get_post_meta( get_the_ID(), 'right_subscribe_url', true );
							if ( ! empty( $right_subscribe_url ) ) { ?>
							<a href="<?php echo($right_subscribe_url.'?'.$centerpromo.$passval); ?>">
						   <?php } ?>
							<div class="btn_text">
								<span><b>Subscribe Now</b></span>
							</div>
						</a>
						</div>
					</div>
					<div class="dets_cont">
						<div id="dets3" class="dets">
							<span class="detstext dets"></span>
						</div>
					</div>
					<div class="mob_txt_sub_cont">
						<div class="mob_txt_sub">
							<span><?php if( get_field('right_mobile_price') ): the_field('right_mobile_price'); endif; ?></span>
						</div>
					</div>
				</div>
				<div class="lower_txt" id="lower_txt_3">
					<div class="txt_title">
						<span><?php if( get_field('right_text_title') ): the_field('right_text_title'); endif; ?></span>
						<span class="txt_title_sub"><?php if( get_field('right_text_price') ): the_field('right_text_price'); endif; ?></span>
					</div>
					<div class="body_txt">
					<p><?php if( get_field('right_text_body') ): the_field('right_text_body'); endif; ?></p>
					</div>
				</div>
				<div class="lst_btn_cont">
					<a href="<?php echo($right_subscribe_url.'?'.$rightpromo.$passval); ?>">
					<div class="lst_btn">
						<span>Subscribe Now</span>
					</div>
					</a>
				</div>
			</div>
	</div>
</div>	
	
	</div><!-- .post-inner -->

</div><!-- .content -->
<?php
$key_1_value = get_post_meta( get_the_ID(), 'manage_account_link', true );
if ( ! empty( $key_1_value ) ) { ?>
<script type="text/javascript">jQuery("#menu-item-67215 a").attr("href", "<?php echo $key_1_value;?>")</script>
<?php } ?>
<?php get_footer(); ?>
