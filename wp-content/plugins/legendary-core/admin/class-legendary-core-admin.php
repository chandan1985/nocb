<?php

class Legendary_Core_Admin
{
    public function __construct()
    {
        $this->load_dependencies();
    }

    private static function load_dependencies()
    {

        require_once plugin_dir_path( dirname( __FILE__ ) ) .  'includes/class-toolbox.php';

    }

    public function adm_menu()
    {
        add_options_page('Legendary Data Core plugin', //Page Title
            'Legendary Data',   //Menu Title //odot:(ldl) Use Legendary Data ? for menu?
            'manage_options',   //permission
            'admin_legendary',  //menu slug
            array($this,'legendary_options_page')
        );
    }

    // adm_options() ; Create storable settings in the dB for plugin options.
    public function adm_options()
    {
	$toolbox = new Toolbox();
        $adm_opts = array('akey' => 'AKEY', 'premium_akey'=>'', 'ekey' => 'EKEY', 'host' => 'https://HOST.DOMAIN.TLD',
	  'qkey' => 'TASK_KEY', 'googlemapskey'=>'Google Map API Key', 'landingpage'=>'lists' //DEH ; mv to another set?
	);
        register_setting('legendary_options','legendary_options');
        register_setting('legendary_options','ldl_resolve_opt');
        register_setting('legendary_options','ldl_targetcat');
        register_setting('legendary_options','ldl_targettag');
        
        $toolbox->add_option('legendary_options',$adm_opts);
    }

    //enqueue_assets() Make available the resources for the admin side.
    public static function enqueue_assets($hook=null) {
        if ( $hook != 'settings_page_admin_legendary' ) {
		return;
	}
	wp_enqueue_script( 'legendary-admin-js', plugins_url ( '../js/legendary-admin.js', __FILE__ ), array( 'jquery' ), LDL_CORE_VERSION );
        wp_enqueue_style( 'legendary-admin-css', plugins_url('../css/legendary-admin.css', __FILE__), false, LDL_CORE_VERSION, 'all');
    }

    /*
     * ldl_common_tags() Return Tags used for  'Legendary Enhancement' content processing.
     *  Here to return provided 'type' at a current (c202006) top (50).
     */
    private function ldl_common_tags($sType="post_tag") {
        $tags = get_terms( $sType, array(
                'orderby'    => 'count',
                'hide_empty' => 1,
                'number'=>50
        ) ); //get_tags();
        return $tags;
    }

    /*
     * ldl_tags_all() Return *all* Tags. Use for 'Legenday Enhancement' content processing.
     *  Here, do not hide post_tags that have no posts
     */
    private function ldl_tags_all($sType="post_tag") {
        $tags = get_terms( $sType, array(
            'orderby' => 'count',
            'hide_empty' => 0
        ) );
        return $tags;
    }

    //ldl_js_init() Set Legendary object in .js for use on the page.
    public static function ldl_wp_footer() { //DEH Rename to ldl_footer() ?
        ?>
        <script>if ( typeof LD == "object" ) LD.init();</script>
        <?php
    }

	private static function ldl_available_roles() {
                global $wp_roles;

                return $wp_roles->roles;
        }

    /*
      legendary_options_page() Adminstrative interface for site's Legendary Core options.
      Specifics to get access to the meroveus instance.
      AKEY, the Access Key
      EKEY, the Environment Key
      Host, the URL for the instance
      QKEY, the task key 
    */
    public function legendary_options_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
////	self::ldl_js_init(); //to add_action() ...
	//TODO:(ldl) Separate file that has render HTML code?
	//DEH ? Change below to stay in <?php
        ?>
    <div class="wrap">

<h2 id="ldata-hed"><a href="https://www.legendarydata.com" target="_new"><img src="https://www.legendarydata.com/images/hed-logo.png" /></a><br /><b>for WordPress</b></h2>
<form id="ldl_option_form" method="post" action="options.php">
        <?php
	$toolbox = new Toolbox(); 
        settings_fields('legendary_options');
        $options = $toolbox->get_option('legendary_options', [] );
        $resolveOpt = $toolbox->get_option('ldl_resolve_opt', array("allchecked"=>2) );
        $targetcats = $toolbox->get_option('ldl_targetcat', []);
        $targettags = $toolbox->get_option('ldl_targettag', [] );
        ?>
        <h3>Main Settings</h3>
        <p>The following inputs will be used to control the main lists landing page on the site. Use the <b>[ldl_lists]</b> short code to place the E-store interface on any page. Which lists appear in the E-store (and the order in which they appear) are controlled inside your Legendary Data server. Populate the <u>Premium AKEY</u> and <u>Premium Role</u> fields to enable all-access subscriptions that allow unlimited downloads and enhanced interactive lists.</p>
        <div class="ldl_main_sect">
	<div class="ldl_wp_admin_sect"><label>Primary AKEY*</label>
	<p>* Required: Enter a primary access key (provided by your Legendary Data administrator) to activate your Legendary integration.</p>
        <input id="ldl_akey" name="legendary_options[akey]" size="41" maxlenght="300" value="<?php echo $options['akey'];?>" placeholder="AKEY" type="text" /></div>

