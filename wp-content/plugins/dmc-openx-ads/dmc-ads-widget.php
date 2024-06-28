<?php
/*
Plugin Name: DMC OpenX Ad Widget
Plugin URI: http://www.dolanmedia.com
Description: Widget used to display links in the sidebar
Version: 1.0.1
Author: Ray Champagne
Author URL: http://raychampagne.com/
*/
	
function openX_get_category_keywords($storyID) {
	$category_list = get_the_category($storyID);
  $catNames = '';
	if($category_list){		
		foreach($category_list as $category) {
			$catParent = get_category_parents($category->cat_ID, false, '|');
			$catNames .= $catParent;
		}
		$catNames = rtrim($catNames, '|');
	}
	return $catNames; //Return post category names as keywords
}
	
class DMC_OpenX2 extends WP_Widget {
    var $plugin_folder = '';
	
    var $default_options = array(
        'title' => '', 
        'zoneID' => '',
        'paddingtop' => '0',
        'paddingright' => '0',
        'paddingbottom' => '0',
        'paddingleft' => '0',
		'alignment' => '',
		'deliveryserver' => ''
    );

    function __construct() {
        $this->plugin_folder = get_option('home').'/'.PLUGINDIR.'/dmc-openx-ads/';
        add_action('admin_head', array(&$this, 'admin_head'));
				
        $widget_ops = array('classname' => 'widget_dmc_openx', 'description' => 'DMC OpenX Ads');
        $control_ops = array('width' => 250, 'height' => 100, 'id_base' => 'dmc_openx');
        parent::__construct('dmc_openx', __('DMC OpenX'), $widget_ops, $control_ops);
		
		if( false == get_option( 'deprecated_openxwpwidget_url2openx' ) ) {
			add_option( 'deprecated_openxwpwidget_url2openx', get_option( 'openxwpwidget_url2openx' ) );
		}
      
	  	if( false == get_option( 'new_openxwpwidget_url2openx' ) ) {
			add_option( 'new_openxwpwidget_url2openx', '' );
		}
    }

    function admin_head() {
        //do nothing for now
        //echo('<link rel="stylesheet" href="'.$this->plugin_folder.'gd-multi.css" type="text/css" media="screen" />');
        //echo('<script type="text/javascript" src="'.$this->plugin_folder.'gd-multi.js"></script>');
    }

    function widget($args, $instance) {
        extract( $args, EXTR_SKIP );
        extract( $instance );
        
    	// retrieve our global widget options and settings
		if( empty( $deliveryserver ) ) {
			$location = stripslashes(get_option('deprecated_openxwpwidget_url2openx'));
		}
		else {
			$location = stripslashes( $deliveryserver );
		}
	
        $before_title = "";
        $after_title = "";
        if (wp_get_theme() =='Jarida') {
        	$after_title = '<div class="widget-container">';
        }
        
        //build the html div tag 
        $before = '';
        $before .= '<div id="open-x" align="';
		$before .= $alignment . '" style="';		
        $before .= 'padding-top:';
        $before .= $paddingtop . 'px;';
        $before .= 'padding-right:';
        $before .= $paddingright . 'px;';
        $before .= 'padding-bottom:';
        $before .= $paddingbottom . 'px;';
        $before .= 'padding-left:';
        $before .= $paddingleft . 'px;';
        $before .='">';
        
        $after = '';
        $after = '</div>';
		
        //get the code to display the ad on the page
        $basecode = $this->_openxwpwidget_get_invocation($location, $zoneID);				
		echo $before_widget;		 
        echo $before;
		echo $before_title;  
        echo $after_title;
        echo $basecode;
        echo $after;
        echo $after_widget;
    }
    
    function update($new_instance, $old_instance) {
		$instance = $old_instance;
	
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['zoneID'] = strip_tags($new_instance['zoneID']);
        $instance['paddingtop'] = strip_tags($new_instance['paddingtop']);
        $instance['paddingright'] = strip_tags($new_instance['paddingright']);
        $instance['paddingbottom'] = strip_tags($new_instance['paddingbottom']);
        $instance['paddingleft'] = strip_tags($new_instance['paddingleft']);
        $instance['alignment'] = strip_tags($new_instance['alignment']);
		$instance['deliveryserver'] = strip_tags($new_instance['deliveryserver']);
		
		return $instance;
	}

