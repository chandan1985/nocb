<?php
/*
Plugin Name: TDC User Management
Plugin URI: http://www.thedolancompany.com
Description: Manage WordPress user roles & capabilities
Version: 0.3
Author: Dave Long

Modifications: 
08/01/2014 - DL - Added option to completely remove blog access when removing Super Admin
				- Expanded network-level role editing to all users
08/05/2014 - DL - Changed Super Admin check to dolan email / IT check
*/

if ( !defined( 'ABSPATH' ) )
	die( 'Direct access not allowed.' );

class tdc_user_management {
	const CUSTOM_ROLE = 'TDC_Support';
	const CUSTOM_ROLE_LABEL = 'TDC Support';

	/*
	 * Class constructor
	 * Build class & set up actions & filters based on page type
	 */
	function __construct() {
		// Redirect Support Users on Login
		add_filter('login_redirect', array( &$this, 'support_user_login' ), 10, 3);

		if( is_admin() ){
			// Allow edit by non-super admin users
			remove_all_filters( 'enable_edit_any_user_configuration' );
			add_filter( 'enable_edit_any_user_configuration', '__return_true');
			add_filter( 'admin_head', array( &$this, 'user_edit_permission_check'), 1, 4 );
			add_filter( 'map_meta_cap', array( &$this, 'unlock_user_caps' ), 1, 4 );

			// Blog Level Menus
			add_action( 'admin_menu', array( &$this, 'setup_plugin_menu' ) );
			add_filter( 'user_row_actions', array( &$this, 'add_edit_user_link' ), 10, 2 );
			// Network Level Menus
			add_action( 'network_admin_menu', array( &$this, 'setup_plugin_network_menu' ) );
			add_filter( 'ms_user_row_actions', array( &$this, 'update_blog_roles_link' ), 10, 2 );

			// Check for and setup TDC Roles & Capabilities if not done already
			add_action( 'admin_init', array( &$this, 'activate' ) );

			// Apply TDC Roles & Capabilities to new blogs upon creation
			add_action( 'wpmu_new_blog', array( &$this, 'setup_new_blog_roles' ), 10, 6 );
		}
	}

	/******* WP FITLERS ************/

	/*
	 * Add 'Edit Capabilities' link to users @ blog level
	 */
	function add_edit_user_link( $actions, $user ){
		// Only show edit link to IT users
		global $current_user;
		if( preg_match( '/thedolancompany\.com|dolanmedia\.com/', $current_user->user_email ) ) {
			$actions[ 'edit_capabilities' ] = '<a href="' . wp_nonce_url( 'users.php?page=tdc-user-management&amp;user_id='. $user->ID, 'tdc_user_edit' . $user->ID ) . '">Edit Capabilities</a>';
		}
		return $actions;
	}

	/*
	 * Add 'Update Blog Roles' / 'Remove Super Admin' link to users @ network level
	 */
	function update_blog_roles_link( $actions, $user ){
		if( $user->ID != 1 ) {
			$label = 'Update Blog Roles';
			if( is_super_admin( $user->ID ) ) {
				$label = 'Remove Super Admin';
			}
			$actions[ 'edit_capabilities' ] = '<a href="' . wp_nonce_url( 'users.php?page=tdc-user-management&amp;user_id='. $user->ID, 'tdc_user_edit' . $user->ID ) . '">' . $label . '</a>';
		}
		return $actions;
	}

	/*
	 * Redirect support reps to the 'all users' page on login
	 */
	function support_user_login( $redirect_to, $request_redirect_to, $user ){
		//Is there a user to check?
		if( isset( $user->ID ) ) {
			$myuser = get_userdata( $user->ID );
			//check for support users & redirect them to the all users page
			if( in_array( tdc_user_management::CUSTOM_ROLE, $myuser->roles ) )
				return get_bloginfo( 'url' ) . '/wp-admin/users.php';
		}
		return $redirect_to;
	}

	/*
	* Override Caps that are super-admin only
	*/
	function unlock_user_caps( $caps, $cap, $user_id, $args ){
		foreach( $caps as $key => $capability ){
			if( $capability != 'do_not_allow' )
				continue;

			switch( $cap ) {
				case 'edit_user':
				case 'edit_users':
					$caps[$key] = 'edit_users';
					break;
				case 'delete_user':
				case 'delete_users':
					$caps[$key] = 'delete_users';
					break;
				case 'create_users':
					$caps[$key] = $cap;
					break;
			}

		}
		return $caps;
	}

