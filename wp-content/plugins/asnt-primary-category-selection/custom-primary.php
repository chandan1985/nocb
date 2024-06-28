<?php
/**
 * Plugin Name: ASNT primary category selection
 * Description: Adds a meta box to the post editor screen to select the primary category
 * Version: 1.0.0
 * Author: Asentech
 * Author URI: https://www.asentechllc.com/
 */

// Add meta box to post editor screen
function add_primary_category_meta_box() {
    add_meta_box(
        'primary_category_meta_box',
        'Primary Category',
        'render_primary_category_meta_box',
        'post',
        'side'
    );
}
add_action( 'add_meta_boxes', 'add_primary_category_meta_box' );

// Render the primary category meta box
function render_primary_category_meta_box( $post ) {
    $primary_category_id = get_post_meta( $post->ID, '_primary_category', true );
    $categories = get_the_category( $post->ID );
    
    // Get a list of category IDs selected in the post or page's category meta box
    $category_ids = array_map( function( $category ) {
        return $category->term_id;
    }, $categories );
    ?>
    <label for="primary_category_id">Select primary category:</label>
    <select name="primary_category_id" id="primary_category_id">
        <option value="">Select a category</option>
        <?php foreach ( $category_ids as $category_id ) : ?>
            <?php $category = get_category( $category_id ); ?>
            <option value="<?php echo esc_attr( $category->term_id ); ?>" <?php selected( $primary_category_id, $category->term_id ); ?>>
                <?php echo esc_html( $category->name ); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}


// Save the primary category ID as post meta
function save_primary_category_meta_box( $post_id ) {
    if ( isset( $_POST['primary_category_id'] ) ) {
        update_post_meta( $post_id, '_primary_category', absint( $_POST['primary_category_id'] ) );
    }
}
add_action( 'save_post', 'save_primary_category_meta_box' );

// Get the primary category ID for a post
function get_primary_category_id( $post_id ) {
    $primary_category_id = get_post_meta( $post_id, '_primary_category', true );
    return $primary_category_id;
}

// Get the primary category name for a post
function get_primary_category_name( $post_id ) {
    $primary_category_id = get_primary_category_id( $post_id );
    if ( $primary_category_id ) {
        $primary_category = get_category( $primary_category_id );
        return $primary_category->name;
    }
    return '';
}

// Enqueue JavaScript to populate the custom meta box
function custom_meta_box_enqueue_scripts($hook) {
    if ('post.php' != $hook && 'post-new.php' != $hook) {
        return;
    }

    wp_enqueue_script('custom-meta-box', plugins_url('primary.js', __FILE__), array('jquery'), '1.0', true);
    wp_localize_script('custom-meta-box', 'ajaxurl', admin_url('admin-ajax.php'));
}
add_action('admin_enqueue_scripts', 'custom_meta_box_enqueue_scripts');