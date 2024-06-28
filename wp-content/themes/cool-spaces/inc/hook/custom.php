<?php
/**
 * Custom theme functions.
 *
 * This file contains hook functions attached to theme hooks.
 *
 * @package Mag_Lite
 */

if ( ! function_exists( 'mag_lite_top_header' ) ) :
	/**
	 * Top Heading
 	 *
	 * @since 1.0.0
	 */
function mag_lite_top_header() {
	?>

	<?php $enable_top_header = mag_lite_get_option( 'enable_top_header' ); 
	if( true == $enable_top_header ):
	?>
		<div class="top-menu-toggle_bar_wrapper">
			<div class="top-menu-toggle_trigger">
				<span></span>
				<span></span>
				<span></span>
			</div>
		</div>

		<div class="top-menu-toggle_body_wrapper hide-menu">
			<div class="top-bar">
				<div class="container">
					<div class="row">

						<div class="top-header-left custom-col-6">
							<?php $top_header_left = mag_lite_get_option( 'top_header_left' ); ?>
							<?php if( 'menu' == $top_header_left) :?>
								<?php wp_nav_menu( array(
									'theme_location'  => 'top-menu',
									'container'       => false,							
									'depth'           => 1,
									'fallback_cb'     => false,

									) ); 
								?>
							<?php endif;?>

							<?php if( 'social-media' == $top_header_left) :?>
								<div class="inline-social-icons social-links">
									<?php wp_nav_menu( array(
										'theme_location'  => 'social-media',
										'container'       => false,							
										'depth'           => 1,
										'fallback_cb'     => false,

										) ); 
									?>
								</div>
							<?php endif;?>	
							
							<?php if( 'address' == $top_header_left) :?>
								<?php $header_address = mag_lite_get_option('header_address');
								$header_number = mag_lite_get_option('header_number');
								$header_email = mag_lite_get_option('header_email');?>
								<ul class="top-address">
									<?php if(!empty($header_address)):?>
										<li>
											<a href="tel:<?php echo preg_replace( '/\D+/', '', esc_attr( $header_number ) ); ?>"><i class="fa fa-phone"></i><?php echo esc_attr($header_number);?></a>
										</li>
									<?php endif;?>

									<?php if(!empty($header_address)):?>
										<li><i class="fa fa-map-marker"></i><?php echo esc_html( $header_address );?></li>
									<?php endif;?>

									<?php if(!empty($header_email)):?>
										<li>
											<a href="mailto:<?php echo esc_attr($header_email);?>"><i class="fa fa-envelope"></i><?php echo esc_attr( antispambot( $header_email ) ); ?></a>
										</li>
									<?php endif;?>									
								</ul>								
							<?php endif;?>	

							<?php if( 'current-date' == $top_header_left) :?>
								<div class="date-section">
									<?php echo esc_html( date_i18n( 'l, F d, Y' ) ); ?>
								</div>
							<?php endif;?>																			
						
						</div>

						<div class="top-header-right custom-col-6">
							<?php $top_header_right = mag_lite_get_option( 'top_header_right' ); ?>
							<?php if( 'menu' == $top_header_right) :?>
								<?php wp_nav_menu( array(
									'theme_location'  => 'top-menu',
									'container'       => false,							
									'depth'           => 1,
									'fallback_cb'     => false,

									) ); 
								?>
							<?php endif;?>

							<?php if( 'social-media' == $top_header_right) :?>
								<div class="inline-social-icons social-links">
									<?php wp_nav_menu( array(
										'theme_location'  => 'social-media',
										'container'       => false,							
										'depth'           => 1,
										'fallback_cb'     => false,

										) ); 
									?>
								</div>
							<?php endif;?>	
							
							<?php if( 'address' == $top_header_right) :?>
								<?php $header_address = mag_lite_get_option('header_address');
								$header_number = mag_lite_get_option('header_number');
								$header_email = mag_lite_get_option('header_email');?>
								<ul class="top-address">
									<?php if(!empty($header_address)):?>
										<li>
											<a href="tel:<?php echo preg_replace( '/\D+/', '', esc_attr( $header_number ) ); ?>"><i class="fa fa-phone"></i><?php echo esc_attr($header_number);?></a>
										</li>
									<?php endif;?>

									<?php if(!empty($header_address)):?>
										<li><i class="fa fa-map-marker"></i><?php echo esc_html( $header_address );?></li>
									<?php endif;?>

									<?php if(!empty($header_email)):?>
										<li>
											<a href="mailto:<?php echo esc_attr($header_email);?>"><i class="fa fa-envelope"></i><?php echo esc_attr( antispambot( $header_email ) ); ?></a>
										</li>
									<?php endif;?>									
								</ul>								
							<?php endif;?>	

							<?php if( 'current-date' == $top_header_right) :?>
								<div class="date-section">
									<?php echo esc_html( date_i18n( 'l, F d, Y' ) ); ?>
								</div>
							<?php endif;?>
						</div>  

					</div>        
				</div>
			</div>
			<div class="news-ticker">
				<?php if ( is_active_sidebar( 'news-ticker-section' ) ) {
	    	
					dynamic_sidebar( 'news-ticker-section' ); 

				} ?>
			</div>
		</div>

	<?php endif;?>

	<?php 	
}

