<!--googleoff: all-->
<div class="column_right">
	<h2>About This Site</h2>
	<p><?php bloginfo('description'); ?></p>
	<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?> <!-- Calling the widgets if you have them selected in the WordPress admin panel -->
	<?php endif; ?>
	<?php wp_tag_cloud(''); ?>
</div>
<!--googleon: all-->
<div class="clear"></div>