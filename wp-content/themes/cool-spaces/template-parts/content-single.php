<?php
/**
 * Template part for displaying Single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Mag_Lite
 */
?>		
<?php
// print '<pre>'; Print_r($post); print '</pre>';
/* Amenities Content */
$features = get_post_meta(get_the_ID(), 'features', TRUE);
$mls_id = get_post_meta(get_the_ID(), 'mls_id', TRUE);
$year_built = get_post_meta(get_the_ID(), 'year_built', TRUE);
$lot_size = get_post_meta(get_the_ID(), 'lot_size', TRUE);
$school_district = get_post_meta(get_the_ID(), 'school_district', TRUE);
$high_school = get_post_meta(get_the_ID(), 'high_school', TRUE);
$middle_school = get_post_meta(get_the_ID(), 'middle_school', TRUE);
$elementary_school = get_post_meta(get_the_ID(), 'elementary_school', TRUE);
$parking_type = get_post_meta(get_the_ID(), 'parking_type', TRUE);
$room_count = get_post_meta(get_the_ID(), 'room_count', TRUE);
$roof_type = get_post_meta(get_the_ID(), 'roof_type', TRUE);
$view_type = get_post_meta(get_the_ID(), 'view_type', TRUE);
$exterior_type = get_post_meta(get_the_ID(), 'exterior_type', TRUE);
$place_type = get_post_meta(get_the_ID(), 'place_type', TRUE);
$address_type = get_post_meta(get_the_ID(), 'address_type', TRUE);
$phone_type = get_post_meta(get_the_ID(), 'phone_type', TRUE);
$website_type = get_post_meta(get_the_ID(), 'website_type', TRUE);
$video_type = get_post_meta(get_the_ID(), 'video_type', TRUE);
?>
<div class="row row-45 row-md-60">
    <div class="col-sm-12 col-lg-8"> 
        <div class="top-section"><h3><?php the_title(); ?></h3></div> 
<?php
$carosal = get_post_meta($post->ID, 'gallery_data', true);
if (isset($carosal) && !empty($carosal)) {
    ?>
            <div class="banner-img" data-lightbox="gallery">
                <!-- Slick Carousel-->
                <div class="slick-slider slider carousel-parent" data-arrows="true" data-loop="false" data-dots="false" data-swipe="true" data-items="1" data-child="#child-carousel" data-for="#child-carousel">
    <?php for ($i = 0; $i < count($carosal['image_url']); $i++) { ?>
                        <div class="item"><img src="<?php echo $carosal['image_url'][$i]; ?>" alt="" width="770" height="520"></div>				                  
                    <?php } ?>
                </div>
                <div class="carousel-thumbnail slider slick-slider" id="child-carousel" data-for=".carousel-parent" data-arrows="true" data-loop="false" data-dots="false" data-swipe="false" data-items="3" data-sm-items="3" data-md-items="5" data-lg-items="5" data-xl-items="5" data-slide-to-scroll="1" data-md-vertical="true">
    <?php for ($i = 0; $i < count($carosal['image_url']); $i++) { ?>
                        <div class="item"><img src="<?php echo $carosal['image_url'][$i]; ?>" alt="" width="770" height="520"></div>
                    <?php } ?> 
                </div>
            </div>
<?php } else { ?>
            <div class="banner-img" data-lightbox="gallery">
            <?php if (has_post_thumbnail()): ?>
                    <figure class="featured-image">
                    <?php the_post_thumbnail('mag-lite-home-slider'); ?>
                    </figure>
                    <?php endif; ?>
            </div>
            <?php } ?>
        <div class="row">
            <div class="col-sm-12">
                <p><?php the_content(); ?></p>
<?php //mag_lite_posted_on();  ?>
            </div>
        </div>        
    </div>

    <div class="col-sm-12 col-lg-4 right-col">
            <?php $sidebar_layout = mag_lite_get_option('layout_options'); 

if ( 'no-sidebar' !== $sidebar_layout ) { ?>
	<div id="secondary" class="custom-col-4"><!-- secondary starting from here -->

		<div class="theiaStickySidebar">

			<?php dynamic_sidebar( 'sidebar-1' ); ?>

		</div>
		
	</div><!-- #secondary -->

<?php } ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr class="bg-gray">
                        <th colspan="2">Property Details</th>
                    </tr>
                </thead>
                <tbody>
<?php if ($place_type != '') { ?>
                        <tr>
                            <td>Name of the Place</td>                          
                        </tr>
                        <tr>
                            <td><?php echo $place_type; ?></td>
                        </tr>
<?php } ?>
                    <?php if ($address_type != '') { ?>
                        <tr>
                            <td>Address</td>                         
                        </tr>
                        <tr>                         
                            <td><?php echo $address_type; ?></td>
                        </tr>
<?php } ?>
                    <?php if ($phone_type != '') { ?>
                        <tr>
                            <td>Phone</td>                          
                        </tr>
                        <tr>                         
                            <td><?php echo $phone_type; ?></td>
                        </tr>
<?php } ?>
                    <?php if ($website_type != '') { ?>
                        <tr>
                            <td>Website</td>                         
                        </tr>
                        <tr>                          
                            <td><a href="<?php echo $website_type; ?>" target="_blank"><?php echo $website_type; ?></td>
                        </tr>
<?php } ?>								  
                </tbody>
            </table>
        </div>
 <?php 
	if ($video_type != '') { 
     $finalUrl = '';
     if(strpos($video_type, 'vimeo.com/') !== false) {
	 
        //it is Vimeo video
        $video = explode("vimeo.com/",$video_type);
		$videoId = (is_array($video) && isset($video[1]))?$video[1]:'';
        if(strpos($videoId, '&') !== false){
            $video = explode("&",$videoId);
			$videoId = (is_array($video) && isset($video[0]))?$video[0]:'';
        }
        $finalUrl.='https://player.vimeo.com/video/'.$videoId;
    }else if(strpos($video_type, 'youtube.com/') !== false) {
	
        //it is Youtube video
        $video = explode("v=",$video_type);
		
		$videoId = (is_array($video) && isset($video[1]))?$video[1]:'';
		
        if(strpos($videoId, '&') !== false){
            $video = explode("&",$videoId);
			$videoId = (is_array($video) && isset($video[0]))?$video[0]:'';
        }
         $finalUrl.='https://www.youtube.com/embed/'.$videoId;
		
    }else if(strpos($video_type, 'youtu.be/') !== false){
        //it is Youtube video
        $video = explode("youtu.be/",$video_type);
		
		$videoId = (is_array($video) && isset($video[1]))?$video[1]:'';
		
        if(strpos($videoId, '&') !== false){
            $video = explode("&",$videoId);
			$videoId = (is_array($video) && isset($video[0]))?$video[0]:'';
        }
        $finalUrl.='https://www.youtube.com/embed/'.$videoId;
    }else{
        //Enter valid video URL
    } ?>
        <table class="table">
                <thead>
                    <tr class="bg-gray">
                        <th colspan="2">Video Details </th>
                    </tr>
                </thead>
            </table>            
            <div class="col-sm-12">
				<iframe id="ytplayer" type="text/html" src="<?php echo $finalUrl ?>" frameborder="0" class="video-section" allowfullscreen></iframe>
            </div>
        <?php } ?>  
    </div> 		
</div>
<!-- #post-<?php the_ID(); ?> -->