endif;
add_action( 'mag_lite_action_header', 'mag_lite_top_header', 10);

if ( ! function_exists( 'mag_lite_site_branding' ) ) :
	/**
	 * Site branding 
 	 *
	 * @since 1.0.0
	 */
function mag_lite_site_branding() {
	?>
	<div class="hgroup-wrap">
		<div class="container">
			<section class="site-branding"> <!-- site branding starting from here -->
				<?php $site_identity = mag_lite_get_option( 'site_identity' );				
					$title = get_bloginfo( 'name', 'display' );
					$description    = get_bloginfo( 'description', 'display' );

					if( 'logo-only' == $site_identity){

						if ( has_custom_logo() ) {

							the_custom_logo();

						}
					} elseif( 'logo-text' == $site_identity){

						if ( has_custom_logo() ) {

							the_custom_logo();

						}

						if ( $description ) {
							echo '<p class="site-description">'.esc_attr( $description ).'</p>';
						}

					} elseif( 'title-only' == $site_identity && $title ){ ?>

						<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
						<?php 

					}elseif( 'title-text' == $site_identity){ 
						
						if( $title ){ ?>

							<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
							<?php 
						}

						if ( $description ) {

							echo '<p class="site-description">'.esc_attr( $description ).'</p>';

						}
						
				} ?> 				
			</section> <!-- site branding ends here -->

			<?php $header_image = get_header_image(); 

			if ( is_active_sidebar( 'header-advertisement' ) ) : ?>
				<div class="hgroup-right"> <!-- hgroup right starting from here -->
					<div class="ads-section">
						<figure>
							<?php dynamic_sidebar( 'header-advertisement' );?>
						</figure>
					</div>
				</div> <!-- hgroup right ends here -->
			<?php endif;?>

		</div>
	</div>

	<?php 
}
endif;
add_action( 'mag_lite_action_header', 'mag_lite_site_branding', 15 );

if ( ! function_exists( 'mag_lite_main_menu' ) ) :
	/**
	 * Primary Menu
 	 *
	 * @since 1.0.0
	 */
function mag_lite_main_menu() {
	?>
	<div id="navbar" class="navbar">  <!-- navbar starting from here -->
		<div class="container">
			<nav id="site-navigation" class="navigation main-navigation">
        		<?php
					wp_nav_menu(
						array(
							'theme_location' => 'menu-1',				
							'container_class' => 'menu-top-menu-container clearfix',
            				'items_wrap' => '<ul>%3$s</ul>',
							'fallback_cb'    => 'wp_page_menu',
							)
						);
				?>
				
			</nav>
			<div id="left-search" class="search-container">
				<div class="search-toggle"></div>
				<div class="search-section">
					<?php get_search_form();?>                   
					<span class="search-arrow"></span>
				</div>            
			</div>
		</div>
	</div> <!-- navbar ends here -->
	<?php 
}
endif;
add_action( 'mag_lite_action_header', 'mag_lite_main_menu', 20 );

