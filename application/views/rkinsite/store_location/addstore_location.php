<script src="http://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyCQplBzEyHAjOBIXHWB1RI_Pls4qLAvxXA" type="text/javascript"></script>

<div class="page-content">
	<div class="page-heading">            
        <h1><?php if(isset($store_locationdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
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
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12">
					<form class="form-horizontal" id="store-locationform">
						<input type="hidden" name="store_locationid" value="<?php if(isset($store_locationdata)){ echo $store_locationdata['id']; } ?>">
						<div class="col-md-12 p-n">
							<div class="col-md-6 p-n">
								<div class="form-group" id="name_div">
									<label class="col-sm-3 control-label">Store Name <span class="mandatoryfield">*</span></label>
									<div class="col-sm-8">
										<input id="name" type="text" name="name" value="<?php if(!empty($store_locationdata)){ echo $store_locationdata['name']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)">
									</div>
								</div>
							</div>
							<div class="col-md-6 p-n">
								<div class="form-group" id="contactperson_div">
									<label class="col-sm-3 control-label">Contact Person</label>
									<div class="col-sm-8">
										<input id="contactperson" type="text" name="contactperson" value="<?php if(!empty($store_locationdata)){ echo $store_locationdata['contactperson']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 p-n">
							<div class="col-md-6 p-n">
								<div class="form-group" id="email_div">
									<label class="col-sm-3 control-label">Email</label>
									<div class="col-sm-8">
										<input id="email" type="text" name="email" value="<?php if(!empty($store_locationdata)){ echo $store_locationdata['email']; } ?>" class="form-control">
									</div>
								</div>
							</div>
							<div class="col-md-6 p-n">
								<div class="form-group" id="mobileno_div">
									<label class="col-sm-3 control-label">Mobile No.</label>
									<div class="col-sm-8">
										<input id="mobileno" type="text" name="mobileno" value="<?php if(!empty($store_locationdata)){ echo $store_locationdata['mobileno']; } ?>" class="form-control number" minlength="10" maxlength="10">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 p-n">
							<div class="col-md-6 p-n">
								<div class="form-group" id="address_div">
									<label class="col-md-3 control-label">Address <span class="mandatoryfield">*</span></label>
									<div class="col-md-8">
										<input type="text" id="address" name="address" class="form-control" value="<?php if(isset($store_locationdata)){ echo $store_locationdata['address']; } ?>" onkeyup="initialize()" placeholder="">
									</div>
								</div>
							</div>
							<div class="col-md-6 p-n">
								<div class="form-group" id="cityid_div">
									<label class="col-md-3 control-label">City <span class="mandatoryfield">*</span></label>
									<div class="col-md-8">
										<input id="cityid" type="text" name="cityid" data-url="<?php echo base_url().ADMINFOLDER.'city/getactivecity';?>" data-provide='city' data-type='1' data-placeholder="Select City" value="<?php if(isset($store_locationdata)){ echo $store_locationdata['cityid']; }else echo 0; ?>" placeholder="Select City" class="form-control">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 p-n">
							<div class="col-md-6 p-n">
								<div class="form-group" id="latitude_div">
									<label class="col-sm-3 control-label">Latitude <span class="mandatoryfield">*</span></label>
									<div class="col-sm-8">
										<input id="latitude" type="text" name="latitude" value="<?php if(!empty($store_locationdata)){ echo $store_locationdata['latitude']; } ?>" class="form-control" onkeypress="return decimal_number_validation(event,this.value,10,7)">
									</div>
								</div>
							</div>
							<div class="col-md-6 p-n">
								<div class="form-group" id="longitude_div">
									<label class="col-sm-3 control-label">Longitude <span class="mandatoryfield">*</span></label>
									<div class="col-sm-8">
										<input id="longitude" type="text" name="longitude" value="<?php if(!empty($store_locationdata)){ echo $store_locationdata['longitude']; } ?>" class="form-control" onkeypress="return decimal_number_validation(event,this.value,10,7)">
									</div>
								</div>
							</div>
						</div>	
						<div class="col-md-12 p-n">
							<div class="col-md-6 p-n">
									<div class="form-group" id="link_div">
										<label class="col-sm-3 control-label">Map Link</label>
										<div class="col-sm-8">
											<input id="link" type="text" name="link" value="<?php if(!empty($store_locationdata)){ echo urldecode($store_locationdata['link']); } ?>" class="form-control">
										</div>
									</div>
							</div>
							<div class="col-md-6 p-n">
								<div class="form-group">
									<label for="focusedinput" class="col-sm-3 control-label">Activate</label>
									<div class="col-sm-8">
										<div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
											<div class="radio">
											<input type="radio" name="status" id="yes" value="1" <?php if(isset($store_locationdata) && $store_locationdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
											<label for="yes">Yes</label>
											</div>
										</div>
										<div class="col-sm-2 col-xs-6">
											<div class="radio">
											<input type="radio" name="status" id="no" value="0" <?php if(isset($store_locationdata) && $store_locationdata['status']==0){ echo 'checked'; }?>>
											<label for="no">No</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6 p-n">
							</div>
						</div>
						<div class="col-md-12 p-n" style="text-align: center;">
							<div class="form-group">
								<?php if(!empty($store_locationdata)){ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
								<?php }else{ ?>
								  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
								  <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
								<?php } ?>
                                <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>store_location" title=<?=cancellink_title?>><?=cancellink_text?></a>
							</div>
						</div>	
					</form>
				</div>
				</div>
		      </div>
		    </div>
		  </div>
		</div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->