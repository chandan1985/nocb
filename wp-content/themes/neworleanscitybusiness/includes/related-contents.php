<?php


/**
* Create a global function for related content on details page.
*/

function asentech_related_contents(){
    global $post;
    global $wp;
    $id = $post->ID;
    $post_type = get_post_type($post->ID);
    $post_type = $post->post_type;
    $wpseo_primary_term = new WPSEO_Primary_Term( 'category', get_the_id() );
    $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
    $term = get_term( $wpseo_primary_term );
    $term_name = $term->name;
    $term_id = $term->term_id;
	$url =  home_url( $wp->request );
	$values = parse_url($url);
	$path = explode('/',$values['path']);

    if(is_singular('post')){ //Posty type article details page
        ?>
        <h2>Related Content</h2>
        <div class="related-content">
            <?php
            if($term_id){
                $term_id_val = $term_id;
            }else{
                $term_obj = get_term_by('slug', 'news', 'category');
                $term_id_val = $term_obj->term_id;
            }
            // $noOf_contents = get_option('related_contents_select_option');
            $noOf_contents = '6';
            if(wp_is_mobile()){
                $total_content = 2;
            }else{
                $total_content = $noOf_contents;
            }
            $query = array (
                'paged' => 1,
                'posts_per_page' => $total_content,
                'offset' => 0,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 0,
                'orderby' => 'date',
                'order' => 'DESC',
                'post_type' => 
                array (
                    'post' => 'post',
                ),
                'post__not_in' => 
                    array (
                        0 => $id,
                    ),
                'tax_query' => 
                array (
                    'taxonomy_category' => 
                    array (
                    'taxonomy' => 'category',
                    'field' => 'id',
                    'terms' => $term_id_val,
                    'operator' => 'IN',
                    'include_children' => false,
                    ),
                ),
            );

            $post_ids = get_posts($query);
            $article_id = [];
            foreach ($post_ids as $post_id) {
                $id = $post_id->ID;
                $title = get_the_title($id);
                $short_title = substr($title, 0, 100)."...";
                $slug = get_permalink( $id);
                $featured_image = get_the_post_thumbnail( $post_id->ID, 'article-list-thumb' );
                $content = get_the_content($id);
                $content = substr($content, 0, 150);
                $content = strip_tags($content);
                $short_content = substr($content, 0, 110)."[...]";
                $excerpt = get_the_excerpt($id);
                $short_excerpt = substr($excerpt, 0, 110)."[...]";
                $post_type = $post_id->post_type;$post_date = $post_id->post_date;
                $post_date = date('F j, Y', strtotime($post_date));
                // $option_arrays = get_option('DFP_Ads_Settings');
                // $option_array = $option_arrays['dfp_property_code'];
            ?>
                <div class="content-box">
                    <?php
                        if ($featured_image) { 
                        ?>
                        <a href="<?php print $slug; ?>"><div class="rc-wrapper">
                        <?php } else { ?>
                            <a href="<?php print $slug; ?>"><div class="rc-wrapper without-image">
                            <?php } ?>
                                    <div class="rc-img">
                                        <?php print $featured_image; ?>
                                    </div>
                                    <div class="rc-content-wrap">
                                            <?php
                                            if(strlen($title) > 100){
                                                print '<div class="rc_title_wrap"><h3 class="rc-img-title">'.$short_title.'</h3></div>';
                                            }else{
                                                print '<div class="rc_title_wrap"><h3 class="rc-img-title">'.$title.'</h3></div>';
                                            }
                                        ?>
                                        <p class="rc-img-content">
                                            <?php
                                            if($excerpt){
                                                if(strlen($excerpt) > 110){
                                                    print $short_excerpt;
                                                }else{
                                                    print $excerpt;
                                                }
                                            }else{
                                                if(strlen($content) > 110){
                                                    print $short_content;
                                                }else{
                                                    print $content;
                                                }
                                            }
                                        ?></p>
                                        <p><b><?php print $post_date; ?></b></p>
                                    </div>
                                </div>
                            
                        </a>
                        </div></a>
                    <?php } ?>
                </div>   
    <?php
    }
}