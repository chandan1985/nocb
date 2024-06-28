<?php
/*
Plugin Name: Custom Byline
Description: Custom Byline is a very light plugin that lets you easily and safely add custom byline in normal posts.
Author: Asentechllc
Version: 1.0
Text Domain: custom-byline
Author URI: http://www.asentechllc.com/
*/

if (!defined('ABSPATH')) die ('No direct access allowed');

add_action('add_meta_boxes', 'dynamic_add_custom_box');
add_action('admin_head-post.php', 'dynamic_inner_script_custom_box');
add_action('admin_head-post-new.php', 'dynamic_inner_script_custom_box');
add_action('save_post', 'dynamic_inner_save_custom_box', 10, 3 );

function dynamic_add_custom_box() {
	$config = unserialize(get_option('tdc_sponsored_content'));
	if(!empty($config['sponsored_option_byline'])) { 
		$post_types = array ( 'post', 'sponsored_content');
		foreach( $post_types as $post_type )
		{
			add_meta_box("demo-meta-box", "Byline Author", "dynamic_inner_custom_box", $post_type, "side", "core", null);	
		}
	}
	else {
		add_meta_box("demo-meta-box", "Byline Author", "dynamic_inner_custom_box", "post" , "side", "core", null);	

	}	
}
	/**
	 * Print the Meta Box scripts
	 */
	function dynamic_inner_custom_box()
	{
		global $post;
		$author_byline = get_post_meta( $post->ID, 'author_byline', true );
		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), 'noncename_author' );
		?>
		<div id="dynamic_form">
			<div id="field_wrap">
				<?php if ( isset( $author_byline['name'] ) ) {
					for( $i = 0; $i < count( $author_byline['name'] ); $i++ ) {	?>
						<div class="field_row">
							<div class="field_left">
								<div class="form_field">
									<label>Author Name</label>
									<input type="text" class="meta_author_name" name="author[name][]" value="<?php esc_html_e( $author_byline['name'][$i] ); ?>" />
								</div>
							</div>
							<div class="clear"></div>
							<div class="field_right">
								<input class="button" type="button" value="Remove" onclick="remove_field(this)" />
							</div>
							<div class="clear" /></div> 
						</div>
				<?php } // endif
			} // endforeach
			?>
		</div>
		<div style="display:none" id="master-row">
			<div class="field_row">
				<div class="field_left">
					<div class="form_field">
						<label>Author Name</label>
						<input class="meta_author_name" value="" type="text" name="author[name][]" />
					</div>
				</div>
				<div class="clear"></div>
				<div class="field_right"> 
					<input class="button" type="button" value="Remove" onclick="remove_field(this)" /> 
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<div id="add_field_row">
			<input class="button" type="button" value="Add Author" onclick="add_field_row();" />
		</div>
	</div>
<?php }
	/**
	 * Print styles
	 */
	function dynamic_inner_script_custom_box()
	{
		// Check for correct post_type
		// global $post;
		// if( 'post' != $post->post_type )// here you can set post type name
			// return; 

		global $post;

		$config = unserialize(get_option('tdc_sponsored_content'));
		if(!empty($config['sponsored_option_byline']) && $config['sponsored_option_byline'] == 'yes' ) { 
			$post_types = array ( 'post', 'sponsored_content');
			$ptype = $post->post_type;
			if(!in_array($ptype, $post_types))
				return; 
		}
		else {
			if( 'post' != $post->post_type )// here you can set post type name
			return; 
		}
		?>  
		<style type="text/css">
			.field_left {float:left;margin:10px 0;}
			.field_right {margin-bottom:10px;}
			.clear {clear:both;}
		</style>
		<script type="text/javascript">
			function remove_field(obj) {
				var parent=jQuery(obj).parent().parent();
		//console.log(parent)
		parent.remove();
	}
	function add_field_row() {
		var row = jQuery('#master-row').html();
		jQuery(row).appendTo('#field_wrap');
	}
</script>
<?php }
	/**
	 * Save post action, process fields
	 */
	function dynamic_inner_save_custom_box( $post_id, $post_object) 
	{
		// Doing revision, exit earlier **can be removed**
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )  
			return;
		// Doing revision, exit earlier
		if ( 'revision' == $post_object->post_type )
			return;
		// Verify authenticity
		if (isset($_POST['noncename_author']) && !wp_verify_nonce( $_POST['noncename_author'], plugin_basename( __FILE__ ) ) )
			return;
		// Correct post type
		// if ( 'post' != $_POST['post_type'] ) // here you can set post type name
		// return;
		
		$config = unserialize(get_option('tdc_sponsored_content'));
		if(!empty($config['sponsored_option_byline']) && $config['sponsored_option_byline'] == 'yes' ) { 
			$post_types = array ( 'post', 'sponsored_content');
			$ptype = $_POST['post_type'];
			if(!in_array($ptype, $post_types)){
				return;
			}
		}
		else {
		   if (isset($_POST['post_type']) && 'post' != $_POST['post_type'] ) // here you can set post type name
		   return;
		}
		
		if ( !empty($_POST['author'] )) {
		// Build array for saving post meta
			$author_byline = array();
			if(isset($_POST['author']['name'])){
				for ($i = 0; $i < count( $_POST['author']['name'] ); $i++ ) {
					if ( !empty($_POST['author']['name'][ $i ]) ) {
						$author_byline['name'][]  = $_POST['author']['name'][ $i ];
					}
				}
			}

			if ( $author_byline ) 
				update_post_meta( $post_id, 'author_byline', $author_byline );
			else 
				delete_post_meta( $post_id, 'author_byline' );
		} 
		// Nothing received, all fields are empty, delete option
		else 
		{
			delete_post_meta( $post_id, 'author_byline' );
		}
	} 
	?>