<?php
$wpsolr_fq = isset($_GET['wpsolr_fq']) ? $_GET['wpsolr_fq'] : array();

$cat = '';
$author = '';
$headline = '';
$range = '';
$min_range = '';
$max_range = '';
if(!empty($wpsolr_fq) && is_array($wpsolr_fq)) {
   foreach ($wpsolr_fq as $vv) {
      if(!empty($vv) && strpos($vv, 'categories:') > -1) {
         $cat = $vv;
      } else if(!empty($vv) && strpos($vv, 'title:') > -1) {
         $headline = $vv;
      } else if(!empty($vv) && strpos($vv, 'author:') > -1) {
         $author = $vv;
      } else if(!empty($vv) && strpos($vv, 'displaydate_dt:') > -1) {
         $range = $vv;
         $temp = explode('displaydate_dt:', $vv);
         $temp = isset($temp[1]) ? explode('-', $temp[1]) : array();
         $min_range = isset($temp[0]) ? $temp[0] : '';
         $max_range = isset($temp[1]) ? $temp[1] : '';
      }
   }
}
$date_start = isset($_GET['date_start']) ? $_GET['date_start'] : '';
$date_end = isset($_GET['date_start']) ? $_GET['date_end'] : '';
$sort = isset($_GET['wpsolr_sort']) ? $_GET['wpsolr_sort'] : '';
$open_advance_search_form = false;
if(!empty($cat) || !empty($range) || !empty($sort)) {
   $open_advance_search_form = true;
}
?>

<form action="<?php echo home_url( '/' ); ?>" method="get" id="adv-search-form">
   <fieldset>
      <div class="input-group mb-3">
         <input type="text" name="s" id="s" class="text form-control" value="<?php the_search_query(); ?>" size="30" aria-label="number" data-uw-rm-form="fx">
         <div class="input-group-append">
            <input type="submit" id="submitSearch" class="input-group-text" value="" aria-label="search" data-uw-rm-empty-ctrl="">
         </div>
      </div>
   </fieldset>

   <a class="arrow-wrapper collapsed" id="solr-adv_serach-toggle" href="javascript:void(0);" data-toggle="collapse" data-target="#advanced_search" data-uw-rm-brl="exc" aria-expanded="false">Advanced Search 
      <span class="arrow-open" style="display: <?php echo $open_advance_search_form ? 'inline-block' : 'none';?>"><img src="<?php echo get_stylesheet_directory_uri();?>/images/arrow-top.png" alt="" role="presentation" style="height: auto;"> </span>
      <span class="arrow-close" style="display: <?php echo $open_advance_search_form ? 'none' : 'inline-block';?>;"><img src="<?php echo get_stylesheet_directory_uri();?>/images/dropdown-arrow.png" alt="" role="presentation" style="height: auto;"> </span>
   </a>

   <fieldset id="solr-adv-fields" style="display: <?php echo $open_advance_search_form ? 'block' : 'none';?>;">
      <div id="advanced_search" class="collapse show" style="">
         <div class="row">
            <div class="col-sm-4">

               <p class="solr-court-select">
                  <label for="solr-court" class="label">Filter by Category</label>
                  <select name="wpsolr_fq[]" class="solr-court" aria-label="select" data-uw-rm-form="fx">
                     <option value="">All</option>
                     <?php
                     $categories = get_categories( array(
                      'orderby' => 'name',
                      'order'   => 'ASC'
                   ) );

                     foreach( $categories as $category ) {
                        echo '<option value="categories:'.$category->name.'" '.selected($cat,'categories:'.$category->name).'>'.$category->name.' ('.$category->count .')</option>';
                     }
                     ?>
                  </select>
               </p>
            </div>
            <div class="col-sm-4">
               <label class="date-title date-text">From Date</label>
               <input type="text" class="solr-dateBox2 solr-datepicker" name="date_start" id="date_start" value="<?php echo $date_start;?>" size="10" length="10" maxlength="10" autocomplete="off" aria-label="number" >
            </div>
            <div class="col-sm-4">
               <label for="date_end" class="date-text">To Date</label>     
               <input type="text" class="solr-dateBox2 solr-datepicker" name="date_end" id="date_end" value="<?php echo $date_end;?>" size="10" maxlength="10" length="10" autocomplete="off" >
            </div>
            <div class="col-sm-12 author-field-wrap">
             <label for="author_search" class="date-text">Author</label> 
             <select id="author_search" name="wpsolr_fq[]" class="text form-control select2-input" > 
               <option value="">All</option>
               <?php 
               $author_args = array(
                  'role__in' => array('author', 'administrator', 'editor'),
                  'orderby' => 'name',
                  'number' => -1,
                  'count_total' => false,
                  'has_published_posts' => array('post')
               );
               $authors = get_users( $author_args );
               if(!empty($authors)) {
                  foreach ( $authors as $val ) {
                     echo '<option value="author:'.$val->display_name.'" '.selected($author,'author:'.$val->display_name).'>'.ucwords($val->display_name).'</option>';
                  }
               }
               ?>  
            </select>    
         </div>
         <div class="col-sm-12">
          <label for="headline_search" class="date-text">Headline</label>     
          <input type="text" id="headline_search" value="<?php echo str_replace('title:', '', $headline);?>" class="text form-control" autocomplete="off" />     
          <input type="hidden" id="headline_search_2" name="wpsolr_fq[]" value="<?php echo $headline;?>" />
       </div>
    </div>
    <input type="hidden" name="wpsolr_fq[]" id="date_range" value="<?php echo $range;?>"> 
    <div class="sort">
      <?php $sort = empty($sort) ? 'sort_by_date_desc' : $sort; ?>
      <label for="sort_date_desc" class="other">Sort Order</label>
      <div class="sort_by"><label class="radio-container"><input name="wpsolr_sort" id="sort_date_desc" value="sort_by_date_desc" <?php checked($sort,'sort_by_date_desc');?> type="radio"> <span class="checkmark"></span>Date Descending (Newest First)</label></div>
      <div class="sort_by"><label class="radio-container"><input name="wpsolr_sort" id="sort_date_asc" value="sort_by_date_asc" <?php checked($sort,'sort_by_date_asc');?> type="radio"> <span class="checkmark"></span>Date Ascending (Oldest First)</label></div>
      <div class="sort_by"><label class="radio-container"><input name="wpsolr_sort" id="sort_relevance" value="sort_by_relevancy_desc" <?php checked($sort,'sort_by_relevancy_desc');?> type="radio"> <span class="checkmark"></span>Relevance </label></div>
   </div>
