<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_Cardpay_Authnet_Gateway
 * 
 * @extends WC_Payment_Gateway
 */
class WC_Cardpay_Authnet_Gateway extends WC_Payment_Gateway_CC
{
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id = 'authnet';
		$this->has_fields = true;
		$this->method_title = 'Authorize.Net CIM';

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Define the supported features
		$this->supports = array(
			'products',
			'refunds',
			'subscriptions',
			'subscription_cancellation',
			'subscription_suspension',
			'subscription_reactivation',
			'subscription_amount_changes',
			'subscription_date_changes',
			'subscription_payment_method_change',
			'subscription_payment_method_change_customer',
			'subscription_payment_method_change_admin',
			'multiple_subscriptions',
			'pre-orders',
			'tokenization',
			'add_payment_method',
			'default_credit_card_form',
		);

		// Define user set variables
		$this->enabled = $this->get_option( 'enabled' );
		$this->title = $this->get_option( 'title' );
		$this->sandbox = $this->get_option( 'sandbox' );
		$this->api_login = $this->get_option( 'api_login' );
		$this->transaction_key = $this->get_option( 'transaction_key' );
		$this->transaction_type = $this->get_option( 'transaction_type' );
		$this->auto_capture = $this->get_option( 'auto_capture' );
		$this->cim_enabled = $this->get_option( 'cim_enabled' );
		$this->cardtypes = $this->get_option( 'cardtypes' );

