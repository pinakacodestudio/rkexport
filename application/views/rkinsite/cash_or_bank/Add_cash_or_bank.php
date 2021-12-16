<div class="page-content">
	<div class="page-heading">
        <h1><?php if(isset($cashorbankdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($cashorbankdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">                        
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-12 col-lg-12 p-n">
					<form class="form-horizontal" id="cashorbankform">
                        <input id="cashorbankid" name="cashorbankid" value="<?php if(isset($cashorbankdata)){ echo $cashorbankdata['id']; }?>" type="hidden">
                           
                        <div class="col-md-12 p-n">
                            <div class="col-md-6">
                                <div class="form-group" id="accountno_div">
                                    <label class="col-sm-4 control-label" for="accountno">Account No. <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-7">								
                                        <input id="accountno" class="form-control" name="accountno" value="<?php if(isset($cashorbankdata)){ echo $cashorbankdata['accountno']; }?>" type="text" maxlength="20" onkeypress="return isNumber(event)">
                                    </div>
                                </div>
                            </div>	
                            <div class="col-md-6">
                                <div class="form-group" id="branchname_div">
                                <label class="col-sm-4 control-label" for="branchname">Branch Name</label>
                                    <div class="col-md-7">								
                                        <input id="branchname" class="form-control" name="branchname" value="<?php if(isset($cashorbankdata)){ echo $cashorbankdata['branchname']; }?>" type="text" onkeypress="return onlyAlphabets(event)">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="bankname_div">
                                <label class="col-sm-4 control-label" for="bankname">Bank Name <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-7">								
                                        <input id="bankname" class="form-control" name="bankname" value="<?php if(isset($cashorbankdata)){ echo $cashorbankdata['bankname']; }?>" type="text" onkeypress="return onlyAlphabets(event)">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="branchaddress_div">
                                <label class="col-sm-4 control-label" for="branchaddress">Branch Address</label>
                                    <div class="col-sm-7">
                                        <textarea id="branchaddress" name="branchaddress" class="form-control" value=""><?php if(isset($cashorbankdata)){ echo $cashorbankdata['branchaddress']; }?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">				
                                <div class="form-group" id="openingbalance_div">
                                    <label class="col-sm-4 control-label" for="openingbalance">Opening Balance</label>
                                    <div class="col-sm-7">
                                        <input id="openingbalance" type="text" name="openingbalance" class="form-control" onkeypress="return decimal_number_validation(event,this.value,10,2)" value="<?php if(isset($cashorbankdata)){ echo number_format($cashorbankdata['openingbalance'],2,'.',''); }?>">
                                    </div>                
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="ifsccode_div">
                                    <label class="col-sm-4 control-label" for="ifsccode">IFSC Code</label>
                                    <div class="col-sm-7">
                                        <input id="ifsccode" type="text" name="ifsccode" class="form-control" value="<?php if(isset($cashorbankdata)){ echo $cashorbankdata['ifsccode']; }?>">
                                    </div>								
                                </div>
                            </div>
                            <div class="col-md-6">				
                                <div class="form-group" id="openingbalancedate_div">
                                    <label class="col-sm-4 control-label" for="openingbalancedate">Opening Balance Date</label>
                                    <div class="col-sm-7">
                                        <input id="openingbalancedate" name="openingbalancedate"  type="text" class="form-control" value="<?php if(isset($cashorbankdata) && $cashorbankdata['openingbalancedate']!="0000-00-00"){ echo $this->general_model->displaydate($cashorbankdata['openingbalancedate']); }else{ /* echo $this->general_model->displaydate($this->general_model->getCurrentDate()); */ } ?>" readonly>                                          
                                    </div>                
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="micrcode_div">
                                    <label class="col-sm-4 control-label" for="micrcode">MICR Code</label>
                                    <div class="col-sm-7">
                                        <input id="micrcode" type="text" name="micrcode" class="form-control" value="<?php if(isset($cashorbankdata)){ echo $cashorbankdata['micrcode']; }?>">
                                    </div>								
                                </div>
                            </div>
                            <div class="col-md-6" style="<?php if(isset($cashorbankdata)){ if('cash'==strtolower($cashorbankdata['bankname'])){echo 'display:none;';}} ?>">				
                                <div class="form-group" id="defaultbank_div">
                                    <label class="col-sm-4 control-label" for="defaultbank">Default Bank</label>
                                    <div class="col-sm-8">
                                        <div class="col-md-3 col-xs-3" style="padding-left: 0px;">
                                            <div class="radio">
                                                <input type="radio" name="defaultbank" id="yes" value="1" <?php if(isset($cashorbankdata) && $cashorbankdata['defaultbank']==1){ echo 'checked'; }?>>
                                                <label for="yes">Yes</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xs-4">
                                            <div class="radio">
                                                <input type="radio" name="defaultbank" id="no" value="0" <?php if(isset($cashorbankdata)){ if($cashorbankdata['defaultbank']==0){ echo 'checked'; } }else{ echo 'checked'; }?>>
                                                <label for="no">No</label>
                                            </div>
                                        </div>
                                    </div>                
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-8 col-lg-8 col-lg-offset-3 col-md-offset-3">
                        <hr>
                            <div class="form-group">
                                <label for="focusedinput" class="col-md-3 control-label">Activate</label>
                                <div class="col-md-5">
                                    <div class="col-md-4 col-xs-4" style="padding-left: 0px;">
                                        <div class="radio">
                                            <input type="radio" name="status" id="yes" value="1" <?php if(isset($cashorbankdata) && $cashorbankdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                            <label for="yes">Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-xs-4">
                                        <div class="radio">
                                            <input type="radio" name="status" id="no" value="0" <?php if(isset($cashorbankdata) && $cashorbankdata['status']==0){ echo 'checked'; }?>>
                                            <label for="no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 text-center">
                            <div class="form-group">
                                <?php if(isset($cashorbankdata)){ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
                                <?php }else{ ?>
                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
                                <?php } ?>
                                <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->uri->segment(2)?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
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