<!DOCTYPE html>
<html>
    <head>
        <title>Welcome</title>
        <meta charset="utf-8">
		<META NAME="robots" CONTENT="noindex,nofollow">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"></script>
        <!-- <link rel="stylesheet" type="text/css" href="interads.css"> -->
        <?php
		$site_url = network_site_url( '/' );
		
        wp_head();
		
        ?>
		
        <?php
        $options = get_option('interstitial-ad');
        if ($options['enabled']) {
            // Check future activation
            // Ignore if not set in admin; return if set & not yet active
            // Check to see if ad locked to home page only
        if (wp_is_mobile() && $options['adtype'] != 'dfp') {
				$invocation = '<!--/* OpenX Interstitial or Floating DHTML Tag */-->
				<script type=\'text/javascript\'>
				<!--//<![CDATA[				
				var m3_u = "' . $options['server_url'] . '/ajs.php";
				var m3_r = Math.floor(Math.random()*99999999999);
				if (!document.MAX_used) document.MAX_used = \',\';
				document.write ("<scr"+"ipt type=\'text/javascript\' src=\'"+m3_u);
				document.write ("?zoneid=' . $options['mobile_zone_id'] . '");
				document.write (\'&amp;cb=\' + m3_r);
				if (document.MAX_used != \',\') document.write ("&amp;exclude=" + document.MAX_used);
				document.write (document.charset ? \'&amp;charset=\'+document.charset : (document.characterSet ? \'&amp;charset=\'+document.characterSet : \'\'));
				document.write ("&amp;loc=" + escape(window.location));
				if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));
				if (document.context) document.write ("&context=" + escape(document.context));
				if (document.mmm_fo) document.write ("&amp;mmm_fo=1");
				document.write ("\'><\/scr"+"ipt>");
				//]]>-->		
				</script>';				
				}
            else if ($options['adtype'] && $options['adtype'] != 'dfp') {
                $invocation = '<!--/* OpenX Interstitial or Floating DHTML Tag */-->
					<script type=\'text/javascript\'>
					<!--//<![CDATA[
					
					
					var m3_u = "' . $options['server_url'] . '/ajs.php";
					
					
   var m3_r = Math.floor(Math.random()*99999999999);
   if (!document.MAX_used) document.MAX_used = \',\';
   document.write ("<scr"+"ipt type=\'text/javascript\' src=\'"+m3_u);
   document.write ("?zoneid=' . $options['zone_id'] . '");
   document.write (\'&amp;cb=\' + m3_r);
   if (document.MAX_used != \',\') document.write ("&amp;exclude=" + document.MAX_used);
   document.write (document.charset ? \'&amp;charset=\'+document.charset : (document.characterSet ? \'&amp;charset=\'+document.characterSet : \'\'));
   document.write ("&amp;loc=" + escape(window.location));
   if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));
   if (document.context) document.write ("&context=" + escape(document.context));
   if (document.mmm_fo) document.write ("&amp;mmm_fo=1");
   document.write ("\'><\/scr"+"ipt>");
   
								

						

					//]]>-->		
					</script>';
            }
			else
			{
				
				$invocation = '';
				if(strpos($site_url, 'rbj.net') === false){
				$invocation = '<script async="async" src="https://www.googletagservices.com/tag/js/gpt.js"></script>
				<script>
				var googletag = googletag || {};
				googletag.cmd = googletag.cmd || [];
				</script>';
				}
				
				
				if (wp_is_mobile()) {
				$invocation .='<script>
				googletag.cmd.push(function() {
				googletag.defineSlot("' . $options['dfp_server_url'] . '", [[300,250], [300,225]], "div-gpt-ad-9999990808178-0").addService(googletag.pubads());
				googletag.pubads().setTargeting("pos",["WelcomeAd"]);
				googletag.pubads().enableSingleRequest();
				googletag.enableServices();
				});
				</script>
				<div id="div-gpt-ad-9999990808178-0" align="center">
				<script>
				googletag.cmd.push(function() { googletag.display("div-gpt-ad-9999990808178-0"); });
				</script>
				</div>';
				}
				else				
				{
				$invocation .='<script>
				googletag.cmd.push(function() {
				googletag.defineSlot("' . $options['dfp_server_url'] . '", ['.$options['width'].', '. $options['height'].'], "div-gpt-ad-9999990808178-0").addService(googletag.pubads());
				googletag.pubads().setTargeting("pos",["WelcomeAd"]);
				googletag.pubads().enableSingleRequest();
				googletag.enableServices();
				});
				</script>
				<div id="div-gpt-ad-9999990808178-0" align="center">
				<script>
				googletag.cmd.push(function() { googletag.display("div-gpt-ad-9999990808178-0"); });
				</script>
				</div>';				
				}
				
				
			}
        }
        //print_r($options['custom_image']);exit;
        $image_attributes = wp_get_attachment_image_src($options['custom_image'],'large');
        ?>
    </head>
	<?php
	if(strpos($site_url, 'rbj.net')!== false){
		echo '<link rel="stylesheet" type="text/css" href="'.plugin_dir_url( __FILE__ ) . 'css/interads.css">';
		}
	
	?>
    <style>
        body {
           
            background: linear-gradient(rgba(0,0,0,.65),rgba(0,0,0,.65)) 0 0/cover,url(<?php echo (isset($image_attributes[0]) && $image_attributes[0] != '') ? $image_attributes[0] : "wp-content/plugins/asentech-interstitial-ads/images/Finance-commerce_Blurred_BG.jpg?quality=30"; ?>) 0 0/cover;
            overflow: auto;
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            background-attachment: fixed;
        }	
    </style>
    <body>
			<!-- Script for ads blocker -->
			<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
			<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
			<script>
			;(function($){
			if ($.adblockDetector) {
			console.error('AdBlock Detector instance exists already');
			return;
			}

			$.adblockDetector = {
			detect: function() {
			var dfd = new $.Deferred();
			var adsEnabled = false;
			var $dummy = $('<div class="ad-right">&nbsp;</div>').appendTo('body');

			setTimeout(function () {
			if ($dummy.height() > 0) {
			adsEnabled = true;
			}
			$dummy.remove();

			dfd.resolve(adsEnabled);
			}, 100);

			return dfd;
			}
			};
			})(jQuery);
			
			
			</script>
			<script>
			$.adblockDetector.detect().done(function(adsEnabled){
			if (!adsEnabled) {
			var redirecturl = document.location.href = String( document.location.href ).replace( "/welcome-ad/?retUrl=","");
			jQuery(location).attr('href',redirecturl);		
			jQuery("#interadsmain").addClass("adblockcls");
			}
			});
			</script>
        <div class="main-section">
            <div class="container" id="interadsmain">
                <div class="row position">
                    <div class="col-md-12 continue-site">
					
					<a href="javascript:void(0)" onclick="interads_close();" class=""> <span class="desktop_click_msg">Click To Continue To Site </span> <span class="mobile_click_msg" style="display:none">Tap To Continue To The Site </span><span class="fa fa-angle-right"></span></a>
					
					<?php
                            
							// Code count timer for redirection
							
							echo "<script>
							var timeleft = ".$options['redirect_time']."
							var downloadTimer = setInterval(function(){
							if(timeleft <= 0){
							clearInterval(downloadTimer);
							document.getElementById('countdown').innerHTML = '0 Seconds.';
							} else {
							document.getElementById('countdown').innerHTML = timeleft + ' seconds.';
							}
							timeleft -= 1;
							}, 1000);

							</script>";		
							
							if ( $options['redirect_time'] )
                            {
                                $site_url = home_url($_SERVER['REQUEST_URI']);
								$cur_url = explode("/welcome-ad/?retUrl=",$site_url);
								$cur_url_new = explode("/welcome-ad/",$site_url);
								echo "<meta http-equiv='refresh' content=".$options['redirect_time'].";url=".$cur_url[0].'/'.$cur_url[1].">";
								echo "<meta http-equiv='refresh' content=".$options['redirect_time'].";url=".$cur_url[0].'/'.$cur_url[1].">";
								echo "<div id='timer'><div class='redirect_text'>You will be redirected to your destination in </div> <div id='countdown'></div></div>";
                            }
							?>
							
                        
							
					</div>
                </div>
                <br>
                <div class="row middle">
                    <div class="middle-section">
                        <div class="body-left">
							<?php
							$page = get_posts(array('name' => 'welcome-ad', 'post_type' => 'page'));

							if ($page) {
								echo apply_filters('the_content', $page[0]->post_content);
							}
							?>
                        </div>
                    </div>
                     <?php if ($options['valign'] == 'middle') { ?>
                        <div class="ad-right adsbox">
                            <div id="interads-cnt" >
                                <?php echo($invocation); ?>

                            </div>
                        </div>
                     <?php } else { ?>
                        <div class="ad-right hide-ad">
                            <div id="interads-cnt" >
                            </div>
                        </div>
					<?php } ?>
                </div>
                    <?php if ($options['valign'] == 'bottom') { ?>
                    <div class="add-section">
                        <div id="interads-cnt" >
							<?php echo($invocation); ?>
                        </div>
                    </div>
				<?php } else { ?>
                    <div class="add-section hide-ad">
                        <div id="interads-cnt" >


                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

</body>

</html>