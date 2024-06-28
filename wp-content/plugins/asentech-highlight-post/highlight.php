<?php
/*
Plugin Name: Asentech Highlight Post on Homepage
Description: A plugin to highlight post on homepage.
Author: Asentech 
Author URI: http://www.asentechllc.com
Version:1.0
*/

if (!defined('ABSPATH')) {
    die();
}

final class Asentech_Highlight_Post {

    public function __construct() {
    	if ( is_admin() ) {
          add_action('admin_init', array(&$this, 'register_settings'));
          add_action('admin_menu', array(&$this, 'Highlight_Post_plugin_menu'));
          add_action('admin_enqueue_scripts', array( $this, 'enqueue_admin_js' ) );
    	}
    	else {
          add_action( 'wp', array( &$this, 'highlight_post' ) );    	  	
          add_action( 'wp_enqueue_scripts',  array( $this, 'enqueue_css' ) );
        }
    }
    
    public function enqueue_admin_js() { 
    // Make sure to add the wp-color-picker dependecy to js file
    wp_enqueue_script( 'cpa_custom_js', plugins_url( 'jquery.custom.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ), '', true  ); 
	}

	function enqueue_css() {
    $plugin_url = plugin_dir_url( __FILE__ );
    wp_enqueue_style( 'cpa_custom', $plugin_url . 'cpa_custom.css' );
    }

    public function Highlight_Post_plugin_menu() {
        add_options_page('Highight Post Settings', 'Highight Post Settings', 'manage_options', 'highlight_post_settings', array(&$this, 'highlight_post_settings_page') );
    }

    public function register_settings() {
        register_setting('highlight_post_details', 'highlight_post_details', array(&$this, 'validate_options'));
    }

    public function validate_options($input) {
        return $input;
    }

    public function highlight_post_settings_page() {
    	$table_name = $wpdb->prefix . 'options';
        $my_fields = get_option('highlight_post_details');
        ?>
	<div class="main-settings-div">  
	 <form action="options.php" enctype="multipart/form-data" method="post" class="detail_form">  
          <?php settings_fields('highlight_post_details'); ?>

           <div ><h1>Highlight Settings</h1></div>
           <div class="hidden_div" style="display: none;">ACF Field Name: <strong>highlight_this_post_on_homepage</strong></div>
           <table> 
             <tbody> 
              <tr class="form-field">
                <td colspan="2">
                <table cellspacing="2" cellpadding="5" style=""  class="form-table form-field_white">
                    <tbody>
                        <tr class="form-field">
                            <th valign="top" scope="row">
                                <label for="btm_acton_endpoint" class="lable_font_size"><?php _e('Select color from color picker', 'highlight_post_details'); ?></label>
                            </th>
                            <td>
                                <input type="text" class="jscolor" name="highlight_post_details[highlight_color]" pattern="^([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$" value="<?php if (!empty($my_fields['highlight_color'])) {
                                    echo $my_fields['highlight_color']; } ?>" /> 
                            </td>
                        </tr>

                        <tr class="form-field">
		                    <th valign="top" scope="row"></th>
		                    <td><input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" /></td>
		                </tr>  
                    </tbody>
                </table>	
            </form>
         </div>
    <?php
    }

    public function is_blog () {
     return ( is_home() || is_front_page() );
    }

    public function highlight_post() {
        if ( $this->is_blog() ) {

         add_filter( 'the_title', array( &$this, 'the_title' ), 11, 2 );
          //Remove from menu
         add_filter( 'pre_wp_nav_menu', array( &$this, 'remove_title_filter_nav_menu' ), 11, 2 ); 
         add_filter( 'wp_nav_menu_items', array( &$this, 'add_title_filter_non_menu' ), 11, 2 ); 
        }
    } 

    public function remove_title_filter_nav_menu( $nav_menu, $args ) {
    // we are working with menu, so remove the title filter
	    remove_filter( 'the_title', array( &$this, 'the_title' ), 11, 2 );
	    return $nav_menu;
	}
	// this filter fires just before the nav menu item creation process

	public function add_title_filter_non_menu( $items, $args ) {
	    // we are done working with menu, so add the title filter back
	    add_filter( 'the_title', array( &$this, 'the_title' ), 11, 2 );
	    return $items;
	}
	// this filter fires after nav menu item creation is done
    
    public function the_title ( $input, $post_id = '' ) {
    	$my_post = get_post( $post_id );
    	$get_field_value = get_field('highlight_this_post_on_homepage',$post_id);
    	if ($get_field_value == 'yes') {
    		if( 'nav_menu_item' == $my_post->post_type || ( preg_match( '<span class="highlightcolor">', $input ) ) || did_action( 'get_footer' ) ) {
               return $input;
            }
         return $this->title_lock_icon( $input );
    	}
     return $input;     
    }

    public function title_lock_icon( $title ) {
     $colour = get_option('highlight_post_details');
     $colour_value = $colour['highlight_color'];
     //return '<span class="highlightcolor" style="background:#'.$colour_value.'">'.$title.'</span>';
	 return $title ;
    }

} // end of class
// Instantiate the class
new Asentech_Highlight_Post();