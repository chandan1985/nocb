<?php
if (!defined('ABSPATH'))
    die('Direct access not allowed.');

$sidebar_ad = new Daily_Alert_Admin();

class Daily_Alert_Admin {

    // Build class & set up action based on page type
    function __construct() {
        if (is_admin()) {
            add_action('admin_menu', array(&$this, 'create_plugin_options_page'));
        }
    }


    /*
     * Add 'DFP Ad' to the settings menu if current user has a dolanmedia or thedolancompany email
     */

    function create_plugin_options_page() {
        //global $current_user;
        //preg_match("/.*\@(.*)\.com/", $current_user->user_email, $matches);
        //if($matches[1] == 'thedolancompany' || $matches[1] == 'dolanmedia')
        //{
            add_action('admin_init', array(&$this, 'register_settings'));
            add_options_page('Asentech Daily Alert', 'Daily Alert', 'manage_options', 'daily-alert', array(&$this, 'build_options_page'));       // }
    }



    /*
     * Register & set up plugin options page via settings API
     */

    function register_settings() {
        register_setting('daily-alert', 'daily-alert', array(&$this, 'validate_options'));
    }


    /*
     * Sanitize and validate input. Accepts an array, return a sanitized array.
     */

    function validate_options($input) {
        // Reset options to default if invalid values specified
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
    
    ?>
        <div class="wrap">
            <h2>Daily Alert Settings</h2>
            <form method="post" action="options.php" enctype="multipart/form-data">
        <?php settings_fields('daily-alert'); ?>
        <?php $options = get_option('daily-alert'); //print_r($options);exit; ?>     

                <div id="dfp">
                  
                    <table class="form-table lightbox">
                        <tr valign="top">
                            <th scope="row"><?php _e('BlueSky ShortCode:') ?></th>
                            <td>
                                <input id="act-on" type="text" size="50" name="daily-alert[act_on]" value="<?php echo (isset($options['act_on'])) ? $options['act_on'] : ''; ?>">
                                <p style="font-size: 11px; font-style: italic; margin: 3px 0 0;"><?php _e('Example: 5') ?></p>
                            </td>
                        </tr> 

						<tr valign="top">
                            <th scope="row"><?php _e('Left Text/HTML:') ?></th>
                            <td>
                                <textarea id="left_txt" rows="10" cols="80" name="daily-alert[left_txt]" value="" /> <?php echo (isset($options['left_txt'])) ? $options['left_txt'] : ''; ?> </textarea>
								<p style="font-size: 11px; font-style: italic; margin: 3px 0 0;"><!-- [one_half]<h2>SIGN UP FOR OUR FREE DAILY ALERTS</h2><p>Never miss a beat! New Orleans' source for local business news, analysis, events and more.</p><p>Get the latest news delivered right to your inbox.</p>[/one_half] --></p>
                            </td>
                        </tr> 
						
						<tr valign="top">
                            <th scope="row"><?php _e('Right Text/HTML:') ?></th>
                            <td>
                                <textarea id="left_txt" rows="10" cols="80" name="daily-alert[right_txt]" value="" /> <?php echo (isset($options['right_txt'])) ? $options['right_txt'] : ''; ?> </textarea>
								<p style="font-size: 11px; font-style: italic; margin: 3px 0 0;"><!-- Sample: [one_half_last][bluesky_form id="5"]<p style="text-align: center;margin-left: 20%;margin-right: 20%;">Signing up for our newsletters indicates you agree with our Privacy Policy and Subscriber Agreement </p>[/one_half_last] --></p>
                            </td>
                        </tr> 
                       
                    </table>
                </div>             
               
                <p class="submit">
                    <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
                </p>
            </form>
        </div>
        <?php
    }
    
}