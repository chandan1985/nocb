<?php get_header(); ?>	

	<!-- This function figures out what type of archive it is and spits it out as the title in a div class="archive-text" -->
	<?php classic_archive_text(); ?> 
	
	<?php while ( wptouch_have_posts() ) { wptouch_the_post(); ?>

	<div class="<?php wptouch_post_classes(); ?> rounded-corners-8px">

		<div class="title-area">
			<a href="<?php wptouch_the_permalink(); ?>" class="ipad-read-entry"></a>

			<?php if ( classic_use_calendar_icons() || classic_use_thumbnail_icons() ) { ?>
				<?php if ( wptouch_get_comment_count() ) { ?> 
					<div class="comment-bubble <?php if ( wptouch_get_comment_count() > 9 && wptouch_get_comment_count() < 99 ) echo 'double'; elseif ( wptouch_get_comment_count() > 99 ) echo 'triple';  ?>">
						<?php comments_number('0','1','%'); ?>
					</div>
				<?php } ?>
			<?php } ?>

			<?php if ( classic_use_calendar_icons() ) { ?>
				<?php include('calendar-icons.php'); ?>	
			<?php } elseif ( classic_use_thumbnail_icons() ) { ?>
				<img src="<?php wptouch_the_post_thumbnail(); ?>" class="attachment-post-thumbnail default-thumbnail" alt="post thumbnail" />	
			<?php } ?>

			<?php if ( !classic_use_calendar_icons() && classic_show_date_in_posts() ) { ?>
				<div class="post-date">
					<?php wptouch_the_time( 'M j / y' ); ?>
				</div>	
			<?php } ?>		
					
			<a class="h2" href="<?php wptouch_the_permalink(); ?>"><?php wptouch_the_title(); ?></a>
					
			<?php if ( classic_show_author_in_posts() ) { ?>
				<div class="post-author">
					<?php echo sprintf( __( 'by %s', 'wptouch-pro' ), get_the_author() ); ?> 
				</div>
			<?php } ?>
								
			<?php if ( wptouch_has_tags() && classic_show_tags_in_posts() ) { ?>
				<div class="post-tags">
					<?php _e( "Tags", "wptouch-pro" ); ?>: <?php wptouch_the_tags(); ?>
				</div>
			<?php } ?>
				
			<?php if ( wptouch_has_categories() && classic_show_categories_in_posts() ) { ?>
				<div class="post-cats">
					<?php _e( "Categories", "wptouch-pro" ); ?>: <?php wptouch_the_categories(); ?>
				</div>
			<?php } ?>
		</div>

			<?php the_excerpt(); ?>

	</div><!-- .wptouch_posts_classes() -->

	<?php } ?>

	<?php if ( wptouch_has_next_posts_link() ) { ?>
		<?php if ( !classic_is_ajax_enabled() ) { ?>	
			<div class="posts-nav post rounded-corners-8px">
				<div class="left"><?php previous_posts_link( __( "Back", "wptouch-pro" ) ) ?></div>
				<div class="right clearfix"><?php next_posts_link( __( "Next", "wptouch-pro" ) ) ?></div>
			</div>
		<?php } else { ?>
			<a class="load-more-link button" href="<?php echo get_next_posts_page_link(); ?>">
				<span></span><?php _e( "Load More Entries&hellip;", "wptouch-pro" ); ?>
			</a>
		<?php } ?>
	<?php } ?>

<?php get_footer(); ?>