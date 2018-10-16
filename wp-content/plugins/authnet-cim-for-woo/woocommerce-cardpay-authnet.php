<?php
/**
 * Plugin Name: Authorize.Net CIM for WooCommerce
 * Plugin URI: http://www.authnetsource.com/woocommerce?pid=da135059c7ef73c4
 * Description: Adds the Authorize.Net Payment Gateway to WooCommerce. Customer Information Manager (CIM) is used to securely support saved credit card profiles, subscriptions, and pre-orders.
 * Version: 2.0.4
 * Author: Cardpay Solutions, Inc.
 * Author URI: http://www.authnetsource.com/
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: woocommerce-cardpay-authnet
 * Domain Path: /languages
 * 
 * Copyright 2016 Cardpay Solutions, Inc.  (email : sales@cardpaysolutions.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @author Cardpay Solutions, Inc.
 * @package Authorize.Net CIM for WooCommerce
 * @since 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Cardpay_Authnet' ) ) :

/**
 * Main class to set up the Authorize.Net gateway
 */
class WC_Cardpay_Authnet {

	/**
	 * @var Singleton The reference the Singleton instance of this class
	 */
	private static $instance;

	/**
	 * Returns the Singleton instance of this class.
	 *
	 * @return Singleton The Singleton instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Prevent cloning of the instance of the Singleton instance.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing of the Singleton instance.
	 *
	 * @return void
	 */
	private function __wakeup() {}

	/**
	 * Constructor
	 */
	public function __construct() {
		define( 'WC_CARDPAY_AUTHNET_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/includes/legacy/templates/' );
		define( 'WC_CARDPAY_AUTHNET_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
		add_action( 'plugins_loaded', array( $this, 'init' ), 0 );
		add_action( 'woocommerce_order_status_completed', array( $this, 'process_capture' ) );
		add_action( 'init', array( $this, 'create_credit_card_post_type' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_css' ) );
	}

	/**
	 * Add relevant links to plugins page
	 * @param  array $links
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		$addons = ( class_exists( 'WC_Subscriptions_Order' ) || class_exists( 'WC_Pre_Orders_Order' ) ) ? '_addons' : '';
		$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_cardpay_authnet_gateway' . $addons ) . '">' . __( 'Settings', 'woocommerce-cardpay-authnet' ) . '</a>',
		);
		return array_merge( $plugin_links, $links );
	}

	/**
	 * Init function
	 */
	public function init() {
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
			return;
		}

		if ( class_exists( 'WC_Payment_Gateway_CC' ) ) {
			include_once( 'includes/class-wc-cardpay-authnet-gateway.php' );
			include_once( 'includes/class-wc-cardpay-authnet-api.php' );

			if ( class_exists( 'WC_Subscriptions_Order' ) || class_exists( 'WC_Pre_Orders_Order' ) ) {
				include_once( 'includes/class-wc-cardpay-authnet-gateway-addons.php' );
			}
		} else {
			include_once( 'includes/legacy/class-wc-cardpay-authnet-gateway.php' );
			include_once( 'includes/legacy/class-wc-cardpay-authnet-api.php' );
			include_once( 'includes/legacy/class-wc-cardpay-authnet-credit-cards.php' );

			if ( class_exists( 'WC_Subscriptions_Order' ) || class_exists( 'WC_Pre_Orders_Order' ) ) {
				include_once( 'includes/legacy/class-wc-cardpay-authnet-gateway-addons.php' );
			}
		}

		// Localisation
		load_plugin_textdomain( 'woocommerce-cardpay-authnet', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		// Add Authorize.Net Gateway
		add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateway' ) );
		add_filter( 'woocommerce_get_customer_payment_tokens', array( $this, 'woocommerce_get_customer_payment_tokens' ), 10, 3 );
	}

	/**
	 * Add Authorize.Net gateway to Woocommerce
	 */
	public function add_gateway( $methods ) {
		if ( class_exists( 'WC_Subscriptions_Order' ) || class_exists( 'WC_Pre_Orders_Order' ) ) {
			$methods[] = 'WC_Cardpay_Authnet_Gateway_Addons';
		} else {
			$methods[] = 'WC_Cardpay_Authnet_Gateway';
		}
		return $methods;
	}

	/**
	 * process_capture function
	 * 
	 * @param int $order_id
	 * @return void
	 */
	public function process_capture( $order_id ) {
		$gateway = new WC_Cardpay_Authnet_Gateway();
		$gateway->process_capture( $order_id );
	}

	/**
	 * Gets saved tokens from legacy credit card post type if they don't already exist in WooCommerce.
	 * @param array $tokens
	 * @return array
	 */
	public function woocommerce_get_customer_payment_tokens( $tokens, $customer_id, $gateway_id ) {
		if ( is_user_logged_in() && 'authnet' === $gateway_id && class_exists( 'WC_Payment_Token_CC' ) ) {
			$args = array(
				'post_type' => 'authnet_credit_card',
				'author' => get_current_user_id(),
			);
			$authnet_cards = get_posts( $args );
			$stored_tokens   = array();

			foreach ( $tokens as $token ) {
				$stored_tokens[] = $token->get_token();
			}

			foreach ( $authnet_cards as $card ) {
				$card_meta = get_post_meta( $card->ID, '_authnet_card', true );
				$post_token = $card_meta['customer_id'] . '|' . $card_meta['payment_id'];
				$exp_month = substr( $card_meta['expiry'], 0, 2 );
				$exp_year = '20' . substr( $card_meta['expiry'], -2 );
				if ( ! in_array( $post_token, $stored_tokens ) ) {
					$token = new WC_Payment_Token_CC();
					$token->set_token( $card_meta['customer_id'] . '|' . $card_meta['payment_id'] );
					$token->set_gateway_id( 'authnet' );
					$token->set_card_type( strtolower( $card_meta['cardtype'] ) );
					$token->set_last4( $card_meta['cc_last4'] );
					$token->set_expiry_month( $exp_month );
					$token->set_expiry_year( $exp_year );
					$token->set_user_id( get_current_user_id() );
					$token->save();
					$tokens[ $token->get_id() ] = $token;
				}
			}
		}
		return $tokens;
	}

	/**
	 * create_credit_card_post_type function
	 */
	public function create_credit_card_post_type() {
		register_post_type( 'authnet_credit_card',
			array(
				'labels' => array(
					'name' => __( 'Credit Cards', 'woocommerce-cardpay-authnet' )
				),
				'public'              => false,
				'show_ui'             => false,
				'map_meta_cap'        => false,
				'rewrite'             => false,
				'query_var'           => false,
				'supports'            => false,
			)
		);
	}

	/**
	 * Load style sheet
	 */
	public function load_css() {
		if ( ! class_exists( 'WC_Payment_Gateway_CC' ) ) {
			wp_enqueue_style( 'cardpay-authnet', plugins_url( 'assets/css/cardpay-authnet.css', __FILE__ ) );
		}
	}
}

endif;

function wc_authnet() {
	return WC_Cardpay_Authnet::get_instance();
}
wc_authnet();
