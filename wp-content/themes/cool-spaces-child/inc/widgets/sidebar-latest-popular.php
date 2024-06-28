<?php
/**
 * Register Sidebar Tab Widgets.
 *
 * @package Mag_Lite
 */

function mag_lite_action_sidebar_latest_tab() {

  register_widget( 'mag_lite_sidebar_latest_tab' );
  
}
add_action( 'widgets_init', 'mag_lite_action_sidebar_latest_tab' );

class mag_lite_sidebar_latest_tab extends WP_Widget
{
  
	function __construct() {

		global $control_ops;

		$widget_ops = array(
		  'classname'   => 'mag-lite-sidebar-latest-tab',
		  'description' => esc_html__( 'Add Widget to Display Tab Section .', 'mag-lite' )
		);

		parent::__construct( 'mag_lite_sidebar_latest_tab',esc_html__( 'ML: Sidebar Latest/Popular Tab', 'mag-lite' ), $widget_ops, $control_ops );
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 
		  'category'         => '',     
		  'number'           => 4,
		  'category_news'    => '',     
		  'number_news'      => 4, 
		) );
		$category = isset( $instance['category'] ) ? absint( $instance['category'] ) : 0;
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 4;
		$category_news = isset( $instance['category_news'] ) ? absint( $instance['category_news'] ) : 0;
		$number_news    = isset( $instance['number_news'] ) ? absint( $instance['number_news'] ) : 4; 		    
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
    <?php
    }

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['category'] = absint( $new_instance['category'] );
		$instance['number'] = (int) $new_instance['number'];	
		$instance['category_news'] = absint( $new_instance['category_news'] );
		$instance['number_news'] = (int) $new_instance['number_news'];			   

		return $instance;
	}

    function widget( $args, $instance ) {

    	extract( $args );   	
    	
        $category  = isset( $instance[ 'category' ] ) ? $instance[ 'category' ] : '';

        $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 4;

        $category_news  = isset( $instance[ 'category_news' ] ) ? $instance[ 'category_news' ] : '';

        $number_news = ( ! empty( $instance['number_news'] ) ) ? absint( $instance['number_news'] ) : 4;

		$category_title = get_cat_name($category);
		$category_news_title = get_cat_name($category_news);  

		if( !empty( $category ) || !empty( $category_news ) ):
		
	        echo $before_widget; ?>        

	    	<div class="tabs">
	            <ul class="tab-links">
	            	<?php if( !empty( $category ) ) { ?>
						<li class="active"><a href="#recent-tab1"><?php echo esc_html( $category_title);?></a></li>
					<?php } ?>

					<?php $active_class = '';
						if( empty( $category ) ){
							$active_class= 'active';
						}
					?>
					<?php if( !empty( $category_news ) ) { ?>
						<li class="<?php echo esc_attr( $active_class);?>"><a href="#recent-tab2"><?php echo esc_html( $category_news_title );?></a></li>
					<?php } ?>

	            </ul>

	            <div class="tab-content">
	            	<?php if( !empty( $category ) ) { ?>
		                <div id="recent-tab1" class="tab active">
		                	
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
			        		        <?php $no_image= ''; 
			            				if( !has_post_thumbnail() ){
			            					$no_image = 'no-image';
			            			}?>
									
									<div class="post small-post <?php echo esc_attr( $no_image );?>">
										<?php if( has_post_thumbnail() ): ?>	
											<figure class="featured-image">
												<?php the_post_thumbnail( 'mag-lite-promo-latest-popular-thumbnail' );?>	
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
									
				        		<?php endwhile;
				        		wp_reset_postdata();?>

			        		<?php endif;?>
				        	
		                </div> 
	                <?php } ?>

	                <?php if( !empty( $category_news ) ) { ?>
		                <div id="recent-tab2" class="tab">
		                	

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

										<div class="post small-post">
											<?php if( has_post_thumbnail() ): ?>	
												<figure class="featured-image">
													<?php the_post_thumbnail( 'mag-lite-promo-latest-popular-thumbnail' );?>	
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
					        		<?php endwhile;
					        		wp_reset_postdata();?>

				        		<?php endif;?>
    
			        		

		                </div>
	                <?php } ?>                       
	        	</div>
			</div>

	        <?php echo $after_widget; 
        endif;

    } 

}