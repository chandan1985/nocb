<?php
/* Admin page for TDC Add to Home Screen
 *
 */

// Disallow direct access
if ( !defined( 'ABSPATH' ) )
	die( 'Direct access not allowed.' );

final class add_to_home_admin {

	###  Class Variables ###
	protected $ops;

	public function __construct( $options = null ) {
		// Set up options from parent
		$this->ops = $options;

		// Add admin menus
		add_action( 'admin_menu', array( &$this, 'create_plugin_options_page' ) );
		// Register settings for API
		add_action( 'admin_init', array( &$this,'register_settings' ) );
	}

	/*
	 * Add 'ATHS Options' to the settings menu if current user has administrator access
	 */
	public function create_plugin_options_page() {
		$page = add_options_page( 'Add to Home Screen Options', 'ATHS Options', 'administrator', 'aths-options', array( &$this, 'build_options_page' ) );
		
		// Set up CSS import on our page only
		add_action('admin_print_styles-' . $page, array(&$this, 'enqueueStylesheet'));
	}

	/**
	 * Import plugin CSS
	 *
	 * @return void
	 */		
	 public function enqueueStylesheet(){
		wp_register_style( 'add_to_home_admin_styles', plugins_url( '/style.css', __FILE__ ) );
		wp_enqueue_style( 'add_to_home_admin_styles' );
	}

	/*
	 * Register & set up plugin options page via settings API
	 */
	public function register_settings() {
		register_setting( 'aths_data', 'aths_data', array( &$this, 'validate_options' ) );
	}

	/*
	 * Sanitize and validate input. Accepts an array, return a sanitized array.
	 */
	public function validate_options( $input ) {
		// Whether to show to first time visitors; important
		if( empty( $input['returning_visitor'] ) )
			$input['returning_visitor'] = false;
		else
			$input['returning_visitor'] = true;

		// ms before display; 0 sec < start_delay < 10 sec
		if( !is_numeric( $input['start_delay'] ) || 10000 < $input['start_delay'] || 0 > $input['start_delay'] )
			$input['start_delay'] = 2000;

		// ms displayed; 5 sec < lifespan < 30 sec
		if( !is_numeric( $input['lifespan'] ) || 30000 < $input['lifespan'] || 5000 > $input['lifespan'] )
			$input['lifespan'] = 20000;

		// bottom_offset; 0 < bottom_offset < 100
		if( !is_numeric( $input['bottom_offset'] ) || 100 < $input['bottom_offset'] || 0 > $input['bottom_offset'] )
			$input['bottom_offset'] = 14;

		// expire time in minutes; expire >= 0
		if( !is_numeric( $input['expire'] ) || 0 > $input['expire'] )
			$input['expire'] = 43200;

		// Confirm that icons are URLs ending in .png
		$regex = '/^http:\/\/\S+\.png$/';
		foreach( $input['touch_icon'] as $key => $val ){
			if( !preg_match( $regex, $val['url'] ) )
				$val['url'] = '';
		}

		// Whether to show touch icon next to bookmark message
		if( empty( $input['show_touch_icon'] ) )
			$input['show_touch_icon'] = false;
		else
			$input['show_touch_icon'] = true;

		// Toggle Apple gloss effects on/off
		if( empty( $input['icon_precomposed'] ) )
			$input['icon_precomposed'] = false;
		else
			$input['icon_precomposed'] = true;

		return $input;
	}
	
	/**
	 *Sort the array keys
	 *return custom keys array
	*/

	 public function array_reorder_keys(&$array, $keynames){
    	if(empty($array) || !is_array($array) || empty($keynames)) return;
    		if(!is_array($keynames)) $keynames = explode(',',$keynames);
    			if(!empty($keynames)) $keynames = array_reverse($keynames);
				    foreach($keynames as $n){
				        if(array_key_exists($n, $array)){
				            $newarray = array($n=>$array[$n]); //copy the node before unsetting
				            unset($array[$n]); //remove the node
				            $array = $newarray + array_filter($array); //combine copy with filtered array
				        	}
				    	}
		}

