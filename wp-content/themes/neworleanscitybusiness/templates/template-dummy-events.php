<?php
/**
 * Template Name: DUMMY - Events
 *
 * @package ThemeScaffold
 */

get_header(); ?>

	<header class="container page-header">
		<h1 class="h1 static-header">Events</h1>
	</header>

	<div class="container">
		<div class="section-title">
			<h2 class="river-heading section-title__heading">Progressive Business Media Conferences</h2>
		</div>
	</div>

	<!-- FIRST RIVER -->
	<div class="container">
		<div class="pbm-row">
			<div class="pbm-col pbm-col-two-third-md post-river">
				
				<!-- FIRST RIVER Dynamic data from DB ot backend  start -->
				
				<?php  
				
				$args_first_river = array(
				          'post_type' => 'tribe_events',
						  'posts_per_page' => 5,
						  'post_status'=>'publish',
						  'orderby'=>'post_date',
						  'order'=>'DESC',
						  'eventDisplay'=>'custom',
						  'meta_query' => array(
						    array(
							 'key' => 'bridge_tower_media_conferences',
							 'value' => '1',
							 'compare' => '=' // Meta query to filter events which are not bridge Tower Media
							),
						  )
				);

				// Custom query.
				$query_first_river = new WP_Query( $args_first_river );

				if( $query_first_river->have_posts()){

					// Start looping over the query results.
					while($query_first_river->have_posts() ) {
                                            $event_cats="";
						$query_first_river->the_post();
						$event_id = get_the_ID();	
						
						$event_arr = tribe_get_event_meta($event_id);  //echo "<pre>";  echo "<pre>"; print_r($event_arr);
 						
						//echo $event_arr['_EventEndDate'][0];
						
					    if(count($event_arr)>0){
							$_EventStartDate = explode(" ",$event_arr['_EventStartDate'][0]);
							$_EventEndDate   = explode(" ",$event_arr['_EventEndDate'][0]);
						}
						$featured_img_url = get_the_post_thumbnail_url($event_id);
                                                $perma = get_the_permalink($event_id);
                                $title = get_the_title($event_id);
                                $venue = tribe_get_venue($event_id);
                                 $term_list  =   wp_get_post_terms( $event_id, Tribe__Events__Main::TAXONOMY );
                    		foreach( $term_list as $term_single ) {
                                    $single_term_id=$single_term_link=$event_cats="";
                                    $single_term_id=$term_single->term_id;
                                    $single_term_link=get_term_link($single_term_id,Tribe__Events__Main::TAXONOMY);
                    			$event_cats .= '<a href="#" class="tag">'.$term_single->name . '</a>';
                    		}
						$event_cat = tribe_get_event_categories($event_id );  //echo "<br>"; print_r($event_cat);
										
					?>
					
					<div class="event-item">
						<div class="<?php echo has_post_thumbnail($event_id)?"aspect-ratio aspect-ratio--9-5 event-item__image":"event-item__no-image"?> " onclick="window.location.href='<?php echo $perma;?>'">
							
							<?php if (has_post_thumbnail($event_id)) { ?>
                                        <img src="<?php echo $featured_img_url; ?>" alt="" class="aspect-ratio__element" />
                                <?php } ?><?php if (has_post_thumbnail($event_id)) { ?>
                                       <img src="<?php echo $featured_img_url; ?>" alt="" class="aspect-ratio__element" />
                                <?php } ?>
							<div class="event-item__date">
								<span class="date__month">
								<?php	
								if(count($event_arr['_EventEndDate'])>0){
									echo date("M", strtotime($_EventEndDate[0]));
						        }
							    ?>
								</span>
								<span class="date__day">
								
								<?php	
								if(count($event_arr['_EventStartDate'])>0){
									echo date("d", strtotime($_EventStartDate[0]));
						        }
							    ?>
								-							
						        <?php	
								if(count($event_arr['_EventEndDate'])>0){
									echo date("d", strtotime($_EventEndDate[0]));
						        }
							    ?>
								
								</span>
								<span class="date__year">
				                <?php	
              					if(count($event_arr['_EventStartDate'])>0){
							     echo date("Y", strtotime($_EventStartDate[0]));
						         }
							    ?>
							   </span> 
							</div>
						</div>

						<div class="event-item__content">
							<p class="tag-list lead">
								<?php echo $event_cats?>
							</p>
							<h2 class="h4 event-item__title">
								 <a class="event-item-link" href="<?php the_permalink();?>"><?php echo get_the_title(); ?></a>
							</h2>
							<?php 
							   $_EventCity = tribe_get_city($event_id); // get  city
							   $_EventState = tribe_get_province($event_id); // get venue state
							   $_EventZip = tribe_get_zip($event_id); // get venue zipcode
							   $_Eventmaplink = esc_url( tribe_get_map_link( $event_id ) ); // get google map link
							?>
							<p class="location-meta tail"><?php echo $venue?>
							<?php if($_EventCity):?>
							   <?php echo $_EventCity;?>
							 <?php endif;?>
							  <?php if($_EventState):?>
							   <?php echo $_EventState;?>
							 <?php endif;?>	
							 <?php if($_EventZip):?>
							   <?php echo $_EventZip;?>
							 <?php endif;?></p>
							
						</div>

						<div class="event-item__cta">
						  <?php 
						     $event_url = tribe_get_event_website_url(get_the_ID());
				             $register_url = get_post_meta(get_the_ID(),'_ecp_custom_2','true');
						  ?>
						  <?php if($event_url):?>
							<a href="<?php echo $event_url;?>" class="button button--block event-item__link" target="_blank">Learn More<span class="screen-reader-text"> about [title]</span></a>
					      <?php endif;?>
                          <?php if($register_url):?>						  
							<a href="<?php echo $register_url;?>" class="button button--block button--outline" target="_blank">Register<span class="screen-reader-text"> to attend [title]</span></a>
						  <?php endif;?>	
						</div>
					</div>
					
					<?php
					
					}

				}

				// Restore original post data.
				wp_reset_postdata();
				
							
				?>
							
				<!-- FIRST RIVER Dynamic data from DB ot backend end  -->
				
			</div>

			<div class="pbm-col pbm-col-third-md sidebar">
				<div class="pbm-sticky">
					<div class="advertisement advertisement--flush">
                        <?php echo do_shortcode('[dfp_ads id=95121]');?>
						<!--<img src="<?php echo site_url();?>/wp-content/uploads/2019/01/img4.jpg" alt="" />-->
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="container advertisement advertisement--borders">
            <?php echo do_shortcode('[dfp_ads id=95122]');?>
		<!--<img src="<?php echo site_url();?>/wp-content/uploads/2019/01/img5.jpg" alt="" />-->
	</div>

	<div class="container">
		<div class="section-title">
			<h2 class="river-heading section-title__heading">All Industry Events</h2>
		</div>
	</div>

	<!-- SECOND RIVER -->
        
       <?php 
       $selected_event_search_cat=0;
       $event_search_date="";
       if(isset($_REQUEST["event-search-category"])&&!empty($_REQUEST["event-search-category"])){
           $selected_event_search_cat=$_REQUEST["event-search-category"];
       }
       if (isset($_REQUEST["event-search-date"]) && !empty($_REQUEST["event-search-date"])) {
           $event_search_date=$_REQUEST["event-search-date"];
       }
       if (isset($_REQUEST["event-search-keyword"]) && !empty($_REQUEST["event-search-keyword"])) {
           $event_search_keyword=$_REQUEST["event-search-keyword"];
       }
       if (isset($_REQUEST["event-search-regions"]) && !empty($_REQUEST["event-search-regions"])) {
           $event_search_regions=$_REQUEST["event-search-regions"];
       }
       if (isset($_REQUEST["event-search-city"]) && !empty($_REQUEST["event-search-city"])) {
           $event_search_city=$_REQUEST["event-search-city"];
       }
        if (isset($_REQUEST["event-search-state"]) && !empty($_REQUEST["event-search-state"])) {
           $event_search_state=$_REQUEST["event-search-state"];
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
	'value_field'	     => 'term_id',
);?>
	<div class="container">
		<div class="pbm-row">
                    <div class="pbm-col pbm-col-two-third-md post-river">
                        <form action="" class="event-search-form">
                            <div class="event-search-form__inner">
                                <div class="event-search-form__inputs">
                                    <label for="event-search-date">Events In</label>
									<?php $this_month = mktime(0, 0, 0, date('m'), 1, date('Y'));?>
									<select name="event-search-date" id="event-search-date">
                                        <option value="" <?php echo $event_search_date==""?"selected":""?>>Show all dates</option>
										<?php for ($i = 0; $i < 12; ++$i) { ?>
                                          <option value="<?php echo date('Y-m-d', strtotime($i.' month', $this_month));?>"><?php echo date('M Y', strtotime($i.' month', $this_month));?></option>
										<?php } ?>  
                                    </select>
                                </div>
                                <div class="event-search-form__inputs">
                                    <label for="event-search-keyword">Search</label>
                                    <input type="text" name="event-search-keyword" id="event-search-keyword" placeholder="Keyword" value="<?php echo $event_search_keyword;?>"/>
                                </div>

                                <div class="event-search-form__inputs">
                                    <button class="button button--square button--secondary" type="button" onclick="change_events();">Find Events</button>
									<button class="button button--square button--secondary" type="button" onclick="clear_events_filter();">Clear Filter</button>
                                </div>
                            </div>
                            <div class="event-search-form__filters">
                                  <!--<button class="button button--text">Show Filters</button> -->
								 <div  id="show_more_block_link" >Show Filter </div>
								 <div  id="show_more_block">
								  <div class="pbm-row">
								 <div class="event-search-form__inputs pbm-col pbm-col-half">
                                    <label for="event-search-category">Category</label>
                                    <?php  wp_dropdown_categories( $event_cat_args );?>
                                </div>
                                <div class="event-search-form__inputs pbm-col pbm-col-half">
                                    <label for="event-search-regions">Regions</label>
                                    <select name="event-search-regions" id="event-search-regions">
                                        <option value="" <?php echo $event_search_regions==""?"selected":""?>>Show all regions</option>
                                        <option value="United States" <?php echo $event_search_regions=="United States"?"selected":""?>>United States of America</option>
                                        <option value="International" <?php echo $event_search_regions=="International"?"selected":""?>>International</option>
                                    </select>
                                </div>
                                <div class="event-search-form__inputs pbm-col pbm-col-half">
                                    <label for="event-search-city">Search city</label>
                                    <input type="text" name="event-search-city" id="event-search-city" placeholder="Search by city..." value="<?php echo $event_search_city;?>"/>
                                </div>
                                <div class="event-search-form__inputs pbm-col pbm-col-half">
                                    <label for="event-search-state">State</label>
                                    <select name="event-search-state" id="event-search-state">
                                        <option value=""><?php esc_html_e( 'Show all states' ); ?></option>
                                        <?php
                                        foreach (Tribe__View_Helpers::loadStates() as $abbr => $fullname) {
                                            $selected = selected(( isset($event_search_state) && ( $event_search_state === $abbr || $event_search_state === $fullname )), true, false);
                                            echo '<option value="' . esc_attr($abbr) . '" ' . $selected . '>' . esc_html($fullname) . '</option>';
                                        }
                                        ?>
                                    </select>
                                   
                                </div>
								
								     
								 </div>
								 </div>
                            </div>
                        </form>
                       <!-- I think event search nav should come here -->
					   
					   <!-- needs to add container here -->
					   <div class="container events_class" id="events_bridge_not">
					    <?php
                        $tribe_paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
						
                       
						$args = array(
				          'post_type' => 'tribe_events',
						  'posts_per_page' => 6,
						  'post_status'=>'publish',
						  'orderby'=>'post_date',
						  'order'=>'DESC',
						  'eventDisplay'=>'custom',
						  'paged' => $tribe_paged,
						  'meta_query' => array(
						    array(
							 'key' => 'bridge_tower_media_conferences',
							 'compare' => 'NOT EXISTS' // Meta query to filter events which are not bridge Tower Media
							),
						  )
						
				         );
				
                        if(isset($_REQUEST["event-search-category"])&&!empty($_REQUEST["event-search-category"])){
                            $args['tax_query'] = array(
                              array(
                                'taxonomy' => Tribe__Events__Main::TAXONOMY,
                                'field'    => 'term_id',
                                'terms'    => $_REQUEST["event-search-category"],
                              ),
                            );
                         }
                        if (isset($_REQUEST["event-search-date"]) && !empty($_REQUEST["event-search-date"])) {
                            $date = new DateTime($_REQUEST["event-search-date"]);
                            $args['start_date'] = $_REQUEST["event-search-date"];
                            $args['meta_query'] = array(
                              'relation'=>"AND",
                              array(
                                'key' => '_EventStartDate',
                                'value' => $_REQUEST["event-search-date"] . ' 00:00:00',
                                'compare' => '<=',
                                'type' => 'DATETIME'
                              ),
                              array(
                                'key' => '_EventEndDate',
                                'value' => $_REQUEST["event-search-date"] . ' 00:00:00',
                                'compare' => '>=',
                                'type' => 'DATETIME'
                              ),
                            );
                        }
                        if ((isset($_REQUEST["event-search-state"]) && !empty($_REQUEST["event-search-state"]))||
                          (isset($_REQUEST["event-search-city"]) && !empty($_REQUEST["event-search-city"]))||
                          (isset($_REQUEST["event-search-regions"]) && !empty($_REQUEST["event-search-regions"]))) {
                           $meta_query_array=array();
                            if (isset($_REQUEST["event-search-city"]) && !empty($_REQUEST["event-search-city"])){
                               $meta_query_array[]=array('key' => '_VenueCity',  
                                'value' => $_REQUEST["event-search-city"],
                                'compare' => 'like',);
                           }
                           if (isset($_REQUEST["event-search-state"]) && !empty($_REQUEST["event-search-state"])){
                               if(!empty($meta_query_array)){
                                   $meta_query_array["relation"]="and";
                               }
                               $meta_query_array[]=array('key' => '_VenueState',  
                                'value' => $_REQUEST["event-search-state"],
                                'compare' => '=',);
                           }
                           if (isset($_REQUEST["event-search-regions"]) && !empty($_REQUEST["event-search-regions"])){
                               if(!empty($meta_query_array)){
                                   $meta_query_array["relation"]="and";
                               }
                              
                               if("United States"==$_REQUEST["event-search-regions"]){
                                    $meta_query_array[]=array('key' => '_VenueCountry',  
                                'value' => "United States",
                                'compare' => '=',);
                               }
                               elseif("International"==$_REQUEST["event-search-regions"]){
                                    $meta_query_array[]=array('key' => '_VenueCountry',  
                                'value' => "United States",
                                'compare' => '!=',);
                               }
                           }
                            $city_query_args = array(
                          'post_type' => 'tribe_venue',
                          'posts_per_page' => -1,
                          'meta_query'=>$meta_query_array,'fields' => 'ids');
                           
                            $events_from_search= get_posts($city_query_args);
                             if(empty($events_from_search)){
                                $events_from_search=array(0); 
                             }
                                if(!empty($args['meta_query'])){
                                    $args['meta_query']["relation"]='AND';
                                }
                                $args['meta_query'][]=array(
									'key'     => '_EventVenueID',
									'value'   =>  $events_from_search ,
									'compare' => 'IN',
								);
                          }
