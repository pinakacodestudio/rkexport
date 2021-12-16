<script type="text/javascript">
	var olduserroleid = <?php if(isset($userdata)){ echo $userdata['roleid'];}else { echo 0; }?>;
	var profileimgpath = '<?php echo PROFILE;?>';
	var defaultprofileimgpath = '<?php echo DEFAULT_PROFILE;?>';
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($userdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url(); ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($userdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default">
		        <div class="panel-body">
					<form action="#" id="userform" class="form-horizontal">
						<input type="hidden" name="userid" value="<?php if(isset($userdata)){ echo $userdata['id']; } ?>">
						<div class="col-md-12 p-n">
							<div class="col-md-4">
								<div class="form-group" id="name_div">
									<div class="col-md-12">
										<label class="control-label" for="name">Name <span class="mandatoryfield">*</span></label>
										<input id="name" class="form-control" name="name" value="<?php if(isset($userdata)){ echo $userdata['name']; } ?>" type="text" tabindex="1" onkeypress="return onlyAlphabets(event)">
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group" id="userrole_div">
									<div class="col-sm-12">
										<label class="control-label" for="userroleid">User Role <span class="mandatoryfield">*</span></label>
										<select id="userroleid" name="userroleid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" tabindex="2">
											<option value="0">Select User Role</option>
											<?php foreach($userroledata as $userrolerow){ ?>
											<option value="<?php echo $userrolerow['id']; ?>" <?php if(isset($userdata)){ if($userdata['roleid'] == $userrolerow['id']){ echo 'selected'; } }  ?>><?php echo $userrolerow['role']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 p-n">
							<div class="col-md-4">
								<div class="form-group" id="email_div">
									<div class="col-md-12">
										<label class="control-label" for="email">Email <span class="mandatoryfield">*</span></label>
										<input id="email" type="text" name="email" value="<?php if(isset($userdata)){ echo $userdata['email']; } ?>" class="form-control" tabindex="3">
									</div>
								</div>
								<div class="form-group">
									<label for="focusedinput" class="col-md-4 control-label">Activate</label>
									<div class="col-md-8">
										<div class="col-md-4 col-xs-4" style="padding-left: 0px;">
											<div class="radio">
											<input type="radio" name="status" id="yes" value="1" <?php if(isset($userdata) && $userdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
											<label for="yes">Yes</label>
											</div>
										</div>
										<div class="col-md-4 col-xs-4">
											<div class="radio">
											<input type="radio" name="status" id="no" value="0" <?php if(isset($userdata) && $userdata['status']==0){ echo 'checked'; }?>>
											<label for="no">No</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group" id="mobile_div">
									<div class="col-md-12">
										<label class="control-label" for="mobileno">Mobile No <span class="mandatoryfield">*</span></label>	
										<input id="mobileno" type="text" name="mobileno" value="<?php if(isset($userdata)){ echo $userdata['mobileno']; } ?>" class="form-control" maxlength="12"  onkeypress="return isNumber(event)" tabindex="5">
									</div>
								</div>
								<?php if(!isset($userdata)){ ?>
								<div class="form-group" id="password_div">
									<div class="col-md-12">
										<label class="control-label" for="password">Password <span class="mandatoryfield">*</span></label>
										<div>
											<div class="col-sm-10" style="padding: 0px;">
												<input id="password" type="text" name="password" class="form-control" tabindex="7">
											</div>
											<div class="col-sm-2" style="padding-right: 0px;">
												<a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Generate Password" onclick="$('#password').val(randString())"><i class="fa fa-refresh" aria-hidden="true"></i></a>
											</div>
										</div>
									</div>
								</div>
								<?php } ?>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="focusedinput" class="col-md-4 control-label">Profile Image</label>
									<div class="col-md-8">
										<input type="hidden" name="oldprofileimage" id="oldprofileimage" value="<?php if(isset($userdata)){ echo $userdata['image']; }?>">
										<input type="hidden" name="removeoldImage" id="removeoldImage" value="0">
										<?php if(isset($userdata) && $userdata['image']!=''){ ?>
		            						<div class="imageupload" id="profileimage">
								                <div class="file-tab"><img src="<?php echo PROFILE.$userdata['image']; ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
								                    <label id="profileimagelabel" class="btn btn-sm btn-primary btn-raised btn-file">
								                        <span id="profileimagebtn">Change</span>
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
								                    <label id="logolabel" class="btn btn-sm btn-primary btn-raised btn-file">
								                        <span id="profileimagebtn">Select Image</span>
								                        <input type="file" name="image" id="image" accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png">
								                    </label>
								                    <button type="button" class="btn btn-sm btn-danger btn-raised" id="remove">Remove</button>
								                </div>
		            						</div>
		            					<?php } ?>
									</div>
								</div>
								
							</div>
						</div>
						<div class="col-md-12 text-center">
							<div class="form-group">
								<?php if(isset($userdata)){ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php }else{ ?>
								  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
								  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php } ?>
								<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>user" title=<?=cancellink_title?>><?=cancellink_text?></a>
							</div>
						</div>
					</form>
				</div>
		      </div>
		    </div>
		  </div>
		</div>
		
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->
