<!-- Container -->
<div class="container asl-p-cont asl-new-bg">
	<div class="row asl-inner-cont">
	<div class="col-md-12">
		<div class="dump-message asl-dumper"></div>
		<h3  class="alert alert-info head-1">Edit Store</h3>
		<form class="form-horizontal" id="frm-addstore">
			<input type="hidden" id="update_id" value="<?php echo $store->id ?>" />
			<fieldset>
			 <div class="row">
				<div class="col-md-8">
					<div class="alert alert-dismissable alert-danger hide">
						 <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
						<h4>Alert!</h4> <strong>Warning!</strong> Best check yo self <a href="#" class="alert-link">alert link</a>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h3 class="panel-title">Store Name</h3></div>
					  	<div class="panel-body">
							<div class="col-md-12">
								<div class="col-md-6">
									<div class="form-group">
							            <label class="col-sm-3 control-label" for="txt_title">Title</label>
							            <div class="col-sm-9"><input type="text" value="<?php echo $store->title ?>" id="txt_title" name="data[title]" class="form-control validate[required]"></div>
						           	</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
							            <label class="col-sm-3 control-label" for="txt_description">Description</label>
							            <div class="col-sm-9"><textarea id="txt_description" name="data[description]" rows="3"  placeholder="Enter Description" maxlength="500" class="input-medium form-control"><?php echo $store->description ?></textarea></div>
						           	</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h3 class="panel-title">Store Address</h3></div>
					  	<div class="panel-body">
					  		<div class="col-md-12">
								<div class="col-md-6">
									<div class="form-group">
							            <label class="col-sm-3 control-label" for="txt_phone">Phone</label>
							            <div class="col-sm-9"><input value="<?php echo $store->phone ?>" type="text" id="txt_phone" name="data[phone]" class="form-control"></div>
						           	</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
							            <label class="col-sm-3 control-label" for="txt_fax">Fax</label>
							            <div class="col-sm-9"><input type="text" value="<?php echo $store->fax ?>" id="txt_fax" name="data[fax]" class="form-control"></div>
						           	</div>
								</div>
							</div>

							<div class="col-md-12">
								<div class="col-md-6">
									<div class="form-group">
							            <label class="col-sm-3 control-label" for="txt_email">Email</label>
							            <div class="col-sm-9"><input type="text" value="<?php echo $store->email ?>" id="txt_email" name="data[email]" class="form-control validate[custom[email]]"></div>
						           	</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
							            <label class="col-sm-3 control-label" for="txt_street">Street</label>
							            <div class="col-sm-9"><input type="text" value="<?php echo $store->street ?>" id="txt_street" name="data[street]" class="form-control validate[required]"></div>
						           	</div>	
								</div>
							</div>


							<div class="col-md-12">
								<div class="col-md-6">
									<div class="form-group">
							            <label class="col-sm-3 control-label" for="txt_city">City</label>
							            <div class="col-sm-9"><input type="text" value="<?php echo $store->city ?>" id="txt_city" name="data[city]" class="form-control validate[required]"></div>
						           	</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
							            <label class="col-sm-3 control-label" for="txt_state">State</label>
							            <div class="col-sm-9"><input type="text" value="<?php echo $store->state ?>" id="txt_state" name="data[state]" class="form-control"></div>
						           	</div>
									
								</div>
							</div>

							<div class="col-md-12">
								<div class="col-md-6">
									<div class="form-group">
							            <label class="col-sm-3 control-label" for="txt_postal_code">Postal Code</label>
							            <div class="col-sm-9"><input type="text" value="<?php echo $store->postal_code ?>" id="txt_postal_code" name="data[postal_code]" class="form-control validate[required]"></div>
						           	</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
							            <label class="col-sm-3 control-label" for="txt_country">Country</label>
							            <div class="col-sm-9">
								            <select  id="txt_country" style="width:100%" name="data[country]" class="form-control validate[required]">
								            	<?php foreach($countries as $country): ?>
								            		<option <?php if($store->country == $country->id) echo 'selected' ?> value="<?php echo $country->id ?>"><?php echo $country->country ?></option>
								            	<?php endforeach ?>
								            </select>
							            </div>
						           	</div>
								</div>
							</div>

							<div class="col-md-12">
								<div class="form-group">
						            <div id="map_canvas" class="map_canvas"></div>
					           	</div>
							</div>
				           	
				           	<div class="col-md-12">
					  			<div class="col-md-6">
						  			<div class="form-group">
							            <label class="col-sm-3 control-label" for="txt_lng">Longitude</label>
							            <div class="col-sm-9"><input type="text" value="<?php echo $store->lng ?>" id="asl_txt_lng" name="data[lng]" value="0.0" readonly="true" class="form-control"></div>
						           	</div>
					  			</div>
					  			<div class="col-md-6">
						  			<div class="form-group">
							            <label class="col-sm-3 control-label" for="txt_lat">Latitude</label>
							            <div class="col-sm-9"><input type="text" value="<?php echo $store->lat ?>" id="asl_txt_lat" name="data[lat]" value="0.0" readonly="true" class="form-control"></div>
						           	</div>
					  			</div>
					  		</div>

					  		<div class="col-md-12">
					  			<div class="col-md-6 col-md-offset-6">
					  				<p class="ralign">
					           			<a id="lnk-edit-coord" class="btn btn-warning">Change Coordinates</a>
					           		</p>
					  			</div>
					  		</div>
				           	<div class="dump-message"></div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h3 class="panel-title">Detailed Information</h3></div>
					  	<div class="panel-body">
					  		<div class="col-md-12">
								<div class="col-md-6">
									<div class="form-group">
							            <label class="col-sm-3 control-label" for="txt_categories">Category</label>
							            <div class="col-sm-9">
							            	<select name="ddl_categories"  id="ddl_categories" multiple class="chosen-select-width form-control">				            	
								            	<?php foreach($category as $catego): ?>
								            	<option 
								            	<?php foreach($storecategory as $scategory ){ ?>
								            		<?php if($scategory->category_id == $catego->id) echo 'selected' ?>
								            		
								            	<?php }?>
								            	value="<?php echo $catego->id ?>"><?php echo $catego->category_name ?></option>	

								            	<?php endforeach ?>
							            	</select>
							            </div>
						           	</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
							            <label class="col-sm-3 control-label" for="txt_url">Site URL</label>
							            <div class="col-sm-9"><input value="<?php echo $store->website ?>" type="text" id="txt_url" name="data[url]" placeholder="http://example.com" class="form-control"></div>
						           	</div>
								</div>
							</div>

							<div class="col-md-12">
								<div class="col-md-6">
									<div class="form-group">
							            <label class="col-sm-3 control-label" for="txt_description_2">Additional Details</label>
							            <div class="col-sm-9"><textarea id="txt_description_2" id="txt_description_2" name="data[description_2]" name="txt_description_1" rows="3"  placeholder="Enter Description" maxlength="500" class="input-medium form-control"><?php echo $store->description_2 ?></textarea></div>
						           	</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
						           		<label class="col-sm-3 control-label"  for="chk_enabled">Disabled</label>
										<div class="col-sm-9">
											<input name="data[is_disabled]" <?php if($store->is_disabled == 1) echo 'checked' ?> id="chk_disabled" value="1" type="checkbox">
										</div>
									</div>
								</div>
							</div>

							<div class="col-md-12">
								<div class="col-md-6">
									<input type="hidden" name="data[logo_id]" value="<?php echo $storelogo[0]->id ?>" class="logoidclass"/>
									<div class="form-group">
						           		<label class="col-sm-3 control-label" for="chk_enabled">Logo</label>
										<div class="col-sm-6">
			                                <select id="ddl-asl-logos">
			                                	<?php foreach($logos as $t):?>
										        <option value="<?php echo $t->id?>" data-imagesrc="<?php echo ASL_URL_PATH.'public/Logo/'.$t->path;?>" data-description="&nbsp;"><?php echo $t->name;?></option>
											    <?php endforeach; ?>
										    </select>
										</div>
										<div class="col-sm-3">
			                                <button type="button" class="btn btn-success" data-toggle="smodal" data-target="#addimagemodel">New Logo</button>
			                            </div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
						           		<label class="col-sm-3 control-label" for="chk_enabled">Marker</label>
										<div class="col-sm-6">
			                                <select id="ddl-asl-markers">
			                                	<?php foreach($markers as $m):?>
										        <option value="<?php echo $m->id?>" data-imagesrc="<?php echo ASL_URL_PATH.'public/icon/'.$m->icon;?>" data-description="&nbsp;"><?php echo $m->marker_name;?></option>
											    <?php endforeach; ?>
										    </select>
										</div>
										<div class="col-sm-3">
			                                <button type="button" class="btn btn-success" data-toggle="smodal" data-target="#addimagemodel">New</button>
			                            </div>
									</div>
								</div>
							</div>
	                        
	                        <div class="col-md-12">
								<div class="col-md-6">
									<div class="form-group">
							            <label class="col-sm-3 control-label" for="txt-ordering">Ordering</label>
							            <div class="col-sm-9"><input type="number" value="<?php echo $store->ordr ?>" id="txt-ordering" name="data[ordr]" placeholder="0" class="form-control"></div>
						           	</div>
						           	<p class="red">Descending Order for the list, higher number on top. <br>Shortcode [ASL_STORELOCATOR sort_by="ordr"]</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
			$open_hours = json_decode($store->open_hours);

			?>
			<!--<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h3 class="panel-title">Set Timing</h3></div>
					  	<div class="panel-body">
					  		<div class="col-md-8">
								<table class="table table-striped asl-time-details">
									<tbody>
										<tr>
											<td colspan="1"><span class="lbl-day">Monday</span></td>
											<td colspan="3">
												<div class="asl-all-day-times" data-day="mon">
													<?php 
													if(is_array($open_hours->mon))
													foreach($open_hours->mon as $mon): $o_hour = explode(' - ', $mon); ?>
													<div class="form-group">
														<div class="input-append input-group bootstrap-timepicker">
												          <input type="text" value="<?php echo $o_hour[0] ?>" class="form-control timepicker validate[required,funcCall[ASLmatchTime]]" placeholder="Start Time">
												          <span class="input-group-addon add-on"><i class="glyphicon glyphicon-time icon-time"></i></span>
												        </div>
												        <div class="input-append input-group bootstrap-timepicker">
												          <input type="text" value="<?php echo $o_hour[1] ?>" class="form-control timepicker validate[required]" placeholder="End Time">
												          <span class="input-group-addon add-on"><i class="glyphicon glyphicon-time icon-time"></i></span>
												        </div>
												        <span class="add-k-delete glyphicon glyphicon-trash"></span>
												    </div>
													<?php endforeach; ?>
												    <div class="asl-closed-lbl">
												    	<div class="a-swith">
												            <input id="cmn-toggle-1" class="cmn-toggle cmn-toggle-round" type="checkbox" <?php if($open_hours->mon && $open_hours->mon == '1') echo 'checked="checked"' ?>>
												            <label for="cmn-toggle-1"></label>
												            <span>Closed</span>
												            <span>Open 24 Hour</span>
												        </div>
												    </div>
											    </div>
											</td>
											<td colspan="1" class="asl-action"><span class="add-k-add glyphicon glyphicon-plus"></span></td>
										</tr>
										<tr>
											<td colspan="1"><span class="lbl-day">Tuesday</span></td>
											<td colspan="3">
												<div class="asl-all-day-times" data-day="tue">
													<?php 
													if(is_array($open_hours->tue))
													foreach($open_hours->tue as $tue): $o_hour = explode(' - ', $tue); ?>
													<div class="form-group">
														<div class="input-append input-group bootstrap-timepicker">
												          <input type="text" value="<?php echo $o_hour[0] ?>" class="form-control timepicker validate[required,funcCall[ASLmatchTime]]" placeholder="Start Time">
												          <span class="input-group-addon add-on"><i class="glyphicon glyphicon-time icon-time"></i></span>
												        </div>
												        <div class="input-append input-group bootstrap-timepicker">
												          <input type="text" value="<?php echo $o_hour[1] ?>" class="form-control timepicker validate[required]" placeholder="End Time">
												          <span class="input-group-addon add-on"><i class="glyphicon glyphicon-time icon-time"></i></span>
												        </div>
												        <span class="add-k-delete glyphicon glyphicon-trash"></span>
												    </div>
													<?php endforeach; ?>
												    <div class="asl-closed-lbl">
												    	<div class="a-swith">
												            <input id="cmn-toggle-2" class="cmn-toggle cmn-toggle-round" type="checkbox" <?php if($open_hours->tue && $open_hours->tue == '1') echo 'checked="checked"' ?>>
												            <label for="cmn-toggle-2"></label>
												            <span>Closed</span>
												            <span>Open 24 Hour</span>
												        </div>
												    </div>
											    </div>
											</td>
											<td colspan="1" class="asl-action"><span class="add-k-add glyphicon glyphicon-plus"></span></td>
										</tr>
										<tr>
											<td colspan="1"><span class="lbl-day">Wednesday</span></td>
											<td colspan="3">
												<div class="asl-all-day-times" data-day="wed">
													<?php 
													if(is_array($open_hours->wed))
													foreach($open_hours->wed as $wed): $o_hour = explode(' - ', $wed); ?>
													<div class="form-group">
														<div class="input-append input-group bootstrap-timepicker">
												          <input type="text" value="<?php echo $o_hour[0] ?>" class="form-control timepicker validate[required,funcCall[ASLmatchTime]]" placeholder="Start Time">
												          <span class="input-group-addon add-on"><i class="glyphicon glyphicon-time icon-time"></i></span>
												        </div>
												        <div class="input-append input-group bootstrap-timepicker">
												          <input type="text" value="<?php echo $o_hour[1] ?>" class="form-control timepicker validate[required]" placeholder="End Time">
												          <span class="input-group-addon add-on"><i class="glyphicon glyphicon-time icon-time"></i></span>
												        </div>
												        <span class="add-k-delete glyphicon glyphicon-trash"></span>
												    </div>
													<?php endforeach; ?>
												    <div class="asl-closed-lbl">
												    	<div class="a-swith">
												            <input id="cmn-toggle-3" class="cmn-toggle cmn-toggle-round" type="checkbox" <?php if($open_hours->wed && $open_hours->wed == '1') echo 'checked="checked"' ?>>
												            <label for="cmn-toggle-3"></label>
												            <span>Closed</span>
												            <span>Open 24 Hour</span>
												        </div>
												    </div>
											    </div>
											</td>
											<td colspan="1" class="asl-action"><span class="add-k-add glyphicon glyphicon-plus"></span></td>
										</tr>
										<tr>
											<td colspan="1"><span class="lbl-day">Thursday</span></td>
											<td colspan="3">
												<div class="asl-all-day-times" data-day="thu">
													<?php 
													if(is_array($open_hours->thu))
													foreach($open_hours->thu as $thu): $o_hour = explode(' - ', $thu); ?>
													<div class="form-group">
														<div class="input-append input-group bootstrap-timepicker">
												          <input type="text" value="<?php echo $o_hour[0] ?>" class="form-control timepicker validate[required,funcCall[ASLmatchTime]]" placeholder="Start Time">
												          <span class="input-group-addon add-on"><i class="glyphicon glyphicon-time icon-time"></i></span>
												        </div>
												        <div class="input-append input-group bootstrap-timepicker">
												          <input type="text" value="<?php echo $o_hour[1] ?>" class="form-control timepicker validate[required]" placeholder="End Time">
												          <span class="input-group-addon add-on"><i class="glyphicon glyphicon-time icon-time"></i></span>
												        </div>
												        <span class="add-k-delete glyphicon glyphicon-trash"></span>
												    </div>
													<?php endforeach; ?>
												    <div class="asl-closed-lbl">
												    	<div class="a-swith">
												            <input id="cmn-toggle-4" class="cmn-toggle cmn-toggle-round" type="checkbox" <?php if($open_hours->thu && $open_hours->thu == '1') echo 'checked="checked"' ?>>
												            <label for="cmn-toggle-4"></label>
												            <span>Closed</span>
												            <span>Open 24 Hour</span>
												        </div>
												    </div>
											    </div>
											</td>
											<td colspan="1" class="asl-action"><span class="add-k-add glyphicon glyphicon-plus"></span></td>
										</tr>
										<tr>
											<td colspan="1"><span class="lbl-day">Friday</span></td>
											<td colspan="3">
												<div class="asl-all-day-times" data-day="fri">
													<?php 
													if(is_array($open_hours->fri))
													foreach($open_hours->fri as $fri): $o_hour = explode(' - ', $fri); ?>
													<div class="form-group">
														<div class="input-append input-group bootstrap-timepicker">
												          <input type="text" value="<?php echo $o_hour[0] ?>" class="form-control timepicker validate[required,funcCall[ASLmatchTime]]" placeholder="Start Time">
												          <span class="input-group-addon add-on"><i class="glyphicon glyphicon-time icon-time"></i></span>
												        </div>
												        <div class="input-append input-group bootstrap-timepicker">
												          <input type="text" value="<?php echo $o_hour[1] ?>" class="form-control timepicker validate[required]" placeholder="End Time">
												          <span class="input-group-addon add-on"><i class="glyphicon glyphicon-time icon-time"></i></span>
												        </div>
												        <span class="add-k-delete glyphicon glyphicon-trash"></span>
												    </div>
													<?php endforeach; ?>
												    <div class="asl-closed-lbl">
												    	<div class="a-swith">
												            <input id="cmn-toggle-5" class="cmn-toggle cmn-toggle-round" type="checkbox" <?php if($open_hours->fri && $open_hours->fri == '1') echo 'checked="checked"' ?>>
												            <label for="cmn-toggle-5"></label>
												            <span>Closed</span>
												            <span>Open 24 Hour</span>
												        </div>
												    </div>
											    </div>
											</td>
											<td colspan="1" class="asl-action"><span class="add-k-add glyphicon glyphicon-plus"></span></td>
										</tr>
										<tr>
											<td colspan="1"><span class="lbl-day">Saturday</span></td>
											<td colspan="3">
												<div class="asl-all-day-times" data-day="sat">
													<?php 
													if(is_array($open_hours->sat))
													foreach($open_hours->sat as $sat): $o_hour = explode(' - ', $sat); ?>
													<div class="form-group">
														<div class="input-append input-group bootstrap-timepicker">
												          <input type="text" value="<?php echo $o_hour[0] ?>" class="form-control timepicker validate[required,funcCall[ASLmatchTime]]" placeholder="Start Time">
												          <span class="input-group-addon add-on"><i class="glyphicon glyphicon-time icon-time"></i></span>
												        </div>
												        <div class="input-append input-group bootstrap-timepicker">
												          <input type="text" value="<?php echo $o_hour[1] ?>" class="form-control timepicker validate[required]" placeholder="End Time">
												          <span class="input-group-addon add-on"><i class="glyphicon glyphicon-time icon-time"></i></span>
												        </div>
												        <span class="add-k-delete glyphicon glyphicon-trash"></span>
												    </div>
													<?php endforeach; ?>
												    <div class="asl-closed-lbl">
												    	<div class="a-swith">
												            <input id="cmn-toggle-6" class="cmn-toggle cmn-toggle-round" type="checkbox" <?php if($open_hours->sat && $open_hours->sat == '1') echo 'checked="checked"' ?>>
												            <label for="cmn-toggle-6"></label>
												            <span>Closed</span>
												            <span>Open 24 Hour</span>
												        </div>
												    </div>
											    </div>
											</td>
											<td colspan="1" class="asl-action"><span class="add-k-add glyphicon glyphicon-plus"></span></td>
										</tr>
										<tr>
											<td colspan="1"><span class="lbl-day">Sunday</span></td>
											<td colspan="3">
												<div class="asl-all-day-times" data-day="sun">
													<?php 
													if(is_array($open_hours->sun))
													foreach($open_hours->sun as $sun): $o_hour = explode(' - ', $sun); ?>
													<div class="form-group">
														<div class="input-append input-group bootstrap-timepicker">
												          <input type="text" value="<?php echo $o_hour[0] ?>" class="form-control timepicker validate[required,funcCall[ASLmatchTime]]" placeholder="Start Time">
												          <span class="input-group-addon add-on"><i class="glyphicon glyphicon-time icon-time"></i></span>
												        </div>
												        <div class="input-append input-group bootstrap-timepicker">
												          <input type="text" value="<?php echo $o_hour[1] ?>" class="form-control timepicker validate[required]" placeholder="End Time">
												          <span class="input-group-addon add-on"><i class="glyphicon glyphicon-time icon-time"></i></span>
												        </div>
												        <span class="add-k-delete glyphicon glyphicon-trash"></span>
												    </div>
													<?php endforeach; ?>
												    <div class="asl-closed-lbl">
												    	<div class="a-swith">
												            <input id="cmn-toggle-7" class="cmn-toggle cmn-toggle-round" type="checkbox" <?php if($open_hours->sun && $open_hours->sun == '1') echo 'checked="checked"' ?>>
												            <label for="cmn-toggle-7"></label>
												            <span>Closed</span>
												            <span>Open 24 Hour</span>
												        </div>
												    </div>
											    </div>
											</td>
											<td colspan="1" class="asl-action"><span class="add-k-add glyphicon glyphicon-plus"></span></td>
										</tr>
									</tbody>
								</table>
							</div>
					  	
					  	</div>
					</div>
				</div>
			</div>-->

			<div class="row">
				<div class="col-md-12">
					<div class="form-group ralign">
			            <button type="button" class="btn btn-primary mrg-r-10" data-loading-text="Updating..." data-completed-text="Updated" id="btn-asl-add">Update Store</button>
		           	</div>
				</div>
			</div>
			</fieldset>
		</form>
	    <div class="smodal fade addimagemodel" id="addimagemodel" role="dialog">
	        <div class="smodal-dialog">     
	          <!-- Modal content-->
	          <div class="smodal-content">
	            <div class="smodal-header">
	              <button type="button" class="close" data-dismiss="smodal">&times;</button>
	              <h4 class="smodal-title">Upload Logo</h4>
	            </div>
	            <div class="smodal-body">
	            	<form id="frm-upload-logo" name="frm-upload-logo" class="frm-upload-box">
			        	<div class="form-group">                    
	                      	<div class="form-group col-md-12">
		                        <label for="txt_name" class="col-sm-2 control-label">Name</label>
		                        <div class="col-sm-10"><input type="text" class="form-control validate[required]" name="data[logo_name]"></div>
	                      	</div>
				        	<div class="input-group col-sm-offset-2 col-sm-10" id="drop-zone">
						      	<input type="text" class="form-control file-name" placeholder="File Path...">
						      	<input type="file" accept=".jpg,.png,.jpeg,.gif,.JPG" class="btn btn-default" style="width:98%;opacity:0;position:absolute;top:0;left:0"  name="files" />
						      	<span class="input-group-btn">
						        	<button class="btn btn-default" onclick="jQuery('#drop-zone input[type=file]').trigger('click')" style="padding:3px 12px" type="button">Browse</button>
						      	</span>
						    </div>
				        </div>
					    <div class="form-group ralign">
							<button class="btn btn-primary btn-start mrg-r-15" type="button" data-loading-text="Submitting ...">Upload Logo</button>
						</div>
						<div class="form-group">
							<div class="progress hideelement progress_bar_" style="display:none">
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

	    <!-- Add Marker -->
	    <div class="smodal fade addimagemodel" id="addmarkermodel" role="dialog">
	        <div class="smodal-dialog">     
	          <!-- Modal content-->
	          <div class="smodal-content">
	            <div class="smodal-header">
	              <button type="button" class="close" data-dismiss="smodal">&times;</button>
	              <h4 class="smodal-title">Upload Marker</h4>
	            </div>
	            <div class="smodal-body">
	            	<form id="frm-upload-marker" name="frm-upload-marker" class="frm-upload-box">
			        	<div class="form-group">                    
	                      	<div class="form-group col-md-12">
		                        <label for="txt_name" class="col-sm-2 control-label">Name</label>
		                        <div class="col-sm-10"><input type="text" class="form-control validate[required]" name="data[category_name]"></div>
	                      	</div>
				        	<div class="input-group col-sm-offset-2 col-sm-10" id="drop-zone-2">
						      	<input type="text" class="form-control file-name" placeholder="File Path...">
						      	<input type="file" accept=".jpg,.png,.jpeg,.gif,.JPG" class="btn btn-default" style="width:98%;opacity:0;position:absolute;top:0;left:0"  name="files" />
						      	<span class="input-group-btn">
						        	<button class="btn btn-default" onclick="jQuery('#drop-zone-2 input[type=file]').trigger('click')" style="padding:3px 12px" type="button">Browse</button>
						      	</span>
						    </div>
				        </div>
					    <div class="form-group ralign">
							<button class="btn btn-primary btn-start mrg-r-15" type="button" data-loading-text="Submitting ...">Upload Marker</button>
						</div>
						<div class="form-group">
							<div class="progress hideelement progress_bar_" style="display:none">
					          <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
					            <span style="position:relative" class="sr-only">0% Complete</span>
					          </div>
					        </div>
						</div>
						<ul></ul>
						<p id="message_upload_1" class="alert alert-warning hide"></p>
					</form>
	            </div>
	            <div class="smodal-footer">
	              <button type="button" class="btn btn-default" data-dismiss="smodal">Close</button>
	            </div>
	          </div>
	        </div>
	      </div>
	</div>
	</div>
</div>
<!-- SCRIPTS -->
<script type="text/javascript">

	var asl_configs =  <?php echo json_encode($all_configs); ?>;
	var ASL_Instance = {
		url: '<?php echo ASL_URL_PATH; ?>',
		sideurl: '<?php echo get_site_url();?>'
	};
	
	asl_engine.pages.edit_store(<?php echo json_encode($store) ?>);
</script>