//                        echo "<pre>";
//                        var_dump($args);die;
                        if (isset($_REQUEST["event-search-keyword"]) && !empty($_REQUEST["event-search-keyword"])) {
                            $args['s'] = $_REQUEST["event-search-keyword"];
                        }
                        //var_dump($args);die;
                        $events_query = new WP_Query($args);
						
                        if ($events_query->have_posts()) { ?>
						 <div class="event-search-nav event-search-nav-new">
					      
                         </div>
                      
                        <?php
                            while ($events_query->have_posts()) {
                                $events_query->the_post();
                                $end_date = $start_date = $show_date = $event_cats = $show_month = $start_month = $end_month = $featured_img_url = "";
                                $event_id = get_the_ID();
                                $event_cats = get_the_term_list($event_id, Tribe__Events__Main::TAXONOMY);
                                $term_list  =   wp_get_post_terms( $event_id, Tribe__Events__Main::TAXONOMY );
                    		foreach( $term_list as $term_single ) {
                                    $single_term_id=$single_term_link=$event_cats="";
                                    $single_term_id=$term_single->term_id;
                                    $single_term_link=get_term_link($single_term_id,Tribe__Events__Main::TAXONOMY);
                    			$event_cats .= '<a href="#" class="tag">'.$term_single->name . '</a>';
                    		}
                                $start_month = tribe_get_start_date($event_id, false, 'M');
                                $start_date = tribe_get_start_date($event_id, false, 'd');
                                $end_month = tribe_get_end_date($event_id, false, 'M');
                                $end_date = tribe_get_end_date($event_id, false, 'd');
                                $end_year = tribe_get_end_date($event_id, false, 'Y');
                                if ($start_month == $end_month) {
                                    $show_month = $start_month;
                                }
                                else {
                                    $show_month = $start_month . " - " . $end_month;
                                }
                                if ($start_date == $end_date) {
                                    $show_date = $start_date;
                                }
                                else {
                                    $show_date = $start_date . " - " . $end_date;
                                }
                                $featured_img_url = get_the_post_thumbnail_url($event_id);
                                $perma = get_the_permalink($event_id);
                                $title = get_the_title($event_id);
                                $venue = tribe_get_venue($event_id);
								
								//$start_date_new = get_post_meta($event_id,'_EventStartDate',true);
								
								//echo 'Start date '. $start_date_new;
								
								//$end_date_new = get_post_meta($event_id,'_EventEndDate',true);
								//echo 'end date '. $end_date_new;
                                ?>

                                <div class="event-item">
                                    <div class="<?php echo has_post_thumbnail($event_id)?"aspect-ratio aspect-ratio--9-5 event-item__image":"event-item__no-image"?> " onclick="window.location.href='<?php echo $perma;?>'">
                                       <?php if (has_post_thumbnail($event_id)) { ?>
                                          <img src="<?php echo $featured_img_url; ?>" alt="" class="aspect-ratio__element" />
										<?php } ?><?php if (has_post_thumbnail($event_id)) { ?>
										  <img src="<?php echo $featured_img_url; ?>" alt="" class="aspect-ratio__element" />
										<?php } ?>
                                        <div class="event-item__date">
                                            <span class="date__month"><?php echo $show_month ?></span>
                                            <span class="date__day"><?php echo $show_date ?></span>
                                            <span class="date__year"><?php echo $end_year ?></span>
                                        </div>
                                    </div>

                                    <div class="event-item__content">
                                        <p class="tag-list lead">
                                            <?php echo $event_cats ?>
                                        </p>
                                        <h2 class="h4 event-item__title">
                                            <a class="event-item-link" href="<?php the_permalink();?>"><?php echo $title;?></a>
                                        </h2>
                                        <p class="location-meta tail"><?php echo $venue ?></p>
                                    </div>

                                    <div class="event-item__cta">
										 <?php 
											 $event_url = tribe_get_event_website_url(get_the_ID());
											 $register_url = get_post_meta(get_the_ID(),'_ecp_custom_2','true');
										 ?>
										 <?php if($event_url):?>
											<a href="<?php echo $event_url;?>" class="button button--block event-item__link" target="_blank">Learn More<span class="screen-reader-text"> about [title]</span></a>
										 <?php endif;?>
										 <?php if($register_url):?>						  
											<a href="<?php echo $register_url;?>" class="button button--block button--outline" target="_blank">Register<span class="screen-reader-text"> to attend [title]</span></a>
										 <?php endif;?>	
								    </div> <!-- .event-item__cta ends here -->
                                </div> <!-- .event-item ends here -->
							  <?php } // ends while statement ?>
							 
							
                        <?php wp_reset_postdata();
                    } // Ends if statement for Wp_Query
                    ?>
					</div> <!-- closing of container -->
					
					<!-- needs to close container here -->
					 <div class="event-search-nav">
					    <?php $theme_image_url = get_stylesheet_directory_uri();?>
                        
						  <a class="previous_events" id="prev_events" onclick="prev_events();"><?php echo '<svg class="pbm-icon chevron-left left" aria-hidden="true">
						    <use xlink:href="'.$theme_image_url.'/dist/svg/sprite.symbol.svg#chevron-left">
							</use>
						    </svg> Previous Events';?>
						  </a>
								
						<a class="next_events" id="nxt_events" onclick="next_events();"><?php echo 'Next Events <svg class="pbm-icon chevron-right right" aria-hidden="true">
									<use xlink:href="'.$theme_image_url.'/dist/svg/sprite.symbol.svg#chevron-right">
							</use>
						</svg>';?>
						</a>
								
								<?php $total_page_count = $events_query->post_count;?>
								<input type="hidden" name="event_perpage" class="event_perpage" value="2"/>
								<input type="hidden" name="event_current_page" class="event_current_page" value="<?php echo $tribe_paged;?>"/>
								<input type="hidden" name="total_page" class="total_page" value="<?php echo $total_page_count;?>"/>
                         </div>
                    </div>

			<div class="pbm-col pbm-col-third-md sidebar">
				<div class="pbm-sticky">
					<div class="advertisement advertisement--flush">
                        <?php echo do_shortcode('[dfp_ads id=46382]');?>
						<!--<img src="<?php echo site_url();?>/wp-content/uploads/2019/01/img3.jpg" alt="" />-->
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<style type="text/css">
	 .event-search-nav a.previous_events{display:none;}
	</style>
	<script>

	 jQuery( document ).ready(function() {
		 //jQuery("#event-search-keyword").val("test");
		 jQuery("#show_more_block_link").click(function() {
			//jQuery("#show_more_block").css("display", "block");
			jQuery("#show_more_block").toggle();
		 });
	 });
	 
	 /* Previous event function */
	 function prev_events(){
		 var current_page = jQuery(".event_current_page").val();
		  //console.log(current_page);
		  current_page = parseInt(current_page) - 1;
		  
		   if(current_page <= 1){
			jQuery("a.previous_events").show();
		  }
		  else {
			jQuery("a.previous_events").hide();  
		  }
		  
		  //console.log(current_page);
		  
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
			event_date : event_date,
			event_keyword : event_keyword,
			event_city : event_city,
			event_perpage : current_page
			
		 };
		 
		 var ajaxurl = "<?php echo admin_url('admin-ajax.php');?>";
		 jQuery.ajax({
			 url : ajaxurl,
			 datatype: "json",
			 type: 'post',
			 data: data,
			 success: function(response) {
				 
				 // total count need to collect from ajx basaed on condirion
				 // bases on that dats set below hidden parameters for pagination.
				 
				jQuery("#events_bridge_not").html(response);     
			  },
		 });
		 
		 jQuery(".event_current_page").val(current_page);
		 
		 if(jQuery(".event_current_page").val() == 1){
			 jQuery("a.previous_events").hide();
		 }
		 else {
			 jQuery("a.previous_events").show();
		 }
		 
	 }
	 
	 /* Previous event function ends */
	 
	 /* Next event function starts */
	 
	 function next_events(){
		 
		  var current_page = jQuery(".event_current_page").val();
		  
		  //console.log(current_page);
		  current_page = parseInt(current_page) + 1;
		  
		  
		  //console.log(current_page);
		  
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
			event_date : event_date,
			event_keyword : event_keyword,
			event_city : event_city,
			event_perpage : current_page
			
		 };
		 
		 var ajaxurl = "<?php echo admin_url('admin-ajax.php');?>";
		 jQuery.ajax({
			 url : ajaxurl,
			 datatype: "json",
			 type: 'post',
			 data: data,
			 success: function(response) {
				 
				 // total count need to collect from ajx basaed on condirion
				 // bases on that dats set below hidden parameters for pagination.
				 
				 //var result = jQuery.parseJSON(response);
				
				 //console.log(result);
				 //console.log(response);
				
				/* if(result.event_count <= 1){
					jQuery("a.previous_events").show();
				} */
				
				 
				jQuery("#events_bridge_not").html(response);     
			  },
		 });
		 
		 jQuery(".event_current_page").val(current_page);
		 
		 //console.log(jQuery(".event_current_page").val());
		 
		 if(jQuery(".event_current_page").val() >= 1){
			 jQuery("a.previous_events").show();
		 }
		 
		 
	 }
	 
	 /* Next event function ends */
	 
	 function change_events(){
		 
		 
		 
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
			event_date : event_date,
			event_keyword : event_keyword,
			event_city : event_city
			
		 };
		 
		 var ajaxurl = "<?php echo admin_url('admin-ajax.php');?>";
		 jQuery.ajax({
			 url : ajaxurl,
			 datatype: "json",
			 type: 'post',
			 data: data,
			 success: function(response) {
				 
				 // total count need to collect from ajx basaed on condirion
				 // bases on that dats set below hidden parameters for pagination.
				 //console.log(response);
				 
				jQuery("#events_bridge_not").html(response);     
				
			 },
		 });
		 
	 }
	 
	 function clear_events_filter(){
		 
		 jQuery("select#event-search-date").find('option:eq(0)').prop('selected',true);
		 jQuery("select#event-search-category").find('option:eq(0)').prop('selected',true);
		 jQuery("select#event-search-regions").find('option:eq(0)').prop('selected',true);
		 jQuery("select#event-search-state").find('option:eq(0)').prop('selected',true);
		 jQuery("input#event-search-keyword").val("");
		 jQuery("input#event-search-city").val("");
			
			
	 }
	 
	</script>

<?php
get_footer();
