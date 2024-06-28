<?php
/*
 * Define some toolbox functions
 */

class Toolbox
{
    public function __construct()
    {
        $this->load_dependencies();
    }

    private function load_dependencies()
    {
        //require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-legendary-core-admin.php';
    }

    /*
       [*]_option functions below are a  'wrap' for multisite behavior.
   */

   public static function add_option($k,$v) {
        if (is_multisite()) {
                global $blog_id;
                add_blog_option($blog_id,$k,$v);
        } else {
                add_option($k,$v);
        }
   }

   public static function get_option($k,$v) {
        if (is_multisite()) {
                global $blog_id;
                $result = get_blog_option($blog_id,$k,$v);
        } else {
                $result = get_option($k,$v);
        }
        return $result;
   }

   public static function delete_option($k,$v) {
        if (is_multisite()) {
                global $blog_id;
                delete_blog_option($blog_id,$k);
        } else {
                delete_option($k);
        }
   }

   public static function update_option($k,$v) {
        if (is_multisite()) {
                global $blog_id;
                update_blog_option($blog_id,$k,$v);
        } else {
                update_option($k,$v);
        }
   }

  // For planning/future; update the call for register_setting()
  function options_validate($opts) {
        //place to review , sanitize / clean input
        return $opts;
  }

}

