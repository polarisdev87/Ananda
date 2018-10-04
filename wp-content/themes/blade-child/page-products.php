<?php
/**
 * Template Name: Products Page
 */


// Get all customer orders
if (is_reorder()) {
	header("Location: /reorders");
} else {

	$user_billing_state = get_user_meta( get_current_user_id(), 'billing_state', true );
	$user_shipping_state = get_user_meta( get_current_user_id(), 'shipping_state', true );

	if (in_array(strtoupper($user_billing_state), ['OK', 'MS', 'KS']) || in_array(strtoupper($user_shipping_state), ['OK', 'MS', 'KS'])) {
		?>
		<style type="text/css">
			#product-section-4464, #product-section-10103 {
				display: none;
			}
		</style>
		<?php
	}


?>
<?php get_header(); ?>

<?php the_post(); ?>

<?php blade_grve_print_header_title( 'page' ); ?>
<?php blade_grve_print_header_breadcrumbs( 'page' ); ?>
<?php blade_grve_print_anchor_menu( 'page' ); ?>
		
<?php
	if ( 'yes' == blade_grve_post_meta( 'grve_disable_content' ) ) {
		get_footer();
	} else {
?>
		<!-- CONTENT -->
		<div id="grve-content" class="clearfix <?php echo blade_grve_sidebar_class( 'page' ); ?>">
			<div class="grve-content-wrapper">
				<!-- MAIN CONTENT -->
				<div id="grve-main-content">
					<div class="grve-main-content-wrapper clearfix">

						<!-- PAGE CONTENT -->
						<div id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
							<?php the_content(); ?>
						</div>
						<!-- END PAGE CONTENT -->

						<?php if ( blade_grve_visibility( 'page_comments_visibility' ) ) { ?>
							<?php comments_template(); ?>
						<?php } ?>

					</div>
				</div>
				<style type="text/css">
					button.mfp-arrow, .mfp-title, .mfp-counter {
						color: #000 !important;
					}
					.mfp-figure figure {
						display: flex;
						align-items: center;
						justify-content: center;
						padding: 0 100px;
					}
					.mfp-bottom-bar {
						position: relative !important;
						margin-top: 0 !important;
					}
					.mfp-title {
						max-width: 450px;
						font-size: 26px;
						line-height: 1.7;
					}
					.mfp-figure small {
						font-size: 14px !important;
						line-height: 1.5 !important;
						color: #4a4a4a;
					}
					img.mfp-img {
						max-width: 60% !important;
						margin-right: 1rem !important;
						object-fit: contain;
					}

					@media only screen and (max-width: 768px) {
						/*.woocommerce div.product div#grve-product-feature-image .thumbnails {
							max-height: 400px;
							padding-right: 7px;

							overflow-y: scroll;
			  				-webkit-overflow-scrolling: touch;
			  			}
						.woocommerce div.product div#grve-product-feature-image .thumbnails::-webkit-scrollbar {
							background-color: #d9d9d9;
							width: 7px;
							border-radius: 4px;
						}
						.woocommerce div.product div#grve-product-feature-image .thumbnails::-webkit-scrollbar-thumb {
							background-color: #7d7d7d;
							border-radius: 4px;
						}*/
					}
				</style>
				<!-- END MAIN CONTENT -->

				<?php blade_grve_set_current_view( 'page' ); ?>
				<?php get_sidebar(); ?>

			</div>
		</div>
		<!-- END CONTENT -->

	<?php get_footer(); ?>

<?php
	}
}
