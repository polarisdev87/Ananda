<!-- Container -->
<div class="container asl-p-cont asl-new-bg">
  <div class="row asl-inner-cont">
    <div class="col-md-12">
      <h3  class="alert alert-info head-1">Manage Logos</h3>
      <?php if(!is_writable(ASL_PLUGIN_PATH.'public/icon')): ?>
        <h3  class="alert alert-danger" style="font-size: 14px"><?php echo ASL_PLUGIN_PATH.'public/icon'.' <= Directory is not writable, Logo Image Upload will Fail, Make directory writable.' ?></h3>  
      <?php endif; ?>
      <div class="row">
        <div class="col-md-12 ralign">
          <button type="button" id="btn-asl-delete-all" class="btn btn-danger mrg-r-10">Delete Selected</button>
          <button type="button" id="btn-asl-new-c" class="btn btn-primary mrg-r-10">New Logo</button>
        </div>
      </div>
    	<table id="tbl_logos" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th align="center">&nbsp;</th>
              <th align="center"><input type="text" data-id="id"  placeholder="Search ID"  /></th>
              <th align="center"><input type="text" data-id="name"  placeholder="Search Name"  /></th>
              <th align="center">&nbsp;</th>
              <th align="center">&nbsp;</th>
            </tr>
            <tr>
              <th align="center"><a class="select-all">Select All</a></th>
              <th align="center">Logo ID</th>
              <th align="center">Name</th>
              <th align="center">Image</th>
              <th align="center">Action&nbsp;</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
      </table>
    	<div class="dump-message asl-dumper"></div>


      <!-- Edit Alert -->
      <div class="smodal fade frm-update" id="confirm-update" role="dialog">
          <div class="smodal-dialog">
          <div class="smodal-content">
              <div class="smodal-header">
                  <h4>Update Logos</h4>
              </div>
              <div class="smodal-body">
                <form name="frm-updatelogo" id="frm-updatelogo">              
                    <div class="form-group">
                          <label class="control-label">Logo ID: <span id="update_logo_id"> </span></label>
                          <input type="hidden"  name="data[logo_id]" id="update_logo_id_input">
                    </div>
                    <div class="form-group col-xs-12">
                          <label for="txt_name"  class="control-label col-md-2">Name</label>
                          <input type="text" class="form-control col-md-10 name-field validate[required]" name="data[logo_name]" id="update_logo_name">
                    </div>
                    <div class="form-group col-xs-12" id="updatelogo_image">
                       <img  src="" id="update_logo_icon" alt="" data-id="same" height="80px" width="80px"/>
                       <button type="button" class="btn btn-default" id="change_image">Change</button>
                    </div>

                    <div class="form-group  " style="display:none" id="updatelogo_editimage" >                  
                        <div class="form-group col-xs-12">
                          <div class="input-group col-sm-offset-2 col-sm-10" id="drop-zone">
                            <input type="text" class="form-control file-name" placeholder="File Path...">
                            <input type="file" accept=".jpg,.png,.jpeg,.gif,.JPG" class="btn btn-default" style="width:98%;opacity:0;position:absolute;top:0;left:0"  name="files" />
                            <span class="input-group-btn">
                              <button class="btn btn-default" onclick="jQuery('#drop-zone input[type=file]').trigger('click')" style="padding:3px 12px" type="button">Browse</button>
                            </span>
                          </div>
                        </div>
                       
                        <div class="form-group">
                          <div class="progress hideelement" style="display:none" id="progress_bar_">
                              <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                  <span style="position:relative" class="sr-only">0% Complete</span>
                              </div>
                          </div>
                        </div>
                        <ul></ul>
                    </div>
                    <p id="message_update"></p>
                    <div class="smodal-footer">
                    <button class="btn btn-primary btn-start mrg-r-15" id="btn-asl-update-logos"   type="button" data-loading-text="Submitting ...">Update Logo</button>
                    <button type="button" class="btn btn-default" data-dismiss="smodal">Cancel</button>
                    </div>
                 </form> 
              </div>
          </div>
          </div>
      </div>
    </div>
  </div>
</div>
<!-- asl-cont end-->

<div class="smodal fade asl-p-cont" id="asl-new-cat-box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="smodal-dialog">
          <div class="smodal-content">
              <div class="smodal-header">
                  <h4>Add New Logo</h4>
              </div>
              <div class="smodal-body">
               <form name="frm-upload-logo" id="frm-addlogo">
                  <div class="form-group col-md-12">
                    <label for="txt_name" class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10"><input type="text" class="form-control validate[required]" name="data[logo_name]"></div>
                  </div>
                  <div class="form-group">
                    <div class="input-group col-sm-offset-2 col-sm-10" id="drop-zone-1">
                      <input type="text" class="form-control file-name" placeholder="File Path...">
                      <input type="file" accept=".jpg,.png,.jpeg,.gif,.JPG" class="btn btn-default" style="width:98%;opacity:0;position:absolute;top:0;left:0"  name="files" />
                      <span class="input-group-btn">
                        <button class="btn btn-default" onclick="jQuery('#drop-zone-1 input[type=file]').trigger('click')" style="padding:3px 12px" type="button">Browse</button>
                      </span>
                    </div>
                  </div>
                  <div class="form-group">
                      <div class="progress hideelement" style="display:none" id="progress_bar">
                        <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                          <span style="position:relative" class="sr-only">0% Complete</span>
                        </div>
                      </div>
                  </div>
                  <ul></ul>
                  <p id="message_upload" class="alert alert-warning hide"></p>
                  <div class="smodal-footer">
                      <button class="btn btn-primary btn-start mrg-r-15" id="btn-asl-add-logos" type="button" data-loading-text="Submitting ...">Add Logo</button>
                      <button type="button" class="btn btn-default" data-dismiss="smodal">Cancel</button>
                  </div>
              </form>
          </div>
      </div>
  </div>
</div>

<!-- SCRIPTS -->
<script type="text/javascript">
var ASL_Instance = {
	url: '<?php echo ASL_URL_PATH ?>'
};

asl_engine.pages.manage_logos();
</script>
