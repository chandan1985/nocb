<?php get_header(); ?>
<?php $article_classes = array('post-listing','hnews','hentry','item');?>
<div class="content-wrap">

<?php
$config = unserialize(get_option('tdc_sponsored_content'));

if (!isset($config['layout']) || empty($config['layout'])) {
	$config['layout'] = 'CNP';
}


if ($config['layout'] == 'NCP') {
	echo '<aside class="sidebar-narrow sc-narrow">';
	dynamic_sidebar('tdc-sponsored-content-narrow');
	echo '</aside>';
}

if ($config['layout'] == 'NPC') {
     echo '<aside class="sidebar-narrow sc-narrow sponsored-npc">';
	dynamic_sidebar('tdc-sponsored-content-narrow');
	echo '</aside>';	
}

if ($config['layout'] == 'PCN') {
	echo '<aside class="sidebar sc-primary sponsored-pcn">';
	dynamic_sidebar('tdc-sponsored-content-primary');
	echo '</aside>';
}

if ($config['layout'] == 'PNC') {
    echo '<aside class="sidebar sc-primary1 sponsored-pnc">';
	dynamic_sidebar('tdc-sponsored-content-primary');
	echo '</aside>';
    echo '<aside class="sidebar-narrow sc-narrow sponsored-pnc-additional">';
	dynamic_sidebar('tdc-sponsored-content-narrow');
	echo '</aside>';
}