if ( ! function_exists( 'mag_lite_slider_section' ) ) :
	/**
	 * Slider Section
 	 *
	 * @since 1.0.0
	 */
function mag_lite_slider_section() {
	if ( !is_front_page() ) { 
		$bg_image_url = get_header_image();
	?>
		<div class="page-title-wrap" style="background-image:url( <?php echo esc_url( $bg_image_url )?>);">
			<div class="container">
		        <?php $enable_breadcrumb = mag_lite_get_option( 'enable_breadcrumb' );
	 
		        if( true === $enable_breadcrumb):?>
		       		<h3><?php mag_lite_breadcrumb(); ?></h3>
		       	<?php endif;	?>
		       	
			</div>
		</div>

	<?php } else{ ?>              

    <?php if ( is_active_sidebar( 'home-slider-section' ) ) { ?>

    	<div class="slider-news-container">
			<div class="container">
				<div class="site-main">

					<?php dynamic_sidebar( 'home-slider-section' ); ?>	
				</div>
			</div>
		</div>		


	<?php } ?>

	<?php }
	?>

	<?php 
}
endif;
add_action( 'mag_lite_action_header', 'mag_lite_slider_section', 25 );

if ( ! function_exists( 'mag_lite_footer_widgets' ) ) :
	/**
	 * Footer Menu
 	 *
	 * @since 1.0.0
	 */
function mag_lite_footer_widget() {
	?>
	<?php if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' )  || is_active_sidebar( 'footer-4' ) ) : ?>
	
		<div id="footer-widget-area" class="footer-3c container"> <!-- widget area starting from here -->
			
					<?php
					$column_count = 0;
					$class_coloumn =12;
					for ( $i = 1; $i <= 4; $i++ ) {
						if ( is_active_sidebar( 'footer-' . $i ) ) {
							$column_count++;
							$class_coloumn = 12/$column_count;
						}
					} ?>

					<?php 
					switch(absint( $class_coloumn )){
						case 1:$column_class = 'footer-1c container';
						case 2:$column_class = 'footer-2c container';
						case 3:$column_class = 'footer-3c container';
						case 4:$column_class = 'footer-4c container';
						default:$column_class = 'footer-widgets-box';
					}
					
					for ( $i = 1; $i <= 3 ; $i++ ) {
						if ( is_active_sidebar( 'footer-' . $i ) ) { ?>
							
								<?php dynamic_sidebar( 'footer-' . $i ); ?>
							
						<?php }
					} ?>
				

		</div> <!-- widget area starting from here -->
		
	<?php endif;?> 	

	<?php 
}
endif;
add_action( 'mag_lite_action_footer', 'mag_lite_footer_widget', 10 );

if ( ! function_exists( 'mag_lite_footer_subscription' ) ) :
	/**
	 * Footer Subscription
 	 *
	 * @since 1.0.0
	 */
function mag_lite_footer_subscription() {
	?>
	<?php 
	$subscription_page = mag_lite_get_option( 'subscription_page' );
	if( !empty( $subscription_page ) ): ?>	
		<?php ; 

		$args = array (	            		            
			'page_id'			=> absint($subscription_page ),
			'post_status'   	=> 'publish',
			'post_type' 		=> 'page',
			);

		$loop = new WP_Query($args); 


		if ( $loop->have_posts() ) : 

			while ($loop->have_posts()) : $loop->the_post(); 
				$feat_image_url = wp_get_attachment_url( get_post_thumbnail_id() );

			?>	
			    <section class="subscribe-section" style="background: url( <?php echo esc_url( $feat_image_url)?>);">
			        <div class="container">
			            <div class="subscribe-content">
			            	<div class="subscribe-content-wrapper">
				                <header class="entry-header heading">
				                	<h2 class="entry-title"><?php the_title();?></h2>
				                </header>

				                <?php the_content();?>	
			                </div>               

			            </div>
			        </div>
			    </section>
		    <?php endwhile;
		    wp_reset_postdata();?>
	    <?php endif;?>
    <?php endif;?>   
	<?php 
}
endif;
add_action( 'mag_lite_action_footer', 'mag_lite_footer_subscription', 15 );