	/*
	 * Checks that both the editing user and the user being edited are
	 * members of the blog and prevents the super admin being edited.
	 */
	function user_edit_permission_check(){
		global $current_user, $profileuser;
	 
		$screen = get_current_screen();

		$current_user = wp_get_current_user();

		if( !is_super_admin( $current_user->ID ) && in_array( $screen->base, array( 'user-edit', 'user-edit-network' ) ) ) { // editing a user profile
			if ( is_super_admin( $profileuser->ID ) ) { // trying to edit a superadmin while less than a superadmin
				wp_die( __( 'You do not have permission to edit this user.' ) );
			} elseif ( ! ( is_user_member_of_blog( $profileuser->ID, get_current_blog_id() ) && is_user_member_of_blog( $current_user->ID, get_current_blog_id() ) )) { // editing user and edited user aren't members of the same blog
				wp_die( __( 'You do not have permission to edit this user.' ) );
			}
		}
	 
	}

	/******* END WP FILTERS ********/

	/******* WP ACTIONS ************/

	/*
	 * Add 'User Mgmt' to the users menu if current user is a super admin
	 */
	function setup_plugin_menu() {
		// Only show edit link to IT users
		global $current_user;
		if( preg_match( '/thedolancompany\.com|dolanmedia\.com/', $current_user->user_email ) ) {
			add_action('admin_print_styles', array(&$this, 'enqueueStylesheet'));
			add_submenu_page( 'users.php', 'TDC User Management', 'User Management', 'edit_users', 'tdc-user-management', array( &$this, 'role_editor_admin' ) );
		}
	}

	/*
	 * Add 'User Mgmt' to the network users menu
	 */
	function setup_plugin_network_menu() {
		add_action('admin_print_styles', array(&$this, 'enqueueStylesheet'));
		add_submenu_page( 'users.php', 'TDC User Management', 'User Management', 'edit_users', 'tdc-user-management', array( &$this, 'edit_super_admin' ) );
		add_submenu_page( 'users.php', 'Restore Default Roles', 'Restore Roles', 'edit_users', 'restore-default-roles', array( &$this, 'restore_default_roles' ) );
	}

	/*
	 * Output plugin CSS
	 */
	function enqueueStylesheet(){
		wp_register_style('tdc-user-management-styles', plugins_url('/css/style.css', __FILE__));
		wp_enqueue_style('tdc-user-management-styles');
	}

	/******* END WP ACTIONS ********/
	
	/******* Public Functions ******/

	/*
	 * Setup & add New Custom Roles on plugin activation
	 */
	function activate( ){
		if( !get_site_option( 'tdc_roles_setup', false, false ) ){
			tdc_user_management::set_default_rolecaps();
			add_site_option( 'tdc_roles_setup', true );
		}
	}

	/*
	 * Build User Mangement page in network admin
	 */
	function edit_super_admin(){
		$nonce_checked = false;
		?>
		<div class="wrap" id="tdc_user_management">
			<h2>TDC User Management</h2>
			<?php

			// Check $_POST & nonce; update user/role depending on hidden field
			if( !empty( $_POST ) && !empty( $_POST['Submit'] ) && check_admin_referer( 'my-nonce' ) ){
				$nonce_checked = true;
				$this->update_super_admin_access( $_POST['edit_user'], $_POST['user_role'] );
			}

			$super_admins = get_super_admins();
			// Edit individual user
			if( isset( $_GET['user_id'] ) ){
				if( !$nonce_checked )
					check_admin_referer();
				$edit_user = get_userdata( $_GET['user_id'] ); 
				$this->edit_global_roles_table( $edit_user );
			}
			// Display Super Admin table
			else {
				echo( $this->display_super_admin_table( $super_admins ) );
			} ?>
		</div>
	<?php
	}

	/*
	 * Build Restore Roles page in network admin
	 */
	function restore_default_roles(){
		?>
		<div class="wrap" id="tdc_user_management">
			<h2>Restore Default Roles</h2>
			<?php

			// Check $_POST & nonce; update user/role depending on hidden field
			if( !empty( $_POST ) && !empty( $_POST['Submit'] ) && check_admin_referer( 'my-nonce' ) ){
				tdc_user_management::set_default_rolecaps();
				?>
				<div id="message" class="updated fade"><p><strong>Default roles & capabilities restored.</strong></p></div>
			<?php } ?>
			<form method="post" action="<?php echo( get_bloginfo( 'url' ) . $_SERVER['PHP_SELF'] . '?' .  $_SERVER['QUERY_STRING'] ); ?>" enctype="multipart/form-data">
				<?php wp_nonce_field('my-nonce'); ?>
				<p>
					<input type="checkbox" name="restore_defaults" value="1">
					<label for="restore_defaults">Restore Default Roles</label><br>
					<small>Checking box above and clicking 'update' will revert all roles & caps to default on all blogs.</small>
				</p>
				<p class="submit">
					<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Update'); ?>" />
				</p>
			</form>
		</div>
	<?php
	}

