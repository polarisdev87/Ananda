<?php

class SalesforceSDK {

	/* dev */
	
	// private $yourInstance = 'na40';
	// private $client_id = '3MVG9i1HRpGLXp.rr5NrL7AxCC_g5Klf.h.zxML5Lw1dRc_ndae5fLFusRS0TlcjH3XWRL3hfclENG4CkVsAm';
	// private $client_secret = '4036959497197072492';
	// private $username = 'lance@shubout.com';
	// private $password = 'Developer@2018UTydBAE3P1dLSuokm6LJgmi6';
	// private $contact_field_id = 'Unique_Import_Id__c';
 //    private $base_url = "https://na40.salesforce.com";
	

	/* production */
	
	private $yourInstance = 'na40';
	private $client_id = '3MVG9CEn_O3jvv0yFWfRjWvZ00r7kMm3yAL3JcQiAzgXDz3NzelQl0jEfe3GTrAsUQuBOJc9hxjeLR1DHOoj8';
	private $client_secret = '1892914933810725936';
	private $username = 'lance032017@gmail.com';
	private $password = 'Developer@2017bNcP9rufUrtVDLybXRGXLC4G';
	private $contact_field_id = 'Unique_Import_Id__c';
    private $base_url = "https://anandahemp.my.salesforce.com";
	

	/* sandbox - partial */

	// private $yourInstance = 'na40';
	// private $client_id = '3MVG9CEn_O3jvv0yFWfRjWvZ00r7kMm3yAL3JcQiAzgXDz3NzelQl0jEfe3GTrAsUQuBOJc9hxjeLR1DHOoj8';
	// private $client_secret = '1892914933810725936';
	// private $username = 'lance032017@gmail.com.partial';
	// private $password = 'Developer@2018tfYBERgFz8YwOHhJAGLRqoJc';
	// private $contact_field_id = 'Unique_Import_Id__c';
 //    private $base_url = "https://anandahemp--partial.lightning.force.com/services/data/";
	


    // private $base_url = "https://anandahemp.my.salesforce.com/services/data/";
    private $token_url = "https://login.salesforce.com/services/oauth2/token";
    private $sandbox_token_url = "https://test.salesforce.com/services/oauth2/token";

    private $auth = null;

    private $debug = false;

	public function __construct( $sandbox = false, $debug = false ) {
		if ($sandbox) {
			$this->yourInstance = 'CS66';
			$this->username = 'lance032017@gmail.com.partial';
			$this->password = 'Developer@2018FDTtGc2DMw2NndLsxBVACOhY';
			$this->base_url = 'https://anandahemp--partial.cs66.my.salesforce.com';
			$this->token_url = 'https://test.salesforce.com/services/oauth2/token';
		}

		if ($debug) {
			$this->debug = $debug;
		}
		// $this->set_base_url();
		
		$this->authenticate();
	}

	public function set_base_url() {
		$this->base_url = 'https://' . $this->yourInstance . '.salesforce.com';
	}

	public function get_base_url() {
		return $this->base_url;
	}


	public function do_request($url, $options = []) {

		$is_auth = $options['is_auth'] ?: false;
	    $token = $this->get_token();
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

	    $response = $this->do_request($this->token_url, ['is_auth' => true, 'post' => true, 'postData' => http_build_query([
	            'grant_type' => 'password',
	            'client_id' => $this->client_id,
	            'client_secret' => $this->client_secret,
	            'username' => $this->username,
	            'password' => $this->password,
	        ])
	    ]);

	    $this->auth = $response;

	}

	public function get_auth() {
		return $this->auth;
	}

	public function get_token() {
		return isset($this->auth->access_token) ? $this->auth->access_token : '';
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

                // foreach($invoices as $invoice) {
                // 	$all_invoices[] = $invoice;
                //     // var_dump($invoice);
                // }
                // var_dump ($invoices_wrapper);
            }

