<?php

/**
 * Executed during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 */
class Legendary_Core_Activator {

        //activate()
        public static function activate() {
            //add_action('wp_enqueue_scripts','Legendary_Core_Public::legendary_public_assets');
            // clear the permalinks
            flush_rewrite_rules();
        }

}
