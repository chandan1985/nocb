<?php


class LDL_search {

    // Store LD API class 
    protected $LDLAPI;
    // Store Routers; templated responses to API calls
    protected $LDLRouters = [];

    public function __construct() {
        // load core and other dependencies
        $this->load_dependencies();

        // initiate shortcode definitions.
        $this->adjoin_public_hooks();
        $this->adjoin_admin_hooks();
        add_action('wp_enqueue_scripts', array($this, 'ldl_search_enqueue_assets'));
    }


    public static function ldl_search_enqueue_assets() {
        wp_enqueue_style('ldl-search-css', plugins_url('../css/ldl-search-style.css', __FILE__), false, LDL_SEARCH_VERSION, 'all');
        wp_register_script('ld-search-js', plugins_url ('../js/ld-search.js', __FILE__ ), array( 'jquery' ), LDL_SEARCH_VERSION, false );

        // register these - and only use them in the needed shortcodes.
        
        wp_register_script('ld-search-s2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js', array( 'jquery' ), null, false );
        wp_register_script('ld-search-s2-jq-ui', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js', array('jquery'), null, false);
        wp_register_script('ld-s2-adapter', plugins_url('../js/select2-adapter/custom-adapter.js', __FILE__), array('ld-search-s2'), null, false );
        
        wp_register_style('ld-search-s2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css');
        wp_register_style('ld-s2-adapter', plugins_url('../css/select2-adapter/custom-adapter.css',__FILE__), false, '1.0.0', 'all');

        Legendary_Core::enqueue_assets();


    }

    private static function load_dependencies() {
        
        // LD Core classes
        require_once WP_PLUGIN_DIR . '/legendary-core/includes/class-toolbox.php';
        require_once WP_PLUGIN_DIR . '/legendary-core/includes/class-legendary-api.php';

        // Search functionality
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ldl-search-router.php';

        // admin
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-ldl-search-admin.php';

    }

    private function adjoin_public_hooks() {
        add_shortcode('ldl_search_stats', array($this, 'ldl_search_stats'));
        add_shortcode('ldl_search_form', array($this, 'ldl_search_form'));
        add_shortcode('ldl_search_results', array($this, 'ldl_search_results'));

        add_action('wp_ajax_ld_search_label_lookup', array($this, 'ld_search_label_lookup'));
        add_action('wp_ajax_nopriv_ld_search_label_lookup', array($this, 'ld_search_label_lookup'));
    }

    private function adjoin_admin_hooks() {
        
        /** admin_init action is called after admin_menu */
        
        $admin = new LDL_Search_Admin();
        add_action('admin_menu', array($admin, 'search_menu'));
        add_action('admin_init', array($admin, 'search_options'));
        // add_action('admin_footer',array($admin,'ldl_wp_footer'));
        add_action('admin_enqueue_scripts', 'LDL_Search_Admin::enqueue_assets');
    }


    public function ldl_search_stats($attrs, $content = null) {
        $router = $this->get_router("ldl_search_stats");
        return $router->display();
    }

    public function ldl_search_form($attrs, $content = null) {
        $router = $this->get_router("ldl_search_form");
        return $router->display();
    }

    public function ldl_search_results($attrs, $content = null) {
        $router = $this->get_router("ldl_search_results");
        return $router->display();
    }

    private function set_API($c) {
        $this->LDLAPI = $c;
    }

    private function get_API()
    {
        if ($this->LDLAPI == null) {
		$options = Toolbox::get_option('legendary_options', []);
		$LDLAPI = new legendary_api($options["akey"], $options["ekey"], $options["host"]);
		$LDLAPI->setPremium( Legendary_Core::hasPremiumAccess( $options ) );
		$this->set_API($LDLAPI);
        }

        return $this->LDLAPI;
    }

    

    public function ld_search_label_lookup() {

        $lAPI = $this->get_API();
        $lbl_key = $_REQUEST['label'];
        $data = array("MODE" => "LABELSEARCH", "LABELKEY" => $lbl_key, "LABELVAL" => "*" );
        $msg = $lAPI->signedRequest($data, "MCORE");

        $items = [];
		foreach ($msg['LABELS'] as $label) {
			$items[] = ['id' => $label['LABELID']."|".str_replace("|","",$label["NAME"]), 'text' => $label['NAME']];
		}
		
		echo json_encode($items);
		die();
    }

    private function set_router($sContext, $r)
    {
        $this->LDLRouters[$sContext] = $r;
    }

    private function get_stored_router($sContext)
    {
        if (array_key_exists($sContext, $this->LDLRouters)) {
            return $this->LDLRouters[$sContext];
        }
        return null;
    }

    private function get_router($sContext)
    {
        if (($ldlRouter = $this->get_stored_router($sContext)) == null) {

            /* instantiate the router object */
            $ldlRouter = new LDL_search_router();
            $LDLAPI = $this->get_API();
            
            $ldlRouter->route($LDLAPI, $sContext);

            $this->set_router($sContext, $ldlRouter);

        }

        return $ldlRouter;
    }


    public function run() {
        return;
    }
}
