<script type="text/javascript">
	var MAIN_LOGO_IMAGE_URL = '<?=MAIN_LOGO_IMAGE_URL?>';
	var countryid = '<?php if(isset($invoicesettingdata) && !empty($invoicesettingdata['countryid'])){ echo $invoicesettingdata['countryid']; }else{ echo DEFAULT_COUNTRY_ID; } ?>';
  	var provinceid = '<?php if(isset($invoicesettingdata) && !empty($invoicesettingdata['provinceid'])){ echo $invoicesettingdata['provinceid']; }else{ echo 0; } ?>';
  	var cityid = '<?php if(isset($invoicesettingdata) && !empty($invoicesettingdata['cityid'])){ echo $invoicesettingdata['cityid']; }else{ echo 0; } ?>';
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($invoicesettingdata) && count($invoicesettingdata)>0){ echo 'Edit'; }else{ echo 'Add'; } ?> <?php echo $this->session->userdata(base_url().'submenuname'); ?></h1>
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Home</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname'); ?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname'); ?></a></li>
              <li class="active"><?php if(isset($invoicesettingdata) && count($invoicesettingdata)>0){ echo 'Edit'; }else{ echo  'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname'); ?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-12 col-lg-12">
					<form class="form-horizontal" id="invoicesettingform">
						
						<div class="col-md-6">
							<div class="form-group" id="businessname_div">
								<label class="col-md-4 control-label">Business Name <span class="mandatoryfield">*</span></label>
								<div class="col-sm-8">
									<input id="businessname" type="text" name="businessname" value="<?php if(isset($invoicesettingdata) && count($invoicesettingdata)>0){ echo $invoicesettingdata['businessname']; } ?>" class="form-control" >
								</div>
							</div>
				            <div class="form-group" id="businessaddress_div">
				              <label class="col-md-4 control-label">Address <span class="mandatoryfield">*</span></label>
				              <div class="col-sm-8">
				                <textarea class="form-control" id="businessaddress" rows="3" name="businessaddress"><?php if(isset($invoicesettingdata) && count($invoicesettingdata)>0){ echo $invoicesettingdata['businessaddress']; } ?></textarea>
				              </div>
				            </div>
							<!-- <div class="form-group" id="cityid_div">
								<label class="col-md-4 control-label">City <span class="mandatoryfield">*</span></label>
								<div class="col-md-8">
									<input id="cityid" type="text" name="cityid" data-url="<?php echo base_url().ADMINFOLDER.'city/getactivecity';?>" data-provide='city' data-type='1' data-placeholder="Select City" value="<?php if(isset($invoicesettingdata)){ echo $invoicesettingdata['cityid']; }else echo 0; ?>" placeholder="Select City" class="form-control">
								</div>
							</div> -->
							<div class="form-group" id="country_div">
                                <label class="col-sm-4 control-label" for="countryid">Country <span class="mandatoryfield">*</span></label>
                                <div class="col-sm-8">
                                    <select id="countryid" name="countryid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                        <option value="0">Select Country</option>
                                        <?php foreach($countrydata as $country){ ?>
                                        <option value="<?php echo $country['id']; ?>" <?php if(isset($invoicesettingdata) && $invoicesettingdata['countryid']==$country['id']){ echo 'selected'; }else{ if(DEFAULT_COUNTRY_ID==$country['id']){ echo 'selected'; } } ?>><?php echo $country['name']; ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="province_div">
                                <label class="col-sm-4 control-label" for="provinceid">Province <span class="mandatoryfield">*</span></label>
                                <div class="col-sm-8">
                                    <select id="provinceid" name="provinceid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                        <option value="0">Select Province</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="city_div">
                                <label class="col-sm-4 control-label" for="cityid">City <span class="mandatoryfield">*</span></label>
                                <div class="col-sm-8">
                                    <select id="cityid" name="cityid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                        <option value="0">Select City</option>
                                    </select>
                                </div>
                            </div>
							
							<div class="form-group" id="postcode_div">
								<label class="col-md-4 control-label">Post Code <span class="mandatoryfield">*</span></label>
								<div class="col-md-8">
									<input type="text" name="postcode" id="postcode" value="<?php if(isset($invoicesettingdata) && count($invoicesettingdata)>0){ echo $invoicesettingdata['postcode']; } ?>" class="form-control number" maxlength="8">
								</div>
							</div>
							<div class="form-group" id="email_div">
								<label class="col-md-4 control-label">Email <span class="mandatoryfield">*</span></label>
								<div class="col-sm-8">
									<input id="email" type="text" name="email" value="<?php if(isset($invoicesettingdata) && count($invoicesettingdata)>0){ echo $invoicesettingdata['email']; } ?>" class="form-control">
								</div>
							</div>
							<div class="form-group" id="gstno_div">
								<label class="col-md-4 control-label">GST No. </label>
								<div class="col-sm-8">
									<input id="gstno" type="text" name="gstno" value="<?php if(isset($invoicesettingdata) && count($invoicesettingdata)>0){ echo $invoicesettingdata['gstno']; } ?>" class="form-control" maxlength="15"  onkeypress="return alphanumeric(event)">
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group" id="image_div">
								<label class="col-md-4 control-label">Company Logo <span class="mandatoryfield">*</span></label>
								<div class="col-sm-6">
	        					<input type="hidden" name="oldlogo" id="oldlogo" value="<?php if(isset($invoicesettingdata) && count($invoicesettingdata)>0){ echo $invoicesettingdata['logo']; } ?>">
	                			<?php if(isset($invoicesettingdata) && count($invoicesettingdata)>0){ ?>
	        						<div class="imageupload" id="companylogo">
					                <div class="file-tab"><img src="<?php echo MAIN_LOGO_IMAGE_URL.$invoicesettingdata['logo']; ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
					                    <label id="logolabel" class="btn btn-sm btn-primary btn-raised btn-file">
					                        <span id="logobtn">Change</span>
					                        <!-- The file is stored here. -->
					                        <input type="file" name="logo" id="logo">
					                    </label>
					                    <button type="button" class="btn btn-sm btn-danger btn-raised" style="display: inline-block;">Remove</button>
					                </div>
	        						</div>
	        					<?php }else{ ?>
	        						<div class="imageupload" id="companylogo">
					                <div class="file-tab">
					                	<img src="" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px;">
					                    <label id="logolabel" class="btn btn-sm btn-primary btn-raised btn-file">
					                        <span id="logobtn">Select Image</span>
					                        <input type="file" name="logo" id="logo" value="">
					                    </label>
					                    <button type="button" class="btn btn-sm btn-danger btn-raised">Remove</button>
					                </div>
	        						</div>
	        					<?php } ?>
								</div>
							</div>
							
						</div>
						<div class="col-md-12">
							<div class="form-group" id="invoicenotes_div">
								<label for="focusedinput" class="col-md-2 control-label">Invoice Notes</label>
								<div class="col-sm-10">
									<?php $data['controlname']="invoicenotes";if(isset($invoicesettingdata) && !empty($invoicesettingdata)){$data['controldata']=$invoicesettingdata['notes'];} ?>
		                      		<?php $this->load->view(ADMINFOLDER.'includes/ckeditor',$data);?>
								</div>
							</div>
						</div>
						<div class="col-md-12 text-center">
							<div class="form-group">
								<?php if(isset($invoicesettingdata) && count($invoicesettingdata)>0){ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised">
								<?php }else{ ?>
								  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
								  <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised">
								<?php } ?>
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
