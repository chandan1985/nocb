<?php
/**
   Template Name: Coolest Home
 */

get_header(); ?>
	<?php 
		$layout_class ='col-8';
		$sidebar_layout = mag_lite_get_option('layout_options'); 
		if( is_active_sidebar('sidebar-1') && 'no-sidebar' !==  $sidebar_layout){
			$layout_class = 'custom-col-8';
		}
		else{
			$layout_class = 'custom-col-12';
		}		
	?>
<section class="middle-section">
		<div class="container">
			<div class="row">
				<div class="col-md-9 left-section">
					<div class="article-main">
					
						<?php
				while ( have_posts() ) : the_post();

					get_template_part( 'template-parts/content', 'single' );

					//the_post_navigation();


					// If comments are open or we have at least one comment, load up the comment template.
					//if ( comments_open() || get_comments_number() ) :
						//comments_template();
					//endif;					

				endwhile; // End of the loop.
			//	wpb_author_info_box()
				/**
				 *  Author box
				 */

			/*	function wpb_author_info_box( $content ) {
				 
				global $post;
				 
				// Detect if it is a single post with a post author
				if ( is_single() && isset( $post->post_author ) ) {
				 
				// Get author's display name 
				$display_name = get_the_author_meta( 'display_name', $post->post_author );
				 
				// If display name is not available then use nickname as display name
				if ( empty( $display_name ) )
				$display_name = get_the_author_meta( 'nickname', $post->post_author );
				 
				// Get author's biographical information or description
				$user_description = get_the_author_meta( 'user_description', $post->post_author );
				 
				// Get author's website URL 
				$user_website = get_the_author_meta('url', $post->post_author);
				 
				// Get link to the author archive page
				$user_posts = get_author_posts_url( get_the_author_meta( 'ID' , $post->post_author));
				  
				if ( ! empty( $display_name ) )
				 
				$author_details = '<p class="author_name">About ' . $display_name . '</p>';
				 
				if ( ! empty( $user_description ) )
				// Author avatar and bio
				 
				$author_details .= '<p class="author_details">' . get_avatar( get_the_author_meta('user_email') , 90 ) . nl2br( $user_description ). '</p>';
				 
				// $author_details .= '<p class="author_links"><a href="'. $user_posts .'">View all posts by ' . $display_name . '</a>';  
				 
				// Check if author has a website in their profile
				if ( ! empty( $user_website ) ) {
				 
				// Display author website link
				$author_details .= ' | <a href="' . $user_website .'" target="_blank" rel="nofollow">Website</a></p>';
				 
				} else { 
				// if there is no author website then just close the paragraph
				$author_details .= '</p>';
				}
				 
				// Pass all this info to post content  
				$content = $content . '<footer class="author_bio_section" >' . $author_details . '</footer>';
				}
				return $content;
				}   */
				 
				// Add our function to the post content filter 
		//		add_action( 'before_sidebar', 'wpb_author_info_box' );
				 
				// Allow HTML in author bio section 
		//		remove_filter('pre_user_description', 'wp_filter_kses');
				 ?>	
					</div>
				</div>
				<div class="col-md-3 right-section">
					<?php dynamic_sidebar('article-content'); ?>
				</div>
			</div>
		</div>	
	</section>
	<?php get_template_part( 'template-parts/post-related' ); // Get Related Posts template ?>
	<div class="clear"></div> 
	<?php if ( is_active_sidebar( 'footer-advertisement' ) ) : ?>
	<div class="bottom-ads">
		<?php dynamic_sidebar( 'footer-advertisement' );?>
	</div>
	<?php endif; ?>
	<div class="clear"></div> 
	<?php
get_footer();