	/*
	 * Build User Mangement page in blog-level admin
	 */
	function role_editor_admin() {
		global $wp_roles;

		$all_caps = $this->get_all_wp_caps(  );
		$nonce_checked = false;
		?>
		<div class="wrap" id="tdc_user_management">
			<h2>TDC User Management</h2>
			<?php

			// Check $_POST & nonce; update user/role depending on hidden field
			if( !empty( $_POST ) && !empty( $_POST['Submit'] ) && check_admin_referer( 'my-nonce' ) ){
				$nonce_checked = true;
				if( !empty( $_POST['current_role'] ) )
					$this->update_role( $_POST['current_role'], $_POST['caps'], $_POST['apply_to_all'] );
				if( !empty( $_POST['edit_user'] ) )
					 $this->update_user( $_POST['edit_user'], $_POST['user_role'], $_POST['caps'], $_POST['apply_to_all'] );
			}

			// If user has been deleted, skip displaying form
			if( !isset( $_POST['user_role'] ) || $_POST['user_role'] !== 'delete' ){
			?>
				<form method="post" action="<?php echo( get_bloginfo( 'url' ) . $_SERVER['PHP_SELF'] . '?' .  $_SERVER['QUERY_STRING'] ); ?>" enctype="multipart/form-data">
					<?php
					wp_nonce_field('my-nonce');
					$roles = $wp_roles->roles;

					// User edit mode
					if( isset( $_GET['user_id'] )) {
						if( !$nonce_checked )
							check_admin_referer();
						$edit_user = get_userdata( $_GET['user_id'] );
						$current_role = array_shift( $edit_user->roles );
						$this->user_table_header( $roles, $edit_user, $current_role ); ?>
						<input type="hidden" name="edit_user" value="<?php echo( $_GET['user_id'] ); ?>">
						<?php
						$role_caps = $edit_user->allcaps;
						$this->output_capability_tables( $all_caps, $current_role, $role_caps, true );
					}
					// Role edit mode
					else {
						if( isset( $_GET['role'] ) && array_key_exists( $_GET['role'], $roles ) )
							$current_role =  $_GET['role'];
						else
							$current_role = 'administrator';
						$this->role_table_header( $roles, $current_role ); ?>
						<input type="hidden" name="current_role" value="<?php echo( $current_role ); ?>">
						<?php
						$this->output_capability_tables( $all_caps, $current_role, $roles[ $current_role ][ 'capabilities' ] );
					}
					?>
					<p><input type="checkbox" name="apply_to_all" value="1" checked><label for="apply_to_all">Apply to All Blogs</label></p>
					<p class="submit">
						<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
					</p>
				</form>
			<?php }
			else{ ?>
				<p><strong>Return to <a href="<?php echo( get_bloginfo( 'url' ) . $_SERVER['PHP_SELF'] ); ?>">Users</a>?</strong></p>
			<?php } ?>
		</div>
	<?php
	}

	/*
	 * Function to set roles & capabilities to TDC defaults upon blog creation
	 */
	function setup_new_blog_roles( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
		switch_to_blog($blog_id);
		tdc_user_management::setup_default_blog_rolecaps();
		restore_current_blog();
	}

	/******* End Public Functions **/

	/******* Private Functions ******/

	/*
	 * Function to display table of all super admin users
	 */
	private function display_super_admin_table( $admin_user_list ){
		global $wpdb;?>

		<h3>Super Admin Users</h3>
			<table class="tdc-rolecaps wp-list-table widefat">
				<thead>
					<tr><th>Username</th><th>E-mail</th><th>Update Access</th></tr>
				</thead>
				<tbody>
				<?php
				$logins = implode( "', '", $admin_user_list );
				$admin = $wpdb->get_results( "SELECT ID, user_login, user_email FROM $wpdb->users WHERE user_login IN ('$logins')" );
				foreach( $admin as $row ){
					if( $row->ID != 1 ){ ?>
					<tr>
						<td>
							<a href="<?php echo( wp_nonce_url( 'user-edit.php?user_id=' . $row->ID, 'edit_user_' . $row->ID ) ); ?>"><?php echo( $row->user_login );?></a>
						</td>
						<td><?php echo( $row->user_email ); ?></td>
						<td>
							<a href="<?php echo( wp_nonce_url( 'users.php?page=tdc-user-management&amp;user_id=' . $row->ID, 'tdc_user_edit' . $row->ID ) ); ?>">Remove Super Admin</a>
						</td>
					</tr><?php
					}
				} ?>
				</tbody>
			</table>
		<?php
	}

