<div class="clear"></div>
<!--googleoff: all-->
<div class="banner">
	<?php
		if (is_front_page())
			dynamic_sidebar( 'Home - Advertising - Footer' );
		else
			dynamic_sidebar( 'Inside - Advertising - Footer' );
	?>
</div>
<div id="footer">
	<a href="http://bridgetowermedia.com"><img src="http://bridgetowermedia.com/files/2016/08/btmlogo140.png" alt="" /></a>
	<div class="footer-nav">
		<div class="menu_outer">
			<div class="menu_inner">
				<?php dynamic_sidebar( 'Footer Menu' ); ?>
			</div>
		</div>
		<div class="content">
			<?php 
				$footertext = quonfig( 12 );
				if( !empty( $footertext ) )
					echo( '<p>' . $footertext . '</p>' );
			?>
			<p>
				Copyright &#169; <?php echo( date( 'Y' ) . ' ' . get_bloginfo( 'name' ) );  ?> &nbsp; &#124; &nbsp;
				<a href='http://bridgetowermedia.com/privacy-policy/' target="_blank">Privacy Policy</a>&nbsp; &#124; &nbsp;
				<a href='http://bridgetowermedia.com/subscriber-agreement/' target="_blank">Subscriber Agreement</a>
			</p>
		</div>
	</div>
</div>
<!--googleon: all-->
</div>
<!--googleoff: all-->
<?php wp_footer(); ?>
<!--googleon: all-->
</body>
</html>