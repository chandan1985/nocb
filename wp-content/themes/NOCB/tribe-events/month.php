<?php

/**
 * List View Template
 * The wrapper template for a list of events. This includes the Past Events and Upcoming Events views
 * as well as those same views filtered to a specific category.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/list.php
 *
 * @package TribeEventsCalendar
 * @version 4.6.19
 *
 */

if (!defined('ABSPATH')) {
  die('-1');
}

// data is coming from plugin
$footer_bottom_ad = get_option('footer_bottom_ad');
$mobile_footer_bottom_ad = get_option('mobile_footer_bottom_ad');
$footer_hide_from_pages = [];
$footer_hide_from_pages[] = get_option('footer_hide_from_pages');
$footer_hide_from_post_type = [];
$footer_hide_from_post_type[] = get_option('footer_hide_from_post_type');
global $post;
$current_post_type = get_post_type($post);
?>

<!-- <div class="container">
  <div class="section-title">
    <h2 class="river-heading section-title__heading">HFD Conferences and Events</h2>
    <div class="section-content"><?php //require get_template_directory() . '/inc/hfd-conferences.php'; ?></div>
  </div>
</div> -->

<div class="container">
  <div class="section-title">
    <h2 class="river-heading section-title__heading">Upcoming Events</h2>
  </div>
</div> <!-- .container ends -->

<!-- SECOND RIVER -->

<?php
$selected_event_search_cat = 0;
$event_search_date = "";
$event_search_regions = "";
//$event_search_keyword="";

$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            // print "<pre>";
            // print_r($actual_link);
            // print "</pre>";

//$view_date = tribe_get_month_view_date();

if (strpos($actual_link, 'category') !== false) {
  $actual_link_new = explode('/', $actual_link);

  $tribe_events_cat = get_term_by('slug', $actual_link_new[count($actual_link_new) - 2], 'tribe_events_cat');
  //print_r($tribe_events_cat);

  $selected_event_search_cat = $tribe_events_cat->term_id;
} else {
  if (isset($_REQUEST["event-search-category"])) {
    $selected_event_search_cat = $_REQUEST["event-search-category"];
  }
}

if (isset($_REQUEST["event-search-date"]) && !empty($_REQUEST["event-search-date"])) {
  $event_search_date = $_REQUEST["event-search-date"];
}
if (isset($_REQUEST["event-search-keyword"]) && !empty($_REQUEST["event-search-keyword"])) {
  $event_search_keyword = $_REQUEST["event-search-keyword"];
}
if (isset($_REQUEST["event-search-regions"]) && !empty($_REQUEST["event-search-regions"])) {
  $event_search_regions = $_REQUEST["event-search-regions"];
}
if (isset($_REQUEST["event-search-city"]) && !empty($_REQUEST["event-search-city"])) {
  $event_search_city = $_REQUEST["event-search-city"];
}
if (isset($_REQUEST["event-search-state"]) && !empty($_REQUEST["event-search-state"])) {
  $event_search_state = $_REQUEST["event-search-state"];
}
$event_cat_args = array(
  'show_option_all'    => 'Show all categories',
  'option_none_value'  => '-1',
  'orderby'            => 'ID',
  'order'              => 'ASC',
  'echo'               => 1,
  'selected'           => $selected_event_search_cat,
  'name'               => 'event-search-category',
  'id'                 => 'event-search-category',
  'taxonomy'           => Tribe__Events__Main::TAXONOMY,
  'hide_if_empty'      => true,
  'value_field'       => 'term_id',
);
// print "<pre>"; print_r ($event_cat_args); print "</pre>";
 ?>
