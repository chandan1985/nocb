<?php
/*
Plugin Name: PBM Home Articles
Plugin URI: https://tabbforum.com/
Description: Plugin Develop For manage of Home page of articles.
Author: Asentic
Author URI: http://www.asentechllc.com/
Version: 1.0
*/
 

if ( !defined( 'ABSPATH' ) ) exit;

function array_merge_for_unavailable_id($equity_data,$sponsored_post_array)
{
          
  $available_values=array();
    foreach($equity_data as $d)
    {
      foreach ($sponsored_post_array as $key => $value)
      {
        if($d==$value['value'])
        {
         //echo "Available";
        
        array_push($available_values,$value['value']);            
        }
        
        
       
      }
    }
  $available_val_count=count($available_values);    
  $unavailable_values=array();

  if($available_val_count > 0)
  {
     foreach($available_values as $a)
     {
      foreach($equity_data as $e)
      {
        if($e!=$a)
        {
          array_push($unavailable_values,$e);
        }
      } 
     }
  }
  else
  {
    foreach($equity_data as $e)
      {
        
        
          array_push($unavailable_values,$e);
        
      } 
  }
     
   
  
  if(count($unavailable_values) > 0)
  {
    
        foreach ($unavailable_values as $unavailable_value ) :  
          $post   = get_post($unavailable_value);
          
          //print_r($post->post_type);
          if($post->post_type == 'tribe_events'){
              $post_type = "EVENTS";
              
          }
          else if($post->post_type == 'pbm-company')
          {
            $post_type = "COMPANY";
          }
		  else if($post->post_type == 'post')
          {
            $post_type = "ARTICLE";
          }elseif($post->post_type == 'pbm-photo-gallery'){
			  $post_type = "PHOTO GALLERY";
		  }elseif($post->post_type == 'pbm-video-gallery'){
			  $post_type = "VIDEO GALLERY";
		  }
          $sponsored_post_array[] = array(

            'label' => $post_type.": ".$post->post_title,
            'value' => $unavailable_value
            );
          
        endforeach;
        
    
    return $sponsored_post_json_equ = json_encode( $sponsored_post_array ); 
    //print_r($sponsored_post_json_equ);
    

  }
  else
  {
    $sponsored_post_json_equ = json_encode( $sponsored_post_array ); 
    //print_r($sponsored_post_json_equ);
    
  }
  return $sponsored_post_json_equ;
   
   //exit();
}


class TabbHeading{
  public function __construct() {
    add_action('admin_enqueue_scripts', array($this,'add_head_foot_sript'));
    add_action('admin_menu', array($this,'headline_plugin_setup'));
  }


 function headline_plugin_setup(){
    add_menu_page('Headline Page', 'Home Articles', 'manage_options', 'headline-plugin',  array($this,'headline_init'));
    add_submenu_page('headline-plugin', 'Manage Headlines', 'Manage', 'manage_options', 'headline-plugin' );
    //add_submenu_page('headline-plugin', 'Headlines History', 'History', 'manage_options',  'headline-history', array($this,'myplugin_history'));
 }


