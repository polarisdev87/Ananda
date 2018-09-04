<?php
/**
 * Template Name: Reorders Page
 */


// Get all customer orders
if (!is_reorder()) {
	header("Location: /products");
} else {

	$user_state = get_user_meta( get_current_user_id(), 'billing_state', true );
	
	if (in_array(strtoupper($user_state), ['OK', 'MS', 'KS'])) {
		?>
		<style type="text/css">
			.product-section {
				display: none !important;
			}
			.product-section#product-section-10251 {
				display: block !important;
				margin: 0 auto;
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
					.add_to_cart_button {
						display: block !important;
					}
					.added_to_cart {
						display: none !important;
					}
					.mfp-bg {
						background: rgba(255,255,255,0.95) !important;
					}
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
					}
					img.mfp-img {
						max-width: 60% !important;
						margin-right: 1rem !important;
						object-fit: contain;
					}
					@media only screen and (min-width: 769px) {
						.grve-row {
							display: flex;
						}
						.grve-column {
							display: flex;
							flex-direction: column;
						}
						.grve-image {
						    display: flex;
						    align-items: center;
						}
						.grve-column h3 {
							min-height: 66px;
						}
						#pos-row h3 {
							min-height: 99px;
						}
					}
				</style>
				<script type="text/javascript">
					jQuery('.out_of_stock').click(function() {
						alert('Ananda Professional 600 mg Tinctures is temporarily out of stock and will be shipped at no cost on July 27.');
					});
				</script>
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
