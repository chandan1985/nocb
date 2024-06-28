<?php get_header(); ?>
<?php //tie_setPostViews() ?>
<?php $article_classes = array('post-listing','hnews','hentry','item');?>
<div class="content-wrap">
	<div class="content">
        <?php
        if( !get_post_meta($post->ID,'tie_hide_breadcrumbs',true) ){
            tie_breadcrumbs();
        } else {
            $article_classes[] = 'hide-breadcrumbs';
        }
        if( get_post_meta($post->ID,'tie_hide_title',true) ){
            $article_classes[] = 'hide-title';
        }
        ?>
		
		<?php if ( ! have_posts() ) : ?>
		<div id="post-0" class="post not-found post-listing">
			<h1 class="post-title"><?php _e( 'Not Found', 'tie' ); ?></h1>
			<div class="entry">
				<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'tie' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		</div>
		<?php endif; ?>

		<?php
		$do_not_duplicate = array();

		while ( have_posts() ) : the_post();
			$get_meta = get_post_custom($post->ID);
			if( !empty( $get_meta['tie_review_position'][0] ) ){
				$review_position = $get_meta['tie_review_position'][0] ;
				$rv = $tie_reviews_attr;
			}
			
			if( !empty( $get_meta["tie_sidebar_pos"][0] ) && $get_meta["tie_sidebar_pos"][0] == 'full' ){
				if(tie_get_option( 'columns_num' ) == '2c') $content_width = 955;
				else $content_width = 1160;
			}
		?>
		
		<?php //Above Post Banner
		if(  empty( $get_meta["tie_hide_above"][0] ) ){
			if( !empty( $get_meta["tie_banner_above"][0] ) ) echo '<div class="e3lan-post">' .do_shortcode(htmlspecialchars_decode($get_meta["tie_banner_above"][0])) .'</div>';
			else tie_banner('banner_above' , '<div class="e3lan-post">' , '</div>' );
		}
		?>
				
		<article id="the-post" <?php if( !empty( $rv['review'] ) ) echo $rv['review']; post_class($article_classes); ?>>
			<?php get_template_part( 'includes/post-head' ); // Get Post Head template ?>	

			<div class="post-inner">
             <?php if( !$get_meta["tie_hide_title"][0] ){ ?>
				<h1 class="name post-title entry-title" itemprop="itemReviewed" itemscope itemtype="http://schema.org/Thing"><span itemprop="name"><?php the_title(); ?></span></h1>
             <?php } ?>
                <?php if( !empty( $get_meta['subhead'][0] ) ){ ?>
                    <h2 class="subhead"><?php echo $get_meta['subhead'][0]; ?></h2>
                <?php } ?>

				<?php get_template_part( 'includes/post-meta' ); // Get Post Meta template ?>	
				<div class="entry entry-content">
					<?php if( !empty( $review_position ) && ( $review_position == 'top' || $review_position == 'both'  ) ) tie_get_review('review-top'); ?>

					<?php the_content(); ?>
					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'tie' ), 'after' => '</div>' ) ); ?>
					
					<?php if( !empty( $review_position ) && ( $review_position == 'bottom' || $review_position == 'both' ) ) tie_get_review('review-bottom'); ?>
          <?php if( get_post_type()=='native_ads') { ?>
          <div class="native_ads">
            <span class="native_headline"><h4> Native Headline: </h4> <?php echo get_post_meta($post->ID, 'native_headline', true) ?></span><br/>
            <span class="native_description"><h4> Native Description:</h4> <?php echo get_post_meta($post->ID, 'native_description', true) ?></span><br/>
            <span class="native_author"><h4> Native Author: </h4> <?php echo get_post_meta($post->ID, 'native_author', true) ?></span>
          </div>
          <?php } ?>
          
					<?php edit_post_link( __( 'Edit', 'tie' ), '<span class="edit-link">', '</span>' ); ?>
				</div><!-- .entry /-->
				<?php the_tags( '<span style="display:none">',' ', '</span>'); ?>
                <span style="display:none" class="updated" title="<?php the_time('Y-m-d');echo 'T';the_time('H:i:s');echo 'Z'; ?>"><?php the_time('g:i a D, F j, Y'); ?></span>
                <?php
                $ap_userids = explode(',', get_option('tdc_jarida_ap_userids'));
                $pub_userids = explode(',', get_option('tdc_jarida_pub_userids'));

                $author_class = 'vcard author';
                if (in_array(get_the_author_meta('ID'),$ap_userids) || in_array(get_the_author_meta('ID'),$pub_userids)) {
                    $author_class .= ' source-org';
                } else {
                    echo '<span class="source-org vcard" style="display:none;"><a class="org fn" href="'.get_bloginfo('url').'">'.get_bloginfo('name').'</a></span>';
                }

                if (get_option('tdc_jarida_ap_bug')) {
                    if (in_array(get_the_author_meta('ID'),$ap_userids)) {
                        echo '<p class="ap-license"><a rel="item-license" href="#APRights" id="APRights">Copyright '.date('Y').' The Associated Press. All rights reserved. This material may not be published, broadcast, rewritten, or redistributed.</a></p>';
                        echo '<span class="ap-bug" style="display:none;"><img src="http://analytics.apnewsregistry.com/analytics/v2/image.svc/AP/RWS/'.get_option('tdc_jarida_pub_domain').'/MAI/post-'.get_the_ID().'"></span>';
                    } else {
                        $our_content = get_post_meta($post->ID, 'we_own_it');
                        if ($our_content[0] == 'Yes') {
                            echo '<span class="ap-bug" style="display:none;"><img src="http://analytics.apnewsregistry.com/analytics/v2/image.svc/'.get_option('tdc_jarida_pub_code').'/RWS/'.get_option('tdc_jarida_pub_domain').'/CAI/post-'.get_the_ID().'"></span>';
                        }
                    }
                }
                ?>
				<?php if ( get_the_author_meta( 'google' ) ){ ?>
				<div style="display:none" class="<?php echo $author_class; ?>" itemprop="author" itemscope itemtype="http://schema.org/Person"><strong class="fn" itemprop="name"><a href="<?php the_author_meta( 'google' ); ?>?rel=author">+<?php echo get_the_author(); ?></a></strong></div>
				<?php }else{ ?>
				<div style="display:none" class="<?php echo $author_class; ?>" itemprop="author" itemscope itemtype="http://schema.org/Person"><strong class="fn" itemprop="name"><?php the_author_posts_link(); ?></strong></div>
				<?php } ?>
				
				<?php if( ( tie_get_option( 'share_post' ) &&  empty( $get_meta["tie_hide_share"][0] ) ) || $get_meta["tie_hide_share"][0] == 'no' ) get_template_part( 'includes/post-share' ); // Get Share Button template ?>
				
			</div><!-- .post-inner -->
		</article><!-- .post-listing -->
		<?php if( tie_get_option( 'post_tags' ) ) the_tags( '<p class="post-tag">'.__( 'Tagged with: ', 'tie' )  ,' ', '</p>'); ?>
        <?php the_terms( $post->ID, 'issues', '<p class="post-issue post-tag">'.__( 'Issue: ', 'tie' ), ' ', '</p>' ); ?>

		
		<?php //Below Post Banner
		if( empty( $get_meta["tie_hide_below"][0] ) ){
			if( !empty( $get_meta["tie_banner_below"][0] ) ) echo '<div class="e3lan-post">' .do_shortcode(htmlspecialchars_decode($get_meta["tie_banner_below"][0])) .'</div>';
			else tie_banner('banner_below' , '<div class="e3lan-post">' , '</div>' );
		}
		?>
		
		<?php if( tie_get_option( 'post_nav' ) ): ?>				
		<div class="post-navigation">
			<div class="post-previous"><?php previous_post_link( '%link', '<span>'. __( 'Previous:', 'tie' ).'</span> %title' ); ?></div>
			<div class="post-next"><?php next_post_link( '%link', '<span>'. __( 'Next:', 'tie' ).'</span> %title' ); ?></div>
		</div><!-- .post-navigation -->
		<?php endif; ?>
		
		<?php if( ( tie_get_option( 'post_authorbio' ) && empty( $get_meta["tie_hide_author"][0] ) ) || ( isset( $get_meta["tie_hide_related"][0] ) && $get_meta["tie_hide_author"][0] == 'no' ) ): ?>		
		<section id="author-box">
			<div class="block-head">
				<h3><?php _e( 'About', 'tie' ) ?> <?php the_author() ?> </h3>
			</div>
			<div class="post-listing">
				<?php tie_author_box() ?>
			</div>
		</section><!-- #author-box -->
		<?php endif; ?>

		<?php get_template_part( 'includes/post-related' ); // Get Related Posts template ?>	

		<?php endwhile;?>

        <?php if( !$get_meta["tie_hide_comments"][0] ){ ?>
        <?php comments_template( '', true ); ?>
        <?php } ?>
	
	</div><!-- .content -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>