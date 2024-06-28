<?php
if (!defined('ABSPATH'))
    die('Direct access not allowed.');

$interstitial_ad = new interstitialAdsAdmin();

class interstitialAdsAdmin {

    // Build class & set up action based on page type
    function __construct() {
        if (is_admin()) {
            add_action('admin_menu', array(&$this, 'create_plugin_options_page'));
        } else
            add_action('wp_head', array(&$this, 'insert_invocation_code'));
    }

    // Generate DFP Invocation code and insert into site header
    function insert_invocation_code() {
        $options = get_option('interstitial-ad');
    }

    /*
     * Add 'DFP Ad' to the settings menu if current user has a dolanmedia or thedolancompany email
     */

    function create_plugin_options_page() {
        global $current_user;
        preg_match("/.*\@(.*)\.com/", $current_user->user_email, $matches);
        //if($matches[1] == 'thedolancompany' || $matches[1] == 'dolanmedia')
        {
            add_action('admin_init', array(&$this, 'register_settings'));
            add_options_page('Asentech DFP Ad', 'DFP Ad', 'manage_options', 'interstitial-ad', array(&$this, 'build_options_page'));
			add_options_page('Asentech DFP Ad Exclude List', 'DFP Ad Exclude List', 'manage_options', 'interstitial-ad-exclusion', array(&$this, 'build_options_page_exclusion'));
        }
    }

    function arthur_image_uploader($optionName, $fieldname) {

        // Set variables
        $options = get_option($fieldname);
        $default_image = 'https://www.placehold.it/800x600';

        if (isset($options[$optionName]) && !empty($options[$optionName])) {
            $image_attributes = wp_get_attachment_image_src($options[$optionName], 'full');
            $src = $image_attributes[0];
            $value = $options[$optionName];
        } else {
            $src = $default_image;
            $value = '';
        }

        // Print HTML field
        echo '
        <div class="upload" style="max-width:400px;">
            <img data-src="' . $default_image . '" src="' . $src . '" style="max-width:100%; height:auto;" />
            <div>
				<input type="hidden" name="' . $fieldname . '[' . $optionName . ']" id="' . $optionName . '" value="' . $value . '" />
                <button type="button" id="image-upload-button" class="upload_image_button button">' . __('Upload', 'igsosd') . '</button>
                <button type="button" class="remove_image_button button">&times;</button>
            </div>
        </div>
    ';
    }

    /*
     * Register & set up plugin options page via settings API
     */

    function register_settings() {
        register_setting('interstitial-ad', 'interstitial-ad', array(&$this, 'validate_options'));
	register_setting('interstitial-ad-exclusion', 'interstitial-ad-exclusion', array(&$this, 'validate_options_exclusion'));
    }
     
	 function sanitize_rejected_url($text)
	{
		$text = str_replace( '\\\\', '\\', $text );
		$text = esc_html(strip_tags($text));
		//~ $text = preg_replace('/[\s]+/', ' ', $text);
		return $text;
	}
	function validate_options_exclusion($input) {
		
		if(isset($input['wp_rejected_uri']))
			$input['wp_rejected_uri'] = $this->sanitize_rejected_url($input['wp_rejected_uri']);
		
        // Reset options to default if invalid values specified
		return $input;
		
	}
    /*
     * Sanitize and validate input. Accepts an array, return a sanitized array.
     */

    function validate_options($input) {
        // Reset options to default if invalid values specified
		return $input;
        if (empty($input['dfp_server_url']))
            $input['enabled'] = '';
        elseif (!is_numeric($input['zone_id'])) {
            $input['zone_id'] = '';
            $input['enabled'] = '';
        }
		elseif (!is_numeric($input['mobile_zone_id'])) {
            $input['mobile_zone_id'] = '';
            $input['enabled'] = '';
        }      
        if (empty($input['home']))
            $input['home'] = 0;
        if (empty($input['cookie']))
            $input['cookie'] = 0;
        if (!is_numeric($input['probability']) || $input['probability'] < 1 || $input['probability'] > 100) {
            $input['probability'] = '50';
        }
        // Verify date & time formatted correctly
        $date_pattern = '/(^[1-9][- \/\.]|^0[1-9][- \/\.]|^1[012][- \/\.])([1-9][- \/\.]|0[1-9][- \/\.]|[12][0-9][- \/\.]|3[01][- \/\.])(201[2-9]$)/';
        $time_pattern = '/(^[1-9]:|^0[1-9]:|^1[012]:)([0-5]\d)\s*(([a|p]m$)|$)/i';
        $date_test = preg_match($date_pattern, $input['active_date']);
        $time_test = preg_match($date_pattern, $input['active_time']);
        // Erase both if date invalid or empty
        if (!$date_test) {
            $input['active_date'] = '';
            $input['active_time'] = '';
        } else
        // Default time to midnight if invalid or empty
        if (!$time_test)
            $input['active_time'] = '';
        return $input;
    }

