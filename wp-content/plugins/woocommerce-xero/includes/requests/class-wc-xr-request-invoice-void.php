<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Request_Invoice_Void extends WC_XR_Request {

	public function __construct( WC_XR_Settings $settings, $invoice_number = '' ) {
		parent::__construct( $settings );

		// Set Endpoint
		$this->set_method( 'POST' );
		$this->set_endpoint( 'Invoices/' . $invoice_number );

		// Set the XML
		$this->set_body( $this->get_xml( $invoice_number ) );

	}

	public function get_xml( $invoice_number ) {
		$xml = '<Invoice>';
		$xml .= '<InvoiceNumber>'. $invoice_number .'</InvoiceNumber>';
		$xml .= '<Status>VOIDED</Status>';
		$xml .= '</Invoice>';
		return $xml;
	}

}
