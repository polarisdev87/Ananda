<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Request_Invoice_Email extends WC_XR_Request {

	public function __construct( WC_XR_Settings $settings, $invoice_id = '' ) {
		parent::__construct( $settings );

		// Set Endpoint
		$this->set_method( 'POST' );
		$this->set_endpoint( 'Invoices/' . $invoice_id . '/Email' );

		// Set the XML
		$this->set_body( '' );

	}

}
