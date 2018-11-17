<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://agilelogix.com
 * @since      1.0.0
 *
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/admin
 * @author     Your Name <support@agilelogix.com>
 */
class AgileStoreLocator_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $AgileStoreLocator    The ID of this plugin.
	 */
	private $AgileStoreLocator;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $AgileStoreLocator       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $AgileStoreLocator, $version ) {

		$this->AgileStoreLocator = $AgileStoreLocator;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in AgileStoreLocator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The AgileStoreLocator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->AgileStoreLocator, ASL_URL_PATH . 'public/css/bootstrap.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'asl_chosen_plugin', ASL_URL_PATH . 'public/css/chosen.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'asl_admin', ASL_URL_PATH . 'admin/css/style.css', array(), $this->version, 'all' );
        
		wp_enqueue_style( 'asl_datatable1', ASL_URL_PATH . 'public/datatable/media/css/demo_page.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'asl_datatable2', ASL_URL_PATH . 'public/datatable/media/css/jquery.dataTables.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in AgileStoreLocator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The AgileStoreLocator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		///wp_enqueue_script( $this->AgileStoreLocator, ASL_URL_PATH . 'public/js/jquery-1.11.3.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->AgileStoreLocator.'-lib', ASL_URL_PATH . 'public/js/libs.min.js', array('jquery'), $this->version, false );
		wp_enqueue_script( $this->AgileStoreLocator.'-choosen', ASL_URL_PATH . 'public/js/chosen.proto.min.js', array('jquery'), $this->version, false );
		wp_enqueue_script( $this->AgileStoreLocator.'-datatable', ASL_URL_PATH . 'public/datatable/media/js/jquery.dataTables.min.js', array('jquery'), $this->version, false );
		wp_enqueue_script( 'asl-bootstrap', ASL_URL_PATH . 'public/js/bootstrap.min.js', array('jquery'), $this->version, false );
		wp_enqueue_script( $this->AgileStoreLocator.'-upload', ASL_URL_PATH . 'admin/js/jquery.fileupload.min.js', array('jquery'), $this->version, false );
		wp_enqueue_script( $this->AgileStoreLocator.'-jscript', ASL_URL_PATH . 'admin/js/jscript.js', array('jquery'), $this->version, false );
		wp_enqueue_script( $this->AgileStoreLocator.'-draw', ASL_URL_PATH . 'public/js/drawing.js', array('jquery'), $this->version, false );
		wp_localize_script( $this->AgileStoreLocator.'-jscript', 'ASL_REMOTE', array( 'URL' => admin_url( 'admin-ajax.php' ),'1.1', true ));


		//Media Uploader
		//wp_enqueue_media();
    	//wp_enqueue_script( $this->AgileStoreLocator.'-uploader', DIR_URL . 'wp_media_uploader.js', array( 'jquery' ), 1.0 );
	}

	public function upload_logo() {

		$response = new \stdclass();
		$response->success = false;


		$uniqid = uniqid();
		$target_dir  = ASL_PLUGIN_PATH."public/Logo/";
	 	//$target_file = $uniqid.'_'. strtolower($_FILES["files"]["name"]);

		$imageFileType = explode('.', $_FILES["files"]["name"]);
		$imageFileType = $imageFileType[count($imageFileType) - 1];

	 	$target_file = $uniqid.'_logo.'.$imageFileType;
	 	$target_name = isset($_POST['data']['logo_name'])?$_POST['data']['logo_name']:('Logo '.time());

	
		//if file not found
		/*
		if (file_exists($target_name)) {
		    $response->message = "Sorry, file already exists.";
		}
		*/

		//to big size
		if ($_FILES["files"]["size"] >  5000000) {
		    $response->message = "Sorry, your file is too large.";
		}
		// not a valid format
		else if(!in_array($imageFileType, array('jpg','png','jpeg','gif','JPG'))) {
		    $response->message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		}
		// upload 
		else if(move_uploaded_file($_FILES["files"]["tmp_name"], $target_dir.$target_file)) {

			global $wpdb;
			$wpdb->insert(ASL_PREFIX.'storelogos', 
			 	array('path'=>$target_file,'name'=>$target_name),
			 	array('%s','%s'));

      		$response->list = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."storelogos ORDER BY id DESC");
      	 	$response->message = "The file has been uploaded.";
      	 	$response->success = true;
		}
		//error
		else {

			$response->message = 'Some Error Occured';
		}

		echo json_encode($response);
	    die;
	}

	/*Import Assets such as Logo, Icons, Markers*/
	public function import_assets() {

		$response = new \stdclass();
		$response->success = false;


		//Validate Admin
		if(!current_user_can('administrator')) {

			$response->error = 'Please login with Administrator Account.';
			echo json_encode($response);die;
		}

		$target_dir  = ASL_PLUGIN_PATH."public/export/";
		$target_file = 'assets_'.uniqid().'.zip';

	 	
	 	/*Move the File to the Import Folder*/
		if(move_uploaded_file($_FILES["files"]["tmp_name"], $target_dir.$target_file)) {

			require_once ASL_PLUGIN_PATH . 'includes/class-agile-store-locator-helper.php';

			$response->success = true;
			
			if(AgileStoreLocator_Helper::extract_assets($target_dir.$target_file)) {

				$response->msg = 'Assets Imported Successfully.';
			}
			else
				$response->msg = 'Failed to Imported Assets.';

			
		}
		//error
		else {

			$response->error = 'Error, file not moved, check permission.';
		}

		echo json_encode($response);die;
	}

	/*POST METHODS*/
	public function add_new_store() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$form_data = stripslashes_deep($_REQUEST['data']);
		

		
		//insert into stores table
		if($wpdb->insert( ASL_PREFIX.'stores', $form_data))
		{
			$response->success = true;
			$store_id = $wpdb->insert_id;


			/*
			//Add THE STORE TIMINGS
			if($form_data['time_per_day'] == '1') {

				$datatime = $_REQUEST['datatime'];
				$datatime['store_id'] = $store_id;
				$wpdb->insert( ASL_PREFIX.'stores_timing', $datatime);
			}
			else
				$wpdb->insert( ASL_PREFIX.'stores_timing', array('store_id' => $store_id));
			*/

				/*Save Categories*/
			if(is_array($_REQUEST['category']))
				foreach ($_REQUEST['category'] as $category) {	

				$wpdb->insert(ASL_PREFIX.'stores_categories', 
				 	array('store_id'=>$store_id,'category_id'=>$category),
				 	array('%s','%s'));			
			}

			$response->msg = 'Store Added Successfully.';
		}
		else {

			$wpdb->show_errors = true;

			$response->error = 'Error occurred while saving Store';
			$response->msg   = $wpdb->print_error();
		}
		
		echo json_encode($response);die;	
	}

	//update Store
	public function update_store() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$form_data = stripslashes_deep($_REQUEST['data']);
		$update_id = $_REQUEST['updateid'];
		
		//update into stores table
		$wpdb->update(ASL_PREFIX."stores",
			array(
				'title'			=> $form_data['title'],
				'description'	=> $form_data['description'],
				'phone'			=> $form_data['phone'],
				'fax'			=> $form_data['fax'],
				'email'			=> $form_data['email'],
				'street'		=> $form_data['street'],
				'postal_code'	=> $form_data['postal_code'],
				'city'			=> $form_data['city'],
				'state'			=> $form_data['state'],
				'lat'			=> $form_data['lat'],
				'lng'			=> $form_data['lng'],
				'website'		=> $form_data['url'],
				'country'		=> $form_data['country'],
				'is_disabled'	=> $form_data['is_disabled'],
				'description_2'	=> $form_data['description_2'],
				'logo_id'		=> $form_data['logo_id'],
				'marker_id'		=> $form_data['marker_id'],
				/*'start_time'	=> $form_data['start_time'],
				'end_time'		=> $form_data['end_time'],*/
				'logo_id'		=> $form_data['logo_id'],
				'open_hours'	=> $form_data['open_hours'],
				'ordr'			=> $form_data['ordr'],
				/*
				'days'			=> $form_data['days'],
				'time_per_day' => $form_data['time_per_day'],
				*/
				'updated_on' 	=> date('Y-m-d H:i:s')
			),
			array('id' => $update_id)
		);

		$sql = "DELETE FROM ".ASL_PREFIX."stores_categories WHERE store_id = ".$update_id;
		$wpdb->query($sql);

			if(is_array($_REQUEST['category']))
			foreach ($_REQUEST['category'] as $category) {	

			$wpdb->insert(ASL_PREFIX.'stores_categories', 
			 	array('store_id'=>$update_id,'category_id'=>$category),
			 	array('%s','%s'));	
		}


		/*
		//ADD THE TIMINGS
		$timing_result = $wpdb->get_results("SELECT count(*) as c FROM ".ASL_PREFIX."stores_timing WHERE store_id = $update_id");

		//INSERT OR UPDATE
		if($timing_result[0]->c == 0) {
			
			$datatime = $_REQUEST['datatime'];
			$datatime['store_id'] = $update_id;
			$wpdb->insert( ASL_PREFIX.'stores_timing', $datatime);
		}

		else {

			$datatime = $_REQUEST['datatime'];
			$wpdb->update( ASL_PREFIX.'stores_timing', $datatime,array('store_id' => $update_id));
		}
		*/


		$response->success = true;


		$response->msg = 'Store Updated Successfully.';


		echo json_encode($response);die;
	}


	//To delete the store/stores
	public function delete_store() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$multiple = $_REQUEST['multiple'];
		$delete_sql;

		if($multiple) {

			$item_ids 		 = implode(",",$_POST['item_ids']);
			$delete_sql 	 = "DELETE FROM ".ASL_PREFIX."stores WHERE id IN (".$item_ids.")";
		}
		else {

			$store_id 		 = $_REQUEST['store_id'];
			$delete_sql 	 = "DELETE FROM ".ASL_PREFIX."stores WHERE id = ".$store_id;
		}


		if($wpdb->query($delete_sql)) {

			$response->success = true;
			$response->msg = ($multiple)?'Stores deleted successfully.':'Store deleted successfully.';
		}
		else {
			$response->error = 'Error occurred while saving record';//$form_data
			$response->msg   = $wpdb->show_errors();
		}
		
		echo json_encode($response);die;
	}


	//To Change the Status of Store
	public function store_status() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$status = (isset($_REQUEST['status']) && $_REQUEST['status'] == '1')?'0':'1';
		$status_title 	 = ($status == '1')?'Disabled':'Enabled'; 
		$delete_sql;

		$item_ids 		 = implode(",",$_POST['item_ids']);
		$update_sql 	 = "UPDATE ".ASL_PREFIX."stores SET is_disabled = {$status} WHERE id IN (".$item_ids.")";

		if($wpdb->query($update_sql)) {

			$response->success = true;
			$response->msg = 'Selected Stores '.$status_title;
		}
		else {
			$response->error = 'Error occurred while Changing Status';
			$response->msg   = $wpdb->show_errors();
		}
		
		echo json_encode($response);die;
	}
	
	//to  Duplicate the store
	public function duplicate_store() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$store_id = $_REQUEST['store_id'];


		$result = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."stores WHERE id = ".$store_id);		

		if($result && $result[0]) {

			$result = (array)$result[0];

			unset($result['id']);
			unset($result['created_on']);
			unset($result['updated_on']);

			//insert into stores table
			if($wpdb->insert( ASL_PREFIX.'stores', $result)){
				$response->success = true;
				$new_store_id = $wpdb->insert_id;

				//get categories and copy them
				$s_categories = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."stores_categories WHERE store_id = ".$store_id);

				/*Save Categories*/
				foreach ($s_categories as $_category) {	

					$wpdb->insert(ASL_PREFIX.'stores_categories', 
					 	array('store_id'=>$new_store_id,'category_id'=>$_category->category_id),
					 	array('%s','%s'));			
				}

				/*
				//Copy the timing of Store
				$timing = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."stores_timing WHERE store_id = $store_id");


				$timing = ($timing)?(array)$timing[0]:array();
				$timing['store_id'] = $new_store_id;

				$wpdb->insert( ASL_PREFIX.'stores_timing', $timing);
				*/

				//SEnd the response
				$response->msg = 'Store Duplicated successfully.';
			}
			else
			{
				$response->error = 'Error occurred while saving Store';//$form_data
				$response->msg   = $wpdb->show_errors();
			}	

		}

		echo json_encode($response);die;
	}

	

	/////////////////////////////////ALL Category Methods
	/*Categories methods*/
	public function add_category() {

		global $wpdb;

		$response = new \stdclass();
			$response->success = false;

			$target_dir  = ASL_PLUGIN_PATH."public/svg/";
			$namefile 	 = substr(strtolower($_FILES["files"]["name"]), 0, strpos(strtolower($_FILES["files"]["name"]), '.'));
			

			$imageFileType = pathinfo($_FILES["files"]["name"],PATHINFO_EXTENSION);
		 	$target_name   = uniqid();
			
			//add extension
			$target_name .= '.'.$imageFileType;

			///CREATE DIRECTORY IF NOT EXISTS
			if(!file_exists($target_dir)) {

				mkdir( $target_dir, 0775, true );
			}
			

			/*//if file not found
			if (file_exists($target_name)) {
			    $response->message = "Sorry, file already exists.";
			}
			//to big size
			else */
			if ($_FILES["files"]["size"] >  5000000) {
			    $response->message = "Sorry, your file is too large.";
			}
			// not a valid format
			else if(!in_array($imageFileType, array('jpg','png','jpeg','JPG','gif','svg','SVG'))) {
			    $response->message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
			}
			// upload 
			else if(move_uploaded_file($_FILES["files"]["tmp_name"], $target_dir.$target_name)) {
				
				$form_data = $_REQUEST['data'];

				if($wpdb->insert(ASL_PREFIX.'categories', 
			 	array(	'category_name' => $form_data['category_name'],			 		
						'is_active'		=> 1,
						'icon'			=> $target_name
			 		),
			 	array('%s','%d','%s'))
				)
				{
					$response->message = "Category Added Successfully";
	  	 			$response->success = true;
				}
				else
				{
					$response->message = 'Error occurred while saving record';//$form_data
					
				}
	      	 	
			}
			//error
			else {

				$response->message = 'Some error occured';
			}

			echo json_encode($response);
		    die;
	}

	//delete category/categories
	public function delete_category() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$multiple = $_REQUEST['multiple'];
		$delete_sql;$cResults;

		if($multiple) {

			$item_ids 		 = implode(",",$_POST['item_ids']);
			$delete_sql 	 = "DELETE FROM ".ASL_PREFIX."categories WHERE id IN (".$item_ids.")";
			$cResults 		 = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."categories WHERE id IN (".$item_ids.")");
		}
		else {

			$category_id 	 = $_REQUEST['category_id'];
			$delete_sql 	 = "DELETE FROM ".ASL_PREFIX."categories WHERE id = ".$category_id;
			$cResults 		 = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."categories WHERE id = ".$category_id );
		}


		if(count($cResults) != 0) {
			
			if($wpdb->query($delete_sql))
			{
					$response->success = true;
					foreach($cResults as $c) {

						$inputFileName = ASL_PLUGIN_PATH.'public/icon/'.$c->icon;
					
						if(file_exists($inputFileName) && $c->icon != 'default.png') {	
									
							unlink($inputFileName);
						}
					}							
			}
			else
			{
				$response->error = 'Error occurred while deleting record';//$form_data
				$response->msg   = $wpdb->show_errors();
			}
		}
		else
		{
			$response->error = 'Error occurred while deleting record';
		}

		if($response->success)
			$response->msg = ($multiple)?'Categories deleted successfully.':'Category deleted successfully.';
		
		echo json_encode($response);die;
	}


	//update category with icon
	public function update_category() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$data = $_REQUEST['data'];
		
		
		// with icon
		if($data['action'] == "notsame") {

			$target_dir  = ASL_PLUGIN_PATH."public/svg/";

			$namefile 	 = substr(strtolower($_FILES["files"]["name"]), 0, strpos(strtolower($_FILES["files"]["name"]), '.'));
			

			$imageFileType = pathinfo($_FILES["files"]["name"],PATHINFO_EXTENSION);
		 	$target_name   = uniqid();
			
			
			//add extension
			$target_name .= '.'.$imageFileType;

		 	
			$res = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."categories WHERE id = ".$data['category_id']);

			

		 	if ($_FILES["files"]["size"] >  5000000) {
			    $response->msg = "Sorry, your file is too large.";
			    
			    
			}
			// not a valid format
			else if(!in_array($imageFileType, array('jpg','png','gif','jpeg','JPG','svg','SVG'))) {
			    $response->msg = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
			    
			}
			// upload 
			else if(move_uploaded_file($_FILES["files"]["tmp_name"], $target_dir.$target_name)) {
				//delete previous file

					
					$update_params = array( 'category_name'=> $data['category_name'], 'icon'=> $target_name);
				
					$wpdb->update(ASL_PREFIX."categories",$update_params,array('id' => $data['category_id']),array( '%s' ), array( '%d' ));		
					$response->msg = 'Updated Successfully.';
					$response->success = true;
					if (file_exists($target_dir.$res[0]->icon)) {	
						unlink($target_dir.$res[0]->icon);
					}
			}
			//error
			else {

				$response->msg = 'Some error occured';
				
			}

		}
		//without icon
		else {

			$wpdb->update(ASL_PREFIX."categories",array( 'category_name'=> $data['category_name']),array('id' => $data['category_id']),array( '%s' ), array( '%d' ));		
			$response->msg = 'Updated Successfully.';
			$response->success = true;	

		}
		
		echo json_encode($response);die;
	}


	//get category by id
	public function get_category_by_id() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$store_id = $_REQUEST['category_id'];
		

		$response->list = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."categories WHERE id = ".$store_id);

		if(count($response->list)!=0){

			$response->success = true;

		}
		else{
			$response->error = 'Error occurred while geting record';//$form_data

		}
		echo json_encode($response);die;
	}


	/*GET the Categories*/
	public function get_categories() {

		global $wpdb;
		$start = isset( $_REQUEST['iDisplayStart'])?$_REQUEST['iDisplayStart']:0;		
		$params  = isset($_REQUEST)?$_REQUEST:null; 	

		$acolumns = array(
			'id','id','category_name','is_active','icon','created_on'
		);

		$columnsFull = $acolumns;

		$clause = array();

		if(isset($_REQUEST['filter'])) {

			foreach($_REQUEST['filter'] as $key => $value) {

				if($value != '') {

					if($key == 'is_active')
					{
						$value = ($value == 'yes')?1:0;
					}

					$clause[] = "$key like '%{$value}%'";
				}
			}	
		} 
		
		
		//iDisplayStart::Limit per page
		$sLimit = "";
		if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".intval( $_REQUEST['iDisplayStart'] ).", ".
				intval( $_REQUEST['iDisplayLength'] );
		}

		/*
		 * Ordering
		 */
		$sOrder = "";
		if ( isset( $_REQUEST['iSortCol_0'] ) )
		{
			$sOrder = "ORDER BY  ";

			for ( $i=0 ; $i < intval( $_REQUEST['iSortingCols'] ) ; $i++ )
			{
				if (isset($_REQUEST['iSortCol_'.$i]))
				{
					$sOrder .= "`".$acolumns[ intval( $_REQUEST['iSortCol_'.$i] )  ]."` ".$_REQUEST['sSortDir_0'];
					break;
				}
			}
			

			//$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
		}


		$sWhere = implode(' AND ',$clause);
		
		if($sWhere != '')$sWhere = ' WHERE '.$sWhere;
		
		$fields = implode(',', $columnsFull);
		
		###get the fields###
		$sql = 	"SELECT $fields FROM ".ASL_PREFIX."categories";

		$sqlCount = "SELECT count(*) 'count' FROM ".ASL_PREFIX."categories";

		/*
		 * SQL queries
		 * Get data to display
		 */
		$sQuery = "{$sql} {$sWhere} {$sOrder} {$sLimit}";
		$data_output = $wpdb->get_results($sQuery);
		
		/* Data set length after filtering */
		$sQuery = "{$sqlCount} {$sWhere}";
		$r = $wpdb->get_results($sQuery);
		$iFilteredTotal = $r[0]->count;
		
		$iTotal = $iFilteredTotal;

	    /*
		 * Output
		 */
		$sEcho = isset($_REQUEST['sEcho'])?intval($_REQUEST['sEcho']):1;
		$output = array(
			"sEcho" => intval($_REQUEST['sEcho']),
			//"test" => $test,
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iFilteredTotal,
			"aaData" => array()
		);
		
		foreach($data_output as $aRow)
	    {
	    	$row = $aRow;

	    	if($row->is_active == 1) {

	        	 $row->is_active = 'Yes';
	        }	       
	    	else {

	    		$row->is_active = 'No';	
	    	}

	    	$row->icon = "<img  src='".ASL_URL_PATH."public/svg/".$row->icon."' alt=''  style='width:20px'/>";	

	    	$row->action = '<div class="edit-options"><span data-id="'.$row->id.'" title="Edit" class="glyphicon glyphicon-edit edit_category"></span> &nbsp; | &nbsp;<span title="Delete" data-id="'.$row->id.'" class="glyphicon glyphicon-trash delete_category"></span></div>';
	    	$row->check  = '<input type="checkbox" data-id="'.$row->id.'" >';
	        $output['aaData'][] = $row;
	    }

		echo json_encode($output);die;
	}


	///////////////////////////ALL Markers Methods//////////////////////
	//upload marker into icon folder
	public function upload_marker() {

		$response = new \stdclass();
		$response->success = false;


		$uniqid = uniqid();
		$target_dir  = ASL_PLUGIN_PATH."public/icon/";
	 	$target_file = $uniqid.'_'. strtolower($_FILES["files"]["name"]);
	 	$target_name = isset($_POST['data']['marker_name'])?$_POST['data']['marker_name']:('Marker '.time());
		
			
		$imageFileType = explode('.', $_FILES["files"]["name"]);
		$imageFileType = $imageFileType[count($imageFileType) - 1];


	
		//if file not found
		/*
		if (file_exists($target_name)) {
		    $response->message = "Sorry, file already exists.";
		}
		*/

		//to big size
		if ($_FILES["files"]["size"] >  22085) {
		    $response->message = "Marker Image too Large, Best Size is 32 x 32.";
		    $response->size = $_FILES["files"]["size"];
		}
		// not a valid format
		else if(!in_array($imageFileType, array('jpg','png','jpeg','gif','JPG'))) {
		    $response->message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		}
		// upload 
		else if(move_uploaded_file($_FILES["files"]["tmp_name"], $target_dir.$target_file)) {

			global $wpdb;
			$wpdb->insert(ASL_PREFIX.'markers', 
			 	array('icon'=>$target_file,'marker_name'=>$target_name),
			 	array('%s','%s'));

      		$response->list = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."markers ORDER BY id DESC");
      	 	$response->message = "The file has been uploaded.";
      	 	$response->success = true;
		}
		//error
		else {

			$response->message = 'Some Error Occured';
		}

		echo json_encode($response);
	    die;
	}


	/*Markers methods*/
	public function add_marker() {

		global $wpdb;

		$response = new \stdclass();
			$response->success = false;

			$target_dir  = ASL_PLUGIN_PATH."public/icon/";
			

			$imageFileType = pathinfo($_FILES["files"]["name"],PATHINFO_EXTENSION);
		 	$target_name   = uniqid();
			
			//add extension
			$target_name .= '.'.$imageFileType;

			///CREATE DIRECTORY IF NOT EXISTS
			if(!file_exists($target_dir)) {

				mkdir( $target_dir, 0775, true );
			}
			

			/*//if file not found
			if (file_exists($target_name)) {
			    $response->message = "Sorry, file already exists.";
			}
			//to big size
			else */
			if ($_FILES["files"]["size"] >  5000000) {
			    $response->message = "Sorry, your file is too large.";
			}
			// not a valid format
			else if(!in_array($imageFileType, array('jpg','png','gif','jpeg','JPG'))) {
			    $response->message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
			}
			// upload 
			else if(move_uploaded_file($_FILES["files"]["tmp_name"], $target_dir.$target_name)) {
				
				$form_data = $_REQUEST['data'];

				if($wpdb->insert(ASL_PREFIX.'markers', 
			 	array(	'marker_name' => $form_data['marker_name'],			 		
						'is_active'		=> 1,
						'icon'			=> $target_name
			 		),
			 	array('%s','%d','%s'))
				)
				{
					$response->message = "Marker Added Successfully";
	  	 			$response->success = true;
				}
				else
				{
					$response->message = 'Error occurred while saving record';//$form_data
					
				}
	      	 	
			}
			//error
			else {

				$response->message = 'Some error occured';
			}

			echo json_encode($response);
		    die;
	}

	//delete marker
	public function delete_marker() {
		
		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$multiple = $_REQUEST['multiple'];
		$delete_sql;$mResults;

		if($multiple) {

			$item_ids 		 = implode(",",$_POST['item_ids']);
			$delete_sql 	 = "DELETE FROM ".ASL_PREFIX."markers WHERE id IN (".$item_ids.")";
			$mResults 		 = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."markers WHERE id IN (".$item_ids.")");
		}
		else {

			$item_id 		 = $_REQUEST['marker_id'];
			$delete_sql 	 = "DELETE FROM ".ASL_PREFIX."markers WHERE id = ".$item_id;
			$mResults 		 = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."markers WHERE id = ".$item_id );
		}


		if(count($mResults) != 0) {
			
			if($wpdb->query($delete_sql)) {

					$response->success = true;

					foreach($mResults as $m) {

						$inputFileName = ASL_PLUGIN_PATH.'public/icon/'.$m->icon;
					
						if(file_exists($inputFileName) && $m->icon != 'default.png') {	
									
							unlink($inputFileName);
						}
					}							
			}
			else
			{
				$response->error = 'Error occurred while deleting record';//$form_data
				$response->msg   = $wpdb->show_errors();
			}
		}
		else
		{
			$response->error = 'Error occurred while deleting record';
		}

		if($response->success)
			$response->msg = ($multiple)?'Markers deleted successfully.':'Marker deleted successfully.';
		
		echo json_encode($response);die;
	}



	//update marker with icon
	public function update_marker() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$data = $_REQUEST['data'];
		
		
		// with icon
		if($data['action'] == "notsame") {

			$target_dir  = ASL_PLUGIN_PATH."public/icon/";

			$namefile 	 = substr(strtolower($_FILES["files"]["name"]), 0, strpos(strtolower($_FILES["files"]["name"]), '.'));
			

			$imageFileType = pathinfo($_FILES["files"]["name"],PATHINFO_EXTENSION);
		 	$target_name   = uniqid();
			
			
			//add extension
			$target_name .= '.'.$imageFileType;

		 	
			$res = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."markers WHERE id = ".$data['marker_id']);

			

		 	if ($_FILES["files"]["size"] >  5000000) {
			    $response->msg = "Sorry, your file is too large.";
			    
			    
			}
			// not a valid format
			else if(!in_array($imageFileType, array('jpg','png','jpeg','gif','JPG'))) {
			    $response->msg = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
			    
			}
			// upload 
			else if(move_uploaded_file($_FILES["files"]["tmp_name"], $target_dir.$target_name)) {
				//delete previous file

				
				
					$wpdb->update(ASL_PREFIX."markers",array( 'marker_name'=> $data['marker_name'], 'icon'=> $target_name),array('id' => $data['marker_id']),array( '%s' ), array( '%d' ));		
					$response->msg = 'Updated Successfully.';
					$response->success = true;
					if (file_exists($target_dir.$res[0]->icon)) {	
						unlink($target_dir.$res[0]->icon);
					}
			}
			//error
			else {

				$response->msg = 'Some error occured';
				
			}

		}
		//without icon
		else {

			$wpdb->update(ASL_PREFIX."markers",array( 'marker_name'=> $data['marker_name']),array('id' => $data['marker_id']),array( '%s' ), array( '%d' ));		
			$response->msg = 'Updated Successfully.';
			$response->success = true;	

		}
		
		echo json_encode($response);die;
	}


	//get marker by id
	public function get_marker_by_id() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$store_id = $_REQUEST['marker_id'];
		

		$response->list = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."markers WHERE id = ".$store_id);

		if(count($response->list)!=0){

			$response->success = true;

		}
		else{
			$response->error = 'Error occurred while geting record';//$form_data

		}
		echo json_encode($response);die;
	}

	/*Methods for Logo*/
	public function delete_logo() {
		
		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$multiple = $_REQUEST['multiple'];
		$delete_sql;$mResults;

		if($multiple) {

			$item_ids 		 = implode(",",$_POST['item_ids']);
			$delete_sql 	 = "DELETE FROM ".ASL_PREFIX."storelogos WHERE id IN (".$item_ids.")";
			$mResults 		 = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."storelogos WHERE id IN (".$item_ids.")");
		}
		else {

			$item_id 		 = $_REQUEST['logo_id'];
			$delete_sql 	 = "DELETE FROM ".ASL_PREFIX."storelogos WHERE id = ".$item_id;
			$mResults 		 = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."storelogos WHERE id = ".$item_id );
		}


		if(count($mResults) != 0) {
			
			if($wpdb->query($delete_sql)) {

					$response->success = true;

					foreach($mResults as $m) {

						$inputFileName = ASL_PLUGIN_PATH.'public/Logo/'.$m->path;
					
						if(file_exists($inputFileName) && $m->path != 'default.png') {	
									
							unlink($inputFileName);
						}
					}							
			}
			else
			{
				$response->error = 'Error occurred while deleting record';//$form_data
				$response->msg   = $wpdb->show_errors();
			}
		}
		else
		{
			$response->error = 'Error occurred while deleting record';
		}

		if($response->success)
			$response->msg = ($multiple)?'Logos deleted Successfully.':'Logo deleted Successfully.';
		
		echo json_encode($response);die;
	}



	//update logo with icon
	public function update_logo() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$data = $_REQUEST['data'];
		
		
		// with icon
		if($data['action'] == "notsame") {

			$target_dir  = ASL_PLUGIN_PATH."public/Logo/";

			$namefile 	 = substr(strtolower($_FILES["files"]["name"]), 0, strpos(strtolower($_FILES["files"]["name"]), '.'));
			

			$imageFileType = pathinfo($_FILES["files"]["name"],PATHINFO_EXTENSION);
		 	$target_name   = uniqid();
			
			
			//add extension
			$target_name .= '.'.$imageFileType;

		 	
			$res = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."storelogos WHERE id = ".$data['logo_id']);

			

		 	if ($_FILES["files"]["size"] >  5000000) {
			    
			    $response->msg = "Sorry, your file is too large.";
			}
			// not a valid format
			else if(!in_array($imageFileType, array('jpg','png','jpeg','gif','JPG'))) {
			    $response->msg = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
			    
			}
			// upload 
			else if(move_uploaded_file($_FILES["files"]["tmp_name"], $target_dir.$target_name)) {
				//delete previous file
				
					$wpdb->update(ASL_PREFIX."storelogos",array( 'name'=> $data['logo_name'], 'path'=> $target_name),array('id' => $data['logo_id']),array( '%s' ), array( '%d' ));		
					$response->msg = 'Updated Successfully.';
					$response->success = true;
					if (file_exists($target_dir.$res[0]->icon)) {	
						unlink($target_dir.$res[0]->icon);
					}
			}
			//error
			else {

				$response->msg = 'Some error occured';
				
			}

		}
		//without icon
		else {

			$wpdb->update(ASL_PREFIX."storelogos",array( 'name'=> $data['logo_name']),array('id' => $data['logo_id']),array( '%s' ), array( '%d' ));		
			$response->msg = 'Updated Successfully.';
			$response->success = true;	

		}
		
		echo json_encode($response);die;
	}


	//get logo by id
	public function get_logo_by_id() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$store_id = $_REQUEST['logo_id'];
		

		$response->list = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."storelogos WHERE id = ".$store_id);

		if(count($response->list)!=0){

			$response->success = true;

		}
		else{
			$response->error = 'Error occurred while geting record';//$form_data

		}
		echo json_encode($response);die;
	}


	/*GET the Logos*/
	public function get_logos() {

		global $wpdb;
		$start = isset( $_REQUEST['iDisplayStart'])?$_REQUEST['iDisplayStart']:0;		
		$params  = isset($_REQUEST)?$_REQUEST:null; 	

		$acolumns = array(
			'id','id','name','path'
		);

		$columnsFull = $acolumns;

		$clause = array();

		if(isset($_REQUEST['filter'])) {

			foreach($_REQUEST['filter'] as $key => $value) {

				if($value != '') {

					$clause[] = "$key like '%{$value}%'";
				}
			}	
		} 

		
		
		//iDisplayStart::Limit per page
		$sLimit = "";
		if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".intval( $_REQUEST['iDisplayStart'] ).", ".
				intval( $_REQUEST['iDisplayLength'] );
		}

		/*
		 * Ordering
		 */
		$sOrder = "";
		if ( isset( $_REQUEST['iSortCol_0'] ) )
		{
			$sOrder = "ORDER BY  ";

			for ( $i=0 ; $i < intval( $_REQUEST['iSortingCols'] ) ; $i++ )
			{
				if (isset($_REQUEST['iSortCol_'.$i]))
				{
					$sOrder .= "`".$acolumns[ intval( $_REQUEST['iSortCol_'.$i] )  ]."` ".$_REQUEST['sSortDir_0'];
					break;
				}
			}
			

			//$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
		}
		

		$sWhere = implode(' AND ',$clause);
		
		if($sWhere != '')$sWhere = ' WHERE '.$sWhere;
		
		$fields = implode(',', $columnsFull);
		
		###get the fields###
		$sql = 	"SELECT $fields FROM ".ASL_PREFIX."storelogos";

		$sqlCount = "SELECT count(*) 'count' FROM ".ASL_PREFIX."storelogos";

		/*
		 * SQL queries
		 * Get data to display
		 */
		$sQuery = "{$sql} {$sWhere} {$sOrder} {$sLimit}";
		$data_output = $wpdb->get_results($sQuery);
		
		/* Data set length after filtering */
		$sQuery = "{$sqlCount} {$sWhere}";
		$r = $wpdb->get_results($sQuery);
		$iFilteredTotal = $r[0]->count;
		
		$iTotal = $iFilteredTotal;

	    /*
		 * Output
		 */
		$sEcho = isset($_REQUEST['sEcho'])?intval($_REQUEST['sEcho']):1;
		$output = array(
			"sEcho" => intval($_REQUEST['sEcho']),
			//"test" => $test,
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iFilteredTotal,
			"aaData" => array()
		);
		
		foreach($data_output as $aRow)
	    {
	    	$row = $aRow;


	    	$row->path 	 = '<img src="'.ASL_URL_PATH.'public/Logo/'.$row->path.'"  style="width:50px"/>';	
	    	$row->check  = '<input type="checkbox" data-id="'.$row->id.'" >';
	    	$row->action = '<div class="edit-options"><span data-id="'.$row->id.'" title="Edit" class="glyphicon glyphicon-edit edit_logo"></span> &nbsp; | &nbsp;<span title="Delete" data-id="'.$row->id.'" class="glyphicon glyphicon-trash delete_logo"></span></div>';
	        $output['aaData'][] = $row;
	    }

		echo json_encode($output);die;
	}


	/*GET the Markers*/
	public function get_markers() {

		global $wpdb;
		$start = isset( $_REQUEST['iDisplayStart'])?$_REQUEST['iDisplayStart']:0;		
		$params  = isset($_REQUEST)?$_REQUEST:null; 	

		$acolumns = array(
			'id','id','marker_name','is_active','icon'
		);

		$columnsFull = $acolumns;

		$clause = array();

		if(isset($_REQUEST['filter'])) {

			foreach($_REQUEST['filter'] as $key => $value) {

				if($value != '') {

					if($key == 'is_active')
					{
						$value = ($value == 'yes')?1:0;
					}

					$clause[] = "$key like '%{$value}%'";
				}
			}	
		} 

		
		
		//iDisplayStart::Limit per page
		$sLimit = "";
		if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".intval( $_REQUEST['iDisplayStart'] ).", ".
				intval( $_REQUEST['iDisplayLength'] );
		}

		/*
		 * Ordering
		 */
		$sOrder = "";
		if ( isset( $_REQUEST['iSortCol_0'] ) )
		{
			$sOrder = "ORDER BY  ";

			for ( $i=0 ; $i < intval( $_REQUEST['iSortingCols'] ) ; $i++ )
			{
				if (isset($_REQUEST['iSortCol_'.$i]))
				{
					$sOrder .= "`".$acolumns[ intval( $_REQUEST['iSortCol_'.$i] )  ]."` ".$_REQUEST['sSortDir_0'];
					break;
				}
			}
			

			//$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
		}
		

		$sWhere = implode(' AND ',$clause);
		
		if($sWhere != '')$sWhere = ' WHERE '.$sWhere;
		
		$fields = implode(',', $columnsFull);
		
		###get the fields###
		$sql = 	"SELECT $fields FROM ".ASL_PREFIX."markers";

		$sqlCount = "SELECT count(*) 'count' FROM ".ASL_PREFIX."markers";

		/*
		 * SQL queries
		 * Get data to display
		 */
		$sQuery = "{$sql} {$sWhere} {$sOrder} {$sLimit}";
		$data_output = $wpdb->get_results($sQuery);
		
		/* Data set length after filtering */
		$sQuery = "{$sqlCount} {$sWhere}";
		$r = $wpdb->get_results($sQuery);
		$iFilteredTotal = $r[0]->count;
		
		$iTotal = $iFilteredTotal;

	    /*
		 * Output
		 */
		$sEcho = isset($_REQUEST['sEcho'])?intval($_REQUEST['sEcho']):1;
		$output = array(
			"sEcho" => intval($_REQUEST['sEcho']),
			//"test" => $test,
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iFilteredTotal,
			"aaData" => array()
		);
		
		foreach($data_output as $aRow)
	    {
	    	$row = $aRow;

	    	if($row->is_active == 1) {

	        	 $row->is_active = 'Yes';
	        }	       
	    	else {

	    		$row->is_active = 'No';	
	    	}


	    	$row->icon 	 = "<img  src='".ASL_URL_PATH."public/icon/".$row->icon."' alt=''  style='width:20px'/>";	
	    	$row->check  = '<input type="checkbox" data-id="'.$row->id.'" >';
	    	$row->action = '<div class="edit-options"><span data-id="'.$row->id.'" title="Edit" class="glyphicon glyphicon-edit edit_marker"></span> &nbsp; | &nbsp;<span title="Delete" data-id="'.$row->id.'" class="glyphicon glyphicon-trash delete_marker"></span></div>';
	        $output['aaData'][] = $row;
	    }

		echo json_encode($output);die;
	}

	/*GET List*/
	public function get_store_list() {
		
		global $wpdb;
		$start = isset( $_REQUEST['iDisplayStart'])?$_REQUEST['iDisplayStart']:0;		
		$params  = isset($_REQUEST)?$_REQUEST:null; 	

		$acolumns = array(
			ASL_PREFIX.'stores.id ',ASL_PREFIX.'stores.id ',ASL_PREFIX.'stores.id ','title','description','lat','lng','street','state','city','phone','email','website','postal_code','is_disabled',ASL_PREFIX.'stores.created_on'/*,'country_id'*/
		);

		$columnsFull = array(
			ASL_PREFIX.'stores.id as id',ASL_PREFIX.'stores.id as id',ASL_PREFIX.'stores.id as id','title','description','lat','lng','street','state','city','phone','email','website','postal_code','is_disabled',ASL_PREFIX.'stores.created_on'/*,ASL_PREFIX.'countries.country_name'*/
		);

		

		$clause = array();

		if(isset($_REQUEST['filter'])) {

			foreach($_REQUEST['filter'] as $key => $value) {

				if($value != '') {

					if($key == 'is_disabled')
					{
						$value = ($value == 'yes')?1:0;
					}
					elseif($key == 'marker_id' || $key == 'logo_id')
					{
						
						$clause[] = ASL_PREFIX."stores.{$key} = '{$value}'";
						continue;
					}

						
					$clause[] = ASL_PREFIX."stores.{$key} LIKE '%{$value}%'";
				}
			}	
		} 
		
		

		//iDisplayStart::Limit per page
		$sLimit = "";
		if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".intval( $_REQUEST['iDisplayStart'] ).", ".
				intval( $_REQUEST['iDisplayLength'] );
		}

		/*
		 * Ordering
		 */
		$sOrder = "";
		if ( isset( $_REQUEST['iSortCol_0'] ) )
		{
			$sOrder = "ORDER BY  ";

			for ( $i=0 ; $i < intval( $_REQUEST['iSortingCols'] ) ; $i++ )
			{
				if (isset($_REQUEST['iSortCol_'.$i]))
				{
					$sOrder .= $acolumns[ intval( $_REQUEST['iSortCol_'.$i] )  ]." ".$_REQUEST['sSortDir_0'];
					break;
				}
			}
			

			//$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
		}
		

		$sWhere = implode(' AND ',$clause);
		
		if($sWhere != '')$sWhere = ' WHERE '.$sWhere;
		
		$fields = implode(',', $columnsFull);
		


		$fields .= ',marker_id,logo_id,group_concat(category_id) as categories,iso_code_2';

		###get the fields###
		$sql = 	"SELECT $fields
				 FROM ".ASL_PREFIX."stores 
				LEFT JOIN ".ASL_PREFIX."stores_categories 
				ON ".ASL_PREFIX."stores.id = ".ASL_PREFIX."stores_categories.store_id 
				LEFT JOIN ".ASL_PREFIX."countries 
				ON ".ASL_PREFIX."stores.country = ".ASL_PREFIX."countries.id ";


		$sqlCount = "SELECT count(*) 'count' FROM ".ASL_PREFIX."stores";
		

		/*
		 * SQL queries
		 * Get data to display
		 */
		$sQuery = "{$sql} {$sWhere} GROUP BY ".ASL_PREFIX."stores.id {$sOrder} {$sLimit}";
		
		$data_output = $wpdb->get_results($sQuery);
		//$data_output = $wpdb->get_results( $sql);
			
		/* Data set length after filtering */
		$sQuery = "{$sqlCount} {$sWhere}";
		$r = $wpdb->get_results($sQuery);
		$iFilteredTotal = $r[0]->count;
		
		$iTotal = $iFilteredTotal;


	    /*
		 * Output
		 */
		$sEcho = isset($_REQUEST['sEcho'])?intval($_REQUEST['sEcho']):1;
		$output = array(
			"sEcho" => intval($_REQUEST['sEcho']),
			//"test" => $test,
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iFilteredTotal,
			"aaData" => array()
		);

		$days_in_words = array('0'=>'Sun','1'=>'Mon','2'=>'Tues','3'=>'Wed','4'=>'Thur','5'=>'Fri','6'=>'Sat');
		foreach($data_output as $aRow)
	    {
	    	$row = $aRow;

	    	$edit_url = 'admin.php?page=edit-agile-store&store_id='.$row->id;

	    	$row->action = '<div class="edit-options"><a class="glyphicon glyphicon-duplicate" title="Duplicate" data-id="'.$row->id.'"></a>&nbsp; | &nbsp;<a href="'.$edit_url.'"><span title="Edit" class="glyphicon glyphicon-edit"></span></a> &nbsp; | &nbsp;<span title="Delete" data-id="'.$row->id.'" class="glyphicon glyphicon-trash"></span></div>';
	    	$row->check  = '<input type="checkbox" data-id="'.$row->id.'" >';

	    	//Show country with state
	    	if($row->state && $row->iso_code_2)
	    		$row->state = $row->state.', '.$row->iso_code_2;

	        if($row->is_disabled == 1){

	        	 $row->is_disabled = 'Yes';

	        }	       
	    	else{
	    		$row->is_disabled = 'No';	
	    	}

	    	//Days
	    	/*
	    	if($row->days) {
	    		$days 	  = explode(',',$row->days);
	    		$days_are = array();
	    		
	    		foreach($days as $d) {

	    			$days_are[] = $days_in_words[$d];
	    		}

	    		$row->days = $days_are;
	    	}
	    	*/

	        $output['aaData'][] = $row;

	        //get the categories
	     	if($aRow->categories) {

	     		$categories_ = $wpdb->get_results("SELECT category_name FROM ".ASL_PREFIX."categories WHERE id IN ($aRow->categories)");

	     		$cnames = array();
	     		foreach($categories_ as $cat_)
	     			$cnames[] = $cat_->category_name;

	     		$aRow->categories = implode(', ', $cnames);
	     	}   
	    }

		echo json_encode($output);die;
	}

	//save infobox :: not used
	private function save_infobox() {


		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;


		//Check for asl-p-cont infbox html
		if(isset($_POST['asl-p-cont'])) {


			$infobx_id = $_POST['asl-infobox'];

		    $wpdb->update(ASL_PREFIX."settings",
				array(
					'content' => stripslashes($_POST['asl-p-cont'])
				),
				array('id' => $infobx_id,'type'=> 'infobox')
			);

			$response->msg 	   = "InfoBox has been Updated Successfully.";
			$response->success = true;
		}
		else
			$response->error   = "Error, No InfoBox HTML found.";

      	

		echo json_encode($response);die;
	}

	//save customize map
	public function save_custom_map() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;


		//Check for asl-p-cont infbox html
		if(isset($_POST['data_map'])) {


			$data_map = $_POST['data_map'];

		    $wpdb->update(ASL_PREFIX."settings",
				array('content' => stripslashes($data_map)),
				array('id' => 1,'type'=> 'map'));

			$response->msg 	   = "Map has been Updated Successfully.";
			$response->success = true;
		}
		else
			$response->error   = "Error Occured saving Map.";

      	
		echo json_encode($response);die;			
	}

	//not used
	private function import_markers_bulk() {

		global $wpdb;
		if ($handle = opendir('...\wp-content\plugins\AgileStoreLocator\public\icon')) {

		    while (false !== ($entry = readdir($handle))) {

		        if ($entry != "." && $entry != "..") {
		        	
		        	$name = str_replace('.png', '', $entry);
		        	$name = str_replace('-', ' ', $name);
		        	$name = str_replace('_', ' ', $name);
		        	$name = ucwords($name);
					$wpdb->insert(ASL_PREFIX.'markers', 
					 	array('icon'=>$entry,'marker_name'=>$name,'is_active'=>1),
					 	array('%s','%s'));

		        }
		    }

		    closedir($handle);
		}

	}

	// import store
	public function import_store() {

		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
		
		global $wpdb;


		$response  = new \stdclass();
		$response->success = false;

		$data_ = $_REQUEST['data_'];

		$countries     = $wpdb->get_results("SELECT id,country FROM ".ASL_PREFIX."countries");
		$all_countries = array();

		foreach($countries as $_country) {

			$all_countries[$_country->country] = $_country->id;
		}


		$wpdb->query("SET NAMES utf8");
		//mysql_set_charset("utf8");


		//Get the API KEY
		$api_key = null;
		if($_REQUEST['use_key'] == '1') {

			$sql = "SELECT `key`,`value` FROM ".ASL_PREFIX."configs WHERE `key` = 'api_key'";
			$configs_result = $wpdb->get_results($sql);
			$api_key = $configs_result[0]->value;
		}

		//Create category if true
		$create_category = ($_REQUEST['create_category'] == '1')?true:false;
		
		$response->summary = array();

		/** PHPExcel_IOFactory */
		include ASL_PLUGIN_PATH.'PHPExcel/Classes/PHPExcel/IOFactory.php';
		require_once ASL_PLUGIN_PATH . 'includes/class-agile-store-locator-helper.php';

		$inputFileName = ASL_PLUGIN_PATH.'public/import/'.$data_;
		
		
		$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);


		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		unset($sheetData[1]);
		
		$index 	   = 1;
		$imported  = 0;
		
		foreach($sheetData as $t) {
			
			$logoid = '0';
			$categoryerror = '';

			if($t['O'] != '') {

				$t['O'] = trim($t['O']);
				$logoresult = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."storelogos WHERE name = '".$t['O']."'" );
				if(count($logoresult) != 0) {

					$logoid = $logoresult[0]->id;
				}
			}


			//CREATE CATEGORY IF NOT FOUND
			$categorys = explode("|", $t['P']);

			if($create_category && $t['P'] != '') {
				
				foreach ($categorys as $_cat) {
					
					$_cat = trim($_cat);
					$category_count = $wpdb->get_results("SELECT count(*) AS `count` FROM ".ASL_PREFIX."categories WHERE category_name = '".$_cat."'" );
					
					//IF COUNT 0 create that category
					if($category_count[0]->count == 0) {

						$wpdb->insert(ASL_PREFIX.'categories', 
					 	array(	'category_name' => $_cat,			 		
								'is_active'		=> 1,
								'icon'			=> 'default.png'
					 		),
					 	array('%s','%d','%s'));

					 	$response->summary[] = 'Row: '.$index.', Category Created: '.$_cat;
					}
				}
			}


			if($t['A'] != '') {

				//Check if Lat/Lng is missing and we have address
				if(!trim($t['H']) || !trim($t['I'])) {

					$coordinates = AgileStoreLocator_Helper::getCoordinates($t['C'],$t['D'],$t['E'],$t['F'],$t['G'],$api_key);
					
					if($coordinates) {

						$t['H'] = $coordinates['lat'];
						$t['I'] = $coordinates['lng'];
					}
					else
						$response->summary[] = 'Row: '.$index.", LAT/LNG Fetch Failed";
					
					//sleep(0.2);
				}
				
				$is_update = false;
				$store_id  = null;

				//Open Hours
				$hours_n_days = explode('|', $t['T']);
				$days 		  = array('mon' => '0','tue'=> '0','wed'=> '0','thu'=> '0','fri'=> '0','sat'=> '0','sun'=> '0');

				foreach($hours_n_days as $_day) {

					$day_hours = explode('=', $_day);

					//is Valid Day
					if(isset($days[$day_hours[0]]) && isset($day_hours[1])) {

						$day_ 	   = $day_hours[0];
						$dhours    = $day_hours[1];


						if($dhours === '1') {

							$days[$day_] = '1';
						}
						else if($dhours === '0') {

							$days[$day_] = '0';
						}
						//For Hours of every day
						else {

							$durations = explode(',', $dhours);

							if(count($durations) > 0) {

								//make it array
								$days[$day_] = array();

								foreach($durations as $hours) {

									$timings = explode('-', $hours);

									if(count($timings) == 2)
										$days[$day_][] = trim($timings[0]).' - '.trim($timings[1]);
								}
							}
						} 
					}
				}

				$days = json_encode($days);


				///////Validate if It's Insert or Update by Columns Y//////
				if($t['U'] && !is_nan($t['U'])) {

					$is_update = true;

					if($wpdb->update( ASL_PREFIX.'stores', array(
						'title' => $t['A'],
						'description' => $t['B'],
						'street' => $t['C'],
						'city' => $t['D'],
						'state' => $t['E'],
						'postal_code' => $t['F'],
						'country' => ($all_countries[$t['G']])?$all_countries[$t['G']]:'223', //for united states
						'lat' => $t['H'],
						'lng' => $t['I'],
						'phone' => $t['J'],
						'fax' => $t['K'],
						'email' => $t['L'],
						'website' => $t['M'],					
						'is_disabled' => $t['N'],
						'logo_id' => $logoid,
						'marker_id' => $t['Q'],
						'open_hours' => $days,
						'description_2' => $t['S'],
						'ordr' => $t['V']
					),array('id'=> $t['U'] )))
					{
						$imported++;
					}
				}
				////Insertion
				else if($wpdb->insert( ASL_PREFIX.'stores', array(
					'title' => $t['A'],
					'description' => $t['B'],
					'street' => $t['C'],
					'city' => $t['D'],
					'state' => $t['E'],
					'postal_code' => $t['F'],
					'country' => ($all_countries[$t['G']])?$all_countries[$t['G']]:'223', //for united states
					'lat' => $t['H'],
					'lng' => $t['I'],
					'phone' => $t['J'],
					'fax' => $t['K'],
					'email' => $t['L'],
					'website' => $t['M'],					
					'is_disabled' => $t['N'],
					'logo_id' => $logoid,
					'marker_id' => $t['Q'],
					'open_hours' => $days,
					'description_2' => $t['S'],
					'ordr' => $t['V']
				)))
				{
					$imported++;
				}
				//Error
				else {
					$has_error = true;
					//$response->summary[] = 'Row: '.$index.', Error: '.$wpdb->show_errors();
					$response->summary[] = 'Row: '.$index.', Error: '.$wpdb->print_error();
					//$wpdb->last_error
				}



				//Get the ID
				$store_id = ($is_update)?$t['U']:$wpdb->insert_id;
				
				
				/////////ADD THE CATEGORIES//////////////////
				if($store_id && $t['P'] != '') {
					
					foreach ($categorys as $category) {
						
						$category = trim($category);
						$categoryid = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."categories WHERE category_name = '".$category."'" );
					
						if(count($categoryid) != 0){

							$wpdb->insert(ASL_PREFIX.'stores_categories', 
						 	array('store_id'=>$store_id,'category_id'=>	$categoryid[0]->id),
						 	array('%s','%s'));	

						}
						else
							$response->summary[] = 'Row: '.$index.", Category ".$category." not  found";
					}
				}
				else
					$response->summary[] = 'Row: '.$index.", Category is NULL";


				/////////Add the Custom Timing///////////
				/*
				if($store_id && $time_per_day) {

					$params_custom_time = array('store_id' => $store_id);

					//$custom_time_start) == 7 &&  count($custom_time_end
					for($m = 0; $m < 7; $m++) {

						$params_custom_time['start_time_'.$m] = $custom_time_start[$m];
						$params_custom_time['end_time_'.$m]   = $custom_time_end[$m];
					}

					
					///Update the Custom Timing
					if($is_update) {

						$wpdb->update( ASL_PREFIX.'stores_timing', $params_custom_time, array('id'=> $t['Y'] ));	
					}
					else
					///Insert the custom timing
						$wpdb->insert( ASL_PREFIX.'stores_timing', $params_custom_time);
					
				} 
				*/

				//If No Logo is found and have image create a new Logo
				if($logoid == '0') {

					//check if logo image is provided and that exist in folder
					if($t['R']) {

						$t['O'] = trim($t['O']);
						$target_file = $t['R'];
						$target_name = $t['O'];

						$wpdb->insert(ASL_PREFIX.'storelogos', 
								 	array('path'=>$target_file,'name'=>$target_name),
								 	array('%s','%s'));

						$logo_id = $wpdb->insert_id;

						//update the logo id to store table
						$wpdb->update(ASL_PREFIX."stores",
							array('logo_id' => $logo_id),
							array('id' => $store_id)
						);

						$response->summary[] = 'Row: '.$index.", logo Created ".$t['O'];
					}
					else
						$response->summary[] = 'Row: '.$index.", logo ".$t['O']." not found";
				}
			}

			$index++;
		}

		
		$response->success = true;
		$response->imported_rows = $imported;
		
		echo json_encode($response);die;	
	}


	public function delete_import_file() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$data_ = $_REQUEST['data_'];			
		
		$inputFileName = ASL_PLUGIN_PATH.'public/import/'.$data_;
		//dd($inputFileName);

		if(file_exists($inputFileName)) {	
					
			unlink($inputFileName);
		
			$response->success = true;
			
			$response->msg = 'File Deleted Successfully.';
		}
		else
		{
			$response->error = 'Error Occurred While Deleting file.';//$form_data
			
		}

		echo json_encode($response);die;	
	}

	public function upload_store_import_file() {

		$response = new stdclass();
		$response->success = false;

		$target_dir  = ASL_PLUGIN_PATH."public/import/";
		$date = new DateTime();

	 	$target_name = $target_dir . strtolower($_FILES["files"]["name"]);
		$namefile 	 = substr(strtolower($_FILES["files"]["name"]), 0, strpos(strtolower($_FILES["files"]["name"]), '.'));
		

		$imageFileType = pathinfo($target_name,PATHINFO_EXTENSION);
		$target_name = 	$target_dir.pathinfo($_FILES['files']['name'], PATHINFO_FILENAME).uniqid().'.'.$imageFileType;


		//if file not found
		if (file_exists($target_name)) {
		    $response->message = "Sorry, file already exists.";
		}			
		// not a valid format
		else if(!in_array($imageFileType, array('xls','xlsx'))) {
		    $response->message = "Sorry, only xls & xlsx files are allowed.";
		}
		// upload 
		else if(move_uploaded_file($_FILES["files"]["tmp_name"], $target_name)) {

      	 	$response->message = "The file has been uploaded.";
      	 	$response->success = true;
		}
		//error
		else {

			$response->message = 'Some error occured';
		}

		echo json_encode($response);
	    die;
	}

	//export store
	public function export_store() {


		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
		
		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$data_ = $_REQUEST['data_'];

		$store = $wpdb->get_results("SELECT `s`.`id`,  `s`.`title`,  `s`.`description`,  `s`.`street`,  `s`.`city`,  `s`.`state`,  `s`.`postal_code`,  `c`.`country`,  `s`.`lat`,  `s`.`lng`,  `s`.`phone`,  `s`.`fax`,  `s`.`email`,  `s`.`website`,  `s`.`description_2`,  `s`.`logo_id`,  `s`.`marker_id`,  `s`.`is_disabled`,   `s`.`open_hours`, `s`.`ordr`, `s`.`created_on` FROM ".ASL_PREFIX."stores s LEFT JOIN ".ASL_PREFIX."countries c ON s.country = c.id ORDER BY s.`id`");
		
		include ASL_PLUGIN_PATH.'PHPExcel/Classes/PHPExcel.php';
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);

		
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'title'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'description'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'street'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'city'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'state'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'zip');
		$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'country'); 
		$objPHPExcel->getActiveSheet()->SetCellValue('H1', 'lat');
		$objPHPExcel->getActiveSheet()->SetCellValue('I1', 'lng');			
		$objPHPExcel->getActiveSheet()->SetCellValue('J1', 'phone');		
		$objPHPExcel->getActiveSheet()->SetCellValue('K1', 'fax'); 			
		$objPHPExcel->getActiveSheet()->SetCellValue('L1', 'email'); 			
		$objPHPExcel->getActiveSheet()->SetCellValue('M1', 'website');			
		$objPHPExcel->getActiveSheet()->SetCellValue('N1', 'is_disabled'); 			
		
		
		$objPHPExcel->getActiveSheet()->SetCellValue('O1', 'logo');			
		$objPHPExcel->getActiveSheet()->SetCellValue('P1', 'categories');
		//$objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'start_time');			
		//$objPHPExcel->getActiveSheet()->SetCellValue('R1', 'end_time');
		$objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'marker_id');
		//$objPHPExcel->getActiveSheet()->SetCellValue('T1', 'days');		
		$objPHPExcel->getActiveSheet()->SetCellValue('R1', '');
		//$objPHPExcel->getActiveSheet()->SetCellValue('V1', 'start_time_by_days');
		//$objPHPExcel->getActiveSheet()->SetCellValue('W1', 'end_time_by_days');
		$objPHPExcel->getActiveSheet()->SetCellValue('S1', 'description_2');
		$objPHPExcel->getActiveSheet()->SetCellValue('T1', 'open_hours');
		$objPHPExcel->getActiveSheet()->SetCellValue('V1', 'order');
		$objPHPExcel->getActiveSheet()->SetCellValue('U1', 'id');
		/*
		$objPHPExcel->getActiveSheet()->SetCellValue('T1', 'discription_2');
		*/		

		$i = 2;

		foreach ($store as $value) {

			$logo_name = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."storelogos WHERE id = ".$value->logo_id );	

			$category = "";
			
			$categories = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."categories RIGHT JOIN ".ASL_PREFIX."stores_categories ON  
				".ASL_PREFIX."categories.id  = ".ASL_PREFIX."stores_categories.category_id WHERE ".ASL_PREFIX."stores_categories.store_id = ".$value->id);

			foreach($categories as $c){

				$category .= $c->category_name.' | ';

			}

			$open_hours_value = '';
			
			if($value->open_hours) {

				$open_hours = json_decode($value->open_hours);

				if(is_object($open_hours)) {

					$open_details = array();
					foreach($open_hours as $key => $_day) {


						$key_value = '';

						if($_day && is_array($_day)) {

							$key_value = implode(',', $_day);
						}
						else if($_day == '1') {

							$key_value = $_day;
						}
						else  {

							$key_value = '0';
						}

						$open_details[] = $key.'='.$key_value;
					}

					$open_hours_value = implode('|', $open_details);
				}
			}

			/*Fill the Values*/
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, $value->title); 
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, $value->description); 
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, $value->street); 
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, $value->city); 
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, $value->state); 
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, $value->postal_code); 
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$i, $value->country); 
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$i, $value->lat); 
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$i, $value->lng); 
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.$i, $value->phone); 
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.$i, $value->fax); 
			$objPHPExcel->getActiveSheet()->SetCellValue('L'.$i, $value->email); 
			$objPHPExcel->getActiveSheet()->SetCellValue('M'.$i, $value->website); 
			$objPHPExcel->getActiveSheet()->SetCellValue('N'.$i, $value->is_disabled); 

			$objPHPExcel->getActiveSheet()->SetCellValue('O'.$i, (isset($logo_name) && isset($logo_name[0]))?$logo_name[0]->name:''); 
			$objPHPExcel->getActiveSheet()->SetCellValue('P'.$i, rtrim($category, " | ")); 
			
			$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$i, $value->marker_id); 
			$objPHPExcel->getActiveSheet()->SetCellValue('R'.$i, '');
			$objPHPExcel->getActiveSheet()->SetCellValue('S'.$i, $value->description_2);
			$objPHPExcel->getActiveSheet()->SetCellValue('T'.$i, $open_hours_value);
			$objPHPExcel->getActiveSheet()->SetCellValue('V'.$i, $value->ordr);
			$objPHPExcel->getActiveSheet()->SetCellValue('U'.$i, $value->id);
			/*
			$objPHPExcel->getActiveSheet()->SetCellValue('T'.$i, $value->description_2); 
			*/

			$i++;
			
		}

		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
		$objWriter->save(ASL_PLUGIN_PATH.'public/export/store.xlsx'); 

		$response->success = true;
		$response->msg = ASL_URL_PATH.'public/export/store.xlsx';

		echo json_encode($response);die;
	}


	public function backup_logo_icons() {

		global $wpdb;

	    require_once ASL_PLUGIN_PATH . 'includes/class-agile-store-locator-helper.php';
		
		
	    $zip_name = 'store-locator-logo-icons-'.time().'.zip';
	    $zip_path = ASL_PLUGIN_PATH.'public/export/'.$zip_name;
	    

		
        $response  = new \stdclass();
		$response->success = false;

        $all_assets = array();

        ///////////Backup Logo Folder/////////
        $logos     = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."storelogos ORDER BY name");
        
        foreach($logos as $logo) {

        	$asset_file 	= ASL_PLUGIN_PATH.'public/Logo/'.$logo->path;
        	
        	//Check if File Exist

        	$all_assets[] = $asset_file;
        }

        ///////////Backup Marker Folder/////////
        $markers   = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."markers");
        
        foreach($markers as $m) {

        	$asset_file 	= ASL_PLUGIN_PATH.'public/icon/'.$m->icon;
        	
        	//Check if File Exist

        	$all_assets[] = $asset_file;
        }

        ///////////Backup Logo Folder//////////
        $categories  = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."categories");
        
        foreach($categories as $c) {

        	$asset_file 	= ASL_PLUGIN_PATH.'public/svg/'.$c->icon;
        	
        	//Check if File Exist
        	$all_assets[] = $asset_file;
        }

        //Created successfull backup
	    if(AgileStoreLocator_Helper::create_zip($all_assets,$zip_path)) {

	    	$response->success 	= true;
	    	$response->msg 		= 'Assets Backup Successfully, Download the Zip File.';
	    	$response->zip 		= ASL_URL_PATH.'public/export/'.$zip_name;
	    }
	    else
	    	$response->error = 'Failed to Create the Backup';

	    echo json_encode($response);die;
	}

	//save setting
	public function save_setting() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$data_ = $_POST['data'];
		$keys  =  array_keys($data_);

		foreach($keys as $key) {
			$wpdb->update(ASL_PREFIX."configs",
				array('value' => $data_[$key]),
				array('key' => $key)
			);
		}



		$response->msg 	   = "Setting has been Updated Successfully.";
      	$response->success = true;

		echo json_encode($response);die;
	}

	
	/*Page Callee*/
	public function admin_plugin_settings() {

		include ASL_PLUGIN_PATH.'admin/partials/add_store.php';
	}

	public function edit_store() {

		global $wpdb;

		$countries = $wpdb->get_results("SELECT id,country FROM ".ASL_PREFIX."countries");
		$logos     = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."storelogos ORDER BY name");
		$markers   = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."markers");
        $category  = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."categories");

		
		$store_id = isset($_REQUEST['store_id'])?$_REQUEST['store_id']:null;

		if(!$store_id) {
			die('Invalid Store Id.');
		}

		$store  = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."stores WHERE id = $store_id");		
		/*
		$timing = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."stores_timing WHERE store_id = $store_id");

		if($timing) {
			$timing = (array)$timing[0];
		}

		else {

			$timing['start_time_0'] = $timing['start_time_1'] = $timing['start_time_2'] = $timing['start_time_3'] = $timing['start_time_4'] = $timing['start_time_5'] = $timing['start_time_6'] = '';
			$timing['end_time_0'] = $timing['end_time_1'] = $timing['end_time_2'] = $timing['end_time_3'] = $timing['end_time_4'] = $timing['end_time_5'] = $timing['end_time_6'] = '';
		}
		*/

		$storecategory = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."stores_categories WHERE store_id = $store_id");

		if(!$store || !$store[0]) {
			die('Invalid Store Id');
		}
		
		$store = $store[0];

		$storelogo = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."storelogos WHERE id = ".$store->logo_id);
		

		//api key
		$sql = "SELECT `key`,`value` FROM ".ASL_PREFIX."configs WHERE `key` = 'api_key' || `key` = 'time_format'";
		$all_configs_result = $wpdb->get_results($sql);


		$all_configs = array();

		foreach($all_configs_result as $c) {
			$all_configs[$c->key] = $c->value;
		}

		include ASL_PLUGIN_PATH.'admin/partials/edit_store.php';		
	}

	public function admin_add_new_store() {
		
		global $wpdb;

		//api key
		$sql = "SELECT `key`,`value` FROM ".ASL_PREFIX."configs WHERE `key` = 'api_key' || `key` = 'time_format'";
		$all_configs_result = $wpdb->get_results($sql);


		$all_configs = array();

		foreach($all_configs_result as $c) {
			$all_configs[$c->key] = $c->value;
		}

        $logos     = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."storelogos ORDER BY name");
        $markers   = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."markers");
        $category = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."categories");
		$countries = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."countries");

		include ASL_PLUGIN_PATH.'admin/partials/add_store.php';    
	}

	public function admin_dashboard() {


		global $wpdb;

		$sql = "SELECT `key`,`value` FROM ".ASL_PREFIX."configs WHERE `key` = 'api_key'";
		$all_configs_result = $wpdb->get_results($sql);

		$all_configs = array('api_key' => $all_configs_result[0]->value);
		$all_stats = array();
		
		$temp = $wpdb->get_results( "SELECT count(*) as c FROM ".ASL_PREFIX."markers");;
        $all_stats['markers']	 = $temp[0]->c; 

        $temp = $wpdb->get_results( "SELECT count(*) as c FROM ".ASL_PREFIX."stores");;
        $all_stats['stores']    = $temp[0]->c;

	
		$temp = $wpdb->get_results( "SELECT count(*) as c FROM ".ASL_PREFIX."categories");;
        $all_stats['categories'] = $temp[0]->c;

        $temp = $wpdb->get_results( "SELECT count(*) as c FROM ".ASL_PREFIX."stores_view");;
        $all_stats['searches'] = $temp[0]->c;


        //top views
        $top_stores = $wpdb->get_results("SELECT COUNT(*) AS views,".ASL_PREFIX."stores_view.`store_id`, title FROM `".ASL_PREFIX."stores_view` LEFT JOIN `".ASL_PREFIX."stores` ON ".ASL_PREFIX."stores_view.`store_id` = ".ASL_PREFIX."stores.`id` WHERE store_id IS NOT NULL GROUP BY store_id ORDER BY views DESC LIMIT 10");
        
        $top_search = $wpdb->get_results("SELECT COUNT(*) AS views, search_str FROM `".ASL_PREFIX."stores_view` WHERE store_id IS NULL AND is_search = 1 GROUP BY place_id ORDER BY views DESC LIMIT 10");



		include ASL_PLUGIN_PATH.'admin/partials/dashboard.php';    
	}

	public function get_stat_by_month() {

		global $wpdb;

		$month  = $_REQUEST['m'];
		$year   = $_REQUEST['y'];

		$c_m 	= date('m');
		$c_y 	= date('y');

		
		if(!$month || is_nan($month)) {

			//Current
			$month = $c_m;
		}

		if(!$year || is_nan($year)) {

			//Current
			$year = $c_y;
		}


		$date = intval(date('d'));

		//Not Current Month
		if($month != $c_m && $year != $c_y) {

			/*Month last date*/
			$a_date = "{$year}-{$month}-1";
			$date 	= date("t", strtotime($a_date));
		}
		
		//https://asl.localhost.com/wp-admin/admin-ajax.php?action=asl_get_stats&nonce=10691543ca&load_all=1&layout=1

		//WHERE YEAR(created_on) = YEAR(NOW()) AND MONTH(created_on) = MONTH(NOW())
		$results = $wpdb->get_results("SELECT DAY(created_on) AS d, COUNT(*) AS c FROM `".ASL_PREFIX."stores_view`  WHERE YEAR(created_on) = {$year} AND MONTH(created_on) = {$month} GROUP BY DAY(created_on)");

		
		
		$days_stats = array();

		for($a = 1; $a <= $date; $a++) {

			$days_stats[(string)$a] = 0; 
		}

		foreach($results as $row) {

			$days_stats[$row->d] = $row->c;
		}
	
		echo json_encode(array('data'=>$days_stats));die;
	}


	public function admin_delete_all_stores() {
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$response = new \stdclass();
		$response->success = false;
		
		global $wpdb;
		$prefix = ASL_PREFIX;
        
        $wpdb->query("TRUNCATE TABLE `{$prefix}stores_categories`");
        $wpdb->query("TRUNCATE TABLE `{$prefix}stores`");
        $wpdb->query("TRUNCATE TABLE `{$prefix}storelogos`");
     	
     	$response->success  = true;
     	$response->msg 	    = 'All Stores with Logo Deleted';

     	echo json_encode($response);die;
	}

	public function validate_api_key() {

		global $wpdb;

		require_once ASL_PLUGIN_PATH . 'includes/class-agile-store-locator-helper.php';

		$response = new \stdclass();
		$response->success = false;

		//Get the API KEY
		$sql 			= "SELECT `key`,`value` FROM ".ASL_PREFIX."configs WHERE `key` = 'api_key'";
		$configs_result = $wpdb->get_results($sql);
		$api_key 		= $configs_result[0]->value;

		if($api_key) {

			//Test Address
			$street 	= '315 West Main Street';
			$city		= 'Walla Walla';
			$state 		= 'WA';
			$zip 		= '45553';
			$country 	= 'US';

			$_address = $street.', '.$zip.'  '.$city.' '.$state.' '.$country;

			$results = AgileStoreLocator_Helper::getLnt($_address,$api_key,true);


			if(isset($results->error_message)) {

				$response->msg 	  = $results->error_message;
			}
			else {

				$response->msg 	   = 'Valid API Key';	
				$response->success = true;	
			}


			//$response->msg 	  = 'API Key is Valid';
			$response->success = false;
		}
		else
			$response->msg = 'API Key is Missing';


		echo json_encode($response);die;
	}

	public function admin_manage_categories() {
		include ASL_PLUGIN_PATH.'admin/partials/categories.php';
	}

	public function admin_store_markers() {
		include ASL_PLUGIN_PATH.'admin/partials/markers.php';
	}

	public function admin_store_logos() {
		include ASL_PLUGIN_PATH.'admin/partials/logos.php';
	}
	

	public function admin_manage_store() {
		include ASL_PLUGIN_PATH.'admin/partials/manage_store.php';
	}

	public function admin_import_stores() {

		//Check if ziparhive is installed
		global $wpdb;

		//Get the API KEY
		$sql 			= "SELECT `key`,`value` FROM ".ASL_PREFIX."configs WHERE `key` = 'api_key'";
		$configs_result = $wpdb->get_results($sql);
		$api_key 		= $configs_result[0]->value;

		if(!$api_key) {

			$api_key = 'API Key is Missing';
		}

		include ASL_PLUGIN_PATH.'admin/partials/import_store.php';
	}

	//Not used
	private function admin_info_box() {

		global $wpdb;

		$settings = array(
		    'teeny' => true,
		    'textarea_rows' => 15,
		    'tabindex' => 1,
		    'tinymce' => true
		);

		$inbox_id = (isset($_GET['infobox_id']) && !is_nan($_GET['infobox_id']))?$_GET['infobox_id']:1;
		$result = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."settings WHERE type = 'infobox' AND id = ".$inbox_id);


		$tooltip = ($result && $result[0])?$result[0]->content:'';

		//add_action( 'init', 'my_theme_add_editor_styles' );
		include ASL_PLUGIN_PATH.'admin/partials/info_box.php';			
	}


	

	public function admin_customize_map() {

		global $wpdb;

		$sql = "SELECT `key`,`value` FROM ".ASL_PREFIX."configs WHERE `key` = 'api_key' OR `key` = 'default_lat' OR `key` = 'default_lng' ORDER BY id;";
		$all_configs_result = $wpdb->get_results($sql);

		
		$config_list = array();
		foreach($all_configs_result as $item) {
			$config_list[$item->key] = $item->value;
		}

		$all_configs = array('api_key' => $config_list['api_key'],'default_lat' => $config_list['default_lat'],'default_lng' => $config_list['default_lng']);
		

		$map_customize  = $wpdb->get_results("SELECT content FROM ".ASL_PREFIX."settings WHERE type = 'map' AND id = 1");
		$map_customize  = ($map_customize && $map_customize[0]->content)?$map_customize[0]->content:'[]';


		//add_action( 'init', 'my_theme_add_editor_styles' );
		include ASL_PLUGIN_PATH.'admin/partials/customize_map.php';
	}

	

	public function admin_user_settings() {
	   
	   	global $wpdb;
	   	
		$sql = "SELECT `key`,`value` FROM ".ASL_PREFIX."configs";
		$all_configs_result = $wpdb->get_results($sql);
		
		$all_configs = array();
		foreach($all_configs_result as $config)
		{
			$all_configs[$config->key] = $config->value;	
		}

		///get Countries
		$countries = $wpdb->get_results("SELECT country,iso_code_2  as code FROM ".ASL_PREFIX."countries");

		include ASL_PLUGIN_PATH.'admin/partials/user_setting.php';
	}



	
}

