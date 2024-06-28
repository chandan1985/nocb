<?php

/**
 * Register Tab Widgets.
 *
 * @package Mag_Lite
 */
function mag_lite_action_home_tab() {
    register_widget('mag_lite_home_tab');
}

add_action('widgets_init', 'mag_lite_action_home_tab');

class mag_lite_home_tab extends WP_Widget {

    function __construct() {
        global $control_ops;
        $widget_ops = array(
            'classname' => '',
            'description' => esc_html__('Add Widget to Display Tab Section .', 'mag-lite')
        );
        parent::__construct('mag_lite_home_tab', esc_html__('ML: Home Tab Section', 'mag-lite'), $widget_ops, $control_ops);
    }

    function form($instance) {
        $instance = wp_parse_args((array) $instance, array(
            'category' => '',
            'number' => 10,
            'category_news' => '',
            'number_news' => 10,
            'show_post_meta' => true,
            'layout' => '',
        ));
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $category = isset($instance['category']) ? absint($instance['category']) : 0;
        $number = isset($instance['number']) ? absint($instance['number']) : 10;
        $category_news = isset($instance['category_news']) ? absint($instance['category_news']) : 0;
        $number_news = isset($instance['number_news']) ? absint($instance['number_news']) : 10;
        $show_post_meta = isset($instance['show_post_meta']) ? (bool) $instance['show_post_meta'] : true;
        $layout = isset($instance['layout']) ? $instance['layout'] : 1;
        ?>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
              <?php echo esc_html__('Title:', 'mag-lite'); ?>
          </label>
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('category')); ?>">
          <?php esc_html_e('Category:', 'mag-lite'); ?>
          </label>
          <?php
          wp_dropdown_categories(array(
              'show_option_none' => '',
              'class' => 'widefat',
              'show_option_all' => esc_html__('Choose Options', 'mag-lite'),
              'name' => esc_attr($this->get_field_name('category')),
              'selected' => absint($category),
          ));
          ?>
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('number')); ?>">
              <?php echo esc_html__('Choose Number', 'mag-lite'); ?>
          </label>
          <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>" max="4" />
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('category_news')); ?>">
          <?php esc_html_e('Category:', 'mag-lite'); ?>
          </label>
          <?php
          wp_dropdown_categories(array(
              'show_option_none' => '',
              'class' => 'widefat',
              'show_option_all' => esc_html__('Choose Options', 'mag-lite'),
              'name' => esc_attr($this->get_field_name('category_news')),
              'selected' => absint($category_news),
          ));
          ?>
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('number_news')); ?>">
              <?php echo esc_html__('Choose Number', 'mag-lite'); ?>
          </label>
          <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number_news')); ?>" name="<?php echo esc_attr($this->get_field_name('number_news')); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number_news); ?>" max="4" />
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('layout')); ?>">
        <?php echo esc_html__('Choose Layout', 'mag-lite'); ?>
          </label>
          <select class="widefat" id="<?php echo esc_attr($this->get_field_id('layout')); ?>" name="<?php echo esc_attr($this->get_field_name('layout')); ?>">
            <option value="1" <?php echo ($layout == 1) ? "selected='selected'" : ''; ?> >Normal</option>
            <option value="2"  <?php echo ($layout == 2) ? "selected='selected'" : ''; ?>>Verticle</option>
          </select>
        </p>
        <p><input class="checkbox" type="checkbox"<?php checked($show_post_meta); ?> id="<?php echo esc_attr($this->get_field_id('show_post_meta')); ?>" name="<?php echo esc_attr($this->get_field_name('show_post_meta')); ?>" />
          <label for="<?php echo esc_attr($this->get_field_id('show_post_meta')); ?>"><?php echo esc_html__('Enable Post Meta', 'mag-lite'); ?></label></p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['category'] = absint($new_instance['category']);
        $instance['number'] = (int) $new_instance['number'];
        $instance['category_news'] = absint($new_instance['category_news']);
        $instance['number_news'] = (int) $new_instance['number_news'];
        $instance['show_post_meta'] = (bool) $new_instance['show_post_meta'];
        $instance['layout'] = (int) $new_instance['layout'];
        return $instance;
    }

    function widget($args, $instance) {
        extract($args);
        $title = (!empty($instance['title']) ) ? esc_html($instance['title']) : '';
        $category = isset($instance['category']) ? $instance['category'] : '';
        $number = (!empty($instance['number']) ) ? absint($instance['number']) : 40;
        $category_news = isset($instance['category_news']) ? $instance['category_news'] : '';
        $number_news = (!empty($instance['number_news']) ) ? absint($instance['number_news']) : 40;
        $show_post_meta = isset($instance['show_post_meta']) ? $instance['show_post_meta'] : true;
        $layout = isset($instance['layout']) ? $instance['layout'] : 1;
        $total_number = $number_news + $number;
        if ($total_number > 5) {
            $total_number = 40;
        }
        $category_title = get_cat_name($category);
        $category_news_title = get_cat_name($category_news);
        //if (!empty($category) || !empty($category_news)): echo $before_widget;
        ?>
        <?php //if (!empty($category) && !empty($category_news)) {  ?>
			<?php
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			$all_args = array('posts_per_page' => 5, 'post_type' => 'post', 'post_status' => 'publish', 'order' => 'ASC' );
			if (absint($category) > 0 || absint($category_news) > 0) {
			$all_args['cat'] = absint($category);
			}
			$categories = get_the_terms($post->ID, $category_news);
			$category_ID = get_cat_ID($category_news_title);
			$category = $category_ID;
			$countposts = get_posts("category=$category");
			$all_query = new WP_Query($all_args);
			
			if ($all_query->have_posts()) : $cn = 1;
			
			$posts = $all_query->posts;
						
			echo "<h3>" . $category_news_title . "</h3>";
			if ($layout == 2)
			$this->showVerticleLayout($posts);
			else
			$this->showNormalLayout($posts);
			
			?>
			
			
			<?php endif;
			echo $after_widget;
			//endif;
			}

    function showNormalLayout($posts) {
        global $post;
        //foreach($posts as $post)
		   //echo count($posts);
           for ($i = 0; $i < count($posts); $i += 6) {
		  
            ?>
            <!-- Verticle Content Section -->
            <!-- Star 1st Section -->
                    <?php
                    $post = get_post($posts[$i + 0], OBJECT);
                    setup_postdata($post);
                    ?>
					
						
						<div class="row row-30  text-left">
						<div class="col-md-6 col-lg-4 order-lg-1">
						<?php if (isset($posts[$i + 0])) { ?>
						<div class="category wow fadeInUp s<?php echo $i + 0; ?>" data-wow-delay=".3s">
						<?php if (has_post_thumbnail()): ?>
						<?php the_post_thumbnail('mag-lite-home-promo'); ?>
						<?php elseif (!has_post_thumbnail()): ?>
						<img width="370" height="250" src="<?php echo get_template_directory_uri(); ?>/assest/img/blank-profile.png" class="attachment-post-thumbnail size-post-thumbnail wp-post-image">
						<?php endif; ?>
						<div class="category-content">
						<?php //mag_lite_entry_categories();   ?>
						<h4 class="entry-title">
						<?php the_title(); ?>
						</h4>
						<a class="btn btn-sm btn-primary" href="<?php the_permalink(); ?>">More details</a>
						</div>
						</div>
						<?php } wp_reset_postdata(); ?>
						<?php
						$post = get_post($posts[$i + 3]->ID, OBJECT);
						setup_postdata($post);
						if (isset($posts[$i + 3])) {
						?>
						<div class="category wow fadeInUp s<?php echo $i + 3; ?>" data-wow-delay=".3s">
						<?php if (has_post_thumbnail()): ?>
						<?php the_post_thumbnail('mag-lite-home-promo'); ?>
						<?php elseif (!has_post_thumbnail()): ?>
						<img width="370" height="250" src="<?php echo get_template_directory_uri(); ?>/assest/img/blank-profile.png" class="attachment-post-thumbnail size-post-thumbnail wp-post-image">
						<?php endif; ?>
						<div class="category-content">
						<h4 class="entry-title">
						<?php the_title(); ?>
						</h4>
						<a class="btn btn-sm btn-primary" href="<?php the_permalink(); ?>">More details</a>
						</div>
						</div>
						<?php } ?>
						</div>
						<?php wp_reset_postdata(); ?>
						<div class="col-md-6 col-lg-4 order-lg-3">
						<?php
						$post = get_post($posts[$i + 1]->ID, OBJECT);
						setup_postdata($post);
						if (isset($posts[$i + 1])) {
						?>
						<div class="category wow fadeInUp s<?php echo $i + 1; ?>" data-wow-delay=".3s">
						<?php if (has_post_thumbnail()): ?>
						<?php the_post_thumbnail('mag-lite-home-promo'); ?>
						<?php elseif (!has_post_thumbnail()): ?>
						<img width="370" height="250" src="<?php echo get_template_directory_uri(); ?>/assest/img/blank-profile.png" class="attachment-post-thumbnail size-post-thumbnail wp-post-image">
						<?php endif; ?>
						<div class="category-content">
						<h4 class="entry-title">
						<?php the_title(); ?>
						</h4>
						<a class="btn btn-sm btn-primary" href="<?php the_permalink(); ?>">More details</a>
						</div>
						</div>
						<?php } ?>
						<?php wp_reset_postdata(); ?>
						<?php
						$post = get_post($posts[$i + 4]->ID, OBJECT);
						setup_postdata($post);
						if (isset($posts[$i + 4])) {
						?>
						<div class="category wow fadeInUp s<?php echo $i + 4; ?>" data-wow-delay=".3s">
						<?php if (has_post_thumbnail()): ?>
						<?php the_post_thumbnail('mag-lite-home-promo'); ?>
						<?php elseif (!has_post_thumbnail()): ?>
						<img width="370" height="250" src="<?php echo get_template_directory_uri(); ?>/assest/img/blank-profile.png" class="attachment-post-thumbnail size-post-thumbnail wp-post-image">
						<?php endif; ?>
						<div class="category-content">
						<h4 class="entry-title">
						<?php the_title(); ?>
						</h4>
						<a class="btn btn-sm btn-primary" href="<?php the_permalink(); ?>">More details</a>
						</div>
						</div>
						<?php } ?>
						</div>
						<?php wp_reset_postdata(); ?>

						<div class="col-md-6 col-lg-4 order-lg-3">
						<?php
						$post = get_post($posts[$i + 2]->ID, OBJECT);
						setup_postdata($post);
						if (isset($posts[$i + 2])) {
						?>
						<div class="category wow fadeInUp s<?php echo $i + 2; ?>" data-wow-delay=".3s">
						<?php if (has_post_thumbnail()): ?>
						<?php the_post_thumbnail('mag-lite-home-promo'); ?>
						<?php elseif (!has_post_thumbnail()): ?>
						<img width="370" height="250" src="<?php echo get_template_directory_uri(); ?>/assest/img/blank-profile.png" class="attachment-post-thumbnail size-post-thumbnail wp-post-image">
						<?php endif; ?>
						<div class="category-content">
						<h4 class="entry-title">
						<?php the_title(); ?>
						</h4>
						<a class="btn btn-sm btn-primary" href="<?php the_permalink(); ?>">More details</a>
						</div>
						</div>
						<?php } ?>
						<?php wp_reset_postdata(); ?>
						<?php
						$post = get_post($posts[$i + 5]->ID, OBJECT);
						setup_postdata($post);
						if (isset($posts[$i + 5])) {
						?>
						<div class="category wow fadeInUp s<?php echo $i + 5; ?>" data-wow-delay=".3s">
						<?php if (has_post_thumbnail()): ?>
						<?php the_post_thumbnail('mag-lite-home-promo'); ?>
						<?php elseif (!has_post_thumbnail()): ?>
						<img width="370" height="250" src="<?php echo get_template_directory_uri(); ?>/assest/img/blank-profile.png" class="attachment-post-thumbnail size-post-thumbnail wp-post-image">
						<?php endif; ?>
						<div class="category-content">
						<h4 class="entry-title">
						<?php the_title(); ?>
						</h4>
						<a class="btn btn-sm btn-primary" href="<?php the_permalink(); ?>">More details</a>
						</div>
						</div>
						<?php } ?>
						</div>
						<?php wp_reset_postdata(); ?>
						<!-- End Content Section -->
						</div>
			
			<?php
        } wp_reset_postdata();
        ?>
		
        <!---  lOOP  -->
		
        <!-- lOOP End -->
        <?php
    }

    function showVerticleLayout($posts) {
	
        global $post;
        //foreach($posts as $post)
        for ($i = 0; $i < count($posts); $i += 5) {
            ?>
            <!-- Main Content Section -->
            <!-- Star 1st Section -->
                    <?php
                   $post = get_post($posts[$i + 0], OBJECT);
                    setup_postdata($post);
					
                    ?>
            <div class="row row-30  text-left">
              <div class="col-md-6 col-lg-4 order-lg-1">
                        <?php if (isset($posts[$i + 0])) { ?>
                    <div class="category wow fadeInUp" data-wow-delay=".3s">
                <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('mag-lite-home-promo'); ?>
                <?php elseif (!has_post_thumbnail()): ?>
                          <img width="370" height="250" src="<?php echo get_template_directory_uri(); ?>/assest/img/blank-profile.png" class="attachment-post-thumbnail size-post-thumbnail wp-post-image">
                    <?php endif; ?>
                      <div class="category-content">
                    <?php //mag_lite_entry_categories();   ?>
                        <h4 class="entry-title">
                    <?php the_title(); ?>
                        </h4>
                        <a class="btn btn-sm btn-primary" href="<?php the_permalink(); ?>">More details</a>
                      </div>
                    </div>
                    <?php } wp_reset_postdata(); ?>
                  <?php
                  $post = get_post($posts[$i + 1]->ID, OBJECT);
                  setup_postdata($post);
                  if (isset($posts[$i + 1])) {
                      ?>
                    <div class="category wow fadeInUp" data-wow-delay=".3s">
                <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('mag-lite-home-promo'); ?>
                    <?php elseif (!has_post_thumbnail()): ?>
                          <img width="370" height="250" src="<?php echo get_template_directory_uri(); ?>/assest/img/blank-profile.png" class="attachment-post-thumbnail size-post-thumbnail wp-post-image">
                  <?php endif; ?>
                      <div class="category-content">
                        <h4 class="entry-title">
                      <?php the_title(); ?>
                        </h4>
                        <a class="btn btn-sm btn-primary" href="<?php the_permalink(); ?>">More details</a>
                      </div>
                    </div>
                    <?php } ?>
              </div>
                    <?php wp_reset_postdata(); ?>
              <div class="col-md-6 col-lg-4 order-lg-3">
                  <?php
                  $post = get_post($posts[$i + 3]->ID, OBJECT);
                  setup_postdata($post);
                  if (isset($posts[$i + 3])) {
                      ?>
                    <div class="category wow fadeInUp" data-wow-delay=".3s">
                <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('mag-lite-home-promo'); ?>
                    <?php elseif (!has_post_thumbnail()): ?>
                          <img width="370" height="250" src="<?php echo get_template_directory_uri(); ?>/assest/img/blank-profile.png" class="attachment-post-thumbnail size-post-thumbnail wp-post-image">
                    <?php endif; ?>
                      <div class="category-content">
                        <h4 class="entry-title">
                    <?php the_title(); ?>
                        </h4>
                        <a class="btn btn-sm btn-primary" href="<?php the_permalink(); ?>">More details</a>
                      </div>
                    </div>
                    <?php } ?>
                    <?php wp_reset_postdata(); ?>
                  <?php
                  $post = get_post($posts[$i + 4]->ID, OBJECT);
                  setup_postdata($post);
                  if (isset($posts[$i + 4])) {
                      ?>
                    <div class="category wow fadeInUp" data-wow-delay=".3s">
                <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('mag-lite-home-promo'); ?>
                    <?php elseif (!has_post_thumbnail()): ?>
                          <img width="370" height="250" src="<?php echo get_template_directory_uri(); ?>/assest/img/blank-profile.png" class="attachment-post-thumbnail size-post-thumbnail wp-post-image">
                  <?php endif; ?>
                      <div class="category-content">
                        <h4 class="entry-title">
                  <?php the_title(); ?>
                        </h4>
                        <a class="btn btn-sm btn-primary" href="<?php the_permalink(); ?>">More details</a>
                      </div>
                    </div>
            <?php } ?>
              </div>
                    <?php wp_reset_postdata(); ?>
                    <?php
                    //
                    $post = get_post($posts[$i + 2], OBJECT);
                    setup_postdata($post);
                    if (isset($posts[$i + 2])) {
                        ?>
                  <div class="col-md-6 col-lg-4 order-lg-2">
                    <div class="category wow fadeInUp" data-wow-delay=".3s">
                <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('mag-lite-home-vert'); ?>
                <?php elseif (!has_post_thumbnail()): ?>
                          <img width="350" height="600" src="<?php echo get_template_directory_uri(); ?>/assest/img/blank-profile.png" class="attachment-post-thumbnail size-post-thumbnail wp-post-image">
                  <?php endif; ?>
                      <div class="category-content">
                        <h4 class="entry-title">
                <?php the_title(); ?>
                        </h4>
                        <a class="btn btn-sm btn-primary" href="<?php the_permalink(); ?>">More details</a>
                      </div>
                    </div>
                  </div>
            <?php } ?>
            </div>
            <?php wp_reset_postdata(); ?>
            <!-- End Content Section -->
            <?php
        }
        wp_reset_postdata();
        ?>
        <!---  lOOP  -->
        <!-- lOOP End -->
        <?php
    }

}