<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Cardpay_Authnet_Gateway_Addons class.
 *
 * @extends WC_Cardpay_Authnet_Gateway
 */
class WC_Cardpay_Authnet_Gateway_Addons extends WC_Cardpay_Authnet_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		if ( class_exists( 'WC_Subscriptions_Order' ) ) {
			add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, array( $this, 'scheduled_subscription_payment' ), 10, 2 );
			add_action( 'woocommerce_subscription_failing_payment_method_updated_' . $this->id, array( $this, 'update_failing_payment_method' ), 10, 2 );

			add_action( 'wcs_resubscribe_order_created', array( $this, 'delete_resubscribe_meta' ), 10 );

			// Allow store managers to manually set Authorize.Net as the payment method on a subscription
			add_filter( 'woocommerce_subscription_payment_meta', array( $this, 'add_subscription_payment_meta' ), 10, 2 );
			add_filter( 'woocommerce_subscription_validate_payment_meta', array( $this, 'validate_subscription_payment_meta' ), 10, 2 );
		}

		if ( class_exists( 'WC_Pre_Orders_Order' ) ) {
			add_action( 'wc_pre_orders_process_pre_order_completion_payment_' . $this->id, array( $this, 'process_pre_order_release_payment' ) );
		}
	}

	/**
	 * Check if order contains subscriptions.
	 *
	 * @param  int $order_id
	 * @return bool
	 */
	protected function order_contains_subscription( $order_id ) {
		return function_exists( 'wcs_order_contains_subscription' ) && ( wcs_order_contains_subscription( $order_id ) || wcs_order_contains_renewal( $order_id ) );
	}

	/**
	 * Check if order contains pre-orders.
	 *
	 * @param  int $order_id
	 * @return bool
	 */
	protected function order_contains_pre_order( $order_id ) {
		return class_exists( 'WC_Pre_Orders_Order' ) && WC_Pre_Orders_Order::order_contains_pre_order( $order_id );
	}

	/**
	 * Process the subscription
	 *
	 * @param int $order_id
	 * 
	 * @return array
	 */
	protected function process_subscription( $order_id ) {
		try {
			$order = wc_get_order( $order_id );
			$amount = $order->get_total();
			if ( isset( $_POST['authnet-token'] ) && !empty( $_POST['authnet-token'] ) ) {
				$post_id = wc_clean( $_POST['authnet-token'] );
				$post = get_post( $post_id );
				$card_meta = get_post_meta( $post->ID, '_authnet_card', true);
				$this->save_subscription_meta( $order->id, $card_meta );
			} else {
				$card = '';
				$authnet = new WC_Cardpay_Authnet_API();
				$response = $authnet->create_profile( $this );

				if ( is_wp_error( $response ) ) {
					throw new Exception( $response->get_error_message() );
				}

				if ( isset( $response->customerProfileId ) && ! empty( $response->customerProfileId ) ) {
					$exp_date_array = explode( "/", $_POST['authnet-card-expiry'] );
					$exp_month = trim( $exp_date_array[0] );
					$exp_year = trim( $exp_date_array[1] );
					$exp_date = $exp_month . substr( $exp_year, -2 );
					$card_meta = array(
						'customer_id' => $response->customerProfileId,
						'payment_id' => $response->customerPaymentProfileIdList[0],
						'expiry' => $exp_date,
					);
					$this->save_subscription_meta( $order->id, $card_meta );
				} else {
					$error_msg = __( 'Payment was declined - please try another card.', 'woocommerce-cardpay-authnet' );
					throw new Exception( $error_msg );
				}
			}

			if ( $amount > 0 ) {
				$payment_response = $this->process_subscription_payment( $order, $order->get_total() );

				if ( is_wp_error( $payment_response ) ) {
					throw new Exception( $payment_response->get_error_message() );
				}
			} else {
				$order->payment_complete();
			}
			// Remove cart
			WC()->cart->empty_cart();

			// Return thank you page redirect
			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order )
			);
		} catch ( Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );

			return array(
				'result'   => 'fail',
				'redirect' => '',
			);
		}
	}

	/**
	 * Store the Authorize.Net card data on the order and subscriptions in the order
	 *
	 * @param int $order_id
	 * @param array $card
	 */
	protected function save_subscription_meta( $order_id, $card ) {
		update_post_meta( $order_id, '_authnet_customer_id', $card['customer_id'] );
		update_post_meta( $order_id, '_authnet_payment_id', $card['payment_id'] );
		update_post_meta( $order_id, '_authnet_expiry', $card['expiry'] );

		// Also store it on the subscriptions being purchased in the order
		foreach( wcs_get_subscriptions_for_order( $order_id ) as $subscription ) {
			update_post_meta( $subscription->id, '_authnet_customer_id', $card['customer_id'] );
			update_post_meta( $subscription->id, '_authnet_payment_id', $card['payment_id'] );
			update_post_meta( $subscription->id, '_authnet_expiry', $card['expiry'] );
		}
	}

	/**
	 * Process the pre-order
	 *
	 * @param int $order_id
	 * @return array
	 */
	protected function process_pre_order( $order_id ) {
		if ( WC_Pre_Orders_Order::order_requires_payment_tokenization( $order_id ) ) {

			try {
				$order = wc_get_order( $order_id );
				if ( isset( $_POST['authnet-token'] ) && !empty( $_POST['authnet-token'] ) ) {
					$post_id = wc_clean( $_POST['authnet-token'] );
					$post = get_post( $post_id );
					$card_meta = get_post_meta( $post->ID, '_authnet_card', true);
				} else {
					$card = '';
					$authnet = new WC_Cardpay_Authnet_API();
					$response = $authnet->create_profile( $this );

					if ( is_wp_error( $response ) ) {
						throw new Exception( $response->get_error_message() );
					}

					if ( isset( $response->customerProfileId ) && ! empty( $response->customerProfileId ) ) {
						$exp_date_array = explode( "/", $_POST['authnet-card-expiry'] );
						$exp_month = trim( $exp_date_array[0] );
						$exp_year = trim( $exp_date_array[1] );
						$exp_date = $exp_month . substr( $exp_year, -2 );
						$card_meta = array(
							'customer_id' =>  $response->customerProfileId,
							'payment_id' => $response->customerPaymentProfileIdList[0],
						);
					} else {
						$error_msg = __( 'Payment was declined - please try another card.', 'woocommerce-cardpay-authnet' );
						throw new Exception( $error_msg );
					}
				}

				// Store the ID in the order
				update_post_meta( $order->id, '_authnet_customer_id', $card_meta['customer_id'] );
				update_post_meta( $order->id, '_authnet_payment_id', $card_meta['payment_id'] );
				update_post_meta( $order->id, '_authnet_expiry', $card_meta['expiry'] );

				// Reduce stock levels
				$order->reduce_order_stock();

				// Remove cart
				WC()->cart->empty_cart();

				// Is pre ordered!
				WC_Pre_Orders_Order::mark_order_as_pre_ordered( $order );

				// Return thank you page redirect
				return array(
					'result'   => 'success',
					'redirect' => $this->get_return_url( $order )
				);
			} catch ( Exception $e ) {
				wc_add_notice( $e->getMessage(), 'error' );

				return array(
					'result'   => 'fail',
					'redirect' => '',
				);
			}

		} else {
			return parent::process_payment( $order_id );
		}
	}

	/**
	 * Process the payment
	 *
	 * @param  int $order_id
	 * @return array
	 */
	public function process_payment( $order_id ) {
		// Processing subscription
		if ( $this->order_contains_subscription( $order_id ) || ( function_exists( 'wcs_is_subscription' ) && wcs_is_subscription( $order_id ) ) ) {
			return $this->process_subscription( $order_id );

		// Processing pre-order
		} elseif ( $this->order_contains_pre_order( $order_id ) ) {
			return $this->process_pre_order( $order_id );

		// Processing regular product
		} else {
			return parent::process_payment( $order_id );
		}
	}

	/**
	 * process_subscription_payment function.
	 *
	 * @param WC_order $order
	 * @param integer $amount (default: 0)
	 * 
	 * @return bool|WP_Error
	 */
	public function process_subscription_payment( $order, $amount = 0 ) {
		$card = array(
			'customer_id' => get_post_meta( $order->id, '_authnet_customer_id', true ),
			'payment_id' => get_post_meta( $order->id, '_authnet_payment_id', true ),
			'expiry' => get_post_meta( $order->id, '_authnet_expiry', true ),
		);

		if ( ! $card ) {
			return new WP_Error( 'cardpay_authnet_error', __( 'Customer not found', 'woocommerce-cardpay_authnet' ) );
		}

		$authnet = new WC_Cardpay_Authnet_API();
		if ( 'authorize' == $this->transaction_type ) {
			$response = $authnet->authorize( $this, $order, $amount, $card );
		} else {
			$response = $authnet->purchase( $this, $order, $amount, $card );
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( isset( $response->transactionResponse->responseCode ) && '1' == $response->transactionResponse->responseCode ) {
			$order->payment_complete();
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
				'cc_expiry' => $card['expiry'],
				'transaction_type' => $this->transaction_type,
			);
			add_post_meta( $order->id, '_authnet_transaction', $tran_meta );
			return true;
		} else {
			$order->add_order_note( __( 'Authorize.Net payment declined', 'woocommerce-cardpay-authnet' ) );

			return new WP_Error( 'authnet_payment_declined', __( 'Payment was declined - please try another card.', 'woocommerce-cardpay-authnet' ) );
		}
	}

	/**
	 * scheduled_subscription_payment function.
	 *
	 * @param float $amount_to_charge The amount to charge.
	 * @param WC_Order $renewal_order A WC_Order object created to record the renewal payment.
	 * @access public
	 * @return void
	 */
	public function scheduled_subscription_payment( $amount_to_charge, $renewal_order ) {
		$result = $this->process_subscription_payment( $renewal_order, $amount_to_charge );

		if ( is_wp_error( $result ) ) {
			$renewal_order->update_status( 'failed', sprintf( __( 'Authorize.Net Transaction Failed (%s)', 'woocommerce' ), $result->get_error_message() ) );
		}
	}

	/**
	 * Update the card meta for a subscription after using Authorize.Net to complete a payment to make up for
	 * an automatic renewal payment which previously failed.
	 *
	 * @access public
	 * @param WC_Subscription $subscription The subscription for which the failing payment method relates.
	 * @param WC_Order $renewal_order The order which recorded the successful payment (to make up for the failed automatic payment).
	 * @return void
	 */
	public function update_failing_payment_method( $subscription, $renewal_order ) {
		update_post_meta( $subscription->id, '_authnet_customer_id', get_post_meta( $renewal_order->id, '_authnet_customer_id', true ) );
		update_post_meta( $subscription->id, '_authnet_payment_id', get_post_meta( $renewal_order->id, '_authnet_payment_id', true ) );
		update_post_meta( $subscription->id, '_authnet_expiry', get_post_meta( $renewal_order->id, '_authnet_expiry', true ) );
	}

	/**
	 * Include the payment meta data required to process automatic recurring payments so that store managers can
	 * manually set up automatic recurring payments for a customer via the Edit Subscription screen in Subscriptions v2.0+.
	 *
	 * @since 2.4
	 * @param array $payment_meta associative array of meta data required for automatic payments
	 * @param WC_Subscription $subscription An instance of a subscription object
	 * @return array
	 */
	public function add_subscription_payment_meta( $payment_meta, $subscription ) {
		$payment_meta[ $this->id ] = array(
			'post_meta' => array(
				'_authnet_customer_id' => array(
					'value' => get_post_meta( $subscription->id, '_authnet_customer_id', true ),
					'label' => 'Authorize.Net Customer ID',
				),
				'_authnet_payment_id' => array(
					'value' => get_post_meta( $subscription->id, '_authnet_payment_id', true ),
					'label' => 'Authorize.Net Payment ID',
				),
				'_authnet_expiry' => array(
					'value' => get_post_meta( $subscription->id, '_authnet_expiry', true ),
					'label' => 'Authorize.Net Expiry',
				),
			),
		);

		return $payment_meta;
	}

	/**
	 * Validate the payment meta data required to process automatic recurring payments so that store managers can
	 * manually set up automatic recurring payments for a customer via the Edit Subscription screen in Subscriptions 2.0+.
	 *
	 * @since 2.4
	 * @param string $payment_method_id The ID of the payment method to validate
	 * @param array $payment_meta associative array of meta data required for automatic payments
	 * @return array
	 */
	public function validate_subscription_payment_meta( $payment_method_id, $payment_meta ) {
		if ( $this->id === $payment_method_id ) {
			if ( ! isset( $payment_meta['post_meta']['_authnet_customer_id']['value'] ) || empty( $payment_meta['post_meta']['_authnet_customer_id']['value'] ) ) {
				throw new Exception( 'An Authorize.Net Customer ID value is required.' );
			}
			if ( ! isset( $payment_meta['post_meta']['_authnet_payment_id']['value'] ) || empty( $payment_meta['post_meta']['_authnet_payment_id']['value'] ) ) {
				throw new Exception( 'An Authorize.Net Payment ID value is required.' );
			}
			if ( ! isset( $payment_meta['post_meta']['_authnet_expiry']['value'] ) || empty( $payment_meta['post_meta']['_authnet_expiry']['value'] ) ) {
				throw new Exception( 'An Authorize.Net Expiry value is required.' );
			}
		}
	}

	/**
	 * Don't transfer customer meta to resubscribe orders.
	 *
	 * @access public
	 * @param int $resubscribe_order The order created for the customer to resubscribe to the old expired/cancelled subscription
	 * @return void
	 */
	public function delete_resubscribe_meta( $resubscribe_order ) {
		delete_post_meta( $resubscribe_order->id, '_authnet_customer_id' );
		delete_post_meta( $resubscribe_order->id, '_authnet_payment_id' );
		delete_post_meta( $resubscribe_order->id, '_authnet_expiry' );
	}

	/**
	 * Process a pre-order payment when the pre-order is released
	 *
	 * @param WC_Order $order
	 * @return wp_error|void
	 */
	public function process_pre_order_release_payment( $order ) {
		$amount = $order->get_total();
		$card = array(
			'customer_id' => get_post_meta( $order->id, '_authnet_customer_id', true ),
			'payment_id' => get_post_meta( $order->id, '_authnet_payment_id', true ),
			'expiry' => get_post_meta( $order->id, '_authnet_expiry', true ),
		);

		if ( ! $card ) {
			return new WP_Error( 'authnet_error', __( 'Customer not found', 'woocommerce-cardpay-authnet' ) );
		}

		$authnet = new WC_Cardpay_Authnet_API();
		if ( 'authorize' == $this->transaction_type ) {
			$response = $authnet->authorize( $this, $order, $amount, $card );
		} else {
			$response = $authnet->purchase( $this, $order, $amount, $card );
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( isset( $response->transactionResponse->responseCode ) && '1' == $response->transactionResponse->responseCode ) {
			$order->payment_complete();
			$amount_approved = number_format( $response->amount / 100, '2', '.', '' );
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
				'cc_expiry' => $card['expiry'],
				'transaction_type' => $this->transaction_type,
			);
			add_post_meta( $order->id, '_authnet_transaction', $tran_meta );
		} else {
			$order->add_order_note( __( 'Authorize.Net payment declined', 'woocommerce-cardpay-authnet' ) );

			return new WP_Error( 'authnet_payment_declined', __( 'Payment was declined - please try another card.', 'woocommerce-cardpay-authnet' ) );
		}
	}
}