<div class="container">
  <div class="pbm-row">
    <div class="pbm-col pbm-col-two-third-md post-river">
      <?php if ($month_year != '') { ?>
        <form class="event-search-form" style="display:none;">
      <?php } else { ?>
        <form class="event-search-form">
      <?php } ?>
          <div class="event-search-form-wrapper">
            <div class="event-search-form__inner">
              <div class="event-search-form__inputs">
                <label for="event-search-date">Events In</label>
                <?php $this_month = mktime(0, 0, 0, date('m'), 1, date('Y')); ?>
                <select name="event-search-date" id="event-search-date">
                  <option value="" <?php echo $event_search_date == "" ? "selected" : "" ?>>Show all dates</option>
                  <?php for ($i = 0; $i < 12; ++$i) {
                    $month_new = date('Y-m', strtotime($i . ' month', $this_month));
                    if ($month_year == $month_new) {
                  ?>
                      <option value="<?php echo $month_new; ?>" selected><?php echo date('M Y', strtotime($i . ' month', $this_month)); ?></option>
                    <?php } else { ?>
                      <option value="<?php echo $month_new; ?>"><?php echo date('M Y', strtotime($i . ' month', $this_month)); ?></option>
                    <?php } ?>

                  <?php } ?>

                </select>
              </div>
              <div class="event-search-form__inputs">
                <label for="event-search-keyword">Search</label>
                <input type="text" name="event-search-keyword" id="event-search-keyword" placeholder="Keyword" value="" />
              </div>
              <div class="event-search-form__inputs">
                <button class="button button--square button--secondary" type="button" onclick="change_events();">Find Events</button>
                <button class="button button--square button--secondary" type="button" onclick="clear_events_filter();">Clear Filter</button>
              </div>
            </div>
            <div class="event-search-form__filters">
              <!--<button class="button button--text">Show Filters</button> -->
              <div id="show_more_block_link">More Filters </div>
              <div id="show_more_block">
                <div class="pbm-row show-expanded-more-event">
                  <div class="event-search-form__inputs pbm-col pbm-col-half">
                    <label for="event-search-category">Category</label>
                    <?php wp_dropdown_categories($event_cat_args); ?>
                  </div>
                  <div class="event-search-form__inputs pbm-col pbm-col-half">
                    <label for="event-search-regions">Regions</label>
                    <?php //if(isset($event_search_regions)):
                    ?>
                    <select name="event-search-regions" id="event-search-regions">
                      <option value="" <?php echo $event_search_regions == "" ? "selected" : "" ?>>Show all regions</option>
                      <option value="United States" <?php echo $event_search_regions == "United States" ? "selected" : "" ?>>United States of America</option>
                      <option value="International" <?php echo $event_search_regions == "International" ? "selected" : "" ?>>International</option>
                    </select>
                    <?php //endif;
                    ?>
                  </div>
                  <div class="event-search-form__inputs pbm-col pbm-col-half">
                    <label for="event-search-city">Search city</label>
                    <input type="text" name="event-search-city" id="event-search-city" placeholder="Search by city..." value="" />
                  </div>
                  <div class="event-search-form__inputs pbm-col pbm-col-half">
                    <label for="event-search-state">State</label>
                    <select name="event-search-state" id="event-search-state">
                      <option value=""><?php esc_html_e('Show all states'); ?></option>
                      <?php
                      foreach (Tribe__View_Helpers::loadStates() as $abbr => $fullname) {
                        $selected = selected((isset($event_search_state) && ($event_search_state === $abbr || $event_search_state === $fullname)), true, false);
                        echo '<option value="' . esc_attr($abbr) . '" ' . $selected . '>' . esc_html($fullname) . '</option>';
                      }
                      ?>
                    </select>

                  </div>
                </div>
              </div>
            </div>
          </div>
          </form> <!-- .event-search-form ends -->

          <!-- I think event search nav should come here -->
          <!-- -->
          <div class="event_loader"> <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/ajax-loader.gif" alt="Loader" title="Loader" width="50"></div>
          <!-- -->
          <!-- needs to add container here -->
          
            <?php
            if(wp_is_mobile()){
              print '<div class="container events_class mobile-view" id="events_bridge_not">';
            }else{
              print '<div class="container events_class" id="events_bridge_not">';
            }

            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $start = ($paged - 1) * 12;

            if(wp_is_mobile()){
              $args = array(
                'posts_per_page' => '-1',
                'paged' => $paged,
                'offset' => $start,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 0,
                'orderby' => 'date',
                'order' => 'ASC',
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
                ),
              );
            }else{
              $args = array(
                'posts_per_page' => '12',
                'paged' => $paged,
                'offset' => $start,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 0,
                'orderby' => 'date',
                'order' => 'ASC',
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
                ),
              );
            }

            if (isset($_REQUEST["event-search-category"]) && !empty($_REQUEST["event-search-category"])) {
              $args['tax_query'] = array(
                array(
                  'taxonomy' => Tribe__Events__Main::TAXONOMY,
                  'field'    => 'term_id',
                  'terms'    => $_REQUEST["event-search-category"],
                ),
              );
            }
            if ((isset($_REQUEST["event-search-state"]) && !empty($_REQUEST["event-search-state"])) ||
              (isset($_REQUEST["event-search-city"]) && !empty($_REQUEST["event-search-city"])) ||
              (isset($_REQUEST["event-search-regions"]) && !empty($_REQUEST["event-search-regions"]))
            ) {
              $meta_query_array = array();
              if (isset($_REQUEST["event-search-city"]) && !empty($_REQUEST["event-search-city"])) {
                $meta_query_array[] = array(
                  'key' => '_VenueCity',
                  'value' => $_REQUEST["event-search-city"],
                  'compare' => 'like',
                );
              }
              if (isset($_REQUEST["event-search-state"]) && !empty($_REQUEST["event-search-state"])) {
                if (!empty($meta_query_array)) {
                  $meta_query_array["relation"] = "and";
                }
                $meta_query_array[] = array(
                  'key' => '_VenueState',
                  'value' => $_REQUEST["event-search-state"],
                  'compare' => '=',
                );
              }
              if (isset($_REQUEST["event-search-regions"]) && !empty($_REQUEST["event-search-regions"])) {
                if (!empty($meta_query_array)) {
                  $meta_query_array["relation"] = "and";
                }

                if ("United States" == $_REQUEST["event-search-regions"]) {
                  $meta_query_array[] = array(
                    'key' => '_VenueCountry',
                    'value' => "United States",
                    'compare' => '=',
                  );
                } elseif ("International" == $_REQUEST["event-search-regions"]) {
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

            if (isset($_REQUEST["event-search-keyword"]) && !empty($_REQUEST["event-search-keyword"])) {
              $args['s'] = $_REQUEST["event-search-keyword"];
            }

            $events_query = new WP_Query($args);

            if ($events_query->have_posts()) { ?>
              <div class="event-search-nav event-search-nav-new"></div>
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
                <div class="event-item col-sm-4">
                <?php } else {?>
                <div class="event-item  col-sm-4">
            <?php } ?>
                  <?php $term_lists = wp_get_post_terms($event_id, Tribe__Events__Main::TAXONOMY);
                  if ($term_lists) :
                  ?>
                    <p class="tag-list lead">
                      <?php
                        foreach ($term_lists as $term_single) :
                          // $wpseo_primary_term = new WPSEO_Primary_Term( 'tribe_events_cat', get_the_id() );
                          // $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
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
                      $EventEndDate = $end_date
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
          </div> <!-- closing of container -->
    </div>

  </div> <!-- .pbm-row ends -->



  <?php
    global $wp;
    $current_url = home_url( add_query_arg( array(), $wp->request ) );
    $current_date = date('Y-m-d');
    $old_evetns = array(
      'posts_per_page' => '-1',
      'paged' => 1,
      'offset' => 0,
      'post_status' => 'publish',
      'ignore_sticky_posts' => 0,
      'post_type' =>
      array(
          'post' => 'tribe_events',
      ),
      'meta_query' => array(
        array(
            'key'     => '_EventEndDate',
            'value'   => $current_date,
            'compare' => '<',
            'type'    => 'DATE',
        ),
      ),
    );
    $all_Oldevents = get_posts($old_evetns);
    if(!empty($all_Oldevents)){
      ?>
        <!-- Evente Footer -->
        <div id="tribe-events-footer">
          <a href="<?php print $current_url ?>/list/?tribe_event_display=past&amp;tribe_paged=1" rel="prev" data-uw-rm-brl="false"><span>«</span> Previous Events</a>
        </div>
        <!-- End event footer -->
      <?php
    }
  ?>

<?php if(!in_array($current_post_type, $footer_hide_from_post_type) && !in_array(get_the_ID(), $footer_hide_from_pages)){ ?>
  <div class="row">
    <div class="col-sm-9">
      <div class='footer-bottom-ad-section'>
      <?php
        if(wp_is_mobile()){
          print do_shortcode($mobile_footer_bottom_ad); 
        }else{
          print do_shortcode($footer_bottom_ad); 
        } 
      ?>
    </div>
  </div>
    <div class="col-sm-3"></div>
  </div>

<?php } ?>

</div> <!-- .container ends -->



<!--Local Events-->
<?php

$events = array(
  //'paged' => 1,
  'posts_per_page' => -1,
  //'offset' => 0,
  'post_status' => 'publish',
  'ignore_sticky_posts' => 0,
  'orderby' => 'local_event_start_date',
  'order' => 'ASC',
  'post_type' => 'local_events',
   'meta_query' => array(
        array(
            'key'     => 'local_event_end_date',
            'value'   => date("Y-m-d"),
            'compare' => '>=', 
            'type'    => 'DATE',
        ),
      )
);


$events_postsbyid = get_posts($events);
if(!empty($events_postsbyid))
{
?>

 <div class="container">
  <div class="section-title">
    <h2 class="river-heading section-title__heading">Local Events</h2>
    <div class="section-content"><?php require_once 'local-events.php'; ?></div>
  </div>
</div>
<?php
}
?>


<script>
  jQuery(document).ready(function() {
    jQuery("#show_more_block_link").click(function() {
      jQuery("#show_more_block").toggle();
    });

  });

  if (window.location.href.indexOf("category") > -1) {
    jQuery("#show_more_block").show();
    change_events();
    jQuery("a.next_events").hide();
  }

  var month_date_event = '<?php echo $month_year; ?>';

  if (month_date_event != '') {
    jQuery("#show_more_block").show();
    change_events();
    jQuery("a.next_events").hide();
  }

  /* Next event function ends */

  function change_events() {

    var month_date_eve = '<?php echo $month_year; ?>';

    if (month_date_eve != '') {
      var first_river_args = '<?php echo $first_river_posts; ?>';
      //console.log(first_river_args);
      if (first_river_args <= 0) {
        var month_date = 'event-date';
      }
      var month_date_new = '<?php echo $month_year; ?>';
    }


    var category = jQuery("#event-search-category").val();
    var regions = jQuery("#event-search-regions").val();
    var state = jQuery("#event-search-state").val();
    var event_date = jQuery("#event-search-date").val();
    var event_keyword = jQuery("#event-search-keyword").val();
    var event_city = jQuery("#event-search-city").val();


    var data = {
      action: 'event_list_ajax',
      category: category,
      regions: regions,
      state: state,
      event_date: event_date,
      event_keyword: event_keyword,
      event_city: event_city,
      month_date: month_date,
      month_date_new: month_date_new

    };

    var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
    jQuery.ajax({
      url: ajaxurl,
      datatype: "json",
      type: 'post',
      data: data,
      success: function(response) {

        // total count need to collect from ajx basaed on condirion
        // bases on that dats set below hidden parameters for pagination.
        //console.log(response);
        jQuery("#events_bridge_not").scrollTop(300);
        jQuery("#events_bridge_not").html(response);

        var filter_res_event_pages = jQuery("#filter_res_event_pages").val();
        //var filter_res_event_pages =(filter_res_event_cnt/6)+1;

        jQuery(".total_page").val(filter_res_event_pages);
        jQuery(".event_current_page").val(1);

        // pagination 
        var current_page = jQuery(".event_current_page").val();

        //console.log(current_page);
        //current_page = parseInt(current_page) + 1;

        // hiding next when reached to end 

        var total_page = jQuery(".total_page").val();

        if (current_page == total_page) {

          jQuery("a.next_events").hide();

        } else if (current_page < total_page) {

          jQuery("a.next_events").show();
        }


        // hiding next  when reached  to end 

        if (current_page > 1) {

          jQuery("a.previous_events").show();
        } else {
          jQuery("a.previous_events").hide();
        }

        // pagination 



      },
    });

  }

  function clear_events_filter() {

    jQuery("select#event-search-date").find('option:eq(0)').prop('selected', true);
    jQuery("select#event-search-category").find('option:eq(0)').prop('selected', true);
    jQuery("select#event-search-regions").find('option:eq(0)').prop('selected', true);
    jQuery("select#event-search-state").find('option:eq(0)').prop('selected', true);
    jQuery("input#event-search-keyword").val("");
    jQuery("input#event-search-city").val("");

    change_events();

  }
</script>