</div>
<script type="text/javascript">
   jQuery(document).ready(function(e) {
      if(jQuery('.select2-input').length) {
         jQuery('.select2-input').select2({width: '100%'});
      }

      jQuery('#adv-search-form').submit(function(et) {
         var headline = jQuery('#headline_search').val();
         if(headline.trim() != '') {
            jQuery('#headline_search_2').val('title:'+headline);
         } else {
            jQuery('#headline_search_2').val('');
         }
         return true;
      });

      jQuery('#date_start').datepicker({
         changeMonth: true, 
         changeYear: true,
         maxDate: new Date(), 
         yearRange: "1993:+00",
         dateFormat: "mm/dd/yy",
         onSelect: function() {
            addValidation();
         },
         showButtonPanel: true,  
         beforeShow: function( input ) {  
          setTimeout(function() {  
            var buttonPane = jQuery( input )  
            .datepicker( "widget" )  
            .find( ".ui-datepicker-buttonpane" );  
            
            var btn = jQuery('<button type="button" class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all">Clear</button>');  
            btn  
            .unbind("click")  
            .bind("click", function () {  
               jQuery.datepicker._clearDate( input );  
            });  
            buttonPane.html('');
            btn.appendTo( buttonPane );  
            
         }, 1 );  
       }
    });
      jQuery("#date_end").datepicker({ 
         changeMonth: true, 
         changeYear: true, 
         maxDate: new Date(),
         yearRange: "1993:+00",
         dateFormat: "mm/dd/yy ",
         onSelect: function() {
            addValidation();
         },
         showButtonPanel: true,  
         beforeShow: function( input ) {  
          setTimeout(function() {  
            var buttonPane = jQuery( input )  
            .datepicker( "widget" )  
            .find( ".ui-datepicker-buttonpane" );  
            
            var btn = jQuery('<button type="button" class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all">Clear</button>');  
            btn  
            .unbind("click")  
            .bind("click", function () {  
               jQuery.datepicker._clearDate( input );  
            });  
            buttonPane.html('');
            btn.appendTo( buttonPane );  
            
         }, 1 );  
       }
    }); 

      jQuery('#solr-adv_serach-toggle').click(function() {
         jQuery('#solr-adv-fields').toggle('slow');
         jQuery('.arrow-open').toggle();
         jQuery('.arrow-close').toggle();
      });


   });
   function addValidation() {
      if(jQuery("#date_start").val() || jQuery("#date_end").val()) {
         jQuery("#date_start").attr("required", true);
         jQuery("#date_end").attr("required", true);
      } else {
         jQuery("#date_start").removeAttr('required');
         jQuery("#date_end").removeAttr('required');
      }
      setTimeout(function() {
		   if((jQuery('#date_start').val() != '') && (jQuery('#date_end').val() != '')){
				var start = new Date(jQuery('#date_start').val()).valueOf();
				var end = new Date(jQuery('#date_end').val() + ' 23:59:59').valueOf();
        
				
				var date_range = 'displaydate_dt:'+(start+'-'+end);
				jQuery('#date_range').val(date_range);
		 } else{
			 jQuery('#date_range').val('');
		 }
      },100);
   }
</script>
</fieldset>
</form>