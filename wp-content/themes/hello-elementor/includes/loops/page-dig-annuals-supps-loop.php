<?php if ( have_posts() ) : while ( have_posts()) : the_post(); ?>
		<?php 
			$meta_demonth = get_post_meta($post->ID, 'de_month', true);
			$meta_deyear = get_post_meta($post->ID, 'de_year', true);
			$meta_deptplink = get_post_meta($post->ID, 'de_ptplink', true);
		?>        
        <div class="art-related-story">
            <a data-pub="<?php echo $meta_title; ?>" data-ptp="<?php echo $meta_deptplink; ?>" href="<?php echo get_the_permalink(); ?>" style="text-transform:capitalize" class="art-related-title ptplinked"><?php echo $meta_demonth; ?> <?php echo $meta_deyear; ?></a>
            <a data-pub="<?php echo $meta_title; ?>" data-ptp="<?php echo $meta_deptplink; ?>" href="<?php echo get_the_permalink(); ?>" class="art-supplement-image ptplinked">
            <?php
				$activeimage = get_needed_image("medium-image");
				if ( has_post_thumbnail() || $activeimage != "" ) {
					echo "<img class='art-related-image dig-edi-image' src='".$activeimage."'>";
				};
			?>
            </a>
            <a data-pub="<?php echo $meta_title; ?>" data-ptp="<?php echo $meta_deptplink; ?>" href="<?php echo get_the_permalink(); ?>" class="art-related-supplement-link ptplinked">View Issue &#9654;</a>
        </div>
	<?php $do_not_duplicate[] = $post->ID; ?>
<?php endwhile; ?>
<?php endif; ?>
<?php wp_reset_query() ?>
