<div class="pagination">
	<?php $page = get_query_var('paged'); ?>
	<?php if ($page != 0){ ?>
		<div class="search-pagination"><?php previous_posts_link(__('&larr; Previous Page', 'themejunkie')) ?></div>
		<div class="search-pagination"><?php echo '( &nbsp;Page '.$page.'&nbsp )'; ?></div>
	<?php } ?>
	    <div class="search-pagination"><?php next_posts_link(__('Next Page &rarr;', 'themejunkie')) ?></div>
</div>