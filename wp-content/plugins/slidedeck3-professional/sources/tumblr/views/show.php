<?php
/**
 * SlideDeck Facebook Content Source
 * 
 * More information on this project:
 * http://www.slidedeck.com/
 * 
 * Full Usage Documentation: http://www.slidedeck.com/usage-documentation 
 * 
 * @package SlideDeck
 * @subpackage SlideDeck 3 Pro for WordPress
 * @author SlideDeck
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

<div id="content-source-tumblr">
    <input type="hidden" name="source[]" value="<?php echo $this->name; ?>" />
    <div class="inner">
        <ul class="content-source-fields">
            <li>
				<?php $tooltip =  __( 'This can be the name of your tumblr domain name', $namespace ) ?>
			    <?php slidedeck2_html_input( 'options[tumblr_domain_name]', $slidedeck['options']['tumblr_domain_name'], array( 'label' => __( "Tumblr Blog name", $namespace ) . '<span class="tooltip" title="' . $tooltip . '"></span>', array( 'size' => 20, 'maxlength' => 255 ) ) ); ?>
            </li>
            <li>
				<?php $tooltip =  __( 'Choose whether to show Photos or Videos.', $namespace ) ?>
			    <?php slidedeck2_html_input( 'options[tumblr_post_type]', $slidedeck['options']['tumblr_post_type'], array( 'type' => 'radio', 'attr' => array( 'class' => 'fancy' ), 'label' => __( 'Post Type', $namespace ), 'values' => array(
			        'photos' => __( 'Photos', $namespace ),
			        'videos' => __( 'Videos', $namespace )
			    ), 'description' => "Choose whether to show Photos or Videos." ) ); ?>
            </li>
	    
        </ul>
    </div>
</div>
