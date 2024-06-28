<?php 
if ( have_posts() ) : 
	if($pagenum != 0){include "pagination.php";}
	while ( have_posts()) : the_post(); ?>
<?php 
if (($post->ID) == 6387){
}
else{
		$fieldarr = array('dir_contact_name','dir_street_address','dir_city','dir_state','dir_zip','dir_phone','dir_fax','dir_website','dir_ages_served','dir_youtube_video_id','dir_vimeo_video_id','dir_media_type','dir_facebook_link','dir_longitude','dir_latitude');
		foreach ($fieldarr as $field){
			${'meta_'.$field} = get_post_meta($post->ID, $field, true);
		}
		$pattern = "/(.*)[a-z](Not Available)/";
		preg_match($pattern,$meta_dir_website,$webmatches);
		if (!isset($webmatches[1])){
			$webmatches[1] = $meta_dir_website;
			if ($webmatches[1] == "" || $webmatches[1] == "Not Available" ){
				$webmatches[1] = "Website Not Available";
			};
		};
		?>
        <div class="directories-listing-<?php echo $meta_dir_media_type; ?> clearfix">
			 
				<a href="<?php the_permalink(); ?>" class="landing-sub-image bor-defaultcat bor-<?php echo $categoryCSS; ?>">
					<?php $activeimage = get_needed_image("category-image-hard");?>
					<?php if ($activeimage != ""){ ?>
						<img class="dir-details-image" src="<?php echo $activeimage; ?>" />
					<?php }
					?>
				</a>
				<a href="<?php the_permalink(); ?>" class="landing-sub-title"><?php the_title(); ?></a>
				<p class="landing-sub-summary dir-details">
				<?php 
					echo $meta_dir_street_address."<br>";
					echo $meta_dir_city."&nbsp;";
					echo $meta_dir_state.",&nbsp;";
					echo $meta_dir_zip."<br>";
				?>
				</p>
				<p class="landing-sub-summary dir-details">
				<?php 
					echo $meta_dir_phone."<br>";
				?>
				</p>
				<p class="landing-sub-summary dir-details">
				<?php 
					echo "<a href='".$webmatches[1]."' target='_blank' rel='nofollow'>".$webmatches[1]."</a><br>";
				?>
				</p>
				<p class="landing-sub-summary dir-details"><?php $excerpt = get_the_excerpt(); echo $excerpt; ?><a class="read-more-link" href="<?php the_permalink(); ?>"><?php echo $linktext; ?></a></p>
                <a class="directories-listing-button" href="<?php the_permalink() ?>" target="_self">View This Listing &rarr;</a>
                
                <?php 
				if($meta_dir_media_type == "3-featured" && $meta_dir_latitude != "" && $meta_dir_longitude != ""){
					$innerarray = array(get_the_title(),$meta_dir_latitude,$meta_dir_longitude,$meta_dir_website_full,$meta_dir_phone,get_the_permalink());
					$map_coords[] = $innerarray;
					
                };
				
				?>
				<?php $do_not_duplicate[] = $post->ID; ?>
        </div>
<?php }; ?>
<?php endwhile; ?>
<?php include "pagination.php"; ?>
<?php endif; ?>