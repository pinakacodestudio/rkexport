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
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl'); ?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($userdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body pt-n">
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
						
								<div class="form-group" id="partycord_div">
									<div class="col-md-12">
										<label class="control-label" for="partycord">Party Cord<span class="mandatoryfield">*</span></label>
										<input id="partycord" class="form-control" name="partycord" value="<?php if(isset($userdata)){ echo $userdata['partycord']; } ?>" type="text" tabindex="1" onkeypress="return onlyAlphabets(event)">
									</div>
								</div>
								<div class="form-group" id="email_div">
									<div class="col-md-12">
										<label class="control-label" for="email">Email <span class="mandatoryfield">*</span></label>
										<input id="email" type="text" name="email" value="<?php if(isset($userdata)){ echo $userdata['email']; } ?>" class="form-control" tabindex="3">
									</div>
								</div>
								<div class="form-group" id="mobile_div">
									<div class="col-md-12">
										<label class="control-label" for="mobileno">Mobile No <span class="mandatoryfield">*</span></label>	
										<input id="mobileno" type="text" name="mobileno" value="<?php if(isset($userdata)){ echo $userdata['mobileno']; } ?>" class="form-control" maxlength="12" onkeypress="return isNumber(event)" tabindex="5">
									</div>
								</div>
								<div class="form-group" id="workforchannelid_div">
									<div class="col-sm-12">
										<label class="control-label" for="workforchannelid">Work for</label>
										<select id="workforchannelid" name="workforchannelid[]" class="selectpicker form-control" data-select-on-tab="true" data-size="5" tabindex="8" title="Select Work for" data-actions-box="true" multiple>
											<?php foreach($channeldata as $channel){ ?>
											<option value="<?php echo $channel['id']; ?>" <?php if(isset($userdata)){ if(in_array($channel['id'], explode(",",$userdata['workforchannelid']))){ echo 'selected'; } }  ?>><?php echo $channel['name']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								

								<div class="form-group" id="Department_div">
									<div class="col-sm-12">
										<label class="control-label" for="Department">Department <a href="<?php echo base_url().ADMINFOLDER?>Department" class="stepy-finish btn-primary btn btn-raised" target="_blank" title="VIEW"><i class="fa fa-plus" aria-hidden="true"></i></a></label>
										<select class="form-control selectpicker" id="Department" name="Departmentid" data-live-search="true" data-select-on-tab="true" data-size="5">
											<option value="0">Select Department</option>
											<?php foreach ($Departmentdata as $Departmentrow) { ?>        
												<option value="<?php echo $Departmentrow['id'];?>" <?php if(!empty($userdata))
												{if($userdata['departmentid']==$Departmentrow['id']){echo "selected";}} ?> >
												<?php echo ucwords($Departmentrow['name']);?></option>
											<?php } ?>
										</select>  
										
									</div>
									
								</div>
								<div class="form-group" id="date_div">
									<div class="col-sm-12">
										<label class="control-label" for="date">Join Date </label>
										    <input id="joindate" name="joindate"  type="text" class="form-control col-sm-6" value="<?php if(isset($userdata)){ echo $this->general_model->displaydate($userdata['joindate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" readonly>
									</div>
								</div>
								<div class="form-group" id="date_div">
									<div class="col-sm-12">
										<label class="control-label" for="date">Birth Date </label>
										    <input id="birthdate" name="birthdate"  type="text" class="form-control col-sm-6" value="<?php if(isset($userdata)){ echo $this->general_model->displaydate($userdata['birthdate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" readonly>
									</div>
								</div>
								<div class="form-group" id="date_div">
									<div class="col-sm-12">
										<label class="control-label" for="anniversarydate">Anniversary Date </label>
										    <input id="anniversarydate" name="anniversarydate"  type="text" class="form-control col-sm-6" value="<?php if(isset($userdata)){ echo $this->general_model->displaydate($userdata['anniversarydate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" readonly>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group" id="userrole_div">
									<div class="col-sm-12">
										<label class="control-label" for="userroleid">Employee Role <span class="mandatoryfield">*</span></label>
										<select id="userroleid" name="userroleid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" tabindex="8">
											<option value="0">Select Employee Role</option>
											<?php foreach($userroledata as $userrolerow){ ?>
											<option value="<?php echo $userrolerow['id']; ?>" <?php if(isset($userdata)){ if($userdata['roleid'] == $userrolerow['id']){ echo 'selected'; } }  ?>><?php echo $userrolerow['role']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							
								<div class="form-group" id="userrole_div">
									<div class="col-sm-12">
										<label class="control-label" for="branchid">Branch Name <span class="mandatoryfield">*</span><a href="<?php echo base_url().ADMINFOLDER?>branch" class="stepy-finish btn-primary btn btn-raised" target="_blank" title="VIEW"><i class="fa fa-plus" aria-hidden="true"></i></a></label>
										<select id="branchid" name="branchid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" tabindex="8">
											<option value="0">Select Branch</option>
											<?php foreach($Branchdata as $Branch){ ?>
											<option value="<?php echo $Branch['id']; ?>" <?php if(isset($userdata)){ if($userdata['branchid'] == $Branch['id']){ echo 'selected'; } }  ?>><?php echo $Branch['branchname']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="form-group" id="userrole_div">
									<div class="col-sm-12">
										<label class="control-label">Gender<span class="mandatoryfield">*</span></label><br>
										<label class="radio-inline">
										<input type="radio" name="gender" value="1" <?php if(isset($userdata['gender'])){ if($userdata['gender']==1){echo 'checked';}} ?> >Man
										</label>
										<label class="radio-inline">
										<input type="radio" name="gender" value="0" <?php if(isset($userdata['gender'])){ if($userdata['gender']!=1){echo 'checked';}} ?>>Woman
										</label>
									</div>
								</div>
								<?php //if(!isset($userdata)){ ?>
								<div class="form-group" id="password_div">
									<div class="col-md-12">
										<label class="control-label" for="password">Password <span class="mandatoryfield">*</span></label>
										<div>
											<div class="col-sm-10" style="padding: 0px;">
												<input id="password" type="text" name="password" class="form-control" tabindex="7" value="<?php if(isset($userdata)){ echo $this->general_model->decryptIt($userdata['password']); } ?>">
											</div>											
											<div class="col-sm-2 mt-sm pl-sm" style="padding-right: 0px;">
												<a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Generate Password" onclick="$('#password').val(randomPassword())"><i class="fa fa-refresh" aria-hidden="true"></i></a>
											</div>
										</div>
									</div>
								</div>
								<?php 	//} 	exit;?>
								<div class="form-group" id="designationid_div">
									<div class="col-md-12">
										<label class="control-label" for="designationid">Designation </label>
										<select id="designationid" name="designationid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" tabindex="2">
											<option value="0">Select Designation</option>
											<?php foreach($designationdata as $designationrow){ ?>
											<option value="<?php echo $designationrow['id']; ?>" <?php if(isset($userdata)){ if($userdata['designationid'] == $designationrow['id']){ echo 'selected'; } }  ?>><?php echo $designationrow['name']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="form-group" id="reportingto_div">
									<div class="col-sm-12">
										<label class="control-label" for="reportingto">Reporting To </label>
										<select class="form-control selectpicker" id="reportingto" name="reportingto" data-live-search="true" data-select-on-tab="true" data-size="5">
											<option value="0">Select Employee</option>
											<?php foreach ($reportingtodata as $reporting) { ?>        
												<option value="<?php echo $reporting['id'];?>"   <?php if(!empty($userdata))
												{if($userdata['reportingto']==$reporting['id']){echo "selected";}} ?> >
												<?php echo ucwords($reporting['name']);?></option>
											<?php } ?>
										</select>  
									</div>
								</div>
								<div class="form-group" id="City_div">
									<div class="col-sm-12">
										<label class="control-label" for="city">City </label>
										<select class="form-control selectpicker" id="city" name="cityid" data-live-search="true" data-select-on-tab="true" data-size="5">
											<option value="0">Select City</option>
											<?php foreach ($Citydata as $cityrow) { ?>        
												<option value="<?php echo $cityrow['id'];?>" <?php if(!empty($userdata))
												{if($userdata['cityid']==$cityrow['id']){echo "selected";}} ?> >
												<?php echo ucwords($cityrow['name']);?></option>
											<?php } ?>
										</select>  
									</div>
								</div>
								
								
								<div class="form-group" id="State_div">
									<div class="col-sm-12">
										<label class="control-label" for="State">State </label>
										<select class="form-control selectpicker" id="State" name="stateid" data-live-search="true" data-select-on-tab="true" data-size="5">
											<option value="0">Select State</option>
											<?php foreach ($statedata as $stateidrow) { ?>        
												<option value="<?php echo $stateidrow['id'];?>" <?php if(!empty($userdata))
												{if($userdata['stateid']==$stateidrow['id']){echo "selected";}} ?> >
												<?php echo ucwords($stateidrow['name']);?></option>
											<?php } ?>
										</select>  
									</div>
								</div>
								<div class="form-group" id="Country_div">
									<div class="col-sm-12">
										<label class="control-label" for="Country">Country</label>
										<select class="form-control selectpicker" id="Country" name="countryid" data-live-search="true" data-select-on-tab="true" data-size="5">
											<option value="0">Select Country</option>
											<?php foreach ($Countrydata as $Countryrow) { ?>        
												<option value="<?php echo $Countryrow['id'];?>" <?php if(!empty($userdata))
												{if($userdata['countryid']==$Countryrow['id']){echo "selected";}} ?> >
												<?php echo ucwords($Countryrow['name']);?></option>
											<?php } ?>
										</select>  
									</div>
								</div>
								<div class="form-group" id="address_div">
									<div class="col-sm-12">
										<label>Address</label>
										<textarea class="form-control" id="address" name="address" rows="3"><?php if(isset($userdata)){ echo $userdata['address']; } ?></textarea>
									</div>
								</div>
								<!-- <div class="form-group mt-xl">
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
								</div> -->
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="focusedinput" class="col-md-4 control-label">Profile Image</label>
									<div class="col-md-8 mt-md">
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
						<?php if(CRM==1){ ?>
						<div class="col-md-12"><hr></div>
						<div class="col-md-12 p-n">
							<div class="col-md-12"><h4>CRM Details</h4></div>
							
							<div class="col-md-4 notification_setting">
								<div class="form-group" id="inquirystatuschange_div">
									<div class="col-sm-12">
										<label class="control-label" for="inquirystatuschange"><?=Inquiry?> Status Change </label>
										<?php $inquirystatuseschangearr=array();
										if(isset($userdata)){
											$inquirystatuseschangearr = explode(",",$userdata['inquirystatuschange']);
										} ?>
										<select class="form-control selectpicker" id="inquirystatuschange" name="inquirystatuschange[]" data-live-search="true" data-size="5"  multiple title="Select <?=Inquiry?> Status Change" data-actions-box="true">
										<?php foreach($inquirystatuses as $is){ ?>
											<option value="<?php echo $is['id']; ?>" <?php if(in_array($is['id'],$inquirystatuseschangearr)){ echo 'selected'; } ?>><?php echo $is['name']; ?></option>
											<?php  } ?>
										</select>
									</div>  
								</div>
							</div>
							<div class="col-md-4 notification_setting">
								<div class="form-group" id="followupstatuschange_div">
									<div class="col-sm-12">
										<label class="control-label" for="followupstatuschange"><?=Followup?> Status Change </label>
										<?php $followupstatuschangearr = array();
											if(isset($userdata)){
												$followupstatuschangearr = explode(",",$userdata['followupstatuschange']);
											} ?>
										<select class="form-control selectpicker" id="followupstatuschange" name="followupstatuschange[]" data-live-search="true" data-size="5"  multiple title="Select <?=Followup?> Status Change" data-actions-box="true">
										<?php foreach($followupstatuses as $fs){ ?>
											<option value="<?php echo $fs['id']; ?>" <?php if(in_array($fs['id'],$followupstatuschangearr)){ echo 'selected'; } ?>><?php echo $fs['name']; ?></option>
											<?php  } ?>
										</select>
									</div>  
								</div>
							</div>
							<div class="col-md-6 notification_setting">
								<div class="form-group">
									<label for="focusedinput" class="col-md-5 control-label">New <?=Inquiry?> / Transfer <?=Inquiry?></label>
									<div class="col-md-7">
										<div class="col-md-4 col-xs-4" style="padding-left: 0px;">
											<div class="radio">
											<input type="radio" name="newtransferinquiry" id="newtransferinquiryyes" value="1" <?php if(isset($userdata) && $userdata['newtransferinquiry']==1){ echo 'checked'; }else{ echo 'checked'; }?> >
											<label for="newtransferinquiryyes">ON</label>
											</div>
										</div>
										<div class="col-md-4 col-xs-4">
											<div class="radio">
											<input type="radio" name="newtransferinquiry" id="newtransferinquiryno" value="0" <?php if(isset($userdata) && $userdata['newtransferinquiry']==0){ echo 'checked'; }?>>
											<label for="newtransferinquiryno" >OFF</label>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="focusedinput" class="col-md-5 control-label">Sub Employee Notification</label>
									<div class="col-md-7">
										<div class="col-md-4 col-xs-4" style="padding-left: 0px;">
											<div class="radio">
											<input type="radio" name="subemployeenotification" id="subemployeenotificationyes" value="1" <?php if(isset($userdata) && $userdata['subemployeenotification']==1){ echo 'checked'; }else{ echo 'checked'; }?> >
											<label for="subemployeenotificationyes">ON</label>
											</div>
										</div>
										<div class="col-md-4 col-xs-4">
											<div class="radio">
											<input type="radio" name="subemployeenotification" id="subemployeenotificationno" value="0" <?php if(isset($userdata) && $userdata['subemployeenotification']==0){ echo 'checked'; }?>>
											<label for="subemployeenotificationno" >OFF</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6 notification_setting">
								<div class="form-group">
									<label for="focusedinput" class="col-md-5 control-label">My EOD Status</label>
									<div class="col-md-6">
										<div class="col-md-4 col-xs-4" style="padding-left: 0px;">
											<div class="radio">
											<input type="radio" name="myeodstatus" id="myeodstatusyes" value="1" <?php if(isset($userdata) && $userdata['myeodstatus']==1){ echo 'checked'; }else{ echo 'checked'; }?> >
											<label for="myeodstatusyes">ON</label>
											</div>
										</div>
										<div class="col-md-4 col-xs-4">
											<div class="radio">
											<input type="radio" name="myeodstatus" id="myeodstatusno" value="0" <?php if(isset($userdata) && $userdata['myeodstatus']==0){ echo 'checked'; }?>>
											<label for="myeodstatusno" >OFF</label>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="focusedinput" class="col-md-5 control-label">Team EOD Status</label>
									<div class="col-md-6">
										<div class="col-md-4 col-xs-4" style="padding-left: 0px;">
											<div class="radio">
											<input type="radio" name="teameodstatus" id="teameodstatusyes" value="1" <?php if(isset($userdata) && $userdata['teameodstatus']==1){ echo 'checked'; }else{ echo 'checked'; }?> >
											<label for="teameodstatusyes">ON</label>
											</div>
										</div>
										<div class="col-md-4 col-xs-4">
											<div class="radio">
											<input type="radio" name="teameodstatus" id="teameodstatusno" value="0" <?php if(isset($userdata) && $userdata['teameodstatus']==0){ echo 'checked'; }?>>
											<label for="teameodstatusno" >OFF</label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 p-n">
							<div class="col-md-6">
								<div class="form-group row">
									<label for="focusedinput" class="col-md-5 control-label">Mail Notification</label>
									<div class="col-md-7">
										<div class="col-md-4 col-xs-4" style="padding-left: 0px;">
										<div class="radio">
											<input type="radio" name="eodmail" id="eodmailon" value="1" <?php if(isset($userdata) && $userdata['eodmailsending']==1){ echo 'checked'; }else{ echo 'checked'; }?> >
											<label for="eodmailon">ON</label>
										</div>
										</div>
										<div class="col-md-4 col-xs-4">
										<div class="radio">
											<input type="radio" name="eodmail" id="eodmailoff" value="0" <?php  if(isset($userdata) && $userdata['eodmailsending']==0){ echo 'checked'; }?>>
											<label for="eodmailoff" >OFF</label>
										</div>
										</div>
									</div>
								</div>
                                <div class="form-group" style="display:none;">
									<label for="focusedinput" class="col-md-5 control-label"><?=Inquiry?> Report Mail</label>
									<div class="col-md-7">
										<div class="col-md-4 col-xs-4" style="padding-left: 0px;">
											<div class="radio">
											<input type="radio" name="inquiryreportmail" id="inquiryreportmailon" value="1" <?php if(isset($userdata) && $userdata['inquiryreportmailsending']==1){ echo 'checked'; }else{ echo 'checked'; }?> >
											<label for="inquiryreportmailon">On</label>
											</div>
										</div>
										<div class="col-md-4 col-xs-4">
											<div class="radio">
											<input type="radio" name="inquiryreportmail" id="inquiryreportmailoff" value="0" <?php  if(isset($userdata) && $userdata['inquiryreportmailsending']==0){ echo 'checked'; }?>>
											<label for="inquiryreportmailoff">Off</label>
											</div>
										</div>
									</div>
                                </div>
                            </div>
						</div>
						<?php } ?>

							<div class="col-md-12 text-center">
								
								<div class="form-group ">
									<label for="focusedinput" class="col-md-5 control-label">Activate</label>
									<div class="col-md-4">
										<div class="col-md-2 col-xs-2" style="padding-left: 0px;">
											<div class="radio">
											<input type="radio" name="status" id="yes" value="1" <?php if(isset($userdata) && $userdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
											<label for="yes">Yes</label>
											</div>
										</div>
										<div class="col-md-2 col-xs-2">
											<div class="radio">
											<input type="radio" name="status" id="no" value="0" <?php if(isset($userdata) && $userdata['status']==0){ echo 'checked'; }?>>
											<label for="no">No</label>
											</div>
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
