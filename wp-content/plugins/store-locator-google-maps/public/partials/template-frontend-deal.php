<link rel='stylesheet' id='asl-plugin-css'  href='<?php echo ASL_URL_PATH ?>public/css/asl.css' type='text/css' media='all' />
<style type="text/css">
#asl-storelocator.asl-p-cont .Status_filter .onoffswitch-inner::before{content: "<?php echo __('OPEN', 'asl_locator') ?>" !important}
#asl-storelocator.asl-p-cont .Status_filter .onoffswitch-inner::after{content: "<?php echo __('CLOSE', 'asl_locator') ?>" !important}
</style>
<?php

//dd($objects[0]->short_desc);
$all_configs['template'] = '0';


?>
<script type="text/javascript">
  //if(!((typeof google === 'object' && typeof google.maps === 'object')))
  var asl_configuration = <?php echo json_encode($all_configs); ?>,
    asl_categories      = <?php echo json_encode($all_categories); ?>,
    asl_markers         = <?php echo json_encode($all_markers); ?>,
    _asl_map_customize  = <?php echo ($map_customize)?$map_customize:'null'; ?>;
</script>

<?php
$class = '';

if($all_configs['display_list'] == '0')
  $class = ' map-full';

if($all_configs['full_width'])
  $class .= ' full-width';

$geo_btn_class = ($all_configs['geo_button'] == '1')?'asl-geo icon-direction-outline':'icon-search';

?>

<div id="asl-storelocator" class="container no-pad storelocator-main asl-p-cont asl-bg-<?php echo $all_configs['color_scheme'].$class; ?> asl-text-<?php echo $all_configs['font_color_scheme'] ?> asl-deals">
<?php if($all_configs['advance_filter']): ?>    
<div class="row Filter_section">
    <div class="col-xs-12 col-sm-3 search_filter">
        <p><?php echo __( 'Search Deals', 'asl_locator') ?></p>
        <p>
          <input type="text" data-submit="disable" data-provide="typeahead" tabindex="2" id="auto-complete-search"  placeholder="<?php echo __( 'Enter your zip/city/state','asl_locator') ?>" class="form-control typeahead">
          <span><i class="glyphicon <?php echo $geo_btn_class ?>" title="<?php echo ($all_configs['geo_button'] == '1')?__('Current Location','asl_locator'):__('Search Location','asl_locator') ?>"></i></span>
        </p>
    </div>
    <div class="col-xs-12 col-sm-9">
        <div class="">
            <div class="col-xs-12">
                <div class="asl-advance-filters hide">
                  <div class="col-sm-9 col-md-8">
                      <div class="row">
                          <div class="col-sm-5 col-xs-5 drop_box_filter">
                              <p><span><?php echo $all_configs['category_title']  ?></span></p>
                              <div class="categories_filter">
                              </div>
                          </div>
                          <div class="col-sm-7 col-xs-7 range_filter hide">
                              <p class="rangeFilter">
                                <span><?php echo __( 'Distance Range','asl_locator') ?></span>
                                <input id="asl-radius-slide" type="text" class="span2" />
                                <span><?php echo __( 'Radius','asl_locator') ?>: <span id="asl-radius-input"></span> <span id="asl-dist-unit">KM</span></span>
                              </p>
                          </div>
                      </div>
                  </div>
                  <div class="col-sm-3 col-xs-4 col-md-3 col-md-push-1 col-sm-push-0 Status_filter">
                      <div class="">
                          <p>
                            <span><?php echo __('Status', 'asl_locator') ?></span>
                          </p>
                          <div class="onoffswitch">
                            <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="asl-open-close" checked>
                            <label class="onoffswitch-label" for="asl-open-close">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                          </div>
                      </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
  <div class="row">
      <div class="col-md-12" id="filter-options">
          <div class="inner-filter"></div>
      </div>
  </div>
  <div class="row">
    <div class="col-sm-3 col-xs-12 asl-panel">
      <?php if(!$all_configs['advance_filter']): ?>    
      <div class="col-xs-12 inside search_filter">
        <p><?php echo __( 'Search Location', 'asl_locator') ?></p>
        <div class="asl-store-search">
            <input type="text" id="auto-complete-search" class="form-control" placeholder="">
            <span><i class="glyphicon glyphicon-screenshot" title="Current Location"></i></span>
        </div>
        <div class="Num_of_store">
          <span><?php echo $all_configs['head_title'] ?>: <span class="count-result">0</span></span>
        </div>    
      </div>
      <?php else: ?>
      <div class="Num_of_store">
        <span><?php echo $all_configs['head_title'] ?>: <span class="count-result">0</span></span>
      </div> 
      <?php endif; ?>
      <!--  Panel Listing -->
      <div id="panel" class="storelocator-panel">
        <div class="asl-overlay" id="map-loading">
          <div class="white"></div>
          <div class="loading"><img style="margin-right: 10px;" class="loader" src="<?php echo ASL_URL_PATH ?>public/Logo/loading.gif"><?php echo __('Loading...', 'asl_locator') ?></div>
        </div>
        <div class="panel-cont">
            <div class="panel-inner">
              <div class="col-md-12">
                    <ul id="p-statelist" class="accordion" role="tablist" aria-multiselectable="true">
                  </ul>
              </div>
            </div>
        </div>
        <div class="directions-cont hide">
          <div class="agile-modal-header">
            <button type="button" class="close"><span aria-hidden="true">&times;</span></button>
            <h4><?php echo __('Directions', 'asl_locator') ?></h4>
          </div>
          <div class="rendered-directions"></div>
        </div>
      </div>
    </div> 
    <div class="col-sm-9 col-xs-12 asl-map">
      <div class="store-locator">
        <div id="asl-map-canv"></div>
        <!-- agile-modal -->
        <div id="agile-modal-direction" class="agile-modal fade">
          <div class="agile-modal-backdrop-in"></div>
          <div class="agile-modal-dialog in">
            <div class="agile-modal-content">
              <div class="agile-modal-header">
                <button type="button" class="close-directions close" data-dismiss="agile-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4><?php echo __('Get Your Directions', 'asl_locator') ?></h4>
              </div>
              <div class="form-group">
                <label for="frm-lbl"><?php echo __('From', 'asl_locator') ?>:</label>
                <input type="text" class="form-control frm-place" id="frm-lbl" placeholder="Enter a Location">
              </div>
              <div class="form-group">
                <label for="frm-lbl"><?php echo __('To', 'asl_locator') ?>:</label>
                <input readonly="true" type="text"  class="directions-to form-control" id="to-lbl" placeholder="Prepopulated Destination Address">
              </div>
              <div class="form-group">
                <span for="frm-lbl"><?php echo __('Show Distance In', 'asl_locator') ?></span>
                <label class="checkbox-inline">
                  <input type="radio" name="dist-type"  id="rbtn-km" value="0"> KM
                </label>
                <label class="checkbox-inline">
                  <input type="radio" name="dist-type" checked id="rbtn-mile" value="1"> Mile
                </label>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-default btn-submit"><?php echo __('GET DIRECTIONS', 'asl_locator') ?></button>
              </div>
            </div>
          </div>
        </div>

        <div id="asl-geolocation-agile-modal" class="agile-modal fade">
          <div class="agile-modal-backdrop-in"></div>
          <div class="agile-modal-dialog in">
            <div class="agile-modal-content">
              <button type="button" class="close-directions close" data-dismiss="agile-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <?php if($all_configs['prompt_location'] == '2'): ?>
              <div class="form-group">
                <h4><?php echo __('LOCATE YOUR GEOPOSITION', 'asl_locator') ?></h4>
              </div>
              <div class="form-group">
                <div class="col-md-9">
                  <input type="text" class="form-control" id="asl-current-loc" placeholder="Your Address">
                </div>
                <div class="col-md-3">
                  <button type="button" id="asl-btn-locate" class="btn btn-default"><?php echo __('LOCATE', 'asl_locator') ?></button>
                </div>
              </div>
              <?php else: ?>
              <div class="form-group">
                <h4><?php echo __('Use my location to find the closest Service Provider near me', 'asl_locator') ?></h4>
              </div>
              <div class="form-group">
                <button type="button" id="asl-btn-geolocation" class="btn btn-default"><?php echo __('USE LOCATION', 'asl_locator') ?></button>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <!-- agile-modal end -->
      </div>
    </div>
  </div>
  <!-- This plugin is developed by "Agile Store Locator" http://agilestorelocator.com -->
