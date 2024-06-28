<?php if ( have_posts() ) : while ( have_posts()) : the_post(); ?>
		<?php 
			$meta_title = get_the_title($post->ID);
			$meta_deptplink = get_post_meta($post->ID, 'de_ptplink', true);
		?>
		<div class="art-related-story">
            <a data-pub="<?php echo $meta_demonth; ?> <?php echo $meta_deyear; ?>" data-ptp="<?php echo $meta_deptplink; ?>" href="<?php echo get_the_permalink(); ?>" class="art-related-title ptplinked <?php echo $meta_demonth; ?>"><?php echo $meta_title; ?></a>
            <a data-pub="<?php echo $meta_demonth; ?> <?php echo $meta_deyear; ?>" data-ptp="<?php echo $meta_deptplink; ?>" href="<?php echo get_the_permalink(); ?>" class="ptplinked">
            <?php
				$activeimage = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), $size = 'medium-image');
				$activeimage = $activeimage[0];
				if ( has_post_thumbnail() || $activeimage != "" ) {
					echo "<img class='art-related-image dig-edi-image' src='".$activeimage."'>";
				};
			?>
            </a>
            <a data-pub="<?php echo $meta_demonth; ?> <?php echo $meta_deyear; ?>" data-ptp="<?php echo $meta_deptplink; ?>" href="<?php echo get_the_permalink(); ?>" class="art-related-title ptplinked">View This Annual &#9654;</a>
        </div>
<?php endwhile; ?>
<?php endif; ?>
<?php wp_reset_query() ?>