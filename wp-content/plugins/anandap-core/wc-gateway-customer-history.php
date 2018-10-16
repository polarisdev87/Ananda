<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


// Make sure WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	return;
}


function wc_customer_history_add_to_gateways( $gateways ) {
	$gateways[] = 'WC_Gateway_Customer_History';
	return $gateways;
}
add_filter( 'woocommerce_payment_gateways', 'wc_customer_history_add_to_gateways' );


// function wc_customer_history_gateway_plugin_links( $links ) {
// 	$plugin_links = array(
// 		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=customer_history_gateway' ) . '">' . __( 'Configure', 'wc-gateway-customer-history' ) . '</a>'
// 	);
// 	return array_merge( $plugin_links, $links );
// }
// add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wc_customer_history_gateway_plugin_links' );


add_action( 'plugins_loaded', 'wc_customer_history_gateway_init', 11 );

function wc_customer_history_gateway_init() {

    class WC_Gateway_Customer_History extends WC_Payment_Gateway {

		/**
		 * Constructor for the gateway.
		 */
		public function __construct() {
	  
			$this->id                 = 'customer_history_gateway';
			$this->icon               = apply_filters('woocommerce_customer_history_icon', '');
			$this->has_fields         = false;
			$this->method_title       = __( 'Customer History', 'wc-gateway-customer-history' );
			$this->method_description = __( 'Look for existing customers on Authorize.net', 'wc-gateway-customer-history' );
		  
			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();
		  
			// Define user set variables
			$this->title        = $this->get_option( 'title' );
			$this->description  = $this->get_option( 'description' );
			$this->instructions = $this->get_option( 'instructions', $this->description );

			add_filter( 'woocommerce_available_payment_gateways', array( $this, 'filter_payment_gateway'), 10 );
		}

		public function filter_payment_gateway($available_gateways) {
			if (!is_user_switched()) {
				unset($available_gateways['customer_history_gateway']);
			} else {
				$gateway = new WC_Cardpay_Authnet_Gateway();
				$authnet = new WC_Cardpay_Authnet_API();
				$response = $authnet->get_customer_profile($gateway);
				if ( is_wp_error( $response ) || $response->messages->resultCode != 'Ok') {
					unset($available_gateways['customer_history_gateway']);
				}
			}
			return $available_gateways;
		}


		/**
		 * Initialize Gateway Settings Form Fields
		 */
		public function init_form_fields() {
	  
			$this->form_fields = apply_filters( 'wc_customer_history_form_fields', array(
		  
				'enabled' => array(
					'title'   => __( 'Enable/Disable', 'wc-gateway-customer-history' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable Lookup Customer History', 'wc-gateway-customer-history' ),
					'default' => 'yes'
				),
				
				'title' => array(
					'title'       => __( 'Title', 'wc-gateway-customer-history' ),
					'type'        => 'text',
					'description' => __( 'This controls the title for the payment method the customer sees during checkout.', 'wc-gateway-customer-history' ),
					'default'     => __( 'Customer History', 'wc-gateway-customer-history' ),
					'desc_tip'    => true,
				),
				
				'description' => array(
					'title'       => __( 'Description', 'wc-gateway-customer-history' ),
					'type'        => 'textarea',
					'description' => __( 'Payment method description that the customer will see on your checkout.', 'wc-gateway-customer-history' ),
					'default'     => __( 'Please see if this is correct customer history.', 'wc-gateway-customer-history' ),
					'desc_tip'    => true,
				),
				
				'instructions' => array(
					'title'       => __( 'Instructions', 'wc-gateway-customer-history' ),
					'type'        => 'textarea',
					'description' => __( 'Instructions that will be added to the thank you page and emails.', 'wc-gateway-customer-history' ),
					'default'     => '',
					'desc_tip'    => true,
				),
			) );
		}

		/**
		 * Builds our payment fields area - including tokenization fields for logged
		 * in users, and the actual payment fields.
		 */
		public function payment_fields() {
			$gateway = new WC_Cardpay_Authnet_Gateway();
			$authnet = new WC_Cardpay_Authnet_API();
			$response = $authnet->get_customer_profile($gateway);
			if ( !is_wp_error( $response ) && $response->messages->resultCode == 'Ok') {
				$card = $response->profile->paymentProfiles[0]->payment->creditCard;
				?>
					<table>
						<tr><th>Type</th><td><?php echo $card->cardType; ?></td></tr>
						<tr><th>Card number</th><td><?php echo $card->cardNumber; ?></td></tr>
						<tr><th>Expiry</th><td><?php echo $card->expirationDate; ?></td></tr>
					</table>
				<?php
			}
		}

	
		/**
		 * Process the payment and return the result
		 *
		 * @param int $order_id
		 * @return array
		 */
		public function process_payment( $order_id ) {
			try {
				global $woocommerce;
				$order = wc_get_order( $order_id );
				$amount = $order->get_total();

				$gateway = new WC_Cardpay_Authnet_Gateway();
				$authnet = new WC_Cardpay_Authnet_API();

				$customer_profile = $authnet->get_customer_profile($gateway);
				$customerProfileId = $customer_profile->profile->customerProfileId;
				$customerPaymentProfileId = $customer_profile->profile->paymentProfiles[0]->customerPaymentProfileId;

				$response = $authnet->purchase_with_history( $gateway, $order, $amount, $customerProfileId, $customerPaymentProfileId );

				if ( is_wp_error( $response ) ) {
					$order->add_order_note( $response->get_error_message() );
					throw new Exception( $response->get_error_message() );
				}

				if ( isset( $response->transactionResponse->responseCode ) && '1' == $response->transactionResponse->responseCode ) {
					$order->payment_complete();
					$woocommerce->cart->empty_cart();
					$amount_approved = number_format( $amount, '2', '.', '' );
					$message = 'authorize' == $gateway->transaction_type ? 'authorized' : 'completed';
					$order->add_order_note(
						sprintf(
							__( "Authorize.Net payment %s for %s. Transaction ID: %s.\n\n <strong>AVS Response:</strong> %s.\n\n <strong>CVV2 Response:</strong> %s.", 'woocommerce-cardpay-authnet' ), 
							$message,
							$amount_approved,
							$response->transactionResponse->transId,
							$gateway->get_avs_message( $response->transactionResponse->avsResultCode ),
							$gateway->get_cvv_message( $response->transactionResponse->cvvResultCode )
						)
					);
					$tran_meta = array(
						'transaction_id' => $response->transactionResponse->transId,
						'cc_last4' => substr( $response->transactionResponse->accountNumber, -4 ),
						'cc_expiry' => '', // this will break some further actions like - refund, ...
						'transaction_type' => $gateway->transaction_type,
					);
					add_post_meta( $order_id, '_authnet_transaction', $tran_meta );
					// Return thankyou redirect
					return array(
						'result' => 'success',
						'redirect' => $gateway->get_return_url( $order ),
					);
				} else {
					$order->add_order_note( __( 'Payment error: Please check your credit card details and try again.', 'woocommerce-cardpay-authnet' ) );

					throw new Exception( __( 'Payment error: Please check your credit card details and try again.', 'woocommerce-cardpay-authnet' ) );
				}
			} catch ( Exception $e ) {
				wc_add_notice( $e->getMessage(), 'error' );

				return array(
					'result'   => 'fail',
					'redirect' => '',
				);
			}
		}

    } // end \WC_Gateway_Customer_History class
}