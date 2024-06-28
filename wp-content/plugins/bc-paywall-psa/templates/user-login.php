<?php 
/*Template Name: User login*/

if(isset($_GET['tpi']) && $_GET['tpi']=='login'){
   wp_head();
 }else{

  get_header();

 }  ?>
<?php $article_classes = array('post-listing','post');?>
<div class="container">
<div class="row">
	
<div class="col-sm-9">
	
	
		
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
		
		<?php 
			if( function_exists('bp_current_component') && bp_current_component() ) $current_id = get_queried_object_id();
			else $current_id = $post->ID;
			$get_meta = get_post_custom( $current_id );
			
			
		?>
		

		<article <?php if( !empty( $rv['review'] ) ) echo $rv['review']; post_class($article_classes); ?>>
			<?php get_template_part( 'includes/post-head' ); // Get Post Head template ?>
			<div class="post-inner">
				<h1 class="name post-title entry-title" itemprop="itemReviewed" itemscope itemtype="http://schema.org/Thing"><span itemprop="name"><?php the_title(); ?></span></h1>
				
				<p class="post-meta"></p>
				<div class="clear"></div>
				<div class="entry">
					
					<?php the_content(); ?>
					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'tie' ), 'after' => '</div>' ) ); ?>

					<?php edit_post_link( __( 'Edit', 'tie' ), '<span class="edit-link">', '</span>' ); ?>
				</div><!-- .entry /-->	
				<span style="display:none" class="updated"><?php the_time( 'Y-m-d' ); ?></span>
				<?php if ( get_the_author_meta( 'google' ) ){ ?>
				<div style="display:none" class="vcard author" itemprop="author" itemscope itemtype="http://schema.org/Person"><strong class="fn" itemprop="name"><a href="<?php the_author_meta( 'google' ); ?>?rel=author">+<?php echo get_the_author(); ?></a></strong></div>
				<?php }else{ ?>
				<div style="display:none" class="vcard author" itemprop="author" itemscope itemtype="http://schema.org/Person"><strong class="fn" itemprop="name"><?php the_author_posts_link(); ?></strong></div>
				<?php } ?>
				
			</div><!-- .post-inner -->
		</article><!-- .post-listing -->
		<?php endwhile; ?>
		</div>
		<div class="col-sm-3">
			<?php
				if ( is_active_sidebar( 'right-sidebar-area' ) ) 
				{
					dynamic_sidebar('right-sidebar-area');
				}
				else
				{
					get_sidebar();
				}	
			?>
		</div>
		</div>
		</div>

<?php 
 if(isset($_GET['tpi']) && $_GET['tpi']=='login'){
   wp_footer();?>
<style>
	.content {	
	padding: 0px 45px 41px 278px;
}</style>
 <?php }else{	

  get_footer();

 } ?>