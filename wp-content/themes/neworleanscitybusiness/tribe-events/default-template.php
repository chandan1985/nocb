<?php

/**
 * Default Events Template
 * This file is the basic wrapper template for all the views if 'Default Events Template'
 * is selected in Events -> Settings -> Display -> Events Template.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/default-template.php
 *
 * @package TribeEventsCalendar
 * @version 4.6.23
 *
 */

if (!defined('ABSPATH')) {
    die('-1');
}

get_header();
?>

<?php
global $wp;
$event_id = get_the_ID();
// $event_cats = get_the_term_list($event_id, Tribe__Events__Main::TAXONOMY);
$term_list = wp_get_post_terms($event_id, Tribe__Events__Main::TAXONOMY);
$event_cats = '';
foreach ($term_list as $term_single) {
	$single_term_id = $single_term_link = '';
	$single_term_id = $term_single->term_id;
	$single_term_link = get_term_link(
		$single_term_id,
		Tribe__Events__Main::TAXONOMY
	);
	//   $event_cats .= '<a href="javascript:void(0)" class="tag">' . $term_single->name . '</a>';
	$event_cats_name = $term_single->name;
	$event_cats_link = get_category_link($term_single);
}

$event_url = home_url($wp->request);
$url_param = end(explode('/', $event_url));
$values = parse_url($event_url);
$path = explode('/',$values['path']);
$category_listing = array_search("category",$path);
$footer_ad_board = get_post_meta( $event_id, 'footer_adboard', true );
$events_slug = tribe_get_option( 'eventsSlug', 'events' );
$current_path = $_SERVER['REQUEST_URI'];
$path_array = explode('/', $current_path);

if(in_array($events_slug, $path_array) && !$category_listing){ ?>
	<div class="breadcrumb-section container">
		<a href="/">Home</a><span>></span>Events<span>
	</div>
	<div class="heading-section container-fluid">
		<div class="container">
			<h1 class="page-heading">New Orleans Citybusiness Events</h1>
		</div>
	</div>

<?php }elseif ($category_listing) { ?>
	<div class="breadcrumb-section container">
		<a href="/">Home</a><span>></span><a href="/<?php print $events_slug ; ?>/">Events</a><span>></span><?php echo $event_cats_name; ?><span>
	</div>
	<div class="heading-section container-fluid">
		<div class="container">
			<h1 class="page-heading"><?php echo $event_cats_name; ?></h1>
		</div>
	</div>

<?php } else { ?>

<div class="breadcrumb-section container">
	<a href="/">Home</a><span>></span><a href="/<?php print $events_slug ; ?>/">Events</a><span>></span><?php
		if ($event_cats_name ) { ?>
			<a href="<?php echo $event_cats_link; ?>"><?php echo $event_cats_name; ?></a><span>></span>
		<?php }the_title();?>
	</div>

	<div class="heading-section container-fluid">
		<div class="container">
			<h1 class="page-heading"><?php the_title(); ?></h1>
		</div>
	</div>

<?php }?>


<div class="event-lsiting-page container">
	<!-- Start row -->
	<div class="row">

	<?php
	if(in_array($events_slug, $path_array)){
	?>
		<!-- Start col-sm-9 -->
		<div class="col-sm-9">
			<div id="tribe-events-pg-template" class="tribe-events-pg-template">
				<?php tribe_events_before_html(); ?>	
				<?php tribe_get_view(); ?>
				<?php tribe_events_after_html(); ?>
			</div> <!-- #tribe-events-pg-template -->	
			
			<div class='footer-bottom-ad-section'>
				<?php dynamic_sidebar('footer-adboard');  ?>
			</div>
			
		</div>
		<!-- End col-sm-9 -->
		<!-- Start col-sm-3 -->
		<div class="col-sm-3">
		<?php						
			dynamic_sidebar('event-right-sidebar');
		?>
		</div>
		<!-- End col-sm-3 -->
	<?php
	}else{
		$full_width = get_post_meta( $event_id, 'full_width', true );
		$show_ad = get_post_meta( $event_id, 'show_ad_in_sidebar', true );
		if($full_width == 'Yes'){
			?>
				<div class="event-lsiting-page container">
					<div class="row">
						<div class="col-sm-9">
							<?php require_once 'event-details.php';?>
						</div>
						<div class="col-sm-3">
							<?php
								if($show_ad != 'No') dynamic_sidebar('event-right-side-ad-area'); 
							?>
						</div>
					</div>
					<div class="row">
						<?php require_once 'event-content.php';?>
					</div>	
					<?php
						if($footer_ad_board != 'No'){
							?>
								<div class='footer-bottom-ad-section'>
									<?php dynamic_sidebar('footer-adboard');  ?>
								</div>
							<?php
						}
					?>
				</div>
			<?php
		}else{
			?>
				<div class="event-lsiting-page container">
					<!-- Start row -->
					<div class="row">
						<!-- Start col-sm-9 -->
						<div class="col-sm-9">
							<div id="tribe-events-pg-template" class="tribe-events-pg-template">
								<?php tribe_events_before_html(); ?>	
								<?php tribe_get_view(); ?>
								<?php tribe_events_after_html(); ?>
							</div> <!-- #tribe-events-pg-template -->	
							
							<?php
								if($footer_ad_board != 'No'){
									?>
										<div class='footer-bottom-ad-section'>
											<?php dynamic_sidebar('footer-adboard');  ?>
										</div>
									<?php
								}
							?>
						</div>
						<!-- End col-sm-9 -->
						<!-- Start col-sm-3 -->
						<div class="col-sm-3">
							<?php						
								dynamic_sidebar('event-right-sidebar');
							?>
						</div>
						<!-- End col-sm-3 -->
					</div>
					<!-- End row -->
					</div>
					<!-- End category bottom ad section -->
				</div>
			<?php
		}
	}
	?>
	</div>
	<!-- End row -->
	</div>




</div>


<?php get_footer();
