<?php
/**
 * Executed during plugin activation.
 * This class defines all code necessary to run during the plugin's activation.
 */
class LDL_search_activator {

    // activate() Ensure resources are in place for the plugin.
    public static function activate() {
        if (!is_plugin_active('legendary-core/legendary-core.php') 
                && current_user_can('activate_plugins') 
                && is_admin()) {

            deactivate_plugins(plugin_basename('ldl-search'));
            
            $htm = self::ldl_search_activation_fail_msg();
            
            wp_die($htm);
        }

        //TODO:(ldl) Potentially include checks for key .php or .js or class files (toolbox, ...).
        
        $txt = '<div><em>Legendary Data Search</em> Active</div>';
        $inf = '<div class="notice notice-info is-dismissible">Legendary Search Plugin Activated.</div>';
        
        add_action('notice_info', $inf);
        flush_rewrite_rules();

    }

    // ldl_search_activation_fail_msg() Craft message in case activation fails.
    
    private static function ldl_search_activation_fail_msg() {
        
        $txt = '<div>Issue. Legendary Search Plugin was <em>not</em> activated.</div>';
        $txt .= '<div>Sorry. It appears the required Legendary Core plugin is not installed correclty.<div>';
        $txt .= '<div>Please contact support@legendarydata.com</div>';
        $txt .= '<div><a href="/wp-admin/plugins.php">Return to Plugins</a></div>';
        
        return $txt;
    }
}
