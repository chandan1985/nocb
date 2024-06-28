<?php

/* Template Name: Webinars */

get_header();

?>



<div class="breadcrumb-section container">
    <a href="/">Home</a><span>></span>Webinars<span>
</div>
<div class="heading-section container-fluid">
    <div class="container">
        <h1 class="page-heading">Webinars</h1>
    </div>
</div>
<div class="webinar-listing-page container">
    <div class="container">
    <div class="section-title">
        <h2 class="river-heading section-title__heading">Upcoming Webinars</h2>
    </div>
    </div>
    <!-- Start row -->
    <div class="row">

        <!-- Start col-sm-9 -->
        <div class="col-sm-9">
            <?php
            if(wp_is_mobile()){
                print '<div class="container events_class mobile-view" id="events_bridge_not">';
              }else{
                print '<div class="container events_class" id="events_bridge_not">';
              }
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $start = ($paged - 1) * 6;
            $args = array(
                'posts_per_page' => -1,
                'paged' => $paged,
                'offset' => $start,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 0,
                'post_type' =>
                array(
                    'post' => 'tribe_events',
                ),
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'bridge_tower_media_conferences',
                        'compare' => 'NOT EXISTS' // Meta query to filter events which are not bridge Tower Media
                    ),
                    array(
                        'key' => '_EventStartDate', // Check the start date field
                        'value' => date("Y-m-d"), // Set today's date (note the similar format)
                        'compare' => '>=', // Return the ones greater than today's date
                        'type' => 'DATE' // Let WordPress know we're working with date
                    ),
                    '_EventStartDate__order_by' => array(
                        'key' => '_EventStartDate',
                        'type' => 'DATE',
                        'compare' => '>=',
                    )
                ),
                'orderby' => array('_EventStartDate__order_by' => 'ASC'),
                'tax_query' => array(
                    'taxonomy_category' => array(
                        'taxonomy' => 'tribe_events_cat',
                        'field' => 'name',
                        'terms' =>
                        array(
                            0 => 'webinars',
                        ),
                        'operator' => 'IN',
                        'include_children' => false,
                    ),
                ),
            );
            $events_query = new WP_Query($args);
            if ($events_query->have_posts()) { ?>
              <div class="event-search-nav event-search-nav-new">

              </div>

              <?php
              while ($events_query->have_posts()) {
                $events_query->the_post();
                $end_date = $start_date = $show_date = $event_cats = $show_month = $start_month = $end_month = $featured_img_url = "";
                $event_id = get_the_ID();
                //                                $event_cats = get_the_term_list($event_id, Tribe__Events__Main::TAXONOMY);
                $term_list  =   wp_get_post_terms($event_id, Tribe__Events__Main::TAXONOMY);
                $event_cats = "";
                foreach ($term_list as $term_single) {
                  $single_term_id = $single_term_link = "";
                  $single_term_id = $term_single->term_id;
                  $single_term_link = get_term_link($single_term_id, Tribe__Events__Main::TAXONOMY);
                  $event_cats .= '<a href="javascript:void(0)" class="tag">' . $term_single->name . '</a>';
                }
                $start_month = tribe_get_start_date($event_id, false, 'M');
                $start_date = tribe_get_start_date($event_id, false, 'd');
                $start_year = tribe_get_start_date($event_id, false, 'Y');

                $end_month = tribe_get_end_date($event_id, false, 'M');
                $end_date = tribe_get_end_date($event_id, false, 'd');
                $end_year = tribe_get_end_date($event_id, false, 'Y');

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

            <?php if(wp_is_mobile()){ ?>
                <div class="event-item col-sm-4" style="display:none">
                <?php } else {?>
                <div class="event-item  col-sm-4">
            <?php } ?>
                  <?php $term_lists = wp_get_post_terms($event_id, Tribe__Events__Main::TAXONOMY);
                  if ($term_lists) :
                  ?>
                    <p class="tag-list lead">
                      <?php
                      //echo '<pre>';print_r($term_list);echo '</pre>';
                      foreach ($term_lists as $term_single) :
                      ?>
                        <?php
                       
						// $single_term_id = $term_single->term_id;
                        // $single_term_link = get_term_link($single_term_id, 'tribe_events_cat'); 
                        // $wpseo_primary_term = new WPSEO_Primary_Term( 'tribe_events_cat', get_the_id() );
                        // $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
                        $wpseo_primary_term = get_primary_category_id(get_the_id());
                        $term = get_term( $wpseo_primary_term );
                        $term_name = $term->name;
                        $term_id = $term->term_id;
                        $permalink = get_category_link($term);
                        // print $permalink;
                        // print $term_id;
						?>
                      <?php endforeach; ?>
						<a href="<?php echo $permalink; ?>" class="tag"><?php echo $term_name; ?></a>
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

                    <div class="event-item__date">
                      <span class="date__month"><?php echo $start_month . " " . $start_date ?><?php echo " - " ?></span>
                      <span class="date__day"><?php echo $end_month . " " . $end_date ?></span>
                      <span class="date__year"><?php echo $show_year ?></span>
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
              <?php } // ends while statement
              if(wp_is_mobile()){
                print '<button class="loadmore-btn">Load More</button>';
              }else{
                echo "<div class='pagination'>".wp_pagenavi( array( 'query' => $events_query ) )."</div>";
              }
              ?>
            <?php wp_reset_postdata();
            } // Ends if statement for Wp_Query
            else {
              echo 'No Events found.';
            }
            ?>
            </div>
        </div>
        <!-- End col-sm-9 -->
        <!-- Start col-sm-3 -->
        <div class="col-sm-3">
            <?php dynamic_sidebar('event-right-sidebar'); ?>
        </div>
        <!-- End col-sm-3 -->

    </div>
    <!-- End row -->
</div>

<?php get_footer(); ?>