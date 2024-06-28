<?php

use wpsolr\core\classes\extensions\licenses\OptionLicenses;
use wpsolr\core\classes\extensions\WPSOLR_Extension;
use wpsolr\core\classes\utilities\WPSOLR_Escape;

/**
 * Included file to display admin options
 */

WPSOLR_Extension::require_once_wpsolr_extension( WPSOLR_Extension::OPTION_LICENSES, true );

// Options name
$option_name = OptionLicenses::get_option_name( WPSOLR_Extension::OPTION_LICENSES );

// Options object

?>


<?php foreach ( $license_manager->get_license_types() as $license_type => $license ) { ?>

    <div id="<?php WPSOLR_Escape::echo_esc_attr( $license_type ); ?>" style="display:none;"
         class="wdm-vertical-tabs-content">

        <form method="POST" id="form_<?php WPSOLR_Escape::echo_esc_attr( $license_type ); ?>"
              class="wpsolr_form_license">

            <input type="hidden" name="<?php WPSOLR_Escape::echo_esc_attr( OptionLicenses::FIELD_LICENSE_PACKAGE ); ?>"
                   value="<?php WPSOLR_Escape::echo_esc_attr( $license_type ); ?>"/>

            <input type="hidden"
                   name="<?php WPSOLR_Escape::echo_esc_attr( OptionLicenses::FIELD_LICENSE_MATCHING_REFERENCE ); ?>"
                   value="<?php WPSOLR_Escape::echo_esc_attr( $license[ OptionLicenses::FIELD_LICENSE_MATCHING_REFERENCE ] ); ?>"/>

            <div class='wrapper wpsolr_license_popup'><h4
                        class='head_div'><?php WPSOLR_Escape::echo_esc_html( $license[ OptionLicenses::FIELD_LICENSE_TITLE ] ); ?></h4>
                <div class="wdm_note">
					<?php WPSOLR_Escape::echo_escaped( $license_manager->get_license_is_activated( $license_type ) ?
						sprintf( 'This feature is already activated with the %s Pack', WPSOLR_Escape::esc_html( $license[ OptionLicenses::FIELD_LICENSE_TITLE ] ) )
						: sprintf( 'This feature requires the WPSOLR PRO plugin, with the %s pack activated.', WPSOLR_Escape::esc_html( $license[ OptionLicenses::FIELD_LICENSE_TITLE ] ) ) );
					?>
                    <br/>
                </div>

				<?php if ( defined( 'WPSOLR_PLUGIN_DIR' ) ) { ?>
                    <hr/>
                    <div class="wdm_row">
                        <div class='col_left'>
							<?php WPSOLR_Escape::echo_escaped( sprintf( 'Your %s Pack license %s', WPSOLR_Escape::esc_html( $license[ OptionLicenses::FIELD_LICENSE_TITLE ] ), $license_manager->get_license_is_activated( $license_type ) ? 'is already activated' : 'is not yet activated.' ) ); ?>
                        </div>
                        <div class='col_right'>

							<?php
							$subscription_number = $license_manager->get_license_subscription_number( $license_type );
							?>
                            <input type="password" class="wpsolr_password" placeholder="Your license #"
                                   style="width:100%"
                                   name="<?php WPSOLR_Escape::echo_esc_attr( OptionLicenses::FIELD_LICENSE_SUBSCRIPTION_NUMBER ); ?>"
                                   value="<?php WPSOLR_Escape::echo_esc_attr( $subscription_number ); ?>"
								<?php disabled( $license_manager->get_license_is_need_verification( $license_type ) || $license_manager->get_license_is_can_be_deactivated( $license_type ) ); ?>

                            >
                            <br/><input type="checkbox" class="wpsolr_password_toggle"/> Show the license

                            <p>

                                <input type="button"
                                       name="<?php WPSOLR_Escape::echo_esc_attr( OptionLicenses::AJAX_VERIFY_LICENCE ); ?>"
                                       class="button-primary wdm-save wpsolr_license_submit"
                                       value="Reactivate this site license"
                                       style="margin-top:10px;<?php WPSOLR_Escape::echo_esc_attr( $license_manager->get_license_is_need_verification( $license_type ) ? '' : 'display:none' ); ?>"
                                >

                                <input type="button"
                                       name="<?php WPSOLR_Escape::echo_esc_attr( OptionLicenses::AJAX_ACTIVATE_LICENCE ); ?>"
                                       class="button-primary wdm-save wpsolr_license_submit"
                                       value="Activate this site license"
                                       style="margin-top:10px;<?php WPSOLR_Escape::echo_escaped( ! $license_manager->get_license_is_can_be_deactivated( $license_type ) ? '' : 'display:none' ); ?>"
                                >

                                <input type="button"
                                       name="<?php WPSOLR_Escape::echo_esc_attr( OptionLicenses::AJAX_DEACTIVATE_LICENCE ); ?>"
                                       class="button-primary wdm-save wpsolr_license_submit"
                                       value="Deactivate this site license"
                                       style="margin-top:10px;<?php WPSOLR_Escape::echo_escaped( $license_manager->get_license_is_can_be_deactivated( $license_type ) ? '' : 'display:none' ); ?>"
                                >

                            </p>

                            <span class="error-message"></span>
                            <br><br>

							<?php if ( ! $license_manager->get_license_is_activated( $license_type ) ) { ?>
                                Questions/Answers:
                                <ol>
                                    <li>
                                        <a href="<?php WPSOLR_Escape::echo_esc_url( $license_manager->add_campaign_to_url( 'http://www.gotosolr.com/en/solr-documentation/license-activations/' ) ); ?>"
                                           target="__new1">
                                            I bought a WPSOLR subscription, but cannot find my license#
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php WPSOLR_Escape::echo_esc_url( $license_manager->add_campaign_to_url( 'https://www.wpsolr.com/knowledgebase/how-to-upgrade-my-subscription/' ) ); ?>"
                                           target="__new2">
                                            I want to add
                                            the <?php WPSOLR_Escape::echo_esc_html( $license[ OptionLicenses::FIELD_LICENSE_TITLE ] ); ?>
                                            pack to
                                            my WPSOLR subscription
                                        </a>
                                    </li>
                                </ol>
							<?php } ?>

                        </div>
                        <div class="clear"></div>
                    </div>
				<?php } ?>


				<?php if ( ! $license_manager->get_license_is_activated( $license_type ) ) { ?>
                    <hr/>
                    <div class="wdm_row">
                        <div class='col_left'>
                            No pack yet ?
                        </div>
                        <div class='col_right'>

							<?php foreach ( $license_manager->get_license_orders_urls( $license_type ) as $license_orders_url ) { ?>

                                <p>
                                    <input name="gotosolr_plan_yearly_trial"
                                           type="button" class="button-primary"
                                           value="<?php WPSOLR_Escape::echo_esc_attr( sprintf( $license_orders_url[ OptionLicenses::FIELD_ORDER_URL_BUTTON_LABEL ], $license[ OptionLicenses::FIELD_LICENSE_TITLE ] ) ); ?>"
                                           onclick="window.open('<?php WPSOLR_Escape::echo_esc_url( $license_orders_url[ OptionLicenses::FIELD_ORDER_URL_LINK ] ); ?>', '__blank');"
                                    />
                                </p>

							<?php } ?>
                            <p>The WPSOLR PRO plugin is a yearly subscription, including all features and extension of
                                WPSOLR, with forums support, and automatic upgrades / fixes.</p>

                            <h4 class="solr_error" style="font-size: 14px">
                                <a
                                        href="<?php WPSOLR_Escape::echo_esc_url( $license_manager->add_campaign_to_url( 'https://www.wpsolr.com/pricing' ) ); ?>"
                                        target="__new1">See WPSOLR PRO pricing and features</a>
                            </h4>

                            <h3><?php WPSOLR_Escape::echo_escaped( sprintf( 'With your WPSOLR PRO plugin installed and the %s pack activated, you will be able to:', WPSOLR_Escape::esc_html( $license[ OptionLicenses::FIELD_LICENSE_TITLE ] ) ) ); ?></h3>
                            <ol>
								<?php foreach ( $license_manager->get_license_features( $license_type ) as $feature ) { ?>
                                    <li>
										<?php WPSOLR_Escape::echo_esc_html( $feature ); ?>
                                    </li>
								<?php } ?>
                            </ol>

                            <h3>Instructions:</h3>
                            Click on the button to be redirected to your order page.<br/>
                            After completion of your order, you will receive an email with:
                            <ol>
                                <li>A link to download WPSOLR PRO</li>
                                <li>A license to activate your WPSOLR PRO</li>
                            </ol>
                            <br/>
                            See documentation here to migrate your free WPSOLR plugin to your new WPSOLR PRO plugin: <a
                                    href="<?php WPSOLR_Escape::echo_esc_url( $license_manager->add_campaign_to_url( 'https://www.wpsolr.com/knowledgebase/migrate-wpsolr-wpsolr-pro/' ) ); ?>"
                                    target="__new1">https://www.wpsolr.com/knowledgebase/how-to-activate-a-license-pack/</a>

                            <h3>Chat</h3>
                            If you are quite, but not completely, convinced, let's have a chat at <a
                                    href="<?php WPSOLR_Escape::echo_esc_url( $license_manager->add_campaign_to_url( 'https://www.wpsolr.com' ) ); ?>"
                                    target="__new1">wpsolr.com chat box</a>.
                            <br/> We also deliver custom developments, if your project needs extra care.

                        </div>
                        <div class="clear"></div>
                    </div>
				<?php } ?>

            </div>

        </form>

    </div>

<?php } ?>