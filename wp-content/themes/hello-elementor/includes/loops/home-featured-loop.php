<?php $featCounter = 0; 
	  $homeTopPosts = array();
	  $homeSponsorPosts = array();
?>
<?php if ( have_posts() ) : while ( have_posts()) : the_post(); ?>
<?php 
$activeimage = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), $size = 'large-image');
$activeimage = $activeimage[0];
$homeTopPosts[$featCounter]['ID'] = $post->ID;
$homeTopPosts[$featCounter]['type'] = 'post';
$homeTopPosts[$featCounter]['activeimage'] = $activeimage;
$homeTopPosts[$featCounter]['permalink'] = get_the_permalink();
$homeTopPosts[$featCounter]['title'] = get_the_title();
$homeTopPosts[$featCounter]['summary'] = get_the_excerpt();
$activeimagepos = get_post_meta($post->ID, 'post_fifa', true); 
if ($activeimagepos == ""){$activeimagepos = "center center";}
$homeTopPosts[$featCounter]['activeimagepos'] = $activeimagepos;
$featCounter++;
 ?>
<?php endwhile; ?>
<?php 
	$featCounter=0; ?>
<?php endif; ?>
<?php wp_reset_query(); ?>
<?php 
$sponsorQueryArgs = array(
			'posts_per_page'=> 2,
			'post_type' => 'sponsoredcontent',
			'post_status' => 'publish',
			'meta_key' => '_thumbnail_id',
			'ignore_sticky_posts' => 1,
			'meta_query' => array(
				array(
					'key'     => 'sc_advertiser_year',
					'value'   => date("Y"),
					'compare' => '=',
				),
				array(
					'key'     => 'sc_advertiser_month',
					'value'   => date("M"),
					'compare' => '=',
				),
			),
		);
		$sponsorQuery = new WP_Query($sponsorQueryArgs);
		//print_r($sponsorQuery->request);exit;
		$sponsorQueryCounter = 1;
		if ($sponsorQuery->have_posts()):
			while ($sponsorQuery->have_posts()) : $sponsorQuery->the_post();  ?>
		<?php 
$activeimage = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), $size = 'large-image');
$activeimage = $activeimage[0];
$homeSponsorPosts[$featCounter]['ID'] = $post->ID;
$homeSponsorPosts[$featCounter]['type'] = 'sponsor';
$homeSponsorPosts[$featCounter]['activeimage'] = $activeimage;
$homeSponsorPosts[$featCounter]['permalink'] = get_the_permalink();
$homeSponsorPosts[$featCounter]['title'] = get_the_title();
$homeSponsorPosts[$featCounter]['summary'] = get_the_excerpt();
$homeSponsorPosts[$featCounter]['activeimagepos'] = '';
$featCounter++;
 ?>	
<?php endwhile; ?>
<?php endif; ?>
<?php wp_reset_query(); 
function array_swap(&$array,$swap_a,$swap_b) {
	list($array[$swap_a],$array[$swap_b]) = array($array[$swap_b],$array[$swap_a]);
}
if (isset($homeSponsorPosts[0]) && isset($homeSponsorPosts[1])) {
	$t = array();
	$t1 = array();
	$t[] = $homeSponsorPosts[0];
	array_splice( $homeTopPosts, 1, 0, $t );
	if(isset($homeSponsorPosts[1])){
	$t1[] = $homeSponsorPosts[1];
	array_splice( $homeTopPosts, 4, 0, $t1 );
	}
}elseif(isset($homeSponsorPosts[0]) && !isset($homeSponsorPosts[1])){
	$t = array();
	$t1 = array();
	$t[] = $homeSponsorPosts[0];
	array_splice( $homeTopPosts, 4, 0, $t );
}
 ?>