	/*
	 * Function to display global user role update table
	 * @param object $user - WP User Object
	 */
	private function edit_global_roles_table( $user ){
		global $wp_roles;
		$roles = $wp_roles->roles;
	?>
		<div class="postbox tdc-network">
			<h3>Admin access for 
				<a href="<?php wp_nonce_url( 'user-edit.php?user_id='. $user->ID, 'edit_user_' . $user->ID ) ?>"><?php echo( $user->display_name ); ?></a>
			</h3>
			<div class="inside">Change Role To:
				<form method="post" action="<?php echo( $_SERVER['PHP_SELF'] . '?' .  $_SERVER['QUERY_STRING'] ); ?>" enctype="multipart/form-data">
					<?php wp_nonce_field('my-nonce'); ?>
					<select name="user_role" id="user_role">
						<option value="">No Access</option>
						<?php $role_vals = array_keys( $roles );
						foreach( $role_vals as $role ){
							$dsp = preg_replace( '/\|user\srole/i', '', $roles[$role][ 'name' ] ); ?>
							<option value="<?php echo( $role ) ?>"><?php echo( $dsp ); ?></option>
						<?php } ?>
					</select>
					<input type="hidden" name="edit_user" value="<?php echo( $user->ID ) ?>">
					<p class="description"><?php
						if( is_super_admin( $user->ID ) ) {
							echo( 'Super Admin will be removed and replaced with the above role for all active blogs.' );
						}
						else {
							echo( 'Change user\'s role to the above role for all active blogs.' );
						}?> 
					</p>
					<p class="submit">
						<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
					</p>
				</form>
			</div>
		</div><?php
	}

	/*
	 * Returns array of user-added WP capabilities
	 */
	private function get_additional_wp_caps(){
		global $wp_roles;

		$syscaps = tdc_user_management::get_default_wp_caps();
		$wp_caps = array();

		$role_caps = $wp_roles->roles;
		foreach( $role_caps as $role )
			$wp_caps = array_merge( $wp_caps, $role[ 'capabilities' ] );

		return array_diff_key( $wp_caps, $syscaps );
	}

	/*
	 * Returns array of built-in & additional WP capabilities
	 */
	private function get_all_wp_caps(){
		global $wp_roles;

		$syscaps = $this->get_default_wp_caps();
		$wp_caps = array();
		$ret_arr = array();

		$role_caps = $wp_roles->roles;
		foreach( $role_caps as $role ){
			$wp_caps = array_merge( $wp_caps, $role[ 'capabilities' ] );
		}

		$added = array_keys( array_diff_key( $wp_caps, $syscaps ) );
		foreach( $added as $cap )
			$ret_arr[ $cap ] = array( 'Additional Capabilities', 1 );

		return array_merge( $syscaps, $ret_arr );
	}

