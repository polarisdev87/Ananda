<?php
/**
 * Order details
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! $order = wc_get_order( $order_id ) ) {
	return;
}

$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
if ( method_exists( $order, 'get_downloadable_items' ) ) {
	$downloads             = $order->get_downloadable_items();
	$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();

	if ( $show_downloads ) {
		wc_get_template( 'order/order-downloads.php', array( 'downloads' => $downloads, 'show_title' => true ) );
	}
}
?>
<h3 class="grve-align-center"><?php esc_html_e( 'Order details', 'woocommerce' ); ?></h3>
<table class="shop_table order_details">
	<thead>
		<tr>
			<th class="product-name grve-link-text grve-heading-color"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
			<th class="product-total grve-link-text grve-heading-color"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach( $order_items as $item_id => $item ) {
				$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );

				$product_get_id = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;
				wc_get_template( 'order/order-details-item.php', array(
					'order'              => $order,
					'item_id'            => $item_id,
					'item'               => $item,
					'show_purchase_note' => $show_purchase_note,
					'purchase_note'      => $product ? $product->get_purchase_note() : '',
					'product'            => $product,
				) );
			}
		?>
		<?php do_action( 'woocommerce_order_items_table', $order ); ?>
	</tbody>
	<tfoot>
		<?php
			foreach ( $order->get_order_item_totals() as $key => $total ) {
				?>
				<tr>
					<th scope="row"><?php echo wp_kses_post( $total['label'] ); ?></th>
					<td><?php echo wp_kses_post( $total['value'] ); ?></td>
				</tr>
				<?php
			}
		?>
			<?php if ( $order->get_customer_note() ) : ?>
				<tr>
					<th><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
					<td><?php echo wptexturize( $order->get_customer_note() ); ?></td>
				</tr>
			<?php endif; ?>
	</tfoot>
</table>

<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>

<?php
if ( $show_customer_details ) {
	wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
}

//Omit closing PHP tag to avoid accidental whitespace output errors.
