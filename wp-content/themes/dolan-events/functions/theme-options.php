<?php
//if (!isset($_GET['action']) && $_GET['action'] != 'edit') {
$directory_file=get_bloginfo('template_directory');
if(isset($_GET['page']) && ("/".$_GET['page'] == TEMPLATEPATH.'/functions/theme-options.php')){	
	add_action('admin_print_scripts', 'my_admin_scripts');
	add_action('admin_print_styles', 'my_admin_styles');
}
function my_admin_scripts() {
	if ( is_admin() ) {
		$directory_file=get_bloginfo('template_directory');

		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');

		wp_enqueue_script('color-picker', $directory_file.'/functions/js/colorpicker.js', array('jquery'));

		wp_register_script('my-upload', $directory_file.'/functions/js/upload.js', array('jquery','media-upload','thickbox'));
		wp_enqueue_script('my-upload');
	}
}

function my_admin_styles() {
	$directory_file=get_bloginfo('template_url');
	wp_enqueue_style('color-picker', $directory_file.'/functions/css/colorpicker.css');
	wp_enqueue_style('thickbox');
}

add_action('admin_menu', 'create_theme_options_page');
add_action('admin_init', 'register_and_build_fields');

function create_theme_options_page() {
	add_menu_page('Theme Options', 'Theme Options', 'administrator', __FILE__, 'build_options_page');
	add_submenu_page( 'Footer Logos', 'Footer Logos', 'Footer Logos', 'administrator', 'footer_logos', 'build_options_page' ); 
}

function register_and_build_fields() {
	register_setting('theme_options', 'theme_options', 'validate_setting');
	add_settings_section('main_section', 'Main Settings', 'section_cb', __FILE__);
  add_settings_field('event_title', 'Event Title:', 'event_title_setting', __FILE__, 'main_section'); // BANNER HEADING
  add_settings_field('parent_nav', 'Show Parent Publication Navigation?', 'parent_pub_navigation_setting', __FILE__, 'main_section'); // PARENT PUB NAVIGATION
  add_settings_field('pub_name', 'Publication Name:', 'pub_name_setting', __FILE__, 'main_section'); // Publication Name
  add_settings_field('parent_logo', 'Parent Publication Logo:', 'parent_logo_setting', __FILE__, 'main_section'); // PARENT PUB LOGO
  add_settings_field('logo', 'Logo:', 'logo_setting', __FILE__, 'main_section'); // LOGO
  add_settings_field('colors', 'Colors:', 'colors_setting', __FILE__, 'main_section'); // STYLESHEET
  add_settings_field('social_icons', 'Social Network Icons:', 'social_icons_setting', __FILE__, 'main_section'); // Social Icons
  add_settings_field('video', 'Video ID:', 'video_setting', __FILE__, 'main_section'); // VIDEO
  add_settings_field('call_to_action', 'Display Call to Action Button?', 'call_to_action_setting', __FILE__, 'main_section'); // CALL TO ACTION
  add_settings_field('home_buttons', 'Display the following buttons on the homepage:', 'home_buttons_setting', __FILE__, 'main_section'); // HOME PAGE BUTTONS
  add_settings_field('sales_rep', 'Sales Rep E-Mail:', 'sales_rep_setting', __FILE__, 'main_section'); // SALES REP E-MAIL
  add_settings_field('bluesky', 'BlueSky Factory E-mail IDs', 'bluesky_setting', __FILE__, 'main_section'); // BLUESKY FACTORY ID CODE
  add_settings_field('footer_logos', 'Footer Logos:<br />(only select seven logos or less)', 'footer_logo_setting', __FILE__, 'main_section'); // LOGO 
  add_settings_field('custom_nav_bar', 'Custom Nav Bar', 'custom_nav_bar_setting', __FILE__, 'main_section'); // Custom Nav Bar
  add_settings_field('photo_gallery_title', 'Photo Gallery Title: (leave blank for default)', 'photo_gallery_title_setting', __FILE__, 'main_section'); // Photo Gallery Title
  add_settings_field('event_signup', 'Disable Event Signup:', 'event_signup_setting', __FILE__, 'main_section'); // Disable Event sign up
  add_settings_field('request_sponsorship_info', 'Disable Request Sponsorship:', 'request_sponsorship_info_setting', __FILE__, 'main_section'); // Disable request sponsorship info  
}


