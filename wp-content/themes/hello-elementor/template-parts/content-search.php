<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package petage
 */
global $post;

$featured_image = get_the_post_thumbnail($post, 'category-list-thumb');
$post_date = get_the_date('M j, Y, g:iA T', $post);
$content_post = get_post($post);
$content = $content_post->post_content;
$content = strip_tags($content);
$short_content = substr($content, 0, 335) . '[...]';
$categories = get_the_category($post);
$cat_name = $categories[0]->name;

// $wpseo_primary_term = new WPSEO_Primary_Term( 'category', $id );
// $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
$wpseo_primary_term = get_primary_category_id(get_the_id());
$term = get_term( $wpseo_primary_term );
$term_name = $term->name;
$term_link = get_category_link($term);

if(empty($term_name)){
	$term_name = $cat_name;
}

?>

<div class="results-listing" id='post-<?php the_ID(); ?>' <?php post_class(); ?>>
    <div class="row">
		<?php if ($featured_image) { ?>
		<div class="col-sm-9 post-item__content">
		<?php } else { ?>
		<div class="col-sm-12 post-item__content">
			<?php } ?>
			<?php if (!empty($term_name)): ?>
			<span class="tag-list">
				<a href="<?php echo esc_url($term_link); ?>"><?php print $term_name; ?></a>
			</span>
			<?php endif; ?>
			<?php if (!empty($post->post_date)): ?>
				<span class="dateline">
					<?php print $post_date; ?>
				</span>
			<?php endif; ?>
				<?php the_title(
        sprintf(
            '<h4 class="entry-title"><a href="%s" rel="bookmark">',
            esc_url(get_permalink())
        ),
        '</a></h4>'
    ); ?>
				<p class="body-text">
					<?php
						if(strlen($content) > 335){
							print $short_content;
						}else{
							print $content;
						}
						// print $short_content; 
					?>
				</p>
		</div>
		<?php if ($featured_image) { ?>
		<div class="col-sm-3">
		<?php print $featured_image; ?>
		</div>
		<?php } ?>
	</div>
</div>
