<?php

require_once('creds.php');

class SalesforceSDK {
	
	private $yourInstance = 'na50';
	private $client_id = SALESFORCE_CLIENT_ID;
	private $client_secret = SALESFORCE_CLIENT_SECRET;
	private $username = SALESFORCE_USERNAME;
	private $password = SALESFORCE_PASSWORD . SALESFORCE_SECRET_TOKEN;
	private $contact_field_id = 'Unique_Import_Id__c';
    private $base_url = "https://anandahemp.my.salesforce.com";

    private $token_url = "https://login.salesforce.com/services/oauth2/token";
    private $sandbox_token_url = "https://test.salesforce.com/services/oauth2/token";

    private $auth = null;

    private $debug = false;

	public function __construct( $sandbox = false, $debug = false ) {
		if ($sandbox) {
			$this->yourInstance = 'CS66';
			$this->username = SANDBOX_SALESFORCE_USERNAME;
			$this->password = SANDBOX_SALESFORCE_PASSWORD . SANDBOX_SALESFORCE_SECRET_TOKEN;
			$this->base_url = 'https://anandahemp--partial.cs66.my.salesforce.com';
			$this->token_url = $this->sandbox_token_url;
		}

		if ($debug) {
			$this->debug = $debug;
		}
		// $this->set_base_url();
		
		// $this->authenticate();
	}

	public function set_base_url() {
		$this->base_url = 'https://' . $this->yourInstance . '.salesforce.com';
	}

	public function get_base_url() {
		return $this->base_url;
	}


	public function do_request($url, $options = []) {

		$is_auth = $options['is_auth'] ?: false;
	    $token = $is_auth ? '' : $this->get_token();
	    $patch = $options['patch'] ?: false;
	    $post = $options['post'] ?: false;
	    $postData = $options['postData'] ?: '';
	    $delete = $options['delete'] ?: false;

	    $headers = [];

	    $ch = curl_init();

	    // var_dump($is_auth ? $url : $this->base_url . $url);

	    curl_setopt($ch, CURLOPT_URL, $is_auth ? $url : $this->base_url . $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_HEADER, FALSE);

	    if ($post) {
	        curl_setopt($ch, CURLOPT_POST, TRUE);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, is_string($postData) ? $postData : json_encode($postData));

	        if (!is_string($postData)) {
	            $headers[] = 'Content-Type: application/json';
	        }
	    }

	    if ($patch) {
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
	    }

	    if ($delete) {
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
	    }

	    if ($token) {
	        $headers[] = "Authorization: Bearer " . $token;
	        $headers[] = 'X-PrettyPrint: 1';
	    }

	    if (count($headers) > 0) {
	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    }

	    $response = curl_exec($ch);
	    curl_close($ch);

