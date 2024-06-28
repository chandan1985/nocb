<?php if ( have_posts() ) : while ( have_posts()) : the_post(); ?>
		<?php 
			$meta_title = get_the_title($post->ID);
			$meta_deptplink = get_post_meta($post->ID, 'de_ptplink', true);
		?>        
        <div class="art-related-story">
            <a data-pub="<?php echo $meta_title; ?>" data-ptp="<?php echo $meta_deptplink; ?>" href="<?php echo get_the_permalink(); ?>" class="art-related-title ptplinked"><?php echo $meta_title; ?></a>
            <a data-pub="<?php echo $meta_title; ?>" data-ptp="<?php echo $meta_deptplink; ?>" href="<?php echo get_the_permalink(); ?>" class="art-supplement-image ptplinked">
            <?php
				$activeimage = get_needed_image("medium-image");
				if ( has_post_thumbnail() || $activeimage != "" ) {
					echo "<img class='art-related-image dig-edi-image' src='".$activeimage."'>";
				};
			?>
            </a>
            <a data-pub="<?php echo $meta_title; ?>" data-ptp="<?php echo $meta_deptplink; ?>" href="<?php echo get_the_permalink(); ?>" class="art-related-supplement-link ptplinked">View Annual &#9654;</a>
        </div>
	<?php $do_not_duplicate[] = $post->ID; ?>
<?php endwhile; ?>
<?php endif; ?>
<?php wp_reset_query() ?>
