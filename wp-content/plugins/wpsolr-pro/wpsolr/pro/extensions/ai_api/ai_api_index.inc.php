<?php

use wpsolr\core\classes\models\WPSOLR_Model_Meta_Type_Abstract;
use wpsolr\core\classes\utilities\WPSOLR_Escape;
use wpsolr\core\classes\utilities\WPSOLR_Option;

$batch_size = ! empty( $options[ WPSOLR_Option::OPTION_AI_API_APIS ][ $ai_api_uuid ]['indexes'][ $index_uuid ][ WPSOLR_Option::OPTION_AI_API_BATCH_SIZE ] ) ? $options[ WPSOLR_Option::OPTION_AI_API_APIS ][ $ai_api_uuid ]['indexes'][ $index_uuid ][ WPSOLR_Option::OPTION_AI_API_BATCH_SIZE ] : 100;
?>

<li class="wpsolr-sorted">
    <div class="wdm_row" data-wpsolr-index-label="<?php WPSOLR_Escape::echo_esc_attr( $index['index_name'] ); ?>">
        <input type='checkbox'
               class="wpsolr-ai-api-index-selected wpsolr_collapser wpsolr-remove-if-empty"
               name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_AI_API_APIS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $ai_api_uuid ); ?>][indexes][<?php WPSOLR_Escape::echo_esc_attr( $index_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_AI_API_INDEX_IS_SELECTED ); ?>]'
               value='1'
			<?php
			checked( isset( $options[ WPSOLR_Option::OPTION_AI_API_APIS ][ $ai_api_uuid ]['indexes'][ $index_uuid ][ WPSOLR_Option::OPTION_AI_API_INDEX_IS_SELECTED ] ) );
			?>
        >
        <span><?php WPSOLR_Escape::echo_esc_attr( $index['index_name'] ); ?> </span>
        <div class="wdm_row wpsolr_collapsed wpsolr-remove-if-hidden">

            <div class="wdm_row">
                <div class='col_left'>
                    Post types to pre-process with this service on this index
                    <div style="float: right">
                        <a href="javascript:void();" class="wpsolr_checker">All</a> |
                        <a href="javascript:void();" class="wpsolr_unchecker">None</a>
                    </div>
                </div>
                <div class='col_right'>
					<?php
					$index_post_types = isset( $options[ WPSOLR_Option::OPTION_AI_API_APIS ][ $ai_api_uuid ]['indexes'][ $index_uuid ][ WPSOLR_Option::OPTION_AI_API_INDEX_POST_TYPES ] )
						? $options[ WPSOLR_Option::OPTION_AI_API_APIS ][ $ai_api_uuid ]['indexes'][ $index_uuid ][ WPSOLR_Option::OPTION_AI_API_INDEX_POST_TYPES ]
						: [];
					?>

					<?php
					/** @var WPSOLR_Model_Meta_Type_Abstract[] $models */
					if ( ! empty( $models ) ) { ?>
						<?php foreach ( $models as $model ) {
							$post_type  = $model->get_type();
							$post_label = $model->get_label();
							?>
                            <div style="float:left;width:33%;">
                                <input type='checkbox'
                                       data-wpsolr-index-post-type="<?php WPSOLR_Escape::echo_esc_attr( $post_type ); ?>"
                                       class="wpsolr_index_post_types wpsolr_checked"
                                       name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_AI_API_APIS ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $ai_api_uuid ); ?>][indexes][<?php WPSOLR_Escape::echo_esc_attr( $index_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_AI_API_INDEX_POST_TYPES ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $post_type ); ?>]'
                                       value='y'
									<?php
									checked( isset( $options[ WPSOLR_Option::OPTION_AI_API_APIS ][ $ai_api_uuid ]['indexes'][ $index_uuid ][ WPSOLR_Option::OPTION_AI_API_INDEX_POST_TYPES ][ $post_type ] ) );
									?>
                                >
								<?php WPSOLR_Escape::echo_esc_html( $post_label ); ?>
                            </div>
						<?php } ?>
					<?php } else { ?>
                        <span>First <a href="/wp-admin/admin.php?page=solr_settings&tab=solr_option&subtab=index_opt">select some post types to index</a>. Then configure them here.</span>
					<?php } ?>

                    <span class='res_err'></span><br>
                </div>
                <div class="clear"></div>
            </div>

        </div>
    </div>
</li>
									