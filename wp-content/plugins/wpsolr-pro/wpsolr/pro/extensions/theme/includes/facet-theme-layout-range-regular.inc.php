<?php

use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\ui\layout\WPSOLR_UI_Layout_Abstract;
use wpsolr\core\classes\utilities\WPSOLR_Escape;
use wpsolr\core\classes\utilities\WPSOLR_Option;

?>

<div style="display:none"
     class="wpsolr-remove-if-hidden wpsolr_facet_type <?php WPSOLR_Escape::echo_esc_attr( WPSOLR_UI_Layout_Abstract::get_css_class_feature_layouts( WPSOLR_UI_Layout_Abstract::FEATURE_RANGE_REGULAR ) ); ?>">

    <div class="wpsolr_err">Range facets are not supported by Algolia's index</div>
    <div class='col_left'>
        Range start
    </div>
    <div class='col_right'>
        <input type='text'
               name='<?php WPSOLR_Escape::echo_esc_attr( $view_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::FACET_FIELD_RANGE_START ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $selected_val ); ?>]'
               placeholder='<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Service_Container::getOption()->get_facets_range_regular_start( $selected_val ) ); ?>'
               value='<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Service_Container::getOption()->get_facets_range_regular_start( $selected_val, '' ) ); ?>'/>
    </div>
    <div class='col_left'>
        Range end
    </div>
    <div class='col_right'>
        <input type='text'
               name='<?php WPSOLR_Escape::echo_esc_attr( $view_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::FACET_FIELD_RANGE_END ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $selected_val ); ?>]'
               placeholder='<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Service_Container::getOption()->get_facets_range_regular_end( $selected_val ) ); ?>'
               value='<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Service_Container::getOption()->get_facets_range_regular_end( $selected_val, '' ) ); ?>'/>
    </div>
    <div class='col_left'>
        Range gap
    </div>
    <div class='col_right'>
        <input type='text'
               name='<?php WPSOLR_Escape::echo_esc_attr( $view_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::FACET_FIELD_RANGE_GAP ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $selected_val ); ?>]'
               placeholder='<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Service_Container::getOption()->get_facets_range_regular_gap( $selected_val ) ); ?>'
               value='<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Service_Container::getOption()->get_facets_range_regular_gap( $selected_val, '' ) ); ?>'/>
    </div>

    <div class='col_left'>
        Facet label template
    </div>
    <div class='col_right'>
        <input type='text'
               name='<?php WPSOLR_Escape::echo_esc_attr( $view_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::FACET_FIELD_LABEL_FIRST ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $selected_val ); ?>]'
               placeholder='<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Service_Container::getOption()->get_facets_range_regular_template( $selected_val ) ); ?>'
               value='<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Service_Container::getOption()->get_facets_range_regular_template( $selected_val, '' ) ); ?>'/>

        A global template with variables {{start}}, {{end}} and {{count}} is used to generate a label for every range
        returned by the search.<br/>
        You can change the global template here.<br/>
        You can also set each range template individually in the localizations. Localizations with no values will use
        the global template.
    </div>

</div>

