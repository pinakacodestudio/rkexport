<div class="page-content">
	<div class="page-heading">            
        <h1><?php if(isset($vehicleinsurancerow)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($vehicleinsurancerow)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>


    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-8 col-lg-8 col-lg-offset-2 col-md-offset-2">
					<form class="form-horizontal" id="vehicleinsuranceform">
						<input type="hidden" name="vehicleinsuranceid" value="<?php if(isset($vehicleinsurancerow)){ echo $vehicleinsurancerow['id']; } ?>">

						
							
						<div class="form-group" id="vehicleid_div">
							<label for="vehicleid" class="col-sm-4 control-label">Vehicle Name <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<select id="vehicleid" name="vehicleid" class="selectpicker form-control" data-live-search="true" data-size="8" title="Select Vehicle Name">
								<option value=0>Select Vehicle Name</option>
									<?php foreach($vehicledata as $row){ ?>
										<option value="<?php echo $row['id']; ?>" <?php if(isset($vehicleinsurancerow)){ if($row['id'] == $vehicleinsurancerow['vehicleid']){ echo 'selected'; } } ?>><?php echo $row['manufacturingcompany']." (".$row['registrationno'].")"; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group" id="companyname_div">
							<label for="companyname" class="col-sm-4 control-label">Company Name <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<input id="companyname" type="text" name="companyname" value="<?php if(!empty($vehicleinsurancerow)){ echo $vehicleinsurancerow['companyname']; } ?>" class="form-control">
							</div>
						</div>

						<div class="form-group" id="policyno_div">
							<label for="policyno" class="col-sm-4 control-label">Policy No. <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<input id="policyno" type="text" name="policyno" value="<?php if(!empty($vehicleinsurancerow)){ echo $vehicleinsurancerow['policyno']; } ?>" class="form-control">
							</div>
						</div>
						<div class="form-group" id="insurancedate_div">
							<label for="fromdate" class="col-sm-4 control-label">Insurance Date <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<div class="input-daterange input-group" id="datepicker-range">
									<input type="text" class="input-small form-control" name="fromdate" id="fromdate" value="<?php if(!empty($vehicleinsurancerow)){ echo $this->general_model->displaydate($vehicleinsurancerow['fromdate']); } ?>" readonly/>
									<span class="input-group-addon">to</span>
									<input type="text" class="input-small form-control" name="todate" id="todate" value="<?php if(!empty($vehicleinsurancerow)){ echo $this->general_model->displaydate($vehicleinsurancerow['todate']); } ?>" readonly/>
								</div>
							</div>
						</div>

						<div class="form-group" id="paymentdate_div">
							<label for="paymentdate" class="col-sm-4 control-label">Payment Date <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<div class="input-daterange input-group" id="datepicker">
									<input type="text" class="input-small form-control" name="paymentdate" id="paymentdate" value="<?php if(!empty($vehicleinsurancerow)){ echo $this->general_model->displaydate($vehicleinsurancerow['paymentdate']); } ?>" style="text-align: left;" readonly/>
								</div>
							</div>
						</div>
						<div class="form-group" id="proof_div">
								<label for="textfile" class="col-md-4 control-label" > Proof <span class="mandatoryfield"> * </span></label>
								<div class="col-md-8" >
										<input type="hidden" name="oldproof" id="oldproof" value="<?php if(isset($vehicleinsurancerow)){ echo $vehicleinsurancerow['proof'];} ?>">
									<div class="input-group" id="fileupload">
										<span class="input-group-btn" style="padding: 0 0px 0px 0px;">
											<span class="btn btn-primary btn-raised btn-sm btn-file">Browse...
													<input type="file" name="fileproof"  id="fileproof" onchange="validfile($(this))">
											</span>
											</span>                                        
											<input type="text" id="textfile" class="form-control" name="textfile" value="<?php  
														if(isset($vehicleinsurancerow)){ echo $vehicleinsurancerow['proof'];}
											?>" readonly >
								</div>                                      
							</div>
						</div>
						<div class="form-group">
							<label for="focusedinput" class="col-sm-4 control-label"></label>
							<div class="col-sm-8">
								<?php if(isset($vehicleinsurancerow)){ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
								<?php }else{ ?>
								  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
								  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
								<?php } ?>
								<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>vehicle-insurance" title=<?=cancellink_title?>><?=cancellink_text;?></a>
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