    /*
     * Build basic theme options page via settings API
     */

    function build_options_page() {
        wp_enqueue_style('thickbox');
        wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
        wp_enqueue_media();
        wp_enqueue_script('media-grid');
        wp_enqueue_script('media');
        wp_enqueue_script('thickbox');
        wp_register_script('interstitial-ad-script', plugins_url('../js/admin-script.js', __FILE__), array('jquery', 'media-upload', 'thickbox'));
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('interstitial-ad-script');

	
	?>
        <div class="wrap">
            <h2>DFP Ad Settings</h2>
            <form method="post" action="options.php" enctype="multipart/form-data">
        <?php settings_fields('interstitial-ad'); ?>
        <?php $options = get_option('interstitial-ad'); //print_r($options);exit; ?>		
                <?php if (empty($options['zone_id']) && empty($options['dfp_server_url'])): ?>
                    <div id="message" class="error fade">
                        <p>
                            Missing Zone ID or DFP Ad URL; DFP Ad is disabled.
                        </p>
                    </div>
        <?php elseif (empty($options['enabled'])): ?>
                    <div id="message" class="error fade">
                        <p>
                            DFP Ad is disabled.
                        </p>
                    </div>
        <?php endif; ?>
                <table class="form-table lightbox">
                    <tr>
                    <div class="option">
                        <input type="radio" name="interstitial-ad[enabled]" value="1" <?php if ($options['enabled']) echo 'checked="checked"'; ?> />
						 <label for="interstitial-ad[enabled]"><?php _e('Enable') ?></label>
                        <input type="radio" name="interstitial-ad[enabled]" value="0" <?php if (!$options['enabled']) echo 'checked="checked"'; ?> />
                        <label for="interstitial-ad[enabled]"><?php _e('Disable Interstitial Ad') ?></label>
                    </div>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Ad Type:') ?></th>
                        <td>
                            <select name="interstitial-ad[adtype]" id="adtype">
                                <option value="dfp" <?php if ($options['adtype'] == 'dfp') echo 'selected="1"'; ?>>DFP</option>
                                <option value="openx" <?php if ($options['adtype'] == 'openx') echo 'selected="1"'; ?>>Open-X / Revive</option>

                            </select>
                        </td>
                    </tr>		
                </table>

                <div id="dfp"  <?php if ($options['adtype'] && $options['adtype'] != 'dfp') echo 'style="display:none;"'; ?>>
                    <h3>DFP ad Settings</h3>
                    <table class="form-table lightbox">
                        <tr valign="top">
                            <th scope="row"><?php _e('Ad Server URL:') ?></th>
                            <td>
                                <input id="server-url" type="text" size="50" name="interstitial-ad[dfp_server_url]" value="<?php echo (isset($options['dfp_server_url'])) ? $options['dfp_server_url'] : ''; ?>">
                                <p style="font-size: 11px; font-style: italic; margin: 3px 0 0;"><?php _e('Example: /1008536/Leaderboard_Icon_Awards') ?></p>
                            </td>
                        </tr>					
                        <tr valign="top">
                            <th scope="row"><?php _e('Height:') ?></th>
                            <td>
                                <input type="text" size="3" name="interstitial-ad[height]" style="text-align: right;" value="<?php echo (isset($options['height'])) ? $options['height'] : ''; ?>">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Width:') ?></th>
                            <td>
                                <input type="text" size="3" name="interstitial-ad[width]" style="text-align: right;" value="<?php echo (isset($options['width'])) ? $options['width'] : ''; ?>">
                            </td>
                        </tr>
                    </table>
                </div>
                <div id="openx" <?php if ($options['adtype'] != 'openx') echo 'style="display:none;"'; ?>>
                    <h3>Open X/Revive Settings</h3>
                    <table class="form-table lightbox">
                        <tr valign="top">
                            <th scope="row"><?php _e('Ad Server URL:') ?></th>
                            <td>
                                <input id="server-url" type="text" size="50" name="interstitial-ad[server_url]" value="<?php echo($options['server_url']); ?>">
                                <p style="font-size: 11px; font-style: italic; margin: 3px 0 0;"><?php _e('Example: ad1.dolanadserver.com/account/www/delivery') ?></p>
                            </td>
                        </tr>					
                        <tr valign="top">
                            <th scope="row"><?php _e('Zone ID:') ?></th>
                            <td>
                                <input type="text" size="3" name="interstitial-ad[zone_id]" style="text-align: right;" value="<?php echo($options['zone_id']); ?>">
                            </td>
                        </tr>
						<tr valign="top">
						<th scope="row"><?php _e('Mobile Zone ID:') ?></th>
						<td>
						<input type="text" size="3" name="interstitial-ad[mobile_zone_id]" style="text-align: right;" value="<?php echo($options['mobile_zone_id']); ?>">
						</td>
						</tr>
                    </table>
                </div>

                <table class="form-table lightbox">
                    <!--tr valign="top">
                            <th scope="row"><?php _e('Description:') ?></th>
                            <td>
                            
                            
        <?php /*
          if(isset($options['desc'])&& $options['desc']!=''):
          $content = $options['desc'];
          wp_editor( $content, 'desc', $settings = array('textarea_rows'=> '10','textarea_name'=>'interstitial-ad[desc]') );
          else:
          wp_editor( '', 'desc', $settings = array('textarea_rows'=> '10','textarea_name'=>'interstitial-ad[desc]') );
          endif; */ ?> 
                            </td>
                    </tr>


                    <tr valign="top">
                            <th scope="row"><?php _e('Horizontal Alignment:') ?></th>
                            <td>
                                    <select name="interstitial-ad[halign]" style="width:70px;">
                                            <option value="center" <?php if ($options['halign'] == 'center') echo 'selected="1"'; ?>>Center</option>
                                            <option value="left" <?php if ($options['halign'] == 'left') echo 'selected="1"'; ?>>Left</option>
                                            <option value="right" <?php if ($options['halign'] == 'right') echo 'selected="1"'; ?>>Right</option>
                                    </select>
                            </td>
                    </tr-->					
                    <tr valign="top">
                        <th scope="row"><?php _e('Vertical Alignment:') ?></th>
                        <td>
                            <select name="interstitial-ad[valign]" >
                                <option value="middle" <?php if ($options['valign'] == 'middle') echo 'selected="1"'; ?>>Middle</option>

                                <option value="bottom" <?php if ($options['valign'] == 'bottom') echo 'selected="1"'; ?>>Bottom</option>
                            </select>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e('Background Image:') ?></th>
                        <td>
        <?php $this->arthur_image_uploader('custom_image', 'interstitial-ad', 800, 600); ?>
                            <p style="font-size: 11px; font-style: italic; margin: 3px 0 0;"><?php _e('Min 800x600 image') ?></p>
                        </td>
                    </tr>
                </table>

                <h3>Delayed Activation</h3>
                <p>Activation of DFP Ad may be delayed using the fields below.  Keep in mind that any time values are <strong>local time</strong> for the site</p>
                <table class="form-table lightbox">
                    <tr valign="top">
                        <th scope="row"><?php _e('Number of Times') ?></th>
                        <td>
                            <input id="num_of_times" class="" type="text" size="10" name="interstitial-ad[num_of_times]" value="<?php echo($options['num_of_times']); ?>">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Activation Date') ?></th>
                        <td>
                            <input id="active-date" class="custom_date" type="text" size="10" name="interstitial-ad[active_date]" value="<?php echo($options['active_date']); ?>">
                            <p style="font-size: 11px; font-style: italic; margin: 3px 0 0;"><?php _e('MM/DD/YYYY format: 12/31/2011 (ignored if empty)') ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('End Date') ?></th>
                        <td>
                            <input id="active-time" type="text"  class="custom_date" size="10" name="interstitial-ad[active_time]" value="<?php echo($options['active_time']); ?>">
                            <p style="font-size: 11px; font-style: italic; margin: 3px 0 0;"><?php _e('MM/DD/YYYY format: 12/31/2011 (ignored if empty)') ?></p>							
                        </td>
                    </tr>
					
					<tr valign="top">
                        <th scope="row"><?php _e('Redirect Duration') ?></th>
                        <td>
                            <input id="redirect_time" class="" type="text" size="10" name="interstitial-ad[redirect_time]" value="<?php echo($options['redirect_time']); ?>">  
                              <p style="font-size: 11px; font-style: italic; margin: 3px 0 0;"><?php _e('Duration for redirecting the user back to website (Default is 20 sec)') ?></p>                            
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
                </p>
            </form>
        </div>
        <?php
    }
	