<?php 
$featCounter  =1;
foreach($homeTopPosts as $post){  ?>
	<?php if($featCounter == 1){ ?>
        <div class="block-50 block-h-59">
            <a class="hp-featured-img-link" style="overflow:hidden; background-position:<?php echo $post['activeimagepos']; ?>; background-image:url(<?php echo $post['activeimage']; ?>);" href="<?php echo $post['permalink']; ?>">
                <div class="hp-featured-img-overlay"></div>
                <h2 class="hp-featured-title"><?php echo $post['title']; ?></h2>
                <div class="hp-featured-expand"><img src="<?php print IMAGES;?>/expand-icon.png" /></div>
                <div class="hp-featured-summary"><?php echo limit_words($post['summary'],20); ?>...</div>
                <div class="hp-featured-readmore">READ MORE</div>
            </a>
        </div>
    <?php } else if($featCounter == 2){ ?>
	
        <div class="block-50 block-h-295">
            <a class="hp-featured-img-link <?php echo ($post['type'] == 'sponsor')?'spons-block':''; ?> hp-featured-sub-img-link" style="overflow:hidden; background-position:<?php echo $post['activeimagepos']; ?>; background-image:url(<?php echo $post['activeimage']; ?>);" href="<?php echo $post['permalink']; ?>">
                <div class="hp-featured-img-overlay"></div>
				<?php if($post['type'] == 'sponsor'){ ?>
					<h3 class="hp-sponsored-title">Sponsored Content</h3>
                    <h2 class="hp-featured-title-sp"><?php echo $post['title']; ?></h2>
					<?php }else{ ?>
					<h2 class="hp-featured-title"><?php echo $post['title']; ?></h2>
					<?php } ?>
               
                <div class="hp-featured-expand"><img src="<?php print IMAGES;?>/expand-icon.png" /></div>
                <div class="hp-featured-summary"><?php echo $post['summary']; ?>...</div>
                <div class="hp-featured-readmore">READ MORE</div>
            </a>
        </div>
    <?php } else if($featCounter == 3){ ?>
        <div class="block-25 block-h-295">
            <a class="hp-featured-img-link hp-featured-sub-img-link" style="overflow:hidden; background-position:<?php echo $post['activeimagepos']; ?>; background-image:url(<?php echo $post['activeimage']; ?>);" href="<?php echo $post['permalink']; ?>">
                <div class="hp-featured-img-overlay"></div>
                <h2 class="hp-featured-title"><?php echo $post['title']; ?></h2>
                <div class="hp-featured-expand"><img src="<?php print IMAGES;?>/expand-icon.png" /></div>
                <div class="hp-featured-summary"><?php echo limit_words($post['summary'],12); ?>...</div>
                <div class="hp-featured-readmore">READ MORE</div>
            </a>
        </div>
        <!--<div class="block-25 block-h-295 block-ad-1">
            <div class="feat-search-sponsor">
                < ? php include 'wp-content/themes/PetAge/includes/ads/petage/ros-websponsor.php'; ? >
            </div>
        </div>-->
    <?php } else if($featCounter == 4){ ?>
        <div class="block-25 block-h-295 block-ad-1">
            <a class="hp-featured-img-link hp-featured-sub-img-link" style="overflow:hidden; background-position:<?php echo $post['activeimagepos']; ?>; background-image:url(<?php echo $post['activeimage']; ?>);" href="<?php echo $post['permalink']; ?>">
                <div class="hp-featured-img-overlay"></div>
                <h2 class="hp-featured-title"><?php echo $post['title']; ?></h2>
                <div class="hp-featured-expand"><img src="<?php print IMAGES;?>/expand-icon.png" /></div>
                <div class="hp-featured-summary"><?php echo limit_words($post['summary'],12); ?>...</div>
                <div class="hp-featured-readmore">READ MORE</div>
            </a>
        </div>
        <div class="block-25 block-h-41 block-cl block-ad-2">
            <div class="feat-med-rect" style="width:100%; height:100%; text-align:center">
                <?php include 'wp-content/themes/PetAge/includes/ads/petage/ros-medrect-1.php'; ?>
            </div>
        </div>
    <?php } else if($featCounter == 5){ 
		?>
            <div class="block-50 block-h-41">
                <a class="hp-featured-img-link <?php echo ($post['type'] == 'sponsor')?'spons-block':''; ?> hp-featured-sub-img-link" style="overflow:hidden; background-position:<?php echo $post['activeimagepos']; ?>; background-image:url(<?php echo $post['activeimage']; ?>);" href="<?php echo $post['permalink']; ?>">
                    <div class="hp-featured-img-overlay"></div>
					<?php if($post['type'] == 'sponsor'){ ?>
					<h3 class="hp-sponsored-title">Sponsored Content</h3>
                    <h2 class="hp-featured-title-sp"><?php echo $post['title']; ?></h2>
					<?php }else{ ?>
					<h2 class="hp-featured-title"><?php echo $post['title']; ?></h2>
					<?php } ?>
                    <div class="hp-featured-expand"><img src="<?php print IMAGES;?>/expand-icon.png" /></div>
                    <div class="hp-featured-summary"><?php echo $post['summary']; ?>...</div>
                    <div class="hp-featured-readmore">READ MORE</div>
                </a>
            </div>
		<?php
		?>
    <?php } else if($featCounter == 6){ ?>
            <div class="block-25 block-h-41">
                <a class="hp-featured-img-link hp-featured-sub-img-link" style="overflow:hidden; background-position:<?php echo $post['activeimagepos']; ?>; background-image:url(<?php echo $post['activeimage']; ?>);" href="<?php echo $post['permalink']; ?>">
                    <div class="hp-featured-img-overlay"></div>
                    <h2 class="hp-featured-title"><?php echo $post['title']; ?></h2>
                    <div class="hp-featured-expand"><img src="<?php print IMAGES;?>/expand-icon.png" /></div>
                    <div class="hp-featured-summary"><?php echo limit_words($post['summary'],12); ?>...</div>
                    <div class="hp-featured-readmore">READ MORE</div>
                </a>
            </div>
    <?php } ?>
	<?php $do_not_duplicate[] = $post['ID']; ?>
    <?php $featCounter ++; ?>
<?php } ?>