 function add_head_foot_sript(){

  if(isset($_REQUEST["page"]) && in_array($_REQUEST["page"],array("headline-plugin","headline-history")))
  {
  wp_enqueue_style('paraia-bootstrap', plugins_url('assets/css/headlines.css', __FILE__), null, '1.0');
  wp_enqueue_style('paraia-bootstrap1', plugins_url('assets/css/bootstrap.min.css', __FILE__), null, '1.0');
  wp_enqueue_style('paraia-custom-css', plugins_url('assets/css/jquerysctipttop.css', __FILE__), null, '1.0');
  wp_enqueue_style('font-awesome-css','https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', null, '1.0');
  wp_enqueue_script('jquery-js', 'https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js', array( 'jquery' ), '3.3.1');
  wp_enqueue_script('bundle-multi-select-js', plugins_url('assets/js/bundle.min.js', __FILE__), array( 'jquery' ), '1.0');
  wp_enqueue_script('sortable-min-js', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', array( 'jquery' ), '2.0');
  }
  //wp_enqueue_style('datetimepicker-css', plugins_url('assets/css/jquery.datetimepicker.css', __FILE__), null, '1.0');
 // wp_enqueue_script('datetimepicker-js', plugins_url('assets/js/jquery.datetimepicker.js', __FILE__), array( 'jquery' ), '1.0');
}

function search_exif($exif, $field)
{
    foreach ($exif as $data)
    {
        if ($data['label'] == $field)
            return $data['raw']['_content'];
    }
}



function myplugin_history(){
  include 'manage-history.php';
}

function headline_init(){
  
  
  
  if(isset($_REQUEST["page"]) && in_array($_REQUEST["page"],array("headline-plugin","headline-history")))
  {
 
              $getHeadLines = get_option( 'headlines_data_val' );
          
              $countH = 0;
                  
                if(!is_array($getHeadLines) )
                {
                 
                  $countH = 0;
               
                }else
                {

                  $headline_final = count($getHeadLines);  
                  $countH = $headline_final;
                }


              $getEquities = get_option( 'equities_data_val' );
              if(!empty($_POST['autocompleteval'])){

          $arrayval = $_POST['autocompleteval'];
          $arrayval1 = "";
           

          if(!empty($arrayval))
          $arrayval1 = explode(",",$arrayval);
          
          $today = date("d-m-Y");  
          
          $my_options = array( $countH => array( 'time' =>$today ,'headline_data' => $arrayval1));
          
             if(empty( $getHeadLines)){
               
               if(!is_array($arrayval1))
               {
                $my_options = "";
               }
               $headline_final = $my_options; 
               
             }else{
              
              if(!is_array($arrayval1))
               {
                $headline_final = $getHeadLines;
               }else{
                $headline_final = array_merge($my_options , $getHeadLines);     
               }
              
             }
             
            update_option('headlines_data_val',$headline_final);
            
          echo '<div class="updated notice">';
          echo '<p>Homepage Second Row has been updated successfully.</p>';
          echo '</div>';
           
            //$my_optionseq = array('equity_data' => $equityval1, 'derivative_data' => $derivativeval1,'fintech_data' => $fintechval1, 'regs_data' => $regsval1,'fixedincome_data' => $fixedincomeval1, 'currencies_data' => $currenciesval1); 

            // update_option( 'equities_data_val', $my_optionseq);
              }
        
        
        
        
        if(!empty($_POST['equitiesval'])){

          $equityval = $_POST['equitiesval'];
          
          $equityval1 = "";
          
          if(!empty($equityval))
          $equityval1 = explode(",",$equityval);
          
            $equities_data_val_array=get_option('equities_data_val');
            
            $replacements = array("equity_data" => $equityval1);
            $basket = array_replace($equities_data_val_array, $replacements);
            
             update_option( 'equities_data_val', $basket);
             
             echo '<div class="updated notice">';
            echo '<p>Home Page First Row has been updated successfully.</p>';
            echo '</div>';
              }
        
        
        
        
        
        if(!empty($_POST['derivativesval'])){

          $derivativeval = $_POST['derivativesval'];
          $derivativeval1 = "";
          
          if(!empty($derivativeval))
          $derivativeval1 = explode(",",$derivativeval);
          
             $derivative_data_val_array=get_option('equities_data_val');
            
            $replacements = array("derivative_data" => $derivativeval1);
            $basket = array_replace($derivative_data_val_array, $replacements);
            
             update_option( 'equities_data_val', $basket);
             
          echo '<div class="updated notice">';
          echo '<p>Home Page Third Row has been updated successfully.</p>';
          echo '</div>';
           
              }
        
        
        
        
          
        if(!empty($_POST['fintechval'])){

          $fintechval = $_POST['fintechval'];
          $fintechval1 = "";
           
          if(!empty($fintechval))
          $fintechval1 = explode(",",$fintechval);
          
             $fintech_data_val_array=get_option('equities_data_val');
            
            $replacements = array("fintech_data" => $fintechval1);
            $basket = array_replace($fintech_data_val_array, $replacements);
            
             update_option( 'equities_data_val', $basket);
             
             echo '<div class="updated notice">';
            echo '<p>Fintech / Crypto Headline has been updated successfully.</p>';
            echo '</div>';
           
              }
        
        
        
              
        if(!empty($_POST['regsval'])){

          $regsval = $_POST['regsval'];
          
          $regsval1 = "";
          
           if(!empty($regsval))
          $regsval1 = explode(",",$regsval);
          
             $regsval_data_val_array=get_option('equities_data_val');
            
            $replacements = array("regs_data" => $regsval1);
            $basket = array_replace($regsval_data_val_array, $replacements);
            
             update_option( 'equities_data_val', $basket);
             
             echo '<div class="updated notice">';
            echo '<p>Regulatory Headline has been updated successfully.</p>';
            echo '</div>';
           
              }
        
        
        
        
                  
        if(!empty($_POST['fixedincomeval'])){

          $fixedincomeval = $_POST['fixedincomeval'];
          
          $fixedincomeval1 = "";
          
          if(!empty($fixedincomeval))
          $fixedincomeval1 = explode(",",$fixedincomeval);
          
             $fixedincomeval_data_val_array=get_option('equities_data_val');
            
            $replacements = array("fixedincome_data" => $fixedincomeval1);
            $basket = array_replace($fixedincomeval_data_val_array, $replacements);
            
             update_option( 'equities_data_val', $basket);
             
             echo '<div class="updated notice">';
            echo '<p>Fixed Income Headline has been updated successfully.</p>';
            echo '</div>';
           
              }
        
        
        
                        
        if(!empty($_POST['currenciesval'])){

          $currenciesval = $_POST['currenciesval'];
           
          $currenciesval1 = "";

          if(!empty($currenciesval))
          $currenciesval1 = explode(",",$currenciesval);
          
             $currenciesval_data_val_array=get_option('equities_data_val');
            
            $replacements = array("currencies_data" => $currenciesval1);
            $basket = array_replace($currenciesval_data_val_array, $replacements);
            
             update_option( 'equities_data_val', $basket);
             
             echo '<div class="updated notice">';
            echo '<p>Currencies & Commodities Headline has been updated successfully.</p>';
            echo '</div>';
           
              }
        
        
        
    
        
                echo "<div class='wrap'>";
                echo "<h1 class='wp-heading-inline'>Manage HeadLines</h1>";

                  $options_cpt = array();
                  
                  /* $args = array(
                      //'post_type' => array( 'opinions', 'research', 'videos' ),
            'post_type' => array( 'opinions', 'videos' ),
                      'post_status' => 'publish',
                      'posts_per_page' => 300,
            
                  );
                  $the_query = new WP_Query( $args ); */
          
      
           
              $args = array(
                'post_type' => array('pbm-photo-gallery','pbm-video-gallery','post'),
						  'post_status' => 'publish',
						  'category__not_in'=>45778,
						  'posts_per_page' => 100,
						  /* 'meta_query' => array(
								array(
									'key' => '_thumbnail_id', 
									'compare' => 'EXISTS', 
								),
						   ) */
                );
              
          
          
            
            
          
          
                  $the_query = new WP_Query( $args );
          
          
          $options_cpt=array();
         
                         
                  if ( $the_query->have_posts() ) {         
                      while ( $the_query->have_posts() ) {
                              $the_query->the_post();
                              if(get_post_type() == 'tribe_events'){
                                  $post_type = "EVENTS";
                              }elseif(get_post_type() == 'pbm-company'){
                                  $post_type = "COMPANY";
                              }elseif(get_post_type() == 'post'){
                                  $post_type = "ARTICLE";
                              }elseif(get_post_type() == 'pbm-photo-gallery'){
                                  $post_type = "PHOTO GALLERY";
                              }elseif(get_post_type() == 'pbm-video-gallery'){
                                  $post_type = "VIDEO GALLERY";
                              }
                              $options_cpt[get_the_ID()] = $post_type.': '.get_the_title();                    
                      }
                      wp_reset_postdata();
                  }
				  
                  
              $getHeadLines = get_option( 'headlines_data_val' );
        
        //print_r($getHeadLines);
         
                    $sponsored_post_array = array();
                    $default_sponsored_array = array();
                    $sponsored_counter = 0;
          
           
          
        $count_head=count($options_cpt);
        
        if($count_head > 0)
        {

          foreach ($options_cpt as $key => $headlines ) :   
          $sponsored_post_array[] = array(

                    'label' => $headlines,
                    'value' => "$key" 
                    );
          $sponsored_post_value_array[] = $key;
          if($sponsored_counter<10)
          {
            $default_sponsored_array[] = $key;
          }
           $sponsored_counter++;
          endforeach;
        }
      else
        {
          $sponsored_post_array = $sponsored_post_array;
        }
        
        //print_r($sponsored_post_array);
        //exit();
        
        //$sponsored_post_json = json_encode( $sponsored_post_array ); 
        
        //print_r( $sponsored_post_json);
        
       // echo "1.".count($sponsored_post_array);
        //print_r($sponsored_post_json);
        
        
        //----------------------------- Equities sponsored ---------------------
        
        $args_equ = array(
                      'post_type' => array('post','tribe_events','pbm-company','pbm-photo-gallery','pbm-video-gallery'),
						  'post_status' => 'publish',
						  'category__not_in'=>45778,
						  'posts_per_page' => 100,
						  'meta_query' => array(
								array(
									'key' => '_thumbnail_id', 
									'compare' => 'EXISTS', 
								),
						   )
            
            );
        
        $the_query_equ = new WP_Query( $args_equ );
        
        $options_cpt_equ=array();
		
		if ( $the_query_equ->have_posts() ) {         
                      while ( $the_query->have_posts() ) {
                              $the_query->the_post();
                              if(get_post_type() == 'tribe_events'){
                                  $post_type = "EVENTS";
                              }elseif(get_post_type() == 'pbm-company'){
                                  $post_type = "COMPANY";
                              }elseif(get_post_type() == 'post'){
                                  $post_type = "ARTICLE";
                              }elseif(get_post_type() == 'pbm-photo-gallery'){
                                  $post_type = "PHOTO GALLERY";
                              }elseif(get_post_type() == 'pbm-video-gallery'){
                                  $post_type = "VIDEO GALLERY";
                              }
                              $options_cpt_equ[get_the_ID()] = $post_type.': '.get_the_title();                    
                      }
                      wp_reset_postdata();
                  }
				  
        
            
    
              $getHeadLines = get_option( 'headlines_data_val' );
         
                    $sponsored_post_array_1 = array();
                    $default_sponsored_array = array();
                    $sponsored_counter = 0;
      $count_eq=count($options_cpt_equ);
        
        if($count_eq > 0)
        {
            foreach ($options_cpt_equ as $key => $headlines ) :   
            $sponsored_post_array_1[] = array(

                      'label' => $headlines,
                      'value' => "$key" 
                      );
            $sponsored_post_value_array[] = $key;
            if($sponsored_counter<10)
            {
              $default_sponsored_array[] = $key;
            }
             $sponsored_counter++;
            endforeach;
        }
        else
        {
          $sponsored_post_array_1 = $sponsored_post_array_1;
        }
              //$sponsored_post_json_equ = json_encode( $sponsored_post_array_1 ); 
        //print_r($sponsored_post_json_equ);
        //exit();
        //echo "2.".count($sponsored_post_array);
        
        
        
        
        //----------------------------- Equities Derivatives ---------------------
        
              
        $args_der = array(
                      'post_type' => array('post'),
						  'post_status' => 'publish',
						  'category__not_in'=>45778,
						  'posts_per_page' => 100,
						  'meta_query' => array(
						  
								array(
									'key' => 'associated_sponsor_id',
									'compare' => 'EXISTS', 
								),
								
						   )
            
            );
        
        $the_query_der= new WP_Query( $args_der);
        
        
            $options_cpt_der=array();
          
          
                  if ( $the_query_der->have_posts() ) {
					while ( $the_query_der->have_posts() ) {
                              $the_query_der->the_post();
							  
                              if(get_post_type() == 'tribe_events'){
                                  $post_type = "EVENTS";
                              }elseif(get_post_type() == 'pbm-company'){
                                  $post_type = "COMPANY";
                              }elseif(get_post_type() == 'post'){
                                  $post_type = "ARTICLE";
                              }
                              $options_cpt_der[get_the_ID()] = $post_type.': '.get_the_title();                    
                      }
                      wp_reset_postdata();
                  }
          
              $getHeadLines = get_option( 'headlines_data_val' );
         
                    $sponsored_post_array_2 = array();
                    $default_sponsored_array = array();
                    $sponsored_counter = 0;
      $count_der=count($options_cpt_der);
        
        if($count_der > 0)
        {

          foreach ($options_cpt_der as $key => $headlines ) :   
          $sponsored_post_array_2[] = array(

                    'label' => $headlines,
                    'value' => "$key" 
                    );
          $sponsored_post_value_array[] = $key;
          if($sponsored_counter<10)
          {
            $default_sponsored_array[] = $key;
          }
           $sponsored_counter++;
          endforeach;
        }
        else
        {
          $sponsored_post_array_2 = $sponsored_post_array_2;
        }
        
      
        
        
              //$sponsored_post_json_der = json_encode( $sponsored_post_array_2 ); 
        
        //echo "2.".count($sponsored_post_array);
        
        //print_r($sponsored_post_json_der);
        
        
        
        
      
        
        
              //$sponsored_post_json_cur= json_encode( $sponsored_post_array_6 ); 
        
        //echo "2.".count($sponsored_post_array);
      
        
        //$equity_data=array("85319","151333","263523");
        //print_r($getHeadLines);
        $first = true;
        foreach($getHeadLines as $getHeadLine)
        {
          
          if ( $first )
          {
            $getHeadLines_array=$getHeadLine['headline_data'];
            $first = false;
          }
          
        }
        
        //print_r($getHeadLines_array);
        
        $sponsored_post_json = array_merge_for_unavailable_id($getHeadLines_array,$sponsored_post_array);
              
              $getHeadLinesOrder = array();
              $result_val_array = "";
               
              if(!empty($getHeadLines) )
              {
                $getHeadLinesOrder = array_shift($getHeadLines);
                foreach ($getHeadLinesOrder['headline_data'] as $key => $value) {
                  if(get_post_status($value) === false )
                  {

                    unset($getHeadLinesOrder['headline_data'][$key]);
                  }
                }
                $result_val_array = "'" .implode ( "', '", $getHeadLinesOrder['headline_data'])."'"; 
              }
        
        
              
              $getequities = get_option( 'equities_data_val' );
       //print_r($getequities );
        
              $equity_data = $getequities['equity_data'];
        
         //print_r($equity_data);
        
        //$equity_data=array("85319","151333","263523");
        
        $sponsored_post_json_equ = array_merge_for_unavailable_id($equity_data,$sponsored_post_array_1);
        
        //print_r($sponsored_post_json_equ);
        
            $equity_data_new = array();
            foreach($equity_data as $equity_data_value)
            {
        if ( 'publish' == get_post_status ( $equity_data_value ) ) {
          // do stuff
          $equity_data_new[] = $equity_data_value;
        }
            }
            $getequities['equity_data'] = $equity_data_new;
            
              
            if(!empty($getequities['equity_data']))
              $equity_val_array = "'" .implode ( "', '", $getequities['equity_data'])."'";
      
      //print_r($equity_val_array);
            
            
            $derivative_data = $getequities['derivative_data'];
      $sponsored_post_json_der = array_merge_for_unavailable_id($derivative_data,$sponsored_post_array_2);
      
      //print_r($equity_data );
        //$equity_data=array("85319","151333","263523");
        
      
            $derivative_data_new = array();
            foreach($derivative_data as $derivative_data_value)
            {
        if ( 'publish' == get_post_status ( $derivative_data_value ) ) {
          // do stuff
          $derivative_data_new[] = $derivative_data_value;
        }
            }
            $getequities['derivative_data'] = $derivative_data_new;
            
            
            if(!empty($getequities['derivative_data']))
              $derivative_val_array = "'" .implode ( "', '", $getequities['derivative_data'])."'";
      
      //print_r($derivative_val_array);
            
            
            
      
      
        

              //print_r( $equity_val_array);
              //die();

        
        
    

            echo '<div class="container-fluid"><div class="col-md-8">';
            

      echo '<form id="headlinedata2" action="' .esc_url($_SERVER['REQUEST_URI']) . '" method="post">'; 
            echo '<h4 style="color:orange;margin-top:50px;"><label>Home Page First Row Articles</label></h4>';
            echo '<input type="hidden" name="txt_equties_ids" id="txt_equties_ids" value=""/>';
            echo '<span class="equities-select"></span>';
            echo '<input type="hidden" name="equitiesval" id="equitiesval">'; 
            echo '<div style="color:red;margin-top:5px;">You have permission to set only 2 Articles/Events/Company/Video Gallery/Photo Gallery.</div>';
      echo '<p><input style="margin-top:20px;" type="button" name="headsubmit" id="headsubmit2" value="Submit"/></p>';
	  echo '</form>';
	  
	  
	  echo '<form id="headlinedata1" action="' .esc_url($_SERVER['REQUEST_URI']) . '" method="post">';  
            echo '<h4 style="color:orange;margin-top:50px;"><label>Home Page Second Row Articles</label></h4>';
            echo '<input type="hidden" name="txt_special_edition_ids" id="txt_special_edition_ids" value=""/>';
            echo '<span class="autocomplete-select"></span>';
            echo '<input type="hidden" name="autocompleteval" id="autocompleteval">';
            echo '<div style="color:red;margin-top:5px;">You have permission to set only 3 Articles/Events/Company/Video Gallery/Photo Gallery.</div>';
      echo '<p><input style="margin-top:20px;" type="button" name="headsubmit" id="headsubmit1" value="Submit"/></p>';
      echo '</form>'; 
      
      echo '<form id="headlinedata3" action="' .esc_url($_SERVER['REQUEST_URI']) . '" method="post">'; 
            echo '<h4 style="color:orange;margin-top:50px;"><label>Home Page Sponsered Articles</label></h4>';
            echo '<input type="hidden" name="txt_derivatives_ids" id="txt_derivatives_ids" value=""/>';
            echo '<span class="derivatives-select"></span>';
            echo '<input type="hidden" name="derivativesval" id="derivativesval">'; 
            echo '<div style="color:red;margin-top:5px;">You have permission to set only 3 Articles.</div>';
      echo '<p><input style="margin-top:20px;" type="button" name="headsubmit" id="headsubmit3" value="Submit"/></p>';
      echo '</form>';
      
      
      
            echo '</div></div>';

            
            
           // $head_diff = array_diff($result_val_array, $sponsored_post_value_array);

           // echo "<pre>";
           // print_r($sponsored_post_value_array);
           // die();

           /* print_r($result_val_array);
            echo "<pre>";
            print_r(json_decode($sponsored_post_json));
            die();*/
              ?>
                  <style>
                  #headlinedata1 .error{
                    border: solid 1px red;
                    margin: 0px;
                  }
                  </style>
                  <script type="text/javascript">
          
                      var testval , equityval , derivativeval;
                      var allgoods = true;
                      $(document).ready(function(){
                          testval = [<?php echo $result_val_array; ?>];
                           equityval = [<?php echo $equity_val_array; ?>];
                           derivativeval = [<?php echo $derivative_val_array; ?>];
                           
               
               
               
              

                          var multi = new SelectPure(".autocomplete-select", {
                              options: <?php echo $sponsored_post_json; ?>,
                              value: [<?php echo $result_val_array; ?>],
                              multiple: true,
                              autocomplete: true,
                              icon: "fa fa-times",
                              onChange: value => { 
                                 
                                  testval = value;  
                                  
                                if(testval.length > 3 ){
                                  $('.autocomplete-select').find(".select-pure__select").addClass("error");
                                }else{
                                  $('.autocomplete-select').find(".select-pure__select").removeClass("error");

                                }     
                              },
                            });

                            $( ".autocomplete-select .select-pure__label" ).sortable({
                                      axis: "x,y",
                                      stop: function( event, ui ) {

                                         var winners_array = $.map($('.autocomplete-select .select-pure__selected-label i'), function(el) {
                                              return $(el).data('value').toString();
                                         });
                                         testval = winners_array;
                                      }
                                  });

             
             
                  

                                var multi = new SelectPure(".equities-select", {
                                  options: <?php echo $sponsored_post_json_equ; ?>,
                                  value: [<?php echo $equity_val_array; ?>],
                                  multiple: true,
                                  autocomplete: true,
                                  icon: "fa fa-times",
                                  onChange: value => { 

                                      
                                      equityval = value;
                                        if(equityval.length > 2 ){
                      
                                      $('.equities-select').find(".select-pure__select").addClass("error");
                                    }else{
                    
                                      $('.equities-select').find(".select-pure__select").removeClass("error");

                                    }      
                                  },
                                });



                            $( ".equities-select .select-pure__label" ).sortable({
                                      axis: "x,y",
                                      stop: function( event, ui ) {
                                         var winners_array1 = $.map($('.equities-select .select-pure__selected-label i'), function(el) {
                                              return $(el).data('value').toString();
                                         });
                                        
                                         equityval = winners_array1;
                                      }
                                  });


                              var multi = new SelectPure(".derivatives-select", {
                                options: <?php echo $sponsored_post_json_der; ?>,
                                value: [<?php echo $derivative_val_array; ?>],
                                multiple: true,
                                autocomplete: true,
                                icon: "fa fa-times",
                                onChange: value => { 
                                   
                                    derivativeval = value;  
                                    if(derivativeval.length > 3 ){
                                      $('.derivatives-select').find(".select-pure__select").addClass("error");
                                    }else{
                                      $('.derivatives-select').find(".select-pure__select").removeClass("error");

                                    }      

                                },
                              });

                              $( ".derivatives-select .select-pure__label" ).sortable({
                                        axis: "x,y",
                                        stop: function( event, ui ) {
                                           var winners_array2 = $.map($('.derivatives-select .select-pure__selected-label i'), function(el) {
                                                return $(el).data('value').toString();
                                           });
                                           derivativeval = winners_array2;
                                        }
                                    });

                    

                             

                           




                                   $('#headsubmit1').click(function(e){

                                        
                                        
                                        $('#autocompleteval').val(testval);
                                        

                                        if(testval.length > 3)
                                        {
                                          allgoods = false;
                                        }else{
                                          allgoods = true;
                                        }  
                                        
                                        if(allgoods)
                                        {
                                          $('#headlinedata1').submit();
                                        } 

                                      });
                    
                    
								$('#headsubmit2').click(function(e){

                                        
                                        
                                        
                                        $('#equitiesval').val(equityval);
                                        


                                        if(equityval.length > 2)
                                        {
                                          allgoods = false;
                                        }else{
                                          allgoods = true;
                                        }  
                                        
                                        if(allgoods)
                                        {
                                          $('#headlinedata2').submit();
                                        } 

                                      });
                    
                    
                    
                    $('#headsubmit3').click(function(e){

                                        
                                        $('#derivativesval').val(derivativeval);
                                        
                                        if(derivativeval.length > 3)
                                        {
                                          allgoods = false;
                                        }else{
                                          allgoods = true;
                                        }  
                                        
                                        if(allgoods)
                                        {
                                          $('#headlinedata3').submit();
                                        } 

                                      });
                    
                    
                    
                    
                      });
                  </script>
              
                <?php
                echo "</div>";
      }
  }
}

