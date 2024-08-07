<?php
    // Tag mobile pagination section.....
    add_action('wp_ajax_ajax_tagMobile', 'load_more_tag_in_mobile');
    add_action( 'wp_ajax_nopriv_ajax_tagMobile', 'load_more_tag_in_mobile' );

    function load_more_tag_in_mobile() {

        $actual_link = $_SERVER['HTTP_REFERER'];
        $actual_link = array_filter(explode('/', $actual_link));
        $term_slug = end($actual_link);
        $term = get_term_by('slug', $term_slug, 'post_tag');
        $term_id = $term->term_id;
        if(isset($_POST['offset'])){
            $offset = $_POST['offset'];
            $count = $offset;
        }else{
            // print "no offset value";
            $offset = 0;
            $count = 1;
        }
        $aboutPosts = array(
            'paged' => 1,
            'posts_per_page' => '12',
            'offset' => $offset,
            'post_status' => 'publish',
            'ignore_sticky_posts' => 0,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_type' =>
            array(
                'sponsored_content' => 'sponsored_content',
                'post' => 'post',
            ),
            'tax_query' => 
            array (
                'taxonomy_post_tag' => 
                array (
                'taxonomy' => 'post_tag',
                'field' => 'id',
                'terms' => $term_id,
                'operator' => 'IN',
                'include_children' => false,
                ),
            ),
        );
        
        $postsbyid = get_posts($aboutPosts);
        if(!isset($_POST['offset'])){
            $article_id = [];
            foreach ($postsbyid as $key => $post_id) {
                $id = $post_id->ID;
                $post_type = $post_id->post_type;
                $title = get_the_title($id);
                $Short_title = substr($title, 0, 80) . ' [...]';
                $slug = get_permalink($id);
                $date = $post_id->post_date;
                $post_date = date('M j, Y', strtotime($date));
                $post_time = date('g:i a T', strtotime($date));
                $featured_image = get_the_post_thumbnail($post_id->ID, 'category-list-thumb');
                $content_post = get_post($id);
                $content = $content_post->post_content;
                $content = strip_tags($content);
                $short_content = substr($content, 0, 400)."[...]";
                $short_content_mobile = substr($content, 0, 120)."[...]";
    
                $excerpt = get_the_excerpt($id);
                $short_excerpt = substr($excerpt, 0, 400)."[...]";
                $short_excerpt_mobile = substr($excerpt, 0, 120)."[...]";

                // $wpseo_primary_term = new WPSEO_Primary_Term( 'category', $id );
                // $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
                // $term = get_term( $wpseo_primary_term );
                // $term_name = $term->name;
                // $term_link = get_category_link($term);
    
            ?>
                <div class='article-listing' data-id = "<?php print $count;?>">
                    <?php 
                        $last_dataId = $count;
                        if ($featured_image) {
                    ?>
                        <div class='article-detail-listing'>
                            <div class='image-section'>
                                <a href="<?php print $slug; ?>"><?php print $featured_image; ?></a>
                            </div>
                            <div class='content-section'>
                                <span><?php echo $post_date; ?></span>
                                <?php
                                    if( $post_type == "sponsored_content"){ ?>
                                            <p class="btn-sponsored">Sponsored</p>
                                        <?php
                                    }
                                ?>
                                <h2><a href="<?php print $slug; ?>"><?php echo $title;?></a></h2>
                                <p class="article-content mobile-show">
                                    <?php
                                        if($excerpt){
                                            if(strlen($excerpt) > 120){
                                                print $short_excerpt_mobile;
                                            }else{
                                                print $excerpt;
                                            }
                                        }else{
                                            if(strlen($content) > 120){
                                                print $short_content_mobile;
                                            }else{
                                                print $content;
                                            }
                                        }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <?php } else { ?>
                        <div class='content-section'>
                            <span><?php echo $post_date; ?></span>
                            <?php
                                if( $post_type == "sponsored_content"){ ?>
                                        <p class="btn-sponsored">Sponsored</p>
                                    <?php
                                }
                            ?>
                            <h2><a href="<?php print $slug; ?>"><?php echo $title;?></a></h2>
                            <p class="article-content mobile-show">
                                <?php
                                    if($excerpt){
                                        if(strlen($excerpt) > 120){
                                            print $short_excerpt_mobile;
                                        }else{
                                            print $excerpt;
                                        }
                                    }else{
                                        if(strlen($content) > 120){
                                            print $short_content_mobile;
                                        }else{
                                            print $content;
                                        }
                                    }
                                ?>
                            </p>
                        </div>
                    <?php }
                    ?>
    
                </div>
            <?php
                $count++;
                    if($key == 3){
                        dynamic_sidebar('category-inline-mobile-ad-row1');
                    }
                    if($key == 7){
                        dynamic_sidebar('category-inline-mobile-ad-row2');
                    }
            }
        }else{
            // die();
            $article_id = [];
            foreach ($postsbyid as $key => $post_id) {
                $id = $post_id->ID;
                $post_type = $post_id->post_type;
                $title = get_the_title($id);
                $Short_title = substr($title, 0, 80) . ' [...]';
                $slug = get_permalink($id);
                $date = $post_id->post_date;
                $post_date = date('M j, Y', strtotime($date));
                $post_time = date('g:i a T', strtotime($date));
                $featured_image = get_the_post_thumbnail($post_id->ID, 'category-list-thumb');
                $content_post = get_post($id);
                $content = $content_post->post_content;
                $content = strip_tags($content);
                $short_content = substr($content, 0, 400)."[...]";
                $short_content_mobile = substr($content, 0, 120)."[...]";
    
                $excerpt = get_the_excerpt($id);
                $short_excerpt = substr($excerpt, 0, 400)."[...]";
                $short_excerpt_mobile = substr($excerpt, 0, 120)."[...]";

                // $wpseo_primary_term = new WPSEO_Primary_Term( 'category', $id );
                // $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
                // $term = get_term( $wpseo_primary_term );
                // $term_name = $term->name;
                // $term_link = get_category_link($term);

    
            ?>
                <div class='article-listing' data-id = "<?php print $count;?>">
                    <?php 
                        $last_dataId = $count;
                        if ($featured_image) {
                    ?>
                        <div class='article-detail-listing'>
                            <div class='image-section'>
                                <a href="<?php print $slug; ?>"><?php print $featured_image; ?></a>
                            </div>
                            <div class='content-section'>
                                <span><?php echo $post_date; ?></span>
                                <?php
                                    if( $post_type == "sponsored_content"){ ?>
                                            <p class="btn-sponsored">Sponsored</p>
                                        <?php
                                    }
                                ?>
                                <h2><a href="<?php print $slug; ?>"><?php echo $title;?></a></h2>
                                <p class="article-content mobile-show">
                                    <?php
                                        if($excerpt){
                                            if(strlen($excerpt) > 120){
                                                print $short_excerpt_mobile;
                                            }else{
                                                print $excerpt;
                                            }
                                        }else{
                                            if(strlen($content) > 120){
                                                print $short_content_mobile;
                                            }else{
                                                print $content;
                                            }
                                        }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <?php } else { ?>
                        <div class='content-section'>
                            <span><?php echo $post_date; ?></span>
                            <?php
                                if( $post_type == "sponsored_content"){ ?>
                                        <p class="btn-sponsored">Sponsored</p>
                                    <?php
                                }
                            ?>
                            <h2><a href="<?php print $slug; ?>"><?php echo $title;?></a></h2>
                            <p class="article-content mobile-show">
                                <?php
                                    if($excerpt){
                                        if(strlen($excerpt) > 120){
                                            print $short_excerpt_mobile;
                                        }else{
                                            print $excerpt;
                                        }
                                    }else{
                                        if(strlen($content) > 120){
                                            print $short_content_mobile;
                                        }else{
                                            print $content;
                                        }
                                    }
                                ?>
                            </p>
                        </div>
                    <?php }
                    ?>
    
                </div>
            <?php
                $count++;
                if($key == 4){
                    dynamic_sidebar('category-inline-mobile-ad-row1');
                }
                if($key == 7){
                    dynamic_sidebar('category-inline-mobile-ad-row2');
                }
            }
        }
        
        if(isset($_POST['offset'])){
            ?>
                <button class="loadmore-btn" data-id = "<?php print $last_dataId+1;?>">Load More</button>
                <div class="category-footer-mobile"><?php dynamic_sidebar('category-footer-mobile');  ?></div> 
            <?php
        }else{
          ?>
                <button class="loadmore-btn" data-id = "<?php print $last_dataId;?>">Load More</button>
                <div class="category-footer-mobile"><?php dynamic_sidebar('category-footer-mobile');  ?></div> 
          <?php
        }
        ?>
        <?php
        wp_reset_postdata();
        wp_die(); 


    }
?>