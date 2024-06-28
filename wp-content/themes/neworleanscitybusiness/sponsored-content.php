<?php
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $start = ($paged - 1) * 12;

        // print $start;

        $aboutPosts = array(
            'paged' => $paged,
            'posts_per_page' => '12',
            'offset' => $start,
            'post_status' => 'publish',
            'ignore_sticky_posts' => 0,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_type' =>
            array(
                'sponsored_content' => 'sponsored_content',
            ),
        );
        $custom_query = new WP_Query( $aboutPosts );
        $postsbyid = get_posts($aboutPosts);
        foreach ($postsbyid as $key => $post_id) {
            $id = $post_id->ID;
            $post_type = $post_id->post_type;
            $title = get_the_title($id);
            $Short_title = substr($title, 0, 80) . ' [...]';
            $slug = get_permalink($id);
            $date = $post_id->post_date;
            $post_date = date('F j, Y', strtotime($date));
            $post_time = date('g:i a T', strtotime($date));
            $featured_image = get_the_post_thumbnail($post_id->ID, 'category-list-thumb');
            $content_post = get_post($id);
            $content = $content_post->post_content;
            $content = strip_tags($content);
            $short_content = substr($content, 0, 400)."[...]";

            $excerpt = get_the_excerpt($id);
            $short_excerpt = substr($excerpt, 0, 400)."[...]";
            // print "<pre>";
            // print_r ($id);
            // print "</pre>";
            ?>

            <div class='article-listing'>
                <?php if ($featured_image) {
                ?>
                    <div class='article-detail-listing'>
                        <div class='image-section'>
                            <a href="<?php print $slug; ?>"><?php print $featured_image; ?></a>
                        </div>
                        <div class='content-section'>
                            <span><?php echo $post_date;?></span>
                            <h2><a href="<?php print $slug; ?>"><?php echo $title; ?></a></h2>
                            <p class="article-content desktop-show">
                                <?php
                                    if($excerpt){
                                        if(strlen($excerpt) > 400){
                                            print $short_excerpt;
                                        }else{
                                            print $excerpt;
                                        }
                                    }else{
                                        if(strlen($content) > 400){
                                            print $short_content;
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
                    <span><?php echo $post_date; ?></span>
                    <h2><a href="<?php print $slug; ?>"><?php echo $title;?></a></h2>
                    <p class="article-content desktop-show">
                        <?php
                            if($excerpt){
                                if(strlen($excerpt) > 400){
                                    print $short_excerpt;
                                }else{
                                    print $excerpt;
                                }
                            }else{
                                if(strlen($content) > 400){
                                    print $short_content;
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
            <?php $key === 3 ? "<div class='content-inline-ad-section'>".dynamic_sidebar('content-inline-ad-1')."</div>" : null ?>
            <?php $key === 7 ? "<div class='content-inline-ad-section'>".dynamic_sidebar('content-inline-ad-2')."</div>" : null ?>
        <?php }	wp_reset_postdata();?>
        <div class="pagenavi"><?php  wp_pagenavi( array( 'query' => $custom_query  )); ?></div>