<!-- Container -->
<div class="asl-p-cont">
<div class="dump-message asl-dumper"></div>
<form class="form-horizontal" id="frm-usersetting">
	<div class="row" id="message_complete"></div>	
	<div class="row">
		<div class="col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading"><h3 class="panel-title">Agile Store Locator Pro Settings</h3></div>
			  	<div class="panel-body">
			  		<div class="alert alert-info" role="alert">
				      Support Forum for New features or Troubleshooting. <a target="_blank" href="https://wordpress.org/support/plugin/agile-store-locator">Create a Ticket</a> 
				    </div>
		           	<div class="form-group s-option no-0">
			            <label class="col-sm-3 control-label" for="txt_Cluster">Cluster</label>
			          	 <select  id="asl-cluster" name="data[cluster]" class="form-control">
			            	<option value="0">OFF</option>	
			            	<option value="1">ON</option>
				        </select>
		           	</div>
		           	<div class="form-group s-option no-1">
			            <label class="col-sm-3 control-label" for="map_type">Default Map</label>
			          	 <select   id="asl-map_type" name="data[map_type]" class="form-control">
			          	 	<option value="hybrid">HYBRID</option>
				            	<option value="roadmap">ROADMAP</option>
				            	<option value="satellite">SATELLITE</option>
				            	<option value="terrain">TERRAIN</option>
				        </select>
		           	</div>
		           	<div class="form-group s-option no-2">
			            <label class="col-sm-3 control-label" for="txt_time_format">Time Format</label>
			          	 <select  id="asl-time_format" name="data[time_format]" class="form-control">
			            	<option value="0">12 Hours</option>	
			            	<option value="1">24 Hours</option>
				        </select>
		           	</div>
		           	<div class="form-group s-option no-3">
			            <label class="col-sm-3 control-label" for="display_list">Display List</label>
			          	<select id="asl-display_list" name="data[display_list]" class="form-control">
			          	 	<option value="1">Yes</option>
				            <option value="0">No</option>
				        </select>
		           	</div>
		           	<div class="form-group s-option no-4 full">
			            <label class="col-sm-3 control-label" for="distance_control">Distance Control</label>
			          	 <select  id="asl-distance_control" name="data[distance_control]" class="form-control">
				         		<option value="0">Slider</option>	
				            	<option value="1">Dropdown</option>
				        </select>
				        <p class="help-p col-sm-offset-3 col-sm-9 red">Choose Either Slider or Dropdown for Radius Miles/KM<br>
				        <a target="_blank" class="red" href="https://agilestorelocator.com/wiki/set-radius-value-distance-range-slider/">To Set Maximum value for Search Range Slider</a></p>
		           	</div>
		           	<div class="row form-group no-of-shop">
			            <label class="col-sm-3 control-label">Search Dropdown Options</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="data[dropdown_range]" id="asl-dropdown_range" placeholder="Example: 10,20,30">
						</div>
						<p class="help-p col-sm-offset-3 col-sm-9 red">Enter the Search dropdown Options number, Comma Separated. Add default value with * symbol.
						Default Value Example: 10*,20,30 . Here 10 is default value</p>
		           	</div>
		           	<div class="form-group s-option no-4 full">
			            <label class="col-sm-3 control-label" for="prompt_location">Prompt</label>
			          	 <select  id="asl-prompt_location" name="data[prompt_location]" class="form-control">
				         		<option value="0">NONE</option>	
				         		<option value="1">GEOLOCATION DIALOG</option>	
				            	<option value="2">TYPE YOUR LOCATION Dialog</option>
				        </select>
				        <p class="help-p col-sm-offset-3 col-sm-9 red">(GEOLOCATION ONLY WORKS WITH HTTPS CONNECTION)</p>
		           	</div>
		           	<div class="form-group s-option no-4 full">
			            <label class="col-sm-3 control-label" for="search_destin">Search Result</label>
			          	 <select  id="asl-search_destin" name="data[search_destin]" class="form-control">
				         		<option value="0">Default</option>
				         		<option value="1">Show My Nearest Location From Search</option>	
				        </select>
				        <p class="help-p col-sm-offset-3 col-sm-9 red">(In search address point to my nearest markers)</p>
		           	</div>
		           	<div class="form-group s-option no-4 full">
			            <label class="col-sm-3 control-label" for="sort_by">Sort List</label>
			          	 <select  id="asl-sort_by" name="data[sort_by]" class="form-control">
				         		<option value="">Default (Distance)</option>
				         		<option value="title">Store ID</option>	
				         		<option value="title">Title</option>
				         		<option value="ordr">Order Field</option>	
				         		<option value="city">City</option>	
				         		<option value="state">State</option>	
				         		<option value="logo_id">Logo ID</option>	
				        </select>
				        <p class="help-p col-sm-offset-3 col-sm-9 red">(Sort your listing based on fields, default is Distance)</p>
		           	</div>
		           	<div class="form-group s-option no-5">
			            <label class="col-sm-3 control-label" for="distance_unit">Distance Unit</label>
			          	 <select  id="asl-distance_unit" name="data[distance_unit]" class="form-control">
				            	<option value="KM">KM</option>	
				            	<option value="Miles">Miles</option>
				        </select>
		           	</div>
		           	<div class="form-group s-option no-6">
			            <label class="col-sm-3 control-label" for="asl-zoom">Default Zoom</label>
			          	 <select  id="asl-zoom" name="data[zoom]" class="form-control">
				            	<?php for($index = 2;$index <= 20;$index++):?>
				            		<option value="<?php echo $index ?>"><?php echo $index ?></option>
				            	<?php endfor; ?>
				        </select>
		           	</div>
					
					<div class="form-group s-option no-6">
			            <label class="col-sm-3 control-label" for="geo_button">Field Button Type</label>
			          	 <select  id="asl-geo_button" name="data[geo_button]" class="form-control">
				         	<option value="1">Geo-Location</option>
				            <option value="0">Search Location</option>
				        </select>
		           	</div>

		           	<div class="form-group s-option no-6">
			            <label class="col-sm-3 control-label" for="week_hours">Display Hours</label>
			          	 <select  id="asl-week_hours" name="data[week_hours]" class="form-control">
				            <option value="0">Today</option>
				         	<option value="1">7 Days</option>
				        </select>
		           	</div>

		           	<div class="form-group s-option no-7 full">
			            <label class="col-sm-3 control-label" for="asl-zoom_li">Clicked Zoom</label>
			          	 <select  id="asl-zoom_li" name="data[zoom_li]" class="form-control">
				            	<?php for($index = 2;$index <= 20;$index++):?>
				            		<option value="<?php echo $index ?>"><?php echo $index ?></option>
				            	<?php endfor; ?>
				        </select>
				        <p class="help-p col-sm-offset-3 col-sm-9 red">(Zoom Value when List Item is Clicked, use zoom_li="10" in ShortCode)</p>
		           	</div>
		           	<div class="form-group s-option no-7 full">
			            <label class="col-sm-3 control-label" for="search_type">Search Type</label>
			          	 <select  name="data[search_type]" id="asl-search_type" class="form-control">
				            	<option value="0">Search By Address</option>
				            	<option value="1">Search By Store Name</option>
				        </select>
				        <p class="help-p col-sm-offset-3 col-sm-9 red">(Search by Either Address or Store Name, use search_type="1" in ShortCode )</p>
		           	</div>
		           	<div class="form-group s-option no-7 full">
			            <label class="col-sm-3 control-label" for="load_all">Marker Load</label>
			          	 <select  name="data[load_all]" id="asl-load_all" class="form-control">
				            	<option value="1">Load All</option>
			          	 		<option value="2">Load Markers with Search Area Button</option>
				            	<option value="0">Load on Bound</option>
				        </select>
				        <p class="help-p col-sm-offset-3 col-sm-9 red">(Use Load on Bound in case of 1000 plus markers)</p>
		           	</div>
		           	<div class="form-group s-option no-7 full">
			            <label class="col-sm-3 control-label" for="single_cat_select">Category Select</label>
			          	 <select  name="data[single_cat_select]" id="asl-single_cat_select" class="form-control">
				            	<option value="0">Multiple Category Selection</option>
				            	<option value="1">Single Category Selection</option>
				        </select>
		           	</div>
		           	<div class="form-group s-option no-7 full">
			            <label class="col-sm-3 control-label">Search Type</label>
			          	 <select  name="data[google_search_type]" id="asl-google_search_type" class="form-control">
				            	<option value="">All</option>
				            	<option value="cities">Cities (Cities)</option>
				            	<option value="regions">Regions (Locality, City, State)</option>
				            	<option value="geocode">Geocode</option>
				            	<option value="address">Address</option>
				        </select>
		           	</div>       
		           	<div class="form-group s-option no-7 full">
			            <label class="col-sm-3 control-label">Full Height</label>
			          	 <select  name="data[full_height]" id="asl-full_height" class="form-control">
				            	<option value="">None</option>
				            	<option value="full-height">Full Height (Not Fixed)</option>
				            	<option value="full-height asl-fixed">Full Height (Fixed)</option>
				        </select>
		           	</div>

		           	<div class="form-group s-option no-7 full">
			            <label class="col-sm-3 control-label" for="Template">Map Region</label>
			          	 <select  id="asl-map_region" name="data[map_region]" class="form-control">
				         	<option value="">None</option>	
			            	<?php foreach($countries as $country): ?>
			            		<option value="<?php echo $country->code ?>"><?php echo $country->country ?></option>
			            	<?php endforeach ?>
				        </select>
		           	</div>

                    <div class="row form-group no-of-shop">
			            <label class="col-sm-3 control-label">Restrict Search</label>
						<div class="col-sm-8">
							<input  type="text" class="form-control validate[minSize[2]]" maxlength="2" name="data[country_restrict]" id="asl-country_restrict" placeholder="Example: US">
						</div>
						<p class="help-p col-sm-offset-3 col-sm-9">(Enter 2 alphabet country <a href="https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2" target="_blank" rel="nofollow">Code</a>)</p>
		           	</div>

                    <div class="row form-group no-of-shop">
			            <label class="col-sm-3 control-label">Map Language</label>
						<div class="col-sm-8">
							<input type="text" class="form-control validate[minSize[2]]" maxlength="2" name="data[map_language]" id="asl-map_language" placeholder="Example: US">
						</div>
						<p class="help-p col-sm-offset-3 col-sm-9">(Enter the Language Code. <a href="https://agilestorelocator.com/wiki/display-maps-different-language/" target="_blank" rel="nofollow">Get Code</a>)</p>
		           	</div>
                    <br clear="both">

		           	<div class="form-group c-option no-0 thirdpart">
			            <label class="col-sm-3 control-label" for="asl-show_categories">Show Categories</label>
			          	<input type="checkbox" value="1" id="asl-show_categories" name="data[show_categories]">
		           	</div>
		           	<div class="form-group c-option no-1 thirdpart">
			            <label class="col-sm-3 control-label" for="asl-time_switch">Time Switch</label>
			          	<input type="checkbox" value="1" id="asl-time_switch" name="data[time_switch]">
		           	</div>
		           	<!--
		           	<div class="form-group">
			            <label class="col-sm-3 control-label" for="asl-direction">Direction Service</label>
			          	<input type="checkbox" value="1" id="asl-direction" name="data[direction]">
		           	</div>
		           	-->
		           	<div class="form-group c-option no-2 thirdpart">
			            <label class="col-sm-3 control-label" for="asl-additional_info">Additional Info</label>
			          	<input type="checkbox" value="1" id="asl-additional_info" name="data[additional_info]">
		           	</div>
		           	<div class="form-group c-option no-3 thirdpart">
			            <label class="col-sm-3 control-label" for="asl-advance_filter">Advance Filter</label>
			          	<input type="checkbox" value="1"  id="asl-advance_filter" name="data[advance_filter]">
		           	</div>
		           	<div class="form-group c-option no-4 thirdpart">
			            <label class="col-sm-3 control-label" for="asl-category_marker">Category marker</label>
			          	<input type="checkbox" value="1"  id="asl-category_marker" name="data[category_marker]">
			          	<p class="help-p red">(Single Category Selection)</p>
		           	</div>
		           	<div class="form-group c-option no-5 thirdpart">
			            <label class="col-sm-3 control-label" for="asl-distance_slider">Distance Control</label>
			          	<input type="checkbox" value="1"  id="asl-distance_slider" name="data[distance_slider]">
		           	</div>
		           	<div class="form-group c-option no-6 thirdpart">
			            <label class="col-sm-3 control-label" for="asl-full_width">Full Width</label>
			          	<input type="checkbox" value="1"  id="asl-full_width" name="data[full_width]">
		           	</div>
		           	<div class="form-group c-option no-6 thirdpart">
			            <label class="col-sm-3 control-label" for="asl-analytics">Analytics</label>
			          	<input type="checkbox" value="1"  id="asl-analytics" name="data[analytics]">
		           	</div>
		           	<div class="form-group c-option no-6 thirdpart">
			            <label class="col-sm-3 control-label" for="asl-scroll_wheel">Mouse Scroll</label>
			          	<input type="checkbox" value="1"  id="asl-scroll_wheel" name="data[scroll_wheel]">
		           	</div>
		           	<div class="form-group c-option no-6 thirdpart">
			            <label class="col-sm-3 control-label" for="asl-sort_by_bound">Sort By Bound</label>
			          	<input type="checkbox" value="1"  id="asl-sort_by_bound" name="data[sort_by_bound]">
		           	</div>
		           	<div class="form-group c-option no-6 thirdpart">
			            <label class="col-sm-3 control-label" for="asl-target_blank">Open Link New Tab</label>
			          	<input type="checkbox" value="1" id="asl-target_blank" name="data[target_blank]">
		           	</div>
		           	<br clear="both">
		           	<div class="form-group page_layout">
			            <label class="col-sm-3 control-label" >Layout</label>
						<div id="radio">
						    <input type="radio" id="asl-layout-0" value="0" name="data[layout]"><label for="asl-layout-0"><img src="<?php echo ASL_URL_PATH ?>/admin/images/layout_1.png" /></label>
						    <input type="radio" id="asl-layout-1" value="1" name="data[layout]"><label for="asl-layout-1"><img src="<?php echo ASL_URL_PATH ?>/admin/images/layout_2.png" /></label>
					  	</div>
		           	</div>
		           	<br clear="both">
		           	<div class="form-group lng-lat">
			            <label class="col-sm-3 control-label">Default Lat/Lng</label>
						<div class="col-sm-4">
							<input  type="text" class="form-control validate[required]" name="data[default_lat]" id="asl-default_lat" placeholder="Lat">
						</div>
						<div class="col-sm-4">
						<input  type="text" class="form-control validate[required]" name="data[default_lng]"  id="asl-default_lng" placeholder="Lng">
						</div>
						<p class="help-p col-sm-offset-3 col-sm-9 red">(Default coordinates for map to load)</p>
		           	</div>
		           	<br clear="both">
		           	<div class="row form-group no-of-shop">
			            <label class="col-sm-3 control-label">Header Title</label>
						<div class="col-sm-8">
							<input  type="text" class="form-control validate[required]" name="data[head_title]" id="asl-head_title" placeholder="Head title">
						</div>
		           	</div>
                    <br clear="both">

                    <div class="row form-group no-of-shop">
			            <label class="col-sm-3 control-label">Category Title</label>
						<div class="col-sm-8">
							<input  type="text" class="form-control validate[required]" name="data[category_title]" id="asl-category_title" placeholder="Category title">
						</div>
		           	</div>
                    <br clear="both">

                    <div class="row form-group no-of-shop">
			            <label class="col-sm-3 control-label">No Item Text</label>
						<div class="col-sm-8">
							<input  type="text" class="form-control validate[required]" name="data[no_item_text]" id="asl-no_item_text" placeholder="No Item Text">
						</div>
		           	</div>
                    <br clear="both">

                    <div class="row form-group no-of-shop">
			            <label class="col-sm-3 control-label">Google API KEY</label>
						<div class="col-sm-8">
							<input  type="text" class="form-control" name="data[api_key]" id="asl-api_key" placeholder="API KEY">
						</div>
						<p class="help-p col-sm-offset-3 col-sm-9 red">(Generate Key from  google console if required)</p>
		           	</div>
                    <br clear="both">
                    
                    <div class="form-group map_layout">
			            <label class="col-sm-3 control-label" >Map Layout</label>
						<div id="radio">
						    <input type="radio" id="asl-map_layout-0" value="0" name="data[map_layout]"><label for="asl-map_layout-0"><img src="<?php echo ASL_URL_PATH ?>admin/images/map/25-blue-water/25-blue-water.png" /></label>
						    <input type="radio" id="asl-map_layout-1" value="1" name="data[map_layout]"><label for="asl-map_layout-1"><img src="<?php echo ASL_URL_PATH ?>admin/images/map/Flat Map/53-flat-map.png" /></label>
						    <input type="radio" id="asl-map_layout-2" value="2" name="data[map_layout]"><label for="asl-map_layout-2"><img src="<?php echo ASL_URL_PATH ?>admin/images/map/Icy Blue/7-icy-blue.png" /></label>
						    <input type="radio" id="asl-map_layout-3" value="3" name="data[map_layout]"><label for="asl-map_layout-3"><img src="<?php echo ASL_URL_PATH ?>admin/images/map/Pale Dawn/1-pale-dawn.png" /></label>
						    <input type="radio" id="asl-map_layout-4" value="4" name="data[map_layout]"><label for="asl-map_layout-4"><img src="<?php echo ASL_URL_PATH ?>admin/images/map/cladme/6618-cladme.png" /></label>
						    <input type="radio" id="asl-map_layout-5" value="5" name="data[map_layout]"><label for="asl-map_layout-5"><img src="<?php echo ASL_URL_PATH ?>admin/images/map/light monochrome/29-light-monochrome.png" /></label>
						    <input type="radio" id="asl-map_layout-6" value="6" name="data[map_layout]"><label for="asl-map_layout-6"><img src="<?php echo ASL_URL_PATH ?>admin/images/map/mostly grayscale/4183-mostly-grayscale.png" /></label>
						    <input type="radio" id="asl-map_layout-7" value="7" name="data[map_layout]"><label for="asl-map_layout-7"><img src="<?php echo ASL_URL_PATH ?>admin/images/map/turquoise water/8-turquoise-water.png" /></label>
						    <input type="radio" id="asl-map_layout-8" value="8" name="data[map_layout]"><label for="asl-map_layout-8"><img src="<?php echo ASL_URL_PATH ?>admin/images/map/unsaturated browns/70-unsaturated-browns.png" /></label>
					  	</div>
		           	</div>
                    
                    <div class="form-group" style="width:100%">
			            <label class="col-sm-3 control-label" for="Template">Template</label>
			          	 <select  id="asl-template" name="data[template]" class="form-control" style="width:200px">
				         		<option value="0" data-src="<?php echo ASL_URL_PATH ?>admin/images/template-0.jpg">Template 0</option>	
				         		<option value="1" data-src="<?php echo ASL_URL_PATH ?>admin/images/template-1.jpg">Template 1</option>	
				         		<option value="2" data-src="<?php echo ASL_URL_PATH ?>admin/images/template-1.jpg">Template 2</option>
				        </select>
		           	</div>
		           	<label class="layout-box" style="width: 50%; clear: both; margin-left: 27%; display: inline-block; margin-top: -10px;">
                        <img src="<?php echo ASL_URL_PATH ?>admin/images/template-0.jpg" style="max-width: 100%">
                        <img src="<?php echo ASL_URL_PATH ?>admin/images/template-1.jpg" style="max-width: 100%">
                        <img src="<?php echo ASL_URL_PATH ?>admin/images/template-2.jpg" style="max-width: 100%">
                    </label>
                    <br clear="both">
                    
                    
                    <div class="template-box box_layout_0 hide"> <div class="row form-group color_scheme">
				            <label class="col-sm-3 control-label">Color Scheme</label>
							<div id="radio" >
                                <span>
                                    <input type="radio" id="asl-color_scheme-0" value="0" name="data[color_scheme]">
                                    <label class="color-box color-0" for="asl-color_scheme-0"></label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme-1" value="1" name="data[color_scheme]">
                                    <label class="color-box color-1" for="asl-color_scheme-1"></label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme-2" value="2" name="data[color_scheme]">
                                    <label class="color-box color-2" for="asl-color_scheme-2"></label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme-3" value="3" name="data[color_scheme]">
                                    <label class="color-box color-3" for="asl-color_scheme-3"></label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme-4" value="4" name="data[color_scheme]">
                                    <label class="color-box color-4" for="asl-color_scheme-4"></label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme-5" value="5" name="data[color_scheme]">
                                    <label class="color-box color-5" for="asl-color_scheme-5"></label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme-6" value="6" name="data[color_scheme]">
                                    <label class="color-box color-6" for="asl-color_scheme-6"></label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme-7" value="7" name="data[color_scheme]">
                                    <label class="color-box color-7" for="asl-color_scheme-7"></label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme-8" value="8" name="data[color_scheme]">
                                    <label class="color-box color-8" for="asl-color_scheme-8"></label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme-9" value="9" name="data[color_scheme]">
                                    <label class="color-box color-9" for="asl-color_scheme-9"></label>
                                </span>
                            </div>
			           	</div>
			           	<div class="row form-group Font_color">
				            <label class="col-sm-3 control-label">Font Colors</label>
							<div id="radio">
                                <span>
                                    <input type="radio" id="asl-font_color_scheme-0" value="0" name="data[font_color_scheme]">
                                    <label class="font-color-box color-0" for="asl-font_color_scheme-0"></label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-font_color_scheme-1" value="1" name="data[font_color_scheme]">
                                    <label class="font-color-box color-1" for="asl-font_color_scheme-1"></label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-font_color_scheme-2" value="2" name="data[font_color_scheme]">
                                    <label class="font-color-box color-2" for="asl-font_color_scheme-2"></label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-font_color_scheme-3" value="3" name="data[font_color_scheme]">
                                    <label class="font-color-box color-3" for="asl-font_color_scheme-3"></label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-font_color_scheme-4" value="4" name="data[font_color_scheme]">
                                    <label class="font-color-box color-4" for="asl-font_color_scheme-4"></label>
                                </span>
                            </div>
			           	</div>
                        <div class="form-group infobox_layout">
				            <label class="col-sm-3 control-label" >Infobox Layout</label>
							<div id="radio">
							    <input type="radio" id="asl-infobox_layout-0" value="0" name="data[infobox_layout]"><label for="asl-infobox_layout-0"><img src="<?php echo ASL_URL_PATH ?>/admin/images/infobox_1.png" /></label>
							    <input type="radio" id="asl-infobox_layout-2" value="2" name="data[infobox_layout]"><label for="asl-infobox_layout-2"><img src="<?php echo ASL_URL_PATH ?>/admin/images/infobox_2.png" /></label>
							    <input type="radio" id="asl-infobox_layout-1" value="1" name="data[infobox_layout]"><label for="asl-infobox_layout-1"><img src="<?php echo ASL_URL_PATH ?>/admin/images/infobox_3.png" /></label>
						  	</div>
			           	</div>
                    </div>
                    <div class="template-box box_layout_1 hide">
			           	<div class="row form-group color_scheme layout_2">
				            <label class="col-sm-3 control-label">Color Scheme</label>
							<div id="radio" >
                                <span>
                                    <input type="radio" id="asl-color_scheme_1-0" value="0" name="data[color_scheme_1]">
                                    <label class="color-box color-0" for="asl-color_scheme_1-0">
                                        <span class="co_1"></span>
                                        <span class="co_2"></span>
                                    </label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme_1-1" value="1" name="data[color_scheme_1]">
                                    <label class="color-box color-1" for="asl-color_scheme_1-1">
                                        <span class="co_1"></span>
                                        <span class="co_2"></span>
                                    </label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme_1-2" value="2" name="data[color_scheme_1]">
                                    <label class="color-box color-2" for="asl-color_scheme_1-2">
                                        <span class="co_1"></span>
                                        <span class="co_2"></span>
                                    </label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme_1-3" value="3" name="data[color_scheme_1]">
                                    <label class="color-box color-3" for="asl-color_scheme_1-3">
                                        <span class="co_1"></span>
                                        <span class="co_2"></span>
                                    </label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme_1-4" value="4" name="data[color_scheme_1]">
                                    <label class="color-box color-4" for="asl-color_scheme_1-4">
                                        <span class="co_1"></span>
                                        <span class="co_2"></span>
                                    </label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme_1-5" value="5" name="data[color_scheme_1]">
                                    <label class="color-box color-5" for="asl-color_scheme_1-5">
                                        <span class="co_1"></span>
                                        <span class="co_2"></span>
                                    </label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme_1-6" value="6" name="data[color_scheme_1]">
                                    <label class="color-box color-6" for="asl-color_scheme_1-6">
                                        <span class="co_1"></span>
                                        <span class="co_2"></span>
                                    </label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme_1-7" value="7" name="data[color_scheme_1]">
                                    <label class="color-box color-7" for="asl-color_scheme_1-7">
                                        <span class="co_1"></span>
                                        <span class="co_2"></span>
                                    </label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme_1-8" value="8" name="data[color_scheme_1]">
                                    <label class="color-box color-8" for="asl-color_scheme_1-8">
                                        <span class="co_1"></span>
                                        <span class="co_2"></span>
                                    </label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme_1-9" value="9" name="data[color_scheme_1]">
                                    <label class="color-box color-9" for="asl-color_scheme_1-9">
                                        <span class="co_1"></span>
                                        <span class="co_2"></span>
                                    </label>
                                </span>
                            </div>
			           	</div>
                    </div>
                    <div class="template-box box_layout_2 hide">
			           	<div class="row form-group color_scheme layout_2">
				            <label class="col-sm-3 control-label">Color Scheme</label>
							<div id="radio" >
                                <span>
                                    <input type="radio" id="asl-color_scheme_2-0" value="0" name="data[color_scheme_2]">
                                    <label class="color-box color-0" for="asl-color_scheme_2-0" style="background-color:#CC3333">
                                        <span class="co_1" style="background-color:#542733" ></span>
                                    </label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme_2-1" value="1" name="data[color_scheme_2]">
                                    <label class="color-box color-1" for="asl-color_scheme_2-1" style="background-color:#008FED">
                                        <span class="co_1" style="background-color:#2580C3"></span>
                                    </label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme_2-2" value="2" name="data[color_scheme_2]">
                                    <label class="color-box color-2" for="asl-color_scheme_2-2" style="background-color:#93628F">
                                        <span class="co_1" style="background-color:#4A2849"></span>
                                    </label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme_2-3" value="3" name="data[color_scheme_2]">
                                    <label class="color-box color-3" for="asl-color_scheme_2-3" style="background-color:#FF9800">
                                        <span class="co_1" style="background-color:#FFC107"></span>
                                    </label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme_2-4" value="4" name="data[color_scheme_2]">
                                    <label class="color-box color-4" for="asl-color_scheme_2-4" style="background-color:#01524B">
                                        <span class="co_1" style="background-color:#75C9D3"></span>
                                    </label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme_2-5" value="5" name="data[color_scheme_2]">
                                    <label class="color-box color-5" for="asl-color_scheme_2-5" style="background-color:#ED468B">
                                        <span class="co_1" style="background-color:#FDCC29"></span>
                                    </label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme_2-6" value="6" name="data[color_scheme_2]">
                                    <label class="color-box color-6" for="asl-color_scheme_2-6" style="background-color:#D55121">
                                        <span class="co_1" style="background-color:#FB9C6C"></span>
                                    </label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme_2-7" value="7" name="data[color_scheme_2]">
                                    <label class="color-box color-7" for="asl-color_scheme_2-7" style="background-color:#D13D94">
                                        <span class="co_1" style="background-color:#AD0066"></span>
                                    </label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme_2-8" value="8" name="data[color_scheme_2]">
                                    <label class="color-box color-8" for="asl-color_scheme_2-8" style="background-color:#99BE3B">
                                        <span class="co_1" style="background-color:#01735A"></span>
                                    </label>
                                </span>
                                <span>
                                    <input type="radio" id="asl-color_scheme_2-9" value="9" name="data[color_scheme_2]">
                                    <label class="color-box color-9" for="asl-color_scheme_2-9" style="background-color:#3D5B99">
                                        <span class="co_1" style="background-color:#EFF1F6"></span>
                                    </label>
                                </span>
                            </div>
			           	</div>
                    </div>

		           	<div class="form-group ralign">
			            <button type="button" class="btn btn-primary mrg-r-10" data-loading-text="Saving..." data-completed-text="Settings Updated" id="btn-asl-user_setting">Save Settings</button>
		           	</div>
				</div>
			</div>
		</div>
		<div class="col-md-5 pad-t-22">
			<div class="alert alert-info" role="alert">
		      If you like our Plugin, please rate us 5 stars. <a target="_blank" href="https://codecanyon.net/item/agile-store-locator-google-maps-for-wordpress/reviews/16973546"><span style="margin-top: 2px"><i class="glyphicon glyphicon-star"></i><i class="glyphicon glyphicon-star"></i><i class="glyphicon glyphicon-star"></i><i class="glyphicon glyphicon-star"></i><i class="glyphicon glyphicon-star"></i></span></a> 
		    </div>
		    <div class="alert alert-info" role="alert">
		      If you have any problem with the plugin or suggestion,<br> please email us at <a  href="mailto:support@agilelogix.com">support@agilelogix.com</a><br> 
		      We will respond as soon as possible to resolve your problem.
		    </div>
		</div>
	</div>
</form>
</div>

<!-- SCRIPTS -->
<script type="text/javascript">
	var ASL_Instance = {
		url: '<?php echo ASL_URL_PATH ?>'
	},
	asl_configs =  <?php echo json_encode($all_configs); ?>;
	asl_engine.pages.user_setting(asl_configs);
</script>