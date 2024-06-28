<?php 
/**
 * Basic Theme Function
 * 
 * @package Mag_Lite
 */


if ( ! function_exists( 'mag_lite_fonts_url' ) ) :

    /**
     * Return fonts URL.
     *
     * @since 1.0.0
     * @return string Font URL.
     */
    function mag_lite_fonts_url() {

    $fonts_url = '';

    /**
     * Translators: If there are characters in your language that are not
     * supported by Libre Franklin, translate this to 'off'. Do not translate
     * into your own language.
     */
    $roboto = _x( 'on', 'Roboto Condensed font: on or off', 'mag-lite' );

    if ( 'off' !== $roboto ) {
        $font_families = array();

        $font_families[] = 'Roboto Condensed:300,400,500,600,700';

        $query_args = array(
            'family' => urlencode( implode( '|', $font_families ) ),
            'subset' => urlencode( 'latin,latin-ext' ),
            );

        $fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
    }

    return esc_url_raw( $fonts_url );
}

endif;

//  Customizer Control
if (class_exists('WP_Customize_Control') && ! class_exists( 'Mag_Lite_Image_Radio_Control' ) ) {
    /**
    * Customize sidebar layout control.
    */
    class Mag_Lite_Image_Radio_Control extends WP_Customize_Control {

        public function render_content() {

            if (empty($this->choices))
                return;

            $name = '_customize-radio-' . $this->id;
            ?>
            <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <ul class="controls" id='mag-lite-img-container'>
                <?php
                foreach ($this->choices as $value => $label) :
                    $class = ($this->value() == $value) ? 'mag-lite-radio-img-selected mag-lite-radio-img-img' : 'mag-lite-radio-img-img';
                    ?>
                    <li style="display: inline;">
                        <label>
                            <input <?php $this->link(); ?>style = 'display:none' type="radio" value="<?php echo esc_attr($value); ?>" name="<?php echo esc_attr($name); ?>" <?php
                                                          $this->link();
                                                          checked($this->value(), $value);
                                                          ?> />
                            <img src='<?php echo esc_url($label); ?>' class='<?php echo esc_attr($class); ?>' />
                        </label>
                    </li>
                    <?php
                endforeach;
                ?>
            </ul>
            <?php
        }

    }
}
