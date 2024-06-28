<?php
 /*
 * Plugin Name: TDC Custom Sharedaddy
 * Description: Customizes sharedaddy share emails
 * Author: Max Schlatter
 * Version: 1.0
*/


if ( !defined( 'ABSPATH' ) )
	die( 'Direct access not allowed.' );


$plugin_name = plugin_basename(__FILE__);
$plugin_dir_path = plugin_dir_path( __FILE__ );
$plugin_dir_url = plugin_dir_url( __FILE__ );

define( 'TDC_CUSTOM_SHAREDADDY_META_BASENAME', $plugin_name );
define( 'TDC_CUSTOM_SHAREDADDY_PLUGIN_DIR_PATH', $plugin_dir_path );
define( 'TDC_CUSTOM_SHAREDADDY_PLUGIN_DIR_URL', $plugin_dir_url );

add_action( 'init', 'tdc_custom_sharedaddy_init', 20 );

function tdc_custom_sharedaddy_init() {
    if (class_exists('Sharing_Source')) {

        if ( is_admin() ) {

			$a = new TDC_Custom_Sharedaddy(); 
			
        } else {
         remove_action('sharing_email_send_post','sharing_email_send_post');
         add_action('sharing_email_send_post','tdc_custom_sharedaddy_email' );
        }
    } else {
        add_action('admin_notices', 'jetpack_not_enabled_notice');
    }
}

function jetpack_not_enabled_notice() {
    ?>
    <div class="updated">
        <p><?php _e( 'TDC-Custom-Sharedaddy is enabled but Jetpack is not.', 'my-text-domain' ); ?></p>
    </div>
    <?php
}



class TDC_Custom_Sharedaddy {
	/*
	 * Class constructor
	 * Reference to parent is required
	 */
	public function __construct() {
		if (isset($_GET['save']) && $_GET['save']==1){$this->tdc_custom_sharedaddy_admin_update();}
		add_action('admin_menu', array(&$this,'create_plugin_options_page'));
		// Import CSS
		//add_action('admin_print_styles', array(&$this, 'enqueueStylesheet'));
	}
	
	public function create_plugin_options_page() {
		add_action('admin_init', array(&$this,'register_settings'));
		$page = add_options_page('TDC Custom Sharedaddy', 'TDC Custom Sharedaddy', 'administrator', 'tdc-custom-sharedaddy', array(&$this,'build_options_page'));
	}
	
	public function enqueueStylesheet(){
		wp_register_style('tdc_custom_sharedaddy_admin_styles', plugins_url('/admin.css', __FILE__));
		wp_enqueue_style('tdc_custom_sharedaddy_admin_styles');
	}
	public function register_settings() {
		//register_setting('tdc_custom_sharedaddy_data', 'tdc_custom_sharedaddy_data');		
		register_setting('tdc_custom_sharedaddy_data','source_email');
		register_setting('tdc_custom_sharedaddy_data','source_label');
		register_setting('tdc_custom_sharedaddy_data','user_entered_name');
		register_setting('tdc_custom_sharedaddy_data','title');
		register_setting('tdc_custom_sharedaddy_data','title_tag');
		register_setting('tdc_custom_sharedaddy_data','no_tag');
		register_setting('tdc_custom_sharedaddy_data','message');

		add_settings_section( 'section-one','Adjust Sharedaddy (Post Sharing) Email Functionality',array(&$this,'section_one_callback'), 'tdc-custom-sharedaddy' );
		add_settings_field('source_email', 'Source Email', array(&$this,'field_one_callback'),'tdc-custom-sharedaddy','section-one');
		add_settings_field('source_label', 'Source Label', array(&$this,'source_label_callback'),'tdc-custom-sharedaddy','section-one');
		add_settings_field('user_entered_name', '', array(&$this,'user_entered_name_callback'),'tdc-custom-sharedaddy','section-one');
		add_settings_field('title', 'Title', array(&$this,'title_callback'),'tdc-custom-sharedaddy','section-one');
		add_settings_field('title_tag', 'Title Tag', array(&$this,'title_tag_callback'),'tdc-custom-sharedaddy','section-one');
		add_settings_field('no_tag', '', array(&$this,'no_tag_callback'),'tdc-custom-sharedaddy','section-one');
		add_settings_field('message', 'Message', array(&$this,'message_callback'),'tdc-custom-sharedaddy','section-one');
	}
	
	function section_one_callback(){
			?>
				<img <?php echo('src="'.TDC_CUSTOM_SHAREDADDY_PLUGIN_DIR_URL.'/images/sharedaddy_bar.png"'); ?>></img>
				</br><img <?php echo('src="'.TDC_CUSTOM_SHAREDADDY_PLUGIN_DIR_URL.'/images/email_share_example.png"'); ?>></img> 
			<?php 
		}
		
	function field_one_callback(){
		$setting = esc_attr(get_option('source_email'));
		echo '<input type="email" name="source_email" value = '.$setting.'>';
		echo '<tr><td></td><td><span id="email_share_message_codes"><small><em>Leave blank for user specified email source.</em></small></span></td></tr>';
	}
	function source_label_callback(){
		$setting = esc_attr(get_option('source_label'));
		echo '<input type="text" name="source_label" value = '.$setting.'>';
	}

