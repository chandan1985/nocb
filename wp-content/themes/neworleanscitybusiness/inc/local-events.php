
<?php

//global $wpdb;
$events = array(
  'posts_per_page' => -1,
  'post_status' => 'publish',
  'ignore_sticky_posts' => 0,
  'meta_key' => 'local_event_start_date',
  'orderby' => 'meta_value',
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

//echo $wpdb->last_query;

foreach ($events_postsbyid as $post_id) {
  $id = $post_id->ID;
  $title = get_the_title($id);
  $slug = get_permalink($id);

  $local_event_url = get_post_meta($id, 'local_event_url', true);
  $on_demand_url = get_post_meta($id, 'on_demand_url', true);
  $month_start = get_post_meta($id, 'local_event_start_date', true);
  $start_month = date("M d", strtotime($month_start));
  $month_end = get_post_meta($id, 'local_event_end_date', true);
  $end_month = date("M d, Y", strtotime($month_end));
  if (!empty(get_post_meta($id, 'local_event_end_date', true))) {
    $year_end = get_post_meta($id, 'local_event_end_date', true);
  }
  $end_year = date("Y", strtotime($year_end));
  $content_post = get_post($id);
  $featured_image = get_the_post_thumbnail($id, 'conference-list-thumb');

?>

  <div class="hfd-conference-item  col-sm-4">
    <?php if ($featured_image) { ?>
      <div class='image-section'><a href="<?php print $local_event_url; ?>"><?php print $featured_image; ?></a></div>
    <?php }  ?>
    <div class="hfd-conference__content">
      <h2 class="h4 hfd-conference__title"><?php echo $title; ?></h2>
      <div class="hfd-conference__date">
        <?php if (strtotime($end_month) < strtotime("now") && !empty(get_post_meta($id, 'conferences_end_date', true))) { ?>
          <span class="date_custom_text">Now available <a href="<?php print $on_demand_url ?>">On Demand</a></span>
        <?php } elseif ((strtotime($end_month) == 0) && strtotime($start_month) < strtotime("now")) { ?>
          <span class="date_custom_text">Now available <a href="<?php print $on_demand_url ?>">On Demand</a></span>
        <?php } else { ?>
          <?php if ($month_start && $month_end) { ?>
            <span class="date__month"><?php echo $start_month . " - " . $end_month ?></span>
          <?php } else { ?>
            <span class="date__month"><?php echo $start_month . " , " . $end_year ?></span>
          <?php } ?>
        <?php } ?>
      </div>
    </div>
  </div>

<?php } ?>