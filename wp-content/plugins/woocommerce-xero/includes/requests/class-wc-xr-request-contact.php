<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Request_Contact extends WC_XR_Request {

	public function __construct( WC_XR_Settings $settings, $email = null, $page = 0, $npi = '', $with_npi = false ) {
		parent::__construct( $settings );

		$this->set_method( 'GET' );
		$this->set_endpoint( 'Contacts' );

		$query = [];
		$where = [];
		if ($email) {
			$where[] = 'EmailAddress.ToLower()=="' . strtolower($email) . '"';
			// $query['where'] = 'EmailAddress!=null&&EmailAddress.StartsWith("'. $email . '")';
		}

		if ($page > 0) {
			$query['page'] = $page;
			// $query['where'] = 'IsCustomer==true';
		}

		if ($npi) {
			$where[] = 'AccountNumber.ToLower()=="' . strtolower($npi) . '"';
		}

		if ($with_npi) {
			$where[] = 'AccountNumber!=null';
		}

		if (count($where) > 0) {
			$query['where'] = implode('&&', $where);
		}

		if (count($query) > 0) {
			$this->set_query($query);
		}
	}

}
