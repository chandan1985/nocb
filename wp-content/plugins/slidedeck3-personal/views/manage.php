<?php
/**
 * Overview list of SlideDecks
 * 
 * More information on this project:
 * http://www.slidedeck.com/
 * 
 * Full Usage Documentation: http://www.slidedeck.com/usage-documentation 
 * 
 * @package SlideDeck
 * @subpackage SlideDeck 3 Pro for WordPress
 * @author Hummingbird Web Solutions Pvt. Ltd.
 */

/*
Copyright 2012 HBWSL  (email : support@hbwsl.com)

This file is part of SlideDeck.

SlideDeck is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

SlideDeck is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with SlideDeck.  If not, see <http://www.gnu.org/licenses/>.
*/
?>

<?php slidedeck2_flash(); ?>

<div class="slidedeck-wrapper">
    <div class="wrap" id="slidedeck-overview">
        <?php if( isset( $_GET['msg_deleted'] ) ): ?>
            <div id="slidedeck-flash-message" class="updated" style="max-width:964px;"><p><?php _e( "SlideDeck successfully deleted!", $namespace ); ?></p></div>
            <script type="text/javascript">(function($){if(typeof($)!="undefined"){$(document).ready(function(){setTimeout(function(){$("#slidedeck-flash-message").fadeOut("slow");},5000);});}})(jQuery);</script>
        <?php endif; ?>
        <span class="demo"><a target="_blank" href="https://www.slidedeck.com/demo/?utm_source=sd3_demo&utm_campaign=sd3_personal&utm_medium=link">Demo</a></span>
        <span class="docs"><a target="_blank" href="https://www.slidedeck.com/docs/?utm_source=sd3_docs&utm_campaign=sd3_personal&utm_medium=link">Documentation</a></span>
        <div id="slidedeck-types">
	<div class="loader" style="display:none">
	<img  src= "<?php echo SLIDEDECK2_URL .'/images/lazy-load-large.gif' ?>" >
	</div>
	<?php 


do_action("slidedeck_dashboard"); ?>
            <?php echo $this->upgrade_button('manage'); ?>
            <h1><?php _e( "Manage SlideDeck 3", $namespace ); ?></h1>
            <?php
               //$create_dynamic_slidedeck_block_html = apply_filters( "{$namespace}_create_dynamic_slidedeck_block", "" );
               //echo $create_dynamic_slidedeck_block_html;
               
               $create_new_slide_slidedeck_block = apply_filters( "{$namespace}_create_new_slide_slidedeck_block", "" );
               echo $create_new_slide_slidedeck_block;
               
               $create_slide_using_template_block = apply_filters( "{$namespace}_create_slide_using_template_block", "" );
               echo $create_slide_using_template_block;
               
               //$create_custom_slidedeck_block_html = apply_filters( "{$namespace}_create_custom_slidedeck_block", "" );
               //echo $create_custom_slidedeck_block_html;
            ?>
        </div>
        
        <div id="slidedeck-table">
            <?php if( !empty( $slidedecks ) ): ?>
                <form action="<?php echo admin_url( 'admin-ajax.php' ); ?>" id="slidedeck-table-sort">
                    <fieldset>
                        <?php  /*
                                *Seach field and submit button for slider search
                                */ ?>
                        <input type="search" placeholder="Search slider by name.." id="slidedeck-search" name="slidedeck-search"  />
                        <input type="submit" id="slidedeck-search-submit" class="button" name="slidedeck-search-submit" value="Search" />
                        <input type="hidden" value="<?php echo $namespace; ?>_sort_manage_table" name="action" />
                        <?php wp_nonce_field( "slidedeck-sort-manage-table" ); ?>

                        <label for="slidedeck-table-sort-select"><?php _e( "Sort By:", $namespace ); ?></label>
                        <select name="orderby" id="slidedeck-table-sort-select" class="fancy">
                            <?php foreach( $order_options as $value => $label ): ?>
                                <option value="<?php echo $value; ?>"<?php if( $value == $orderby ) echo ' selected="selected"'; ?>><?php _e( $label, $namespace ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </fieldset>
                </form>
            <?php endif; ?>
            
            <div class="float-wrapper">
                <div class="left">
                    <?php include( SLIDEDECK2_DIRNAME . '/views/elements/_manage-table.php' ); ?>
                </div>
                <div class="right">
                    <div class="right-inner">
                        <div id="manage-iab" class="iab">
                            <!-- <iframe height="100%" frameborder="0" scrolling="no" width="100%" allowtransparency="true" src="<?php echo $sidebar_ad_url; ?>"></iframe> -->
                        </div>
                        <div id="slidedeck-support-questions" class="right-column-module">
                            <h4><?php _e( "Have questions?", $namespace ); ?></h4>
                            <p><?php _e( "See if there are any solutions in our support section.", $namespace ); ?></p>
                            <a href="<?php admin_url( 'admin.php' ); ?>?page=slidedeck2.php/support" class="button slidedeck-noisy-button" target="_blank"><span><?php _e( "Get Support" , $namespace ); ?></span></a>
                        </div>
                        
                        <?php do_action( "{$namespace}_manage_sidebar_bottom" ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
