<!DOCTYPE html>
<html <?php language_attributes(); ?> prefix="og: http://ogp.me/ns#">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<!-- Blueconic Script -->
<?php 
	$bc_config = get_option('tdc_paywall_data');
	if(isset($bc_config['bc_head_script'])){
		echo $bc_config['bc_head_script'];
	}
?>
<!-- end -->

<?php wp_head(); ?>

</head>
<?php global $is_IE ?>
<body id="top" <?php body_class(); ?>>
<?php if( tie_get_option('banner_bg_url') && tie_get_option('banner_bg') ): ?>
	<a href="<?php echo tie_get_option('banner_bg_url') ?>" target="_blank" class="background-cover"></a>
<?php else: ?>
	<div class="background-cover"></div>
<?php endif; ?>
	<div class="wrapper<?php if(tie_get_option( 'theme_layout' ) == 'full') echo ' full-site'; if(tie_get_option( 'columns_num' ) == '2c') echo ' layout-2c'; if( tie_get_option( 'lazy_load' ) && !tie_is_android() ) echo ' animated'; ?>">
		<?php if(!tie_get_option( 'top_menu' )): ?>
		<div class="top-nav fade-in animated1 <?php echo tie_get_option( 'top_left' ); ?>">
			<div class="container">
				<div class="search-block">
					<form method="get" id="searchform" action="<?php echo home_url(); ?>/">
						<button class="search-button" type="submit" value="<?php if( !$is_IE ) _e( 'Search' , 'tie' ) ?>"></button>
						<input type="text" id="s" name="s" value="<?php _e( 'Search...' , 'tie' ) ?>" onfocus="if (this.value == '<?php _e( 'Search...' , 'tie' ) ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'Search...' , 'tie' ) ?>';}"  />
					</form>
				</div><!-- .search-block /-->
				<?php tie_get_social( 'yes' , 'flat' , 'tooldown' , true ); ?>

				<?php
					//tdc - force the nav always
					wp_nav_menu(array('container_class' => 'top-menu', 'theme_location' => 'top-menu', 'fallback_cb' => 'tie_nav_fallback'));

					if( tie_get_option( 'top_left' ) == 'head_menu' ) {
						// wp_nav_menu(array('container_class' => 'top-menu', 'theme_location' => 'top-menu', 'fallback_cb' => 'tie_nav_fallback'));
					}
					elseif(tie_get_option( 'top_left' ) == 'head_brnews') {
						get_template_part('includes/breaking-news');
					}
				?>
				
				<?php /*BEGIN CUSTOM MOBILE MENU IMPLEMENTATION*/
				/* <div class="top-menu-liney" style="display:none;"><a class="top-menu-footer-liney" href="#footer-top-menu-liney"><img src="<?php echo get_stylesheet_directory_uri().'/images/lineywhite.png';?>"></a></div> */
				$mobile_menu =  has_nav_menu( 'mobile' ); ?>
				<div class="top-menu-liney" style="display:none;">
					<a class="top-menu-footer-liney" <?php if(!$mobile_menu){ ?> href="#footer-top-menu-liney" <?php }else{ ?> href="javascript:;" onclick="toggleMobileMenu();" <?php } ?>>
						<?php /* <img src="<?php echo get_stylesheet_directory_uri().'/images/mobilemenu/lineybox.png';?>"> */ ?>
					</a>
				</div>
				<?php /*END CUSTOM MOBILE MENU IMPLEMENTATION*/ ?>
				
				<?php tie_language_selector_flags(); ?>

			</div>
		</div><!-- .top-menu /-->
		<?php endif; ?>

		<div class="container">
		<header id="theme-header">
		<div class="header-content fade-in animated1">
