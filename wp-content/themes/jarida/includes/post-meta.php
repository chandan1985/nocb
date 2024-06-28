<?php
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
global $get_meta;
if( ( tie_get_option( 'post_meta' ) && empty( $get_meta["tie_hide_meta"][0] ) ) || $get_meta["tie_hide_meta"][0] == 'no' ): ?>		
<p class="post-meta">
<?php if( tie_get_option( 'post_author' ) ): ?>		
	<span class="post-meta-author"><?php _e( 'Posted by: ' , 'tie' ); ?> <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) )?>" title="<?php sprintf( esc_attr__( 'View all posts by %s', 'tie' ), get_the_author() ) ?>"><?php echo get_the_author(); ?></a><?php
	if ( is_plugin_active( 'custom-byline/custom-byline.php' ) ):	
	global $post;
	$carosal = get_post_meta($post->ID, 'author_byline', true);
	if (isset($carosal) && !empty($carosal)) {
	for ($i = 0; $i < count($carosal['name']); $i++) { 
     $auth[] = $carosal['name'][$i]; 
	} ?>, <span class="byline"><?php echo implode(', ', $auth);?></span>
	<?php
	}
	endif;
	?>
	
	
	</span>
<?php endif; ?>
    <?php
    // see if the POST has a byline. if so, use it
    if ( !empty($get_meta["byline"][0]) ) {
        echo '<span class="pm-byline">'.$get_meta["byline"][0].'</span>';
// if not, see if the USER has a byline. is so, use it
    } elseif ( get_the_author_meta( 'byline' ) ) {
        echo '<span class="pm-byline">'.the_author_meta( 'byline' ).'</span>';
    }
    // else dont output a byline
    ?>
<?php if( tie_get_option( 'post_cats' ) ): ?>
	<span class="post-cats"><?php _e( 'in ' , 'tie' ); ?> <?php printf('%1$s', get_the_category_list( ', ' ) ); ?></span>
<?php endif; ?>	
<?php if( tie_get_option( 'post_date' ) && tie_get_option( 'time_format' ) != 'none' ): ?>		
	<?php __( 'on ' , 'tie' ); ?> <?php tie_get_time() ?>
<?php endif; ?>	
<?php if( tie_get_option( 'post_comments' ) ): ?>
	<span class="post-comments"><?php comments_popup_link( __( '0', 'tie' ), __( '1 Comment', 'tie' ), __( '% Comments', 'tie' ) ); ?></span>
<?php endif; ?>
<?php echo tie_views(); ?>
</p>
<div class="clear"></div>
<?php endif; ?>