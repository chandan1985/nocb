<?php
/*
 * Custom Templates Class for BC-Paywall PSA
 * Author: Asentech
 */
 
  // Disallow direct access
if( !defined( 'ABSPATH' ) ) {
	die( 'Direct access not allowed.' );
}
 
class bc_paywall_templates {

	/**
	 * Return HTML for Subscribe tab of paywall
	 *
	 * @param array $paywall_options
	 * @return html subscribe tab content or empty string if not active
	 */
	public static function build_subscribe_text( $paywall_options ) {
		$subscribe_text = '';
		if( 'Custom Display' == $paywall_options['subscribe_display'] ) {
			$subscribe_text = $paywall_options['custom_subscribe_text'];
		}
		elseif( 'Compare Page' == $paywall_options['subscribe_display'] ) {
			$compare = get_page_by_path( $paywall_options['compare_path'] );
			$subscribe_text = $compare->post_content;
		}
		$temp = self::parse_custom_links( array( 0 => $subscribe_text ), $paywall_options );
		return array_shift( $temp );
	}

	/**
	 * Output HTML for tabbed Subscribe / Register / Login forms
	 * Use with ob_start() to fire WordPress register / login hooks
	 *
	 * @param array $paywall_options
	 * @param array $paywall_data
	 * @param string $default_view = 'subscribe|register|login'
	 * @param boolean $preview - output form w/o buttons for admin preview; not yet implemented
	 * @return html login form
	 */
	public static function display_paywall( $paywall_options, $paywall_data, $default_view = 'subscribe', $preview = false ) {
		$subscribe_text = self::build_subscribe_text( $paywall_options );
		if( 'subscribe' == $default_view && ( 'Deactivate' == $paywall_options['subscribe_display'] || !$subscribe_text ) ) {
			$default_view = 'login';
		}
		self::display_dmcss_message( $paywall_data );
		//Before Login Text
		if( $paywall_options['before_login_text'] ) {?>
		<div id = "before_login_text">
		<?php 
					
			$custom_strings = array($paywall_options['before_login_text']);
			$custom_strings = self::parse_custom_links( $custom_strings, $paywall_options );
			echo( $custom_strings[0] );		
			
		?>
		</div>
		<?php } //Begin Login Form ?>
		
		<div id="form_tabs">
			<div id="form_nav">
			<ul id="dmcss_tabs"><?php
				if( $subscribe_text ): ?>
					<li id="subscribe_tab" <?php if( 'subscribe' == $default_view ) { echo( 'class="active_tab"' ); } ?>><a id="subscribe_link" href="#subscribe_div">Subscribe</a></li><?php
				endif;
				if( $paywall_options['allow_registered_users'] ): ?>
					<li id="register_tab" <?php if( 'register' == $default_view ) { echo( 'class="active_tab"' ); } ?>><a id="register_link" href="#register_div">Register</a></li><?php
				endif; ?>
				<li id="login_tab" <?php if( 'login' == $default_view ) { echo( 'class="active_tab"' ); } ?>><a id="login_link" href="#login_div">Login</a></li>
			</ul>
			</div>
			<div id="subscribe_div" class="dmcss_tab_div <?php if( 'subscribe' == $default_view ) { echo( 'active_tab' ); } ?>"><?php
				echo( $subscribe_text ); ?>
			</div><?php
			if( $paywall_options['allow_registered_users'] ): ?>
				<div id="register_div" class="dmcss_tab_div <?php if( 'register' == $default_view ) { echo( 'active_tab' ); } ?>"><?php
					$custom_strings = array();
					if( $paywall_options['custom_register'] ) {
						$custom_strings = preg_split( '/\$register_form/', $paywall_options['custom_register_text'], NULL, PREG_SPLIT_NO_EMPTY );
						$custom_strings = self::parse_custom_links( $custom_strings, $paywall_options );
						echo( $custom_strings[0] );
					}
					$clean_url = preg_replace( '/(\?.*)/', '', $_SERVER['REQUEST_URI'] ) . '?dmcss=register'; ?>
					<form method="post" action="<?php echo( $clean_url ); ?>">
						<div class="field">
							<label for="dmcss_user">E-mail Address:</label>
							<input type="text" name="user_email" class="input" value="<?php esc_attr( $user_login ); ?>" size="20" tabindex="10" />
						</div>
						<p class="submit"><?php
							do_action( 'register_form' ); 
							do_action( 'signup_extra_fields', $paywall_data['error'] ); ?>
							<input type="submit" name="user-submit" class="button-primary <?php if( $preview ) { echo( 'disabled' ); } ?>" value="<?php esc_attr_e( 'Register' ); ?>" tabindex="100" />
							<input type="hidden" name="redirect_to" value="<?php echo( $clean_url ); ?>" />
							<input type="hidden" name="user-cookie" value="1" />
						</p>
					</form>
					<?php echo( $custom_strings[1] ); ?>
				</div><?php
			endif; ?>
			<div id="login_div" class="dmcss_tab_div <?php if( 'login' == $default_view ) { echo( 'active_tab' ); } ?>"><?php
				if( $paywall_options['custom_login'] ) {
					$custom_strings = preg_split( '/\$login_form/', $paywall_options['custom_login_text'], NULL, PREG_SPLIT_NO_EMPTY );
					$custom_strings = self::parse_custom_links( $custom_strings, $paywall_options );
				}
				self::login_tab_content( $paywall_options, $custom_strings );
			?>
			</div>
		</div><?php
	}

