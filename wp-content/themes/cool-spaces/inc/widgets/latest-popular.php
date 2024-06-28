<?php
/**
 * Register Tab Widgets.
 *
 * @package Mag_Lite
 */

function mag_lite_action_latest_tab() {

	register_widget( 'mag_lite_latest_tab' );
	
}
add_action( 'widgets_init', 'mag_lite_action_latest_tab' );

class mag_lite_latest_tab extends WP_Widget
{
	
	function __construct() {

		global $control_ops;

		$widget_ops = array(
			'classname'   => 'mag-lite-latest-tab',
			'description' => esc_html__( 'Add Widget to Display Tab Section .', 'mag-lite' )
			);

		parent::__construct( 'mag_lite_latest_tab',esc_html__( 'ML: Home Latest/Popular Tab', 'mag-lite' ), $widget_ops, $control_ops );
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 
			'category'         => '',     
			'number'           => 4,
			'category_news'    => '',     
			'number_news'      => 4, 
			'show_post_meta'	 => true,	
			) );
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$category = isset( $instance['category'] ) ? absint( $instance['category'] ) : 0;
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 4;
		$category_news = isset( $instance['category_news'] ) ? absint( $instance['category_news'] ) : 0;
		$number_news    = isset( $instance['number_news'] ) ? absint( $instance['number_news'] ) : 4; 	
		$show_post_meta = isset( $instance['show_post_meta'] ) ? (bool) $instance['show_post_meta'] : true;	    
		?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>">
				<?php echo esc_html__( 'Title:', 'mag-lite' ); ?>				
			</label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>	

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

				<input class="tiny-text" id="<?php echo esc_attr($this->get_field_id( 'number' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'number' )); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>" max="4" />
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

					<input class="tiny-text" id="<?php echo esc_attr($this->get_field_id( 'number_news' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'number_news' )); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number_news); ?>" max="4" />
				</p>
				<p><input class="checkbox" type="checkbox"<?php checked( $show_post_meta ); ?> id="<?php echo esc_attr($this->get_field_id( 'show_post_meta' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'show_post_meta' )); ?>" />
					<label for="<?php echo esc_attr($this->get_field_id( 'show_post_meta' )); ?>"><?php echo esc_html__( 'Enable Post Meta', 'mag-lite' ); ?></label></p>   	    	    
					<?php
				}

				function update( $new_instance, $old_instance ) {
					$instance = $old_instance;

					$instance['title'] = sanitize_text_field( $new_instance['title'] );
					$instance['category'] = absint( $new_instance['category'] );
					$instance['number'] = (int) $new_instance['number'];	
					$instance['category_news'] = absint( $new_instance['category_news'] );
					$instance['number_news'] = (int) $new_instance['number_news'];
					$instance['show_post_meta'] = (bool) $new_instance['show_post_meta'];  			   

					return $instance;
				}

				function widget( $args, $instance ) {

					extract( $args );

					$title = ( ! empty( $instance['title'] ) ) ? esc_html($instance['title']) :'';    	
					
					$category  = isset( $instance[ 'category' ] ) ? $instance[ 'category' ] : '';

					$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 4;

					$category_news  = isset( $instance[ 'category_news' ] ) ? $instance[ 'category_news' ] : '';

					$number_news = ( ! empty( $instance['number_news'] ) ) ? absint( $instance['number_news'] ) : 4;

					$show_post_meta = isset( $instance['show_post_meta'] ) ? $instance['show_post_meta'] : true; 

					$category_title = get_cat_name($category);
					$category_news_title = get_cat_name($category_news); 

					if( !empty( $category ) || !empty( $category_news ) ): 
						echo $before_widget; ?>

					<section class="video-tab-section section-news-title mag-lite-latest-tab">

						<div class="tabs">
							<ul class="tab-links">

								<?php if( !empty( $title ) ):?>
									<header class="entry-header heading">
										<h2 class="entry-title"><span><?php echo esc_html( $title);?></span></h2>
									</header>
								<?php endif;?>

								<?php if( !empty( $category) && !empty( $category_news) ) { ?>
								<li class="active"><a href="#video-tab0"><?php echo esc_html__( 'All','mag-lite');?></a></li>
								<?php } ?>	
								
								<?php if( !empty( $category ) ) { ?>

								<li><a href="#video-tab1"><?php echo esc_html( $category_title);?></a></li>

								<?php } ?>

								<?php if( !empty( $category_news ) ) { ?>
								<li><a href="#video-tab2"><?php echo esc_html( $category_news_title );?></a></li>
								<?php } ?> 

							</ul>

							<div class="tab-content">
								<?php if( !empty( $category) && !empty( $category_news) ) { ?>
								<div id="video-tab0" class="tab active">
									<div class="row">
										<?php $all_args = array(
											'posts_per_page' => absint( $number ),
											'post_type' => 'post',
											'post_status' => 'publish',
										//	'orderby' => 'rand', 
											'order'   => 'DESC',     
											);

										if ( absint( $category ) > 0 ||  absint( $category_news ) > 0 ) {
											$all_args['cat'] = array( absint( $category ), absint( $category_news ) );
										}
										$the_query = new WP_Query( $all_args );    

										if ($the_query->have_posts()) : $cn = 0;
										while ( $the_query->have_posts() ) : $the_query->the_post(); $cn++;?>

										<?php $post_class = 'small-post'; 
										$image_size = 'mag-lite-promo-latest-popular-thumbnail';
										if( $cn == 1){
											$post_class = 'large-post';
											$image_size ='mag-lite-promo-latest-popular-medium';
										}?>
										<?php $no_image= ''; 
										if( !has_post_thumbnail() ){
											$no_image = 'no-image';
										}?>

										<div class="custom-col-6">
											<div class="post <?php echo esc_attr( $post_class );?> <?php echo esc_attr( $no_image);?>">

												<?php if( has_post_thumbnail() ): ?>	
													<figure class="featured-image">
														<?php the_post_thumbnail( $image_size );?>	
													</figure>
												<?php endif;?>	


												<div class="post-content">
													<header class="entry-header">
														<?php mag_lite_entry_categories();?>
														<h3 class="entry-title">
															<a href="<?php the_permalink();?>"><?php the_title();?></a>
														</h3>
													</header>
													<?php if( $cn == 1 ):?>
														<div class="entry-content">
															<?php
															$excerpt = mag_lite_the_excerpt(20);
															echo wp_kses_post( wpautop( $excerpt ) );
															?>
														</div>
													<?php endif;?>

													<?php if( true== $show_post_meta): ?>

														<div class="entry-meta">
															
															<?php mag_lite_posted_on(); 
															mag_lite_posted_by(); 

															?>

														</div>

													<?php endif; ?>
												</div>
											</div>
										</div>
									<?php endwhile;
									wp_reset_postdata();?>

								<?php endif;?>
							</div>
						</div>
						<?php } ?>
						<?php if( !empty( $category ) ) { ?>
						<div id="video-tab1" class="tab">

							<div class="row">
								<?php $category_args = array(
									'posts_per_page' => absint( $number ),
									'post_type' => 'post',
									'post_status' => 'publish',      
									);

								if ( absint( $category ) > 0 ) {
									$category_args['cat'] = absint( $category );
								}
								$the_query = new WP_Query( $category_args );    

								if ($the_query->have_posts()) : $cn = 0;
								while ( $the_query->have_posts() ) : $the_query->the_post(); $cn++;?>

								<?php $post_class = 'small-post'; 
								$image_size = 'mag-lite-promo-latest-popular-thumbnail';
								if( $cn == 1){
									$post_class = 'large-post';
									$image_size ='mag-lite-promo-latest-popular-medium';
								}?>
								<?php $no_image= ''; 
								if( !has_post_thumbnail() ){
									$no_image = 'no-image';
								}?>

								<div class="custom-col-6">
									<div class="post <?php echo esc_attr( $post_class );?> <?php echo esc_attr( $no_image);?>">

										<?php if( has_post_thumbnail() ): ?>	
											<figure class="featured-image">
												<?php the_post_thumbnail( $image_size );?>	
											</figure>
										<?php endif;?>	


										<div class="post-content">
											<header class="entry-header">
												<?php mag_lite_entry_categories();?>
												<h3 class="entry-title">
													<a href="<?php the_permalink();?>"><?php the_title();?></a>
												</h3>
											</header>
											<?php if( $cn == 1 ):?>
												<div class="entry-content">
													<?php
													$excerpt = mag_lite_the_excerpt(20);
													echo wp_kses_post( wpautop( $excerpt ) );
													?>
												</div>
											<?php endif;?>

											<?php if( true== $show_post_meta): ?>

												<div class="entry-meta">
													
													<?php mag_lite_posted_on(); 
													mag_lite_posted_by(); 

													?>

												</div>

											<?php endif; ?>
										</div>
									</div>
								</div>
							<?php endwhile;
							wp_reset_postdata();?>

						<?php endif;?>
					</div>
				</div>
				<?php } ?>
				<?php if( !empty( $category_news ) ) { ?> 
				<div id="video-tab2" class="tab">
					<div class="row">
						<?php $category_news_args = array(
							'posts_per_page' => absint( $number_news ),
							'post_type' => 'post',
							'post_status' => 'publish',      
							);

						if ( absint( $category_news ) > 0 ) {
							$category_news_args['cat'] = absint( $category_news );
						}
						$the_query = new WP_Query( $category_news_args );    

						if ($the_query->have_posts()) : $cn = 0;
						while ( $the_query->have_posts() ) : $the_query->the_post(); $cn++;?>

						<?php $post_class = 'small-post'; 
						$image_size = 'mag-lite-promo-latest-popular-thumbnail';
						if( $cn == 1){
							$post_class = 'large-post';
							$image_size ='mag-lite-promo-latest-popular-medium';
						}?>

						<div class="custom-col-6">
							<div class="post <?php echo esc_attr( $post_class );?>">

								<?php if( has_post_thumbnail() ): ?>	
									<figure class="featured-image">
										<?php the_post_thumbnail( $image_size );?>	
									</figure>
								<?php endif;?>	
								

								<div class="post-content">
									<header class="entry-header">
										<?php mag_lite_entry_categories();?>
										<h3 class="entry-title">
											<a href="<?php the_permalink();?>"><?php the_title();?></a>
										</h3>
									</header>
									<?php if( $cn == 1 ):?>
										<div class="entry-content">
											<?php
											$excerpt = mag_lite_the_excerpt(30);
											echo wp_kses_post( wpautop( $excerpt ) );
											?>
										</div>
									<?php endif;?>

									<?php mag_lite_posted_on(); ?>

								</div>
							</div>
						</div>
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