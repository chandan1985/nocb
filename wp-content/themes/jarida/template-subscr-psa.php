<?php
/*
Template Name: Acton Subscribe Page
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
.page-template-template-subscr-psa .wrapper .container{width: 100%;}
.page-template-template-subscr-psa #main-content {margin: 0px auto 0px;}
.page-template-template-subscr-psa footer, 
.page-template-template-subscr-psa .footer-bottom {display: none;}
.page-template-template-subscr-psa .plan-box .body_txt {color: #000 !important;}
.page-template-template-subscr-psa .plan-box .premium-non-refundable {color: #000 !important; line-height: initial;}
.page-template-template-subscr-psa .plan-box .value_cont {max-height: 300px;height: 280px;}
@media screen and (min-width: 74.9375rem){
	.page-template-template-subscr-psa .wrapper .container {
	    max-width: 100% !important;
	}
}
</style>
<div class="content-wrap" style="font-family:'Roboto', 'Arial', sans-serif; color:#ffffff; background: linear-gradient(to bottom left , #000010 10%, #205492 80%);">
	<div class="subscribe_wraper">
		<div class="subscribe_top">
			<div style="padding: 32px 10% 0px 10%; margin: 0px;">
				<span style="font-family:'Roboto', 'Arial', sans-serif; color:#ffffff; font-size: 36px; font-weight: lighter;">
					<?php if( get_field('top_main') ): the_field('top_main'); endif; ?>
				</span>
			</div>
			<div style="padding: 0 10% 0px 10%; margin: 0px;">
				<span style="font-family:'Roboto', 'Arial', sans-serif; color:#ffffff; font-size: 50px; font-weight: bold;">
				<?php if( get_field('top_sub') ): the_field('top_sub'); endif; ?>
				</span>
			</div>		
		</div>

		<div id="subscribe_plans_box" class="subscribe_plans_box" style="padding-left: 10%; padding-top: 3%; padding-bottom: 16px; padding-right: 5px">
			<div class="ifram-wrap" width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #fff; padding-top: 30px; padding-bottom: 30px;">
			
				<?php 
					$bc_config = get_option('tdc_paywall_data');
					if(isset($bc_config['sub_embedded_data'])){
						echo $bc_config['sub_embedded_data'];
					}
				?>
			</div>
			<!-- <table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tbody>
				<tr>
				  <td bgcolor="#FFF" width="35%">
					  <div style="text-align:center; display:block;">
					  	<a href="<?php echo site_url();?>">
						  <img src="<?php echo site_url();?>/wp-content/blogs.dir/1/files/2020/01/OKC.svg" style="padding: 20px; width: 70%;" alt="journal Record Logo">
						</a>
					  </div>
				  </td>
				  <td>
				  </td>
				</tr>
			  </tbody>
			</table> -->
			<!-- <?php 
				$left_subscribe_url = get_post_meta( get_the_ID(), 'left_subscribe_url', true );
				$middle_subscribe_url = get_post_meta( get_the_ID(), 'middle_subscribe_url', true );
				$right_subscribe_url = get_post_meta( get_the_ID(), 'right_subscribe_url', true );
			?>
			<table class="plan-box" width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#fff" style="padding-top: 30px; padding-bottom: 30px;">
				  <tbody>
			  		<tr>
						<td width="33.33%" style="border-right: 1.5px solid #204892; padding: 30px;">
				  			<div class="value_cont" style="margin: 0; line-height:2em; text-align: center;">
								<div style="font-size:30px; line-height: 30px; color: #000; font-family:'Roboto', 'Arial', sans-serif; font-weight:500; text-align: center;">
									<?php if( get_field('left_value') ): the_field('left_value'); endif; ?>
								</div>
								<p style="font-size:45px; line-height: 53px; color: #000; font-family:'Roboto', 'Arial', sans-serif; text-align: center; margin:0;"> <?php if( get_field('left_text_price') ): the_field('left_text_price'); endif; ?></span>
									<span style="font-size:15px; line-height: 18px; font-weight: bold; color: #000; font-family:'Roboto', 'Arial', sans-serif; text-align: center;">&nbsp;/<?php if( get_field('left_option') ): the_field('left_option'); endif; ?></span>
								</p>

								<p class="body_txt" style="font-family:'Roboto', 'Arial', sans-serif; font-style: normal; font-weight: 300; font-size: 14px; line-height: 16px; margin-top: 10px; text-align: center; letter-spacing: 0.07em;">
									<?php if( get_field('left_text_body') ): the_field('left_text_body'); endif; ?>
								</p>
							</div>
							<div class="lst_btn_cont" style="width:100%; display:block;">
								<a href="<?php echo($left_subscribe_url.'?'.$leftpromo.$passval); ?>" style="text-decoration:none;">
									<div style="width:200px; height:57px; color:#fff; background-color:#204892; font-family:'Roboto', 'Arial', sans-serif; font-weight:500; line-height:57px; letter-spacing: 1px; text-align:center; vertical-align:middle; margin:0 auto; border-radius:5px; margin-top: 0px; font-size: 18px;">SUBSCRIBE
									</div>
								</a>
							</div> 
						</td>

						<td width="33.33%" style="border-right: 1.5px solid #204892; padding: 30px;">
				  			<div class="value_cont" style="margin: 0; line-height:2em; text-align: center;">
								<div style="font-size:30px; line-height: 30px; color: #000; font-family:'Roboto', 'Arial', sans-serif; font-weight:500; text-align: center;">
									<?php if( get_field('middle_value') ): the_field('middle_value'); endif; ?>
								</div>
								<p style="font-size:45px; line-height: 53px; color: #000; font-family:'Roboto', 'Arial', sans-serif; text-align: center; margin:0;"> <?php if( get_field('middle_text_price') ): the_field('middle_text_price'); endif; ?></span>
									<span style="font-size:15px; line-height: 18px; font-weight: bold; color: #000; font-family:'Roboto', 'Arial', sans-serif; text-align: center;">&nbsp;/<?php if( get_field('middle_option') ): the_field('middle_option'); endif; ?></span>
								</p>

								<p class="body_txt" style="font-family:'Roboto', 'Arial', sans-serif; font-style: normal; font-weight: 300; font-size: 14px; line-height: 16px; margin-top: 10px; text-align: center; letter-spacing: 0.07em;">
									<?php if( get_field('middle_text_body') ): the_field('middle_text_body'); endif; ?>
								</p>
							</div>	
							<div class="lst_btn_cont" style="width:100%; display:block;">
								<a href="<?php echo($middle_subscribe_url.'?'.$centerpromo.$passval); ?>" style="text-decoration:none;">
									<div style="width:200px; height:57px; color:#fff; background-color:#204892; font-family:'Roboto', 'Arial', sans-serif; font-weight:500; line-height:57px; letter-spacing: 1px; text-align:center; vertical-align:middle; margin:0 auto; border-radius:5px; margin-top: 0px; font-size: 18px;">SUBSCRIBE
									</div>
								</a>
							</div>  
						</td>

						<td width="33.33%" style="padding: 30px;">
				  			<div class="value_cont" style="margin:0; line-height:2em; text-align: center;">
								<div style="font-size:30px; line-height: 30px; color: #000; font-family:'Roboto', 'Arial', sans-serif; font-weight:500; text-align: center;">
									<?php if( get_field('right_value') ): the_field('right_value'); endif; ?>
								</div>
								<p style="font-size:45px; line-height: 53px; color: #000; font-family:'Roboto', 'Arial', sans-serif; text-align: center; margin:0;"> <?php if( get_field('right_text_price') ): the_field('right_text_price'); endif; ?></span>
									<span style="font-size:15px; line-height: 18px; font-weight: bold; color: #000; font-family:'Roboto', 'Arial', sans-serif; text-align: center;">&nbsp;/<?php if( get_field('right_option') ): the_field('right_option'); endif; ?></span>
								</p>


								<p class="body_txt" style="font-family:'Roboto', 'Arial', sans-serif; font-style: normal; font-weight: 300; font-size: 14px; line-height: 16px; margin-top: 10px; text-align: center; letter-spacing: 0.07em;">
									<?php if( get_field('right_text_body') ): the_field('right_text_body'); endif; ?>
								</p>
							</div>	
							<div class="lst_btn_cont" style="width:100%; display:block;">
								<a href="<?php echo($right_subscribe_url.'?'.$rightpromo.$passval); ?>" style="text-decoration:none;">
									<div style="width:200px; height:57px; color:#fff; background-color:#204892; font-family:'Roboto', 'Arial', sans-serif; font-weight:500; line-height:57px; letter-spacing: 1px; text-align:center; vertical-align:middle; margin:0 auto; border-radius:5px; margin-top: 0px; font-size: 18px;">SUBSCRIBE
									</div>
								</a>
							</div>  
						</td>
					</tr>
				</tbody>
			</table> -->
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
	  			<tbody>
					<tr>
						<td></td>
			  			<td width="25%">
				  			<img src="https://bridgetowermedia.com/files/2018/11/BTM-stacked-white-e1577972932931.png" style="padding: 20px; margin-top: 10%; width: 40%; float: right;" alt="BridgeTower Media">
				 		</td>
					</tr>
				</tbody>
			</table>	
		</div>
	</div>	
</div><!-- .content wrap -->
<?php
$key_1_value = get_post_meta( get_the_ID(), 'manage_account_link', true );
if ( ! empty( $key_1_value ) ) { ?>
<script type="text/javascript">jQuery("#menu-item-67215 a").attr("href", "<?php echo $key_1_value;?>")</script>
<?php } ?>
<?php get_footer(); ?>
