<?php

use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\utilities\WPSOLR_Escape;
use wpsolr\core\classes\utilities\WPSOLR_Option;
use wpsolr\core\classes\utilities\WPSOLR_Zip_Generator;
use wpsolr\pro\classes\hosting_api\WPSOLR_Hosting_Api_Google_Retail;
use wpsolr\pro\extensions\recommendations\WPSOLR_Option_Recommendations;

$project_id = json_decode( $current_index['index_key_json'], true )['project_id'];
?>

<div class="wdm_row">
    <div class='col_left'>
        Model type
    </div>
    <div class='col_right'>
        <select class="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option_Recommendations::CLASS_RECOMMENDATION_TYPE ); ?>"
                name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATIONS_RECOMMENDATIONS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $recommendation_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_TYPE ); ?>]'
        >
			<?php
			$options = WPSOLR_Option_Recommendations::get_type_definitions();

			foreach ( $options as $option ) {
				$selected = ( $option['code'] === $recommendation_type ) ? 'selected' : '';
				$disabled = $option['disabled'] ? 'disabled' : '';
				?>
                <option
                        value="<?php WPSOLR_Escape::echo_esc_attr( $option['code'] ); ?>"
					<?php WPSOLR_Escape::echo_esc_attr( $selected ); ?>
					<?php WPSOLR_Escape::echo_esc_attr( $disabled ); ?>
                >
					<?php WPSOLR_Escape::echo_esc_html( $option['label'] ); ?>
                </option>
			<?php } ?>

        </select>
        <p>
        <ol>
            <li>
                <a href="<?php /** @var WPSOLR_Hosting_Api_Google_Retail $index_hosting_api */
				WPSOLR_Escape::echo_esc_url( $index_hosting_api->get_account_dashboard_url( $current_index, 'models' ) ); ?>"
                   target="_google_retail_project">Create and train a model </a>
                in your Google Retail dashboard for
                project "<?php WPSOLR_Escape::echo_esc_html( $project_id ); ?>
            </li>
            <li>
                <a href="<?php WPSOLR_Escape::echo_esc_url( $index_hosting_api->get_account_dashboard_url( $current_index, 'configs' ) ); ?>"
                   target="_google_retail_project">Create a Serving Config</a> on the model
            </li>
            <li>
                Select the Serving Config in the list
            </li>
        </ol>
        </p>
    </div>
    <div class="clear"></div>
</div>

<div class="wdm_row wpsolr-remove-if-hidden">
    <div class='col_left'>
        Serving config Id
    </div>
    <div class='col_right'>
        <input type='text'
               class="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_GOOGLE_RETAIL_SERVING_CONFIG_ID ); ?>"
               name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATIONS_RECOMMENDATIONS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $recommendation_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_GOOGLE_RETAIL_SERVING_CONFIG_ID ); ?>]'
               placeholder="Your Serving config Id here"
               value="<?php WPSOLR_Escape::echo_esc_html( $recommendation_serving_config_id ); ?>">
        <p>
        <ol>
            <li>
                <a href="<?php WPSOLR_Escape::echo_esc_url( $index_hosting_api->get_account_dashboard_url( $current_index, 'configs' ) ); ?>"
                   target="_google_retail_project">Create a Serving Config</a> on the model
            </li>
            <li>
                Copy the Serving Config Id in the list
            </li>
        </ol>
        </p>

    </div>
    <div class="clear"></div>
</div>

