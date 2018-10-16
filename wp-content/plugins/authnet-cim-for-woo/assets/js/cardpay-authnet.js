jQuery( document ).ready( function($) {
	$(document).on( 'click', '.delete-card', function() {
		var id = $(this).data('id');
		var nonce = $(this).data('nonce');
		var post = $(this).parents('tr:first');
		$.ajax({
			type: 'post',
			url: MyAjax.ajaxurl,
			data: {
				action: 'delete_card',
				nonce: nonce,
				id: id
			},
			success: function( result ) {
				if ( result == 'error' ) {
					$('.authnet-success-message').hide();
					$('.authnet-error-message').show();
				} else {
					post.fadeOut( function() {
						post.remove();
					});
					$('.authnet-error-message').hide();
					$('.authnet-success-message').show();
				}
			}
		});
		return false;
	});

	$(document).on( 'submit', '#authnet-cc-form', function(e) {
		e.preventDefault();
		var authnetCcForm = $(this).serialize();
		$.ajax({
			type: 'post',
			url: MyAjax.ajaxurl+'?action=add_update_card',
			data: authnetCcForm,
			success: function( result ) {
				if ( result == 'error' ) {
					$('html, body').animate({
						scrollTop: $('#credit-cards').offset().top
					}, 500);
					$('.authnet-success-message').hide();
					$('.authnet-error-message').show();
				} else {
					$('#credit-cards-table').replaceWith(result);
					$('.add-card-heading').hide();
					$('.edit-card-heading').hide();
					$('.make-default').hide();
					$('.authnet-credit-card').hide();
					$('#authnet-card-number').val('');
					$('#authnet-card-expiry').val('');
					$('#authnet-card-cvc').val('');
					$('#authnet-card-id').val('');
					$('#authnet-make-default').prop('checked', false);
					$('.add-card').show();
					$('html, body').animate({
						scrollTop: $('#credit-cards').offset().top
					}, 500);
					$('.authnet-error-message').hide();
					$('.authnet-success-message').show();
				}
			}
		});
		return false;
	});

	$(document).on( 'click', '.add-card', function(e) {
		e.preventDefault();
		$('.edit-card-heading').hide();
		$('.add-card-heading').show();
		$('.authnet-credit-card').show();
		$('.make-default').show();
		$('.add-card').hide();
		$('#authnet-card-id').val('');
		$('html, body').animate({
			scrollTop: $('.add-card-heading').offset().top
		}, 500);
	});

	$(document).on( 'click', '.edit-card', function(e) {
		e.preventDefault();
		var id = $(this).data('id');
		var title = $(this).data('title');
		var exp = $(this).data('exp');
		var is_default = $(this).data('default');
		$('.edit-card-heading').show();
		$('.add-card-heading').hide();
		$('.authnet-credit-card').show();
		$('.add-card').hide();
		$('.edit-card-heading').html(title);
		$('#authnet-card-expiry').val(exp);
		$('#authnet-card-id').val(id);
		if (is_default == 'no') {
			$('.make-default').show();
		} else {
			$('.make-default').hide();
		}
		$('html, body').animate({
			scrollTop: $('.edit-card-heading').offset().top
		}, 500);
	});

	$(document).on( 'click', '.cc-form-cancel', function(e) {
		e.preventDefault();
		$('.add-card-heading').hide();
		$('.edit-card-heading').hide();
		$('.make-default').hide();
		$('.authnet-credit-card').hide();
		$('#authnet-card-number').val('');
		$('#authnet-card-expiry').val('');
		$('#authnet-card-cvc').val('');
		$('#authnet-card-id').val('');
		$('#authnet-make-default').prop('checked', false);
		$('.add-card').show();
		$('html, body').animate({
			scrollTop: $('#credit-cards').offset().top
		}, 500);
	});

	$(document).ajaxStart(function() {
		$( '.authnet-credit-card' ).block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
	});

	$(document).ajaxComplete(function() {
		$( '.authnet-credit-card' ).unblock();
	});
});
