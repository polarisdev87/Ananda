<?php
/**
 * Related Products
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
	if ( empty( $product ) || ! $product->exists() ) {
		return;
	}

	$related = $product->get_related( $posts_per_page );

	if ( sizeof( $related ) == 0 ) return;

	$product_get_id = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;

	$args = apply_filters( 'woocommerce_related_products_args', array(
		'post_type'            => 'product',
		'ignore_sticky_posts'  => 1,
		'no_found_rows'        => 1,
		'posts_per_page'       => $posts_per_page,
		'orderby'              => $orderby,
		'post__in'             => $related,
		'post__not_in'         => array( $product_get_id )
	) );

	$products = new WP_Query( $args );

	$woocommerce_loop['columns'] = $columns;

	if ( $products->have_posts() ) : ?>
	<div id="grve-related-products" class="grve-singular-section grve-bookmark clearfix">
		<div class="grve-container grve-padding-top-md grve-padding-bottom-md grve-border grve-border-top">
			<div class="grve-wrapper">
				<div class="related products">

					<h2><?php esc_html_e( 'Related products', 'woocommerce' ); ?></h2>

					<?php woocommerce_product_loop_start(); ?>

						<?php while ( $products->have_posts() ) : $products->the_post(); ?>

							<?php wc_get_template_part( 'content', 'product' ); ?>

						<?php endwhile; // end of the loop. ?>

					<?php woocommerce_product_loop_end(); ?>

				</div>
			</div>
		</div>
	</div>
	<?php endif;
} else {
	if ( $related_products ) : ?>

	<div id="grve-related-products" class="grve-singular-section grve-bookmark clearfix">
		<div class="grve-container grve-padding-top-md grve-padding-bottom-md grve-border grve-border-top">
			<div class="grve-wrapper">
				<div class="related products">

					<h2><?php esc_html_e( 'Related products', 'woocommerce' ); ?></h2>


					<?php woocommerce_product_loop_start(); ?>

						<?php foreach ( $related_products as $related_product ) : ?>

							<?php
								$post_object = get_post( $related_product->get_id() );

								setup_postdata( $GLOBALS['post'] =& $post_object );

								wc_get_template_part( 'content', 'product' ); ?>

						<?php endforeach; ?>

					<?php woocommerce_product_loop_end(); ?>

				</div>
			</div>
		</div>
	</div>

	<?php endif;
}
wp_reset_postdata();

//Omit closing PHP tag to avoid accidental whitespace output errors.
