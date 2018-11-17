//var saved = {"zoom":9,"center":[40.13425922366932,-91.1920161015625],"shapes":[null,null,null,{"type":"polygon","color":"#1E90FF","coord":[[40.67855510939917,-91.72210693359375],[40.611867443030064,-91.56005859375],[40.52006312552015,-91.58477783203125],[40.52215098562377,-91.8072509765625],[40.632714496550626,-91.834716796875]]},{"type":"rectangle","color":"#1E90FF","ne":[40.80965166748856,-90.69488525390625],"sw":[40.703545803451426,-90.9283447265625]},{"type":"circle","color":"#1E90FF","center":[40.62437645591559,-91.3211059570312],"radius":7629.6704397923095}],"markers":[[40.93011520598304,-91.94183349609375],[40.80757278825516,-91.23046875]]};
var asl_drawing = {};

(function( $,app_engine ) {

	var selectedColor;
	asl_drawing = {
		shapes : [],
		//allow_edit : true,
		shapes_index : 0,
		scale : null,
		post_data : [],
		markers:[],
		current_map: null,
		drawingManager:null,
		selectedShape: null,
		initialize: function(_map,no_drawing){

			if(!google.maps.drawing)return;
			
			var that = this;

			that.current_map = _map;
			
			if(no_drawing) {

				//map_options['draggable'] 	= false,
				map_options['draggable'] 	= true,
				map_options['scrollwheel'] 	= false,
				map_options['disableDefaultUI'] = false,
				map_options['disableDoubleClickZoom'] = true,
				map_options['zoomControl'] 	= false,
				map_options['panControl'] 	= false;
			}
			

			var polyOptions = {
				strokeWeight : 1,
				strokeOpacity : .75,
				fillOpacity : 0.45,
				editable : false,
				strokeColor: '#CC3333',
				fillColor: '#CC3333'
			};

			//instant Initialize
			that.drawingManager = new google.maps.drawing.DrawingManager({
					drawingControlOptions : {
						drawingModes : [/*google.maps.drawing.OverlayType.MARKER, */google.maps.drawing.OverlayType.CIRCLE, google.maps.drawing.OverlayType.POLYGON, google.maps.drawing.OverlayType.RECTANGLE]
					},
					markerOptions : {
						draggable : true
					},
					polylineOptions : {
						editable : true
					},
					rectangleOptions : polyOptions,
					circleOptions : polyOptions,
					polygonOptions : polyOptions,
					map : that.current_map
			});


			//add the button bind for Colors
			$('.asl-p-cont .color_scheme input').bind('click',function(e){

				that.selectColor(this.value);
				that.setSelectedShapeColor(this.value);
			});


			$('#asl-fill-option label').bind('click',function(e){

				var value = $(this).data('value');

				if(that.selectedShape)
					if(value == 0) {

						console.log(this.value);
						that.selectedShape.set('fillColor','transparent');
					}
					else
						that.selectedShape.set('fillColor',that.selectedShape.get('strokeColor'));
			});

			//Overlay Complete
			google.maps.event.addListener(that.drawingManager, 'overlaycomplete', function (e) {
				
				if (e.type == google.maps.drawing.OverlayType.MARKER) {
					that.markers.push(e);
					return true;
				}

				that.shapes.push(e);
				that.drawingManager.setDrawingMode(null);
				var newShape = e.overlay;
				newShape.type = e.type;
				newShape._id_ = new Date().getTime();

				google.maps.event.addListener(newShape, 'click', function () {
					that.setSelection(newShape);
				});
				that.setSelection(newShape);
			});
			
				
			//Change Event
			google.maps.event.addListener(that.drawingManager, 'drawingmode_changed', that.clearSelection);
			google.maps.event.addListener(that.current_map, 'click', that.clearSelection);



			$('#asl-delete-shape').bind('click',function(e){

				that.deleteSelectedShape();
			});

			$('#asl-clear-all').bind('click',function(e){

				that.clearAll();
			});

			
			google.maps.Polygon.prototype.my_getBounds=function(){
			    var bounds = new google.maps.LatLngBounds()
			    this.getPath().forEach(function(element,index){bounds.extend(element)})
			    return bounds
			};
		},
		bind_selection: function(_new_shape) {

			var that = this;

			google.maps.event.addListener(_new_shape, 'click', function () {
				
				that.setSelection(_new_shape);
			});
		},
		loadData: function(saved) {
			
			var that = this;
			_map = that.current_map;

			var index_c = new Date().getTime();

			var saved_shapes = [];

			for(var i in saved.shapes) {

				if(!saved.shapes[i])continue;


				if(saved.shapes[i].type == 'polygon') {

					var new_shape = that.create_polygon.call(that,saved.shapes[i].coord,_map,saved.shapes[i]);
					
					new_shape.type = saved.shapes[i].type;
					new_shape._id_ = index_c;

					that.shapes.push(new_shape);

					that.bind_selection(new_shape);
				}
				else if(saved.shapes[i].type == 'circle') {
					
					var new_shape = that.create_circle.call(that,saved.shapes[i],_map);

					new_shape.type = saved.shapes[i].type;
					new_shape._id_ = index_c;

					that.shapes.push(new_shape);

					that.bind_selection(new_shape);
				}
				else if(saved.shapes[i].type == 'rectangle') {

					var new_shape = that.create_rectangle.call(that,saved.shapes[i],_map);
					
					new_shape.type = saved.shapes[i].type;
					new_shape._id_ = index_c;

					that.shapes.push(new_shape);

					that.bind_selection(new_shape);
				}

				index_c++;
			}
		},
		clearAll: function(){

			//All the marker clear
			for(var i in this.markers)
			{
				if(this.markers[i].setMap)
					this.markers[i].setMap(null);
				else if(this.markers[i].overlay)
					this.markers[i].overlay.setMap(null);

				delete this.markers[i];
			}

			this.markers = [];

			//All the shapes clear
			for (var i in this.shapes) {
				
				if(this.shapes[i].setMap)
					this.shapes[i].setMap(null);
				else
					this.shapes[i].overlay.setMap(null);

				delete this.shapes[i];
			}

			this.shapes = [];
		},
		deleteSelectedShape: function() {
			

			if (this.selectedShape) {
				this.selectedShape.setMap(null);

				//Delete from shapes
				for(var k in this.shapes) {

					var shape = (this.shapes[k].overlay)?this.shapes[k].overlay:this.shapes[k];

					if(shape._id_ == asl_drawing.selectedShape._id_) {
						this.shapes.splice(k,1);
						break;
					}
				}
			}
		},
		selectColor: function(color){

			var that = this;
			selectedColor = color;
			var polylineOptions = that.drawingManager.get('polylineOptions');
			polylineOptions.strokeColor = /*polylineOptions.fillColor = */color;
			that.drawingManager.set('polylineOptions', polylineOptions);
			var rectangleOptions = that.drawingManager.get('rectangleOptions');
			rectangleOptions.fillColor = /*rectangleOptions.strokeColor =*/ color;
			that.drawingManager.set('rectangleOptions', rectangleOptions);
			var circleOptions = that.drawingManager.get('circleOptions');
			circleOptions.fillColor = /*circleOptions.strokeColor = */color;
			that.drawingManager.set('circleOptions', circleOptions);
			var polygonOptions = that.drawingManager.get('polygonOptions');
			polygonOptions.fillColor = /*polygonOptions.strokeColor =*/ color;
			that.drawingManager.set('polygonOptions', polygonOptions);
		},
		setSelectedShapeColor: function(color) {

			if (this.selectedShape) {
				if (this.selectedShape.type == google.maps.drawing.OverlayType.POLYLINE) {


					this.selectedShape.set('strokeColor', color);

					if(this.selectedShape.get('fillColor') != 'transparent')
						this.selectedShape.set('fillColor', color);
				}
				else {

					if(this.selectedShape.get('fillColor') != 'transparent')
						this.selectedShape.set('fillColor', color);
					this.selectedShape.set('strokeColor', color);
				}
			}
		},
		setSelection: function(shape) {

			asl_drawing.clearSelection();

			this.selectedShape = shape;

			var color = shape.get('fillColor') || shape.get('strokeColor');

			$('.asl-p-cont .color_scheme input').each(function(e){

				if(this.value == color) {

					this.checked = true;
					return true;
				}
			})

			shape.setEditable(true);
			this.selectColor(color);
			/*
			if (shape.type == 'polygon') {
				//var paths = shape.getPaths().getAt(0);
				//sqm = google.maps.geometry.spherical.computeArea(paths);
				//$('#CalcAcres').html(sqm * 0.000247105 + ' Acres');
			} else {
				//$('#CalcAcres').html('');
			}
			*/
		},
		clearSelection: function() {

			if (this.selectedShape) {
				this.selectedShape.setEditable(false);
				this.selectedShape = null;
			}
		},
		create_marker: function(_data) {

			var _map = that.current_map;
			var myLatLng = {lat: _data[0], lng: _data[1]};
			
			return new google.maps.Marker({
				position: myLatLng,
				map: _map
			});
		},
		create_image_marker: function(_data,_icon,_map) {

			if(!_map)_map = this.current_map;

			var myLatLng = {lat: _data[0], lng: _data[1]};
			

			var marker = new google.maps.Marker({
				position: myLatLng,
				map: _map,
				icon: _icon
			});
			/*var marker_icon = new google.maps.MarkerImage(_icon, null, null, null);
			marker.setIcon(marker_icon);*/
			
			return marker;
			/*var infowindow =  new google.maps.InfoWindow({
				content: 'Your Location'
			});*/
		},
		create_rectangle: function(_data,_map) {
			
			if(!_map)_map = this.current_map;

	        return  new google.maps.Rectangle({
				strokeColor: _data.strokeColor,
				fillColor: _data.color,
				strokeWeight : 1,
				type:'rectangle',
				editable:(asl_drawing.allow_edit)?asl_drawing.allow_edit:false,
				map: _map,
				bounds: new google.maps.LatLngBounds(
				new google.maps.LatLng(_data.sw[0], _data.sw[1]),
				new google.maps.LatLng(_data.ne[0],_data.ne[1]))
			});  
		},
		create_circle: function(_data,_map) {
			
			if(!_map)_map = this.current_map;


			return new google.maps.Circle({
				strokeColor: _data.strokeColor,
				fillColor: _data.color,
				type:'circle',
				strokeWeight : 1,
				map: _map,
				editable:(asl_drawing.allow_edit)?asl_drawing.allow_edit:false,
				center: new google.maps.LatLng(_data.center[0], _data.center[1]),
				radius: _data.radius
		    });
	        
		},
		create_polygon: function(_points,_map,_shape) {

	        if(!_map)_map = this.current_map;


	        var coords = [];

	        for(var i in _points){

	        	coords.push({lat: _points[i][0], lng: _points[i][1]});
	        }
	 	
	        return new google.maps.Polygon({
			    paths: coords,
				fillColor: _shape.color,
			    strokeColor: _shape.strokeColor,
			    strokeWeight : 1,
			    editable: (asl_drawing.allow_edit)?true:false, // Editable geometry off by default
			    type:'polygon',
			    map:_map
			});

		},
		polygon : function (e) {

			var handler = this;
			var map_scale = handler.scale;
			var cordinates = [];
			var _shape  = (e.overlay)?e.overlay:e; 
			var temp = {
				type : _shape.type,
				color : _shape.fillColor,
				strokeColor : _shape.strokeColor
			};
			
			var paths = _shape.getPath().getArray();
			
			for (var i = 0; i < paths.length; i++) {
				//cord = handler.fromLatLngToPoints(map, cord, map_scale);
				//cordinates.push([cord.x,cord.y]);
				cordinates.push([paths[i].lat(), paths[i].lng()]);
			}

			temp['coord'] = cordinates;
			handler.post_data[handler.shapes_index] = temp;
			handler.shapes_index++;
		},
		circle : function (e) {
			

			var handler = this;
			var _shape  = (e.overlay)?e.overlay:e; 
			var map_scale = handler.scale;
			var temp = {
				type : _shape.type,
				color : _shape.fillColor,
				strokeColor : _shape.strokeColor
			};

			var center = _shape.getBounds().getCenter();

			temp['center'] = [center.lat(), center.lng()];
			temp['radius'] = _shape.getRadius();

			
			handler.post_data[handler.shapes_index] = temp;
			handler.shapes_index++;
		},
		rectangle : function (e) {
			
			var handler = this;
			var _shape  = (e.overlay)?e.overlay:e; 
			var map_scale = handler.scale;
			var temp = {
				type : _shape.type,
				color : _shape.fillColor,
				strokeColor : _shape.strokeColor
			};
			var ne = _shape.getBounds().getNorthEast();
			var sw = _shape.getBounds().getSouthWest();
			
			temp['ne'] = [ne.lat(), ne.lng()];
			temp['sw'] = [sw.lat(), sw.lng()];

			handler.post_data[handler.shapes_index] = temp;
			handler.shapes_index++;
		},
		fromLatLngToPoints : function (_map, latLng, scale) {

			var handler = this;
			if(!_map)_map = this.current_map;
			var topRight = _map.getProjection().fromLatLngToPoint(_map.getBounds().getNorthEast());
			var bottomLeft = _map.getProjection().fromLatLngToPoint(_map.getBounds().getSouthWest());
			var worldPoint = _map.getProjection().fromLatLngToPoint(latLng);
			return new google.maps.Point((worldPoint.x - bottomLeft.x) * scale, (worldPoint.y - topRight.y) * scale);
		},
		get_data: function (_map) {

			var handler = this;
			if(!_map)_map = this.current_map;

			handler.scale = Math.pow(2, _map.getZoom());
			handler.post_data = [];
			//var cord_center = map.getProjection().fromLatLngToPoint(map.getCenter());
			
			for (var i in handler.shapes) {
				
				var _shape = (handler.shapes[i].overlay)?handler.shapes[i].overlay:handler.shapes[i];
				if (_shape == null)
					continue;

				if (_shape.type == "polygon"){
					
					handler.polygon(_shape);
					
				}
				else if (_shape.type == "rectangle")
					handler.rectangle(_shape);
				else if (_shape.type == "circle")
					handler.circle(_shape);
			}

			var _markers = [];
			for(var i in handler.markers) {
				var _m = (handler.markers[i].overlay)?handler.markers[i].overlay:handler.markers[i];
				if(_m.position)
					_markers.push([_m.position.lat(), _m.position.lng()]);
			}


			return {
				zoom: _map.getZoom(),
				center: [_map.getCenter().lat(),_map.getCenter().lng()],
				shapes  : handler.post_data,
				markers : _markers
			};
		}
	};

})( jQuery,asl_engine );
/*
google.maps.event.addDomListener(window, 'load', function(){
	
});*/