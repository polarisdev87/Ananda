<?php
/**
 * Proceed to checkout button
 *
 * Contains the markup for the proceed to checkout button on the cart
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( function_exists( 'wc_get_checkout_url' ) ) {
	$get_checkout_url = wc_get_checkout_url();
} else {
	$get_checkout_url = WC()->cart->get_checkout_url();
}
echo '<a class="grve-btn grve-woo-btn grve-custom-btn grve-fullwidth-btn grve-bg-primary-1 grve-bg-hover-black" href="' . esc_url( $get_checkout_url ) . '"><span>' . esc_html__( 'Proceed to checkout', 'woocommerce' ) . '</span></a>';

//Omit closing PHP tag to avoid accidental whitespace output errors.