</div>

<script id="tmpl_list_item" type="text/x-jsrender">
  <div class="item" data-id="{{:id}}">
    <div class="col-md-12">
      <div class="item-thumb">
          <a class="thumb-a">
            {{if path}}
              <img src="<?php echo ASL_URL_PATH ?>public/Logo/{{:path}}" alt="logo">
            {{/if}}
          </a>
      </div>
    </div>
    <div class="col-md-12 addr-sec">
      <p class="p-title">{{:title}}</p>
      {{if description_2}}
      <p class="p-description">{{:description_2}}</p>
      {{/if}}
      {{if description }}
      <p class="asl-price">{{:description}}</p>
      {{/if}}
      <p class="p-area"><span class="glyphicon icon-location"></span>{{:address}}</p>
      {{if phone}}
      <p class="p-area"><span class="glyphicon glyphicon-phone"></span> <?php echo __( 'Phone','asl_locator') ?>: {{:phone}}</p>
      {{/if}}
      {{if email}}
      <p class="p-area"><span class="glyphicon icon-at"></span>{{:email}}</p>
      {{/if}}
      {{if c_names}}
      <p class="p-category"><span class="glyphicon icon-tag"></span> {{:c_names}}</p>
      {{/if}}
    </div>
  </div>
</script>



<script id="asl_too_tip" type="text/x-jsrender">
<div class="image_map_popup" style="display:none"><img src="{{:URL}}public/Logo/{{:path}}" /></div>
  <h3>{{:title}}</h3>
  <div class="infowindowContent">
    <div class="info-addr">
      <div class="address"><span class="glyphicon icon-location"></span>{{:address}}</div>
      {{if phone}}
      <div class="phone"><span class="glyphicon icon-phone-outline"></span><b>Phone: </b><a href="tel:{{:phone}}">{{:phone}}</a></div>
      {{/if}}
      {{if end_time && start_time}}
      <div class="p-time"><span class="glyphicon icon-clock-1"></span> {{:start_time}} - {{:end_time}}</div>
      {{/if}}
      {{if days_str}}
      <div class="p-time"><span class="glyphicon icon-calendar-outlilne"></span> {{:days_str}}</div>
      {{/if}}
      {{if show_categories && c_names}}
      <div class="categories"><span class="glyphicon icon-tag"></span>{{:c_names}}</div>
      {{/if}}
      {{if distance}}
      <div class="distance">Distance: {{:dist_str}}</div>
      {{/if}}
    </div>
    <div class="img_box" style="display:none">
    <img src="{{:URL}}public/Logo/{{:path}}" alt="logo">
  </div>
  <div class="asl-buttons"></div>
</div><div class="arrow-down"></div>
</script>
