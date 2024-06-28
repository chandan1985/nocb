<?php $featCounter = 1; ?>
<?php if ( have_posts() ) : while ( have_posts()) : the_post(); ?>
	<?php if($featCounter == 1){ ?>
        <div class="block-50 block-h-59">
        	<?php 
			$activeimage = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), $size = 'large-image');
			$activeimage = $activeimage[0];
			?>
            <?php $activeimagepos = get_post_meta($post->ID, 'post_fifa', true); ?>
            <?php if ($activeimagepos == ""){$activeimagepos = "center center";}?>
            <a class="hp-featured-img-link" style="overflow:hidden; background-position:<?php echo $activeimagepos; ?>; background-image:url(<?php echo $activeimage; ?>);" href="<?php echo get_the_permalink(); ?>">
                <div class="hp-featured-img-overlay"></div>
                <h2 class="hp-featured-title"><?php echo get_the_title(); ?></h2>
                <div class="hp-featured-expand"><img src="<?php print IMAGES;?>/expand-icon.png" /></div>
                <div class="hp-featured-summary"><?php echo limit_words(get_the_excerpt(),20); ?>...</div>
                <div class="hp-featured-readmore">READ MORE</div>
            </a>
        </div>
    <?php } else if($featCounter == 2){ ?>
        <div class="block-50 block-h-295">
            <?php 
			$activeimage = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), $size = 'large-image');
			$activeimage = $activeimage[0];
			?>
            <?php $activeimagepos = get_post_meta($post->ID, 'post_fifa', true); ?>
            <?php if ($activeimagepos == ""){$activeimagepos = "center center";}?>
            <a class="hp-featured-img-link hp-featured-sub-img-link" style="overflow:hidden; background-position:<?php echo $activeimagepos; ?>; background-image:url(<?php echo $activeimage; ?>);" href="<?php echo get_the_permalink(); ?>">
                <div class="hp-featured-img-overlay"></div>
                <h2 class="hp-featured-title"><?php echo get_the_title(); ?></h2>
                <div class="hp-featured-expand"><img src="<?php print IMAGES;?>/expand-icon.png" /></div>
                <div class="hp-featured-summary"><?php echo limit_words(get_the_excerpt(),20); ?>...</div>
                <div class="hp-featured-readmore">READ MORE</div>
            </a>
        </div>
    <?php } else if($featCounter == 3){ ?>
        <div class="block-25 block-h-295">
            <?php 
			$activeimage = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), $size = 'medium-image');
			$activeimage = $activeimage[0];
			?>
            <?php $activeimagepos = get_post_meta($post->ID, 'post_fifa', true); ?>
            <?php if ($activeimagepos == ""){$activeimagepos = "center center";}?>
            <a class="hp-featured-img-link hp-featured-sub-img-link" style="overflow:hidden; background-position:<?php echo $activeimagepos; ?>; background-image:url(<?php echo $activeimage; ?>);" href="<?php echo get_the_permalink(); ?>">
                <div class="hp-featured-img-overlay"></div>
                <h2 class="hp-featured-title"><?php echo get_the_title(); ?></h2>
                <div class="hp-featured-expand"><img src="<?php print IMAGES;?>/expand-icon.png" /></div>
                <div class="hp-featured-summary"><?php echo limit_words(get_the_excerpt(),12); ?>...</div>
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
            <?php 
			$activeimage = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), $size = 'medium-image');
			$activeimage = $activeimage[0];
			?>
            <?php $activeimagepos = get_post_meta($post->ID, 'post_fifa', true); ?>
            <?php if ($activeimagepos == ""){$activeimagepos = "center center";}?>
            <a class="hp-featured-img-link hp-featured-sub-img-link" style="overflow:hidden; background-position:<?php echo $activeimagepos; ?>; background-image:url(<?php echo $activeimage; ?>);" href="<?php echo get_the_permalink(); ?>">
                <div class="hp-featured-img-overlay"></div>
                <h2 class="hp-featured-title"><?php echo get_the_title(); ?></h2>
                <div class="hp-featured-expand"><img src="<?php print IMAGES;?>/expand-icon.png" /></div>
                <div class="hp-featured-summary"><?php echo limit_words(get_the_excerpt(),12); ?>...</div>
                <div class="hp-featured-readmore">READ MORE</div>
            </a>
        </div>
        <div class="block-25 block-h-41 block-cl block-ad-2">
            <div class="feat-med-rect" style="background-color:#333; width:100%; height:100%; text-align:center">
                <?php include 'wp-content/themes/PetAge/includes/ads/petage/ros-medrect-1.php'; ?>
            </div>
        </div>
    <?php } else if($featCounter == 5){ ?>
		
		<?php
		$storyTitle4 = get_the_title();
		$activeimage = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), $size = 'large-image');
		$storyActiveImage4 = $activeimage[0];
		$storyActiveImagePos4 = get_post_meta($post->ID, 'post_fifa', true);
		if ($storyActiveImagePos4 == ""){$storyActiveImagePos4 = "center center";}
		$storyPermalink4 = get_the_permalink();
		$storySummary4 = limit_words(get_the_excerpt(),20);
		
		$sponsorQueryArgs = array(
			'posts_per_page'=> 4,
			'post_type' => 'sponsoredcontent',
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
		$sponsorQueryCounter = 1;
		if ($sponsorQuery->have_posts()){
			while ($sponsorQuery->have_posts()) : $sponsorQuery->the_post(); ?>
				<div class="block-50 block-h-41 spons-block spons-block-<?php echo $sponsorQueryCounter; ?>" style="display:none">
					<?php 
					$scadvertiser = get_post_meta($post->ID, 'sc_advertiser', true);
                    $activeimage = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), $size = 'large-image');
                    $activeimage = $activeimage[0];
					$activeimagepos = "";
                    ?>
                    <?php if ($activeimagepos == ""){$activeimagepos = "center center";}?>
                    <a class="hp-featured-img-link hp-featured-sub-img-link" rel="nofollow" style="overflow:hidden; background-position:<?php echo $activeimagepos; ?>; background-image:url(<?php echo $activeimage; ?>);" href="<?php echo get_the_permalink(); ?>">
                        <div class="hp-featured-img-overlay"></div>
                        <h3 class="hp-sponsored-title">Sponsored Content</h3>
                        <h2 class="hp-featured-title-sp"><?php echo get_the_title(); ?></h2>
                        <div class="hp-featured-expand"><img src="<?php print IMAGES;?>/expand-icon.png" /></div>
                        <div class="hp-featured-summary"><?php echo limit_words(get_the_excerpt(),20); ?>...</div>
                        <div class="hp-featured-readmore">READ MORE</div>
                    </a>
            	</div>
                <?php $sponsorQueryCounter++; ?>
        	<?php	
			endwhile;
			?>
			<script>
				//Script handles randomizing Sponsored Content
            	$(document).ready(function(e) {
               		var sponsorCount = $(".spons-block").length;
					var sponsorShow = Math.floor(Math.random() * sponsorCount) + 1;
					$(".spons-block-"+sponsorShow).css("display",'block');
                });
            </script>
			<?php
		}
		else{
		?>
            <div class="block-50 block-h-41">
                <?php 
                $activeimage = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), $size = 'large-image');
                $activeimage = $activeimage[0];
                ?>
                <?php $activeimagepos = get_post_meta($post->ID, 'post_fifa', true); ?>
                <?php if ($activeimagepos == ""){$activeimagepos = "center center";}?>
                <a class="hp-featured-img-link hp-featured-sub-img-link" style="overflow:hidden; background-position:<?php echo $activeimagepos; ?>; background-image:url(<?php echo $activeimage; ?>);" href="<?php echo get_the_permalink(); ?>">
                    <div class="hp-featured-img-overlay"></div>
                    <h2 class="hp-featured-title"><?php echo get_the_title(); ?></h2>
                    <div class="hp-featured-expand"><img src="<?php print IMAGES;?>/expand-icon.png" /></div>
                    <div class="hp-featured-summary"><?php echo limit_words(get_the_excerpt(),20); ?>...</div>
                    <div class="hp-featured-readmore">READ MORE</div>
                </a>
            </div>
		<?php
		};
		?>
    <?php } else if($featCounter == 6){ ?>
    	<!--Removed for Retailer Sentiment Index Story that is added below.-->
    <?php }; ?>
	<?php $do_not_duplicate[] = $post->ID; ?>
    <?php $featCounter ++; ?>
