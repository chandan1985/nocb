<?php

use wpsolr\core\classes\extensions\indexes\WPSOLR_Option_Indexes;
use wpsolr\core\classes\extensions\licenses\OptionLicenses;
use wpsolr\core\classes\extensions\WPSOLR_Extension;
use wpsolr\core\classes\models\WPSOLR_Model_Builder;
use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\utilities\WPSOLR_Escape;
use wpsolr\core\classes\utilities\WPSOLR_Help;
use wpsolr\core\classes\utilities\WPSOLR_Option;
use wpsolr\pro\classes\ai_api\WPSOLR_AI_Api_Abstract;

global $license_manager;

WPSOLR_Extension::require_once_wpsolr_extension( WPSOLR_Extension::OPTION_AI_API, true );

$extension_options_name = WPSOLR_Option::OPTION_AI_API;
$settings_fields_name   = 'extension_ai_api_opt';

$options          = WPSOLR_Service_Container::getOption()->get_option_ai_api();
$options_nb_calls = WPSOLR_Service_Container::getOption()->get_option_ai_api_nb_calls();

$is_plugin_active = WPSOLR_Extension::is_plugin_active( WPSOLR_Extension::OPTION_AI_API );
?>

<?php
$option_indexes = new WPSOLR_Option_Indexes();
$indexes        = $option_indexes->get_indexes();
$post_types     = WPSOLR_Service_Container::getOption()->get_option_index_post_types();
$models         = WPSOLR_Model_Builder::get_model_type_objects( $post_types );

$ai_apis = WPSOLR_Service_Container::getOption()->get_option_ai_api_apis();
if ( isset( $_POST['wpsolr_new_ai_api'] ) && ! isset( $ai_apis[ $_POST['wpsolr_new_ai_api'] ] ) ) {
	$ai_apis = array_merge( [ sanitize_text_field( $_POST['wpsolr_new_ai_api'] ) => [ 'is_new' => true ] ], $ai_apis );
}

?>


<style>
    .wpsolr_hide {
        display: none;
    }

    .wdm_row .wdm_row .col_left, .wdm_row .wdm_row .col_right {
        width: 98%;
    }

    .wdm_row .wdm_row .wdm_row .wdm_row {
        border-bottom: 1px solid lightgrey;
        margin-top: 10px;
        padding-bottom: 10px;
    }