	/**
	 * Output HTML for Login tab of paywall
	 *
	 * @param array $paywall_options
	 * @param array $custom_strings
	 * @return void
	 */
	public static function login_tab_content( $paywall_options, $custom_strings = array() ) {
    if(isset($custom_strings[0])){
    	$custom_strings[0];
    } 
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'advanced-nocaptcha-recaptcha/advanced-nocaptcha-recaptcha.php' ) || is_plugin_active( 'wp-recaptcha/wp-recaptcha.php' )) { 
    $old_options = get_option("recaptcha_options");	
    $pub= $old_options['site_key'];	
    $private= $old_options['secret'];
	}
    ?>
	<?php 
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'advanced-nocaptcha-recaptcha/advanced-nocaptcha-recaptcha.php' ) || is_plugin_active( 'wp-recaptcha/wp-recaptcha.php' )) {  ?>
    <script type="text/javascript">
      var onloadCallback = function() {
        grecaptcha.render('html_element', {
          'sitekey' : '<?php echo $pub;?>'
        });
      };
    </script>
	<?php } ?>
	<?php	echo isset($custom_strings[0]) ? $custom_strings[0] : ''; ?>
		<form name="loginform" class="loginform" style="text-align:left" id="loginform" action="<?php echo( site_url('wp-login.php', 'login_post') ); ?>" method="post"><?php
			// User name input & label
			$fieldLabel = $paywall_options['field_label'];
			if( empty( $fieldLabel ) ) {
				$fieldLabel = dmcss_wp::DEFAULT_FIELD_LABEL;
			} ?>
			<div class="field">
				<label for="dmcss_user"><?php echo( $fieldLabel ); ?></label>
				<input type="hidden" name="login_red" value="<?php echo $_GET['tpi'];?>"/>
				<input type="text" name="log" id="user_login" class="input" value="" size="20" tabindex="10" />
			</div><?php

			// Password input & label ?>
			<div class="field">
				<label for="dmcss_pass"><?php _e('Password'); ?>:</label>
				<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" tabindex="20" />
			</div><?php
			do_action('login_form');

			// Log In Button & hidden fields
			// Preserve any URL forward when logging in
			$redirect_to = preg_replace( '/\\?.*/', '', $_SERVER['REQUEST_URI'] );
			preg_match( '/forward=([^&]*)/', $_SERVER['REQUEST_URI'], $matches );
			if( isset( $matches[1] ) ){
				$redirect_to .= '?forward=' . $matches[1];
			}
			$preview = false;
			$interim_login = '';
			?>
			<p class="submit">
				<input type="submit" name="wp-submit" id="wp-submit" class="button-primary button <?php if( $preview ) { echo( 'disabled' ); } ?>" value="<?php esc_attr_e('Log In'); ?>" tabindex="100" /><?php
				if ( $interim_login ) : ?>
					<input type="hidden" name="interim-login" value="1" /><?php
				endif; ?>
				<input type="hidden" name="rememberme" value="1" />
				<input type="hidden" name="redirect_to" value="<?php echo($redirect_to); ?>" />
			</p>
		</form>
		
		
		<?php
		if( $paywall_options['custom_login'] && !empty( $custom_strings[1] ) ) {
			echo( $custom_strings[1] );
		}
		else { ?>
			<div class="links"><?php 
				if( $paywall_options['show_subscribe'] && !empty( $paywall_options['subscribe_url'] ) ): ?>
					<p><a href="<?php echo( $paywall_options['subscribe_url'] ); ?>"><?php _e('Subscribe'); ?></a></p><?php
				endif; 
				if( $paywall_options['show_forgot_password'] && !empty( $paywall_options['forgot_pwd_url'] ) ): ?>
					<p><a href="<?php echo( $paywall_options['forgot_pwd_url'] ); ?>"><?php _e('Forgot your password?'); ?></a></p><?php
				endif; ?>
			</div><?php
		}
	}

	/**
	 * Return template for Web Service error
	 *
	 * @param string $username
	 * @param string $pubcode
	 * @param array $result
	 * @return html login form
	 */
	public static function ws_custom_error( $username, $pubcode, $result ) {
		// Log errors to file if defined
		if( defined( 'PAYWALL_LOG_FILE' ) ) {
			$log_str = ' ['. time() . '] [' . gethostbyaddr('127.0.0.1') .'] [' . $_SERVER[ 'SERVER_NAME' ] . '] [' . $username . ' / ' . $pubcode . '] [' . $_SERVER['REMOTE_ADDR'] . '] ' . var_export( $result, true ) . PHP_EOL;
			error_log( $log_str, 3, PAYWALL_LOG_FILE );
		}
		// Output custom error to screen
		$em_msg = '
			<style>
				#myerror{
					background: #DDDDDD;
					font-size:14px;
					padding-bottom: 5px;
					border: 1px solid #000000;
				}
				#header{
					font-weight:bold;
					padding: 5px 10px;
					background: #4D4DFF;
					border-bottom: 1px solid #000000;
				}
				#error-txt, #error-detail{
					padding: 15px 10px 10px;
				}
				#detail-less, #error-detail{
					display:none;
				}
				#error-detail{
					background:#EFEFEF;
					margin:0px 10px 5px;
					border: 1px solid #000000;
				}
				#myerror p{
					padding: 0px 10px;
					margin: 0px;
				}
				#myerror p a{
					color: #333333;
					font-size: 11px;
				}
			</style>
			<script>
				function hidediv() {
					document.getElementById(\'detail-more\').style.display = \'block\';
					document.getElementById(\'detail-less\').style.display = \'none\';
					document.getElementById(\'error-detail\').style.display = \'none\';
				}

				function showdiv(id) {
					document.getElementById(\'detail-more\').style.display = \'none\';
					document.getElementById(\'detail-less\').style.display = \'block\';
					document.getElementById(\'error-detail\').style.display = \'block\';
				}
			</script>	
			<div id="myerror">
				<div id="header">ERROR:</div>
				<div id="error-txt">
					Web Service returned invalid response; please <a href="' . $_SERVER['HTTP_REFERER'] . '">try again</a>.  If this issue persists, please notify customer service at 1-800-451-9998.
				</div>
				<p id="detail-more">
					<a href="javascript:showdiv();">details</a>
				</p>
				<p id="detail-less">
					<a href="javascript:hidediv();">hide</a>
				</p>
				<div id="error-detail">'
					. var_export( $result, true ) . '
				</div>
			</div>';	
		wp_die(__($em_msg));
	}

	/**
	 * Output logged out message or error
	 *
	 * @param array $paywall_data
	 * @param string $_GET['loggedout']
	 * @return void
	 */
	public static function display_dmcss_message( $paywall_data ) {
		// Show error(s) / message(s) / logout message
		$codes =  $paywall_data['error']->get_error_codes();
		if( !empty( $codes ) ) {
			echo( '<div id="login_error" class="message">' );
			foreach( $codes as $code ) {
				$err_msg =  $paywall_data['error']->get_error_message( $code );
				if( !empty( $err_msg ) ) {
				echo( '<p>' . $err_msg . '</p>' );
				}
			}
			echo( '</div>' );	
		}
		elseif( isset( $paywall_data['message'] ) ) {
			echo( '<div id="login_message" class="message">' . $paywall_data['message'] . '</div>' );
		}
		elseif(isset($_GET['loggedout']) && $_GET['loggedout'] == 'true' ) {
			$logged_out = $paywall_options['logged_out'];
			if( empty( $logged_out ) ) {
				$logged_out = dmcss_wp::DEFAULT_LOGGEDOUT_MSG;
			 }
			 echo( '<div id="login_message" class="message">' . $logged_out . '</div>' );
		}
	}

	/**
	 * Handle content replacement for $subscribe_link and $forgot_password_link
	 *
	 * @param array $strings - array of strings to be scanned for replacement
	 * @param array $paywall_options - copy of paywall options
	 * @return array $strings input array w/ replaced content
	 */
	public static function parse_custom_links( $strings = array(), $paywall_options=array() ) {
		foreach( $strings as $key => $string ) {
			$replace = '';
			if( !empty( $paywall_options['forgot_pwd_url'] ) ) {
				$replace = '<a href="' . $paywall_options['forgot_pwd_url'] . '">Forgot your password?</a>';
			}
			$strings[$key] = preg_replace( '/\$forgot_password_link/', $replace , $strings[$key] );

			$replace = '';
			if( !empty( $paywall_options['subscribe_url'] ) ) {
				$replace = '<a href="' . $paywall_options['subscribe_url'] . '">Subscribe</a>';
			}
			$strings[$key] = preg_replace( '/\$subscribe_link/', $replace, $strings[$key] );
		}
		return $strings;
	}
}