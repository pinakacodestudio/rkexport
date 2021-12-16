<script>
var adpage_section_id = '<?php if(isset($advertisementdata)){ echo $advertisementdata['adpage_section_id']; } ?>';
</script>
<div class="page-content">
	<div class="page-heading">            
        <h1><?php if(isset($advertisementdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Home</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($store_locationdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>              
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12 ">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12 ">
					<form class="form-horizontal" id="advertisementform">
						<input type="hidden" name="advid" value="<?php if(isset($advertisementdata)){ echo $advertisementdata['id']; } ?>">
                         
						<div class="col-md-12 p-n">
							<div class="col-md-6">
								<div class="form-group" id="adpage_div">
									<label for="focusedinput" class="col-sm-3 control-label">Page <span class="mandatoryfield">*</span></label>
									<div class="col-sm-8">
										<select id="adpage_id" name="adpage_id" class="selectpicker form-control" data-live-search="true" data-size="5" tabindex="8">
											<option value="0">Select Page</option>
											<?php foreach($this->AdPage as $apkey=>$ap){ ?>
												<option value="<?php echo $apkey; ?>" <?php if(isset($advertisementdata)){ if($advertisementdata['adpage_id'] == $apkey){ echo 'selected'; } } ?>><?=$ap?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group" id="adpage_section_div">
									<label for="focusedinput" class="col-sm-3 control-label">Page Section <span class="mandatoryfield">*</span></label>
									<div class="col-sm-8">
										<select id="adpage_section_id" name="adpage_section_id" class="selectpicker form-control" data-live-search="true" data-size="5" tabindex="8">
											<option value="0">Select Page Section</option>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group" id="google_ad_div">
									<label for="focusedinput" class="col-sm-3 control-label">Google Ad Script</label>
									<div class="col-sm-8">
										<textarea id="google_ad" name="google_ad" rows="1" class="form-control" ><?php if(isset($advertisementdata['google_ad'])){ echo $advertisementdata['google_ad']; }?></textarea>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group" id="amazon_ad_div">
									<label for="focusedinput" class="col-sm-3 control-label">Amazon Ad Script</label>
									<div class="col-md-8">
										<textarea id="amazon_ad" name="amazon_ad" rows="1" class="form-control" ><?php if(isset($advertisementdata['amazon_ad'])){ echo $advertisementdata['amazon_ad']; }?></textarea>
									</div>
								</div>
							</div>
							
							<div class="col-md-6 ">
						      	<div class="form-group ">
						      		<label for="focusedinput" class="col-md-3 control-label">Upload Image</label>
						      		<div class="col-md-8">
						      			<input type="hidden" name="oldtestimonialsimage" id="oldtestimonialsimage" value="<?php if(isset($advertisementdata)){ echo $advertisementdata['image']; }?>">
						      			<input type="hidden" name="removeoldImage" id="removeoldImage" value="0">
						      			<?php if(isset($advertisementdata) && $advertisementdata['image']!=''){ ?>
	                  						<div class="imageupload" id="testimonialsimage">
						      	                <div class="file-tab"><img src="<?php echo ADVERTISEMENT.$advertisementdata['image']; ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
						      	                    <label id="testimonialsimagelabel" class="btn btn-sm btn-primary btn-raised btn-file">
						      	                        <span id="testimonialsimagebtn">Change</span>
						      	                        <!-- The file is stored here. -->
						      	                        <input type="file" name="image" id="image" accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png">
						      	                    </label>
						      	                    <button type="button" class="btn btn-sm btn-danger btn-raised" id="remove" style="display: inline-block;">Remove</button>
						      	                </div>
	                  						</div>
	                  					<?php }else{ ?>
	                  						<!-- <script type="text/javascript"> var ACTION = 0;</script> -->
	                  						<div class="imageupload">
						      	                <div class="file-tab">
						      	                	<img src="" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px;">
						      	                    <label for="image" id="logolabel" class="btn btn-sm btn-primary btn-raised btn-file">
						      	                        <span id="testimonialsimagebtn">Select Image</span>
						      	                        <input type="file" name="image" id="image"  accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
						      	                    </label>
						      	                    <button type="button" class="btn btn-sm btn-danger btn-raised" id="remove">Remove</button>
						      	                </div>
	                  						</div>
	                  					<?php } ?>
						      		</div>
						      	</div>
						      </div>
							  <div class="col-md-6">
							<div class="form-group" id="adtype_div">
									<label for="focusedinput" class="col-sm-3 control-label">Active Type <span class="mandatoryfield">*</span></label>
									<div class="col-sm-8">
										<select id="adtype" name="adtype" class="selectpicker form-control" data-live-search="true" data-size="5" tabindex="8">
											<option value="0">Select Active Type</option>
											<option value="1" <?php if(isset($advertisementdata['adtype']) && $advertisementdata['adtype']==1 ){ echo "selected"; } ?>>Google</option>
											<option value="2" <?php if(isset($advertisementdata['adtype']) && $advertisementdata['adtype']==2 ){ echo "selected"; } ?>>Amazon</option>
											<option value="3" <?php if(isset($advertisementdata['adtype']) && $advertisementdata['adtype']==2 ){ echo "selected"; } ?>>Custome Add</option>
										</select>
									</div>
								</div>
							</div>
						</div>				
							
							
							
						<div class="form-group">
							<label for="focusedinput" class="col-sm-4 control-label">Activate</label>
							<div class="col-sm-8">
								<div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
									<div class="radio">
									<input type="radio" name="status" id="yes" value="1" <?php if(isset($advertisementdata) && $advertisementdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
									<label for="yes">Yes</label>
									</div>
								</div>
								<div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
									<div class="radio">
									<input type="radio" name="status" id="no" value="0" <?php if(isset($advertisementdata) && $advertisementdata['status']==0){ echo 'checked'; }?>>
									<label for="no">No</label>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="focusedinput" class="col-sm-4 control-label"></label>
							<div class="col-sm-8">
								<?php if(!empty($advertisementdata)){ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
								<?php }else{ ?>
								  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
								  <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
								<?php } ?>
								<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>advertisement" title=<?=cancellink_title?>><?=cancellink_text?></a>
							</div>
						</div>
					</form>
				</div>
				</div>
		      </div>
		    </div>
		  </div>
		</div>
    </div>
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->