</style>
<script>

    jQuery(document).ready(function ($) {

        var g_field_name_services = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_SERVICES ); ?>';
        var g_field_name_providers = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_PROVIDERS ); ?>';
        var g_field_name_default_service = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_DEFAULT_SERVICE ); ?>';
        var g_field_name_provider = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_PROVIDER ); ?>';
        var g_field_name_provider_type = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_SERVICE_TYPE ); ?>';
        var g_field_name_fields = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_FIELDS ); ?>';
        var g_field_name_label = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_LABEL ); ?>';
        var g_field_name_default_value = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_DEFAULT_VALUE ); ?>';
        var g_field_name_service_url = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_URL ); ?>';
        var g_field_name_service_documentation_url = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_DOCUMENTATION_URL ); ?>';
        var g_field_name_service_documentation_text = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_DOCUMENTATION_TEXT ); ?>';
        var g_field_name_placeholder = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_PLACEHOLDER ); ?>';
        var g_field_name_instruction = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_INSTRUCTION ); ?>';
        var g_field_name_is_create_only = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_IS_CREATE_ONLY ); ?>';
        var g_field_name_is_update_only = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_IS_UPDATE_ONLY ); ?>';
        var g_field_name_field_format = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT ); ?>';
        var g_field_name_field_format_display = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_DISPLAY ); ?>';
        var g_field_name_field_format_type = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_TYPE ); ?>';
        var g_field_name_field_format_type_mandatory = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_TYPE_MANDATORY ); ?>';
        var g_field_name_field_format_type_integer_2_digits = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_TYPE_INTEGER_MINIMUM_2_DIGITS ); ?>';
        var g_field_name_field_format_type_integer_positive = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_TYPE_INTEGER_MINIMUM_POSITIVE ); ?>';
        var g_field_name_field_format_type_float_between_0_1 = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_TYPE_FLOAT_BETWEEN_0_1 ); ?>';
        var g_field_name_field_format_error_label = '<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_ERROR_LABEL ); ?>';

        var g_ui_api_fields = <?php WPSOLR_Escape::echo_esc_json( wp_json_encode( WPSOLR_AI_Api_Abstract::get_all_ui_fields() ) ); ?>;

        //console.log(g_current_provider, g_current_service_id);
        //console.log(JSON.stringify(g_ui_api_fields, null, 2));

        function show_hide_fields(parent, provider, service_id, is_form_validation) {

            //console.log('1', provider, service_id, g_ui_api_fields[g_field_name_services][service_id]);

            // Hide all fields by default
            parent.find('.wpsolr_hide').hide();

            var provider = ((typeof provider === 'undefined')) ? parent.find('.ai_api_provider').val() : provider;
            var provider_default_service = g_ui_api_fields[g_field_name_providers][provider][g_field_name_default_service];
            //alert(provider_default_service);

            //alert(service_id);
            var service_id = ((typeof service_id === 'undefined')) ? provider_default_service : service_id;
            //alert(service_id);

            // Show the ai_api api information
            var url = g_ui_api_fields[g_field_name_services][service_id][g_field_name_service_url];
            //var label = g_ui_api_fields[g_field_name_services][service_id][g_field_name_label];
            var label = 'API documentation';
            //console.log('2', provider, service_id, url, label);
            if ('' !== url) {
                parent.find('.wpsolr_hide.' + g_field_name_service_url).show().html('<a href=' + "'" + url + "'" + " target='_new'>" + '' + label + "</a>   |   ");
            }

            // Show the documentation ai_api api information
            var documentation_url = g_ui_api_fields[g_field_name_services][service_id][g_field_name_service_documentation_url];
            parent.find('.wpsolr_hide.' + g_field_name_service_documentation_url).show().html("<a href=" + "'" + documentation_url + "'" + " target='_new'>Tutorial</a>");

            // Show the documentation ai_api api documentation text
            var documentation_text = g_ui_api_fields[g_field_name_services][service_id][g_field_name_service_documentation_text];
            parent.find('.wpsolr_hide.' + g_field_name_service_documentation_text).show().html(documentation_text);

            // Manage the ai_api api list content based on provider selection
            //console.log("hui", provider, JSON.stringify(g_ui_api_fields[g_field_name_providers][provider], null, 2));
            parent.find('.ai_api_service').val(service_id);
            jQuery.each(g_ui_api_fields[g_field_name_services], function (service_id, fields) {

                if (provider === fields[g_field_name_provider]) {

                    parent.find('.ai_api_service option[value="' + service_id + '"]').show();

                } else {
                    parent.find('.ai_api_service option[value="' + service_id + '"]').hide();
                }
            });

            //console.log("hey", g_field_name_services, service_id, g_field_name_fields, JSON.stringify(g_ui_api_fields[g_field_name_services][g_current_service_id][g_field_name_fields], null, 2));
            // Manage the fields
            var has_errors = false;
            jQuery.each(g_ui_api_fields[g_field_name_services][service_id][g_field_name_fields], function (field_name, field_properties) {

                // Show the field
                var section_field_selector = '.wpsolr_hide.' + field_name;
                parent.find(section_field_selector).show();
                //console.log('3 show:', section_field_selector);
                parent.find('.' + field_name).prop({readOnly: false});

                // Set the field properties
                jQuery.each(field_properties, function (field_property_name, field_property_value) {

                    var current_field_name_in_form_selector = '.' + field_name;
                    var current_field_name_update_selector = '.wpsolr_is_index_readonly .' + field_name;

                    var current_object = parent.find(section_field_selector + ' .' + field_property_name);

                    switch (field_property_name) {
                        case g_field_name_default_value:
                            current_object = parent.find('.' + field_name + ' ' + '.wpsolr-ui-field');
                            //console.log('default value', field_name, current_object.val(), field_property_value);
                            if (!current_object.val()) {
                                current_object.val(field_property_value);
                            }
                            break;

                        case g_field_name_is_create_only:
                            current_object = parent.find(current_field_name_update_selector);
                            current_object.prop({readOnly: field_property_value});
                            //console.log(field_name, field_property_value, current_object.prop('readOnly'));
                            break;

                        case g_field_name_is_update_only:
                            current_object = parent.find(current_field_name_update_selector);
                            if (current_object.length === 0) {
                                // Hide on creation
                                parent.find(section_field_selector).hide();
                            }
                            break;

                        case g_field_name_placeholder:
                            current_object = parent.find('.' + field_name + ' ' + '.wpsolr-ui-field');
                            current_object.prop(g_field_name_placeholder, field_property_value);
                            //console.log('placeholder', current_field_name_in_form_selector, field_property_value);
                            break;

                        case g_field_name_label:
                        case g_field_name_instruction:
                            current_object.html(field_property_value);
                            break;

                        case g_field_name_field_format:
                            //console.log('error', g_field_name_field_format_error_label, field_property_value);

                            var current_object_error = parent.find(section_field_selector + ' .' + g_field_name_field_format_error_label);

                            var is_error = false;
                            if (parent.find(section_field_selector).is(":visible") && is_form_validation) {
                                //console.log('validation', g_field_name_field_format_type, g_field_name_field_format_type_mandatory, field_property_value[g_field_name_field_format_type]);

                                var current_object = parent.find('.' + field_name + ' ' + '.wpsolr-ui-field');

                                var field_format_type = ((typeof field_property_value[g_field_name_field_format_type] === 'undefined')) ? '' : field_property_value[g_field_name_field_format_type];

                                switch (field_format_type) {
                                    case g_field_name_field_format_type_mandatory:
                                        is_error = (0 === current_object.val().trim().length);
                                        //console.log('mandatory', parent.data('wpsolr-ai-api-label'), field_name, current_object.val(), is_error);
                                        break;

                                    case g_field_name_field_format_type_integer_2_digits:
                                        is_error = (isNaN(parseInt(current_object.val())) || (parseInt(current_object.val()) <= 9));
                                        break;

                                    case g_field_name_field_format_type_integer_positive:
                                        is_error = (isNaN(parseInt(current_object.val())) || (parseInt(current_object.val()) <= 0));
                                        break;

                                    case g_field_name_field_format_type_float_between_0_1:
                                        is_error = (isNaN(parseFloat(current_object.val())) || (parseFloat(current_object.val()) < 0) || (parseFloat(current_object.val()) > 1));
                                        break;
                                }
                            }

                            current_object_error.html(is_error ? field_property_value[g_field_name_field_format_error_label] : '');
                            //console.log('error?', is_error, field_property_value[g_field_name_field_format_error_label]);
                            has_errors = has_errors || is_error;
                            break;
                    }

                });

            });

            // Valid, or not!
            var is_checked = (is_form_validation && !has_errors);
            return is_checked;
        }

        // Refresh fields on provider selection
        $(document).on('change', '.ai_api_provider', function (e) {

            show_hide_fields($(this).closest('.wpsolr_ai_api'), this.val, undefined, false);
        });

        // Refresh fields on service selection
        $(document).on('change', '.ai_api_service', function (e) {

            var value = (0 === this.value.length) ? 'no_service' : this.value;

            // Toggle elements
            //$('.show_' + value).show();
            //$('.hide_' + value).hide();

            $('.index_service_id_label').html($(this).find("option:selected").text());

            show_hide_fields($(this).closest('.wpsolr_ai_api'), undefined, value, false);
        });


        // Control all the services fields on save
        $(document).on("click", "#save_ai_api", function (e) {

            // Block or continue submit based on checked fields
            if (control_show_hide_fields_of_all_services(true)) {
                // Continue
                return true;
            }

            // Show global error
            $('.wpsolr_global_error_msg').html('Problems detected. Please check each warning message.');

            // Stop saving
            return false;
        });


        // Init the fields of each service
        function control_show_hide_fields_of_all_services(is_form_validation) {

            var is_checked = true;

            $('.ai_api_provider').each(function () {

                var parent = $(this).closest('.wpsolr_ai_api');
                var g_current_provider = $(this).val();
                var g_current_service_id = parent.find('.ai_api_service').val();
                is_checked = show_hide_fields(parent, g_current_provider, g_current_service_id, is_form_validation) && is_checked;
            });

            return is_checked;
        }

        // Init all services fields
        control_show_hide_fields_of_all_services(true);
    });

