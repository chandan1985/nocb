<?php

/**
 * Executed during plugin activation.
 * This class defines all code necessary to run during the plugin's activation.
 */

class LDL_search_deactivator {

    public static function deactivate() {
        flush_rewrite_rules();
    }

}
