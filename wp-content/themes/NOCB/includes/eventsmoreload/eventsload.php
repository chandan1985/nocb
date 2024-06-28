<?php
/* Events page load more */


/*------------------------------------------------------------------------------------------------------------------------------------------------*/
function event_list_ajax()
{

	$category = $_POST['category'];
	$regions = $_POST['regions'];
	$state = $_POST['state'];
	$event_date = $_POST['event_date'];
	$event_keyword = $_POST['event_keyword'];
	$event_city = $_POST['event_city'];
	$event_perpage = $_POST['event_perpage'];
	$month_date = $_POST['month_date'];
	$month_date_new = $_POST['month_date_new'];

	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	if ($month_date != '') {
		$posts_per_page = 12;
		$start = ($paged - 1) * 12;
	} else {
		$posts_per_page = 12;
		$start = ($paged - 1) * 12;
	}
	$start_day = 01;
	//						   $end_day = 31;
	$end_day = date("t", strtotime($event_date . '-' . $start_day));

	$event_search_date_1 = $event_date . '-' . $start_day;
	$event_search_date_2 = $event_date . '-' . $end_day;


	$selected_event_search_cat = 0;
	$event_search_date = "";
	if (isset($category) && !empty($category)) {
		$selected_event_search_cat = $category;
	}
	if (isset($event_date) && !empty($event_date)) {
		$event_search_date = $event_date;
	}
	if (isset($event_keyword) && !empty($event_keyword)) {
		$event_search_keyword = $event_keyword;
	}
	if (isset($regions) && !empty($regions)) {
		$event_search_regions = $regions;
	}
	if (isset($event_city) && !empty($event_city)) {
		$event_search_city = $event_city;
	}
	if (isset($state) && !empty($state)) {
		$event_search_state = $state;
	}

	$record_per_page = 12;

	if ($month_date_new != '') {
		$end_date_of_month = date("t", strtotime($month_date_new . "-01"));

	} else if (empty($event_date)) {
		$args = array(
			'post_type' => 'tribe_events',
			'posts_per_page' => $posts_per_page,
			'paged' => $paged,
			'offset' => $start,
			'post_status' => 'publish',
			'orderby' => 'post_date',
			'order' => 'ASC',
			'eventDisplay' => 'custom',
			'paged' => $event_perpage,
			'start_date'   => date('Y-m-d', strtotime('now')),
			'end_date'     => date('Y-m-d', strtotime('+12 Months')),
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'bridge_tower_media_conferences',
					'compare' => 'NOT EXISTS' // Meta query to filter events which are not bridge Tower Media
				),
			)

		);
	}else {
		$args = array(
			'post_type' => 'tribe_events',
			'posts_per_page' => $posts_per_page,
			'paged' => $paged,
			'offset' => $start,
			'post_status' => 'publish',
			'orderby' => 'post_date',
			'order' => 'ASC',
			'eventDisplay' => 'custom',
			'paged' => $event_perpage,
			'start_date'   => date($event_search_date_1, strtotime('now')),
			'end_date'     => date($event_search_date_2, strtotime('+12 Months')),
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'bridge_tower_media_conferences',
					'compare' => 'NOT EXISTS' // Meta query to filter events which are not bridge Tower Media
				),
			)

		);
	}

	// args ends 
	if (isset($category) && !empty($category)) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => Tribe__Events__Main::TAXONOMY,
				'field'    => 'term_id',
				'terms'    => $category,
			),
		);
	}
	if ((isset($state) && !empty($state)) ||
		(isset($event_city) && !empty($event_city)) ||
		(isset($regions) && !empty($regions))
	) {
		$meta_query_array = array();
		if (isset($event_city) && !empty($event_city)) {
			$meta_query_array[] = array(
				'key' => '_VenueCity',
				'value' => $event_city,
				'compare' => 'like',
			);
		}
		if (isset($state) && !empty($state)) {
			if (!empty($meta_query_array)) {
				$meta_query_array["relation"] = "and";
			}
			$meta_query_array[] = array(
				'key' => '_VenueState',
				'value' => $state,
				'compare' => '=',
			);
		}
		if (isset($regions) && !empty($regions)) {
			if (!empty($meta_query_array)) {
				$meta_query_array["relation"] = "and";
			}

			if ("United States" == $regions) {
				$meta_query_array[] = array(
					'key' => '_VenueCountry',
					'value' => "United States",
					'compare' => '=',
				);
			} elseif ("International" == $regions) {
				$meta_query_array[] = array(
					'key' => '_VenueCountry',
					'value' => "United States",
					'compare' => '!=',
				);
			}
		}
		$city_query_args = array(
			'post_type' => 'tribe_venue',
			'posts_per_page' => -1,
			'meta_query' => $meta_query_array, 'fields' => 'ids'
		);

		$events_from_search = get_posts($city_query_args);
		if (empty($events_from_search)) {
			$events_from_search = array(0);
		}
		if (!empty($args['meta_query'])) {
			$args['meta_query']["relation"] = 'AND';
		}
		$args['meta_query'][] = array(
			'key'     => '_EventVenueID',
			'value'   =>  $events_from_search,
			'compare' => 'IN',
		);
	}
	//                        
	if (isset($event_keyword) && !empty($event_keyword)) {
		$args['s'] = $event_keyword;
	}
	//var_dump($args);die;


	$events_query = new WP_Query($args);
	$events_query->set("tribe_is_event_query", True);
	//                        $events_query->set( 'orderby', Tribe__Events__Query::set_orderby( null, $events_query ) );
	//  do_action( 'tribe_events_pre_get_posts', $events_query );


	// echo '<pre>';print_r($events_query);echo '</pre>';
	if ($events_query->have_posts()) { ?>


		<?php
		while ($events_query->have_posts()) :
			$events_query->the_post();
			$end_date = $start_date = $show_date = $event_cats = $show_month = $start_month = $end_month = $featured_img_url = "";
			$event_id = get_the_ID();
			$event_cats = get_the_term_list($event_id, Tribe__Events__Main::TAXONOMY);
			$term_list  =   wp_get_post_terms($event_id, Tribe__Events__Main::TAXONOMY);
			foreach ($term_list as $term_single) {
				$single_term_id = $single_term_link = $event_cats = "";
				$single_term_id = $term_single->term_id;
				$single_term_link = get_term_link($single_term_id, Tribe__Events__Main::TAXONOMY);
				$event_cats .= '<a href="' . $single_term_link . '" class="tag">' . $term_single->name . '</a>';
			}
			$start_month = tribe_get_start_date($event_id, false, 'M');
			$start_date = tribe_get_start_date($event_id, false, 'd');
			$start_year = tribe_get_start_date($event_id, false, 'Y');

			$end_month = tribe_get_end_date($event_id, false, 'M');
			$end_date = tribe_get_end_date($event_id, false, 'd');
			$end_year = tribe_get_end_date($event_id, false, 'Y');

			if ($start_month == $end_month) {
				$show_month = $start_month;
			} else {
				$show_month = $start_month . " - " . $end_month;
			}
			if ($start_date == $end_date && $start_month == $end_month) {
				$show_date = $start_date;
			} else if ($start_date == $end_date) {
				$show_date = $start_date . " - " . $end_date;
			} else {
				$show_date = $start_date . " - " . $end_date;
			}

			if ($start_year == $end_year) {
				$show_year = $start_year;
			} else {
				$show_year = $start_year . " - " . $end_year;
			}

			$featured_img_url = get_the_post_thumbnail_url($event_id);
			$perma = get_the_permalink($event_id);
			$title = get_the_title($event_id);
			$venue = tribe_get_venue($event_id);

		?>
			<div class="event-item  col-sm-4">

				<?php $term_lists = wp_get_post_terms($event_id, Tribe__Events__Main::TAXONOMY);
				if ($term_lists) :
				?>
					<p class="tag-list lead">
					<?php
                        foreach ($term_lists as $term_single) :
                        //   $wpseo_primary_term = new WPSEO_Primary_Term( 'tribe_events_cat', get_the_id() );
                        //   $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
						$wpseo_primary_term = get_primary_category_id(get_the_id());
                          $term = get_term( $wpseo_primary_term );
                          $term_name = $term->name;
                          $term_id = $term->term_id;
                          $permalink = get_category_link($term);
                        endforeach; 
                        if(!$term_name){
                          $category_detail=get_the_terms($event_id, 'tribe_events_cat');
                          $non_primary_category = reset($category_detail);
                          $term_name = $non_primary_category->name;
                          $term_id = $non_primary_category->term_id;
                          $permalink = get_category_link($term_id);
                        }
                    ?>
						<span class="tag"><?php echo $term_name; ?></span>	
					</p>
				<?php endif; ?>
				<div class="<?php echo has_post_thumbnail($event_id) ? "aspect-ratio aspect-ratio--9-5 event-item__image" : "event-item__no-image" ?> " onclick="window.location.href='<?php echo $perma; ?>'">
						<?php if (has_post_thumbnail($event_id)) { ?>
						<img src="<?php echo $featured_img_url; ?>" alt="<?php echo $title ?>" title="<?php echo $title ?>" class="aspect-ratio__element" width="150px" />
						<?php } ?>
				</div>
				<div class="event-item__content">
					<h2 class="h4 event-item__title">
						<a class="event-item-link" href="<?php the_permalink(); ?>"><?php echo $title; ?></a>
					</h2>

					<?php
                      $EventStartDate = $start_date;
                      $EventEndDate = $end_date;
                    ?>

                    <div class="event-item__date">
                    <?php   
                        $comingSoon = get_post_meta( $event_id, 'coming_in_late_year', true );
                            if( $comingSoon == "yes"){
                    ?>
                              <span class="coming-soon">Details coming soon!</span>

                    <?php } else{  ?>
                      <?php
                        if($EventStartDate != $EventEndDate){		
                      ?>
                        <span class="date__month"><?php echo $start_month . " " . $start_date ?><?php echo " - " ?></span>
                      <?php
                      }
                      ?>

                      <span class="date__day"><?php echo $end_month . " " . $end_date ?>, </span>
                      <span class="date__year"><?php echo $show_year ?></span>

                    <?php } ?>
                    </div>

					<?php
					$_EventCity = tribe_get_city($event_id); // get  city

					$event_id_venue = tribe_get_venue_id($event_id);
					if (tribe_get_country($event_id_venue) == __('United States', 'tribe-events-calendar')) {
						$_EventState = tribe_get_state($event_id);
					} else {
						$_EventState = tribe_get_province($event_id);
					}

					$_EventZip = tribe_get_zip($event_id); // get venue zipcode
					$_EventCountry = tribe_get_country($event_id);

					?>


					<p class="location-meta tail">
						<?php echo $venue; ?><br />
						<?php if ($_EventCity) : ?><?php echo $_EventCity; ?><?php endif; ?><?php if ($_EventState) : ?>, <?php echo $_EventState; ?><?php endif; ?><br />
						<?php if ($_EventCountry) : ?><?php echo $_EventCountry; ?><?php endif; ?>
					</p>
				</div>

				<div class="event-item__cta">
					<?php
					$event_url = tribe_get_event_website_url(get_the_ID());
					$register_url = get_post_meta(get_the_ID(), '_ecp_custom_2', 'true');
					?>
					<a href="<?php echo get_the_permalink($event_id); ?>" class="button button--block event-item__link">Learn More<span class="screen-reader-text"> about [title]</span></a>
					<?php if ($register_url) : ?>
						<a href="<?php echo $register_url; ?>" class="button button--block button--outline" target="_blank">Register<span class="screen-reader-text"> to attend [title]</span></a>
					<?php endif; ?>
				</div> <!-- .event-item__cta ends here -->
			</div> <!-- .event-item ends here -->
		<?php endwhile; ?>
		<!-- I think pagiantion div will comes here -->


		<?php 
		// die();
		// echo wp_pagenavi();
		if(wp_is_mobile()){
			print '<button class="loadmore-btn">Load More</button>';
		}else{
			print '<ul id="events-pager"></ul>';
		}
		wp_reset_postdata(); //echo 'total_search='.$events_query->found_posts;

		if (($events_query->found_posts % $posts_per_page) == 0) {
			$total_page_count = floor($events_query->found_posts / $posts_per_page);
		} else {
			$total_page_count = floor($events_query->found_posts / $posts_per_page) + 1;
		}

		?>
		<input type="hidden" id="filter_res_event_pages" name="filter_res_event_pages" value="<?php echo $total_page_count; ?>">
	<?php } else { ?>
		<input type="hidden" id="filter_res_event_pages" name="filter_res_event_pages" value="1">
		<p class="error_msg"><?php echo 'No Events found.'; // if events_query has closed here ?> </p>

<?php
	}
	wp_die();
}

add_action('wp_ajax_nopriv_event_list_ajax', 'event_list_ajax');
add_action('wp_ajax_event_list_ajax', 'event_list_ajax');
?>