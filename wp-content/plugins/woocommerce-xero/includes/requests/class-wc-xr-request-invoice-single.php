<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Request_Invoice_Single extends WC_XR_Request {

	public function __construct( WC_XR_Settings $settings, $id = '' ) {
		parent::__construct( $settings );

		// Set Endpoint
		$this->set_method( 'GET' );
		$this->set_endpoint( 'Invoices/' . $id );

		$this->set_query( $query );

	}

}
