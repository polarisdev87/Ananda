<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Request_Invoice_Get extends WC_XR_Request {

	public function __construct( WC_XR_Settings $settings, $page_no = 0 ) {
		parent::__construct( $settings );

		// Set Endpoint
		$this->set_method( 'GET' );
		$this->set_endpoint( 'Invoices' );

		$query = ['where' => 'InvoiceNumber!=null&&InvoiceNumber.StartsWith("WP-")'];

		if ($page_no > 0) {
			$query['page'] = $page_no;
		}

		$this->set_query( $query );

	}

}
