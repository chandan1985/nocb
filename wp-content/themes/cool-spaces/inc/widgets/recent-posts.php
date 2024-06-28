<?php
/**
 * Register Video Widgets.
 *
 * @package Mag_Lite
 */

function mag_lite_action_recent_posts() {

  register_widget( 'mag_lite_recent_posts' );
  
}
add_action( 'widgets_init', 'mag_lite_action_recent_posts' );

class mag_lite_recent_posts extends WP_Widget
{
  
	function __construct() {

		global $control_ops;

		$widget_ops = array(
		  'classname'   => 'mag-lite-recent-posts',
		  'description' => esc_html__( 'Add Widget to Display Video .', 'mag-lite' )
		);

		parent::__construct( 'mag_lite_recent_posts',esc_html__( 'ML: Recent Posts', 'mag-lite' ), $widget_ops, $control_ops );
	}

	function form( $instance ) {
	    $title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
	    $number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 3;
	    $show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;   
	?>
	    <p><label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php echo esc_html__( 'Title:', 'mag-lite' ); ?></label>
	    <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
	    <p>
	    	<label for="<?php echo esc_attr($this->get_field_id( 'number' )); ?>">
	    		<?php echo esc_html__( 'Choose Number', 'mag-lite' );?>    		
	    	</label>

	    	<input class="tiny-text" id="<?php echo esc_attr($this->get_field_id( 'number' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'number' )); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>" max="5" />
	    </p>
	    <p><input class="checkbox" type="checkbox"<?php checked( $show_date ); ?> id="<?php echo esc_attr($this->get_field_id( 'show_date' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'show_date' )); ?>" />
	    <label for="<?php echo esc_attr($this->get_field_id( 'show_date' )); ?>"><?php echo esc_html__( 'Display post date?', 'mag-lite' ); ?></label></p>   	    
    <?php
    }

	function update( $new_instance, $old_instance ) {
	    $instance = $old_instance;
	    $instance['title'] = sanitize_text_field( $new_instance['title'] );
	    $instance['number'] = (int) $new_instance['number'];
	    $instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
	    return $instance;

	}

    function widget( $args, $instance ) {

    	extract( $args );
    	
	    $title = ( ! empty( $instance['title'] ) ) ? esc_html($instance['title']) : esc_html__( 'Latest Blog','mag-lite' );

	    $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

	    $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
	    if ( ! $number )
	      $number = 3;
	    $show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

       	echo $before_widget; ?>

       		<?php if( !empty( $title ) ): ?>

       			<h2 class="widget-title"><span><?php echo esc_attr( $title );?></span></h2> 

   			<?php endif;?>

	        <?php $recent_args = array(
	            'posts_per_page' => absint( $number ),
	            'post_type' => 'post',
	            'post_status' => 'publish',      
	        );
	       
	        $the_query = new WP_Query( $recent_args );    

	        if ($the_query->have_posts()) : ?>

	        	<div class="latest-post-wrapper">

	        		<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
						<div class="post">
							<div class="post-content">
								<div class="entry-meta">
									
									<?php  mag_lite_time_ago(); ?>

								</div>	
								<a href="<?php the_permalink();?>"><?php the_title();?></a>
							</div>
						</div>
        			<?php endwhile;?>

    			</div>

	        <?php endif;

        echo $after_widget;

    } 

}