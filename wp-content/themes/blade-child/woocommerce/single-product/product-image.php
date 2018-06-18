<?php
/**
 * Single Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $woocommerce, $product;

//Classes Images
$product_images_classes = array( 'images' );

if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
	$lighbox_enabled = get_option( 'woocommerce_enable_lightbox', '' );
} else {
	$lighbox_enabled = 'no';
}
if ( 'yes' != $lighbox_enabled && 'popup' == blade_grve_option( 'product_gallery_mode', 'popup' ) ) {
	$product_images_classes[] = 'grve-gallery-popup';
}
$product_images_class_string = implode( ' ', $product_images_classes );

//Classes
$product_image_classes = array( 'grve-product-image', 'woocommerce-product-gallery__image' );
$grve_product_image_effect = blade_grve_option( 'product_image_effect', 'zoom' );
if ( 'zoom' == $grve_product_image_effect ) {
	$product_image_classes[] = 'easyzoom';
}
$product_image_class_string = implode( ' ', $product_image_classes );


?>
<div id="grve-product-feature-image" class="<?php echo esc_attr( $product_images_class_string ); ?>">
	<div class="<?php echo esc_attr( $product_image_class_string ); ?>">
		<?php
			if ( has_post_thumbnail() ) {

				$image_title 	= esc_attr( get_the_title( get_post_thumbnail_id() ) );
				$image_caption 	= get_post( get_post_thumbnail_id() )->post_excerpt;
				$image_link  	= wp_get_attachment_url( get_post_thumbnail_id() );

				if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
					$attributes = array(
						'title' => $image_title,
						'alt'	=> $image_title,
					);
				} else {
					$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
					$full_size_image   = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
					$attributes = array(
						'title' => $image_title,
						'alt'	=> $image_title,
						'data-large_image'        => $full_size_image[0],
						'data-large_image_width'  => $full_size_image[1],
						'data-large_image_height' => $full_size_image[2],
					);
				}

				$image = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), $attributes );

				if ( method_exists( $product, 'get_gallery_image_ids' ) ) {
					$attachment_ids = $product->get_gallery_image_ids();
				} else {
					$attachment_ids = $product->get_gallery_attachment_ids();
				}

				$attachment_count = count( $attachment_ids );

				if ( $attachment_count > 0 ) {
					$gallery = '[product-gallery]';
				} else {
					$gallery = '';
				}

				echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" class="woocommerce-main-image zoom" title="%s" data-rel="prettyPhoto' . $gallery . '">%s</a>', $image_link, $image_caption, $image ), $post->ID );

			} else {

				echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" class="woocommerce-main-image zoom"><img class="wp-post-image" src="%s" data-large_image="%s" alt="%s" /></a>', wc_placeholder_img_src(), wc_placeholder_img_src(), wc_placeholder_img_src(), esc_html__( 'Placeholder', 'woocommerce' ) ), $post->ID );

			}
		?>
	</div>
	<?php do_action( 'woocommerce_product_thumbnails' ); ?>

</div>
