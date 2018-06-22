<?php
/**
 * Checkout Payment Section
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/payment.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;


if ( ! is_ajax() ) {
	do_action( 'woocommerce_review_order_before_payment' );
}
?>
<div id="payment" class="woocommerce-checkout-payment">
	<?php if ( WC()->cart->needs_payment() ) : ?>
	<ul class="payment_methods methods">
		<?php
			if ( ! empty( $available_gateways ) ) {
				foreach ( $available_gateways as $gateway ) {
					wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
				}
			} else {
				if ( ! WC()->customer->get_country() ) {
					$no_gateways_message = esc_html__( 'Please fill in your details above to see available payment methods.', 'woocommerce' );
				} else {
					$no_gateways_message = esc_html__( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' );
				}

				echo '<li>' . apply_filters( 'woocommerce_no_available_payment_methods_message', $no_gateways_message ) . '</li>';
			}
		?>
	</ul>
	<?php endif; ?>

	<div class="form-row place-order">

		<noscript><?php echo wp_kses( __( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the <em>Update Totals</em> button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ), array( 'br' => array(), 'em' => array() ) ); ?><br/><input type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>" /></noscript>

		

		<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

		<?php wc_get_template( 'checkout/terms.php' ); ?>

		<?php echo apply_filters( 'woocommerce_order_button_html', '<input type="submit" class="grve-btn grve-woo-btn grve-custom-btn grve-fullwidth-btn grve-bg-primary-1 grve-bg-hover-black" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '" />' ); ?>

		<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

		<?php wp_nonce_field( 'woocommerce-process_checkout' ); ?>
	</div>
</div>
<?php
if ( ! is_ajax() ) {
	do_action( 'woocommerce_review_order_after_payment' );
}
