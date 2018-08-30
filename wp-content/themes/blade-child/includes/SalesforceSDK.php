<?php

class SalesforceSDK {

	/* dev */
	
	// private $yourInstance = 'na40';
	// private $client_id = '3MVG9i1HRpGLXp.rr5NrL7AxCC_g5Klf.h.zxML5Lw1dRc_ndae5fLFusRS0TlcjH3XWRL3hfclENG4CkVsAm';
	// private $client_secret = '4036959497197072492';
	// private $username = 'lance@shubout.com';
	// private $password = 'Developer@2018UTydBAE3P1dLSuokm6LJgmi6';
	// private $contact_field_id = 'Unique_Import_Id__c';
 //    private $base_url = "https://na40.salesforce.com/services/data/";
	

	/* production */
	
	private $yourInstance = 'na40';
	private $client_id = '3MVG9CEn_O3jvv0yFWfRjWvZ00r7kMm3yAL3JcQiAzgXDz3NzelQl0jEfe3GTrAsUQuBOJc9hxjeLR1DHOoj8';
	private $client_secret = '1892914933810725936';
	private $username = 'lance032017@gmail.com';
	private $password = 'Developer@2018tfYBERgFz8YwOHhJAGLRqoJc';
	private $contact_field_id = 'Unique_Import_Id__c';
    private $base_url = "https://anandahemp.my.salesforce.com/services/data/";
	


    // private $base_url = "https://anandahemp.my.salesforce.com/services/data/";
    private $token_url = "https://login.salesforce.com/services/oauth2/token";

    private $auth = null;

	public function __construct( $yourInstance = 'na40' ) {
		$this->yourInstance = $yourInstance;
		// $this->set_base_url();
		
		$this->authenticate();
	}

