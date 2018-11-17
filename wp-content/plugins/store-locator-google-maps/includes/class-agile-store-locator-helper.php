<?php



class AgileStoreLocator_Helper {


	public static function getLnt($_address,$key,$debug = false) {

		$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($_address);

		if($key) {
			$url .= '&key='.$key;
		}

		

		$crl = curl_init();
		
		curl_setopt($crl, CURLOPT_URL, $url );                                                               
		curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);             
		curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, 0);                
		
		$result = curl_exec($crl);
		curl_close($crl);
		$result = json_decode($result);
		

		//Debug
		if($debug) {

			return $result;
		}

		if(isset($result->results[0])) {

			$result1=$result->results[0];

			$result1 = array(
				'address'=> $result1->formatted_address,
				'lat' => $result1->geometry->location->lat,
				'lng' => $result1->geometry->location->lng
			);
			return $result1;
		}
		else
			return array();
	}

	public static function fix_backward_compatible()
	{
		
		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		$prefix 	 = $wpdb->prefix."asl_";
		$table_name  = ASL_PREFIX."stores_timing";
		$store_table = ASL_PREFIX."stores";
		$database    = $wpdb->dbname;


		//Add Open Hours Column		
		$sql 	= "SELECT count(*) as c FROM information_schema.COLUMNS WHERE TABLE_NAME = '{$store_table}' AND COLUMN_NAME = 'open_hours';";// AND TABLE_SCHEMA = '{$database}'
		$result = $wpdb->get_results($sql);
		
		if($result[0]->c == 0) {

			$wpdb->query("ALTER TABLE {$store_table} ADD open_hours text;");
		}
		else {
			
			return;
		}


		//Check if Exist
		/*
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			return;
		}
		*/
		


		//Convert All Timings
		$stores = $wpdb->get_results("SELECT s.`id` , s.`start_time`, s.`time_per_day` , s.`end_time`, t.* FROM {$store_table} s LEFT JOIN {$table_name} t ON s.`id` = t.`store_id`");
		
		foreach($stores as $timing) {

			$time_object = new \stdClass();
			$time_object->mon = array();
			$time_object->tue = array();
			$time_object->wed = array();
			$time_object->thu = array();
			$time_object->fri = array();
			$time_object->sat = array();
			$time_object->sun = array();
			

			if($timing->time_per_day == '1') {

				if($timing->start_time_0 && $timing->end_time_0) {

					$time_object->sun[] = $timing->start_time_0 .' - '. $timing->end_time_0;
				}

				if($timing->start_time_1 && $timing->end_time_1) {

					$time_object->mon[] = $timing->start_time_1 .' - '. $timing->end_time_1;
				}

				if($timing->start_time_2 && $timing->end_time_2) {

					$time_object->tue[] = $timing->start_time_2 .' - '. $timing->end_time_2;
				}


				if($timing->start_time_3 && $timing->end_time_3) {

					$time_object->wed[] = $timing->start_time_3 .' - '. $timing->end_time_3;
				}

				if($timing->start_time_4 && $timing->end_time_4) {

					$time_object->thu[] = $timing->start_time_4 .' - '. $timing->end_time_4;
				}

				if($timing->start_time_5 && $timing->end_time_5) {

					$time_object->fri[] = $timing->start_time_5 .' - '. $timing->end_time_5;
				}

				if($timing->start_time_6 && $timing->end_time_6) {

					$time_object->sat[] = $timing->start_time_6 .' - '. $timing->end_time_6;
				}
			}
			else if(trim($timing->start_time) && trim($timing->end_time)) {

				$time_object->mon[] = $time_object->sun[] = $time_object->tue[] = $time_object->wed[] = $time_object->thu[] =$time_object->fri[] = $time_object->sat[] = trim($timing->start_time) .' - '. trim($timing->end_time);
			}
			else {

				$time_object->mon = $time_object->tue = $time_object->wed = $time_object->thu = $time_object->fri = $time_object->sat = $time_object->sun = '1';
			}
			
			$time_object = json_encode($time_object);

			//Update new timings
			$wpdb->update(ASL_PREFIX."stores",
				array('open_hours'	=> $time_object),
				array('id' => $timing->id)
			);
		}


		$sql = "DROP TABLE IF EXISTS `".$table_name."`;";
		$wpdb->query( $sql );
	}

	public static function getaddress($lat,$lng) {

		$url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng);

		$json = @file_get_contents($url);
		$data=json_decode($json);
		$status = $data->status;
		if($status=="OK")
		return $data->results[0]->formatted_address;
		else
		return false;
	}

	public static function getCoordinates($street,$city,$state,$zip,$country,$key)
	{
		$params = array(
			'address' => $street,'city'=> $city, 'state'=> $state,'postcode'=> $zip, 'country' => $country
		);

		if($params['postcode'] && $params['city']) {

			$_address = $params['address'].', '.$params['postcode'].'  '.$params['city'].' '.$params['state'].' '.$params['country'];
			$response = self::getLnt($_address,$key);
			
			if(/*$response['address'] && */isset($response['lng']) && $response['lng'] && isset($response['lat']) && $response['lat']) {
				
				return $response;
			}
			else {
				return null;
			}
		}
		else
		{
			return null;
		}
		
		return true;
	}

	public static function create_zip($files = array(),$destination = '',$overwrite = false) {
		
		//if the zip file already exists and overwrite is false, return false
		if(file_exists($destination) && !$overwrite) { return false; }
		

		//vars
		$valid_files = array();
		//if files were passed in...
		if(is_array($files)) {
			//cycle through each file
			foreach($files as $file) {

				//make sure the file exists
				if(file_exists($file)) {
					$valid_files[] = $file;
				}
			}
		}

		//if we have good files...
		if(count($valid_files)) {
			//create the archive
			$zip = new ZipArchive();
			//if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				
				return false;
			}

			//add the files
			foreach($valid_files as $file) {

				$relativePath = str_replace(ASL_PLUGIN_PATH.'public/', '', $file);
				$zip->addFile($file,$relativePath);
			}
			//debug
			//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
			
			//close the zip -- done!
			$zip->close();
			
			//check to make sure the file exists
			return file_exists($destination);
		}
		else
		{
			return false;
		}
	}

	public static function extract_assets($zip_path) {

		if(!file_exists($zip_path)) {
			return false;
		}

		$zip = new ZipArchive();
		
		if ($zip->open($zip_path) === true) {

		    $allow_exts = array('jpg','png','jpeg','JPG','gif','svg','SVG');	
		    
		    for($i = 0; $i < $zip->numFiles; $i++) {
		        
		        $a_file = $zip->getNameIndex($i);
		        
		        $extension  = explode('.', $a_file);
		        $extension  = $extension[count($extension) - 1];

		        //Extract only allowed extension
				if(in_array($extension, $allow_exts)) {

					$zip->extractTo(ASL_PLUGIN_PATH.'public', array($a_file));
				}
		    }  

		    //Close the connection                 
		    $zip->close();   

		    return true;                
		}

		return false;
	}
}

?>