<?php $logo_margin =''; if( tie_get_option( 'logo_margin' )) $logo_margin = ' style="margin-top:'.tie_get_option( 'logo_margin' ).'px"';  ?>
			<div class="logo"<?php echo $logo_margin ?>>
			<?php if( !is_singular() ) echo '<h1>'; else echo '<h2>'; ?>

				<?php
				// masthead url
				if (!tie_get_option( 'masthead_url' )) {
					$cur_blog_id = defined( 'BLOG_ID_CURRENT_SITE' )?  BLOG_ID_CURRENT_SITE :  1;
					$mastheadlink = get_site_url($cur_blog_id);
				} else {
					$mastheadlink = home_url();
				}
				?>
				<?php if( tie_get_option('logo_setting') == 'title' ): ?>
				<a  href="<?php echo $mastheadlink; ?>/"><?php bloginfo('name'); ?></a>
				<span><?php bloginfo( 'description' ); ?></span>
				<?php else : ?>
				<?php if( tie_get_option( 'logo' ) ) $logo = tie_get_option( 'logo' );
						else $logo = get_stylesheet_directory_uri().'/images/logo.png';
					// custom post header image
					if( is_singular() ){
						$get_meta = get_post_custom($post->ID);
						if( !empty($get_meta['tdc_custom_header'][0]) ){
							$logo = $get_meta['tdc_custom_header'][0];
						}
					}

					$svg_fixer = str_replace('.svg','.png',$logo);
				?>
				<a title="<?php bloginfo('name'); ?>" href="<?php echo $mastheadlink; ?>/">
					<img onerror="this.src='<?php echo $svg_fixer; ?>';this.onerror=null;" src="<?php echo $logo ; ?>" alt="<?php bloginfo('name'); ?>" /><strong><?php bloginfo('name'); ?> <?php bloginfo( 'description' ); ?></strong>
				</a>
<?php endif; ?>
			<?php if( !is_singular() ) echo '</h1>'; else echo '</h2>'; ?>
			</div><!-- .logo /-->
<?php if( tie_get_option( 'logo_retina' ) && tie_get_option( 'logo_retina_width' ) && tie_get_option( 'logo_retina_height' )): ?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	var retina = window.devicePixelRatio > 1 ? true : false;
	if(retina) {
       	jQuery('#theme-header .logo img').attr('src', '<?php echo tie_get_option( 'logo_retina' ); ?>');
       	jQuery('#theme-header .logo img').attr('width', '<?php echo tie_get_option( 'logo_retina_width' ); ?>');
       	jQuery('#theme-header .logo img').attr('height', '<?php echo tie_get_option( 'logo_retina_height' ); ?>');
	}
});
</script>
<?php endif; ?>
<?php /*BEGIN CUSTOM MOBILE MENU IMPLEMENTATION*/ ?>
		<?php if($mobile_menu){ ?>
		<div class="overlay" id="mobilemenu" >
			<?php
				get_template_part('includes/mobile-menu');
			?>
		</div>
		<?php } ?>
<?php /*BEGIN CUSTOM MOBILE MENU IMPLEMENTATION*/ ?>
			<?php tie_banner('banner_top' , '<div class="e3lan-top">' , '</div>' ); ?>
			<div class="clear"></div>
		</div>
		<?php $stick = ''; ?>
		<?php if( tie_get_option( 'stick_nav' ) ) $stick = ' fixed-enabled' ?>
		<?php
		//UberMenu Support
		$navID = 'main-nav';
		if ( class_exists( 'UberMenu' ) ){
			$uberMenus = get_option( 'wp-mega-menu-nav-locations' );
			if( !empty($uberMenus) && is_array($uberMenus) && in_array("primary", $uberMenus)) $navID = 'main-nav-uber';
		}?>
			<nav id="<?php echo $navID; ?>" class="fade-in animated2<?php echo $stick; ?>">
				<div class="container">
				<?php $orig_post = $post; wp_nav_menu( array( 'container_class' => 'main-menu', 'theme_location' => 'primary' ,'fallback_cb' => 'tie_nav_fallback',  'walker' => new tie_mega_menu_walker()  ) ); $post = $orig_post; ?>
				</div>
			</nav><!-- .main-nav /-->
		</header><!-- #header /-->

<?php
$sidebar = $sidebar_pos = '';

if( tie_get_option( 'sidebar_pos' ) == 'left' || ( tie_get_option( 'columns_num' ) == '2c' && tie_get_option( 'sidebar_pos' ) == 'nright' ) ) $sidebar = ' sidebar-left';
elseif( $sidebar_pos == 'right' || ( tie_get_option( 'columns_num' ) == '2c' && tie_get_option( 'sidebar_pos' ) == 'nleft' ) ) $sidebar = ' sidebar-right';
elseif( tie_get_option( 'sidebar_pos' ) == 'nleft' ) $sidebar = ' sidebar-narrow-left';
elseif( tie_get_option( 'sidebar_pos' ) == 'nright' ) $sidebar = ' sidebar-narrow-right';

