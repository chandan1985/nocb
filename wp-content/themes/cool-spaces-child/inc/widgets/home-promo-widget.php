<?php
/**
 * Register Slider Widgets.
 *
 * @package Mag_Lite
 */

function mag_lite_action_home_promo_widget() {

  register_widget( 'mag_lite_home_promo_widget' );
  
}
add_action( 'widgets_init', 'mag_lite_action_home_promo_widget' );

class mag_lite_home_promo_widget extends WP_Widget
{
  
	function __construct() {

		global $control_ops;

		$widget_ops = array(
		  'classname'   => 'mag-lite-home-promo-widget',
		  'description' => esc_html__( 'Add Widget to Display Promo Post .', 'mag-lite' )
		);

		parent::__construct( 'mag_lite_home_promo_widget',esc_html__( 'ML: Home Promo', 'mag-lite' ), $widget_ops, $control_ops );
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 
		  'category'         => '',     
		  'number'           => 5,
		  'show_post_meta'	 => true,	
		) );

		$category = isset( $instance['category'] ) ? absint( $instance['category'] ) : 0;
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5; 
		$show_post_meta = isset( $instance['show_post_meta'] ) ? (bool) $instance['show_post_meta'] : true;   
	?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>">
				<?php esc_html_e( 'Category:', 'mag-lite' ); ?>			
			</label>

			<?php
				wp_dropdown_categories(array(
					'show_option_none' => '',
					'show_option_all'  => esc_html__('From Recent Post','mag-lite'),
					'name'             => esc_attr($this->get_field_name( 'category' )),
					'selected'         => absint( $category ),          
				) );
			?>
		</p>
	    <p>
	    	<label for="<?php echo esc_attr($this->get_field_id( 'number' )); ?>">
	    		<?php echo esc_html__( 'Choose Number', 'mag-lite' );?>    		
	    	</label>

	    	<input class="tiny-text" id="<?php echo esc_attr($this->get_field_id( 'number' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'number' )); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>" max="5" />
	    </p>
	    <p><input class="checkbox" type="checkbox"<?php checked( $show_post_meta ); ?> id="<?php echo esc_attr($this->get_field_id( 'show_post_meta' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'show_post_meta' )); ?>" />
	    <label for="<?php echo esc_attr($this->get_field_id( 'show_post_meta' )); ?>"><?php echo esc_html__( 'Enable Post Meta', 'mag-lite' ); ?></label></p>   	    
    <?php
    }

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['category'] = absint( $new_instance['category'] );
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_post_meta'] = (bool) $new_instance['show_post_meta'];  

		return $instance;
	}

    function widget( $args, $instance ) {

    	extract( $args );

        $category  = isset( $instance[ 'category' ] ) ? $instance[ 'category' ] : '';

        $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 4;

        $show_post_meta = isset( $instance['show_post_meta'] ) ? $instance['show_post_meta'] : true;


        echo $before_widget;

	        $promo_args = array(
	            'posts_per_page' => absint( $number ),
	            'post_type' => 'post',
	            'post_status' => 'publish',      
	        );

	        if ( absint( $category ) > 0 ) {
	          $promo_args['cat'] = absint( $category );
	        }
	        $the_query = new WP_Query( $promo_args );    

	        if ($the_query->have_posts()) : $i = 1?>

	            <section class="featured-news-section">
	            	
	            		
            			<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
							<?php $post_class = '';
							$image_size = 'mag-lite-home-promo-default';
							$post_wrapper = 'custom-col-4';
							if( $i == 1 ) { 
								$post_class = 'flexible-post';
								$image_size = 'mag-lite-home-promo';
								$post_wrapper = 'custom-col-7';								
							} elseif ( $i == 2 ){ 
								$post_class = 'simple-post';
								$image_size = 'mag-lite-home-promo-center';
								$post_wrapper = 'custom-col-5';								
							} ?>            				
							<?php if( $i == 1 ) { 
								echo '<div class="row">';
							} elseif ( $i ==3){ 
								echo '<div class="row row-content-wrapper">'; 
							} ?>
            				<?php $no_image= ''; 
	            				if( !has_post_thumbnail() ){
	            					$no_image = 'no-image';
            				}?>	

					            <div class="<?php echo esc_attr( $post_wrapper);?>">
					                <div class="post <?php echo esc_attr( $no_image);?> <?php echo esc_attr( $post_class);?>">
					                	<?php if( $i!= 2 ):?>
						                    <figure class="featured-image">	
						                    	<?php the_post_thumbnail( $image_size );?>
						                    </figure>
					                    <?php endif;?>
					                    <div class="post-content">
					                    	<?php mag_lite_entry_categories();?>
					                        <header class="entry-header">	
					                            <h3 class="entry-title">
					                            	<a href="<?php the_permalink()?>"><?php the_title();?></a>
					                            </h3>

					                        </header>
					                        <?php if( $i == 1 ):?>
						                        <div class="entry-content">
			                                        <?php
			                                            $excerpt = mag_lite_the_excerpt(30);
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
					                	<?php if( $i == 2 ):?>
						                    <figure class="featured-image">
						                    	<?php the_post_thumbnail( $image_size );?>
						                    </figure>
					                    <?php endif;?>
					                </div>
					            </div> 

							<?php if( $i == 2 ) { echo '</div>'; } ?>

						<?php $i++; ?>	
						<?php endwhile;
						if( $i > 4 ) { echo '</div>'; }  
						wp_reset_postdata();?>

		                
	            	
	            </section>

	        <?php endif;

        echo $after_widget;

    } 

}