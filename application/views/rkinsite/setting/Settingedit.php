<script type="text/javascript">
	var MAIN_LOGO_IMAGE_URL = '<?=MAIN_LOGO_IMAGE_URL?>';
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($settingdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url(); ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($settingdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
					<form class="form-horizontal" id="settingform">
						<div class="row">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group" id="name_div">
										<label for="name" class="col-sm-4 control-label">Company Name <span class="mandatoryfield">*</span></label>
										<div class="col-sm-8">
											<input id="name" type="text" name="name" value="<?php if(isset($settingdata)){ echo $settingdata['businessname']; } ?>" class="form-control" >
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group" id="website_div">
										<label for="website" class="col-sm-4 control-label">Website <span class="mandatoryfield">*</span></label>
										<div class="col-sm-8">
											<input id="website" type="text" name="website" value="<?php if(isset($settingdata)){ echo $settingdata['website']; } ?>" class="form-control">
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group" id="email_div">
										<label for="email" class="col-sm-4 control-label">Email <span class="mandatoryfield">*</span></label>
										<div class="col-sm-8">
											<input id="email" type="text" name="email" value="<?php if(isset($settingdata)){ echo $settingdata['email']; } ?>" class="form-control">
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group" id="mobileno_div">
										<label for="mobileno" class="col-sm-4 control-label">Mobile No <span class="mandatoryfield">*</span></label>
										<div class="col-sm-8">
											<input id="mobileno" type="text" name="mobileno" value="<?php echo $settingdata['mobileno']; ?>" class="form-control" maxlength="10"  onkeypress="return isNumber(event)">
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group" id="address_div">
										<label for="address" class="col-sm-2 control-label">Address <span class="mandatoryfield">*</span></label>
										<div class="col-sm-10">
											<textarea class="form-control" id="address" rows="3" name="address"><?php echo $settingdata['address']; ?></textarea>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group row" id="facebook_div">
									<label class="col-md-4 label-control" for="facebooklink">Facebook Link<span class="mandatoryfield">*</span></label>
									<div class="col-md-8">
										<input id="facebooklink" type="text" name="facebooklink" value="<?php echo $settingdata['facebooklink']; ?>" class="form-control">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group row" id="google_div">
									<label class="col-md-4 label-control" for="googlelink">Google Link<span class="mandatoryfield">*</span></label>
									<div class="col-md-8">
										<input id="googlelink" type="text" name="googlelink" value="<?php echo $settingdata['googlelink']; ?>" class="form-control">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group row" id="twitter_div">
									<label class="col-md-4 label-control" for="twitterlink">Twitter Link<span class="mandatoryfield">*</span></label>
									<div class="col-md-8">
										<input id="twitterlink" type="text" name="twitterlink" value="<?php echo $settingdata['twitterlink']; ?>" class="form-control">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group row" id="instagram_div">
									<label class="col-md-4 label-control" for="instagramlink">Instragram Link<span class="mandatoryfield">*</span></label>
									<div class="col-md-8">
										<input id="instagramlink" type="text" name="instagramlink" value="<?php echo $settingdata['instagramlink']; ?>" class="form-control">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group row" id="fcmkey_div">
									<label class="col-md-4 label-control" for="fcmkey">Fcm Key<span class="mandatoryfield">*</span></label>
									<div class="col-md-8">
										<input id="fcmkey" type="text" name="fcmkey" value="<?php echo $settingdata['fcmkey']; ?>" class="form-control">
									</div>
								</div>
							</div>
						</div>
						<div class="row">
								<div class="col-md-3">
									<div class="form-group">
									<div class="col-sm-12">
										<label class="control-label">Company Logo <span class="mandatoryfield">*</span></label>
		            					<input type="hidden" name="oldlogo" id="oldlogo" value="<?php echo $settingdata['logo']; ?>">
		        						<?php if(isset($settingdata)){ ?>
		            						<div class="imageupload" id="companylogo">
								                <div class="file-tab"><img src="<?php echo MAIN_LOGO_IMAGE_URL.$settingdata['logo']; ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
								                    <label id="logolabel" class="btn btn-sm btn-primary btn-raised btn-file">
								                        <span id="logobtn">Change</span>
								                        <!-- The file is stored here. -->
								                        <input type="file" name="logo" id="logo" accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
								                    </label>
								                    <button type="button" class="btn btn-sm btn-danger btn-raised" style="display: inline-block;">Remove</button>
								                </div>
		            						</div>
		            					<?php }else{ ?>
		            						<div class="imageupload">
								                <div class="file-tab">
								                	<img src="" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px;">
								                    <label id="logolabel" class="btn btn-sm btn-primary btn-raised btn-file">
								                        <span id="logobtn">Select Image</span>
								                        <input type="file" name="logo" id="logo" value="" accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
								                    </label>
								                    <button type="button" class="btn btn-sm btn-danger btn-raised">Remove</button>
								                </div>
		            						</div>
		            					<?php } ?>	
									</div>
								</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group row">
										   <div class="col-md-12">
										   	<label class="control-label" for="fileImg">Company Dark  Logo<span class="mandatoryfield">*</span></label>
			            					<input type="hidden" name="olddarklogo" id="olddarklogo" value="<?php echo $settingdata['company_small_logo']; ?>">
			        						<?php if(isset($settingdata)){ ?>
			            						<div class="imageupload" id="companysmalllogo">
									                <div class="file-tab"><img src="<?php echo MAIN_LOGO_IMAGE_URL.$settingdata['company_small_logo']; ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
									                    <label id="logolabel" class="btn btn-sm btn-primary btn-raised btn-file">
									                        <span id="darklogobtn">Change</span>
									                        <!-- The file is stored here. -->
									                        <input type="file" name="darklogo" id="darklogo" accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
									                    </label>
									                    <button type="button" class="btn btn-sm btn-danger btn-raised" style="display: inline-block;">Remove</button>
									                </div>
			            						</div>
			            					<?php }else{ ?>
			            						<div class="imageupload">
									                <div class="file-tab">
									                	<img src="" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px;">
									                    <label id="logolabel" class="btn btn-sm btn-primary btn-raised btn-file">
									                        <span id="smalllogobtn">Select Image</span>
									                        <input type="file" name="darklogo" id="darklogo" value="" accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
									                    </label>
									                    <button type="button" class="btn btn-sm btn-danger btn-raised">Remove</button>
									                </div>
			            						</div>
			            					<?php } ?>	
										</div>
									</div>	
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<div class="col-sm-12">
											<label class="control-label">Favicon icon <span class="mandatoryfield">*</span></label>
											<input type="hidden" name="oldfaviconicon" id="oldfaviconicon" value="<?php echo $settingdata['favicon']; ?>">
			        						<?php if(isset($settingdata)){ ?>
			            						<div class="imageupload">
									                <div class="file-tab"><img src="<?php echo MAIN_LOGO_IMAGE_URL.$settingdata['favicon']; ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
									                	
									                    <label id="faviconlabel" class="btn btn-sm btn-primary btn-raised btn-file">
									                        <span id="faviconbtn">Change</span>
									                        <!-- The file is stored here. -->
									                        <input type="file" name="faviconicon" id="faviconicon" accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
									                    </label>
									                    <button type="button" class="btn btn-sm btn-danger btn-raised" style="display: inline-block;">Remove</button>
									                </div>
			            						</div>
			            					<?php }else{ ?>
			            						<div class="imageupload">
									                <div class="file-tab">
									                	<img src="" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px;">
									                    <label id="faviconlabel" class="btn btn-sm btn-primary btn-raised btn-file">
									                        <span id="faviconbtn">Select Image</span>
									                        <input type="file" name="faviconicon" id="faviconicon" value="" accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
									                    </label>
									                    <button type="button" class="btn btn-sm btn-danger btn-raised">Remove</button>
									                </div>
			            						</div>
			            					<?php } ?>
										</div>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group row">
										   <div class="col-md-12">
										   	<label class="control-label" for="fileImg">Product Default Image<span class="mandatoryfield">*</span></label>
			            					<input type="hidden" name="oldproductdefaultimage" id="oldproductdefaultimage" value="<?php echo $settingdata['productdefaultimage']; ?>">
			        						<?php if(isset($settingdata)){ ?>
			            						<div class="imageupload" id="productdefaultimagediv">
									                <div class="file-tab"><img src="<?php echo PRODUCT.$settingdata['productdefaultimage']; ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
									                    <label id="logolabel" class="btn btn-sm btn-primary btn-raised btn-file">
									                        <span id="productdefaultimagebtn">Change</span>
									                        <!-- The file is stored here. -->
									                        <input type="file" name="productdefaultimage" id="productdefaultimage" accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
									                    </label>
									                    <button type="button" class="btn btn-sm btn-danger btn-raised" style="display: inline-block;">Remove</button>
									                </div>
			            						</div>
			            					<?php }else{ ?>
			            						<div class="imageupload">
									                <div class="file-tab">
									                	<img src="" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px;">
									                    <label id="logolabel" class="btn btn-sm btn-primary btn-raised btn-file">
									                        <span id="productdefaultimagebtn">Select Image</span>
									                        <input type="file" name="productdefaultimage" id="productdefaultimage" value="" accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
									                    </label>
									                    <button type="button" class="btn btn-sm btn-danger btn-raised">Remove</button>
									                </div>
			            						</div>
			            					<?php } ?>	
										</div>
									</div>	
								</div>
								<div class="col-md-12" style="text-align: center;">

									<div class="form-group">
										<?php if(isset($settingdata)){ ?>
											<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
											<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
										<?php }else{ ?>
										  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
										  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
										<?php } ?>
										<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>setting" title=<?=cancellink_title?>><?=cancellink_text?></a>
									</div>
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
