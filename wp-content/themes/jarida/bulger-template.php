<?php
/*
Template Name: Bulger Report
*/
?>
<?php get_header(); ?>
<link rel="stylesheet" href="/wp-content/themes/jarida/bulger-images/bulger-style.css" type="text/css" media="screen" />
<link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Merriweather' rel='stylesheet' type='text/css'>

<div id="wrap-bulger">
	<div id="container-bulger">
		<div id="header-bulger">
			<h1>The <span class="large-orange">Bulger</span> Beat</h1>
			<span id="tag">Coverage of the case against reputed mobster James “Whitey” Bulger</span>
				<img src="<?php bloginfo('template_url'); ?>/bulger-images/bulger-headshot.png" alt="James whitey Bulger" style="float:right; margin-top:-105px;"/>
		</div>	
		<div id="social-bluger">
			<a href="https://www.facebook.com/MassLW"><img src="<?php bloginfo('template_url'); ?>/bulger-images/facebook.png" alt="facebook" /></a>
			<a href="http://twitter.com/masslw"><img src="<?php bloginfo('template_url'); ?>/bulger-images/twitter.png" alt="twitter" /></a>
			<a href="/category/the-bulger-beat/feed"><img src="<?php bloginfo('template_url'); ?>/bulger-images/rss.png" alt="rss" /></a>
		</div>
		<div id="content-bulger">
			<div><span class="feature-bulger">featured story</span></div>
				<div id="left-bulger">
			
			
					<?php the_post(); ?>
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<div class="entry-bulger">
														
						<?php query_posts('category_name=bulger-beat-feature&posts_per_page=1'); ?>

						<?php while (have_posts()) : the_post(); ?>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<?php 
						if ( has_post_thumbnail() ) {
							the_post_thumbnail();
						}
						?>
						<?php the_excerpt(); ?>         
						
						<span class="rounded-button" style="float:right;"><a href="<?php the_permalink(); ?>">read more</a></span>

						<?php endwhile; ?>

						</div>	
					</div>
					<div>
					<div id="left-under-one">
					<div><span class="feature-bulger">bulger news</span></div>
					<?php if ( !function_exists('dynamic_sidebar')
						|| !dynamic_sidebar('Bulger - Bulger News') ) : ?>
						<?php endif; ?>						
						<?php if ( !function_exists('dynamic_sidebar')
						|| !dynamic_sidebar('Bulger - Ad Left side') ) : ?>
						<?php endif; ?>
						<?php if ( !function_exists('dynamic_sidebar')
						|| !dynamic_sidebar('Bulger - Bulger Timeline') ) : ?>
						<?php endif; ?>
						<?php if ( !function_exists('dynamic_sidebar')
						|| !dynamic_sidebar('Bulger - Bulger Twitter') ) : ?>
						<?php endif; ?>



					</div>
					<div id="left-under-two">
					<?php if ( !function_exists('dynamic_sidebar')
						|| !dynamic_sidebar('Bulger - Bulger Podcasts') ) : ?>
						<?php endif; ?>
						
						
						<?php if ( !function_exists('dynamic_sidebar')
						|| !dynamic_sidebar('Bulger - Bulger Files') ) : ?>
						<?php endif; ?>
						
						<?php if ( !function_exists('dynamic_sidebar')
						|| !dynamic_sidebar('Bulger - Lawyers Weekly In The News') ) : ?>
						<?php endif; ?>
						
						<?php if ( !function_exists('dynamic_sidebar')
						|| !dynamic_sidebar('Bulger - Bulger Bench') ) : ?>
						<?php endif; ?>


						
					</div>
					<div class="clear"></div>
					
					</div>
				</div>
				
				
				
				
				
				
				<div id="right-bulger">
				<?php if ( !function_exists('dynamic_sidebar')
			|| !dynamic_sidebar('Bulger - Ad Top Left and Countdown') ) : ?>
	<?php endif; ?>
	<?php if ( !function_exists('dynamic_sidebar')
			|| !dynamic_sidebar('Bulger - Poll Question') ) : ?>
	<?php endif; ?>
	<?php if ( !function_exists('dynamic_sidebar')
			|| !dynamic_sidebar('Bulger - Sources Say') ) : ?>
	<?php endif; ?>
				</div>
				<div class="clear"></div>
				
				
				
				
		</div>
	</div>
</div>	
	
	<?php get_footer(); ?>