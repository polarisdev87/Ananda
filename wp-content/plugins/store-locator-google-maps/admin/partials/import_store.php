<!-- Container -->
<div class="container asl-p-cont asl-new-bg">
  	<div class="row asl-inner-cont">
  	<div class="col-md-12">
		<div class="dump-message asl-dumper"></div>
		<div class="row" id="message_complete">
		</div>
		<h3  class="alert alert-info head-1">Import Stores</h3>
		<?php if(!class_exists('ZipArchive')): ?>
  			<h3  class="alert alert-danger" style="font-size: 14px">ZipArchive is not found in your PHP, Contact your server admin OR login to your cpanel and enable it. Import/Export will Not work without this library.</h3>	
  		<?php endif; ?>
  		<?php if(!is_writable(ASL_PLUGIN_PATH.'public/import')): ?>
        <h3  class="alert alert-danger" style="font-size: 14px"><?php echo ASL_PLUGIN_PATH.'public/import'.' <= Directory is not writable, Excel Import will Fail, Make directory writable.' ?></h3>  
      <?php endif; ?>
		<div class="well">
			<div class="row">
				<div class="col-md-4">
					<div style="display: block">
						<input type="checkbox" class="inline-checkbox" checked="checked" id="asl-create-category" style="display:inline-block;margin: 0px !important;">
						<label style="display: inline-block;line-height: 15px;margin-top: 5px">Create Category If NOT Exist</label>
					</div>
					<div style="display: block">
						<input type="checkbox" class="inline-checkbox" id="asl-use-key" style="display: inline-block;margin: 0px !important">
						<label  style="display: inline-block;line-height: 15px;margin-top: 5px">USE API KEY</label>		
					</div>
				</div>
				<div class="col-md-1">
					<button type="button" class="btn btn-success" data-toggle="smodal" data-target="#import_store_file_emodel">Upload</button>
				</div>
				<div class="col-md-2">
					<button type="button" class="btn btn-success" id="export_store_file_">Export All</button>
				</div>
				<div class="col-md-3">
					<button type="button" class="btn btn-danger" data-loading-text="Deleting..." id="asl-delete-stores">Delete All Stores & Logo</button>
				</div>
				<div class="col-md-1">
					<a target="_blank" class="btn" href="<?php echo ASL_URL_PATH.'public/export/template-import.xlsx' ?>">Template.xlsx</a>
				</div>
			</div>
		</div>
		<div class="alert alert-info" role="alert">
			If lat/lng cell is empty, the store locator import will fetch that lat/lng from google api using your provided address. ASL is not responsible if google api doesn't provide correct values.
			API [http://maps.googleapis.com/maps/api/geocode/json?address=MYHOMEADDRESS&sensor=false]. API has Usage Limit Per Second, too many records may be blocked by Google. <br>Your API Key:<b style="margin-right: 10px;margin-left: 5px"><?php echo $api_key ?></b><a id="btn-validate-key" class="btn btn-primary"> Validate It</a>
		</div>
		<table id="tbl_stores" class="table table-bordered">
	      <thead>
	      	<tr>        
	          <th align="center">File Name</th>
	          <th align="center">Date</th>
	          <th align="center">View</th>
	          <th align="center">Import</th>
	          <th align="center">Delete</th>
	        </tr>
	      </thead>
	      <tbody>
	     	<?php $dir    = ASL_PLUGIN_PATH.'public/import/';
				$files = scandir($dir);

				foreach($files as $file):
				
					if($file != '.' && $file != '..'):?>
					<tr>
					<td><?php echo $file; ?></td>
					<td><?php echo date("F d Y ",filemtime($dir.$file)); ?></td>
					<td><a href="<?php echo ASL_URL_PATH.'public/import/'.$file ?>" class="btn btn-info">View File</a></td>
					<td><button type="button" class="btn btn-primary btn-asl-import_store" data-loading-text="Importing..."  data-id="<?php echo $file;?>">Import</button></td>
					<td><button type="button" class="btn btn-danger btn-asl-delete_import_file"  data-id="<?php echo $file;?>">Delete</button></td>	
					<tr>
			<?php 	
					endif;

				endforeach;
			?>
	      </tbody>
	    </table>


	    <div class="smodal fade" id="import_store_file_emodel" role="dialog">
	        <div class="smodal-dialog">
	        
	          <!-- Modal content-->
	          <div class="smodal-content">
	            <div class="smodal-header">
	              <button type="button" class="close" data-dismiss="smodal">&times;</button>
	              <h4 class="smodal-title">Upload Excel File</h4>
	            </div>
	            <div class="smodal-body">
	            	<form id="import_store_file" name="import_store_file" >
			        	<div class="form-group">
				        	<div class="input-group col-sm-offset-3 col-sm-9" id="drop-zone">
						      <input type="text" class="form-control file-name" placeholder="File Path...">
						      <input type="file" class="btn btn-default" accept=".xlsx,.xls" style="width:98%;opacity:0;position:absolute;top:0;left:0"  name="files" />
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
			
		<div class="smodal fade asl-p-cont" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		    <div class="smodal-dialog">
		        <div class="smodal-content">
		            <div class="smodal-header">
		                <h4>Delete All Stores</h4>
		            </div>
		            <div class="smodal-body">
		                Are you sure you want to Delete ALL stores?
		            </div>
		            <div class="smodal-footer">
		                <button type="button" class="btn btn-default" data-dismiss="smodal">Cancel</button>
		                <a class="btn btn-danger btn-ok" id="btn-delete-stores">Delete</a>
		            </div>
		        </div>
		    </div>
		</div>
	</div>
	</div>
</div>

<!-- SCRIPTS -->
<script type="text/javascript">
	var ASL_Instance = {
		url: '<?php echo ASL_URL_PATH ?>'
	};
	asl_engine.pages.import_store();
</script>