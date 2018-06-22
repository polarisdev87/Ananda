<?php
/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//Add Single Product Hooks
add_action( 'grve_woocommerce_after_single_product_summary_meta', 'woocommerce_template_single_meta', 10 );

add_action( 'grve_woocommerce_after_single_product_summary_sections', 'woocommerce_upsell_display', 10 );
add_action( 'grve_woocommerce_after_single_product_summary_sections', 'woocommerce_output_related_products', 20 );


get_header( 'shop' ); ?>


	<?php
		/**
		 * woocommerce_before_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 */
		do_action( 'woocommerce_before_main_content' );
	?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php wc_get_template_part( 'content', 'single-product' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php
		/**
		 * woocommerce_after_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>



	<div class="grve-single-wrapper">
		<div id="grve-single-post-meta-bar" class="grve-singular-section grve-align-center clearfix">
			<div class="grve-container grve-padding-top-md grve-padding-bottom-md grve-border grve-border-top">
				<div class="grve-wrapper">

					<?php
						/**
						 * grve_woocommerce_after_single_product_summary_meta hook
						 *
						 * @hooked woocommerce_template_single_meta - 10 (outputs product meta)
						 */

						do_action( 'grve_woocommerce_after_single_product_summary_meta' );
					?>

				</div>
			</div>
		</div>

		<?php
			/**
			 * grve_woocommerce_after_single_product_summary_sections hook
			 *
			 * @hooked woocommerce_upsell_display - 10
			 * @hooked woocommerce_output_related_products - 20
			 */
			do_action( 'grve_woocommerce_after_single_product_summary_sections' );
		?>
	</div>
	<?php
		/**
		 * woocommerce_sidebar hook
		 *
		 * @hooked woocommerce_get_sidebar - 10
		 */
		do_action( 'woocommerce_sidebar' );
	?>

<?php get_footer( 'shop' );
