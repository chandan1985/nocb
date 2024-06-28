<?php /* Template Name: Login */ ?>

<?php get_header(); ?>
<?php include 'includes/header-advertisement.php'; ?>
<?php include 'includes/header-navigation.php'; ?>

<?php wp_get_current_user(); /* Changed from get_currentuserinfo */?>
<div class="heading-section container-fluid">
	<div class="container">
		<h1 class="page-heading">My Account</h1>
	</div>
</div>
<div class="container section-container support-page clearfix">
    <section class="section landing quar3 clearfix">
    	<div class="landing-title-container">
        	<?php 
			if(is_page('create-an-account')){
				wp_logout();
				echo "<h1 class='support-title'>Create an Account</h1>";
				echo "<h2 class='support-subtext'>Please use the form below to create a new account.</h2>";
				echo "<p class='support-bodytext'>Creating an account is free and allows you to submit events to our calendar and create business listings. Print subscriptions can be managed from our <a href='/subscribe'>subscription page.</a></p>";
				the_content();
				?>
            	</div>
                <div class="clear"></div>
                <?php
			}
			elseif(is_page('update-account-information')){
				echo "<h1 class='support-title'>Update Account Information</h1>";
				echo "<h2 class='support-subtext'>Please use the form below to update your account information.</h2>";
				the_content();
				?>
            	</div>
                <div class="clear"></div>
                <?php
			}
			elseif(is_page('edit-directory-listing')){
				echo "<h1 class='support-title'>Edit a Buyer's Guide Listing</h1>";
				echo "<h2 class='support-subtext'>Please select an exisiting business listing to edit.</h2>";
				if (is_user_logged_in () == true){
					$activeuser = $current_user->ID;
					$userpostargs = array(
						'author' 	=> $activeuser,
						'orderby' 	=> 'title',
						'order'		=> 'ASC',
						'post_type'	=> 'directorylisting',
					);
					query_posts($userpostargs);
					if ( have_posts() ) : while ( have_posts()) : the_post();
						$listingtype = get_post_meta( get_the_ID(), 'dir_media_type', true );
						$listingid = get_the_ID();
						$listingname = get_the_title();
						echo '<a class="listing-edit" href="/edit-directory-listing-form?lid='.$listingid.'&ltype='.$listingtype.'">'.$listingname.'</a><br>';
					endwhile;
					endif;
					wp_reset_query();
				}
				else{
					echo "<p>You are not currently logged in.</p>";
				}
				the_content();
				?>
                <a href="/my-account">Return to My Account page.</a>
            	</div>
                <div class="clear"></div>
                <?php
			}
			elseif(is_page('edit-directory-listing-form')){
				$activeid = filter_input(INPUT_GET,"lid",FILTER_SANITIZE_STRING);
				$activetype = filter_input(INPUT_GET,"ltype",FILTER_SANITIZE_STRING);
				
				$checktype = get_post_meta( $activeid, 'dir_media_type', true );
				
				if ($activetype == "3-featured"){ $fintype = 7; }
				elseif ($activetype == "2-premium"){ $fintype = 8; }
				else{$fintype = 13; }
				
				echo "<h1 class='support-title'>Edit a Business Directory Listing</h1>";
				echo "<h2 class='support-subtext'>Please use the form below to edit your listing.<span style='display:none'>activeid - " . $activeid . ", ct - " . $checktype . ", at - " . $activetype . ", fintype - " . $fintype . "</span></h2>";
				$pmeta = get_post_meta($activeid);
				echo '<span style="display:none">';
				var_dump($pmeta);
				echo '</span>';

				if (is_user_logged_in()){
					echo '<span style="display:none">logged in</span>';
					if($checktype == $activetype){
						echo '<span style="display:none">check and active</span>';
						do_action('gform_update_post/setup_form', array('post_id' => $activeid, 'form_id' => $fintype));
						$scstring = '';
						if ($fintype == 13){
							$scstring = '[gravityform id="13"]';
						} else {
							$scstring = '[gravityform id="'.$fintype.'" update="'.$activeid.'"]';
						}
						echo do_shortcode( $scstring );
					}
					else{
						echo"<p>An error has occured. Sorry for the inconvenience. Please try again later.</p>";
					}	
				}
				else{
					echo "<p>You are not currently logged in.</p>";
				}
				the_content();
				?>
                <a href="/my-account">Return to My Account page.</a>
            	</div>
                <div class="clear"></div>
                <?php
			}
			else{
				if(isset($_GET['login']) && $_GET['login'] == 'failed' && (is_user_logged_in() == false)){
				?>
					<p class="login-failed">Login Failed, please try again. <a href="<?php echo wp_lostpassword_url( get_permalink() ); ?>">Or reset your password&nbsp;&raquo;</a></p>
                    <!-- <a href="mailto:gpolyn@nocb.com?subject=Password%20Reset%20Request%20Central%20Penn%20Parent&body=Please%20include%20your%20name%20and%20the%20email%20address%20you%20used%20to%20create%20your%20account%20on%20CentralPennParent.com%20below.%20We%20will%20send%20your%20new%20password%20to%20this%20email%20address.">Or email us to reset your password&nbsp;&raquo;</a> --> 
				<?php
				}
				if (is_user_logged_in()) {
				?>
					<!-- <h1 class="support-title">My Account</h1> -->
					<h2 class="support-subtext"> Hello, <?php echo $user_login; ?></h2>
					
					<p>What would you like to do?</p>
					<!-- <a class="support-link" href="/add-buyers-guide-listing/">Create a business listing</a>
                    <a class="support-link" href="/edit-directory-listing/">Edit a business listing</a> -->
					<a class="support-link" href="/calendar/community/add">Submit a calendar event</a>
					<a class="support-link" href="/update-account-information/">Update online settings</a>
					<a class="support-link support-link-logout" id="wp-submit" href="<?php echo wp_logout_url( get_permalink() ); ?>" title="Logout">Logout</a>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                    <p class='support-bodytext'>Trying to update your print subscription? Visit the <a href='/subscribe'>subscription page</a>.</p>
                    <p>&nbsp;</p>
                    <p class='support-bodytext'>Need to cancel a business listing? <br />Please <a href="mailto:gpolyn@nocb.com?subject=Cancel%20Buyers%20Guide%20Listing&cc=rkeenan@bridgetowermedia.com&body=Please%20cancel%20my%20business%20listing.%20My%20contact%20information%20and%20the%20name%20of%20my%20listing%20is:">email us</a> with your contact information and the name of your listing.</p>
                    </div>
					
					
					
					<?php
				} 
				else {
					if(isset($_GET['newaccount']) && (is_user_logged_in() == false)){
					?>
						<p class="support-created">Your account has been created/updated. Please log in using your new credentials below.</p>
					<?php
					}
					echo "<h1 class='support-title'>My Account</h1>";
					echo "<h2 class='support-subtext'>Please use the form below to sign in.</h2>";
					echo "<p class='support-bodytext'>Creating an account is free and allows you to submit events to our calendar and create business listings. Print subscriptions can be managed from our <a href='/subscribe'>subscription page.</a></p>";
					 
						  $loginargs = array(
									'echo'           => true,
									'form_id'        => 'myaccount-login',
									'label_username' => __( 'Username' ),
									'label_password' => __( 'Password' ),
									'label_remember' => __( 'Remember Me' ),
									'label_log_in'   => __( 'Log In' ),
									'id_username'    => 'user_login',
									'id_password'    => 'user_pass',
									'id_remember'    => 'rememberme',
									'id_submit'      => 'wp-submit',
									'remember'       => true,
									'value_username' => NULL,
									'value_remember' => true
						); 
									
						wp_login_form($loginargs);
						echo "<span class='create-account'>Or create a <a href='/create-an-account/'>new account here&nbsp;&raquo;</a>";
				}
			};
			?> 
    </section>
</div>

<?php //include 'includes/digital-edition-section.php'; ?>

<?php get_footer(); ?>