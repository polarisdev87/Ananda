<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

//Remove Single Product Hooks
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );

//Add Single Product Hooks
add_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
add_action( 'woocommerce_single_product_summary', 'blade_grve_woo_single_title', 5 );

?>
<!-- Chris -->
<style type="text/css">
#grve-modal-overlay, .mfp-bg, #grve-loader-overflow {
    background-color: rgba(255,255,255,1);
}
/*
#product-10226.product-type-grouped .price, #product-10226.product-type-grouped form.cart table {
	display: none;
}
#product-10103.product-type-grouped .price, #product-10103.product-type-grouped form.cart table {
	display: none;
}
#product-4464.product-type-grouped .price, #product-4464.product-type-grouped form.cart table {
	display: none;
}
*/
</style>
<?php
	/**
	 * woocommerce_before_single_product hook
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }

	// if (get_the_ID() == '10226') {
	//  	add_filter( 'woocommerce_quantity_input_args', 'custom_quantity', 10, 2 );
	// 	function custom_quantity( $args, $product ) {
	// 	    $args['input_value'] = 16;
	// 	    return $args;
	// 	}
	// }
?>
<div id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="grve-container">
		<?php
			/**
			 * woocommerce_before_single_product_summary hook
			 *
			 * @hooked woocommerce_show_product_sale_flash - 10
			 * @hooked woocommerce_show_product_images - 20
			 */
			do_action( 'woocommerce_before_single_product_summary' );
		
		?>
	
		<div id="grve-entry-summary" class="summary entry-summary grve-bookmark">
			<?php the_title( '<h3 class="product_title entry-title">', '</h3>' ); ?>
			<?php
				/**
				 * woocommerce_single_product_summary hook
				 *
				 * @hooked woocommerce_template_single_title - 5
				 * @hooked woocommerce_template_single_rating - 10
				 * @hooked woocommerce_template_single_price - 10
				 * @hooked woocommerce_template_single_excerpt - 20
				 * @hooked woocommerce_template_single_add_to_cart - 30
				 * @hooked woocommerce_template_single_meta - 40
				 * @hooked woocommerce_template_single_sharing - 50
				 * @hooked WC_Structured_Data::generate_product_data() - 60
				 */
				do_action( 'woocommerce_single_product_summary' );
			
			?>

		</div><!-- .summary -->
	</div>

	<?php
		/**
		 * woocommerce_after_single_product_summary hook
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
	?>
	
</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>

<script type="text/javascript">
	jQuery(document).ready(function() {
		/*

		// THC Free default quantity for grouped product
		if (jQuery('#product-10226').length > 0) {
			jQuery('#product-10226 button[type=submit]').attr('disabled', 'disabled');
			setTimeout(function() {
				jQuery('#product-10226 .qty[name="quantity[10251]"]').val(16);
				jQuery('#product-10226 button[type=submit]').attr('disabled', '').removeAttr('disabled');
			}, 1500);
		}
		// Counter Top Point of Sale Acrylic Display
		if (jQuery('#product-10103').length > 0) {
			jQuery('#product-10103 button[type=submit]').attr('disabled', 'disabled');
			setTimeout(function() {
				jQuery('#product-10103 .qty[name="quantity[9204]"]').val(12);
				jQuery('#product-10103 .qty[name="quantity[9205]"]').val(12);
				jQuery('#product-10103 .qty[name="quantity[9206]"]').val(8);
				jQuery('#product-10103 .qty[name="quantity[9197]"]').val(8);
				jQuery('#product-10103 .qty[name="quantity[9208]"]').val(16);
				jQuery('#product-10103 button[type=submit]').attr('disabled', '').removeAttr('disabled');
			}, 1500);
		}
		// Vertical Point of Sale Display
		if (jQuery('#product-4464').length > 0) {
			jQuery('#product-4464 button[type=submit]').attr('disabled', 'disabled');
			setTimeout(function() {
				jQuery('#product-4464 .qty[name="quantity[9204]"]').val(12);
				jQuery('#product-4464 .qty[name="quantity[9205]"]').val(12);
				jQuery('#product-4464 .qty[name="quantity[9206]"]').val(8);
				jQuery('#product-4464 .qty[name="quantity[9197]"]').val(8);
				jQuery('#product-4464 .qty[name="quantity[9208]"]').val(16);
				jQuery('#product-4464 button[type=submit]').attr('disabled', '').removeAttr('disabled');
			}, 1500);
		}
		*/

		// jQuery("input.qty").attr("readonly","readonly");
	});
</script>
