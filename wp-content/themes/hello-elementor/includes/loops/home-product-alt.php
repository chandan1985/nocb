<?php 
    $productarray = array();
?>
<h2 class="product-title home-product-alt-title"><span class="petage-red">Stockroom //</span><br>New Products</h2>
<?php if ( have_posts() ) : while ( have_posts()) : the_post(); ?>
	<?php
	$featuredproduct = is_object_in_term($post->ID,'product_category','sponsored-products');
	$productarray = array('link' => get_the_permalink(), 'title' => get_the_title(), 'excerpt' => limit_words(get_the_excerpt(),30), 'image' => get_needed_image('medium-image-hard'), 'featured' => $featuredproduct);
	
	?>

    <div class="product-recalls product-recalls-none clearfix">
        <?php if($productarray[featured] == true){ echo '<span class="hp-featured-product">Featured Product</span>';} ?>
        <h3><a href="<?php echo $productarray[link]; ?>"><?php echo $productarray[title]; ?></a></h3>
        <a href="<?php echo $productarray[link]; ?>"><img src="<?php echo $productarray[image]; ?>" /></a>
        <a href="<?php echo $productarray[link]; ?>">View Product ></a>
    </div>
    
<?php endwhile; ?>
<h2 class="product-title home-product-alt-title">&nbsp;</h2>
<?php endif; ?>
<?php wp_reset_query() ?>