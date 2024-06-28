<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Mag_Lite
 */

if ( ! function_exists( 'mag_lite_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function mag_lite_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: post date. */
			esc_html_x( ' %s', 'post date', 'mag-lite' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		echo '<span class="posted-on"><i class="fa fa-clock-o"></i>' . $posted_on . '</span>'; // WPCS: XSS OK.

	}
endif;
if ( ! function_exists( 'mag_lite_posted_by' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function mag_lite_posted_by() {
		$byline = sprintf(
			/* translators: %s: post author. */
			esc_html_x( 'By %s', 'post author', 'mag-lite' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="byline"><i class="fa fa-user"></i> ' . $byline . '</span>'; // WPCS: XSS OK.

	}
endif;


if ( ! function_exists( 'mag_lite_entry_categories' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function mag_lite_entry_categories() {

		global $post;
		$post_id         = $post->ID;
		$categories_list = get_the_category( $post_id );
		if ( ! empty( $categories_list ) ) {
			?>
			<div class="post-cat-list">
				<?php
				foreach ( $categories_list as $cat_data ) {
					$cat_name = $cat_data->name;
					$cat_id   = $cat_data->term_id;
					$cat_link = get_category_link( $cat_id );
					?>
					<span class="cat-links mag-lite-cat-<?php echo esc_attr( $cat_id ); ?>"><a
							href="<?php echo esc_url( $cat_link ); ?>"><?php echo esc_html( $cat_name ); ?></a></span>
					<?php
				}
				?>
			</div>
			<?php
		}


	}
endif;	

if ( ! function_exists( 'mag_lite_entry_tags' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function mag_lite_entry_tags() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'mag-lite' ) );
			if ( $tags_list ) {
				/* translators: 1: list of tags. */
				printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'mag-lite' ) . '</span>', $tags_list ); // WPCS: XSS OK.
			}
		}	


	}
endif;	

if ( ! function_exists( 'mag_lite_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function mag_lite_entry_footer() {

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link(
				sprintf(
					wp_kses(
						/* translators: %s: post title */
						__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'mag-lite' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					get_the_title()
				)
			);
			echo '</span>';
		}

		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'mag-lite' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;

if( ! function_exists( 'mag_lite_time_ago' ) ):
/** 
 *  Prints HTML with meta information for the number of views
 *
 */
function mag_lite_time_ago(){	

	$time_diff = human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago'; 	
			
	echo '<span class="date"><i class="fa fa-clock-o"></i>' . $time_diff .' </span>'; // WPCS: XSS OK.
		

}
endif;






