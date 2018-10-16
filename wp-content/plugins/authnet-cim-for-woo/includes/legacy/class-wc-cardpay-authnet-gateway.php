<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_Cardpay_Authnet_Gateway
 * 
 * @extends WC_Payment_Gateway
 */
class WC_Cardpay_Authnet_Gateway extends WC_Payment_Gateway
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
			if ( isset( $_POST['authnet-token'] ) && !empty( $_POST['authnet-token'] ) ) {
				$post_id = wc_clean( $_POST['authnet-token'] );
				$post = get_post( $post_id );
				$card = get_post_meta( $post->ID, '_authnet_card', true);
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
					$exp_date = $card['expiry'];
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
				if ( isset( $_POST['authnet-save-card'] ) && is_user_logged_in() && 'yes' == $this->cim_enabled ) {
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
		if ( $order->payment_method != $this->id ) {
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
	 * save_card function.
	 *
	 * @access public
	 * @param Object $response
	 * @return void
	 */
	public function save_card( $response, $exp_date ) {
		if ( isset( $response->profileResponse->customerProfileId ) && ! empty( $response->profileResponse->customerProfileId ) ) {
			$current_cards = count( $this->get_saved_cards() );
			$card = array(
				'post_type' => 'authnet_credit_card',
				'post_title' => sprintf( __( 'Token %s &ndash; %s', 'woocommerce-cardpay-authnet' ), $response->profileResponse->customerPaymentProfileIdList[0], strftime( _x( '%b %d, %Y @ %I:%M %p', 'Token date parsed by strftime', 'woocommerce-cardpay-authnet' ) ) ),
				'post_content' => '',
				'post_status' => 'publish',
				'ping_status' => 'closed',
				'post_author' => get_current_user_id(),
				'post_password' => uniqid('card_'),
				'post_category' => '',
			);
			$post_id = wp_insert_post( $card );
			$card_meta = array(
				'customer_id' => $response->profileResponse->customerProfileId,
				'payment_id' => $response->profileResponse->customerPaymentProfileIdList[0],
				'cc_last4' => substr( $response->transactionResponse->accountNumber, -4 ),
				'expiry' => $exp_date,
				'cardtype' =>  $response->transactionResponse->accountType,
				'is_default' => $current_cards ? 'no' : 'yes',
			);
			add_post_meta( $post_id, '_authnet_card', $card_meta );
		}
	}

	/**
	 * Credit card form.
	 *
	 * @param  array $args
	 * @param  array $fields
	 */
	public function credit_card_form( $args = array(), $fields = array() ) {

		wp_enqueue_script( 'wc-credit-card-form' );
		wp_enqueue_script( 'cardpay-authnet-credit-card-form', WC_CARDPAY_AUTHNET_PLUGIN_URL . '/assets/js/cardpay-authnet-credit-card-form.js', array(), false, true );

		$default_args = array(
			'fields_have_names' => true
		);

		$args = wp_parse_args( $args, apply_filters( 'woocommerce_credit_card_form_args', $default_args, $this->id ) );

		$default_fields = array(
			'card-number-field' => '<p class="form-row form-row-wide hide-if-token">
				<label for="' . esc_attr( $this->id ) . '-card-number">' . __( 'Card Number', 'woocommerce' ) . ' <span class="required">*</span></label>
				<input id="' . esc_attr( $this->id ) . '-card-number" class="input-text wc-credit-card-form-card-number" type="text" maxlength="20" autocomplete="off" placeholder="•••• •••• •••• ••••" name="' . ( $args['fields_have_names'] ? $this->id . '-card-number' : '' ) . '" />
			</p>',
			'card-expiry-field' => '<p class="form-row form-row-first hide-if-token">
				<label for="' . esc_attr( $this->id ) . '-card-expiry">' . __( 'Expiry (MM/YY)', 'woocommerce' ) . ' <span class="required">*</span></label>
				<input id="' . esc_attr( $this->id ) . '-card-expiry" class="input-text wc-credit-card-form-card-expiry" type="text" autocomplete="off" placeholder="' . esc_attr__( 'MM / YY', 'woocommerce' ) . '" name="' . ( $args['fields_have_names'] ? $this->id . '-card-expiry' : '' ) . '" />
			</p>',
			'card-cvc-field' => '<p class="form-row form-row-last hide-if-token">
				<label for="' . esc_attr( $this->id ) . '-card-cvc">' . __( 'Card Code', 'woocommerce' ) . ' <span class="required">*</span></label>
				<input id="' . esc_attr( $this->id ) . '-card-cvc" class="input-text wc-credit-card-form-card-cvc" type="text" autocomplete="off" placeholder="' . esc_attr__( 'CVC', 'woocommerce' ) . '" name="' . ( $args['fields_have_names'] ? $this->id . '-card-cvc' : '' ) . '" />
			</p>',
		);

		if ( 'yes' == $this->cim_enabled && is_user_logged_in() ) {
			$saved_cards = $this->get_saved_cards();

			array_push(
				$default_fields,
				'<p class="form-row form-row-wide hide-if-token">
					<label for="' . esc_attr( $this->id ) . '-save-card"><input id="' . esc_attr( $this->id ) . '-save-card" class="input-checkbox wc-credit-card-form-save-card" type="checkbox" name="' . ( $args['fields_have_names'] ? $this->id . '-save-card' : '' ) . '" /><span>' . __( 'Save card for future use?', 'woocommerce-cardpay-authnet' ) . ' </span></label>
				</p>'
			);
			if ( count( $saved_cards )) {
				$option_values = '';
				foreach ( $saved_cards as $card ) {
					$card_meta = get_post_meta( $card->ID, '_authnet_card', true );
					$card_desc = '************' . $card_meta['cc_last4'] . ' - ' . $card_meta['cardtype'] . ' - Exp: ' . $card_meta['expiry'];
					$option_values .= '<option value="' . esc_attr( $card->ID ) . '"' . ( 'yes' == $card_meta['is_default'] ? 'selected="selected"' : '' ) . '>' . esc_attr( $card_desc ) . '</option>';
				}
				$option_values .= '<option value="">' . __('Add new card', 'woocommerce-cardpay-authnet' ) . '</option>';
				array_unshift(
					$default_fields,
					'<p class="form-row form-row-wide">
						<label for="' . esc_attr( $this->id ) . '-token">' . __( 'Payment Information', 'woocommerce-cardpay-authnet' ) . ' <span class="required">*</span></label>
						<select id="' . esc_attr( $this->id ) . '-token" class="wc-credit-card-form-token" name="' . ( $args['fields_have_names'] ? $this->id . '-token' : '' ) . '" >' .
						$option_values . '</select>
					</p>'
				);
			}
		}

		$fields = wp_parse_args( $fields, apply_filters( 'woocommerce_credit_card_form_fields', $default_fields, $this->id ) );
		?>
		<fieldset id="<?php echo $this->id; ?>-cc-form">
			<?php do_action( 'woocommerce_credit_card_form_start', $this->id ); ?>
			<?php
				foreach ( $fields as $field ) {
					echo $field;
				}
			?>
			<?php do_action( 'woocommerce_credit_card_form_end', $this->id ); ?>
			<div class="clear"></div>
		</fieldset>
		<?php
	}

	/**
	 * get_saved_cards function.
	 *
	 * @access private
	 * @return array
	 */
	private function get_saved_cards() {
		$args = array(
			'post_type' => 'authnet_credit_card',
			'author' => get_current_user_id(),
			'orderby' => 'post_date',
			'order' => 'ASC',
		);
		$cards = get_posts( $args );
		return $cards;
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