function section_cb() {}
function dummyCallback() {}

function event_title_setting() {
	$options = get_option('theme_options');
	echo "<input name='theme_options[event_title]' type='text' value='{$options['event_title']}' />";
}
function parent_pub_navigation_setting() {
	$options = get_option('theme_options');
	?>
	<label><input id="theme_options[parent_nav]" name="theme_options[parent_nav]" type="checkbox" value="1" <?php checked(1, $options['parent_nav']); ?> /> yes</label><br />
	<label>Parent Blog ID Number: <input id="theme_options[parent_blog_id]" name="theme_options[parent_blog_id]" type="text" value="<?php echo $options['parent_blog_id'] ?>" /></label><br />
	<label>Parent Blog URL: <input id="theme_options[parent_blog_url]" name="theme_options[parent_blog_url]" type="text" value="<?php echo $options['parent_blog_url'] ?>" /></label><br />
	<?php
}

function pub_name_setting(){
	$options = get_option('theme_options');
	echo "<input name='theme_options[pub_name]' type='text' value='{$options['pub_name']}' />";
}

function parent_logo_setting() {
	$options = get_option('theme_options');
	echo "<div class='alignleft' style='width:100px;'><em>current logo:</em><br /><img src='{$options['parent_logo']}' alt='logo' width='100' style='width:100px;border:1px solid #ccc;padding:5px;background:#fff;' /></div>"; 
	echo "<div class='alignleft' style='width:300px;margin-left:50px;'><label><em>upload a new logo:</em><br /><input id='parent_logo_data' type='text' size='20' name='theme_options[parent_logo]' value='{$options['parent_logo']}' /></label>";
	echo '<input id="upload_parent_logo" type="button" value="Upload Image" /></div>';
}


function logo_setting() {
	$options = get_option('theme_options');
	echo "<div class='alignleft' style='width:100px;'><em>current logo:</em><br /><img src='{$options['logo']}' alt='logo' width='100' style='width:100px;border:1px solid #ccc;padding:5px;background:#fff;' /></div>"; 
	echo "<div class='alignleft' style='width:300px;margin-left:50px;'><label><em>upload a new logo:</em><br /><input id='logo_data' type='text' size='20' name='theme_options[logo]' value='{$options['logo']}' /></label>";
	echo '<input id="upload_logo" type="button" value="Upload Image" /></div>';
}