if( is_singular() || ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) ){
	$current_ID = $post->ID;
	if( function_exists( 'is_woocommerce' ) && is_woocommerce() ) $current_ID = woocommerce_get_page_id('shop');

	$get_meta = get_post_custom( $current_ID );

	if( !empty($get_meta["tie_sidebar_pos"][0]) ){
		$sidebar_pos = $get_meta["tie_sidebar_pos"][0];

		if( $sidebar_pos == 'left' || ( tie_get_option( 'columns_num' ) == '2c' && $sidebar_pos == 'nright' )) $sidebar = ' sidebar-left';
		elseif( $sidebar_pos == 'full' ) $sidebar = ' full-width';
		elseif( $sidebar_pos == 'right' || ( tie_get_option( 'columns_num' ) == '2c' && $sidebar_pos == 'nleft' )) $sidebar = ' sidebar-right';
		elseif( $sidebar_pos == 'nright' ) $sidebar = ' sidebar-narrow-right';
		elseif( $sidebar_pos == 'nleft' ) $sidebar = ' sidebar-narrow-left';
	}
}
if(  function_exists('is_bbpress') && is_bbpress() && tie_get_option( 'bbpress_full' )) $sidebar = ' full-width';
?>
	<div id="main-content" class="container fade-in animated3<?php echo $sidebar ; ?>">
	<?php /*Header background cover below added for mobile menu*/ ?>
	<div id="content-background-cover" class="mobile-menu-background-cover"></div>
<?php
$current_url = home_url($_SERVER['REQUEST_URI']);
$options_exclusions = get_option('ad-exclusion');
$wp_rejected_uri = $options_exclusions['wp_rejected_uri'];
if(!empty($wp_rejected_uri))
$wp_rejected_uriArr = preg_split('/\r\n|[\r\n]/', $wp_rejected_uri);
if (is_array($wp_rejected_uriArr) || is_object($wp_rejected_uriArr)) {
foreach ( $wp_rejected_uriArr as $expr ) {
if( $expr != '' && @preg_match( "~$expr~", $current_url ) )
return ; 
}
}
$exclude = "sponsored_content";
$exclude_url = "";
$categories = get_the_terms( get_the_ID(), 'category' );
foreach( $categories as $category ) {
$cat_name[] = $category->name;
} 
if ( !in_array($current_url, $exclude_url))
{
if (!is_singular('sponsored_content')){
	
	if(wp_is_mobile())
	{
		
		
		//	echo do_shortcode('[dfp_ads id=619648]');
		//echo do_shortcode("[dfp_ads id='619648']");
		echo "<script async='async' src='https://www.googletagservices.com/tag/js/gpt.js'></script>
		<script>
		var googletag = googletag || {};
		googletag.cmd = googletag.cmd || [];
		</script>
		<script>
		googletag.cmd.push(function() {
		googletag.defineOutOfPageSlot('/13051489/neworleanscitybusiness/neworleanscitybusiness_mobile', 'div-gpt-ad-mobileoop1x1').addService(googletag.pubads()).setTargeting('pos',['WelcomeAd']);		
		googletag.pubads().enableSingleRequest();
		googletag.pubads().collapseEmptyDivs(true);
		googletag.enableServices();
		});
		</script>
		
		<div id='div-gpt-ad-mobileoop1x1' style='width:1px; height:1px;'
		class='div-gpt-ad-mobileoop1x1 neworleanscitybusiness dfp_ad_pos'>
		<script type='text/javascript'>
		googletag.cmd.push(function () {
		googletag.display('div-gpt-ad-mobileoop1x1');
		googletag.pubads().collapseEmptyDivs(true);
		});
		</script>
		</div>";

		
	}
	else
	{
		//echo do_shortcode('[dfp_ads id=619647]');
		//echo do_shortcode("[dfp_ads id='619647']");
		
		echo "<script async='async' src='https://www.googletagservices.com/tag/js/gpt.js'></script>
		<script>
		var googletag = googletag || {};
		googletag.cmd = googletag.cmd || [];
		</script>
		<script>
		googletag.cmd.push(function() {
		googletag.defineOutOfPageSlot('/13051489/neworleanscitybusiness', 'div-gpt-ad-desktopoop1x1').addService(googletag.pubads());
		googletag.pubads().enableSingleRequest();
		googletag.pubads().collapseEmptyDivs(true);
		googletag.enableServices();
		});
		</script>
		
		<div id='div-gpt-ad-desktopoop1x1'
		class='div-gpt-ad-desktopoop1x1 neworleanscitybusiness dfp_ad_pos'>
		<script type='text/javascript'>
		googletag.cmd.push(function () {
		googletag.display('div-gpt-ad-desktopoop1x1');
		});
		</script>
		</div>";
		
	}
}
}
?>