<?php
/**
 * Register Slider Widgets.
 *
 * @package Mag_Lite
 */

function mag_lite_action_promo_slider() {

  register_widget( 'mag_lite_promo_slider' );
  
}
add_action( 'widgets_init', 'mag_lite_action_promo_slider' );

class mag_lite_promo_slider extends WP_Widget
{
  
	function __construct() {

		global $control_ops;

		$widget_ops = array(
		  'classname'   => 'mag-lite-promo-slider',
		  'description' => esc_html__( 'Add Widget to Display Slider .', 'mag-lite' )
		);

		parent::__construct( 'mag_lite_promo_slider',esc_html__( 'ML: Promo Slider', 'mag-lite' ), $widget_ops, $control_ops );
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 
		  'category'         => '',     
		  'number'           => 4, 
		   'show_post_meta'	 => true,
		) );

		$category = isset( $instance['category'] ) ? absint( $instance['category'] ) : 0;
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 4;  
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

	    	<input class="tiny-text" id="<?php echo esc_attr($this->get_field_id( 'number' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'number' )); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>" max="4" />
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

	        $promo_slider_args = array(
	            'posts_per_page' => absint( $number ),
	            'post_type' => 'post',
	            'post_status' => 'publish',      
	        );

	        if ( absint( $category ) > 0 ) {
	          $promo_slider_args['cat'] = absint( $category );
	        }
	        $the_query = new WP_Query( $promo_slider_args );
	        if ($the_query->have_posts()) : ?>            
	            	
        		<div id="news-slider" class="news-slider owl-carousel owl-theme">
        			<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

        				<?php $no_image= ''; 
        				if( !has_post_thumbnail() ){
        					$no_image = 'no-image';
        				}?>

						<div class="slider-content <?php echo esc_attr( $no_image);?>">
							<?php if( has_post_thumbnail() ): ?>	
								<figure class="slider-image">
									<?php the_post_thumbnail( 'mag-lite-promo-slider' );?>	
								</figure>
							<?php endif;?>
							<div class="slider-text">
								<?php mag_lite_entry_categories();?>
								<h3 class="slider-title">
									<a href="<?php the_permalink()?>"><?php the_title();?></a>
								</h3>

			                        <?php if( true== $show_post_meta): ?>

				                        <div class="entry-meta">
				                        
				                            <?php mag_lite_posted_on(); 
				                            mag_lite_posted_by(); 

				                            ?>

				                        </div>

			                        <?php endif; ?>
								
							</div>
						</div>

					<?php endwhile;
					wp_reset_postdata();?>

                </div>            	
	            

	        <?php endif;

       	echo $after_widget;

    } 

}