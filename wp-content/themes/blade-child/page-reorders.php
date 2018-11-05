<?php
/**
 * Template Name: Reorders Page
 */


// Get all customer orders
if (!is_reorder()) {
	header("Location: /products");
} else {

	$user_billing_state = get_user_meta( get_current_user_id(), 'billing_state', true );
	$user_shipping_state = get_user_meta( get_current_user_id(), 'shipping_state', true );
	
	if (in_array(strtoupper($user_billing_state), ['OK', 'MS', 'KS']) || in_array(strtoupper($user_shipping_state), ['OK', 'MS', 'KS'])) {
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

	if (!is_reorder_pets()) {
		?>
		<style type="text/css">
			.product-section#product-section-13166 {
				display: none !important;
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

					.invalid_state_popup_overlay {
    					background-color: rgba(255,255,255,0.90);
					    position: fixed;
					    top: 0;
					    left: 0;
					    right: 0;
					    bottom: 0;
					    width: 100%;
					    height: 100%;
					    cursor: pointer;
					    z-index: 9998;
					}
					.invalid_state_popup {
						position: fixed;
						left: 50%;
						top: 50%;
						transform: translate(-50%, -50%);
						width: 500px;
						height: 350px;
					    background-color: #232323;
					    color: #777777;
					    z-index: 9999;
					}
					.invalid_state_popup_close {
						position: absolute;
						top: -8px;
						right: -8px;
						font-size: 72px;
						line-height: 1em;
						width: 1em;
						text-align: center;
						cursor: pointer;
					}
					.invalid_state_popup_content {
						display: flex;
						width: 100%;
						height: 100%;
						align-items: center;
						justify-content: center;
						padding: 38px;
						flex-direction: column;
					}
					.invalid_state_popup_message {
						color: #fff;
						text-transform: none;
					}
				</style>
				<script type="text/javascript">
					jQuery('.out_of_stock').click(function() {
						alert('Ananda Professional 600 mg Tinctures is temporarily out of stock and will be shipped at no cost on July 27.');
					});
				</script>
				<?php if (!check_if_valid_states() && false) { 
                    $customer = new WC_Customer(get_current_user_id()); ?>
					<div class="invalid_state_popup_overlay"></div>
					<div class="invalid_state_popup">
						<div class="invalid_state_popup_close">&times;</div>
						<div class="invalid_state_popup_content">
							<div class="grve-h6 invalid_state_popup_message">
								While Ananda Professional CBD products are federally compliant and can be legally sold in all states, <?php echo get_full_state_name($customer->get_shipping_state()); ?> has regulations that make it not prudent for us to sell our products in <?php echo get_full_state_name($customer->get_shipping_state()); ?> at this time.<br/><br/>
								Legislation continuously changes and, if it is okay with you, would you mind if we contact you in the future once the regulations on CBD are favorable?
							</div>
							<a class="grve-btn grve-woo-btn grve-fullwidth-btn grve-bg-primary-1 grve-bg-hover-black invalid_state_popup_action" href="javascript:void(0)"><span>Click here to stay updated</span></a>
						</div>
						</div>
					</div>
					<script type="text/javascript">
						jQuery('.invalid_state_popup_overlay, .invalid_state_popup_close').click(function() {
							jQuery('.invalid_state_popup_overlay').hide();
							jQuery('.invalid_state_popup').hide();
						});
						jQuery('.invalid_state_popup_action').click(function() {
							jQuery.post( '/?action_name=subscribe_red_states', {'customer_id': '<?php echo get_current_user_id(); ?>'});
							jQuery('.invalid_state_popup_overlay').hide();
							jQuery('.invalid_state_popup').hide();
						})
					</script>
				<?php } ?>
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
