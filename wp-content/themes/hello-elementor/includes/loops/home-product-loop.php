<?php 
$productarray = array(); 
$prodcounter = 0;
?>
<?php if ( have_posts() ) : while ( have_posts()) : the_post(); ?>
	<?php
	$featuredproduct = is_object_in_term($post->ID,'product_category','sponsored-products');
	$productarray[$prodcounter] = array('link' => get_the_permalink(), 'title' => get_the_title(), 'excerpt' => limit_words(get_the_excerpt(),30), 'image' => get_needed_image('medium-image-hard'), 'featured' => $featuredproduct);
	$prodcounter++;
	?>
<?php endwhile; ?>
<?php endif; ?>
<?php wp_reset_query() ?>
<div class="mod-sub-product" style="left:-70%">
    <?php if($productarray[6][featured] == true){ echo '<span class="hp-featured-product">Featured Product</span>';} ?>
    <a href="<?php echo $productarray[6][link]; ?>" class="mod-sub-product-title"><?php echo $productarray[6][title]; ?></a>
            <a href="<?php echo $productarray[6][link]; ?>" class="mod-sub-product-image">
        <img src="<?php echo $productarray[6][image]; ?>" />
    </a>
    <a href="<?php echo $productarray[6][link]; ?>" class="mod-sub-product-link">View product &gt;</a>
</div>
<div class="mod-sub-product" style="left:-35%">
    <?php if($productarray[4][featured] == true){ echo '<span class="hp-featured-product">Featured Product</span>';} ?>
    <a href="<?php echo $productarray[4][link]; ?>" class="mod-sub-product-title"><?php echo $productarray[4][title]; ?></a>
            <a href="<?php echo $productarray[4][link]; ?>" class="mod-sub-product-image">
        <img src="<?php echo $productarray[4][image]; ?>" />
    </a>
    <a href="<?php echo $productarray[4][link]; ?>" class="mod-sub-product-link">View product &gt;</a>
</div>
<div class="mod-sub-product" style="left:0%">
    <?php if($productarray[2][featured] == true){ echo '<span class="hp-featured-product">Featured Product</span>';} ?>
    <a href="<?php echo $productarray[2][link]; ?>" class="mod-sub-product-title"><?php echo $productarray[2][title]; ?></a>
            <a href="<?php echo $productarray[2][link]; ?>" class="mod-sub-product-image">
        <img src="<?php echo $productarray[2][image]; ?>" />
    </a>
    <a href="<?php echo $productarray[2][link]; ?>" class="mod-sub-product-link">View product &gt;</a>
</div>
<div class="mod-sub-product mod-sub-product-featured" style="left:35%">
    <?php if($productarray[0][featured] == true){ echo '<span class="hp-featured-product">Featured Product</span>';} ?>
    <a href="<?php echo $productarray[0][link]; ?>" class="mod-sub-product-title"><?php echo $productarray[0][title]; ?></a>
            <a href="<?php echo $productarray[0][link]; ?>" class="mod-sub-product-image">
        <img src="<?php echo $productarray[0][image]; ?>" />
    </a>
    <a href="<?php echo $productarray[0][link]; ?>" class="mod-sub-product-link">View product &gt;</a>
</div>
<div class="mod-sub-product" style="left:70%">
    <?php if($productarray[1][featured] == true){ echo '<span class="hp-featured-product">Featured Product</span>';} ?>
    <a href="<?php echo $productarray[1][link]; ?>" class="mod-sub-product-title"><?php echo $productarray[1][title]; ?></a>
            <a href="<?php echo $productarray[1][link]; ?>" class="mod-sub-product-image">
        <img src="<?php echo $productarray[1][image]; ?>" />
    </a>
    <a href="<?php echo $productarray[1][link]; ?>" class="mod-sub-product-link">View product &gt;</a>
</div>
<div class="mod-sub-product" style="left:105%">
    <?php if($productarray[3][featured] == true){ echo '<span class="hp-featured-product">Featured Product</span>';} ?>
    <a href="<?php echo $productarray[3][link]; ?>" class="mod-sub-product-title"><?php echo $productarray[3][title]; ?></a>
            <a href="<?php echo $productarray[3][link]; ?>" class="mod-sub-product-image">
        <img src="<?php echo $productarray[3][image]; ?>" />
    </a>
    <a href="<?php echo $productarray[3][link]; ?>" class="mod-sub-product-link">View product &gt;</a>
</div>
<div class="mod-sub-product" style="left:140%">
    <?php if($productarray[5][featured] == true){ echo '<span class="hp-featured-product">Featured Product</span>';} ?>
    <a href="<?php echo $productarray[5][link]; ?>" class="mod-sub-product-title"><?php echo $productarray[5][title]; ?></a>
            <a href="<?php echo $productarray[5][link]; ?>" class="mod-sub-product-image">
        <img src="<?php echo $productarray[5][image]; ?>" />
    </a>
    <a href="<?php echo $productarray[5][link]; ?>" class="mod-sub-product-link">View product &gt;</a>
</div>