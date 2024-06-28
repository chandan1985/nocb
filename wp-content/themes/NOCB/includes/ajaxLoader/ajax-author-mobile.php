<?php
    // Author mobile pagination section.....
    add_action('wp_ajax_ajax_authorMobile', 'authors_in_mobile');
    add_action( 'wp_ajax_nopriv_ajax_authorMobile', 'authors_in_mobile' );

    function authors_in_mobile() {
        $actual_link = $_SERVER['HTTP_REFERER'];
        $actual_link = array_filter(explode('/', $actual_link));
        $author_name = end($actual_link);
        $user = get_user_by('login', $author_name);
        $author_id = $user->ID;
        if(isset($_POST['offset'])){
            $offset = $_POST['offset'];
            $count = $offset;
        }else{
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
                'post' => 'post',
            ),
            'author'  => $author_id, 
        );
        // $wp_query = new WP_Query($aboutPosts);
        $postsbyid = get_posts($aboutPosts);

        if($postsbyid){
            if(!isset($_POST['offset'])){
                $article_id = [];
                foreach ($postsbyid as $key => $post_id) {
                    $id = $post_id->ID;
                    $title = get_the_title($id);
                    $Short_title = substr($title, 0, 80) . ' [...]';
                    $slug = get_permalink($id);
                    // $post_date = get_the_date('h:i A', $id);
                    // $post_time = get_the_time('g:i a T', $id);
                    $date = $post_id->post_date;
                    $post_date = date('F j, Y', strtotime($date));
                    $post_time = date('g:i a T', strtotime($date));
                    $featured_image = get_the_post_thumbnail($post_id->ID, 'webinar-list-thumb');
                    $content_post = get_post($id);
                    $content = $content_post->post_content;
                    $content = strip_tags($content);
                    $short_content = substr($content, 0, 300)."[...]";
                    $short_content_mobile = substr($content, 0, 180)."[...]";
        
                    $excerpt = get_the_excerpt($id);
                    $short_excerpt = substr($excerpt, 0, 400)."[...]";
                    $short_excerpt_mobile = substr($excerpt, 0, 120)."[...]";
        
                    // $wpseo_primary_term = new WPSEO_Primary_Term( 'category', $id );
                    // $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
                    $wpseo_primary_term = get_primary_category_id($id);
                    $term = get_term( $wpseo_primary_term );
                    $term_name = $term->name;
                    $term_link = get_category_link($term);
        
                    $sponsored_cmpnyId = get_post_meta($id, 'associated_sponsor_id', true);
                    $cmpny_link = get_permalink( $sponsored_cmpnyId);
                    $cmpny_linkArray = explode('/',$cmpny_link);
                    $option_arrays = get_option('DFP_Ads_Settings');
					$option_array = $option_arrays['dfp_property_code'];
        
                    ?>
                    <div class='article-listing' data-id = "<?php print $count;?>">
                        <?php $last_dataId = $count;
                        if ($featured_image) {
                        ?>
                            <div class='article-detail-listing'>
                                <div class='image-section'>
                                    <a href="<?php print $slug; ?>"><?php print $featured_image; ?></a>
                                </div>
                                <div class='content-section'>
                                    <?php
                                        if($sponsored_cmpnyId){
                                            $article_id[] = $id;
                                            $keys = array_search ($id, $article_id);
                                            ?>
                                                <p class="tag-list">
                                                    <script>var postSlot0, postSlot1, postSlot2, postSlot3, postSlot4, postSlot5, postSlot6, postSlot7;
                                                        googletag.cmd.push(function() {
                                                            var postSlot<?php print $keys; ?> = googletag.defineSlot("<?php print $option_array; ?>", [1, 1], "div-gpt-ad-author_mobile-9997770808178-<?php print $keys; ?>").addService(googletag.pubads()).setTargeting("SCmodule",<?php print $id; ?>);
                                                            googletag.pubads().enableSingleRequest();
                                                            googletag.enableServices();
                                                        });
                                                    </script>
                                                </p>
                                            <?php
                                        }
                                    ?>
                                    <h2 class="mobile-show"><a href="<?php print $slug; ?>"><?php echo $title; ?></a></h2>
                                    <div class="content-time-data">
                                        <?php
                                            if($sponsored_cmpnyId){
                                                ?>
                                                    <a href="<?php print $cmpny_link; ?>" class="btn-sponsored">Sponsored</a>
                                                <?php
                                            }
                                            if($term_name){
                                                ?>
                                                    <a href="<?php print $term_link; ?>" class="btn-custom"><?php print $term_name; ?></a>
                                                <?php
                                            }
                                        ?>
                                        <span><?php echo $post_date; ?></span>
                                    </div>
                                    <h2 class="desktop-show"><a href="<?php print $slug; ?>"><?php echo $title; ?></a></h2>
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
                        <?php } else {
                        ?>
                        <div class='content-section'>
                            <?php
                                if($sponsored_cmpnyId){
                                    $article_id[] = $id;
                                    $keys = array_search ($id, $article_id);
                                    ?>
                                        <p class="tag-list">
                                            <script>var postSlot0, postSlot1, postSlot2, postSlot3, postSlot4, postSlot5, postSlot6, postSlot7;
                                                googletag.cmd.push(function() {
                                                    var postSlot<?php print $keys; ?> = googletag.defineSlot("<?php print $option_array; ?>", [1, 1], "div-gpt-ad-author_mobile-9997770808178-<?php print $keys; ?>").addService(googletag.pubads()).setTargeting("SCmodule",<?php print $id; ?>);
                                                    googletag.pubads().enableSingleRequest();
                                                    googletag.enableServices();
                                                });
                                            </script>
                                        </p>
                                    <?php
                                }
                            ?>
                            <h2 class="mobile-show"><a href="<?php print $slug; ?>"><?php echo $title; ?></a></h2>
                            <div class="content-time-data">
                                <?php
                                    if($sponsored_cmpnyId){
                                        ?>
                                            <a href="<?php print $cmpny_link; ?>" class="btn-sponsored">Sponsored</a>
                                       
                                       <?php
                                    }
                                    if($term_name){
                                        ?>
                                            <a href="<?php print $term_link; ?>" class="btn-custom"><?php print $term_name; ?></a>
                                        <?php
                                    }
                                ?>
                                <span><?php echo $post_date; ?></span>
                            </div>
                            <h2 class="desktop-show"><a href="<?php print $slug; ?>"><?php echo $title; ?></a></h2>
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
                         if($key == 3){
                            dynamic_sidebar('category-inline-mobile-ad-row1');
                        }
                        if($key == 7){
                            dynamic_sidebar('category-inline-mobile-ad-row2');
                        }
                    ?>
                <?php
                $count++;
                }
            }else{
                $article_id = [];
                foreach ($postsbyid as $post_id) {
                    $id = $post_id->ID;
                    $title = get_the_title($id);
                    $Short_title = substr($title, 0, 80) . ' [...]';
                    $slug = get_permalink($id);
                    // $post_date = get_the_date('h:i A', $id);
                    // $post_time = get_the_time('g:i a T', $id);
                    $date = $post_id->post_date;
                    $post_date = date('F j, Y', strtotime($date));
                    $post_time = date('g:i a T', strtotime($date));
                    $featured_image = get_the_post_thumbnail($post_id->ID, 'webinar-list-thumb');
                    $content_post = get_post($id);
                    $content = $content_post->post_content;
                    $content = strip_tags($content);
                    $short_content = substr($content, 0, 300)."[...]";
                    $short_content_mobile = substr($content, 0, 180)."[...]";
        
                    $excerpt = get_the_excerpt($id);
                    $short_excerpt = substr($excerpt, 0, 400)."[...]";
                    $short_excerpt_mobile = substr($excerpt, 0, 120)."[...]";
        
                    // $wpseo_primary_term = new WPSEO_Primary_Term( 'category', $id );
                    // $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
                    $wpseo_primary_term = get_primary_category_id($id);
                    $term = get_term( $wpseo_primary_term );
                    $term_name = $term->name;
                    $term_link = get_category_link($term);
        
                    $sponsored_cmpnyId = get_post_meta($id, 'associated_sponsor_id', true);
                    $cmpny_link = get_permalink( $sponsored_cmpnyId);
                    $cmpny_linkArray = explode('/',$cmpny_link);
                    $option_arrays = get_option('DFP_Ads_Settings');
					$option_array = $option_arrays['dfp_property_code'];
        
                    ?>
                    <div class='article-listing' data-id = "<?php print $count;?>">
                        <?php $last_dataId = $count;
                        if ($featured_image) {
                        ?>
                            <div class='article-detail-listing'>
                                <div class='image-section'>
                                    <a href="<?php print $slug; ?>"><?php print $featured_image; ?></a>
                                </div>
                                <div class='content-section'>
                                    <?php
                                        if($sponsored_cmpnyId){
                                            $article_id[] = $id;
                                            $keys = array_search ($id, $article_id);
                                            ?>
                                                <p class="tag-list">
                                                    <script>var postSlot0, postSlot1, postSlot2, postSlot3, postSlot4, postSlot5, postSlot6, postSlot7;
                                                        googletag.cmd.push(function() {
                                                            var postSlot<?php print $keys; ?> = googletag.defineSlot("<?php print $option_array; ?>", [1, 1], "div-gpt-ad-author_mobile-9997770808178-<?php print $keys; ?>").addService(googletag.pubads()).setTargeting("SCmodule",<?php print $id; ?>);
                                                            googletag.pubads().enableSingleRequest();
                                                            googletag.enableServices();
                                                        });
                                                    </script>
                                                </p>
                                            <?php
                                        }
                                    ?>
                                    <h2 class="mobile-show"><a href="<?php print $slug; ?>"><?php echo $title; ?></a></h2>
                                    <div class="content-time-data">
                                        <?php
                                            if($sponsored_cmpnyId){
                                                ?>
                                                    <a href="<?php print $cmpny_link; ?>" class="btn-sponsored">Sponsored</a>
                                                <?php
                                            }
                                            if($term_name){
                                                ?>
                                                    <a href="<?php print $term_link; ?>" class="btn-custom"><?php print $term_name; ?></a>
                                                <?php
                                            }
                                        ?>
                                        <span><?php echo $post_date; ?></span>
                                    </div>
                                    <h2 class="desktop-show"><a href="<?php print $slug; ?>"><?php echo $title; ?></a></h2>
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
                        <?php } else {
                        ?>
                        <div class='content-section'>
                            <?php
                                if($sponsored_cmpnyId){
                                    $article_id[] = $id;
                                    $keys = array_search ($id, $article_id);
                                    ?>
                                        <p class="tag-list">
                                            <script>var postSlot0, postSlot1, postSlot2, postSlot3, postSlot4, postSlot5, postSlot6, postSlot7;
                                                googletag.cmd.push(function() {
                                                    var postSlot<?php print $keys; ?> = googletag.defineSlot("<?php print $option_array; ?>", [1, 1], "div-gpt-ad-author_mobile-9997770808178-<?php print $keys; ?>").addService(googletag.pubads()).setTargeting("SCmodule",<?php print $id; ?>);
                                                    googletag.pubads().enableSingleRequest();
                                                    googletag.enableServices();
                                                });
                                            </script>
                                        </p>
                                    <?php
                                }
                            ?>
                            <h2 class="mobile-show"><a href="<?php print $slug; ?>"><?php echo $title; ?></a></h2>
                            <div class="content-time-data">
                                <?php
                                    if($sponsored_cmpnyId){
                                        ?>
                                            <a href="<?php print $cmpny_link; ?>" class="btn-sponsored">Sponsored</a>
                                        <?php
                                    }
                                    if($term_name){
                                        ?>
                                            <a href="<?php print $term_link; ?>" class="btn-custom"><?php print $term_name; ?></a>
                                        <?php
                                    }
                                ?>
                                <span><?php echo $post_date; ?></span>
                            </div>
                            <h2 class="desktop-show"><a href="<?php print $slug; ?>"><?php echo $title; ?></a></h2>
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
                         if($key == 3){
                            dynamic_sidebar('category-inline-mobile-ad-row1');
                        }
                        if($key == 7){
                            dynamic_sidebar('category-inline-mobile-ad-row2');
                        }
                    ?>
                <?php
                $count++;
                }
            }
            if(isset($_POST['offset'])){
                ?>
                    <button class="loadmore-btn" data-id = "<?php print $last_dataId+1;?>">Load More</button>
                <?php
            }else{
              ?>
                    <button class="loadmore-btn" data-id = "<?php print $last_dataId;?>">Load More</button>
              <?php
            }
        }else{
            print "No posts available.";
        }
        wp_reset_postdata();
        wp_die();
    }
?>