<?php
/**
 * Order Customer Details
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() ) : ?>

<div class="col2-set addresses">
	<div class="col-1">

<?php endif; ?>

<header class="title">
	<h3 class="grve-align-center"><?php esc_html_e( 'Billing address', 'woocommerce' ); ?></h3>
</header>
<address>
	<?php echo ( $address = $order->get_formatted_billing_address() ) ? $address : esc_html__( 'N/A', 'woocommerce' ); ?>
	<?php if ( $order->get_billing_phone() ) : ?>
		<p class="woocommerce-customer-details--phone"><?php echo esc_html( $order->get_billing_phone() ); ?></p>
	<?php endif; ?>
	<?php if ( $order->get_billing_email() ) : ?>
		<p class="woocommerce-customer-details--email"><?php echo esc_html( $order->get_billing_email() ); ?></p>
	<?php endif; ?>
</address>

<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() ) : ?>

	</div><!-- /.col-1 -->
	<div class="col-2">
		<header class="title">
			<h3 class="grve-align-center"><?php esc_html_e( 'Shipping address', 'woocommerce' ); ?></h3>
		</header>
		<address>
			<?php echo ( $address = $order->get_formatted_shipping_address() ) ? $address : esc_html__( 'N/A', 'woocommerce' ); ?>
		</address>
	</div><!-- /.col-2 -->
</div><!-- /.col2-set -->

<?php endif;

//Omit closing PHP tag to avoid accidental whitespace output errors.
