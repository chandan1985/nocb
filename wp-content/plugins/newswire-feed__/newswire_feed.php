<?php
/*
Plugin Name: Newswire Feeds
Description: A plugin to manage feed newswire option
Author: Asentech
Version: 0.1
*/


class NewswireFeed {
    function __construct() { 
		add_action( 'wpmueditblogaction', 'action_wpmueditblogaction', 10, 1 );
        // Don't run anything else in the plugin, if user is not an asentech employee
        if ( ! self::user_access() ) {
            return;
        }
    }

    static function activation_check() {
        if ( ! self::user_access() ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die( __( 'You are not authorised to activate this plugin!', 'newswire-feed' ) );
        }
    }

    function deactivation_check() {
    	if ( ! self::user_access() ) {
                if ( isset( $_GET['deactivate'] ) ) {
                    unset( $_GET['deactivate'] );
                }
            wp_die( __( 'You are not authorised to deactivate this plugin!', 'newswire-feed' ) );
        }
    }

    static function user_access() {
    		global $current_user;
            if(isset($current_user->user_email) && preg_match( '/asentech\.com|asentechllc\.com|thedolancompany\.com/', $current_user->user_email ) ) {
            	return true;
            }
        return false;
    }
}

		global $newswirefeed;
		$newswirefeed = new NewswireFeed();

		register_activation_hook( __FILE__, array( 'NewswireFeed', 'activation_check' ) );

		register_deactivation_hook( __FILE__, array( 'NewswireFeed', 'deactivation_check' ) );

		//Setting up the menu
		add_action('admin_menu', 'plugin_setup_menu');

		function plugin_setup_menu() {
					global $current_user;  
		             if( function_exists( 'add_menu_page' ) ) {
			            if( preg_match( '/asentech\.com|asentechllc\.com|thedolancompany\.com/', $current_user->user_email ) ) {
			                add_menu_page( 'Newswire Feed', 'Newswire Feed', 'administrator', 'newswire-feed',  'plugin_init' );
			             }  
		            }          
		     }

		 // define the wpmueditblogaction callback 
		function action_wpmueditblogaction( $id ) {  ?>
		   <script>
				 var input = document.getElementById('feed_newswire');
				 var text = document.createElement('code');
				 text.innerHTML = input.value;
				 input.parentNode.replaceChild(text, input);
		   </script>
		<?php } 

		 function add_admin_scripts( $hook ) {
		    global $post;
		    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
		    	    wp_register_script( 'recommended', plugin_dir_url( __FILE__ ).'recommended.js' , array( 'jquery' ), NULL, false );
			        wp_enqueue_script( 'recommended' );
		            newswire_own_it();		   
		      }
		   }
		 add_action( 'admin_enqueue_scripts', 'add_admin_scripts', 10, 1 );

		function newswire_own_it() { 
			global $current_user;
			if( preg_match( '/associated press|bloomberg/i', $current_user->display_name ) ) {	?>
 			<script>
 				window.onload = function() {
				 	if (document.getElementById("we_own_it")) {
 					var options= document.getElementById('we_own_it').options;
 					var option_length = options.length;
					for (var i= 0; i < option_length; i++) {
					    if (options[i].value==='No') {
					        options[i].selected= true;
					        break;
					    }
					}
 				}
				
 				if (document.getElementById("acf-field-we_own_it")) { 
 					var options= document.getElementById('acf-field-we_own_it').options;
					var option_length = options.length;
					for (var i= 0; i<option_length; i++) {
					    if (options[i].value==='No') {
					        options[i].selected= true;
					        break;
					    }
					}
 				}
			};
		   </script>
    	<?php } // else { ?>

    		<!--	<script>	
 				window.onload = function() {
				 	if (document.getElementById("we_own_it")) {
 					var options= document.getElementById('we_own_it').options;
 					var option_length = options.length;
					for (var i= 0; i < option_length; i++) {
					    if (options[i].value==='Yes') {
					        options[i].selected= true;
					        break;
					    }
					}
 				}
				
 				if (document.getElementById("acf-field-we_own_it")) { 
 					var options= document.getElementById('acf-field-we_own_it').options;
					var option_length = options.length;
					for (var i= 0; i<option_length; i++) {
					    if (options[i].value==='Yes') {
					        options[i].selected= true;
					        break;
					    }
					}
 				}
			};
		   </script> -->

    	<?php // }	 
		} 

		function plugin_init() {
				global $wpdb;
		  		$option_name = 'feed_newswire';
	            $sql = "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s";
	            $sql = $wpdb->prepare($sql, $option_name);
	            $options = $wpdb->get_results( $sql );
                $feed_value =  isset($options['0']->option_value) ? $options['0']->option_value : '';
			    if(isset($_POST['feed_option'])) {
					$feed_option = $_POST['feed_option'];
					 updatefeed($feed_option);
				} ?>
			  <h2>Update feed option value</h2>
			   <form action="" method="POST">
				<input type="text" placeholder="<?php echo $feed_value;?>" name="feed_option" value="<?php echo $feed_value;?>" >
				  <input type="submit" value="Submit">
			   </form>
								
		<?php } 
	    function updatefeed($feed_option) {
	  		global $wpdb;
            $sql = "UPDATE {$wpdb->options} SET option_value = %s WHERE option_name = 'feed_newswire' ";
            $sql = $wpdb->prepare($sql, $feed_option);
            $wpdb->query($sql);
	        echo "Feed option Updated Successfully";
	        echo "<script type='text/javascript'>window.location=document.location.href;</script>";
	   }