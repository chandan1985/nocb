<?php
/*
 * Defines the Legendary Core class plugin
 */

class Legendary_Core
{
    protected $LDLAPI;
    protected $LDLRouter; //DEH ; mv to ldl-core

    public function __construct()
    {
        $this->load_dependencies();
        // $this->set_locale(); //TODO:(LDL) Evolve to include localization
        $this->adjoin_admin_hooks();
        $this->adjoin_public_hooks();
    }

    /*
     load_dependencies() Load needed dependencies needed for this plugin.
      class-toolbox ; Methods for common WP resources.
      class-ldl-core ; Legendary methods for interface with meroveus instance.
      class-ldl-router ; Legeneary methods for routing responses and managing content.
     */
    private static function load_dependencies()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-toolbox.php';
        /** Class for plugin admin set up */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-legendary-core-admin.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-legendary-api.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-legendary-router.php';
    }

    // adjoin_admin_hooks() Add actions and filters for Administration of the plugin.
    private function adjoin_admin_hooks()
    {
        /** admin_init action is called after admin_menu */
        $admin = new Legendary_Core_Admin();
        add_action('admin_menu', array($admin, 'adm_menu'));
        add_action('admin_init', array($admin, 'adm_options'));
        add_action('admin_footer',array($admin,'ldl_wp_footer'));
        add_action('admin_enqueue_scripts', 'Legendary_Core_Admin::enqueue_assets');
    }

    // adjoin_public_hooks()  Add actions and any filters for 'Public' function of the plugin.
    private function adjoin_public_hooks()
    {
        add_shortcode('ldl_lists', array($this, 'lists_shortcode'));
        add_action('wp_ajax_ldl_relay', array($this, 'ldl_relay_callback'));
        add_action('wp_ajax_nopriv_ldl_relay', array($this, 'ldl_relay_callback'));
        add_filter("template_redirect", array($this,'ldl_loadAssets'));
        add_filter('the_content', array($this, 'ldl_enhance'));
        add_filter('pre_get_document_title', array($this,'ldl_alter_title'));
        add_filter('the_title', array($this,'ldl_alter_content_title'));
	/* 20210129 tdf added the following to prevent filtering of menu names */
	add_filter( 'pre_wp_nav_menu', array($this,'ldl_remove_title_filter_nav_menu'), 10, 2 );
	add_filter( 'wp_nav_menu_items', array($this, 'ldl_add_title_filter_non_menu'), 10, 2 );

    }

	/* 20210129 tdf added the following two methods to prevent filtering of menu names */
	public function ldl_remove_title_filter_nav_menu( $nav_menu, $args ) {
    		// if traversing menu, remove the title filter
    		remove_filter( 'the_title', array($this,'ldl_alter_content_title'), 10, 2 );
    		return $nav_menu;
	}

	public function ldl_add_title_filter_non_menu( $items, $args ) {
		// we are done working with menu, so add the title filter back
		add_filter( 'the_title', array($this,'ldl_alter_content_title'), 10, 2 );
		return $items;
	}

	/* 20210316 tdf break up enqueue_assets into separate methods to allow great discretion when including list-specific assets vs core assets */
	public static function enqueue_assets() {
		self::enqueue_list_assets();
		self::enqueue_core_assets();
	}

    // enqueue_assets() Make available the resources for the public side.
    	public static function enqueue_core_assets() {
        	$options = Toolbox::get_option('legendary_options', []);
 
       		/*if (array_key_exists("googlemapskey", $options) && $options["googlemapskey"] != "") {
			wp_enqueue_script('googlemaps', 'https://maps.googleapis.com/maps/api/js?key=' . $options["googlemapskey"], array(), null, false);
			wp_enqueue_script($options["host"] . '/js/google.infobox.min.js', array(), null, false);
        	}
        	wp_enqueue_script('ldlchartjs', $options["host"] . '/js/chartjs.bundle.min.js', array(), null, false);
        	wp_enqueue_script('ldlchartjsplugin', $options["host"] . '/js/chartjs.plugin.annotation.min.js', array(), null, false);*/

		wp_enqueue_script('ldlmythyr', $options["host"] . '/js/mythyr-widget.min.js', array(), LDL_CORE_VERSION);
		wp_enqueue_script('legendary-js', plugins_url('../js/legendary.js', __FILE__), array('jquery'), LDL_CORE_VERSION);
		wp_enqueue_style('legendary-css', plugins_url('../css/legendary.css', __FILE__), null, LDL_CORE_VERSION);
	}

	public static function enqueue_list_assets() {
		$options = Toolbox::get_option('legendary_options', []);
		if (array_key_exists("googlemapskey", $options) && $options["googlemapskey"] != "") {
			wp_enqueue_script('googlemaps', 'https://maps.googleapis.com/maps/api/js?key=' . $options["googlemapskey"], array(), null, false);
			wp_enqueue_script($options["host"] . '/js/google.infobox.min.js', array(), LDL_CORE_VERSION);
		}
		wp_enqueue_script('ldlchartjs', $options["host"] . '/js/chartjs.bundle.min.js', array(), LDL_CORE_VERSION);
		wp_enqueue_script('ldlchartjsplugin', $options["host"] . '/js/chartjs.plugin.annotation.min.js', array(), LDL_CORE_VERSION);
	}

    /* 20200330 tdf wrap native get_post function to allow local storage and prevent unneeded sql calls */
    public function ldl_get_post()
    {
        /* access the global post rather than call get_post() */
        global $post;
        return $post;
    }

    // ldl_is_single() ; (tmp) Wrap is_single()
    public function ldl_is_single()
    {
        return is_single();
    }

    // SetAPI() This instance's LDL Core Object is set here.
    private function SetAPI($c)
    {
        $this->LDLAPI = $c;
    }

	/* OLD VERSION OF hasPremiumAccess
	public static function hasPremiumAccess($options) {
		if ( !isset( $options["premium_akey"] ) || strlen($options["premium_akey"]) != 25 ) {
			return false;
		}
		$user = ldl_get_current_user();
		$aRoles = is_array($user->roles) ? $user->roles : array();
		if ( array_search("ldata-premium-access", $aRoles) !== FALSE ) {
			/* standard, extensible indicator of premium access *
			return true;
		}
		if ( isset( $options["premiumrole"] ) ) {
                        /* if user has the premium role, then use the premium akey *
                        if ( array_search($options["premiumrole"], $aRoles) !== FALSE ) {
                                //$aKey = $options["premium_akey"];
				return true;
                        }
		}
		if ( isset( $options["premium_metakey"] ) && isset( $options["premium_metaval"] ) ) {
			if ( $options["premium_metakey"] == "" || $options["premium_metaval"] == "" ) return false;
			return @(ldl_get_user_meta( $user->ID, $options["premium_metakey"], true ) == $options["premium_metaval"]);
		}
		return false;
	}*/

	public static function hasPremiumAccess( $options ) {
		if ( ! isset( $options["premium_akey"] ) || strlen( $options["premium_akey"] ) != 25 ) {
			return false;
		}

		$user = ldl_get_current_user();

		$aRoles = is_array($user->roles) ? $user->roles : array();
                if ( array_search("ldata-premium-access", $aRoles) !== FALSE ) {
			/* standard, extensible indicator of premium access */
			return true;
                }

		if ( isset( $options["premiumrole"] ) ) {
			/* if user has the premium role(s), then use the premium akey */
			if ( !is_array( $options["premiumrole"] ) ) {
				/* to keep the plugin backward compatible */
				if ( in_array( $options["premiumrole"], (array) $user->roles ) ) {
					return apply_filters( 'ld_has_premium_access', true);
				}
			} else {
				/* premiumrole set as an array */
				foreach ( $options["premiumrole"] as $r ) {
					if ( in_array( $r, (array) $user->roles ) ) {
						return apply_filters( 'ld_has_premium_access', true);
					}
				}
			}
		}
		if ( ( isset( $options["premium_metakey"] ) && ! empty( $options['premium_metakey'] ) ) && ( isset( $options["premium_metaval"] ) && ! empty( $options['premium_metaval'] ) ) ) {
			return apply_filters( 'ld_has_premium_access', @( ldl_get_user_meta( $user->ID, $options["premium_metakey"], true ) == $options["premium_metaval"] ));
		}

		return apply_filters( 'ld_has_premium_access', false);
	}

    // GetAPI() Provide the instance's LDL Core Object.
    private function GetAPI()
    {
	if ($this->LDLAPI == null ) {
            	$options = Toolbox::get_option('legendary_options', []);
		/* 20200715 tdf added stanza to support premium level user access */
		$aKey = $options["akey"];
		
		if ( ($bPremium = self::hasPremiumAccess( $options )) == true ) {
			$aKey = $options["premium_akey"];
		}
            	$LDLAPI = new legendary_api($aKey, $options["ekey"], $options["host"]);
	    	/* store internally whether the core is premium or not */
	    	$LDLAPI->setPremium( $bPremium );
            	$this->SetAPI($LDLAPI);
        }
        return $this->LDLAPI;
    }

    // ldl_loadAssets() Goal to have remote assets on page. Here callable resource.
	public function ldl_loadAssets() {
        	if ($this->ldl_contains_shortcode() ) {
			self::enqueue_list_assets();
        	}	
		if ( $this->ldl_is_enhanced_content() || $this->ldl_contains_shortcode() ) {
			self::enqueue_core_assets();
		}
        
   	 }
    // SetRouter() This instance's Router Object is set here.
    private function SetRouter($r)
    {
        $this->LDLRouter = $r;
    }

    // GetRouter() Provide the instance's Router Object.
    private function GetRouter( $sContext="main" )
    {
        if ($this->LDLRouter == null) {
            	/* instantiate the router object */
            	$ldlRouter = new legendary_router();
            	$LDLAPI = $this->GetAPI();
		$ldlRouter->route( $LDLAPI, $sContext );
		$this->SetRouter($ldlRouter);
	    	 
        }
        return $this->LDLRouter;
    }

    public function ldl_wp_footer() {
        ?> <script>LD.initPage();</script>
        <?php
    }
    
    // ldl_alter_title() Enhance the title on the page
    public function ldl_alter_title($title)
    {
        $post = $this->ldl_get_post();
        if (is_object($post) && strpos($post->post_content, "[ldl_lists]") !== false) {
            /* return the page title */
            $ldlRouter = $this->GetRouter();
            $tit = $ldlRouter->getPageTitle($post->post_title);
            return $tit;
        }
        return $title;
    }

    // ldl_alter_content_title() Enhance the title of the content on the page.
    public function ldl_alter_content_title( $sTitle ) {
        $post = $this->ldl_get_post();
        if ( is_object($post) && $sTitle == $post->post_title && strpos($post->post_content, "[ldl_lists]") !== false ) {
            $ldlRouter = $this->GetRouter();
            $tit = $ldlRouter->getListTitle($post->post_title);
            return $tit;
        }
        return $sTitle;
}



    // ldl_relay_callback() AJAX for core functionality.
    public function ldl_relay_callback()
    {
        /*if (isset($_POST["admin_host"])) {
            $host = $_POST["admin_host"];
            $aKey = $_POST["admin_akey"];
            $eKey = $_POST["admin_ekey"];
        } else {
            /* now acquire the settings variables *
            $options = Toolbox::get_option('legendary_options', []);
            $host = $options["host"];
            $aKey = $options["akey"];
            $eKey = $options["ekey"];
        }*/
        /* instantiate the core object */
       $oCore = $this->GetAPI();
        die($oCore->relay());
    }

    // ldl_enhance() Enrich content with Business Data from Legendary instance
    public function ldl_enhance($content)
    {
        if ($this->ldl_is_single()) {
            if (strpos($content, 'ldl-host="') !== false) {
                //exit -- do not append the javasscript widget if one of our widgets already included in the post
                $content .= '<!-- Has content with the LDL Mythyr widget.-->';
                return $content;
            }
            global $post;
            global $wpdb;
            $postid = $post->ID;

            $defarr = [];
            $options = Toolbox::get_option('legendary_options', []);
            $ldlopts = Toolbox::get_option('ldl_resolve_opt', array("allchecked" => 2));
            $targetcats = Toolbox::get_option('ldl_targetcat', $defarr); //dh - make default an empty array
            $targettags = Toolbox::get_option('ldl_targettag', $defarr); //dh - make default an empty array

            if ($ldlopts['allchecked'] == "2") {
                return $content;
            }
            $host = $options['host'];
            /* dh - here ensure the url has schema or a default of https://www.legendarydata.com */
            if (filter_var($host, FILTER_VALIDATE_URL) === false) {
                $host = "https://www.legendarydata.com";
            }

            $modDt = $post->post_modified;
            $pubTtl = str_replace('"', "", $post->post_title);
            $oCore = $this->GetAPI();
            $ldlwidget = $oCore->getResolveWidget($options["landingpage"], $postid, $pubTtl, $modDt);

            if ($ldlopts['allchecked']) {
                $content .= $ldlwidget;
                return $content;
            }

            if (!empty($targetcats)) {
                foreach ($targetcats as $catslug => $onOff) {
                    if (has_category($catslug)) {
                       return $content;
                    }
                    /* 20200609 tdf removed the following lines -- just because exclude satsified doesn't mean INCLUDE only satisfied */
	 	    /*$content .= $ldlwidget;
                    return $content;*/
                }
            }
            //moves Tag check as Categories are potentially Excluded            
            if (!empty($targettags)) {
                foreach ($targettags as $tgtslug => $onOff) {
                    if (has_tag($tgtslug)) { 
                        $content .= $ldlwidget;
                        return $content;
                    }
                }
            } else if ( !empty($targetcats) ) {
		/* if no tags selected but exclude categories available then insert widget */
			$content .= $ldlwidget;
                        return $content;
		}

    }
	return $content;
 }
    // ldl_get_core_router_main() ; Return results from LDL route 'main'.
    /*public function legendary_api_router_main()
    {
        $ldlRouter = $this->GetRouter();
        $LDLAPI = $this->GetAPI();
        $ldlRouter->route($LDLAPI,"main");//updated $output for display()
        return;
    }*/

    // ldl_contains_shortcode() ; Check to see if page contains "[ldl_lists]"
    public function ldl_contains_shortcode()
    {
        global $post;
        return @(strpos($post->post_content, "[ldl_lists]") !== false);
    }

    /* ldl_is_enhanced_content()
    check to see if the page will be parsed for enhanced hyperlinking of business names / exec names
     */
    public function ldl_is_enhanced_content()
    {
        if (!is_object($this->ldl_get_post()) || !$this->ldl_is_single()) {
            /* not a qualifying block of content for enhancement */
            return false;
        }
        $ldlopts = Toolbox::get_option('ldl_resolve_opt', array("allchecked" => 2));
        if ($ldlopts['allchecked'] == "2") {
            /* enhancement setting currently set to OFF (2) */
            return false;
        }
        /* YES! the content is likely enhanced */
        return true;
    }

   /*
    lists_shortcode()
    Callable for the [ldl_lists] shortcode.
    Use methods from legendary_api to generate the results.
     */
    public function lists_shortcode()
    {
        $ldlRouter = $this->GetRouter();
        return $ldlRouter->display();
    }

    //run() Method called on new instance creation.
    public function run()
    {
        return;
    }
}
