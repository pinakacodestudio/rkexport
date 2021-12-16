<div class="page-content">
	<div class="page-heading">            
        <h1><?php if(isset($vehiclepollutioncertificaterow)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($vehiclepollutioncertificaterow)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
					<form class="form-horizontal" id="vehiclepollutioncertificateform">
						<input type="hidden" name="vehiclepollutioncertificateid" value="<?php if(isset($vehiclepollutioncertificaterow)){ echo $vehiclepollutioncertificaterow['id']; } ?>">
						

						
						<div class="form-group" id="vehicleid_div">
							<label for="vehicleid" class="col-sm-4 control-label">Vehicle Name <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<select id="vehicleid" name="vehicleid" class="selectpicker form-control" data-live-search="true" data-size="8" title="Select Vehicle Name">
									<option value=0>Select Vehicle </option>
									<?php foreach($vehicledata as $row){ ?>
										<option value="<?php echo $row['id']; ?>" <?php if(isset($vehiclepollutioncertificaterow)){ if($row['id'] == $vehiclepollutioncertificaterow['vehicleid']){ echo 'selected'; } } ?>><?php echo $row['manufacturingcompany']." (".$row['registrationno'].")"; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						
						<div class="form-group" id="pcno_div">
							<label for="pcno" class="col-sm-4 control-label">Pollution Certificate No. <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<input id="pcno" type="text" name="pcno" value="<?php if(!empty($vehiclepollutioncertificaterow)){ echo $vehiclepollutioncertificaterow['pcno']; } ?>" class="form-control">
							</div>
						</div>

						<div class="form-group" id="issuingauthority_div">
							<label for="issuingauthority" class="col-sm-4 control-label">Issuing Authority Name <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<input id="issuingauthority" type="text" name="issuingauthority" value="<?php if(!empty($vehiclepollutioncertificaterow)){ echo $vehiclepollutioncertificaterow['issuingauthority']; } ?>" class="form-control">
							</div>
						</div>
						<div class="form-group" id="pcdate_div">
							<label for="fromdate" class="col-sm-4 control-label">Pollution Certificate Date <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<div class="input-daterange input-group" id="datepicker-range">
									<input type="text" class="input-small form-control" name="fromdate" id="fromdate" value="<?php if(!empty($vehiclepollutioncertificaterow)){ echo $this->general_model->displaydate($vehiclepollutioncertificaterow['fromdate']); } ?>" readonly/>
									<span class="input-group-addon">to</span>
									<input type="text" class="input-small form-control" name="todate" id="todate" value="<?php if(!empty($vehiclepollutioncertificaterow)){ echo $this->general_model->displaydate($vehiclepollutioncertificaterow['todate']); } ?>" readonly/>
								</div>
							</div>
						</div>

						<div class="form-group" id="proof_div">
								<label for="textfile" class="col-md-4 control-label" > Proof <span class="mandatoryfield"> * </span></label>
								<div class="col-md-8" >
										<input type="hidden" name="oldproof" id="oldproof" value="<?php if(isset($vehiclepollutioncertificaterow)){ echo $vehiclepollutioncertificaterow['proof'];} ?>">
									<div class="input-group" id="fileupload">
										<span class="input-group-btn" style="padding: 0 0px 0px 0px;">
											<span class="btn btn-primary btn-raised btn-sm btn-file">Browse...
													<input type="file" name="fileproof"  id="fileproof" onchange="validfile($(this))">
											</span>
											</span>                                        
											<input type="text" id="textfile" class="form-control" name="textfile" value="<?php  
														if(isset($vehiclepollutioncertificaterow)){ echo $vehiclepollutioncertificaterow['proof'];}
											?>" readonly >
								</div>                                      
							</div>
						</div>

						<div class="form-group">
							<label for="focusedinput" class="col-sm-4 control-label">Activate</label>
							<div class="col-sm-8">
								<div class="col-sm-2" style="padding-left: 0px;">
									<div class="radio">
									<input type="radio" name="status" id="yes" value="1" <?php if(isset($vehiclepollutioncertificaterow) && $vehiclepollutioncertificaterow['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
									<label for="yes">Yes</label>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="radio">
									<input type="radio" name="status" id="no" value="0" <?php if(isset($vehiclepollutioncertificaterow) && $vehiclepollutioncertificaterow['status']==0){ echo 'checked'; }?>>
									<label for="no">No</label>
									</div>
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<label for="focusedinput" class="col-sm-4 control-label"></label>
							<div class="col-sm-8">
								<?php if(isset($vehiclepollutioncertificaterow)){ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
								<?php }else{ ?>
								  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
								  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
								<?php } ?>
								<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>vehicle-pollution-certificate" title=<?=cancellink_title?>><?=cancellink_text;?></a>
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