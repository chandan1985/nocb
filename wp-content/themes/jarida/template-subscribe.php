<?php
/*
Template Name: Subscribe Page
*/
?>
<?php
wp_register_script( 'subscribe_template', get_template_directory_uri().'/js/subscribe_template.js', $in_footer = true);
wp_enqueue_script('subscribe_template');
wp_register_style( 'subscribe_template', get_template_directory_uri().'/css/subscribe_template.css');
wp_enqueue_style('subscribe_template');
$data = array();
if(get_field('subscribe_url')){$data['subscribe_url'] = get_field('subscribe_url');}
if($data['subscribe_url'] == "" || $data['subscribe_url'] == null){
    $data = get_option('tdc_paywall_data');
}
if(shortcode_exists('subscribe_source')){
	$passval = do_shortcode('[subscribe_source]');
}
if(get_field('left_promocode')){$leftpromo = 'promocode='.get_field('left_promocode');}
if(get_field('center_promocode')){$centerpromo = 'promocode='.get_field('center_promocode');}
if(get_field('right_promocode')){$rightpromo = 'promocode='.get_field('right_promocode');}
?>
<?php get_header(); ?>
<script type="javascript/text" src=""></script>

<div class="content-wrap">
	<div class="content">
<div id="subscribe_cover">
	<div class="subscribe_top">
		<div class="top_main">
		<span>
		<?php if( get_field('top_main') ): the_field('top_main'); endif; ?>
		</span>
		</br>
		<p class="top_sub">
		<?php if( get_field('top_sub') ): the_field('top_sub'); endif; ?> Have a promocode? <a href="<?php echo($data['subscribe_url']); ?>"><u>Click here</u>.</a>
		</p>
		</div>
	</div>
	<div id="subscribe_three_column">
			<div id="sub_c_1" class="subscribe_c">
				<div class="topbox">
				<a href="<?php echo($data['subscribe_url'].'?'.$leftpromo.$passval); ?>" class="ancr"><div></div></a>
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
						<a href="<?php echo($data['subscribe_url'].'?'.$leftpromo.$passval); ?>">
							<div class="btn_text">
								<span><i class="turnarrow"></i><b class="turnarrowlabel">Subscribe</b></span>
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
					<a href="<?php echo($data['subscribe_url'].'?'.$leftpromo.$passval); ?>">
					<div class="lst_btn">
						<span>Subscribe</span>
					</div>
					</a>
				</div>
			</div>
			<div id="sub_c_2" class="subscribe_c">
				<div class="topbox">
				<a href="<?php echo($data['subscribe_url'].'?'.$centerpromo.$passval); ?>" class="ancr"><div></div></a>
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
						<a href="<?php echo($data['subscribe_url'].'?'.$centerpromo.$passval); ?>">
							<div class="btn_text">
								<span><b>Subscribe</b></span>
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
					<a href="<?php echo($data['subscribe_url'].'?'.$centerpromo.$passval); ?>">
					<div class="lst_btn">
						<span>Subscribe</span>
					</div>
					</a>
				</div>
			</div>
			<div id="sub_c_3" class="subscribe_c">
				<div class="topbox">
				<a href="<?php echo($data['subscribe_url'].'?'.$rightpromo.$passval); ?>" class="ancr"><div></div></a>
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
						<a href="<?php echo($data['subscribe_url'].'?'.$rightpromo.$passval); ?>">
							<div class="btn_text">
								<span><b>Subscribe</b></span>
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
					<a href="<?php echo($data['subscribe_url'].'?'.$rightpromo.$passval); ?>">
					<div class="lst_btn">
						<span>Subscribe</span>
					</div>
					</a>
				</div>
			</div>
	</div>
</div>	
	
	</div><!-- .post-inner -->

</div><!-- .content -->

<?php get_footer(); ?>