	public function set_base_url() {
		$this->base_url = 'https://' . $this->yourInstance . '.salesforce.com/services/data/';
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

	public function get_token() {
		return isset($this->auth->access_token) ? $this->auth->access_token : '';
	}

	public function describe($table) {
		return $this->do_request('v20.0/sobjects/' . $_GET['table'] . '/describe');
	}

	public function get_invoice_by_id($id) {
        $invoice_manager = new WC_XR_Invoice_Manager(new WC_XR_Settings());

        $response = $invoice_manager->get_invoice_by_id($id);

        return $response;
	}

	public function get_all_invoices() {

        $invoice_manager = new WC_XR_Invoice_Manager(new WC_XR_Settings());

        $page_no = 1;

        do {
            $response = $invoice_manager->get_all_invoices($page_no);
            $invoices_wrapper = $response->Invoices;
            $invoices = $invoices_wrapper->Invoice ?: [];

            var_dump('current page________________' . $page_no . '______' . count($invoices_wrapper) . '_______' . count($invoices));

            if (count($invoices) > 0) {
                // $invoices = $invoices_wrapper->Invoice;

                foreach($invoices as $invoice) {
                    var_dump($invoice);
                }
                // var_dump ($invoices_wrapper);
            }

            $page_no++;
        } while(count($invoices) > 0);

	}

	public function get_contact_by_id($xero_contact_id) {

        $contact_manager = new WC_XR_Contact_Manager(new WC_XR_Settings());

		$response = $contact_manager->get_contact_by_id($xero_contact_id);

		return $response;
	}

	public function get_account_from_external_xero_contact_id($xero_contact_id = 'BLANK_ID') {

        $response = $this->do_request('v20.0/sobjects/Account/' . $this->contact_field_id . '/' . $xero_contact_id);

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

        $response = $this->do_request('v20.0/sobjects/Account/', ['post' => true, 'postData' => $data]);

        if ($response->success == true) {
        	$obj = new stdClass();
        	$obj->Id = (string)$response->id;
        	return $obj;
        } else {
        	var_dump($response);
        	return null;
        }

	}

	public function migrate_invoices() {

        $invoice_manager = new WC_XR_Invoice_Manager(new WC_XR_Settings());

        $invoices_data = [
            'records' => []
        ];

        $page_no = 1;

        $account_ref_no = 1;
        $invoice_ref_no = 1;
        $line_item_ref_no = 1;
        $tracking_category_ref_no = 1;

        $accounts_from_ext = [];

        do {
            $response = $invoice_manager->get_all_invoices($page_no);
            $invoices_wrapper = $response->Invoices;

            var_dump('current page________________' . $page_no . '______' . count($invoices_wrapper));

            if (count($invoices_wrapper) > 0) {
                foreach ($invoices_wrapper->Invoice as $invoice) {

                	// var_dump($invoice);

                	$ext_id = (string)$invoice->Contact->ContactID;
                	if (!isset($accounts_from_ext[$ext_id])) {
                		$account = $this->get_account_from_external_xero_contact_id($ext_id);
                		if ($account) {
                			$accounts_from_ext[$ext_id] = $account;
                		} else {
                			var_dump ('================= invalid external id ' . $ext_id . '------------------');
                			continue ;
                		}
                	} else {
                		$account = $accounts_from_ext[$ext_id];
                	}

                	// var_dump($account);

                	if (!$account->Id) {
                		var_dump('______________ Relevant Account not existing ___________');
                		continue;
                	}
                	var_dump('_______________ Relevant Account existing___________');

					$invoice_record = [
			            'attributes' => [
			                'type' => 'Xero_Invoice__c',
			                'referenceId' => 'REF__INVOICE_' . $invoice_ref_no++ . '_' . (string)$invoice->InvoiceNumber
			            ],
			            'AmountCredited__c' => (string)$invoice->AmountCredited,
			            'AmountDue__c' => (string)$invoice->AmountDue,
			            'AmountPaid__c' => (string)$invoice->AmountPaid,
			            'BrandingTheme__c' => 'Ananda Professional',//(string)$invoice->BrandingThemeID,
			            'Date__c' => (string)$invoice->Date,
			            'DueDate__c' => (string)$invoice->DueDate,
			            'FullyPaidOnDate__c' => (string)$invoice->FullyPaidOnDate,
			            'InvoiceNumber__c' => (string)$invoice->InvoiceNumber,
			            'Reference__c' => (string)$invoice->Reference,
			            'Status__c' => (string)$invoice->Status,
			            'SubTotal__c' => (string)$invoice->SubTotal,
			            'Total__c' => (float)$invoice->Total,
			            'TotalDiscount__c' => (string)$invoice->TotalDiscount,
			            'TotalTax__c' => (string)$invoice->TotalTax,
			            // 'AccountNumber__c' => $account->AccountNumber ?: '',
			            'Unique_Import_Id__c' => $account->Id ?: '',
			            'Xero_Invoice_ID__c' => (string)$invoice->InvoiceID,
			        ];

			        $line_items_data = [
			            'records' => []
			        ];
			        $line_items_wrapper = $invoice->LineItems;
			        if (count($line_items_wrapper) > 0) {
			            foreach($line_items_wrapper->LineItem as $line_item) {
			                $line_item_record = [
			                    'attributes' => [
			                        'type' => 'Xero_Invoice_Item__c',
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
			                                'type' => 'Xero_Invoice_Item_Tracking_Category__c',
			                                'referenceId' => 'REF__TRACKING_CATGEGORY_' . $tracking_category_ref_no++ . '_' . (string)$tracking_category->TrackingCategoryID,
			                            ],
			                            'Name__c' => (string)$tracking_category->Name,
			                            'Option__c' => (string)$tracking_category->Option,
			                            'Tracking_Category_ID__c' => (string)$tracking_category->TrackingCategoryID,
			                        ];
			                    }

			                    $line_item_record['Xero_Invoice_Item_Tracking_Category__r'] = $tracking_categories_data;
			                }

			                $line_items_data['records'][] = $line_item_record;
			            }
			            $invoice_record['Xero_Invoice_Items__r'] = $line_items_data;
			        }

                    $invoices_data['records'][] = $invoice_record;

                    if (count($invoices_data['records']) > 5) {
                    	$this->handle_submit_invoices_data_queue($invoices_data);
                    	$invoices_data['records'] = [];
				    }

                }
                // var_dump ($invoices_wrapper->Invoice);
            }

            $page_no++;
        } while(count($invoices_wrapper) > 0);

        // exit('ddd');

        $this->handle_submit_invoices_data_queue($invoices_data);

        // $response = curl_salesforce($line_item_records_url, $token, true, $line_items_data);
        // var_dump('___________________Line Items Records Result___________________');
        // var_dump($response);

	}

	public function handle_submit_invoices_data_queue($invoices_data) {
		// var_dump($invoices_data);

		while (count($invoices_data['records']) > 0) {
	        $response = $this->do_request('v34.0/composite/tree/Xero_Invoice__c/', ['post' => true, 'postData' => $invoices_data]);
	        if ($response->hasErrors) {
	        	$error_refIds = [];
	        	foreach ($response->results as $error) {
	        		$error_refIds[] = (string)$error->referenceId;
	        		var_dump('Error occured _____________________ on ' . (string)$error->referenceId . '___and skip it');
	        		var_dump($error);
	        	}

	        	foreach ($invoices_data['records'] as $key => $record) {
	        		if (in_array($record['attributes']['referenceId'], $error_refIds)) {
	        			unset($invoices_data['records'][$key]);
	        		}
	        	}
	        } else {
	        	var_dump('_________________ INVOICE SUBMIT RESULT ___________________');
	        	var_dump($invoices_data);
	        	var_dump($response);
	        	$invoices_data['records'] = [];
	        }
	    }
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


        // $response = $this->do_request('v34.0/composite/tree/Account/', ['post' => true, 'postData' => $contacts_data]);
        // var_dump('___________________Contact Records Result___________________');
        // var_dump($response);

	}
}