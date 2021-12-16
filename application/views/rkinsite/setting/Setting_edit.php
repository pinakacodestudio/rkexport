<style type="text/css">
	.datepicker1 {
		text-align: left !important;
		border-radius: 3px !important;
	}

	.discountonbilldiv {
		box-shadow: 0px 1px 6px #333;
		padding: 10px;
		margin-left: 30px
	}

	.nav-tabs>li {
		margin-bottom: 0px;
	}
</style>
<script type="text/javascript">
	var MAIN_LOGO_IMAGE_URL = '<?=MAIN_LOGO_IMAGE_URL?>';
	var countryid = '<?php if(isset($settingdata) && !empty($settingdata['countryid'])){ echo $settingdata['countryid']; }else{ echo DEFAULT_COUNTRY_ID; } ?>';
	var provinceid = '<?php if(isset($settingdata) && !empty($settingdata['provinceid'])){ echo $settingdata['provinceid']; }else{ echo 0; } ?>';
	var cityid = '<?php if(isset($settingdata) && !empty($settingdata['cityid'])){ echo $settingdata['cityid']; }else{ echo 0; } ?>';
</script>
<div class="page-content">
	<div class="page-heading">
		<h1><?php if(isset($settingdata)){ echo 'Edit'; }else{ echo 'Add'; } ?>
			<?=$this->session->userdata(base_url().'submenuname')?></h1>
		<small>
			<ol class="breadcrumb">
				<li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
				<li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
				<li><a
						href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a>
				</li>
				<li class="active"><?php if(isset($settingdata)){ echo 'Edit'; }else{ echo 'Add'; } ?>
					<?=$this->session->userdata(base_url().'submenuname')?></li>
			</ol>
		</small>
	</div>

	<div class="container-fluid">

		<div data-widget-group="group1">
			<div class="row">
				<div class="col-sm-12 col-md-12 col-lg-12">
					<form class="form-horizontal" id="settingform">
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default border-panel">
									<div class="panel-heading">
										<h2>Company Settings</h2>
									</div>
									<div class="panel-body">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group" id="name_div">
													<label for="name" class="col-sm-4 control-label">Company Name <span
															class="mandatoryfield">*</span></label>
													<div class="col-sm-8">
														<input id="name" type="text" name="name"
															value="<?php if(isset($settingdata)){ echo $settingdata['businessname']; } ?>"
															class="form-control">
													</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group" id="website_div">
													<label for="website" class="col-sm-4 control-label">Website <span
															class="mandatoryfield">*</span></label>
													<div class="col-sm-8">
														<input id="website" type="text" name="website"
															value="<?php if(isset($settingdata)){ echo $settingdata['website']; } ?>"
															class="form-control">
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group" id="address_div">
													<label for="address" class="col-md-4 control-label">Address <span class="mandatoryfield">*</span></label>
													<div class="col-md-8">
														<textarea class="form-control" id="address" rows="5" name="address"><?php echo $settingdata['address']; ?></textarea>
													</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group" id="country_div">
													<label class="col-sm-4 control-label" for="countryid">Country <span class="mandatoryfield">*</span></label>
													<div class="col-sm-8">
														<select id="countryid" name="countryid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
															<option value="0">Select Country</option>
															<?php foreach($countrydata as $country){ ?>
															<option value="<?php echo $country['id']; ?>" <?php /* if(isset($settingdata) && $settingdata['countryid']==$country['id']){ echo 'selected'; }else{ */ if(DEFAULT_COUNTRY_ID==$country['id']){ echo 'selected'; } /* } */ ?>><?php echo $country['name']; ?>
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
											</div>
										</div>
										
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="col-md-4 control-label">Company Logo <span
															class="mandatoryfield">*</span></label>
													<div class="col-md-8">
														<input type="hidden" name="oldlogo" id="oldlogo"
															value="<?php echo $settingdata['logo']; ?>">
														<?php if(isset($settingdata)){ ?>
														<div class="imageupload" id="companylogo">
															<div class="file-tab"><img
																	src="<?php echo MAIN_LOGO_IMAGE_URL.$settingdata['logo']; ?>"
																	alt="Image preview" class="thumbnail"
																	style="max-width: 150px; max-height: 150px">
																<label id="logolabel"
																	class="btn btn-sm btn-primary btn-raised btn-file">
																	<span id="logobtn">Change</span>
																	<!-- The file is stored here. -->
																	<input type="file" name="logo" id="logo"
																		accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
																</label>
																<button type="button"
																	class="btn btn-sm btn-danger btn-raised"
																	style="display: inline-block;">Remove</button>
															</div>
														</div>
														<?php }else{ ?>
														<div class="imageupload">
															<div class="file-tab">
																<img src="" alt="Image preview" class="thumbnail"
																	style="max-width: 150px; max-height: 150px;">
																<label id="logolabel"
																	class="btn btn-sm btn-primary btn-raised btn-file">
																	<span id="logobtn">Select Image</span>
																	<input type="file" name="logo" id="logo" value=""
																		accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
																</label>
																<button type="button"
																	class="btn btn-sm btn-danger btn-raised">Remove</button>
															</div>
														</div>
														<?php } ?>
													</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="col-md-4 control-label" for="fileImg">Company Dark
														Logo<span class="mandatoryfield">*</span></label>
													<div class="col-md-8">
														<input type="hidden" name="olddarklogo" id="olddarklogo"
															value="<?php echo $settingdata['company_small_logo']; ?>">
														<?php if(isset($settingdata)){ ?>
														<div class="imageupload" id="companysmalllogo">
															<div class="file-tab"><img
																	src="<?php echo MAIN_LOGO_IMAGE_URL.$settingdata['company_small_logo']; ?>"
																	alt="Image preview" class="thumbnail"
																	style="max-width: 150px; max-height: 150px">
																<label id="logolabel"
																	class="btn btn-sm btn-primary btn-raised btn-file">
																	<span id="darklogobtn">Change</span>
																	<!-- The file is stored here. -->
																	<input type="file" name="darklogo" id="darklogo"
																		accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
																</label>
																<button type="button"
																	class="btn btn-sm btn-danger btn-raised"
																	style="display: inline-block;">Remove</button>
															</div>
														</div>
														<?php }else{ ?>
														<div class="imageupload">
															<div class="file-tab">
																<img src="" alt="Image preview" class="thumbnail"
																	style="max-width: 150px; max-height: 150px;">
																<label id="logolabel"
																	class="btn btn-sm btn-primary btn-raised btn-file">
																	<span id="smalllogobtn">Select Image</span>
																	<input type="file" name="darklogo" id="darklogo"
																		value=""
																		accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
																</label>
																<button type="button"
																	class="btn btn-sm btn-danger btn-raised">Remove</button>
															</div>
														</div>
														<?php } ?>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="col-md-4 control-label">Favicon icon <span
															class="mandatoryfield">*</span></label>
													<div class="col-md-8">
														<input type="hidden" name="oldfaviconicon" id="oldfaviconicon"
															value="<?php echo $settingdata['favicon']; ?>">
														<?php if(isset($settingdata)){ ?>
														<div class="imageupload" id="faviconiconfile">
															<div class="file-tab"><img
																	src="<?php echo MAIN_LOGO_IMAGE_URL.$settingdata['favicon']; ?>"
																	alt="Image preview" class="thumbnail"
																	style="max-width: 150px; max-height: 150px">

																<label id="faviconlabel"
																	class="btn btn-sm btn-primary btn-raised btn-file">
																	<span id="faviconbtn">Change</span>
																	<!-- The file is stored here. -->
																	<input type="file" name="faviconicon"
																		id="faviconicon"
																		accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
																</label>
																<button type="button"
																	class="btn btn-sm btn-danger btn-raised"
																	style="display: inline-block;">Remove</button>
															</div>
														</div>
														<?php }else{ ?>
														<div class="imageupload">
															<div class="file-tab">
																<img src="" alt="Image preview" class="thumbnail"
																	style="max-width: 150px; max-height: 150px;">
																<label id="faviconlabel"
																	class="btn btn-sm btn-primary btn-raised btn-file">
																	<span id="faviconbtn">Select Image</span>
																	<input type="file" name="faviconicon"
																		id="faviconicon" value=""
																		accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
																</label>
																<button type="button"
																	class="btn btn-sm btn-danger btn-raised">Remove</button>
															</div>
														</div>
														<?php } ?>
													</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="col-md-4 control-label" for="fileImg">Product Default
														Image<span class="mandatoryfield">*</span></label>
													<div class="col-md-8">
														<input type="hidden" name="oldproductdefaultimage"
															id="oldproductdefaultimage"
															value="<?php echo $settingdata['productdefaultimage']; ?>">
														<?php if(isset($settingdata)){ ?>
														<div class="imageupload" id="productdefaultimagediv">
															<div class="file-tab"><img
																	src="<?php echo PRODUCT.$settingdata['productdefaultimage']; ?>"
																	alt="Image preview" class="thumbnail"
																	style="max-width: 150px; max-height: 150px">
																<label id="logolabel"
																	class="btn btn-sm btn-primary btn-raised btn-file">
																	<span id="productdefaultimagebtn">Change</span>
																	<!-- The file is stored here. -->
																	<input type="file" name="productdefaultimage"
																		id="productdefaultimage"
																		accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
																</label>
																<button type="button"
																	class="btn btn-sm btn-danger btn-raised"
																	style="display: inline-block;">Remove</button>
															</div>
														</div>
														<?php }else{ ?>
														<div class="imageupload">
															<div class="file-tab">
																<img src="" alt="Image preview" class="thumbnail"
																	style="max-width: 150px; max-height: 150px;">
																<label id="logolabel"
																	class="btn btn-sm btn-primary btn-raised btn-file">
																	<span id="productdefaultimagebtn">Select
																		Image</span>
																	<input type="file" name="productdefaultimage"
																		id="productdefaultimage" value=""
																		accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
																</label>
																<button type="button"
																	class="btn btn-sm btn-danger btn-raised">Remove</button>
															</div>
														</div>
														<?php } ?>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="col-md-4 control-label" for="fileImg">Default Category
														Image<span class="mandatoryfield">*</span></label>
													<div class="col-md-8">
														<input type="hidden" name="olddefaultimagecategory"
															id="olddefaultimagecategory"
															value="<?php echo $settingdata['defaultimagecategory']; ?>">
														<?php if(isset($settingdata)){ ?>
														<div class="imageupload" id="defaultimagecategorydiv">
															<div class="file-tab"><img
																	src="<?php echo CATEGORY.$settingdata['defaultimagecategory']; ?>"
																	alt="Image preview" class="thumbnail"
																	style="max-width: 150px; max-height: 150px">
																<label id="logolabel"
																	class="btn btn-sm btn-primary btn-raised btn-file">
																	<span id="defaultimagecategorybtn">Change</span>
																	<!-- The file is stored here. -->
																	<input type="file" name="defaultimagecategory"
																		id="defaultimagecategory"
																		accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
																</label>
																<button type="button"
																	class="btn btn-sm btn-danger btn-raised"
																	style="display: inline-block;">Remove</button>
															</div>
														</div>
														<?php }else{ ?>
														<div class="imageupload">
															<div class="file-tab">
																<img src="" alt="Image preview" class="thumbnail"
																	style="max-width: 150px; max-height: 150px;">
																<label id="logolabel"
																	class="btn btn-sm btn-primary btn-raised btn-file">
																	<span id="defaultimagecategorybtn">Select
																		Image</span>
																	<input type="file" name="defaultimagecategory"
																		id="defaultimagecategory" value=""
																		accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
																</label>
																<button type="button"
																	class="btn btn-sm btn-danger btn-raised">Remove</button>
															</div>
														</div>
														<?php } ?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default border-panel">
									<div class="panel-heading">
										<h2>Contact Details</h2>
									</div>
									<div class="panel-body pt-n">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<div class="col-md-12">
														<label class="control-label" for="mobileno1">Mobile No.</label>
													</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<div class="col-md-12">
														<label class="control-label" for="googlelink">Email</label>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
											<?php if(!empty($settingdata) && !empty($mobiledata)) { ?>
												<?php for ($m=0; $m < count($mobiledata); $m++) { ?>
													<div class="row m-n countmobile" id="countmobile<?=($m+1)?>">
														<input type="hidden" name="mobilecontactdetailid<?=($m+1)?>" value="<?=$mobiledata[$m]['id']?>" id="mobilecontactdetailid<?=($m+1)?>">
														<div class="form-group mt-n" id="mobileno<?=($m+1)?>_div">
															<div class="col-md-8">
																<input id="mobileno<?=($m+1)?>" type="text" name="mobileno[]" value="<?=$mobiledata[$m]['mobileno']?>" class="form-control" maxlength="10" onkeypress="return isNumber(event)">
															</div>
															<div class="col-md-2 m-n p-sm pt-sm">	
																<?php if($m==0){?>
																	<?php if(count($mobiledata)>1){ ?>
																		<button type="button" class="btn btn-default btn-raised rm-mobile" onclick="removemobile(1)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
																	<?php }else { ?>
																		<button type="button" class="btn btn-default btn-raised add-mobile" onclick="addnewmobile()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
																	<?php } ?>

																<?php }else if($m!=0) { ?>
																	<button type="button" class="btn btn-default btn-raised rm-mobile" onclick="removemobile(<?=($m+1)?>)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
																<?php } ?>
																<button type="button" class="btn btn-default btn-raised btn-sm rm-mobile" onclick="removemobile(<?=($m+1)?>)"  style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
															
																<button type="button" class="btn btn-default btn-raised add-mobile" onclick="addnewmobile()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button> 
															</div>
														</div>
													</div>
												<?php } ?>
											<?php }else{ ?>
												<div class="row m-n countmobile" id="countmobile1">
													<div class="form-group mt-n" id="mobileno1_div">
														<div class="col-md-8">
															<input id="mobileno1" type="text" name="mobileno[]" value="" class="form-control" maxlength="10" onkeypress="return isNumber(event)">
														</div>
														<div class="col-md-2 m-n p-sm pt-sm">	
															<button type="button" class="btn btn-default btn-raised rm-mobile" onclick="removemobile(1)" style="padding: 5px 10px;display: none;"><i class="fa fa-minus"></i></button>
															<button type="button" class="btn btn-default btn-raised add-mobile" onclick="addnewmobile()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
														</div>
													</div>
												</div>
											<?php } ?>
											</div>
											<div class="col-md-6">
											<?php if(!empty($settingdata) && !empty($emaildata)) { ?>
												<?php for ($e=0; $e < count($emaildata); $e++) { ?>
													<div class="row m-n countemail" id="countemail<?=($e+1)?>">
														<input type="hidden" name="emailcontactdetailid<?=($e+1)?>" value="<?=$emaildata[$e]['id']?>" id="emailcontactdetailid<?=($e+1)?>">
														<div class="form-group mt-n" id="email<?=($e+1)?>_div">
															<div class="col-md-8">
																<input id="email<?=($e+1)?>" type="text" name="email[]" value="<?=$emaildata[$e]['email']?>" class="form-control">
															</div>
															<div class="col-md-2 m-n p-sm pt-sm">	
																<?php if($e==0){?>
																	<?php if(count($emaildata)>1){ ?>
																		<button type="button" class="btn btn-default btn-raised rm-email" onclick="removeemail(1)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
																	<?php }else { ?>
																		<button type="button" class="btn btn-default btn-raised add-email" onclick="addnewemail()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
																	<?php } ?>

																<?php }else if($e!=0) { ?>
																	<button type="button" class="btn btn-default btn-raised rm-email" onclick="removeemail(<?=($e+1)?>)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
																<?php } ?>
																<button type="button" class="btn btn-default btn-raised btn-sm rm-email" onclick="removeemail(<?=($e+1)?>)"  style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
															
																<button type="button" class="btn btn-default btn-raised add-email" onclick="addnewemail()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button> 
															</div>
														</div>
													</div>
												<?php } ?>
											<?php }else{ ?>
												<div class="row m-n countemail" id="countemail1">
													<div class="form-group mt-n" id="email1_div">
														<div class="col-md-8">
															<input id="email1" type="text" name="email[]" value="" class="form-control">
														</div>
														<div class="col-md-2 m-n p-sm pt-sm">	
															<button type="button" class="btn btn-default btn-raised rm-email" onclick="removeemail(1)" style="padding: 5px 10px;display: none;"><i class="fa fa-minus"></i></button>
															<button type="button" class="btn btn-default btn-raised add-email" onclick="addnewemail()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
														</div>
													</div>
												</div>
											<?php } ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default border-panel">
									<div class="panel-heading">
										<h2>Color Settings</h2>
									</div>
									<div class="panel-body">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group row" id="themecolor_div">
													<label class="col-md-4 control-label" for="themecolor">Theme Color<span class="mandatoryfield">*</span>
													</label>
													<div class="col-md-8">
														<div>	
															<div class="col-sm-11" style="padding: 0px;">
																<input type="text" id="themecolor" class="form-control selectcolor" name="themecolor" data-defaultvalue="#03a9f4" value="<?php if(!empty($settingdata)){ echo $settingdata['themecolor']; }else{ echo '#70c24a'; } ?>">
															</div>
															<div class="col-sm-1 pr-n pl-n mt-sm">
																<a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Reset" onclick="$('input[name=\'themecolor\']').minicolors('value','#03a9f4');"><i class="fa fa-refresh" aria-hidden="true"></i></a>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group row" id="fontcolor_div">
													<label class="col-md-4 control-label" for="fontcolor">Font Color<span class="mandatoryfield">*</span></label>
													<div class="col-md-8">
														<div>	
															<div class="col-sm-11" style="padding: 0px;">
																<input type="text" id="fontcolor" class="form-control selectcolor" name="fontcolor" data-defaultvalue="#FFFFFF" value="<?php if(!empty($settingdata)){ echo $settingdata['fontcolor']; }else{ echo '#fff'; } ?>">
															</div>
															<div class="col-sm-1 pr-n pl-n mt-sm">
																<a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Reset" onclick="$('input[name=\'fontcolor\']').minicolors('value','#FFF');"><i class="fa fa-refresh" aria-hidden="true"></i></a>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										
										

										<div class="row">
											<div class="col-md-6">
												<div class="form-group" id="sidebarbgcolor_div">
													<label for="sidebarbgcolor" class="col-md-4 control-label">Sidebar BG Color<span
															class="mandatoryfield">*</span>
													</label>
													<div class="col-md-8">
														<div>
															<div class="col-sm-11" style="padding: 0px;">
																<input type="text" id="sidebarbgcolor" class="form-control selectcolor" name="sidebarbgcolor" value="<?php if(!empty($settingdata) && $settingdata['sidebarbgcolor']!=""){ echo $settingdata['sidebarbgcolor']; }else{ echo '#2196f3'; } ?>">
															</div>
															<div class="col-sm-1 pr-n pl-n mt-sm">
																<a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Reset" onclick="$('input[name=\'sidebarbgcolor\']').minicolors('value','#2196f3');"><i class="fa fa-refresh" aria-hidden="true"></i></a>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group" id="sidebarmenuactivecolor_div">
													<label for="sidebarmenuactivecolor" class="col-md-4 control-label">Sidebar Menu Active Color<span
															class="mandatoryfield">*</span>
													</label>
													<div class="col-md-8">
														<div>
															<div class="col-sm-11" style="padding: 0px;">
																<input type="text" id="sidebarmenuactivecolor" class="form-control selectcolor" name="sidebarmenuactivecolor" value="<?php if(!empty($settingdata) && $settingdata['sidebarmenuactivecolor']!=""){ echo $settingdata['sidebarmenuactivecolor']; }else{ echo '#42a5f5'; } ?>">
															</div>
															<div class="col-sm-1 pr-n pl-n mt-sm">
																<a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Reset" onclick="$('input[name=\'sidebarmenuactivecolor\']').minicolors('value','#42a5f5');"><i class="fa fa-refresh" aria-hidden="true"></i></a>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-6">
												<div class="form-group" id="sidebarsubmenubgcolor_div">
													<label for="sidebarsubmenubgcolor" class="col-md-4 control-label">Sidebar Submenu BG Color<span
															class="mandatoryfield">*</span>
													</label>
													<div class="col-md-8">
														<div>
															<div class="col-sm-11" style="padding: 0px;">
																<input type="text" id="sidebarsubmenubgcolor" class="form-control selectcolor" name="sidebarsubmenubgcolor" value="<?php if(!empty($settingdata) && $settingdata['sidebarsubmenubgcolor']!=""){ echo $settingdata['sidebarsubmenubgcolor']; }else{ echo '#1a78c2'; } ?>">
															</div>
															<div class="col-sm-1 pr-n pl-n mt-sm">
																<a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Reset" onclick="$('input[name=\'sidebarsubmenubgcolor\']').minicolors('value','#1a78c2');"><i class="fa fa-refresh" aria-hidden="true"></i></a>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group" id="sidebarsubmenuactivecolor_div">
													<label for="sidebarsubmenuactivecolor" class="col-md-4 control-label">Sidebar Submenu Active Color<span
															class="mandatoryfield">*</span></label>
													<div class="col-md-8">
														<div>
															<div class="col-sm-11" style="padding: 0px;">
																<input type="text" id="sidebarsubmenuactivecolor" class="form-control selectcolor" name="sidebarsubmenuactivecolor" value="<?php if(!empty($settingdata) && $settingdata['sidebarsubmenuactivecolor']!=""){ echo $settingdata['sidebarsubmenuactivecolor']; }else{ echo '#2196f3'; } ?>">
															</div>
															<div class="col-sm-1 pr-n pl-n mt-sm">
																<a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Reset" onclick="$('input[name=\'sidebarsubmenuactivecolor\']').minicolors('value','#2196f3');"><i class="fa fa-refresh" aria-hidden="true"></i></a>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-6">
												<div class="form-group" id="footerbgcolor_div">
													<label for="footerbgcolor" class="col-md-4 control-label">Footer BG Color<span
															class="mandatoryfield">*</span>
													</label>
													<div class="col-md-8">
														<div>
															<div class="col-sm-11" style="padding: 0px;">
																<input type="text" id="footerbgcolor" class="form-control selectcolor" name="footerbgcolor" data-defaultvalue="#03a9f4" value="<?php if(!empty($settingdata) && $settingdata['footerbgcolor']!=""){ echo $settingdata['footerbgcolor']; }else{ echo '#03a9f4'; } ?>">
															</div>
															<div class="col-sm-1 pr-n pl-n mt-sm">
																<a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Reset" onclick="$('input[name=\'footerbgcolor\']').minicolors('value','#03a9f4');"><i class="fa fa-refresh" aria-hidden="true"></i></a>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group" id="linkcolor_div">
													<label for="linkcolor" class="col-md-4 control-label">Link Color<span
															class="mandatoryfield">*</span>
													</label>
													<div class="col-md-8">
														<div>
															<div class="col-sm-11" style="padding: 0px;">
																<input type="text" id="linkcolor" class="form-control selectcolor" name="linkcolor" data-defaultvalue="#03a9f4" value="<?php if(!empty($settingdata) && $settingdata['linkcolor']!=""){ echo $settingdata['linkcolor']; }else{ echo '#03a9f4'; } ?>">
															</div>
															<div class="col-sm-1 pr-n pl-n mt-sm">
																<a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Reset" onclick="$('input[name=\'linkcolor\']').minicolors('value','#bf2e2e');"><i class="fa fa-refresh" aria-hidden="true"></i></a>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-6">
												<div class="form-group" id="tableheadercolor_div">
													<label for="tableheadercolor" class="col-md-4 control-label">Table Header Color<span
															class="mandatoryfield">*</span>
													</label>
													<div class="col-md-8">
														<div>
															<div class="col-sm-11" style="padding: 0px;">
																<input type="text" id="tableheadercolor" class="form-control selectcolor" name="tableheadercolor" value="<?php if(!empty($settingdata) && $settingdata['tableheadercolor']!=""){ echo $settingdata['tableheadercolor']; }else{ echo '#000000'; } ?>">
															</div>
															<div class="col-sm-1 pr-n pl-n mt-sm">
																<a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Reset" onclick="$('input[name=\'tableheadercolor\']').minicolors('value','#bd9117');"><i class="fa fa-refresh" aria-hidden="true"></i></a>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default border-panel">
									<div class="panel-heading">
										<h2>Order Mail Setting</h2>
									</div>
									<div class="panel-body">
										<div class="row">
											<div class="col-md-12">
												<div class="form-group row" id="orderemails_div">
													<label class="col-md-2 control-label" for="orderemails">Order Email</label>
													<div class="col-md-9">
														<input id="orderemails" type="text" name="orderemails"
															value="<?php echo $settingdata['orderemails']; ?>"
															class="form-control">
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default border-panel">
									<div class="panel-heading">
										<h2>Action</h2>
									</div>
									<div class="panel-body">
										<div class="row">
											<div class="form-group text-center">
												<?php if(isset($settingdata)){ ?>
												<input type="button" id="submit" onclick="checkvalidation()"
													name="submit" value="UPDATE" class="btn btn-primary btn-raised">
												<input type="reset" name="reset" value="RESET"
													class="btn btn-info btn-raised">
												<?php }else{ ?>
												<input type="button" id="submit" onclick="checkvalidation()"
													name="submit" value="ADD" class="btn btn-primary btn-raised">
												<input type="reset" name="reset" value="RESET"
													class="btn btn-info btn-raised">
												<?php } ?>
												<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>setting"
													title=<?=cancellink_title?>><?=cancellink_text?></a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div> <!-- .container-fluid -->
</div> <!-- #page-content -->