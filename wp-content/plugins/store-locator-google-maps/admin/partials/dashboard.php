<!-- Container -->
<div class="container asl-p-cont asl-new-bg">
  <div class="row asl-inner-cont">
  <div class="col-md-12">
    <h3  class="alert alert-info head-1">Agile Store Locator Dashboard</h3>
    <div class="alert alert-info" role="alert">
      Please visit the documentation page to explore all options. <a target="_blank" href="https://agilestorelocator.com">Agile Store Locator</a> 
    </div>
    <?php if(!$all_configs['api_key']): ?>
        <h3  class="alert alert-danger" style="font-size: 14px">Google API KEY is missing, the Map Search and Direction will not work without it, Please add Google API KEY. <a href="https://agilestorelocator.com/blog/enable-google-maps-api-agile-store-locator-plugin/" target="_blank">How to Add API Key?</a></h3>
    <?php endif; ?>
    <h3 class="alert alert-warning" style="width:100%;font-size: 14px"><span style="margin-right: 10px">Backup My Logo, Custom Markers, and Category Icons. </span><a style="margin-right: 10px" class="btn btn-default" id="btn-assets-backup">Backup Assets</a>
    <a class="btn btn-primary hide" id="lnk-assets-download" target="_blank">Download Link</a>
    <button type="button" class="btn btn-success pull-right" data-toggle="smodal" data-target="#import_assets_model">Import Assets Zip</button>
    </h3>
    <div class="dashboard-area">
      <div class="row">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-3 stats-store">
                <div class="stats">
                    <div class="stats-a"><span class="glyphicon glyphicon-shopping-cart"></span></div>
                    <div class="stats-b"><?php echo $all_stats['stores'] ?><br><span>Stores</span></div>
                </div>
              </div>
              <div class="col-md-3 stats-category">
                <div class="stats">
                    <div class="stats-a"><span class="glyphicon glyphicon-tag"></span></div>
                    <div class="stats-b"><?php echo $all_stats['categories'] ?><br><span>Categories</span></div>
                </div>
              </div>
              <div class="col-md-3 stats-marker">
                <div class="stats">
                    <div class="stats-a"><span class="glyphicon glyphicon-map-marker"></span></div>
                    <div class="stats-b"><?php echo $all_stats['markers'] ?><br><span>Markers</span></div>
                </div>
              </div>
              <div class="col-md-3 stats-searches">
                <div class="stats">
                    <div class="stats-a"><span class="glyphicon glyphicon-search"></span></div>
                    <div class="stats-b"><?php echo $all_stats['searches'] ?><br><span>Searches</span></div>
                </div>
              </div>
            </div>
          </div>
      </div>
      <div class="row"></div>
      <ul class="nav nav-tabs" style="margin-top:30px">
        <li role="presentation" class="active"><a href="#asl-analytics">Analytics</a></li>
        <li role="presentation"><a href="#asl-views">Top Views</a></li>
      </ul>
      <div class="tab-content" id="asl-tabs">
        
        <div class="tab-pane fade in active" role="tabpanel" id="asl-analytics" aria-labelledby="asl-analytics">
          <div class="row">
            <div class="col-md-4 ralign col-md-offset-8" style="margin-top: 30px">
              <div class="form-group">
                <label class="col-sm-3 control-label" style="line-height:35px;width:30%" for="asl-search-month">Period</label>
                <select id="asl-search-month" class="form-control" style="width:70%">
                  <?php 
                  for ($i=0; $i<=12; $i++) { 
                    echo '<option value="'.date('m-Y', strtotime("-$i month")).'">'.date('m/Y', strtotime("-$i month")).'</option>';
                  }
                  ?>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="canvas-holder" style="width:100%">
                  <canvas id="asl_search_canvas" style="width:300px;height:400px"></canvas>
              </div>
            </div>
          </div>
        </div>

        <div class="tab-pane fade" role="tabpanel" id="asl-views" aria-labelledby="asl-views">
          
          <div class="col-md-12"> 
            <ul class="list-group">
              <li class="list-group-item active"><span class="store-id">Store ID</span> Most Views Stores List<span class="views">Views</span></li>
              <?php foreach($top_stores as $s):?>
              <li class="list-group-item"><span class="store-id"><?php echo $s->store_id ?></span> <?php echo $s->title ?> <span class="views"><?php echo $s->views ?></span></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <br clear="both">
          <div class="col-md-12"> 
            <ul class="list-group">
              <li class="list-group-item active"> Most Search Locations <span class="views">Views</span></li>
              <?php foreach($top_search as $s):?>
              <li class="list-group-item"> <?php echo $s->search_str ?> <span class="views"><?php echo $s->views ?></span></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>

      </div>  
    </div>

    <div class="dump-message asl-dumper"></div>
  </div>  
  </div>


    <div class="smodal fade" id="import_assets_model" role="dialog">
        <div class="smodal-dialog">
        
          <!-- Modal content-->
          <div class="smodal-content">
            <div class="smodal-header">
              <button type="button" class="close" data-dismiss="smodal">&times;</button>
              <h4 class="smodal-title">Upload Assets Zip File</h4>
            </div>
            <div class="smodal-body">
              <form id="import_assets_file" name="import_assets_file">
              <div class="form-group">
                <div class="input-group col-sm-offset-3 col-sm-9" id="drop-zone">
                <input type="text" class="form-control file-name" placeholder="File Path...">
                <input type="file" class="btn btn-default" accept=".zip" style="width:98%;opacity:0;position:absolute;top:0;left:0"  name="files" />
                <span class="input-group-btn">
                  <button class="btn btn-default" onclick="jQuery('#drop-zone input[type=file]').trigger('click')" style="padding:3px 12px" type="button">Browse</button>
                </span>
              </div>
              </div>
            <div class="form-group ralign">
            <button class="btn btn-default btn-start mrg-r-15" type="button" data-loading-text="Submitting ...">Upload File</button>
          </div>
          <div class="form-group">
            <div class="progress hideelement" style="display:none" id="progress_bar_">
                  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                    <span style="position:relative" class="sr-only">0% Complete</span>
                  </div>
                </div>
          </div>
          <ul></ul>
          <p id="message_upload" class="alert alert-warning hide"></p>
        </form>
            </div>
            <div class="smodal-footer">
              <button type="button" class="btn btn-default" data-dismiss="smodal">Close</button>
            </div>
          </div>
          
        </div>
    </div>
</div>
<!-- asl-cont end-->








<!-- SCRIPTS -->
<script type="text/javascript">
var ASL_Instance = {
	url: '<?php echo ASL_URL_PATH ?>'
};

asl_engine.pages.dashboard();
</script>