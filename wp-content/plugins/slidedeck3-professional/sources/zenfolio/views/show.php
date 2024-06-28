<?php
/**
 * SlideDeck Zenfolio Content Source
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

$zenfolio_username = $slidedeck['options']['zenfolio_username'];
$zenfolio_password = $slidedeck['options']['zenfolio_password'];
//$galleries_select = $slidedeck['options']['zenfolio_galleries'];

?>
<div id="content-source-zenfolio">
    <input type="hidden" name="source[]" value="<?php echo $this->name; ?>" />
    <div class="inner">
        <ul class="content-source-fields">
            <li class="zenfolio_username">
                <?php slidedeck2_html_input( 'options[zenfolio_username]', $slidedeck['options']['zenfolio_username'], array( 'type' => 'text', 'attr' => array( 'class' => 'fancy' ), 'label' => __( 'Username', $this->namespace ), 
		 ) ); ?>
            </li>
	    <li class="zenfolio_passowrd">
                <?php slidedeck2_html_input( 'options[zenfolio_password]', $slidedeck['options']['zenfolio_password'], array( 'type' => 'password', 'attr' => array( 'class' => 'fancy' ), 'label' => __( 'Password', $this->namespace ), 
		 ) ); ?>
            </li>
	    <li class="zenfolio_authenticate">
                
	    <a class="zenfolio-authenticate-ajax-token button" href="#authenticate"><?php _e( "Authenticate", $this->namespace ); ?></a>
            </li>
	     <li class="zenfolio_galleries">
                <?php if( $galleries_select ): ?>
                <div id="zenfolio-user-galleries">
                    <?php echo $galleries_select; ?>
                </div>
                <?php endif; ?>
            </li>
	     
            
        </ul>
    </div>
</div>