		// Add test mode warning if sandbox
		if ( 'yes' == $this->sandbox ) {
			$this->description = __( 'TEST MODE ENABLED. Use test card number 4111111111111111 with any 3-digit CVC and a future expiration date.', 'woocommerce-cardpay-authnet' );
		}

		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Admin notices
	 */
	public function admin_notices() {
		if ( 'no' == $this->enabled ) {
			return;
		}

		// Show message if API Login ID is empty in live mode
		if ( ! $this->api_login && 'no' == $this->sandbox ) {
			echo '<div class="error"><p>' . sprintf( __( 'Authorize.Net error: The API Login ID is required. <a href="%s">Click here to update your Authorize.Net settings.</a>', 'woocommerce-cardpay-authnet' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_cardpay_authnet_gateway' ) ) . '</p></div>';
			return;
		}

		// Show message if Transaction Key is empty in live mode
		if ( ! $this->transaction_key && 'no' == $this->sandbox ) {
			echo '<div class="error"><p>' . sprintf( __( 'Authorize.Net error: The Transaction Key is required. <a href="%s">Click here to update your Authorize.Net settings.</a>', 'woocommerce-cardpay-authnet' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_cardpay_authnet_gateway' ) ) . '</p></div>';
			return;
		}

		// Show message when in live mode and no SSL on the checkout page
		if ( 'no' == $this->sandbox && get_option( 'woocommerce_force_ssl_checkout' ) == 'no' && ! class_exists( 'WordPressHTTPS' ) ) {
			echo '<div class="error"><p>' . sprintf( __( 'Authorize.Net is enabled, but the <a href="%s">force SSL option</a> is disabled; your checkout may not be secure! Please enable SSL and ensure your server has a valid SSL certificate.', 'woocommerce-cardpay-authnet'), admin_url( 'admin.php?page=wc-settings&tab=checkout' ) ) . '</p></div>';
		}
	}

	/**
	 * Administrator area options
	 */
	public function admin_options() {
		?>
		<h3><img src="<?php echo WC_HTTPS::force_https_url( WC_CARDPAY_AUTHNET_PLUGIN_URL . '/assets/images/authnet_logo.png' ) ?>" alt="Authorize.Net" /></h3>
		<div class="authnet-description" style="width:50%;">
			<p>
				Authorize.Net makes accepting credit cards simple.  Accept all major credit cards including Visa, MasterCard, American Express, Discover, JCB, and Diners Club.
				The Authorize.Net extension allows your logged in customers to securely store and re-use credit card profiles to speed up the checkout process.
				We also support Subscription and Pre-Order features.
			</p>
			<p>We can set up your Authorize.Net gateway for only $10/month with no set-up fees. Lowest merchant processing rates in the industry.</p>
		</div>
		<p><a href="http://www.authnetsource.com/woocommerce?pid=da135059c7ef73c4" target="_blank" class="button-primary">Click Here To Sign Up!</a></p>
		<hr>
		<table class="form-table">
			<?php $this->generate_settings_html(); ?>
		</table><!--/.form-table-->
		<?php
	}

	/**
	 * Init payment gateway settings form fields
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'       => __( 'Enable/Disable', 'woocommerce-cardpay-authnet' ),
				'label'       => __( 'Enable Authorize.Net', 'woocommerce-cardpay-authnet' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no',
			),
			'title' => array(
				'title'       => __( 'Title', 'woocommerce-cardpay-authnet' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-cardpay-authnet' ),
				'default'     => __( 'Credit Card', 'woocommerce-cardpay-authnet' ),
				'desc_tip'    => true,
			),
			'sandbox' => array(
				'title'       => __( 'Use Sandbox', 'woocommerce-cardpay-authnet' ),
				'label'       => __( 'Enable sandbox mode - live payments will not be taken if enabled.', 'woocommerce-cardpay-authnet' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no',
			),
			'api_login' => array(
				'title'       => __( 'API Login ID', 'woocommerce-cardpay-authnet' ),
				'type'        => 'text',
				'description' => __( 'Contact sales at (866) 502-8910 if you have not received your API Login ID. Not required for Sandbox mode.', 'woocommerce-cardpay-authnet' ),
				'default'     => '',
			),
			'transaction_key' => array(
				'title'       => __( 'Transaction Key', 'woocommerce-cardpay-authnet' ),
				'type'        => 'text',
				'description' => __( 'Contact sales at (866) 502-8910 if you have not received your Transaction Key. Not required for Sandbox mode.', 'woocommerce-cardpay-authnet' ),
				'default'     => '',
			),
			'transaction_type' => array(
				'title'       => __( 'Transaction Type', 'woocommerce-cardpay-authnet' ),
				'type'        => 'select',
				'description' => '',
				'default'     => 'purchase',
				'options'     => array(
					'purchase'  => 'Authorize & Capture',
					'authorize' => 'Authorize Only',
				),
			),
			'auto_capture' => array(
				'title'       => __( 'Auto Capture', 'woocommerce-cardpay-authnet' ),
				'label'       => __( 'Automatically attempt to capture transactions that are processed as Authorize Only when order is marked complete.', 'woocommerce-cardpay-authnet' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no',
			),
			'cim_enabled' => array(
				'title'       => __( 'Allow Stored Cards', 'woocommerce-cardpay-authnet' ),
				'label'       => __( 'Allow logged in customers to save credit card profiles to use for future purchases', 'woocommerce-cardpay-authnet' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'yes',
			),
			'cardtypes' => array(
				'title'    => __( 'Accepted Cards', 'woocommerce-cardpay-authnet' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'css'      => 'width: 350px;',
				'desc_tip' => __( 'Select the card types to accept.', 'woocommerce-cardpay-authnet' ),
				'options'  => array(
					'visa'       => 'Visa',
					'mastercard' => 'MasterCard',
					'amex'       => 'American Express',
					'discover'   => 'Discover',
					'jcb'        => 'JCB',
					'diners'     => 'Diners Club',
				),
				'default' => array( 'visa', 'mastercard', 'amex', 'discover' ),
			),
		);
	}

	/**
	 * get_icon function.
	 *
	 * @access public
	 * @return string
	 */
	public function get_icon() {
		$icon = '';
		if( is_array( $this->cardtypes ) ) {
			$card_types = array_reverse( $this->cardtypes );
			foreach ( $card_types as $card_type ) {
				$icon .= '<img src="' . WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/' . $card_type . '.png' ) . '" alt="' . $card_type . '" />';
			}
		}
		return apply_filters( 'woocommerce_gateway_icon', $icon, $this->id );
	}

	/**
	 * process_payment function.
	 *
	 * @access public
	 * @param mixed $order_id
	 * @return void
	 */
	public function process_payment( $order_id ) {
		try {
			global $woocommerce;
			$order = wc_get_order( $order_id );
			$amount = $order->get_total();
			$card = '';
			if ( isset( $_POST['wc-authnet-payment-token'] ) && 'new' !== $_POST['wc-authnet-payment-token'] ) {
				$token_id = wc_clean( $_POST['wc-authnet-payment-token'] );
				$card = WC_Payment_Tokens::get( $token_id );
				// Return if card does not belong to current user
				if ( $card->get_user_id() !== get_current_user_id() ) {
					return;
				}
			}

			$authnet = new WC_Cardpay_Authnet_API();
			if ( 'authorize' == $this->transaction_type ) {
				$response = $authnet->authorize( $this, $order, $amount, $card );
			} else {
				$response = $authnet->purchase( $this, $order, $amount, $card );
			}

			if ( is_wp_error( $response ) ) {
				$order->add_order_note( $response->get_error_message() );
				throw new Exception( $response->get_error_message() );
			}

			if ( isset( $response->transactionResponse->responseCode ) && '1' == $response->transactionResponse->responseCode ) {
				$order->payment_complete();
				$woocommerce->cart->empty_cart();
				if ( ! empty( $card ) ) {
					$exp_date = $card->get_expiry_month() . substr( $card->get_expiry_year(), -2 );
				} else {
					$exp_date_array = explode( "/", $_POST['authnet-card-expiry'] );
					$exp_month = trim( $exp_date_array[0] );
					$exp_year = trim( $exp_date_array[1] );
					$exp_date = $exp_month . substr( $exp_year, -2 );
				}
				$amount_approved = number_format( $amount, '2', '.', '' );
				$message = 'authorize' == $this->transaction_type ? 'authorized' : 'completed';
				$order->add_order_note(
					sprintf(
						__( "Authorize.Net payment %s for %s. Transaction ID: %s.\n\n <strong>AVS Response:</strong> %s.\n\n <strong>CVV2 Response:</strong> %s.", 'woocommerce-cardpay-authnet' ), 
						$message,
						$amount_approved,
						$response->transactionResponse->transId,
						$this->get_avs_message( $response->transactionResponse->avsResultCode ),
						$this->get_cvv_message( $response->transactionResponse->cvvResultCode )
					)
				);
				$tran_meta = array(
					'transaction_id' => $response->transactionResponse->transId,
					'cc_last4' => substr( $response->transactionResponse->accountNumber, -4 ),
					'cc_expiry' => $exp_date,
					'transaction_type' => $this->transaction_type,
				);
				add_post_meta( $order_id, '_authnet_transaction', $tran_meta );
				// Save the card if possible
				if ( isset( $_POST['wc-authnet-new-payment-method'] ) && is_user_logged_in() && 'yes' == $this->cim_enabled ) {
					$this->save_card( $response, $exp_date );
				}
				// Return thankyou redirect
				return array(
					'result' => 'success',
					'redirect' => $this->get_return_url( $order ),
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

	/**
	 * process_refund function.
	 *
	 * @access public
	 * @param int $order_id
	 * @param float $amount
	 * @param string $reason
	 * @return bool|WP_Error
	 */
	public function process_refund( $order_id, $amount = NULL, $reason = '' ) {
		$order = wc_get_order( $order_id );

		if ( $amount > 0 ) {
			try {
				$authnet = new WC_Cardpay_Authnet_API();
				$response = $authnet->refund( $this, $order, $amount );

				if ( is_wp_error( $response ) ) {
					throw new Exception( $response->get_error_message() );
				}

				if ( isset( $response->transactionResponse->responseCode ) && '1' == $response->transactionResponse->responseCode ) {
					$refunded_amount = number_format( $amount, '2', '.', '' );
					$order->add_order_note( sprintf( __( 'Authorize.Net refund completed for %s. Refund ID: %s', 'woocommerce-cardpay-authnet' ), $refunded_amount, $response->transactionResponse->transId ) );
					return true;
				} else {
					throw new Exception( __( 'Authorize.Net refund attempt failed.', 'woocommerce-cardpay-authnet' ) );
				}
			} catch ( Exception $e ) {
				$order->add_order_note( $e->getMessage() );
				return new WP_Error( 'authnet_error', $e->getMessage() );
			}

		} else {
			return false;
		}
	}

	/**
	 * process_capture function.
	 *
	 * @access public
	 * @param int $order_id
	 * @return bool
	 */
	public function process_capture( $order_id ) {
		$order = wc_get_order( $order_id );

		// Return if another payment method was used
		$payment_method = version_compare( WC_VERSION, '3.0.0', '<' ) ? $order->payment_method : $order->get_payment_method();
		if ( $payment_method != $this->id ) {
			return;
		}

		// Attempt to process the capture
		$tran_meta = get_post_meta( $order_id, '_authnet_transaction', true);
		$orig_tran_type = $tran_meta['transaction_type'];
		$amount = $order->get_total();
		if ( 'authorize' == $orig_tran_type && 'yes' == $this->auto_capture ) {
			try {
				$authnet = new WC_Cardpay_Authnet_API();
				$response = $authnet->capture( $this, $order, $amount );

				if ( is_wp_error( $response ) ) {
					throw new Exception( $response->get_error_message() );
				}

				if ( isset( $response->transactionResponse->responseCode ) && '1' == $response->transactionResponse->responseCode ) {
					$captured_amount = number_format( $amount, '2', '.', '' );
					$order->add_order_note( sprintf( __( 'Authorize.Net auto capture completed for %s. Capture ID: %s', 'woocommerce-cardpay-authnet' ), $captured_amount, $response->transactionResponse->transId ) );
					return true;
				} else {
					throw new Exception( __( 'Authorize.Net auto capture failed. Log into your gateway to manually process the capture.', 'woocommerce-cardpay-authnet' ) );
				}
			} catch ( Exception $e ) {
				$order->add_order_note( $e->getMessage() );
				return true;
			}
		}
	}

	/**
	 * Add payment method via account screen.
	 */
	public function add_payment_method() {
		$authnet = new WC_Cardpay_Authnet_API();
		$response = $authnet->create_profile( $this );
		if ( isset( $response->customerProfileId ) && ! empty( $response->customerProfileId ) ) {
			$card_number = str_replace( ' ', '', $_POST['authnet-card-number'] );
			$card_type = $authnet->get_card_type( $card_number );
			$exp_date_array = explode( "/", $_POST['authnet-card-expiry'] );
			$exp_month = trim( $exp_date_array[0] );
			$exp_year = trim( $exp_date_array[1] );
			$exp_date = $exp_month . substr( $exp_year, -2 );

			$token = new WC_Payment_Token_CC();
			$token->set_token( $response->customerProfileId . '|' . $response->customerPaymentProfileIdList[0] );
			$token->set_gateway_id( 'authnet' );
			$token->set_card_type( strtolower( $card_type ) );
			$token->set_last4( substr( $card_number, -4) );
			$token->set_expiry_month( substr( $exp_date, 0, 2 ) );
			$token->set_expiry_year( '20' . substr( $exp_date, -2 ) );
			$token->set_user_id( get_current_user_id() );
			$token->save();

			return array(
				'result'   => 'success',
				'redirect' => wc_get_endpoint_url( 'payment-methods' ),
			);
		} else {
			if ( isset( $response->messages ) ) {
				$error_msg = __( 'Error adding card: ', 'woocommerce-cardpay-authnet' ) . $response->messages->message[0]->text;
			} else {
				$error_msg = __( 'Error adding card. Please try again.', 'woocommerce-cardpay-authnet' );
			}
			wc_add_notice( $error_msg, 'error' );
			return;
		}
	}

	/**
	 * save_card function.
	 *
	 * @access public
	 * @param Object $response
	 * @return void
	 */
	public function save_card( $response, $exp_date ) {
		if ( isset( $response->profileResponse->customerProfileId ) && ! empty( $response->profileResponse->customerProfileId ) ) {
			$token = new WC_Payment_Token_CC();
			$token->set_token( $response->profileResponse->customerProfileId . '|' . $response->profileResponse->customerPaymentProfileIdList[0] );
			$token->set_gateway_id( 'authnet' );
			$token->set_card_type( $response->transactionResponse->accountType );
			$token->set_last4( substr( $response->transactionResponse->accountNumber, -4 ) );
			$token->set_expiry_month( substr( $exp_date, 0, 2 ) );
			$token->set_expiry_year( '20' . substr( $exp_date, -2 ) );
			$token->set_user_id( get_current_user_id() );
			$token->save();
		}
	}

	/**
	 * Builds our payment fields area - including tokenization fields for logged
	 * in users, and the actual payment fields.
	 */
	public function payment_fields() {
		if ( $this->description ) {
			echo apply_filters( 'wc_cardpay_authnet_description', wpautop( wp_kses_post( $this->description ) ) );
		}

		if ( $this->supports( 'tokenization' ) && is_checkout() && $this->cim_enabled ) {
			$this->tokenization_script();
			$this->saved_payment_methods();
			$this->form();
			$this->save_payment_method_checkbox();
		} else {
			$this->form();
		}
	}

	/**
	 * Output field name HTML
	 *
	 * Gateways which support tokenization do not require names - we don't want the data to post to the server.
	 *
	 * @param  string $name
	 * @return string
	 */
	public function field_name( $name ) {
		return ' name="' . esc_attr( $this->id . '-' . $name ) . '" ';
	}

	/**
	 * get_avs_message function.
	 * 
	 * @access public
	 * @param string $code
	 * @return string
	 */
	public function get_avs_message( $code ) {
		$avs_messages = array(
			'A' => __( 'Street Address: Match -- First 5 Digits of ZIP: No Match', 'woocommerce-cardpay-authnet' ),
			'B' => __( 'Address not provided for AVS check or street address match, postal code could not be verified', 'woocommerce-cardpay-authnet' ),
			'E' => __( 'AVS Error', 'woocommerce-cardpay-authnet' ),
			'G' => __( 'Non U.S. Card Issuing Bank', 'woocommerce-cardpay-authnet' ),
			'N' => __( 'Street Address: No Match -- First 5 Digits of ZIP: No Match', 'woocommerce-cardpay-authnet' ),
			'P' => __( 'AVS not applicable for this transaction', 'woocommerce-cardpay-authnet' ),
			'R' => __( 'Retry, System Is Unavailable', 'woocommerce-cardpay-authnet' ),
			'S' => __( 'AVS Not Supported by Card Issuing Bank', 'woocommerce-cardpay-authnet'),
			'U' => __( 'Address Information For This Cardholder Is Unavailable', 'woocommerce-cardpay-authnet' ),
			'W' => __( 'Street Address: No Match -- All 9 Digits of ZIP: Match', 'woocommerce-cardpay-authnet' ),
			'X' => __( 'Street Address: Match -- All 9 Digits of ZIP: Match', 'woocommerce-cardpay-authnet' ),
			'Y' => __( 'Street Address: Match - First 5 Digits of ZIP: Match', 'woocommerce-cardpay-authnet' ),
			'Z' => __( 'Street Address: No Match - First 5 Digits of ZIP: Match', 'woocommerce-cardpay-authnet' ),
		);
		if ( array_key_exists( $code, $avs_messages ) ) {
			return $avs_messages[$code];
		} else {
			return '';
		}
	}

	/**
	 * get_cvv_message function.
	 * 
	 * @access public
	 * @param string $code
	 * @return string
	 */
	public function get_cvv_message( $code ) {
		$cvv_messages = array(
			'M' => __( 'CVV2/CVC2 Match', 'woocommerce-cardpay-authnet' ),
			'N' => __( 'CVV2 / CVC2 No Match', 'woocommerce-cardpay-authnet' ),
			'P' => __( 'Not Processed', 'woocommerce-cardpay-authnet' ),
			'S' => __( 'Merchant Has Indicated that CVV2 / CVC2 is not present on card', 'woocommerce-cardpay-authnet' ),
			'U' => __( 'Issuer is not certified and/or has not provided visa encryption keys', 'woocommerce-cardpay-authnet' ),
		);
		if ( array_key_exists( $code, $cvv_messages ) ) {
			return $cvv_messages[$code];
		} else {
			return '';
		}
	}
}
