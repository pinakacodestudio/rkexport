<script type="text/javascript">
	var oldbranchid = <?php if(isset($vehiclerow)){ echo $vehiclerow['branchid'];}else { echo 0; }?>;
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($vehiclerow)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url(); ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($vehiclerow)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
					<form class="form-horizontal" id="vehicleform">
						<input type="hidden" name="vehicleid" value="<?php if(isset($vehiclerow)){ echo $vehiclerow['id']; } ?>">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group" id="school_div">
									<label for="schoolid" class="col-sm-4 control-label">School <span class="mandatoryfield">*</span></label>
									<div class="col-sm-8">
										<select id="schoolid" name="schoolid" class="selectpicker form-control" data-live-search="true" data-size="8" title="Select School">
											<?php foreach($schooldata as $row){ ?>
												<option value="<?php echo $row['id']; ?>" <?php if(isset($vehiclerow)){ if($row['id'] == $vehiclerow['schoolid']){ echo 'selected'; } }else if($this->session->userdata(base_url().'SCHOOL') == $row['id']){ echo "selected"; } ?>><?php echo $row['name']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group" id="branch_div">
									<label for="branchid" class="col-sm-4 control-label">Branch</label>
									<div class="col-sm-8">
										<select id="branchid" name="branchid" class="selectpicker form-control" data-live-search="true" data-size="8" title="Select Branch">
											
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group" id="manufacturingcompany_div">
									<label for="manufacturingcompany" class="col-sm-4 control-label">Mfg. Company <span class="mandatoryfield">*</span></label>
									<div class="col-sm-8">
										<input id="manufacturingcompany" type="text" name="manufacturingcompany" value="<?php if(!empty($vehiclerow)){ echo $vehiclerow['manufacturingcompany']; } ?>" class="form-control">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group" id="registrationno_div">
									<label for="registrationno" class="col-sm-4 control-label">Registration No. <span class="mandatoryfield">*</span></label>
									<div class="col-sm-8">
										<input id="registrationno" type="text" name="registrationno" value="<?php if(!empty($vehiclerow)){ echo $vehiclerow['registrationno']; } ?>" class="form-control">
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group" id="type_div">
									<label for="type" class="col-sm-4 control-label">Bus Type <span class="mandatoryfield">*</span></label>
									<div class="col-sm-8">
										<select id="type" name="type" class="selectpicker form-control" data-live-search="true" data-size="5" title="Select Type">
											<?php foreach($this->Vehicletype as $key=>$value){ ?>
												<option value="<?php echo $key; ?>" <?php if(isset($vehiclerow)){ if($vehiclerow['type'] == $key){ echo 'selected'; } } ?>><?php echo $value; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group" id="seatingcapacity_div">
									<label for="seatingcapacity" class="col-sm-4 control-label">Seating Capacity <span class="mandatoryfield">*</span></label>
									<div class="col-sm-8">
										<input id="seatingcapacity" type="text" name="seatingcapacity" value="<?php if(!empty($vehiclerow)){ echo $vehiclerow['seatingcapacity']; } ?>" class="form-control" onkeypress="return isNumber(this.value)" >
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group" id="purchasedate_div">
									<label for="purchasedate" class="col-sm-4 control-label">Purchase Date <span class="mandatoryfield">*</span></label>
									<div class="col-sm-8">
										<div class="input-daterange input-group" id="datepicker">
											<input type="text" class="input-small form-control" name="purchasedate" id="purchasedate" value="<?php if(!empty($vehiclerow)){ echo date("d/m/Y",strtotime($vehiclerow['purchasedate'])); } ?>" style="text-align: left;" readonly	/>
											
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group" id="modelyear_div">
									<label for="modelyear" class="col-sm-4 control-label">Model Year <span class="mandatoryfield">*</span></label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="modelyear" name="modelyear" value="<?php if(isset($vehiclerow) && $vehiclerow['model']!='0000'){ echo $vehiclerow['model']; }?>" readonly>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12 text-center">
								<div class="form-group">
									<label for="focusedinput" class="col-sm-5 control-label">Activate</label>
									<div class="col-sm-6">
										<div class="col-sm-2" style="padding-left: 0px;">
											<div class="radio">
											<input type="radio" name="status" id="yes" value="1" <?php if(isset($vehiclerow) && $vehiclerow['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
											<label for="yes">Yes</label>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="radio">
											<input type="radio" name="status" id="no" value="0" <?php if(isset($vehiclerow) && $vehiclerow['status']==0){ echo 'checked'; }?>>
											<label for="no">No</label>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<?php if(isset($vehiclerow)){ ?>
										<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
										<input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
									<?php }else{ ?>
									  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
									  <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
									<?php } ?>
									<a class="<?=cancelbtn_class;?>" href="<?=ADMIN_URL?>vehicle" title=<?=cancelbtn_title?>><?=cancelbtn_text;?></a>
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