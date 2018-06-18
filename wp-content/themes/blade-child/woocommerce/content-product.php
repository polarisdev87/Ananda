<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

// Ensure visibility
// if ( empty( $product ) || ! $product->is_visible() ) {
// 	return;
// }

// Extra post classes
$classes = array();

$image_size = 'shop_catalog';
//Second Image Classes
$image_classes = array();
$image_classes[] = 'attachment-' . $image_size;
$image_classes[] = 'size-' . $image_size;
$image_classes[] = 'grve-product-thumbnail-second';
$image_class_string = implode( ' ', $image_classes );

//Second Product Image
$product_thumb_second_id = '';
if ( method_exists( $product, 'get_gallery_image_ids' ) ) {
	$attachment_ids = $product->get_gallery_image_ids();
} else {
	$attachment_ids = $product->get_gallery_attachment_ids();
}
if ( $attachment_ids ) {
	$loop = 0;
	foreach ( $attachment_ids as $attachment_id ) {
		$image_link = wp_get_attachment_url( $attachment_id );
		if (!$image_link) {
			continue;
		}
		$loop++;
		$product_thumb_second_id = $attachment_id;
		if ($loop == 1) {
			break;
		}
	}
}

$grve_product_overview_image_effect = blade_grve_option( 'product_overview_image_effect', 'second' );

if ( 'second' == $grve_product_overview_image_effect && !empty( $product_thumb_second_id ) ) {
	$classes[] = 'grve-with-second-image';
}

//Remove Actions
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title' , 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

//Add Actions
add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_title', 9);

?>
<li <?php post_class( $classes ); ?>>

	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

	<div class="grve-product-item">
		<div class="grve-product-media grve-image-hover">

			<a href="<?php echo is_user_logged_in() ? esc_url( get_permalink() ) : esc_url('/my-account'); ?>">

				<?php
					/**
					 * woocommerce_before_shop_loop_item_title hook
					 *
					 * @hooked woocommerce_show_product_loop_sale_flash - 10
					 * @hooked woocommerce_template_loop_product_thumb_secondnail - 10
					 */
					do_action( 'woocommerce_before_shop_loop_item_title' );

					if ( 'second' == $grve_product_overview_image_effect && !empty( $product_thumb_second_id ) ) {
						echo wp_get_attachment_image( $product_thumb_second_id, $image_size , "", array( 'class' => $image_class_string ) );
					}

					/**
					 * woocommerce_shop_loop_item_title hook
					 *
					 * @hooked woocommerce_template_loop_product_title - 10
					 */
					do_action( 'woocommerce_shop_loop_item_title' );

					/**
					 * woocommerce_after_shop_loop_item_title hook
					 *
					 * @hooked woocommerce_template_loop_rating - 5
					 * @hooked woocommerce_template_loop_price - 10
					 */
					do_action( 'woocommerce_after_shop_loop_item_title' );
				?>

			</a>

			<?php woocommerce_template_loop_rating(); ?>

			<?php if (is_user_logged_in()) : ?>
			<div class="grve-product-content">
				<div class="grve-product-switcher">
					<div class="grve-product-price grve-link-text">
						<?php woocommerce_template_loop_price(); ?>
					</div>
					<div class="grve-add-to-cart-btn grve-link-text">
						<?php woocommerce_template_loop_add_to_cart(); ?>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<?php

		/**
		 * woocommerce_after_shop_loop_item hook
		 *
		 * @hooked woocommerce_template_loop_add_to_cart - 10
		 */
		do_action( 'woocommerce_after_shop_loop_item' );

	?>

</li>
