<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Request_Invoice_Get extends WC_XR_Request {

	public function __construct( WC_XR_Settings $settings, $startsWith = '', $page_no = 0, $year = false ) {
		parent::__construct( $settings );

		// Set Endpoint
		$this->set_method( 'GET' );
		$this->set_endpoint( 'Invoices' );

		$query = [];

		if ($startsWith) {
			$query['where'] = 'InvoiceNumber!=null&&InvoiceNumber.StartsWith("'. $startsWith . '")';
		}

		if ($page_no > 0) {
			$query['page'] = $page_no;
		}

		if ($year) {
			$query['where'] = (isset($query['where']) ? ($query['where'] . '&&') : '') . 'Date >= DateTime('. date('Y,n,d', strtotime('-2 months')) .')&&Date<DateTime('. $year .',12,31)';
		}

		$this->set_query( $query );

	}

}
