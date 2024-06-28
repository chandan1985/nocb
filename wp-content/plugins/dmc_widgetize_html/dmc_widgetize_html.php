<?php

/*
 * Plugin Name: DMC Widgetize HTML
 * Plugin URI: http://www.dolanmedia.com
 * Description: Widgetize HTML/Text
 * Author: Dave Masse
 * Version: 0.1
 * Author URI: http://www.cvwp.com
 */

class dmc_widgetize_html {

// See large comment section at end of this file
public static function dmc_wp_widget_text($args, $widget_args = 1) {
	extract( $args, EXTR_SKIP );
	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	$options = get_option('dmc_widget_text');
	if ( !isset($options[$number]) )
		return;
	extract( $options[$number] );
	
	// Show title when being called from the admin only. This will display the
	// title for each widget instance and make managing sidebars easier.
	if ($before_title == '%BEG_OF_TITLE%' && $after_title == '%END_OF_TITLE%')
		echo $before_title . $title . $after_title;
	
	echo $before_widget . do_shortcode($text) . $after_widget;
}

public static function dmc_wp_widget_text_control($widget_args) {
	global $wp_registered_widgets;
	static $updated = false;

	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	$options = get_option('dmc_widget_text');
	if ( !is_array($options) )
		$options = array();

	if ( !$updated && !empty($_POST['sidebar']) ) {
		$sidebar = (string) $_POST['sidebar'];

		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( isset($sidebars_widgets[$sidebar]) )
			$this_sidebar =& $sidebars_widgets[$sidebar];
		else
			$this_sidebar = array();

		foreach ( $this_sidebar as $_widget_id ) {
			if ( 'dmc_wp_widget_text' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				if ( !in_array( "text-$widget_number", $_POST['widget-id'] ) ) // the widget has been removed.
					unset($options[$widget_number]);
			}
		}

		foreach ( (array) $_POST['dmc-widget-text'] as $widget_number => $widget_text ) {
			if ( !isset($widget_text['text']) && isset($options[$widget_number]) ) // user clicked cancel
				continue;
			$title = strip_tags(stripslashes($widget_text['title']));
			$text = stripslashes( $widget_text['text'] );
			$options[$widget_number] = compact( 'title', 'text' );
		}
		
		update_option('dmc_widget_text', $options);
		$updated = true;
	}

	if ( -1 == $number ) {
		$title = '';
		$text = '';
		$number = '%i%';
	} else {
		$title = esc_attr($options[$number]['title']);
		$text = format_to_edit($options[$number]['text']);
	}
?>
		<p>
			<h3>Title (for reference only):</h3>
			<input class="widefat" id="text-title-<?php echo $number; ?>" name="dmc-widget-text[<?php echo $number; ?>][title]" type="text" value="<?php echo $title; ?>" />
			<h3>HTML/Text:</h3>
			<textarea class="widefat" id="dmc-widget-text-<?php echo $number; ?>" name="dmc-widget-text[<?php echo $number; ?>][text]" style="width: 580px; height: 300px;"><?php echo $text; ?></textarea>
			<input type="hidden" name="dmc-widget-text[<?php echo $number; ?>][submit]" value="1" />
		</p>
<?php
}

function dmc_wp_widget_text_register() {
	if ( !$options = get_option('dmc_widget_text') )
		$options = array();
	$widget_ops = array('description' => __('Arbitrary text or HTML'));
	$control_ops = array('width' => 600, 'height' => 350, 'id_base' => 'dmc-widget-text');
	$name = __('DMC Text');

	$id = false;
	foreach ( array_keys($options) as $o ) {
		// Old widgets can have null values for some reason
		if ( !isset($options[$o]['title']) || !isset($options[$o]['text']) )
			continue;
		$id = "dmc-widget-text-$o"; // Never never never translate an id
		wp_register_sidebar_widget($id, $name, array( 'dmc_widgetize_html', 'dmc_wp_widget_text' ), $widget_ops, array( 'number' => $o ));
		wp_register_widget_control($id, $name, array( 'dmc_widgetize_html', 'dmc_wp_widget_text_control' ), $control_ops, array( 'number' => $o ));
	}

	// If there are none, we register the widget's existance with a generic template
	if ( !$id ) {
		wp_register_sidebar_widget( 'dmc-widget-text-1', $name, array( 'dmc_widgetize_html', 'dmc_wp_widget_text' ), $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'dmc-widget-text-1', $name, array( 'dmc_widgetize_html', 'dmc_wp_widget_text_control' ), $control_ops, array( 'number' => -1 ) );
	}
}

}

// This is important
$dmc_widgetize_html = new dmc_widgetize_html();
add_action( 'widgets_init', array( $dmc_widgetize_html, 'dmc_wp_widget_text_register' ) );

?>