if ($config['layout'] == 'NPC') {
    echo '<aside class="sidebar sc-primary1 sponsored-npc-css">';
	dynamic_sidebar('tdc-sponsored-content-primary');
	echo '</aside>';	
}
?>
	<div class="content sc-content">
        <?php
        if( !get_post_meta($post->ID,'tie_hide_breadcrumbs',true) ){
            //tie_breadcrumbs();
        } else {
            $article_classes[] = 'hide-breadcrumbs';
        }
        if( get_post_meta($post->ID,'tie_hide_title',true) ){
            $article_classes[] = 'hide-title';
        }
        ?>
		
		<?php if ( ! have_posts() ) : ?>
		<div id="post-0" class="post not-found post-listing">
			<h1 class="post-title"><?php _e( 'Not Found', 'tie' ); ?></h1>
			<div class="entry">
				<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'tie' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		</div>
		<?php endif; ?>

		<?php
		$do_not_duplicate = array();

		while ( have_posts() ) : the_post();
			$get_meta = get_post_custom($post->ID);
			if( !empty( $get_meta['tie_review_position'][0] ) ){
				$review_position = $get_meta['tie_review_position'][0] ;
				$rv = $tie_reviews_attr;
			}
			
			if( !empty( $get_meta["tie_sidebar_pos"][0] ) && $get_meta["tie_sidebar_pos"][0] == 'full' ){
				if(tie_get_option( 'columns_num' ) == '2c') $content_width = 955;
				else $content_width = 1160;
			}
		?>

		<div class="sc_banner"> <?php _e('FROM OUR PARTNER', 'tdc_sc') ?> </div>
		
		<?php //Above Post Banner
		if(  empty( $get_meta["tie_hide_above"][0] ) ){
			if( !empty( $get_meta["tie_banner_above"][0] ) ) echo '<div class="e3lan-post">' .do_shortcode(htmlspecialchars_decode($get_meta["tie_banner_above"][0])) .'</div>';
			else tie_banner('banner_above' , '<div class="e3lan-post">' , '</div>' );
		}
		?>

				
		<article id="the-post" <?php if( !empty( $rv['review'] ) ) echo $rv['review']; post_class($article_classes); ?>>

			<div class="post-inner">
             <?php if( empty($get_meta["tie_hide_title"][0]) ){ ?>
				<h1 class="name post-title entry-title" itemprop="itemReviewed" itemscope itemtype="http://schema.org/Thing"><span itemprop="name"><?php the_title(); ?></span></h1>
             <?php } 
			  $config = unserialize(get_option('tdc_sponsored_content'));
			  if(!empty($config['sponsored_option_byline']) && $config['sponsored_option_byline'] == 'yes' ) {

			  	if ( is_plugin_active( 'custom-byline/custom-byline.php' ) ):   
			     $carosal = get_post_meta($id, 'author_byline', true);

			     if (isset($carosal) && !empty($carosal)) {
			    	for ($i = 0; $i < count($carosal['name']); $i++) { 
			     	$auth[] = $carosal['name'][$i]; 
			    	 } ?>
			    	 <p class="post-meta"><span class="post-meta-author">By: <?php echo implode(', ', $auth);?></span></p>
			     <?php }
			     endif;
			 	}
                 if( !empty( $get_meta['subhead'][0] ) ){ ?>
                    <h2 class="subhead"><?php echo $get_meta['subhead'][0]; ?></h2>
                <?php } ?>

				<div class="entry entry-content">
					<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'full' ); } ?><br><br>
					<?php the_content(); ?>
					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'tie' ), 'after' => '</div>' ) ); ?>
					
					<?php if( !empty( $review_position ) && ( $review_position == 'bottom' || $review_position == 'both' ) ) tie_get_review('review-bottom'); ?>

					<?php edit_post_link( __( 'Edit', 'tie' ), '<span class="edit-link">', '</span>' ); ?>
				</div><!-- .entry /-->
				<?php the_tags( '<span style="display:none">',' ', '</span>'); ?>
                <span style="display:none" class="updated" title="<?php the_time('Y-m-d');echo 'T';the_time('H:i:s');echo 'Z'; ?>"><?php the_time('g:i a D, F j, Y'); ?></span>
				
				<?php if( ( tie_get_option( 'share_post' ) &&  empty( $get_meta["tie_hide_share"][0] ) ) || $get_meta["tie_hide_share"][0] == 'no' ) get_template_part( 'includes/post-share' ); // Get Share Button template ?>
				
			</div><!-- .post-inner -->
		</article><!-- .post-listing -->
		<?php if( tie_get_option( 'post_tags' ) ) the_tags( '<p class="post-tag">'.__( 'Tagged with: ', 'tie' )  ,' ', '</p>'); ?>

		<section id="author-box">
			<div class="block-head">
				<h3>Brought to you by our Sponsor: 
			<?php 
				global $post;
				$sponsor = get_post_meta($post->ID, 'meta_sc_sponsor', true);
				echo '<i>' . $sponsor . '</i>';
			?> </h3>
			<div class="tdc_SC_sponsor_identity">
			<?php
				global $post;
				$img = get_post_meta($post->ID, 'meta_sc_sponsor_logo', true);
				if (!empty($img)) {
					echo '<img src="' . $img . '" class="tdc_SC_sponsor_logo"/>';
				}
				$blurb = get_post_meta($post->ID, 'meta_sc_sponsor_blurb', true);
				if (!empty($blurb)) {
					echo '<div>' . $blurb . '</div>';
				}
			?>
			</div>
			</div>
		</section><!-- #author-box -->

		<section id="related_content">
		
		<?php
		
			global $post;

			$sponsor = get_post_meta($post->ID, 'meta_sc_sponsor', true);

			$args = array(
                        	'post_type'    => 'sponsored_content',
                        	'orderby' => 'date',
                        	'order' => 'DESC',
                        	'meta_key' => 'meta_sc_sponsor',
                        	'posts_per_page' => 3,
                        	'paged' => 0,
				//'no_found_rows' => true,
				'post__not_in' => array( $post->ID ),
                               	'meta_key'     => 'meta_sc_sponsor',
                               	'meta_value'   => $sponsor,
                	);
			$query = new WP_Query($args);

			if ($query->found_posts > 0) {
				echo '<div class="block-head sc_related_section">';
				echo "<h3>More Content from:<i> " . $sponsor . "</i></h3>";

				echo '<div class="sc_related_wrapper">';



				$w = (100 / $query->found_posts) - 5;
				foreach ($query->posts as $sc_post) {
					echo '<div class="sc_related" style="width:' . $w . '%;">';
					echo '<a href="' . get_the_permalink($sc_post->ID) . '" rel="nofollow">' . $sc_post->post_title . '</a>';
					echo '</div>';
				}


				echo '</div>';
				echo '</div>';
				echo '</div>';
			}			
			if (!$query->found_posts)
			{
			 echo '</div>';
			}

		?>
		</section>

		<?php 
		endwhile;		
		if ($config['layout'] == 'PCN') 
		{
		echo '<aside class="sidebar-narrow sc-narrow sponsored-pcn-css">';
		dynamic_sidebar('tdc-sponsored-content-narrow');
		echo '</aside>';
		}	

        if ($config['layout'] == 'CPN') 
		{
        echo '<aside class="sidebar sc-primary sponsored-cpn-css">';
		dynamic_sidebar('tdc-sponsored-content-primary');
		echo '</aside>';
        echo '<aside class="sidebar-narrow sc-narrow sponsor-content-css">';
		dynamic_sidebar('tdc-sponsored-content-narrow');
		echo '</aside>'; 					       		
		}		
		?>	
<!-- .content -->
<?php
if ($config['layout'] == 'NCP') 
{
        echo '<aside class="sidebar sc-primary">';
        dynamic_sidebar('tdc-sponsored-content-primary');
        echo '</aside>';
}
if ($config['layout'] == 'CNP') 
{
        echo '<aside class="sidebar-narrow sc-narrow sponsor-cpn-css">';
        dynamic_sidebar('tdc-sponsored-content-narrow');
        echo '</aside>';
        echo '<aside class="sidebar sc-primary tdc-sponsored-primary">';
        dynamic_sidebar('tdc-sponsored-content-primary');
        echo '</aside>';
}
?>
</div><!-- content-wrap123 -->
<div class="clear"></div>
<?php get_footer(); ?>