	/*
	 * Returns array of built-in WP capabilities (WP 3.3 wp-admin/includes/schema.php)
	 */
	private function get_default_wp_caps() {
		$core_caps = array();

		$core_caps[ 'edit_posts' ] = array( 'Editing Capabilities', 1 );
		$core_caps[ 'edit_others_posts' ] = array( 'Editing Capabilities', 1 );
		$core_caps[ 'publish_posts' ] = array( 'Editing Capabilities', 1 );
		$core_caps[ 'edit_published_posts' ] = array( 'Editing Capabilities', 1 );
		$core_caps[ 'edit_private_posts' ] = array( 'Editing Capabilities', 1 );
		$core_caps[ 'edit_pages' ] = array( 'Editing Capabilities', 1 );
		$core_caps[ 'edit_others_pages' ] = array( 'Editing Capabilities', 1 );
		$core_caps[ 'publish_pages' ] = array( 'Editing Capabilities', 1 );
		$core_caps[ 'edit_published_pages' ] = array( 'Editing Capabilities', 1 );
		$core_caps[ 'edit_private_pages' ] = array( 'Editing Capabilities', 1 );
		$core_caps[ 'manage_categories' ] = array( 'Editing Capabilities', 1 );

		$core_caps[ 'delete_posts' ] = array( 'Deletion Capabilities', 1 );
		$core_caps[ 'delete_others_posts' ] = array( 'Deletion Capabilities', 1 );
		$core_caps[ 'delete_published_posts' ] = array( 'Deletion Capabilities', 1 );
		$core_caps[ 'delete_private_posts' ] = array( 'Deletion Capabilities', 1 );
		$core_caps[ 'delete_pages' ] = array( 'Deletion Capabilities', 1 );
		$core_caps[ 'delete_others_pages' ] = array( 'Deletion Capabilities', 1 );
		$core_caps[ 'delete_published_pages' ] = array( 'Deletion Capabilities', 1 );
		$core_caps[ 'delete_private_pages' ] = array( 'Deletion Capabilities', 1 );

		$core_caps[ 'add_users' ] = array( 'User Capabilities', 1 );
		$core_caps[ 'create_users' ] = array( 'User Capabilities', 1 );
		$core_caps[ 'delete_users' ] = array( 'User Capabilities', 1 );
		$core_caps[ 'edit_users' ] = array( 'User Capabilities', 1 );
		$core_caps[ 'list_users' ] = array( 'User Capabilities', 1 );
		$core_caps[ 'promote_users' ] = array( 'User Capabilities', 1 );
		$core_caps[ 'remove_users' ] = array( 'User Capabilities', 1 );

		$core_caps[ 'activate_plugins' ] = array( 'Plugin & Theme Capabilities', 1 );
		$core_caps[ 'delete_plugins' ] = array( 'Plugin & Theme Capabilities', 0 );
		$core_caps[ 'edit_plugins' ] = array( 'Plugin & Theme Capabilities', 0 );
		$core_caps[ 'install_plugins' ] = array( 'Plugin & Theme Capabilities', 0 );
		$core_caps[ 'update_plugins' ] = array( 'Plugin & Theme Capabilities', 0 );
		$core_caps[ 'delete_themes' ] = array( 'Plugin & Theme Capabilities', 0 );
		$core_caps[ 'edit_themes' ] = array( 'Plugin & Theme Capabilities', 0 );
		$core_caps[ 'edit_theme_options' ] = array( 'Plugin & Theme Capabilities', 1 );
		$core_caps[ 'install_themes' ] = array( 'Plugin & Theme Capabilities', 0 );
		$core_caps[ 'switch_themes' ] = array( 'Plugin & Theme Capabilities', 1 );
		$core_caps[ 'update_themes' ] = array( 'Plugin & Theme Capabilities', 0 );

		$core_caps[ 'import' ] = array( 'Other Capabilities', 1 );
		$core_caps[ 'manage_links' ] = array( 'Other Capabilities', 1 );
		$core_caps[ 'manage_options' ] = array( 'Other Capabilities', 1 );
		$core_caps[ 'unfiltered_html' ] = array( 'Other Capabilities', 1 );
		$core_caps[ 'upload_files' ] = array( 'Other Capabilities', 1 );
		$core_caps[ 'moderate_comments' ] = array( 'Other Capabilities', 1 );
		$core_caps[ 'read' ] = array( 'Other Capabilities', 1 );
		$core_caps[ 'unfiltered_upload' ] = array( 'Other Capabilities', 1 );
		$core_caps[ 'edit_dashboard' ] = array( 'Other Capabilities', 1 );
		$core_caps[ 'read_private_posts' ] = array( 'Other Capabilities', 1 );
		$core_caps[ 'read_private_pages' ] = array( 'Other Capabilities', 1 );
		$core_caps[ 'export' ] = array( 'Other Capabilities', 1 );
		$core_caps[ 'update_core' ] = array( 'Other Capabilities', 0 );

		$core_caps[ 'edit_files' ] = array( 'Deprecated Capabilities', 0 );
		$core_caps[ 'level_10' ] = array( 'Deprecated Capabilities', 1 );
		$core_caps[ 'level_9' ] = array( 'Deprecated Capabilities', 1 );
		$core_caps[ 'level_8' ] = array( 'Deprecated Capabilities', 1 );
		$core_caps[ 'level_7' ] = array( 'Deprecated Capabilities', 1 );
		$core_caps[ 'level_6' ] = array( 'Deprecated Capabilities', 1 );
		$core_caps[ 'level_5' ] = array( 'Deprecated Capabilities', 1 );
		$core_caps[ 'level_4' ] = array( 'Deprecated Capabilities', 1 );
		$core_caps[ 'level_3' ] = array( 'Deprecated Capabilities', 1 );
		$core_caps[ 'level_2' ] = array( 'Deprecated Capabilities', 1 );
		$core_caps[ 'level_1' ] = array( 'Deprecated Capabilities', 1 );
		$core_caps[ 'level_0' ] = array( 'Deprecated Capabilities', 1 );
		return $core_caps;
	}

	/*
	 * Function to output capability tables
	 * @param array $built_in - formatted caps from config plus added caps
	 * @param string $current_role - role shown when in role mode
	 * @param array $compare_caps - list of capabilities to compare against
	 * @param boolean $user_mode - true if we're in user mode
	 */
	private function output_capability_tables( $built_in, $current_role, $compare_caps, $user_mode = false ) {
		// Differentiate user role caps & additional caps in user mode
		if( $user_mode ){
			global $wp_roles;
			$roles = $wp_roles->roles;
			$role_caps = $roles[ $current_role ][ 'capabilities' ];
		}

		// Prepare caps array for output
		reset( $built_in );
		$output_html = '';
		// While loop to step through each capability group
		while( $element = current( $built_in ) ){
			$group = $element[0];
			if( preg_match( '/deletion/i', $group ) )
				$maxct = 4;
			else
				$maxct = 5;

			// While loop to build each group table ?>
			<table class="tdc-rolecaps wp-list-table widefat" cellspacing="0">
				<thead>
					<tr><th colspan="<?php echo( $maxct ); ?>"><?php echo( $group ); ?></th></tr>
				</thead>
				<tbody>
				<?php
				while( $element[0] == $group ){
					// 2nd while loop to build individual table rows ?>
					<tr>
					<?php
					$cell_ct = 0;
					while( $cell_ct < $maxct ) {
						$name = key( $built_in );
						// Only output enabled caps
						if( $element[1] == 1 ){
							// Mark user caps given by role
							if( $user_mode && array_key_exists( $name, $role_caps ) ){ ?>
								<td class="rolecap">
							<?php 
							}
							// Mark deprecated caps
							elseif( preg_match( '/deprecated/i', $group ) ){ ?>
								<td class="deprecated"> 
							<?php 
							}
							else{ ?>
								<td>
							<?php
							}
								 // Disable input on role caps in user mode
								if( $user_mode && array_key_exists( $name, $role_caps ) ){ ?>
									<input disabled
								<?php
								}
								else{?>
									<input 
								<?php } ?>
									type="checkbox" value="1" name="caps[<?php echo( $name ); ?>]"
								<?php // Check boxes for caps user/role has
								if( array_key_exists( $name, $compare_caps ) ){ ?>
									checked
								<?php } ?>
								>
								<label for="<?php echo( $name ) ?>"><?php echo( $name ); ?></label>
							</td>
							<?php
							$cell_ct++;
						}
						// Make sure next cap belongs to this group
						next( $built_in );
						$element = current( $built_in );
						if( $element[0] !== $group )
							break;
					} ?>
					</tr>
				<?php }?>
				</tbody>
			</table><?php
		}
	}

