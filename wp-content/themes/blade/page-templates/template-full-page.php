<?php
/*
Template Name: Scrolling Full Screen Sections
*/
?>
<?php get_header(); ?>

<?php the_post(); ?>

<?php

	$scrolling_page = blade_grve_post_meta( 'grve_scrolling_page' );
	$responsive_scrolling_page = blade_grve_post_meta( 'grve_responsive_scrolling', blade_grve_option( 'responsive_scrolling_page_visibility', 'yes' ) );
	$scrolling_lock_anchors = blade_grve_post_meta( 'grve_scrolling_lock_anchors', 'yes' );
	$scrolling_loop = blade_grve_post_meta( 'grve_scrolling_loop', 'none' );
	$scrolling_speed = blade_grve_post_meta( 'grve_scrolling_speed', 1000 );

	$wrapper_attributes = array();
	if( 'pilling' == $scrolling_page ) {
		$scrolling_page_id = 'grve-pilling-page';
		$scrolling_direction = blade_grve_post_meta( 'grve_scrolling_direction', 'vertical' );
		$wrapper_attributes[] = 'data-scroll-direction="' . esc_attr( $scrolling_direction ) . '"';
	} else {
		$scrolling_page_id = 'grve-fullpage';
	}
	$wrapper_attributes[] = 'id="' . esc_attr( $scrolling_page_id ) . '"';
	$wrapper_attributes[] = 'data-device-scrolling="' . esc_attr( $responsive_scrolling_page ) . '"';
	$wrapper_attributes[] = 'data-lock-anchors="' . esc_attr( $scrolling_lock_anchors ) . '"';
	$wrapper_attributes[] = 'data-scroll-loop="' . esc_attr( $scrolling_loop ) . '"';
	$wrapper_attributes[] = 'data-scroll-speed="' . esc_attr( $scrolling_speed ) . '"';

?>

			<!-- CONTENT -->
			<div id="grve-content" class="clearfix">
				<div class="grve-content-wrapper">
					<!-- MAIN CONTENT -->
					<div id="grve-main-content">
						<div class="grve-main-content-wrapper clearfix" style="padding: 0;">

							<!-- PAGE CONTENT -->
							<div id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
								<div <?php echo implode( ' ', $wrapper_attributes ); ?>>
									<?php the_content(); ?>
								</div>
							</div>
							<!-- END PAGE CONTENT -->

						</div>
					</div>
					<!-- END MAIN CONTENT -->

				</div>
			</div>
			<!-- END CONTENT -->

			<!-- SIDE AREA -->
			<?php
				$grve_sidearea_data = blade_grve_get_sidearea_data();
				blade_grve_print_side_area( $grve_sidearea_data );
			?>
			<!-- END SIDE AREA -->

			<!-- HIDDEN MENU -->
			<?php blade_grve_print_hidden_menu(); ?>
			<!-- END HIDDEN MENU -->

			<!-- CART AREA -->
			<?php blade_grve_print_cart_area(); ?>
			<!-- END CART AREA -->

			<?php blade_grve_print_search_modal(); ?>
			<?php blade_grve_print_form_modals(); ?>
			<?php blade_grve_print_language_modal(); ?>
			<?php blade_grve_print_social_modal(); ?>

		</div> <!-- end #grve-theme-wrapper -->

		<?php wp_footer(); // js scripts are inserted using this function ?>

	</body>

</html>