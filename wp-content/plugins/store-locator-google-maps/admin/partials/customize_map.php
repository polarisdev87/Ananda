<!-- Container -->
<div class="asl-p-cont">
<div class="dump-message asl-dumper"></div>
<div class="row" id="message_complete">
</div>
	<h3  class="alert alert-info head-1">Customize Map</h3>
	<div class="well">
        <div class="row map-option-top" style="margin-bottom:10px">
            <div class="col-md-2">
                <div class="btn-group" id="asl-fill-option">
                  <label class="btn btn-default" data-value="0">
                    Border
                  </label>
                  <label class="btn btn-default" data-value="1">
                    Solid
                  </label>
                </div>
            </div>
            <div class="col-md-3">
            	<button class="btn btn-warning" type="button" id="asl-delete-shape"><span style="margin-right:10px;font-size:12px" class="glyphicon glyphicon-trash"></span><span>Delete</span></button>
            	<button class="btn btn-danger" type="button" id="asl-clear-all"><span>Clear All</span></button>
            </div>
            <div class="col-md-7">
            <div class="color_scheme pull-right">
                    <div id="radio" class="map_cange">
                        <span>
                            <input type="radio" id="asl-color_scheme-0" value="#CC3333" name="data[color_scheme]">
                            <label class="color-box color-0" for="asl-color_scheme-0"></label>
                        </span>
                        <span>
                            <input type="radio" id="asl-color_scheme-1" value="#E11619" name="data[color_scheme]">
                            <label class="color-box color-1" for="asl-color_scheme-1"></label>
                        </span>
                        <span>
                            <input type="radio" id="asl-color_scheme-2" value="#542733" name="data[color_scheme]">
                            <label class="color-box color-2" for="asl-color_scheme-2"></label>
                        </span>
                        <span>
                            <input type="radio" id="asl-color_scheme-3" value="#278BBC" name="data[color_scheme]">
                            <label class="color-box color-3" for="asl-color_scheme-3"></label>
                        </span>
                        <span>
                            <input type="radio" id="asl-color_scheme-4" value="#78C1E4" name="data[color_scheme]">
                            <label class="color-box color-4" for="asl-color_scheme-4"></label>
                        </span>
                        <span>
                            <input type="radio" id="asl-color_scheme-5" value="#ACD55D" name="data[color_scheme]">
                            <label class="color-box color-5" for="asl-color_scheme-5"></label>
                        </span>
                        <span>
                            <input type="radio" id="asl-color_scheme-6" value="#A8BD78" name="data[color_scheme]">
                            <label class="color-box color-6" for="asl-color_scheme-6"></label>
                        </span>
                        <span>
                            <input type="radio" id="asl-color_scheme-7" value="#EAAE40" name="data[color_scheme]">
                            <label class="color-box color-7" for="asl-color_scheme-7"></label>
                        </span>
                        <span>
                            <input type="radio" id="asl-color_scheme-8" value="#E68EC1" name="data[color_scheme]">
                            <label class="color-box color-8" for="asl-color_scheme-8"></label>
                        </span>
                        <span>
                            <input type="radio" id="asl-color_scheme-9" value="#B39571" name="data[color_scheme]">
                            <label class="color-box color-9" for="asl-color_scheme-9"></label>
                        </span>
                    </div>
                </div>
            </div>
        </div>
		<div class="row">
			<div class="map_canvas" style="height:500px" id="map_canvas"></div>
		</div>
		<form id="frm-asl-layers">
	        <div class="row map-option-bottom">
	            <div class="col-xs-3">
	                <label class="">Traffic Layer</label>
	                <div class="onoffswitch2">
	                    <input type="checkbox" name="data[trafic_layer]" class="onoffswitch2-checkbox" id="asl-trafic_layer">
	                    <label class="onoffswitch2-label" for="asl-trafic_layer">
	                        <span class="onoffswitch2-inner"></span>
	                        <span class="onoffswitch2-switch"></span>
	                    </label>
	                </div>
	            </div>
	            <div class="col-xs-3">
	                <label class="">Transit Layer</label>
	                <div class="onoffswitch2">
	                    <input type="checkbox" name="data[transit_layer]" class="onoffswitch2-checkbox" id="asl-transit_layer" >
	                    <label class="onoffswitch2-label" for="asl-transit_layer">
	                        <span class="onoffswitch2-inner"></span>
	                        <span class="onoffswitch2-switch"></span>
	                    </label>
	                </div>
	            </div>
	            <div class="col-xs-3">
	                <label class="">bike Layer</label>
	                <div class="onoffswitch2">
	                    <input type="checkbox" name="data[bike_layer]" class="onoffswitch2-checkbox" id="asl-bike_layer" >
	                    <label class="onoffswitch2-label" for="asl-bike_layer">
	                        <span class="onoffswitch2-inner"></span>
	                        <span class="onoffswitch2-switch"></span>
	                    </label>
	                </div>
	            </div>
	            <div class="col-xs-3">
	                <label class="">Marker Animation</label>
	                <div class="onoffswitch2">
	                    <input type="checkbox" name="data[marker_animations]" class="onoffswitch2-checkbox" id="asl-marker_animations">
	                    <label class="onoffswitch2-label" for="asl-marker_animations">
	                        <span class="onoffswitch2-inner"></span>
	                        <span class="onoffswitch2-switch"></span>
	                    </label>
	                </div>
	            </div>
	        </div>
        </form>
        <div class="row">
	        <div class="col-xs-12 pd-0 form-group btn-group">
	            <button type="button" id="asl-save-map" data-loading-text="Saving..." class="pull-right btn btn-primary">Save Customization</button>
	        </div>
	    </div>
	</div>

</div>

<!-- SCRIPTS -->
<script type="text/javascript">
	var ASL_Instance = {
		url: '<?php echo ASL_URL_PATH ?>'
	};

	var asl_configs 	  =  <?php echo json_encode($all_configs); ?>;
	var asl_map_customize =  <?php echo $map_customize; ?>;
	
	asl_engine.pages.customize_map(asl_map_customize);
</script>