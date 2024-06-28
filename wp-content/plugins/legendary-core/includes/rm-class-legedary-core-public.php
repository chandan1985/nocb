<?php

/*
Resources for the public side of the site.
 */
class Legendary_Core_Public
{
    public function __construct()
    {
        $this->load_dependencies();
    }

    /*
     load_dependencies() Load needed dependencies needed for this plugin.
      class-toolbox ; Methods for common WP resources.
      ldl-core ; Legendary methods for interface with meroveus instance. TODO:(ldl) Ingest into class-legendary-core or -public?
      ldl-core-router ; Legeneary methods for routing responses and managing content.
     */
    private static function load_dependencies()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-toolbox.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/ldl-core.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/ldl-core-router.php';
    }


}
