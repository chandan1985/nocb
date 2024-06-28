<?php
/**
 * Register Slider Widgets.
 *
 * @package Mag_Lite
 */

function mag_lite_action_news_ticker_slider() {

  register_widget( 'mag_lite_news_ticker_slider' );
  
}
add_action( 'widgets_init', 'mag_lite_action_news_ticker_slider' );

class mag_lite_news_ticker_slider extends WP_Widget
{
  
	function __construct() {

		global $control_ops;

		$widget_ops = array(
		  'classname'   => 'mag-lite-news-ticker-slider',
		  'description' => esc_html__( 'Add Widget to Display Slider .', 'mag-lite' )
		);

		parent::__construct( 'mag_lite_news_ticker_slider',esc_html__( 'ML: News Ticker Slider', 'mag-lite' ), $widget_ops, $control_ops );
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 
		  'title'			=> esc_html__( 'Latest News', 'mag-lite' ),
		  'icon'			=> '',
		  'category'         => '',     
		  'number'           => 4, 

		) );
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : esc_html__( 'Latest News', 'mag-lite' );
		$category = isset( $instance['category'] ) ? absint( $instance['category'] ) : 0;
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 4;  
		$icon     = isset( $instance['icon'] ) ? esc_attr( $instance['icon'] ) : '';  
	?>
	    <p><label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php echo esc_html__( 'News Title:', 'mag-lite' ); ?></label>
	    <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
	    <p><label for="<?php echo esc_attr($this->get_field_id( 'icon' )); ?>"><?php echo esc_html__( 'Icon:', 'mag-lite' ); ?></label>
	    <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'icon' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'icon' )); ?>" type="text" value="<?php echo esc_attr($icon); ?>" /></p>
	    <p>
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
    <?php
    }

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['icon'] = sanitize_text_field( $new_instance['icon'] );
		$instance['category'] = absint( $new_instance['category'] );
		$instance['number'] = (int) $new_instance['number'];	   

		return $instance;
	}

    function widget( $args, $instance ) {

    	extract( $args );

	    $title = ( ! empty( $instance['title'] ) ) ? esc_html($instance['title']) : esc_html__( 'Latest News','mag-lite' );

	    $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );  

		$icon = ( ! empty( $instance['icon'] ) ) ? esc_html($instance['icon']) :''; 
    	
        $category  = isset( $instance[ 'category' ] ) ? $instance[ 'category' ] : '';

        $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 4;        

        echo $before_widget;

        echo '<div class="container">';

       		if( !empty( $title ) ): ?>

       			<h2 class="widget-title">
	       			<?php if( !empty( $icon ) ):?>
       					<span class=" ticker-icon fa <?php echo esc_attr( $icon);?>"></span>
   					<?php endif;?>
   					<span class="breaking-news-title"><?php echo esc_attr( $title );?></span>
   					
   				</h2> 

   			<?php endif;     

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

	            <div class="breaking-news-section">	            	
            		<div class="breaking-news ticker">
            			<ul class="breaking-news-ticker">
	            			<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
	            				<li>
									<p><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php the_title(); ?></a></p> 
								</li>
							<?php endwhile;
							wp_reset_postdata();?>
						</ul>

	                </div>
	            	
	            </div>

	        <?php endif;

		echo '</div>';

        echo $after_widget;

    } 

}