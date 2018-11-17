<?php

$all_configs['infobox_layout'] = '0';

$class = '';


//if time_switch and distance is off then no-advance filter
if($all_configs['time_switch'] == '0' && $all_configs['distance_slider'] == '0')
  $all_configs['advance_filter'] = '0';


if($all_configs['display_list'] == '0')
  $class = ' map-full';

if($all_configs['full_width'])
  $class .= ' full-width';


if($all_configs['advance_filter'] == '0')
  $class .= ' no-asl-filters';

if($all_configs['layout'] == '1' || $all_configs['advance_filter'] == '0'){
  $class .= ' asl-no-advance';
}
else if($all_configs['show_categories'] == '0') {
 $class .= ' asl-no-categories'; 
}

//add Full height
if($all_configs['full_height'])
  $class .= ' '.$all_configs['full_height'];


$distance_control = ($all_configs['distance_control'] == '1')?'1':'0';


$geo_btn_class = ($all_configs['geo_button'] == '1')?'asl-geo icon-direction-outline':'icon-search';

?>
<link rel='stylesheet' id='asl-plugin-css'  href='<?php echo ASL_URL_PATH ?>public/css/asl-2.css' type='text/css' media='all' />
<style type="text/css">
#asl-storelocator.asl-p-cont .Status_filter .onoffswitch-inner::before{content: "<?php echo __('OPEN', 'asl_locator') ?>" !important}
#asl-storelocator.asl-p-cont .Status_filter .onoffswitch-inner::after{content: "<?php echo __('CLOSE', 'asl_locator') ?>" !important}
</style>
<div id="asl-storelocator" class="container asl-template-2 no-pad storelocator-main asl-p-cont asl-bg-<?php echo $all_configs['color_scheme_2'].$class; ?> asl-text-<?php echo $all_configs['font_color_scheme'] ?>">
  <div class="row">
      <div class="col-md-12" id="filter-options">
          <div class="inner-filter"></div>
      </div>
  </div>
  <div class="row">
    <div class="col-sm-4 col-xs-12 asl-panel-box">
      <?php if($all_configs['advance_filter'] && $all_configs['layout'] != '1'): ?> 
      <div class="col-sm-12 filter-box asl-dist-ctrl-<?php echo $distance_control ?>">
          <div class="col-sm-4 col-xs-4 col-md-4 Status_filter">
              <div class="">
                  <p>
                    <span><?php echo __('Status', 'asl_locator') ?></span>
                  </p>
                  <div class="onoffswitch">
                    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="asl-open-close" checked>
                    <label class="onoffswitch-label" for="asl-open-close">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch asl-ico"></span>
                    </label>
                  </div>
              </div>
          </div>
          <div class="col-xs-8 col-sm-8 col-md-8 pull-right">
              <div class="range_filter hide">
                  <p class="rangeFilter">
                    <span><?php echo __( 'Distance Range','asl_locator') ?></span>
                    <input id="asl-radius-slide" type="text" class="span2" />
                    <span><?php echo __( 'Radius','asl_locator') ?>: <span id="asl-radius-input"></span> <span id="asl-dist-unit"><?php echo __( 'KM','asl_locator') ?></span></span>
                  </p>
              </div>
          </div>
      </div>
      <?php else: ?>
        <div class="Num_of_store">
          <div class="calign"><?php echo $all_configs['head_title']  ?> <span class="count-result">0</span></div>
        </div>
      <?php endif; ?>

      <div class="col-sm-12 col-xs-12 asl-panel">
        <?php if($all_configs['advance_filter'] && $all_configs['layout'] != '1'): ?> 

            <?php
            //if show_categories false
            if($all_configs['show_categories'] == '1'): ?> 
            <div class="Num_of_store hide">
              <span class="icon col-xs-2"><img src="<?php echo ASL_URL_PATH ?>public/img/icon-1.png"></span>
              <span class="col-xs-8 asl-cat-name"><span class="sele-cat"></span> <span class="count-result">0</span></span>
              <span class="back-button col-xs-2"><i class="glyphicon icon-left-open"></i></span>
            </div>   
            <div class="cats-title">
              <span class="icon"><img src="<?php echo ASL_URL_PATH ?>public/img/category_icon.png"></span>
              <span><?php echo $all_configs['category_title']  ?></span>
            </div>
            <?php else: ?>
              <div class="Num_of_store">
              <div class="calign"> <?php echo $all_configs['head_title']  ?> <span class="count-result">0</span></div>
            </div>   
            <?php endif; ?>

         <?php endif; ?>
        <!--  Panel Listing -->
        <?php if($all_configs['advance_filter'] == '1' && $all_configs['layout'] != '1' && $all_configs['show_categories'] == '1'): ?>
        <div class="categories-panel">
        </div>
        <?php endif ?>

        <div id="panel" class="storelocator-panel <?php if($all_configs['advance_filter'] && $all_configs['layout'] != '1' && $all_configs['show_categories'] == '1') echo 'hide'; ?>">
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
          <div class="directions-cont hide" style="padding-top:12px">
            <div class="agile-modal-header">
              <button type="button" class="close"><span aria-hidden="true">&times;</span></button>
              <h4><?php echo __('Directions', 'asl_locator') ?></h4>
            </div>
            <div class="rendered-directions"></div>
          </div>
        </div>
      </div> 
    </div>
    <div class="col-sm-8 col-xs-12 asl-map">
      <div class="store-locator">
          
        <div class=" search_filter inside-map">
            <p>
              <input type="text" id="auto-complete-search" class="form-control" placeholder="<?php echo __( 'Find A Store','asl_locator') ?>">
              <span><i class="glyphicon <?php echo $geo_btn_class ?>" title="<?php echo ($all_configs['geo_button'] == '1')?__('Current Location','asl_locator'):__('Search Location','asl_locator') ?>"></i></span>
            </p>
        </div>
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
                <input type="text" class="form-control frm-place" id="frm-lbl" placeholder="<?php echo __('Enter a Location', 'asl_locator') ?>">
              </div>
              <div class="form-group">
                <label for="frm-lbl"><?php echo __('To', 'asl_locator') ?>:</label>
                <input readonly="true" type="text"  class="directions-to form-control" id="to-lbl" placeholder="<?php echo __( 'Prepopulated Destination Address','asl_locator') ?>">
              </div>
              <div class="form-group">
                <span for="frm-lbl"><?php echo __('Show Distance In', 'asl_locator') ?></span>
                <label class="checkbox-inline">
                  <input type="radio" name="dist-type"  id="rbtn-km" value="0"> <?php echo __( 'KM','asl_locator') ?>
                </label>
                <label class="checkbox-inline">
                  <input type="radio" name="dist-type" checked id="rbtn-mile" value="1"> <?php echo __( 'Mile','asl_locator') ?>
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
                  <input type="text" class="form-control" id="asl-current-loc" placeholder="<?php echo __('Your Address', 'asl_locator') ?>">
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
</div>
<!-- This plugin is developed by "Agile Store Locator" https://agilestorelocator.com -->
<?php
////*SCRIPT TAGS STARTS FROM HERE:: FROM EVERY THING BELOW THIS LINE:: VARIALBLE LIMIT IS NOT SUPPORTING LONGER HTML*////
if($atts['no_script'] == 0):
?>
<script id="tmpl_list_item" type="text/x-jsrender">
  <div class="item" data-id="{{:id}}">
    <div class="col-xs-4 img-section">
        <a class="thumb-a">
            {{if path}}
              <img src="<?php echo ASL_URL_PATH ?>public/Logo/{{:path}}" alt="logo">
            {{/if}}
          </a>
    </div>
    <div class="col-xs-8 data-section">
    <div class="title-item">
    <p class="p-title">{{:title}}</p>
    </div>
    <div class="clear"></div>
    <div class="addr-sec">
      
      <p class="p-area">{{:address}}</p>
      {{if distance}}
        <p class="p-area p-distance"><?php echo __( 'Distance','asl_locator') ?>: {{:dist_str}}</p>
      {{/if}}
      {{if phone}}
      <p class="p-area"><span class="glyphicon icon-phone-outline"></span> <?php echo __( 'Phone','asl_locator') ?>: {{:phone}}</p>
      {{/if}}
      {{if email}}
      <p class="p-area"><span class="glyphicon icon-at"></span><a href="mailto:{{:email}}" style="text-transform: lowercase">{{:email}}</a></p>
      {{/if}}
      {{if c_names}}
      <p class="p-category"><span class="glyphicon icon-tag"></span> {{:c_names}}</p>
      {{/if}}
      {{if open_hours}}
      <p class="p-time"><span class="glyphicon icon-clock-1"></span> {{:open_hours}}</p>
      {{/if}}
      {{if days_str}}
      <p class="p-time"><span class="glyphicon icon-calendar-outlilne"></span>{{:days_str}}</p>
      {{/if}}
    </div>
    <div class="">
      <div class="col-xs-5 col-md-12 item-thumb">
      </div>
    </div>
    <div class="col-xs-12 distance">
          <a class="p-direction btn btn-block"><span class="s-direction"><?php echo __('Directions', 'asl_locator') ?></span></a>
    </div>
    <div class="clear"/>
  </div>
  <div>
</script>



<script id="asl_too_tip" type="text/x-jsrender">
<div class="image_map_popup" style="display:none"><img src="{{:URL}}public/Logo/{{:path}}" /></div>
  <h3>{{:title}}</h3>
  <div class="infowindowContent">
    <div class="info-addr">
      <div class="address"><span class="glyphicon icon-location"></span>{{:address}}</div>
      {{if phone}}
      <div class="phone"><span class="glyphicon icon-phone-outline"></span><b><?php echo __('Phone', 'asl_locator') ?>: </b><a href="tel:{{:phone}}">{{:phone}}</a></div>
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
      <div class="distance"><?php echo __('Distance', 'asl_locator') ?>: {{:dist_str}}</div>
      {{/if}}
    </div>
    <div class="img_box" style="display:none">
    <img src="{{:URL}}public/Logo/{{:path}}" alt="logo">
  </div>
  <div class="asl-buttons"></div>
</div><div class="arrow-down"></div>
</script>
<?php endif; ?>