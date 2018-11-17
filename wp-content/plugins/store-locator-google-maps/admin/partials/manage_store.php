<!-- Container -->
<div class="container asl-p-cont asl-new-bg">
  <div class="row asl-inner-cont">
    <div class="col-md-12">
	  <h3  class="alert alert-info head-1">Manage Stores</h3>

    <div class="row">
      <div class="col-md-3 ralign">
        <select class="form-control" id="asl-ddl-status">
          <option value="1">Enable</option>
          <option value="0">Disable</option>
        </select>
      </div>
      <div class="col-md-1 ralign">
        <button class="btn btn-info" id="btn-change-status" type="button">Change</button>
      </div>
      <div class="col-md-3 ralign">
        <button type="button" id="btn-asl-delete-all" class="btn btn-danger mrg-r-10">Delete Selected</button>
      </div>
    </div>
    <div class="alert alert-info mrg-t-20" role="alert">
      Store Locator Listing columns can easily be updated by simply add/remove from the template, Please visit the link for more <a href="https://agilestorelocator.com/blog/customize-google-marker-infowindow-sidebar-store-locator/" target="_blank">"Customize Store Locator"</a>.
    </div>
	  <table id="tbl_stores" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th><input type="text" data-id="id"  disabled="disabled" style="opacity: 0" placeholder="Search ID"  /></th>
          <th style="position: relative;" class="asl-search-btn">
            <input type="text" data-id="-id" disabled="disabled" style="opacity: 0" placeholder="Search ID"  />
          </th>
          <th><input type="text" data-id="id"  placeholder="Search ID"  /></th>
          <th><input type="text" data-id="title"  placeholder="Search Title"  /></th>
          <th><input type="text" data-id="description"  placeholder="Search Description"  /></th>
          <th><input type="text" data-id="lat"  placeholder="Search Lat"  /></th>
          <th><input type="text" data-id="lng"  placeholder="Search Lng"  /></th>
          <th><input type="text" data-id="street"  placeholder="Search Street"  /></th>
          <th><input type="text" data-id="state"  placeholder="Search State"  /></th>
          <th><input type="text" data-id="city"  placeholder="Search City"  /></th>
          <th><input type="text" data-id="phone"  placeholder="Search Phone"  /></th>
          <th><input type="text" data-id="email"  placeholder="Search Email"  /></th>
          <th><input type="text" data-id="website"  placeholder="Search URL"  /></th>
          <th><input type="text" data-id="postal_code"  placeholder="Search Zip"  /></th>
          <th><input type="text" data-id="is_disabled"  placeholder="Disabled"  /></th>
          <th><input type="text" data-id="category" disabled="disabled" style="opacity:0"  placeholder="Categories"  /></th>
          <th><input type="text" data-id="marker_id"  placeholder="Marker ID"  /></th>
          <th><input type="text" data-id="logo_id"  placeholder="Logo ID" /></th>
          <th><input type="text" data-id="created_on"  placeholder="Created On"  /></th>
        </tr>
        <tr>
          <th><a class="select-all">Select All</a></th>
          <th>Action&nbsp;</th>
          <th>Store ID</th>
          <th>Title</th>
          <th>Description</th>
          <th>Lat</th>
          <th>Lng</th>
          <th>Street</th>
          <th>State</th>
          <th>City</th>
          <th>Phone</th>
          <th>Email</th>
          <th>URL</th>
          <th>Postal Code</th>
          <th>Disabled</th>
          <th>Categories</th>
          <th>Marker ID</th>
          <th>Logo ID</th>
          <th>Created On</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
	  <div class="dump-message asl-dumper"></div>
    </div>
  </div>
</div>



<!-- SCRIPTS -->
<script type="text/javascript">
var ASL_Instance = {
	url: '<?php echo ASL_URL_PATH ?>'
};
asl_engine.pages.manage_stores();
</script>