	function form($instance) {
		$instance = wp_parse_args((array)$instance, array(
				'title' => $this->default_options['title'],
				'zoneID' => $this->default_options['zoneID'],
				'paddingtop' => $this->default_options['paddingtop'],
				'paddingright' => $this->default_options['paddingright'],
				'paddingbottom' => $this->default_options['paddingbottom'],
				'paddingleft' => $this->default_options['paddingleft'],
				'alignment' => $this->default_options['alignment'],
				'deliveryserver' => get_option('deprecated_openxwpwidget_url2openx')
			)
		);
		
        $title = strip_tags($instance['title']);
        $zoneID = strip_tags($instance['zoneID']);
        $paddingtop = (int)$instance['paddingtop'];
        $paddingright = (int)$instance['paddingright'];
        $paddingbottom = (int)$instance['paddingbottom'];
        $paddingleft = (int)$instance['paddingleft'];
        $alignment = strip_tags($instance['alignment']);
		$deliveryserver = strip_tags($instance['deliveryserver']);
        include("dmc_openx_form.php");
    }
	
    function render_options($opts = array()) {
    	//render options after they are posted?    
    }
/*============================================================================================================================================================================*/
/* modified invocation code to include category on posts/pages (TRS) */   
	function _openxwpwidget_get_invocation($location, $zoneID)
    {
        if (empty($location) || $location == '' || intval($zoneID) == 0)
            return '';

        $random = md5(rand(0, 999999999));
        $n = substr(md5(rand(0, 999999999)), 0, 6);

		
		if (is_single()) {
			global $post;
			$categories = openX_get_category_keywords($post->ID);
			$thesecatnames = addslashes($categories);
		}

		if (is_category()) {
			$thesecatnames = addslashes(single_cat_title( '', false ));
		}
			
if (is_single() || is_category())
{		
        return 

"

<!--/* OpenX Javascript Tag v2.5.60-beta */-->

<script type='text/javascript'><!--//<![CDATA[
   var m3_u = (location.protocol=='https:'?'https://" . $location . "/ajs.php':'http://" . $location . "/ajs.php');
   var m3_r = Math.floor(Math.random()*99999999999);
   if (!document.MAX_used) document.MAX_used = ',';
   document.write (\"<scr\"+\"ipt type='text/javascript' src='\"+m3_u);
   document.write (\"?zoneid=". $zoneID ."\");
   document.write ('&amp;category=". $thesecatnames ."');
   document.write ('&amp;cb=' + m3_r);
   if (document.MAX_used != ',') document.write (\"&amp;exclude=\" + document.MAX_used);
   document.write (\"&amp;loc=\" + escape(window.location));
   if (document.referrer) document.write (\"&amp;referer=\" + escape(document.referrer));
   if (document.context) document.write (\"&context=\" + escape(document.context));
   if (document.mmm_fo) document.write (\"&amp;mmm_fo=1\");
   document.write (\"'><\/scr\"+\"ipt>\");
//]]>--></script><noscript><a href='https://$location/ck.php?n=$n&amp;cb=$random' target='_blank'><img src='https://" . $location . "/avw.php?zoneid=$zoneID&amp;cb=$random&amp;n=$n' border='0' alt='' /></a></noscript>
";
}

else

{
       return 

"

<!--/* OpenX Javascript Tag v2.5.60-beta */-->

<script type='text/javascript'><!--//<![CDATA[
   var m3_u = (location.protocol=='https:'?'https://" . $location . "/ajs.php':'http://" . $location . "/ajs.php');
   var m3_r = Math.floor(Math.random()*99999999999);
   if (!document.MAX_used) document.MAX_used = ',';
   document.write (\"<scr\"+\"ipt type='text/javascript' src='\"+m3_u);
   document.write (\"?zoneid=". $zoneID ."\");
   document.write ('&amp;cb=' + m3_r);
   if (document.MAX_used != ',') document.write (\"&amp;exclude=\" + document.MAX_used);
   document.write (\"&amp;loc=\" + escape(window.location));
   if (document.referrer) document.write (\"&amp;referer=\" + escape(document.referrer));
   if (document.context) document.write (\"&context=\" + escape(document.context));
   if (document.mmm_fo) document.write (\"&amp;mmm_fo=1\");
   document.write (\"'><\/scr\"+\"ipt>\");
//]]>--></script><noscript><a href='https://$location/ck.php?n=$n&amp;cb=$random' target='_blank'><img src='https://" . $location . "/avw.php?zoneid=$zoneID&amp;cb=$random&amp;n=$n' border='0' alt='' /></a></noscript>
";
}

    
   }

    
    function openxwpwidget_admin_menuitem()
    {
        if (function_exists('add_options_page')) {
            add_options_page( 'DMC OpenX', 'DMC OpenX', 'manage_options', 'dmc-openx', array($this, 'openxwpwidget_adminsection'), 8 );
            //add_action( "admin_print_scripts", 'openxwpwidget_admin_head' );
        }
    }
    
	/**
     * this callback function gets called for every real content
     * delivered to normal users. The callback will be installed
     * below (near end-of-file)
     *
     * @param string the content
     *
     * @return string the (maybe un-) modified content
     */
    function openxwpwidget_replace_magic($content)
    {
       // find the magic zone-tags somehow, we replace {openx:NNN}
       // with a invocationcode, whereas NNN is a zoneID
       if (($matches = preg_match_all('/\{openx\:(\d+)\}/', $content, $aResult)) !== false) {
           $content = $this->_openxwpwidget_replace_zones($content, $aResult);
       }

       return $content;
    }
	
    /**
     * this function replace any magic openx-zones in the given content
     *
     * @param string the content
     * @param array of strings with zone-numbers found in content
     *
     * @return string the (maybe un-) modified content
     */
    function _openxwpwidget_replace_zones($content, $aZones)
    {
        $url2openx = get_option('deprecated_openxwpwidget_url2openx');
        $url2openx = stripslashes($url2openx);

        // prepare our search/replacement, with perl I would have
        // used a closure to replace it in a single-path
        $from = array();
        $to = array();
        if($aZones){
            foreach ($aZones as $hits) {
                $zoneID = isset($hits[0]) ? $hits[0] : 0;
                $random = md5(rand(0, 999999999));
                $from[] = '{openx:' . $zoneID . '}';
                $to[]   = $this->_openxwpwidget_get_invocation($url2openx, $zoneID);
            }
        }
        return str_replace($from, $to, $content);
    }
    
    /** this function represents the admin setup page for this
     *  plugin.
     */
    function openxwpwidget_adminsection()
    {	
        if (isset($_POST['deprecated_openxwpwidget_url2openx'])) {
            $url2openx = $_POST['deprecated_openxwpwidget_url2openx'];
            // remove a trailing http://, internally we use it without
            $url2openx = preg_replace('/^https?:\/\//', '', $url2openx);
            update_option('deprecated_openxwpwidget_url2openx', $url2openx);
        }
        $deprecated_url2openx = stripslashes(get_option('deprecated_openxwpwidget_url2openx'));
		
		if (isset($_POST['new_openxwpwidget_url2openx'])) {
            $url2openx = $_POST['new_openxwpwidget_url2openx'];
            // remove a trailing http://, internally we use it without
            $url2openx = preg_replace('/^https?:\/\//', '', $url2openx);
            update_option('new_openxwpwidget_url2openx', $url2openx);
        }
        $new_url2openx = stripslashes(get_option('new_openxwpwidget_url2openx'));

        // uh, I am not a web-designer, so someone else pick up this
        // part please ...
        ?>

<STYLE TYPE="TEXT/CSS">
div#openxwpwidget b {
        color: red;
}
</STYLE>

<DIV CLASS="wrap">
  <DIV id="poststuff">
    <DIV id="openxwpwidget">
       <p>
       Type the path to your adservers delivery directory into the
       textfield.</p>
       <p>Sample path to adserver: <b>ads.openx.org/delivery.</b></p>
       <p>Now you simply add ad-code into your content like
       <b>{openx:N}</b>, whereas <b>N</b> is a zoneID at your
       adserver.</p>
       <p>In addition, this plugin acts as a widget, so you can add
       it to a sidebar.</p>

      <FORM name="openxwpwidget_form" method="post">
         <p>Deprecated Url to OpenX-AdServer:</p>
         <input type=textfield size="60" name="deprecated_openxwpwidget_url2openx" id="url2openx" value="<?php echo $deprecated_url2openx; ?>">
         <p>&nbsp;</p>
		 <p>New Url to OpenX-AdServer:</p>
         <input type=textfield size="60" name="new_openxwpwidget_url2openx" id="url2openx" value="<?php echo $new_url2openx; ?>">
         <p>&nbsp;</p>
		 <input type="hidden" name="updated" value="true"/>
         <input type="submit" name="submit" value="Save" />
      </FORM>
	  
    </DIV>
  </DIV>
</DIV>

      <?php
    }
/*=======================================================================================================================================================================================*/

	function register() {
		register_widget('DMC_OpenX2');
	}
}
$dmcopenx = new DMC_OpenX2();
// and finally install our admin-setup-callback and content-filter
add_action('admin_menu', array($dmcopenx, 'openxwpwidget_admin_menuitem'));
add_filter('the_content', array($dmcopenx, 'openxwpwidget_replace_magic'));
add_action('widgets_init', array($dmcopenx, 'register'));

?>