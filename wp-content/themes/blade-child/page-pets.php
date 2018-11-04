<?php
/**
 * Template Name: Pets
 */

?>

<?php get_header(); ?>
<?php the_post(); ?>

<?php $coa_attachments = ananda_get_coa_attachments() ?>

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

<!-- 						<div class="grve-container">
							<input type="text" id="lookup" class="grve-input"/>
						</div> -->

						<?php if ( blade_grve_visibility( 'page_comments_visibility' ) ) { ?>
							<?php comments_template(); ?>
						<?php } ?>

					</div>
				</div>
				<!-- END MAIN CONTENT -->

				<?php blade_grve_set_current_view( 'page' ); ?>
				<?php get_sidebar(); ?>

			</div>
		</div>
		<!-- END CONTENT -->

	<script type="text/javascript">

		jQuery(document).ready(function($){

			var batches = <?php echo stripslashes(json_encode($coa_attachments)); ?>;

			$('#lookup-submit').on('click tap', function(){
				var search = $('#lookup').val();

				var index = batches.findIndex(function(item, i){
				  return item.batch === search
				});

				if(index == -1){
					$('#lookup-error').text( 'Error: Batch number '+search+' was not found. Please try again.' );
				}else{
					$('#lookup-error').text('');
					var url = batches[index].attachment_url;
					var win = window.open(url + '?v=' + (new Date().getTime()), '_blank');
  			 		win.focus();
				}

			});

			// var options = {
			// 	data: <?php //echo stripslashes(json_encode($coa_attachments)) ?>,
			// 	getValue: 'batch',
			// 	list: {
			// 		match: {
			// 			enabled: true
			// 		},
			// 		onChooseEvent: function() {
			// 			var url = $("#lookup").getSelectedItemData().attachment_url;
			// 			var win = window.open(url, '_blank');
  			// 			win.focus();
			// 		}
			// 	}
			// };

			// jQuery("#lookup").easyAutocomplete(options);

		});

	</script>

	<?php get_footer(); ?>

<?php
	}

//Omit closing PHP tag to avoid accidental whitespace output errors.
