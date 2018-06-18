<?php
/**
 * The Gallery Post Type Template
 */
?>

<?php
if ( is_singular() ) {
	$grve_disable_media = blade_grve_post_meta( 'grve_disable_media' );
	$slider_items = blade_grve_post_meta( 'grve_post_slider_items' );
	$gallery_mode = blade_grve_post_meta( 'grve_post_type_gallery_mode', 'gallery' );
	$gallery_image_mode = blade_grve_post_meta( 'grve_post_type_gallery_image_mode' );
	$image_size_slider = 'blade-grve-large-rect-horizontal';
	if ( 'resize' == $gallery_image_mode ) {
		$image_size_slider = 'blade-grve-fullscreen';
	}
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class('grve-single-post'); ?> itemscope itemType="http://schema.org/BlogPosting">
<?php
		if ( !empty( $slider_items ) && 'yes' != $grve_disable_media ) {
?>
			<div id="grve-single-media">
				<div class="grve-container">
					<?php blade_grve_print_gallery_slider( $gallery_mode, $slider_items, $image_size_slider  ); ?>
				</div>
			</div>
<?php
		}
?>
		<div id="grve-post-content">
			<?php blade_grve_print_post_simple_title(); ?>
			<?php blade_grve_print_post_structured_data(); ?>
			<div itemprop="articleBody">
				<?php the_content(); ?>
			</div>
		</div>

	</article>

<?php
} else {
	$grve_post_class = blade_grve_get_post_class();
?>

	<!-- Article -->
	<article id="post-<?php the_ID(); ?>" <?php post_class( $grve_post_class ); ?> itemscope itemType="http://schema.org/BlogPosting">
		<?php do_action( 'blade_grve_inner_post_loop_item_before' ); ?>
		<?php blade_grve_print_post_feature_media( 'gallery' ); ?>
		<div class="grve-post-content">
			<?php blade_grve_print_post_structured_data(); ?>
			<?php blade_grve_print_post_meta_top(); ?>
			<?php do_action( 'blade_grve_inner_post_loop_item_title_link' ); ?>
			<div itemprop="articleBody">
				<?php blade_grve_print_post_excerpt(); ?>
			</div>
			<?php blade_grve_print_post_meta_bottom(); ?>
		</div>
		<?php do_action( 'blade_grve_inner_post_loop_item_after' ); ?>
	</article>
	<!-- End Article -->

<?php
}

//Omit closing PHP tag to avoid accidental whitespace output errors.
