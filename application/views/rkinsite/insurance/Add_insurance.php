<div class="page-content">
	<div class="page-heading">            
        <h1><?php if(isset($insurancedata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($insurancedata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>


    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
					<form class="form-horizontal" id="insuranceform">
						<input type="hidden" name="insuranceid" value="<?php if(isset($insurancedata)){ echo $insurancedata['id']; } ?>">
						<div class="row">
		    				<div class="col-md-6">
								<div class="form-group" id="vehicle_div">
									<label for="vehicleid" class="col-md-4 col-sm-3 control-label">Select Vehicle <span class="mandatoryfield">*</span></label>
									<div class="col-md-8 col-sm-6">
										<select id="vehicleid" name="vehicleid" class="selectpicker form-control" data-live-search="true" data-size="8" title="Select Vehicle">
										<option value=0>Select Vehicle</option>
											<?php foreach($vehicledata as $row){ ?>
												<option value="<?php echo $row['id']; ?>" <?php if(isset($insurancedata)){ if($row['id'] == $insurancedata['vehicleid']){ echo 'selected'; } } ?>><?php echo $row['vehiclename']." (".$row['vehicleno'].")"; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group" id="companyname_div">
									<label for="companyname" class="col-md-4 col-sm-3 control-label">Insurance Company <span class="mandatoryfield">*</span></label>
									<div class="col-md-8 col-sm-6">
										<input id="companyname" type="text" name="companyname" data-url="<?php echo base_url().ADMINFOLDER.'insurance/searchInsuranceCompany';?>" value="<?php if(isset($insurancedata)){ echo $insurancedata['companyname']; } ?>" placeholder="Select Insurance Company" data-provide="companyname" class="form-control">
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group" id="insurancedate_div">
									<label for="fromdate" class="col-md-4 col-sm-3 control-label">Insurance Time <span class="mandatoryfield">*</span></label>
									<div class="col-md-8 col-sm-6">
										<div class="input-daterange input-group" id="datepicker-range">
											<div class="input-group">
												<input type="text" class="input-small form-control" name="fromdate" style="text-align: left;" id="fromdate" value="<?php if(!empty($insurancedata) && $insurancedata['fromdate']!='0000-00-00'){ echo $this->general_model->displaydate($insurancedata['fromdate']); } ?>" readonly/>
												<span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
											</div>
											<span class="input-group-addon">to</span>
											<div class="input-group">
												<input type="text" class="input-small form-control" name="todate" style="text-align: left;" id="todate" value="<?php if(!empty($insurancedata) && $insurancedata['todate']!='0000-00-00'){ echo $this->general_model->displaydate($insurancedata['todate']); } ?>" readonly/>
												<span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group" id="paymentdate_div">
									<label for="paymentdate" class="col-md-4 col-sm-3 control-label">Payment Date</label>
									<div class="col-md-8 col-sm-6">
										<div class="input-daterange input-group" id="datepicker">
											<div class="input-group">
												<input type="text" class="input-small form-control" name="paymentdate" id="paymentdate" value="<?php if(!empty($insurancedata) && $insurancedata['paymentdate']!='0000-00-00'){ echo $this->general_model->displaydate($insurancedata['paymentdate']); } ?>" style="text-align: left;" readonly/>
												<span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group" id="policyno_div">
									<label for="policyno" class="col-md-4 col-sm-3 control-label">Policy No.</label>
									<div class="col-md-8 col-sm-6">
										<input id="policyno" type="text" name="policyno" value="<?php if(!empty($insurancedata)){ echo $insurancedata['policyno']; } ?>" class="form-control">
									</div>
								</div>
							</div>
		    				<div class="col-md-6">
								<div class="form-group" id="proof_div">
									<label for="textfile" class="col-md-4 col-sm-3 control-label">Proof</label>
									<div class="col-md-8 col-sm-6">
										<input type="hidden" name="oldproof" id="oldproof" value="<?php if(isset($insurancedata)){ echo $insurancedata['proof'];} ?>">
										<div class="input-group" id="fileupload">
											<span class="input-group-btn" style="padding: 0 0px 0px 0px;">
												<span class="btn btn-primary btn-raised btn-sm btn-file">Browse...
													<input type="file" name="fileproof"  id="fileproof" accept=".jpe,.jpeg,.jpg,.pbm,.png,.pdf" onchange="validfile($(this),this)">
												</span>
											</span>                                        
											<input type="text" id="textfile" class="form-control" name="textfile" value="<?php  
														if(isset($insurancedata)){ echo $insurancedata['proof'];}
											?>" readonly >
										</div>                                      
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group" id="amount_div">
									<label for="amount" class="col-md-4 col-sm-3 control-label">Amount</label>
									<div class="col-md-8 col-sm-6">
										<input id="amount" type="text" name="amount"
											class="form-control" value="<?php if(isset($insurancedata)){ echo number_format($insurancedata['amount'],2,'.',''); } ?>" onkeypress="return decimal_number_validation(event, this.value, 8)">
									</div>
								</div>
		    				</div>
						</div>
						<div class="form-group">
							<label for="focusedinput" class="col-md-4  col-sm-3 col-xs-1 control-label"></label>
							<div class="col-md-7 col-sm-9 col-xs-11">
								<?php if(isset($insurancedata)){ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
									<input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
								<?php }else{ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
									<input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="ADD & NEW" class="btn btn-primary btn-raised">
								<?php } ?>
								<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
								<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text;?></a>
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