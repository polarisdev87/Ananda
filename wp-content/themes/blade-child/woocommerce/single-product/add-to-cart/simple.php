<?php
/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

$product_get_id = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;

?>

<?php
	// Availability
	$availability      = $product->get_availability();
	$availability_html = empty( $availability['availability'] ) ? '' : '<div class="grve-small-text stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</div>';

	echo apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $product );
?>
<div class="grve-product-form grve-border">
	<?php if ( $product->is_in_stock() ) : ?>

		<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

		<form class="cart" method="post" enctype='multipart/form-data'>
		 	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		 	<?php
		 		if ( ! $product->is_sold_individually() ) {
		 			woocommerce_quantity_input( array(
		 				'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
		 				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product ),
		 				'input_value' => ( isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : 1 )
		 			) );
		 		}
		 	?>

		 	<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product_get_id ); ?>" />

		 	<button type="submit" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

			<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
		</form>

		<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

	<?php endif; ?>

</div>
