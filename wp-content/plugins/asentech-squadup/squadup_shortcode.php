<?php
/*
Plugin Name: SquadUp Integration Shortcode
Description: A plugin to add SquadUp shortcode.
Version: 1.0
Author: Asentech
Author URI: https://www.asentechllc.com
*/

class ASN_squadup_shortcode{
	/**
	 * $shortcode_tag 
	 * holds the name of the shortcode tag
	 * @var string
	 */
	public $shortcode_tag = 'squadup';

	/**
	 * __construct 
	 * class constructor will set the needed filter and action hooks
	 * 
	 * @param array $args 
	 */
	function __construct($args = array()){
		//add shortcode
		add_shortcode( $this->shortcode_tag, array( $this, 'shortcode_handler' ) );

		remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );
		
		if ( is_admin() ){
			add_action('admin_head', array( $this, 'admin_head') );
			add_action( 'admin_enqueue_scripts', array($this , 'admin_enqueue_scripts' ) );
		}
		else {
			add_action( 'init', array($this , 'init_fun' ) );
		}
	}

	function init_fun() {
 		//wp_enqueue_style('squad-up-stylesheet', plugins_url('/css/squadup.css', __FILE__ ));
 		// Adding shortcode
		add_shortcode( $this->shortcode_tag, array( $this, 'shortcode_handler' ) );
 	}
	
	/**
	 * shortcode_handler
	 * @param  array  $atts shortcode attributes
	 * @param  string $content shortcode content
	 * @return string
	 */
	function shortcode_handler($atts , $content = null) { 
		ob_start();?>
			<link rel="stylesheet" href="https://s3.amazonaws.com/checkout.squadup.com/default/css/bootstrap-namespace.min.css">
			<link rel="stylesheet" href="https://www.mediacutlet.com/btm/css/bootstrap-btm-squadup.css">
				<div id="squadup-checkout"></div>
				<script>
				    squadup = {
				        title: "<?php echo isset($atts['stitle']) ? $atts['stitle'] : ''; ?>",
				        image: "<?php echo isset($atts['simage']) ? $atts['simage'] : ''; ?>",
				        root: "squadup-checkout",
				        userId: [<?php echo isset($atts['suid']) ? $atts['suid'] : ''; ?>],
				        eventId: "<?php echo isset($atts['seid']) ? $atts['seid'] : ''; ?>",
				        confirmEmail: <?php echo isset($atts['semail']) ? $atts['semail'] : ''; ?>,
				        brandingPosition: "<?php echo isset($atts['sbrand']) ? $atts['sbrand'] : ''; ?>",
				        ticketGuardianEnabled: <?php echo isset($atts['sticket']) ? $atts['sticket'] : ''; ?>,
				        descriptionEnabled: <?php echo isset($atts['sdesc']) ? $atts['sdesc'] : ''; ?>,
				        shoppingCartEnabled: <?php echo isset($atts['scart']) ? $atts['scart'] : ''; ?>
				    }
				</script>
				<script src="https://s3.amazonaws.com/checkout.squadup.com/main-v2.min.js"></script>
	<?php
	     $output = ob_get_clean();
         return $output;
      } 

	/**
	 * admin_head
	 * calls your functions into the correct filters
	 * @return void
	 */
	function admin_head() {
		// check user permissions
		if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
			return;
		}
		
		// check if WYSIWYG is enabled
		if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', array( $this ,'mce_external_plugins' ) );
			add_filter( 'mce_buttons', array($this, 'mce_buttons' ) );
			add_filter( 'teeny_mce_buttons', array($this, 'mce_buttons' ) );
			
		}
	}

	/**
	 * mce_external_plugins 
	 * Adds our tinymce plugin
	 * @param  array $plugin_array 
	 * @return array
	 */
	function mce_external_plugins( $plugin_array ) {
		$plugin_array[$this->shortcode_tag] = plugins_url( 'js/mce-button.js' , __FILE__ );
		return $plugin_array;
	}

	/**
	 * mce_buttons 
	 * Adds our tinymce button
	 * @param  array $buttons 
	 * @return array
	 */
	function mce_buttons( $buttons ) {
		array_push( $buttons, $this->shortcode_tag );
		return $buttons;
	}

	/**
	 * admin_enqueue_scripts 
	 * Used to enqueue custom styles
	 * @return void
	 */
	function admin_enqueue_scripts(){
		 wp_enqueue_style('squadup_shortcode', plugins_url( 'css/mce-button.css' , __FILE__ ) );
	}
}//end class

new ASN_squadup_shortcode();