<?php
/**
 * Template Name: Products Page
 */


// Get all customer orders
$customer_orders = get_posts( array(
    'numberposts' => -1,
    'meta_key'    => '_customer_user',
    'meta_value'  => get_current_user_id(),
    'post_type'   => wc_get_order_types(),
    'post_status' => 'wc-completed', // array_keys( wc_get_order_statuses() ),
) );

$loyal_count = 1;

if ( count( $customer_orders ) >= $loyal_count ) {
	header("Location: /reorders");
} else {

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
					img.mfp-img {
						max-width: 60% !important;
						margin-right: 1rem !important;
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