	    return json_decode($response);
	}

	public function authenticate() {

		$this->printTime();
		echo 'new token requested <br/>';

	    $response = $this->do_request($this->token_url, ['is_auth' => true, 'post' => true, 'postData' => http_build_query([
	            'grant_type' => 'password',
	            'client_id' => $this->client_id,
	            'client_secret' => $this->client_secret,
	            'username' => $this->username,
	            'password' => $this->password,
	        ])
	    ]);

	    $this->auth = $response;

	    $_SESSION['SALESFORCE_API_TOKEN'] = $this->auth->access_token ?: '';
	    $_SESSION['SALESFORCE_LAST_ACTIVITY'] = time();
	}

	public function get_auth() {
		return $this->auth;
	}

	public function get_token() {
		if (!isset($_SESSION['SALESFORCE_LAST_ACTIVITY']) || $_SESSION['SALESFORCE_LAST_ACTIVITY']=='' || $_SESSION['SALESFORCE_LAST_ACTIVITY'] < time() - 60 * 5) {
			$this->authenticate();
		}
		if (!isset($_SESSION['SALESFORCE_API_TOKEN']) || $_SESSION['SALESFORCE_API_TOKEN']=='') {
			$this->authenticate();
		}
		return $_SESSION['SALESFORCE_API_TOKEN'];
	}

	public function reset() {
		$old_token = $_SESSION['SALESFORCE_API_TOKEN'];
		$_SESSION['SALESFORCE_API_TOKEN'] = '';
		unset($_SESSION['SALESFORCE_API_TOKEN']);
		echo 'session cleared; old token was ' . $old_token;
	}

	public function describe($table) {
		return $this->do_request('/services/data/v20.0/sobjects/' . $table . '/describe');
	}

	public function get_invoice_by_id($id) {
        $invoice_manager = new WC_XR_Invoice_Manager(new WC_XR_Settings());

        $response = $invoice_manager->get_invoice_by_id($id);

        return $response;
	}

	public function get_all_invoices($startsWith = '', $year = false) {

        $invoice_manager = new WC_XR_Invoice_Manager(new WC_XR_Settings());

        $all_invoices = [];

        $page_no = 1;

        do {
            $response = $invoice_manager->get_all_invoices($startsWith, $page_no, $year);
            $invoices_wrapper = $response->Invoices;
            $invoices = $invoices_wrapper->Invoice ?: [];

            var_dump('current page________________' . $page_no . '______' . count($invoices_wrapper) . '_______' . count($invoices));

            if (count($invoices) > 0) {
                // $invoices = $invoices_wrapper->Invoice;

                foreach($invoices as $invoice) {
                	$all_invoices[] = $invoice;
                //     // var_dump($invoice);
                }
                // var_dump ($invoices_wrapper);
            }

            $page_no++;
        } while(count($invoices) > 0);

        return $all_invoices;
        // return $page_no;
	}

	public function get_contact_by_id($xero_contact_id) {

        $contact_manager = new WC_XR_Contact_Manager(new WC_XR_Settings());

		$response = $contact_manager->get_contact_by_id($xero_contact_id);

		return $response;
	}

	public function get_contact_by_email($email) {

        $contact_manager = new WC_XR_Contact_Manager(new WC_XR_Settings());

		$response = $contact_manager->get_id_by_email($email);

		return $response;
	}

	public function get_all_accounts($fields = ['Id', 'Name', 'NPI_Number__c', 'Unique_Import_Id__c']) {

		$results = [];

		$url = '/services/data/v43.0/query/?q=' . urlencode('SELECT ' . implode(', ', $fields) . ' FROM Account');

		while ($response = $this->do_request($url)) {
			foreach ($response->records as $el) {
				$record = [];
				foreach ($fields as $field) {
					$record[$field] = (string)$el->$field;
				}
				$results[] = $record;
			}
			// $results = array_merge($results, array_map(function($el) use ($fields) {
			// 	$record = [];
			// 	foreach ($fields as $field) {
			// 		$record[$field] = (string)$el->$field;
			// 	}
			// 	return $record;
			// }, $response->records));
			if (!$response->done && $response->nextRecordsUrl) {
				$url = $response->nextRecordsUrl;
			} else {
				break;
			}
		}
		return $results;
	}

	public function get_account_from_external_xero_contact_id($xero_contact_id = 'BLANK_ID') {

        $response = $this->do_request('/services/data/v20.0/sobjects/Account/' . $this->contact_field_id . '/' . $xero_contact_id);

        if ($response->Id) {
        	return $response;
        }

        var_dump('__________ not exist --- so migrate ____________');


        $response = $this->get_contact_by_id($xero_contact_id);

        if (!$response) return null;

        $contact = $response->Contacts->Contact[0];

        foreach ($contact->Phones->Phone as $phone) {
        	if ((string)$phone->PhoneType == 'DEFAULT') {
        		$phone_number = (string)$phone->PhoneAreaCode . (string)$phone->PhoneNumber;
        	}
        }
        foreach ($contact->Addresses->Address as $item) {
        	if ((string)$item->AddressType == 'POBOX') {
        		$address = $item;
        	}
        }

        $data = [
			'Unique_Import_Id__c' => $xero_contact_id,
			// 'Account Owner Name' => '',
			// 'Assigned_Rep__c' => '',
			'Name' => (string)$contact->Name,
			// 'Parent Account Name' => '',
			// 'Website' => '',
			// 'Description' => '',
			'Type' => 'Customer',
			'Subtype__c' => 'Pharmacy',
			'Phone' => $phone_number ?: '',
			// 'Industry' => '',
			'BillingStreet' => (string)$address->AddressLine1,
			'BillingCity' => (string)$address->City,
			'BillingState' => (string)$address->Region,
			'BillingPostalCode' => (string)$address->PostalCode,
			'BillingCountry' => (string)$address->Country,
			'ShippingStreet' => (string)$address->AddressLine1,
			'ShippingState' => (string)$address->City,
			'ShippingCity' => (string)$address->Region,
			'ShippingPostalCode' => (string)$address->PostalCode,
			'ShippingCountry' => (string)$address->Country,
			// 'Business Interest' => '',
			// 'Wholesaler' => '',
			// 'Num of Stores' => '',
			// 'Num of Patients with Chronic Pain' => '',
			// 'Num of Patients with Insomnia' => '',
			// 'Ownership Structure' => '',
			// 'Primary Major Wholesaler' => '',
			// 'Other Major Wholesalers' => '',
			// 'GPO Name' => '',
			// 'Average Order Size' => '',
			// 'Average Time Between Orders' => '',
			'NPI_Number__c' => (string)$contact->AccountNumber,
			'Brand__c' => 'Ananda Professional',
        ];

        $response = $this->do_request('/services/data/v20.0/sobjects/Account/', ['post' => true, 'postData' => $data]);

        if ($response->success == true) {
        	$obj = new stdClass();
        	$obj->Id = (string)$response->id;
        	return $obj;
        } else {
        	var_dump($response);
        	return null;
        }

	}

	public function create_account_from_xero_contact_id($xero_contact_id = '', $branding_theme = '') {
		if (!$xero_contact_id) return null;

        $response = $this->get_contact_by_id($xero_contact_id);

        if ($this->debug) {
        	// echo '<pre>', var_dump($response), '</pre>';
        }

        if (!$response) return null;

        $contact = $response->Contacts->Contact[0];

        if ($this->debug) {
        	echo '<span style="color: #f30">** xero contact api response **</span></br>';
        	echo '<pre>', var_dump($contact), '</pre>';
        }

        foreach ($contact->Phones->Phone as $phone) {
        	if ((string)$phone->PhoneType == 'DEFAULT') {
        		$phone_number = (string)$phone->PhoneAreaCode . (string)$phone->PhoneNumber;
        	}
        }
        foreach ($contact->Addresses->Address as $item) {
        	if ((string)$item->AddressType == 'POBOX') {
        		$address = $item;
        	}
        }

        $data = [
			'Unique_Import_Id__c' => $xero_contact_id,
			'Name' => (string)$contact->Name,
			'Type' => 'Customer',
			'Subtype__c' => 'Pharmacy',
			'Phone' => $phone_number ?: '',
			'BillingStreet' => (string)$address->AddressLine1,
			'BillingCity' => (string)$address->City,
			'BillingState' => (string)$address->Region,
			'BillingPostalCode' => (string)$address->PostalCode,
			'BillingCountry' => (string)$address->Country,
			'ShippingStreet' => (string)$address->AddressLine1,
			'ShippingCity' => (string)$address->City,
			'ShippingState' => (string)$address->Region,
			'ShippingPostalCode' => (string)$address->PostalCode,
			'ShippingCountry' => (string)$address->Country,
			'NPI_Number__c' => (string)$contact->AccountNumber,
			'Brand__c' => $branding_theme ?: (string)$contact->BrandingTheme->Name,
        ];

        $contactData = [
        	'AccountId' => '',
        	'FirstName' => (string)$contact->FirstName,
        	'LastName' => (string)$contact->LastName,
			'Phone' => $phone_number ?: '',
			'Email' => (string)$contact->EmailAddress,
			'MailingStreet' => (string)$address->AddressLine1,
			'MailingCity' => (string)$address->City,
			'MailingState' => (string)$address->Region,
			'MailingPostalCode' => (string)$address->PostalCode,
        ];

        if ($this->debug) {
        	echo '<span style="color: #f30">** data to be inserted through salesforce api **</span></br>';
        	echo '<pre>', var_dump($data), '</pre>';
        }

        $response = $this->do_request('/services/data/v43.0/sobjects/Account/', ['post' => true, 'postData' => $data]);

        if ($this->debug) {
        	echo '<span style="color: #f30">** salesforce api response: insert new account **</span></br>';
        	echo '<pre>', var_dump($response), '</pre>';
        }

        if ($response->success == true) {
        	$contactData['AccountId'] = (string)$response->id;
        	$contact_response = $this->do_request('/services/data/v43.0/sobjects/Contact/', ['post' => true, 'postData' => $contactData]);
        	// echo '<pre>', var_dump($contact_response), '</pre>';
        	return [
        		'Id' => (string)$response->id,
				'Name' => (string)$contact->Name,
        		'Unique_Import_Id__c' => $xero_contact_id,
        		'NPI_Number__c' => (string)$contact->AccountNumber,
        	];
        }

        if ($this->debug) {
        	echo '<span style="color: #f30">** NPI Number used for new account creation **</span></br>';
        	echo '<pre>', var_dump($data['NPI_Number__c']), '</pre>';
        }

        if (!$data['NPI_Number__c']) return null;

        unset($data['NPI_Number__c']);

        if ($this->debug) {
        	echo '<span style="color: #f30">** data to be upserted through salesforce api **</span></br>';
        	echo '<pre>', var_dump($data), '</pre>';
        }

        $response = $this->do_request('/services/data/v20.0/sobjects/Account/NPI_Number__c/' . (string)$contact->AccountNumber, ['post' => true, 'postData' => $data, 'patch' => true]);

        if ($this->debug) {
        	echo '<span style="color: #f30">** salesforce api response: update existing account based on NPI **</span></br>';
        	echo '<pre>', var_dump($response), '</pre>';
        }

        if ($response->success == true) {
        	// $contactData['AccountId'] = (string)$response->id;
        	// patch contact
        	return [
        		'Id' => (string)$response->id,
				'Name' => (string)$contact->Name,
        		'Unique_Import_Id__c' => $xero_contact_id,
        		'NPI_Number__c' => (string)$contact->AccountNumber,
        	];
        } else {
        	$response = $this->do_request('/services/data/v20.0/sobjects/Account/NPI_Number__c/' . (string)$contact->AccountNumber);

        	if ($this->debug) {
        		echo '<span style="color: #f30">** salesforce api response: get existing account based on NPI **</span></br>';
        		echo '<pre>', var_dump($response), '</pre>';
        	}

        	if (!$response->Id) {
        		$response = $this->do_request('/services/data/v20.0/sobjects/Account/Unique_Import_Id__c/' . $xero_contact_id);
        	}

        	if ($this->debug) {
        		echo '<span style="color: #f30">** salesforce api response: get existing account based on Unique_Import_Id__c (Xero Contact Id) **</span></br>';
        		echo '<pre>', var_dump($response), '</pre>';
        	}

        	if ($response->Id) {
	        	return [
	        		'Id' => (string)$response->Id,
					'Name' => (string)$contact->Name,
	        		'Unique_Import_Id__c' => $xero_contact_id,
        			'NPI_Number__c' => (string)$contact->AccountNumber,
	        	];
        	} else {
        		return null;
        	}
        }
	}

	public function get_all_salesforce_invoices($startsWith = '', $year = false, $fields = ['Id', 'InvoiceNumber__c', 'Account_ID__c', 'Xero_Invoice_ID__c', 'UpdatedDateUTC__c']) {

		$results = [];

		if ($startsWith) {
			$where = [];
			if (is_array($startsWith)) {
				foreach ($startsWith as $start) {
					$where[] = "InvoiceNumber__c LIKE '" . $start . "%'";
				}
			} else {
				$where[] = "InvoiceNumber__c LIKE '" . $startsWith . "%'";
			}
			$url = '/services/data/v43.0/query/?q=' . urlencode('SELECT ' . implode(', ', $fields) . ' FROM Ananda_Invoice__c WHERE '. implode(' OR ', $where));
		} else {
			$url = '/services/data/v43.0/query/?q=' . urlencode('SELECT ' . implode(', ', $fields) . ' FROM Ananda_Invoice__c');
		}

		while ($response = $this->do_request($url)) {
			$results = array_merge($results, array_map(function($el) use ($fields) {
				$item = [];
				foreach ($fields as $key) {
					$item[$key] = $el->$key;
				}
				return $item;
			}, $response->records));
			if (!$response->done && $response->nextRecordsUrl) {
				$url = $response->nextRecordsUrl;
			} else {
				break;
			}
		}

		return $results;
	}

	public function migrate_invoices($startsWith = '', $year = false) {

		$this->authenticate();

		$this->printTime();
		echo ('_________ getting Accounts __________ <br/>');
		$accounts = $this->get_all_accounts();
		foreach ($accounts as $key => $item) {
			if ($item['NPI_Number__c'] == '0000000000') {
				$dummy_account = $item;
				break;
			}
		}


        $invoice_manager = new WC_XR_Invoice_Manager(new WC_XR_Settings());

        $invoices_data = [
            'records' => []
        ];


		$this->printTime();
		echo ('_________ getting Invoices ____________ <br/>');
		$salesforce_invoices = $this->get_all_salesforce_invoices($startsWith, $year);
        $invoices_patch_data = [
        	'allOrNone' => false,
        	'records' => [],
        ];

        if (!is_array($startsWith)) {
        	$startsWith = [$startsWith];
        }

        $invoice_ref_no = 1;
        $line_item_ref_no = 1;
        $tracking_category_ref_no = 1;

        foreach ($startsWith as $startsWithKeyword) {

	        $page_no = 1;

	        $account_ref_no = 1;

	        // $accounts_from_ext = [];

	        do {
	        	sleep(1);
	            $response = $invoice_manager->get_all_invoices($startsWithKeyword, $page_no, $year);
	            $invoices_wrapper = $response->Invoices;
	            $invoices = $invoices_wrapper->Invoice ?: [];

		        $this->printTime();
	            echo ('___________ current page________________' . $page_no . '______' . count($invoices_wrapper) . '_______' . count($invoices) . '<br/>');

	            // echo ' _______________ current page________________' . $page_no . '______' . count($invoices_wrapper) . '<br/>';

	            if (count($invoices) > 0) {
	                foreach ($invoices as $invoice) {

	                	// var_dump($invoice);

	                	$ext_id = (string)$invoice->Contact->ContactID;

	                	$branding_theme = '';
	                	if ((string)$invoice->BrandingThemeID == 'c862879a-49c7-40a8-ba05-0dd14a18813f') {
	                		$branding_theme = 'Ananda Hemp';
	                	} else if ((string)$invoice->BrandingThemeID == 'd415c810-ccc3-4e10-b6ae-4c7cd17030e8') {
	                		$branding_theme = 'Ananda Professional';
	                	}

	                	/*if (!isset($accounts_from_ext[$ext_id])) {
	                		$account = $this->get_account_from_external_xero_contact_id($ext_id);
	                		if ($account) {
	                			$accounts_from_ext[$ext_id] = $account;
	                		} else {
	                			var_dump ('================= invalid external id ' . $ext_id . '------------------');
	                			continue ;
	                		}
	                	} else {
	                		$account = $accounts_from_ext[$ext_id];
	                	}*/

	                	$account = null;

	                	foreach ($accounts as $key => $item) {
	                		if ($item['Unique_Import_Id__c'] == $ext_id) {
	                			$account = $item;
	                			break;
	                		}
	                	}
	                	if (!$account) {
	                		if ( strpos((string)$invoice->InvoiceNumber, 'WP-') === 0 
	                			|| strpos((string)$invoice->InvoiceNumber, 'INV-') === 0
	                			|| strpos((string)$invoice->InvoiceNumber, 'CN-') === 0
	                			|| strpos((string)$invoice->InvoiceNumber, 'AE-') === 0
	                			|| strpos((string)$invoice->InvoiceNumber, 'FPN-') === 0
	                			|| strpos((string)$invoice->InvoiceNumber, 'TCG-') === 0
	                			|| strpos((string)$invoice->InvoiceNumber, 'CPC-') === 0
	                		) {
	                			$account = $this->create_account_from_xero_contact_id($ext_id, $branding_theme);
	                			if ($account) {
	                				echo ' ____________ created account with ' . $ext_id . ' from invoice ___' . (string)$invoice->InvoiceNumber . '<br/>';
	                				$accounts[] = $account;
	                			} else {
	                				echo ' _____________ failed to create account with invoice ___ ' . (string)$invoice->InvoiceNumber . ' due to ' . $ext_id . '<br/>';
		            				if ($dummy_account) {
		            					$account = $dummy_account;
		            				} else {
		            					// echo '_______found no dummy account for ' . (string)$invoice->InvoiceNumber . '<br/>';
		            					continue;
		            				}
	                			}
	                		} else {
	            				echo ' _____________ skipping relationship invoice ___ ' . (string)$invoice->InvoiceNumber . ' due to ' . $ext_id . '<br/>';
	            				if ($dummy_account) {
	            					$account = $dummy_account;
	            				} else {
	            					// echo '_______found no dummy account for ' . (string)$invoice->InvoiceNumber . '<br/>';
	            					continue;
	            				}
	            			}
	                	} else {

	                		// echo '_______________ found relevant account for ' . (string)$invoice->InvoiceNumber . '<br/>';
	                	}

	                	// var_dump($account);

						$invoice_record = [
				            'attributes' => [
				                'type' => 'Ananda_Invoice__c',
				                'referenceId' => 'REF__INVOICE_' . $invoice_ref_no++ . '_' . (string)$invoice->InvoiceNumber . '___' . (string)$invoice->InvoiceID
				            ],
				            'AmountCredited__c' => (string)$invoice->AmountCredited,
				            'AmountDue__c' => (string)$invoice->AmountDue,
				            'AmountPaid__c' => (string)$invoice->AmountPaid,
				            'BrandingTheme__c' => $branding_theme, //(string)$invoice->BrandingThemeID,
				            'Date__c' => date('Y-m-d', strtotime((string)$invoice->Date)),
				            'DueDate__c' => date('Y-m-d', strtotime((string)$invoice->DueDate)),
				            'FullyPaidOnDate__c' => $invoice->FullyPaidOnDate ? ((string)$invoice->FullyPaidOnDate . 'Z') : '',
				            'InvoiceNumber__c' => (string)$invoice->InvoiceNumber,
				            'Reference__c' => (string)$invoice->Reference,
				            'Status__c' => (string)$invoice->Status,
				            'SubTotal__c' => (string)$invoice->SubTotal,
				            'Total__c' => (float)$invoice->Total,
				            'TotalDiscount__c' => (string)$invoice->TotalDiscount,
				            'TotalTax__c' => (string)$invoice->TotalTax,
				            'Type__c' => (string)$invoice->Type,
				            // 'AccountNumber__c' => $account->AccountNumber ?: '',
				            'Account_ID__c' => $account['Id'] ?: '',
				            'Xero_Invoice_ID__c' => (string)$invoice->InvoiceID,
				            'UpdatedDateUTC__c' => (string)$invoice->UpdatedDateUTC,
				        ];

			        	$invoice_key = array_search((string)$invoice->InvoiceID, array_column($salesforce_invoices, 'Xero_Invoice_ID__c'));

				        if ($invoice_key !== false) {

				        	echo 'found from salesforce invoices list ' . (string)$invoice->InvoiceNumber . ' ----- ' . $invoice_key . ' ------ (should update existing one) ';

				        	if ($salesforce_invoices[$invoice_key]['UpdatedDateUTC__c'] != (string)$invoice->UpdatedDateUTC
				        		|| $salesforce_invoices[$invoice_key]['Account_ID__c'] != $account['Id']
				        	) {

				        		echo 'and it is modified';

				        		unset($invoice_record['attributes']['referenceId']);

				        		$invoice_record['id'] = $salesforce_invoices[$invoice_key]['Id'];

				        		$invoices_patch_data['records'][] = $invoice_record;

			                    if (count($invoices_patch_data['records']) > 150) {
		        					$this->printTime();
		        					echo ' _________ doing patch updates for updated invoices ( ' . count($invoices_patch_data['records']) . ')' . '<br/>';
		        					// echo '<pre>', var_dump($invoices_patch_data), '</pre>';
		        					$response = $this->do_request('/services/data/v43.0/composite/sobjects', ['post' => true, 'postData' => $invoices_patch_data, 'patch' => true]);
		        					// var_dump($response);
			                    	$invoices_patch_data['records'] = [];
							    }
				        	} else {
				        		echo 'and it is not modified';
				        	}

				        	echo '<br/>';

				        } else {

			        		echo 'not found from salesforce invoices list ' . (string)$invoice->InvoiceID . '____' . (string)$invoice->InvoiceNumber . ' (should create new one)<br/>';

					        $line_items_data = [
					            'records' => []
					        ];
					        $line_items_wrapper = $invoice->LineItems;
					        if (count($line_items_wrapper) > 0) {
					            foreach($line_items_wrapper->LineItem as $line_item) {
					                $line_item_record = [
					                    'attributes' => [
					                        'type' => 'Ananda_Invoice_LineItem__c',
					                        'referenceId' => 'REF__LINE_ITEM_' . $line_item_ref_no++ . '_' . (string)$line_item->LineItemID,
					                    ],
					                    'AccountCode__c' => (string)$line_item->AccountCode,
					                    'Description__c' => substr((string)$line_item->Description, 0, 250),
					                    'DiscountRate__c' => (string)$line_item->DiscountRate,
					                    'ItemCode__c' => (string)$line_item->ItemCode,
					                    'LineAmount__c' => (string)$line_item->LineAmount,
					                    'Quantity__c' => (string)$line_item->Quantity,
					                    'TaxAmount__c' => (string)$line_item->TaxAmount,
					                    'TaxType__c' => (string)$line_item->TaxType,
					                    'UnitAmount__c' => (string)$line_item->UnitAmount,
					                    'AccountCode__c' => (string)$line_item->AccountCode,
					                    'Xero_Invoice_Item_ID__c' => (string)$line_item->LineItemID,
					                ];

					                $tracking_categories_data = [
					                    'records' => []
					                ];
					                $tracking_categories_wrapper = $line_item->Tracking;
					                if (count($tracking_categories_wrapper) > 0) {
					                    foreach ($tracking_categories_wrapper->TrackingCategory as $tracking_category) {
					                        $tracking_categories_data['records'][] = [
					                            'attributes' => [
					                                'type' => 'Ananda_Invoice_TrackingCategory__c',
					                                'referenceId' => 'REF__TRACKING_CATGEGORY_' . $tracking_category_ref_no++ . '_' . (string)$tracking_category->TrackingCategoryID,
					                            ],
					                            'Name__c' => (string)$tracking_category->Name,
					                            'Option__c' => (string)$tracking_category->Option,
					                            'Tracking_Category_ID__c' => (string)$tracking_category->TrackingCategoryID,
					                        ];
					                    }

					                    $line_item_record['Ananda_Invoice_TrackingCategories__r'] = $tracking_categories_data;
					                }

					                $line_items_data['records'][] = $line_item_record;
					            }
					            $invoice_record['Ananda_Invoice_LineItems__r'] = $line_items_data;
					        }

		                    $invoices_data['records'][] = $invoice_record;

		                    if (count($invoices_data['records']) > 6) {
		                    	$this->handle_submit_records_data_queue($invoices_data, 'Ananda_Invoice__c');
		                    	$invoices_data['records'] = [];
						    }
						}

	                }
	                // var_dump ($invoices_wrapper->Invoice);
	            }

	            $page_no++;
	        } while(count($invoices) > 0);

	    } // end foreach

        // exit('ddd');

		if (count($invoices_patch_data['records']) > 0) {
			$this->printTime();
			echo ' _________ doing patch updates for updated invoices ( ' . count($invoices_patch_data['records']) . ')' . '<br/>';
			// echo '<pre>', var_dump($invoices_patch_data), '</pre>';
			$response = $this->do_request('/services/data/v43.0/composite/sobjects', ['post' => true, 'postData' => $invoices_patch_data, 'patch' => true]);
			// var_dump($response);
        	$invoices_patch_data['records'] = [];
	    }

        $this->handle_submit_records_data_queue($invoices_data, 'Ananda_Invoice__c');

        // $response = curl_salesforce($line_item_records_url, $token, true, $line_items_data);
        // var_dump('___________________Line Items Records Result___________________');
        // var_dump($response);

	}

	public function printTime() {
		echo '<span style="color: #f30">'. date('Y-m-d H:i:s') .'</span>';
	}

	public function handle_submit_records_data_queue($records_data, $table) {
		// var_dump($records_data);

		while (count($records_data['records']) > 0) {
	        $response = $this->do_request('/services/data/v34.0/composite/tree/'. $table .'/', ['post' => true, 'postData' => $records_data]);
	        if ($response->hasErrors) {
	        	$error_refIds = [];
	        	$error_duplicate_ids = [];
	        	foreach ($response->results as $error) {
	        		$error_refIds[(string)$error->referenceId] = (string)$error->errors[0]->statusCode;
	        		$this->printTime();
	        		echo '_______________ Error occured _____________________ on ' . (string)$error->referenceId . '___and skip it <br/>';
	        		if ((string)$error->errors[0]->statusCode != 'DUPLICATE_VALUE') {
		        		echo '<pre>', var_dump($error), '</pre>';
		        		echo '<br/>';
		        	} else {
		        		$tmp = explode(': ', (string)$error->errors[0]->message);
		        		$error_duplicate_ids[] = $tmp[2];
		        	}
	        	}

	        	if (count($error_duplicate_ids) > 0) {
					$response = $this->do_request('/services/data/v43.0/composite/sobjects?ids=' . implode(',', $error_duplicate_ids), ['delete' => true]);
					// echo '<pre>', var_dump($response), '</pre>';
					// echo '<br/>';
	        	}

	        	foreach ($records_data['records'] as $key => $record) {
	        		if (isset($error_refIds[$record['attributes']['referenceId']]) && $error_refIds[$record['attributes']['referenceId']] == 'DUPLICATE_VALUE') {
	        			unset($records_data['records'][$key]);
		        	}
	        	}

				$records_data = array_map('array_values', $records_data);
	        } else {
	        	$this->printTime();
	        	echo '_________________ RECORD SUBMIT RESULT (' . count($records_data['records']) .  ') ___________________ <br/>';
	        	// var_dump($records_data);
	        	// echo '<pre>', var_dump($response), '</pre>';
	        	// echo '<br/>';
	        	// if (is_array($response) && $response[0]->errorCode) {
	        	// 	echo '<pre>', var_dump($records_data), '</pre>';
	        	// 	echo '<br/>';
	        	// }
	        	$records_data['records'] = [];
	        }
	    }
	}

	public function pull_xero_contacts_in_table_format() {
		echo '<table border="1"><thead><tr>
			<th>ID</th>
			<th>Name</th>
			<th>NPI</th>
		</tr></thead><tbody>';

        $contact_manager = new WC_XR_Contact_Manager(new WC_XR_Settings());

        $contacts_data = [
        	'records' => []
        ];

        $page_no = 1;
        $account_ref_no = 1;

        do {
            $response = $contact_manager->get_all_contacts($page_no);
            $contacts_wrapper = $response->Contacts;

            // var_dump('current page________________' . $page_no . '______' . count($contacts_wrapper));
            // var_dump($contacts_wrapper);

            if (count($contacts_wrapper) > 0) {
                foreach ($contacts_wrapper->Contact as $contact) {

                	$contact_record = [
                		'attributes' => [
                			'type' => 'Account',
                			'referenceId' => 'CONTACT_' . $account_ref_no++ . (string)$contact->ContactID,
                		],
                		'Xero_Contact_ID__c' => $contact->ContactID,
                		'Name' => (string)$contact->Name,
                		'NPI_Number__c' => (string)$contact->AccountNumber,
                		'AccountNumber' => (string)$contact->AccountNumber,

                	];

                    $contacts_data['records'][] = $contact_record;


                    echo '<tr>
                    	<td>'.(string)$contact->ContactID.'</td>
                    	<td>'.(string)$contact->Name.'</td>
                    	<td>'.((string)$contact->AccountNumber ?: '').'</td>
                    </tr>';
                }
            }

            $page_no++;
        } while(count($contacts_wrapper) > 0);

		echo '</tbody></table>';
		exit('');
	}

	public function migrate_contacts() {

        $contact_manager = new WC_XR_Contact_Manager(new WC_XR_Settings());

        $contacts_data = [
        	'records' => []
        ];

        $page_no = 8;
        $account_ref_no = 1;

        // do {
            $response = $contact_manager->get_all_contacts($page_no);
            $contacts_wrapper = $response->Contacts;

            var_dump('current page________________' . $page_no . '______' . count($contacts_wrapper));
            var_dump($contacts_wrapper);

            if (count($contacts_wrapper) > 0) {
                foreach ($contacts_wrapper->Contact as $contact) {

                	$contact_record = [
                		'attributes' => [
                			'type' => 'Account',
                			'referenceId' => 'CONTACT_' . $account_ref_no++ . (string)$contact->ContactID,
                		],
                		'Xero_Contact_ID__c' => $contact->ContactID,
                		'Name' => (string)$contact->Name,
                		'NPI_Number__c' => (string)$contact->AccountNumber,
                		'AccountNumber' => (string)$contact->AccountNumber,

                	];

                    $contacts_data['records'][] = $contact_record;
                }
            }

            $page_no++;
        // } while(count($contacts_wrapper) > 0);


        // $response = $this->do_request('/services/data/v34.0/composite/tree/Account/', ['post' => true, 'postData' => $contacts_data]);
        // var_dump('___________________Contact Records Result___________________');
        // var_dump($response);

	}


	public function get_all_stores() { // stores from AP.com
		global $wpdb;
		$ASL_PREFIX = ASL_PREFIX;
		$bound = '';
		$clause = '';
   		$country_field = " {$ASL_PREFIX}countries.`country`,";
   		$extra_sql = "LEFT JOIN {$ASL_PREFIX}countries ON s.`country` = {$ASL_PREFIX}countries.id";

		$query   = "SELECT s.`id`, `title`,  `description`, `street`,  `city`,  `state`, `postal_code`, {$country_field} `lat`,`lng`,`phone`,  `fax`,`email`,`website`,`logo_id`,{$ASL_PREFIX}storelogos.`path`,`marker_id`,`description_2`,`open_hours`, `ordr`,
					group_concat(category_id) as categories FROM {$ASL_PREFIX}stores as s 
					LEFT JOIN {$ASL_PREFIX}storelogos ON logo_id = {$ASL_PREFIX}storelogos.id
					LEFT JOIN {$ASL_PREFIX}stores_categories ON s.`id` = {$ASL_PREFIX}stores_categories.store_id
					$extra_sql
					WHERE (is_disabled is NULL || is_disabled = 0) AND (`lat` != '' AND `lng` != '') {$bound} {$clause}
					GROUP BY s.`id` ORDER BY `title` ";


		$all_results = $wpdb->get_results($query);

		return $all_results;
	}

	/*public function migrate_stores_old() { // migrate from AP.com to PharmacyStore object (SF)
		$stores = $this->get_all_stores();

		$stores_data = [
			'records' => []
		];

		$referenceId = 1;

		foreach ($stores as $key => $store) {
			$store_record = [
				'attributes' => [
					'type' => 'PharmacyStore__c',
					'referenceId' => 'Store_REF_' . $referenceId++ . '_' . $store->id,
				],
				'Title__c' => $store->title,
				'Description__c' => $store->description,
				'Phone__c' => $store->phone,
				'Fax__c' => $store->fax,
				'Email__c' => $store->email,
				'Street__c' => $store->street,
				'City__c' => $store->city,
				'State__c' => $store->state,
				'PostalCode__c' => $store->postal_code,
				'Country__c' => $store->country,
				'Lat__c' => $store->lat,
				'Long__c' => $store->lng,
				'StoreID__c' => $store->id,
			];
			$stores_data['records'][] = $store_record;

			if (count($stores_data['records']) > 100) {
				$this->handle_submit_records_data_queue($stores_data, 'PharmacyStore__c');
				$stores_data['records'] = [];
			}
		}

        $this->handle_submit_records_data_queue($stores_data, 'PharmacyStore__c');

	    // return $response;
	}*/

	public function get_all_pharmacy_accounts() { // from Salesforce Account object - Type: Customer, SubType: Pharamcy

		$results = [];

		// $url = '/services/data/v43.0/sobjects/Account/0016A00000ZzQK2QAN';
		$url = '/services/data/v43.0/query/?q=' . urlencode('SELECT Id, Name, Phone, StoreID__c, BillingStreet, BillingCity, BillingState, BillingPostalCode, BillingCountry FROM Account where Type=\'Customer\' and Subtype__c=\'Pharmacy\'');

		while ($response = $this->do_request($url)) {
			foreach ($response->records as $el) {
				$results[] = [
					'Id'				=> $el->Id,
					'StoreID'			=> $el->StoreID__c,
					'Name'			 	=> $el->Name,
					'Phone'	 			=> $el->Phone,
					'BillingStreet'	 	=> $el->BillingStreet,
					'BillingCity'	 	=> $el->BillingCity,
					'BillingPostalCode'	=> $el->BillingPostalCode,
					'BillingState'	 	=> $el->BillingState,
				];
			}
			// $results = array_merge($results, array_map(function($el) {
			// 	return [
			// 		'Id'				=> $el->Id,
			// 		'StoreID'			=> $el->StoreID__c,
			// 		'Name'			 	=> $el->Name,
			// 		'Phone'	 			=> $el->Phone,
			// 		'BillingStreet'	 	=> $el->BillingStreet,
			// 		'BillingCity'	 	=> $el->BillingCity,
			// 		'BillingPostalCode'	=> $el->BillingPostalCode,
			// 		'BillingState'	 	=> $el->BillingState,
			// 	];
			// }, $response->records));
			if (!$response->done && $response->nextRecordsUrl) {
				$url = $response->nextRecordsUrl;
			} else {
				break;
			}
		}
		return $results;
	}

	public function migrate_stores() { // migrate from SF Account object to AP.com
		$accounts = $this->get_all_pharmacy_accounts();

		// echo '<pre>', var_dump($accounts), '</pre>';

		$account_patch_data = [
        	'allOrNone' => false,
        	'records' => [],
		];

		foreach ($accounts as $account) {
			if ($storeID = $this->upsert_store($account)) {
				$account_record = [
					'attributes' => [
						'type' => 'Account',
					],
					'id' => $account['Id'],
					'StoreID__c' => $storeID,
				];
				$account_patch_data['records'][] = $account_record;

				$this->printTime();
				echo 'Store ' . $storeID . '(AP.com) has been migrated for SF Account - ' . $account['Id'] . ' (' . $account['Name'] . ')' . '<br/>';

				if (count($account_patch_data['records']) > 100) {
					$response = $this->do_request('/services/data/v43.0/composite/sobjects', ['post' => true, 'postData' => $account_patch_data, 'patch' => true]);
                	$account_patch_data['records'] = [];
				}
			}
		}

		if (count($account_patch_data['records']) > 0) {
			$response = $this->do_request('/services/data/v43.0/composite/sobjects', ['post' => true, 'postData' => $account_patch_data, 'patch' => true]);
        	$account_patch_data['records'] = [];
		}

		echo 'OK';
	}

	public function upsert_store_with_salesforce($data) {
		$data['StoreID'] = $this->upsert_store($data); // ignore relationship based on StoreID for now
		// $this->update_salesforce_account_with_storeID($data);
	}

	public function mark_pet_store($data) {
		if (!$data['NPI']) return 'Nothing found';
		// if ($data['storeID']) {
		if ($data['NPI']) {
			global $wpdb;

			$store_row = $wpdb->get_row("SELECT * FROM " . ASL_PREFIX.'stores' . " where description like '%" . $data['NPI'] . "%'");
			if (!$store_row) {
				return 'Could not find relevant NPI number';
			}
			$data['StoreID'] = $store_row->id;

		    $formatted = [
		    	'is_disabled'	=> 0,
		    	'marker_id' => 151,
		    	'logo_id' => 4,
		    ];
			$wpdb->update(ASL_PREFIX.'stores', $formatted, ['id' => $data['StoreID']]);
			
			return 'Successfully marked pet on stores with NPI of '. $data['NPI'];
		}
		return 'Nothing marked';
	}
	public function unmark_pet_store($data) {
		if (!$data['NPI']) return 'Nothing found';
		// if ($data['storeID']) {
		if ($data['NPI']) {
			global $wpdb;

			$store_row = $wpdb->get_row("SELECT * FROM " . ASL_PREFIX.'stores' . " where description like '%" . $data['NPI'] . "%'");
			if (!$store_row) {
				return 'Could not find relevant NPI number';
			}
			$data['StoreID'] = $store_row->id;

		    $formatted = [
		    	'marker_id' => null,
		    	'logo_id' => null,
		    ];
			$wpdb->update(ASL_PREFIX.'stores', $formatted, ['id' => $data['StoreID']]);
			
			return 'Successfully unmarked pet on stores with NPI of '. $data['NPI'];
		}
		return 'Nothing unmarked';
	}

	public function upsert_store($data) {
		if (!$data['NPI'] || !$data['PreferredName']) { // ignore updating store locator
			return 0;
		}

	    global $wpdb;

		$store_row = $wpdb->get_row("SELECT * FROM " . ASL_PREFIX.'stores' . " where description like '%" . $data['NPI'] . "%'");
		if ($store_row) {
			$data['StoreID'] = $store_row->id;
		} else {
			$data['StoreID'] = '';
		}

		// $data['StoreID'] = filter_var($data['StoreID'], FILTER_SANITIZE_NUMBER_INT);
		$api_key = 'AIzaSyAYNYAu6Mkub12FRnGWDwYXP2aLff-rTaw';

        $address = $data['ShippingStreet'] . ', ' . $data['ShippingCity'] . ', ' . $data['ShippingState'] . ' ' . $data['ShippingPostalCode']; // Google HQ
        $prepAddr = urlencode($address);
        $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false&key=' . $api_key);
        $output = json_decode($geocode);
        if ($output->status == 'OK') {
	        $latitude = $output->results[0]->geometry->location->lat;
	        $longitude = $output->results[0]->geometry->location->lng;
	    } else {
	    	$latitude = 0;
	    	$longitude = 0;
	    }
	    // var_dump($output);

	    $formatted = [
	    	'title' 		=> stripslashes($data['PreferredName'] != 'null' ? $data['PreferredName'] : ''),
	    	'description' 	=> $data['NPI'],
	    	'street' 		=> $data['ShippingStreet'] != 'null' ? $data['ShippingStreet'] : '',
	    	'city' 			=> $data['ShippingCity'] != 'null' ? $data['ShippingCity'] : '',
	    	'state' 		=> $data['ShippingState'] != 'null' ? $data['ShippingState'] : '',
	    	'postal_code' 	=> $data['ShippingPostalCode'] != 'null' ? $data['ShippingPostalCode'] : '',
	    	'country' 		=> 223, //United States
	    	'lat' 			=> $latitude != 'null' ? $latitude : null,
	    	'lng' 			=> $longitude != 'null' ? $longitude : null,
	    	'phone' 		=> $data['Phone'] != 'null' ? $data['Phone'] : '',
	    	'fax' 			=> '',
	    	'email' 		=> '',
	    	'open_hours' 	=> '{"mon":"0","tue":"0","wed":"0","thu":"0","fri":"0","sat":"0","sun":"0"}',
	    	'is_disabled'	=> 0,
	    ];

	    $storeID = '';
	    if (isset($data['StoreID']) && (!!$data['StoreID'])) {
		    if ($wpdb->update(ASL_PREFIX.'stores', $formatted, ['id' => $data['StoreID']]) !== FALSE) {
		    	$storeID = $data['StoreID'];
		    }
	    } else {
		    if ($wpdb->insert(ASL_PREFIX.'stores', $formatted)) {
		    	$storeID = $wpdb->insert_id;
		    }
	    }
	    return $storeID;
	}
	

	/*public function upsert_store_old($data) { // old method from trigger - PharmacyStore object
		$api_key = 'AIzaSyAYNYAu6Mkub12FRnGWDwYXP2aLff-rTaw';

        $address = $data['Street'] . ', ' . $data['City'] . ', ' . $data['State'] . ' ' . $data['PostalCode']; // Google HQ
        $prepAddr = urlencode($address);
        $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false&key=' . $api_key);
        $output = json_decode($geocode);
        if ($output->status == 'OK') {
	        $latitude = $output->results[0]->geometry->location->lat;
	        $longitude = $output->results[0]->geometry->location->lng;
	    } else {
	    	$latitude = $data['Lat'];
	    	$longitude = $data['Long'];
	    }
	    // var_dump($output);

	    global $wpdb;
	    $formatted = [
	    	'title' 		=> $data['Title'] != 'null' ? $data['Title'] : '',
	    	'description' 	=> $data['Description'] != 'null' ? $data['Description'] : '',
	    	'street' 		=> $data['Street'] != 'null' ? $data['Street'] : '',
	    	'city' 			=> $data['City'] != 'null' ? $data['City'] : '',
	    	'state' 		=> $data['State'] != 'null' ? $data['State'] : '',
	    	'postal_code' 	=> $data['PostalCode'] != 'null' ? $data['PostalCode'] : '',
	    	'country' 		=> 223, //United States
	    	'lat' 			=> $latitude != 'null' ? $latitude : null,
	    	'lng' 			=> $longitude != 'null' ? $longitude : null,
	    	'phone' 		=> $data['Phone'] != 'null' ? $data['Phone'] : '',
	    	'fax' 			=> $data['Fax'] != 'null' ? $data['Fax'] : '',
	    	'email' 		=> $data['Email'] != 'null' ? $data['Email'] : '',
	    	'open_hours' 	=> '{"mon":"0","tue":"0","wed":"0","thu":"0","fri":"0","sat":"0","sun":"0"}',
	    ];

	    if (isset($data['StoreID']) && !$data['StoreID']) {
		    if ($wpdb->update(ASL_PREFIX.'stores', $formatted, ['id' => $data['StoreID']])) {
		    	$storeID = $data['StoreID'];
		    	return $storeID . '|' . $latitude . '|' . $longitude;
		    } else {
		    	return '';
		    }
	    } else {
		    if ($wpdb->insert(ASL_PREFIX.'stores', $formatted)) {
		    	$storeID = $wpdb->insert_id;
		    	return $storeID . '|' . $latitude . '|' . $longitude;
		    } else {
		    	return '';
		    }
	    }
	}*/

	public function update_salesforce_account_with_storeID($data) {
		$account_patch_data = [
        	'allOrNone' => false,
        	'records' => [
        		[
					'attributes' => [
						'type' => 'Account',
					],
					'id' => $data['Id'],
					'StoreID__c' => $data['StoreID'],
				]
        	],
		];

		return $response = $this->do_request('/services/data/v43.0/composite/sobjects', ['post' => true, 'postData' => $account_patch_data, 'patch' => true]);
	}

	public function delete_store($data) {
		if (!$data['NPI']) return 'Nothing found';
		// if ($data['storeID']) {
		if ($data['NPI']) {
			global $wpdb;

			$store_row = $wpdb->get_row("SELECT * FROM " . ASL_PREFIX.'stores' . " where description like '%" . $data['NPI'] . "%'");
			if (!$store_row) {
				return 'Could not find relevant NPI number';
			}
			$data['StoreID'] = $store_row->id;

		    $formatted = [
		    	'is_disabled'	=> 1,
		    ];
			$wpdb->update(ASL_PREFIX.'stores', $formatted, ['id' => $data['StoreID']]);
			// instead of delete, we provide enable/disable
			// $wpdb->query("delete from " . ASL_PREFIX."stores" . " where description like '%" . $data['NPI'] . "%'");

			// $data['StoreID'] = 0;
			// $this->update_salesforce_account_with_storeID($data);

			// return 'Successfully removed stores with IDs of '. $data['storeID'];
			return 'Successfully removed stores with NPI of '. $data['NPI'];
		}
		return 'Nothing deleted';
	}

	public function confirm_payment($data) {
		if (!$data['ID']) return 'Nothing found';
		if ($data['ID']) {
			// echo $data['ID'];
			$args = explode('-', $data['ID']);

			if (count($args) != 2) return 'Invalid Order ID';

			$order_id = $args[1];
			$order = wc_get_order($order_id);

			if (!$order) return 'Order is not available';

			if ($order->get_payment_method() != 'cheque') return 'This is not an order with ACH Payment Option.';

			if ($order->get_status() != 'on-hold') return 'This order has been modified before';

			$result = $order->set_status( 'wc-processing', 'Manually confirmed by Salesforce', true );
			// var_dump($result);
			$order->save();

			$updateData = [
				'SubmissionStatusTrigger__c' => '1'
			];
        	$response = $this->do_request('/services/data/v20.0/sobjects/Ananda_Invoice__c/InvoiceNumber__c/' . (string)$data['ID'], ['post' => true, 'postData' => $updateData, 'patch' => true]);
        	if (is_array($response) && $response[0]->errorCode) {
        		$this->authenticate();
        		$response = $this->do_request('/services/data/v20.0/sobjects/Ananda_Invoice__c/InvoiceNumber__c/' . (string)$data['ID'], ['post' => true, 'postData' => $updateData, 'patch' => true]);
        	}

			return 'Successfully confirmed payment for ' . $data['ID'];
		}
		return 'Nothing confirmed';
	}

	public function create_invoice_from_quote($quote_id) {

        $quote = $this->do_request('/services/data/v43.0/sobjects/Quote/' . $quote_id);
        $quote_lines_response = $this->do_request('/services/data/v43.0/sobjects/Quote/' . $quote_id . '/QuoteLineItems');
        if ($quote_lines_response->done) {


        	$account = $this->do_request('/services/data/v43.0/sobjects/Account/' . $quote->AccountId);
        	$contact = $this->do_request('/services/data/v43.0/sobjects/Account/' . $quote->AccountId . '/Contact');

        	$quote_lines = $quote_lines_response->records;

        	echo '<pre>', var_dump($account), '</pre>';
        	echo '<pre>', var_dump($contact), '</pre>';
        	echo '<pre>', var_dump($quote), '</pre>';

        	$xml = '';

        	foreach ($quote_lines as $line) {
        		echo '<pre>', var_dump($line), '</pre>';
        	}

        }
	}

	public function update_account_test() {
		$address = '209 US-54 El Dorado Springs, MO 64744';

		$api_key = 'AIzaSyAYNYAu6Mkub12FRnGWDwYXP2aLff-rTaw';

        $prepAddr = urlencode($address);
        $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false&key=' . $api_key);
        $output = json_decode($geocode);

		echo '<pre>', var_dump($output), '</pre>';

	    exit('');
	}

	public function drop_invalid_accounts() {

		$results = [];

		// $url = '/services/data/v43.0/sobjects/Account/0016A00000ZzQK2QAN';
		$url = '/services/data/v43.0/query/?q=' . urlencode('SELECT Id, Name, NPI_Number__c, Phone, StoreID__c, Unique_Import_Id__c, CreatedById FROM Account where Type=\'Customer\' and Subtype__c=\'Pharmacy\' and NPI_Number__c=\'\'');  //and CreatedById=\'0056A000001hYBSQA2\'');

		$delete_ids = [];

		while ($response = $this->do_request($url)) {
			foreach ($response->records as $el) {
				$results[] = [
					'Id'				=> $el->Id,
					'Name'			 	=> $el->Name,
					'NPI_Number__c'	 	=> $el->NPI_Number__c,
					'Unique_Import_Id__c' => $el->Unique_Import_Id__c,
					'CreatedById' => $el->CreatedById,
				];
				$delete_ids[] = $el->Id;
			}
			// $results = array_merge($results, array_map(function($el) {
			// 	return [
			// 		'Id'				=> $el->Id,
			// 		'StoreID'			=> $el->StoreID__c,
			// 		'Name'			 	=> $el->Name,
			// 		'Phone'	 			=> $el->Phone,
			// 		'BillingStreet'	 	=> $el->BillingStreet,
			// 		'BillingCity'	 	=> $el->BillingCity,
			// 		'BillingPostalCode'	=> $el->BillingPostalCode,
			// 		'BillingState'	 	=> $el->BillingState,
			// 	];
			// }, $response->records));
			if (!$response->done && $response->nextRecordsUrl) {
				$url = $response->nextRecordsUrl;
			} else {
				break;
			}
		}


		// $response = $this->do_request('/services/data/v43.0/composite/sobjects?ids=' . implode(',', $delete_ids), ['delete' => true]);
		// echo '<pre>', var_dump($response), '</pre>';

		echo '<pre>', var_dump($results), '</pre>';
		exit('');
	}

	public function update_accounts() {

		$api_key = 'AIzaSyAYNYAu6Mkub12FRnGWDwYXP2aLff-rTaw';

		$accounts = $this->get_all_accounts(['Id', 'Name', 'NPI_Number__c', 'Unique_Import_Id__c', 'BillingStreet', 'BillingCity', 'BillingState', 'BillingPostalCode', 'BillingCountry', 'ShippingStreet', 'ShippingCity', 'ShippingState', 'ShippingPostalCode']);

		// echo '<pre>', var_dump($accounts), '</pre>';
		// exit('');

		$account_patch_data = [
        	'allOrNone' => false,
        	'records' => [],
		];

		foreach ($accounts as $account) {

        	// $address = $account['BillingStreet'] . ', ' . $account['BillingCity'] . ', ' . $account['BillingState'] . ' ' . $account['BillingPostalCode']; // Google HQ
        	$address = $account['ShippingStreet'] . ', ' . $account['ShippingCity'] . ', ' . $account['ShippingState'] . ' ' . $account['ShippingPostalCode']; // Google HQ
			if (!$account['ShippingStreet'] && !$account['ShippingCity'] && !$account['ShippingState'] && !$account['ShippingPostalCode']) {
        		$address = $account['BillingStreet'] . ', ' . $account['BillingCity'] . ', ' . $account['BillingState'] . ' ' . $account['BillingPostalCode']; // Google HQ
			}

	        $prepAddr = urlencode($address);
	        $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false&key=' . $api_key);
	        $output = json_decode($geocode);

			$this->printTime();
			// var_dump($account);
        	if ($output->status == 'OK') {
				$address = $output->results[0]->formatted_address;
				$address_parts = explode(', ', $address);
				$state_parts = explode(' ', $address_parts[2]);

				$account_record = [
					'attributes' => [
						'type' => 'Account',
					],
					'id' => $account['Id'],
					'ShippingStreet' => $address_parts[0],
					'ShippingCity' => $address_parts[1],
					'ShippingState' => $state_parts[0],
					'ShippingPostalCode' => $state_parts[1],
				];
				$account_patch_data['records'][] = $account_record;

				echo 'Account ' . $account['Name'] . '('. $account['Id'] . ') has been updated with new address - ' . $address . '<br/>';
			} else {
				echo 'Account ' . $account['Name'] . '('. $account['Id'] . ') is invalid address - ' . $address . '<br/>';
			}

			if (count($account_patch_data['records']) > 100) {
				$response = $this->do_request('/services/data/v43.0/composite/sobjects', ['post' => true, 'postData' => $account_patch_data, 'patch' => true]);
            	$account_patch_data['records'] = [];
			}
		}

		if (count($account_patch_data['records']) > 0) {
			$response = $this->do_request('/services/data/v43.0/composite/sobjects', ['post' => true, 'postData' => $account_patch_data, 'patch' => true]);
        	$account_patch_data['records'] = [];
		}
	}

	public function get_npi_organization_name($account_number = '') {

		if (!$account_number) return '';

        $ch = curl_init('https://npiregistry.cms.hhs.gov/api/?number=' . $account_number);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $npi_response = curl_exec($ch);
        curl_close($ch);
        $npi_response = json_decode($npi_response);

        $contact_name = '';
        if (isset($npi_response->result_count) && $npi_response->result_count > 0) {
        	$org = $npi_response->results[0];
        	if (count($org->other_names) > 0 && $org->other_names[0]->organization_name) {
        		$contact_name = $org->other_names[0]->organization_name;
        	} else {
        		$contact_name = $org->basic->organization_name;
        	}
        }

        return $contact_name;
	}

	public function update_salesforce_accounts_name() {

		$accounts = [];

		$url = '/services/data/v43.0/query/?q=' . urlencode('SELECT Id, Name, NPI_Number__c FROM Account where NPI_Number__c!=\'\'');

		while ($response = $this->do_request($url)) {
			foreach ($response->records as $el) {
				$accounts[] = [
					'Id'				=> $el->Id,
					'Name'			 	=> $el->Name,
					'NPI_Number__c'	 	=> $el->NPI_Number__c,
				];
			}
			if (!$response->done && $response->nextRecordsUrl) {
				$url = $response->nextRecordsUrl;
			} else {
				break;
			}
		}

		// echo '<pre>', var_dump($accounts), '</pre>';

		$account_patch_data = [
        	'allOrNone' => false,
        	'records' => [],
		];

		foreach ($accounts as $account) {
			if ($contact_name = $this->get_npi_organization_name($account['NPI_Number__c'])) {
				$account_record = [
					'attributes' => [
						'type' => 'Account',
					],
					'id' => $account['Id'],
					'Name' => $contact_name,
				];
				$account_patch_data['records'][] = $account_record;

				$this->printTime();
				echo 'Salesforce Account with NPI - ' . $account['NPI_Number__c'] . ' has changed name from "' . $account['Name'] . '" to "' . $contact_name . '"<br/>';

				if (count($account_patch_data['records']) > 100) {
					$response = $this->do_request('/services/data/v43.0/composite/sobjects', ['post' => true, 'postData' => $account_patch_data, 'patch' => true]);
                	$account_patch_data['records'] = [];
				}
			}
		}

		if (count($account_patch_data['records']) > 0) {
			$response = $this->do_request('/services/data/v43.0/composite/sobjects', ['post' => true, 'postData' => $account_patch_data, 'patch' => true]);
        	$account_patch_data['records'] = [];
		}
	}

	public function reset_salesforce_store_id() {

		$accounts = [];

		$url = '/services/data/v43.0/query/?q=' . urlencode('SELECT Id, Name, NPI_Number__c, StoreID__c FROM Account');

		while ($response = $this->do_request($url)) {
			foreach ($response->records as $el) {
				$accounts[] = [
					'Id'				=> $el->Id,
					'Name'			 	=> $el->Name,
					'NPI_Number__c'	 	=> $el->NPI_Number__c,
					'StoreID__c'	 	=> $el->StoreID__c,
				];
			}
			if (!$response->done && $response->nextRecordsUrl) {
				$url = $response->nextRecordsUrl;
			} else {
				break;
			}
		}

		// echo '<pre>', var_dump($accounts), '</pre>';

		$account_patch_data = [
        	'allOrNone' => false,
        	'records' => [],
		];

		foreach ($accounts as $account) {
			$account_record = [
				'attributes' => [
					'type' => 'Account',
				],
				'id' => $account['Id'],
				'StoreID__c' => '',
			];
			$account_patch_data['records'][] = $account_record;

			$this->printTime();
			// echo 'Salesforce Account with NPI - ' . $account['NPI_Number__c'] . ' has changed name from "' . $account['Name'] . '" to "' . $contact_name . '"<br/>';

			if (count($account_patch_data['records']) > 100) {
				$response = $this->do_request('/services/data/v43.0/composite/sobjects', ['post' => true, 'postData' => $account_patch_data, 'patch' => true]);
            	$account_patch_data['records'] = [];
			}
		}

		if (count($account_patch_data['records']) > 0) {
			$response = $this->do_request('/services/data/v43.0/composite/sobjects', ['post' => true, 'postData' => $account_patch_data, 'patch' => true]);
        	$account_patch_data['records'] = [];
		}
	}

	public function update_xero_contacts_name() {

        $contact_manager = new WC_XR_Contact_Manager(new WC_XR_Settings());

        $page_no = 1;

        do {
            $response = $contact_manager->get_all_contacts_with_npi($page_no);
            $contacts_wrapper = $response->Contacts;
            $contacts = $contacts_wrapper->Contact ?: [];

            $this->printTime();
            echo ('___________ current page________________' . $page_no . '______' . count($contacts_wrapper) . '_______' . count($contacts) . '<br/>');

            if (count($contacts) > 0) {
                foreach ($contacts as $contact) {

			        $contact_name = $this->get_npi_organization_name((string)$contact->AccountNumber);

			        if (!$contact_name) continue;

					$patch = new WC_XR_Contact();
					$patch->set_id( (string)$contact->ContactID );
					$patch->set_name( $contact_name );

					$contact_request_update = new WC_XR_Request_Update_Contact( new WC_XR_Settings(), (string)$contact->ContactID, $patch );
					$contact_request_update->do_request();

					$this->printTime();
					echo 'Xero Contact with NPI - ' . $account_number . ' has changed name from "' . (string)$contact->Name . '" to "' . $contact_name . '"<br/>';
                }
            }

            $page_no++;
        } while(count($contacts) > 0);

	}

	public function check_missing_xero_invoices() {
		$args = array(
		    'limit' => -1,
		);
		$orders = wc_get_orders($args);

		echo '<table border=1><thead><tr><th>Order ID</th><th>Method</th><th>Created</th><th>Status</th><th>Xero ID</th><th>Customer</th><th>Billing</th><th>Shipping</th></tr></thead><tbody>';

		foreach($orders as $order) {
			echo '<tr>';
			echo '<td>'. $order->get_id() .'</td>';
			echo '<td>'. $order->get_payment_method() .'</td>';
			echo '<td>'. date('Y-m-d H:i:s', strtotime($order->get_date_created())) .'</td>';
			echo '<td>'. $order->get_status() .'</td>';
			echo '<td>'. $order->get_meta('_xero_invoice_id') .'</td>';
			if (method_exists($order, 'get_customer_id')) {
				$customer = new WC_Customer($order->get_customer_id());
				echo '<td>'. $customer->get_first_name() . ' ' . $customer->get_last_name() . '(' . $customer->get_username() . ') - ' . $customer->get_email() .'</td>';
				echo '<td>'. $customer->get_billing_company() .'</td>';
				echo '<td>'. $customer->get_shipping_company() .'</td>';
			} else {
				echo '<td colspan="3"></td>';
			}
			echo '</tr>';
		}

		echo '</tbody></table>';
	}

	public function update_tracking_number($order_id, $tracking_number) {
		$updateData = [
			'Tracking_Number__c' => $tracking_number
		];

		$order = wc_get_order( $order_id );

        $invoice_manager = new WC_XR_Invoice_Manager(new WC_XR_Settings());
        $invoice = $invoice_manager->get_invoice_by_order($order);
        $invoice_number = $invoice->get_invoice_number();

    	$response = $this->do_request('/services/data/v20.0/sobjects/Ananda_Invoice__c/InvoiceNumber__c/' . $invoice_number, ['post' => true, 'postData' => $updateData, 'patch' => true]);
    	if (is_array($response) && $response[0]->errorCode) {
    		$this->authenticate();
    		$response = $this->do_request('/services/data/v20.0/sobjects/Ananda_Invoice__c/InvoiceNumber__c/' . $invoice_number, ['post' => true, 'postData' => $updateData, 'patch' => true]);
    	}
	}

	public function get_shipstation_shipments($year = false, $orderNumber = '', $pageSize = 100, $page = 1) {
		$ch = curl_init();

		$params = [
			'page' => $page,
			'pageSize' => $pageSize,
		];

		if ($year) {
			$params['createDateStart'] = $year . '-01-01 00:00:00';
			// $params['createDateEnd'] = ($year + 1) . '-01-01 00:00:00';
		}

		if ($orderNumber) {
			$params['orderNumber'] = $orderNumber;
		}

		$target = 'https://ssapi.shipstation.com/shipments?' . http_build_query($params);

		echo '<pre>', var_dump($target), '</pre>';

		curl_setopt($ch, CURLOPT_URL, $target);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);

		$apiKey = '0892ed2f35de44569caf6cbf20d833d4';
		$apiSecret = '8bd4a247d1d545c790ce19788da0cdc8';

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		  "Authorization: Basic " . base64_encode($apiKey . ':' . $apiSecret)
		));

		$response = curl_exec($ch);
		curl_close($ch);

		return json_decode($response);
	}

	public function migrate_trackings($year = false) {
		// if (!$year) $year = date('Y'); // set current year

		$salesforce_invoices = $this->get_all_salesforce_invoices();

		$this->printTime();
		echo ' found ' . count($salesforce_invoices) . ' invoices from salesforce api <br/>';

		$page = 1;

        $invoices_patch_data = [
        	'allOrNone' => false,
        	'records' => [],
        ];

        $stores = [
        	'366983' => [
        		'shipments' => [],
        		'name' => 'Ananda Professional',
        		'candidates' => ['AE', 'WP', 'CN'],
        	],
        	'362016' => [
        		'shipments' => [],
        		'name' => 'Kentucky Fulfillment',
        		'candidates' => ['INV', 'AE', 'WP', 'CN'],
        	],
        	'341365' => [
        		'shipments' => [],
        		'name' => 'Ananda Hemp',
        		'candidates' => ['WS'],
        	],
        	'338547' => [
        		'shipments' => [],
        		'name' => 'California Fulfillment',
        		'candidates' => ['INV', 'WS'],
        	],
        ];

		$all_shipments = [];

		do {
			$response = $this->get_shipstation_shipments($year, '', 500, $page++);
			if ($response->shipments) {
				foreach ($response->shipments as $shipment) {
					$storeId = $shipment->advancedOptions ? $shipment->advancedOptions->storeId : null;
					if (!$storeId) continue;
					$stores[$storeId]['shipments'][] = [
						'orderId' => $shipment->orderId,
						'orderKey' => $shipment->orderKey,
						'orderNumber' => $shipment->orderNumber,
						'orderNumberFiltered' => preg_replace('/[^0-9]/', '', $shipment->orderNumber),
						'trackingNumber' => $shipment->trackingNumber,
						'createDate' => $shipment->createDate,
						'shipDate' => $shipment->shipDate,
						'shipToName' => $shipment->shipTo->name,
						'storeId' => $storeId,
					];
				}
			}
			// if ($page > 1) break;
		} while(isset($response->page, $response->pages) && $response->page < $response->pages);

		$this->printTime();
		echo ' shipment search is done <br/>';
		// echo '<pre>', var_dump($stores), '</pre>';

		foreach ($salesforce_invoices as $invoice) {
			$invoice_number = $invoice['InvoiceNumber__c'];
			$filteredInvoiceNumber = preg_replace('/[^0-9]/', '', $invoice_number);

			if (!$invoice_number || !$filteredInvoiceNumber) continue;

			$args = explode('-', strtoupper($invoice_number));

			if (count($args) < 2) continue;

			$prefix = $args[0];
			$found_storeId = null;
			$found_shipment_key = null;
			foreach ($stores as $storeId => $store) {
				if (in_array($prefix, $store['candidates'])) {
					$tmp_shipment_key = array_search(preg_replace('/[^0-9]/', '', $invoice_number), array_column($store['shipments'], 'orderNumberFiltered'));
					if ($tmp_shipment_key !== false) {
						$found_storeId = $storeId;
						$found_shipment_key = $tmp_shipment_key;
						break;
					}
				}
			}

			if ($found_storeId && $found_shipment_key) {

				$trackingNumber = $stores[$found_storeId]['shipments'][$found_shipment_key]['trackingNumber'];
				$orderNumber = $stores[$found_storeId]['shipments'][$found_shipment_key]['orderNumber'];

			    echo 'trackingNumber - ' . $trackingNumber . ' - found from salesforce invoices list ' . $invoice_number . ' ----- [' . $found_shipment_key . '] in store ' . $stores[$found_storeId]['name'] . ' with ' . $orderNumber . ' <br/> ';

        		$invoice_record = [
		            'attributes' => [
		                'type' => 'Ananda_Invoice__c',
		            ],
        			'id' => $invoice['Id'],
        			'Tracking_Number__c' => $trackingNumber
        		];

        		$invoices_patch_data['records'][] = $invoice_record;

                if (count($invoices_patch_data['records']) > 150) {
					$this->printTime();
					echo ' _________ doing patch updates for updated invoices ( ' . count($invoices_patch_data['records']) . ')' . '<br/>';
					// echo '<pre>', var_dump($invoices_patch_data), '</pre>';
					$sf_response = $this->do_request('/services/data/v43.0/composite/sobjects', ['post' => true, 'postData' => $invoices_patch_data, 'patch' => true]);
					// var_dump($sf_response);
                	$invoices_patch_data['records'] = [];
			    }
			} else {
			    // echo 'not found from salesforce invoices list ' . $invoice_number . ' <br/> ';
			}
		}

		if (count($invoices_patch_data['records']) > 0) {
			$this->printTime();
			echo ' _________ doing patch updates for updated invoices ( ' . count($invoices_patch_data['records']) . ')' . '<br/>';
			// echo '<pre>', var_dump($invoices_patch_data), '</pre>';
			$sf_response = $this->do_request('/services/data/v43.0/composite/sobjects', ['post' => true, 'postData' => $invoices_patch_data, 'patch' => true]);
			// var_dump($sf_response);
        	$invoices_patch_data['records'] = [];
	    }
	}
	public function migrate_branding() {
		$accounts = $this->get_all_accounts(['Id', 'Name', 'Brand__c']);
		$salesforce_invoices = $this->get_all_salesforce_invoices('', false, ['Id', 'Account_ID__c', 'BrandingTheme__c', 'InvoiceNumber__c']);

        $patch_data = [
        	'allOrNone' => false,
        	'records' => [],
        ];

        foreach($accounts as $account) {
        	$found = false;
        	foreach ($salesforce_invoices as $invoice) {
        		if ($invoice['Account_ID__c'] == $account['Id'] && $invoice['BrandingTheme__c'] !== $account['Brand__c']) {
        			$found = $invoice['BrandingTheme__c'] ?: '';
        			echo 'Account with Brand "'. $account['Brand__c'] .'" named "'. $account['Name'] .'" will be updated with "'. $found .'" ('.$invoice['InvoiceNumber__c'].'). <br/>';
        			break;
        		}
        	}
        	if ($found === false) continue;
    		$account_record = [
	            'attributes' => [
	                'type' => 'Account',
	            ],
				'id' => $account['Id'],
				'Brand__c' => $found,
    		];
    		$patch_data['records'][] = $account_record;
            if (count($patch_data['records']) > 99) {
				$this->printTime();
				echo ' _________ doing patch updates for accounts ( ' . count($patch_data['records']) . ')' . '<br/>';
				// echo '<pre>', var_dump($patch_data), '</pre>';
				$response = $this->do_request('/services/data/v44.0/composite/sobjects', ['post' => true, 'postData' => $patch_data, 'patch' => true]);
				// var_dump($response);
            	$patch_data['records'] = [];
		    }
        }
        if (count($patch_data['records']) > 0) {
			$this->printTime();
			echo ' _________ doing patch updates for accounts ( ' . count($patch_data['records']) . ')' . '<br/>';
			// echo '<pre>', var_dump($patch_data), '</pre>';
			$response = $this->do_request('/services/data/v43.0/composite/sobjects', ['post' => true, 'postData' => $patch_data, 'patch' => true]);
			// var_dump($response);
        	$patch_data['records'] = [];
	    }
	}

	public function check_fpn_orders() {
		$args = array(
		    'limit' => -1,
		);
		$orders = wc_get_orders($args);

		echo '<table border=1><thead><tr><th>Order ID</th><th>Created</th><th>Status</th><th>Xero ID</th><th>Customer</th><th>Billing</th><th>Shipping</th><th>Total</th></tr></thead><tbody>';

		foreach($orders as $order) {
			if ($order->get_status() == 'cancelled') continue;
			// $shipstation_note = get_post_meta($order->get_id(), 'shipstation_note', true);
			// if (!$shipstation_note) continue;
			$customer = null;
			if (method_exists($order, 'get_customer_id')) {
				if (!is_user_has_role('fpn', $order->get_customer_id())) continue;
				$customer = new WC_Customer($order->get_customer_id());
			} else {
				continue;
			}

			echo '<tr' . ($order->get_status()=='on-hold' ? ' style="color: red;"' : '') . '>';
			echo '<td>'. $order->get_id() .'</td>';
			echo '<td>'. date('Y-m-d H:i:s', strtotime($order->get_date_created())) .'</td>';
			echo '<td>'. $order->get_status() .'</td>';
			echo '<td>'. $order->get_meta('_xero_invoice_id') .'</td>';

			echo '<td>'. $customer->get_first_name() . ' ' . $customer->get_last_name() . '(' . $customer->get_username() . ' - ' . $customer->get_id() . ') - ' . $customer->get_email() .'</td>';
			echo '<td>'. $customer->get_billing_company() .' - '. $customer->get_billing_city() .', '. $customer->get_billing_state() .'</td>';
			echo '<td>'. $customer->get_shipping_company() .' - '. $customer->get_shipping_city() .', '. $customer->get_shipping_state() .'</td>';

			echo '<td>'. $order->get_formatted_order_total() . '</td>';
			echo '</tr>';
		}

		echo '</tbody></table>';
	}

	public function migrate_cancelled_orders() {

		$args = array(
		    'limit' => -1,
    		'status' => 'cancelled',
		);
		$orders = wc_get_orders($args);

        $invoice_manager = new WC_XR_Invoice_Manager(new WC_XR_Settings());

		foreach($orders as $order) {
			$invoice_manager->void_invoice($order->get_id(), true);
		}

	}
}