</script>

<form id="wpsolr_form_new_ai_api" method="post">
    <input type="hidden" name="wpsolr_new_ai_api"
           value="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option_Indexes::generate_uuid() ); ?>"/>
</form>


<div wdm-vertical-tabs-contentid="extension_groups-options" class="wdm-vertical-tabs-content wpsolr-col-9">
    <form action="options.php" method="POST" id='extension_groups_settings_form'>
		<?php
		settings_fields( $settings_fields_name );
		?>

        <div class='wrapper'>
            <h4 class='head_div'>Artificial Intelligence extension</h4>

            <div class="wdm_note">
                Pre-process your documents before indexing. Then use the extracted information to configure your facets.
                <ol>
                    <li>Add one or more AI steps</li>
                    <li>Select an AI provider</li>
                    <li>Select an AI service (entity, sentiment, phrases, classification ...). The services list depends
                        on the provider selected.
                    </li>
                    <li>Set the service parameters</li>
                    <li>Select indexes to process</li>
                    <li>Select post types to process</li>
                </ol>

                Retrieved information is saved as custom fields: if AI is not used anymore, the data can still be
                searched and faceted.
            </div>

            <div class="wdm_row">
                <div class='col_left'>
                    Activate the AI extension
					<?php WPSOLR_Escape::echo_escaped( WPSOLR_Help::get_help( WPSOLR_Help::HELP_ADDON_AI_API_TEXT ) ); ?>
                    <br>
					<?php WPSOLR_Escape::echo_escaped( WPSOLR_Help::get_help( WPSOLR_Help::HELP_ADDON_AI_API_IMAGE ) ); ?>
                </div>
                <div class='col_right'>
                    <input type='checkbox' <?php WPSOLR_Escape::echo_escaped( $is_plugin_active ? '' : 'readonly' ); ?>
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[is_extension_active]'
                           value='is_extension_active'
						<?php checked( 'is_extension_active', isset( $options['is_extension_active'] ) ? $options['is_extension_active'] : '' ); ?>>
                </div>
                <div class="clear"></div>
            </div>

            <div class="wdm_row">
                <div class='col_left'>
                </div>
                <div class='col_right'>
                    <input type="button"
                           name="add_ai_api"
                           id="add_ai_api"
                           class="button-primary"
                           value="Add an AI step"
                           onclick="jQuery('#wpsolr_form_new_ai_api').submit();"
                    />
                    (<?php WPSOLR_Escape::echo_esc_html( count( $ai_apis ) ); ?> steps already)
                </div>
                <div class="clear"></div>
            </div>

			<?php foreach ( $ai_apis as $ai_api_uuid => $ai_api ) {
				$is_index_in_creation = $ai_api['is_new'] ?? false;
				$ai_api_label         = isset( $ai_api[ WPSOLR_Option::OPTION_AI_API_LABEL ] ) ? $ai_api[ WPSOLR_Option::OPTION_AI_API_LABEL ] : 'rename me';
				$provider_id          = $options[ WPSOLR_Option::OPTION_AI_API_APIS ][ $ai_api_uuid ][ WPSOLR_Option::OPTION_AI_API_PROVIDER ] ?? '';
				try {
					$provider_label = WPSOLR_AI_Api_Abstract::get_ai_api_provider_by_id( $provider_id )['label'];
				} catch ( Exception $e ) {
					$provider_label = $e->getMessage();
				}
				$service_id = $options[ WPSOLR_Option::OPTION_AI_API_APIS ][ $ai_api_uuid ][ WPSOLR_Option::OPTION_AI_API_SERVICE ] ?? '';
				try {
					$service_label = WPSOLR_AI_Api_Abstract::get_ai_api_service_by_id( $service_id )->get_label();
				} catch ( Exception $e ) {
					$service_label = $e->getMessage();
				}

				?>

                <div class="wpsolr_ai_api"
                     data-wpsolr-ai-api-label="<?php WPSOLR_Escape::echo_esc_attr( $ai_api_label ); ?>"
					<?php if ( $is_index_in_creation ) { ?>
                        id="current_ai_api_edited_id"
					<?php } ?>
                     class="wrapper <?php echo( ! $is_index_in_creation ? "wpsolr_is_index_readonly" : "" ); ?>"
                >
                    <h4 class='head_div'><?php WPSOLR_Escape::echo_esc_html( $ai_api_label ); ?></h4>
                    <div class="wdm_row">
                        <div class='col_left'>
                            Label
                            <input type="button"
                                   style="float:right;"
                                   name="delete_ai_api"
                                   class="wpsolr-ai-api-delete-button button-secondary"
                                   value="Delete"
                                   onclick="jQuery(this).closest('.wpsolr_ai_api').remove();"
                            />
                        </div>
                        <div class='col_right'>
                            <input type='text'
                                   name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_AI_API_APIS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $ai_api_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_AI_API_LABEL ); ?>]'
                                   placeholder="Enter a Number"
                                   value="<?php WPSOLR_Escape::echo_esc_attr( $ai_api_label ); ?>">

                        </div>
                        <div class="clear"></div>
                    </div>

                    <div class="wdm_row ai_api_stats_nb_calls">
                        <div class='col_left'>
                            Number of calls to this API
                        </div>
                        <div class='col_right'>
							<?php
							$nb_calls = isset( $options_nb_calls[ $ai_api_uuid ] ) ? $options_nb_calls[ $ai_api_uuid ] : 0;
							?>
                            <span class="nb_calls"><?php WPSOLR_Escape::echo_esc_html( $nb_calls ); ?></span>
                        </div>
                        <div class="clear"></div>
                    </div>

                    <div class="wdm_row ai_api_is_active">
                        <div class='col_left'>
                            Activate
                        </div>
                        <div class='col_right'>

                            <input type='checkbox'
                                   class="wpsolr_checked wpsolr_collapser"
                                   name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_AI_API_APIS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $ai_api_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_AI_API_IS_ACTIVE ); ?>]'
                                   value='y'
								<?php
								checked( isset( $options[ WPSOLR_Option::OPTION_AI_API_APIS ][ $ai_api_uuid ][ WPSOLR_Option::OPTION_AI_API_IS_ACTIVE ] ) );
								?>
                            >
                            <span class="wpsolr_collapsed">
                                This ai_api will pre-process indexed documents
                            </span>
                        </div>
                        <div class="clear"></div>
                    </div>

                    <div class="wdm_row ai_api_is_cache">
                        <div class='col_left'>
                            Cache
                        </div>
                        <div class='col_right'>

                            <input type='checkbox'
                                   class="wpsolr_checked wpsolr_collapser"
                                   name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_AI_API_APIS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $ai_api_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_AI_API_IS_CACHED ); ?>]'
                                   value='y'
								<?php
								checked( isset( $options[ WPSOLR_Option::OPTION_AI_API_APIS ][ $ai_api_uuid ][ WPSOLR_Option::OPTION_AI_API_IS_CACHED ] ) );
								?>
                            >
                            <span class="wpsolr_collapsed">
                                Call the ai_api service only once per document.
                                If you index 10 times your data, the AI service will be called only once per document.
                                Can save lots of money.
                                But your AI information can be out of date if you modified your documents.
                            </span>
                        </div>
                        <div class="clear"></div>
                    </div>

                    <div class="wdm_row">
                        <div class='col_left'>
                            Provider
                        </div>
                        <div class='col_right'>

							<?php if ( $is_index_in_creation ) { ?>
                                <select class="ai_api_provider"
                                        name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_AI_API_APIS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $ai_api_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_AI_API_PROVIDER ); ?>]'
                                >
									<?php
									foreach ( WPSOLR_AI_Api_Abstract::AI_PROVIDERS as $ai_api_provider ) { ?>
                                        <option class="<?php WPSOLR_Escape::echo_esc_attr( $ai_api_provider['id'] ); ?>"
                                                value="<?php WPSOLR_Escape::echo_esc_attr( $ai_api_provider['id'] ); ?>"
											<?php disabled( $ai_api_provider['is_disabled'] ); ?>
											<?php selected( $ai_api_provider['id'], $provider_id ); ?>

                                        >
											<?php
											WPSOLR_Escape::echo_escaped( sprintf(
												'%s (%s)%s',
												WPSOLR_Escape::esc_html( $ai_api_provider['label'] ),
												WPSOLR_Escape::esc_html( $ai_api_provider[ WPSOLR_AI_Api_Abstract::FIELD_NAME_SERVICE_TYPE ] ),
												$ai_api_provider['is_disabled'] ? ' - Coming soon' : ''
											) );
											?>
                                        </option>
									<?php } ?>
                                </select>
							<?php } else { ?>
                                <input type="hidden" class="ai_api_provider"
                                       name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_AI_API_APIS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $ai_api_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_AI_API_PROVIDER ); ?>]'
                                       value="<?php WPSOLR_Escape::echo_esc_attr( $provider_id ); ?>"
                                >
								<?php WPSOLR_Escape::echo_esc_html( $provider_label ); ?>
							<?php } ?>

                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="wdm_row">
                        <div class='col_left'>
                            Provider's service
                        </div>
                        <div class='col_right'>
							<?php if ( $is_index_in_creation ) { ?>
                                <select class="ai_api_service"
                                        name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_AI_API_APIS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $ai_api_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_AI_API_SERVICE ); ?>]'
                                >
									<?php
									foreach ( WPSOLR_AI_Api_Abstract::get_ai_api_services() as $ai_api_service ) { ?>
                                        <option class="<?php WPSOLR_Escape::echo_esc_attr( $ai_api_service->get_id() ); ?>"
                                                value="<?php WPSOLR_Escape::echo_esc_attr( $ai_api_service->get_id() ); ?>"
											<?php disabled( $ai_api_service->get_is_disabled() ); ?>
											<?php selected( $ai_api_service->get_id(), $service_id ); ?>
                                        >
											<?php
											if ( $ai_api_service->get_is_no_hosting() ) {
												WPSOLR_Escape::echo_esc_html( $ai_api_service->get_label() );
											} else {
												WPSOLR_Escape::echo_esc_attr( sprintf(
													'%s%s',
													$ai_api_service->get_label(),
													sprintf( $ai_api_service->get_is_disabled() ?
														( empty( $ai_api_service->get_incompatibility_reason() ) ? ' - Coming soon' : WPSOLR_Escape::esc_html( $ai_api_service->get_incompatibility_reason() ) )
														: (
														empty( $ai_api_service->get_latest_version() ) ? ''
															: sprintf( ' - Tested on WPSOLR %s', WPSOLR_Escape::esc_html( $ai_api_service->get_latest_version() ) ) )
													)
												) );
											}
											?>
                                        </option>
									<?php } ?>
                                </select>
							<?php } else { ?>
                                <input type="hidden" class="ai_api_service"
                                       name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_AI_API_APIS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $ai_api_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_AI_API_SERVICE ); ?>]'
                                       value="<?php WPSOLR_Escape::echo_esc_attr( $service_id ); ?>"
                                >
								<?php WPSOLR_Escape::echo_esc_html( $service_label ); ?>
							<?php } ?>

                            <span class="wpsolr_hide <?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_URL ); ?>"></span>
                            <span class="wpsolr_hide <?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_DOCUMENTATION_URL ); ?>"></span>
                            <p>
                            <p class="wpsolr_hide <?php WPSOLR_Escape::echo_esc_attr( WPSOLR_AI_Api_Abstract::FIELD_NAME_DOCUMENTATION_TEXT ); ?>"
                               style="color:blue;">

                            </p>
                            </p>

                        </div>
                        <div class="clear"></div>
                    </div>


					<?php
					$is_index_readonly = false;
					$is_new_index      = true;
					$subtab            = $ai_api_uuid;

					WPSOLR_AI_Api_Abstract::include_edit_field(
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FIELDS_URL,
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_DISPLAY_INPUT_TEXT,
						$is_index_readonly, $is_new_index, $extension_options_name, $options,
						$ai_api_uuid, $subtab, false, true );

					WPSOLR_AI_Api_Abstract::include_edit_field( WPSOLR_AI_Api_Abstract::FIELD_NAME_FIELDS_SERVICE_KEY,
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_DISPLAY_INPUT_TEXT,
						$is_index_readonly, $is_new_index, $extension_options_name, $options,
						$ai_api_uuid, $subtab, false, true );

					WPSOLR_AI_Api_Abstract::include_edit_field( WPSOLR_AI_Api_Abstract::FIELD_NAME_FIELDS_SERVICE_SECRET,
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_DISPLAY_INPUT_TEXT,
						$is_index_readonly, $is_new_index, $extension_options_name, $options,
						$ai_api_uuid, $subtab, true, true );

					WPSOLR_AI_Api_Abstract::include_edit_field( WPSOLR_AI_Api_Abstract::FIELD_NAME_FIELDS_SERVICE_KEY_JSON,
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_DISPLAY_TEXTAREA,
						$is_index_readonly, $is_new_index, $extension_options_name, $options,
						$ai_api_uuid, $subtab, false, true );

					WPSOLR_AI_Api_Abstract::include_edit_field( WPSOLR_AI_Api_Abstract::FIELD_NAME_FIELDS_SERVICE_AWS_REGION,
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_DISPLAY_INPUT_TEXT,
						$is_index_readonly, $is_new_index, $extension_options_name, $options,
						$ai_api_uuid, $subtab, false, false );


					WPSOLR_AI_Api_Abstract::include_edit_field(
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FIELDS_INTERNAL_IMAGE_SEND_URL,
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_DISPLAY_CHECKBOX,
						$is_index_readonly, $is_new_index, $extension_options_name, $options,
						$ai_api_uuid, $subtab, false, false );

					WPSOLR_AI_Api_Abstract::include_edit_field(
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FIELDS_EXTERNAL_IMAGE_SEND_CONTENT,
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_DISPLAY_CHECKBOX,
						$is_index_readonly, $is_new_index, $extension_options_name, $options,
						$ai_api_uuid, $subtab, false, false );

					WPSOLR_AI_Api_Abstract::include_edit_field( WPSOLR_AI_Api_Abstract::FIELD_NAME_FIELDS_SERVICE_LANGUAGE,
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_DISPLAY_INPUT_TEXT,
						$is_index_readonly, $is_new_index, $extension_options_name, $options,
						$ai_api_uuid, $subtab, false, false );


					WPSOLR_AI_Api_Abstract::include_edit_field(
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FIELDS_SERVICE_IMAGE_TYPE_TEXT,
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_DISPLAY_CHECKBOX,
						$is_index_readonly, $is_new_index, $extension_options_name, $options,
						$ai_api_uuid, $subtab, false, false );

					WPSOLR_AI_Api_Abstract::include_edit_field(
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FIELDS_SERVICE_IMAGE_TYPE_LABEL,
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_DISPLAY_CHECKBOX,
						$is_index_readonly, $is_new_index, $extension_options_name, $options,
						$ai_api_uuid, $subtab, false, false );

					WPSOLR_AI_Api_Abstract::include_edit_field(
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FIELDS_SERVICE_IMAGE_TYPE_LABEL_TRANSLATE,
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_DISPLAY_INPUT_TEXT,
						$is_index_readonly, $is_new_index, $extension_options_name, $options,
						$ai_api_uuid, $subtab, false, false );

					WPSOLR_AI_Api_Abstract::include_edit_field(
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FIELDS_SERVICE_IMAGE_TYPE_PROPERTY,
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_DISPLAY_CHECKBOX,
						$is_index_readonly, $is_new_index, $extension_options_name, $options,
						$ai_api_uuid, $subtab, false, false );

					WPSOLR_AI_Api_Abstract::include_edit_field(
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FIELDS_SERVICE_IMAGE_TYPE_FACE,
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_DISPLAY_CHECKBOX,
						$is_index_readonly, $is_new_index, $extension_options_name, $options,
						$ai_api_uuid, $subtab, false, false );

					WPSOLR_AI_Api_Abstract::include_edit_field(
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FIELDS_SERVICE_IMAGE_TYPE_LANDMARK,
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_DISPLAY_CHECKBOX,
						$is_index_readonly, $is_new_index, $extension_options_name, $options,
						$ai_api_uuid, $subtab, false, false );

					WPSOLR_AI_Api_Abstract::include_edit_field(
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FIELDS_SERVICE_IMAGE_TYPE_LOGO,
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_DISPLAY_CHECKBOX,
						$is_index_readonly, $is_new_index, $extension_options_name, $options,
						$ai_api_uuid, $subtab, false, false );

					WPSOLR_AI_Api_Abstract::include_edit_field( WPSOLR_AI_Api_Abstract::FIELD_NAME_FIELDS_SERVICE_MIN_CONFIDENCE,
						WPSOLR_AI_Api_Abstract::FIELD_NAME_FORMAT_DISPLAY_INPUT_TEXT,
						$is_index_readonly, $is_new_index, $extension_options_name, $options,
						$ai_api_uuid, $subtab, false, false );

					?>

                    <div class="wdm_row">
                        <div class='col_left'>
                            Indexes
                        </div>
                        <div class='col_right'>
                            <ul class="ui-sortable">
								<?php
								$loop       = 0;
								$batch_size = 100;

								if ( isset( $ai_api[ WPSOLR_Option::OPTION_AI_API_INDEXES ] ) ) {
									foreach ( $ai_api[ WPSOLR_Option::OPTION_AI_API_INDEXES ] as $index_uuid => $ai_api_index ) {
										$index = $option_indexes->get_index( $index_uuid );
										if ( isset( $index['index_name'] ) ) {
											include( 'ai_api_index.inc.php' );
										}
									}
								}

								if ( ! empty( $indexes ) ) {
									foreach ( $indexes as $index_uuid => $index ) {
										if ( ! isset( $ai_api[ WPSOLR_Option::OPTION_AI_API_INDEXES ] ) || ! isset( $ai_api[ WPSOLR_Option::OPTION_AI_API_INDEXES ][ $index_uuid ] ) ) { // Prevent duplicate
											include( 'ai_api_index.inc.php' );
										}
									}
								} else {
									?>
                                    <span>First <a href="/wp-admin/admin.php?page=solr_settings&tab=solr_indexes">add an index</a>. Then configure it here.</span>
									<?php
								}
								?>
                            </ul>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
			<?php } ?>


            <div class='wdm_row'>
                <div class="submit">
					<?php if ( $license_manager->get_license_is_activated( OptionLicenses::LICENSE_PACKAGE_AI_API ) ) { ?>
                        <div class="wpsolr_premium_block_class">
							<?php WPSOLR_Escape::echo_escaped( $license_manager->show_premium_link( true, OptionLicenses::LICENSE_PACKAGE_AI_API, OptionLicenses::TEXT_LICENSE_ACTIVATED, true, true ) ); ?>
                        </div>
                        <input
                                name="save_ai_api"
                                id="save_ai_api" type="submit"
                                class="button-primary wdm-save"
                                value="Save Options"/>
                        <span class="wpsolr_err wpsolr_global_error_msg"></span>
					<?php } else { ?>
						<?php WPSOLR_Escape::echo_escaped( $license_manager->show_premium_link( true, OptionLicenses::LICENSE_PACKAGE_AI_API, 'Save Options', true, true ) ); ?>
                        <br/>
					<?php } ?>
                </div>
            </div>
        </div>

    </form>
</div>