global $tabbHeading;
$tabbHeading = new TabbHeading();


add_action("slidedeck_after_options_group_wrapper", "asentechllc_after_options_group_wrapper");

function asentechllc_after_options_group_wrapper( $slidedeck)
{
       $custom_headline_date = get_post_meta( $slidedeck['id'],  'slidedeck_custom_headline_date', true );
       $custom_publish_date = get_post_meta( $slidedeck['id'],  'slidedeck_custom_publish_date', true );
        ?>
        <style>
            #slidedeck-form-body fieldset#slidedeck-section-custom-datefields{
                border-top: 1px solid #bdc4cb;
                border-bottom: none;
                padding: 0;
            }
            #slidedeck-form-body fieldset#slidedeck-section-custom-datefields label{
                min-width: 182px;
                display: inline-block;
                margin-bottom: 15px;
            }
        </style>
        
        <fieldset id="slidedeck-section-custom-datefields" class="slidedeck-form-section collapsible clearfix<?php echo empty( $custom_headline_date ) ? ' closed' : '' ; ?>">
            <div class="hndl-container">
                <h3 class="hndl"><span class="indicator"></span>Additional Information</h3>
            </div>
            <div class="inner clearfix" style="padding: 15px;" >
               
                <div id="custom-slidedeck-css1">
                    <label for="custom_publish_date" class="label">Publish Date<span class="tooltip"></span></label> 

                    <input type="text" name="custom_publish_date" id="datetimepicker1" class="custom_publish_date fancy fancy-text form-control datetimepicker" data-value="<?php echo $custom_publish_date; ?>" value="<?php echo $custom_publish_date; ?>" >
                </div>
                <div id="custom-slidedeck-css1">
                    <label for="custom_headline_date" class="label">Headline Date<span class="tooltip"></span></label> 

                    <input type="text" name="custom_headline_date" class="custom_headline_date fancy fancy-text form-control datetimepicker" data-value="<?php echo $custom_headline_date; ?>"  value="<?php echo $custom_headline_date; ?>" >
                </div>
            </div>
        </fieldset>
        <script type="text/javascript">
                $(document).ready(function(){

                    setTimeout(function (){
                    <?php 
                       if(!empty($custom_publish_date)){ 
                    ?>
                    $(".custom_publish_date").val('<?php echo $custom_publish_date; ?>');
                    <?php 
                       }
                       if(!empty($custom_headline_date)){
                    ?>
                    $(".custom_headline_date").val('<?php echo $custom_headline_date; ?>');  
                    <?php 
                       }
                    ?>
                  },500);
                })   

        </script>
        <!--<script type="text/javascript">
          jQuery('#datetimepicker1').datetimepicker({
            format:'Y/m/d H:i',
            inline:false,
            formatDate:'Y/m/d',
            lang:'en'
          });
        </script>-->
    <?php
}

