<?php

class LDL_Search_Admin {
    public function __construct() {
		// add_action( 'wp_ajax_fieldsearch',        array($this, 'get_search') );
    }

    public function load_dependencies() {
        require_once WP_PLUGIN_DIR . '/legendary-core/includes/class-toolbox.php';
    }

    public function search_menu() {

        add_options_page('Legendary Data Search Plugin', //Page Title
            'Legendary Data Search',   //Menu Title 
            'manage_options',   //permission
            'admin_legendary_search',  //menu slug
            array($this,'ld_search_options')
        );
    }

    public function search_options() {
        
        $toolbox = new Toolbox();

        $search_opts = array('ld-search-business-list' => '', 'ld-search-executive-list' => '', 'ld-search-ordered-fields' => '', 'ld-search-subscribe-page'=>'', 'ld-search-results-page'=>'', 'ld-container-field-pair'=>"");

        register_setting('ld_search','ld_search_options');

        $toolbox->add_option('ld_search_options', $search_opts);
    }

    public function ld_search_options() {

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        $toolbox = new Toolbox(); 
        $core_options = $toolbox->get_option('legendary_options', []);
        $search_options = $toolbox->get_option('ld_search_options', []);

        $sof = htmlspecialchars($search_options['ld-search-ordered-fields']);

        ?>

<div class="wrap">
    <h2 id="ldata-hed"><a href="https://www.legendarydata.com" target="_new">
        <img src="https://www.legendarydata.com/images/hed-logo.png" /></a><br /><b>for WordPress</b>
    </h2>
    <h3>Search Settings</h3>
    <p>Configure your custom search interface with the inputs below. Be sure to populate all fields and for the two lists selected, be sure to assign in your Legendary Data database permissions for both lists corresponding to the AKEY and Premium AKEY accounts referenced in your core Legendary Data plugin settings.</p>
	<p>Plugin supports three distinct shortcodes:</p>
	<ul>
	<li><b>[ldl_search_stats]</b> &mdash; displays key statistics across available lists</li>
	<li><b>[ldl_search_form]</b> &mdash; displays simple form for performing custom searches</li>
	<li><b>[ldl_search_results]</b> &mdash; displays results of custom search</li>
	</ul>
    <form id="ldl_option_form" method="post" action="options.php">
    <?php
        
        // settings_fields outputs html
        settings_fields('ld_search');
    ?>
        <input type="hidden" name="akey" value="<?= $core_options['akey'] ?>">
        <input type="hidden" name="ekey" value="<?= $core_options['ekey'] ?>">
        <input type="hidden" name="host" value="<?= $core_options['host'] ?>">    
        
        <div ldl-lookup="business-list" class="ldl_lookup_block" ldl-status="error">

            <div class="ldl_wp_admin_sect"><label>Select Business List</label>
                <div class="ldl-error">Loading ...</div>
                <select id="ld_search_business_list">
                </select> 
            </div>
        </div>

        <div ldl-lookup="person-list" class="ldl_lookup_block" ldl-status="error">
            <div class="ldl_wp_admin_sect"><label>Select Person List</label>
                <div class="ldl-error">Loading ...</div>
                <select id="ld_search_person_list"></select>
            </div>
        </div>

	<div ldl-lookup="container-field-pairs" class="ldl_lookup_block" ldl-status="error">
            <div class="ldl_wp_admin_sect"><label>Select Business/Person Container Pair</label>
                <div class="ldl-error">Loading ...</div>
                <select id="ld_search_container_field_pair"></select>
            </div>
        </div>
        
        <div class="" style="margin-top: 10px;">
            <div class="ldl_wp_admin_sect">
                <label>Select Fields</label>
                <span>Search for up to 5 fields.</span><br/>

                <select class="ld-search-fields">
                <option></option>
                </select>
            </div>
        </div>

        <p style="margin:5px 0 0;">Drag and drop selected fields to control the order</p>
        
        <div class="ld-search-fields-data sortable" style="margin-top: 10px;margin-bottom:10px;">
        </div>
    
	<div class="ldl_wp_admin_sect"> 
	<label>Results Page</label> 
	<p style="margin:0;">Enter the <u>Results Page</u> &mdash; the page that will host the [ldl_search_results] short code</p> 
	<input type="text" name="ld_search_options[ld-search-results-page]" value="<?= @$search_options['ld-search-results-page']; ?>" />
 	</div>

	<div class="ldl_wp_admin_sect">
        <label>Subscribe Page</label>
        <p style="margin:0;">Enter the <u>Subscribe Page</u> to which non-premium users will be redirected on search attempt</p>
        <input type="text" name="ld_search_options[ld-search-subscribe-page]" value="<?= @$search_options['ld-search-subscribe-page']; ?>" />
        </div>

	<input id="s_container-field-pairs" name="ld_search_options[ld-container-field-pair]" value="<?= @$search_options['ld-container-field-pair']; ?>" type="hidden" />
        <input id="s_business_listid_value" name="ld_search_options[ld-search-business-list]" value="<?= @$search_options['ld-search-business-list']; ?>" type="hidden" />
        <input id="s_person_listid_value" name="ld_search_options[ld-search-executive-list]" value="<?= @$search_options['ld-search-executive-list']; ?>" type="hidden" />
        <input id="s_search-ordered-fields" name="ld_search_options[ld-search-ordered-fields]" value="<?= $sof ?>" type="hidden" />
        
        <p class="submit">
            <input type="submit" id="ldl-search-save-btn" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>

</div>
        <?php
    }

    public static function enqueue_assets($hook=null) {   
	/* 20210528 tdf only enqueue admin assets if actually on the ldl search admin settings page */
	if ( $hook != 'settings_page_admin_legendary_search' ) {
		return;
	}

        wp_enqueue_script( 'ld-search-admin-js', plugins_url ( '../js/ld-search-admin.js', __FILE__ ), array( 'jquery' ), LDL_SEARCH_VERSION, false );
        wp_localize_script( 'ld-search-admin-js', 'ld_search_options', array());
        
        wp_enqueue_script( 'ld-search-select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js', 
                    array( 'jquery' ), null, false );

        wp_enqueue_script('ld-search-jq-ui', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js', array('jquery'), null, false);
        
        wp_enqueue_style('ld-search-select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css');
        wp_enqueue_style('ldl-search-css', plugins_url('../css/ldl-search-style.css', __FILE__), false, LDL_SEARCH_VERSION, 'all');

    }

}