	/*
	 * Function to output header for role table
	 * @param array $roles_list - List of WP User Roles
	 * @param string $current_role - Role currently viewed
	 */
	private function role_table_header( $roles_list, $current_role ) {
		if( !isset( $roles_list ) || !isset( $current_role ) )
			return; ?>

		<ul class="subsubsub">
		<?php
			$role_ct = 0;
			foreach( $roles_list as $role => $arr ){?>
				<li>
					<a
						<?php if( preg_match( '/' . $current_role . '/i', $role ) ){ ?>
							class="current"
						<?php } ?>
						href="<?php echo( get_bloginfo( 'url' ) . $_SERVER[ 'PHP_SELF' ] ); ?>?page=tdc-user-management
						<?php if( !preg_match( '/administrator/i', $role ) ) ?>
							&amp;role=<?php echo( $role ); ?>
						"><?php echo( preg_replace( '/\|user\srole/i', '', $arr[ 'name' ] ) ) ?>
					</a>
					<?php
					$role_ct++;
					if( $role_ct < count( $roles_list ) ){ ?>
						 | 
					<?php } ?>
				</li><?php
			}?>
		</ul><?php
	}

	/*
	 * Walk through blogs and setup default roles & capabilities
	 */
	private function set_default_rolecaps(){
		global $wpdb, $blog_id;

		$current = $blog_id;
		$site_blogs = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs WHERE deleted != 1 ORDER BY blog_id ASC" );

		foreach( $site_blogs as $blog ){
			// Update other blogs
			if( $blog != $current) {
				switch_to_blog( $blog );
				tdc_user_management::setup_default_blog_rolecaps();
				restore_current_blog();
			}
		}
		// Update the current blog
		tdc_user_management::setup_default_blog_rolecaps();
	}

