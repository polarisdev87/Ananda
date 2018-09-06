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

	public function get_auth() {
		return $this->auth;
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

	public function get_all_invoices($startsWith = '') {

        $invoice_manager = new WC_XR_Invoice_Manager(new WC_XR_Settings());

        $all_invoices = [];

        $page_no = 1;

        do {
            $response = $invoice_manager->get_all_invoices($startsWith, $page_no);
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

	public function get_all_accounts() {

		$response = $this->do_request('v20.0/query/?q=SELECT+id,name,NPI_Number__c,Unique_Import_Id__c+from+Account');

		return array_map(function ($account) {
			return [
				'Id' => $account->Id,
				'Name' => $account->Name,
				'NPI_Number__c' => $account->NPI_Number__c,
				'Unique_Import_Id__c' => $account->Unique_Import_Id__c,
			];
		}, $response->records);

		return $response->records;
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

	public function create_account_from_xero_contact_id($xero_contact_id = '') {
		if (!$xero_contact_id) return null;

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

        $response = $this->do_request('v20.0/sobjects/Account/', ['post' => true, 'postData' => $data]);

        if ($response->success == true) {
        	return [
        		'Id' => (string)$response->id,
        		'Unique_Import_Id__c' => $xero_contact_id,
        	];
        } else {
        	return null;
        }
	}

	public function migrate_invoices($startsWith = '') {

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

        $page_no = 1;

        $account_ref_no = 1;
        $invoice_ref_no = 1;
        $line_item_ref_no = 1;
        $tracking_category_ref_no = 1;

        // $accounts_from_ext = [];

        do {
            $response = $invoice_manager->get_all_invoices($startsWith, $page_no);
            $invoices_wrapper = $response->Invoices;
            $invoices = $invoices_wrapper->Invoice ?: [];

	        $this->printTime();
            echo ('___________ current page________________' . $page_no . '______' . count($invoices_wrapper) . '_______' . count($invoices));

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
                		if ( strpos((string)$invoice->InvoiceNumber, 'WP-') === 0) {
                			$account = $this->create_account_from_xero_contact_id($ext_id);
	        				$this->printTime();
                			if ($account) {
                				// echo ' ____________ created account with ' . $ext_id . ' from invoice___' . (string)$invoice->InvoiceNumber . '<br/>';
                				$accounts[] = $account;
                			} else {
                				// echo ' _____________ failed to create account with invoice' . (string)$invoice->InvoiceNumber . ' due to ' . $ext_id . '<br/>';
                				continue;
                			}
                		} else {
            				// echo ' _____________ skipping relationship invoice___' . (string)$invoice->InvoiceNumber . ' due to ' . $ext_id . '<br/>';
            				if ($dummy_account) {
            					$account = $dummy_account;
            				} else {
            					// echo '_______found no dummy account for ' . (string)$invoice->InvoiceNumber . '<br/>';
            					continue;
            				}
            				// continue;
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
			            'Type__c' => (string)$invoice->Type,
			            // 'AccountNumber__c' => $account->AccountNumber ?: '',
			            'Account_ID__c' => $account['Id'] ?: '',
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
                // var_dump ($invoices_wrapper->Invoice);
            }

            $page_no++;
        } while(count($invoices) > 0);

        // exit('ddd');

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
	        $response = $this->do_request('v34.0/composite/tree/'. $table .'/', ['post' => true, 'postData' => $records_data]);
	        if ($response->hasErrors) {
	        	$error_refIds = [];
	        	foreach ($response->results as $error) {
	        		$error_refIds[] = (string)$error->referenceId;
	        		// $this->printTime();
	        		// echo '_______________ Error occured _____________________ on ' . (string)$error->referenceId . '___and skip it <br/>';
	        		if ((string)$error->errors[0]->statusCode!='DUPLICATE_VALUE') {
		        		// echo '<pre>', var_dump($error), '</pre>';
		        		// echo '<br/>';
		        	}
	        	}

	        	foreach ($records_data['records'] as $key => $record) {
	        		if (in_array($record['attributes']['referenceId'], $error_refIds)) {
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


	public function get_all_stores() {
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

	public function migrate_stores() {
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
	}

	public function add_new_store($data) {
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
	    if ($wpdb->insert(ASL_PREFIX.'stores', $formatted)) {
	    	$storeID = $wpdb->insert_id;
	    	return $storeID . '|' . $latitude . '|' . $longitude;
	    } else {
	    	return '';
	    }
	}

	public function edit_store($data) {
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
	    	// 'country' 		=> 223, //United States
	    	'lat' 			=> $latitude != 'null' ? $latitude : null,
	    	'lng' 			=> $longitude != 'null' ? $longitude : null,
	    	'phone' 		=> $data['Phone'] != 'null' ? $data['Phone'] : '',
	    	'fax' 			=> $data['Fax'] != 'null' ? $data['Fax'] : '',
	    	'email' 		=> $data['Email'] != 'null' ? $data['Email'] : '',
	    	// 'open_hours' 	=> '{"mon":"0","tue":"0","wed":"0","thu":"0","fri":"0","sat":"0","sun":"0"}',
	    ];
	    if ($wpdb->update(ASL_PREFIX.'stores', $formatted, ['id' => $data['StoreID']])) {
	    	$storeID = $data['StoreID'];
	    	return $storeID . '|' . $latitude . '|' . $longitude;
	    } else {
	    	return '';
	    }
	}

	public function delete_stores($storeIDs) {
		if ($storeIDs) {
			global $wpdb;
			$wpdb->query('delete from ' . ASL_PREFIX.'stores' . ' where id in (' . $storeIDs . ')');
			return 'Successfully removed stores with IDs of '. $storeIDs;
		}
		return 'Nothing deleted';
	}
}