        <div class="ldl_wp_admin_sect"><label>EKEY*</label>
	<p>* Required: Enter an environment key (provided by your Legendary Data administrator) to activate your Legendary integration.</p>
        <input id="ldl_ekey" name="legendary_options[ekey]" size="41" maxlenght="300" value="<?php echo $options['ekey'];?>" placeholder="EKEY" type="text" /></div>

        <div class="ldl_wp_admin_sect"><label>Host URL*</label>
	<p>* Required: Enter the host (website) of your Legendary database.</p>
        <input id="ldl_host" name="legendary_options[host]" size="41" maxlength="2048" value="<?php echo $options['host']; ?>" placeholder="https://HOST.DOMAIN.TLD" type="text" /></div>

        <div ldl-lookup="qkey" class="ldl_lookup_block" ldl-status="error">
        <div class="ldl_wp_admin_sect"><label>Task (to activate e-Store list submissions)</label>
        <div class="ldl-error">Loading ...</div>
        <select id="ldl_qkey"></select>
        </div></div>

	</div><!--end main sect -->

	<div class="ldl_premium_sect">
	<div class="ldl_wp_admin_sect"><label>Premium AKEY*</label>
	<p>* Required: In order to activate Premium data access for certain users, you <b>must</b> set a Premium AKEY that is different from the primary AKEY set to the left.</p>
	<input id="ldl_akey" name="legendary_options[premium_akey]" size="41" maxlenght="300" value="<?php echo @$options['premium_akey'];?>" placeholder="Premium AKEY" type="text" /></div>

	<div class="ldl_wp_admin_sect"><label>Premium User Indicator*</label>
	<p>Select <u>either or both</u> of the following to complete Premium access setup</p>
	</div>
	<table class="ldl_premium_or_tbl">
	<tr>
	<td>
	&check;
	</td>
	<td>
        <div class="ldl_wp_admin_sect"><label>Premium Role</label>
	<p>Select a role that, when assigned to a User, will indicate the user should recieve Premium access.</p>
	<ul class="ldl_options_box">
	<?php
	$aRoles = self::ldl_available_roles();
	$sRoleSelect = (array_key_exists("premiumrole", $options) ? $options["premiumrole"] : "administrator");
	$aSelectRoles = is_array( $sRoleSelect ) ? $sRoleSelect : array( $sRoleSelect );
	foreach ( $aRoles as $sRoleKey => $oRole ) {
		$chk = in_array( $sRoleKey, $aSelectRoles ) ? "checked" : "";
		echo '<li><label for="'.$oRole["name"].'"><input type="checkbox" name="legendary_options[premiumrole][]" value="'.$sRoleKey.'" '.$chk.'>' . $oRole["name"]. "</label></li>\n";
	}
	?>	
	</ul>
	</div>
	</td>
	</tr>
	<tr>
	<td>
	&check;
	</td>
	<td>
	<div class="ldl_wp_admin_sect"><label>Premium Meta Key/Value Pair</label>
	<p>Enter a Key/Value pair for meta data that, when assigned to a User, will indicate that user should receive Premium access.</p>
	<table class="ldl_metakeypair_table">
	<tr><td><input type="text" id="ldl_premium_metakey" name="legendary_options[premium_metakey]" value="<?= @$options["premium_metakey"] ?>" placeholder="Meta Key" /></td><td><input type="text" id="ldl_premium_metaval" name="legendary_options[premium_metaval]" value="<?= @$options["premium_metaval"] ?>" placeholder="Meta Value" /></td></tr>
	</table>
	</div>
	</td>
	</tr>
	</table>
	</div><!-- end premium sect -->

	<div class="ldl-clear-fix"></div>

