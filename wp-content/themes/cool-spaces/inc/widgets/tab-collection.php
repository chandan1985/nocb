<?php
/**
 * Register Tab Widgets.
 *
 * @package Mag_Lite
 */

function mag_lite_action_tab_collection() {

  register_widget( 'mag_lite_tab_collection' );
  
}
add_action( 'widgets_init', 'mag_lite_action_tab_collection' );

class mag_lite_tab_collection extends WP_Widget
{
  
	function __construct() {

		global $control_ops;

		$widget_ops = array(
		  'classname'   => 'mag-lite-tab-collecion',
		  'description' => esc_html__( 'Add Widget to Display Tab Section .', 'mag-lite' )
		);

		parent::__construct( 'mag_lite_tab_collection',esc_html__( 'ML: Home Tab Collection', 'mag-lite' ), $widget_ops, $control_ops );
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 
		  'category'         => '',     
		  'number'           => 4,
		  'category_news'    => '',     
		  'number_news'      => 4, 
		  'category_style'	 => '',
		  'number_style'     => 4,	
		) );		
		$category = isset( $instance['category'] ) ? absint( $instance['category'] ) : 0;
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 4;
		$category_news = isset( $instance['category_news'] ) ? absint( $instance['category_news'] ) : 0;
		$number_news    = isset( $instance['number_news'] ) ? absint( $instance['number_news'] ) : 4;
		$category_style = isset( $instance['category_style'] ) ? absint( $instance['category_style'] ) : 0;
		$number_style    = isset( $instance['number_style'] ) ? absint( $instance['number_style'] ) : 4; 		 		    
	?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>">
				<?php esc_html_e( 'Category:', 'mag-lite' ); ?>			
			</label>

			<?php
				wp_dropdown_categories(array(
					'show_option_none' => '',
					'class'			   => 'widefat',	
					'show_option_all'  => esc_html__('Choose Option','mag-lite'),
					'name'             => esc_attr($this->get_field_name( 'category' )),
					'selected'         => absint( $category ),          
				) );
			?>
		</p>
	    <p>
	    	<label for="<?php echo esc_attr($this->get_field_id( 'number' )); ?>">
	    		<?php echo esc_html__( 'Choose Number', 'mag-lite' );?>    		
	    	</label>

	    	<input class="tiny-text" id="<?php echo esc_attr($this->get_field_id( 'number' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'number' )); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>" max="9" />
	    </p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'category_news' ) ); ?>">
				<?php esc_html_e( 'Category:', 'mag-lite' ); ?>			
			</label>

			<?php
				wp_dropdown_categories(array(
					'show_option_none' => '',
					'class'			   => 'widefat',	
					'show_option_all'  => esc_html__('Choose Option','mag-lite'),
					'name'             => esc_attr($this->get_field_name( 'category_news' )),
					'selected'         => absint( $category_news ),          
				) );
			?>
		</p>
	    <p>
	    	<label for="<?php echo esc_attr($this->get_field_id( 'number_news' )); ?>">
	    		<?php echo esc_html__( 'Choose Number', 'mag-lite' );?>    		
	    	</label>

	    	<input class="tiny-text" id="<?php echo esc_attr($this->get_field_id( 'number_news' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'number_news' )); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number_news); ?>" max="10" />
	    </p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'category_style' ) ); ?>">
				<?php esc_html_e( 'Category:', 'mag-lite' ); ?>			
			</label>

			<?php
				wp_dropdown_categories(array(
					'show_option_none' => '',
					'class'			   => 'widefat',	
					'show_option_all'  => esc_html__('Choose Option','mag-lite'),
					'name'             => esc_attr($this->get_field_name( 'category_style' )),
					'selected'         => absint( $category_style ),          
				) );
			?>
		</p>
	    <p>
	    	<label for="<?php echo esc_attr($this->get_field_id( 'number_style' )); ?>">
	    		<?php echo esc_html__( 'Choose Number', 'mag-lite' );?>    		
	    	</label>

	    	<input class="tiny-text" id="<?php echo esc_attr($this->get_field_id( 'number_style' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'number_style' )); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number_style); ?>" max="10" />
	    </p>	    	    
    <?php
    }

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['category'] = absint( $new_instance['category'] );
		$instance['number'] = (int) $new_instance['number'];	
		$instance['category_news'] = absint( $new_instance['category_news'] );		
		$instance['number_news'] = (int) $new_instance['number_news'];	
		$instance['category_style'] = absint( $new_instance['category_style'] );
		$instance['number_style'] = (int) $new_instance['number_style'];			   


		return $instance;
	}

    function widget( $args, $instance ) {

    	extract( $args );
    	
        $category  = isset( $instance[ 'category' ] ) ? $instance[ 'category' ] : '';

        $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 9;

        $category_news  = isset( $instance[ 'category_news' ] ) ? $instance[ 'category_news' ] : '';

        $number_news = ( ! empty( $instance['number_news'] ) ) ? absint( $instance['number_news'] ) : 10;

        $category_style  = isset( $instance[ 'category_style' ] ) ? $instance[ 'category_style' ] : '';

        $number_style = ( ! empty( $instance['number_style'] ) ) ? absint( $instance['number_style'] ) : 10;
           

		$category_title = get_cat_name($category);
		$category_news_title = get_cat_name($category_news);  
		$category_style_title = get_cat_name($category_style);

		if( !empty( $category ) || !empty( $category_news ) || !empty( $category_style ) ):

	        echo $before_widget; ?>

	        <section class="news-section all-news-section">

	        	<div class="tabs">
	                <ul class="tab-links">

	                	
						<li class="active"><a href="#all-news-tab0"><?php echo esc_html__( 'All','mag-lite');?></a></li>
						              	

	                	<?php if( !empty( $category ) ){ ?>
							<li><a href="#all-news-tab1"><?php echo esc_html( $category_title);?></a></li>
						<?php } ?>

						<?php if( !empty( $category_news ) ){ ?>
							<li><a href="#all-news-tab2"><?php echo esc_html( $category_news_title );?></a></li>
						<?php } ?>

						<?php if( !empty( $category_style ) ){ ?>
							<li><a href="#all-news-tab3"><?php echo esc_html( $category_style_title );?></a></li>
						<?php } ?>

	                </ul>

	                <div class="tab-content">

		                    <div id="all-news-tab0" class="tab active">
		                    	
						        <?php $all_args = array(
						            'posts_per_page' => absint( $number ),
						            'post_type' => 'post',
						            'post_status' => 'publish',      
						        );

						        if ( absint( $category ) > 0 ||  absint( $category_news ) > 0 || absint( $category_style ) > 0 ) {
						          $all_args['cat'] = array( absint( $category ), absint( $category_news ), absint( $category_style ) );
						        }
						        $the_query = new WP_Query( $all_args );    

						        if ($the_query->have_posts()) : $cn = 1;
						        	while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
										<?php if( $cn == 1 ) { 
											echo '<div id="news-slider-tab" class="owl-carousel owl-theme news-slider-tab">';
										} elseif ( $cn == 4){ 
											echo '<div class="row">'; 
										} ?>

										<?php $post_class = '';
										$image_size= 'mag-lite-promo-slider';
											  $post_width_class = '';
			            				$no_image= ''; 
				            				if( !has_post_thumbnail() ){
				            					$no_image = 'no-image';
			            					}
		            							  
											if( $cn >5 ) {
												$post_class = 'small-post';
												$image_size= 'mag-lite-promo-latest-popular-thumbnail';

											} 
											if( $cn >3 ) {
												$post_width_class = 'custom-col-6';

											}
											$slider_post = 'slider-content';
											$slider_text = 'slider-text';
											if( $cn >3 ) {
												$slider_post = 'post';
												$slider_text = 'post-content';
												

											}
										?>
										
										<div class="<?php echo esc_attr( $slider_post);?> <?php echo esc_attr( $no_image);?> <?php echo esc_attr( $post_width_class);?> <?php echo esc_attr( $post_class);?>">
											<?php if( has_post_thumbnail() ): ?>	
												<figure class="featured-image">
													<?php the_post_thumbnail( $image_size );?>	
												</figure>
											<?php endif;?>							

											<div class="<?php echo esc_attr( $slider_text);?>">
												<header class="entry-header">
													<?php mag_lite_entry_categories();?>
													<h3 class="entry-title">
														<a href="<?php the_permalink();?>"><?php the_title();?></a>
													</h3>
												</header>
												<div class="entry-meta">
													<?php mag_lite_posted_on(); ?>
												
												</div>

											</div>
										</div>								

										<?php if( $cn == 3 ) { 
											echo '</div>'; 
										}
								
										$cn++; ?>
					        		<?php endwhile;	
					        		if ( $cn < 3 ) { 
					        			echo '</div>'; 
					        		} elseif ( $cn <= 10 ){
					        			echo '</div>';
					        		} 					        			        		 
					        		wp_reset_postdata();?>

				        		<?php endif;?>
					        	
		                    </div>  	                	

	                	<?php if( !empty( $category ) ){ ?>
		                    <div id="all-news-tab1" class="tab">
		                    	
						        <?php $category_args = array(
						            'posts_per_page' => absint( $number ),
						            'post_type' => 'post',
						            'post_status' => 'publish',      
						        );

						        if ( absint( $category ) > 0 ) {
						          $category_args['cat'] = absint( $category );
						        }
						        $the_query = new WP_Query( $category_args );    

						        if ($the_query->have_posts()) : $cn = 1;
						        	while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
										<?php if( $cn == 1 ) { 
											echo '<div id="news-slider-tab" class="owl-carousel owl-theme news-slider-tab">';
										} elseif ( $cn == 4){ 
											echo '<div class="row">'; 
										} ?>

										<?php $post_class = '';
										$image_size= 'mag-lite-promo-slider';
											  $post_width_class = '';
			            				$no_image= ''; 
				            				if( !has_post_thumbnail() ){
				            					$no_image = 'no-image';
			            					}
		            							  
											if( $cn >5 ) {
												$post_class = 'small-post';
												$image_size= 'mag-lite-promo-latest-popular-thumbnail';

											} 
											if( $cn >3 ) {
												$post_width_class = 'custom-col-6';

											}
											$slider_post = 'slider-content';
											$slider_text = 'slider-text';
											if( $cn >3 ) {
												$slider_post = 'post';
												$slider_text = 'post-content';
												

											}
										?>
										
										<div class="<?php echo esc_attr( $slider_post);?> <?php echo esc_attr( $no_image);?> <?php echo esc_attr( $post_width_class);?> <?php echo esc_attr( $post_class);?>">
											<?php if( has_post_thumbnail() ): ?>	
												<figure class="featured-image">
													<?php the_post_thumbnail( $image_size );?>	
												</figure>
											<?php endif;?>							

											<div class="<?php echo esc_attr( $slider_text);?>">
												<header class="entry-header">
													<?php mag_lite_entry_categories();?>
													<h3 class="entry-title">
														<a href="<?php the_permalink();?>"><?php the_title();?></a>
													</h3>
												</header>
												<div class="entry-meta">
													<?php mag_lite_posted_on(); ?>
												
												</div>

											</div>
										</div>								

										<?php if( $cn == 3 ) { 
											echo '</div>'; 
										}
								
										$cn++; ?>
					        		<?php endwhile;	
					        		if ( $cn <=3 ) { echo '</div>'; }
					        		if ( $cn <=10 ) { echo '</div>'; }	        		 
					        		wp_reset_postdata();?>

				        		<?php endif;?>
					        	
		                    </div>  
	                    <?php } ?>              	

	                    <?php if( !empty( $category_news ) ){ ?>
		                    <div id="all-news-tab2" class="tab">
		                    	<div class="row">
							        <?php $category_arg = array(
							            'posts_per_page' => absint( $number_news ),
							            'post_type' => 'post',
							            'post_status' => 'publish',      
							        );

							        if ( absint( $category_news ) > 0 ) {
							          $category_arg['cat'] = absint( $category_news );
							        }
							        $the_query = new WP_Query( $category_arg );    

							        if ($the_query->have_posts()) : $cn = 1;
							        	while ( $the_query->have_posts() ) : $the_query->the_post(); //var_dump( $cn);?>
										<?php $post_class = '';
										$image_size= 'mag-lite-promo-slider';
											  $post_width_class = '';
											if( $cn >2 ) {
												$post_class = 'small-post';
												$image_size= 'mag-lite-promo-latest-popular-thumbnail';

											} 
											$no_image= ''; 
				            				if( !has_post_thumbnail() ){
				            					$no_image = 'no-image';
		            						}
										?>

											<div class="post custom-col-6 <?php echo esc_attr( $no_image);?> <?php echo esc_attr( $post_class);?>">
												<?php if( has_post_thumbnail() ): ?>	
													<figure class="featured-image">
														<?php the_post_thumbnail( $image_size);?>	
													</figure>
												<?php endif;?>							

												<div class="post-content">
													<header class="entry-header">
														<?php mag_lite_entry_categories();?>
														<h3 class="entry-title">
															<a href="<?php the_permalink();?>"><?php the_title();?></a>
														</h3>
													</header>

													<?php mag_lite_posted_on(); ?>

												</div>
											</div>								

																
											<?php $cn++; ?>
						        		<?php endwhile; ?>

						        		<?php wp_reset_postdata();?>

						        	<?php endif;?>

						        </div>	

		                	</div>
	                	<?php } ?>

	                	<?php if( !empty( $category_style ) ){ ?>
		                    <div id="all-news-tab3" class="tab">
		                    	<div class="row">
							        <?php $category_style_args = array(
							            'posts_per_page' => absint( $number_style ),
							            'post_type' => 'post',
							            'post_status' => 'publish',      
							        );				        

							        if ( absint( $category_style ) > 0 ) {
							          $category_style_args['cat'] = absint( $category_style );
							        }
							        $the_query = new WP_Query( $category_style_args );    

							        if ($the_query->have_posts()) : $cn = 1;
							        	while ( $the_query->have_posts() ) : $the_query->the_post(); 
							        		$no_image= ''; 
				            				if( !has_post_thumbnail() ){
				            					$no_image = 'no-image';
			            					}
		            					?>

											<div class="post <?php echo esc_attr( $no_image);?> custom-col-6 small-post">
												<?php if( has_post_thumbnail() ): ?>	
													<figure class="featured-image">
														<?php the_post_thumbnail( 'thumbnail' );?>	
													</figure>
												<?php endif;?>							

												<div class="post-content">
													<header class="entry-header">
														<?php mag_lite_entry_categories();?>
														<h3 class="entry-title">
															<a href="<?php the_permalink();?>"><?php the_title();?></a>
														</h3>
													</header>

													<?php mag_lite_posted_on(); ?>

												</div>
											</div>								

											<?php 													
											$cn++; ?>
						        		<?php endwhile;				        		
						        		wp_reset_postdata();?>

						        	<?php endif;?>

						        </div>	

		                	</div>
	                	<?php } ?>
	                      
	            	</div>
	    		</div>

	        </section>

	        <?php echo $after_widget; 
        endif;

    } 

}