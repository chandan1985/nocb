<?php 
/* Product page load more code end */


 /*------------------------------------------------------------------------------------------------------------------------------------------------*/
 function product_listing_load_more() {
	
	$paged = $_POST['data']['load_more_start'];
	$post_id = $_POST['data']['post_id'];
	$record_per_page = 6;
    	//echo 'Post ID '.$post_id;
	$args_product = [
							'no_found_rows'          => true,
							'order'                  => 'asc',
							'orderby'                => 'name',
							'post_status'            => 'publish',
							'post_type'              => 'pbm-product',
							'posts_per_page'         => $record_per_page,
							'offset' => $paged,
							'update_post_meta_cache' => false,
							'update_post_term_cache' => false,
							// The relationship query is provided by the content-connect plugin.
							'relationship_query' => [
								[
									'related_to_post' => $post_id,
									'name'            => 'company-product',
								]
							],
						];

						$query_product = new WP_Query( $args_product );
						if ( $query_product->have_posts() ) :?>
						   <?php 
						    $loop_cnt = 0;
						    while ( $query_product->have_posts() ) : $query_product->the_post();
							    $loop_cnt = $loop_cnt + 1;
                                //$product_feat_img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'products-feat');
                                $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
                                $thumb = wp_get_attachment_image_url(get_post_thumbnail_id(), 'large');
                                $data = [
                                    'src' => $image[0],
                                    'w' => $image[1],
                                    'h' => $image[2],
                                    'msrc' => $thumb[0],
                                ];
						   ?>
							   <div class="pbm-col pbm-col-half pbm-col-third-md pbm-guide-product-item">
                                    <div class="container container--flush-sm">
                                        <div class="pbm-gallery" itemscope="" itemtype="http://schema.org/ImageGallery" data-pbm-gallery-single="">
                                            <figure
                                                itemprop="associatedMedia"
                                                itemscope
                                                itemtype="http://schema.org/ImageObject"
                                                class="pbm-col pbm-col-md"
                                                data-pbm-thumb="<?php echo esc_attr(wp_json_encode($data)); ?>"
                                                data-pbm-idx="1"
                                                >
                                                <?php if(!empty($image[0])){ ?>
                                                <a href="<?php if(!empty($image[0])){ echo esc_url($image[0]);}else{ ?><?php echo get_template_directory_uri(); ?>/assets/images/placeholder.jpg<?php }?>" class="aspect-ratio aspect-ratio--1-1 gallery__image" itemprop="contentUrl">
                                                    <img src="<?php echo esc_url($thumb); ?>" alt="" class="aspect-ratio__element" itemprop="thumbnail" alt="<?php echo get_the_title();?>" title="<?php echo get_the_title();?>"/>
                                                </a>
                                               <figcaption itemprop="caption description" class="gallery__image-description">
                                                <h3 class="h6"><?php echo get_the_title();?></h3>
                                                   <?php echo get_the_content();?>
                                                </figcaption>
                                                <?php } else { ?>
                                                <a href="<?php if(!empty($image[0])){ echo esc_url($image[0]);}else{ ?><?php echo get_template_directory_uri(); ?>/assets/images/placeholder.jpg<?php }?>" class="aspect-ratio aspect-ratio--1-1 gallery__image" itemprop="contentUrl" >
                                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/placeholder.jpg" alt="" class="aspect-ratio__element" itemprop="thumbnail" alt="<?php echo get_the_title();?>"  title="<?php echo get_the_title();?>"/>
                                                </a>
                                                 <figcaption itemprop="caption description" class="gallery__image-description">
                                                     <figcaption class="content-popup"> <?php echo get_the_content(); ?></figcaption>   
                                                <h3 class="h6">
                                                    <?php echo get_the_title(); ?></h3>
                                                    </figcaption>
                                                         <?php }?>
                                                 <figcaption class="pbm-guide-product-item__content">
                                                    <h3 class="h6">
                                                    <?php echo get_the_title(); ?></h3>
        <?php //echo wpautop(wp_trim_words(get_the_excerpt(), 15)); ?>
                                                 </figcaption>
                                            </figure>
                                           
                                        </div>
                                    </div>


                                </div>
						   <?php endwhile; 
						   wp_reset_postdata(); endif;?>
						   
	<?php
	die();
	
  }
  
add_action( 'wp_ajax_nopriv_product_listing_load_more', 'product_listing_load_more');
add_action('wp_ajax_product_listing_load_more', 'product_listing_load_more');


?>