<div class="wdm_row">
    <div class='col_left'>
        Presentation
    </div>
    <div class='col_right'>

        <div class="wdm_row">
            <div class='col_left'>
                Template
            </div>
            <div class='col_right'>

                <label>
                    <select class="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option_Recommendations::CLASS_RECOMMENDATION_LAYOUT ); ?>"
                            style="width:100%"
                            name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATIONS_RECOMMENDATIONS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $recommendation_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_LAYOUT_ID ); ?>]'
                    >
						<?php
						$options = WPSOLR_Option_Recommendations::get_template_definitions();

						foreach ( $options as $option ) {
							$selected = ( $option['code'] === $recommendation_layout ) ? 'selected' : '';
							?>
                            <option value="<?php WPSOLR_Escape::echo_esc_attr( $option['code'] ); ?>"
								<?php WPSOLR_Escape::echo_esc_attr( $selected ); ?>
                            >
								<?php WPSOLR_Escape::echo_esc_html( ( $option['code'] === WPSOLR_Option::OPTION_RECOMMENDATION_LAYOUT_ID_CUSTOM_FILE ) ? $option['label'] : $option['label'] ); ?>
                            </option>
						<?php } ?>

                    </select>
					<?php WPSOLR_Escape::echo_escaped( WPSOLR_Zip_Generator::get_download_link( WPSOLR_Zip_Generator::EXAMPLE_ID_SUGGESTION_CUSTOM_TEMPLATE, 'Download and install this example child theme' ) ); ?>
                    to create your own twig templates. More templates coming.
                </label>

            </div>
            <div class="clear"></div>
        </div>

        <!-- Maximum -->
        <div class="wdm_row wpsolr-remove-if-hidden">
            <div class='col_left'>
                Maximum
            </div>
            <div class='col_right'>

                <label>
                    <input class="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_NB ); ?>"
                           type='number' step="1" min="1"
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATIONS_RECOMMENDATIONS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $recommendation_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_NB ); ?>]'
                           placeholder=""
                           value="<?php WPSOLR_Escape::echo_esc_attr( $recommendation_nb ); ?>">
                    Enter the maximum number of recommendations displayed.
                </label>


            </div>
            <div class="clear"></div>
        </div>

        <!-- Show text -->
        <div class="wdm_row wpsolr-remove-if-hidden">
            <div class='col_left'>
                Filter
            </div>
            <div class='col_right'>
                <label>
                    <input class="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_IS_ARCHIVE ); ?>"
                           type='checkbox'
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATIONS_RECOMMENDATIONS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $recommendation_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_IS_ARCHIVE ); ?>]'
                           value='is_active'
						<?php checked( $recommendation_is_archive ); ?>>
                    Click to filter recommendations with the archive type of the page
                    containing
                    the current search box.
                    Leave uncheck to search globally (unfiltered). Admin archives are
                    automatically filtered by their post type.
                </label>
            </div>
            <div class="clear"></div>
        </div>

        <!-- Show text -->
        <div class="wdm_row wpsolr-remove-if-hidden">
            <div class='col_left'>
                Description
            </div>
            <div class='col_right'>
                <label>
                    <input class="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_IS_SHOW_TEXT ); ?>"
                           type='checkbox'
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATIONS_RECOMMENDATIONS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $recommendation_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_IS_SHOW_TEXT ); ?>]'
                           value='is_active'
						<?php checked( $recommendation_is_show_text ); ?>>
                    Show description
                </label>
            </div>
            <div class="clear"></div>
        </div>

        <!-- Image size -->
        <div class="wdm_row wpsolr-remove-if-hidden">
            <div class='col_left'>
                Image size
            </div>
            <div class='col_right'>

                <label>
                    %<input class="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_IMAGE_WIDTH_PCT ); ?>"
                            type='number' step="1" min="0" max="100"
                            name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATIONS_RECOMMENDATIONS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $recommendation_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_IMAGE_WIDTH_PCT ); ?>]'
                            placeholder=""
                            value="<?php WPSOLR_Escape::echo_esc_attr( $recommendation_image_width_pct ); ?>">
                    Enter a % width for the thumbnail images: 0, 10, 20, ... 100. Leave
                    empty or use "0" to hide
                    images.
                </label>


            </div>
            <div class="clear"></div>
        </div>

        <!-- Custom CSS -->
        <div class="wdm_row wpsolr-remove-if-hidden">
            <div class='col_left'>
                Custom css
            </div>
            <div class='col_right'>

                <label>
                                                <textarea
                                                        class="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_CUSTOM_CSS ); ?>"
                                                        name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATIONS_RECOMMENDATIONS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $recommendation_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_CUSTOM_CSS ); ?>]'
                                                        placeholder=""
                                                        rows="4"
                                                ><?php WPSOLR_Escape::echo_esc_textarea( $recommendation_custom_css ); ?></textarea>
                </label>
                Enter your custom css code here. To keep isolation, prefix all your css
                selectors with .c<?php WPSOLR_Escape::echo_esc_html( $recommendation_uuid ); ?>
            </div>
            <div class="clear"></div>
        </div>


        <div class="wdm_row wpsolr-remove-if-hidden">
            <div class='col_left'>
                Order by
            </div>
            <div class='col_right'>

                <label>
                    <select class="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_ORDER_BY ); ?>"
                            style="width:100%"
                            name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATIONS_RECOMMENDATIONS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $recommendation_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_ORDER_BY ); ?>]'
                    >
						<?php
						$options = WPSOLR_Option_Recommendations::get_order_by_definitions();

						foreach ( $options as $option ) {
							$selected = ( $option['code'] === $recommendation_order_by ) ? 'selected' : '';
							?>
                            <option value="<?php WPSOLR_Escape::echo_esc_attr( $option['code'] ); ?>"
								<?php WPSOLR_Escape::echo_esc_attr( $selected ); ?>
								<?php WPSOLR_Escape::echo_escaped( isset( $option['disabled'] ) && ( $option['disabled'] ) ? 'disabled' : '' ); ?>
                            >
								<?php WPSOLR_Escape::echo_esc_html( $option['label'] ); ?>
                            </option>
						<?php } ?>

                    </select>
                    Select how to sort the recommendations
                </label>

            </div>
            <div class="clear"></div>
        </div>

        <div class="wdm_row wpsolr-remove-if-hidden <?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_MODELS ); ?>">
            <div class='col_left'>
                Show
            </div>
            <div class='col_right'>
                <div style="float: right">
                    <a href="javascript:void();" class="wpsolr_checker">All</a> |
                    <a href="javascript:void();" class="wpsolr_unchecker">None</a>
                </div>
                <br>

                <ul class="ui-sortable">
					<?php
					$loop       = 0;
					$batch_size = 100;

					if ( isset( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_MODELS ] ) ) {
						foreach ( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_MODELS ] as $model_type => $dontcare ) {
							include( 'recommendation_models.inc.php' );
						}
					}

					$model_types = WPSOLR_Service_Container::getOption()->get_option_index_post_types();
					if ( ! empty( $model_types ) ) {
						foreach ( $model_types as $model_type ) {
							if ( ! isset( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_MODELS ] ) || ! isset( $recommendation[ WPSOLR_Option::OPTION_RECOMMENDATION_MODELS ][ $model_type ] ) ) { // Prevent duplicate
								include( 'recommendation_models.inc.php' );
							}
						}
					} else {
						?>
                        <span>First <a
                                    href="/wp-admin/admin.php?page=solr_settings&tab=solr_indexes">add an index</a>. Then configure it here.</span>
						<?php
					}
					?>
                </ul>
            </div>
            <div class="clear"></div>
        </div>

        <!-- Custom Twig template file-->
        <div class="wdm_row wpsolr-remove-if-hidden">
            <div class='col_left'>
                Use my custom Twig file
            </div>
            <div class='col_right'>

                <label>
                    <input class="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_CUSTOM_TEMPLATE_FILE ); ?>"
                           type='text'
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATIONS_RECOMMENDATIONS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $recommendation_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_RECOMMENDATION_CUSTOM_TEMPLATE_FILE ); ?>]'
                           placeholder=""
                           value="<?php WPSOLR_Escape::echo_esc_attr( $recommendation_custom_file ); ?>">
                    Custom Twig file, relative to your folder
                    "child-theme/<?php WPSOLR_Escape::echo_esc_html( WPSOLR_Option_Recommendations::TEMPLATE_ROOT_DIR ); ?>
                    /twig".
                    Example: "my-recommendations.twig" will be transformed in
                    "child-theme/<?php WPSOLR_Escape::echo_esc_html( WPSOLR_Option_Recommendations::TEMPLATE_ROOT_DIR ); ?>
                    /twig/my-recommendations.twig"
                </label>


            </div>
            <div class="clear"></div>
        </div>

    </div>
    <div class="clear"></div>
</div>
