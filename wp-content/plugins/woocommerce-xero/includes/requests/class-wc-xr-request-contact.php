<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Request_Contact extends WC_XR_Request {

	public function __construct( WC_XR_Settings $settings, $email = null, $page = 0 ) {
		parent::__construct( $settings );

		$this->set_method( 'GET' );
		$this->set_endpoint( 'Contacts' );

		$query = [];
		if ($email) {
			$query['where'] = 'EmailAddress=="' . $email . '"';
		}

		if ($page > 0) {
			$query['page'] = $page;
			$query['where'] = 'IsCustomer==true';
		}

		if (count($query) > 0) {
			$this->set_query($query);
		}
	}

}
