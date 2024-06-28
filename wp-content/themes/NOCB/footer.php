<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package nocb
 */

 $footer_site_logo = get_option('footer_site_logo');
 $footer_navigation_menu = get_option('footer_navigation_menu');
 $footer_navigation_menu = array(
	 'menu' => $footer_navigation_menu,
	 'menu_class' => 'menu',
 );
 $site_about_info = get_option('site_about_info');
 $my_account_logout_menu = get_option('my_account_logout_menu');
 $my_account_logout_menu = '[tdc-snippet snippet="My Account"]';
 $contact_menu = get_option('contact_menu');
 $label_for_contact_menu = get_option('label_for_contact_menu');
 $contact_menu = array(
	'menu' => $contact_menu,
	'menu_class' => 'menu',
 );
 $advertise_menu = get_option('advertise_menu');
 $label_for_advertise_menu = get_option('label_for_advertise_menu');
 $advertise_menu = array(
	'menu' => $advertise_menu,
	'menu_class' => 'menu',
 );
 $connect_html = get_option('connect_html');
 $newsletter_html= get_option('newsletter_html');
 $digital_edition_html= get_option('digital_edition_html');
 $footer_btm_logo = get_option('footer_btm_logo');
 $copyright_html= get_option('copyright_html');
?>

<footer class="site-footer">

    <!-- Footer section start here -->
    <div class="footer-top-area">
        <div class="container">
            <div class="row">
                <div class="footer-top-left col-md-3"><a href="/"><img src="<?php print  $footer_site_logo; ?>"></a></div>
                <div class="footer-top-right col-md-9"><?php wp_nav_menu($footer_navigation_menu); ?></div>
            </div>
        </div>
    </div>

    <div class="footer-middle-area">
        <div class="container">
            <div class="row">
                <div class="footer-middle-first col-md-3">
					<div class="site-about-info"><?php print $site_about_info; ?></div>
                    <div class="my-account">
                    <?php 
                        print do_shortcode($my_account_logout_menu);
                    ?> 
                    </div>
				</div>
                <div class="footer-middle-second col-md-3">
                    <div class="contact-menu">
                        <h2><?php print $label_for_contact_menu; ?></h2>
                        <?php wp_nav_menu($contact_menu); ?>
                    </div>  
                    <div class="advertise-menu-section">
                        <h2><?php print $label_for_advertise_menu; ?></h2>
                        <?php wp_nav_menu($advertise_menu); ?>
                    </div>  

                </div>
                <div class="footer-middle-third col-md-3">
                    <?php 
                    print $connect_html; 
                    print do_shortcode($newsletter_html);
                    ?>
                </div>
                <div class="footer-middle-fourth col-md-3">
                    <?php print $digital_edition_html; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom-area">
        <div class="container">
            <div class="row">
                <div class="footer-bottom-left col-md-2">
                    <img src="<?php print $footer_btm_logo; ?>">
                </div>
                <div class="footer-bottom-right col-md-10">
                    <div class="copyright-region">
                        <?php print do_shortcode($copyright_html); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</footer><!-- site-footer -->

</div><!-- #page -->
<?php
	$footer_js = get_option('custom_js_for_footer');
	print html_entity_decode($footer_js, ENT_QUOTES);	
?>
<?php wp_footer(); ?>
</body>

</html>