<script type="text/javascript">
	var countryid = <?php if(isset($areadata)){ echo $areadata['countryid'];}else { echo '0'; }?>;
	var provinceid = <?php if(isset($areadata)){ echo $areadata['stateid'];}else { echo '0'; }?>;
	var cityid = <?php if(isset($areadata)){ echo $areadata['cityid'];}else { echo '0'; }?>;
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($areadata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl');?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($areadata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-6 col-lg-6 col-lg-offset-3 col-md-offset-3">
						<form class="form-horizontal" id="area-form" name="area-form">
							<input type="hidden" name="areaid" value="<?php if(isset($areadata)){ echo $areadata['id']; } ?>">
							<div class="form-group" id="country_div">
								<label for="countryid" class="col-sm-3 control-label">Country <span class="mandatoryfield">*</span></label>
								<div class="col-sm-8">
									<select id="countryid" name="countryid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" tabindex="1">
										<option value="0">Select Country</option>
										<?php foreach($countrydata as $countryrow){ ?>
											<option value="<?php echo $countryrow['id']; ?>" <?php if(isset($areadata)){ if($areadata['countryid'] == $countryrow['id']){ echo 'selected'; } } ?>><?php echo $countryrow['name']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group" id="province_div">
								<label for="focusedinput" class="col-sm-3 control-label">Province <span class="mandatoryfield">*</span></label>
								<div class="col-sm-8">
									<select id="provinceid" name="provinceid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" readonly tabindex="2">
										<option value="0">Select Province</option>
									</select>
								</div>
							</div>
							<div class="form-group" id="city_div">
								<label class="col-sm-3 control-label">City <span class="mandatoryfield">*</span></label>
								<div class="col-sm-8">
									<select id="cityid" name="cityid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" readonly tabindex="3">
										<option value="0">Select City</option>
									</select>
								</div>
							</div>
							<div class="form-group" id="areaname_div">
								<label for="focusedinput" class="col-sm-3 control-label">Area Name <span class="mandatoryfield">*</span></label>
								<div class="col-sm-8">
									<input id="areaname" type="text" name="areaname" value="<?php if(!empty($areadata)){ echo $areadata['areaname']; } ?>" class="form-control" tabindex="4" onkeypress="return onlyAlphabets(event)">
								</div>
							</div>
							<div class="form-group" id="pincode_div">
								<label for="focusedinput" class="col-sm-3 control-label">Pin Code <span class="mandatoryfield">*</span></label>
								<div class="col-sm-8">
									<input id="pincode" type="text" name="pincode" value="<?php if(!empty($areadata)){ echo $areadata['pincode']; } ?>" class="form-control" tabindex="5" maxlength="10" onkeypress="return isNumber(event)">
								</div>
							</div>
							<div class="form-group">
								<label for="focusedinput" class="col-sm-3 control-label"></label>
								<div class="col-sm-8">
									<?php if(!empty($areadata)){ ?>
										<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
										<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
									<?php }else{ ?>
									  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
									  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
									<?php } ?>
									<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
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