function video_setting() {
	$options = get_option('theme_options');
	$radio_options = array(
		'yes' => array(
			'value' => 'youtube',
			'label' => __( 'Youtube' )
		),
		'no' => array(
			'value' => 'vimeo',
			'label' => __( 'Vimeo' )
		)
	);

	if ( ! isset( $checked ) )
		$checked = '';
	foreach ( $radio_options as $p ) {
		$radio_setting = isset($options['videoType']) ? $options['videoType'] : '';

		if ( '' != $radio_setting ) {
			if ( $options['videoType'] == $p['value'] ) {
				$checked = "checked=\"checked\"";
			} else {
				$checked = '';
			}
		}
		?>
		<label><input type="radio" name="theme_options[videoType]" value="<?php echo $p['value'] ?>" <?php echo $checked; ?> /> <?php echo $p['label']; ?> </label>
		<?php
	}
	echo "&nbsp;&nbsp;&nbsp;<label>Video ID <input type='text' name='theme_options[video]' value='{$options['video']}' /></label>";
}
function call_to_action_setting() {
	$options = get_option('theme_options');
	$call_to_action = isset($options['call_to_action']) ? $options['call_to_action'] : '';
	echo '<table class="widefat fixed" cellpadding="0" style="width:600px;">';
	echo '<thead><tr><th class="manage-column column-cb check-column"></th><th class="manage-column column-title" width="120">Label</th><th class="manage-column column-title" width="120">Link (format:http//www.domainname.com)</th></tr></thead>';
	?>
	<tr>
		<td>
			<input id="theme_options[call_to_action]" name="theme_options[call_to_action]" type="checkbox" value="1" <?php checked( 1, $call_to_action ); ?> />	
		</td>
		<td>
			<input id="theme_options[cta_label]" name="theme_options[cta_label]" type="text" value="<?php echo $options['cta_label'] ?>" />
		</td>
		<td>
			<input id="theme_options[cta_link]" name="theme_options[cta_link]" type="text" value="<?php echo $options['cta_link'] ?>" />
		</td>
		<tr>
			<?php
			echo '</table>';
		}

		function get_string_between($string, $start, $end){
			$string = " ".$string;
			$ini = strpos($string,$start);
			if ($ini == 0) return "";
			$ini += strlen($start);
			$len = strpos($string,$end,$ini) - $ini;
			return substr($string,$ini,$len);
		}

		function colors_setting() {
			$options = get_option('theme_options');

			$colorHeaders = array('Background Color', 'Header Color', 'Link Color', 'Button Color', 'Button Mouseover Color', 'Button Text Color', 'Secondary Button Color', 'Secondary Button Mouseover Color', 'Secondary Button Text Color');
			$colorOptions = array('background_color', 'header_color', 'link_color', 'button_color', 'button_over_color', 'button_text_color', 'secondary_button_color', 'secondary_button_over_color', 'secondary_button_text_color');
			$count = 0;

			foreach($colorOptions as $color) {
				if(!empty($options[$color])) {
					$el = $options[$color];
				} else {
					$el = '#123456';
				}
				echo "<div style='clear:left;'>";
				echo "<label>".$colorHeaders[$count]."<br /><div id='body_background_picker' class='colorSelector'><div style='background-color:".$el."'></div></div>";
				echo "<input class='of-color' name='theme_options[" . $color ."]' id='bgcolor' type='text' value='".$el."' /></label>";
				echo "</div>";
				$count++;
			}
		}

		function get_between($input, $start, $end)
		{
			$substr = substr($input, strlen($start)+strpos($input, $start), (strlen($input) - strpos($input, $end))*(-1));
			return $substr;
		} 
		function social_icons_setting() {
			$options = get_option('theme_options');

			$social_icons = array('facebook', 'twitter', 'linkedin', 'vimeo', 'youtube');

			echo '<table class="widefat fixed" cellpadding="0" style="width:600px;">';
			echo '<thead><tr><th class="manage-column column-cb check-column"></th><th class="manage-column column-title" width="120">Icon</th><th class="manage-column column-title" width="120">Link (format:http//www.domainname.com)</th></tr></thead>';
			$count = 0;
			while ($count <= 4):
				$social_icon = isset($options['social_icon'.$count]) ? $options['social_icon'.$count] : '';
				?>
				<tr>
					<td>
						<input id="theme_options[social_icon<?php echo $count; ?>]" name="theme_options[social_icon<?php echo $count; ?>]" type="checkbox" value="1" <?php checked( 1, $social_icon ); ?> />	
					</td>
					<td>
						<img src="<?php bloginfo('template_url') ?>/functions/images/<?php echo $social_icons[$count] ?>.png" />
						<input id="theme_options[social_icon_url<?php echo $count; ?>]" name="theme_options[social_icon_url<?php echo $count; ?>]" type="hidden" value="<?php bloginfo('template_url'); ?>/images/common/<?php echo $social_icons[$count] ?>.png"  />	
					</td>
					<td>
						<input id="theme_options[social_icon_link<?php echo $count; ?>]" name="theme_options[social_icon_link<?php echo $count; ?>]" type="text" value="<?php echo $options['social_icon_link'.$count] ?>" />
					</td>
					<tr>
						<?php
						$count++;
					endwhile;
					echo '</table>';
				}



				function home_buttons_setting() {
					$options = get_option('theme_options');
					echo '<table class="widefat fixed" cellpadding="0" style="width:600px;">';
					echo '<thead><tr><th class="manage-column column-cb check-column"></th><th class="manage-column column-title" width="120">Label</th><th class="manage-column column-title" width="120">Link (format:http//www.domainname.com)</th></tr></thead>';
					$count = 1;
					while ($count <= 3):
						$home_button = isset($options['home_button'.$count]) ? $options['home_button'.$count] : '';
						?>
						<tr>
							<td>
								<input id="theme_options[home_button<?php echo $count; ?>]" name="theme_options[home_button<?php echo $count; ?>]" type="checkbox" value="1" <?php checked( 1, $home_button ); ?> />	
							</td>
							<td>
								<input id="theme_options[home_button_label<?php echo $count; ?>]" name="theme_options[home_button_label<?php echo $count; ?>]" type="text" value="<?php echo $options['home_button_label'.$count] ?>" />
							</td>
							<td>
								<input id="theme_options[home_button_link<?php echo $count; ?>]" name="theme_options[home_button_link<?php echo $count; ?>]" type="text" value="<?php echo $options['home_button_link'.$count] ?>" />
							</td>
							<tr>
								<?php
								$count++;
							endwhile;
							echo '</table>';
							$count = $count-1;
							echo "<input name='theme_options[numButtons]' type='hidden' value='".$count."' />";
						}


						function sales_rep_setting() {
							$options = get_option('theme_options');
							echo "<label>Form Text: (leave black for default) <input name='theme_options[sales_rep_text]' type='text' value='{$options['sales_rep_text']}' /></label>";
							echo "<label>Email:<input name='theme_options[sales_rep]' type='text' value='{$options['sales_rep']}' /></label>";
						}
						function bluesky_setting() {
							$options = get_option('theme_options');
							echo "<label>Form Text:(leave blank for default)<input name='theme_options[bsText]' type='text' value='{$options['bsText']}' /></label><br/> ";
							echo "<label>Client ID: <input name='theme_options[bsClientID]' type='text' value='{$options['bsClientID']}' /></label> ";
							echo "<label>Form ID: <input name='theme_options[bsFormID]' type='text' value='{$options['bsFormID']}' /></label>";
						}

						function footer_logo_setting() {
							$options = get_option('theme_options');
							echo '<table class="widefat page fixed" cellpadding="0" style="width:600px;">';
							echo '<thead><tr><th width="120">Image URL<br />(maximum width: 150px)</th><th width="120">Link<br /> (format:http//www.domainname.com)</th></tr></thead>';
							$count = 1;
							while($count<=8){
								?>
								<tr>
									<td>
										<input id="footer_logo<?php echo $count; ?>" name="theme_options[footer_logo<?php echo $count; ?>]" type="text" value="<?php echo $options['footer_logo'.$count] ?>" />
										<input id="upload_logo<?php echo $count ?>" type="button" value="Upload Image" />	
									</td>
									<td>
										<input id="theme_options[footer_link<?php echo $count; ?>]" name="theme_options[footer_link<?php echo $count; ?>]" type="text" value="<?php echo $options['footer_link'.$count] ?>" />	
									</td>
									<tr>
										<?php
										$count++;
									}
									echo '</table>';
								}

								function custom_nav_bar_setting() {
									$options = get_option('theme_options');
									$custom_nav_bar = isset($options['custom_nav_bar']) ? $options['custom_nav_bar'] : '';
									?>
									<label><input id="theme_options[custom_nav_bar]" name="theme_options[custom_nav_bar]" type="checkbox" value="1" <?php checked(1, $custom_nav_bar); ?> /> yes</label>	
									<?php
									echo '<table class="widefat page fixed" cellpadding="0" style="width:600px;">';
									echo '<thead><tr><th width="120" style="width:80px">Label<br/ ></th><th width="120" style="width:240px">Link<br /> (format:http//www.domainname.com)</th></tr></thead>';
									$count = 1;
									while($count<=5){
										?>
										<tr>
											<td>
												<input id="custom_nav_bar_label<?php echo $count; ?>" name="theme_options[custom_nav_bar_label<?php echo $count; ?>]" type="text" value="<?php echo $options['custom_nav_bar_label'.$count] ?>" />
											</td>
											<td>
												<input id="theme_options[custom_nav_bar_link<?php echo $count; ?>]" name="theme_options[custom_nav_bar_link<?php echo $count; ?>]" type="text" size="60" value="<?php echo $options['custom_nav_bar_link'.$count] ?>" />	
											</td>
											<tr>
												<?php
												$count++;
											}
											echo '</table>';
										}

										function event_signup_setting() {
											$options = get_option('theme_options');
											$event_signup = isset($options['event_signup']) ? $options['event_signup'] : '';
											?>
											<label><input id="theme_options[event_signup]" name="theme_options[event_signup]" type="checkbox" value="1" <?php checked(1, $event_signup); ?> /> yes</label>	
											<?php
										}

										function request_sponsorship_info_setting() {
											$options = get_option('theme_options');
											$request_sponsorship_info = isset($options['request_sponsorship_info']) ? $options['request_sponsorship_info'] : '';
											?>
											<label><input id="theme_options[request_sponsorship_info]" name="theme_options[request_sponsorship_info]" type="checkbox" value="1" <?php checked(1, $request_sponsorship_info); ?> /> yes</label>	
											<?php
										}

										function photo_gallery_title_setting() {
											$options = get_option('theme_options');
											echo "<input name='theme_options[photo_gallery_title]' type='text' value='{$options['photo_gallery_title']}' />";
										}

										function validate_setting($theme_options) {
											$options = get_option('theme_options');
											$keys = array_keys($_FILES);
											$i = 0;

											foreach ( $_FILES as $image ) {
		// if a files was upload
												if ($image['size']) {

		 // if it is an image
													if ( preg_match('/(jpg|jpeg|png|gif)$/', $image['type']) ) {
														$override = array('test_form' => false);
		   // save the file, and store an array, containing its location in $file
														$file = wp_handle_upload( $image, $override );
														$theme_options[$keys[$i]] = $file['url'];
													} else {
		   // Not an image. 
														$options = get_option('theme_options');
														$theme_options[$keys[$i]] = $options[$logo];
		   // Die and let the user know that they made a mistake.
														wp_die('No image was uploaded.');
													}
												}

		// Else, the user didn't upload a file.
		// Retain the image that's already on file.
												else {
													$options = get_option('theme_options');
													$theme_options[$keys[$i]] = $options[$keys[$i]];	
												}
												$i++;
											}
											$logoCount = $options['numLogos'];
											return $theme_options;
										}



										function build_options_page() {
											if ( ! isset( $_REQUEST['updated'] ) )
												$_REQUEST['updated'] = false;
											?>
											<script type="text/javascript" charset="utf-8">
												jQuery(document).ready(function() {
													jQuery('.colorSelector').each(function(){
		var Othis = this; //cache a copy of the this variable for use inside nested function
		var initialColor = jQuery(Othis).next('input').attr('value');
		jQuery(this).ColorPicker({
			color: initialColor,
			onShow: function (colpkr) {
				jQuery(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				jQuery(colpkr).fadeOut(500);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				jQuery(Othis).children('div').css('backgroundColor', '#' + hex);
				jQuery(Othis).next('input').attr('value','#' + hex);
			}
		});
	}); //end color picker
												});
											</script>


											<div class="wrap">
												<div class="icon32" id="icon-tools"> <br /> </div>
												<h2>Theme Options</h2>
												<?php if ( false !== $_REQUEST['updated'] ) : ?>
													<div class="updated fade"><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>
												<?php endif; ?>
												<p>Set theme options here.</p>
												<form method="post" action="options.php" enctype="multipart/form-data">
													<?php settings_fields('theme_options'); ?>
													<?php do_settings_sections(__FILE__); ?>
													<p class="submit">
														<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
													</p>
												</form>
											</div>
											<?php
										}
										?>