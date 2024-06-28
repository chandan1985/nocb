<?php
/*
  Template Name: Thank You Page St Countian
 */
?>
<?php
wp_register_script('subscribe_template', get_template_directory_uri() . '/js/subscribe_template.js', $in_footer = true);
wp_enqueue_script('subscribe_template');
wp_register_style('subscribe_template', get_template_directory_uri() . '/css/subscribe_template.css');
wp_enqueue_style('subscribe_template');
?>
<?php get_header(); ?>
<style>
.content {
    float: none;
    width: 60%;
    margin: 0 auto;
}
.sidebar-narrow-left .content-wrap, .sidebar-narrow-left .content-wrap .sidebar-narrow {
    display: table;
    width: 100%;
    margin: 0 auto;
}
.sidebar-narrow-left .sidebar, .sidebar-narrow-left .content-wrap .content {
    float: none;
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
<hr style="border: 2px solid #bbb; margin-bottom: 2%; margin-top: -2%;">
<div class="content-wrap">
    <div class="content">
        <div id="form_opening" >
          
            <?php while (have_posts()) : the_post(); ?> <!--Because the_content() works only inside a WP Loop -->
                <div class="form-content-page" >
				<h1 class="subpage-title"><?php the_title(); ?></h1>
				<hr style="border: 1px solid #bbb">
                <?php the_content(); ?>
                </div><!-- .entry-content-page -->

    <?php
        endwhile; //resetting the page loop
        wp_reset_query(); //resetting the page query
        
        if(isset($_POST['e_mail_1'])) {
         global $wpdb;            
        $results = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'options WHERE option_name = "tdc_paywall_data" ', OBJECT );
        $unserialize_array =  unserialize($results['0']->option_value);        
            $user_email = $_POST['e_mail_1'];
            $from_name = $unserialize_array['email_from_name'];
            $from_email = $unserialize_array['email_from'];
            $message = '<div><div><span style="color: #555555; font-family: Verdana, sans-serif; font-size: medium;">Thank you for subscribing to <em>the Saint Louis Countian</em>. We are pleased that you have chosen us to be your trusted provider of legal news and public notice information for Saint Louis County.</span></div><div><span style="color: #555555; font-family: Verdana, sans-serif; font-size: small;">&nbsp;</span></div><div><span style="color: #555555; font-family: Verdana, sans-serif; font-size: medium;">In addition to the subscription you purchased, you also have complete access to the <em>Saint Louis Countian</em> digital public notice database at:</span></div><ul><li><span style="color: #555555; font-family: Calibri, sans-serif; font-size: small;"><span style="font-family: Verdana, sans-serif; font-size: medium;">24/7 digital access at <a href="http://mlmcounties.com">http://mlmcounties.com</a></span></span></li></ul></div><div><div><hr /></div><div><span style="color: purple; font-family: Verdana, sans-serif; font-size: medium;">If you have any questions or problems, please visit our <a href="http://mlmcounties.com/faq/">Frequently Asked Questions</a> page on our website, or contact us at&nbsp;</span><a href="tel:8776159536"><span style="font-family: Verdana, sans-serif; font-size: medium;">877-615-9536</span></a><span style="color: purple; font-family: Verdana, sans-serif; font-size: medium;">&nbsp;or </span><a href="mailto:service@bridgetowermedia.com"><span style="font-family: Verdana, sans-serif; font-size: medium;"><span style="color: purple;">service@bridgetowermedia.com</span></span></a></div></div>';
            $message_headers = "From: \"{$from_name}\" <{$from_email}>\n" . "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";
        // Build message content
           $subject = $unserialize_array['email_sub'];
           wp_mail( $user_email, $subject, $message, $message_headers );
           wp_mail( 'btmpsa@gmail.com', $subject, $message, $message_headers );
		   
		   //ActOn API Call
		   
		    // $url = 'https://www.iproduction.com/custom/apps/acton_api.php';
			// $attrs = array();
			// $attrs['email'] = isset($_REQUEST['e_mail_1']) ? $_REQUEST['e_mail_1'] : '';
			// $attrs['source_type'] = 'PSA';
			// $attrs['source_code'] = 'Subscription';
			// $curlreq = false; 
			
			// if (preg_match('/djcoregon/', $_SERVER['HTTP_HOST'])) {
				// $attrs['special_offers'] = 1;
				// $attrs['site'] = 'djcoregon';
				// $curlreq = true;
			// }

			// if (preg_match('/nydailyrecord/', $_SERVER['HTTP_HOST'])) {
				// $attrs['special_offers'] = 1;
				// $attrs['site'] = 'nydailyrecord';
				// $curlreq = true;
			// }

			// if (preg_match('/thedailyrecord/', $_SERVER['HTTP_HOST'])) {
				// $attrs['Insider'] = 1; 
				// $attrs['special_offers'] = 1;
				// $attrs['site'] = 'thedailyrecord';
				// $curlreq = true;
			// }

			// if (preg_match('/molawyersmedia/', $_SERVER['HTTP_HOST'])) {
				// $attrs['week_in_review'] = 1;
				// $attrs['special_offers'] = 1;
				// $attrs['site'] = 'molawyersmedia';
				// $curlreq = true;
			// }
			
			// if (preg_match('/neworleanscitybusiness/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'neworleanscitybusiness';
				// $attrs['special_offers'] = 1;
				// $attrs['week_in_review'] = 1;
				// $curlreq = true;
			// }
			
			// if (preg_match('/libn/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'libn';
				// $attrs['special_offers'] = 1;
				// $curlreq = true;
			// }
			
			// if (preg_match('/finance-commerce/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'finance-commerce';
				// $attrs['special_offers'] = 1;
				// $attrs['week_in_review'] = 1;
				// $curlreq = true;
			// }
			
			// if (preg_match('/minnlawyer/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'minnlawyer';
				// $attrs['special_offers'] = 1;
				// $curlreq = true;
			// }
			
			// if (preg_match('/politicsinminnesota/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'politicsinminnesota';
				// $attrs['special_offers'] = 1;
				// $curlreq = true;
			// }
			
			// if (preg_match('/masslawyersweekly/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'masslawyersweekly';
				// $attrs['special_offers'] = 1;
				// $curlreq = true;
			// }
			
			// if (preg_match('/rilawyersweekly/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'rilawyersweekly';
				// $attrs['special_offers'] = 1;
				// $curlreq = true;
			// }
			
			// if (preg_match('/newenglandinhouse/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'newenglandinhouse';
				// $attrs['special_offers'] = 1;
				// $curlreq = true;
			// }
			
			// if (preg_match('/azcapitoltimes/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'azcapitoltimes';
				// $attrs['special_offers'] = 1;
				// $curlreq = true;
			// }
			
			// if (preg_match('/yellowsheetreport/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'yellowsheetreport';
				// $attrs['special_offers'] = 1;
				// $curlreq = true;
			// }
			
			// if (preg_match('/milawyersweekly/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'milawyersweekly';
				// $attrs['special_offers'] = 1;
				// $curlreq = true;
			// }
			
			// if (preg_match('/idahobusinessreview/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'idahobusinessreview';
				// $attrs['special_offers'] = 1;
				// $curlreq = true;
			// }
			
			// if (preg_match('/dailyreporter/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'dailyreporter';
				// $attrs['special_offers'] = 1;
				// $curlreq = true;
			// }
			
			// if (preg_match('/wislawjournal/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'wislawjournal';
				// $attrs['special_offers'] = 1;
				// $curlreq = true;
			// }
			
			// if (preg_match('/mecktimes/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'mecktimes';
				// $attrs['special_offers'] = 1;
				// $curlreq = true;
			// }
			
			// if (preg_match('/nclawyersweekly/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'nclawyersweekly';
				// $attrs['special_offers'] = 1;
				// $curlreq = true;
			// }
			
			// if (preg_match('/sclawyersweekly/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'sclawyersweekly';
				// $attrs['special_offers'] = 1;
				// $curlreq = true;
			// }
			
			// if (preg_match('/molawyersmedia/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'molawyersmedia';
				// $attrs['special_offers'] = 1;
				// $attrs['weekly_jobs'] = 1;
				// $attrs['event_awards'] = 1;
				// $curlreq = true;
			// }
			
			// if (preg_match('/mlmcounties/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'mlmcounties';
				// $attrs['special_offers'] = 1;
				// $attrs['weekly_jobs'] = 1;
				// $attrs['event_awards'] = 1;
				// $curlreq = true;
			// }
			
			// if (preg_match('/journalrecord/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'journalrecord';
				// $attrs['special_offers'] = 1;
				// $curlreq = true;
			// }
			
			// if (preg_match('/jrlr/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'jrlr';
				// $attrs['special_offers'] = 1;
				// $curlreq = true;
			// }
			
			// if (preg_match('/valawyersweekly/', $_SERVER['HTTP_HOST'])) {
				// $attrs['site'] = 'valawyersweekly';
				// $attrs['special_offers'] = 1;
				// $curlreq = true;
			// }
			
			// if($curlreq){
				// $ch = curl_init();
				// curl_setopt($ch, CURLOPT_URL, $url);
				// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				// curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Marketo Proxy 1.1.0)");
				// curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
				// curl_setopt($ch, CURLOPT_TIMEOUT, 60);
				// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				// curl_setopt($ch, CURLOPT_POST, 1);
				// curl_setopt($ch, CURLOPT_POSTFIELDS, $attrs);
				// $result = curl_exec($ch);
			// }
			
			$dir = plugin_dir_path( __DIR__ );
			$plugin_dir = ABSPATH . 'wp-content/plugins/BTMActOn/classes/acton_class.php';
			$btmActonapi = new actonClass();

	        $user_email = $_REQUEST['e_mail_1'];
	        if (!empty($_REQUEST['transaction_first_name'])) { $user_fname = $_REQUEST['transaction_first_name']; } else { $user_fname = '' ;}
	        if (!empty($_REQUEST['transaction_last_name']))  { $user_lname = $_REQUEST['transaction_last_name'];  } else { $user_lname = '' ;}

	        $results = get_option("btm_acton_details");
	        $base_url = $results['btm_acton_end_point'];
	        $acton_listid = $results['acton_listid'];
			
			$SourceCodeField = $results['SourceCodeField'];
	        $SourceCode = 'Subscription';
	        $sourceTypeField = $results['sourceTypeField'];
	        $sourceType = 'PSA';
			
	        $get_acton_details = $btmActonapi->check_token();
	        $access_token = $get_acton_details;
	        $new_contact = $btmActonapi->create_new_contact($access_token,$base_url,$acton_listid,$user_fname,$user_lname,$user_email,$SourceCodeField,$SourceCode,$sourceTypeField,$sourceType,$results); 
	        json_encode($new_contact); 
			
			//Acton API Call End Here
			
          }
        ?>    
      </div>
    </div><!-- .post-inner -->

</div><!-- .content -->
<?php get_footer(); ?>