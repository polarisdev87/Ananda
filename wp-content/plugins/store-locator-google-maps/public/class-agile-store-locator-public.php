<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://agilelogix.com
 * @since      1.0.0
 *
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/public
 * @author     Your Name <email@agilelogix.com>
 */
class AgileStoreLocator_Public {

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
	 * @param      string    $AgileStoreLocator       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $AgileStoreLocator, $version ) {

		$this->AgileStoreLocator = $AgileStoreLocator;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->AgileStoreLocator.'-all-css',  ASL_URL_PATH.'public/css/all-css.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->AgileStoreLocator.'-asl-responsive',  ASL_URL_PATH.'public/css/asl_responsive.css', array(), $this->version, 'all' );
		//wp_enqueue_style( $this->AgileStoreLocator.'-asl-responsive',  ASL_URL_PATH.'public/css/asl-blue.css', array(), $this->version, 'all' );
		//wp_enqueue_style( $this->AgileStoreLocator.'-asl-responsive',  ASL_URL_PATH.'public/css/blue/test2c10.css', array(), $this->version, 'all' );
		//wp_enqueue_style( $this->AgileStoreLocator,  ASL_URL_PATH.'public/css/agile-store-locator-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		$title_nonce = wp_create_nonce( 'asl_remote_nonce' );
		
		
		global $wp_scripts,$wpdb;

		$sql = "SELECT `key`,`value` FROM ".ASL_PREFIX."configs WHERE `key` = 'api_key' OR `key` = 'map_language' OR `key` = 'map_region' ORDER BY id ASC;";
		$all_result = $wpdb->get_results($sql);
		

		//$map_url = '//maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry,places';

		
		
		$map_url = '//maps.googleapis.com/maps/api/js?libraries=places,drawing';

		if(isset($all_result[0]) && $all_result[0]->value) {
			$api_key = $all_result[0]->value;

			$map_url .= '&key='.$api_key;
		}


		if(isset($all_result[1]) && $all_result[1]->value) {
			
			$map_country = $all_result[1]->value;
			$map_url .= '&language='.$map_country;
		}

		if(isset($all_result[2]) && $all_result[2]->value) {
			
			$map_region = $all_result[2]->value;
			$map_url   .= '&region='.$map_region;
		}

		wp_enqueue_script('google-maps', $map_url , array('jquery'), null, true );
		wp_enqueue_script( $this->AgileStoreLocator.'-lib', ASL_URL_PATH . 'public/js/libs_new.min.js', array('jquery'), $this->version, true );
	}

	function add_async_attribute($tag, $handle) {
	 	
	    if ($handle == 'jquery-core' || $handle == 'jquery-migrate' || $handle == 'agile-store-locator-lib' || $handle == 'agile-store-locator-script'  || $handle == 'google-map' )
        	return str_replace( ' src', ' data-cfasync="false" src', $tag );	
    	else
    		return $tag;
    	

	}

	public function search_log() {

		global $wpdb;
		
		$nonce = isset($_GET['nonce'])?$_GET['nonce']:null;
		/*
		if ( ! wp_verify_nonce( $nonce, 'asl_remote_nonce' ))
 			die ( 'CRF check error.');
 		*/

 		if(!isset($_POST['is_search'])) {
 			die ( 'CRF check error.');
 		}

		$is_search 	  = ($_POST['is_search'] == '1')?1:0;
		$ip_address   = $_SERVER['REMOTE_ADDR'];


		$ASL_PREFIX = ASL_PREFIX;

		if($is_search == 1) {
			
			$search_str   = $_POST['search_str'];
			$place_id     = $_POST['place_id'];

			//To avoid multiple creations
			$count = $wpdb->get_results( $wpdb->prepare( "SELECT COUNT(*) AS c FROM `{$ASL_PREFIX}stores_view` WHERE (created_on > NOW() - INTERVAL 15 MINUTE) AND place_id = %s",
				$place_id
			));

			if($count[0]->c < 1) {

				$wpdb->query( $wpdb->prepare( "INSERT INTO {$ASL_PREFIX}stores_view (search_str, place_id, is_search, ip_address ) VALUES ( %s, %s, %d, %s )", 
			    	$search_str, $place_id, $is_search ,$ip_address 
				));
			}
		}
		else {

			$store_id   = $_POST['store_id'];

			//To avoid multiple creations
			$count = $wpdb->get_results( $wpdb->prepare( "SELECT COUNT(*) AS c FROM `{$ASL_PREFIX}stores_view` WHERE (created_on > NOW() - INTERVAL 15 MINUTE) AND store_id = %s",
				$store_id
			));

			if($count[0]->c < 1) {
				
				$wpdb->query( $wpdb->prepare( "INSERT INTO {$ASL_PREFIX}stores_view (store_id, is_search, ip_address ) VALUES ( %s, %d, %s )", 
			    	$store_id, $is_search ,$ip_address
				));
			}

		}

		echo die('[]');
	}

	/*
	add_filter( 'template_include', array($this,'include_template_function'), 1 );
	function include_template_function( $template_path ) {
	 	

	    if ( get_post_type() == 'post-timeline' ) {

	        if ( is_single() ) {
	            // checks if the file exists in the theme first,
	            // otherwise serve the file from the plugin
	            if ( $theme_file = locate_template( array ( 'single-post-timeline.php' ) ) ) {
	                $template_path = $theme_file;
	            } else {
	                $template_path = POST_TIMELINE_PLUGIN_PATH . 'public/partials/post-timeline-page.php';
	            }
	        }
	    }
	    return $template_path;
	}
	*/

	/*Display the Search box for the Store locator Shortcode :: ASL_SEARCHBOX*/
	public function searchBox($atts) {

	}

	/*Frontend of Plugin*/
	public function frontendStoreLocator($atts)
	{
		global $wpdb;

		//die(get_locale());

		wp_enqueue_script( $this->AgileStoreLocator.'-script', ASL_URL_PATH . 'public/js/site_script.js', array('jquery'), $this->version, true );

		$configs = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."configs");

		$all_configs = array();
		
		foreach($configs as $_config)
			$all_configs[$_config->key] = $_config->value;

		$all_configs['URL'] = ASL_URL_PATH;

		
		if(!$atts) {

			$atts = array();
		}
		

		$all_configs = shortcode_atts( $all_configs, $atts );

		//add the missing attributes into settings
		$all_configs = array_merge($all_configs,$atts);

		$category_clause = "";

		//Only show Valid Categories		
		if(isset($atts['category'])) {

			$all_configs['category'] = $atts['category'];

			$load_categories = explode(',', $all_configs['category']);

			$the_categories  = array();

			foreach($load_categories as $_c) {

				if(!is_nan($_c)) {

					$the_categories[] = $_c;
				}
			}

			$the_categories  = implode(',', $the_categories);
			$category_clause = " AND id IN (".$the_categories.')';
			$all_configs['category'] = $the_categories;
		}

		//min and max zoom
		
		if(isset($atts['maxZoom'])) {
			
			$all_configs['maxZoom'] = $atts['maxZoom'];
		}

		if(isset($atts['minZoom'])) {
			
			$all_configs['minZoom'] = $atts['minZoom'];
		}


		if(isset($atts['minZoom'])) {
			
			$all_configs['minZoom'] = $atts['minZoom'];
		}
		

		//for limited markers
		
		if(isset($atts['stores'])) {
			
			$all_configs['stores'] = $atts['stores'];
		}
		


		if(isset($atts['fixed_radius']) && !is_nan($atts['fixed_radius'])) {
			
			$all_configs['fixed_radius'] = $atts['fixed_radius'];
		}

		
		if(isset($atts['select_category'])) {
			
			$all_configs['select_category'] = $atts['select_category'];
		}


		//ADD The missing parameters
		$default_options = array(
			'cluster' => '1',
			'prompt_location' => '2',
			'map_type' => 'roadmap',
			'distance_unit' => 'Miles',
			'zoom' => '9',
			'show_categories' => '1',
			'additional_info' => '1',
			'distance_slider' => '1',
			'layout' => '0',
			'default_lat' => '-33.947128',
			'default_lng' => '25.591169',
			'map_layout' => '0',
			'infobox_layout' => '0',
			'advance_filter' => '1',
			'color_scheme' => '0',
			'time_switch' => '0',
			'category_marker' => '0',
			'load_all' => '1',
			'head_title' => 'Number Of Shops',
			'font_color_scheme' => '1',
			'template' => '0',
			'color_scheme_1' => '0',
			'api_key' => '',
			'display_list' => '1',
			'full_width' => '0',
			'time_format' => '0',
			'category_title' => 'Category',
			'no_item_text' => 'No Item Found',
			'zoom_li' => '13',
			'single_cat_select' => '0',
			'country_restrict' => '',
			'google_search_type' => '',
			'color_scheme_2' => '0',
			'analytics' => '0',
			'sort_by_bound' => '0',
			'scroll_wheel' => '0',
			'mobile_optimize' 	=> null,
			'mobile_load_bound' => null,
			'search_type' => '0',
			'search_destin' => '0',
			'full_height' => '',
			'map_language' => '',
			'map_region' => '',
			'sort_by' => '',
			'distance_control' => '0',
			'dropdown_range' => '20,40,60,80,*100',
			'target_blank' => '1'
		);

		$all_configs  = array_merge($default_options,$all_configs);
	
		//for search enter key
		/*
		if(isset($atts['enter_key'])) {
			
			$all_configs['enter_key'] = $atts['enter_key'];
		}
		*/

		if(isset($atts['user_center'])) {
			
			$all_configs['user_center'] = $atts['user_center'];
		}

		//Get the categories
		$all_categories = array();
		$results = $wpdb->get_results("SELECT id,category_name as name,icon FROM ".ASL_PREFIX."categories WHERE is_active = 1".$category_clause.' ORDER BY category_name ASC');

		foreach($results as $_result) {

			$all_categories[$_result->id] = $_result;
		}
		


		//Get the Markers
		$all_markers = array();
		$results = $wpdb->get_results("SELECT id,marker_name as name,icon FROM ".ASL_PREFIX."markers WHERE is_active = 1");

		foreach($results as $_result) {
			
			$all_markers[$_result->id] = $_result;
		}

		

		///get the map configuration
		switch($all_configs['map_layout']) {

			//
			case '-1':
				$all_configs['map_layout'] = '[]';
			break;

			//25-blue-water
			case '0':
				$all_configs['map_layout'] = '[{featureType:"administrative",elementType:"labels.text.fill",stylers:[{color:"#444444"}]},{featureType:"landscape",elementType:"all",stylers:[{color:"#f2f2f2"}]},{featureType:"poi",elementType:"all",stylers:[{visibility:"off"}]},{featureType:"road",elementType:"all",stylers:[{saturation:-100},{lightness:45}]},{featureType:"road.highway",elementType:"all",stylers:[{visibility:"simplified"}]},{featureType:"road.arterial",elementType:"labels.icon",stylers:[{visibility:"off"}]},{featureType:"transit",elementType:"all",stylers:[{visibility:"off"}]},{featureType:"water",elementType:"all",stylers:[{color:"#46bcec"},{visibility:"on"}]}]';
			break;

			//Flat Map
			case '1':
				//$all_configs['map_layout'] = '[{featureType:"landscape",elementType:"all",stylers:[{visibility:"on"},{color:"#f3f4f4"}]},{featureType:"landscape.man_made",elementType:"geometry",stylers:[{weight:.9},{visibility:"off"}]},{featureType:"poi.park",elementType:"geometry.fill",stylers:[{visibility:"on"},{color:"#83cead"}]},{featureType:"road",elementType:"all",stylers:[{visibility:"on"},{color:"#ffffff"}]},{featureType:"road",elementType:"labels",stylers:[{visibility:"off"}]},{featureType:"road.highway",elementType:"all",stylers:[{visibility:"on"},{color:"#fee379"}]},{featureType:"road.arterial",elementType:"all",stylers:[{visibility:"on"},{color:"#fee379"}]},{featureType:"water",elementType:"all",stylers:[{visibility:"on"},{color:"#7fc8ed"}]}]';
				$all_configs['map_layout'] = '[{featureType:"landscape",elementType:"all",stylers:[{visibility:"on"},{color:"#f3f4f4"}]},{featureType:"landscape.man_made",elementType:"geometry",stylers:[{weight:.9},{visibility:"off"}]},{featureType:"poi.park",elementType:"geometry.fill",stylers:[{visibility:"on"},{color:"#83cead"}]},{featureType:"road",elementType:"all",stylers:[{visibility:"on"},{color:"#ffffff"}]},{featureType:"road",elementType:"labels",stylers:[{visibility:"off"}]},{featureType:"road.highway",elementType:"all",stylers:[{visibility:"on"},{color:"#fee379"}]},{featureType:"road.arterial",elementType:"all",stylers:[{visibility:"on"},{color:"#fee379"}]},{featureType:"water",elementType:"all",stylers:[{visibility:"on"},{color:"#7fc8ed"}]}]';
			break;

			//Icy Blue
			case '2':
				$all_configs['map_layout'] = '[{stylers:[{hue:"#2c3e50"},{saturation:250}]},{featureType:"road",elementType:"geometry",stylers:[{lightness:50},{visibility:"simplified"}]},{featureType:"road",elementType:"labels",stylers:[{visibility:"off"}]}]';
			break;


			//Pale Dawn
			case '3':
				$all_configs['map_layout'] = '[{featureType:"administrative",elementType:"all",stylers:[{visibility:"on"},{lightness:33}]},{featureType:"landscape",elementType:"all",stylers:[{color:"#f2e5d4"}]},{featureType:"poi.park",elementType:"geometry",stylers:[{color:"#c5dac6"}]},{featureType:"poi.park",elementType:"labels",stylers:[{visibility:"on"},{lightness:20}]},{featureType:"road",elementType:"all",stylers:[{lightness:20}]},{featureType:"road.highway",elementType:"geometry",stylers:[{color:"#c5c6c6"}]},{featureType:"road.arterial",elementType:"geometry",stylers:[{color:"#e4d7c6"}]},{featureType:"road.local",elementType:"geometry",stylers:[{color:"#fbfaf7"}]},{featureType:"water",elementType:"all",stylers:[{visibility:"on"},{color:"#acbcc9"}]}]';
			break;


			//cladme
			case '4':
				$all_configs['map_layout'] = '[{featureType:"administrative",elementType:"labels.text.fill",stylers:[{color:"#444444"}]},{featureType:"landscape",elementType:"all",stylers:[{color:"#f2f2f2"}]},{featureType:"poi",elementType:"all",stylers:[{visibility:"off"}]},{featureType:"road",elementType:"all",stylers:[{saturation:-100},{lightness:45}]},{featureType:"road.highway",elementType:"all",stylers:[{visibility:"simplified"}]},{featureType:"road.arterial",elementType:"labels.icon",stylers:[{visibility:"off"}]},{featureType:"transit",elementType:"all",stylers:[{visibility:"off"}]},{featureType:"water",elementType:"all",stylers:[{color:"#4f595d"},{visibility:"on"}]}]';
			break;


			//light monochrome
			case '5':
				$all_configs['map_layout'] = '[{featureType:"administrative.locality",elementType:"all",stylers:[{hue:"#2c2e33"},{saturation:7},{lightness:19},{visibility:"on"}]},{featureType:"landscape",elementType:"all",stylers:[{hue:"#ffffff"},{saturation:-100},{lightness:100},{visibility:"simplified"}]},{featureType:"poi",elementType:"all",stylers:[{hue:"#ffffff"},{saturation:-100},{lightness:100},{visibility:"off"}]},{featureType:"road",elementType:"geometry",stylers:[{hue:"#bbc0c4"},{saturation:-93},{lightness:31},{visibility:"simplified"}]},{featureType:"road",elementType:"labels",stylers:[{hue:"#bbc0c4"},{saturation:-93},{lightness:31},{visibility:"on"}]},{featureType:"road.arterial",elementType:"labels",stylers:[{hue:"#bbc0c4"},{saturation:-93},{lightness:-2},{visibility:"simplified"}]},{featureType:"road.local",elementType:"geometry",stylers:[{hue:"#e9ebed"},{saturation:-90},{lightness:-8},{visibility:"simplified"}]},{featureType:"transit",elementType:"all",stylers:[{hue:"#e9ebed"},{saturation:10},{lightness:69},{visibility:"on"}]},{featureType:"water",elementType:"all",stylers:[{hue:"#e9ebed"},{saturation:-78},{lightness:67},{visibility:"simplified"}]}]';
			break;
			

			//mostly grayscale
			case '6':
				$all_configs['map_layout'] = '[{featureType:"administrative",elementType:"all",stylers:[{visibility:"on"},{lightness:33}]},{featureType:"administrative",elementType:"labels",stylers:[{saturation:"-100"}]},{featureType:"administrative",elementType:"labels.text",stylers:[{gamma:"0.75"}]},{featureType:"administrative.neighborhood",elementType:"labels.text.fill",stylers:[{lightness:"-37"}]},{featureType:"landscape",elementType:"geometry",stylers:[{color:"#f9f9f9"}]},{featureType:"landscape.man_made",elementType:"geometry",stylers:[{saturation:"-100"},{lightness:"40"},{visibility:"off"}]},{featureType:"landscape.natural",elementType:"labels.text.fill",stylers:[{saturation:"-100"},{lightness:"-37"}]},{featureType:"landscape.natural",elementType:"labels.text.stroke",stylers:[{saturation:"-100"},{lightness:"100"},{weight:"2"}]},{featureType:"landscape.natural",elementType:"labels.icon",stylers:[{saturation:"-100"}]},{featureType:"poi",elementType:"geometry",stylers:[{saturation:"-100"},{lightness:"80"}]},{featureType:"poi",elementType:"labels",stylers:[{saturation:"-100"},{lightness:"0"}]},{featureType:"poi.attraction",elementType:"geometry",stylers:[{lightness:"-4"},{saturation:"-100"}]},{featureType:"poi.park",elementType:"geometry",stylers:[{color:"#c5dac6"},{visibility:"on"},{saturation:"-95"},{lightness:"62"}]},{featureType:"poi.park",elementType:"labels",stylers:[{visibility:"on"},{lightness:20}]},{featureType:"road",elementType:"all",stylers:[{lightness:20}]},{featureType:"road",elementType:"labels",stylers:[{saturation:"-100"},{gamma:"1.00"}]},{featureType:"road",elementType:"labels.text",stylers:[{gamma:"0.50"}]},{featureType:"road",elementType:"labels.icon",stylers:[{saturation:"-100"},{gamma:"0.50"}]},{featureType:"road.highway",elementType:"geometry",stylers:[{color:"#c5c6c6"},{saturation:"-100"}]},{featureType:"road.highway",elementType:"geometry.stroke",stylers:[{lightness:"-13"}]},{featureType:"road.highway",elementType:"labels.icon",stylers:[{lightness:"0"},{gamma:"1.09"}]},{featureType:"road.arterial",elementType:"geometry",stylers:[{color:"#e4d7c6"},{saturation:"-100"},{lightness:"47"}]},{featureType:"road.arterial",elementType:"geometry.stroke",stylers:[{lightness:"-12"}]},{featureType:"road.arterial",elementType:"labels.icon",stylers:[{saturation:"-100"}]},{featureType:"road.local",elementType:"geometry",stylers:[{color:"#fbfaf7"},{lightness:"77"}]},{featureType:"road.local",elementType:"geometry.fill",stylers:[{lightness:"-5"},{saturation:"-100"}]},{featureType:"road.local",elementType:"geometry.stroke",stylers:[{saturation:"-100"},{lightness:"-15"}]},{featureType:"transit.station.airport",elementType:"geometry",stylers:[{lightness:"47"},{saturation:"-100"}]},{featureType:"water",elementType:"all",stylers:[{visibility:"on"},{color:"#acbcc9"}]},{featureType:"water",elementType:"geometry",stylers:[{saturation:"53"}]},{featureType:"water",elementType:"labels.text.fill",stylers:[{lightness:"-42"},{saturation:"17"}]},{featureType:"water",elementType:"labels.text.stroke",stylers:[{lightness:"61"}]}]';
			break;


			//turquoise water
			case '7':
				$all_configs['map_layout'] = '[{stylers:[{hue:"#16a085"},{saturation:0}]},{featureType:"road",elementType:"geometry",stylers:[{lightness:100},{visibility:"simplified"}]},{featureType:"road",elementType:"labels",stylers:[{visibility:"off"}]}]';
			break;


			//unsaturated browns
			case '8':
				$all_configs['map_layout'] = '[{elementType:"geometry",stylers:[{hue:"#ff4400"},{saturation:-68},{lightness:-4},{gamma:.72}]},{featureType:"road",elementType:"labels.icon"},{featureType:"landscape.man_made",elementType:"geometry",stylers:[{hue:"#0077ff"},{gamma:3.1}]},{featureType:"water",stylers:[{hue:"#00ccff"},{gamma:.44},{saturation:-33}]},{featureType:"poi.park",stylers:[{hue:"#44ff00"},{saturation:-23}]},{featureType:"water",elementType:"labels.text.fill",stylers:[{hue:"#007fff"},{gamma:.77},{saturation:65},{lightness:99}]},{featureType:"water",elementType:"labels.text.stroke",stylers:[{gamma:.11},{weight:5.6},{saturation:99},{hue:"#0091ff"},{lightness:-86}]},{featureType:"transit.line",elementType:"geometry",stylers:[{lightness:-48},{hue:"#ff5e00"},{gamma:1.2},{saturation:-23}]},{featureType:"transit",elementType:"labels.text.stroke",stylers:[{saturation:-64},{hue:"#ff9100"},{lightness:16},{gamma:.47},{weight:2.7}]}]';
			break;

			//turquoise water
			default:
				$all_configs['map_layout'] = '[{"featureType":"administrative","elementType":"all","stylers":[{"visibility":"on"},{"lightness":33}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2e5d4"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#c5dac6"}]},{"featureType":"poi.park","elementType":"labels","stylers":[{"visibility":"on"},{"lightness":20}]},{"featureType":"road","elementType":"all","stylers":[{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#c5c6c6"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#e4d7c6"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#fbfaf7"}]},{"featureType":"water","elementType":"all","stylers":[{"visibility":"on"},{"color":"#acbcc9"}]}]';
				//$all_configs['map_layout'] = '[{featureType:"landscape",elementType:"all",stylers:[{visibility:"on"},{color:"#f3f4f4"}]},{featureType:"landscape.man_made",elementType:"geometry",stylers:[{weight:.9},{visibility:"off"}]},{featureType:"poi.park",elementType:"geometry.fill",stylers:[{visibility:"on"},{color:"#83cead"}]},{featureType:"road",elementType:"all",stylers:[{visibility:"on"},{color:"#ffffff"}]},{featureType:"road",elementType:"labels",stylers:[{visibility:"off"}]},{featureType:"road.highway",elementType:"all",stylers:[{visibility:"on"},{color:"#fee379"}]},{featureType:"road.arterial",elementType:"all",stylers:[{visibility:"on"},{color:"#fee379"}]},{featureType:"water",elementType:"all",stylers:[{visibility:"on"},{color:"#7fc8ed"}]}]';
			break;
		}


		//Load the map customization
		$map_customize  = $wpdb->get_results("SELECT content FROM ".ASL_PREFIX."settings WHERE type = 'map' AND id = 1");
		$map_customize  = ($map_customize && $map_customize[0]->content)?$map_customize[0]->content:'[]';
			
		
		//For Translation	
		$words = array(
			'direction' => __('Directions','asl_locator'),
			'zoom' 		=> __('Zoom Here','asl_locator'),
			'detail' 	=> __('Website','asl_locator'),
			'select_option' => __('Select Option','asl_locator'),
			'search' 	=> __('Search','asl_locator'),
			'all_selected' => __('All selected','asl_locator'),
			'none' 		=> __('None','asl_locator'),
			'none_selected' => __('None Selected','asl_locator'),
			'reset_map' => __('Reset Map','asl_locator'),
			'reload_map' => __('Scan Area','asl_locator'),
			'selected' => __('selected','asl_locator'),
			'current_location' => __('Current Location','asl_locator'),
			'your_cur_loc' => __('Your Current Location','asl_locator'),
			/*Template words*/
			'Miles' 	 => __('Miles','asl_locator'),
			'Km' 	 	 => __('Km','asl_locator'),
			'phone' 	 => __('Phone','asl_locator'),
			'fax' 		 => __('Fax','asl_locator'),
			'directions' => __('Directions','asl_locator'),
			'distance' 	 => __('Distance','asl_locator'),
			'read_more'  => __('Read more','asl_locator'),
			'hide_more'  => __('Hide Details','asl_locator'),
			'select_distance'  => __('Select Distance','asl_locator'),
			'none_distance'  => __('None','asl_locator')
		);

		$all_configs['words'] 	  = $words;
		$all_configs['version']   = $this->version;


		ob_start();
		
		$template_file = null;

		switch($all_configs['template']) {

			case '2':
				if($all_configs['color_scheme_2'] < 0 && $all_configs['color_scheme_2'] > 9)
					$all_configs['color_scheme_2'] = 0;


				if(isset($atts['no_script']) && $atts['no_script'] == '1') {

					wp_localize_script( $this->AgileStoreLocator.'-script', 'asl_info_list', '<div class="item" data-id="{{:id}}">    <div class="col-xs-4 img-section">        <a class="thumb-a">            {{if path}}              <img src="'.ASL_URL_PATH.'public/Logo/{{:path}}" alt="logo">            {{/if}}          </a>    </div>    <div class="col-xs-8 data-section">    <div class="title-item">    <p class="p-title">{{:title}}</p>    </div>    <div class="clear"></div>    <div class="addr-sec">            <p class="p-area">{{:address}}</p>      {{if distance}}        <p class="p-area p-distance">'.$words['distance'].': {{:dist_str}}</p>      {{/if}}      {{if phone}}      <p class="p-area"><span class="glyphicon glyphicon-earphone"></span> '.$words['phone'].': {{:phone}}</p>      {{/if}}      {{if email}}      <p class="p-area"><span class="glyphicon glyphicon-envelope"></span>{{:email}}</p>      {{/if}}      {{if c_names}}      <p class="p-category"><span class="glyphicon glyphicon-tags"></span> {{:c_names}}</p>      {{/if}}      {{if open_hours}}<div class="p-area"><span class="glyphicon glyphicon-time"></span> {{:open_hours}}</div>{{/if}}      {{if days_str}}      <p class="p-time"><span class="glyphicon glyphicon-calendar"></span>{{:days_str}}</p>      {{/if}}    </div>    <div class="">      <div class="col-xs-5 col-md-12 item-thumb">      </div>    </div>    <div class="col-xs-12 distance">          <a class="p-direction btn btn-block"><span class="s-direction">'.$words['direction'].'</span></a>    </div>    <div class="clear"/>  </div>  <div>');
					wp_localize_script( $this->AgileStoreLocator.'-script', 'asl_info_box', '<div class="image_map_popup" style="display:none"><img src="{{:URL}}public/Logo/{{:path}}" /></div>  <h3>{{:title}}</h3>  <div class="infowindowContent">    <div class="info-addr">      <div class="address"><span class="glyphicon glyphicon-map-marker"></span>{{:address}}</div>      {{if phone}}      <div class="phone"><span class="glyphicon glyphicon-earphone"></span><b>'.$words['phone'].': </b><a href="tel:{{:phone}}">{{:phone}}</a></div>      {{/if}}      {{if open_hours}}      <div class="p-time"><span class="glyphicon glyphicon-time"></span> {{:open_hours}}</div>      {{/if}}      {{if days_str}}      <div class="p-time"><span class="glyphicon glyphicon-calendar"></span> {{:days_str}}</div>      {{/if}}      {{if show_categories && c_names}}      <div class="categories"><span class="glyphicon glyphicon-tags"></span>{{:c_names}}</div>      {{/if}}      {{if distance}}      <div class="distance">'.$words['distance'].': {{:dist_str}}</div>      {{/if}}    </div>    <div class="img_box" style="display:none">    <img src="{{:URL}}public/Logo/{{:path}}" alt="logo">  </div>  <div class="asl-buttons"></div></div><div class="arrow-down"></div>');

				}
				else
					$atts['no_script'] = 0;

		    	$template_file = 'template-frontend-2.php';
		    	break;

			case '1':
				if($all_configs['color_scheme_1'] < 0 && $all_configs['color_scheme_1'] > 9)
					$all_configs['color_scheme_1'] = 0;

				if(isset($atts['no_script']) && $atts['no_script'] == '1') {

					wp_localize_script( $this->AgileStoreLocator.'-script', 'asl_info_list', '<div class="item" data-id="{{:id}}">  	<div class="col-md-9 col-xs-9 addr-sec">    	<p class="p-title">{{:title}}</p>    	<p class="p-area"><span class="glyphicon glyphicon-map-marker"></span>{{:address}}</p>    	{{if phone}}      <p class="p-area"><span class="glyphicon glyphicon-earphone"></span> '. $words['phone'] .': {{:phone}}</p>      {{/if}}      {{if c_names}}      <p class="p-category"><span class="glyphicon glyphicon-tags"></span> {{:c_names}}</p>      {{/if}}      {{if open_hours}}<div class="p-area"><span class="glyphicon glyphicon-time"></span> {{:open_hours}}</div>{{/if}}      {{if days_str}}      <p class="p-time"><span class="glyphicon glyphicon-calendar"></span>{{:days_str}}</p>      {{/if}}  	</div>    <div class="col-md-3 col-xs-3">    	<div class="col-xs-5 col-md-12 item-thumb">        	<a class="thumb-a">            {{if path}}              <img src="'. ASL_URL_PATH .'public/Logo/{{:path}}" alt="logo">            {{/if}}        	</a>      </div>  	</div>    <div class="col-xs-12 distance">        <div class="col-xs-6">          <p class="p-direction"><span class="s-direction">'. $words['direction'] .'</span></p>        </div>        {{if distance}}        <div class="col-xs-6">            <span class="s-distance">' . $words['distance'] . ': {{:dist_str}}</span>        </div>        {{/if}}    </div>    {{if description_2}}    <div class="col-xs-12">      <button type="button" onclick="javascript:asl_jQuery(this).next().next().slideToggle(100);asl_jQuery(this).next().show();asl_jQuery(this).hide()" class="asl_Readmore_button">'. $words['read_more'].'</button>      <button type="button" onclick="javascript:asl_jQuery(this).next().slideToggle(100);asl_jQuery(this).prev().show();asl_jQuery(this).hide()" style="display:none" class="asl_Readmore_button">'.$words['hide_more'].'</button>    <p class="more_info" style="display:none">{{:description_2}}</p>    </div>    {{/if}}  	<div class="clear"/>  </div>');
					wp_localize_script( $this->AgileStoreLocator.'-script', 'asl_info_box', '<div class="image_map_popup" style="display:none"><img src="{{:URL}}public/Logo/{{:path}}" /></div>  <h3>{{:title}}</h3>  <div class="infowindowContent">    <div class="info-addr">      <div class="address"><span class="glyphicon glyphicon-map-marker"></span>{{:address}}</div>      {{if phone}}      <div class="phone"><span class="glyphicon glyphicon-earphone"></span><b>'.$words['phone'].': </b><a href="tel:{{:phone}}">{{:phone}}</a></div>      {{/if}}      {{if open_hours}}<div class="p-time"><span class="glyphicon glyphicon-time"></span> {{:open_hours}}</div>{{/if}}      {{if days_str}}      <div class="p-time"><span class="glyphicon glyphicon-calendar"></span> {{:days_str}}</div>      {{/if}}      {{if show_categories && c_names}}      <div class="categories"><span class="glyphicon glyphicon-tags"></span>{{:c_names}}</div>      {{/if}}      {{if distance}}      <div class="distance">'.$words['distance'].': {{:dist_str}}</div>      {{/if}}    </div>    <div class="img_box" style="display:none">    <img src="{{:URL}}public/Logo/{{:path}}" alt="logo">  </div>  <div class="asl-buttons"></div></div><div class="arrow-down"></div>');

				}
				else
					$atts['no_script'] = 0;

		    	$template_file = 'template-frontend-1.php';
		    	break;


			case 'deal':
				if($all_configs['color_scheme'] < 0 && $all_configs['color_scheme'] > 9)
					$all_configs['color_scheme'] = 0;

		    	$template_file = 'template-frontend-deal.php';
		    	break;

		    case 'realestate':
				if($all_configs['color_scheme_2'] < 0 && $all_configs['color_scheme_2'] > 9)
					$all_configs['color_scheme_2'] = 0;

		    	$template_file = 'template-frontend-realestate.php';
		    	break;


			default:
				if($all_configs['color_scheme'] < 0 && $all_configs['color_scheme'] > 9)
					$all_configs['color_scheme'] = 0;

				if(isset($atts['no_script']) && $atts['no_script'] == '1') {

					wp_localize_script( $this->AgileStoreLocator.'-script', 'asl_info_list', '<div class="item" data-id="{{:id}}">    <div class="addr-sec">    <p class="p-title">{{:title}}</p>    </div>    <div class="clear"></div>    <div class="col-md-9 col-xs-9 addr-sec">            <p class="p-area"><span class="glyphicon glyphicon-map-marker"></span>{{:address}}</p>      {{if phone}}      <p class="p-area"><span class="glyphicon glyphicon-earphone"></span> '. $words['phone'] .': {{:phone}}</p>      {{/if}}      {{if email}}      <p class="p-area"><span class="glyphicon glyphicon-envelope"></span>{{:email}}</p>      {{/if}}      {{if fax}}        <p class="p-area"><span class="glyphicon glyphicon-fax"></span> '. $words['fax'] .':{{:fax}}</p>      {{/if}}        {{if c_names}}      <p class="p-category"><span class="glyphicon glyphicon-tags"></span> {{:c_names}}</p>      {{/if}}      {{if open_hours}}<div class="p-area"><span class="glyphicon glyphicon-time"></span> {{:open_hours}}</div>{{/if}}      {{if days_str}}      <p class="p-time"><span class="glyphicon glyphicon-calendar"></span> {{:days_str}}</p>      {{/if}}    </div>    <div class="col-md-3 col-xs-3">      <div class="col-xs-5 col-md-12 item-thumb">          <a class="thumb-a">            {{if path}}              <img src="'. ASL_URL_PATH .'public/Logo/{{:path}}" alt="logo">            {{/if}}          </a>      </div>    </div>    <div class="col-xs-12 distance">        <div class="col-xs-6">          <p class="p-direction"><span class="s-direction">'. $words['directions'] .'</span></p>        </div>        {{if distance}}        <div class="col-xs-6">            <a class="s-distance">'. $words['distance'] .': {{:dist_str}}</a>        </div>        {{/if}}    </div>    {{if description_2}}    <div class="col-xs-12">      <a onclick="javascript:asl_jQuery(this).next().next().slideToggle(100);asl_jQuery(this).next().show();asl_jQuery(this).hide()" class="asl_Readmore_button">'. $words['read_more'] .'</a>      <a onclick="javascript:asl_jQuery(this).next().slideToggle(100);asl_jQuery(this).prev().show();asl_jQuery(this).hide()" style="display:none" class="asl_Readmore_button">'. $words['hide_more'] .'</a>    <p class="more_info" style="display:none">{{:description_2}}</p>    </div>    {{/if}}    <div class="clear"/>  </div>');
					wp_localize_script( $this->AgileStoreLocator.'-script', 'asl_info_box', '<div class="image_map_popup" style="display:none"><img src="{{:URL}}public/Logo/{{:path}}" /></div>    <h3>{{:title}}</h3>    <div class="infowindowContent">      <div class="info-addr">        <div class="address"><span class="glyphicon glyphicon-map-marker"></span>{{:address}}</div>        {{if phone}}        <div class="phone"><span class="glyphicon glyphicon-earphone"></span><b>'. $words['phone'] .': </b><a href="tel:{{:phone}}">{{:phone}}</a></div>        {{/if}}        {{if open_hours}}        <div class="p-time"><span class="glyphicon glyphicon-time"></span> {{:open_hours}}</div>        {{/if}}        {{if days_str}}        <div class="p-time"><span class="glyphicon glyphicon-calendar"></span> {{:days_str}}</div>        {{/if}}        {{if show_categories && c_names}}        <div class="categories"><span class="glyphicon glyphicon-tags"></span>{{:c_names}}</div>        {{/if}}        {{if distance}}        <div class="distance">'. $words['distance'] .': {{:dist_str}}</div>        {{/if}}      </div>      <div class="img_box" style="display:none">      <img src="{{:URL}}public/Logo/{{:path}}" alt="logo">    </div>    <div class="asl-buttons"></div>  </div><div class="arrow-down"></div>');
				}
				else
					$atts['no_script'] = 0;

		    	$template_file = 'template-frontend-0.php';

		    	break;
		}


		//Customization of Template
		if($template_file) {

			if ( $theme_file   = locate_template( array ( $template_file ) ) ) {
	            $template_path = $theme_file;
	        }
	        else {
	            $template_path = 'partials/'.$template_file;
	        }

	        include $template_path;
		}
        

		$sl_output = ob_get_contents();
		ob_end_clean();


		/*
		var asl_configuration = <?php echo json_encode($all_configs); ?>,
			asl_categories      = <?php echo json_encode($all_categories); ?>,
			asl_markers         = <?php echo json_encode($all_markers); ?>,
			_asl_map_customize  = <?php echo ($map_customize)?$map_customize:'null'; ?>;
		*/

		$title_nonce = wp_create_nonce( 'asl_remote_nonce' );
		
		wp_localize_script( $this->AgileStoreLocator.'-script', 'ASL_REMOTE', array(
		    'ajax_url' => admin_url( 'admin-ajax.php' ),
		    'nonce'    => $title_nonce,
		));
		
		wp_localize_script( $this->AgileStoreLocator.'-script', 'asl_configuration',$all_configs);
		wp_localize_script( $this->AgileStoreLocator.'-script', 'asl_categories',$all_categories);
		wp_localize_script( $this->AgileStoreLocator.'-script', 'asl_markers',$all_markers);
		wp_localize_script( $this->AgileStoreLocator.'-script', '_asl_map_customize',(($map_customize)?$map_customize:'null'));
		
		return $sl_output;
	}
	

	private function debug($data) {

		echo '<pre>';
		print_r($data);
		echo '</pre>';
		die;
	}

	//not used
	private function add_test_stores() 
	{
		global $wpdb;

		$content = file_get_contents('wordpress_language/marc.json');
		$stores  = json_decode($content);
		


		foreach($stores as $store) {

			$store 		= (array)$store;
			$categories = $store['categories'];

			unset($store['id']);
			unset($store['days_str']);
			unset($store['categories']);
			unset($store['path']);

			$store['country'] = '105';
			
			if($wpdb->insert( ASL_PREFIX.'stores',$store)) {

				$store_id = $wpdb->insert_id;

				
				$categories = explode(',', $categories);

				foreach ($categories as $category) {	

					$wpdb->insert(ASL_PREFIX.'stores_categories', 
					 	array('store_id'=>$store_id,'category_id'=>$category),
					 	array('%s','%s'));			
				}
			}
			else {

				$wpdb->show_errors = true;

				die($wpdb->print_error());
			}
		}

		die('all done');
	}

	public function load_stores()
	{
		//header('Content-Type: application/json');
		global $wpdb;

		//$this->debug($wpdb->get_results('SHOW TABLES'));
		//$this->debug($wpdb->get_results('SELECT * FROM wp_asl_configs'));
		
		$nonce = isset($_GET['nonce'])?$_GET['nonce']:null;
		/*
		if ( ! wp_verify_nonce( $nonce, 'asl_remote_nonce' ))
 			die ( 'CRF check error.');
 		*/


		$load_all 	 = ($_GET['load_all'] == '1')?true:false;
		$accordion   = ($_GET['layout'] == '1')?true:false;
		$category    = (isset($_GET['category']))?$_GET['category']:null;
		$stores      = (isset($_GET['stores']))?$_GET['stores']:null;


		$ASL_PREFIX = ASL_PREFIX;

		$bound   = '';

		$extra_sql = '';
		$country_field = '';


		

		//Load on bound :: no Load all
		if(!$load_all) {
			
			$nw     =  $_GET['nw'];
	        $se     =  $_GET['se'];

	        $a      = floatval($nw[0]);
	        $b      = floatval($nw[1]);

	        $c      = floatval($se[0]);
	        $d      = floatval($se[1]);
	    

			$bound   = "AND (($a < $c AND s.lat BETWEEN $a AND $c) OR ($c < $a AND s.lat BETWEEN $c AND $a))
                    	AND (($b < $d AND s.lng BETWEEN $b AND $d) OR ($d < $b AND s.lng BETWEEN $d AND $b))";
       	}
       	else if($accordion) {

       		$country_field = " {$ASL_PREFIX}countries.`country`,";
       		$extra_sql = "LEFT JOIN {$ASL_PREFIX}countries ON s.`country` = {$ASL_PREFIX}countries.id";
       	}


       	$clause = '';

       	if($category) {

			$load_categories = explode(',', $category);
			$the_categories  = array();

			foreach($load_categories as $_c) {

				if(!is_nan($_c)) {

					$the_categories[] = $_c;
				}
			}

			$the_categories  = implode(',', $the_categories);
			$category_clause = " AND id IN (".$the_categories.')';
			$clause = " AND {$ASL_PREFIX}stores_categories.`category_id` IN (".$the_categories.")";
		}


       	///if marker param exist
		if($stores) {

			$stores = explode(',', $stores);

			//only number
			$store_ids = array();
			foreach($stores as $m) {

				if(!is_nan($m)) {
					$store_ids[] = $m;
				}
			}

			if($store_ids) {

				$store_ids = implode(',', $store_ids);
				$clause    .= " AND s.`id` IN ({$store_ids})";				
			}
		}



		$query   = "SELECT s.`id`, `title`,  `description`, `street`,  `city`,  `state`, `postal_code`, {$country_field} `lat`,`lng`,`phone`,  `fax`,`email`,`website`,`logo_id`,{$ASL_PREFIX}storelogos.`path`,`marker_id`,`description_2`,`open_hours`, `ordr`,
					group_concat(category_id) as categories FROM {$ASL_PREFIX}stores as s 
					LEFT JOIN {$ASL_PREFIX}storelogos ON logo_id = {$ASL_PREFIX}storelogos.id
					LEFT JOIN {$ASL_PREFIX}stores_categories ON s.`id` = {$ASL_PREFIX}stores_categories.store_id
					$extra_sql
					WHERE (is_disabled is NULL || is_disabled = 0) AND (`lat` != '' AND `lng` != '') {$bound} {$clause}
					GROUP BY s.`id` ORDER BY `title` ";

		$query .= " LIMIT 10000";

		
		$all_results = $wpdb->get_results($query);


		//die($wpdb->last_error);
		$days_in_words = array('sun'=>__( 'Sun','asl_locator'), 'mon'=>__('Mon','asl_locator'), 'tue'=>__( 'Tues','asl_locator'), 'wed'=>__( 'Wed','asl_locator' ), 'thu'=> __( 'Thur','asl_locator'), 'fri'=>__( 'Fri','asl_locator' ), 'sat'=> __( 'Sat','asl_locator')) ;
		$days 		   = array('mon','tue','wed','thu','fri','sat','sun');


		foreach($all_results as $aRow) {

			if($aRow->open_hours) {

				$days_are 	= array();
				$open_hours = json_decode($aRow->open_hours);

				foreach($days as $day) {

					if(!empty($open_hours->$day)) {

						$days_are[] = $days_in_words[$day];
					}
				}

				$aRow->days_str = implode(', ', $days_are);
			}
	    }

		echo json_encode($all_results);die;
	}

}