	function user_entered_name_callback(){
		$setting = esc_attr(get_option('user_entered_name'));
		if($setting){
			echo '<input type="checkbox" name="user_entered_name" value="1" checked>  Use user entered name as source label';
		}else{
			echo '<input type="checkbox" name="user_entered_name" value="1" />  Use user entered name as source label';	
		}
		echo '</br><span id="email_share_message_codes"><small><em>Leave blank and unchecked to use the Blog Name as the email Label.</em></small></span>';
	}
	function title_callback(){
		$setting = esc_attr(get_option('title'));
		echo '<input type="text" name="title" value = '.$setting.'>';
		echo '</br><span id="email_share_message_codes"><small><em>Leave blank to use the post title as the email name.</em></small></span>';
	}
	function title_tag_callback(){
		$setting = esc_attr(get_option('title_tag'));
		if($setting){
			echo '<input type="text" name="title_tag" value = '.$setting.'>';
		}else{
			echo '<input type="text" name="title_tag" value = '.'[Shared&nbsp;Post]'.'>';
		}
	}
	function no_tag_callback(){
		$setting = esc_attr(get_option('no_tag'));
		if($setting ){
			echo '<input type="checkbox" name="no_tag" value="1" checked> Use no tag';
		}else{
			echo '<input type="checkbox" name="no_tag" value="1" > Use no tag';
		}
	}
	function message_callback(){
		$setting = esc_attr(get_option('message'));
		if($setting){
		echo '<textarea style="vertical-align:top;width:300px;height:100px;"  name="message" value>'.$setting.'</textarea>';
		}else{
		echo '<textarea style="vertical-align:top;width:300px;height:100px;"  name="message" value>'.'~user_name~ (~user_email~) thinks you may be interested in the following post:
		~post_title~
		~post_link~'.'</textarea>';
		}
		
		echo '</br><span id="email_share_message_codes"><small><em><strong>Codes: &nbsp;&nbsp~user_name~&nbsp;&nbsp ~user_email~&nbsp;&nbsp ~post_title~&nbsp;&nbsp ~post_link~&nbsp;&nbsp ~blog_name~&nbsp;&nbsp</strong> </em></small></span>';
		echo '</br><span id="email_share_message_codes"><small><em>Codes include data from the post, blog, and user in the email body. </em></small></span>';
		echo '</br><span id="email_share_message_codes"><small><em><strong>Make sure to inlcude ~post_link~ or the email will not link to your story.</strong> </em></small></span>';
		echo '</br><span id="email_share_message_codes"><small><em>Example: "Hi I am ~blog_name~" &nbsp;&nbsp; sends &nbsp;&nbsp; "Hi I am Massachussets Lawyers Weekly".</em></small></span>';
	    echo '</br><span id="email_share_message_codes"><small><em><strong>Leave blank for the default email.</strong></em></small></span>';	
	}
	
	function build_options_page() {
	?>
		<form action="options.php" method="POST">
			<?php settings_fields('tdc_custom_sharedaddy_data'); ?>
			<?php do_settings_sections('tdc-custom-sharedaddy'); ?>
			<?php submit_button(); ?>
		</form>
	<?php
	}

}

function tdc_custom_sharedaddy_email( $data) {
		/*debugging*/
		//echo ('<script type="text/javascript">alert("foo");</script>');
		//wp_mail( $data['target'], '['.__( 'Shared Post', 'jetpack' ).'] '.$data['post']->post_title, 'hi', 'From: "max" <max@max.com>' );
		
		$email_share_option_values = get_option( 'tdc_custom_sharedaddy_data', array());
		//echo ('<script type="text/javascript">alert("'.var_dump($data).'");</script>');
		
		//Cycle through options for the sender (email source)
		$setting = esc_attr(get_option('source_email'));
			if($setting != ''){
				$sender = $setting;
			}else{
				$sender = $data['source'];
			}
		
		//Cycle through options for the sender label(email label)	
        $setting = esc_attr(get_option('source_label'));
		$setting2 = esc_attr(get_option('use_enetered_name'));
			if($setting2 == 1){
				$sender_label = $data['name'];
			}elseif($setting != ''){
				$sender_label = $setting;
			}
			else{
			    $sender_label = wp_specialchars_decode(get_bloginfo('name'));
			}
			
		//Cycle through options for the email title
		$setting = esc_attr(get_option('title'));
			if($setting != ''){
				$email_title = $setting;
			}
			else{
			    $email_title = wp_specialchars_decode($data['post']->post_title);
			}		
			
		//Cycle through options for the tag(email tag)
		$setting = esc_attr(get_option('title_tag'));
		$setting2 = esc_attr(get_option('no_tag'));
			if($setting2 == 1){
				$title_tag = '';
			}elseif($setting != ''){
				$title_tag = $setting;
			}
			else{
			    $title_tag='['.__( 'Shared Post', 'jetpack' ).']';
			}
		
		//Cycle through options for the message (email body)
		$setting = esc_attr(get_option('message'));
		if($setting != ''){
			$new_content = str_replace ( '~user_email~' , $data['name'] , $setting);
			$new_content = str_replace ( '~user_name~' , $data['source'] , $new_content);
			$new_content = str_replace ( '~post_title~' , $data['post']->post_title , $new_content);
			$new_content = str_replace ( '~post_link~' , get_permalink( $data['post']->ID ) , $new_content);
			$content=$new_content;
		}
		else{	
			$content  = sprintf( __( '%1$s (%2$s) thinks you may be %3$s interested in the following post:'."\n\n", 'jetpack' ), $data['name'], $data['source'],$user);
			$content .= $data['post']->post_title."\n";
			$content .= get_permalink( $data['post']->ID )."\n";
		}	

		wp_mail( $data['target'], $title_tag.$email_title, $content, 'From: "'.$sender_label.'" <'.$sender.'>' );

}
