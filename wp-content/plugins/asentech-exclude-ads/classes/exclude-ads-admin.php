<?php
if (!defined('ABSPATH'))
    die('Direct access not allowed.');

$exclude_ad = new excludeAds();

class excludeAds {

    function excludeAds() {
		add_action('admin_menu', array(&$this, 'create_plugin_options_page'));
    }

    function create_plugin_options_page() {
		add_action('admin_init', array(&$this, 'register_settings'));
		add_options_page('Asentech Ad Exclude List', 'Welcome Ad Exclude List', 'manage_options', 'ad-exclusion', array(&$this, 'build_options_page_exclusion'));
    }

    /*
     * Register & set up plugin options page via settings API
     */

    function register_settings() {
        register_setting('ad-exclusion', 'ad-exclusion', array(&$this, 'validate_options_exclusion'));
    }

     function sanitize_rejected_url($text) {
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
	
    function build_options_page_exclusion(){
        wp_enqueue_style('thickbox');
        wp_enqueue_style('jquery-ui-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
        wp_enqueue_media();
        wp_enqueue_script('media-grid');
        wp_enqueue_script('media');
        wp_enqueue_script('thickbox');
        
        ?>
        <div class="wrap">
        <h2>Welcome Ad's Exclusion List</h2>
        <form method="post" action="options.php" enctype="multipart/form-data">
        <?php settings_fields('ad-exclusion'); ?>
        <?php $options_exclusions = get_option('ad-exclusion');

       /*  $this->ad_edit_rejected_pages();
        echo "\n"; */
        $this->ad_edit_rejected();
        echo "\n";
        ?>
        </form>
        </div>
        <?php
    }
    
      /* function ad_edit_rejected_pages() {
        
        $options_exclusions = get_option('ad-exclusion');

        $pagetype = $options_exclusions['page_type'];
        $option_arr = array('pages','frontpage','home','search','feed','archives','author');
        echo '<p>' . __( 'Do not show welcome ad\'s on the following page types.', 'sidebar_ad' ) . '</p>';
        
        echo '<select name="ad-exclusion[page_type][]" multiple size="6" > ' . __( 'Select Page Type', 'sidebar_ad' ) . '<br />';

           

        foreach ($option_arr  as $option_arr_val) {
                if ( in_array($option_arr_val, $pagetype) )
                   $str_flag = "selected";
                else 
                    $str_flag="";
            
                echo '<option value="'.$option_arr_val.'" '.$str_flag.'>'. ucfirst($option_arr_val) .'</option>';
        }

        echo '</select>';

        echo '<div class="submit"><input class="button-primary" type="submit"  value="' . __( 'Save Settings', 'sidebar_ad' ) . '" /></div>';
        
     } */

	function ad_edit_rejected(){
		$options_exclusions = get_option('ad-exclusion');
		echo "<p>" . __( 'Add here strings (not a filename) that forces a page not to be display interstitial ad\'s. For example, if your URLs include year and you dont want to display interstitial ad\'s on that last year posts, it&#8217;s enough to specify the year, i.e. &#8217;/2004/&#8217;. This Plugin will search if that string is part of the URI and if so, it will not display interstitial ad\'s that page.', 'sidebar_ad' ) . "</p>\n";
		echo '<textarea name="ad-exclusion[wp_rejected_uri]" cols="40" rows="4" style="width: 50%; font-size: 12px;" class="code">';
		echo $options_exclusions['wp_rejected_uri'];
		echo '</textarea> ';
		echo '<div class="submit"><input class="button-primary" type="submit" value="' . __( 'Save Strings', 'sidebar_ad' ) . '" /></div>';
	}
}