            $page_no++;
        } while(count($invoices) > 0);

        // return $all_invoices;
        return $page_no;
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

	public function create_account_from_xero_contact_id($xero_contact_id = '') {
		if (!$xero_contact_id) return null;

        $response = $this->get_contact_by_id($xero_contact_id);

        if ($this->debug) echo '<pre>', var_dump($response), '</pre>';

        if (!$response) return null;

        $contact = $response->Contacts->Contact[0];

        if ($this->debug) echo '<pre>', var_dump($contact), '</pre>';

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
			'ShippingState' => (string)$address->City,
			'ShippingCity' => (string)$address->Region,
			'ShippingPostalCode' => (string)$address->PostalCode,
			'ShippingCountry' => (string)$address->Country,
			'NPI_Number__c' => (string)$contact->AccountNumber,
			'Brand__c' => 'Ananda Professional',
        ];

        if ($this->debug) echo '<pre>', var_dump($data), '</pre>';

        $response = $this->do_request('/services/data/v43.0/sobjects/Account/', ['post' => true, 'postData' => $data]);

        if ($this->debug) echo '<pre>', var_dump($response), '</pre>';

        if ($response->success == true) {
        	return [
        		'Id' => (string)$response->id,
				'Name' => (string)$contact->Name,
        		'Unique_Import_Id__c' => $xero_contact_id,
        		'NPI_Number__c' => (string)$contact->AccountNumber,
        	];
        }

        if ($this->debug) var_dump($data['NPI_Number__c']);

        if (!$data['NPI_Number__c']) return null;

        unset($data['NPI_Number__c']);

        if ($this->debug) echo '<pre>', var_dump($data), '</pre>';
        if ($this->debug) var_dump((string)$contact->AccountNumber);

        $response = $this->do_request('/services/data/v20.0/sobjects/Account/NPI_Number__c/' . (string)$contact->AccountNumber, ['post' => true, 'postData' => $data, 'patch' => true]);

        if ($this->debug) echo '<pre>', var_dump($response), '</pre>';

        if ($response->success == true) {
        	return [
        		'Id' => (string)$response->id,
				'Name' => (string)$contact->Name,
        		'Unique_Import_Id__c' => $xero_contact_id,
        		'NPI_Number__c' => (string)$contact->AccountNumber,
        	];
        } else {
        	$response = $this->do_request('/services/data/v20.0/sobjects/Account/NPI_Number__c/' . (string)$contact->AccountNumber);
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

	public function get_all_salesforce_invoices($startsWith = '') {

		$results = [];

		if ($startsWith) {
			$url = '/services/data/v43.0/query/?q=' . urlencode('SELECT Id, InvoiceNumber__c, Account_ID__c, Xero_Invoice_ID__c, UpdatedDateUTC__c FROM Ananda_Invoice__c WHERE InvoiceNumber__c LIKE \'' . $startsWith . '%\'');
		} else {
			$url = '/services/data/v43.0/query/?q=' . urlencode('SELECT Id, InvoiceNumber__c, Account_ID__c, Xero_Invoice_ID__c, UpdatedDateUTC__c FROM Ananda_Invoice__c');
		}

		while ($response = $this->do_request($url)) {
			$results = array_merge($results, array_map(function($el) {
				return [
					'Id'				 => $el->Id,
					'Account_ID__c'		 => $el->Account_ID__c,
					'Xero_Invoice_ID__c' => $el->Xero_Invoice_ID__c,
					'InvoiceNumber__c'	 => $el->InvoiceNumber__c,
					'UpdatedDateUTC__c'	 => $el->UpdatedDateUTC__c,
				];
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
		$salesforce_invoices = $this->get_all_salesforce_invoices($startsWith);
        $invoices_patch_data = [
        	'allOrNone' => false,
        	'records' => [],
        ];

        $page_no = 1;

        $account_ref_no = 1;
        $invoice_ref_no = 1;
        $line_item_ref_no = 1;
        $tracking_category_ref_no = 1;

        // $accounts_from_ext = [];

        do {
        	sleep(1);
            $response = $invoice_manager->get_all_invoices($startsWith, $page_no, $year);
            $invoices_wrapper = $response->Invoices;
            $invoices = $invoices_wrapper->Invoice ?: [];

	        $this->printTime();
            echo ('___________ current page________________' . $page_no . '______' . count($invoices_wrapper) . '_______' . count($invoices) . '<br/>');

            // echo ' _______________ current page________________' . $page_no . '______' . count($invoices_wrapper) . '<br/>';

            if (count($invoices) > 0) {
                foreach ($invoices as $invoice) {

                	// var_dump($invoice);

                	$ext_id = (string)$invoice->Contact->ContactID;
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
                		) {
                			$account = $this->create_account_from_xero_contact_id($ext_id);
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

                	$branding_theme = '';
                	if ((string)$invoice->BrandingThemeID == 'c862879a-49c7-40a8-ba05-0dd14a18813f') {
                		$branding_theme = 'Ananda Hemp';
                	} else if ((string)$invoice->BrandingThemeID == 'd415c810-ccc3-4e10-b6ae-4c7cd17030e8') {
                		$branding_theme = 'Ananda Professional';
                	}

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

			        if ($invoice_key) {

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
				                    'Description__c' => (string)$line_item->Description,
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

        // exit('ddd');

		if ($update && count($invoices_patch_data['records']) > 0) {
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
	    	'title' 		=> $data['PreferredName'] != 'null' ? $data['PreferredName'] : '',
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

	public function recover_xero_contacts() {

        $contact_manager = new WC_XR_Contact_Manager(new WC_XR_Settings());

        $page_no = 1;

    	$elligible_contacts = [
    		"CORNER PHARMACY LLC","PEOPLES PHARMACY","ELKTON FAMILY PHARMACY","CERTACARE INC","PROFESSIONAL PHARMACY","CROSSROADS CARE PHARMACY LLC","SWEETGRASS PHARMACY & COMPOUNDING LLC","WALTON DRUG CO INC","SOLUTIONS PHARMACY","DUPRE PHARMACY","ORANGE PHARMACY","THE MEDICINE SHOPPE 1675","SOUTH FORK PHARMACY","GREENLEAF APOTHECARY, LLC","SUNSHINE PHARMACY","MOOSE PHARMACY OF KANNAPOLIS","MED-SAVE MARTIN, #2070","MED-SAVE LANCASTER, MEDICINE SHOPPE 2111","MEDICINE SHOPPE","THE MEDICINE SHOPPE","THE MEDICINE SHOPPE PHARMACY","GATTI PHARMACY","CURLEW PHARMACY","WOODBURN PHARMACY, LLC","COLONIAL DRUGS OF ORLANDO, LLC","COLONIAL DRUGS OF KISSIMMEE, LLC","THE MEDICINE SHOPPE PHARMACY","DICKSON APOTHECARY","BEST VALUE DRUG","NATIONAL RX","MEDOZ PHARMACY OF POLK INC.","SEASHORE DRUGS","CHEEK AND SCOTT DRUGS, INC","CHEEK & SCOTT DRUGS INC","CHEEK AND SCOTT DRUGS INC.","SHELDON'S EXPRESS PHARMACY #5","FORT WAYNE CUSTOM RX","NATION'S MEDICINES","YOUR GORDONSVILLE PHARMACY","LINTON FAMILY PHARMACY","BOLTON'S II, INC.","HEALTHY WAY PHARMACY CEDARKNOLL","CANNON PHARMACY MOORESVILLE","HAMPSHIRE PHARMACY","GALLOWAY-SANDS PHARMACY #2","MEDICINE ARTS PHARMACY","OKIE'S PHARMACY II","BRADLEY DRUG CO","CORNER DRUG STORE","BYPASS PHARMACY","GET RX HELP PHARMACY","RIVERPOINT PHARMACY","EAGLE HIGHLAND PHARMACY INC","SHELDON'S EXPRESS PHARMACY","TOMAHAWK PHARMACY, LLC","CONROY APOTHECARY INC","ARROW PHARMACY","PARIS APOTHECARY, LLC","PARKSIDE PHARMACY INC","CUREMED PHARMACY","DR. AZIZ PHARMACY","NUCARE PHARMACY & SURGICAL","CHELSEA ROYAL CARE PHARMACY, INC.","THE CORNER DRUG STORE","DELTONA PHARMACY OF FLORIDA, LLC","MEDICAL ARTS SUPPLY INC","PHARMACY PLUS INC","FRANKLIN COMMUNITY PHARMACY","REGENCY DRUGS","THE PRESCRIPTIONS SHOPPE","PORTAGE PHARMACY","PLANTATION PHARMACY AT ELLIS OAKS","FIRST CARE PHARMACY","SPRINGFIELD DRUGS","DEPIETROS PHARMACY LLC","ANNAPOLIS PROFESSIONAL PHARMACY","RICKETTS PHARMACY, INC","DIXIE PHARMACY","DAVIS DRUGS","CEDRA PHARMACY","CEDRA PHARMACY","EAST WASHINGTON PHARMACY LLC","BYPASS PHARMACY # 4","KINDRED CARE PHARMACY","PHARMACY CARE SOLUTIONS, INC","APTHORP PHARMACY","UNITED PHARMACY","JERRYS DRUG & SURGICAL SUPPLY","GROVE HARBOR MEDICAL CENTER PHARMACY","WHITTIER DRUGS","SOUTHERN RX","APOTHECARE PHARMACY III","APOTHECARE PHARMACY VINE GROVE","CONNEYS PHARMACY","GREENWOOD PHARMACY","SHAKAMAK PHARMACY","COOPER DRUGS INC","STEPHANIE'S DOWN HOME PHARMACY, INC.","LAKEWAY PHARMACY","THE CHEMIST SHOP","UPLAND FAMILY PHARMACY","DUNDEE PHARMACY","WEST SIDE PHARMACY","STONE'S PHARMACY INC.","AMERICAN KINETICS LAB","EASTPARK PHARMACY","PARVIN'S PHARMACY","RICHARDSON PHARMACY","PAUL'S PHARMACY","COLLINGSWORTH PHARMACY","LAKE CUMBERLAND PHARMACY","DUNLOP PHARMACY","TOWN & COUNTRY DRUG, INC.","MEDICAL CENTER PHARMACY","POWHATAN DRUG INC","PAOLI PHARMACY INC.","KELLY'S PHARMACY INC.","MORNINGSIDE MEDICAL PHARMACY, INC.","BUSHY RUN PHARMACY","YOUNG'S PHARMACY","MIDWAY PHARMACY","LAKE WYLIE PHARMACY","ADD DRUG","PLAZA DRUG OF LONDON","PRIMISHA PHARMACY","CYNTHIANA HOMETOWN PHARMACY","JIFFY SCRIPTS RX, LLC","COLUMBUS LOCAL PHARMACY","WILLIAMS BROS. HEALTH CARE PHARMACY","GLADWYNE PHARMACY","WAYNE PHARMACY","DARIEN PHARMACY","ELLICOTT CITY PHARMACY","ASHLAND PHARMACY, INC.","MEDICAP PHARMACY","OLDE TOWNE PHARMACY , INC.","MICHAEL YARWORTH","ORTIZ PHARMACY, INC.","LONGLEY'S PHARMACY","VALPARAISO PROFESSIONAL PHARMACY","POINT LOMA SHELTER ISLAND PHARMACY","POINT LOMA CABRILLO DRUG","PARK AVENUE PHARMACY, INC","KAREMORE PHARMACY","TEGA CAY FAMILY PHARMACY","CONDO PHARMACY","SMITHS ST HELENA PHARMACY","PARKER PHARMACY INC","CLINIC PHARMACY OF KY","LAFAYETTE PHARMACY","SARASOTA DISCOUNT PHARMACY","UNI-MED PHARMACY","REDI CORPORATION","PROSPERITY PHARMACY MANASSAS","ORGANYC PHARMACY","LONG BEACH CHEMISTS","PURE MERIDIAN","THE PRESCRIPTION SHOP","CRESCENT CENTER DRUG","WILSONS SAV-MOR DRUGS","FOUNTAIN VALLEY MED CENTER PHARMACY","HOFFMAN DRUG","CAREMART PHARMACY LLC","WILSHIRE LAPEER PHARMACY","KINGSTON SPRINGS PHARMACY","AN NOOR PHARMACY INC","UNION AVENUE COMPOUNDING PHARMACY","SIERRA SAN ANTONIO PHARMACY","SAL PHARMACY , INC","JOSEPH'S PHARMACY","CHAPIN PHARMACY","DANIELS PHARMACY OF BARNWELL","DANIELS PHARMACY OF BLACKVILLE","GASTON FAMILY PHARMACY","PINE RIDGE PHARMACY","ARNOLD DRUG COMPANY","FAMILY PHARMACY","VILLAGE PHARMACY","CROSBY'S DRUGS, INC.","GILLESPIES DRUGS","HYRUM'S FAMILY VALUE PHARMACY","INDIANA PREMIER PHARMACY","WESTRIVER PHARMACY","SIERRA PHARMACY","DOUG'S FAMILY PHARMACY","SCALES PHARMACY","OC WELLNESS AND SPECIALTY PHARMACY","JR PHARMACY LLC 2","JR PHARMACY POPLAR LLC","MEDICINE SHOPPE","EAST TENNESSEE DISCOUNT DRUG","TOWN AND COUNTRY DRUGS AND HOME MEDICAL","HERNANDO'S HOMETOWN PHARMACY","DE LEON PHARMACY","H AND S PHARMACY","BEREA DRUG","CENTURY MEDICINES","KINGS PHARMACY","JAYHAWK PHARMACY","SERV U PHARMACY INC","PLATEAU DRUGS","SCOTT COUNTY PHARMACY","JAMESTOWN PHARMACY","O'BRIEN & DOBBINS PHARMACY","ROSEDALE PHARMACY","AMBULATORY CARE PHARMACY","OAKLAND DRUGS","HOME CARE PHARMACY OF PLAM COAST INC.","STOVALLS PRESCRIPTION SHOP","EL TORO PHARMACY","LIBERTY MEDICENTER PHARMACY","BEVERLY HILLS APOTHECARY","ACADIANA PRESCRIPTION SHOP","CHET JOHNSON DRUGS, INC.","CRANFORDS DRUG STORE","BURBANK COMPOUNDING PHARMACY","RIVER CITY PHARMACY","RACELAND'S PHARMACY EXPRESS","YINGER PHARMACY SHOPPE","DOGWOOD PHARMACY","BATH DRUG","GRAVES DRUG STORE EMPORIA INC","HILL COUNTRY MEDICAL EQUIPMENT","VASHON PHARMACY INC","SURGOINSVILLE PHARMACY","L'ANSE PHARMACY INC.","HOT SPRINGS PHARMACY","MARCUM'S PHARMACY, INC","CITY DRUG","ARNOLD DRUG COMPANY","CLOVERDALE PHARMACY","ENGLEKING RX, LLC","THOMPSON PHARMACY","BRIGHTON HEALTHMART PHARMACY","VIGO HEALTH PHARMACY","ATWOOD PRESCRIPTION CENTER INC.","JB PHARMACY","GREATER CARE PHARMACY LLC","SCHAEFER SEVEN DRUGS","SALEM CRSSRDS APOTHECARY","GARRETT DRUG COMPANY","HARTSVILLE DRUG CO., INC.","DISCOUNT PHARMACY LLC","CORNER DRUG COMPANY INC","MICKEY FINE PHARMACY","SINCLAIR PHARMACY","HERRIN DRUG","BIG SANDY PHARMACY","CORNER DRUG","BEDDINGFIELD DRUGS LLC","WELLNESS PHARMACY AND COMPOUND","KELLY'S PHARMACY INC.","HOUSTON FAMILY PHARMACY","REGENCY MEDICAL PHARMACY","WALTON PHARMACY","LUDLOW PHARMACY","MONTEREY DRUGS","GRAY PHARMACY","SULLIVAN PHARMACY INC.","TRI-VALLEY PHARMACY","TRI-VALLEY PHARMACY","IRWIN-POTTER DRUG","FREEMAN PHARMACY LLC","FREEMAN PHARMACY LLC","FREEMAN PHARMACY","CLEARWATER PHARMACY","PRATT PHARMACY INC","HURST DISCOUNT DRUG","HOLLANDS PHARMACY","SALINE PHARMACY","ANN ARBOR PHARMACY LLC","DRUM'S PHARMACY INC","ACC APOTHECARY","BAGGETT PHARMACY, INC","EVANS DRUGS","GIL DRUGS","OAKLEY HEALTH MART","R & M DRUG","R & M DRUG","CLARKSVILLE MEDICAL SOLUTIONS","THORNCHERRY","CLARK COUNTY PHARMACY","SIPPLE'S CHIROPRACTIC","WESTERN MEDICAL EQUIPMENT","WESTERN MEDICAL EQUIPMENT","TEXOMA MEDICAL SERVICES INC","WESTERN DRUG #5","TEXOMA MEDICAL SERVICES, INC.","WESTERN DRUG #2","WESTERN DRUG #6","LIVINGSTON PHARMACY","VASHAN PHARMACY COMPOUNDING/CONSULTING","BLOOMINGDALE DRUG OF KINGSPORT, LLC","MEDICAL CENTER PHARMACY","MEDICINE CENTER","VOORHIES HEALTH PHARMACY INC","GREENLEAF PHARMACY","MEDICINE SHOPPE","DUREN PHARMACY","HOMETOWN PHARMACY","VERSAILLES INDEPENDENT PHARMACY","TRACE PHARMACY","GALLERY DRUGS","SALINAS PHARMACY, INC.","NOLITA CHEMISTS","99 CENTS TOP GRADE","MAPLEWOOD PHARMACY","JAMES PHARMACY","APOTHECA INTEGRATIVE PHARMACY","MARYVILLE PHARMACY CERETTOS","CHINOOK PHARMACY","UPTOWN RX PHARMACY & NUTRITION","VICTORY PHARMACY","SALEM HEALTH MART PHARMACY","LITTLES HSC PHARMACY","BUCHANAN BROTHERS PHARMACY INC","ADVANCE PHARMACY SOLUTION","KIRBY AND COMPANY PHARMACY LLC","BIRCH RUN DRUGS","ROMAN PHARMACY","BTV PHARMACY","CLOVERDALE DRUGS","HOPE PHARMACY","BEL GRIFFIN, PLLC","LINSKY PHARMACY","LINCOLN KNOLLS PHARMACY","THE MEDICINE SHOPPE PHARMACY","MOHRMANN'S DRUG STOREL LLC","JOSEFS PHARMACY LLC","TERRY'S PHARMACY, INC.","HOLDEN PHARMACY","MATHES PHARMACY INC","VINEYARD SCRIPTS","WILLIAMS BROS HEALTH CARE PHARMACY","CUSTOM PRESCRIPTION SHOPPE","PLAYA PHARMACY","REGENTS PHARMACY","CUSTOM PHARMACY, INC","CENTRE PARK PHARMACY","MODERN PHARMACY LLC","OSWALD'S PHARMACY","JENNY'S PHARMACY& DISCOUNT INC","LIBERTY PHARMACY, INC","LANDY'S PHARMACY","BULLS GAP DRUGS","FISHERS PHARMACY","DOWNTOWN HEALTH MART PHARMACY","WELLNESS PLUS PHARMACY INC","MOORE'S PHARMACY","NEW AMSTERDAM DRUG MART INC","ART PHARMACY CORP","YOUNG PHARMACY","B.V.M. PHARMACY INC.","CARO DRUGS","EXPRESS PHARMACY","FULTON DRUGS INC","GET WELL RX INC","HERITAGE CHEMISTS INC","IRVING PHARMACY","JERSEY VILLAGE PHARMACY-COMPOUNDING","KINGS PARK PHARMACY","WELLNESS PHARMACY","SHALOM'S PHARMACY INC","MAIN FAIR PHARMACY","WILLOW LAKE PHARMACY INC","PHARMACY SPECIALTIES & CLINIC, INC.","MADISON AVENUE PHARMACY","PAPER MILL PHARMACY, INC","GOOD DAY PHARMACY LLC","LEROY PHARMACY","MT VERNON PHARMACY","LAKEVIEW PHARMACY OF RACINE INC","SMITH FAMILY PHARMACY INC","PARKVIEW PHARMACY","LILY'S PHARMACY, LLC","MIDLOTHIAN APOTHECARY","HOGAN'S PHARMACY","BELEW DRUG CHOTO","MEDICENTER PHARMACY","ELLAHI DRUGS INC","THE MEDICINE SHOPPE PHARMACY","COMPREHENSIVE CARE PHARMACY","SOO'S DRUG STORE","VILLA RICA DRUGS","GARST RX","EDGERTON PHARMACY","BRANDON PHARMACY","MEDICAP PHARMACY","CENTURY MEDICINES","ROBERTS' SOUTH BANK PHARMACY, INC.","PRINCEVILLE PHARMACY","RALEIGH PHARMACY","ROCKY MOUNTAIN PHARMACY","ROCK HILL PHARMACY","DARLING PHARMACY","WESTERN DRUG #3","MADISON PHARMACY","HEALTHSMART PHARMACY (EAST)","FAMILY 1 PHARMACY","PERSON STREET PHARMACY LLC","DRUG CENTER PHARMACY 2","LYNN'S PHARMACY","MCMAHAN PHARMACY SERVICES","LAKETOWN PHARMACY","CUSTOM PLUS PHARMACY LLC","SHERMANS PHARMACY","CAMPTON DISCOUNT DRUGS","BATH HOMETOWN PHARMACY","DISCOUNT DRUGS INC","OKULEYS PHARMACY INC.","LEMED PHARMACY III LLC","ISAK PHARMACY INC.","GOREVILLE PROFESSIONAL PHARMACY","TOWNE PHARMACY","SERVICE PHARMACY SHERBURNE","SERVICE PHARMACY NEW BERLIN","POTOMAC CARE PHARMACY","SERVICE PHARMACY","PROCARE PHARMACY","RUSTON WELLNESS & COMPOUNDING PHARMACY","NEWNAN PHARMACY INC.","BROOKS PHARMACY","ATTICA PHARMACY, INC.","QUEENSBRIDGE PLAZA PHARMACY CORP","UNIVERSAL PHARMACY","BANDY'S PHARMACY II","APALACHIN PHARMACY","ARNOLD PROFESSIONAL PHARMACY","WHITAKER PHARMACY","VILLAGE PHARMACY","PRINCETON DRUG","BRIGHTON 11TH PHARMACY INC.","SANFORD PHARMACY, INC","DBA DOVER FAMILY PHARMACY","SAINT PARIS PHARMACY","MIDTOWN PHARMACY","GRAVES DRUG","HEARTLAND APOTHECARY","MIDWEST FAMILY HEALTH","MINERAL PHARMACY INC","MILLINGTON PHARMACY","DSD PHARMACY INC.","WESTERN UNIVERSITY PHARMACY","SANDSRX, LLC","THE COMPOUNDING PHARMACY","LENOX TERRACE DRUG STORE INC","CAMDEN DRUG","METRO DRUGS","SPINDALE DRUG COMPANY","SOUTH DIXIE PHARMACY","ST. MICHAEL'S PHARMACY","SOUTHSIDE PHARMACY","ROCKVILLE PHARMACY","SMITH BROS DRUG COMPANY","NORLAND AVENUE PHARMACY, LLC","ROARK'S PHARMACY, INC.","PREFERRED PHARMACY SEVIERVILLE","GOLDEN HEALTH PHARMACY","JAMES & WILKS PHARMACY","BREMO PHARMACY","CORE PHARMACY","RIGGS DRUG EMORY RD","PARK PHARMACY","MUNSEY PHARMACY","MAIN STREET PHARMACY","TRINITY PHARMACY","CLINIC PHARMACY","ROBERTS' PHARMACY LLC","COLONIAL HEIGHTS PHARMACY","THE MEDICINE SHOPPE PHARMACY","MORGANS PHARMACY","FUSION RX","MEDICAL CENTER PHARMACY","BRANDENBURG PHARMACY CARE","QUICK CARE PHARMACY INC","A&W PHARMACY","PRESTON'S PHARMACY","MASONIC VILLAGE PHARMACY","WHITEFISH PHARMACY","MOORE FAMILY STORES INC.","MOORE FAMILY STORES INC.","RIGHT CHOICE PHARMACY INC","DIAMONDHEAD HEALTH MART PHARMACY","PHARMACY IN THE BAY","WHEELER'S CUSTOM COMPOUNDING, INC.","EXTENDED LIVING PHARMACY LLC","CLAYTON'S PHARMACY","HEALTHSMART PHARMACY","MEDICINE SHOPPE","ERNIE'S DRUG, INC.","KAY'S PHARMACY","BERTRAM PHARMACY","RED CROSS DRUG STORE","SPRING CITY PHARMACY","MORRISTOWN PHARMACY","PALACE DRUG","IVEY'S PHARMACY","ROYAL PHARMACY","REEVES DRUG STORE INC","MEDICINE SHOPPE","THE ROBBINS PHARMACY","EXTRA CARE PHARMACY INC","MASSEY HILL DRUG CO.","HARRISON PHARMACY","TOWNE LAKE FAMILY PHARMACY LLC","TOLEDO PHARMACY","PROFESSIONAL PHARMACY","LEVYS PHARMACY INC","ASTON PHARMACY","PASTEUR PHARMACY","BARBEE PHARMACY & GIFTS","HOMETOWN PHARMACY LLC","COWAN DRUGS","FARRAGUT PHARMACY INC","MINT PHARMACY AND SKIN CLINIC","ECONOMY PHARMACY EAST","ECONOMY PHARMACY EXPRESS","THE MEDICINE SHOPPE","MAINLINE PHARMACY","METCALFE DRUG","MAINLINE PHARMACY","MAINLINE PHARMACY","MAINLINE PHARMACY","BLAIRSVILLE PHARMACY","SOMERSET DRUG","TOWNSHIP PHARMACY","COMMUNITY PHARMACY","C.O. BIGELOW CHEMISTS","MARISTE PHARMACY","SANATOGA PHARMACY","ST LUKE PHARMACY INC","JEFFERSON COMPOUNDING CENTER","POOLES PHARMACY CARE","DUTCHESS CHEMISTS INC.","MEDICINE SHOPPE","COMMUNITY SURGICAL PHARMACY","MAIN PHARMACY","ASHVILLE APOTHECARY","BENS PHARMACY","MEDICINE SHOPPE PHARMACY OF SADDLE BROOK","CAREPLUS DISCOUNT PHARMACY","EASTERN STATES COMPOUNDING PHARMACY","GALLOWAY SANDS PHARMACY","THE MEDICINE SHOPPE","RIVERTOWN PHARMACY","WIL SAV DRUGS","WARD SPECIALTY PHARMACY LLC","VINEYARD PHARMACY","OCALA PHARMACY LLC","FAMILY HEALTH CENTER OF SOUTHERN OKLAHOMA PHARMACY","KOONCE DRUG COMPANY, INC","TABOR CITY MEDICINE MART","MEDICAL CENTER PHARMACY","BUCKEYE DRUGS","LITTLE'S PHARMACY","MOODY PHARMACY","PHILS DISCOUNT DRUG","KINGMAN DRUG INC","BYRD WATSON DRUG CO","NOBLESVILLE LOW COST PHARMACY","HEALTHMART PHARMACY","RIVER VILLAGE PHARMACY","COCHRANE RIDENHOUR DRUG CO #3","MCCAYSVILLE DRUG CENTER INC","JASPER DRUG STORE","YORK & CO. PHARMACY","MEDICAP PHARMACY","TKS PHARMACY","HARROLDS PHARMACY INC","HOWARDS PHARMACY LLC","DANNY'S PHARMACY","VALUEPLUS PHARMACY INC","JACK'S DISCOUNT PHARMACY","MARTINS PHARMACY INC","LAS VEGAS SCRIPTS RX LLC.","KIOWA COUNTY PHARMACY","ANDERSON DRUGS AND HOME CARE","PHARMACY EXPRESS","HIDENWOOD PHARMACY, INC","TARRYTOWN PHARMACY","WHOLESOME HEALTH PHARMACY","WESTERN COLORADO SPECIALTY PHARMACY","PROFESSIONAL PHARMACY","BUNTES PHARMACY","GEORGE'S FAMILY PHARMACY, INC.","OAK PARK PHARMACY","CHERRY GROVE DRUG","ROCKY HILL PHARMACY, LLC","JJ BEANS AT LENOX LLC","FOWLERS PHARMACY","BRODIE LANE PHARMACY","NORTHSIDE PHARMACY","KNOX PROFESSIONAL PHARMACY","BAYDOUN PHARMACY","MCBAIN FAMILY PHARMACY","RED DOOR PHARMACY AND GIFTS","MCCAYS TOTAL PHARMACY","LAGRANGE PHARMACY INC.","BENZER PHARMACY 105","BENZER PHARMACY","BENZER PHARMACY 146","BENZER PHARMACY 135","COMMUNITY CARE PHARMACY","CHASE DRUGS & CLINICAL SERVICES","DOUG'S PHARMACY","MEDICAP PHARMACY","PERKINS DRUG","GOOD NEIGHBOR PHARMACY","RXCARE PHARMACY","BENZER PHARMACY","BROOKSVILLE DRUGS INC","MINT HILL PHARMACY","BENZER NY 1 LLC","BENZER PHARMACY 160","BENZER PHARMACY","OCEAN CHEMIST","COOKEVILLE MEDICAL FAMILY PHARMACY","DOWNHOME PHARMACY","KEDVON PHARMACY SOLUTIONS, INC.","EXPRESS PHARMACY","HAYES DRUG","BENZER CA 1 LLC","MEDI-THRIFT INC","RX CARE PHARMACY INC","BUDERER DRUG COMPANY, AVON","BUDERER DRUG COMPANY, INC.","TALLENT DRUG COMPANY","MEDICAL ARTS PHARMACY","BRUNDAGES WAYMART PHARMACY","CENTER DRUG","METROLPOLIS DRUGS II","PHARMACY AT THE WAVE INC","SPROUL PHARMACY","CLEMENTS PHARMACY","BUNCH MEDICAL LLC","BENZER PHARMACY","APOTHECARE PHARMACY OF ELIZABETHTOWN PSC","PRESCRIPTION ALTERNATIVES INC","D-REX DRUGS OF JONESVILLE INC","REEDS COMPOUNDING PHARMACY","PHARMEDICO PHARMACY","RED STICK PHARMACY LLC","CHRIS' PHARMACY IN PORT VINCENT","BETTER LIVING MEDICAL","BROKEN ARROW FAMILY DRUG NORTH","DCA PHARMACY","PREVO DRUG","BOATWRIGHT DRUG CO., INC","BOWKER'S PHARMACY","I CARE PHARMACY","VYTOS PHARMACY","WILLIAM MICHAEL RICHARDSON","LEWISVILLE DRUG COMPANY","PITT STREET PHARMACY INC","GIBSON PRESCRIPTION PHARMACY","ANDREPONT PHARMACY INC","DELMARVA PHARMACY","BENZER PHARMACY","CARE PLUS PHARMACY","POWERS HEALTHMART PHARMACY","TOMS RIVERSIDE PHARMACY 9775","COST LESS PRESCRIPTIONS INC","CUSTOM-MED COMPOUNDING PHARMACY","THE MEDICINE SHOPPE PHARMACY"
    	];

        do {
        	sleep(1);
            $response = $contact_manager->get_all_contacts($page_no);
            $contacts_wrapper = $response->Contacts;
            $contacts = $contacts_wrapper->Contact ?: [];

            $this->printTime();
            echo ('___________ current page________________' . $page_no . '______' . count($contacts_wrapper) . '_______' . count($contacts) . '<br/>');

            if (count($contacts) > 0) {
                foreach ($contacts as $contact) {
                	$this->printTime();	
                	echo 'processing --- ' . (string)$contact->ContactID . ' ----- ';

                	if (in_array((string)$contact->Name, $elligible_contacts)) {
                		echo 'recovering contact - found -----';
                	} else {
                		echo 'recovering contact - not found ----- <br/>';
                		continue;
                	}

                	// echo '<br/>';
                	// continue;

                	sleep(2);

                	if($contact_manager->recover_contact((string)$contact->ContactID, (string)$contact->Name)) {
                		echo 'Xero contact recovered (' . (string)$contact->ContactID . ') - ' . (string)$contact->Name . '<br/>';
                	} else {
                		echo 'failed ' . (string)$contact->ContactID . ') - ' . (string)$contact->Name . '<br/>';
                	}

			  //       $contact_name = $this->get_npi_organization_name((string)$contact->AccountNumber);

			  //       if (!$contact_name) continue;

					// $patch = new WC_XR_Contact();
					// $patch->set_id( (string)$contact->ContactID );
					// $patch->set_name( $contact_name );

					// $patch->set_email_address( $billing_email );
					// $patch->set_account_number( $account_number );

					// $contact_request_update = new WC_XR_Request_Update_Contact( new WC_XR_Settings(), (string)$contact->ContactID, $patch );
					// $contact_request_update->do_request();

					// $this->printTime();
					// echo 'Xero Contact with NPI - ' . $account_number . ' has changed name from "' . (string)$contact->Name . '" to "' . $contact_name . '"<br/>';
                }
            }

            $page_no++;
        } while(count($contacts) > 0);

	}
}