add_action("slidedeck_after_save", "asentechllc_after_save",10,4);

function asentechllc_after_save( $id, $data, $deprecated, $sources ) {
        // global $SlideDeckPlugin;

        echo $custom_publish_date = $_REQUEST['custom_publish_date'];
        update_post_meta( $id, 'slidedeck_custom_publish_date', $custom_publish_date );
        echo $custom_headline_date = $_REQUEST['custom_headline_date'];
        update_post_meta( $id, 'slidedeck_custom_headline_date', $custom_headline_date );
    }

add_action( 'wp_ajax_register_on_24', 'register_on_24' );

function register_on_24()
{
  global $wpdb,$current_user;
  $webinarEventId = "";
  $response = "";
  if(isset($_REQUEST["eventid"]) )
  {
    $webinarEventId = (isset($_REQUEST["eventid"]))?$_REQUEST["eventid"]:0;
    $validposttype = get_post_type($webinarEventId);
    if($validposttype === "webinars")
    {
      try {
        $url = 'http://event.on24.com/utilApp/r'; 
        $current_user_id = get_current_user_id();
         $current_user_meta = array_map( function( $a ){ return $a[0]; }, get_user_meta( $current_user_id ) );

         $on24_event_id = get_field("on24_event_id",$webinarEventId);
         $on24_session_id = "";
         $on24_key = get_field("on24_key",$webinarEventId);
        
         $params         = array( 'eventid' => $on24_event_id,
                           'sessionid' => $on24_session_id,
                           'key' => $on24_key,
                           'firstname' => $current_user_meta["first_name"],
                           'lastname' => $current_user_meta["last_name"],
                           'email' => $current_user->user_email,
                           'company' => $current_user_meta["company"],
                           'company_industry' => $current_user_meta["industry_area"],
                           'job_title' => $current_user_meta["title"],
                           'work_phone' => $current_user_meta["phone"],
                           'address_street1' => $current_user_meta["address"],
                           'city' => $current_user_meta["city"],
                           'state' => $current_user_meta["address_state"],
                           'zip' => $current_user_meta["zip_code"],
                           'country' => $current_user_meta["country"],
                           'deletecookie' => true );
                $responsecall = wp_remote_post($url, $params);
                $response = array("result"=>"success", "message"=>"You've been registered! You will receive an email with additional details.");
        }
      catch(Exception $e) {
          $response = array("result"=>"failure", "message"=>'Webinar registration failed: #' .$e->getMessage());
      }
    }else{
      $response = array("result"=>"failure","message"=>"That webinar was not found.");  
    }

  }else{
    $response = array("result"=>"failure","message"=>"That webinar was not found.");
  }

  echo json_encode($response);
  wp_die(); 
}    

?>
