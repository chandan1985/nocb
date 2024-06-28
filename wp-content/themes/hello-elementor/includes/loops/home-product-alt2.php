<?php 
    $productarray = array();
?>
<h2 class="product-title home-product-alt-title"><span class="petage-red">Stockroom //</span><br>New Products</h2>
<?php if ( have_posts() ) : ?>
<div class="product-frame"><div class="product-slide">
<?php while ( have_posts()) : the_post(); ?>
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
</div>
<div class="product-nav">
    <a href="#" class="product-button product-down fa fa-caret-down"></a>
    <a href="#" class="product-button product-up fa fa-caret-up"></a>
</div>
</div>
<script>
    $(document).ready(function(){
        var frameHeight;
        var index = 0;
        function setFrameHeight(position){
            var slideTop = 0;
            for (var i=0; i<position; i++){
                slideTop += $(".product-slide .product-recalls:eq(" + i + ")").height() + 62;
            }
            $(".product-frame .product-slide").css({"top" : "-" + slideTop + "px"});
            var height1 = $(".product-slide .product-recalls:eq(" + position + ")").height() + 62;
            position++;
            var height2 = $(".product-slide .product-recalls:eq(" + position + ")").height() + 62;
            position++;
            var height3 = $(".product-slide .product-recalls:eq(" + position + ")").height() + 62;
            frameHeight = height1 + height2 + height3 + 40;
            $(".product-frame").css({"height" : frameHeight + "px"});
        }
        setFrameHeight(index);
        $(".product-down").click(function(){
            if (index<3){
                index++;
                setFrameHeight(index);
            }
            return false;
        });
        $(".product-up").click(function(){
            if (index>0){
                index--;
                setFrameHeight(index);
            }
            return false;
        });
    });
</script>
<?php endif; ?>
<?php wp_reset_query() ?>