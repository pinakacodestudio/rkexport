<style type="text/css">
   .datepicker1{
    text-align: left !important;
    border-radius: 3px !important;
  }
</style>
<div class="page-content">
	<div class="page-heading">            
        <h1>Add <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active">Add <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-12 col-lg-12">
					<form class="form-horizontal" id="vouchercodeform">
						<input type="hidden" name="voucherid" value="<?php if(isset($vouchercodedata)){ echo $vouchercodedata['id']; } ?>">
						<div class="col-md-6">
							<div class="form-group" id="channelid_div">
									<label class="col-sm-4 control-label" for="channelid">Select Channel</label>
									<div class="col-sm-8">
										<select id="channelid" name="channelid[]" class="selectpicker form-control" data-select-on-tab="true" data-size="5" title="Select Channel" data-live-search="true" data-actions-box="true" multiple>
											<?php 
											$channelidarr=array();
											if(isset($vouchercodedata) && !empty($vouchercodedata['channelid'])){
												$channelidarr = explode(",", $vouchercodedata['channelid']);
											}
											foreach($channeldata as $cd){ 
												
												$selected = '';
												if(in_array($cd['id'], $channelidarr)){
													$selected = 'selected';
												}	
												?>
												<option value="<?php echo $cd['id']; ?>" <?php echo $selected; ?>><?php echo $cd['name']; ?></option>
											<?php } ?>
										</select>
									</div>
							</div>
							<div class="form-group" id="name_div">
								<label class="col-sm-4 control-label" for="name">Name <span class="mandatoryfield">*</span></label>
								<div class="col-sm-8">
									<input id="name" type="text" name="name" value="<?php if(!empty($vouchercodedata)){ echo $vouchercodedata['name']; } ?>" class="form-control">
								</div>
							</div>
							<div class="form-group" id="vouchercode_div">
								<label class="col-sm-4 control-label" for="vouchercode">Coupon Code <span class="mandatoryfield">*</span></label>
								<div class="col-sm-8">
									<input id="vouchercode" type="text" name="vouchercode" class="form-control" onkeypress="return alphanumeric(event)" maxlength="10" value="<?php if(!empty($vouchercodedata)){ echo $vouchercodedata['vouchercode']; }else{ echo $vouchercode; } ?>" >
								</div> 
							</div>	
							<div class="form-group" id="maximumusage_div">
								<label class="col-sm-4 control-label" for="maximumusage">Maximum Usage By Single Customer <span class="mandatoryfield">*</span></label>
								<div class="col-sm-8">
									<input id="maximumusage" type="text" name="maximumusage" class="form-control" onkeypress="return isNumber(event)" maxlength="8" value="<?php if(!empty($vouchercodedata)){ echo $vouchercodedata['maximumusage']; }else{ echo 1; } ?>">
								</div>
							</div>

							<div class="row">
								<button type="button" id="cleardatebtn" class="btn btn-primary btn-raised btn-xs pull-right">Clear Date</button>
							</div>
							 <div class="input-daterange" id="datepicker-range">
								<div class="form-group row" id="startdate_div">
									<label class="col-sm-4 control-label" for="startdate">Date </label>
									<div class="col-sm-4">
										<input id="startdate" type="text" name="startdate" value="<?php if(!empty($vouchercodedata)){  if($vouchercodedata['startdate']!="0000-00-00"){ echo $this->general_model->displaydate($vouchercodedata['startdate']); }} ?>" class="form-control datepicker1" placeholder="Start" readonly>
									</div>
									<div class="col-sm-4">
									<input id="enddate" type="text" name="enddate" value="<?php if(!empty($vouchercodedata)){if($vouchercodedata['enddate']!="0000-00-00"){ echo $this->general_model->displaydate($vouchercodedata['enddate']); }} ?>" class="form-control datepicker1" placeholder="End" readonly>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="col-sm-4 control-label">Discount Type</label>
								<div class="col-sm-8">
									<div class="col-sm-4 col-xs-6" style="padding-left: 0px;">
										<div class="radio">
										<input type="radio" name="discounttype" id="percentage" value="1" checked <?php if(isset($vouchercodedata) && $vouchercodedata['discounttype']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
										<label for="percentage">Percentage</label>
										</div>
									</div>
									<div class="col-sm-4 col-xs-6">
										<div class="radio">
										<input type="radio" name="discounttype" id="amounttype" value="0" <?php if(isset($vouchercodedata) && $vouchercodedata['discounttype']==0){ echo 'checked'; }?>>
										<label for="amounttype">Amount</label>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group" id="percentageval_div">
								<label class="col-sm-4 control-label" for="percentageval">Percentage (%) <span class="mandatoryfield">*</span></label>
								<div class="col-sm-8">
									<input id="percentageval" type="text" name="percentageval" class="form-control" onkeypress="return decimal_number_validation(event,this.value)" maxlength="5" value="<?php if(!empty($vouchercodedata) && $vouchercodedata['discounttype']==1){ echo $vouchercodedata['discountvalue']; } ?>">
								</div>
							</div>
							<div class="form-group" id="amount_div" style="display: none;">
								<label class="col-sm-4 control-label" for="amount">Amount <span class="mandatoryfield">*</span></label>
								<div class="col-sm-8">
									<input id="amount" type="text" name="amount" class="form-control" onkeypress="return decimal_number_validation(event,this.value)" maxlength="10" value="<?php if(!empty($vouchercodedata) && $vouchercodedata['discounttype']==0){ echo $vouchercodedata['discountvalue']; } ?>">
								</div>
							</div>

							<div class="form-group" id="minbillamount_div">
								<label class="col-sm-4 control-label" for="minbillamount">Minimum Bill Amount </label>
								<div class="col-sm-8">
									<input id="minbillamount" type="text" name="minbillamount" class="form-control" onkeypress="return decimal_number_validation(event,this.value)" maxlength="10" value="<?php if(!empty($vouchercodedata) && $vouchercodedata['minbillamount']>0){ echo $vouchercodedata['minbillamount']; } ?>">
								</div>
							</div>
							
							<div class="form-group" id="noofcustomerused_div">
								<label class="col-sm-4 control-label" for="noofcustomerused">No Of Customer Used <span class="mandatoryfield">*</span></label>
								<div class="col-sm-8">
									<input id="noofcustomerused" type="text" name="noofcustomerused" class="form-control" onkeypress="return isNumber(event)" maxlength="8" value="<?php if(!empty($vouchercodedata)){ echo $vouchercodedata['noofcustomerused']; }else{ echo 1; } ?>">
								</div>
							</div>
							
							<div class="form-group">
								<label for="focusedinput" class="col-sm-3 control-label">Activate</label>
								<div class="col-sm-8">
									<div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
										<div class="radio">
										<input type="radio" name="status" id="yes" value="1" <?php if(isset($vouchercodedata) && $vouchercodedata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
										<label for="yes">Yes</label>
										</div>
									</div>
									<div class="col-sm-2 col-xs-6">
										<div class="radio">
										<input type="radio" name="status" id="no" value="0" <?php if(isset($vouchercodedata) && $vouchercodedata['status']==0){ echo 'checked'; }?>>
										<label for="no">No</label>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group text-center">
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="<?php if(isset($vouchercodedata)){ echo 'UPDATE'; }else{ echo 'ADD'; }?>" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
									<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>voucher-code" title=<?=cancellink_title?>><?=cancellink_text?></a>
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