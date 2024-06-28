<?php


/**
* Create a global function for related content on details page.
*/

function asentech_related_contents(){


    if(is_singular('post')){ //Posty type article details page
        global $post;
        global $wp;
        $id = $post->ID;
        $post_type = get_post_type($post->ID);
        $post_type = $post->post_type;
        // $wpseo_primary_term = new WPSEO_Primary_Term( 'category', get_the_id() );
        // $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
        $wpseo_primary_term = get_primary_category_id(get_the_id());
        $term = get_term( $wpseo_primary_term );
        $term_name = $term->name;
        $term_id = $term->term_id;

        // get the first selected category from the post.
        $categories = get_the_terms( get_the_id(), 'category' );
        if($categories){
            $categories = array_shift(array_slice($categories,0,1));
            $category_id =  $categories->term_id;
        }

        $default_term = get_term_by('slug', 'news', 'category');
        

        if($wpseo_primary_term){
            $term_id = $term->term_id;
        }elseif($categories){
            $term_id = $category_id;
        }else{
            $term_id = $default_term->term_id;
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
                    'sponsored_content' => 'sponsored_content',
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
                    'terms' => $term_id,
                    'operator' => 'IN',
                    'include_children' => false,
                    ),
                ),
            );

            $post_ids = get_posts($query);
			if($post_ids){ ?>
			<h2>Related Content</h2>
			<?php } ?>

			<div class="related-content">

			<?php
            $article_id = [];
            foreach ($post_ids as $post_id) {
                $id = $post_id->ID;
                $title = get_the_title($id);
                $title = strip_tags($title);
                $short_title = substr($title, 0, 100)."...";
                $slug = get_permalink( $id);
                $featured_image = get_the_post_thumbnail( $post_id->ID, 'article-list-thumb' );
                $related_content = $post_id->post_content;
                $related_content = preg_replace('/\[caption.*\[\/caption\]/', '', $related_content);
                $related_content = strip_tags($related_content);
                $related_content = preg_replace('/<iframe.*?\/iframe>|https?:\/\/[^\s]+|(\[feed url=".*?" number="\d+"\]|\[.*?\])/', '', $related_content);
                $related_short_content = substr($related_content, 0, 110)."[...]";
                $related_excerpt = $post_id->post_excerpt;
                $related_excerpt = strip_tags($related_excerpt);
                $related_short_excerpt = substr($related_excerpt, 0, 110)."[...]";
                $post_type = $post_id->post_type;
                $post_date = $post_id->post_date;
                $post_date = date('F j, Y', strtotime($post_date));
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
                                    <?php
                                        if( $post_type == "sponsored_content"){ ?>
                                                <p class="btn-sponsored">Sponsored</p>
                                            <?php
                                        }
                                    ?>
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
                                        if($related_excerpt){
                                            if(strlen($related_excerpt) > 110){
                                                print $related_short_excerpt;
                                            }else{
                                                print $related_excerpt;
                                            }
                                        }else{
                                            if(strlen($related_content) > 110){
                                                print $related_short_content;
                                            }else{
                                                print $related_content;
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
    }elseif(is_singular('product')){ //Post type Product details page
        global $post;
        global $wp;
        $id = $post->ID;
        $post_type = get_post_type($post->ID);
        $post_type = $post->post_type;
        // $wpseo_primary_term = new WPSEO_Primary_Term( 'product_category', get_the_id() );
        // $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
        $wpseo_primary_term = get_primary_category_id(get_the_id());
        $term = get_term( $wpseo_primary_term );
        $term_name = $term->name;
        $term_id = $term->term_id;

        // get the first selected category from the post.
        $categories = get_the_terms( get_the_id(), 'product_category' );
        $categories = array_shift(array_slice($categories,0,1));
        $category_id =  $categories->term_id;

        if($wpseo_primary_term){
            $term_id = $term->term_id;
        }else{
            $term_id = $category_id;
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
                'product' => 'product',
            ),
            'post__not_in' => 
                array (
                    0 => $id,
                ),
            'tax_query' => 
            array (
                'taxonomy_category' => 
                array (
                'taxonomy' => 'product_category',
                'field' => 'id',
                'terms' => $term_id,
                'operator' => 'IN',
                'include_children' => false,
                ),
            ),
        );

        $post_ids = get_posts($query);
        // print "<pre>";
        // print_r ($post_ids);
        // print "</pre>";
        if($post_ids){ ?>
        <h2>Related Content</h2>
        <?php } ?>

        <div class="related-content">

        <?php
        $article_id = [];
        foreach ($post_ids as $post_id) {
            $id = $post_id->ID;
            $title = get_the_title($id);
            $title = strip_tags($title);
            $short_title = substr($title, 0, 100)."...";
            $slug = get_permalink( $id);
            $featured_image = get_the_post_thumbnail( $post_id->ID, 'article-list-thumb' );
            $related_content = $post_id->post_content;
            $related_content = strip_tags($related_content);
            $related_content = preg_replace('/<iframe.*?\/iframe>/i','', $related_content);
            $related_content = preg_replace('/\[feed url=".*?" number="\d+"\]/','', $related_content);
            $related_short_content = substr($related_content, 0, 110)."[...]";
            $related_excerpt = $post_id->post_excerpt;
            $related_short_excerpt = substr($related_excerpt, 0, 110)."[...]";
            $post_type = $post_id->post_type;
            $post_date = $post_id->post_date;
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
                                    if($related_excerpt){
                                        if(strlen($related_excerpt) > 110){
                                            print $related_short_excerpt;
                                        }else{
                                            print $related_excerpt;
                                        }
                                    }else{
                                        if(strlen($related_content) > 110){
                                            print $related_short_content;
                                        }else{
                                            print $related_content;
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
        }elseif(is_singular('sponsoredcontent')){ //Post type Sponsored Content details page
            global $post;
            global $wp;
            $id = $post->ID;
            $post_type = get_post_type($post->ID);
            $post_type = $post->post_type;
            // $wpseo_primary_term = new WPSEO_Primary_Term( 'sponsoredcontent_category', get_the_id() );
            // $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
            $wpseo_primary_term = get_primary_category_id(get_the_id());
            $term = get_term( $wpseo_primary_term );
            $term_name = $term->name;
            $term_id = $term->term_id;
    
            // get the first selected category from the post.
            $categories = get_the_terms( get_the_id(), 'sponsoredcontent_category' );
            $categories = array_shift(array_slice($categories,0,1));
            $category_id =  $categories->term_id;
            // $category_name =  $categories->name;
            // $category_slug =  $category->slug;
    
            if($wpseo_primary_term){
                $term_id = $term->term_id;
            }else{
                $term_id = $category_id;
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
                    'sponsoredcontent' => 'sponsoredcontent',
                ),
                'post__not_in' => 
                    array (
                        0 => $id,
                    ),
                'tax_query' => 
                array (
                    'taxonomy_category' => 
                    array (
                    'taxonomy' => 'sponsoredcontent_category',
                    'field' => 'id',
                    'terms' => $term_id,
                    'operator' => 'IN',
                    'include_children' => false,
                    ),
                ),
            );
    
            $post_ids = get_posts($query);
            // print "<pre>";
            // print_r ($post_ids);
            // print "</pre>";
            if($post_ids){ ?>
            <h2>Related Content</h2>
            <?php } ?>
    
            <div class="related-content">
    
            <?php
            $article_id = [];
            foreach ($post_ids as $post_id) {
                $id = $post_id->ID;
                $title = get_the_title($id);
                $title = strip_tags($title);
                $short_title = substr($title, 0, 100)."...";
                $slug = get_permalink( $id);
                $featured_image = get_the_post_thumbnail( $post_id->ID, 'article-list-thumb' );
                $related_content = $post_id->post_content;
                $related_content = strip_tags($related_content);
                $related_content = preg_replace('/<iframe.*?\/iframe>/i','', $related_content);
                $related_content = preg_replace('/\[feed url=".*?" number="\d+"\]/','', $related_content);
                $related_short_content = substr($related_content, 0, 110)."[...]";
                $related_excerpt = $post_id->post_excerpt;
                $related_short_excerpt = substr($related_excerpt, 0, 110)."[...]";
                $post_type = $post_id->post_type;
                $post_date = $post_id->post_date;
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
                                        if($related_excerpt){
                                            if(strlen($related_excerpt) > 110){
                                                print $related_short_excerpt;
                                            }else{
                                                print $related_excerpt;
                                            }
                                        }else{
                                            if(strlen($related_content) > 110){
                                                print $related_short_content;
                                            }else{
                                                print $related_content;
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
            }elseif(is_singular('digital_guides')){ //Post type Sponsored Content details page
                global $post;
                global $wp;
                $id = $post->ID;
                $post_type = get_post_type($post->ID);
                $post_type = $post->post_type;
                // $wpseo_primary_term = new WPSEO_Primary_Term( 'digital_guide', get_the_id() );
                // $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
                $wpseo_primary_term = get_primary_category_id(get_the_id());
                $term = get_term( $wpseo_primary_term );
                $term_name = $term->name;
                $term_id = $term->term_id;
        
                // get the first selected category from the post.
                $categories = get_the_terms( get_the_id(), 'digital_guide' );
                $categories = array_shift(array_slice($categories,0,1));
                $category_id =  $categories->term_id;
                // $category_name =  $categories->name;
                // $category_slug =  $category->slug;
        
                if($wpseo_primary_term){
                    $term_id = $term->term_id;
                }else{
                    $term_id = $category_id;
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
                        'digital_guides' => 'digital_guides',
                    ),
                    'post__not_in' => 
                        array (
                            0 => $id,
                        ),
                    'tax_query' => 
                    array (
                        'taxonomy_category' => 
                        array (
                        'taxonomy' => 'digital_guide',
                        'field' => 'id',
                        'terms' => $term_id,
                        'operator' => 'IN',
                        'include_children' => false,
                        ),
                    ),
                );
        
                $post_ids = get_posts($query);
                // print "<pre>";
                // print_r ($post_ids);
                // print "</pre>";
                if($post_ids){ ?>
                <h2>Related Content</h2>
                <?php } ?>
        
                <div class="related-content">
        
                <?php
                $article_id = [];
                foreach ($post_ids as $post_id) {
                    $id = $post_id->ID;
                    $title = get_the_title($id);
                    $title = strip_tags($title);
                    $short_title = substr($title, 0, 100)."...";
                    $slug = get_permalink( $id);
                    $featured_image = get_the_post_thumbnail( $post_id->ID, 'article-list-thumb' );
                    $related_content = $post_id->post_content;
                    $related_content = strip_tags($related_content);
                    $related_content = preg_replace('/<iframe.*?\/iframe>/i','', $related_content);
                    $pattern = '[feed url="';

                    // Remove the pattern from the content
                    $result = str_replace($pattern, '', $content);

                    // Remove the closing part of the pattern
                    $result = str_replace('" number="3"]', '', $result);
                    $related_short_content = substr($related_content, 0, 110)."[...]";
                    $related_excerpt = $post_id->post_excerpt;
                    $related_short_excerpt = substr($related_excerpt, 0, 110)."[...]";
                    $post_type = $post_id->post_type;
                    $post_date = $post_id->post_date;
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
                                            if($related_excerpt){
                                                if(strlen($related_excerpt) > 110){
                                                    print $related_short_excerpt;
                                                }else{
                                                    print $related_excerpt;
                                                }
                                            }else{
                                                if(strlen($related_content) > 110){
                                                    print $related_short_content;
                                                }else{
                                                    print $related_content;
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