jQuery(document).ajaxComplete(function () {
	var initToken = jQuery('#authnet-token').val();

	if (initToken) {
		jQuery('.hide-if-token').hide();
	}

	jQuery('#authnet-token').change(function () {
		var token = jQuery('#authnet-token').val();
		if (token) {
			jQuery('.hide-if-token').hide();
		} else {
			jQuery('.hide-if-token').show();
		}
	});
});

jQuery(document).ready(function () {
	var initToken = jQuery('#authnet-token').val();

	if (initToken) {
		jQuery('.hide-if-token').hide();
	}

	jQuery('#authnet-token').change(function () {
		var token = jQuery('#authnet-token').val();
		if (token) {
			jQuery('.hide-if-token').hide();
		} else {
			jQuery('.hide-if-token').show();
		}
	});
});
