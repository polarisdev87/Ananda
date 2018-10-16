<table class="shop_table shop_table_responsive credit_cards" id="credit-cards-table">
	<thead>
		<tr>
			<th><?php _e( 'Card Details', 'woocommerce-cardpay-authnet' ); ?></th>
			<th><?php _e( 'Expires', 'woocommerce-cardpay-authnet' ); ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $cards as $card ):
			$card_meta = get_post_meta( $card->ID, '_authnet_card', true );
			$card_type = $card_meta['cardtype'];
			if ( 'American Express' == $card_type ) {
				$card_type_img = 'amex';
			} elseif ( 'Diners Club' == $card_type ) {
				$card_type_img = 'diners';
			} else {
				$card_type_img = strtolower( $card_type );
			}
			$cc_last4 = $card_meta['cc_last4'];
			$is_default = $card_meta['is_default'];
			$cc_exp = $card_meta['expiry'];
		?>
		<tr>
			<td>
				<img src="<?php echo WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/' . $card_type_img . '.png' ) ?>" alt=""/>
				<?php printf( __( '%s ending in %s %s', 'woocommerce-cardpay-authnet' ), $card_type, $cc_last4, 'yes' == $is_default ? '(default)' : '' ) ?>
			</td>
			<td><?php printf( __( '%s/%s' ), substr( $cc_exp, 0, 2 ), substr( $cc_exp, -2 ) ) ?></td>
			<td>
				<a href="#" data-id="<?php echo esc_attr( $card->ID ) ?>" data-title="<?php printf( __( 'Edit %s ending in %s', 'woocommerce-cardpay-authnet' ), $card_type, $cc_last4 ) ?>" data-exp="<?php printf( __( '%s / %s' ), substr( $cc_exp, 0, 2 ), substr( $cc_exp, -2 ) ) ?>" data-default="<?php echo esc_attr( $is_default ) ?>" class="edit-card"><?php _e( 'Edit', 'woocommerce-cardpay-authnet' ) ?></a> |
				<a href="#" data-id="<?php echo esc_attr( $card->ID ) ?>" data-nonce="<?php echo wp_create_nonce( 'delete_card_nonce' ) ?>" class="delete-card"><?php _e( 'Delete', 'woocommerce-cardpay-authnet' ); ?></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