	/*
	 * Build basic theme options page via settings API
	 */
	public function build_options_page() { ?>
		<div class="wrap" id="ATHS_Options">
			<h2>TDC Add to Home Screen Options</h2>
			<form method="post" name="ATHS_options_form" id="ATHS_options_form" action="options.php" enctype="multipart/form-data">
				<?php settings_fields( 'aths_data' ); ?>
				<?php /* ?>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<h3 class="title">Popup Options</h3>
							</th>
							<td>
								<fieldset>
									<label for="returning_visitor">
										<input type="checkbox" id="returning_visitor" name="aths_data[returning_visitor]" value="true" <?php if( $this->ops['returning_visitor'] ): ?> checked="yes" <?php endif; ?> />
										<?php _e( 'Show to returning visitors only' ) ?>
										<p class="description"><?php _e( 'Set this to true and the message won\'t be shown the first time one user visits your blog.<br>It can be useful to target only returning visitors and not irritate first time visitors.<br>Default: on' ); ?></p>
									</label>
									<label for="message">
										<h4><?php _e( 'Custom message' ); ?></h4>
										<p><?php _e( 'Type the custom message that you want appearing in the balloon.' ); ?></p>
										<textarea rows="3" cols="50" id="message" name="aths_data[message]"/><?php echo( $this->ops['message'] ); ?></textarea>
										<p class="description"><?php _e( 'Use %device to show user\'s device on message, and %icon to display the add icon.<br>Default is: Install this web app on your %device: tap %icon and then <strong>Add to Home Screen</strong>.' ); ?></p>
									</label>
									<label for="animation_in">
										<h4><?php _e( 'Animation in' ); ?></h4>
										<select name="aths_data[animation_in]" id="animation_in">
											<option value="drop"<?php echo( selected( $this->ops['animation_in'], drop ) ); ?>>drop</option>
											<option value="bubble"<?php echo( selected( $this->ops['animation_in'], bubble ) ); ?>>bubble</option>
											<option value="fade"<?php echo( selected( $this->ops['animation_in'], fade ) ); ?>>fade</option>
										</select>
										<p class="description"><?php _e( 'The animation the balloon appears with.' ); ?></p>
									</label>
									<label for="animation_out">
										<h4><?php _e( 'Animation out' ); ?></h4>
										<select name="aths_data[animation_out]" id="animation_out">
											<option value="drop"<?php echo( selected( $this->ops['animation_out'], drop ) ); ?>>drop</option>
											<option value="bubble"<?php echo( selected( $this->ops['animation_out'], bubble ) ); ?>>bubble</option>
											<option value="fade"<?php echo( selected( $this->ops['animation_out'], fade ) ); ?>>fade</option>
										</select>
										<p class="description"><?php _e( 'The animation the balloon exits with.' ); ?></p>
									</label>
									<label for="startdelay">
										<h4><?php _e( 'Start delay' ); ?></h4>
										<input type="text" id="start_delay" name="aths_data[start_delay]" value="<?php echo( $this->ops['start_delay'] ); ?>"/>
										<p class="description"><?php _e( 'Milliseconds to wait before showing the message. Default: 2000' ); ?></p>
									</label>
									<label for="lifespan">
										<h4><?php _e( 'Lifespan' ); ?></h4>
										<input type="text" id="lifespan" name="aths_data[lifespan]" value="<?php echo( $this->ops['lifespan'] ); ?>"/>
										<p class="description"><?php _e( 'Milliseconds to wait before hiding the message. Default: 20000' ); ?></p>
									</label>
									<label for="bottomoffset">
										<h4><?php _e( 'Bottom offset' ); ?></h4>
										<input type="text" id="bottom_offset" name="aths_data[bottom_offset]" value="<?php echo ( $this->ops['bottom_offset'] ); ?>"  />
										<p class="description"><?php _e( 'Distance in pixels from the bottom (iPhone) or the top (iPad). Default: 14' ); ?></p>
									</label>
									<label for="expire">
										<h4><?php _e( 'Expire timeframe' ); ?></h4>
										<input type="text" id="expire" name="aths_data[expire]" value="<?php echo( $this->ops['expire'] ); ?>"  />
										<p class="description"><?php _e( 'Minutes before displaying the message again. Default: 43200 (once per week).' ); ?></p>
									</label>
									<label for="pagetarget">
										<h4><?php _e( 'On which page(s) should the balloon appear?' ); ?></h4>
										<select name="aths_data[page_target]" id="page_target">
											<option value="home_only"<?php echo( selected( $this->ops['page_target'], home_only ) ); ?>><?php _e( 'Home only' ); ?></option>
											<option value="all_pages"<?php echo( selected( $this->ops['page_target'], all_pages ) ); ?>><?php _e( 'All pages' ); ?></option>
										</select>
										<p class="description"><?php _e( 'Default: Home only.<br>If the user bookmarks a post or page, they will be directed there when opening their home screen link instead of the blog main page.' ); ?></p>
									</label>
								</fieldset>
							</td>
						</tr>
					</tbody>
				</table>
				<?php */ ?>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<h3 class="title">iOS Bookmark Icons</h3>
								<p class="description"><?php _e( 'If mentionned, those fields add <i>link rel="apple-touch-icon"</i> in the page HEAD (convenient for those who have no touch icon). Just paste the URLs of your icons.' ); ?></p>
							</th>
							<td>
								<fieldset>
									<table class="widefat">
										<thead>
											<tr>
												<th class="icon">Icon</th>
												<th>URL / Description</th>
											</tr>
										</thead>
										<tbody>
											<tr> <td>
												<h3>For IOS Devices</h3>
											</td></tr>
											<?php
											$this->array_reorder_keys($this->ops['touch_icon'], '57x57,60x60,72x72,76x76,114x114,144x144,120x120,128x128,152x152,167x167,180x180,256x256'); 

											foreach( $this->ops['touch_icon'] as $key => $val ) : ?>
												<?php if($val['desc'] != '') { ?>
												<tr>
													<td class="icon">
														<?php if( !empty( $val['url'] ) )
															echo( '<img src="' . $val['url'] . '">');
														else
															echo('<img src="' . plugins_url( '/images/' . $key . '.gif', __FILE__ ) . '">'); ?>
													</td>
													<td>
														<input type="url" id="aths_data[touch_icon][<?php echo( $key); ?>][url]" name="aths_data[touch_icon][<?php echo( $key); ?>][url]" value="<?php echo isset($val['url']) ? $val['url'] : ''; ?>"/>
														<p class="description"><?php echo isset($val['desc']) ? $val['desc'] : ''; ?></p>
													</td>
												</tr>
											<?php } ?>
											<?php endforeach;?>

											<tr> <td>
												<h3>For Other Browsers (Chrome, Mozilla etc)</h3>
											</td></tr>

											<?php
											$this->array_reorder_keys($this->ops['icon'], '48x48,96x96,144x144,192x192,256x256,384x384'); 

											foreach( $this->ops['icon'] as $key => $val ) : ?>
												<?php if($val['desc'] != '') { ?>
												<tr>
													<td class="icon">
														<?php if( !empty( $val['url'] ) )
															echo( '<img src="' . $val['url'] . '">');
														else
															echo('<img src="' . plugins_url( '/images/' . $key . '.gif', __FILE__ ) . '">'); ?>
													</td>
													<td>
														<input type="url" id="aths_dataicon][<?php echo( $key); ?>][url]" name="aths_data[icon][<?php echo( $key); ?>][url]" value="<?php echo isset($val['url']) ? $val['url'] : ''; ?>"/>
														<p class="description"><?php echo isset($val['desc']) ? $val['desc'] : ''; ?></p>
													</td>
												</tr>
												<?php } ?>
											<?php endforeach;?>

											<tr> <td>
												<h3>For Microsoft Tiles </h3>
											</td></tr>

											<?php
											foreach( $this->ops['tile_icon'] as $key => $val ) : ?>
												<?php if($val['desc'] != '') { ?>
												<tr>
													<td class="icon">
														<?php if( !empty( $val['url'] ) )
															echo( '<img src="' . $val['url'] . '">');
														else
															echo('<img src="' . plugins_url( '/images/' . $key . '.gif', __FILE__ ) . '">'); ?>
													</td>
													<td>
														<input type="url" id="aths_dataicon][<?php echo( $key); ?>][url]" name="aths_data[tile_icon][<?php echo( $key); ?>][url]" value="<?php echo isset($val['url']) ? $val['url'] : ''; ?>"/>
														<p class="description"><?php isset($val['desc']) ? $val['desc'] : ''; ?></p>
													</td>
												</tr>
												<?php } ?>
											<?php endforeach;?>


										</tbody>
									</table>
								</fieldset>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<h3 class="title">Bookmark Icon Settings</h3>
							</th>
							<td>
								<fieldset>
									<label for="show_touch_icon">
										<input type="checkbox" id="show_touch_icon" name="aths_data[show_touch_icon]" value="true" <?php if( $this->ops['show_touch_icon'] ): ?> checked="yes" <?php endif; ?> />
										<?php _e('Show Touch icon', 'adhs'); ?>
										<p class="description"><?php _e( 'If checked, the iOS bookmark icon above will display next to the message if available.' ); ?></p>
									</label>
									<label for="icon_precomposed">
										<input type="checkbox" id="icon_precomposed" name="aths_data[icon_precomposed]" value="true" <?php if( $this->ops['icon_precomposed'] ): ?> checked="yes" <?php endif; ?> />
										<?php _e('Precomposed icons', 'adhs'); ?>
										<p class="description"><?php _e('If checked, icons will display without the Apple gloss effect.', 'adhs'); ?></p>
									</label>
								</fieldset>
							</td>
						</tr>
					</tbody>
				</table>
				<p class="submit"><input type="submit" name="submit" value="Save Settings" class="button-primary"></p>
			</form>
		</div>
	<?php }
}