<?php endwhile; ?>
<?php endif; ?>
<?php wp_reset_query(); ?>

<?php query_posts(array('post_type'=>retailersentiment,'posts_per_page'=> 1,'ignore_sticky_posts' => 1));  ?>
<?php if ( have_posts() ) : while ( have_posts()) : the_post(); ?>
        <div class="block-25 block-h-41">
            <?php 
            $activeimage = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), $size = 'medium-image');
            $activeimage = $activeimage[0];
            ?>
            <?php $activeimagepos = get_post_meta($post->ID, 'post_fifa', true); ?>
            <?php if ($activeimagepos == ""){$activeimagepos = "center center";}?>
            <a class="hp-featured-img-link hp-featured-sub-img-link" style="overflow:hidden; background-position:<?php echo $activeimagepos; ?>; background-image:url(<?php echo $activeimage; ?>);" href="<?php echo get_the_permalink(); ?>">
                <div class="hp-featured-img-overlay"></div>
                <h2 class="hp-featured-title"><?php echo get_the_title(); ?></h2>
                <div class="hp-featured-expand"><img src="<?php print IMAGES;?>/expand-icon.png" /></div>
                <div class="hp-featured-summary"><?php echo limit_words(get_the_excerpt(),12); ?>...</div>
                <div class="hp-featured-readmore">READ MORE</div>
            </a>
        </div>
<?php endwhile; ?>
<?php endif; ?>
<?php wp_reset_query(); ?>