if ( ! function_exists( 'mag_lite_footer_copyright' ) ) :
	/**
	 * Footer Copyright 	 *
	 * @since 1.0.0
	 */
function mag_lite_footer_copyright() {
	?>
	<div class="site-generator"> <!-- site-generator starting from here -->
		<div class="container">
				<?php $footer_social_icon = mag_lite_get_option('footer_social_icon'); ?>

				<?php 
				$copyright_footer = mag_lite_get_option( 'copyright_text' ); 
				if ( ! empty( $copyright_footer ) ) {
					$copyright_footer = wp_kses_data( $copyright_footer );
				}
				// Powered by content.
				$powered_by_text = sprintf( __( 'Theme of %s', 'mag-lite' ), '<a target="_blank" rel="designer" href="https://rigorousthemes.com/">Rigorous Themes</a>' );  
				?>
				<span class="copy-right"><?php echo wp_kses_post($powered_by_text);?><?php echo esc_html( $copyright_footer );?></span>
				<?php $enable_footer_menu = mag_lite_get_option( 'enable_footer_menu' ); 
				if( true == $enable_footer_menu ) : ?>
					<?php if ( has_nav_menu( 'social-media' ) ) : ?>

						<div class="inline-social-icons social-links">					

							<?php wp_nav_menu( array(
								'theme_location'  => 'social-media',
								'container'       => false,	
								'fallback_cb'     => 'wp_page_menu',

							) ); ?>
							
						</div>

					<?php endif; ?>
				<?php endif; ?>
		</div> 
	</div> <!-- site-generator ends here -->       

	<?php 
}
endif;
//add_action( 'mag_lite_action_footer', 'mag_lite_footer_copyright', 20 );

if ( ! function_exists( 'mag_lite_footer_custom' ) ) :
	/**
	 * Footer Copyright 	 *
	 * @since 1.0.0
	 */
function mag_lite_footer_custom() {
	?>
	<div class="site-generator"> <!-- site-generator starting from here -->
		<div class="container">
				<?php $footer_social_icon = mag_lite_get_option('footer_social_icon'); ?>

				<?php 
				$copyright_footer = mag_lite_get_option( 'copyright_text' ); 
				if ( ! empty( $copyright_footer ) ) {
					$copyright_footer = wp_kses_data( $copyright_footer );
				}
				// Powered by content.
				$powered_by_text = sprintf( __( 'Theme of %s', 'mag-lite' ), '<a target="_blank" rel="designer" href="https://rigorousthemes.com/">Rigorous Themes</a>' );  
				?>
				<span class="copy-right"><?php echo wp_kses_post($powered_by_text);?><?php echo esc_html( $copyright_footer );?></span>
				<?php $enable_footer_menu = mag_lite_get_option( 'enable_footer_menu' ); 
				if( true == $enable_footer_menu ) : ?>
					<?php if ( has_nav_menu( 'social-media' ) ) : ?>

						<div class="inline-social-icons social-links">					

							<?php wp_nav_menu( array(
								'theme_location'  => 'social-media',
								'container'       => false,	
								'fallback_cb'     => 'wp_page_menu',

							) ); ?>
							
						</div>

					<?php endif; ?>
				<?php endif; ?>
		</div> 
	</div> <!-- site-generator ends here -->       

	<?php 
}
endif;


add_action( 'mag_lite_action_footer_new', 'mag_lite_footer_custom', 10 );