	function build_options_page_exclusion(){
		wp_enqueue_style('thickbox');
        wp_enqueue_style('jquery-ui-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
        wp_enqueue_media();
        wp_enqueue_script('media-grid');
        wp_enqueue_script('media');
        wp_enqueue_script('thickbox');
        
        ?>
        <div class="wrap">
		<h2>Interstitial Ad's Exclusion List</h2>
		<form method="post" action="options.php" enctype="multipart/form-data">
		<?php settings_fields('interstitial-ad-exclusion'); ?>
		<?php $options_exclusions = get_option('interstitial-ad-exclusion');

        $this->interstitial_ad_edit_rejected_pages();
		echo "\n";
		$this->interstitial_ad_edit_rejected();
		echo "\n";
		?>
		</form>
		</div>
		<?php
	}
	
	  function interstitial_ad_edit_rejected_pages() {
		
		$options_exclusions = get_option('interstitial-ad-exclusion');

        /*saquib comment
        $post_types = get_post_types( array( 'public' => true), 'names', 'and' );
        // remove attachment from the list
         unset( $post_types['attachment'] );
         unset( $post_types['post'] );

        $arr = $options_exclusions['post_type'];
	    echo '<p>' . __( 'Do not show interstitial ad\'s on the following page types.', 'interstitial_ads' ) . '</p>';
		
        echo '<select name="interstitial-ad-exclusion[post_type][]" multiple size="6" > ' . __( 'Select Post Type', 'interstitial_ads' ) . '<br />';

        foreach ($post_types  as $post_type) {
            if ( in_array($post_type, $arr) )
               $str_flag = "selected";
            else 
                $str_flag="";
            
        echo '<option value="'.$post_type.'" '.$str_flag.'>'. ucfirst($post_type) .'</option>';
        }

        echo '</select>';
        */

        $pagetype = $options_exclusions['page_type'];
        $option_arr = array('pages','frontpage','home','search','feed','archives','author');
        echo '<p>' . __( 'Do not show interstitial ad\'s on the following page types.', 'interstitial_ads' ) . '</p>';
        
        echo '<select name="interstitial-ad-exclusion[page_type][]" multiple size="6" > ' . __( 'Select Page Type', 'interstitial_ads' ) . '<br />';

           

        foreach ($option_arr  as $option_arr_val) {
                if ( in_array($option_arr_val, $pagetype) )
                   $str_flag = "selected";
                else 
                    $str_flag="";
            
                echo '<option value="'.$option_arr_val.'" '.$str_flag.'>'. ucfirst($option_arr_val) .'</option>';
        }

        echo '</select>';

		echo '<div class="submit"><input class="button-primary" type="submit"  value="' . __( 'Save Settings', 'interstitial_ads' ) . '" /></div>';
		
	 }

	
	/*saquib comment
     function interstitial_ad_edit_rejected_pages(){
		
		 $options_exclusions = get_option('interstitial-ad-exclusion');
	    echo '<p>' . __( 'Do not show interstitial ad\'s on the following page types.', 'interstitial_ads' ) . '</p>';
		
		echo '<label><input type="checkbox" value="1" name="interstitial-ad-exclusion[single]" ' . checked( 1, $options_exclusions[ 'single' ], false ) . ' /> ' . __( 'Single Posts', 'interstitial_ads' ) . ' (is_single)</label><br />';
		echo '<label><input type="checkbox" value="1" name="interstitial-ad-exclusion[pages]" ' . checked( 1, $options_exclusions[ 'pages' ], false ) . ' /> ' . __( 'Pages', 'interstitial_ads' ) . ' (is_page)</label><br />';
		echo '<label><input type="checkbox" value="1" name="interstitial-ad-exclusion[frontpage]" ' . checked( 1, $options_exclusions[ 'frontpage' ], false ) . ' /> ' . __( 'Front Page', 'interstitial_ads' ) . ' (is_front_page)</label><br />';
		echo '&nbsp;&nbsp;<label><input type="checkbox" value="1" name="interstitial-ad-exclusion[home]" ' . checked( 1, $options_exclusions[ 'home' ], false ) . ' /> ' . __( 'Home', 'interstitial_ads' ) . ' (is_home)</label><br />';
		echo '<label><input type="checkbox" value="1" name="interstitial-ad-exclusion[archives]" ' . checked( 1, $options_exclusions[ 'archives' ], false ) . ' /> ' . __( 'Archives', 'interstitial_ads' ) . ' (is_archive)</label><br />';
		echo '&nbsp;&nbsp;<label><input type="checkbox" value="1" name="interstitial-ad-exclusion[tag]" ' . checked( 1, $options_exclusions[ 'tag' ], false ) . ' /> ' . __( 'Tags', 'interstitial_ads' ) . ' (is_tag)</label><br />';
		echo '&nbsp;&nbsp;<label><input type="checkbox" value="1" name="interstitial-ad-exclusion[category]" ' . checked( 1, $options_exclusions[ 'category' ], false ) . ' /> ' . __( 'Category', 'interstitial_ads' ) . ' (is_category)</label><br />';
		echo '<label><input type="checkbox" value="1" name="interstitial-ad-exclusion[feed]" ' . checked( 1, $options_exclusions[ 'feed' ], false ) . ' /> ' . __( 'Feeds', 'interstitial_ads' ) . ' (is_feed)</label><br />';
		echo '<label><input type="checkbox" value="1" name="interstitial-ad-exclusion[search]" ' . checked( 1, $options_exclusions[ 'search' ], false ) . ' /> ' . __( 'Search Pages', 'interstitial_ads' ) . ' (is_search)</label><br />';
		echo '<label><input type="checkbox" value="1" name="interstitial-ad-exclusion[author]" ' . checked( 1, $options_exclusions[ 'author' ], false ) . ' /> ' . __( 'Author Pages', 'interstitial_ads' ) . ' (is_author)</label><br />';

		echo '<div class="submit"><input class="button-primary" type="submit"  value="' . __( 'Save Settings', 'interstitial_ads' ) . '" /></div>';
		
	 }
	*/
	
	

	 function interstitial_ad_edit_rejected(){
			$options_exclusions = get_option('interstitial-ad-exclusion');
			
		 	echo "<p>" . __( 'Add here strings (not a filename) that forces a page not to be display interstitial ad\'s. For example, if your URLs include year and you dont want to display interstitial ad\'s on that last year posts, it&#8217;s enough to specify the year, i.e. &#8217;/2004/&#8217;. This Plugin will search if that string is part of the URI and if so, it will not display interstitial ad\'s that page.', 'interstitial_ads' ) . "</p>\n";
			echo '<textarea name="interstitial-ad-exclusion[wp_rejected_uri]" cols="40" rows="4" style="width: 50%; font-size: 12px;" class="code">';
			echo $options_exclusions['wp_rejected_uri'];
			echo '</textarea> ';
			echo '<div class="submit"><input class="button-primary" type="submit" value="' . __( 'Save Strings', 'interstitial_ads' ) . '" /></div>';
	 }
}