	/*
	 * Setup default roles & capabilities on the current blog
	 */
	private function setup_default_blog_rolecaps(){
		global $wpdb, $wp_roles;

		//Administrator:
		$user_caps = array(
			'edit_posts'=> true,
			'edit_others_posts'=> true,
			'publish_posts'=> true,
			'edit_published_posts'=> true,
			'edit_private_posts'=> true,
			'edit_pages'=> true,
			'edit_others_pages'=> true,
			'publish_pages'=> true,
			'edit_published_pages'=> true,
			'edit_private_pages'=> true,
			'manage_categories'=> true,
			'delete_posts'=> true,
			'delete_others_posts'=> true,
			'delete_published_posts'=> true,
			'delete_private_posts'=> true,
			'delete_pages'=> true,
			'delete_others_pages'=> true,
			'delete_published_pages'=> true,
			'delete_private_pages'=> true,
			'add_users'=> true,
			'create_users'=> true,
			'delete_users'=> true,
			'edit_users'=> true,
			'list_users'=> true,
			'promote_users'=> true,
			'remove_users'=> true,
			'activate_plugins' => true,
			'edit_theme_options'=> true,
			'switch_themes'=> true,
			'import'=> true,
			'manage_links'=> true,
			'manage_options'=> true,
			'upload_files'=> true,
			'moderate_comments'=> true,
			'read'=> true,
			'unfiltered_upload'=> true,
			'edit_dashboard'=> true,
			'read_private_posts'=> true,
			'read_private_pages'=> true,
			'export'=> true,
			'level_10'=> true,
			'level_9'=> true,
			'level_8'=> true,
			'level_7'=> true,
			'level_6'=> true,
			'level_5'=> true,
			'level_4'=> true,
			'level_3'=> true,
			'level_2'=> true,
			'level_1'=> true,
			'level_0'=> true
		);
		tdc_user_management::update_role_caps( 'administrator', $user_caps, true );

		//Editor:
		$user_caps = array(
			'edit_posts'=> true,
			'edit_others_posts'=> true,
			'publish_posts'=> true,
			'edit_published_posts'=> true,
			'edit_private_posts'=> true,
			'edit_pages'=> true,
			'edit_others_pages'=> true,
			'publish_pages'=> true,
			'edit_published_pages'=> true,
			'edit_private_pages'=> true,
			'manage_categories'=> true,
			'delete_posts'=> true,
			'delete_others_posts'=> true,
			'delete_published_posts'=> true,
			'delete_private_posts'=> true,
			'delete_pages'=> true,
			'delete_others_pages'=> true,
			'delete_published_pages'=> true,
			'delete_private_pages'=> true,
			'manage_links'=> true,
			'upload_files'=> true,
			'moderate_comments'=> true,
			'read'=> true,
			'read_private_posts'=> true,
			'read_private_pages'=> true,
			'level_7'=> true,
			'level_6'=> true,
			'level_5'=> true,
			'level_4'=> true,
			'level_3'=> true,
			'level_2'=> true,
			'level_1'=> true,
			'level_0'=> true
		);
		tdc_user_management::update_role_caps( 'editor', $user_caps, true );

		//Author:
		$user_caps = array(
			'edit_posts'=> true,
			'publish_posts'=> true,
			'edit_published_posts'=> true,
			'delete_posts'=> true,
			'delete_published_posts'=> true,
			'upload_files'=> true,
			'read'=> true,
			'level_2'=> true,
			'level_1'=> true,
			'level_0'=> true
		);
		tdc_user_management::update_role_caps( 'author', $user_caps, true );

		//Contributor:
		$user_caps = array(
			'edit_posts'=> true,
			'delete_posts'=> true,
			'read'=> true,
			'level_1'=> true,
			'level_0'=> true
		);
		tdc_user_management::update_role_caps( 'contributor', $user_caps, true );

		//Subscriber:
		$user_caps = array(
			'read'=> true,
			'level_1'=> true,
			'level_0'=> true
		);
		tdc_user_management::update_role_caps( 'subscriber', $user_caps, true );

		//TDC Support:
		$user_caps = array(
			'add_users'=> true,
			'create_users'=> true,
			'edit_users'=> true,
			'list_users'=> true,
			'promote_users'=> true,
			'remove_users'=> true,
			'read'=> true,
			'level_0'=> true
		);
		// Update TDC Support role
		if( get_role( tdc_user_management::CUSTOM_ROLE ) )
			tdc_user_management::update_role_caps( tdc_user_management::CUSTOM_ROLE, $user_caps, true );
		// Or add it if it doesn't exist
		else
			add_role( tdc_user_management::CUSTOM_ROLE, tdc_user_management::CUSTOM_ROLE_LABEL, $user_caps );
	}

	/*
	 * Function to update a WP Role
	 * @param string $role - Role to update
	 * @param array $new_caps - List of capabilities to add to role
	 * @param boolean $all_blogs - When true, update all blogs
	 */
	private function update_role( $role, $new_caps, $all_blogs = false ){
		global $wp_roles, $wpdb, $blog_id;

		// Apply changes to all other blogs
		if( $all_blogs ){
			$current = $blog_id;
			$role_display_name = $wp_roles->roles[$role]['name'];
			$site_blogs = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs WHERE deleted != 1 ORDER BY blog_id ASC" );

			foreach( $site_blogs as $blog ){
				// Update other blogs
				if( $blog != $current) {
					switch_to_blog( $blog );
					if( get_role( $role ) )
						$this->update_role_caps( $role, $new_caps );
					else
						add_role( $role, $role_display_name, $new_caps );
					restore_current_blog();
				}
			}
		}
		// Update the current blog
		$this->update_role_caps( $role, $new_caps );
		?>
		<div id="message" class="updated fade"><p><strong>User role <?php echo( $role ); ?> updated.</strong></p></div>
		<?php
	}

	/*
	 * Function to update caps for an existing WP Role
	 * @param string $role - Role to update
	 * @param array $new_caps - List of capabilities to add to role
	 */
	private function update_role_caps( $role, $new_caps, $ignore_additional = false ){
		global $wp_roles;
		$cur_caps = $wp_roles->roles[$role]['capabilities'];

		// Find caps to add and remove
		$add_caps = array_diff_key( $new_caps, $cur_caps );
		$del_caps = array_diff_key( $cur_caps, $new_caps );

		if( $ignore_additional ){
			$added_caps = tdc_user_management::get_additional_wp_caps();
			$del_caps = array_diff_key( $del_caps, $added_caps );
		}

		// Update caps for role
		if( !empty( $add_caps ) || !empty( $del_caps ) ){
			$wprole = get_role( $role );
			foreach( $add_caps as $key => $val ){
				$wprole->add_cap( $key );
			}
			foreach( $del_caps as $key => $val ){
				$wprole->remove_cap( $key );
			}
		}
	}

