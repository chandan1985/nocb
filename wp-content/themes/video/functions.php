<?php 
if ( function_exists('register_sidebar') ) {
	/*
	 * When adding a new sidebar, you must include a unique ID name to prevent
	 * WordPress from rearranging the sidebars. This won't affect new sites
	 * created after the changes are committed, but it will affect existing sites.
	 */
	register_sidebar( array( 'name' => 'Main Menu', 'id' => 'main-menu', 'before_widget' => '', 'after_widget' => '' ) );
	register_sidebar( array( 'name' => 'Footer Menu', 'id' => 'footer-menu', 'before_widget' => '', 'after_widget' => '' ) );
}

if (!function_exists('themetoolkit')) {
    function themetoolkit($theme='',$array='',$file='') {
        global ${$theme};
        if ($theme == '' or $array == '' or $file == '') {
            die ('No theme name, theme option, or parent defined in Theme Toolkit');
        }
        ${$theme} = new ThemeToolkit($theme,$array,$file);
    }
}
if (!class_exists('ThemeToolkit')) {
    class ThemeToolkit{
        var $option, $infos;
        function __construct($theme,$array,$file){
            global $wp_version;
            if ( $wp_version >= 2 and count(@preg_grep('#^\.\./themes/[^/]+/functions.php$#', get_option('active_plugins'))) > 0 ) {
                wp_cache_flush();
                $this->upgrade_toolkit();
            }
            $this->infos['path'] = '../themes/' . basename(dirname($file));
            if (!empty($array['debug']) && !empty($_GET['page'])) {
                if ((basename($file)) == $_GET['page']) $this->infos['debug'] = 1;
                unset($array['debug']);
            }
            if (!empty($_GET['page']) && (basename($file)) == $_GET['page']){
                $this->infos['menu_options'] = $array;
                $this->infos['classname'] = $theme;
            }
            $this->option=array();
            $this->pluginification();
            $this->do_init();
            $this->read_options();
            $this->file = $file;
            add_action('admin_menu', array(&$this, 'add_menu'));
        }
        function add_menu() {
            global $wp_version;
            if ( $wp_version >= 2 ) {
                $level = 'edit_themes';
            } else {
                $level = 9;
            }
            add_theme_page('Video Control Panel', 'Video Control Panel', 'edit_theme_options', basename($this->file), array(&$this,'admin_menu'));
        }
        function do_init() {
            $themes = wp_get_themes();
            $shouldbe= basename($this->infos['path']);
            foreach ($themes as $theme) {
                $current= basename($theme['Template Dir']);
                if ($current == $shouldbe) {
                    if (get_option('template') == $current) {
                        $this->infos['active'] = TRUE;
                    } else {
                        $this->infos['active'] = FALSE;
                    }
                $this->infos['theme_name'] = $theme['Name'];
                $this->infos['theme_shortname'] = $current;
                $this->infos['theme_site'] = $theme['Title'];
                $this->infos['theme_version'] = $theme['Version'];
                $this->infos['theme_author'] = preg_replace("#>\s*([^<]*)</a>#", ">\\1</a>", $theme['Author']);
                }
            }
        }
        function read_options() {
            $options = get_option('theme-'.$this->infos['theme_shortname'].'-options');
            $options['_________junk-entry________'] = 'junk text';
            foreach ($options as $key=>$val) {
                $this->option["$key"] = stripslashes($val);
            }
            array_pop($this->option);
            return $this->option;
        }
        function store_options($array) {
            update_option('theme-'.$this->infos['theme_shortname'].'-options','');
            if (update_option('theme-'.$this->infos['theme_shortname'].'-options',$array)) {
                return "Settings successfully saved";
            } else {
                return "Could not save settings !";
            }
        }
          function delete_options() {
            delete_option('theme-'.$this->infos['theme_shortname'].'-options');
            $this->depluginification();
            if ($this->infos['active']) {
                update_option('template', 'default');
                update_option('stylesheet', 'default');
                do_action('switch_theme', 'Default');
            }
            print '<meta http-equiv="refresh" content="0;URL=themes.php?activated=true">';
            echo "<script> self.location(\"themes.php?activated=true\");</script>";
            exit;
        }
        function is_installed() {
            global $wpdb;
            $where = 'theme-'.$this->infos['theme_shortname'].'-options';
            $check = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->options WHERE option_name = '$where'");
            if ($check == 0) {
                return FALSE;
            } else {
                return TRUE;
            }
        }
        function do_firstinit() {
            global $wpdb;
            $options = array();
            foreach(array_keys($this->option) as $key) {
                $options["$key"]='';
            }
            add_option('theme-'.$this->infos['theme_shortname'].'-options',$options, 'Options for theme '.$this->infos['theme_name']);
            return "Theme options added in database (1 entry in table '". $wpdb->options ."')";
        }
        function admin_menu () {
			include( 'admin-options.php' );
        }

        function pluginification () {
            global $wp_version;
            if ($wp_version<2) {        
                $us = $this->infos['path'].'/functions.php';
                $them = get_option('active_plugins');
                if (!in_array($us,$them)) {
                    $them[]=$us;
                    update_option('active_plugins',$them); 
                    return TRUE; 
                } else { 
                    return FALSE; 
                } 
            }
        } 
        function depluginification () {
            global $wp_version;
            if ($wp_version<2) {
                $us = $this->infos['path'].'/functions.php';
                $them = get_option('active_plugins');
                if (in_array($us,$them)) {
                    $here = array_search($us,$them);
                    unset($them[$here]);
                    update_option('active_plugins',$them);
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        }
        function upgrade_toolkit () {
            $plugins=get_option('active_plugins');
            $delete=@preg_grep('#^\.\./themes/[^/]+/functions.php$#', $plugins);
            $result=array_diff($plugins,$delete);
            $temp = array();
            foreach($result as $item) $temp[]=$item;
            $result = $temp;
            update_option('active_plugins',$result);
            wp_cache_flush;
        }
    }
}
themetoolkit(
    'quonfig', array( 
	    'listheight' => 'Height of episode list',
		'entrynumber'=> 'Max number of entries',
	    'categorylist' => 'Categories show up in a list or in a dropdown box',
		'diggbutton' => 'Digg buttons enabled',
		'maxentry' => 'Max number of entries before the next button',
		'wdtube' => 'Use of Wordtube',
		'flvplayer' => 'Use of built in flv player',
		'slplayer' => 'Use of built in silverlight player',
		
	),
    __FILE__     
);
function quonfig($i) {
    global $quonfig;
	switch($i) {
	case 1:
	    print $quonfig->option['listheight'];
		break;
	case 2:
	    print $quonfig->option['entrynumber'];
		break;
	case 3:
	    return $quonfig->option['categorylist'];
		break;
	case 4:
	    return $quonfig->option['diggbutton'];
		break;
	case 5:
	    return $quonfig->option['maxentry'];
		break;
	case 6:
	    return $quonfig->option['wdtube'];
		break;
	case 7:
	    return $quonfig->option['flvplayer'];
		break;
	case 8:
	    return $quonfig->option['slplayer'];
		break;
	case 9:
	    return isset($quonfig->option['headerlink']) ? $quonfig->option['headerlink'] : '';
		break;
	case 10:
	    return isset($quonfig->option['headerlogourl']) ? $quonfig->option['headerlogourl'] : '';
		break;
	case 11:
	    return isset($quonfig->option['backgroundlogo']) ? $quonfig->option['backgroundlogo'] : '';
		break;
	case 12:
	    return isset($quonfig->option['footertext']) ? $quonfig->option['footertext'] : '';
		break;
	}
}

if (!$quonfig->is_installed()) {
   $set_defaults['listheight'] = '350';
   $set_defaults['entrynumber'] = '6';
   $set_defaults['categorylist'] = '0';
   $set_defaults['diggbutton'] = '2';
   $set_defaults['maxentry'] = '50';
   $set_defaults['wdtube'] = '2';
   $set_defaults['flvplayer'] = '2';
   $set_defaults['slplayer'] = '2';
   $result = $quonfig->store_options($set_defaults);
}
?>
