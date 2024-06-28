<?php
/**
 * Show options for ordering
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/orderby.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.6.0
 */

use wpsolr\core\classes\services\WPSOLR_Service_Container;
use wpsolr\core\classes\ui\WPSOLR_Query_Parameters;
use wpsolr\core\classes\utilities\WPSOLR_Escape;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php
// Use default WPSOLR orderby instead of WooCommerce default orderby
$orderby = WPSOLR_Service_Container::get_query()->get_wpsolr_sort();

// Remove default 'relevance' orderby item, as WPSOLR relevance is already there.
if ( isset( $catalog_orderby_options['relevance'] ) ) {
	unset( $catalog_orderby_options['relevance'] );
}
?>

<form class="woocommerce-ordering" method="get">
    <select name="<?php WPSOLR_Escape::echo_esc_attr( WPSOLR_Query_Parameters::SEARCH_PARAMETER_SORT ); ?>"
            class="orderby"
            aria-label="<?php esc_attr_e( 'Shop order', 'woocommerce' ); ?>">
		<?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
            <option value="<?php WPSOLR_Escape::echo_esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php WPSOLR_Escape::echo_esc_html( $name ); ?></option>
		<?php endforeach; ?>
    </select>
    <input type="hidden" name="paged" value="1"/>
	<?php wc_query_string_form_fields( null, array( 'orderby', 'submit', 'paged', 'product-page' ) ); ?>
</form>
