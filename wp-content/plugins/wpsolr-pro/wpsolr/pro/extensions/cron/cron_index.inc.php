<?php

use wpsolr\core\classes\models\WPSOLR_Model_Meta_Type_Abstract;
use wpsolr\core\classes\utilities\WPSOLR_Escape;
use wpsolr\core\classes\utilities\WPSOLR_Option;
use wpsolr\pro\extensions\cron\WPSOLR_Option_Cron;

$batch_size = ! empty( $options['indexing'][ $cron_uuid ]['indexes'][ $index_uuid ][ WPSOLR_Option::OPTION_CRON_BATCH_SIZE ] ) ? $options['indexing'][ $cron_uuid ]['indexes'][ $index_uuid ][ WPSOLR_Option::OPTION_CRON_BATCH_SIZE ] : 100;
?>

<li class="wpsolr-sorted">
    <div class="wdm_row" data-wpsolr-index-label="<?php WPSOLR_Escape::echo_esc_attr( $index['index_name'] ); ?>">
        <input type='checkbox'
               class="wpsolr-cron-index-selected wpsolr_collapser wpsolr-remove-if-empty"
               name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[indexing][<?php WPSOLR_Escape::echo_esc_attr( $cron_uuid ); ?>][indexes][<?php WPSOLR_Escape::echo_esc_attr( $index_uuid ); ?>][is_in_cron]'
               value='1'
			<?php
			checked( isset( $options['indexing'][ $cron_uuid ]['indexes'][ $index_uuid ]['is_in_cron'] ) );
			?>
        >
        <span><?php WPSOLR_Escape::echo_esc_attr( $index['index_name'] ); ?> </span>
        <div class="wdm_row wpsolr_collapsed wpsolr-remove-if-hidden">
            <div class="wdm_row">
                <div class='col_left'>
                    Number of documents sent to the index as a single commit<br>
                    You can change this number to control indexing's performance
                </div>
                <div class='col_right'>
                    <input type='text'
                           class="wpsolr-cron-index-batch-size"
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[indexing][<?php WPSOLR_Escape::echo_esc_attr( $cron_uuid ); ?>][indexes][<?php WPSOLR_Escape::echo_esc_attr( $index_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_CRON_BATCH_SIZE ); ?>]'
                           placeholder="Enter a Number"
                           value="<?php WPSOLR_Escape::echo_esc_attr( $batch_size ); ?>">
                    <span class='res_err'></span><br>
                </div>
                <div class="clear"></div>
            </div>
            <div class="wdm_row">
                <div class='col_left'>
                    Delete first
                </div>
                <div class='col_right'>
                    <input type='checkbox'
                           class="wpsolr-cron-index-delete-first wpsolr_collapser"
                           name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[indexing][<?php WPSOLR_Escape::echo_esc_attr( $cron_uuid ); ?>][indexes][<?php WPSOLR_Escape::echo_esc_attr( $index_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_CRON_IS_DELETE_FIRST ); ?>]'
                           value='y'
						<?php
						checked( isset( $options['indexing'][ $cron_uuid ]['indexes'][ $index_uuid ][ WPSOLR_Option::OPTION_CRON_IS_DELETE_FIRST ] ) );
						?>
                    >

                    <span class="wpsolr_collapsed">
                                            Delete the selected post types prior to indexing.
                                        </span>
                    <span class='res_err'></span><br>
                </div>
                <div class="clear"></div>
            </div>

            <div class="wdm_row">
                <div class='col_left'>
                    Index mode
                </div>
                <div class='col_right'>
					<?php
					$index_type = isset( $options['indexing'][ $cron_uuid ]['indexes'][ $index_uuid ][ WPSOLR_Option::OPTION_CRON_INDEX_TYPE ] )
						? $options['indexing'][ $cron_uuid ]['indexes'][ $index_uuid ][ WPSOLR_Option::OPTION_CRON_INDEX_TYPE ]
						: '';
					?>
                    <select
                            class="wpsolr-cron-index-mode"
                            name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[indexing][<?php WPSOLR_Escape::echo_esc_attr( $cron_uuid ); ?>][indexes][<?php WPSOLR_Escape::echo_esc_attr( $index_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_CRON_INDEX_TYPE ); ?>]'
                    >
                        <option value="" <?php selected( $index_type, '', true ); ?> >
                            Do not index
                        </option>
                        <option value="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_CRON_INDEX_TYPE_FULL ); ?>" <?php selected( $index_type, WPSOLR_Option::OPTION_CRON_INDEX_TYPE_FULL, true ); ?> >
                            Index all the data
                        </option>
                        <option value="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_CRON_INDEX_TYPE_INCREMENTAL ); ?>" <?php selected( $index_type, WPSOLR_Option::OPTION_CRON_INDEX_TYPE_INCREMENTAL, true ); ?> >
                            Index incrementally
                        </option>

                    </select>


                    <span class='res_err'></span><br>
                </div>
                <div class="clear"></div>
            </div>

            <div class="wdm_row">
                <div class='col_left'>
                    Post types to index
                    <div style="float: right">
                        <a href="javascript:void();" class="wpsolr_checker">All</a> |
                        <a href="javascript:void();" class="wpsolr_unchecker">None</a>
                    </div>
                </div>
                <div class='col_right'>
					<?php
					$index_post_types = isset( $options['indexing'][ $cron_uuid ]['indexes'][ $index_uuid ][ WPSOLR_Option::OPTION_CRON_INDEX_POST_TYPES ] )
						? $options['indexing'][ $cron_uuid ]['indexes'][ $index_uuid ][ WPSOLR_Option::OPTION_CRON_INDEX_POST_TYPES ]
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
                                       name='<?php WPSOLR_Escape::echo_esc_attr( $extension_options_name ); ?>[indexing][<?php WPSOLR_Escape::echo_esc_attr( $cron_uuid ); ?>][indexes][<?php WPSOLR_Escape::echo_esc_attr( $index_uuid ); ?>][<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Option::OPTION_CRON_INDEX_POST_TYPES ); ?>][<?php WPSOLR_Escape::echo_esc_attr( $post_type ); ?>]'
                                       value='y'
									<?php
									checked( isset( $options['indexing'][ $cron_uuid ]['indexes'][ $index_uuid ][ WPSOLR_Option::OPTION_CRON_INDEX_POST_TYPES ][ $post_type ] ) );
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

            <div class="wdm_row">
                <div class='col_left'>
                    Logs
                </div>
                <div class='col_right'>
					<?php
					$option_logs_name = WPSOLR_Option_Cron::get_option_cron_indices_log_name( $cron_uuid );
					$option_logs      = get_option( $option_logs_name, [] );
					$log              = isset( $option_logs['indexes'][ $index_uuid ] ) ? $option_logs['indexes'][ $index_uuid ] : '';
					?>
                    <input type="button" class="button-secondary wpsolr_collapser" value="Show last execution logs"/>
                    <div class="wpsolr_collapsed" style="margin:10px;">
                        <textarea
                                name='<?php WPSOLR_Escape::echo_esc_attr( $option_logs_name ); ?>[<?php WPSOLR_Escape::echo_esc_attr( $index_uuid ); ?>]'
                                rows="20"><?php WPSOLR_Escape::echo_esc_textarea( empty( $log ) ? 'No log available.' : $log ); ?></textarea>
                    </div>
                </div>
                <div class="clear"></div>
            </div>

        </div>
    </div>
</li>
									