<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Mag_Lite
 */

?>	
<?php //global $mag_lite_post_count; 
$archive_layout = mag_lite_get_option( 'archive_layout' );
$design_class = '';
$post_class = ''; 
$image_size = 'mag-lite-archive';
if( 'first-design' == $archive_layout):
	$design_class = 'post flexible-post'; 
	$image_size = 'mag-lite-home-tab';
endif;?>	
<?php 
	$image_class= '';
	if( !has_post_thumbnail() ){
		$image_class= 'no-image';
	} 
	
?>

		        			
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="category wow fadeInUp <?php echo esc_attr( $design_class);?> <?php echo esc_attr( $image_class);?> <?php echo esc_attr( $post_class);?>">

		<?php if( has_post_thumbnail() ): ?>
			<figure class="featured-image">
				<?php the_post_thumbnail(); ?>
			</figure>
		<?php endif;?>
		<div class="category-content">
		<?php //mag_lite_entry_categories();?>
		<h4 class="entry-title"><?php the_title(); ?></h4>
		<a class="btn btn-sm btn-primary" href="<?php the_permalink(); ?>">More details</a>
		<?php //mag_lite_posted_on(); ?>
		</div>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
