<?php
/*
Template Name: Partner Content
*/

// data is coming from plugin
$footer_bottom_ad = get_option('footer_bottom_ad');
$mobile_footer_bottom_ad = get_option('mobile_footer_bottom_ad');
$footer_hide_from_pages = [];
$footer_hide_from_pages[] = get_option('footer_hide_from_pages');
$footer_hide_from_post_type = [];
$footer_hide_from_post_type[] = get_option('footer_hide_from_post_type');
global $post;
$current_post_type = get_post_type($post);
?>
<?php get_header(); ?>
<div class='breadcrumb-section container'>
	<?php asentech_custom_breadcrumb(); ?>
</div>
<div class="heading-section container-fluid">
	<div class="container">
		<h1 class="page-heading"><?php the_title();  ?></h1>
	</div>
</div>
<div class="container">
  <div class="content-wrap">
    <div class="content categorised-content-listing">
      <?php //tie_breadcrumbs() ?>
      <?php
        $cat_image = apply_filters( 'taxonomy-images-queried-term-image', '', array('image_size' => 'full') );
        $cat_title = $cat_image?$cat_image:'<div>'.single_cat_title( '', false ).'</div>';
        ?>
      <!--<div class="page-head">
          <h1 class="page-title">-->
            <?php //the_title(); ?>
          <!--</h1>
        </div>-->
        
        <div class="page-head category-template">
        <?php if( tie_get_option( 'category_rss' ) ): ?>
        <a class="rss-cat-icon ttip" title="<?php _e( 'Feed Subscription', 'tie' ); ?>" href="<?php echo get_category_feed_link($category_id) ?>"><?php _e( '', 'tie' ); ?></a>
        <?php endif; ?>
        <div class="stripe-line"></div>

        <?php
        if( tie_get_option( 'category_desc' ) ):	
          $category_description = category_description();
          if ( ! empty( $category_description ) )
          echo '<div class="clear"></div><div class="archive-meta">' . $category_description . '</div>';
        endif;
        ?>
      </div>
        <div class="row">
          <div class="col-sm-9">
            <div class="post-listing">
            <!-- PostDisplay from Native ads-->
            <?php
              $post_types = get_post_types('','names');
              $myposts = get_posts(array('post_type' => 'native_ads','order' => 'Desc','posts_per_page' => -1));
              $myposts2 = get_posts(array('post_type' => 'sponsored_content','order' => 'Desc','posts_per_page' => -1));
              $myposts1 = get_posts(array('category_name' => 'sponsored, sponsored-blogs','posts_per_page' => -1));
              $ids = array_merge($myposts, $myposts1, $myposts2);
              $postids = array();
              foreach( $ids as $item ) {
              $postids[]=$item->ID; //create a new query only of the post ids
              }
              $uniqueposts = array_unique($postids);
              
              $paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
              
            $wp_query = new WP_Query(array('paged' => $paged,'post__in' => $uniqueposts,'post_type' => array('post', 'native_ads', 'sponsored_content'),'post_status' => 'publish',
              'posts_per_page' => 10 ));
        
              foreach( $wp_query->posts as $key => $post ) {
              setup_postdata($post);?>
              <article class="item-list article-listing">
                  <?php //get_template_part( 'includes/archives-meta' ); ?>	
                  <div class="article-detail-listing">
                    <?php if( has_post_thumbnail()) { ?>
                      <div class="image-section">
                        <div class="post-thumbnail single-post-thumb archive-wide-thumb">
                          <a href="<?php the_permalink(); ?>"  rel="bookmark"><?php the_post_thumbnail(); ?></a>
                        </div>
                      </div>
                    <?php } ?>
                    <div class="content-section">
                      <h2 class="post-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
                      <p class="post-meta">
                        <?php if( get_post_type() == 'sponsored_content' ){
                          $sponsor = get_post_meta($post->ID, 'meta_sc_sponsor', true); ?>
                          <span> <strong>FROM OUR PARTNER : </strong><?php echo $sponsor; ?> </span>
                        <?php } else { ?>
                          <span class="post-meta-author"><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) )?>" title="<?php sprintf( esc_attr__( 'View all posts by %s', 'tie' ), get_the_author() ) ?>"><?php echo get_the_author() ?> </a></span>
                        <?php } ?>
                        <?php if( tie_get_option( 'arc_meta_date' ) ): ?>		
                          <?php tie_get_time() ?>
                        <?php endif; ?>	
                        <?php if( tie_get_option( 'arc_meta_cats' ) ): ?>
                          <span class="post-cats"><?php printf('%1$s', get_the_category_list( ', ' ) ); ?></span>
                        <?php endif; ?>	
                        <?php if( tie_get_option( 'arc_meta_views' ) ) echo tie_views(); ?>
                      </p>
                      <div class="entry">
                        <p><?php the_excerpt() ?></p>
                        <a class="more-link" href="<?php the_permalink() ?>"><?php _e( 'Read More &raquo;', 'tie' ) ?></a>
                      </div>
                    </div>
                    <div class="clear"></div> 
                  </div>
              </article><!-- .item-list -->
              <?php $key === 3 ? "<div class='content-inline-ad-section'>".dynamic_sidebar('content-inline-ad-1')."</div>" : null ?>
              <?php $key === 7 ? "<div class='content-inline-ad-section'>".dynamic_sidebar('content-inline-ad-2')."</div>" : null ?>
            <?php } ?>
            </div><!-- .post-listing -->
            <div class="pagenavi"><?php echo wp_pagenavi();?></div>
            <?php
            //$total1 = ceil(count($wp_query->post_count) / 5);
            //if ($wp_query->post_count > 1) tie_pagenavi($wp_query,$total1); ?>
            <div class="right-sidebar-mobile mt-4">
              <?php
                if(wp_is_mobile()){
                  dynamic_sidebar('article-right-sidebar-mobile'); 
                }
              ?>
            </div>
          </div>
          <div class="col-sm-3 desktop-show">
            <?php dynamic_sidebar('right-sidebar-area'); ?>
          </div>
        </div>
    </div><!-- .content -->

    <?php if(!in_array($current_post_type, $footer_hide_from_post_type) && !in_array(get_the_ID(), $footer_hide_from_pages)){ ?>
			<div class="row">
				<div class="col-sm-9">
					<div class='footer-bottom-ad-section'>
					<?php
						if(wp_is_mobile()){
							print do_shortcode($mobile_footer_bottom_ad); 
						}else{
							print do_shortcode($footer_bottom_ad); 
						} 
					?>
				</div>
			</div>
				<div class="col-sm-3"></div>
			</div>

		<?php } ?>

  </div>
</div>
<?php get_footer(); ?>