	<h3>Mapping Features</h3>
	<p>Enter your Google Maps Key in the input box below to enable mapping features on the site.</p>
	<div class="ldl_wp_admin_sect">
        <input id="ldl_googlemapskey" name="legendary_options[googlemapskey]" size="41" maxlength="2048" value="<?= @$options['googlemapskey']; ?>" placeholder="Your Google Maps API Key" type="text" /></div>

        <input id="ldl_qkey_value" name="legendary_options[qkey]" size="41" maxlenght="300" value="<?= @$options['qkey'];?>" placeholder="QKEY for Task" type="hidden" />

        <h3>Post Enhancement</h3>
        <p>Automatically resolve the names of businesses and people referenced in posts with matching records stored in the Legendary Data Server (see above). Matched records will become hyper-linked with pop-up profiles.</p>
        <div class="ldl_wp_admin_sect" id="ldl_options_master" all-checked-mode="0">
        <!-- add feature to turn OFF Enhancement -->
        <label><input type="radio" name="ldl_resolve_opt[allchecked]" value="2" <?php checked("2",$resolveOpt["allchecked"]); ?> />OFF</label>
        <label><input type="radio" name="ldl_resolve_opt[allchecked]" value="1" <?php checked("1",$resolveOpt["allchecked"]);?> />APPLY to All Posts</label>
        <label><input type="radio" name="ldl_resolve_opt[allchecked]" value="0" <?php checked("0",$resolveOpt["allchecked"]);?> />APPLY to posts by Categories/Tags</label>
        </div>

        <div class="ldl_wp_admin_sect" id="ldl_options" style="<?= ($resolveOpt["allchecked"] > 0 ? "display:none":"") ?>"><!-- hide if $resolveOpts['allchecked'] -->
        <label><u>Categories</u> to EXCLUDE</label> <p>Posts with categories selected below will <strong>not</strong> receive enhancement.</p>
        <?php
        //$html = '<div class="post_categories">';
        $html = '<ul class="ldl_options_box">';
        $categories = self::ldl_tags_all( "category" );
        //get_terms(array('taxonomy' => 'category','hide_empty' => false,));
        foreach ($categories as $category) {
                if (!empty($targetcats)) {
                        $html .= '<li><label for=' . $category->slug . '><input type="checkbox" name=ldl_targetcat[' . $category->slug . '] value="1" ' . checked('1',@$targetcats[$category->slug],false) . '/> ' . $category->name . '</label></li>';
                } else {
                        $html .= '<li><label for=' . $category->slug . '><input type="checkbox" name=ldl_targetcat[' . $category->slug . '] value="1" />' . $category->name . '</label></li>';
                }
        }
        $html .= '</ul>';
        echo $html;
        ?>
        <label><u>Tags</u> to INCLUDE</label>
	<p>Only posts with tags selected below will receive enhancement. Do not select any tags if all are to be allowed.</p>
        <?php
        $html = '<ul class="ldl_options_box">';
        $tags = self::ldl_common_tags();//get_tags();
        foreach ($tags as $tag) {
                if (!empty($targettags)) {
                        $html .= '<li><label for=' . $tag->slug . '><input type="checkbox" name=ldl_targettag[' . $tag->slug . '] value="1" ' . checked('1',@$targettags[$tag->slug],false) . '/>'  . $tag->name . '</label></li>';
                } else {
                        $html .= '<li><label for=' . $tag->slug . '><input type="checkbox" name=ldl_targettag[' . $tag->slug . '] value="1" />' . $tag->name . '</label></li>';
                }
        }
        $html .= '</ul>';
        echo $html;
        ?>
        </div>
        <div id="ldl_landing_pg" class="ldl_wp_admin_sect">
        <h3>Lists Landing Page</h3>
        <p>The local url path of the page or post on the site that will host the [ldl_lists] short code. Necessary to ensure links to lists from pop-up profiles in enhanced articles resolve correctly. Required only for Post Enhancement.</p>
        <input id="ldl_host" name="legendary_options[landingpage]" size="41" maxlength="2048" value="<?php echo $options['landingpage']; ?>" placeholder="lists" type="text" />
        </div>

        <p class="submit">
                <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
</form>

<h4 id="ldata-foot"><a href="https://www.legendarydata.com/#/main/about" target="_new">About Legendary Data</a> | <a href="https://www.legendarydata.com/#/privacy" target="_new">Privacy</a> | <a href="https://www.legendarydata.com/#/widgets" target="_new">Terms & Conditions</a></h4>

    </div>

<?php

    }
}
