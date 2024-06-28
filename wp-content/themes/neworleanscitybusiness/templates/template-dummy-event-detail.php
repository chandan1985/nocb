<?php
/**
 * Template Name: DUMMY - Event detail
 *
 * @package ThemeScaffold
 */

get_header(); ?>

	<div class="container">
		<?php get_template_part( 'partials/event-item', 'details' ); ?>

		<div class="event-content">
			<div class="event-content__description post-content">
				<p class="event-label lead">Event Description</p>
				<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Porro aspernatur possimus nam eum culpa sapiente eveniet, reprehenderit accusamus quod harum! Error facilis alias beatae soluta voluptatum dolor eius veniam recusandae!</p>
				<blockquote>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam de isto magna dissensio est.</p>
				</blockquote>
			</div>
			<div class="event-content__sidebar">
				<p class="event-label lead">Share this event</p>

				<ul class="sharing">
					<li><a href="#"><?php pbm_svg_icon( 'twitter' ); ?></a></li>
					<li><a href="#"><?php pbm_svg_icon( 'facebook' ); ?></a></li>
					<li><a href="#"><?php pbm_svg_icon( 'linkedin' ); ?></a></li>
					<li><a href="#"><?php pbm_svg_icon( 'pinterest' ); ?></a></li>
					<li><a href="#"><?php pbm_svg_icon( 'email' ); ?></a></li>
				</ul>

				<div class="event-content__cta">
					<a href="#learnmore" class="button button--block event-item__link">Learn More<span class="screen-reader-text"> about [title]</span></a>
					<a href="#register" class="button button--block button--outline">Register<span class="screen-reader-text"> to attend [title]</span></a>
				</div>
			</div>
		</div>
	</div>

<?php
get_footer();