	/*
	 * Function to remove a super admin & set blog-level access
	 * @param int $user_id - User to remove from super admin
	 * @param string $role - Replacement blog-level role
	 */
	private function update_super_admin_access( $user_id, $role ){
		global $wpdb;

		$all_blogs = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs WHERE deleted != 1 ORDER BY blog_id ASC" );

		foreach( $all_blogs as $blog ){
			switch_to_blog( $blog );
			$edit_user = new WP_User( $user_id );
			$edit_user->remove_all_caps();
			if( !empty( $role ) ) {
				$edit_user->add_role( $role );
			}
			restore_current_blog();
		}
		if( !is_super_admin( $user_id ) || revoke_super_admin( $user_id ) ){ ?>
			<div id="message" class="updated fade">
				<p>
					<strong>
						User <a href="<?php echo( wp_nonce_url( 'user-edit.php?user_id='. $edit_user->ID, 'edit_user_' . $edit_user->ID ) ); ?>"> <?php echo( $edit_user->display_name ); ?></a> updated.
					</strong>
				</p>
			</div><?php
		}
		else{ ?>
			<div id="message" class="error fade">
				<p>
					<strong>
						Cannot remove Super Admin from  <a href="<?php echo( wp_nonce_url( 'user-edit.php?user_id='. $edit_user->ID, 'edit_user_' . $edit_user->ID ) ); ?>"> <?php echo( $edit_user->display_name ); ?></a>.
					</strong>
				</p>
			</div><?php
		}
	}

	/*
	 * Function to update a WP User
	 * @param int $user_id - ID of User to update
	 * @param string $new_role - Role to set User to
	 * @param array $new_caps - List of capabilities to add to user
	 * @param boolean $all_blogs - When true, update all blogs
	 */
	private function update_user( $user_id, $new_role, $new_caps = array(), $all_blogs = false ){
		global $wp_roles, $wpdb, $blog_id, $switched;
		$current = $blog_id;
		if( !isset( $new_caps ) )
			$new_caps = array();

		if( $all_blogs )
			$site_blogs = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs WHERE deleted != 1 ORDER BY blog_id ASC" );
		else
			$site_blogs[0] = $current;
			
		foreach( $site_blogs as $blog ){
			if( $blog != $current )
				switch_to_blog( $blog );
			$edit_user = new WP_User( $user_id );
			// Update user role if needed
			$cur_role = array_shift( $edit_user->roles );
			if( $cur_role !== $new_role ){
				$cur_role = $new_role;
				$edit_user->remove_all_caps();
				if( $new_role !== 'delete' ){
					$edit_user->add_role( $new_role );
				}
			}

			// Find caps to add and remove
			$roles = $wp_roles->roles;
			$role_caps = $roles[ $cur_role ][ 'capabilities' ];
			if( !isset( $role_caps ) )
				$role_caps = array();
			$cur_caps = $edit_user->caps;
			unset( $cur_caps[ $cur_role ] );
			$new_caps = array_diff_key( $new_caps, $role_caps );
			$add_caps = array_diff_key( $new_caps, $cur_caps );
			$del_caps = array_diff_key( $cur_caps, $new_caps );

			if( !empty( $add_caps ) || !empty( $del_caps ) ){
				foreach( $add_caps as $key => $val ){
					$edit_user->add_cap( $key );
				}
				foreach( $del_caps as $key => $val ){
					$edit_user->remove_cap( $key );
				}
			}
			if( $switched )
				restore_current_blog();
		}
		?>
		<div id="message" class="updated fade">
			<p>
				<strong>
					User <a href="<?php echo( wp_nonce_url( 'user-edit.php?user_id='. $user->ID, 'edit_user_' . $edit_user->ID ) ); ?>"> <?php echo( $edit_user->display_name ); ?></a> updated.
				</strong>
			</p>
		</div>
		<?php
	}

	/*
	 * Function to output header for user table
	 * @param array $roles_list - List of WP User Roles
	 * @param object $edit_user - WP User Object
	 * @param string $current_role - Edit user's role
	 */
	private function user_table_header( $roles_list, $edit_user, $user_role ) { ?>
		<div class="postbox tdc-rolecaps">
			<h3>Capabilities for <a href="<?php echo( wp_nonce_url( 'user-edit.php?user_id='. $edit_user->ID, 'edit_user_' . $user->ID ) ); ?>"><?php echo( $edit_user->display_name ); ?></a></h3>
			<div class="inside">
				Select Role:
				<select name="user_role" id="user_role">
					<?php
					$role_vals = array_keys( $roles_list );

					foreach( $role_vals as $role ){
						$dsp = preg_replace( '/\|user\srole/i', '', $roles_list[$role][ 'name' ] ); ?>
						<option value="<?php echo( $role ); ?>"
						<?php
						if( stristr( $user_role, $role ) )
							echo( 'selected' ); ?>
						> <?php echo( $dsp ); ?></option>
						<?php
					}?>
					<option value="delete">Remove Access</option>
				</select>
			</div>
		</div>
	<?php
	}

	/******* End Private Functions */
}
?>